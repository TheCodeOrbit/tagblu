<?php 
$MPkey=0;
//echo "<br>Total Records in location module is=";
//print_r($Records);

foreach($Records as $Record): //print_r($Record);?>

	<tr>
		<td class="<?php echo $ColumnList[0]->tablename;?>Delete text-center"><a href="javascript:void(0);"><span class="glyphicon glyphicon-trash"></span></a></td>
		<?php
		foreach($ColumnList as $key=> $Field):
		$attr_name="{$Field->tablename}[{$MPkey}][{$Field->fieldname}]";
		$attr_id="{$Field->tablename}_{$MPkey}_{$Field->fieldname}";
		?>

		<?php	if($Field['uitype']==1):?><!--HI ui type is 1-->
		<td class="<?php echo $Field->td_classname;?>">
			<?php 
			echo CHtml::textField($attr_name,$Multiple_Record->{$Field->fieldname},array('id'=>$attr_id,'class'=>$Field->classname));
			?>
			<?php //echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
			<div id="<?php echo $attr_id.'_em_';?>" class="ajxwarning errorMessage tooltip_img bb1" style="display:none;"></div>
		</td>

		<?php	elseif($Field['uitype']==8): ?>
		<?php $PickList=new PickList;
		$PickList->fieldid=$Field->fieldid;
		echo $Block->Fields[0]->tablename;
		$fieldoptions=$PickList->getPickListOption($ModuleName);
		//print_r($fieldoptions);
		?>
		<td class="<?php echo $Field->td_classname;?>">
		<?php echo CHtml::dropDownList($attr_name,$Multiple_Record->{$Field->fieldname},$fieldoptions,array('empty' => 'Select an Option','class'=>$Field->classname,'id'=>$attr_id));?>
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
				<span class="transponame input-group-addon">
					<span class="glyphicon glyphicon-remove-circle cursorPointer text-info" type="button" onclick="<?php echo $obj_name; ?>RemoveValue('<?php echo $Field->columnname ?>','<?php echo $MPkey ?>');"></span>
				</span>
				<!--<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>-->
				<input type="text" value="<?php echo $refValue;?>" id="<?php echo $Field->fieldname.$MPkey;?>" name ="<?php echo $Field->columnname.$MPkey;?>" size=12 class="<?php echo $Field->classname;?>" readonly="readonly" autocomplete="off">
				<input type="hidden" value="<?php echo $refIdValue;?>" id="<?php echo $Field->fieldname."id".$MPkey;?>" name ="<?php echo $attr_name;?>" >
				<span class="transearch input-group-addon">
					<span type="button" class="glyphicon glyphicon-search cursorPointer text-info"></span>
				</span>
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
	</tr><!-- multiple table ends -->
<?php $MPkey++;endforeach;?>