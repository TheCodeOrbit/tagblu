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
<div id="ListDrill" class="show">
	<!-- Left side -->
	<?php //include_once 'LeftSide.php' ?>

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
	<div id="rightside-main">
		<?php //include_once "ListViewHeader.php";?>
		<span id="collapsebtn" class="glyphicon glyphicon-chevron-left toggleButton" data-toggle="collapse" data-target="#leftsiede-summary"></span>
		<div><!-- top scroll for table -->
			<div class="div1"></div>
		</div>
		<input type="hidden" value="<?php echo $module; ?>" id="module" name="module"/>
		<input type="hidden" value="<?php echo $isDepotUser; ?>" id="isDepotUser" name="isDepotUser"/>

		<div><!-- Wrapper for scroll -->
			<div id="ListDrill" class="show">
			<div class="body-menu d-flex justify-content-between">
                <div>
                    <p class="body-heading">List <?php echo ucfirst($module); ?> Data</p>
                </div>
                <?php $url = $ActionUrl.'List';
							echo CHtml::beginForm($url, 'POST',array("name"=>"listsearchfm","id"=>"listsearchfm")); ?>
						<input type="hidden" value="<?php echo $listserchvals['startdate']; ?>" name="startdate" id="startdate" /> 
						<input type="hidden" value="<?php echo $listserchvals['enddate']; ?>" name="enddate" id="enddate" /> 
						<div class="d-flex transform-center">
							<?php echo $listserchvals['vals']; ?>
							<!--<th><button type="button" class="btn btn-info" id="listsearch" name="listsearch" onclick="listsearchfm.submit();">Search</button></th>-->
						</div>
						<?php echo CHtml::endForm();?>
			<?php //echo "<br>Operation opt=".$operation['opt'];
				//print_r($val);
				if(in_array('5',$val) or $operation['opt'] =='1') {?>			
						<button type="button" class="btn btn-primary input-save d-flex justify-content-center align-items-center" data-bs-toggle="modal" data-bs-target="#exampleModal"  onclick="window.location ='<?php echo $ActionUrl.'ApproveList'; ?>'">
                    <svg width="21" height="22" viewBox="0 0 21 22" fill="currentcolor" xmlns="http://www.w3.org/2000/svg" class="me-3">
                        <g clip-path="url(#clip0_1333_25820)">
                        <path d="M19.6875 13.6251C19.6888 12.968 19.5257 12.321 19.2129 11.7431C18.9001 11.1651 18.4477 10.6747 17.8968 10.3164C17.3459 9.95812 16.7141 9.74341 16.059 9.69183C15.4039 9.64026 14.7463 9.75347 14.1462 10.0211C13.546 10.2888 13.0224 10.7024 12.623 11.2243C12.2237 11.7461 11.9613 12.3597 11.8598 13.0089C11.7583 13.6582 11.8208 14.3225 12.0418 14.9414C12.2628 15.5602 12.6352 16.1139 13.125 16.552V21.5001L15.75 20.2572L18.375 21.5001V16.552C18.7876 16.1841 19.1178 15.7332 19.3439 15.2288C19.5701 14.7244 19.6872 14.1779 19.6875 13.6251ZM17.0625 19.4264L15.75 18.8049L14.4375 19.4264V17.333C15.2857 17.6393 16.2143 17.6393 17.0625 17.333V19.4264ZM15.75 16.2501C15.2308 16.2501 14.7233 16.0962 14.2916 15.8078C13.8599 15.5193 13.5235 15.1093 13.3248 14.6297C13.1261 14.15 13.0741 13.6222 13.1754 13.113C13.2767 12.6038 13.5267 12.1361 13.8938 11.769C14.261 11.4019 14.7287 11.1519 15.2379 11.0506C15.7471 10.9493 16.2749 11.0013 16.7545 11.2C17.2342 11.3986 17.6442 11.7351 17.9326 12.1668C18.221 12.5985 18.375 13.106 18.375 13.6251C18.3741 14.3211 18.0973 14.9882 17.6052 15.4803C17.1131 15.9724 16.4459 16.2493 15.75 16.2501Z"/>
                        <path d="M16.4062 3.78125H14.4375V3.125C14.4365 2.77722 14.2978 2.44399 14.0519 2.19807C13.806 1.95215 13.4728 1.81354 13.125 1.8125H7.875C7.52722 1.81354 7.19399 1.95215 6.94807 2.19807C6.70215 2.44399 6.56354 2.77722 6.5625 3.125V3.78125H4.59375C4.24597 3.78229 3.91274 3.9209 3.66682 4.16682C3.4209 4.41274 3.28229 4.74597 3.28125 5.09375V18.875C3.28229 19.2228 3.4209 19.556 3.66682 19.8019C3.91274 20.0478 4.24597 20.1865 4.59375 20.1875H10.5V18.875H4.59375V5.09375H6.5625V7.0625H14.4375V5.09375H16.4062V8.375H17.7188V5.09375C17.7177 4.74597 17.5791 4.41274 17.3332 4.16682C17.0873 3.9209 16.754 3.78229 16.4062 3.78125ZM13.125 5.75H7.875V3.125H13.125V5.75Z"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_1333_25820">
                        <rect width="21" height="21" fill="white" transform="translate(0 0.5)"/>
                        </clipPath>
                        </defs>
                    </svg>
                    <span>approve</span>
                </button>
		<?php }?>	
          	</div>      
				<div class="body-container flex flex-col justify-content-between align-items-center"><!-- Main List Body div-->			
				<div class="body-outline general-list height-full-body">
					<div data-simplebar class="adjusted-height">
						<table class="table-view table table-striped">
						<thead>
							<tr class="list-general table-primary">
								<?php //print_r($RecordList);
								$col_span=count($ColumnList)+1;
								foreach ($ColumnList as $key=> $Column): ?>
								<th><?php echo $Column;?></th>
								<?php endforeach;?>
								<th>Action</th>
								<th>Status</th>
							</tr>
							<!--<?php $url = $ActionUrl.'List';
								echo CHtml::beginForm($url, 'POST',array("name"=>"listsearchfm","id"=>"listsearchfm")); ?>
							<input type="hidden" value="<?php echo $listserchvals['startdate']; ?>" name="startdate" id="startdate" /> 
							<input type="hidden" value="<?php echo $listserchvals['enddate']; ?>" name="enddate" id="enddate" /> 
							<tr>
								<?php echo $listserchvals['vals']; ?>
								<th><button type="button" class="btn btn-info" id="listsearch" name="listsearch" onclick="listsearchfm.submit();">Search</button></th>
							</tr>
							<?php echo CHtml::endForm();?> -->
						</thead>

						<tbody>
							<?php //print_r($RecordList);
							
							$addUrl="{$ActionUrl}Create";
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
								<td class="cursorPointer text-center"><?php echo strip_tags($Record[$key]);?></td>
								<?php endforeach;?>
								<!-- Action TD Added -->
								<td>
									<div class="d-flex justify-content-evenly">
									<a href="<?php echo $ActionUrl;?>Detail/Record/<?php echo $Record['RecordId']; ?>">
										<div class="action-icon-container d-flex justify-content-center align-items-center">
											<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
												<path d="M17.02 19.5C17.85 19.5 18.5228 18.8284 18.5228 18C18.5228 17.1716 17.85 16.5 17.02 16.5C16.1899 16.5 15.5171 17.1716 15.5171 18C15.5171 18.8284 16.1899 19.5 17.02 19.5Z" fill="#3D89CF"/>
												<path d="M22.8639 17.6093C22.3996 16.4287 21.5984 15.41 20.5596 14.6796C19.5208 13.9491 18.2901 13.539 17.02 13.5C15.7499 13.539 14.5192 13.9491 13.4804 14.6796C12.4416 15.41 11.6404 16.4287 11.1761 17.6093L11.0085 18L11.1761 18.3907C11.6404 19.5713 12.4416 20.59 13.4804 21.3204C14.5192 22.0509 15.7499 22.461 17.02 22.5C18.2901 22.461 19.5208 22.0509 20.5596 21.3204C21.5984 20.59 22.3996 19.5713 22.8639 18.3907L23.0314 18L22.8639 17.6093ZM17.02 21C16.4255 21 15.8444 20.8241 15.3501 20.4944C14.8558 20.1648 14.4706 19.6962 14.2431 19.148C14.0156 18.5999 13.956 17.9967 14.072 17.4147C14.188 16.8328 14.4743 16.2982 14.8946 15.8787C15.315 15.4591 15.8506 15.1734 16.4336 15.0576C17.0167 14.9419 17.621 15.0013 18.1702 15.2284C18.7195 15.4554 19.1889 15.8399 19.5192 16.3333C19.8494 16.8266 20.0257 17.4067 20.0257 18C20.0247 18.7953 19.7077 19.5578 19.1443 20.1202C18.5808 20.6826 17.8169 20.999 17.02 21Z" fill="#3D89CF"/>
												<path d="M5.74854 12.75H9.50569V14.25H5.74854V12.75Z" fill="#3D89CF"/>
												<path d="M5.74854 9H14.7657V10.5H5.74854V9Z" fill="#3D89CF"/>
												<path d="M5.74854 5.25H14.7657V6.75H5.74854V5.25Z" fill="#3D89CF"/>
												<path d="M17.0201 1.5H3.49432C3.0961 1.50119 2.71453 1.6596 2.43295 1.94065C2.15136 2.2217 1.99264 2.60254 1.99146 3V21C1.99264 21.3975 2.15136 21.7783 2.43295 22.0593C2.71453 22.3404 3.0961 22.4988 3.49432 22.5H9.50577V21H3.49432V3H17.0201V11.25H18.5229V3C18.5218 2.60254 18.363 2.2217 18.0815 1.94065C17.7999 1.6596 17.4183 1.50119 17.0201 1.5Z" fill="#3D89CF"/>
											</svg>
										</div>
										</a>
									<?php if(in_array('2',$val) and $module == $permod){ ?> 
									<?php } if($valuecheck=='3'){ ?>  <?php } else{ if($operation['opt'] =='1') { if($twantyfour == '1' && $recdate == $currentdate) { ?>
									<a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"> 
										<div class="action-icon-container d-flex justify-content-center align-items-center">
											<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
												<path d="M17.1102 5.40807L19.2322 7.52507L17.1102 5.40807ZM18.4748 3.54307L12.7368 9.27007C12.4404 9.56557 12.2382 9.94205 12.1557 10.3521L11.6257 13.0001L14.2788 12.4701C14.6896 12.3881 15.0663 12.1871 15.3628 11.8911L21.1008 6.16407C21.2732 5.99197 21.41 5.78766 21.5033 5.56281C21.5966 5.33795 21.6446 5.09695 21.6446 4.85357C21.6446 4.61019 21.5966 4.36919 21.5033 4.14433C21.41 3.91948 21.2732 3.71517 21.1008 3.54307C20.9284 3.37097 20.7237 3.23446 20.4984 3.14132C20.2731 3.04818 20.0316 3.00024 19.7878 3.00024C19.5439 3.00024 19.3025 3.04818 19.0772 3.14132C18.8519 3.23446 18.6472 3.37097 18.4748 3.54307V3.54307Z" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M19.6409 15V18C19.6409 18.5304 19.4298 19.0391 19.054 19.4142C18.6782 19.7893 18.1686 20 17.6371 20H6.61612C6.08468 20 5.575 19.7893 5.19921 19.4142C4.82342 19.0391 4.6123 18.5304 4.6123 18V7C4.6123 6.46957 4.82342 5.96086 5.19921 5.58579C5.575 5.21071 6.08468 5 6.61612 5H9.62185" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</div>
										</a>  
										<?php } else if ($user_id=='1') { ?> <a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>">
											<div class="action-icon-container d-flex justify-content-center align-items-center">
												<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
													<path d="M17.1102 5.40807L19.2322 7.52507L17.1102 5.40807ZM18.4748 3.54307L12.7368 9.27007C12.4404 9.56557 12.2382 9.94205 12.1557 10.3521L11.6257 13.0001L14.2788 12.4701C14.6896 12.3881 15.0663 12.1871 15.3628 11.8911L21.1008 6.16407C21.2732 5.99197 21.41 5.78766 21.5033 5.56281C21.5966 5.33795 21.6446 5.09695 21.6446 4.85357C21.6446 4.61019 21.5966 4.36919 21.5033 4.14433C21.41 3.91948 21.2732 3.71517 21.1008 3.54307C20.9284 3.37097 20.7237 3.23446 20.4984 3.14132C20.2731 3.04818 20.0316 3.00024 19.7878 3.00024C19.5439 3.00024 19.3025 3.04818 19.0772 3.14132C18.8519 3.23446 18.6472 3.37097 18.4748 3.54307V3.54307Z" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
													<path d="M19.6409 15V18C19.6409 18.5304 19.4298 19.0391 19.054 19.4142C18.6782 19.7893 18.1686 20 17.6371 20H6.61612C6.08468 20 5.575 19.7893 5.19921 19.4142C4.82342 19.0391 4.6123 18.5304 4.6123 18V7C4.6123 6.46957 4.82342 5.96086 5.19921 5.58579C5.575 5.21071 6.08468 5 6.61612 5H9.62185" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</div>
										
										</a></a></span> <?php } else{ ?>  
										<?php } } else { if($twantyfour == '1' && $recdate == $currentdate) { ?>
										
										<a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>">
										<div class="action-icon-container d-flex justify-content-center align-items-center">
											<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
												<path d="M17.1102 5.40807L19.2322 7.52507L17.1102 5.40807ZM18.4748 3.54307L12.7368 9.27007C12.4404 9.56557 12.2382 9.94205 12.1557 10.3521L11.6257 13.0001L14.2788 12.4701C14.6896 12.3881 15.0663 12.1871 15.3628 11.8911L21.1008 6.16407C21.2732 5.99197 21.41 5.78766 21.5033 5.56281C21.5966 5.33795 21.6446 5.09695 21.6446 4.85357C21.6446 4.61019 21.5966 4.36919 21.5033 4.14433C21.41 3.91948 21.2732 3.71517 21.1008 3.54307C20.9284 3.37097 20.7237 3.23446 20.4984 3.14132C20.2731 3.04818 20.0316 3.00024 19.7878 3.00024C19.5439 3.00024 19.3025 3.04818 19.0772 3.14132C18.8519 3.23446 18.6472 3.37097 18.4748 3.54307V3.54307Z" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M19.6409 15V18C19.6409 18.5304 19.4298 19.0391 19.054 19.4142C18.6782 19.7893 18.1686 20 17.6371 20H6.61612C6.08468 20 5.575 19.7893 5.19921 19.4142C4.82342 19.0391 4.6123 18.5304 4.6123 18V7C4.6123 6.46957 4.82342 5.96086 5.19921 5.58579C5.575 5.21071 6.08468 5 6.61612 5H9.62185" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</div>
										
										</a>
										<?php }else{  if($user_id=='165'||$user_id=='178'||$user_id=='166'||$user_id=='133'||$user_id=='119'||$user_id=='127'||$user_id=='208'||$user_id=='220'||$user_id=='221'||$user_id=='217'||$user_id=='222'){?> 
										<a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>">
										<div class="action-icon-container d-flex justify-content-center align-items-center">
											<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
												<path d="M17.1102 5.40807L19.2322 7.52507L17.1102 5.40807ZM18.4748 3.54307L12.7368 9.27007C12.4404 9.56557 12.2382 9.94205 12.1557 10.3521L11.6257 13.0001L14.2788 12.4701C14.6896 12.3881 15.0663 12.1871 15.3628 11.8911L21.1008 6.16407C21.2732 5.99197 21.41 5.78766 21.5033 5.56281C21.5966 5.33795 21.6446 5.09695 21.6446 4.85357C21.6446 4.61019 21.5966 4.36919 21.5033 4.14433C21.41 3.91948 21.2732 3.71517 21.1008 3.54307C20.9284 3.37097 20.7237 3.23446 20.4984 3.14132C20.2731 3.04818 20.0316 3.00024 19.7878 3.00024C19.5439 3.00024 19.3025 3.04818 19.0772 3.14132C18.8519 3.23446 18.6472 3.37097 18.4748 3.54307V3.54307Z" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M19.6409 15V18C19.6409 18.5304 19.4298 19.0391 19.054 19.4142C18.6782 19.7893 18.1686 20 17.6371 20H6.61612C6.08468 20 5.575 19.7893 5.19921 19.4142C4.82342 19.0391 4.6123 18.5304 4.6123 18V7C4.6123 6.46957 4.82342 5.96086 5.19921 5.58579C5.575 5.21071 6.08468 5 6.61612 5H9.62185" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</div>
										</a>
										
										<?php } } } } ?>
										
										<?php if(in_array('3',$val) and $module == $permod){ ?>
									<?php } else if($operation['opt'] =='1') { ?>
									<a href="<?php echo $ActionUrl;?>Delete/Record/<?php echo $Record['RecordId']; ?>" onclick="return checkDelete()">
										<div class="action-icon-container d-flex justify-content-center align-items-center">
											<svg viewBox="0 0 18 19" class="action-icon action-icon--delete">
												<path d="M5.14414 15.2656H12.8539L13.2793 6.26562H4.71875L5.14414 15.2656Z"/>
												<path d="M15.1875 5H12.9375V3.59375C12.9375 2.97324 12.433 2.46875 11.8125 2.46875H6.1875C5.56699 2.46875 5.0625 2.97324 5.0625 3.59375V5H2.8125C2.50137 5 2.25 5.25137 2.25 5.5625V6.125C2.25 6.20234 2.31328 6.26562 2.39062 6.26562H3.45234L3.88652 15.459C3.91465 16.0584 4.41035 16.5312 5.00977 16.5312H12.9902C13.5914 16.5312 14.0854 16.0602 14.1135 15.459L14.5477 6.26562H15.6094C15.6867 6.26562 15.75 6.20234 15.75 6.125V5.5625C15.75 5.25137 15.4986 5 15.1875 5ZM6.32812 3.73438H11.6719V5H6.32812V3.73438ZM12.8549 15.2656H5.14512L4.71973 6.26562H13.2803L12.8549 15.2656Z"/>
											</svg>
										</div>
									</a>
							
							<?php } else { ?> 
									<a href="<?php echo $ActionUrl;?>Delete/Record/<?php echo $Record['RecordId']; ?>" onclick="return checkDelete()">
									<div class="action-icon-container d-flex justify-content-center align-items-center">
										<svg viewBox="0 0 18 19" class="action-icon action-icon--delete">
											<path d="M5.14414 15.2656H12.8539L13.2793 6.26562H4.71875L5.14414 15.2656Z"/>
											<path d="M15.1875 5H12.9375V3.59375C12.9375 2.97324 12.433 2.46875 11.8125 2.46875H6.1875C5.56699 2.46875 5.0625 2.97324 5.0625 3.59375V5H2.8125C2.50137 5 2.25 5.25137 2.25 5.5625V6.125C2.25 6.20234 2.31328 6.26562 2.39062 6.26562H3.45234L3.88652 15.459C3.91465 16.0584 4.41035 16.5312 5.00977 16.5312H12.9902C13.5914 16.5312 14.0854 16.0602 14.1135 15.459L14.5477 6.26562H15.6094C15.6867 6.26562 15.75 6.20234 15.75 6.125V5.5625C15.75 5.25137 15.4986 5 15.1875 5ZM6.32812 3.73438H11.6719V5H6.32812V3.73438ZM12.8549 15.2656H5.14512L4.71973 6.26562H13.2803L12.8549 15.2656Z"/>
										</svg>
									</div>
									</a>
									<?php } ?>
									</div>

								</td>
								<!-- Action TD End-->
							<!--	<td nowrap="">
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
								</td> -->
								
								<td>
									<div class="d-flex justify-content-center">
										<div class="circle bg-success"></div>
									</div>
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
								<td class="cursorPointer text-center" ><?php echo strip_tags($Record[$key]);?></td>
								<?php endforeach;?>
								
								<!-- Action TD Added -->
								<td>
									<div class="d-flex justify-content-evenly">
										<div class="action-icon-container d-flex justify-content-center align-items-center">
											<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
												<path d="M17.02 19.5C17.85 19.5 18.5228 18.8284 18.5228 18C18.5228 17.1716 17.85 16.5 17.02 16.5C16.1899 16.5 15.5171 17.1716 15.5171 18C15.5171 18.8284 16.1899 19.5 17.02 19.5Z" fill="#3D89CF"/>
												<path d="M22.8639 17.6093C22.3996 16.4287 21.5984 15.41 20.5596 14.6796C19.5208 13.9491 18.2901 13.539 17.02 13.5C15.7499 13.539 14.5192 13.9491 13.4804 14.6796C12.4416 15.41 11.6404 16.4287 11.1761 17.6093L11.0085 18L11.1761 18.3907C11.6404 19.5713 12.4416 20.59 13.4804 21.3204C14.5192 22.0509 15.7499 22.461 17.02 22.5C18.2901 22.461 19.5208 22.0509 20.5596 21.3204C21.5984 20.59 22.3996 19.5713 22.8639 18.3907L23.0314 18L22.8639 17.6093ZM17.02 21C16.4255 21 15.8444 20.8241 15.3501 20.4944C14.8558 20.1648 14.4706 19.6962 14.2431 19.148C14.0156 18.5999 13.956 17.9967 14.072 17.4147C14.188 16.8328 14.4743 16.2982 14.8946 15.8787C15.315 15.4591 15.8506 15.1734 16.4336 15.0576C17.0167 14.9419 17.621 15.0013 18.1702 15.2284C18.7195 15.4554 19.1889 15.8399 19.5192 16.3333C19.8494 16.8266 20.0257 17.4067 20.0257 18C20.0247 18.7953 19.7077 19.5578 19.1443 20.1202C18.5808 20.6826 17.8169 20.999 17.02 21Z" fill="#3D89CF"/>
												<path d="M5.74854 12.75H9.50569V14.25H5.74854V12.75Z" fill="#3D89CF"/>
												<path d="M5.74854 9H14.7657V10.5H5.74854V9Z" fill="#3D89CF"/>
												<path d="M5.74854 5.25H14.7657V6.75H5.74854V5.25Z" fill="#3D89CF"/>
												<path d="M17.0201 1.5H3.49432C3.0961 1.50119 2.71453 1.6596 2.43295 1.94065C2.15136 2.2217 1.99264 2.60254 1.99146 3V21C1.99264 21.3975 2.15136 21.7783 2.43295 22.0593C2.71453 22.3404 3.0961 22.4988 3.49432 22.5H9.50577V21H3.49432V3H17.0201V11.25H18.5229V3C18.5218 2.60254 18.363 2.2217 18.0815 1.94065C17.7999 1.6596 17.4183 1.50119 17.0201 1.5Z" fill="#3D89CF"/>
											</svg>
										</div>
										
										<div class="action-icon-container d-flex justify-content-center align-items-center">
											<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
												<path d="M17.1102 5.40807L19.2322 7.52507L17.1102 5.40807ZM18.4748 3.54307L12.7368 9.27007C12.4404 9.56557 12.2382 9.94205 12.1557 10.3521L11.6257 13.0001L14.2788 12.4701C14.6896 12.3881 15.0663 12.1871 15.3628 11.8911L21.1008 6.16407C21.2732 5.99197 21.41 5.78766 21.5033 5.56281C21.5966 5.33795 21.6446 5.09695 21.6446 4.85357C21.6446 4.61019 21.5966 4.36919 21.5033 4.14433C21.41 3.91948 21.2732 3.71517 21.1008 3.54307C20.9284 3.37097 20.7237 3.23446 20.4984 3.14132C20.2731 3.04818 20.0316 3.00024 19.7878 3.00024C19.5439 3.00024 19.3025 3.04818 19.0772 3.14132C18.8519 3.23446 18.6472 3.37097 18.4748 3.54307V3.54307Z" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M19.6409 15V18C19.6409 18.5304 19.4298 19.0391 19.054 19.4142C18.6782 19.7893 18.1686 20 17.6371 20H6.61612C6.08468 20 5.575 19.7893 5.19921 19.4142C4.82342 19.0391 4.6123 18.5304 4.6123 18V7C4.6123 6.46957 4.82342 5.96086 5.19921 5.58579C5.575 5.21071 6.08468 5 6.61612 5H9.62185" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</div>
											
										<div class="action-icon-container d-flex justify-content-center align-items-center">
											<svg viewBox="0 0 18 19" class="action-icon action-icon--delete">
												<path d="M5.14414 15.2656H12.8539L13.2793 6.26562H4.71875L5.14414 15.2656Z"/>
												<path d="M15.1875 5H12.9375V3.59375C12.9375 2.97324 12.433 2.46875 11.8125 2.46875H6.1875C5.56699 2.46875 5.0625 2.97324 5.0625 3.59375V5H2.8125C2.50137 5 2.25 5.25137 2.25 5.5625V6.125C2.25 6.20234 2.31328 6.26562 2.39062 6.26562H3.45234L3.88652 15.459C3.91465 16.0584 4.41035 16.5312 5.00977 16.5312H12.9902C13.5914 16.5312 14.0854 16.0602 14.1135 15.459L14.5477 6.26562H15.6094C15.6867 6.26562 15.75 6.20234 15.75 6.125V5.5625C15.75 5.25137 15.4986 5 15.1875 5ZM6.32812 3.73438H11.6719V5H6.32812V3.73438ZM12.8549 15.2656H5.14512L4.71973 6.26562H13.2803L12.8549 15.2656Z"/>
											</svg>
										</div>
									</div>

								</td>
								<!-- Action TD End-->
								<!-- <td nowrap="">
									<span><a href="<?php echo $ActionUrl;?>Detail/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-list" alt="Details" title="Details"></span></a></span>
									<?php if($valuecheck=='3'){ ?> <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php }else { if($twantyfour == '1' && $recdate == $currentdate) { ?>
									<span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span> 
									<?php } else if ($user_id=='1') { ?> <span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span> <?php } else{?>
									<?php } } ?>
								</td>-->
								
								<td>
									<div class="d-flex justify-content-center">
										<div class="circle bg-success"></div>
									</div>
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
								<td class="cursorPointer text-center" ><?php echo strip_tags($Record[$key]);?></td>
								<?php endforeach;?>
								
								<!-- Action TD Added -->
								<td>
									<div class="d-flex justify-content-evenly">
										<div class="action-icon-container d-flex justify-content-center align-items-center">
											<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
												<path d="M17.02 19.5C17.85 19.5 18.5228 18.8284 18.5228 18C18.5228 17.1716 17.85 16.5 17.02 16.5C16.1899 16.5 15.5171 17.1716 15.5171 18C15.5171 18.8284 16.1899 19.5 17.02 19.5Z" fill="#3D89CF"/>
												<path d="M22.8639 17.6093C22.3996 16.4287 21.5984 15.41 20.5596 14.6796C19.5208 13.9491 18.2901 13.539 17.02 13.5C15.7499 13.539 14.5192 13.9491 13.4804 14.6796C12.4416 15.41 11.6404 16.4287 11.1761 17.6093L11.0085 18L11.1761 18.3907C11.6404 19.5713 12.4416 20.59 13.4804 21.3204C14.5192 22.0509 15.7499 22.461 17.02 22.5C18.2901 22.461 19.5208 22.0509 20.5596 21.3204C21.5984 20.59 22.3996 19.5713 22.8639 18.3907L23.0314 18L22.8639 17.6093ZM17.02 21C16.4255 21 15.8444 20.8241 15.3501 20.4944C14.8558 20.1648 14.4706 19.6962 14.2431 19.148C14.0156 18.5999 13.956 17.9967 14.072 17.4147C14.188 16.8328 14.4743 16.2982 14.8946 15.8787C15.315 15.4591 15.8506 15.1734 16.4336 15.0576C17.0167 14.9419 17.621 15.0013 18.1702 15.2284C18.7195 15.4554 19.1889 15.8399 19.5192 16.3333C19.8494 16.8266 20.0257 17.4067 20.0257 18C20.0247 18.7953 19.7077 19.5578 19.1443 20.1202C18.5808 20.6826 17.8169 20.999 17.02 21Z" fill="#3D89CF"/>
												<path d="M5.74854 12.75H9.50569V14.25H5.74854V12.75Z" fill="#3D89CF"/>
												<path d="M5.74854 9H14.7657V10.5H5.74854V9Z" fill="#3D89CF"/>
												<path d="M5.74854 5.25H14.7657V6.75H5.74854V5.25Z" fill="#3D89CF"/>
												<path d="M17.0201 1.5H3.49432C3.0961 1.50119 2.71453 1.6596 2.43295 1.94065C2.15136 2.2217 1.99264 2.60254 1.99146 3V21C1.99264 21.3975 2.15136 21.7783 2.43295 22.0593C2.71453 22.3404 3.0961 22.4988 3.49432 22.5H9.50577V21H3.49432V3H17.0201V11.25H18.5229V3C18.5218 2.60254 18.363 2.2217 18.0815 1.94065C17.7999 1.6596 17.4183 1.50119 17.0201 1.5Z" fill="#3D89CF"/>
											</svg>
										</div>
										
										<div class="action-icon-container d-flex justify-content-center align-items-center">
											<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
												<path d="M17.1102 5.40807L19.2322 7.52507L17.1102 5.40807ZM18.4748 3.54307L12.7368 9.27007C12.4404 9.56557 12.2382 9.94205 12.1557 10.3521L11.6257 13.0001L14.2788 12.4701C14.6896 12.3881 15.0663 12.1871 15.3628 11.8911L21.1008 6.16407C21.2732 5.99197 21.41 5.78766 21.5033 5.56281C21.5966 5.33795 21.6446 5.09695 21.6446 4.85357C21.6446 4.61019 21.5966 4.36919 21.5033 4.14433C21.41 3.91948 21.2732 3.71517 21.1008 3.54307C20.9284 3.37097 20.7237 3.23446 20.4984 3.14132C20.2731 3.04818 20.0316 3.00024 19.7878 3.00024C19.5439 3.00024 19.3025 3.04818 19.0772 3.14132C18.8519 3.23446 18.6472 3.37097 18.4748 3.54307V3.54307Z" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M19.6409 15V18C19.6409 18.5304 19.4298 19.0391 19.054 19.4142C18.6782 19.7893 18.1686 20 17.6371 20H6.61612C6.08468 20 5.575 19.7893 5.19921 19.4142C4.82342 19.0391 4.6123 18.5304 4.6123 18V7C4.6123 6.46957 4.82342 5.96086 5.19921 5.58579C5.575 5.21071 6.08468 5 6.61612 5H9.62185" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</div>
											
										<div class="action-icon-container d-flex justify-content-center align-items-center">
											<svg viewBox="0 0 18 19" class="action-icon action-icon--delete">
												<path d="M5.14414 15.2656H12.8539L13.2793 6.26562H4.71875L5.14414 15.2656Z"/>
												<path d="M15.1875 5H12.9375V3.59375C12.9375 2.97324 12.433 2.46875 11.8125 2.46875H6.1875C5.56699 2.46875 5.0625 2.97324 5.0625 3.59375V5H2.8125C2.50137 5 2.25 5.25137 2.25 5.5625V6.125C2.25 6.20234 2.31328 6.26562 2.39062 6.26562H3.45234L3.88652 15.459C3.91465 16.0584 4.41035 16.5312 5.00977 16.5312H12.9902C13.5914 16.5312 14.0854 16.0602 14.1135 15.459L14.5477 6.26562H15.6094C15.6867 6.26562 15.75 6.20234 15.75 6.125V5.5625C15.75 5.25137 15.4986 5 15.1875 5ZM6.32812 3.73438H11.6719V5H6.32812V3.73438ZM12.8549 15.2656H5.14512L4.71973 6.26562H13.2803L12.8549 15.2656Z"/>
											</svg>
										</div>
									</div>

								</td>
								<!-- Action TD End-->
								<!--
								<td nowrap="">
									<span><a href="<?php echo $ActionUrl;?>Detail/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-list" alt="Details" title="Details"></span></a></span>
									<?php if($valuecheck=='3'){ ?> <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php } else { if($twantyfour == '1' && $recdate == $currentdate){?>
									<span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span> 
									<?php } else if ($user_id=='1') { ?> <span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span> <?php }else{ ?>
									<?php } } ?>
									<span><a href="<?php echo $ActionUrl;?>Delete/Record/<?php echo $Record['RecordId']; ?>" onclick="return checkDelete()"><span class="glyphicon glyphicon-trash" alt="Delete" title="Delete"></span></a></span>
								</td>
								-->
								
								<td>
									<div class="d-flex justify-content-center">
										<div class="circle bg-success"></div>
									</div>
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
								<td class="cursorPointer text-center" ><?php echo strip_tags($Record[$key]);?></td>
								<?php endforeach;?>
								
								<!-- Action TD Added -->
								<td>
									<div class="d-flex justify-content-evenly">
										<div class="action-icon-container d-flex justify-content-center align-items-center">
											<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
												<path d="M17.02 19.5C17.85 19.5 18.5228 18.8284 18.5228 18C18.5228 17.1716 17.85 16.5 17.02 16.5C16.1899 16.5 15.5171 17.1716 15.5171 18C15.5171 18.8284 16.1899 19.5 17.02 19.5Z" fill="#3D89CF"/>
												<path d="M22.8639 17.6093C22.3996 16.4287 21.5984 15.41 20.5596 14.6796C19.5208 13.9491 18.2901 13.539 17.02 13.5C15.7499 13.539 14.5192 13.9491 13.4804 14.6796C12.4416 15.41 11.6404 16.4287 11.1761 17.6093L11.0085 18L11.1761 18.3907C11.6404 19.5713 12.4416 20.59 13.4804 21.3204C14.5192 22.0509 15.7499 22.461 17.02 22.5C18.2901 22.461 19.5208 22.0509 20.5596 21.3204C21.5984 20.59 22.3996 19.5713 22.8639 18.3907L23.0314 18L22.8639 17.6093ZM17.02 21C16.4255 21 15.8444 20.8241 15.3501 20.4944C14.8558 20.1648 14.4706 19.6962 14.2431 19.148C14.0156 18.5999 13.956 17.9967 14.072 17.4147C14.188 16.8328 14.4743 16.2982 14.8946 15.8787C15.315 15.4591 15.8506 15.1734 16.4336 15.0576C17.0167 14.9419 17.621 15.0013 18.1702 15.2284C18.7195 15.4554 19.1889 15.8399 19.5192 16.3333C19.8494 16.8266 20.0257 17.4067 20.0257 18C20.0247 18.7953 19.7077 19.5578 19.1443 20.1202C18.5808 20.6826 17.8169 20.999 17.02 21Z" fill="#3D89CF"/>
												<path d="M5.74854 12.75H9.50569V14.25H5.74854V12.75Z" fill="#3D89CF"/>
												<path d="M5.74854 9H14.7657V10.5H5.74854V9Z" fill="#3D89CF"/>
												<path d="M5.74854 5.25H14.7657V6.75H5.74854V5.25Z" fill="#3D89CF"/>
												<path d="M17.0201 1.5H3.49432C3.0961 1.50119 2.71453 1.6596 2.43295 1.94065C2.15136 2.2217 1.99264 2.60254 1.99146 3V21C1.99264 21.3975 2.15136 21.7783 2.43295 22.0593C2.71453 22.3404 3.0961 22.4988 3.49432 22.5H9.50577V21H3.49432V3H17.0201V11.25H18.5229V3C18.5218 2.60254 18.363 2.2217 18.0815 1.94065C17.7999 1.6596 17.4183 1.50119 17.0201 1.5Z" fill="#3D89CF"/>
											</svg>
										</div>
										
										<div class="action-icon-container d-flex justify-content-center align-items-center">
											<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
												<path d="M17.1102 5.40807L19.2322 7.52507L17.1102 5.40807ZM18.4748 3.54307L12.7368 9.27007C12.4404 9.56557 12.2382 9.94205 12.1557 10.3521L11.6257 13.0001L14.2788 12.4701C14.6896 12.3881 15.0663 12.1871 15.3628 11.8911L21.1008 6.16407C21.2732 5.99197 21.41 5.78766 21.5033 5.56281C21.5966 5.33795 21.6446 5.09695 21.6446 4.85357C21.6446 4.61019 21.5966 4.36919 21.5033 4.14433C21.41 3.91948 21.2732 3.71517 21.1008 3.54307C20.9284 3.37097 20.7237 3.23446 20.4984 3.14132C20.2731 3.04818 20.0316 3.00024 19.7878 3.00024C19.5439 3.00024 19.3025 3.04818 19.0772 3.14132C18.8519 3.23446 18.6472 3.37097 18.4748 3.54307V3.54307Z" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M19.6409 15V18C19.6409 18.5304 19.4298 19.0391 19.054 19.4142C18.6782 19.7893 18.1686 20 17.6371 20H6.61612C6.08468 20 5.575 19.7893 5.19921 19.4142C4.82342 19.0391 4.6123 18.5304 4.6123 18V7C4.6123 6.46957 4.82342 5.96086 5.19921 5.58579C5.575 5.21071 6.08468 5 6.61612 5H9.62185" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</div>
											
										<div class="action-icon-container d-flex justify-content-center align-items-center">
											<svg viewBox="0 0 18 19" class="action-icon action-icon--delete">
												<path d="M5.14414 15.2656H12.8539L13.2793 6.26562H4.71875L5.14414 15.2656Z"/>
												<path d="M15.1875 5H12.9375V3.59375C12.9375 2.97324 12.433 2.46875 11.8125 2.46875H6.1875C5.56699 2.46875 5.0625 2.97324 5.0625 3.59375V5H2.8125C2.50137 5 2.25 5.25137 2.25 5.5625V6.125C2.25 6.20234 2.31328 6.26562 2.39062 6.26562H3.45234L3.88652 15.459C3.91465 16.0584 4.41035 16.5312 5.00977 16.5312H12.9902C13.5914 16.5312 14.0854 16.0602 14.1135 15.459L14.5477 6.26562H15.6094C15.6867 6.26562 15.75 6.20234 15.75 6.125V5.5625C15.75 5.25137 15.4986 5 15.1875 5ZM6.32812 3.73438H11.6719V5H6.32812V3.73438ZM12.8549 15.2656H5.14512L4.71973 6.26562H13.2803L12.8549 15.2656Z"/>
											</svg>
										</div>
									</div>

								</td>
								<!-- Action TD End-->
								<!--
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
								-->
								
								<td>
									<div class="d-flex justify-content-center">
										<div class="circle bg-success"></div>
									</div>
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
								<td class="cursorPointer text-center" ><?php echo strip_tags($Record[$key]);?></td>
								<?php endforeach;?>
								<td>
									<div class="d-flex justify-content-evenly">
									<a href="<?php echo $ActionUrl;?>Detail/Record/<?php echo $Record['RecordId']; ?>">
										<div class="action-icon-container d-flex justify-content-center align-items-center">
											<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
												<path d="M17.02 19.5C17.85 19.5 18.5228 18.8284 18.5228 18C18.5228 17.1716 17.85 16.5 17.02 16.5C16.1899 16.5 15.5171 17.1716 15.5171 18C15.5171 18.8284 16.1899 19.5 17.02 19.5Z" fill="#3D89CF"/>
												<path d="M22.8639 17.6093C22.3996 16.4287 21.5984 15.41 20.5596 14.6796C19.5208 13.9491 18.2901 13.539 17.02 13.5C15.7499 13.539 14.5192 13.9491 13.4804 14.6796C12.4416 15.41 11.6404 16.4287 11.1761 17.6093L11.0085 18L11.1761 18.3907C11.6404 19.5713 12.4416 20.59 13.4804 21.3204C14.5192 22.0509 15.7499 22.461 17.02 22.5C18.2901 22.461 19.5208 22.0509 20.5596 21.3204C21.5984 20.59 22.3996 19.5713 22.8639 18.3907L23.0314 18L22.8639 17.6093ZM17.02 21C16.4255 21 15.8444 20.8241 15.3501 20.4944C14.8558 20.1648 14.4706 19.6962 14.2431 19.148C14.0156 18.5999 13.956 17.9967 14.072 17.4147C14.188 16.8328 14.4743 16.2982 14.8946 15.8787C15.315 15.4591 15.8506 15.1734 16.4336 15.0576C17.0167 14.9419 17.621 15.0013 18.1702 15.2284C18.7195 15.4554 19.1889 15.8399 19.5192 16.3333C19.8494 16.8266 20.0257 17.4067 20.0257 18C20.0247 18.7953 19.7077 19.5578 19.1443 20.1202C18.5808 20.6826 17.8169 20.999 17.02 21Z" fill="#3D89CF"/>
												<path d="M5.74854 12.75H9.50569V14.25H5.74854V12.75Z" fill="#3D89CF"/>
												<path d="M5.74854 9H14.7657V10.5H5.74854V9Z" fill="#3D89CF"/>
												<path d="M5.74854 5.25H14.7657V6.75H5.74854V5.25Z" fill="#3D89CF"/>
												<path d="M17.0201 1.5H3.49432C3.0961 1.50119 2.71453 1.6596 2.43295 1.94065C2.15136 2.2217 1.99264 2.60254 1.99146 3V21C1.99264 21.3975 2.15136 21.7783 2.43295 22.0593C2.71453 22.3404 3.0961 22.4988 3.49432 22.5H9.50577V21H3.49432V3H17.0201V11.25H18.5229V3C18.5218 2.60254 18.363 2.2217 18.0815 1.94065C17.7999 1.6596 17.4183 1.50119 17.0201 1.5Z" fill="#3D89CF"/>
											</svg>
										</div>
									</a>  
									<?php //echo "<br>Edit Value Check=".$valuecheck;/4
									
									if($valuecheck=='3'){ ?>  <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>  <?php } else { if($twantyfour == '1' && $recdate == $currentdate) { ?>
									<a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>">
										<div class="action-icon-container d-flex justify-content-center align-items-center">
											<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
												<path d="M17.1102 5.40807L19.2322 7.52507L17.1102 5.40807ZM18.4748 3.54307L12.7368 9.27007C12.4404 9.56557 12.2382 9.94205 12.1557 10.3521L11.6257 13.0001L14.2788 12.4701C14.6896 12.3881 15.0663 12.1871 15.3628 11.8911L21.1008 6.16407C21.2732 5.99197 21.41 5.78766 21.5033 5.56281C21.5966 5.33795 21.6446 5.09695 21.6446 4.85357C21.6446 4.61019 21.5966 4.36919 21.5033 4.14433C21.41 3.91948 21.2732 3.71517 21.1008 3.54307C20.9284 3.37097 20.7237 3.23446 20.4984 3.14132C20.2731 3.04818 20.0316 3.00024 19.7878 3.00024C19.5439 3.00024 19.3025 3.04818 19.0772 3.14132C18.8519 3.23446 18.6472 3.37097 18.4748 3.54307V3.54307Z" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M19.6409 15V18C19.6409 18.5304 19.4298 19.0391 19.054 19.4142C18.6782 19.7893 18.1686 20 17.6371 20H6.61612C6.08468 20 5.575 19.7893 5.19921 19.4142C4.82342 19.0391 4.6123 18.5304 4.6123 18V7C4.6123 6.46957 4.82342 5.96086 5.19921 5.58579C5.575 5.21071 6.08468 5 6.61612 5H9.62185" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</div>
										</a>   
										<?php } else if ($user_id=='1' ||$user_id=='222' ||$user_id=='217'||$user_id=='208') { ?> <a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>">
											<div class="action-icon-container d-flex justify-content-center align-items-center">
												<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
													<path d="M17.1102 5.40807L19.2322 7.52507L17.1102 5.40807ZM18.4748 3.54307L12.7368 9.27007C12.4404 9.56557 12.2382 9.94205 12.1557 10.3521L11.6257 13.0001L14.2788 12.4701C14.6896 12.3881 15.0663 12.1871 15.3628 11.8911L21.1008 6.16407C21.2732 5.99197 21.41 5.78766 21.5033 5.56281C21.5966 5.33795 21.6446 5.09695 21.6446 4.85357C21.6446 4.61019 21.5966 4.36919 21.5033 4.14433C21.41 3.91948 21.2732 3.71517 21.1008 3.54307C20.9284 3.37097 20.7237 3.23446 20.4984 3.14132C20.2731 3.04818 20.0316 3.00024 19.7878 3.00024C19.5439 3.00024 19.3025 3.04818 19.0772 3.14132C18.8519 3.23446 18.6472 3.37097 18.4748 3.54307V3.54307Z" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
													<path d="M19.6409 15V18C19.6409 18.5304 19.4298 19.0391 19.054 19.4142C18.6782 19.7893 18.1686 20 17.6371 20H6.61612C6.08468 20 5.575 19.7893 5.19921 19.4142C4.82342 19.0391 4.6123 18.5304 4.6123 18V7C4.6123 6.46957 4.82342 5.96086 5.19921 5.58579C5.575 5.21071 6.08468 5 6.61612 5H9.62185" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</div>
										</a> 
										
										<?php } else { // Temporary added ?>
										
										<a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>">
											<div class="action-icon-container d-flex justify-content-center align-items-center">
												<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
													<path d="M17.1102 5.40807L19.2322 7.52507L17.1102 5.40807ZM18.4748 3.54307L12.7368 9.27007C12.4404 9.56557 12.2382 9.94205 12.1557 10.3521L11.6257 13.0001L14.2788 12.4701C14.6896 12.3881 15.0663 12.1871 15.3628 11.8911L21.1008 6.16407C21.2732 5.99197 21.41 5.78766 21.5033 5.56281C21.5966 5.33795 21.6446 5.09695 21.6446 4.85357C21.6446 4.61019 21.5966 4.36919 21.5033 4.14433C21.41 3.91948 21.2732 3.71517 21.1008 3.54307C20.9284 3.37097 20.7237 3.23446 20.4984 3.14132C20.2731 3.04818 20.0316 3.00024 19.7878 3.00024C19.5439 3.00024 19.3025 3.04818 19.0772 3.14132C18.8519 3.23446 18.6472 3.37097 18.4748 3.54307V3.54307Z" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
													<path d="M19.6409 15V18C19.6409 18.5304 19.4298 19.0391 19.054 19.4142C18.6782 19.7893 18.1686 20 17.6371 20H6.61612C6.08468 20 5.575 19.7893 5.19921 19.4142C4.82342 19.0391 4.6123 18.5304 4.6123 18V7C4.6123 6.46957 4.82342 5.96086 5.19921 5.58579C5.575 5.21071 6.08468 5 6.61612 5H9.62185" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</div>
										</a>
										
										<?php } } ?>
										<a href="<?php echo $ActionUrl;?>Delete/Record/<?php echo $Record['RecordId']; ?>" onclick="return checkDelete()"> 
											<div class="action-icon-container d-flex justify-content-center align-items-center">
												<svg viewBox="0 0 18 19" class="action-icon action-icon--delete">
													<path d="M5.14414 15.2656H12.8539L13.2793 6.26562H4.71875L5.14414 15.2656Z"/>
													<path d="M15.1875 5H12.9375V3.59375C12.9375 2.97324 12.433 2.46875 11.8125 2.46875H6.1875C5.56699 2.46875 5.0625 2.97324 5.0625 3.59375V5H2.8125C2.50137 5 2.25 5.25137 2.25 5.5625V6.125C2.25 6.20234 2.31328 6.26562 2.39062 6.26562H3.45234L3.88652 15.459C3.91465 16.0584 4.41035 16.5312 5.00977 16.5312H12.9902C13.5914 16.5312 14.0854 16.0602 14.1135 15.459L14.5477 6.26562H15.6094C15.6867 6.26562 15.75 6.20234 15.75 6.125V5.5625C15.75 5.25137 15.4986 5 15.1875 5ZM6.32812 3.73438H11.6719V5H6.32812V3.73438ZM12.8549 15.2656H5.14512L4.71973 6.26562H13.2803L12.8549 15.2656Z"/>
												</svg>
											</div>
										</a>
									</div>

								</td>
								<!-- <td nowrap="">
									<span><a href="<?php echo $ActionUrl;?>Detail/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-list" alt="Details" title="Details"></span></a></span>
									<?php if($valuecheck=='3'){ ?>  <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>  <?php } else { if($twantyfour == '1' && $recdate == $currentdate) { ?>
									<span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span> 
									<?php } else if ($user_id=='1' ||$user_id=='222' ||$user_id=='217'||$user_id=='208') { ?> <span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>"><span class="glyphicon glyphicon-pencil" alt="Edit" title="Edit"></span></a></span> <?php } else { ?>
									<?php } } ?>
									<span><a href="<?php echo $ActionUrl;?>Delete/Record/<?php echo $Record['RecordId']; ?>" onclick="return checkDelete()"><span class="glyphicon glyphicon-trash" alt="Delete" title="Delete"></span></a></span>
								</td> -->
								<td>
									<div class="d-flex justify-content-center">
							<?php if($Record['approve_status']==1){?>
											<div class="circle bg-success"></div>
							<?php } elseif($Record['approve_status']==2) {?>
							<div class="circle"></div>
							<?php }else {?>
							<div class="circle bg-danger"></div>
							<?php } ?>
									</div>
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
					<div class="seprator"></div>
					<div class="body-footer d-flex justify-content-between align-items-center mx-3 position-relative">
						<!-- pagination -->
						<?php include_once "ListViewPagination.php";?>					
						<div class="add-btn btn-primary d-flex justify-content-center align-items-center position-static">
							<a href="<?php echo $addUrl;?>">
								<svg width="16" height="16" viewBox="0 0 16 16" fill="currentcolor">
									<path d="M6 16H10V10H16V6H10V0H6V6H0V10H6V16Z" fill="#ffffff"/>
								</svg> 
							</a>                       
						</div>
					</div>
				</div>
			</div>
			
			
			</div>
		</div>
	</div>
</div>
