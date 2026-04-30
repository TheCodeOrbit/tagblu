<style>
    th, td {
        vertical-align: middle;
    }
    table thead tr {
        z-index: 10;
    }
</style>

<?php
//echo "<br>Approve edit page calling";
//die;
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
            <p class="body-heading">Approve <?php echo $ModuleLabel;?> Data</p>

            <?php $url = $ActionUrl.'ApproveList';
                echo CHtml::beginForm($url, 'POST',array("name"=>"listsearchfm","id"=>"listsearchfm")); ?>
                <input type="hidden" value="<?php echo $listserchvals['startdate']; ?>" name="startdate" id="startdate" /> 
                <input type="hidden" value="<?php echo $listserchvals['enddate']; ?>" name="enddate" id="enddate" /> 
                    
                <div class="d-flex position-absolute start-50 translate-middle-x">
                    <select name="searchres[month:1]" id="month_1" class="form-select w-150px me-3" aria-label="select example" onchange="listsearchfm.submit();">
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
                    <select name="searchres[day:1]" id="day_1" class="form-select w-150px" aria-label="select example" onchange="listsearchfm.submit();">
                        <option selected="true" disabled>Select Days</option>
                        <option value="1-15" <?php echo ($listserchvals['day']== '1-15' ? "selected=selected" : '');?>>From Day 1 to Day 15</option>
                        <option value="16-31" <?php echo ($listserchvals['day']== '16-31' ? "selected=selected" : '');?>>From Day 16 to Day 31</option>
                    </select>            
                </div>
            <?php echo CHtml::endForm();?>
        </div>
        <div class="body-container">
		    <?php $form=$this->beginWidget('CActiveForm', array( 
                //'enableClientValidation'=>true,
                //'clientOptions'=>array( 'validateOnSubmit'=>true, ), 
			    ));
    		?>
            <div class="body-outline height-full-body">
                <div data-simplebar class="adjusted-height">
                    <table class="table-view table table-striped">
                        <thead>

                            <tr class="table-primary">
                                <?php if($ModuleName!="obcesummary" and $ModuleName!="washeryinput" and $ModuleName!="logisticmine_12" and $ModuleName!="logisticsiding"){?>
                                <th>
                                    <div class="custom-checkbox">
                                        <!-- below input checkbox id needs to be unique -->
                                        <input type="checkbox" id="checkbox1" name="checkbox" class="checkbox checkbox--prim"/>
                                        <?php //echo $form->checkBox($model,'subscribed',array('value' => '1', 'uncheckValue'=>'0')); ?>
                                        <label for="checkbox1" class="sm">Checkbox 5</label>
                                    </div>
                                </th>
                                <?php }?>	
                            <?php //print_r($RecordList);	
                                $col_span=count($ColumnList)+1;
                                foreach ($ColumnList as $key=> $Column): ?>
                                    <th><?php echo $Column;?></th>
                                    <?php endforeach;?>
                                    <?php if($ModuleName=="obcesummary" or $ModuleName=="washeryinput" or $ModuleName=="logisticmine_12" or $ModuleName=="logisticsiding"){?>
                                <th>Action</th>
                                <th>Status</th>	
				            <?php }?>	
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
                        </thead>
                        <tbody class="text-center">
                            <?php //print_r($RecordList);
                                $addUrl="{$ActionUrl}Create";
                                if(count($RecordList)>0):
                                    foreach ($RecordList as $Record): 
                                        //print_r($Record);
                                        //die;
                                        $dt= str_replace("/","-",$Record['date']);
                                        $currdt = date('Y-m-d',strtotime($dt)); 
                                        $recdate = date('Y-m-d', strtotime('+1 day', strtotime($currdt)));
                            ?>
                                <tr>
                                    <?php if($ModuleName!="obcesummary" and $ModuleName!="washeryinput" and $ModuleName!="logisticmine_12" and $ModuleName!="logisticsiding"){?>
                                        <td>
                                            <?php if(!in_array($Record['date'], $arrDate)) {
                                                $arrDate[]=$Record['date'];
                                            ?>  
                                                <div class="custom-checkbox">
                                                    <input type="checkbox" id="checkbox<?php echo $Record[RecordId];?>" name="ApproveList[<?php echo $Record[RecordId];?>]" class="checkbox checkbox--prim approve-check"/>
                                                    <label for="checkbox<?php echo $Record[RecordId];?>" class="sm">Checkbox 5</label>
                                                </div>  
                                            <?php  } ?>
                                        </td>
                                    <?php  } ?>
                                    <?php foreach ($ColumnList as $key=> $Column):?>
                                        <td><?php echo strip_tags($Record[$key]);?></td>
                                    <?php endforeach;?>

                                    <?php if($ModuleName=="obcesummary" or $ModuleName=="washeryinput" or $ModuleName=="logisticmine_12" or $ModuleName=="logisticsiding"){?>	
                                        <td>
                                            <div class="d-flex justify-content-evenly">
                                                <a href="<?php echo $ActionUrl;?>ApproveDetail/Record/<?php echo $Record['RecordId']; ?>">
                                                    <div class="action-icon-container d-flex justify-content-center align-items-center">
                                                        <svg width="21" height="22" viewBox="0 0 21 22" fill="currentcolor" xmlns="http://www.w3.org/2000/svg">
                                                            <g clip-path="url(#clip0_1333_25820)">
                                                                <path d="M19.6875 13.6251C19.6888 12.968 19.5257 12.321 19.2129 11.7431C18.9001 11.1651 18.4477 10.6747 17.8968 10.3164C17.3459 9.95812 16.7141 9.74341 16.059 9.69183C15.4039 9.64026 14.7463 9.75347 14.1462 10.0211C13.546 10.2888 13.0224 10.7024 12.623 11.2243C12.2237 11.7461 11.9613 12.3597 11.8598 13.0089C11.7583 13.6582 11.8208 14.3225 12.0418 14.9414C12.2628 15.5602 12.6352 16.1139 13.125 16.552V21.5001L15.75 20.2572L18.375 21.5001V16.552C18.7876 16.1841 19.1178 15.7332 19.3439 15.2288C19.5701 14.7244 19.6872 14.1779 19.6875 13.6251ZM17.0625 19.4264L15.75 18.8049L14.4375 19.4264V17.333C15.2857 17.6393 16.2143 17.6393 17.0625 17.333V19.4264ZM15.75 16.2501C15.2308 16.2501 14.7233 16.0962 14.2916 15.8078C13.8599 15.5193 13.5235 15.1093 13.3248 14.6297C13.1261 14.15 13.0741 13.6222 13.1754 13.113C13.2767 12.6038 13.5267 12.1361 13.8938 11.769C14.261 11.4019 14.7287 11.1519 15.2379 11.0506C15.7471 10.9493 16.2749 11.0013 16.7545 11.2C17.2342 11.3986 17.6442 11.7351 17.9326 12.1668C18.221 12.5985 18.375 13.106 18.375 13.6251C18.3741 14.3211 18.0973 14.9882 17.6052 15.4803C17.1131 15.9724 16.4459 16.2493 15.75 16.2501Z" fill="#3D89CF"/>
                                                                <path d="M16.4062 3.78125H14.4375V3.125C14.4365 2.77722 14.2978 2.44399 14.0519 2.19807C13.806 1.95215 13.4728 1.81354 13.125 1.8125H7.875C7.52722 1.81354 7.19399 1.95215 6.94807 2.19807C6.70215 2.44399 6.56354 2.77722 6.5625 3.125V3.78125H4.59375C4.24597 3.78229 3.91274 3.9209 3.66682 4.16682C3.4209 4.41274 3.28229 4.74597 3.28125 5.09375V18.875C3.28229 19.2228 3.4209 19.556 3.66682 19.8019C3.91274 20.0478 4.24597 20.1865 4.59375 20.1875H10.5V18.875H4.59375V5.09375H6.5625V7.0625H14.4375V5.09375H16.4062V8.375H17.7188V5.09375C17.7177 4.74597 17.5791 4.41274 17.3332 4.16682C17.0873 3.9209 16.754 3.78229 16.4062 3.78125ZM13.125 5.75H7.875V3.125H13.125V5.75Z" fill="#3D89CF"/>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_1333_25820">
                                                                    <rect width="21" height="21" fill="white" transform="translate(0 0.5)"/>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                </a>
                                            </div>
                                        </td>

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
                                    <?php }?>
                                </tr>
                            <?php endforeach;?>
                            <?php endif;?>
                        </tbody>
                    </table>

                </div>
                <div class="seprator"></div>
                <div class="body-footer d-flex justify-content-between align-items-center mx-2rem">
                        
                    <?php include_once "ListViewPagination.php";?>
                    <?php if($ModuleName!="obcesummary" and $ModuleName!="washeryinput" and $ModuleName!="logisticmine_12" and $ModuleName!="logisticsiding"){?>	
                        <div>
                            <button type="button" class="btn btn-primary input-save me-5" data-toggle="modal" data-target="#addCommentModal">Recheck</button>
                            <?php echo CHtml::submitButton('approve', array('class' => 'btn btn-primary input-save','data-bs-toggle'=>'modal')); ?>	
                        </div>
                        <?php }?>
                        
                        <!-- popUpModal -->
<div class="modal fade" id="addCommentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="d-flex justify-content-center adani-bold">
                <h5 class="modal-title h3" id="exampleModalLongTitle">Add Comments</h5>
            </div>
            <div class="d-flex justify-content-center my-4">
                <textarea name="ApproveList[comment]" class="form-control textarea-height b-prim addcomments" rows="3"></textarea>
            </div>
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-danger input-save me-5" data-dismiss="modal">Discard</button>
                <?php echo CHtml::submitButton('Submit', array('name' => 'btnrecheck','class' => 'btn btn-primary input-save close')); ?>
            </div>
        </div>
    </div>
</div>
                        <?php $this->endWidget(); ?>
                    </div>
                </div>
                
        </div>
    </div>
    
    
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/chosen.jquery.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.inputmask.bundle.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/dailydrilling/ApproveList.js"></script>
