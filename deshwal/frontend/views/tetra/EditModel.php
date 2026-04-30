<?php
/**
 * EditModel class.
 * EditModel is the data structure for keeping
 * EditModel form data. It is used by the 'Module' action of 'Controller'.
 */
class EditModel extends CActiveRecord
{
	public $_members = array();
	public $tableName;
	public $fieldId;
	public $recordId;
	public $Multiple_Records=array();
	function __construct($tablename,$fieldid='')
	{
		$this->fieldId=$fieldid;
		$this->setTableName($tablename);
		$Columns=$this->getProperty();
		//print_r($Columns);
		//die;	
		foreach($Columns as $Column)
		$this->_members[$Column[columnname]] = null;
		$this->_members[$fieldid] = null;
		$this->_members['tableName'] = null;
		$this->_members['fieldId'] = null;
		$this->_members['mode'] = null;
		parent::__construct();
	}
	public function tableName()
	{
		return $this->tableName;
	}	
	public function setTableName($tablename)
	{
		$this->tableName=$tablename;
	}
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		$fieldId=$this->fieldId;	
		$validator=$this->getValidation();
		$arr_rules=array();
		foreach($validator as $validator_key=> $validator_name)
		{
			$validation_rule=$this->getValidationRule("$validator_name");
			$arr_rules[$validator_key][0]=$validation_rule;
			$arr_rules[$validator_key][1]="$validator_name";
			if($validator_name=="length")
			$arr_rules[$validator_key]['max']=100;

			if($validator_name=="numerical")
			$arr_rules[$validator_key]['integerOnly']=true;
		}
		
		$validator_key+=1;
		$arr_rules[$validator_key][0]="mode,tableName,fieldId,$fieldId,Multiple_Records,recordId";
		$arr_rules[$validator_key][1]='safe';
		return $arr_rules;
		
	}
	public function getValidation()
	{
		$table_name=$this->tableName();
		$connection=Yii::app()->db;
		$command= $connection->createCommand("select distinct(validator_name) from field where tablename='$table_name'");
		$validator=$command->queryAll();
		$final_validator=array();
		foreach($validator as $validator_name)
		{
			if(strpos($validator_name['validator_name'],'~')!=false)
			{
			$arr_vali=explode("~",$validator_name['validator_name']);
			foreach($arr_vali as $vali)
			$final_validator[]=$vali;
			}
			else
			$final_validator[]=$validator_name['validator_name'];
			
		}
		return array_unique($final_validator);
	}
	public function getValidationRule($validator)
	{
		$table_name=$this->tableName();
		$connection=Yii::app()->db;
		$command= $connection->createCommand("select columnname from field where tablename='$table_name' and (validator_name like '%$validator%')");
		$arr_Columns = $command->queryAll();
		$Columns="";	
		foreach($arr_Columns as $column)
		$Columns.=$column['columnname'].",";
		$Columns=substr($Columns,0,-1);
		return 	$Columns;
	}
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		$Columns=$this->getProperty();	
		$arr_lable=array();
		foreach($Columns as $Column)
		$arr_lable[$Column[columnname]]=$Column[fieldlable];
		/*echo "<br>Lable=";
		print_r($arr_lable);
		die;*/
		return 	$arr_lable;
	}
	public function getProperty()
	{
		$table_name=$this->tableName();
		$connection=Yii::app()->db;
		$query="select field.columnname,field.fieldlable from field where tablename='$table_name'";
		$command= $connection->createCommand($query);
		$Columns = $command->queryAll();
		return 	$Columns;		
	}
	public function getTabDetail($ModuleName)
	{
		$connection=Yii::app()->db;
		$q_tab="select * from tab where name='$ModuleName'";
		$arr_tab=$connection->createCommand($q_tab)->queryRow();
		return $arr_tab;
	}
	public function getActionList($ModuleName)
	{
		$ActionList=array();
		$actionName=$ModuleName;
		$arr_tab=$this->getTabDetail($ModuleName);
		$ActionList['ActionName']=$actionName;
		$ActionList['ModuleName']=$ModuleName;
		$ActionList['ModuleLabel']=$arr_tab['tablable'];
		return $ActionList;
	}
	public function getFieldDetail($rolebasedrecord)
	{
		$fieldid=$this->fieldId;
		$fieldids=$this->fieldId;
		$table_name=$this->tableName();
		$RecordId=$this->_members[$fieldid];
		$roleid =$rolebasedrecord;
		if(!empty($RecordId))
		{
		$view="edit_view";
		$Record=$this->find("$fieldid=".$RecordId);
		}
		else
		$view="create_view";
		$Tab=new Tab;
		$Column=$Tab->with('Blocks')->find("Blocks.edit_view=1 and Blocks.presence=1 and name='$table_name'");
		$tabId=$Column->tabid;

		$model1=new Reference($table_name,$fieldid);
		foreach($Column->Blocks as $BlockKey=>$Block)
		{
			if($Block->blocktype=="Multiple" and !empty($RecordId))
			{
				$Multiple_table=$Block->Fields[0]->tablename;
				if($Multiple_table=="contractorattendancedata")
				{
				$obj_module_contractorattendancedata=new contractorattendancedata;
				$contractorattendancedata_data=$obj_module_contractorattendancedata->findAll("attendanceid=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$contractorattendancedata_data;
				}
				if($Multiple_table=="data_obremovals")
				{
				$obj_module_data_obremoval=new data_obremoval;
				$data_obremoval_data=$obj_module_data_obremoval->findAll("obcesummary_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$data_obremoval_data;
				}
				if($Multiple_table=="overall_rom_qty")
				{
				$obj_module_overall_rom_qty=new overall_rom_qty;
				$overall_rom_qty_data=$obj_module_overall_rom_qty->findAll("cess_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$overall_rom_qty_data;
				}
				if($Multiple_table=="overall_obremoval_data")
				{
				$obj_module_overall_obremoval_data=new overall_obremoval_data;
				$overall_obremoval_data_data=$obj_module_overall_obremoval_data->findAll("obrshift_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$overall_obremoval_data_data;
				}
				if($Multiple_table=="cp_loss")
				{
				$obj_module_cp_loss=new cp_loss;
				$cp_loss_data=$obj_module_cp_loss->findAll("cess_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$cp_loss_data;
				}
				if($Multiple_table=="cess_data")
				{
				$obj_module_cess_data=new cess_data;
				$cess_data_data=$obj_module_cess_data->findAll("cess_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$cess_data_data;
				}
				if($Multiple_table=="cess_contractorwise")
				{
				$obj_module_cess_contractorwise=new cess_contractorwise;
				$cess_contractorwise_data=$obj_module_cess_contractorwise->findAll("cess_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$cess_contractorwise_data;
				}
				if($Multiple_table=="rainfall")
				{
				$obj_module_rainfall=new rainfall;
				$rainfall_data=$obj_module_rainfall->findAll("obrshift_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$rainfall_data;
				}
				if($Multiple_table=="equipment_hiring")
				{
				$obj_module_equipment_hiring=new equipment_hiring;
				$equipment_hiring_data=$obj_module_equipment_hiring->findAll("obrshift_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$equipment_hiring_data;
				}
				if($Multiple_table=="obremoval_data")
				{
				$obj_module_obremoval_data=new obremoval_data;
				$obremoval_data_data=$obj_module_obremoval_data->findAll("obrshift_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$obremoval_data_data;
				}
				if($Multiple_table=="obrshift_data")
				{
				$obj_module_obrshift_data=new obrshift_data;
				$obrshift_data_data=$obj_module_obrshift_data->findAll("obrshift_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$obrshift_data_data;
				}
				if($Multiple_table=="obr_loss_production_hours")
				{
				$obj_module_obr_loss_production_hours=new obr_loss_production_hours;
				$obr_loss_production_hours_data=$obj_module_obr_loss_production_hours->findAll("obrshift_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$obr_loss_production_hours_data;
				}
				if($Multiple_table=="obr_contractor_machine")
				{
				$obj_module_obr_contractor_machine=new obr_contractor_machine;
				$obr_contractor_machine_data=$obj_module_obr_contractor_machine->findAll("obr_contractorid=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$obr_contractor_machine_data;
				}
				if($Multiple_table=="mine_loss_dispatch_hours")
				{
				$obj_module_mine_loss_dispatch_hours=new mine_loss_dispatch_hours;
				$mine_loss_dispatch_hours_data=$obj_module_mine_loss_dispatch_hours->findAll("logisticmine_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$mine_loss_dispatch_hours_data;
				}
				if($Multiple_table=="reject_coal_dispatch")
				{
				$obj_module_reject_coal_dispatch=new reject_coal_dispatch;
				$reject_coal_dispatch_data=$obj_module_reject_coal_dispatch->findAll("logisticmine_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$reject_coal_dispatch_data;
				}
				if($Multiple_table=="road_dispatch_siding")
				{
				$obj_module_road_dispatch_siding=new road_dispatch_siding;
				$road_dispatch_siding_data=$obj_module_road_dispatch_siding->findAll("logisticmine_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$road_dispatch_siding_data;
				}
				if($Multiple_table=="washed_coal_dispatch")
				{
				$obj_module_washed_coal_dispatch=new washed_coal_dispatch;
				$washed_coal_dispatch_data=$obj_module_washed_coal_dispatch->findAll("logisticmine_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$washed_coal_dispatch_data;
				}
				if($Multiple_table=="rakedispatch_siding")
				{
				$obj_module_rakedispatch_siding=new rakedispatch_siding;
				$rakedispatch_siding_data=$obj_module_rakedispatch_siding->findAll("logistic_siding_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$rakedispatch_siding_data;
				}
				if($Multiple_table=="unproductive_loss")
				{
				$obj_module_unproductive_loss=new unproductive_loss;
				$unproductive_loss_data=$obj_module_unproductive_loss->findAll("logistic_siding_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$unproductive_loss_data;
				}
				if($Multiple_table=="loss_dispatch_hours")
				{
				$obj_module_loss_dispatch_hours=new loss_dispatch_hours;
				$loss_dispatch_hours_data=$obj_module_loss_dispatch_hours->findAll("logistic_siding_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$loss_dispatch_hours_data;
				}
				if($Multiple_table=="loss_production_hours")
				{
				$obj_module_loss_production_hours=new loss_production_hours;
				$loss_production_hours_data=$obj_module_loss_production_hours->findAll("washeryinput_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$loss_production_hours_data;
				}
				if($Multiple_table=="romintake")
				{
				$obj_module_romintake=new romintake;
				$romintake_data=$obj_module_romintake->findAll("washeryinput_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$romintake_data;
				}
				if($Multiple_table=="washed_coal_production")
				{
				$obj_module_washed_coal_production=new washed_coal_production;
				$washed_coal_production_data=$obj_module_washed_coal_production->findAll("washeryinput_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$washed_coal_production_data;
				}
				if($Multiple_table=="flocullent_consumption")
				{
				$obj_module_flocullent_consumption=new flocullent_consumption;
				$flocullent_consumption_data=$obj_module_flocullent_consumption->findAll("washeryinput_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$flocullent_consumption_data;
				}
				if($Multiple_table=="power_consumption")
				{
				$obj_module_power_consumption=new power_consumption;
				$power_consumption_data=$obj_module_power_consumption->findAll("washeryinput_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$power_consumption_data;
				}
				if($Multiple_table=="washery_equipment")
				{
				$obj_module_washery_equipments=new washery_equipment;
				$washery_equipments_data=$obj_module_washery_equipments->findAll("washeryinput_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$washery_equipments_data;
				}
	     			if($Multiple_table=="drilling_data")
				{
				$obj_drilling_data=new drilling_data;
				$data_drilling_data=$obj_drilling_data->findAll("dailydrilling_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$data_drilling_data;
				}
				if($Multiple_table=="openingstock_data")
				{
				$obj_openingstock_data=new openingstock_data;
				$openingstock_data_data=$obj_openingstock_data->findAll("openingstockid=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$openingstock_data_data;
				}	if($Multiple_table=="daily_blasting_data")
				{
				$obj_daily_blasting_data=new daily_blasting_data;
				$blasting_data_data=$obj_daily_blasting_data->findAll("dailyblasting_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$blasting_data_data;
				}
	     			if($Multiple_table=="stockadjment_data")
				{
				$obj_openingstock_data=new stockadjment_data;
				$openingstock_data_data=$obj_openingstock_data->findAll("stock_adjmentid=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$openingstock_data_data;
				}

				if($Multiple_table=="obr_contractor_dumper")
				{
				$obj_module_obr_contractor_dumper=new obr_contractor_dumper;
				$obr_contractor_dumper_data=$obj_module_obr_contractor_dumper->findAll("obr_contractorid=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$obr_contractor_dumper_data;
				}
				if($Multiple_table=="ce_machine_details")
				{
				$obj_module_ce_machine_details=new ce_machine_details;
				$ce_machine_data=$obj_module_ce_machine_details->findAll("cecontractor_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$ce_machine_data;
				}
				if($Multiple_table=="ce_dumper_running_details")
				{
				$obj_module_ce_dumper_running_details=new ce_dumper_running_details;
				$ce_dumper_running_details_data=$obj_module_ce_dumper_running_details->findAll("cecontractor_id=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$ce_dumper_running_details_data;
				}
				if($Multiple_table=="treefelling_data")
				{
				$obj_module_treefelling_data=new treefelling_data;
				$treefellingdata=$obj_module_treefelling_data->findAll("treefellingid=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$treefellingdata;
				}
				if($Multiple_table=="productiondata")
				{
				$obj_module_productiondata=new productiondata;
				$productiondata_data=$obj_module_productiondata->findAll("productionid=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$productiondata_data;
				}
				if($Multiple_table=="productiontotaldata")
				{
				$obj_module_productiontotaldata=new productiontotaldata;
				$overall_productiontotaldata=$obj_module_productiontotaldata->findAll("productionid=$RecordId");
				$Record->Multiple_Records[$Block->blockid]=$overall_productiontotaldata;
				}
				foreach($Block->Fields as $FieldKey=>$Field)
				{
					if($Field->uitype==8)
					{
						$PickList=new PickList;
						$PickList->fieldid=$Field->fieldid;
						$Column->Blocks[$BlockKey]->Fields[$FieldKey]->fieldoptions=$PickList->getPickListOption($table_name);
					}
					else if($Field->uitype==22)
					{
						$PickList=new MultiList;
						$PickList->fieldid=$Field->fieldid;
						$Column->Blocks[$BlockKey]->Fields[$FieldKey]->fieldoptions=$PickList->getMultiListOption($table_name);
					}
					elseif($Field->uitype==12)
					{  
						$refModuleName=$model1->getRelatedNoduleName($Field->fieldid);
						$Column->Blocks[$BlockKey]->Fields[$FieldKey]->relatedmodulename=$refModuleName;
						$refFieldDispName=$model1->getRelatedDisplayFieldName($Field->fieldid);
						$Column->Blocks[$BlockKey]->Fields[$FieldKey]->getRelatedDisplayFieldName=$refFieldDispName;
						foreach($Record->Multiple_Records[$Block->blockid] as $MPkey=> $Multiple_Record)
						{
							$refFieldValue=$Multiple_Record->{$Field->fieldname};
							//echo "<br>refFieldValue=$refFieldValue";
							$refFieldDispValue=$model1->getRefEntityValue($Field->fieldid,$refFieldValue);
							//echo "<br>refFieldDispValue=".$refFieldDispValue;
							$Record->Multiple_Records[$Block->blockid][$MPkey]->{$refFieldDispName}=$refFieldDispValue;
						}
					}		
				}
			}
			else
			{
				$FieldType=new FieldType;
				foreach($Block->Fields as $FieldKey=>$Field)
				{	
					$FieldTypeRecord=$FieldType->find("uitype=$Field->uitype");
					$Column->Blocks[$BlockKey]->Fields[$FieldKey]->fieldtype=$FieldTypeRecord->getfieldtype;
					if($Field->classname=="")
					$Column->Blocks[$BlockKey]->Fields[$FieldKey]->classname=$FieldTypeRecord->classname;
					if($Field->uitype==8)
					{
					$PickList=new PickList;
					$PickList->fieldid=$Field->fieldid;
					$Column->Blocks[$BlockKey]->Fields[$FieldKey]->fieldoptions=$PickList->getPickListOption($table_name);
					}
					else if($Field->uitype==22)
					{
					$PickList=new MultiList;
					$PickList->fieldid=$Field->fieldid;
					$Column->Blocks[$BlockKey]->Fields[$FieldKey]->fieldoptions=$PickList->getMultiListOption($table_name);
					}
					elseif($Field->uitype==53){
					$Column->Blocks[$BlockKey]->Fields[$FieldKey]->fieldoptions=$this->getusers($Field->fieldid,$Field->uitype,$roleid);
					}
					
					//  code for date field's edit view
					elseif($Field->uitype==13)
					{
						$Date_show = date("d/m/Y", strtotime($Record->{$Field->fieldname}));
						//$Record->{$Field->fieldname} = $Date_show;
						if($Date_show == '' or $Date_show =='1970-01-01' or $Date_show=='-0001-11-30'){ 
						}else{
						$Column->Blocks[$BlockKey]->Fields[$FieldKey]->fieldoptions=$this->getusers($Field->fieldid,$Field->uitype,$roleid);
						}
					}
					elseif($Field->uitype==17)
					{
						$Date_show = date("d/m/Y", strtotime($Record->{$Field->fieldname}));
						//$Record->{$Field->fieldname} = $Date_show;
						if($Date_show == '' or $Date_show =='1970-01-01' or $Date_show=='-0001-11-30'){ 
						}else{
						$Column->Blocks[$BlockKey]->Fields[$FieldKey]->fieldoptions=$this->getusers($Field->fieldid,$Field->uitype,$roleid);
						}
					}
					elseif($Field->uitype==19)
					{
						$Date_show = date("d/m/Y", strtotime($Record->{$Field->fieldname}));
						//$Record->{$Field->fieldname} = $Date_show;
						if($Date_show == '' or $Date_show =='1970-01-01' or $Date_show=='-0001-11-30'){ 
						}else{
						$Column->Blocks[$BlockKey]->Fields[$FieldKey]->fieldoptions=$this->getusers($Field->fieldid,$Field->uitype,$roleid);
						}
					}
					elseif($Field->uitype==15)
					{
						$month_Date_Show = substr(date("d/m/Y", strtotime($Record->{$Field->fieldname})),3);
						//$Record->{$Field->fieldname} = $month_Date_Show;
						if($month_Date_Show == '' or $month_Date_Show =='1970-01-01' or $month_Date_Show=='-0001-11-30'){ 
						}else{
						$Column->Blocks[$BlockKey]->Fields[$FieldKey]->fieldoptions=$this->getusers($Field->fieldid,$Field->uitype,$roleid);
						}
					}			

					elseif($Field->uitype==12)
					{  
					if($_REQUEST['Record'] != '')
					{
					$Column->Blocks[$BlockKey]->Fields[$FieldKey]->reffieldvalue=$model1->getReferenceEntityValue($Field->fieldid,$Field->fieldname,$fieldids);
					$Column->Blocks[$BlockKey]->Fields[$FieldKey]->relatedmodulename=$model1->getRelatedNoduleName($Field->fieldid);
					$Column->Blocks[$BlockKey]->Fields[$FieldKey]->getRelatedDisplayFieldName=$model1->getRelatedDisplayFieldName($Field->fieldid);
					
                    			}
					else
					{
				$Column->Blocks[$BlockKey]->Fields[$FieldKey]->relatedmodulename=$model1->getRelatedNoduleName($Field->fieldid);
					$Column->Blocks[$BlockKey]->Fields[$FieldKey]->getRelatedDisplayFieldName=$model1->getRelatedDisplayFieldName($Field->fieldid);
					}
					}
				}
			}
		}
		/*echo "<pre>";
		print_r($Record);
		die;*/
		if(!empty($RecordId))
		return array($Column,$Record);
		else
		return $Column;
	}
	

	public function getusername($uitype)
	{
		$model=new UsersDetails();
		$Columns=$model->username($uitype);
		return $Columns;
	}

	public function getusers($fieldid,$uitype,$roleid)
	{
		$model=new UsersDetails();
		$userDetail=$model->users($fieldid,$uitype,$roleid);
		return $userDetail;
	}

	public function checkMultiselect()
		{
			$table_name=$this->tableName();
			$multitoField=Yii::app()->db->createCommand()->select('columnname')->from('field')->where("tablename='$table_name' and uitype=22")->queryRow();
			if(count($multitoField)<1)
			return false;
			else
			return $multitoField['columnname'];
		}

	public function checkAutoNo()
		{
			$table_name=$this->tableName();
			$autoField=Yii::app()->db->createCommand()->select('columnname')->from('field')->where("tablename='$table_name' and uitype=11")->queryRow();
			if(count($autoField)<1)
			return false;
			else
			return $autoField['columnname'];
		}

	
	public function getAutoNo($tabs,$uid)
		{
			$table_name	= $this->tableName();	
			$model		= new AutoNo();
			$orderno	= $model->getautomoduleno($tabs,$uid,$table_name);
			return $orderno;
		}
	public function setAutoNo($tabs)
		{
			$table_name	= $this->tableName();	
			$model		= new AutoNo();
			$upAutoNo	= $model->setAutomoduleno($tabs,$table_name);
			return $upAutoNo;
		}	
	
	public function setMultiSelectNo($multitoField)
		{
			$vals		= $_POST['EditModel'][$multitoField];
			if($vals !=''){
			$getvalue	= implode(",",$vals);
			}
			return $getvalue;
		}
	public function relations()
	    {
		return array(
 		    'drilling_data'=>array(self::HAS_MANY, 'drilling_data', 'dailydrilling_id'),
        	   'openingstock_data'=>array(self::HAS_MANY, 'openingstock_data', 'openingstockid')
		 			
		);
	    }


	
	public function getDepotCode()
		{
			$user_id=Yii::app()->session['id'];
			$rs_depot=Yii::app()->db->createCommand("select depot_code from users where id=$user_id");
			$arr_depot= $rs_depot->queryRow();
			$depot_code=$arr_depot['depot_code'];
			return $depot_code;
		}
	public function checkDepotUser()
		{
			$user_id=Yii::app()->session['id'];
			$rs_user=Yii::app()->db->createCommand("select utypeid from users where id=$user_id");
			$arr_user= $rs_user->queryRow();
			$depot_user_type=$arr_user['utypeid'];
			if($depot_user_type==9)
			return 1;
			else
			return 0;
		}
	public function getAppDate($ModuleName)
	{
		$depot_id=$this->getDepotCode();
		//print_r($_POST);
		/*if($ModuleName=="Order")
		$division_id=$_POST['EditModel']['division'];			
		else*/
		$division_id=$_POST['EditModel']['divisionid'];
		

		//echo "<br>division_id=$division_id";
		if($division_id=="")
		$app_date=date('Y-m-d H:i:s');
		else
		{
		$q_depot_date="select app_date from DepotDate where depot_code=$depot_id and division=$division_id";
		//echo "<br>q_depot_date=$q_depot_date";
		
		$rs_depot_date=Yii::app()->db->createCommand($q_depot_date);
		$arr_depot_date= $rs_depot_date->queryRow();
		//echo "<br>App Date Array";
		//print_r($arr_depot_date);
		$app_date=$arr_depot_date['app_date'];
		//echo "app_date=$app_date";
		$app_time=date("H:i:s");
		//echo "<br>time=".date("H:i:s");
		$app_date=$app_date." ".$app_time;
		//echo "<br>Final App Date=$app_date";
		//die;
		}
		return $app_date;
					
	}
	public function getRelatedRefField()
		{
			$ModuleName=$this->tableName;
			$SourceRecord=Yii::app()->request->getParam('SourceRecord');
			$ref_fields=array();
			if($ModuleName=="Customer2Division" or $ModuleName=="Contact")
			{
			$rs_ref_field=Yii::app()->db->createCommand("select d.depotid,d.depotname,c.customername from Customer c inner join Depot d on c.user_depot_code=d.depotid  where c.customerid=$SourceRecord");
			$arr_ref_field= $rs_ref_field->queryRow();
			$ref_fields['depotid']=$arr_ref_field['depotid'];	
			$ref_fields['depotname']=$arr_ref_field['depotname'];
			$ref_fields['customerid']=$SourceRecord;	
			$ref_fields['customername']=$arr_ref_field['customername'];
			}
			elseif($ModuleName=="Depot2Division")
			{
			$rs_ref_field=Yii::app()->db->createCommand("select d.depotid,d.depotname from Depot d where d.depotid=$SourceRecord");
			$arr_ref_field= $rs_ref_field->queryRow();
			$ref_fields['depotid']=$arr_ref_field['depotid'];	
			$ref_fields['depotname']=$arr_ref_field['depotname'];
			}
			
			//print_r($ref_fields);
			//die;
			return $ref_fields;
		}
	public function getModuleType()
		{
			$ModuleName=$this->tableName;
			$rs_module=Yii::app()->db->createCommand("select module_type from tab where name='$ModuleName'");
			$arr_module= $rs_module->queryRow();
			$module_type=$arr_module['module_type'];
			return $module_type;
		}
	public function getRecordAutoNo($AutoColumn,$RecordId)
	{
		$fieldId=$this->fieldId;
		$tableName=$this->tableName;
		$connection = Yii::app()->db;
		$query = "SELECT $AutoColumn FROM $tableName WHERE $fieldId = '$RecordId'";
		$command = $connection->createCommand($query);
		$Columns = $command->queryRow();
		$AutoNo=$Columns[$AutoColumn];
		return $AutoNo;
	}
	public function saveModule($tabs)
		{
			/*echo "<pre>";
			print_r($_POST);
			die;*/
			$SourceRecord=Yii::app()->request->getParam('SourceRecord');
			$uid=Yii::app()->session['id'];
			$depot_code=$this->getDepotCode();
			$transaction = $this->dbConnection->beginTransaction(); // Transaction begin
			try
			{
				if($multitoField=$this->checkMultiselect()){
				$multivals=$this->setMultiSelectNo($multitoField);
				$this->{$multitoField}=$multivals;
				} 
				$ModuleName=$this->tableName;	
				if($this->checkDepotUser()){
				$current_depot_code=$depot_code;
				$this->user_depot_code=$depot_code;
				//$this->depotname=$depot_code;
				}
				else
				{
					$current_depot_code=$_POST['EditModel']['depotname'];	
					$this->user_depot_code=$_POST['EditModel']['depotname'];
				}
				//$assinid=$_POST['EditModel']['ownerid'];				
				$this->creatorid=$uid;
				//$this->ownerid=$assinid;
				$this->modifiedby=$uid;
				if($this->getModuleType()=="Master" or $this->getModuleType()=="Related")
				{
				$this->createdtime=date('Y-m-d H:i:s');
				$this->modifiedtime=date('Y-m-d H:i:s');
				//$this->createdtime=new CDbExpression('NOW()');
				//$this->modifiedtime=new CDbExpression('NOW()');	
				}
				else
				{
				$app_date=$this->getAppDate($ModuleName);
				//echo "<br>app_date=$app_date";
				$this->modifiedtime=date('Y-m-d H:i:s');
				$this->createdtime=date('Y-m-d H:i:s');
				//$this->createdtime=$app_date;
				//$this->modifiedtime=$app_date;
				}
				$this->save(false);
				$entityId=$this->{$this->fieldId};
				if($SourceRecord!="")
				{
					$entityRelModel=new EntityRel;
					$entityRelModel->saveEntityRel($ModuleName,$entityId);
				}
				$this->{$this->fieldId}=$entityId;
				$id =$this->fieldId;
				if($ModuleName=="contractorattendance"){
					$ModelReceiptProduct=new contractorattendancedata;
					$ModelReceiptProduct->contractorattendancedatavalue($entityId);
					$objDashboard=new dashboard_status;
					$objDashboard->updateDashboardContractorStatus(17);
					$sendfunction=new sendfunction;
					$sendfunction->savesendfunction($entityId,$ModuleName,$id);
				}else if($ModuleName=="obr_contractor"){
					$Modelobrmachine=new obr_contractor_machine;
					$Modelobrmachine->obr_contractormachinesave($entityId);
					$Modelobrdumper=new obr_contractor_dumper;
					$Modelobrdumper->obr_contractordumpersave($entityId);
					$objDashboard=new dashboard_status;
					$objDashboard->updateDashboardContractorStatus(6);
					$sendfunction=new sendfunction;
					$sendfunction->savesendfunction($entityId,$ModuleName,$id);
				}else if($ModuleName=="cess"){
					$cess_data=new cess_data;
					$cess_data->cess_datasave($entityId);
					$cp_loss=new cp_loss;
					$cp_loss->cp_losssave($entityId);
					$cess_contractorwise=new cess_contractorwise;
					$cess_contractorwise->cess_contractorwisesave($entityId);
					$overall_rom_qty=new overall_rom_qty;
					$overall_rom_qty->overall_rom_qtysave($entityId);
					$objDashboard=new dashboard_status;
					$objDashboard->updateDashboardStatus(11);
					$sendfunction=new sendfunction;
					$sendfunction->savesendfunction($entityId,$ModuleName,$id);
				}else if($ModuleName=="washeryinput"){
					$ModelLossProductionHours=new loss_production_hours;
					$ModelLossProductionHours->save_loss_production_hours($entityId);
					$modelromintake=new romintake;
					$modelromintake->save_romintake($entityId);
					$modelwashed_coal_production=new washed_coal_production;
					$modelwashed_coal_production->save_washed_coal_production($entityId);
					$modelflocullent_consumption=new flocullent_consumption;
					$modelflocullent_consumption->save_flocullent_consumption($entityId);
					$modelpower_consumption=new power_consumption;
					$modelpower_consumption->save_power_consumption($entityId);
					$modelwashery_equipment=new washery_equipment;
					$modelwashery_equipment->save_washery_equipment($entityId);
					$objDashboard=new dashboard_status;
					$objDashboard->updateDashboardStatus(15);
					$sendfunction=new sendfunction;
					$sendfunction->savesendfunction($entityId,$ModuleName,$id);
				}else if($ModuleName=="cecontractor"){
					$Modelcemachine=new ce_machine_details;
					$Modelcemachine->ce_contractormachinesave($entityId);
					$Modelcerdumper=new ce_dumper_running_details;
					$Modelcerdumper->ce_contractordumpersave($entityId);
					$objDashboard=new dashboard_status;
					$objDashboard->updateDashboardContractorStatus(13);
					$sendfunction=new sendfunction;
					$sendfunction->savesendfunction($entityId,$ModuleName,$id);
				}else if($ModuleName=="dailydrilling"){
					$drillingdata=new drilling_data;
					$drillingdata->savedailydrillingvalue($entityId);
					$objDashboard=new dashboard_status;
					$objDashboard->updateDashboardStatus(2);
					$sendfunction=new sendfunction;
					$sendfunction->savesendfunction($entityId,$ModuleName,$id);
				}else if($ModuleName=="logisticmine"){
					$mine_loss_dispatch_hours=new mine_loss_dispatch_hours;
					$mine_loss_dispatch_hours->save_mine_loss_dispatch_hours($entityId);
					$reject_coal_dispatch=new reject_coal_dispatch;
					$reject_coal_dispatch->save_reject_coal_dispatch($entityId);
					$road_dispatch_siding=new road_dispatch_siding;
					$road_dispatch_siding->save_road_dispatch_siding($entityId);
					$washed_coal_dispatch=new washed_coal_dispatch;
					$washed_coal_dispatch->save_washed_coal_dispatch($entityId);	
					$objDashboard=new dashboard_status;
					$objDashboard->updateDashboardStatus(18);
					$sendfunction=new sendfunction;
					$sendfunction->savesendfunction($entityId,$ModuleName,$id);
				}else if($ModuleName=="logisticsiding"){
					$rakedispatch_siding=new rakedispatch_siding;
					$rakedispatch_siding->save_rakedispatch_siding($entityId);
					$unproductive_loss=new unproductive_loss;
					$unproductive_loss->save_unproductive_loss($entityId);
					$loss_dispatch_hours=new loss_dispatch_hours;
					$loss_dispatch_hours->save_loss_dispatch_hours($entityId);
					//echo "<br>all save done";
					$objDashboard=new dashboard_status;
					$objDashboard->updateDashboardStatus(10);
					$sendfunction=new sendfunction;
					$sendfunction->savesendfunction($entityId,$ModuleName,$id);
				}else if($ModuleName=="obrshiftsummary"){
					$rainfall=new rainfall;
					$rainfall->rainfallsave($entityId);
					$equipment_hiring=new equipment_hiring;
					$equipment_hiring->equipment_hiringsave($entityId);
					$obr_loss_production_hours=new obr_loss_production_hours;
					$obr_loss_production_hours->obr_loss_production_hourssave($entityId);
					$obremoval_data=new obremoval_data;
					$obremoval_data->obremoval_datasave($entityId);
					$obrshift_data=new obrshift_data;
					$obrshift_data->obrshift_datasave($entityId);
					$overall_obremoval_data=new overall_obremoval_data;
					$overall_obremoval_data->overall_obremoval_datasave($entityId);
					$objDashboard=new dashboard_status;
					$objDashboard->updateDashboardStatus(5);
					$sendfunction=new sendfunction;
					$sendfunction->savesendfunction($entityId,$ModuleName,$id);
				}else if($ModuleName=="openingstock"){
					$openingstock=new openingstock_data;
					$openingstock->openingstocksave($entityId);
					$sendfunction=new sendfunction;
					$sendfunction->savesendfunction($entityId,$ModuleName,$id);
				}else if($ModuleName=="stockadjustment"){
					$openingstock=new stockadjment_data;
					$openingstock->openingstocksave($entityId);
					$sendfunction=new sendfunction;
					$sendfunction->savesendfunction($entityId,$ModuleName,$id);
				}else if($ModuleName=="dailyblasting"){
					$openingstock=new daily_blasting_data;
					$openingstock->openingstocksave($entityId);
					$objDashboard=new dashboard_status;
					$objDashboard->updateDashboardStatus(3);
					$sendfunction=new sendfunction;
					$sendfunction->savesendfunction($entityId,$ModuleName,$id);
				}else if($ModuleName=="treefelling"){
					$openingstock=new treefelling_data;
					$openingstock->savetreefelling_data($entityId);
					$objDashboard=new dashboard_status;
					$objDashboard->updateDashboardStatus(4);
					$sendfunction=new sendfunction;
					$sendfunction->savesendfunction($entityId,$ModuleName,$id);
				}else if($ModuleName=="production"){
					$productiondata=new productiondata;
					$productiondata->productiondatasave($entityId);
					$productiontotaldata=new productiontotaldata;
					$productiontotaldata->productiontotaldatasave($entityId);
					$sendfunction=new sendfunction;
					$sendfunction->savesendfunction($entityId,$ModuleName,$id);
				}else if($ModuleName=="obcesummary"){
					$Modeldata_obremoval=new data_obremoval;
					$Modeldata_obremoval->data_obremovalsave($entityId);
					$coal_extraction_data=new coal_extraction_data;
					$coal_extraction_data->coal_extraction_datasave($entityId);
					$ob_loss_hours_production=new ob_loss_hours_production;
					$ob_loss_hours_production->ob_loss_hours_productionsave($entityId);
					$ob_loss_production_hours=new ob_loss_production_hours;
					$ob_loss_production_hours->ob_loss_production_hourssave($entityId);
					$objDashboard=new dashboard_status;
					$objDashboard->updateDashboardStatus(35);
					$sendfunction=new sendfunction;
					$sendfunction->savesendfunction($entityId,$ModuleName,$id);
				}
				if($autoField=$this->checkAutoNo())
				$this->setAutoNo($tabs);
				$transaction->commit();   
				return true;
			}
			catch(Exception $e)
			{
				$transaction->rollBack();
				return false;
			}

		}
	public function updateModule($RecordId)
		{
			//echo "<br>function updateModule";
			$transaction = $this->dbConnection->beginTransaction(); 
			try
			{
			if($multitoField=$this->checkMultiselect()){
				$multivals=$this->setMultiSelectNo($multitoField);
				$_POST['EditModel'][$multitoField]=$multivals;
			} 
			$uid=Yii::app()->session['id'];
			$assinid=$_POST['Entity']['ownerid'];
			$depot_code=$this->getDepotCode();
			$ModuleName=$this->tableName;
			if($this->checkDepotUser()){
			//$this->user_depot_code=$depot_code;
			$_POST['EditModel']['user_depot_code']=$depot_code;
			//$this->depotname=$depot_code;
			}
			else
			{	
			//$this->user_depot_code=$_POST['EditModel']['depotname'];
			$_POST['EditModel']['user_depot_code']=$_POST['EditModel']['depotname'];
			}
			
			//echo "RecordId=$RecordId and ModuleName=$ModuleName";
			
			if($this->getModuleType()=="Master" or $this->getModuleType()=="Related"){
			$this->modifiedtime=date('Y-m-d H:i:s');
			$this->createdtime=date('Y-m-d H:i:s');	
			//$this->modifiedtime=new CDbExpression('NOW()');	
			}else
			{
			$app_date=$this->getAppDate($ModuleName);
			//echo "<br>app_date=$app_date";
			$this->modifiedtime=date('Y-m-d H:i:s');
			$this->createdtime=date('Y-m-d H:i:s');
			}
			//print_r($this);
			//die;
			$this->creatorid=$uid;
			$this->ownerid=$assinid;
			$this->modifiedby=$uid;
			$output=$this->updateAll($_POST['EditModel'],"$this->fieldId=".$RecordId);
			if($ModuleName=="contractorattendance"){
				$ModelReceiptProduct=new contractorattendancedata;
	        		$ModelReceiptProduct->contractorattendancedataDelete($RecordId);
				$ModelReceiptProduct->contractorattendancedatavalue($RecordId);
				$objDashboard=new dashboard_status;
				$objDashboard->updateDashboardContractorStatus(17);
			}else if($ModuleName=="cess"){
				$cess_data=new cess_data;
	        		$cess_data->cess_dataDelete($RecordId);
				$cess_data->cess_datasave($RecordId);
				$cp_loss=new cp_loss;
	        		$cp_loss->cp_lossDelete($RecordId);
				$cp_loss->cp_losssave($RecordId);
				$cess_contractorwise=new cess_contractorwise;
	        		$cess_contractorwise->cess_contractorwiseDelete($RecordId);
				$cess_contractorwise->cess_contractorwisesave($RecordId);
				$overall_rom_qty=new overall_rom_qty;
	        		$overall_rom_qty->overall_rom_qtyDelete($RecordId);
				$overall_rom_qty->overall_rom_qtysave($RecordId);
			}else if($ModuleName=="dailydrilling"){
				$ModelReceiptProduct=new drilling_data;
	      			$ModelReceiptProduct->drillingdataDelete($RecordId);
				$ModelReceiptProduct->savedailydrillingvalue($RecordId);
			}else if($ModuleName=="logisticmine"){
				$mine_loss_dispatch_hours=new mine_loss_dispatch_hours;
				$mine_loss_dispatch_hours->manage_mine_loss_dispatch_hours($RecordId);
				$mine_loss_dispatch_hours->save_mine_loss_dispatch_hours($RecordId);
				$reject_coal_dispatch=new reject_coal_dispatch;
				$reject_coal_dispatch->manage_reject_coal_dispatch($RecordId);
				$reject_coal_dispatch->save_reject_coal_dispatch($RecordId);
				$road_dispatch_siding=new road_dispatch_siding;
				$road_dispatch_siding->manage_road_dispatch_siding($RecordId);
				$road_dispatch_siding->save_road_dispatch_siding($RecordId);
				$washed_coal_dispatch=new washed_coal_dispatch;
				$washed_coal_dispatch->manage_washed_coal_dispatch($RecordId);
				$washed_coal_dispatch->save_washed_coal_dispatch($RecordId);
			}else if($ModuleName=="logisticsiding"){
				$rakedispatch_siding=new rakedispatch_siding;
				$rakedispatch_siding->manage_rakedispatch_siding($RecordId);
				$rakedispatch_siding->save_rakedispatch_siding($RecordId);
				$unproductive_loss=new unproductive_loss;
				$unproductive_loss->manage_unproductive_loss($RecordId);
				$unproductive_loss->save_unproductive_loss($RecordId);
				$loss_dispatch_hours=new loss_dispatch_hours;
				$loss_dispatch_hours->manage_loss_dispatch_hours($RecordId);
				$loss_dispatch_hours->save_loss_dispatch_hours($RecordId);
				}
			else if($ModuleName=="washeryinput"){
				$ModelLossProductionHours=new loss_production_hours;
	      			$ModelLossProductionHours->manage_loss_production_hours($RecordId);
				$ModelLossProductionHours->save_loss_production_hours($RecordId);
				$modelromintake=new romintake;
	      			$modelromintake->manage_romintake($RecordId);
				$modelromintake->save_romintake($RecordId);
				$modelwashed_coal_production=new washed_coal_production;
	      			$modelwashed_coal_production->manage_washed_coal_production($RecordId);
				$modelwashed_coal_production->save_washed_coal_production($RecordId);
				$modelflocullent_consumption=new flocullent_consumption;
	      			$modelflocullent_consumption->manage_flocullent_consumption($RecordId);
				$modelflocullent_consumption->save_flocullent_consumption($RecordId);
				$modelpower_consumption=new power_consumption;
	      			$modelpower_consumption->manage_power_consumption($RecordId);
				$modelpower_consumption->save_power_consumption($RecordId);
				$modelwashery_equipment=new washery_equipment;
	      			$modelwashery_equipment->manage_washery_equipment($RecordId);
				$modelwashery_equipment->save_washery_equipment($RecordId);
			}else if($ModuleName=="obr_contractor"){
				$Modelobrmachine=new obr_contractor_machine;
				$Modelobrmachine->obr_contractormachineDelete($RecordId);
				$Modelobrmachine->obr_contractormachinesave($RecordId);
				$Modelobrdumper=new obr_contractor_dumper;
				$Modelobrdumper->obr_contractordumperDelete($RecordId);
				$Modelobrdumper->obr_contractordumpersave($RecordId);
				$objDashboard=new dashboard_status;
				$objDashboard->updateDashboardContractorStatus(6);
			}else if($ModuleName=="cecontractor"){
				$Modelcemachine=new ce_machine_details;
				$Modelcemachine->ce_contractormachineDelete($RecordId);
				$Modelcemachine->ce_contractormachinesave($RecordId);
				$Modelcerdumper=new ce_dumper_running_details;
				$Modelcerdumper->ce_contractordumperDelete($RecordId);
				$Modelcerdumper->ce_contractordumpersave($RecordId);
				$objDashboard=new dashboard_status;
				$objDashboard->updateDashboardContractorStatus(13);
			}else if($ModuleName=="obrshiftsummary"){
				$rainfall=new rainfall;
				$rainfall->rainfallDelete($RecordId);
				$rainfall->rainfallsave($RecordId);
				$equipment_hiring=new equipment_hiring;
				$equipment_hiring->equipment_hiringDelete($RecordId);
				$equipment_hiring->equipment_hiringsave($RecordId);
				$obr_loss_production_hours=new obr_loss_production_hours;
				$obr_loss_production_hours->obr_loss_production_hoursDelete($RecordId);
				$obr_loss_production_hours->obr_loss_production_hourssave($RecordId);
				$obremoval_data=new obremoval_data;
				$obremoval_data->obremoval_dataDelete($RecordId);
				$obremoval_data->obremoval_datasave($RecordId);
				$obrshift_data=new obrshift_data;
				$obrshift_data->obrshift_dataDelete($RecordId);
				$obrshift_data->obrshift_datasave($RecordId);
				$overall_obremoval_data=new overall_obremoval_data;
				$overall_obremoval_data->overall_obremoval_dataDelete($RecordId);
				$overall_obremoval_data->overall_obremoval_datasave($RecordId);
			}else if($ModuleName=="openingstock"){
				$openingstock=new openingstock_data;
  				$openingstock->openingstockDelete($RecordId);
				$openingstock->openingstocksave($RecordId);
			}else if($ModuleName=="stockadjustment"){
				$openingstock=new stockadjment_data;
  				$openingstock->openingstockDelete($RecordId);
				$openingstock->openingstocksave($RecordId);
			}else if($ModuleName=="dailyblasting"){
				$openingstock=new daily_blasting_data;
 				$openingstock->openingstockDelete($RecordId);
				$openingstock->openingstocksave($RecordId);
			}else if($ModuleName=="treefelling"){
				$openingstock=new treefelling_data;
  				$openingstock->openingstockDelete($RecordId);
				$openingstock->savetreefelling_data($RecordId);
			}else if($ModuleName=="production"){
				$productiondata=new productiondata;
				$productiondata->productiondataDelete($RecordId);
				$productiondata->productiondatasave($RecordId);
				$productiontotaldata=new productiontotaldata;
				$productiontotaldata->productiontotaldataDelete($RecordId);
				$productiontotaldata->productiontotaldatasave($RecordId);
			}else if($ModuleName=="obcesummary"){
				$Modeldata_obremoval=new data_obremoval;
				$Modeldata_obremoval->data_obremovalDelete($RecordId);
				$Modeldata_obremoval->data_obremovalsave($RecordId);
				$coal_extraction_data=new coal_extraction_data;
				$coal_extraction_data->coal_extraction_dataDelete($RecordId);
				$coal_extraction_data->coal_extraction_datasave($RecordId);
				$ob_loss_hours_production=new ob_loss_hours_production;
				$ob_loss_hours_production->ob_loss_hours_productionDelete($RecordId);
				$ob_loss_hours_production->ob_loss_hours_productionsave($RecordId);
				$ob_loss_production_hours=new ob_loss_production_hours;
				$ob_loss_production_hours->ob_loss_production_hoursDelete($RecordId);
				$ob_loss_production_hours->ob_loss_production_hourssave($RecordId);
			}
			$transaction->commit();   
			return true;
			}
			catch(Exception $e)
			{
				$transaction->rollBack();
				return false;
			}
		}
	public function getValidateRender($arrRender,$Record)
		{
			$ModuleName=$this->tableName;
			$arrRender['Record']=$Record;
			return $arrRender;
		}
	public function validateModule()
		{
			$ModuleName=$this->tableName;
			$module_val=$this->validate();
			return $module_val;
		}
}
