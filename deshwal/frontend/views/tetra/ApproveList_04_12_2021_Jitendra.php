<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/chosen.jquery.min.js"></script>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.inputmask.bundle.js"></script>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/dailydrilling/ApproveList.js"></script>

<?php

$arrDate=array();

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

$ActionUrl=Yii::app()->createAbsoluteUrl($ModuleName)."/";
?>

<div id="ApproveDrill" class="">
            <!-- Add Drilling Data Screen -->
            <div class="body-menu d-flex">
                <p class="body-heading">Approve Drilling Data</p>

		<?php $url = $ActionUrl.'ApproveList';
							echo CHtml::beginForm($url, 'POST',array("name"=>"listsearchfm","id"=>"listsearchfm")); ?>
						<input type="hidden" value="<?php echo $listserchvals['startdate']; ?>" name="startdate" id="startdate" /> 
						<input type="hidden" value="<?php echo $listserchvals['enddate']; ?>" name="enddate" id="enddate" /> 
						
						
                <div class="d-flex position-absolute start-50 translate-middle-x">
                    <!--<div class="input-outline flex jc-spbt ai-cntr m-r-10 w-150px">-->
                        <!-- <input type="date"> --> <!-- current date by default should come here -->
			<?php //echo "Month=".$listserchvals['month']; echo "Day=".$listserchvals['day'];?>
			<select name="searchres[month:1]" id="month_1" class="form-select w-150px" aria-label="select example" onchange="listsearchfm.submit();">
                        <option selected="true" disabled>Select Month</option>
                        <option value="1" <?php echo ($listserchvals['month']== '1' ? "selected=selected" : '');?>>January</option>
                        <option value="2" <?php echo ($listserchvals['month']== '2' ? "selected=selected" : '');?>>February</option>
			<option value="3" <?php echo ($listserchvals['month']== '3' ? "selected=selected" : '');?>>March</option>
			<option value="4" <?php echo ($listserchvals['month']== '4' ? "selected=selected" : '');?>>April</option>
			<option value="5" <?php echo ($listserchvals['month']== '5' ? "selected=selected" : '');?>>May</option>
			<option value="6" <?php echo ($listserchvals['month']== '6' ? "selected=selected" : '');?>>June</option>
			<option value="7" <?php echo ($listserchvals['month']== '7' ? "selected=selected" : '');?>>July</option>
			<option value="8" <?php echo ($listserchvals['month']== '8' ? "selected=selected" : '');?>>August</option>
			<option value="9" <?php echo ($listserchvals['month']== '9' ? "selected=selected" : '');?>>September</option>
			<option value="10" <?php echo ($listserchvals['month']== '10' ? "selected=selected" : '');?>>October</option>
			<option value="11" <?php echo ($listserchvals['month']== '11' ? "selected=selected" : '');?>>November</option>
			<option value="12" <?php echo ($listserchvals['month']== '12' ? "selected=selected" : '');?>>December</option>

                    </select>
                    <!--</div>-->
			<?php //echo $_POST['day:1'];?>
                    <select name="searchres[day:1]" id="day_1" class="form-select w-150px" aria-label="select example" onchange="listsearchfm.submit();">
                        <option selected="true" disabled>Select Days</option>
                        <option value="1-15" <?php echo ($listserchvals['day']== '1-15' ? "selected=selected" : '');?>>From Day 1 to Day 15</option>
                        <option value="16-31" <?php echo ($listserchvals['day']== '16-31' ? "selected=selected" : '');?>>From Day 16 to Day 31</option>
                    </select>  
				<?php //echo $listserchvals['vals']; ?>
							<!--<button type="button" class="btn btn-info" id="listsearch" name="listsearch" onclick="listsearchfm.submit();">Search</button>-->
						              
                </div>
		<?php echo CHtml::endForm();?>
            </div>
            
            <div class="body-container">
		<?php $form=$this->beginWidget('CActiveForm', array( 
			//'enableClientValidation'=>true,
			//'clientOptions'=>array( 'validateOnSubmit'=>true, ), 
			));

			//echo $form->hiddenField($model,'users_id',array('value' => Yii::app()->user->id));
		?>
                <div data-simplebar class="body-outline">
                    <div class="adjusted-height">
                        <table>
                            <tr>
                                <th class="bg-white b-prim">
                                    <div class="custom-checkbox">
                                        <!-- below input checkbox id needs to be unique -->
                                        <input type="checkbox" id="checkbox1" name="checkbox" class="checkbox checkbox--prim"/>
                                        <?php //echo $form->checkBox($model,'subscribed',array('value' => '1', 'uncheckValue'=>'0')); ?>
                                        <label for="checkbox1" class="sm">Checkbox 5</label>
                                    </div>
                                </th>
                                <?php //print_r($RecordList);	
                                        $col_span=count($ColumnList)+1;
                                    foreach ($ColumnList as $key=> $Column): ?>
                                    <th><?php echo $Column;?></th>
                                <?php endforeach;?>	
                                <!--<th>Shift</th>
                                <th>Date</th>
                                <th>Drilling material</th>
                                <th>Drill Machine No.</th>
                                <th>Working Area</th>
                                <th>No of Holes Drilled</th>
                                <th>Burden(m)</th>
                                <th>Hole Depth(m)</th>
                                <th>Spacing(m)</th>
                                <th>Type of Drilling</th>
                                <th>Bench height(m)</th>-->
                            </tr>
                            <?php //print_r($RecordList);
                        
                            $addUrl="{$ActionUrl}Create";
                            if(count($RecordList)>0):
                            foreach ($RecordList as $Record): 
                            //print_r($Record);
                            //die;
                            $dt= str_replace("/","-",$Record['date']);
                            $currdt = date('Y-m-d',strtotime($dt)); 
                            $recdate = date('Y-m-d', strtotime('+1 day', strtotime($currdt)));?>
                            <tr>
                                <td>
                                    <?php if(!in_array($Record['date'], $arrDate) )
                                        {
                                            $arrDate[]=$Record['date'];
                                        ?>
                                                
                                                    <div class="custom-checkbox">
                                                        <input type="checkbox" id="checkbox<?php echo $Record[RecordId];?>" name="ApproveList[<?php echo $Record[RecordId];?>]" class="checkbox checkbox--prim approve-check"/>
                                                        <label for="checkbox<?php echo $Record[RecordId];?>" class="sm">Checkbox 5</label>
                                                    </div>
                                                
                                    <?php  } ?>
                                </td>
                                <?php foreach ($ColumnList as $key=> $Column):?>
                                <td><?php echo strip_tags($Record[$key]);?></td>
                                <?php endforeach;?>		

                            </tr>	
                            <?php endforeach;?>
                            <?php endif;?>
                        </table>

                        <div class="seprator"></div>
                        <div class="body-footer d-flex justify-content-between align-items-center mx-2rem">
                            
                            <?php include_once "ListViewPagination.php";?>
                            <div>
                            <!-- <button type="button" class="btn btn-primary input-save me-4" data-bs-toggle="modal" data-bs-target="#exampleModal">discard</button>-->
                            <?php echo CHtml::submitButton('Recheck', array('name' => 'btnrecheck','class' => 'btn btn-primary input-save me-5 close')); ?>
                                <!--<button type="button" class="btn btn-primary input-save" data-bs-toggle="modal" data-bs-target="#exampleModal">approve</button>-->
                                <?php echo CHtml::submitButton('approve', array('class' => 'btn btn-primary input-save','data-bs-toggle'=>'modal')); ?>	
                            </div>
                            <?php $this->endWidget(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
