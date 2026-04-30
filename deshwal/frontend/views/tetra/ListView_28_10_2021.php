<?php 
$ModuleName=$ActionList['ModuleName'];
$ModuleLabel=$ActionList['ModuleLabel'];
$siteDir=Yii::app()->params['dirName'];
 $user_id=$_SESSION[$siteDir."_id"];
//echo "<pre>";
//print_r($ModuleName);
//die;
$ActionName=$ActionList['ActionName'];
$OrderBy=$ActionList['OrderBy'];
$SortOrder=$ActionList['SortOrder'];
$val = explode(",",$operation['opt']);
$permod = $operation['name'];
$module = $ModuleName;
$isDepotUser = $operation['opt'];
$currentdate = date('Y-m-d');

if($SortOrder=="ASC")
{
$NextOrder="DESC";
$SortClass="glyphicon glyphicon-chevron-down";
}
else
{
$NextOrder="ASC";
$SortClass="glyphicon glyphicon-chevron-up";
}
//echo "<br>ModuleName=$ModuleName and ActionName=$ActionName";
$ActionUrl=Yii::app()->createAbsoluteUrl($ModuleName)."/";
//echo "<br>ActionUrl=$ActionUrl";
?>		
<div class="row" id="fullpage">
	<!-- Left side -->
	<?php include_once 'LeftSide.php' ?>

<?php 
function monthclosedatecheck($sub_id_serch,$module,$uid)
{
$dateexplode=explode('/',$sub_id_serch);
$month=$dateexplode['1'];//20-09-2018
$year=$dateexplode['2'];
$sub_id_serch=$month."-".$year;

//echo $sub_id_serch;

$query = "select parent from tab where name=:module";
    $connection = Yii::app()->db;
	$command = $connection->createCommand($query); 
	$command->bindParam(':module',$module,PDO::PARAM_STR);
    $Columns = $command->queryRow();
    $parent = $Columns['parent'];
    $querya = "select is_admin from users where users.id=:uid";
    $connection = Yii::app()->db;
	$commanda = $connection->createCommand($querya);
	$commanda->bindParam(':uid',$uid,PDO::PARAM_INT);
	$Columnsa = $commanda->queryRow();
	
    $check_admin = $Columnsa['is_admin'];
//p=2
   
if($parent=='1' || $parent=='9'|| $parent=='10'){
$parentid=4;
}else{

if($month=='1' || $month=='2' ||$month=='3'){
$fyear1=$year-1;
$fyear=$fyear1."-".$year;
}else{
$fyear1=$year+1;
$fyear= $year.'-'.$fyear1;

}
$query3 = "select yearid from fyear where yearname=:fyear";
    $connection = Yii::app()->db;
	$command3 = $connection->createCommand($query3);
	$command3->bindParam(':fyear',$fyear,PDO::PARAM_STR);
	$Columns3 = $command3->queryRow();
	
    $yearid = $Columns3['yearid'];


//$query1 = "select count(*) as count from mnth_close where cl_month='$month' and cl_f_year='$yearid' and deleted='0'";
$Columns1 = Yii::app()->db->createCommand()->select('count(*) as count')->from('mnth_close')
->where('cl_month=:month and cl_f_year=:yearid and deleted=:deleted',array(':month'=>$month,':yearid'=>$yearid,':deleted'=>0))
->queryRow();	
$count = $Columns1['count'];

if($count>0){
$parentid=3;
}else{
$parentid=4;

}

}

if($check_admin=='1'){

$parentid=4;
}else{
$parentid=$parentid;
}

return $parentid;



}


?>

	<!-- Right side contents -->
	<div class="col-sm-10 rightside-page" id="rightside-main">
		<?php include_once "ListViewHeader.php";?>
		<span id="collapsebtn" class="glyphicon glyphicon-chevron-left toggleButton" data-toggle="collapse" data-target="#leftsiede-summary"></span>
		<div class="wrapper1"><!-- top scroll for table -->
			<div class="div1"></div>
		</div>
		<input type="hidden" value="<?php echo $module; ?>" id="module" name="module"/>
		<input type="hidden" value="<?php echo $isDepotUser; ?>" id="isDepotUser" name="isDepotUser"/>

		<div class="wrapper2"><!-- Wrapper for scroll -->
			<div class="div2">
				<table class="table table-bordered table-striped table-hover" id="listingTable"><!-- Listing Table -->
					<thead>
						<tr class="customerForm-header">
							<?php //print_r($RecordList);
							$col_span=count($ColumnList)+1;
							foreach ($ColumnList as $key=> $Column): ?>
							<th id="<?php echo $key; ?>" class="shorter" order-data="asc" nowrap=""><a href="<?php echo $ActionUrl.'List'; ?>/OrderBy/<?php echo $key; ?>/SortOrder/<?php echo $NextOrder;?>"><?php echo $Column;?>
							<?php if($SortOrder!="" and $key==$OrderBy):?>
							<span class ="<?php echo $SortClass;?>"> </span></a>
							<?php endif;?>
							</th>
							<?php endforeach;?>
							<th nowrap=""><a href="#" class="dropdown-toggle">Action</a></th>
						</tr>
						<?php $url = $ActionUrl.'List';
							echo CHtml::beginForm($url, 'POST',array("name"=>"listsearchfm","id"=>"listsearchfm")); ?>
						<input type="hidden" value="<?php echo $listserchvals['startdate']; ?>" name="startdate" id="startdate" /> 
						<input type="hidden" value="<?php echo $listserchvals['enddate']; ?>" name="enddate" id="enddate" /> 
						<tr>
							<?php echo $listserchvals['vals']; ?>
							<th><button type="button" class="btn btn-info" id="listsearch" name="listsearch" onclick="listsearchfm.submit();">Search</button></th>
						</tr>
						<?php echo CHtml::endForm();?>
					</thead>

					<tbody>
						<?php //print_r($RecordList);
						if(count($RecordList)>0):
						foreach ($RecordList as $Record): 
						$dt= str_replace("/","-",$Record['date']);
						$currdt = date('Y-m-d',strtotime($dt)); 
						$recdate = date('Y-m-d', strtotime('+1 day', strtotime($currdt)));?>

						<!--**************khushboo Code Start*********************-->	



						<?php if($modulepermission['shareid'] == '0') {




 ?>
						<?php 
									$Record['createdtime'] = date('d/m/Y H:i:s',strtotime($Record['createdtime'])); 
									$Record['modified'] = date('d/m/Y H:i:s',strtotime($Record['modified']));					 
						?>
						<tr>
							<?php foreach ($ColumnList as $key=> $Column):

if($Column=='Date'){

//echo $Column."".$Record[$key];//18/07/2018  
$valuecheck=monthclosedatecheck($Record[$key],$module,$user_id);


}


?> 
							<td class="cursorPointer" onclick="window.location ='<?php echo $ActionUrl.'Summary/Record/'.$Record['RecordId']; ?>'"><?php echo strip_tags($Record[$key]);?></td>
							<?php endforeach;?>
							<td nowrap="">
								<span><a href="<?php echo $ActionUrl;?>Detail/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-list" alt="Details" title="Details"></span></a></span>
								<?php if(in_array('2',$val) and $module == $permod){ ?>
								<?php } if($valuecheck=='3'){ ?> <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>  <?php } else{ if($operation['opt'] =='1') { if($twantyfour == '1' && $recdate == $currentdate) { ?> 
								<span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span>
								<?php } else if ($user_id=='1') { ?> <span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span> <?php } else{ ?>
								<?php } } else { if($twantyfour == '1' && $recdate == $currentdate) { ?>
								<span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span> 
								<?php }else{  if($user_id=='165'||$user_id=='178'||$user_id=='166'||$user_id=='133'||$user_id=='119'||$user_id=='127'||$user_id=='208'||$user_id=='220'||$user_id=='221'||$user_id=='217'||$user_id=='222'){?>  <span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span>  ?>
								<?php } } } } ?>

								<?php if(in_array('3',$val) and $module == $permod){ ?>
								<?php } else if($operation['opt'] =='1') { ?>
								<span><a href="<?php echo $ActionUrl;?>Delete/Record/<?php echo $Record['RecordId']; ?>" onclick="return checkDelete()"><span class="glyphicon glyphicon-trash" alt="Delete" title="Delete"></span></a></span>
								<?php } else { ?> 
								<span><a href="<?php echo $ActionUrl;?>Delete/Record/<?php echo $Record['RecordId']; ?>" onclick="return checkDelete()"><span class="glyphicon glyphicon-trash" alt="Delete" title="Delete"></span></a></span>
								<?php } ?>
							</td>
						</tr>

						<?php } else if($modulepermission['shareid'] == '1') { ?>
						<?php 
									$Record['createdtime'] = date('d/m/Y h:i:s',strtotime($Record['createdtime'])); 
									$Record['modified'] = date('d/m/Y h:i:s',strtotime($Record['modified']));?>
						<tr>
							<?php foreach ($ColumnList as $key=> $Column): 

if($Column=='Date'){

//echo $Column."".$Record[$key];//18/07/2018  
$valuecheck=monthclosedatecheck($Record[$key],$module,$user_id);



}

?> 
							<td class="cursorPointer" onclick="window.location ='<?php echo $ActionUrl.'Summary/Record/'.$Record['RecordId']; ?>'"><?php echo strip_tags($Record[$key]);?></td>
							<?php endforeach;?>
							<td nowrap="">
								<span><a href="<?php echo $ActionUrl;?>Detail/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-list" alt="Details" title="Details"></span></a></span>
								<?php if($valuecheck=='3'){ ?> <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php }else { if($twantyfour == '1' && $recdate == $currentdate) { ?>
								<span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span> 
								<?php } else if ($user_id=='1') { ?> <span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span> <?php } else{?>
								<?php } } ?>
							</td>
						</tr>

						<?php } else if($modulepermission['shareid'] == '2'){ ?>
						<?php 
									$Record['createdtime'] = date('d/m/Y h:i:s',strtotime($Record['createdtime'])); 
									$Record['modified'] = date('d/m/Y h:i:s',strtotime($Record['modified']));					 
						?>
						<tr>
							<?php foreach ($ColumnList as $key=> $Column):


if($Column=='Date'){

//echo $Column."".$Record[$key];//18/07/2018  
$valuecheck=monthclosedatecheck($Record[$key],$module,$user_id);



}

?> 
							<td class="cursorPointer" onclick="window.location ='<?php echo $ActionUrl.'Summary/Record/'.$Record['RecordId']; ?>'"><?php echo strip_tags($Record[$key]);?></td>
							<?php endforeach;?>
							<td nowrap="">
								<span><a href="<?php echo $ActionUrl;?>Detail/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-list" alt="Details" title="Details"></span></a></span>
								<?php if($valuecheck=='3'){ ?> <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php } else { if($twantyfour == '1' && $recdate == $currentdate){?>
								<span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span> 
								<?php } else if ($user_id=='1') { ?> <span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span> <?php }else{ ?>
								<?php } } ?>
								<span><a href="<?php echo $ActionUrl;?>Delete/Record/<?php echo $Record['RecordId']; ?>" onclick="return checkDelete()"><span class="glyphicon glyphicon-trash" alt="Delete" title="Delete"></span></a></span>
							</td>
						</tr>

						<?php } else if($modulepermission['shareid'] == '3'){ ?>
						<?php 
									$Record['createdtime'] = date('d/m/Y h:i:s',strtotime($Record['createdtime'])); 
									$Record['modified'] = date('d/m/Y h:i:s',strtotime($Record['modified']));					 
						?>
						<tr>
							<?php foreach ($ColumnList as $key=> $Column):

if($Column=='Date'){

//echo $Column."".$Record[$key];//18/07/2018  
$valuecheck=monthclosedatecheck($Record[$key],$module,$user_id);



}

?> 
							<td class="cursorPointer" onclick="window.location ='<?php echo $ActionUrl.'Summary/Record/'.$Record['RecordId']; ?>'"><?php echo strip_tags($Record[$key]);?></td>
							<?php endforeach;?>
							<td nowrap="">
								<span><a href="<?php echo $ActionUrl;?>Detail/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-list" alt="Details" title="Details"></span></a></span>

								<?php if(in_array('2',$val) and $module == $permod){ ?>
								<?php } if($valuecheck=='3'){ ?> <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php } else {  if($operation['opt'] =='1') { if($twantyfour == '1' && $recdate == $currentdate) { ?>
								<span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span>
								<?php } else if ($user_id=='1') { ?> <span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span> <?php } else { ?>
								<?php } } else { if($twantyfour == '1' && $recdate == $currentdate) { ?>
								<span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span> 
								<?php } else { if($user_id=='165'||$user_id=='178'||$user_id=='166'||$user_id=='133'||$user_id=='119'||$user_id=='127'||$user_id=='208'||$user_id=='220'||$user_id=='221'||$user_id=='217'||$user_id=='222'){?>  <span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span> 
								<?php } } } } ?>

								<?php if(in_array('3',$val) and $module == $permod){ ?>
								<?php } else if($operation['opt'] =='1') { ?>
								<span><a href="<?php echo $ActionUrl;?>Delete/Record/<?php echo $Record['RecordId']; ?>" onclick="return checkDelete()"><span class="glyphicon glyphicon-trash" alt="Delete" title="Delete"></span></a></span>
								<?php } else { ?> 
								<span><a href="<?php echo $ActionUrl;?>Delete/Record/<?php echo $Record['RecordId']; ?>" onclick="return checkDelete()"><span class="glyphicon glyphicon-trash" alt="Delete" title="Delete"></span></a></span>
								<?php } ?>
							</td>
						</tr>

						<?php } else { ?>
						<?php 
									$Record['createdtime'] = date('d/m/Y h:i:s',strtotime($Record['createdtime'])); 
									$Record['modified'] = date('d/m/Y h:i:s',strtotime($Record['modified']));
						?>
						<tr>
							<?php foreach ($ColumnList as $key=> $Column):


//print_r($Column);

if($Column=='Date'){

//echo $Column."".$Record[$key];//18/07/2018  
$valuecheck=monthclosedatecheck($Record[$key],$module,$user_id);

}
?> 
							<td class="cursorPointer" onclick="window.location ='<?php echo $ActionUrl.'Summary/Record/'.$Record['RecordId']; ?>'"><?php echo strip_tags($Record[$key]);?></td>
							<?php endforeach;?>
							<td nowrap="">
								<span><a href="<?php echo $ActionUrl;?>Detail/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-list" alt="Details" title="Details"></span></a></span>
								<?php if($valuecheck=='3'){ ?>  <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>  <?php } else { if($twantyfour == '1' && $recdate == $currentdate) { ?>
								<span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span> 
								<?php } else if ($user_id=='1' ||$user_id=='222' ||$user_id=='217'||$user_id=='208') { ?> <span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span> <?php } else { ?>
								<?php } } ?>
								<span><a href="<?php echo $ActionUrl;?>Delete/Record/<?php echo $Record['RecordId']; ?>" onclick="return checkDelete()"><span class="glyphicon glyphicon-trash" alt="Delete" title="Delete"></span></a></span>
							</td>
						</tr>
						<?php } ?>
						<!--**************khushboo Code End*********************-->
						<?php endforeach; else :?>
						<tr>
							<td class="text-center" colspan="<?php echo $col_span;?>">No Record Found</td><!-- Show when records are empty -->
						</tr>
						<?php endif;?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
