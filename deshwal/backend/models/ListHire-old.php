<?php

namespace app\models;

use Yii;

class ListHire{

	function listing($roleid,$modulepermission,$Query,$ColumnKey,$join,$OrderBy,$SortOrder,$TableName,$groupby){
		if($arr_cond = isset($_POST['searchres']) != ''){
		$model 	  	= new ListSearch;	
		$cond	  	= $model->SerachResult($TableName);
		//echo $cond;die;
		}
		else $cond = '';	
		$uid		= Yii::$app->user->id;
		//get profile of user
        $profilerr	=    Yii::$app->db->createCommand("SELECT profileid FROM role2profile rp join user2role ur on rp.roleid = ur.roleid WHERE  ur.userid = :uid")
        ->bindValue(':uid', $uid)
        ->queryOne();
       
        $profileid = $profilerr['profileid'];
        //now check for global action
        $hasadminpower =    Yii::$app->db->createCommand("SELECT count(*) as cnt FROM `profile2globalpermissions` where globalactionid in (1,2) and globalactionpermission=0 and profileid = :profileid")
        ->bindValue(':profileid', $profileid)
        ->queryOne();
        // echo $hasadminpower['cnt'];die;
        //print_r($hasadminpower);die;
        if($hasadminpower['cnt'] == 2 )
        {
            $isadmin = 1;
            $access = 1;
           
        }
		//echo $TableName;die;
		if($isadmin=='1'){
			if($TableName =='`user`'){
			$Query="select DISTINCT(id) as RecordId,user.user_name,yearname as fyear,
				user.is_admin ,user.first_name,user.last_name,title,minenamename as mine_name,
				concat(manpower.first_name,' ',manpower.last_name) as manpower_name,
				user.employee_code,contractormaster.company_name as company_name,
				UserType.user_type as utypeid
				from user inner join minename on minename.minenameid= user.mine_name
				left join manpower on manpower.manpowerid= user.manpower_name 
				inner join fyear on fyear.yearid= user.fyear
				inner join UserType on UserType.utypeid = user.utypeid
				INNER JOIN contractormaster on contractormaster.contractormaster_id = user.company_name
				where $TableName.deleted=0 $cond $groupby order by $OrderBy $SortOrder";
			}else{
			$join.=" inner join user on (user.id=$TableName.ownerid)";
			$Query="select $ColumnKey $join where $TableName.deleted=0 $cond $groupby order by $OrderBy $SortOrder";
			$Query = str_replace(",$TableName.ownerid",",concat(first_name,' ',last_name) as ownerid",$Query);
			}
		}else{
			if($modulepermission['shareid'] == '1' || $modulepermission['shareid'] == '2' || $modulepermission['shareid'] == '0'){
				//echo "w";
				if($TableName =='`user`'){
					$Query="select DISTINCT(id) as RecordId,user.user_name,yearname as fyear,
						user.is_admin ,user.first_name,user.last_name,title,minenamename as mine_name,
						concat(manpower.first_name,' ',manpower.last_name) as manpower_name,
						user.employee_code,contractormaster.company_name as company_name,
						UserType.user_type as utypeid
						from user inner join minename on minename.minenameid= user.mine_name
						left join manpower on manpower.manpowerid= user.manpower_name 
						inner join fyear on fyear.yearid= user.fyear
						inner join UserType on UserType.utypeid = user.utypeid
						INNER JOIN contractormaster on contractormaster.contractormaster_id = user.company_name
						where $TableName.deleted=0 and $TableName.ownerid IN (".$roleid['userid'].") 
				$cond $groupby order by $OrderBy $SortOrder";
					}else{
						
					
				$uid = $_SESSION[Yii::$app->params['dirName'].'_id'];
				$join.=" inner join user on (user.id=$TableName.ownerid)";
				$Query="select $ColumnKey $join where $TableName.deleted=0 and 
						   $TableName.ownerid='".$uid."' $cond $groupby order by $OrderBy $SortOrder";
				$Query="select $ColumnKey $join where $TableName.deleted=0 and $TableName.ownerid IN (".$roleid['userid'].") 
				$cond $groupby order by $OrderBy $SortOrder";
				$Query = str_replace(",$TableName.ownerid",",concat(first_name,' ',last_name) as ownerid",$Query);
					}
					//echo $Query;

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
			}*/else{
			$join.=" inner join user on (user.id=$TableName.ownerid)";
			$Query="select $ColumnKey $join where $TableName.deleted=0 and $TableName.ownerid IN (".$roleid['userid'].") 
				$cond $groupby order by $OrderBy $SortOrder";
			$Query = str_replace(",$TableName.ownerid",",concat(first_name,' ',last_name) as ownerid",$Query);
			}
		}
		//die;
		// echo $Query;die;
		return $Query;
	}
}
