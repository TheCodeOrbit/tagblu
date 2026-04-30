<div class="d-flex justify-content-between align-items-center me-4">
	<?php //echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
	<?php $dt=date('Y-m-d',strtotime($Record->{$field['fieldname']})); 
	if($_REQUEST['Record'] != ''){
		echo $form->hiddenField($model,$field['fieldname'], array ('value'=>$dt));
	}else{
		echo $form->hiddenField($model,$field['fieldname'], array ('value'=>""));
	}

	if($dt == '' or $dt =='1970-01-01' or $dt=='-0001-11-30'){ 
		$Record[$field['fieldname']] = "";	 
	}else{ 
		//$Record[$field['fieldname']] = date("d/m/Y",strtotime($Record->{$field['columnname']}));
	}

	//$form->widget('zii.widgets.jui.CJuiDatePicker', array('model' => $model,'name' => "DateMonthYear",'id'=> $field['fieldname'],'value'=>$Record[$field['fieldname']],'options'=>array('showAnim'=>'explode','duration'=>500,'dateFormat' => 'dd/mm/yy',),'htmlOptions' => array('size' => '25',),));
	//echo "<input name='DateMonthYear' id='".$field['fieldname']."' value='".$Record[$field['fieldname']]."' type='date'>";

	//echo "<input type='date'>";

	// Todo: add class="input-outline " to below element Once completed remove the comment.
	echo $form->{"dateField"}($model,$field['columnname'],array ('class' => "input-outline",'value'=>$Record->{$field['columnname']}));
	echo $form->error($model,$field['fieldname'],array('class'=>'ajxwarning errorMessage error-container','style'=>"top: 3rem;position: relative;left: -17rem;")); 	
	?>
	
 
</div>
