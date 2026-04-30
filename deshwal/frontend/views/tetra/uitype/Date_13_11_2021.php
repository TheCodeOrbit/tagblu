<td class="record-label">
	<?php echo $form->labelEx($model,$field['fieldlable'], array ('class' => 'control-label labelwidth')); ?>
</td>

<td class="record-value">
	<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
	<?php $dt=date('Y-m-d',strtotime($Record->{$field['fieldname']})); 
	if($_REQUEST['Record'] != ''){
		echo $form->hiddenField($model,$field['fieldname'], array ('value'=>$dt));
	}else{
		echo $form->hiddenField($model,$field['fieldname'], array ('value'=>""));
	}

	if($dt == '' or $dt =='1970-01-01' or $dt=='-0001-11-30'){ 
		$Record[$field['fieldname']] = "";	 
	}else{ 
		$Record[$field['fieldname']] = date("d/m/Y",strtotime($Record->{$field['columnname']}));
	}

	$form->widget('zii.widgets.jui.CJuiDatePicker', array('model' => $model,'name' => "DateMonthYear",'id'=> $field['fieldname'],
		'value'=>$Record[$field['fieldname']],'options'=>array('showAnim'=>'explode','duration'=>500,
		'dateFormat' => 'dd/mm/yy',),'htmlOptions' => array('size' => '25',),));?>

	<span>
		<img style="height: 20px; width: 20px; position: relative; top: 3px; right: 25px;" src="<?php echo Yii::app()->request->baseUrl; ?>/images/calendar/cal_icon.png" alt="calendar">
	</span>
</td>