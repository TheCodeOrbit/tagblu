<?php

namespace app\models;
use yii\helpers\Html; // Add this import at the top of your PHP file
use backend\components\AccessHelper;
use Yii;

class ListHireReport
{

    function listing($roleid, $modulepermission, $Query, $ColumnKey, $join, $OrderBy, $SortOrder, $TableName, $groupby, $isreference, $ModuleName = '', $where = '',$orwhere = '')
    {

        $userroleid = (is_array($roleid) && isset($roleid['roleid'])) ? $roleid['roleid'] : '';
        $filterFielduitype = Yii::$app->request->post('filterFielduitype');
        $filterFieldtablename = Yii::$app->request->post('filterFieldtablename');
        $filterFieldName = Yii::$app->request->post('filterFieldName');
        $labelValue = Yii::$app->request->post('labelValue');
        $inputValue = Yii::$app->request->post('inputValue');
        $fieldId = Yii::$app->request->post('fieldId');
        $filterOperator = Yii::$app->request->post('filteroperator');
        $filterselectbox = Yii::$app->request->post('filterselectbox');
        $cond = '';
        //echo $where;die;


        // added on 14 jan 2025 for export only converted = 0
        // echo $TableName;die;
        if ($TableName == "`leadinformation`") {
            //removed hide converted leads on 04 Mar 2025
            // $cond .= " and if((`leadinformation`.leadstatus != 4 && `leadinformation`.leadstatus != 3),converted = 0,1=1) ";
        }
        if ($TableName == "`contacts`" || $TableName == "`vendor_account`" || $TableName == "`sourcingdeal`" || $TableName == "`opportunity`") {
            $cond .= " and $TableName.is_temp = 0 ";
        }

        if ($where != '') {
            $cond .= " and " . $where;
        }
        if ($filterselectbox) {
            //get tabid
            $tbar = Yii::$app->db->createCommand("SELECT tabid FROM tab WHERE  name = :name")
                ->bindValue(':name', $ModuleName)
                ->queryOne();
            $Tabid = $tbar['tabid'];
            $default_filter = Yii::$app->db->createCommand("SELECT * FROM default_filter WHERE  id = :id and tabid=:tabid and (userid=1 or userid=:userid)")
                ->bindValue(':id', $filterselectbox)
                ->bindValue(':tabid', $Tabid)
                ->bindValue(':userid', Yii::$app->user->id)
                ->queryOne();

            $cond .= " " . $default_filter['default_condition'];
            //get user filter

        }
        // echo $cond;die;
        if ($filterFielduitype == 8) {
            // if ($filterFieldName == 'ownerid') {
            //     $filterFieldtablename = 'userownerid';
            //     $filterFieldName = 'username';
            // } else {
            //     //get fieldname
            //     $PickListDetail = $this->getPickListDetail($fieldId);
            //     $targettable = $PickListDetail['targettable'];
            //     $targetfield = $PickListDetail['targetfield'];
            //     $dispfield = $PickListDetail['dispfield'];

            //     $filterFieldtablename = $targettable . $filterFieldName;
            //     $filterFieldName = $dispfield;
            // }
        }

        if ($filterFieldName == 'creatorid') {
            $filterFieldtablename = 'usercreatorid';
            $filterFieldName = 'username';
        }
        if ($filterFieldName == 'modifiedby') {
            $filterFieldtablename = 'usermodifiedby';
            $filterFieldName = 'username';
        }
        if ($filterFielduitype == 12 || $filterFielduitype == 27 || $filterFielduitype == 28) {
            //get fieldname
            $getEntityNameDetail = $this->getReferenceEntityNameDetail($fieldId);
            $targettable = $getEntityNameDetail['targettable'];
            $targetfield = $getEntityNameDetail['entityidfield'];
            $dispfield = $getEntityNameDetail['fieldname'];

            // $filterFieldtablename = $targettable;
            $filterFieldtablename = $targettable . $filterFieldName;
            $filterFieldName = $dispfield;
        }
        if ($filterFielduitype == 25) {
            //get fieldname            
            $filterFieldtablename = 'tab';
            $filterFieldName = 'tablabel';
        }
        if ($filterFielduitype == 17) {
            $date = $inputValue; // original date in Y-m-d format

            // Convert to a timestamp
            $timestamp = strtotime($date);

            // Format the timestamp to d-m-Y
            $inputValue = date('Y-m-d', $timestamp);
        }

        if ($filterFielduitype == 13) {
            $date = $inputValue; // original date in Y-m-d format

            // Convert to a timestamp
            $timestamp = strtotime($date);

            // Format the timestamp to d-m-Y
            $inputValue = date('Y-m-d H:i:s', $timestamp);
        }
        //add by ptpatel on date 02-04-25
        if ($filterFielduitype == 22) {
            $inputValueArr = $inputValue;
            $inputValue = implode("','", $inputValue);

        }
        // echo $inputValue;die;

        if ($filterFielduitype == 22) {
            switch ($filterOperator) {
                case 'Equals':
                    $cond .= " and $filterFieldtablename.$filterFieldName IN ('$inputValue')";
                    break;
                case 'Not_Equals':
                    $cond .= " and $filterFieldtablename.$filterFieldName NOT IN ('$inputValue')";
                    break;
                case 'Contains':
                    // $i=0;
                    foreach ($inputValueArr as $arr) {
                        // if(count($inputValueArr) - 1 == $i)
                        $cond .= " and $filterFieldtablename.$filterFieldName like '%$arr%' ";
                        // else
                        //     $cond .= " and $filterFieldtablename.$filterFieldName like '%$arr%' OR";
                        // $i++;
                    }
                    break;
                case 'Not_Contains':
                    $i = 0;
                    foreach ($inputValueArr as $arr) {
                        // if(count($inputValueArr) - 1 == $i)
                        $cond .= " and $filterFieldtablename.$filterFieldName NOT like '%$arr%' ";
                        //     else
                        // $cond .= " and $filterFieldtablename.$filterFieldName NOT like '%$arr%' OR";
                        // $i++;
                    }
                    break;
                case 'In':
                    $cond .= " and  $filterFieldtablename.$filterFieldName in ('$inputValue')";
                    // $query->andWhere(['in', $columnName, $inputValue]); // $inputValue should be an array
                    break;
                case 'Not_In':
                    $cond .= " and  $filterFieldtablename.$filterFieldName NOT IN ('$inputValue')";
                    // $query->andWhere(['not in', $columnName, $inputValue]); // $inputValue should be an array
                    break;
                case 'is_Empty':
                    $cond .= " and  ($filterFieldtablename.$filterFieldName is NULL OR $filterFieldtablename.$filterFieldName = '' )";
                    // $query->andWhere(['or', [$columnName => null], [$columnName => '']]);
                    break;
                case 'is_Not_Empty':
                    $cond .= " and  ($filterFieldtablename.$filterFieldName is NOT NULL OR $filterFieldtablename.$filterFieldName !='')";
                    // $query->andWhere(['and', ['is not', $columnName, null], ['<>', $columnName, '']]);
                    break;
                case 'Begins_with':
                    // $cond .= " and  ($filterFieldtablename.$filterFieldName like '$inputValue%')";
                    $i = 0;
                    foreach ($inputValueArr as $arr) {
                        // if(count($inputValueArr) - 1 == $i)
                        $cond .= " and $filterFieldtablename.$filterFieldName like '%$arr%' ";
                        // else
                        // $cond .= " and $filterFieldtablename.$filterFieldName like '%$arr%' AND ";
                        // $i++;
                    }
                    // $query->andWhere(['like', $columnName, "$inputValue%", false]); // Searches for values beginning with $inputValue
                    break;
                default:
            }
            // echo $cond;exit;
        }
        ////ended added by ptpatel on date 02-04-25
        else {
            switch ($filterOperator) {
                case 'Equals':
                    $cond .= " and $filterFieldtablename.$filterFieldName	 ='$inputValue'";
                    break;
                case 'Not_Equals':
                    $cond .= " and $filterFieldtablename.$filterFieldName <> '$inputValue'";
                    break;
                case 'Contains':
                    $cond .= " and $filterFieldtablename.$filterFieldName like '%$inputValue%'";
                    break;
                case 'Not_Contains':
                    $cond .= " and $filterFieldtablename.$filterFieldName not like '%$inputValue%'";
                    break;
                case 'In':
                    $cond .= " and  $filterFieldtablename.$filterFieldName in ('$inputValue')";
                    // $query->andWhere(['in', $columnName, $inputValue]); // $inputValue should be an array
                    break;
                case 'Not_In':
                    $cond .= " and  $filterFieldtablename.$filterFieldName NOT IN ('$inputValue')";
                    // $query->andWhere(['not in', $columnName, $inputValue]); // $inputValue should be an array
                    break;
                case 'is_Empty':
                    $cond .= " and  ($filterFieldtablename.$filterFieldName is NULL OR $filterFieldtablename.$filterFieldName = '' )";
                    // $query->andWhere(['or', [$columnName => null], [$columnName => '']]);
                    break;
                case 'is_Not_Empty':
                    $cond .= " and  ($filterFieldtablename.$filterFieldName is NOT NULL OR $filterFieldtablename.$filterFieldName !='')";
                    // $query->andWhere(['and', ['is not', $columnName, null], ['<>', $columnName, '']]);
                    break;
                case 'Begins_with':
                    $cond .= " and  ($filterFieldtablename.$filterFieldName like '$inputValue%')";
                    // $query->andWhere(['like', $columnName, "$inputValue%", false]); // Searches for values beginning with $inputValue
                    break;
                default:
            }
        }


        // if($arr_cond = isset($_POST['searchres']) != ''){
        // $model 	  	= new ListSearch;	
        // $cond	  	= $model->SerachResult($TableName);
        // //echo $cond;die;
        // }
        // else $cond = '';	
        $uid = Yii::$app->user->id;
        $past_assigned_records = null;
        //get profile of user
        $profilerr = Yii::$app->db->createCommand("SELECT profileid FROM role2profile rp join user2role ur on rp.roleid = ur.roleid WHERE  ur.userid = :uid")
            ->bindValue(':uid', $uid)
            ->queryOne();

        $profileid = $profilerr['profileid'];
        //now check for global action
        $hasadminpower = Yii::$app->db->createCommand("SELECT count(*) as cnt FROM `profile2globalpermissions` where globalactionid in (1,2) and globalactionpermission=0 and profileid = :profileid")
            ->bindValue(':profileid', $profileid)
            ->queryOne();
        // echo $hasadminpower['cnt'];die;
        //print_r($hasadminpower);die;
        $isadmin = 0;
        $access = 0;

        if ($hasadminpower['cnt'] == 2) {
            $isadmin = 1;
            $access = 1;
        }
        $hasadminpower = Yii::$app->db->createCommand("SELECT count(*) as cnt FROM `user` where is_admin=1 and id=:userid")
            ->bindValue(':userid', $uid)
            ->queryOne();
        // echo $hasadminpower['cnt'];die;
        //print_r($hasadminpower);die;
        if ($hasadminpower['cnt'] > 0) {
            $isadmin = 1;
            $access = 1;
        }


        if ($ModuleName == "pickup") {
            if (empty($isadmin) && !empty($ModuleName)) {
                $records_list = Yii::$app->db->createCommand("SELECT distinct module_reference_id FROM `owner_tracker` where module=:module and ownerid=:ownerid and deleted=:deleted")
                    ->bindValue(':module', $ModuleName)
                    ->bindValue(':ownerid', $uid ?? $roleid['userid'])
                    ->bindValue(':deleted', 0)
                    ->queryAll();
                if ($records_list && is_array($records_list) && count($records_list) > 0) {
                    $past_assigned_records = array_map(function ($item) {
                        return $item['module_reference_id'];
                    }, $records_list);
                }
            }
            if (!empty($past_assigned_records) && is_array($past_assigned_records))
                $past_assigned_records = implode(",", $past_assigned_records);
        }
        if (empty($past_assigned_records))
            $past_assigned_records = 0;
        //echo $TableName;die;
        if($orwhere == ' AND ( ' || $orwhere == "")
            $orwhere = ' ( ';
        else{
             $orwhere = preg_replace('/\bor\b/i', '', $orwhere, 1);
        }
        // echo $orwhere;die;
        // if ($isadmin == '1') {
            //  if($TableName =='`user`'){
            // $Query="select DISTINCT(id) as RecordId,user.user_name,yearname as fyear,
            // 	user.is_admin ,user.first_name,user.last_name,title,minenamename as mine_name,
            // 	concat(manpower.first_name,' ',manpower.last_name) as manpower_name,
            // 	user.employee_code,contractormaster.company_name as company_name,
            // 	UserType.user_type as utypeid
            // 	from user inner join minename on minename.minenameid= user.mine_name
            // 	left join manpower on manpower.manpowerid= user.manpower_name 
            // 	inner join fyear on fyear.yearid= user.fyear
            // 	inner join UserType on UserType.utypeid = user.utypeid
            // 	INNER JOIN contractormaster on contractormaster.contractormaster_id = user.company_name
            // 	where $TableName.deleted=0 $cond $groupby order by $OrderBy $SortOrder";
            // }else
            // {
            if ($TableName != '`sourcingdeal_contact_role`' && $TableName != '`opportunity_contact_role`')
                $join .= " inner join user as owner on (owner.id=$TableName.ownerid)";
            $Query = "select $ColumnKey $join where $TableName.deleted=0 $cond $orwhere ) $groupby order by $OrderBy $SortOrder";
            $Query = str_replace(",$TableName.ownerid", ",concat(first_name,' ',last_name) as ownerid", $Query);
            // }
            $Query = preg_replace('/\(\s*\)/', '', $Query);
        // }
        //die;
        // echo $isreference ;die;
        // echo $Query;die;
        return $Query;
    }
    function getPickListDetail($fieldid)
    {

        $connection = Yii::$app->db;
        // $command=$connection->createCommand("select targettable,targetfield,dispfield  from picklist where     fieldid=:fieldid")->bindParam(':fieldid'=>$fieldid);
        $command = $connection->createCommand("select targettable,targetfield,dispfield from `picklist`  where fieldid=:fieldid")->bindParam(':fieldid', $fieldid);
        $Columns = $command->queryOne();

        return $Columns;
    }
    function getPickListDetailvalue($fieldid, $record)
    {

        $connection = Yii::$app->db;
        // $command=$connection->createCommand("select targettable,targetfield,dispfield  from picklist where     fieldid=:fieldid")->bindParam(':fieldid'=>$fieldid);
        $command = $connection->createCommand("select targettable,targetfield,dispfield from `picklist`  where fieldid=:fieldid")->bindParam(':fieldid', $fieldid);
        $Columns = $command->queryOne();
        if ($Columns) {
            $targettable = $Columns['targettable'];
            $targetfield = $Columns['targetfield'];
            $dispfield = $Columns['dispfield'];

            $command = $connection->createCommand("select $dispfield as showfield from $targettable  where $targetfield=:record")->bindParam(':record', $record);
            $Columns = $command->queryOne();
            if (!empty($Columns['showfield'])) {
                // print_r($Columns);
                return $Columns['showfield'];
            } else
                return '';
        } else {
            return '';
        }
    }
    function getPickListDetailMultiple($fieldid, $record)
    {
        $connection = Yii::$app->db;
        // $command=$connection->createCommand("select targettable,targetfield,dispfield  from picklist where     fieldid=:fieldid")->bindParam(':fieldid'=>$fieldid);
        $command = $connection->createCommand("select targettable,targetfield,dispfield from `picklist`  where fieldid=:fieldid")->bindParam(':fieldid', $fieldid);
        $Columns = $command->queryOne();
        $targettable = $Columns['targettable'];
        $targetfield = $Columns['targetfield'];
        $dispfield = $Columns['dispfield'];
        $recordaarr = explode(",", $record);
        $val = '';
        foreach ($recordaarr as $value) {
            $command = $connection->createCommand("select $dispfield as showfield from $targettable  where $targetfield=:record")->bindParam(':record', $value);
            $Columns = $command->queryOne();
            if (!empty($Columns['showfield'])) {
                $val .= $Columns['showfield'] . ",";
            }
            # code...
        }


        return $val;
    }
    function getPickListDetailMultiplewitval($fieldid, $record)
    {
        $connection = Yii::$app->db;
        // $command=$connection->createCommand("select targettable,targetfield,dispfield  from picklist where     fieldid=:fieldid")->bindParam(':fieldid'=>$fieldid);
        $command = $connection->createCommand("select targettable,targetfield,dispfield from `picklist`  where fieldid=:fieldid")->bindParam(':fieldid', $fieldid);
        $Columns = $command->queryOne();
        $targettable = $Columns['targettable'];
        $targetfield = $Columns['targetfield'];
        $dispfield = $Columns['dispfield'];
        $recordaarr = explode(",", $record);
        $val = array();
        foreach ($recordaarr as $value) {
            $command = $connection->createCommand("select $dispfield as showfield,$targetfield from $targettable  where $targetfield=:record")->bindParam(':record', $value);
            $Columns = $command->queryOne();
            if (!empty($Columns['showfield'])) {
                $val[$Columns[$targetfield]] = $Columns['showfield'];
            }
            # code...
        }


        return $val;
    }
    function getSalutation($fieldid)
    {


        $connection = Yii::$app->db;
        // $command=$connection->createCommand("select targettable,targetfield,dispfield  from picklist where     fieldid=:fieldid")->bindParam(':fieldid'=>$fieldid);
        $command = $connection->createCommand("select `salutationtype` as dispfield from `salutationtype`  where salutationid=:fieldid")->bindValue(':fieldid', $fieldid);
        $Columns = $command->queryOne();
        // print_r($Columns);die;
        if ($Columns == false || is_null($Columns))
            return '';
        else
            return $Columns['dispfield'];
    }
    function getuser($fieldid, $record)
    {

        $connection = Yii::$app->db;
        // $command=$connection->createCommand("select targettable,targetfield,dispfield  from picklist where     fieldid=:fieldid")->bindParam(':fieldid'=>$fieldid);


        $command = $connection->createCommand("select concat(first_name,' ',if(last_name is null,'',last_name)) as showfield from user  where id=:record")->bindParam(':record', $record);
        $Columns = $command->queryOne();
        if (!empty($Columns['showfield'])) {
            // print_r($Columns);
            return $Columns['showfield'];
        } else
            return '';
    }
    function getReferenceEntityNameDetail($fieldid)
    {

        $connection = Yii::$app->db;
        $command = $connection->createCommand("select targettable,entityidfield,fieldname from `entityname`  where fieldid=:fieldid")->bindParam(':fieldid', $fieldid);
        $Columns = $command->queryOne();
        return $Columns;
    }
}
