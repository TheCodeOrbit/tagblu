<?php

namespace app\models;

use Yii;
class Pageination {
	function TotalPagecount($RecordList){
		$connection	= Yii::$app->db;
		$count 		= count ($RecordList);
		return $count;
		
	}
	function TotalRecords($TotalPagecount){
		//echo $TotalPagecount;die;
		$connection	= Yii::$app->db;
		$command	= $connection->createCommand($TotalPagecount);
		$rowCount	= $command->queryAll();
	//	print_r($rowCount);die;

		$count 		= count ($rowCount);
		$uid		= Yii::$app->session[Yii::$app->params['dirName'].'_id'];	
		$orderby	= isset($_REQUEST['orderby'])?$_REQUEST['orderby']:'';
		$nextorder	= isset($_REQUEST['nextorder'])?$_REQUEST['nextorder']:'';	
	// 	$admin_query	= Yii::$app->db->createCommand()->select('records_show,UserType.utypeid')
	// 			  ->from('UserType')
	// 			  ->join('users' , 'users.utypeid = UserType.utypeid')
	// 			  ->where("id='$uid'")
	// 			  ->queryAll();

	// //	print_r($admin_query);die;

	// 	$records	= $admin_query[0]['records_show'];
		$countid 	= $count;
		$records = 10;
		$totpagecount   = ceil($countid/$records);

		
		if(isset($_REQUEST['pageNumber']) && $_REQUEST['pageNumber'] !=''){	
		$pageNumber 	= $_REQUEST['pageNumber'];
		}else{
		$pageNumber 	= 0;//$_REQUEST['pageNumber'];
		}

		if(isset($_REQUEST['pageNumberpre']) && $_REQUEST['pageNumberpre'] !=''){	
		$pageNumberpre 	= $_REQUEST['pageNumberpre'];
		}else{
		$pageNumberpre 	=0;///$_REQUEST['pageNumberpre'];
		}
	
		if(isset($_REQUEST['pagejump']) && $_REQUEST['pagejump'] !=''){	
		$pagejumpno 	= $_REQUEST['pagejump'];
		}else{
		$pagejumpno 	= '';//$_REQUEST['pagejump'];
		}
		
			
		if($pageNumber !=''){
			$nextPageNumber 	= $pageNumber + 1;
			$nextPageNumberpre	= $pageNumber + 1;
			$pagejumps 		= $nextPageNumber;
			$pageEndRange 		= (($nextPageNumber * $records) - 1);
			$pageStartRange 	= (($pageEndRange - $records) + 1);
			$pageStartRanges	= $pageStartRange + 1;
			if($pageEndRange > $countid){
			$pageEndRanges      = $countid;
			}else{
			$pageEndRanges      = $pageEndRange + 1;
			}
			if($totpagecount > ($pageNumber +1)){
				$nextPageExist 	= 'TRUE';
			}else{
				$nextPageExist 	= 'FALSE';
			}
			if($totpagecount >= ($pageNumber +1)){
				$previousPageExist 	= 'TRUE';
			}else{
				$previousPageExist 	= 'FALSE';
			}
		}else if($pagejumpno !=''){
			//$pagejump 		= $pagejumpno -1;
			$pagejump 		= $pagejumpno;
			$nextPageNumber		= $pagejump;
			$nextPageNumberpre	= $pagejump;
			if($pagejumpno == 0){
			$pagejumps 		= 1;
			}else if($pagejumpno > $pageNumber){
			$pagejumps 		= $pagejumpno;
			}else{
			$pagejumps 		= $pagejumpno;
			}
			$pageStartRange		= ($pagejump * $records);
			//$pageEndRange		= $pageStartRange + 1;
			$pageEndRange		= ($pageStartRange + $records);
			$pageStartRanges     	= $pageStartRange + 1;
			if($pageEndRange > $countid){
			$pageEndRanges      = $countid;
			}else{
			$pageEndRanges      = $pageEndRange;
			}
			if($totpagecount > ($pagejump +1)){
				$nextPageExist 	= 'TRUE';
			}else{
				$nextPageExist 	= 'FALSE';
			}
			if(($pagejumpno - 1) > 1){
				$previousPageExist 	= 'TRUE';
			}else{
				$previousPageExist 	= 'FALSE';
			}
		}else if(isset($_GET['textsearch']) && $_GET['textsearch'] !=''){
			$pageStartRanges	= 1;
			$pageStartRange		= 0;
			if($records > $countid){
				$nextPageExist 	= 'FALSE';
				$pageEndRanges  = $countid;
			}else{
				$nextPageExist 	= 'TRUE';
				$pageEndRanges  = $records;
			}
			$previousPageExist 	= 'FALSE';
		}else if($pageNumberpre !=''){
			$nextPageNumber		= $pageNumberpre -1;
			$nextPageNumberpre	= $pageNumberpre -1;
			$pagejumps 		= $nextPageNumber;
			$pageEndRange		= (($nextPageNumber * $records));
			$pageStartRange		= $pageEndRange - $records;
			$pageStartRanges	= $pageStartRange + 1;
			$pageEndRanges     	= $pageEndRange + 1;
			if($pageEndRange > $countid){
			$pageEndRanges      = $countid;
			}else{
			$pageEndRanges      = $pageEndRange;
			}
			if($pageNumberpre > 1){
				$nextPageExist 	= 'TRUE';
			}else{
				$nextPageExist 	= 'FALSE';
			}
			if(($pageNumberpre - 1) > 1){
				$previousPageExist 	= 'TRUE';
			}else{
				$previousPageExist 	= 'FALSE';
			}
		}else{

			if($countid > $records){
			$nextPageExist = 'TRUE';
			}else{
			$nextPageExist = 'FALSE';
			}
			$pageStartRanges      = 1;
			if($records > $countid){
			$pageEndRanges      = $countid;
			}else{
			$pageEndRanges      = $records;
			}
			$nextPageNumber       = 1;
			$nextPageNumberpre    = 1;
			$previousPageExist = 'FALSE';
			$pagejumps = '1';

		}
		$pageStartRangepagejump ='';//added

		return array('noofpages'=>$totpagecount,'defaultrecord'=>$records,'totrecords'=>$countid,'nextPageNumber'=>$nextPageNumber,'pageEndRange'=>$pageEndRange,'pageStartRange'=>$pageStartRange,'previousPageExists'=>$previousPageExist,'nextPageExists'=>$nextPageExist,'pagejumps'=>$pagejumps,'pageStartRangepagejump'=>$pageStartRangepagejump,'pageStartRanges'=>$pageStartRanges,'pageEndRanges'=>$pageEndRanges,'orderby'=>$orderby,'nextorder'=>$nextorder);
	}
}
