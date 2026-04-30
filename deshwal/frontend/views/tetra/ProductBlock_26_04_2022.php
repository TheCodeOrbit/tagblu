<tr id="prod_tbl" class="prod_tbl">
<?php if($Block->Fields[0]->tablename != 'p_reconciliation_data' AND $Block->Fields[0]->tablename != 'ce_p_reconciliation_data') { ?>
<!--<td class="<?php echo $Block->Fields[0]->tablename;?>Delete text-center" id="<?php echo $Block->Fields[0]->tablename.'_'.$cnt_multiple_product.'_Delete'?>"><a href="javascript:void;"><span class="glyphicon glyphicon-trash"></span></a></td>-->
<?php }
//echo "<pre>";
//print_r($Block);
//die;
//$form=$this->beginWidget('CActiveForm');
$ModuleName="";
$obj_name=$Block->Fields[0]->tablename;
if($Block->Fields[0]->tablename=="openingstock_data")
    $ModuleName="openingstock";
if($Block->Fields[0]->tablename=="mine_loss_dispatch_hours")
    $ModuleName="logisticmine_12";

if($Block->Fields[0]->tablename=="vehicle_detention")
    $ModuleName="logisticmine_12";

if($Block->Fields[0]->tablename=="siding_receipt")
    $ModuleName="logisticsiding";

if($Block->Fields[0]->tablename=="vehicle_detention_siding")
   $ModuleName="logisticsiding";


if($Block->Fields[0]->tablename=="loss_dispatch_hours")
    $ModuleName="logisticsiding";
if($Block->Fields[0]->tablename=="rakedispatch_siding")
    $ModuleName="logisticsiding";





if($Block->Fields[0]->tablename=="stockadjment_data")
    $ModuleName="stockadjustment";
if($Block->Fields[0]->tablename=="treefelling_data")
    $ModuleName="treefelling";
if($Block->Fields[0]->tablename=="drilling_data")
    $ModuleName="dailydrilling";
if($Block->Fields[0]->tablename=="p_reconciliation_data")
    $ModuleName="p_reconciliation";
if($Block->Fields[0]->tablename=="ce_p_reconciliation_data")
    $ModuleName="ce_p_reconciliation";

if($Block->Fields[0]->tablename=="data_obremoval" or $Block->Fields[0]->tablename=="coal_extraction_data" or $Block->Fields[0]->tablename=="ob_loss_hours_production" or $Block->Fields[0]->tablename=="ob_loss_production_hours")
    $ModuleName="obcesummary";

//echo "<br>Module Name=$ModuleName";
$MPkey=1;

foreach($Block->Fields as $key=> $Field):
	/*echo "<pre>";		
	print_r($Field);
	die;*/
	//echo $Field->fieldid;
	if($Field->edit_view==1):

	$attr_name="{$Field->tablename}[{$cnt_multiple_product}][{$Field->fieldname}]";
	$attr_id="{$Field->tablename}_{$cnt_multiple_product}_{$Field->fieldname}";
	$attr_class ="{$Field->fieldname}";
	if($attr_class=="trip"){
	$attr_class ="trips";
	}elseif($attr_class=="qty"){
		$attr_class ="qty total-qty";
	
	}
	//echo "<br>attr_name=$attr_name and attr_id=$attr_id";
	?>

	<?php if($Field['uitype']==1):?><!--HI ui type is 1-->
	<td class="<?php echo $Field->td_classname;?>">
		<?php //echo $attr_class;?>
	<?php 	if(strpos($Field->classname, 'ReadOnly') !== false){
			echo CHtml::textField($attr_name, '',array('id'=>$attr_id, 'class'=>$Field->classname.' input-border form-control a','readonly'=>true));
		}
		else{
			if($Block->Fields[0]->tablename =='contractorattendancedata' && ($Field->columnname == 'shift_operator_name' or $Field->columnname == 'role')){
			echo CHtml::textField($attr_name, '',array('id'=>$attr_id, 'class'=>$Field->classname.' input-border form-control b','readonly'=>true));
			}else{
				//print_r($Field);
				// echo $Field->fieldid;
				if($Field->fieldid==1899 || $Field->fieldid==280 || $Field->fieldid==1806 || $Field->fieldid==2075  || $Field->fieldid==2081 || $Field->fieldid==1938){
					$timeformat='timeformat';
					$p_holder='hh:mm';
				}else{
					$timeformat='';
					$p_holder='';
				}
				if($Field->fieldid==1863){
					$attr_class=$attr_class."_ehs";
				}else{
					$attr_class=$attr_class;
				}
				if($Field->fieldid==1897){
					$readonly='readonly';
					$style="background-color: rgb(183, 178, 178);";
				}else{
					$readonly='';
					$style="";
				}
			echo CHtml::textField($attr_name, '',array('id'=>$attr_id, 'class'=>$timeformat.' input-border form-control '.$attr_class, 'placeholder'=>$p_holder,'readonly'=>$readonly, 'style'=>$style));
			}
		}
		if (strpos($Field->classname, 'col-sm-3') !== false)
		$tooltip_class="tooltipImages";
		else
		$tooltip_class="tooltip_img"; 
	 ?>
	 <div class="error-container hide">All errors displayed Here</div>
		<div id="<?php echo $attr_id.'_em_';?>" class="ajxwarning errorMessage <?php echo $tooltip_class;?> bb1" style="display:none;"></div>
	</td>
	<?php elseif($Field['uitype']==14):?>
		
		<td>
			<!-- Button trigger modal -->
			<!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal123"> -->
			<button type="button" value="Stoppage reason"  class="btn btn-primary form-control target-button <?php echo $obj_name.'_'.$cnt_multiple_product.'_'.$Field->fieldname.'_reason'; ?>"  
			 data-bs-target="#<?php echo $obj_name.'_'.$cnt_multiple_product.'_'.$Field->fieldname.'_reason'; ?>" onclick="materialvalidation(event)">Stoppage</button>
			<div class="error-container hide">All errors displayed Here</div>

			<!-- Modal -->
			<div class="modal fade append-to-body" id="<?php echo $obj_name.'_'.$cnt_multiple_product.'_'.$Field->fieldname.'_reason';?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog custom-modal-class modal-dialog-centered">
				<div class="modal-content">
					
						<div class="modal-header">
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body" data-que="<?php echo $cnt_multiple_product;?>">
								<div class="d-flex justify-content-between">
									<div class="input-heading " style="width:50%">Stopage Reason</div>
									<div class="input-heading " style="width:50%">Hours</div>
									<div class="input-heading " style="width:13%">Tools</div>

								</div>
								<div class="position-relative">
									<div class="d-flex justify-content-between anchor-class">
										<select class=" input-border form-control reason_<?php echo $cnt_multiple_product;?> reason" name="<?php echo $Field->tablename;?>[<?php echo $cnt_multiple_product;?>][reason][]">
											<?php $PickList=new PickList;
												$PickList->fieldid=$Field->fieldid;
												$fieldoptionsReason=$PickList->getPickListReasonOption();
												echo $fieldoptionsReason;
											?>
										</select>
										<input placeholder="hh:mm" maxlength="8" pattern="^((\d+:)?\d+:)?\d*$" class="<?php echo $Field->tablename;?> input-border form-control timeformat <?php echo $Field->tablename;?>_<?php echo $cnt_multiple_product;?>" name="<?php echo $Field->tablename;?>[<?php echo $cnt_multiple_product;?>][loss_hour][]" <?php echo $Field->fieldid;?> >
										<div class="action-icon-container input-border disabled w-120px"><svg viewBox="0 0 18 19" class="action-icon action-icon--delete"><path d="M5.14414 15.2656H12.8539L13.2793 6.26562H4.71875L5.14414 15.2656Z"></path><path d="M15.1875 5H12.9375V3.59375C12.9375 2.97324 12.433 2.46875 11.8125 2.46875H6.1875C5.56699 2.46875 5.0625 2.97324 5.0625 3.59375V5H2.8125C2.50137 5 2.25 5.25137 2.25 5.5625V6.125C2.25 6.20234 2.31328 6.26562 2.39062 6.26562H3.45234L3.88652 15.459C3.91465 16.0584 4.41035 16.5312 5.00977 16.5312H12.9902C13.5914 16.5312 14.0854 16.0602 14.1135 15.459L14.5477 6.26562H15.6094C15.6867 6.26562 15.75 6.20234 15.75 6.125V5.5625C15.75 5.25137 15.4986 5 15.1875 5ZM6.32812 3.73438H11.6719V5H6.32812V3.73438ZM12.8549 15.2656H5.14512L4.71973 6.26562H13.2803L12.8549 15.2656Z"></path></svg></div>
									</div>
								</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="<?php echo $Field->tablename;?> btn btn-primary btn-custom add-rows-to-pop reasonadd" >
										<svg width="16" height="16" viewBox="0 0 16 16">
											<path d="M6 16H10V10H16V6H10V0H6V6H0V10H6V16Z" fill="#ffffff"/>
										</svg> 
							</button>
							<button type="button" class="btn btn-outline-danger btn-custom me-5 reasonclose" onclick="hourvalidation(event)" data-bs-dismiss="modal">Close</button>
						</div>
					
				</div>
			</div>
			</div>
		</td>
	<?php	elseif($Field['uitype']==8): ?>
		<?php
		 $PickList=new PickList;
		$PickList->fieldid=$Field->fieldid;
                $obj_name=$Field->tablename;
		$fieldoptions=$PickList->getPickListOption($ModuleName);
		//print_r($fieldoptions);
		?>
	<td class="<?php echo $Field->td_classname;?>">
		<div id="<?php echo $obj_name._.$cnt_multiple_product._.$Field->columnname._em_;?>" class="ajxwarning errorMessage tooltipImages bb2" style="display:none">Stock Type cannot be blank.</div>

		<?php if($Block->Fields[0]->tablename =='contractorattendancedata' && $Field->columnname == 'code'){?>
		<select id="contractorattendancedata_<?php echo $cnt_multiple_product?>_<?php echo $Field->columnname;?>" name="contractorattendancedata[<?php echo $cnt_multiple_product?>][<?php echo $Field->columnname;?>]" class="<?php echo $Field->classname;?>">
		<option value="">Select an Option</option>
		<?php foreach($manpower as $key=>$manpowername){?>
		<option value="<?php echo $manpowername['code'];?>"><?php echo $manpowername['code'];?></option>
		<?php }?>
		</select>

		<?php } else if($Block->Fields[0]->tablename =='contractorattendancedata' && $Field->columnname=='machine_dumper_no') { ?>
		<select id="contractorattendancedata_<?php echo $cnt_multiple_product?>_<?php echo $Field->columnname;?>" name="contractorattendancedata[<?php echo $cnt_multiple_product?>][<?php echo $Field->columnname;?>]" class="<?php echo $Field->classname;?>">
		<option value="">Select an Option</option>
		</select>

		<?php } else if($Block->Fields[0]->tablename =='contractorattendancedata' && $Field->columnname=='att_status') { ?>
		<select id="contractorattendancedata_<?php echo $cnt_multiple_product?>_<?php echo $Field->columnname;?>" name="contractorattendancedata[<?php echo $cnt_multiple_product?>][<?php echo $Field->columnname;?>]" class="<?php echo $Field->classname;?>">
		<?php foreach($attst as $key=>$attstatus){?>
		<option value="<?php echo $attstatus['statusid'];?>"><?php echo $attstatus['statusname'];?></option>
		<?php } ?>
		</select>

		<?php } else if($Block->Fields[0]->tablename =='contractorattendancedata' && $Field->columnname=='equipment_type') { ?>
		<select id="contractorattendancedata_<?php echo $cnt_multiple_product?>_<?php echo $Field->columnname;?>" name="contractorattendancedata[<?php echo $cnt_multiple_product?>][<?php echo $Field->columnname;?>]" class="<?php echo $Field->classname;?>">
		<option value="">Select an Option</option>
		<?php foreach($machinetype as $key=>$machinetypes){?>
		<option value="<?php echo $machinetypes['typeid'];?>"><?php echo $machinetypes['type'];?></option>
		<?php } ?>
		</select>

		<?php } else { 
			//echo $Field->fieldid;
if($Field->fieldid==1860){
	//echo $MPkey;
	//echo $fieldoptions[$MPkey];
	//print_r($fieldoptions);
	$refIdValue=$Record['location_id'];
		$refValue=$PickList->getPickListOption($ModuleName);	
		//$dataa=$fieldoptions;
		//$key=1;
		//print_r($refValue);
		//die;
//$fieldoptions=$PickList->getPickListOption($ModuleName);
			?>
			<div class="d-flex flex-col">
				<?php 
				$aa=explode("_",$attr_id);
				$key=$aa[2]; 
				//echo $Field->fieldid;?>


				<input type="text" value="<?php echo $refValue[$key];?>" id="<?php echo $Field->fieldname.$MPkey;?>" name ="<?php echo $Field->columnname.$MPkey;?>" size=12 class="<?php echo $Field->classname;?>" readonly="readonly" autocomplete="off" >
<input type="hidden" value="<?php echo $key;?>" id="<?php echo $Field->fieldname."id".$MPkey;?>" name ="<?php echo $attr_name;?>" >

				<?php }else{ 
if($Field->fieldid==1793){
		$fieldclass=$Field->fieldname."2";
		}else{
		$fieldclass=$Field->fieldname;
		}
		if($Field->fieldid==1894){
// $serial = Yii::app()->db->createCommand()
// 				->select('equipment_id,serial_no','presence')
// 				->from('equipment')
// 				->where('deleted = :deleted','equipment_purpose = :equipment_purpose','equipment_type = :equipment_type' array(':deleted' =>'0',':equipment_purpose' =>10,'equipment_type' => 22))
//                 ->queryAll();

					//$fieldoptions=$PickList->getwaterPickListOption();

 //print_r($serial);
		}
//echo $Field->fieldid;
		echo CHtml::dropDownList($attr_name, '',$fieldoptions,array('empty' => 'Select','class'=>$fieldclass.' h5 input-border p-2 mb-0 a','aria-label'=>'select example','id'=>$attr_id));}?>
						<div class="error-container hide">All errors displayed Here</div>

			</div>
		<?php } ?>
	</td>
				
	<?php	elseif($Field['uitype']==12): ?>
		<td class="<?php echo $Field->td_classname;?>">	<!--uitype is 12-->
		<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
		<?php //echo $Block->Fields[0]->tablename."and fieldname=". $Field->fieldname; ?>
		<div id="<?php echo $Field->columnname.'id'.$cnt_multiple_product.'_em_';?>" class="ajxwarning errorMessage tooltipImages bb5" style="display:none">Product Name cannot be blank.</div>
			<div class="input-group inputwidth">
				<span class="input-group-addon">
					<span class="glyphicon glyphicon-remove-circle cursorPointer text-info" type="button" onclick="<?php echo $obj_name; ?>RemoveValue('<?php echo $Field->columnname ?>','<?php echo $cnt_multiple_product ?>');"></span>
				</span>
				<!--<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>-->
				<input type="text" value="" id="<?php echo $Field->columnname.$cnt_multiple_product;?>" name ="<?php echo $Field->columnname.$cnt_multiple_product;?>" size=12 class="<?php echo $Field->classname;?>" readonly="readonly" autocomplete="off">
				<input type="hidden" value="" id="<?php echo $Field->columnname.'id'.$cnt_multiple_product;?>" name ="<?php echo $attr_name;?>" >
		</div>
		<div class="error-container hide">All errors displayed Here</div>
		</td>
		<?php	elseif($Field['uitype']==18): ?>
		<td class="<?php echo $Field->td_classname;?>">	<!--uitype is 12-->
			<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
			<div id="<?php echo $Field->columnname.'id'.$cnt_multiple_product.'_em_';?>" class="ajxwarning errorMessage tooltipImages bb5" style="display:none">Product Name cannot be blank.</div>
			<div class="input-group inputwidth">
				<span class="input-group-addon">
					<span class="glyphicon glyphicon-remove-circle cursorPointer text-info" type="button" onclick="<?php echo $obj_name; ?>RemoveValue('<?php echo $Field->columnname ?>','<?php echo $cnt_multiple_product ?>');"></span>
				</span>
				<!--<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>-->
				<input type="text" value="" id="<?php echo $Field->columnname.$cnt_multiple_product;?>" name ="<?php echo $Field->columnname.$cnt_multiple_product;?>" size=12 class="<?php echo $Field->classname;?>" <?php echo (strpos($Field->classname, 'ReadOnly') !== false ? 'readonly="readonly"' : "");?> autocomplete="off">	
				<input type="hidden" value="" id="<?php echo $Field->columnname.'id'.$cnt_multiple_product;?>" name ="<?php echo $attr_name;?>" >
				<span class="transearch input-group-addon">
					<span type="button" class="glyphicon glyphicon-search cursorPointer text-info" data-toggle="modal" data-target="#myModal22" onclick="showProductlistPop('<?php echo $cnt_multiple_product;?>','<?php echo $Field->columnname;?>','<?php echo $Field->relatedmodulename;?>','<?php echo $Field->fieldid;?>')"></span>

				</span>
			</div>
			<div class="error-container hide">All errors displayed Here</div>
		</td>
		<!-- month year -->
		<?php elseif($Field['uitype']==15):?>
		<td class="<?php echo $Field->td_classname;?>"><?php 
			if (strpos($Field->classname, 'ReadOnly') !== false) 
			echo CHtml::textField($attr_name, '',array('id'=>$attr_id,'class'=>$Field->classname,'readonly'=>true));
			else
			echo CHtml::textField($attr_name, '',array('id'=>$attr_id,'class'=>$Field->classname));
			?>
			<div id="<?php echo $attr_id.'_em_';?>" class="ajxwarning errorMessage tooltip_img bb6" style="display:none;"></div>
			<div class="error-container hide">All errors displayed Here</div>
		</td>
		<?php elseif($Field['uitype']==22):?>
		
		<?php 		$PickList=new MultiList;
		$PickList->fieldid=$Field->fieldid;
		$fieldoptions=$PickList->getMultiListOption($ModuleName); ?>
	 	<td class="<?php echo $Field->td_classname;?> multi_chosen">
		<?php 
		if($RecordID !=''){
		$vals	= explode(",",$Record->{$field['columnname']});
		$selected =array();
		foreach($vals as $val){
		$selected[$val] = array('selected' => 'selected');
		}
		}
		echo CHtml::listBox($attr_name, '',$fieldoptions,array('class'=>' inputwidth multi-select resean-select','id'=>$attr_id,'multiple' => 'true','value'=>$field['columnname']));
		?>
		<div class="error-container hide">All errors displayed Here</div>
		</td>
		<?php elseif($Field['uitype']==13):?>

		<td>
					<div class="error-container hide">All errors displayed Here</div>

		<div id="<?php echo $attr_id.'_em_';?>" class="ajxwarning errorMessage <?php echo $tooltip_class;?> bb1" style="display:none;"></div>
		<input  type="hidden" name="<?php echo $attr_name;?>" id="<?php echo $attr_id.'_dt';?>" value="<?php echo $field['columnname'];?>" />
		<input class=" input-border dtpick <?php echo $Field->classname;?>" type="text" id="<?php echo $attr_id;?>" value="<?php echo $field['columnname'];?>" autocomplete="off" readonly/>
		</td>

<?php elseif ($Field['uitype'] == 27): ?>
            <td>
                <div id="<?php echo $attr_id . '_em_'; ?>"
                     class="ajxwarning errorMessage tooltip_img bb1" style="display:none;"></div>
                <input type="hidden" name="<?php echo $attr_name; ?>" id="<?php echo $attr_id . '_jqdt'; ?>"
                       value="<?php echo $field['columnname']; ?>"/>
                <input class=" input-border inputwidth jqdt <?php echo $Field->classname; ?>" type="text"
                       id="<?php echo $attr_id; ?>" value="<?php echo $field['columnname']; ?>" autocomplete="off"/>
                       <div class="error-container hide">All errors displayed Here</div>
            </td>

		<?php endif;?>

	<?php endif;?>
<?php $MPkey++; endforeach;?>

								<td class="<?php echo $Block->Fields[0]->tablename;?>Delete input-border" id="<?php echo $Block->Fields[0]->tablename.'_'.$cnt_multiple_product.'_Delete'?>" class="<?php echo $Block->Fields[0]->tablename.'Delete'?>">
									<div class="d-flex justify-content-center align-items-center">
										<div class="action-icon-container <?php echo $obj_name;?>Delete" id="<?php echo $Block->Fields[0]->tablename.'_'.$MPkey.'_Delete'?>">
											<svg viewBox="0 0 18 19" class="action-icon action-icon--delete">
												<path d="M5.14414 15.2656H12.8539L13.2793 6.26562H4.71875L5.14414 15.2656Z"/>
												<path d="M15.1875 5H12.9375V3.59375C12.9375 2.97324 12.433 2.46875 11.8125 2.46875H6.1875C5.56699 2.46875 5.0625 2.97324 5.0625 3.59375V5H2.8125C2.50137 5 2.25 5.25137 2.25 5.5625V6.125C2.25 6.20234 2.31328 6.26562 2.39062 6.26562H3.45234L3.88652 15.459C3.91465 16.0584 4.41035 16.5312 5.00977 16.5312H12.9902C13.5914 16.5312 14.0854 16.0602 14.1135 15.459L14.5477 6.26562H15.6094C15.6867 6.26562 15.75 6.20234 15.75 6.125V5.5625C15.75 5.25137 15.4986 5 15.1875 5ZM6.32812 3.73438H11.6719V5H6.32812V3.73438ZM12.8549 15.2656H5.14512L4.71973 6.26562H13.2803L12.8549 15.2656Z"/>
											</svg>
										</div>
									</div>

                            	</td>
</tr>
<script>
	$('.dtpick').change(function(){	
		var id = $(this).attr('id');
		var vals = $(this).val();
		var new_Date = vals.split('-').reverse().join('-'); 
		$('#'+id+'_dt').val(new_Date);
	});
	$(".dtpick").each(function(){
	var id = $(this).attr('id');
	var vals = $(this).val();
		$( "#"+id ).datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'dd-mm-yy',
		});
	});
</script>
<script src="<?php echo Yii::app()->baseUrl . '/js/Tetra/jqdt.js' ?>"></script>
