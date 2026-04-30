	<?php //echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
	<?php $dt=date('Y-m-d',strtotime($Record->{$field['fieldname']})); 
	if($_REQUEST['Record'] != ''){
		echo $form->hiddenField($model,$field['fieldname'], array ('value'=>$dt));
	}else{
		echo $form->hiddenField($model,$field['fieldname'], array ('value'=>""));
	}

	if($dt == '' or $dt =='1970-01-01' or $dt=='-0001-11-30'){ 
		$Record[$field['fieldname']] = "";	 
	}

	if($Block->blocktype=="Simple" and $ActionName=="Edit"){
		echo $form->{"dateField"}($model,$field['columnname'],array ('class' => "input-outline me-3".' actionname'.$ActionName,'readonly'=>'readonly','value'=>$Record->{$field['columnname']}));
	} else if($Block->blocktype=="Simple"){
		echo $form->{"dateField"}($model,$field['columnname'],array ('class' => "input-outline me-3".' actionname'.$ActionName,'value'=>$Record->{$field['columnname']}));
	} else {
		echo $form->{"dateField"}($model,$field['columnname'],array ('class' => "input-outline simple-table__container__input".' actionname'.$ActionName,'value'=>$Record->{$field['columnname']}));
	}
	echo $form->error($model,$field['fieldname'],array('class'=>'ajxwarning errorMessage error-container','style'=>"top: 3rem;position: relative;left: -17rem;")); 	
	?>
