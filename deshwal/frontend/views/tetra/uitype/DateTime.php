<td class="record-label">
	<?php echo $form->labelEx($model,$field['fieldname'], array ('class' => 'control-label labelwidth')); ?>
</td>

<td class="record-value">
<?php echo $form->error($model,$field['fieldname'], array('class'=>'ajxwarning errorMessage'));
 
$disp_dt=(($Record->{$field[fieldname]} !='' && $Record->{$field[fieldname]} !='0000-00-00 00:00:00') ? date('d-m-Y H:i',strtotime($Record->{$field[fieldname]})) : '');
?>
<input  type="hidden" name="<?php echo 'EditModel['.$field->fieldname.']'; ?>" id="<?php echo 'EditModel_'.$field->fieldname.'_jqdt'; ?>" value="<?php echo $Record->{$field[fieldname]};?>" />

<input class="form-control inputwidth jqdt <?php echo $field->classname;?>" type="text" id="<?php echo 'EditModel_'.$field['fieldname'];?>" value="<?php echo $disp_dt;?>" autocomplete="off" />

</td>
