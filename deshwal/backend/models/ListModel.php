<?php

namespace app\models;

use backend\models\AccessCheck;
use common\models\Blocks;
use Yii;
use yii\filters\AccessRule;
use yii\helpers\ArrayHelper;

/**
 * ListModel class.
 * ListModel is the data structure for keeping
 * ListModel form data. It is used by the 'Module' action of 'Controller'.
 */
class ListModel extends \yii\db\ActiveRecord
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
    public function getListRecord($OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '')
    {
        $ColumnList = $this->getColumnList();
        // print_r($ColumnList);die;
        list($Column, $ListQuery, $totalitemcount) = $this->getQuery($ColumnList, $OrderBy, $SortOrder, $rolebasedrecord, $modulepermission);
        //echo "dd";die;

        $RecordList = Yii::$app->db->createCommand($ListQuery)
            ->queryAll();
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

    public function getQuery($ColumnList, $OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '')
    {

        $FieldId = $this->fieldId;
        $TableName = "`" . $this->tableName() . "`";
        $RecordId = $this->_members[$FieldId];
        $ColumnKey = "";
        $roleid = $rolebasedrecord;
        $Query = '';
        $groupby = '';
        // echo "<br>role id=";
        // echo gettype($rolebasedrecord['userid']); 
        // print_r($roleid);
        // die;
        $join = "from $TableName";
        //$join="from Entity inner join $TableName on(Entity.entityid=$TableName.$FieldId)";
        $Column = array();
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

                        $ColumnKey .= "concat(user" . $arrColumn['fieldname'] . '.first_name," ",user' . $arrColumn['fieldname'] . ".last_name) as `" . $arrColumn['fieldname'] . "`,";
                        $join .= " left join `user` as user" . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=user" . $arrColumn['fieldname'] . ".id)";
                    } else if ($PickListDetail['targettable'] == 'tab') {


                        $ColumnKey .= 'UPPER(' . $PickListDetail['targettable'] . '.' . $PickListDetail['dispfield'] . ') as `' . $arrColumn['fieldname'] . "`,";
                        $join .= " left join " . $PickListDetail['targettable'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $PickListDetail['targettable'] . "." . $PickListDetail["targetfield"] . ")";
                    } else {


                        $ColumnKey .= $PickListDetail['targettable'] . $arrColumn['fieldname'] . '.' . $PickListDetail['dispfield'] . ' as `' . $arrColumn['fieldname'] . "`,";
                        $join .= " left join " . $PickListDetail['targettable'] . " as " . $PickListDetail['targettable'] . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $PickListDetail['targettable'] . $arrColumn['fieldname'] . "." . $PickListDetail["targetfield"] . ")";
                    }
                }
            } else if ($arrColumn['uitype'] == 53) {
                /*$PickList=new PickList;   
                    $PickList->fieldid=$Field->fieldid;
                    $BlockDetail->Fields[$FieldKey]->fieldoptions=$PickList->getPickListValue();*/


                $ColumnKey .= "user" . $arrColumn['fieldname'] . '.username as ' . $arrColumn['fieldname'] . ",";
                $join .= " left join `user` as user" . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=user" . $arrColumn['fieldname'] . ".id)";
            } else if ($arrColumn['uitype'] == 22 || $arrColumn['uitype'] == 9) {
                $PickListDetail = $this->getPickListDetail($arrColumn['fieldid']);
                $targettable = $PickListDetail['targettable'];
                $targetfield = $PickListDetail['targetfield'];
                $dispfield = $PickListDetail['dispfield'];
                if ( //this condition added by ptpatel to resolve daily report mail on date 23-02-2026
                    $PickListDetail['targettable'] === 'contacts'
                    && $this->tableName() === 'meeting_information'
                ) {
                    $alias = $PickListDetail['targettable'];

                    $ColumnKey .= "
                        GROUP_CONCAT(
                            DISTINCT CONCAT(
                                $alias.first_name,
                                ' ',
                                IFNULL($alias.last_name, '')
                            )
                            ORDER BY $alias.contacts_id
                            SEPARATOR ', '
                        ) AS {$arrColumn['fieldname']},
                    ";
                } else if ($PickListDetail['targettable'] != 'user') {
                    $ColumnKey .= "GROUP_CONCAT(" . $PickListDetail['targettable'] . "." . $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
                } else {
                    // $ColumnKey .= "GROUP_CONCAT(" . $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
                    //DISTINCT added by ptpatel on date 23-02-2026 to remove issue of repeat users in internal participants in meeting 
                    $ColumnKey .= "GROUP_CONCAT(DISTINCT " . $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
                }
                // $join .= " left join " . $PickListDetail['targettable'] . " on FIND_IN_SET(" . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . "," . $TableName . "." . $arrColumn['fieldname'] . ")";
                //added by ptpatel on date 23-02-2026 to resolve 122, 134 like field value
                $join .= " left join " . $PickListDetail['targettable'] . " on FIND_IN_SET(" . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . ",REPLACE(" . $TableName . "." . $arrColumn['fieldname'] . ", ' ', ''))";
                if($targettable == 'role' && $targetfield == 'roleid' && $dispfield == 'rolename'){
                    $groupby = "Group By $TableName.$FieldId";
                }else{
                    $groupby = "Group By $FieldId";
                }
            } else if ($arrColumn['uitype'] == 12 || $arrColumn['uitype'] == 27 || $arrColumn['uitype'] == 28) {
                $getEntityNameDetail = $this->getReferenceEntityNameDetail($arrColumn['fieldid']);
                if ($getEntityNameDetail) {
                    $targettable = $getEntityNameDetail['targettable'];
                    $targetfield = $getEntityNameDetail['entityidfield'];
                    $dispfield = $getEntityNameDetail['fieldname'];
                    $ColumnKey .= $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . "." . $dispfield . " as " . $arrColumn['fieldname'] . ",";

                    $join .= " LEFT OUTER JOIN " . $getEntityNameDetail['targettable'] . " as " . $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . "." . $getEntityNameDetail['entityidfield'] . ")";
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
            } else if ($arrColumn['uitype'] == 5) {
                $unique_alias = "attachments" . $arrColumn['fieldname'];
                $ColumnKey .= "$unique_alias.name " . " as " . $arrColumn['fieldname'] . ",";


                $join .= " LEFT OUTER JOIN `attachments` as $unique_alias " . " on (" . $TableName . "." . $arrColumn['fieldname'] . "= $unique_alias.attachmentsid)";
            } elseif ($arrColumn['uitype'] == 6) {
                if ($arrColumn['fieldname'] == 'is_admin' && $arrColumn['tablename'] == "user")
                    $ColumnKey .= "if(user.is_admin is not null,if(user.is_admin=0,'No','Yes'),'') as is_admin,";
                else
                    $ColumnKey .= str_replace("$arrColumn[fieldname]", "if($arrColumn[fieldname] is not null,if($arrColumn[fieldname]=0,'No','Yes'),'') as $arrColumn[fieldname], ", $arrColumn['fieldname']);
            } elseif ($arrColumn['uitype'] == 13) {
                $ColumnKey .= 'DATE_FORMAT(' . $TableName . "." .$arrColumn['fieldname'] . ',' . "'%d-%m-%Y %H:%i:%s'" . ') as `' .  $arrColumn['fieldname']  . '`,';
            } elseif ($arrColumn['uitype'] == 15) {
                $ColumnKey .= 'DATE_FORMAT(' . $TableName . "." .$arrColumn['fieldname'] . ',' . "'%m/%Y'" . ') as `' .$arrColumn['fieldname'] . '`,';
            } elseif ($arrColumn['uitype'] == 17) {
                $ColumnKey .= 'DATE_FORMAT(' . $TableName . "." .$arrColumn['fieldname'] . ',' . "'%d-%m-%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
            } elseif ($arrColumn['uitype'] == 19) {
                $ColumnKey .= 'DATE_FORMAT(' . $TableName . "." .$arrColumn['fieldname'] . ',' . "'%d/%m/%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
            } 
            //code added by ptpatel on date 01-11-2025 for refrence number with , seperated value
            else if ($arrColumn['uitype'] == 31) {

                    $getEntityNameDetail = $this->getReferenceEntityNameDetail($arrColumn['fieldid']);
                    if ($getEntityNameDetail) {
                        $targettable = $getEntityNameDetail['targettable'];        // e.g. salesorder_dit
                        $targetfield = $getEntityNameDetail['entityidfield'];      // e.g. salesorder_dit_id
                        $dispfield   = $getEntityNameDetail['fieldname'];          // e.g. salesorder_dit_no
                        $alias       = $targettable . $arrColumn['fieldname'];     // e.g. salesorder_ditreference_number

                        // SELECT COLUMN with GROUP_CONCAT (for SO numbers)
                        $ColumnKey .= "GROUP_CONCAT(DISTINCT {$alias}.{$dispfield} 
                                            ORDER BY {$alias}.{$dispfield} 
                                            SEPARATOR ', ') AS {$arrColumn['fieldname']},";

                        // JOIN CONDITION using FIND_IN_SET for multi-IDs
                        $join .= " LEFT JOIN {$targettable} AS {$alias}
                                    ON FIND_IN_SET({$alias}.{$targetfield}, {$TableName}.{$arrColumn['fieldname']})";
                    }

            } 
            //end code added by ptpatel on date 01-11-2025
            else {
                $ColumnKey .= $arrColumn['tablename'] . "." . $arrColumn['fieldname'] . ",";
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
        $where = '1=1';
        // print_r($_REQUEST['selectedRowIds']);
        // die;
        // added on 4 jan 2025 for export by deepika
        if (isset($_REQUEST['selectedRowIds']) && !empty($_REQUEST['selectedRowIds'])) {
            // Get the post data
            $selectedRowIds = Yii::$app->request->post('selectedRowIds');

            //if condition added by ptpatel to export all data into inventory
            if ($_REQUEST['selectedRowIds'] != 'all') {
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
        //widget filter code   
        $widgetid = Yii::$app->request->get('widgetid');
        if ($widgetid != "") {
            $connection = Yii::$app->db;
            if (strpos($widgetid, '_') !== false) {
                list($widgetid, $stageid) = explode('_', $widgetid, 2);
                if($widgetid == 15) //15 sourcingdeal stage wise count
                {
                    $sql_filter = "Select * from widgets_filter where id = '$widgetid'";
                    $command = $connection->createCommand($sql_filter);
                    $result = $command->queryOne();
                    $where .= " " . $result['default_condition']. " ".$stageid;
                    // $join .= ' JOIN (
                    //         SELECT 
                    //             s.sourcingdeal_id,
                    //             s.stage_id,
                    //             ROW_NUMBER() OVER (PARTITION BY s.sourcingdeal_id ORDER BY s.id DESC) AS rn
                    //         FROM rep_soucingdeal_stage_log s
                    //         WHERE s.updatetime IS NULL AND s.sourcingdeal_id NOT IN (SELECT sourcingdeal_id from sourcingdeal where deleted = 1 OR is_temp = 1)
                    //     ) AS replog
                    //     ON replog.sourcingdeal_id = sourcingdeal.sourcingdeal_id 
                    //     AND replog.rn = 1';
                }
                ////16 Payment Approved Account wise age wise amount (Numeric Chart) (0-7/ 8-15/>15)
                //17 Payment approved with Client Name Count And Amount (Ageing 0-10 Days,11-15 Days, >15 Days)
                else if($widgetid == 16 || $widgetid == 17) 
                {
                    $sql_filter = "Select * from widgets_filter where id = '$widgetid'";
                    $command = $connection->createCommand($sql_filter);
                    $result = $command->queryOne();
                    $where .= " " . $result['default_condition']. " ".$stageid;
                }
            } else {
                $sql_filter = "Select * from widgets_filter where id = '$widgetid'";
                $command = $connection->createCommand($sql_filter);
                $result = $command->queryOne();
                $where .= " " . $result['default_condition'];
            }
        }
        // echo $Query;die;
        //end widget filter code
        if (!empty($RecordId)) {
            $join .= " inner join user on (user.id=$TableName.ownerid)";
            $Query = "select $ColumnKey $join where $TableName.deleted=0 and 
            $FieldId=$RecordId";
            $Query = str_replace(",$TableName.ownerid", ",concat(first_name,' ',last_name) as ownerid", $Query);
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
            if ($id) {
                if ($TableName == '`leadinformation`') {
                    $ColumnKey .= ", IF ((" . $TableName . ".ownerid = " . $id . " || $hasadminpower = 1) , 
                    IF ((" . $TableName . ".converted = 1 || $TableName .leadstatus IN (4,5,6,9)) , '0' , '1')  , '0')";
                } else {
                    $ColumnKey .= ", IF ((" . $TableName . ".ownerid = " . $id . " || $hasadminpower = 1) , '1' , '0')";
                }
                $ColumnKey .= ' as isEdit ';
            } 
            $ColumnKey = preg_replace('/,\s*,/', ',', $ColumnKey);  
            //code added by ptpatel end here on date 22-03-25
            $Query = $recordlisting->listing($roleid, $modulepermission, $Query, $ColumnKey, $join, $OrderBy, $SortOrder, $TableName, $groupby, $isreference, $ModuleName, $where);
            //code added by ptpatel on date 01-11-2025
            if($TableName == "`purchase_order_dit`")
            {
                $groupBy = " GROUP BY {$TableName}.{$this->fieldId} "; // example: purchase_order_dit.purchaseorder_dit_id

                // Find position of ORDER BY (case-insensitive)
                $pos = strpos($Query, "order by");
                // echo $pos;die;
                if ($pos !== false) {
                    // Insert GROUP BY before ORDER BY
                    $Query = substr_replace($Query, $groupBy, $pos, 0);
                } 
            }
            //code ended added by ptpatel on date 01-11-2025
            // echo "<br>Query=$Query";
            // die;
            $connection = Yii::$app->db;
            $pagination = new Pageination();
            $totalitemcount = $pagination->TotalRecords($Query);
            $pageEndRange = $totalitemcount['defaultrecord'];
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
            // echo $_REQUEST['limit'];die;
            // added on 14 jan 2025 by deepika for export
            if (isset($_REQUEST['selectedRowIds']) && !empty($_REQUEST['selectedRowIds'])) {
                $Query = "$query_res "; //when agination
            } else
                $Query = "$query_res limit $pageStartRange,$pageEndRange"; //when agination

            // echo $Query;
            // die;
            // $Query = "$query_res";
            //$recordlisting=new ListHire();
            //$Query=$recordlisting->listing($roleid,$modulepermission,$Query,$ColumnKey,$join,$OrderBy,$SortOrder,$TableName);
        }
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

    //function added by ptpatel on date 08-04-25
    public function getKanbanListRecord($OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '', $wherecondition = '')
    {
        $ColumnList = $this->getColumnList();
        // print_r($ColumnList);die;
        list($Column, $ListQuery, $totalitemcount) = $this->getKanbanQuery($ColumnList, $OrderBy, $SortOrder, $rolebasedrecord, $modulepermission, $wherecondition);
        //echo "dd";die;

        $RecordList = Yii::$app->db->createCommand($ListQuery)
            ->queryAll();
        return array($Column, $RecordList, $totalitemcount);
    }
    //function added by ptpatel on date 08-04-25
    public function getKanbanQuery($ColumnList, $OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '', $wherecondition = '')
    {

        $FieldId = $this->fieldId;
        $TableName = "`" . $this->tableName() . "`";
        $RecordId = $this->_members[$FieldId];
        $ColumnKey = "";
        $roleid = $rolebasedrecord;
        $Query = '';
        $groupby = '';
        // echo "<br>role id=";
        // echo gettype($rolebasedrecord['userid']); 
        // print_r($roleid);
        // die;
        $join = "from $TableName";
        //$join="from Entity inner join $TableName on(Entity.entityid=$TableName.$FieldId)";
        $Column = array();
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

                        $ColumnKey .= "concat(user" . $arrColumn['fieldname'] . '.first_name," ",user' . $arrColumn['fieldname'] . ".last_name) as `" . $arrColumn['fieldname'] . "`,";
                        $join .= " left join `user` as user" . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=user" . $arrColumn['fieldname'] . ".id)";
                    } else if ($PickListDetail['targettable'] == 'tab') {


                        $ColumnKey .= 'UPPER(' . $PickListDetail['targettable'] . '.' . $PickListDetail['dispfield'] . ') as `' . $arrColumn['fieldname'] . "`,";
                        $join .= " left join " . $PickListDetail['targettable'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $PickListDetail['targettable'] . "." . $PickListDetail["targetfield"] . ")";
                    } else {


                        $ColumnKey .= $PickListDetail['targettable'] . $arrColumn['fieldname'] . '.' . $PickListDetail['dispfield'] . ' as `' . $arrColumn['fieldname'] . "`,";
                        $join .= " left join " . $PickListDetail['targettable'] . " as " . $PickListDetail['targettable'] . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $PickListDetail['targettable'] . $arrColumn['fieldname'] . "." . $PickListDetail["targetfield"] . ")";
                    }
                }
            } else if ($arrColumn['uitype'] == 53) {
                /*$PickList=new PickList;   
                    $PickList->fieldid=$Field->fieldid;
                    $BlockDetail->Fields[$FieldKey]->fieldoptions=$PickList->getPickListValue();*/


                $ColumnKey .= "user" . $arrColumn['fieldname'] . '.username as ' . $arrColumn['fieldname'] . ",";
                $join .= " left join `user` as user" . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=user" . $arrColumn['fieldname'] . ".id)";
            } else if ($arrColumn['uitype'] == 22 || $arrColumn['uitype'] == 9) {
                $PickListDetail = $this->getPickListDetail($arrColumn['fieldid']);
                $targettable = $PickListDetail['targettable'];
                $targetfield = $PickListDetail['targetfield'];
                $dispfield = $PickListDetail['dispfield'];
                if ($PickListDetail['targettable'] != 'user') {
                    $ColumnKey .= "GROUP_CONCAT(" . $PickListDetail['targettable'] . "." . $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
                } else {
                    $ColumnKey .= "GROUP_CONCAT(" . $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
                }
                $join .= " left join " . $PickListDetail['targettable'] . " on FIND_IN_SET(" . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . "," . $TableName . "." . $arrColumn['fieldname'] . ")";



                $groupby = "Group By $FieldId";
            } else if ($arrColumn['uitype'] == 12 || $arrColumn['uitype'] == 27 || $arrColumn['uitype'] == 28) {
                $getEntityNameDetail = $this->getReferenceEntityNameDetail($arrColumn['fieldid']);
                if ($getEntityNameDetail) {
                    $targettable = $getEntityNameDetail['targettable'];
                    $targetfield = $getEntityNameDetail['entityidfield'];
                    $dispfield = $getEntityNameDetail['fieldname'];
                    $ColumnKey .= $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . "." . $dispfield . " as " . $arrColumn['fieldname'] . ",";

                    $join .= " LEFT OUTER JOIN " . $getEntityNameDetail['targettable'] . " as " . $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . "." . $getEntityNameDetail['entityidfield'] . ")";
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
            } else if ($arrColumn['uitype'] == 5) {
                $unique_alias = "attachments" . $arrColumn['fieldname'];
                $ColumnKey .= "$unique_alias.name " . " as " . $arrColumn['fieldname'] . ",";


                $join .= " LEFT OUTER JOIN `attachments` as $unique_alias " . " on (" . $TableName . "." . $arrColumn['fieldname'] . "= $unique_alias.attachmentsid)";
            } elseif ($arrColumn['uitype'] == 6) {
                if ($arrColumn['fieldname'] == 'is_admin' && $arrColumn['tablename'] == "user")
                    $ColumnKey .= "if(user.is_admin is not null,if(user.is_admin=0,'No','Yes'),'') as is_admin,";
                else
                    $ColumnKey .= str_replace("$arrColumn[fieldname]", "if($arrColumn[fieldname] is not null,if($arrColumn[fieldname]=0,'No','Yes'),'') as $arrColumn[fieldname], ", $arrColumn['fieldname']);
            } elseif ($arrColumn['uitype'] == 13) {
                $ColumnKey .= 'DATE_FORMAT(`' . $arrColumn['fieldname'] . '`,' . "'%d-%m-%Y %H:%i:%s'" . ') as `' . $arrColumn['fieldname'] . '`,';
            } elseif ($arrColumn['uitype'] == 15) {
                $ColumnKey .= 'DATE_FORMAT(`' . $arrColumn['fieldname'] . '`,' . "'%m/%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
            } elseif ($arrColumn['uitype'] == 17) {
                $ColumnKey .= 'DATE_FORMAT(`' . $arrColumn['fieldname'] . '`,' . "'%d-%m-%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
            } elseif ($arrColumn['uitype'] == 19) {
                $ColumnKey .= 'DATE_FORMAT(`' . $arrColumn['fieldname'] . '`,' . "'%d/%m/%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
            } else {
                $ColumnKey .= $arrColumn['tablename'] . "." . $arrColumn['fieldname'] . ",";
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
        $where = '1=1';
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
        if (!empty($RecordId)) {
            $join .= " inner join user on (user.id=$TableName.ownerid)";
            $Query = "select $ColumnKey $join where $TableName.deleted=0 and 
            $FieldId=$RecordId";
            $Query = str_replace(",$TableName.ownerid", ",concat(first_name,' ',last_name) as ownerid", $Query);
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
            if ($id) {
                if ($TableName == '`leadinformation`') {
                    $ColumnKey .= ", IF ((" . $TableName . ".ownerid = " . $id . " || $hasadminpower = 1) , 
                    IF ((" . $TableName . ".converted = 1 || $TableName .leadstatus IN (4,5,6,9)) , '0' , '1')  , '0')";
                } else {
                    $ColumnKey .= ", IF ((" . $TableName . ".ownerid = " . $id . " || $hasadminpower = 1) , '1' , '0')";
                }
                $ColumnKey .= ' as isEdit ';
            }
            // if ($TableName == '`leadinformation`') {
            //     $OrderBy =" leadinformation.leadstatus";
            // }
            if ($where != '' && $TableName == '`leadinformation`' && $wherecondition != '') {
                $where = " leadstatus = $wherecondition ";
            }
            $Query = 'WITH ranked_leads AS (';
            //code added by ptpatel end here on date 22-03-25
            $Query .= $recordlisting->listing($roleid, $modulepermission, $Query, $ColumnKey, $join, $OrderBy, $SortOrder, $TableName, $groupby, $isreference, $ModuleName, $where);
            if ($TableName == '`leadinformation`') {
                $Query .= ')
                     SELECT *
                     FROM ranked_leads
                    ORDER BY leadstatus DESC';
            }
            // echo "<br>Query=$Query";

            // die;
            // $Query = "WITH ranked_leads AS (
            //         SELECT
            //             `leadinformation`.leadid AS RecordId,
            //             lead_statusleadstatus.leadstatus_value AS leadstatus,
            //             leadinformation.leadname,
            //             leadinformation.address,
            //             citycity.city_name AS city,
            //             lead_sourcelead_source.leadsource_value AS lead_source,
            //             leadinformation.website,
            //             IF ((`leadinformation`.ownerid = 1 || 1 = 1),
            //                 IF ((`leadinformation`.converted = 1 || `leadinformation`.leadstatus IN (4,5,6,9)), '0', '1'),
            //                 '0'
            //             ) AS isEdit 

            //         FROM `leadinformation`
            //         LEFT JOIN lead_status AS lead_statusleadstatus ON (`leadinformation`.leadstatus = lead_statusleadstatus.leadstatusid)
            //         LEFT JOIN city AS citycity ON (`leadinformation`.city = citycity.cityid)
            //         LEFT JOIN lead_source AS lead_sourcelead_source ON (`leadinformation`.lead_source = lead_sourcelead_source.leadsourceid)
            //         INNER JOIN user AS owner ON (owner.id = `leadinformation`.ownerid)
            //         WHERE `leadinformation`.deleted = 0
            //         )
            //         SELECT *
            //         FROM ranked_leads
            //         ORDER BY leadstatus, RecordId DESC";

            // return array($Column, $Query, '');
            $connection = Yii::$app->db;
            $pagination = new Pageination();
            $totalitemcount = $pagination->TotalRecords($Query);
            $pageEndRange = $totalitemcount['defaultrecord'];
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
            // echo $_REQUEST['limit'];die;
            // added on 14 jan 2025 by deepika for export
            if (isset($_REQUEST['selectedRowIds']) && !empty($_REQUEST['selectedRowIds'])) {
                $Query = "$query_res "; //when agination
            } else
                $Query = "$query_res limit $pageStartRange,$pageEndRange"; //when agination

            // echo $Query;
            // die; 
            // $Query = "$query_res";
            //$recordlisting=new ListHire();
            //$Query=$recordlisting->listing($roleid,$modulepermission,$Query,$ColumnKey,$join,$OrderBy,$SortOrder,$TableName);
        }
        // echo "AS";
        // echo "<pre>";print_r($totalitemcount);die;
        // echo "<br>Query=$Query";
        // die;

        return array($Column, $Query, $totalitemcount);
    }
    /**code for export all start from here */
    /**to get export = 1 column function added by ptpatel on date 31-07-25 */
    public function getExportAllRecord($OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '')
    {
        $ColumnList = $this->getExportAllList();
        // print_r($ColumnList);die;
        list($Column, $ListQuery, $totalitemcount) = $this->getQueryforExportAll($ColumnList, $OrderBy, $SortOrder, $rolebasedrecord, $modulepermission);
        //echo "dd";die;

        $RecordList = Yii::$app->db->createCommand($ListQuery)
            ->queryAll();
        return array($Column, $RecordList, $totalitemcount);
    }
    public function getExportAllList()
    {
        $connection = Yii::$app->db;
        $tablename = $this->tableName;
        // echo $tablename;die;
        $tabId = $this->getTabDetail($this->moduleName)['tabid'];
        $blocks = Blocks::findAll(['tabid' => $tabId,'display_status'=>1]);
        $blockIds = ArrayHelper::getColumn($blocks, 'blockid');
        $command = $connection->createCommand("
             SELECT field.fieldid, field.columnname AS fieldname, field.fieldlabel, field.uitype, field.tablename,field.sequence,field.block
             FROM field 
             WHERE field.tabid = :tabid And 
             detail_view = 1 AND tablename = :tablename AND block IN (" . implode(',', $blockIds) . ") ORDER BY field.block,field.sequence")
            ->bindValue(':tabid', $tabId)
            ->bindValue(':tablename', $tablename);

        $ColumnList = $command->queryAll();
        return $ColumnList;
    }

    public function getQueryforExportAll($ColumnList, $OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '')
    {

        $FieldId = $this->fieldId;
        $TableName = "`" . $this->tableName() . "`";
        $RecordId = $this->_members[$FieldId];
        $ColumnKey = "";
        $roleid = $rolebasedrecord;
        $Query = '';
        $groupby = '';
        $aliasno = 1;
        // echo "<br>role id=";
        // echo gettype($rolebasedrecord['userid']); 
        // print_r($roleid);
        // die;
        $join = "from $TableName";
        //$join="from Entity inner join $TableName on(Entity.entityid=$TableName.$FieldId)";
        $Column = array();
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

                        $ColumnKey .= "concat(user" . $arrColumn['fieldname'] . '.first_name," ",user' . $arrColumn['fieldname'] . ".last_name) as `" . $arrColumn['fieldname'] . "`,";
                        $join .= " left join `user` as user" . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=user" . $arrColumn['fieldname'] . ".id)";
                    } else if ($PickListDetail['targettable'] == 'tab') {


                        $ColumnKey .= 'UPPER(' . $PickListDetail['targettable'] . '.' . $PickListDetail['dispfield'] . ') as `' . $arrColumn['fieldname'] . "`,";
                        $join .= " left join " . $PickListDetail['targettable'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $PickListDetail['targettable'] . "." . $PickListDetail["targetfield"] . ")";
                    } else {


                        $ColumnKey .= $PickListDetail['targettable'] . $arrColumn['fieldname'] . '.`' . $PickListDetail['dispfield'] . '` as `' . $arrColumn['fieldname'] . "`,";
                        $join .= " left join " . $PickListDetail['targettable'] . " as " . $PickListDetail['targettable'] . $arrColumn['fieldname'] . " on (" . $TableName . ".`" . $arrColumn['fieldname'] . "`=" . $PickListDetail['targettable'] . $arrColumn['fieldname'] . "." . $PickListDetail["targetfield"] . ")";
                    }
                }
            } else if ($arrColumn['uitype'] == 53) {
                /*$PickList=new PickList;   
                    $PickList->fieldid=$Field->fieldid;
                    $BlockDetail->Fields[$FieldKey]->fieldoptions=$PickList->getPickListValue();*/


                $ColumnKey .= "user" . $arrColumn['fieldname'] . '.username as ' . $arrColumn['fieldname'] . ",";
                $join .= " left join `user` as user" . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=user" . $arrColumn['fieldname'] . ".id)";
            } else if ($arrColumn['uitype'] == 22 || $arrColumn['uitype'] == 9) {
                /*$PickListDetail = $this->getPickListDetail($arrColumn['fieldid']);
                $targettable = $PickListDetail['targettable'];
                $targetfield = $PickListDetail['targetfield'];
                $dispfield = $PickListDetail['dispfield'];
                if ($PickListDetail['targettable'] != 'user') {
                    // below if condition reuire when concat function is there than it throgh error special in meeting module
                    if (stripos($PickListDetail['dispfield'], 'concat') !== false) {
                        $ColumnKey .= "GROUP_CONCAT(". $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
                    }
                    else{
                        $ColumnKey .= "GROUP_CONCAT(" . $PickListDetail['targettable'] . "." . $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
                    }
                } else {
                    $ColumnKey .= "GROUP_CONCAT(" . $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
                }
                $alias = $PickListDetail['targettable'] . '_alias';
                // $join .= " left join " . $PickListDetail['targettable'] . " on FIND_IN_SET(" . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . "," . $TableName . "." . $arrColumn['fieldname'] . ")";

                $join .= " left join " . $PickListDetail['targettable'] . " AS " . $alias . " on FIND_IN_SET(" . $alias . ".". $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . "," . $TableName . "." . $arrColumn['fieldname'] . ")";



                $groupby = "Group By $TableName.$FieldId";*/
                $PickListDetail = $this->getPickListDetail($arrColumn['fieldid']);
                $targettable = $PickListDetail['targettable'];
                $targetfield = $PickListDetail['targetfield'];
                $dispfield = $PickListDetail['dispfield'];
                // $alias = $targettable . '_alias';
                $alias = $arrColumn['fieldname'] . '_alias'; 
                if ($targettable != 'user') {
                    if (stripos($dispfield, 'concat') !== false) {
                        // Replace targettable with alias inside dispfield (e.g. user. → user_alias.)
                        // $modifiedDispField = str_ireplace($targettable . ".", $alias . ".", $dispfield);
                        $modifiedDispField = preg_replace_callback(
                                '/\b([a-zA-Z_][a-zA-Z0-9_]*)\b/',
                                function ($matches) use ($alias) {
                                    $keywords = ['CONCAT', 'IF', 'IS', 'NULL']; // SQL functions & keywords to skip
                                    $word = strtoupper($matches[1]);
                                    return in_array($word, $keywords) ? $matches[1] : $alias . '.' . $matches[1];
                                },
                                $dispfield
                            );
                        // Optional debug output
                        // echo $modifiedDispField; die;
                        //DISTINCT added by ptpatel on 23-02-2026 to remove duplicate value
                        $ColumnKey .= "GROUP_CONCAT(DISTINCT " . $modifiedDispField . " ORDER BY " . $alias . "." . $targetfield . ") AS " . $arrColumn['fieldname'] . ",";
                    } else {
                        //DISTINCT added by ptpatel on 23-02-2026 to remove duplicate value
                        $ColumnKey .= "GROUP_CONCAT(DISTINCT " . $alias . "." . $dispfield . " ORDER BY " . $alias . "." . $targetfield . ") AS " . $arrColumn['fieldname'] . ",";
                    }
                } else {
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
                            }

                            else {
                        // Non-CONCAT fields
                        //DISTINCT added by ptpatel on 23-02-2026 to remove duplicate value
                        $ColumnKey .= "GROUP_CONCAT(DISTINCT " . $alias . "." . $dispfield . " ORDER BY " . $alias . "." . $targetfield . ") AS " . $arrColumn['fieldname'] . ",";
                    }

                }

                // Proper alias used in join
                // $join .= " LEFT JOIN " . $targettable . " AS " . $alias . " ON FIND_IN_SET(" . $alias . "." . $targetfield . ", " . $TableName . "." . $arrColumn['fieldname'] . ")";
                //added by ptpatel to resolve issue of 112, 123 like value on date 23-02-2026
                $join .= " LEFT JOIN " . $targettable . " AS " . $alias . " ON FIND_IN_SET(" . $alias . "." . $targetfield . ",  REPLACE(" . $TableName . "." . $arrColumn['fieldname'] . " , ' ', ''))";

                $groupby = "GROUP BY $TableName.$FieldId";

            } else if ($arrColumn['uitype'] == 12 || $arrColumn['uitype'] == 27 || $arrColumn['uitype'] == 28) {
                $getEntityNameDetail = $this->getReferenceEntityNameDetail($arrColumn['fieldid']);
                if ($getEntityNameDetail) {
                    $targettable = $getEntityNameDetail['targettable'];
                    $targetfield = $getEntityNameDetail['entityidfield'];
                    $dispfield = $getEntityNameDetail['fieldname'];
                    $ColumnKey .= $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . "." . $dispfield . " as " . $arrColumn['fieldname'] . ",";

                    $join .= " LEFT OUTER JOIN " . $getEntityNameDetail['targettable'] . " as " . $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . "." . $getEntityNameDetail['entityidfield'] . ")";
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
            } else if ($arrColumn['uitype'] == 5) {
                $unique_alias = "attachments" . $arrColumn['fieldname'];
                $ColumnKey .= "$unique_alias.name " . " as " . $arrColumn['fieldname'] . ",";

                // `" . $arrColumn['fieldname'] . "` tick added by ptpatel on date 01-08-25 to resolve error for .3years_financial_statement column in account
                $join .= " LEFT OUTER JOIN `attachments` as $unique_alias " . " on (" . $TableName . ".`" . $arrColumn['fieldname'] . "`= $unique_alias.attachmentsid)";
            } elseif ($arrColumn['uitype'] == 6) {
                if ($arrColumn['fieldname'] == 'is_admin' && $arrColumn['tablename'] == "user")
                    $ColumnKey .= "if(user.is_admin is not null,if(user.is_admin=0,'No','Yes'),'') as is_admin,";
                else
                    $ColumnKey .= str_replace("{$arrColumn['fieldname']}", "if({$TableName} . `{$arrColumn['fieldname']}` is not null,if({$TableName} . `{$arrColumn['fieldname']}`=0,'No','Yes'),'') as `$arrColumn[fieldname]`, ", $arrColumn['fieldname']);
                // $ColumnKey .= str_replace("$arrColumn[fieldname]", "if($arrColumn[fieldname] is not null,if($arrColumn[fieldname]=0,'No','Yes'),'') as $arrColumn[fieldname], ", $arrColumn['fieldname']);
            } elseif ($arrColumn['uitype'] == 13) {
                $ColumnKey .= 'DATE_FORMAT(' . $TableName . '.`' . $arrColumn['fieldname'] . '`,' . "'%d-%m-%Y %H:%i:%s'" . ') as `' . $arrColumn['fieldname'] . '`,';
            } elseif ($arrColumn['uitype'] == 15) {
                $ColumnKey .= 'DATE_FORMAT(' . $TableName . '.`' . $arrColumn['fieldname'] . '`,' . "'%m/%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
            } elseif ($arrColumn['uitype'] == 17) {
                $ColumnKey .= 'DATE_FORMAT(' . $TableName . '.`' . $arrColumn['fieldname'] . '`,' . "'%d-%m-%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
                // $ColumnKey .= 'DATE_FORMAT(`' . $arrColumn['fieldname'] . '`,' . "'%d-%m-%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
            } elseif ($arrColumn['uitype'] == 19) {
                $ColumnKey .= 'DATE_FORMAT(' . $TableName . '.`' . $arrColumn['fieldname'] . '`,' . "'%d/%m/%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
            } 
            //code added by ptpatel on date 01-11-2025 for refrence number with , seperated value
            else if ($arrColumn['uitype'] == 31) {

                    $getEntityNameDetail = $this->getReferenceEntityNameDetail($arrColumn['fieldid']);
                    if ($getEntityNameDetail) {
                        $targettable = $getEntityNameDetail['targettable'];        // e.g. salesorder_dit
                        $targetfield = $getEntityNameDetail['entityidfield'];      // e.g. salesorder_dit_id
                        $dispfield   = $getEntityNameDetail['fieldname'];          // e.g. salesorder_dit_no
                        $alias       = $targettable . $arrColumn['fieldname'];     // e.g. salesorder_ditreference_number

                        // SELECT COLUMN with GROUP_CONCAT (for SO numbers)
                        $ColumnKey .= "GROUP_CONCAT(DISTINCT {$alias}.{$dispfield} 
                                            ORDER BY {$alias}.{$dispfield} 
                                            SEPARATOR ', ') AS {$arrColumn['fieldname']},";

                        // JOIN CONDITION using FIND_IN_SET for multi-IDs
                        $join .= " LEFT JOIN {$targettable} AS {$alias}
                                    ON FIND_IN_SET({$alias}.{$targetfield}, {$TableName}.{$arrColumn['fieldname']})";
                    }

            } 
            //end code added by ptpatel on date 01-11-2025
            else {
                $ColumnKey .= $arrColumn['tablename'] . "." . $arrColumn['fieldname'] . ",";
            }
            if ($OrderBy == $arrColumn['fieldname'])
                $OrderBy = $arrColumn['tablename'] . "." . $OrderBy;
        }
        $ColumnKey = substr(trim($ColumnKey), 0, -1);

        if ($TableName)

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
        $where = '1=1';
        if (isset($_REQUEST['from_date']) && !empty($_REQUEST['from_date']) && 
        isset($_REQUEST['to_date']) && !empty($_REQUEST['to_date'])) {
            $from_date = date('Y-m-d 00:00:00', strtotime($_REQUEST['from_date']));
            $to_date   = date('Y-m-d 23:59:59', strtotime($_REQUEST['to_date']));

            $where .= " AND $TableName.`createdtime` BETWEEN '{$from_date}' AND '{$to_date}' ";
        }
        // added on 4 jan 2025 for export by deepika
        if (isset($_REQUEST['selectedRowIds']) && !empty($_REQUEST['selectedRowIds'])) {
            // Get the post data
            $selectedRowIds = Yii::$app->request->post('selectedRowIds');

            //if condition added by ptpatel to export all data into inventory
            if ($_REQUEST['selectedRowIds'] != 'all') {
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
        //widget filter code   
        $widgetid = Yii::$app->request->get('widgetid');
        if ($widgetid != "") {
            $connection = Yii::$app->db;
            $sql_filter = "Select * from widgets_filter where id = '$widgetid'";
            $command = $connection->createCommand($sql_filter);
            $result = $command->queryOne();
            $where .= " " . $result['default_condition'];
        }
        // echo $Query;die;
        //end widget filter code
        if (!empty($RecordId)) {
            $join .= " inner join user on (user.id=$TableName.ownerid)";
            $Query = "select $ColumnKey $join where $TableName.deleted=0 and 
            $FieldId=$RecordId";
            $Query = str_replace(",$TableName.ownerid", ",concat(first_name,' ',last_name) as ownerid", $Query);
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
            if ($id) {
                if ($TableName == '`leadinformation`') {
                    $ColumnKey .= ", IF ((" . $TableName . ".ownerid = " . $id . " OR $hasadminpower = 1) , 
                    IF ((" . $TableName . ".converted = 1 OR $TableName .leadstatus IN (4,5,6,9)) , '0' , '1')  , '0')";
                } else {
                    $ColumnKey .= ", IF ((" . $TableName . ".ownerid = " . $id . " OR $hasadminpower = 1) , '1' , '0')";
                }
                $ColumnKey .= ' as isEdit ';
            }
            // echo $tablename;die;
            $tabId = $this->getTabDetail($this->moduleName)['tabid'];
            if ($tabId == 18) {
                // $ColumnKey .= ', oem_users.oem_role_user_names ';
                // $join .=" LEFT JOIN (
                //                 SELECT 
                //                     oem_mgr.vendoraccid,
                //                     GROUP_CONCAT(DISTINCT CONCAT(role.rolename, '-', user.first_name, ' ', user.last_name) SEPARATOR ', ') AS oem_role_user_names
                //                 FROM vendor_account_oem_manager_detail AS oem_mgr
                //                 INNER JOIN user 
                //                     ON user.id = oem_mgr.userid 
                //                 AND user.role = oem_mgr.roleid
                //                 INNER JOIN role 
                //                     ON role.roleid = oem_mgr.roleid
                //                 GROUP BY oem_mgr.vendoraccid
                //             ) AS oem_users ON oem_users.vendoraccid = vendor_account.vendoraccid ";

                // $ColumnKey .= ', org_users.org_role_user_names ';
                //     $join .=" LEFT JOIN (
                //                 SELECT 
                //                     org_mgr.vendoraccid,
                //                     GROUP_CONCAT(DISTINCT CONCAT(role.rolename, '-', user.first_name, ' ', user.last_name) SEPARATOR ', ') AS org_role_user_names
                //                 FROM vendor_account_orgaisation_section AS org_mgr
                //                 INNER JOIN user 
                //                     ON user.id = org_mgr.userid 
                //                 AND user.role = org_mgr.roleid
                //                 INNER JOIN role 
                //                     ON role.roleid = org_mgr.roleid
                //                 GROUP BY org_mgr.vendoraccid
                //             ) AS org_users ON org_users.vendoraccid = vendor_account.vendoraccid ";

                //removed role joining on 6 feb 2026 by deepika
                $ColumnKey .= ', oem_users.oem_role_user_names ';
                $join .=" LEFT JOIN (
                                SELECT 
                                    oem_mgr.vendoraccid,
                                    GROUP_CONCAT(DISTINCT CONCAT(role.rolename, '-', user.first_name, ' ', user.last_name) SEPARATOR ', ') AS oem_role_user_names
                                FROM vendor_account_oem_manager_detail AS oem_mgr
                                INNER JOIN user 
                                    ON user.id = oem_mgr.userid 
                                INNER JOIN role 
                                    ON role.roleid = oem_mgr.roleid
                                GROUP BY oem_mgr.vendoraccid
                            ) AS oem_users ON oem_users.vendoraccid = vendor_account.vendoraccid ";

                $ColumnKey .= ', org_users.org_role_user_names ';
                    $join .=" LEFT JOIN (
                                SELECT 
                                    org_mgr.vendoraccid,
                                    GROUP_CONCAT(DISTINCT CONCAT(role.rolename, '-', user.first_name, ' ', user.last_name) SEPARATOR ', ') AS org_role_user_names
                                FROM vendor_account_orgaisation_section AS org_mgr
                                INNER JOIN user 
                                    ON user.id = org_mgr.userid 
                                INNER JOIN role 
                                    ON role.roleid = org_mgr.roleid
                                GROUP BY org_mgr.vendoraccid
                            ) AS org_users ON org_users.vendoraccid = vendor_account.vendoraccid ";
            }
            //code added by ptpatel end here on date 22-03-25
            $Query = $recordlisting->listing($roleid, $modulepermission, $Query, $ColumnKey, $join, $OrderBy, $SortOrder, $TableName, $groupby, $isreference, $ModuleName, $where);
            if($TableName == "`purchase_order_dit`")
            {
                $groupBy = " GROUP BY {$TableName}.{$this->fieldId} "; // example: purchase_order_dit.purchaseorder_dit_id

                // Find position of ORDER BY (case-insensitive)
                $pos = strpos($Query, "order by");
                // echo $pos;die;
                if ($pos !== false) {
                    // Insert GROUP BY before ORDER BY
                    $Query = substr_replace($Query, $groupBy, $pos, 0);
                } 
            }
            // echo "<br>Query=$Query";
            // die;
            $connection = Yii::$app->db;
            $pagination = new Pageination();
            $totalitemcount = $pagination->TotalRecords($Query);
            $pageEndRange = $totalitemcount['defaultrecord'];
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
            // echo $_REQUEST['limit'];die;
            // added on 14 jan 2025 by deepika for export
            // if (isset($_REQUEST['selectedRowIds']) && !empty($_REQUEST['selectedRowIds'])) {
            //     $Query = "$query_res "; //when agination
            // } else
            //     $Query = "$query_res limit $pageStartRange,$pageEndRange"; //when agination

            // echo $Query;
            // die;
            // $Query = "$query_res";
            //$recordlisting=new ListHire();
            //$Query=$recordlisting->listing($roleid,$modulepermission,$Query,$ColumnKey,$join,$OrderBy,$SortOrder,$TableName);
        }
        // echo "<br>Query=$Query";
        // die;

        return array($Column, $Query, $totalitemcount);
    }
    /**code for Export all end here  */

    /**code for filter report start from here */
    /**to get export = 1 column function added by ptpatel on date 31-07-25 */
    public function getFilterReportRecord($OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '',$filters = [])
    {
        $ColumnList = $this->getFilterReportList();
        // print_r($ColumnList);die;
        list($Column, $ListQuery, $totalitemcount) = $this->getQueryforFilterReport($ColumnList, $OrderBy, $SortOrder, $rolebasedrecord, $modulepermission,$filters);
        //echo "dd";die;

        $RecordList = Yii::$app->db->createCommand($ListQuery)
            ->queryAll();
        return array($Column, $RecordList, $totalitemcount);
    }
    public function getFilterReportList()
    {
        $connection = Yii::$app->db;
        $tablename = $this->tableName;
        // echo $tablename;die;
        $tabId = $this->getTabDetail($this->moduleName)['tabid'];
        $blocks = Blocks::findAll(['tabid' => $tabId,'display_status'=>1]);
        $blockIds = ArrayHelper::getColumn($blocks, 'blockid');
        $command = $connection->createCommand("
             SELECT field.fieldid, field.columnname AS fieldname, field.fieldlabel, field.uitype, field.tablename,field.sequence,field.block
             FROM field 
             WHERE field.tabid = :tabid And 
             detail_view = 1 AND tablename = :tablename AND block IN (" . implode(',', $blockIds) . ") ORDER BY field.block,field.sequence")
            ->bindValue(':tabid', $tabId)
            ->bindValue(':tablename', $tablename);

        $ColumnList = $command->queryAll();
        return $ColumnList;
    }

    public function getQueryforFilterReport($ColumnList, $OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '', $filters)
    {

        $FieldId = $this->fieldId;
        $TableName = "`" . $this->tableName() . "`";
        $RecordId = $this->_members[$FieldId];
        $ColumnKey = "";
        $roleid = $rolebasedrecord;
        $Query = '';
        $groupby = '';
        // echo "<br>role id=";
        // echo gettype($rolebasedrecord['userid']); 
        // print_r($roleid);
        // die;
        $join = "from $TableName";
        //$join="from Entity inner join $TableName on(Entity.entityid=$TableName.$FieldId)";
        $Column = array();
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

                        $ColumnKey .= "concat(user" . $arrColumn['fieldname'] . '.first_name," ",user' . $arrColumn['fieldname'] . ".last_name) as `" . $arrColumn['fieldname'] . "`,";
                        $join .= " left join `user` as user" . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=user" . $arrColumn['fieldname'] . ".id)";
                    } else if ($PickListDetail['targettable'] == 'tab') {


                        $ColumnKey .= 'UPPER(' . $PickListDetail['targettable'] . '.' . $PickListDetail['dispfield'] . ') as `' . $arrColumn['fieldname'] . "`,";
                        $join .= " left join " . $PickListDetail['targettable'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $PickListDetail['targettable'] . "." . $PickListDetail["targetfield"] . ")";
                    } else {


                        $ColumnKey .= $PickListDetail['targettable'] . $arrColumn['fieldname'] . '.`' . $PickListDetail['dispfield'] . '` as `' . $arrColumn['fieldname'] . "`,";
                        $join .= " left join " . $PickListDetail['targettable'] . " as " . $PickListDetail['targettable'] . $arrColumn['fieldname'] . " on (" . $TableName . ".`" . $arrColumn['fieldname'] . "`=" . $PickListDetail['targettable'] . $arrColumn['fieldname'] . "." . $PickListDetail["targetfield"] . ")";
                    }
                }
            } else if ($arrColumn['uitype'] == 53) {
                /*$PickList=new PickList;   
                    $PickList->fieldid=$Field->fieldid;
                    $BlockDetail->Fields[$FieldKey]->fieldoptions=$PickList->getPickListValue();*/


                $ColumnKey .= "user" . $arrColumn['fieldname'] . '.username as ' . $arrColumn['fieldname'] . ",";
                $join .= " left join `user` as user" . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=user" . $arrColumn['fieldname'] . ".id)";
            } else if ($arrColumn['uitype'] == 22 || $arrColumn['uitype'] == 9) {
                /*$PickListDetail = $this->getPickListDetail($arrColumn['fieldid']);
                $targettable = $PickListDetail['targettable'];
                $targetfield = $PickListDetail['targetfield'];
                $dispfield = $PickListDetail['dispfield'];
                if ($PickListDetail['targettable'] != 'user') {
                    // below if condition reuire when concat function is there than it throgh error special in meeting module
                    if (stripos($PickListDetail['dispfield'], 'concat') !== false) {
                        $ColumnKey .= "GROUP_CONCAT(". $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
                    }
                    else{
                        $ColumnKey .= "GROUP_CONCAT(" . $PickListDetail['targettable'] . "." . $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
                    }
                } else {
                    $ColumnKey .= "GROUP_CONCAT(" . $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
                }
                $alias = $PickListDetail['targettable'] . '_alias';
                // $join .= " left join " . $PickListDetail['targettable'] . " on FIND_IN_SET(" . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . "," . $TableName . "." . $arrColumn['fieldname'] . ")";

                $join .= " left join " . $PickListDetail['targettable'] . " AS " . $alias . " on FIND_IN_SET(" . $alias . ".". $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . "," . $TableName . "." . $arrColumn['fieldname'] . ")";



                $groupby = "Group By $TableName.$FieldId";*/
                $PickListDetail = $this->getPickListDetail($arrColumn['fieldid']);
                $targettable = $PickListDetail['targettable'];
                $targetfield = $PickListDetail['targetfield'];
                $dispfield = $PickListDetail['dispfield'];
                $alias = $targettable . '_alias';

                if ($targettable != 'user') {
                    if (stripos($dispfield, 'concat') !== false) {
                        // Replace targettable with alias inside dispfield (e.g. user. → user_alias.)
                        // $modifiedDispField = str_ireplace($targettable . ".", $alias . ".", $dispfield);
                        $modifiedDispField = preg_replace_callback(
                                '/\b([a-zA-Z_][a-zA-Z0-9_]*)\b/',
                                function ($matches) use ($alias) {
                                    $keywords = ['CONCAT', 'IF', 'IS', 'NULL']; // SQL functions & keywords to skip
                                    $word = strtoupper($matches[1]);
                                    return in_array($word, $keywords) ? $matches[1] : $alias . '.' . $matches[1];
                                },
                                $dispfield
                            );
                        // Optional debug output
                        // echo $modifiedDispField; die;

                        $ColumnKey .= "GROUP_CONCAT(" . $modifiedDispField . " ORDER BY " . $alias . "." . $targetfield . ") AS " . $arrColumn['fieldname'] . ",";
                    } else {
                        $ColumnKey .= "GROUP_CONCAT(" . $alias . "." . $dispfield . " ORDER BY " . $alias . "." . $targetfield . ") AS " . $arrColumn['fieldname'] . ",";
                    }
                } else {
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

                            $ColumnKey .= "GROUP_CONCAT(" . $modifiedDispField . " ORDER BY " . $alias . "." . $targetfield . ") AS " . $arrColumn['fieldname'] . ",";
                            }

                            else {
                        // Non-CONCAT fields
                        $ColumnKey .= "GROUP_CONCAT(" . $alias . "." . $dispfield . " ORDER BY " . $alias . "." . $targetfield . ") AS " . $arrColumn['fieldname'] . ",";
                    }

                }

                // Proper alias used in join
                $join .= " LEFT JOIN " . $targettable . " AS " . $alias . " ON FIND_IN_SET(" . $alias . "." . $targetfield . ", " . $TableName . "." . $arrColumn['fieldname'] . ")";

                $groupby = "GROUP BY $TableName.$FieldId";

            } else if ($arrColumn['uitype'] == 12 || $arrColumn['uitype'] == 27 || $arrColumn['uitype'] == 28) {
                $getEntityNameDetail = $this->getReferenceEntityNameDetail($arrColumn['fieldid']);
                if ($getEntityNameDetail) {
                    $targettable = $getEntityNameDetail['targettable'];
                    $targetfield = $getEntityNameDetail['entityidfield'];
                    $dispfield = $getEntityNameDetail['fieldname'];
                    $ColumnKey .= $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . "." . $dispfield . " as " . $arrColumn['fieldname'] . ",";

                    $join .= " LEFT OUTER JOIN " . $getEntityNameDetail['targettable'] . " as " . $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . "." . $getEntityNameDetail['entityidfield'] . ")";
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
            } else if ($arrColumn['uitype'] == 5) {
                $unique_alias = "attachments" . $arrColumn['fieldname'];
                $ColumnKey .= "$unique_alias.name " . " as " . $arrColumn['fieldname'] . ",";

                // `" . $arrColumn['fieldname'] . "` tick added by ptpatel on date 01-08-25 to resolve error for .3years_financial_statement column in account
                $join .= " LEFT OUTER JOIN `attachments` as $unique_alias " . " on (" . $TableName . ".`" . $arrColumn['fieldname'] . "`= $unique_alias.attachmentsid)";
            } elseif ($arrColumn['uitype'] == 6) {
                if ($arrColumn['fieldname'] == 'is_admin' && $arrColumn['tablename'] == "user")
                    $ColumnKey .= "if(user.is_admin is not null,if(user.is_admin=0,'No','Yes'),'') as is_admin,";
                else
                    $ColumnKey .= str_replace("{$arrColumn['fieldname']}", "if({$TableName} . `{$arrColumn['fieldname']}` is not null,if({$TableName} . `{$arrColumn['fieldname']}`=0,'No','Yes'),'') as `$arrColumn[fieldname]`, ", $arrColumn['fieldname']);
                // $ColumnKey .= str_replace("$arrColumn[fieldname]", "if($arrColumn[fieldname] is not null,if($arrColumn[fieldname]=0,'No','Yes'),'') as $arrColumn[fieldname], ", $arrColumn['fieldname']);
            } elseif ($arrColumn['uitype'] == 13) {
                $ColumnKey .= 'DATE_FORMAT(' . $TableName . '.`' . $arrColumn['fieldname'] . '`,' . "'%d-%m-%Y %H:%i:%s'" . ') as `' . $arrColumn['fieldname'] . '`,';
            } elseif ($arrColumn['uitype'] == 15) {
                $ColumnKey .= 'DATE_FORMAT(' . $TableName . '.`' . $arrColumn['fieldname'] . '`,' . "'%m/%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
            } elseif ($arrColumn['uitype'] == 17) {
                $ColumnKey .= 'DATE_FORMAT(' . $TableName . '.`' . $arrColumn['fieldname'] . '`,' . "'%d-%m-%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
                // $ColumnKey .= 'DATE_FORMAT(`' . $arrColumn['fieldname'] . '`,' . "'%d-%m-%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
            } elseif ($arrColumn['uitype'] == 19) {
                $ColumnKey .= 'DATE_FORMAT(' . $TableName . '.`' . $arrColumn['fieldname'] . '`,' . "'%d/%m/%Y'" . ') as `' . $arrColumn['fieldname'] . '`,';
            } 
            //code added by ptpatel on date 01-11-2025 for refrence number with , seperated value
            else if ($arrColumn['uitype'] == 31) {

                    $getEntityNameDetail = $this->getReferenceEntityNameDetail($arrColumn['fieldid']);
                    if ($getEntityNameDetail) {
                        $targettable = $getEntityNameDetail['targettable'];        // e.g. salesorder_dit
                        $targetfield = $getEntityNameDetail['entityidfield'];      // e.g. salesorder_dit_id
                        $dispfield   = $getEntityNameDetail['fieldname'];          // e.g. salesorder_dit_no
                        $alias       = $targettable . $arrColumn['fieldname'];     // e.g. salesorder_ditreference_number

                        // SELECT COLUMN with GROUP_CONCAT (for SO numbers)
                        $ColumnKey .= "GROUP_CONCAT(DISTINCT {$alias}.{$dispfield} 
                                            ORDER BY {$alias}.{$dispfield} 
                                            SEPARATOR ', ') AS {$arrColumn['fieldname']},";

                        // JOIN CONDITION using FIND_IN_SET for multi-IDs
                        $join .= " LEFT JOIN {$targettable} AS {$alias}
                                    ON FIND_IN_SET({$alias}.{$targetfield}, {$TableName}.{$arrColumn['fieldname']})";
                    }

            } 
            //end code added by ptpatel on date 01-11-2025
            else {
                $ColumnKey .= $arrColumn['tablename'] . "." . $arrColumn['fieldname'] . ",";
            }
            if ($OrderBy == $arrColumn['fieldname'])
                $OrderBy = $arrColumn['tablename'] . "." . $OrderBy;
        }
        $ColumnKey = substr(trim($ColumnKey), 0, -1);

        if ($TableName)

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
        $where = '1=1';
        if(!empty($filters)){
            if (isset($filters['from_date']) && !empty($filters['from_date']) && 
                isset($filters['to_date']) && !empty($filters['to_date'])) {
                $from_date = date('Y-m-d 00:00:00', strtotime($filters['from_date']));
                $to_date   = date('Y-m-d 23:59:59', strtotime($filters['to_date']));

                $where .= " AND $TableName.`createdtime` BETWEEN '{$from_date}' AND '{$to_date}' ";
            }
            //sourcing deal account filter
            if (isset($filters['vendor_account_name']) && !empty($filters['vendor_account_name'])) {
                $where .= " AND $TableName.`vendor_account_name` = {$filters['vendor_account_name']} ";
            }
        }
        // Example dynamic filter handling
    // if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
    //     $query->andWhere(['between', 'created_date', $filters['from_date'], $filters['to_date']]);
    // }

    // if (!empty($filters['status'])) {
    //     $query->andWhere(['status' => $filters['status']]);
    // }
        // added on 4 jan 2025 for export by deepika
        if (isset($_REQUEST['selectedRowIds']) && !empty($_REQUEST['selectedRowIds'])) {
            // Get the post data
            $selectedRowIds = Yii::$app->request->post('selectedRowIds');

            //if condition added by ptpatel to export all data into inventory
            if ($_REQUEST['selectedRowIds'] != 'all') {
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
        //widget filter code   
        $widgetid = Yii::$app->request->get('widgetid');
        if ($widgetid != "") {
            $connection = Yii::$app->db;
            $sql_filter = "Select * from widgets_filter where id = '$widgetid'";
            $command = $connection->createCommand($sql_filter);
            $result = $command->queryOne();
            $where .= " " . $result['default_condition'];
        }
        // echo $Query;die;
        //end widget filter code
        if (!empty($RecordId)) {
            $join .= " inner join user on (user.id=$TableName.ownerid)";
            $Query = "select $ColumnKey $join where $TableName.deleted=0 and 
            $FieldId=$RecordId";
            $Query = str_replace(",$TableName.ownerid", ",concat(first_name,' ',last_name) as ownerid", $Query);
        } else {
            // added on 14 jan 2025 to open reference to all users   
            $isreference = 0;
            $recordlisting = new ListHireReport();
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
            if ($id) {
                if ($TableName == '`leadinformation`') {
                    $ColumnKey .= ", IF ((" . $TableName . ".ownerid = " . $id . " OR $hasadminpower = 1) , 
                    IF ((" . $TableName . ".converted = 1 OR $TableName .leadstatus IN (4,5,6,9)) , '0' , '1')  , '0')";
                } else {
                    $ColumnKey .= ", IF ((" . $TableName . ".ownerid = " . $id . " OR $hasadminpower = 1) , '1' , '0')";
                }
                $ColumnKey .= ' as isEdit ';
            }
            // echo $tablename;die;
            $tabId = $this->getTabDetail($this->moduleName)['tabid'];
            if ($tabId == 18) {
                $ColumnKey .= ', oem_users.oem_role_user_names ';
                $join .=" LEFT JOIN (
                                SELECT 
                                    oem_mgr.vendoraccid,
                                    GROUP_CONCAT(DISTINCT CONCAT(role.rolename, '-', user.first_name, ' ', user.last_name) SEPARATOR ', ') AS oem_role_user_names
                                FROM vendor_account_oem_manager_detail AS oem_mgr
                                INNER JOIN user 
                                    ON user.id = oem_mgr.userid 
                                AND user.role = oem_mgr.roleid
                                INNER JOIN role 
                                    ON role.roleid = oem_mgr.roleid
                                GROUP BY oem_mgr.vendoraccid
                            ) AS oem_users ON oem_users.vendoraccid = vendor_account.vendoraccid ";

                $ColumnKey .= ', org_users.org_role_user_names ';
                    $join .=" LEFT JOIN (
                                SELECT 
                                    org_mgr.vendoraccid,
                                    GROUP_CONCAT(DISTINCT CONCAT(role.rolename, '-', user.first_name, ' ', user.last_name) SEPARATOR ', ') AS org_role_user_names
                                FROM vendor_account_orgaisation_section AS org_mgr
                                INNER JOIN user 
                                    ON user.id = org_mgr.userid 
                                AND user.role = org_mgr.roleid
                                INNER JOIN role 
                                    ON role.roleid = org_mgr.roleid
                                GROUP BY org_mgr.vendoraccid
                            ) AS org_users ON org_users.vendoraccid = vendor_account.vendoraccid ";
            }
            //code added by ptpatel end here on date 22-03-25
            $Query = $recordlisting->listing($roleid, $modulepermission, $Query, $ColumnKey, $join, $OrderBy, $SortOrder, $TableName, $groupby, $isreference, $ModuleName, $where);
            if($TableName == "`purchase_order_dit`")
            {
                $groupBy = " GROUP BY {$TableName}.{$this->fieldId} "; // example: purchase_order_dit.purchaseorder_dit_id

                // Find position of ORDER BY (case-insensitive)
                $pos = strpos($Query, "order by");
                // echo $pos;die;
                if ($pos !== false) {
                    // Insert GROUP BY before ORDER BY
                    $Query = substr_replace($Query, $groupBy, $pos, 0);
                } 
            }
            // echo "<br>Query=$Query";
            // die;
            $connection = Yii::$app->db;
            $pagination = new Pageination();
            $totalitemcount = $pagination->TotalRecords($Query);
            $pageEndRange = $totalitemcount['defaultrecord'];
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
            // echo $_REQUEST['limit'];die;
            // added on 14 jan 2025 by deepika for export
            // if (isset($_REQUEST['selectedRowIds']) && !empty($_REQUEST['selectedRowIds'])) {
            //     $Query = "$query_res "; //when agination
            // } else
            //     $Query = "$query_res limit $pageStartRange,$pageEndRange"; //when agination

            // echo $Query;
            // die;
            // $Query = "$query_res";
            //$recordlisting=new ListHire();
            //$Query=$recordlisting->listing($roleid,$modulepermission,$Query,$ColumnKey,$join,$OrderBy,$SortOrder,$TableName);
        }
        // echo "<br>Query=$Query";
        // die;

        return array($Column, $Query, $totalitemcount);
    }
    /**code for filter report end here  */
}
