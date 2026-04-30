<td class="record-label">
	<?php echo $form->labelEx($model,$field['fieldlable'], array ('class' => 'control-label labelwidth')); ?>
</td>
	
<td class="record-value">
	<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
	<input type="hidden" value="19" id="uitype" name="uitype"/>
	<?php 
		$dt=date('Y-m-d',strtotime($Record->{$field['fieldname']}));
		if($dt == '' or $dt =='1970-01-01' or $dt=='-0001-11-30'){ 
			$Record[$field['fieldname']] = " ";	 
		}else{ 
			$Record[$field['fieldname']] = date("d/m/Y",strtotime($Record->{$field['columnname']}));
		}
	?>
	<input type="hidden"  value="<?php if ($RecordID !='') echo $dt; else ''; ?>" id="<?php echo 'EditModel_'.$field['fieldname']; ?>" name="<?php echo 'EditModel['.$field['fieldname'].']'; ?>" />
	<input type="text"  value="<?php echo $Record[$field['fieldname']]; ?>"  id="<?php echo $field['fieldname']; ?>" name="<?php echo $field['fieldname']; ?>" class="<?php echo $field['classname'] ?>" autocomplete="off"/>
</td>