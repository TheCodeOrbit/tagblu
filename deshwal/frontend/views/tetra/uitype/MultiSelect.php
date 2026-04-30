<td class="record-label">
	<?php echo $form->labelEx($model,$field['fieldname'], array ('class' => 'control-label labelwidth')); ?>
</td>

<td class="record-value">
	<?php
	echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>

	<?php
		if($RecordID !=''){
		$vals	= explode(",",$Record->{$field['columnname']});
		$selected =array();
		foreach($vals as $val){
		$selected[$val] = array('selected' => 'selected');
		}
		}
		echo $form->{$field[fieldtype]}($model,$field['fieldname'], $field['fieldoptions'],array('class' => 'form-control inputwidth','value'=>$field['columnname'],'empty'=>'Select an Option','multiple' => 'true',
		'options' =>$selected)); 

		/* $data = array('1' => 'OB Removal', '3' => 'Adani', '6' => 'Coal Extraction');
		$selected   = array(
			'102' => array('selected' => 'selected'),
			//'103' => array('selected' => 'selected'),
		);*/
		//$htmlOptions = array('size' => '3', 'prompt'=>'Select an Option', 'multiple' => 'true', 'options' => $selected);
		//echo $form->listBox($model,$field['fieldname'], $data, $htmlOptions);*/
	?>
</td>