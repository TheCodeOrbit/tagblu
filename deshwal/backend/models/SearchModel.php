<?php

namespace app\models;

use backend\models\AccessCheck;
use Yii;
use yii\filters\AccessRule;

/**
 * ListModel class.
 * ListModel is the data structure for keeping
 * ListModel form data. It is used by the 'Module' action of 'Controller'.
 */
class SearchModel extends \yii\db\ActiveRecord
{
    public $_members = [];
    private static $_tableName; // Static property to hold the dynamic table name

    public $tableName;
    public $fieldId;
    public $moduleName;
    public $recordId;
    public $Multiple_Records = [];
    function __construct($tablename, $fieldid = '', $moduleName)
    {
        $this->fieldId = $fieldid;
        $this->setTableName($tablename);

        $this->fieldId = $fieldid;
        $this->moduleName = $moduleName;
        self::$_tableName = $tablename; // Set the dynamic table name
        $this->setTableName(self::$_tableName);
        $Columns = $this->getProperty();
        foreach ($Columns as $Column) {
            $this->_members[$Column["columnname"]] = null;
        }
        $this->_members[$fieldid] = null;
        $this->_members["tableName"] = null;
        $this->_members["moduleName"] = null;
        $this->_members["fieldId"] = null;
        $this->_members["mode"] = null;
        parent::__construct();
    }
    // Static tableName() method required by ActiveRecord
    public static function tableName()
    {
        return self::$_tableName; // Return the dynamic table name
    }
    public function setTableName($tablename)
    {
        $this->tableName = $tablename;
    }
    public function getProperty()
    {
        $table_name = $this->tableName();
        $Columns = Yii::$app->db
            ->createCommand(
                "SELECT field.columnname,field.fieldlabel FROM field  WHERE tablename=:tablename"
            )
            ->bindValue(":tablename", $table_name)
            ->queryAll();
        // (new \yii\db\Query())
        // ->select(['field.columnname', 'field.fieldlabel'])->from('field')->where('tablename = :tablename', [':tablename' => $table_name])->all();
        return $Columns;
    }
    public function getTabDetail($ModuleName)
    {
        $connection = Yii::$app->db;
        $q_tab = "select * from tab where name='$ModuleName'";
        $arr_tab = $connection->createCommand($q_tab)->queryOne();
        return $arr_tab;
    }
    public function getActionList($ModuleName)
    {
        $ActionList = array();
        $actionName = $ModuleName;
        $arr_tab = $this->getTabDetail($ModuleName);
        $ActionList['ActionName'] = $actionName;
        $ActionList['ModuleName'] = $ModuleName;
        $ActionList['ModuleLabel'] = $arr_tab['tablabel'];
        return $ActionList;
    }
    public function getColumnList()
    {
        $table_name = $this->tableName();
        $connection = Yii::$app->db;
        // echo "
        //             SELECT field.fieldid, field.columnname AS fieldname, field.fieldlabel, field.uitype, field.tablename 
        //             FROM customview 
        //             JOIN cvcolumnlist ON customview.cvid = cvcolumnlist.cvid
        //             JOIN field ON cvcolumnlist.columnname = field.columnname
        //             WHERE  UPPER(customview.entitytype) = UPPER($this->moduleName)
        //             AND customview.setdefault = 1 
        //             AND field.tablename = $table_name 
        //             ORDER BY cvcolumnlist.columnindex
        //             ";die;
        $command = $connection->createCommand("
            SELECT field.fieldid, field.columnname AS fieldname, field.fieldlabel, field.uitype, field.tablename 
            FROM customview 
            JOIN cvcolumnlist ON customview.cvid = cvcolumnlist.cvid
            JOIN field ON cvcolumnlist.columnname = field.columnname
            WHERE  UPPER(customview.entitytype) = UPPER(:entitytype)
            AND customview.setdefault = :setdefault 
            AND field.tablename = :tablename 
            And customview.userid = :userid
            ORDER BY cvcolumnlist.columnindex
            ")
            ->bindValue(':entitytype', $this->moduleName)
            ->bindValue(':setdefault', 1)
            ->bindValue(':userid', Yii::$app->user->id)
            ->bindValue(':tablename', $table_name);

        $ColumnList = $command->queryAll();
        return $ColumnList;
    }

    public function getfilterColumnList()
    {
        $table_name = $this->tableName();
        $connection = Yii::$app->db;
        //check if user list is exist


        $command = $connection->createCommand("
            SELECT field.fieldid, field.columnname AS fieldname, field.fieldlabel, field.uitype, field.tablename 
            FROM customview 
            JOIN cvcolumnlist ON customview.cvid = cvcolumnlist.cvid
            JOIN field ON cvcolumnlist.columnname = field.columnname
            WHERE  UPPER(customview.entitytype) = UPPER(:entitytype)
            AND customview.setdefault = :setdefault 
            AND field.tablename = :tablename
            AND field.fieldname != 'related_to_id' 
            AND customview.userid = :userid
            ORDER BY cvcolumnlist.columnindex
            ")
            ->bindValue(':entitytype', $this->moduleName)
            ->bindValue(':setdefault', 1)
            ->bindValue(':userid', Yii::$app->user->id)
            ->bindValue(':tablename', $table_name);

        $ColumnList = $command->queryAll();
        return $ColumnList;
    }

    public function getMassEditColumnList()
    {
        $table_name = $this->tableName();
        $connection = Yii::$app->db;
        // echo "
        //             SELECT field.fieldid, field.columnname AS fieldname, field.fieldlabel, field.uitype, field.tablename 
        //             FROM customview 
        //             JOIN cvcolumnlist ON customview.cvid = cvcolumnlist.cvid
        //             JOIN field ON cvcolumnlist.columnname = field.columnname
        //             WHERE  UPPER(customview.entitytype) = UPPER($this->moduleName)
        //             AND customview.setdefault = 1 
        //             AND field.tablename = $table_name 
        //             ORDER BY cvcolumnlist.columnindex
        //             ";die;
        $command = $connection->createCommand("
            SELECT field.fieldid, field.columnname AS fieldname, field.fieldlabel, field.uitype, field.tablename 
            From  field
            WHERE field.tablename = :tablename 
            AND masseditable =1
            ORDER BY sequence
            ")
            ->bindValue(':tablename', $table_name);

        $ColumnList = $command->queryAll();
        return $ColumnList;
    }

    public function getExportList()
    {
        $table_name = $this->tableName();
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
             SELECT field.fieldid, field.columnname AS fieldname, field.fieldlabel, field.uitype, field.tablename 
             FROM customview 
             JOIN cvcolumnlist ON customview.cvid = cvcolumnlist.cvid
             JOIN field ON cvcolumnlist.columnname = field.columnname
             WHERE  UPPER(customview.entitytype) = UPPER(:entitytype)
             AND customview.setdefault = :setdefault 
             AND field.tablename = :tablename 
             And export = 1 
             And customview.userid = 1
             ORDER BY cvcolumnlist.columnindex
             ")
            ->bindValue(':entitytype', $this->moduleName)
            ->bindValue(':setdefault', 1)
            ->bindValue(':tablename', $table_name);

        $ColumnList = $command->queryAll();
        return $ColumnList;
    }
    public function getKanbanList()
    {
        $table_name = $this->tableName();
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
            SELECT field.fieldid, field.columnname AS fieldname, field.fieldlabel, field.uitype, field.tablename 
            FROM customview 
            JOIN cvcolumnlist ON customview.cvid = cvcolumnlist.cvid
            JOIN field ON cvcolumnlist.columnname = field.columnname
            WHERE  UPPER(customview.entitytype) = UPPER(:entitytype)
            AND customview.setdefault = :setdefault 
            AND field.tablename = :tablename 
            And customview.userid = 1
            And kanbanview = 1 limit 1
            ")
            ->bindValue(':entitytype', $this->moduleName)
            ->bindValue(':setdefault', 1)
            ->bindValue(':tablename', $table_name);

        $ColumnList = $command->queryOne();
        return $ColumnList;
    }
    public function getListRecord($tableName, $keyword, $OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '')
    {
        $ColumnList = $this->getColumnList();
        // print_r($ColumnList);die;
        list($Column, $ListQuery, $totalitemcount) = $this->getQuery($tableName, $keyword, $ColumnList, $OrderBy, $SortOrder, $rolebasedrecord, $modulepermission);
        // echo $ListQuery;die;
        $RecordList = Yii::$app->db->createCommand($ListQuery)->queryAll();
        return array($Column, $RecordList, $totalitemcount);
    }

    public function getExportRecord($OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '')
    {
        $ColumnList = $this->getExportList();
        // print_r($ColumnList);die;
        list($Column, $ListQuery, $totalitemcount) = $this->getQuery($ColumnList, $OrderBy, $SortOrder, $rolebasedrecord, $modulepermission);
        //echo "dd";die;

        $RecordList = Yii::$app->db->createCommand($ListQuery)
            ->queryAll();
        return array($Column, $RecordList, $totalitemcount);
    }

    public function getQuery($tableName, $keyword, $ColumnList, $OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '')
    {

        $FieldId = $this->fieldId;
        $TableName = "`" . $this->tableName() . "`";
        $RecordId = $this->_members[$FieldId];
        $ColumnKey = "";
        $roleid = $rolebasedrecord;
        $Query = '';
        $groupby = '';
        $join = "from $TableName";
        $where = '';
        $orwhere = ' AND ( ';
        // $where .= " and leadinformation.leadname LIKE '%$keyword%' ";
        //$join="from Entity inner join $TableName on(Entity.entityid=$TableName.$FieldId)";
        $Column = array();
        $tableAliasMap = [];
        foreach ($ColumnList as $arrColumn) {  //echo "<pre>"; print_r($arrColumn); die;
            $Column[$arrColumn['fieldname']] = $arrColumn['fieldlabel'];
            if ($arrColumn['uitype'] == 8 || $arrColumn['uitype'] == 10) {
                /*$PickList=new PickList;   
                    $PickList->fieldid=$Field->fieldid;
                    $BlockDetail->Fields[$FieldKey]->fieldoptions=$PickList->getPickListValue();*/

                $PickListDetail = $this->getPickListDetail($arrColumn['fieldid']);
                if ($PickListDetail) {
                    $targettable = $PickListDetail['targettable'];
                    $targetfield = $PickListDetail['targetfield'];
                    $dispfield = $PickListDetail['dispfield'];
                    if ($arrColumn['fieldname'] == "ownerid" || $PickListDetail['targettable'] == 'user') {
                        $alias = $this->getTableAlias($PickListDetail['targettable'], $tableAliasMap);
                        $ColumnKey .= "concat(user" . $arrColumn['fieldname'] . '.first_name," ",user' . $arrColumn['fieldname'] . ".last_name) as `" . $arrColumn['fieldname'] . "`,";
                        $join .= " left join `user` as user" . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=user" . $arrColumn['fieldname'] . ".id)";
                        if ($keyword != '') {
                            $orwhere .= " or concat(user" . $arrColumn['fieldname'] . '.first_name," ",user' . $arrColumn['fieldname'] . ".last_name) LIKE '%$keyword%' ";
                        }
                    } else if ($PickListDetail['targettable'] == 'tab') {


                        $ColumnKey .= 'UPPER(' . $PickListDetail['targettable'] . '.' . $PickListDetail['dispfield'] . ') as `' . $arrColumn['fieldname'] . "`,";
                        $join .= " left join " . $PickListDetail['targettable'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $PickListDetail['targettable'] . "." . $PickListDetail["targetfield"] . ")";
                    } else {
                        /**$ColumnKey .= $PickListDetail['targettable'] . $arrColumn['fieldname'] . '.' . $PickListDetail['dispfield'] . ' as `' . $arrColumn['fieldname'] . "`,";
                        $join .= " left join " . $PickListDetail['targettable'] . " as " . $PickListDetail['targettable'] . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $PickListDetail['targettable'] . $arrColumn['fieldname'] . "." . $PickListDetail["targetfield"] . ")";
                        if ($keyword != '') {
                            $orwhere .= "or ".$PickListDetail['targettable'] . $arrColumn['fieldname'] . '.' . $PickListDetail['dispfield'] . " LIKE '%$keyword%' ";
                        } */
                        $alias = $this->getTableAlias($PickListDetail['targettable'], $tableAliasMap);
                        $ColumnKey .= "$alias.`{$PickListDetail['dispfield']}` as `{$arrColumn['fieldname']}`,";
                        $join .= " LEFT JOIN {$PickListDetail['targettable']} AS $alias
                                ON ($TableName.{$arrColumn['fieldname']} = $alias.{$PickListDetail['targetfield']})";

                        if ($keyword != '') {
                            $orwhere .= " OR $alias.`{$PickListDetail['dispfield']}` LIKE '%$keyword%'";
                        }
                    }
                   
                }
            } else if ($arrColumn['uitype'] == 53) { //53 is hidden so no need to add where condition here
                /*$PickList=new PickList;   
                    $PickList->fieldid=$Field->fieldid;
                    $BlockDetail->Fields[$FieldKey]->fieldoptions=$PickList->getPickListValue();*/


                $ColumnKey .= "user" . $arrColumn['fieldname'] . '.username as ' . $arrColumn['fieldname'] . ",";
                $join .= " left join `user` as user" . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=user" . $arrColumn['fieldname'] . ".id)";
            } else if ($arrColumn['uitype'] == 22 || $arrColumn['uitype'] == 9) { //9 is multiselect
                $PickListDetail = $this->getPickListDetail($arrColumn['fieldid']);
                $targettable = $PickListDetail['targettable'];
                $targetfield = $PickListDetail['targetfield'];
                $dispfield = $PickListDetail['dispfield'];       
                if ($PickListDetail['targettable'] != 'user') {
                    
                    $alias = $this->getTableAlias($PickListDetail['targettable'], $tableAliasMap);
                    // $ColumnKey .= "GROUP_CONCAT(" . $PickListDetail['targettable'] . "." . $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
                    // if ($keyword != '') {
                    //     $orwhere .= " or " . $PickListDetail['targettable'] ."." .  $PickListDetail['dispfield'] . " LIKE '%$keyword%' ";
                    // }
                    if(trim($arrColumn['fieldname']) == 'external_participants')
                    {
                        $ColumnKey .= "GROUP_CONCAT(DISTINCT concat(".$alias.".first_name,' ',".$alias.".last_name) ORDER BY " . $alias . "." . $PickListDetail['targetfield'] . " ) AS " . $arrColumn['fieldname'] . ",";
                    }
                    else
                        $ColumnKey .= "GROUP_CONCAT(DISTINCT ".$alias.".". $PickListDetail['dispfield'] .
                                " ORDER BY " . $alias . "." . $PickListDetail['targetfield'] .
                                " ) AS " . $arrColumn['fieldname'] . ",";

                    if ($keyword != '') {
                        if(trim($arrColumn['fieldname']) == 'external_participants')
                            $orwhere .= " OR concat(".$alias.".first_name,' ',".$alias.".last_name) LIKE '%$keyword%' ";
                        else
                        {
                            // $orwhere .= " OR `" .  $PickListDetail['dispfield'] . "` LIKE '%$keyword%' ";
                            $orwhere .= " OR  $alias.`{$PickListDetail['dispfield']}` LIKE '%$keyword%' ";
                           
                        }
                    }
                   $join .= " LEFT JOIN " . $PickListDetail['targettable'] . " " . $alias .
                        " ON FIND_IN_SET(" . $alias . "." . $PickListDetail['targetfield'] .
                        ", REPLACE(" . $TableName . "." . $arrColumn['fieldname'] . ", ' ', ''))";
                } else {
                    /*$ColumnKey .= "GROUP_CONCAT(" . $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
                    if ($keyword != '') {
                        $orwhere .= " or " .  $PickListDetail['dispfield'] . " LIKE '%$keyword%' ";
                    }
                    // $ColumnKey .= "GROUP_CONCAT(" . $alias . "." . $PickListDetail['dispfield'] .
                    //             " ORDER BY " . $alias . "." . $PickListDetail['targetfield'] .
                    //             ") AS " . $arrColumn['fieldname'] . ",";

                    // if ($keyword != '') {
                    //     $orwhere .= " OR " . $alias . "." . $PickListDetail['dispfield'] . " LIKE '%$keyword%' ";
                    // }
                    $join .= " LEFT JOIN " . $PickListDetail['targettable'] . " " .
                    " ON FIND_IN_SET(user." . $PickListDetail['targetfield'] .
                    ", " . $TableName . "." . $arrColumn['fieldname'] . ")";*/
                    //below code added by ptpatel to resolve same table twice in onq query on date 24-02-2026
                    $alias = $this->getTableAlias($PickListDetail['targettable'], $tableAliasMap);
                     if (stripos($dispfield, 'concat') !== false) {

                            $dispfield = preg_replace('/\b' . preg_quote($targettable, '/') . '\./i', '', $dispfield);

                            // Step 2: Prefix alias only to unqualified fields (skip SQL keywords)
                            $keywords = ['CONCAT', 'IF', 'IS', 'NULL'];

                            $modifiedDispField = preg_replace_callback(
                                '/(?<![\.\w])(\b[a-zA-Z_][a-zA-Z0-9_]*\b)/',
                                function ($matches) use ($alias, $keywords) {
                                    $word = $matches[1];
                                    return in_array(strtoupper($word), $keywords) ? $word : $alias . '.' . $word;
                                },
                                $dispfield
                            );

                            // echo $modifiedDispField; die;
                            //DISTINCT added by ptpatel on 23-02-2026 to remove duplicate value
                            $ColumnKey .= "GROUP_CONCAT(DISTINCT " . $modifiedDispField . " ORDER BY " . $alias . "." . $targetfield . ") AS " . $arrColumn['fieldname'] . ",";
                            if ($keyword != '') {
                                $orwhere .= " or {$modifiedDispField} LIKE '%$keyword%' ";
                            }
                            }
                    else{

                        $ColumnKey .= "GROUP_CONCAT(DISTINCT ".$alias."." . $PickListDetail['dispfield'] . 
                                    " order by " . $alias . "." . $PickListDetail['targettable'] .
                                     " ) as " . $arrColumn['fieldname'] . ",";
                        if ($keyword != '') {
                            $orwhere .= " or $alias.{$PickListDetail['dispfield']}` LIKE '%$keyword%' ";
                        }
                    }
                    // $ColumnKey .= "GROUP_CONCAT(" . $alias . "." . $PickListDetail['dispfield'] .
                    //             " ORDER BY " . $alias . "." . $PickListDetail['targetfield'] .
                    //             ") AS " . $arrColumn['fieldname'] . ",";

                    // if ($keyword != '') {
                    //     $orwhere .= " OR " . $alias . "." . $PickListDetail['dispfield'] . " LIKE '%$keyword%' ";
                    // }
                    $join .= " LEFT JOIN " . $PickListDetail['targettable'] . " AS " . $alias .
                    " ON FIND_IN_SET(" . $alias . "." . $PickListDetail['targetfield'] .
                    ", REPLACE(" . $TableName . "." . $arrColumn['fieldname'] . ", ' ', ''))";
                }
                // $join .= " left join " . $PickListDetail['targettable'] . " on FIND_IN_SET(" . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . "," . $TableName . "." . $arrColumn['fieldname'] . ")";

                

                $groupby = "Group By  $TableName . $FieldId";
            } else if ($arrColumn['uitype'] == 12 || $arrColumn['uitype'] == 27 || $arrColumn['uitype'] == 28) {
                $getEntityNameDetail = $this->getReferenceEntityNameDetail($arrColumn['fieldid']);
                if ($getEntityNameDetail) {
                    $targettable = $getEntityNameDetail['targettable'];
                    $targetfield = $getEntityNameDetail['entityidfield'];
                    $dispfield = $getEntityNameDetail['fieldname'];
                    $ColumnKey .= $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . "." . $dispfield . " as " . $arrColumn['fieldname'] . ",";

                    $join .= " LEFT OUTER JOIN " . $getEntityNameDetail['targettable'] . " as " . $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . "." . $getEntityNameDetail['entityidfield'] . ")";
                    if ($keyword != '') {
                        $orwhere .= " or " . $getEntityNameDetail['targettable'] .$arrColumn['fieldname'] . ".`" . $dispfield . "` LIKE '%$keyword%'";
                    }
                }
            } else if ($arrColumn['uitype'] == 26) {
                $ColumnKey .=
                    "CASE ";
                $getEntityNameDetailval = $this->getReferenceEntityNameDetailMultiple($arrColumn['fieldid']);

                foreach ($getEntityNameDetailval as $getEntityNameDetail) {
                    $modulename = $getEntityNameDetail['modulename'];
                    $targettable = $getEntityNameDetail['targettable'];
                    $targetfield = $getEntityNameDetail['entityidfield'];
                    $dispfield = $getEntityNameDetail['fieldname'];

                    if ($modulename == 'opportunities') {
                        $ColumnKey .=
                            "
            WHEN $TableName.related_to = (select tabid from tab where tab.name = '$modulename') THEN opportunity.$dispfield
            ";
                        } else {
                            $ColumnKey .=
                                "
            WHEN $TableName.related_to = (select tabid from tab where tab.name = '$modulename') THEN $targettable.$dispfield
            ";
                        }



                        // $ColumnKey .= $getEntityNameDetail['targettable'] . "." . $dispfield . " as " . $arrColumn['fieldname'] . ",";


                        $join .= " LEFT OUTER JOIN " . $getEntityNameDetail['targettable'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $getEntityNameDetail['targettable'] . "." . $getEntityNameDetail['entityidfield'] . ")";
                    }
                    $ColumnKey .= "ELSE NULL
            END AS " . $arrColumn['fieldname'] . ",";
                // echo $ColumnKey;die;
            } else if ($arrColumn['uitype'] == 25) {

                // $ColumnKey .= 'mrelated_to.mrelatedto_value ' . " as " . $arrColumn['fieldname'] . ",";
                // $join .= " LEFT OUTER JOIN `mrelated_to` "  . " on (" . $TableName . "." . $arrColumn['fieldname'] . "= mrelated_to.mrelatedtoid)";
                $ColumnKey .= 'tab.tablabel ' . " as " . $arrColumn['fieldname'] . ",";
                $join .= " LEFT OUTER JOIN `tab` " . " on (" . $TableName . "." . $arrColumn['fieldname'] . "= tab.tabid)";
                if ($keyword != '') {
                    $orwhere .= " or " . $TableName . ".`" . $arrColumn['fieldname'] . "` LIKE '%$keyword%'";
                }
            } else if ($arrColumn['uitype'] == 5) {
                $unique_alias = "attachments" . $arrColumn['fieldname'];
                $ColumnKey .= "$unique_alias.name " . " as " . $arrColumn['fieldname'] . ",";
                $join .= " LEFT OUTER JOIN `attachments` as $unique_alias " . " on (" . $TableName . "." . $arrColumn['fieldname'] . "= $unique_alias.attachmentsid)";

                if ($keyword != '') {
                    $orwhere .= " or " . $TableName . ".`" .$arrColumn['fieldname'] . "` LIKE '%$keyword%'";
                    // $orwhere .= " or " . $unique_alias . ".`" .$arrColumn['fieldname'] . "` LIKE '%$keyword%'";
                }
            } elseif ($arrColumn['uitype'] == 6) {
                if ($arrColumn['fieldname'] == 'is_admin' && $arrColumn['tablename'] == "user")
                    $ColumnKey .= "if(user.is_admin is not null,if(user.is_admin=0,'No','Yes'),'') as is_admin,";
                //added on date 07-01-2026
                else  {
                    $ColumnKey .= str_replace(
                        $arrColumn['fieldname'],
                        "IF($TableName.`{$arrColumn['fieldname']}` IS NOT NULL, IF($TableName.`{$arrColumn['fieldname']}` = 0, 'No', 'Yes'), '') AS `{$arrColumn['fieldname']}`",
                        $arrColumn['fieldname']
                    ) . ", ";
                }
                // else
                //     $ColumnKey .= str_replace("$arrColumn[fieldname]", "if($arrColumn[fieldname] is not null,if($arrColumn[fieldname]=0,'No','Yes'),'') as $arrColumn[fieldname], ", $arrColumn['fieldname']);
            } elseif ($arrColumn['uitype'] == 13) {
                $ColumnKey .= 'DATE_FORMAT('.$TableName.'.`' . $arrColumn['fieldname'] . '`,' . "'%d-%m-%Y %H:%i:%s'" . ') as `' . $arrColumn['fieldname'] . '`,';
                if ($keyword != '') {
                     $orwhere .= " OR DATE_FORMAT(" . $TableName . ".`" . $arrColumn['fieldname'] . "`, '%d-%m-%Y %H:%i:%s') 
                  LIKE '%" . addslashes($keyword) . "%'";
                    // $orwhere .= " or " . $unique_alias . ".`" .$arrColumn['fieldname'] . "` LIKE '%$keyword%'";
                }
            } elseif ($arrColumn['uitype'] == 15) {
                $ColumnKey .= 'DATE_FORMAT('.$TableName.'.`' . $arrColumn['fieldname'] . '`,' . "'%m/%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
                if ($keyword != '') {
                    // $orwhere .= " or " . $TableName . ".`" .$arrColumn['fieldname'] . "` LIKE '%$keyword%'";
                     $orwhere .= " OR DATE_FORMAT(" . $TableName . ".`" . $arrColumn['fieldname'] . "`, '%m/%Y') 
                  LIKE '%" . addslashes($keyword) . "%'";
                }
            } elseif ($arrColumn['uitype'] == 17) {
                //added on date 07-01-2026
                $ColumnKey .= 'DATE_FORMAT('.$TableName.'.`' . $arrColumn['fieldname'] . '`,' . "'%d-%m-%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
                if ($keyword != '') {
                    // $orwhere .= " or " . $TableName . ".`" .$arrColumn['fieldname'] . "` LIKE '%$keyword%'";
                     $orwhere .= " OR DATE_FORMAT(" . $TableName . ".`" . $arrColumn['fieldname'] . "`, '%d-%m-%Y') 
                  LIKE '%" . addslashes($keyword) . "%'";
                }
                //added on date 07-01-2026
                // $ColumnKey .= 'DATE_FORMAT(`' . $arrColumn['fieldname'] . '`,' . "'%d-%m-%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
            } elseif ($arrColumn['uitype'] == 19) {
                $ColumnKey .= 'DATE_FORMAT('.$TableName.'.`' . $arrColumn['fieldname'] . '`,' . "'%d/%m/%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
                if ($keyword != '') {
                    // $orwhere .= " or " . $TableName . ".`" .$arrColumn['fieldname'] . "` LIKE '%$keyword%'";
                     $orwhere .= " OR DATE_FORMAT(" . $TableName . ".`" . $arrColumn['fieldname'] . "`, '%d/%m/%Y') 
                  LIKE '%" . addslashes($keyword) . "%'";
                }
            } else {
                $ColumnKey .= $arrColumn['tablename'] . ".`" . $arrColumn['fieldname'] . "`,";
                if ($keyword != '') {
                    $orwhere .= " or " . $arrColumn['tablename'] . ".`" . $arrColumn['fieldname'] . "` LIKE '%$keyword%' ";
                }
            }
            if ($OrderBy == $arrColumn['fieldname'])
                $OrderBy = $arrColumn['tablename'] . "." . $OrderBy;
        }
        $ColumnKey = substr(trim($ColumnKey), 0, -1);


        $ColumnKey = "DISTINCT(" . $TableName . ".$FieldId) as RecordId," . $ColumnKey;
        if ($TableName == "`users`" and $OrderBy == '') {
            //echo $TableName;die;
            $OrderBy = "$TableName.$FieldId";
            $SortOrder = "DESC";
        } else if ($OrderBy == '' and $TableName != "`production`") {
            $OrderBy = "$TableName.$FieldId";
            $SortOrder = "DESC";
        } else if ($TableName == "`production`" and $OrderBy == '') {
            $OrderBy = "$TableName.productionid";
            $SortOrder = "DESC";
        }
        // echo $ColumnKey;die;

        $SourceModule = Yii::$app->request->get('sourcemodule');
        $SourceRecordId = Yii::$app->request->get('sourceid');
        $ModuleName = $this->moduleName;

        // print_r($_REQUEST['selectedRowIds']);
        // die;
        // added on 4 jan 2025 for export by deepika
        if (isset($_REQUEST['selectedRowIds']) && !empty($_REQUEST['selectedRowIds'])) {
            // Get the post data
            $selectedRowIds = Yii::$app->request->post('selectedRowIds');

            // Use array_map to filter and validate the integers
            $validRowIds = array_map(function ($value) {
                return filter_var($value, FILTER_VALIDATE_INT);
            }, $selectedRowIds);

            // Remove null values (invalid integers)
            $validRowIds = array_filter($validRowIds, function ($value) {
                return $value !== false;
            });

            // Convert the filtered values to a comma-separated string
            $validRowIdsString = implode(",", $validRowIds);

            $where .= " and ($TableName.$FieldId in ($validRowIdsString)) ";

            // Now $validRowIds contains only valid integers

        }
        // end added on 4 jan 2025 for export by deepika
        if ($SourceModule != "" and $SourceRecordId != "") {
            // $join .= " inner join EntityRel on (EntityRel.relentityid=$TableName.$FieldId and EntityRel.module='$SourceModule' and EntityRel.entityid=$SourceRecordId and EntityRel.relmodule='$ModuleName')";
            $getralatedkeys = $this->getralatedkeys($SourceModule);
            // print_r($getralatedkeys);
            // die;
            //first check reation table
            if (!empty($getralatedkeys) && count($getralatedkeys) == 1) {
                foreach ($getralatedkeys as $item) {
                    if ($item['related_fieldname'] == 'related_to')
                        $where .= " and $TableName.related_to='$SourceModule' and $TableName.related_to_id=$SourceRecordId";
                    else
                        $where .= " and $TableName." . $item['related_fieldname'] . "=" . $SourceRecordId;
                }
            } else
                $where .= " and $TableName.related_to='$SourceModule' and $TableName.related_to_id=$SourceRecordId";
        }
        //if column name have number add `` added on date 07-01-2026 
        $ColumnKey = preg_replace(
            '/\bas\s+([0-9][a-zA-Z0-9_]*)/i',
            'as `$1`',
            $ColumnKey
        );
        $where = preg_replace(
           '/\b([0-9][a-zA-Z0-9_]*)\b(?=\s+LIKE\b)/i',
            '`$1`',
            $where
        );
        $orwhere = preg_replace(
        '/\b([0-9][a-zA-Z0-9_]*)\b(?=\s+LIKE\b)/i',
        '`$1`',
        $orwhere
        );
        $join = preg_replace(
                '/((?:`?[a-zA-Z_][a-zA-Z0-9_]*`?)\.)\b([0-9][a-zA-Z0-9_]*)\b/',
                '$1`$2`',
                $join
            );
        // echo $join;die;
        //if column name have number add `` added on date 07-01-2026

        if (!empty($RecordId)) {
            $join .= " inner join user on (user.id=$TableName.ownerid)";
            $Query = "select $ColumnKey $join where $TableName.deleted=0 and 
            $FieldId=$RecordId";
            $Query = str_replace(",$TableName.ownerid", ",concat(first_name,' ',last_name) as ownerid", $Query);
            if ($keyword != '') {
                $orwhere .= " or ownerid LIKE '%$keyword%'";
            }
        } else {
            // added on 14 jan 2025 to open reference to all users   
            $isreference = 0;
            $recordlisting = new ListHire();
            //code added by ptpatel start from here on date 22-03-25
            $model = new AccessCheck();
            $id = Yii::$app->user->id;
            $tabs = $model->tabs($id, $ModuleName);
            $profile = $model->profile($id, $tabs, $ModuleName);
            $modelaccess = $model->moduleaccess($id, $profile, $tabs);
            $rolebasedrecord = $model->rolebasedrecord($id, $profile);
            $hasadminpower = $model->hasadminpower($profile);

            //this code is for alloed single edit in listview table cell
            //0 not allowed 1= allowed
            // 4,5,6,9 this is leadstatus which is not allowed to edit in listview

            // if ($id) {
            //     if ($TableName == '`leadinformation`') {
            //         $ColumnKey .= ", IF ((" . $TableName . ".ownerid = " . $id . " || $hasadminpower = 1) , 
            //         IF ((" . $TableName . ".converted = 1 || $TableName .leadstatus IN (4,5,6,9)) , '0' , '1')  , '0')";
            //     } else {
            //         $ColumnKey .= " IF ((" . $TableName . ".ownerid = " . $id . " || $hasadminpower = 1) , '1' , '0')";
            //     }
            //     $ColumnKey .= ' as isEdit ';
            // }
            // echo $ColumnKey;die;
            //code added by ptpatel end here on date 22-03-25
            // if($orwhere != "")
                // $where .= " 1=1 ".$orwhere  ;
            $Query = $recordlisting->listing($roleid, $modulepermission, $Query, $ColumnKey, $join, $OrderBy, $SortOrder, $TableName, $groupby, $isreference, $ModuleName, $where, $orwhere);
            // echo $Query;die;
            // echo $orwhere; die;
            // if ($orwhere != '(')
            //     $Query = preg_replace('/\bORDER BY\b/i', "And " . preg_replace('/\bor\b/i', '', $orwhere, 1) . ") ORDER BY", $Query, 1);
           // below if else condition is added by ptpatel on date 28-03-25 it requires to append orwhere condition because meeting and task has two order by word
            // $occurrences = substr_count(strtoupper($Query), strtoupper('ORDER BY'));
            // if ($occurrences == 1) {
            //     if ($orwhere != '(')
            //     $Query = preg_replace('/\bORDER BY\b/i', "And " . preg_replace('/\bor\b/i', '', $orwhere, 1) . ") ORDER BY", $Query, 1);     
            // }
            // else
            // {
            //     if ($orwhere != '(') {
            //         $orwhere = trim($orwhere);
                
            //         if (stripos($Query, 'WHERE') !== false) {
            //             // Append $orwhere using AND before GROUP BY
            //             $Query = preg_replace('/\bGROUP BY\b/i', " AND ($orwhere) GROUP BY", $Query, 1);
            //         } else {
            //             $Query = preg_replace('/\bGROUP BY\b/i', " WHERE $orwhere GROUP BY", $Query, 1);
            //         }
            //         $Query = str_replace('(( or', '( ', $Query);
            //     }  
            // }
            //  echo $orwhere; die;
            // echo $Query;die;
            $connection = Yii::$app->db;
            $pagination = new Pageination();
            $totalitemcount = $pagination->TotalRecords($Query);
            $pageEndRange = $totalitemcount['defaultrecord'];
            // echo "<pre>";print_r($_REQUEST);exit;
            if (isset($_REQUEST['pageNumber']) && $_REQUEST['pageNumber'] != '') {
                $pageStartRange = $totalitemcount['pageStartRange'];
            } else if (isset($_REQUEST['pageNumberpre']) && $_REQUEST['pageNumberpre'] != '') {
                $pageStartRange = $totalitemcount['pageStartRange'];
            } else if (isset($_REQUEST['pagejump']) && $_REQUEST['pagejump'] != '') {
                $pageStartRange = $totalitemcount['pageStartRange'];
            } else {
                $pageStartRange = '0';
            }
            $query_res = $Query;

            // Get pagination parameters from the request

            if (!empty($_REQUEST['start'])) {
                // Sanitize the limit value to ensure it's a valid number
                $start = filter_var(Yii::$app->request->get('start'), FILTER_VALIDATE_INT);

                if ($start !== false && $start > 0) {
                    $pageStartRange = $start; // Limit (number of records), default to 10
                } else {
                    $pageStartRange = 10; // Fallback to default value
                }
            }
            if (!empty($_REQUEST['limit'])) {
                // Sanitize the limit value to ensure it's a valid number
                $limit = filter_var(Yii::$app->request->get('limit'), FILTER_VALIDATE_INT);

                if ($limit !== false && $limit > 0) {
                    $pageEndRange = $limit; // Limit (number of records), default to 10
                } else {
                    $pageEndRange = 10; // Fallback to default value
                }
            }
            // added on 14 jan 2025 by deepika for export
            if (isset($_REQUEST['selectedRowIds']) && !empty($_REQUEST['selectedRowIds'])) {
                $Query = "$query_res "; //when agination
            } else
                $Query = "$query_res limit $pageStartRange,$pageEndRange"; //when agination
            // echo $Query;die;
            //redefine query with $orwhere condition it will add and befor $orwhere and remove or from front
            // if ($orwhere != '(')
            //     $Query = preg_replace('/\bORDER BY\b/i', "And " . preg_replace('/\bor\b/i', '', $orwhere, 1) . ") ORDER BY", $Query, 1);
        }
        // echo $Query;
        //     die;
        // echo "<br>Query=$Query";
        // die;

        return array($Column, $Query, $totalitemcount);
    }
    public function getPickListDetail($fieldid)
    {

        $connection = Yii::$app->db;
        $command = $connection->createCommand(" select targettable,targetfield,dispfield
             from picklist where fieldid=:fieldid")
            ->bindParam(':fieldid', $fieldid);
        $Columns = $command->queryOne();

        return $Columns;
    }
    public function getReferenceEntityNameDetail($fieldid)
    {

        $connection = Yii::$app->db;
        $command = $connection->createCommand("select targettable,entityidfield,fieldname from `entityname`  where fieldid=:fieldid")->bindParam(':fieldid', $fieldid);
        $Columns = $command->queryOne();
        return $Columns;
    }
    public function getReferenceEntityNameDetailMultiple($fieldid)
    {
        // echo "select modulename,targettable,entityidfield,fieldname from `entityname`  where fieldid=$fieldid group by modulename";die;

        $connection = Yii::$app->db;
        $command = $connection->createCommand("select modulename,targettable,entityidfield,fieldname from `entityname`  where fieldid=:fieldid group by modulename")->bindParam(':fieldid', $fieldid);
        $Columns = $command->queryAll();
        return $Columns;
    }
    public function getRelatedDetail($fieldid)
    {

        $connection = Yii::$app->db;
        $command = $connection->createCommand("select  	mrelatedto_value from `mrelated_to`  where mrelatedtoid=:fieldid")->bindParam(':fieldid', $fieldid);
        $Columns = $command->queryOne();
        return $Columns;
    }
    public function getralatedkeys($SourceModule)
    {
        $connection = Yii::$app->db;

        //         echo "SELECT tablabel as modulename,source_module,related_fieldname,related_recordfieldnme 
        // FROM module_relation
        // join tab on tab.tabid = module_relation.source_module
        // WHERE related_module=(SELECT tabid from tab where name = '$this->moduleName') and  module_relation.source_module=$SourceModule";die;

        $arr_tab = Yii::$app->db
            ->createCommand("SELECT tablabel as modulename,source_module,related_fieldname,related_recordfieldnme 
FROM module_relation
join tab on tab.tabid = module_relation.source_module
WHERE related_module=(SELECT tabid from tab where name = :relatedmodule) and  module_relation.source_module=:SourceModule")
            ->bindValue(":relatedmodule", $this->moduleName)
            ->bindValue(":SourceModule", $SourceModule)
            ->queryAll();
        // $arr_tab = Yii::$app->db->createCommand()
        // ->select()
        // ->from('tab')
        // ->where('name =:name', array(':name' =>$ModuleName))
        // ->queryRow();
        return $arr_tab;
    }

    /**Below function added by ptpatel on date 30-08-2025 to resolve issue if logged in user id not found in customview then it get it from admin  */
    public function getSearchAllColumnList($isQuick = false)
    {
        $table_name = $this->tableName();
        $connection = Yii::$app->db;

        if ($isQuick) {
            // Fetch more columns for better search accuracy, but still limited
            $query = "SELECT fieldid, columnname AS fieldname, fieldlabel, uitype, tablename
                      FROM field 
                      WHERE tablename = :tablename 
                      AND (list_view = 1 OR uitype = 1 OR uitype = 2) 
                      ORDER BY sequence ASC";
            $command = $connection->createCommand($query)->bindValue(':tablename', $table_name);
        } else {
            $command = $connection->createCommand("
            SELECT field.fieldid, field.columnname AS fieldname, field.fieldlabel, field.uitype, field.tablename
            FROM field 
            WHERE field.tablename = :tablename 
            AND field.list_view = 1 
            ORDER BY sequence
            ")
            ->bindValue(':tablename', $table_name);
        }

            // field.detail_view = 1 OR
            //         field.create_view = 1 OR  
            //         field.edit_view = 1
        $ColumnList = $command->queryAll();
        // Skip slow SHOW COLUMNS check in quick mode to save time
        if ($isQuick) {
            return $ColumnList;
        }
        
        // For full mode, still perform the check but efficiently
        $validColumns = [];
        $actualColumns = Yii::$app->db->createCommand("SHOW COLUMNS FROM `$table_name`")->queryColumn();
        foreach ($ColumnList as $field) {
            if (in_array($field['fieldname'], $actualColumns)) {
                $validColumns[] = $field;
            }
        }
        return $validColumns;
    }

    public function getsearchAllListRecord($tableName, $keyword, $OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '', $isQuick = false)
    {
        $ColumnList = $this->getSearchAllColumnList($isQuick);
        // print_r($ColumnList);die;
        list($Column, $ListQuery, $totalitemcount) = $this->getQuery($tableName, $keyword, $ColumnList, $OrderBy, $SortOrder, $rolebasedrecord, $modulepermission);
        // echo $ListQuery;die;
        $RecordList = Yii::$app->db->createCommand($ListQuery)->queryAll();
        return array($Column, $RecordList, $totalitemcount);
    }
    /**code ended for function added by ptpatel on date 30-08-2025 to resolve issue if logged in user id not found in customview then it get it from admin  */
    function getTableAlias($table, &$tableAliasMap)
    {
        if (!isset($tableAliasMap[$table])) {
            $tableAliasMap[$table] = 1;
            // return $table; // first time → no alias suffix
            return $table . $tableAliasMap[$table];
        }

        $tableAliasMap[$table]++;
        return $table . $tableAliasMap[$table]; // contacts2, contacts3
    }
}
