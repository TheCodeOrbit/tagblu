<?php

namespace app\models;
use app\models\ModtrackerDetail;

use Yii;

/**
 * This is the model class for table "modtracker_basic".
 *
 * @property int $id
 * @property int|null $crmid
 * @property string|null $module
 * @property int|null $whodid
 * @property string|null $changedon
 * @property int|null $status
 */
class ModtrackerBasic extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'modtracker_basic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['crmid', 'whodid', 'status'], 'integer'],
            [['changedon'], 'safe'],
            [['module'], 'string', 'max' => 50],
        ];
    }
    public function getModtrackerDetail()
    {
        return $this->hasOne(ModtrackerDetail::className(), ['id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'crmid' => 'Crmid',
            'module' => 'Module',
            'whodid' => 'Whodid',
            'changedon' => 'Changedon',
            'status' => 'Status',
        ];
    }

    public function auditlog($oldAttributes, $newattributes, $ModuleName, $crmid, $auditstatus, $whodid, $relationmodule = '', $relationid = '')
    {
        // Check if $ModuleName is numeric or a string
        if (is_numeric($ModuleName)) {
            // If $ModuleName is a number (e.g., a tabid), use it in the query
            $sql = "SELECT tablename, tablekeyid FROM tab WHERE tabid = :tabid";
        } else {
            // If $ModuleName is a string (e.g., a module name), use it in the query
            $sql = "SELECT tablename, tablekeyid FROM tab WHERE name = :tabid";
        }

        // Now execute the query with the dynamically built SQL
        $mod = Yii::$app->db->createCommand($sql)
            ->bindValue(":tabid", $ModuleName)
            ->queryOne();

        if ($mod !== false) {
            // Proceed with your code if the result is valid
            $tableName = $mod['tablename'];
            $fieldId = $mod['tablekeyid'];
        } else {
            // Handle case where no record is found
            echo "No record found for the specified module name.\n";
            echo "Executed SQL: $sql\n";
            echo "With ModuleName: " . $ModuleName;
        }

        // echo $auditstatus;die;
        if ($auditstatus == 0 || $auditstatus == 5) {//insert
            //insert in modtrackerbasic table
            // echo "insert into `modtracker_basic` set crmid=$crmid,module='$ModuleName',whodid=$whodid,status=0,changedon='".date("Y-m-d H:i:s")."'";
            Yii::$app->db->createCommand("insert into `modtracker_basic` set crmid=:crmid,module=:module,whodid=:whodid,status=:status,changedon=:changedon")
                ->bindValue("crmid", $crmid)
                ->bindValue("module", $ModuleName)
                ->bindValue("whodid", $whodid)
                ->bindValue("status", $auditstatus)
                ->bindValue("changedon", date("Y-m-d H:i:s"))
                ->execute();
            $lastid = Yii::$app->db->getLastInsertID();
            foreach ($newattributes as $column => $value) {
                if (is_array($value)) {
                    $value = implode(",", $value);// don't know how to deal with multiselect logs
                }
                # code...
                Yii::$app->db->createCommand("insert into `modtracker_detail` set id=:id,fieldname=:fieldname,prevalue=:prevalue,postvalue=:postvalue")
                    ->bindValue("id", $lastid)
                    ->bindValue("fieldname", $column)
                    ->bindValue("prevalue", '')
                    ->bindValue("postvalue", $value)
                    ->execute();

            }
            //add workflow for emails
            //get tablename and fieldid from tab table
            // \common\components\WorkflowService::run($this->moduleName, $this->tableName(),$this->fieldId,$modelleadetail->sourcingdeal_id,  [], $modelleadetail->attributes);
            if (isset($tableName) && isset($fieldId) && !empty($tableName) && !empty($fieldId)) {
                $new = Yii::$app->db->createCommand("
                        SELECT *
                        FROM $tableName
                        WHERE $fieldId = :id
                    ")->bindValue(':id', $crmid)->queryOne();

                \common\components\WorkflowService::run(
                    $ModuleName,              // moduleName
                    $crmid,                      // recordId
                    $oldAttributes, // old data
                    (array) $new,                    // new data
                    $tableName,             // tablename
                    $fieldId                  // primary key field
                );

                \common\components\WorkflowService::flushQueue();

            }
            //end workflow
        } else if ($auditstatus == 3) {//added module
            //insert in modtrackerbasic table
            // echo "insert into `modtracker_basic` set crmid=$crmid,module=$ModuleName,whodid=$whodid,status=$auditstatus,changedon="+date("Y-m-d H:i:s");die;
            //get module name
            $mod = Yii::$app->db->createCommand("select name from tab where tabid = :tabid")->bindValue(":tabid", $ModuleName)->queryOne();
            $ModuleName = $mod['name'];
            Yii::$app->db->createCommand("insert into `modtracker_basic` set crmid=:crmid,module=:module,whodid=:whodid,status=$auditstatus,changedon=:changedon")
                ->bindValue("crmid", $crmid)
                ->bindValue("module", $ModuleName)
                ->bindValue("whodid", $whodid)
                ->bindValue("changedon", date("Y-m-d H:i:s"))
                ->execute();
            $id = Yii::$app->db->getLastInsertID();

            Yii::$app->db->createCommand("insert into `modtracker_relations` set id=:id,targetmodule=:targetmodule,targetid=:targetid,changedon=:changedon")
                ->bindValue("id", $id)
                ->bindValue("targetmodule", $relationmodule)
                ->bindValue("targetid", $relationid)
                ->bindValue("changedon", date("Y-m-d H:i:s"))
                ->execute();

        }
        // || $auditstatus == 9 added by ptpatel for update record via import functionality
        // || $auditstatus == 10 added by ptpatel for update record via single edit  functionality
        else if ($auditstatus == 2 || $auditstatus == 1 || $auditstatus == 4 || $auditstatus == 7 || $auditstatus == 8 || $auditstatus == 9 || $auditstatus == 10) {//update
            if (!$oldAttributes) {
                // die("Record not found!");
            }
            // echo "new";
            // echo $crmid;die;

            //  Compare old and new values
            $changes = [];
            foreach ($newattributes as $column => $newValue) {
                //echo $column;die;
                if (
                    $column != "modifiedtime" && $column != "modifiedby" && $column != "creatorid" &&
                    $column != "createdtime"
                ) {
                    $oldValue = $oldAttributes[$column] ?? null;
                    //echo $oldValue." ".$newValue;die; 
                    if ($oldValue != $newValue) {
                        $changes[$column] = [
                            'prevalue' => $oldValue,
                            'postvalue' => $newValue
                        ];
                    }
                }
            }
            // print_r($newattributes);print_r($oldAttributes);die;
            //Log changes
            if (!empty($changes)) {
                $transaction = Yii::$app->db->beginTransaction();

                try {
                    //insert in modtrackerbasic table
                    Yii::$app->db->createCommand("insert into `modtracker_basic` set crmid=:crmid,module=:module,whodid=:whodid,status=:status,changedon=:changedon")
                        ->bindValue("crmid", $crmid)
                        ->bindValue("status", $auditstatus)
                        ->bindValue("module", $ModuleName)
                        ->bindValue("whodid", $whodid)
                        ->bindValue("changedon", date("Y-m-d H:i:s"))
                        ->execute();

                    $lastid = Yii::$app->db->getLastInsertID();

                    foreach ($changes as $column => $values) {

                        Yii::$app->db->createCommand("insert into `modtracker_detail` set id=:id,fieldname=:fieldname,prevalue=:prevalue,postvalue=:postvalue")
                            ->bindValue("id", $lastid)
                            ->bindValue("fieldname", $column)
                            ->bindValue("prevalue", $values['prevalue'])
                            ->bindValue("postvalue", $values['postvalue'])
                            ->execute();
                    }
                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    echo 'An error occurred: ' . $e->getMessage();
                    die;

                }

                //echo "Changes logged successfully.";
            }

            //add workflow for emails
            //get tablename and fieldid from tab table
            // \common\components\WorkflowService::run($this->moduleName, $this->tableName(),$this->fieldId,$modelleadetail->sourcingdeal_id,  [], $modelleadetail->attributes);
            if (isset($tableName) && isset($fieldId) && !empty($tableName) && !empty($fieldId)) {
                $new = Yii::$app->db->createCommand("
                        SELECT *
                        FROM $tableName
                        WHERE $fieldId = :id
                    ")->bindValue(':id', $crmid)->queryOne();

                \common\components\WorkflowService::run(
                    $ModuleName,              // moduleName
                    $crmid,                      // recordId
                    $oldAttributes, // old data
                    (array) $new,                    // new data
                    $tableName,             // tablename
                    $fieldId                  // primary key field
                );

                \common\components\WorkflowService::flushQueue();

            }
            //end workflow


        } else if ($auditstatus == 6) {//added module
            //insert in modtrackerbasic table
            // echo "insert into `modtracker_basic` set crmid=$crmid,module=$ModuleName,whodid=$whodid,status=$auditstatus,changedon="+date("Y-m-d H:i:s");die;
            //get module name
            $mod = Yii::$app->db->createCommand("select name from tab where tabid = :tabid")->bindValue(":tabid", $ModuleName)->queryOne();
            $ModuleName = $mod['name'];
            Yii::$app->db->createCommand("insert into `modtracker_basic` set crmid=:crmid,module=:module,whodid=:whodid,status=$auditstatus,changedon=:changedon")
                ->bindValue("crmid", $crmid)
                ->bindValue("module", $ModuleName)
                ->bindValue("whodid", $whodid)
                ->bindValue("changedon", date("Y-m-d H:i:s"))
                ->execute();
            $id = Yii::$app->db->getLastInsertID();



        }

        // centralized place to track change of ownerid to manage listview for intermediate users
        $owner_trakcer_enabled = true;
        $old_ownerid = null;
        $new_ownerid = null;
        $record_creatorid = null;
        if ($auditstatus == 0)
            $owner_trakcer_enabled = false;
        if (!empty($oldAttributes)) {
            $old_ownerid = $oldAttributes["ownerid"] ?? null;
            $record_creatorid = $oldAttributes["creatorid"] ?? null;
        }
        if (!empty($newattributes)) {
            if (!empty($newattributes["ownerid"])) {
                $new_ownerid = $newattributes["ownerid"];
            }
        }
        if ($old_ownerid == $new_ownerid)
            $owner_trakcer_enabled = false;
        if ($record_creatorid == $new_ownerid)
            $owner_trakcer_enabled = false;
        if (!empty($newattributes) && $owner_trakcer_enabled) {
            if (!empty($newattributes["ownerid"])) {
                Yii::$app->db->createCommand("INSERT INTO owner_tracker(module,module_reference_id,ownerid,created_on,created_by,deleted) VALUES(:module,:module_reference_id,:ownerid,:created_on,:created_by,:deleted)")
                    ->bindValue("module_reference_id", $crmid)
                    ->bindValue("ownerid", $newattributes["ownerid"])
                    ->bindValue("module", $ModuleName)
                    ->bindValue("created_by", $whodid)
                    ->bindValue("created_on", date("Y-m-d H:i:s"))
                    ->bindValue("deleted", 0)
                    ->execute();
            }
        }

    }
}
