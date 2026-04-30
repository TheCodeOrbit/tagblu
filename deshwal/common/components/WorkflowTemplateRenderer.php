<?php
namespace common\components;

use Yii;
use app\models\Reference;
use app\models\ListHire;
class WorkflowTemplateRenderer
{
    /**
     * MAIN RENDER FUNCTION
     */
   public static function renderWithFieldLabels(
        $templateid,
        $template,
        $moduleName,
        $tablename,
        $fieldId,
        $recordId,
        $oldData,
        $newData
    ) {

        // -------------------------------------------------
        // 1. WHAT placeholders are needed (alias + column)
        // -------------------------------------------------
        $relations = Yii::$app->db->createCommand("
            SELECT relation_columnname, aliasname
            FROM workflow_relation
            WHERE templateid = :templateid
        ")->bindValue(':templateid', $templateid)->queryAll();

        // -------------------------------------------------
        // 2. HOW tables are joined (LEVEL chain)
        // -------------------------------------------------
        $mappings = Yii::$app->db->createCommand("
            SELECT level, related_table, related_field, related_key
            FROM workflow_relation_mapping
            WHERE template_id = :templateid
            ORDER BY level ASC
        ")->bindValue(':templateid', $templateid)->queryAll();

        // -------------------------------------------------
        // 3. Build JOINs + SELECT safely
        // -------------------------------------------------
        $select    = "m.*";
        $joinSql  = "";
        $prevAlias = "m";

        // cache: table.column => field meta
        $fieldMetaCache = [];

        foreach ($mappings as $mapRow) {

            if (
                empty($mapRow['related_table']) ||
                empty($mapRow['related_field']) ||
                empty($mapRow['related_key']) ||
                empty($mapRow['level'])
            ) {
                continue;
            }

            $levelNo = (int) filter_var($mapRow['level'], FILTER_SANITIZE_NUMBER_INT);
            $alias   = "r{$levelNo}";
            $table   = $mapRow['related_table'];

            $joinSql .= "
                LEFT JOIN {$table} {$alias}
                    ON {$prevAlias}.{$mapRow['related_field']}
                    = {$alias}.{$mapRow['related_key']}
            ";

            foreach ($relations as $rel) {

                if (empty($rel['relation_columnname']) || empty($rel['aliasname'])) {
                    continue;
                }

                $col = $rel['relation_columnname'];
                $cacheKey = "{$table}.{$col}";

                if (!array_key_exists($cacheKey, $fieldMetaCache)) {
                    $fieldMetaCache[$cacheKey] =
                        Yii::$app->db->createCommand("
                            SELECT *
                            FROM field
                            WHERE tablename = :table
                            AND columnname = :col
                            LIMIT 1
                        ")
                        ->bindValue(':table', $table)
                        ->bindValue(':col', $col)
                        ->queryOne() ?: false;
                }

                if ($fieldMetaCache[$cacheKey] === false) {
                    continue;
                }

                $safeAlias = "L{$levelNo}_" . trim($rel['aliasname'], '{}');
                $select   .= ", {$alias}.{$col} AS {$safeAlias}";
            }

            $prevAlias = $alias;
        }

        // -------------------------------------------------
        // 4. Fetch record
        // -------------------------------------------------
        $sql = "
            SELECT {$select}
            FROM {$tablename} m
            {$joinSql}
            WHERE m.{$fieldId} = :id
        ";

        $record = Yii::$app->db->createCommand($sql)
            ->bindValue(':id', $recordId)
            ->queryOne();

        if (!$record) {
            return $template;
        }

        // -------------------------------------------------
        // 5. MAIN table placeholders
        // -------------------------------------------------
        $fields = Yii::$app->db->createCommand("
            SELECT *
            FROM field
            WHERE tablename = :t
        ")->bindValue(':t', $tablename)->queryAll();

        $map = [];

        foreach ($fields as $f) {

            $label = preg_replace('/\s+/', '', trim($f['fieldlabel']));
            $placeholderLabel = '{' . $label . '}';
            $placeholderColumn = '{' . $f['columnname'] . '}';

            $column = $f['columnname'];
            $uitype = (int) ($f['uitype'] ?? 0);

            $value = $record[$column] ?? $newData[$column] ?? $oldData[$column] ?? '';

            $formattedValue = (string) self::applyUiTypeFormatting(
                $value,
                $uitype,
                $f,
                $record,
                $tablename,
                $fieldId
            );

            // -------------------------------------------------
            // FIX: uitype 31 (multi-select IDs like 2,3,4)
            // -------------------------------------------------
            if ($uitype === 31 && is_string($value) && strpos($value, ',') !== false) {

                // split IDs
                $ids = array_filter(array_map('trim', explode(',', $value)));

                $labels = [];

                foreach ($ids as $id) {

                    // apply formatting per single value
                    $label = self::applyUiTypeFormatting(
                        $id,
                        $uitype,
                        $f,
                        $record,
                        $tablename,
                        $fieldId
                    );

                    if ($label !== '' && $label !== null) {
                        $labels[] = $label;
                    }
                }

                // remove duplicates & join
                $formattedValue = implode(', ', array_unique($labels));
            }

            $formattedValue = (string) $formattedValue;


            $map[$placeholderLabel]  = $formattedValue;
            $map[$placeholderColumn] = $formattedValue;
        }

        // -------------------------------------------------
        // 6. RELATED placeholders (MULTI LEVEL + MULTI VALUE SAFE)
        // -------------------------------------------------
        $relatedValueBucket = [];

        foreach ($mappings as $mapRow) {

            $levelNo = (int) filter_var($mapRow['level'], FILTER_SANITIZE_NUMBER_INT);
            $table   = $mapRow['related_table'];

            foreach ($relations as $rel) {

                if (empty($rel['aliasname']) || empty($rel['relation_columnname'])) {
                    continue;
                }

                $aliasName   = $rel['aliasname']; // {SalesOrderNo}
                $aliasColumn = "L{$levelNo}_" . trim($aliasName, '{}');

                if (!array_key_exists($aliasColumn, $record)) {
                    continue;
                }

                $value = $record[$aliasColumn];
                if ($value === '' || $value === null) {
                    continue;
                }

                $metaKey = "{$table}.{$rel['relation_columnname']}";

                if (isset($fieldMetaCache[$metaKey]) && $fieldMetaCache[$metaKey]) {

                    $f = $fieldMetaCache[$metaKey];
                    $uitype = (int) ($f['uitype'] ?? 0);

                    $value = self::applyUiTypeFormatting(
                        $value,
                        $uitype,
                        $f,
                        $record,
                        $tablename,
                        $fieldId
                    );
                }

                if (!isset($relatedValueBucket[$aliasName])) {
                    $relatedValueBucket[$aliasName] = [];
                }

                if (!in_array((string) $value, $relatedValueBucket[$aliasName], true)) {
                    $relatedValueBucket[$aliasName][] = (string) $value;
                }
            }
        }

        foreach ($relatedValueBucket as $aliasName => $values) {
            $map[$aliasName] = implode(', ', $values);
        }

        // -------------------------------------------------
        // 7. URLs
        // -------------------------------------------------
        $base = Yii::$app->params['frontendBaseUrl'] ?? Yii::$app->request->hostInfo;
        $mod  = strtolower($moduleName);

        $map['{detail_url}'] =
            Yii::$app->urlManager->createAbsoluteUrl("$moduleName/detail?Record=$recordId");
        $map['{edit_url}'] =
            Yii::$app->urlManager->createAbsoluteUrl("$moduleName/edit?Record=$recordId");
        $map['{create_url}'] =
            Yii::$app->urlManager->createAbsoluteUrl("$moduleName/create");

        $map["{{$mod}_edit_url}"]   = "$base/$mod/update?id=$recordId";
        $map["{{$mod}_detail_url}"] = "$base/$mod/detail?id=$recordId";
        $map["{{$mod}_create_url}"] = "$base/$mod/create";

        // -------------------------------------------------
        // 8. Replace placeholders
        // -------------------------------------------------
        return strtr($template, $map);
    }






    /**
     * APPLY FORMATTING BASED ON UI TYPE
     */
    private static function applyUiTypeFormatting($value, $uitype, $field, $record, $tablename)
    {
        try{
        if ($value === null)
            return '';
        if ($uitype == 53) {
            // echo $uitype;die;
        }
        switch ($uitype) {
            // --------------------
            // Checkbox
            // --------------------
            // checkbox (Yes/No)
            case 6:
                    if ((string)$value === '1' || (int)$value === 1) return 'Yes';
                    if ((string)$value === '0' || (int)$value === 0) return 'No';
                    return (string)$value;

            // --------------------
            // DATE
            // --------------------
            case 17: // yyyy-mm-dd
                return (!empty($value) && $value != "0000-00-00")
                    ? date("d-m-Y", strtotime($value))
                    : '';

            // --------------------
            // DATETIME
            // --------------------
            case 13: // yyyy-mm-dd hh:ii:ss
                return (!empty($value) && $value != "0000-00-00 00:00:00")
                    ? date("d-m-Y H:i", strtotime($value))
                    : '';

            // --------------------
            // PICKLIST
            // --------------------
            case 10:
            case 8:
                return self::getPicklistLabel($field['fieldid'], $value);
            
            // --------------------
            // Comma Separated Values
            // --------------------
            case 22:
            case 9:
                return self::getCommavaluesLabel($field['fieldid'], $value);

            // --------------------
            // REFERENCE (User, Accounts, Vendor, Contact, etc)
            // --------------------
            case 12:
            case 27:
            case 28:
                return self::getReferenceValue($tablename, $field, $value);
            // --------------------
            // mULTIPLE REFERENCE (User, Accounts, Vendor, Contact, etc)
            // --------------------
            case 31:
                return self::getReferenceMultipleValue($tablename, $field, $value);

            // --------------------
            // ownerid
            // --------------------
            case 53:
                return self::getOwnerUser($field['fieldid'], $value);

            default:
                return $value;
        }
        }
        catch (\Exception $e) {
            Yii::error("applyUiTypeFormatting error: " . $e->getMessage() . " for fieldid:" . ($field['fieldid'] ?? '') , __METHOD__);
            return (string)$value;
        }
    }

    /**
     * PICKLIST → LABEL
     */
    private static function getPicklistLabel($fieldid, $value)
    {
        if (!$value)
            return '';


        try {
            $modellist = new Listhire;
            $value = $modellist->getPickListDetailvalue($fieldid, $value);

            // return $label ?: $value;
            return $value;

        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Comma Separated → LABEL
     */
    private static function getCommavaluesLabel($fieldid, $value)
    {
        if (!$value)
            return '';


        try {
            $modellist = new Listhire;
            $value = $modellist->getPickListDetailMultiple($fieldid, $value);

            // return $label ?: $value;
            return $value;

        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Checkobx → LABEL
     */
    private static function getCheckboxLabel($fieldid, $value)
    {
        if (!$value)
            return '';


        try {
            if ($value == 1)
                $value = "Yes";
            else if ($value == 0)
                $value = "No";
            return $value;

        } catch (\Exception $e) {

            return $value;
        }
    }

    /**
     * Usernae → LABEL
     */
    private static function getOwnerUser($fieldid, $value)
    {
        if (!$value)
            return '';


        try {
            $modellist = new Listhire;
            $value = $modellist->getuser($fieldid, $value);
            return $value;

        } catch (\Exception $e) {

            return $value;
        }
    }
    /**
     * REFERENCE FIELDS
     */
    private static function getReferenceValue($TableName, $field, $value)
    {
        if (!$value)
            return '';

        $related = $field['columnname'];
        if (!$related)
            return $value;


        try {

            $model1 = new Reference($TableName, $field['fieldid']);

            $ref_hid_value = $value;
            $value = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);

        } catch (\Exception $e) {
            //echo $e->getMessage();die;
        }

        return $value;
    }
    /**
     * REFERENCE FIELDS WITH COMMA SEPARATED VALUES
     */
    private static function getReferenceMultipleValue($TableName, $field, $value)
    {
        if (!$value)
            return '';

        $related = $field['columnname'];
        if (!$related)
            return $value;



        try {
            $model1 = new Reference($TableName, $field['fieldid']);
            $ref_hid_value_arr = $value;
            $exploded_ref_hid = explode(",", $ref_hid_value_arr);
            $valuenew = '';
            foreach ($exploded_ref_hid as $ref_hid_value) {
                $valuenew .= $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
            }
            $value = $valuenew;


        } catch (\Exception $e) {
        }

        return $value;
    }
}
