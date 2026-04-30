<?php 
$MPkey=0;
//echo "<br>Total Records in location module is=";
//print_r($Records);

if(count($Records)>0)
{
foreach($Records as $Record): //print_r($Record);?>


	<tr id="prod_tbl" class="prod_tbl">
		<!--<td class="<?php echo $ColumnList[0]->tablename;?>Delete text-center"><a href="javascript:void(0);"><span class="glyphicon glyphicon-trash"></span></a></td>-->
		<?php
		foreach($ColumnList as $key=> $Field):
		$attr_name="{$Field->tablename}[{$MPkey}][{$Field->fieldname}]";
		$attr_id="{$Field->tablename}_{$MPkey}_{$Field->fieldname}";
		$attr_class="{$Field->tablename}{$Field->fieldname}";

		?>

		<?php	if($Field['uitype']==1):?><!--HI ui type is 1  $Field->classname-->
		<td class="<?php echo $Field->td_classname;?>">
			<?php 

			if($Field->fieldid==275 || $Field->fieldid==276 || $Field->fieldid==2075 || $Field->fieldid==2081){
					$timeformat='timeformat';
					$p_holder='hh:mm';
				}else{
					$timeformat='';
					$p_holder='';
				}

					//	echo $Field->fieldid;

			echo CHtml::textField($attr_name,$Multiple_Record->{$Field->fieldname},array('id'=>$attr_id,'class'=>$timeformat.' input-border form-control '.$attr_class, 'placeholder'=>$p_holder));
			?>
			<div class="error-container hide">All errors displayed Here</div>

			<?php //echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
			<div id="<?php echo $attr_id.'_em_';?>" class="ajxwarning errorMessage tooltip_img bb1" style="display:none;"></div>
		</td>

		<?php	elseif($Field['uitype']==8): ?>
		<?php $PickList=new PickList;
		$PickList->fieldid=$Field->fieldid;
		echo $Block->Fields[0]->tablename;
		$fieldoptions=$PickList->getPickListOption($ModuleName);

		if($Field->fieldid==538 || $Field->fieldid==541){
		$refIdValue=$Record['location_id'];
		$refValue=$Record['location_name'];
		}
		if($Field->fieldid==1792 || $Field->fieldid==1795){
		$disabled="disabled";
		}else{
		$disabled="";
		}

		if($Field->fieldid==1793){
		$fieldclass=$Field->fieldname."2";
		}else{
		$fieldclass=$Field->fieldname;
		}

		//print_r($fieldoptions);
		?>
		<td class="<?php echo $Field->td_classname;?>">
		<?php if($Field->fieldid==538 || $Field->fieldid==541){echo CHtml::dropDownList($attr_name,$Multiple_Record->{$Field->fieldname},$fieldoptions,array('empty' => 'Select an Option','class'=>$fieldclass.' w-100 h5 input-border p-2 mb-0 ','disabled'=>$disabled,'id'=>$attr_id,'options' => array($refIdValue=>array('selected'=>true)) ));}
		else
			{echo CHtml::dropDownList($attr_name,$Multiple_Record->{$Field->fieldname},$fieldoptions,array('empty' => 'Select an Option','class'=>$fieldclass.' w-100 h5 input-border p-2 mb-0 ','disabled'=>$disabled,'id'=>$attr_id));}
?>
		<div class="error-container hide">All errors displayed Here</div>

		<?php //echo $form->error($model,$Field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
		</td>

		<?php	elseif($Field['uitype']==12): 
		$refIdValue=$Record['location_id'];
		$refValue=$Record['location_name'];
		//echo "<br>refIdValue=$refIdValue and refValue=$refValue";
		?>
		<td class="<?php echo $Field->td_classname;?>">	<!--uitype is 12-->
			<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
			<div class="input-group">
				<!--<span class="transponame input-group-addon">
					<span class="glyphicon glyphicon-remove-circle cursorPointer text-info" type="button" onclick="<?php echo $obj_name; ?>RemoveValue('<?php echo $Field->columnname ?>','<?php echo $MPkey ?>');"></span>
				</span>-->
				<!--<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>-->
				<input type="text" value="<?php echo $refValue;?>" id="<?php echo $Field->fieldname.$MPkey;?>" name ="<?php echo $Field->columnname.$MPkey;?>" size=12 class="<?php echo $Field->classname;?>" readonly="readonly" autocomplete="off" style="width: 150px;">
				<input type="hidden" value="<?php echo $refIdValue;?>" id="<?php echo $Field->fieldname."id".$MPkey;?>" name ="<?php echo $attr_name;?>" >
				<!--<span class="transearch input-group-addon">
					<span type="button" class="glyphicon glyphicon-search cursorPointer text-info"></span>
				</span>-->
			</div>
		</td>

		<?php elseif($Field['uitype']==22):?>
		<?php $PickList=new MultiList;
		$PickList->fieldid=$Field->fieldid;
		$fieldoptions=$PickList->getMultiListOption($ModuleName); ?>
		<td class="<?php echo $Field->td_classname;?>">
			<?php 
			if($RecordID !=''){
			$vals	= explode(",",$Record->{$field['columnname']});
			$selected =array();
			foreach($vals as $val){
			$selected[$val] = array('selected' => 'selected');
			}
			}
			echo CHtml::listBox($attr_name, '',$fieldoptions,array('empty' => 'Select an Option','class'=>'form-control multi-select','id'=>$attr_id,'multiple' => 'true','value'=>$field['columnname']));

			?>
			
		</td>
		<?php endif;?>
		<?php endforeach;?>
		<td class="<?php echo $ColumnList[0]->tablename;?>Delete input-border" id="<?php echo $ColumnList[0]->tablename.'_'.$cnt_multiple_product.'_Delete'?>" class="<?php echo $ColumnList[0]->tablename.'Delete'?>">
									<div class="d-flex justify-content-center align-items-center">
										<div class="action-icon-container <?php echo $ColumnList[0]->tablename;?>Delete" id="<?php echo $ColumnList[0]->tablename.'_'.$MPkey.'_Delete'?>">
											<svg viewBox="0 0 18 19" class="action-icon action-icon--delete">
												<path d="M5.14414 15.2656H12.8539L13.2793 6.26562H4.71875L5.14414 15.2656Z"/>
												<path d="M15.1875 5H12.9375V3.59375C12.9375 2.97324 12.433 2.46875 11.8125 2.46875H6.1875C5.56699 2.46875 5.0625 2.97324 5.0625 3.59375V5H2.8125C2.50137 5 2.25 5.25137 2.25 5.5625V6.125C2.25 6.20234 2.31328 6.26562 2.39062 6.26562H3.45234L3.88652 15.459C3.91465 16.0584 4.41035 16.5312 5.00977 16.5312H12.9902C13.5914 16.5312 14.0854 16.0602 14.1135 15.459L14.5477 6.26562H15.6094C15.6867 6.26562 15.75 6.20234 15.75 6.125V5.5625C15.75 5.25137 15.4986 5 15.1875 5ZM6.32812 3.73438H11.6719V5H6.32812V3.73438ZM12.8549 15.2656H5.14512L4.71973 6.26562H13.2803L12.8549 15.2656Z"/>
											</svg>
										</div>
									</div>
                            	</td>
	</tr><!-- multiple table ends -->
		
<?php $MPkey++;endforeach;}?>
