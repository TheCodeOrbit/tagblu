<?php

namespace app\models;

use Yii;

/**
 * ListModel class.
 * ListModel is the data structure for keeping
 * ListModel form data. It is used by the 'Module' action of 'Controller'.
 */
class ApproveListModel extends \yii\db\ActiveRecord
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

                    if($modulename == 'opportunities')
                    {
                    $ColumnKey .=
                        "
        WHEN $TableName.related_to = (select tabid from tab where tab.name = '$modulename') THEN opportunity.$dispfield
        ";
                    }
                    else{
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
        if($TableName == "`leadinformation`")
        {
            $where .= " and $TableName.leadstatus=4 and $TableName.ownerid =".Yii::$app->user->id;//pending for approval and ownerid=loggedin user
        }
        // echo $where;die;
        if (!empty($RecordId)) {
            $join .= " inner join user on (user.id=$TableName.ownerid)";
            $Query = "select $ColumnKey $join where $TableName.deleted=0 and 
            $FieldId=$RecordId";
            $Query = str_replace(",$TableName.ownerid", ",concat(first_name,' ',last_name) as ownerid", $Query);
        } else {
            // added on 14 jan 2025 to open reference to all users   
            $isreference = 0;
            $recordlisting = new ListHire();
            $Query = $recordlisting->listing($roleid, $modulepermission, $Query, $ColumnKey, $join, $OrderBy, $SortOrder, $TableName, $groupby, $isreference, $ModuleName, $where);
            //echo $Query;die;
            //echo "<br>Query=$Query";
            //die;
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
                $Query = "$query_res ";//when agination
            } else
                $Query = "$query_res limit $pageStartRange,$pageEndRange";//when agination

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
}
