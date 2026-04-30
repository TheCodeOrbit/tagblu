<td class="record-label">
	<?php echo $form->labelEx($model,$field['fieldlable'], array ('class' => 'control-label labelwidth')); ?>
</td>

<td class="record-value">
	<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
	<?php $dt=date('Y-m',strtotime($Record->{$field['columnname']}));
	echo $form->hiddenField($model,$field['fieldname'], array ('value'=>$dt));
		if($dt == '' or $dt =='1970-01-01' or $dt=='-0001-11-30'){ 
			$Record[$field['fieldname']] = " ";	
		}else{
			$Record[$field['fieldname']] = date("m/Y",strtotime($Record->{$field['columnname']}));
		}

	$form->widget('zii.widgets.jui.CJuiDatePicker', array('model' => $model,'name' => "MonthYear",'id'=> $field['fieldname'],
	'value'=>$Record[$field['fieldname']],'options'=>array('showAnim'=>'explode','duration'=>500,'dateFormat' => 'mm/yy',),
	'htmlOptions' => array('size' => '25',),));?>

	<span>
		<img style="height: 20px; width: 20px; position: relative; top: 3px; right: 25px;" src="<?php echo Yii::app()->request->baseUrl; ?>/images/calendar/cal_icon.png" alt="calendar">
	</span>
</td>