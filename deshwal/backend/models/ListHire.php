<?php

namespace app\models;
use yii\helpers\Html; // Add this import at the top of your PHP file
use backend\components\AccessHelper;
use Yii;

class ListHire
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
        //added on 28 nov 2025 for showing Owners and their respective managers can view only their own deals, payments, and accounts, as per the organization structure in reference || but right now removed isreference for all modules reference 
        if(empty($ModuleName) && !empty($_REQUEST['mname']))
        {
            $ModuleName = $_REQUEST['mname'];
            //$isreference =0;
            if($isreference == 1 && ($ModuleName == 'sourcingdeal' || $ModuleName == 'payments' || $ModuleName == 'vendoraccount' || $ModuleName == 'opportunities'))
            {
                $isreference = 0;
            }
            //transportor name for pickup module code added by ptpatel on date 24-12-2025 
            // maintabid and fieldname match
            if(($_REQUEST['maintabid'] == 2093 && preg_match('/^transporter_name_\d+$/', $_REQUEST['field'])) || 
            ($_REQUEST['maintabid'] == 2089 && preg_match('/^transporter_name_vp_\d+$/', $_REQUEST['field'])) ||
            (isset($_REQUEST['srctabid']) && $_REQUEST['srctabid'] == 88 && (preg_match('/^transporter_name$/', $_REQUEST['field']) || preg_match('/^vendor_name$/', $_REQUEST['field'])))
            ||
            (isset($_REQUEST['srctabid']) && $_REQUEST['srctabid'] == 2 && (preg_match('/^vendor_name$/', $_REQUEST['field'])))) 
            {
                $isreference = 1;
            }
            //transportor name for pickup module code added by ptpatel on date 24-12-2025 
        }
       


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
        if ($isadmin == '1') {
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
            // $Query = preg_replace('/\(\s*\)/', '', $Query);
            $Query = preg_replace('/(?<![A-Za-z0-9_])\(\s*\)/', '', $Query);//this will not remove () if there is function infront of it added by ptpatel to resolve widget filter issue on date 14-11-2025
        } else {
            if ($modulepermission['shareid'] == '1' || $modulepermission['shareid'] == '2' || $modulepermission['shareid'] == '0') {
                //echo "w";
                // if ($TableName == '`user`') {
                //     $Query = "select DISTINCT(id) as RecordId,user.user_name,yearname as fyear,
                // 		user.is_admin ,user.first_name,user.last_name,title,minenamename as mine_name,
                // 		concat(manpower.first_name,' ',manpower.last_name) as manpower_name,
                // 		user.employee_code,contractormaster.company_name as company_name,
                // 		UserType.user_type as utypeid
                // 		from user inner join minename on minename.minenameid= user.mine_name
                // 		left join manpower on manpower.manpowerid= user.manpower_name 
                // 		inner join fyear on fyear.yearid= user.fyear
                // 		inner join UserType on UserType.utypeid = user.utypeid
                // 		INNER JOIN contractormaster on contractormaster.contractormaster_id = user.company_name
                // 		where $TableName.deleted=0 and $TableName.ownerid IN (" . $roleid['userid'] . ") 
                // $cond $groupby order by $OrderBy $SortOrder";
                // } else {


                $uid = Yii::$app->user->id; // $_SESSION[Yii::$app->params['dirName'].'_id'];
                $join .= " inner join user as owner on (owner.id=$TableName.ownerid)";
                $Query = "select $ColumnKey $join where $TableName.deleted=0 and 
						   $TableName.ownerid='" . $uid . "' $cond $groupby order by $OrderBy $SortOrder";
                if ($ModuleName == "leads") {
                     // added DevIt clevel role id to allow view all records
                   //commented on 03 sept 2025 as it is not approved, approved on 11 sept 2025
                   $alowview = "No";
                    $role = \app\models\Role::find()
                        ->select([
                            "has_c_level" => new \yii\db\Expression(
                                "CASE WHEN rolename LIKE '%C Level%' THEN 'Yes' ELSE 'No' END"
                            )
                        ])
                        ->where(['roleid' => $userroleid])
                        ->asArray()
                        ->one();

                    if ($role && $role['has_c_level'] === 'Yes') {
                        $alowview =  "Yes";
                    } else {
                        $alowview = "No";
                    }
                    //commented on 03 sept 2025 as it is not approved yet
                    // $alowview = "No";
                    //get sourceid and sourcemodule aaded on 5 sept 2025 or allowing related list
                    $sourcemodule = Yii::$app->request->get('sourcemodule', '');
                    $sourcemodule = Html::encode($sourcemodule);  // Encode special characters to prevent XSS
                
                    $sourceid = Yii::$app->request->get('sourceid', '');
                    $sourceid = Html::encode($sourceid);  // Encode special characters to prevent XSS
                    //end on 5 sept 2025
                    // echo $isreference." ".$allowview." ".$sourceid." ".$sourcemodule;die;
                    if ($isreference == 1 || $alowview == 'Yes' || (!empty($sourceid) && !empty($sourcemodule))) { // added DevIt clevel role id to allow view all records
                    
                        $Query = "select $ColumnKey $join where $TableName.deleted=0 
                            $cond $groupby order by $OrderBy $SortOrder";
                    } else {
                        //    old condtion

                        //                         $Query = "select $ColumnKey $join where $TableName.deleted=0 AND (
                        //     leadinformation.vertical_manager IN  (" . Yii::$app->user->id . ")
                        //     OR
                        //     $TableName.ownerid IN (" . $roleid['userid'] . ")
                        // )            $cond $groupby order by $OrderBy $SortOrder";
                        $Query = "select $ColumnKey $join where $TableName.deleted=0 AND 
                ($TableName.ownerid IN (" . $roleid['userid'] . ") || $TableName.creatorid IN (" . $roleid['userid'] . "))
                $cond $orwhere ) $groupby order by $OrderBy $SortOrder";
                    }
                } else {
                    // added DevIt clevel role id to allow view all records
                   //commented on 03 sept 2025 as it is not approved yet, approved on 11 sept 2025
                   $alowview = "No";
                    $role = \app\models\Role::find()
                        ->select([
                            "has_c_level" => new \yii\db\Expression(
                                "CASE WHEN rolename LIKE '%C Level%' THEN 'Yes' ELSE 'No' END"
                            )
                        ])
                        ->where(['roleid' => $userroleid])
                        ->asArray()
                        ->one();

                    if ($role && $role['has_c_level'] === 'Yes') {
                        $alowview =  "Yes";
                    } else {
                        $alowview = "No";
                    }
                    //commented on 03 sept 2025 as it is not approved yet
                    //$alowview = "No";

                    //added on 9 sept to give view all permission to product masters
                    // if($ModuleName == "productdit" || $ModuleName == "products"){
                    //     $alowview = "Yes";
                    // }
                     //as per discussion on 11 sept provide view all for master type
                    $master_type = 0;
                    $sql_master ="select count(*) as cnt from  `tab` WHERE `name` = :modulename and`parent` LIKE '10' ";
                    $rec_master = Yii::$app->db->createCommand($sql_master)->bindValue(':modulename', $ModuleName)->queryOne();
                    if ($rec_master && $rec_master['cnt'] >0 ) {
                        $alowview = "Yes";
                    }

                    //get sourceid and sourcemodule aaded on 5 sept 2025 or allowing related list
                    $sourcemodule = Yii::$app->request->get('sourcemodule', '');
                    $sourcemodule = Html::encode($sourcemodule);  // Encode special characters to prevent XSS
                
                    $sourceid = Yii::$app->request->get('sourceid', '');
                    $sourceid = Html::encode($sourceid);  // Encode special characters to prevent XSS
                    //end on 5 sept 2025
                    // echo $isreference." ".$allowview." ".$sourceid." ".$sourcemodule;die;
                    if ($isreference == 1 ||  (!empty($sourceid) && !empty($sourcemodule))) { // added DevIt clevel role id to allow view all records
                        $Query = "select $ColumnKey $join where $TableName.deleted=0 $cond $groupby order by $OrderBy $SortOrder";
                    } 
                    else if ($isreference == 1 || $alowview == 'Yes' || (!empty($sourceid) && !empty($sourcemodule))) { // added DevIt clevel role id to allow view all records
                    //removed isreference on 28 nov 2025
                        $Query = "select $ColumnKey $join where $TableName.deleted=0 $cond $orwhere ) $groupby order by $OrderBy $SortOrder";
                        // $Query = preg_replace('/\(\s*\)/', '', $Query);
                         $Query = preg_replace('/(?<![A-Za-z0-9_])\(\s*\)/', '', $Query);//this will not remove () if there is function infront of it added by ptpatel to resolve widget filter issue on date 14-11-2025
                    }
                    else {
                        if ($ModuleName == "pickup" && !empty($past_assigned_records)) {
                            $Query = "select $ColumnKey $join where $TableName.deleted=0 and (( $TableName.ownerid IN (" . $roleid['userid'] . ") ||  $TableName.creatorid IN (" . $roleid['userid'] . ") || $TableName.pickup_id IN(" . $past_assigned_records . ")) ";
                        } else if ($ModuleName == "opportunities") {
                            $h99Condition = "";
                            if (Yii::$app->user->identity->role == 'H99') {
                                $h99Condition = "( $TableName.opportunity_stage IN ('1','2','3') ) OR ";
                            }
                            /*$Query = "select $ColumnKey $join where $TableName.deleted=0  
                             and 
                            ( " . $h99Condition . "
                              ( $TableName.ownerid IN (" . $roleid['userid'] . ") ||  $TableName.creatorid IN (" . $roleid['userid'] . ")
                            OR (
                            FIND_IN_SET('1',opportunity.team_responsible) AND opportunity.opportunity_stage='4' AND (" . $uid . " = IFNULL(opportunity.sa_assigned, 0) || " . $uid . " = IFNULL(opportunity.sf_assigned, 0))
                            )
                            OR (
                            FIND_IN_SET('3',opportunity.team_responsible) AND opportunity.opportunity_stage='4' AND " . $uid . " = IFNULL(opportunity.sf_assigned, 0)
                            )
                            OR (
                            FIND_IN_SET('2',opportunity.team_responsible) AND opportunity.opportunity_stage='4' AND " . $uid . " = IFNULL(opportunity.procurement_team_member, 0)
                            )
                            )";*/
                            $Query = "select $ColumnKey $join where $TableName.deleted=0  
                             and 
                            ( " . $h99Condition . "
                              ( $TableName.ownerid IN (" . $roleid['userid'] . ") ||  $TableName.creatorid IN (" . $roleid['userid'] . ")
                            OR (
                            FIND_IN_SET('1',opportunity.team_responsible) 
                            AND (opportunity.opportunity_stage = '4' OR opportunity.opportunity_stage = '5') 
                            AND (
                                        FIND_IN_SET({$uid}, opportunity.sa_assigned)
                                        OR FIND_IN_SET({$uid}, opportunity.sf_assigned)
                                    )
                            )
                            OR (
                            FIND_IN_SET('3',opportunity.team_responsible) 
                            AND (opportunity.opportunity_stage = '4' OR opportunity.opportunity_stage = '5')
                            AND FIND_IN_SET({$uid}, opportunity.sf_assigned)
                            )
                            OR (
                            FIND_IN_SET('2',opportunity.team_responsible) 
                            AND (opportunity.opportunity_stage = '4' OR opportunity.opportunity_stage = '5')
                            AND FIND_IN_SET({$uid}, opportunity.procurement_team_member)
                            )
                            )";
                            
                        } 
                    //     else if ($ModuleName == "vendoraccount") {
                    //         // Get the records for the organization section
                    //         $sql_v = "SELECT vendoraccid FROM `vendor_account_orgaisation_section` WHERE userid = :uid";
                    //         $org_records = Yii::$app->db->createCommand($sql_v)
                    //             ->bindValue(':uid', $uid)
                    //             ->queryAll();

                    //         // Initialize the base query components
                    //         $baseQuery = "SELECT $ColumnKey $join WHERE $TableName.deleted = 0 AND ((
                    //  $TableName.ownerid IN (" . $roleid['userid'] . ") ||  $TableName.creatorid IN (" . $roleid['userid'] . ")";

                    //         // If organization records exist, include vendoraccid condition
                    //         if ($org_records) {
                    //             // Extract vendoraccid values from the result set
                    //             $vendoraccids = array_column($org_records, 'vendoraccid');

                    //             // Directly insert the vendoraccid values as a comma-separated string
                    //             $vendoraccids_string = implode(',', $vendoraccids);

                    //             // Append the condition for vendoraccid to the query
                    //             $baseQuery .= " OR `vendor_account`.vendoraccid IN ($vendoraccids_string)";
                    //         }

                    //         // Append the rest of the conditions
                    //         $baseQuery .= ")";

                    //         // Prepare the query string (without execution)
                    //         $Query = $baseQuery;

                    //         // Optional: You can output or log the query to see the final result
                    //         // echo $query;
                    //         // die; // Make sure there's no extra code after this point if you are using die here
                    //     }
                    //     else if ($ModuleName == "vendorlocations" || $ModuleName == "contacts" || $ModuleName == "contracts") {
                    //         // Get the records for the organization section
                    //         $sql_v = "SELECT vendoraccid FROM `vendor_account_orgaisation_section` WHERE userid = :uid";
                    //         $org_records = Yii::$app->db->createCommand($sql_v)
                    //             ->bindValue(':uid', $uid)
                    //             ->queryAll();

                    //         // Initialize the base query components
                    //         $baseQuery = "SELECT $ColumnKey $join WHERE $TableName.deleted = 0 AND ((
                    //  $TableName.ownerid IN (" . $roleid['userid'] . ") ||  $TableName.creatorid IN (" . $roleid['userid'] . ")";

                    //         // If organization records exist, include vendoraccid condition
                    //         if ($org_records) {
                    //             // Extract vendoraccid values from the result set
                    //             $vendoraccids = array_column($org_records, 'vendoraccid');

                    //             // Directly insert the vendoraccid values as a comma-separated string
                    //             $vendoraccids_string = implode(',', $vendoraccids);

                    //             // Append the condition for vendoraccid to the query
                    //             if ($ModuleName == "vendorlocations" )
                    //             $baseQuery .= " OR `vendor_locations`.vendor_account IN ($vendoraccids_string)";
                    //             else if ($ModuleName == "contacts" )
                    //             $baseQuery .= " OR `contacts`.vendor_account_name IN ($vendoraccids_string)";
                    //             else if ($ModuleName == "contracts" )
                    //             $baseQuery .= " OR `contracts`.account_name IN ($vendoraccids_string)";
                    //         }

                    //         // Append the rest of the conditions
                    //         $baseQuery .= ")";

                    //         // Prepare the query string (without execution)
                    //         $Query = $baseQuery;

                    //         // Optional: You can output or log the query to see the final result
                    //         // echo $query;
                    //         // die; // Make sure there's no extra code after this point if you are using die here
                    //     } 
                        else {
                            //this if condition added by ptpatel for account_open_all for sourcing deal and opportunity module on date 23-03-2026
                            if(
                                (
                                    (isset($_REQUEST["srctabid"]) && (int) $_REQUEST["srctabid"] == 51) && 
                                    (preg_match('/^vendor_account_name$/', $_REQUEST['field']))
                                ) || 
                                (
                                    (isset($_REQUEST["srctabid"]) && (int) $_REQUEST["srctabid"] == 8) && 
                                    (preg_match('/^vendor_account_name$/', $_REQUEST['field']))
                                )
                                ){
                                $Query = "select $ColumnKey $join where $TableName.deleted=0 and (( $TableName.ownerid IN (" . $roleid['userid'] . ") ||  $TableName.creatorid IN (" . $roleid['userid'] . ") || $TableName.account_open_all = 1 ) ";
                            }
                            else
                                //end if condition added by ptpatel for account_open_to_all for sourcing deal and opportunity module on date 23-03-2026
                            $Query = "select $ColumnKey $join where $TableName.deleted=0 and (( $TableName.ownerid IN (" . $roleid['userid'] . ") ||  $TableName.creatorid IN (" . $roleid['userid'] . ")) ";
                        }
                        //added new condition on 5 nov 2025 for giving access from a to b
                       
                        if(!empty($ModuleName))
                        $condshared = AccessHelper::getVisibilityCondition($uid, $ModuleName,$TableName,$cond);
                        else $condshared='';

                        if(!empty($ModuleName))
                        $viewwhere = AccessHelper::getModuleViewRightsCondition($uid, $ModuleName,$TableName);
                        $viewwhereaccess = '';
                         if(!empty($viewwhere))
                            $viewwhereaccess .= " OR ".$viewwhere;
                       
                         
                       
                        if($condshared)
                        {
                            $Query .= " $condshared )  $cond $viewwhereaccess $orwhere ) $groupby order by $OrderBy $SortOrder";                           
                        }
                        else
                        $Query .= " $cond ) $viewwhereaccess $orwhere ) $groupby order by $OrderBy $SortOrder";
                        

                    }
                }
                $Query = str_replace(",$TableName.ownerid", ",concat(first_name,' ',last_name) as ownerid", $Query);
                // $Query = preg_replace('/\(\s*\)/', '', $Query);
                 $Query = preg_replace('/(?<![A-Za-z0-9_])\(\s*\)/', '', $Query);//this will not remove () if there is function infront of it added by ptpatel to resolve widget filter issue on date 14-11-2025
                // }
                //  echo "shre".$Query;exit;

            }/*else if($utypeid == '9' && $modulepermission['shareid'] == '0' ){
            if($TableName =='`Product`'){
                $join	.= " inner join Depot2Division on Depot2Division.
                         division_id = Product.ProductDivision_productdivisionid
                         INNER JOIN Depot ON Depot.depotid = Depot2Division.depotname";
                $Query	 = "select $ColumnKey $join where $TableName.deleted=0 and 
                       Depot.depotid='".$_SESSION['depot_code']."' $cond order by $OrderBy $SortOrder";
                $Query   = str_replace(",$TableName.ownerid",",concat(first_name,' ',last_name) as ownerid",$Query);
            }else if($TableName =='`PriceBook`'){
                $join	.= " inner join Depot on PriceBook.depotname = Depot.depotid";
                $Query	 = "select $ColumnKey $join where $TableName.deleted=0 and 
                       PriceBook.user_depot_code='".$_SESSION['depot_code']."' $cond order by $OrderBy $SortOrder";
                $Query   = str_replace(",$TableName.ownerid",",concat(first_name,' ',last_name) as ownerid",$Query);
            }else{
                $join	.= " inner join user on $TableName.user_depot_code=user.depot_code";
                $Query	 = "select $ColumnKey $join where $TableName.deleted=0 and 
                       $TableName.user_depot_code='".$_SESSION['depot_code']."' $cond order by $OrderBy $SortOrder";
                $Query   = str_replace(",$TableName.ownerid",",concat(first_name,' ',last_name) as ownerid",$Query);
            }
        }*/ 
        else {
                if ($TableName != '`sourcingdeal_contact_role`' && $TableName != '`opportunity_contact_role`')
                    $join .= " inner join user as owner on (owner.id=$TableName.ownerid)";
                 // added DevIt clevel role id to allow view all records
                   //commented on 03 sept 2025 as it is not approved yet,approved on 12sept 2025
                   $alowview = "No";
                    $role = \app\models\Role::find()
                        ->select([
                            "has_c_level" => new \yii\db\Expression(
                                "CASE WHEN rolename LIKE '%C Level%' THEN 'Yes' ELSE 'No' END"
                            )
                        ])
                        ->where(['roleid' => $userroleid])
                        ->asArray()
                        ->one();

                    if ($role && $role['has_c_level'] === 'Yes') {
                        $alowview =  "Yes";
                    } else {
                        $alowview = "No";
                    }
                    //commented on 03 sept 2025 as it is not approved yet
                    // $alowview = "No";

                    //get sourceid and sourcemodule aaded on 5 sept 2025 or allowing related list
                    $sourcemodule = Yii::$app->request->get('sourcemodule', '');
                    $sourcemodule = Html::encode($sourcemodule);  // Encode special characters to prevent XSS
                
                    $sourceid = Yii::$app->request->get('sourceid', '');
                    $sourceid = Html::encode($sourceid);  // Encode special characters to prevent XSS
                    //end on 5 sept 2025
                    // echo $isreference." ".$allowview." ".$sourceid." ".$sourcemodule;die;
                    //removed isreference from condition on 28 nov 2025
                    if ($isreference == 1 || ($TableName == '`sourcingdeal_contact_role`' || $TableName == '`opportunity_contact_role`') || $alowview == 'Yes' || (!empty($sourceid) && !empty($sourcemodule))) { // added DevIt clevel role id to allow view all records
                    $Query = "select $ColumnKey $join where $TableName.deleted=0  $cond $groupby order by $OrderBy $SortOrder";
                    }
                else
                    $Query = "select $ColumnKey $join where $TableName.deleted=0 and ( $TableName.ownerid IN (" . $roleid['userid'] . ") || $TableName.creatorid IN (" . $roleid['userid'] . ") ) $cond $groupby order by $OrderBy $SortOrder";


                $Query = str_replace(",$TableName.ownerid", ",concat(first_name,' ',last_name) as ownerid", $Query);
            }
            // echo $Query;die;
        }
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
    function getfilename($attachmentsid)
    {
        $connection = Yii::$app->db;
        $command = $connection->createCommand("select name from `attachments`  where attachmentsid=:attachmentsid")->bindParam(':attachmentsid', $attachmentsid);
        $Columns = $command->queryOne();
        return (isset($Columns['name']) && !empty($Columns['name'])) ? $Columns['name'] : 'N/A';
    }
}
