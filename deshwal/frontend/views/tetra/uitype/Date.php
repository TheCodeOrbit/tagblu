	<?php //echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage')); ?>
	<?php $dt=date('Y-m-d',strtotime($Record->{$field['fieldname']})); 
	if($_REQUEST['Record'] != ''){
		echo $form->hiddenField($model,$field['fieldname'], array ('value'=>$dt));
	}else{
		$cur_date=date('Y-m-d');
		echo $form->hiddenField($model,$field['fieldname'], array ('value'=>$cur_date));
	}

	if($dt == '' or $dt =='1970-01-01' or $dt=='-0001-11-30'){ 
		//$Record[$field['fieldname']] = "";
		$cur_disp_date=date('d/m/Y');
		$Record[$field['fieldname']] = $cur_disp_date;	 
		$Record[$field['columnname']] = $cur_disp_date;	 
	}

	if($Record->{$field['columnname']} == '' or $Record->{$field['columnname']} =='1970-01-01' or $Record->{$field['columnname']}=='-0001-11-30')
	{
		$cur_disp_date=date('d/m/Y');
		$Record[$field['fieldname']] = $cur_disp_date;	 
		$Record[$field['columnname']] = $cur_disp_date;
	}
	//$cur_disp_date=date('d/m/Y');
	//$cur_disp_date=date('Y/m/d');
	//$cur_disp_date=date('Y-m-d');
	//echo "<br>cur_disp_date=$cur_disp_date";
//die;
//$Record->{$field['columnname']}
	if($Block->blocktype=="Simple" and $ActionName=="Edit"){
		echo $form->{"dateField"}($model,$field['columnname'],array ('class' => "input-outline me-3".' actionname'.$ActionName,'readonly'=>'readonly','value'=>$Record->{$field['columnname']}));
	} else if($Block->blocktype=="Simple"){
		//echo "<br>Inside simple case";
		//die;
		$cur_disp_date=date('Y-m-d');
		echo $form->{"dateField"}($model,$field['columnname'],array ('class' => "input-outline me-3".' actionname'.$ActionName,'value'=>"$cur_disp_date"));
	} else {
		echo $form->{"dateField"}($model,$field['columnname'],array ('class' => "input-outline simple-table__container__input".' actionname'.$ActionName,'value'=>$Record->{$field['columnname']}));
	}
	echo $form->error($model,$field['fieldname'],array('class'=>'ajxwarning errorMessage error-container','style'=>"top: 3rem;position: relative;left: -17rem;")); 	
	?>
