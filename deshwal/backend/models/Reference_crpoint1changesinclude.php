<?php
namespace app\models;
/**
 * EditModel class.
 * EditModel is the data structure for keeping
 * EditModel form data. It is used by the 'Module' action of 'Controller'.
 */
//session_start(); 
use Yii;
class Reference extends \yii\db\ActiveRecord
{
	public $_members = array();
	public $getTableName;
	public $fieldId;
	public $Multiple_Records = array();
	function __construct($getTableName, $fieldid = '')
	{
		$this->fieldId = $fieldid;
		$this->setgetTableName($getTableName);
		$Columns = $this->getProperty();
		//print_r($Columns);
		//die;	
		foreach ($Columns as $Column)
			$this->_members[$Column['columnname']] = null;

		$this->_members[$fieldid] = null;
		$this->_members['getTableName'] = null;
		$this->_members['fieldId'] = null;
		$this->_members['mode'] = null;
		parent::__construct();
	}
	public function getTableName()
	{
		return $this->getTableName;
	}
	public function setgetTableName($getTableName)
	{
		$this->getTableName = $getTableName;
	}
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		$fieldId = $this->fieldId;
		$validator = $this->getValidation();
		$arr_rules = array();
		foreach ($validator as $validator_key => $validator_name) {
			$validation_rule = $this->getValidationRule("$validator_name");
			$arr_rules[$validator_key][0] = $validation_rule;
			$arr_rules[$validator_key][1] = "$validator_name";
			if ($validator_name == "length")
				$arr_rules[$validator_key]['max'] = 100;

			if ($validator_name == "numerical")
				$arr_rules[$validator_key]['integerOnly'] = true;
		}

		$validator_key += 1;
		$arr_rules[$validator_key][0] = "mode,getTableName,fieldId,$fieldId,Multiple_Records";
		$arr_rules[$validator_key][1] = 'safe';
		return $arr_rules;

	}
	public function getValidation()
	{
		$table_name = $this->getTableName();
		$connection = Yii::$app->db;
		$validator = Yii::$app->db->createCommand()
			->selectDistinct('validator_name')
			->from('field')
			->where('getTableName=:getTableName', array(':getTableName' => $table_name))
			->queryAll();
		$final_validator = array();
		foreach ($validator as $validator_name) {
			if (strpos($validator_name['validator_name'], '~') != false) {
				$arr_vali = explode("~", $validator_name['validator_name']);
				foreach ($arr_vali as $vali)
					$final_validator[] = $vali;
			} else
				$final_validator[] = $validator_name['validator_name'];

		}
		return array_unique($final_validator);
	}
	public function getValidationRule($validator)
	{
		$table_name = $this->getTableName();
		$connection = Yii::$app->db;
		$command = $connection->createCommand("select columnname from field where getTableName='$table_name' and (validator_name like '%$validator%')");
		$arr_Columns = $command->queryAll();
		$Columns = "";
		foreach ($arr_Columns as $column)
			$Columns .= $column['columnname'] . ",";
		$Columns = substr($Columns, 0, -1);
		return $Columns;
	}
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		$Columns = $this->getProperty();
		$arr_lable = array();
		foreach ($Columns as $Column)
			$arr_lable[$Column[columnname]] = $Column[fieldlabel];
		/*echo "<br>Lable=";
												  print_r($arr_lable);
												  die;*/
		return $arr_lable;
	}
	public function getProperty()
	{
		$Columns = (new \yii\db\Query())
			->select(['field.columnname', 'field.fieldlabel'])
			->from('field')
			->where('tablename = :tablename')
			->addParams([':tablename' => $this->getTableName])
			->all(Yii::$app->db);
		return $Columns;
	}

	public function getRelatedNoduleName($fieldid)
	{
		$query = "select modulename from entityname where fieldid=$fieldid";
		$connection = Yii::$app->db;
		$Columns = Yii::$app->db->createCommand("select modulename from entityname where fieldid =:fieldid")
			->bindParam(':fieldid', $fieldid)
			->queryOne();

		$relatedmod = $Columns['modulename'] ?? null;// modified to check if return value exist
		return $relatedmod;
	}
	public function getRelatedNoduleNameBytab($fieldid, $tabid)
	{
		$connection = Yii::$app->db;


		$Columnstab = Yii::$app->db->createCommand("select name from tab where tabid=:tabid")
			->bindParam(':tabid', $tabid)
			->queryOne();
		$modulename = $Columnstab['name'];
		$Columns = Yii::$app->db->createCommand("select modulename from entityname where fieldid =:fieldid and modulename=:modulename")
			->bindParam(':fieldid', $fieldid)
			->bindParam(':modulename', $modulename)
			->queryOne();
		// if($tabid == 18)
		// echo "select modulename from entityname where fieldid =$fieldid and tabid=$tabid";die;
		if ($Columns) {
			$relatedmod = $Columns['modulename'];
			return $relatedmod;
		} else {
			return '';
		}
	}
	public function getRelatedDisplayFieldNameBytab($fieldid, $tabid)
	{
		$Columnstab = Yii::$app->db->createCommand("select name from tab where tabid=:tabid")
			->bindParam(':tabid', $tabid)
			->queryOne();
		$modulename = $Columnstab['name'];

		$Columns = Yii::$app->db->createCommand("select fieldname from entityname where fieldid =:fieldid  and modulename=:modulename")
			->bindParam(':fieldid', $fieldid)
			->bindParam(':modulename', $modulename)
			->queryOne();
		if ($Columns) {
			$relatedDField = $Columns['fieldname'];
			return $relatedDField;
		} else {
			return '';
		}
	}
	public function getRelatedDisplayFieldName($fieldid)
	{

		$Columns = Yii::$app->db->createCommand("select fieldname from entityname where fieldid =:fieldid")
			->bindParam(':fieldid', $fieldid)
			->queryOne();
		$relatedDField = $Columns['fieldname'] ?? null; // modified to check if return value exist
		return $relatedDField;
	}
	public function getRelatedConditionFieldName($fieldid)
	{
		$Columns = Yii::$app->db->createCommand("select condition_field from entityname where fieldid =:fieldid")
			->bindParam(':fieldid', $fieldid)
			->queryOne();
		$relatedDField = null;
		if ($Columns !== false && $Columns['condition_field'] !== null) {
			$relatedDField = $Columns['condition_field'];
		}
		return $relatedDField;


	}

	public function getRelatedFieldId($modulename, $fieldname)
	{
		$Columns = Yii::$app->db->createCommand(
			" select fieldid
		from field 
		where getTableName =:getTableName and fieldname =:fieldname"
		)
			->bindParam(':getTableName', $modulename)
			->bindParam(':fieldname', $fieldname)
			->queryOne();
		$fieldid = $Columns['fieldid'];
		return $fieldid;
	}


	public function getReferenceEntityNameDetail($fieldid, $relatedto = '')
	{
		$module = '';
		if (!empty($relatedto)) {
			//get module name
			$sql = "select `name` as relatedmodule from `tab` where tabid = :relatedto ";
			$Columns = Yii::$app->db->createCommand($sql)
				->bindParam(':relatedto', $relatedto)
				->queryOne();
			// print_r($Columns);die;
			$module = $Columns['relatedmodule'];
		}
		if (!empty($module)) {

			$Columns = Yii::$app->db->createCommand("select targettable,entityidfield,fieldname	from entityname
		where fieldid =:fieldid and modulename= :modulename ")
				->bindParam(':fieldid', $fieldid)
				->bindParam(':modulename', $module)
				->queryOne();

		} else {
			$Columns = Yii::$app->db->createCommand("select targettable,entityidfield,fieldname	from entityname
		where fieldid =:fieldid")
				->bindParam(':fieldid', $fieldid)
				->queryOne();
		}
		// print_r($Columns);die;

		return $Columns;
	}

	public function ReferenceEntityBasedSearchCondition($getTableName, $fieldid)
	{
		//first get the ui type of the field
		if (empty($fieldid))
			return null;
		$FieldData = Yii::$app->db->createCommand("select uitype from field where fieldid =:fieldid")
			->bindParam(':fieldid', $fieldid)
			->queryOne();
		if (empty($FieldData))
			return null;
		$ClickedOnFieldUiType = $FieldData["uitype"] ?? null;
		if ($ClickedOnFieldUiType != 12)
			return null;
		$Columns = Yii::$app->db->createCommand("select * from entityname
		where fieldid =:fieldid")
			->bindParam(':fieldid', $fieldid)
			->queryOne();
		if (empty($Columns))
			return null;
		$condition_field = $Columns["condition_field"] ?? null;
		$condition_value = $Columns["condition_value"] ?? null;
		if (empty($condition_field))
			return null;
		if (empty($condition_value))
			return null;
		return " FIND_IN_SET('$condition_value', $getTableName.$condition_field) > 0  ";
		// return "$condition_field = '$condition_value' ";
	}
	public function getMultiReferenceEntityNameDetail($fieldid, $tblname)
	{
		// echo "select targettable,entityidfield,fieldname	from entityname
// 		where fieldid =$fieldid and targettable=$tblname";die;
		$Columns = Yii::$app->db->createCommand("select targettable,entityidfield,fieldname	from entityname
		where fieldid =:fieldid and targettable=:tblname")
			->bindParam(':fieldid', $fieldid)
			->bindParam(':tblname', $tblname)
			->queryOne();

		return $Columns;
	}

	public function getPickListDetail($fieldid)
	{


		// $Columns = Yii::$app->db->createCommand()
		// 	->select('targettable,targetfield,dispfield')
		// 	->from('picklist')
		// 	->where('fieldid =:fieldid', array(':fieldid' =>$fieldid))
		// 	->queryRow();
		$Columns = Yii::$app->db->createCommand("select targettable,targetfield,dispfield
		from picklist 
		where fieldid =:fieldid")
			->bindParam(':fieldid', $fieldid)
			->queryOne();

		return $Columns;
	}
	public function getPickListValue($fieldid)
	{
		$Columns = $this->getPickListDetail($fieldid);
		$targettable = $Columns['targettable'];
		$targetfield = $Columns['targetfield'];
		$dispfield = $Columns['dispfield'];
		$q_picklist = "select $dispfield,$targetfield from $targettable";
		$connection = Yii::$app->db;
		$command = $connection->createCommand($q_picklist);
		$arr_picklist = $command->queryAll();
		$picklistDetail = array();
		$i = 0;
		foreach ($arr_picklist as $picklist)
			$picklistDetail[$picklist[$targetfield]] = $picklist[$dispfield];
		return $picklistDetail;
	}

	public function getReferenceEntityValue($fieldid, $fieldIdvalue, $fieldid1)
	{
		$table_name = $this->getTableName();
		$RecordId = Yii::$app->request->getParam('Record');
		$Columns = $this->getReferenceEntityNameDetail($fieldid);
		$targettable = $Columns['targettable'];
		$targetfield = $Columns['entityidfield'];
		$dispfield = $Columns['fieldname'];
		$q_picklist = "select $dispfield,$targetfield from $targettable where $targetfield=(select $fieldIdvalue from `$table_name` where $fieldid1=$RecordId )";
		$connection1 = Yii::$app->db;
		$command1 = $connection1->createCommand($q_picklist);
		$arr_picklist1 = $command1->queryAll();
		$picklistDetail1 = array();

		$i = 0;
		foreach ($arr_picklist1 as $picklist1)
			$rFieldValue = $picklist1[$dispfield];
		return $rFieldValue;
		//return $arr_picklist1;
	}
	public function getRefEntityValue($fieldId, $recordId, $relatedto = '')
	{
		//echo "<br>public function getRefEntityValue";
		$Columns = $this->getReferenceEntityNameDetail($fieldId, $relatedto);
		$targettable = $Columns['targettable'];
		$targetfield = $Columns['entityidfield'];
		$dispfield = $Columns['fieldname'];

		$q_ref = "select $dispfield from $targettable where $targetfield='$recordId'";
		//echo "<br>q_ref=$q_ref";
		//if($targettable!="Product")
		//die;
		$connection1 = Yii::$app->db;
		$command1 = $connection1->createCommand($q_ref);
		$arr_row = $command1->queryOne();
		// print_r($arr_row);die;
		if (isset($arr_row[$dispfield])) {
			return $arr_row[$dispfield];
		} else
			return "";
	}
	public function getMultiRefEntityValue($fieldId, $recordId, $referttablename)
	{
		//echo "<br>public function getRefEntityValue";
		$Columns = $this->getMultiReferenceEntityNameDetail($fieldId, $referttablename);
		$targettable = $Columns['targettable'];
		$targetfield = $Columns['entityidfield'];
		$dispfield = $Columns['fieldname'];

		$q_ref = "select $dispfield from $targettable where $targetfield='$recordId'";
		//echo "<br>q_ref=$q_ref";
		//if($targettable!="Product")
		//die;
		$connection1 = Yii::$app->db;
		$command1 = $connection1->createCommand($q_ref);
		$arr_row = $command1->queryOne();
		// print_r($arr_row);die;
		if (isset($arr_row[$dispfield])) {
			return $arr_row[$dispfield];
		} else
			return "";
	}

	//public function getReferenceEntityValueMult($fieldid,$fieldIdvalue,$fieldid1,$pid='')
	/*public function getReferenceEntityValueMult($fieldid,$fieldIdvalue,$fieldid1,$table,$pid)
						 {
							 $table_name=$this->getTableName();			
							 $RecordId=Yii::$app->request->getParam('Record');
							 $Columns=$this->getReferenceEntityNameDetail($fieldid);
							 $targettable=$Columns['targettable'];
							 $targetfield=$Columns['entityidfield'];
							 $dispfield=$Columns['fieldname'];
							 $q_picklist="select $dispfield,$targetfield from $targettable where $targetfield=(select $fieldIdvalue from `$table_name` where $fieldid1=$RecordId )";
							 $connection1=Yii::$app->db;
							 $command1= $connection1->createCommand($q_picklist);
							 $arr_picklist1 = $command1->queryAll();	
							 $picklistDetail1=array();
							 $i=0;
							 foreach($arr_picklist1 as $picklist1)
							 $rFieldValue=$picklist1[$dispfield];
							 //return 	$rFieldValue;	
							 return $pid;
						 }*/

	public function reffieldvaluemul($pid = '')
	{

		return $pid->result();
	}

	public function getTabDetail($ModuleName)
	{
		$connection = Yii::$app->db;
		$arr_tab = Yii::$app->db
			->createCommand("SELECT * FROM tab WHERE  name=:name")
			->bindValue(":name", $ModuleName)
			->queryOne();
		// $arr_tab = Yii::$app->db->createCommand()
		// ->select()
		// ->from('tab')
		// ->where('name =:name', array(':name' =>$ModuleName))
		// ->queryRow();
		return $arr_tab;
	}

	public function getActionList($ModuleName)
	{
		$ActionList = array();
		$actionName = $ModuleName;
		$arr_tab = $this->getTabDetail($ModuleName);
		$ActionList['ActionName'] = $actionName;
		$ActionList['ModuleName'] = $ModuleName;
		$ActionList["ModuleLabel"] = $arr_tab["tablabel"];

		return $ActionList;
	}

	public function getRelatedmoduleActiond($Tab, $related_module)
	{
		$connection = Yii::$app->db;
		$arr_tab = Yii::$app->db
			->createCommand("SELECT actions as moduleactions FROM `module_relation` INNER JOIN tab on tab.tabid=module_relation.related_module where module_relation.source_module=:tabid and related_module=:related_module and module_relation.deleted=0 order by sequence")
			->bindValue(":tabid", $Tab)
			->bindValue(":related_module", $related_module)
			->queryOne();
		// $arr_tab = Yii::$app->db->createCommand()
		// ->select()
		// ->from('tab')
		// ->where('name =:name', array(':name' =>$ModuleName))
		// ->queryRow();
		return $arr_tab;
	}
	/*
					  public function getColumnList_Prod()
							  {
								  $table_name=$this->getTableName();
								  $connection=Yii::$app->db;			

								  $q_column_list="select field.fieldid,field.columnname as fieldname,field.fieldlabel,field.uitype,field.getTableName from field where (field.getTableName='Product' or field.getTableName='OrderProduct') and fieldname IN ('productcode','productname','order_qty')";


								  $command= $connection->createCommand($q_column_list);
								  $ColumnList = $command->queryAll();
								  return 	$ColumnList;				
							  }
					  */

	public function getProductListInv($OrderBy = '', $SortOrder = '')
	{
		$ColumnList = $this->getColumnList_Prod();
		//echo "<br><pre>";
		//print_r($ColumnList);
		list($Column, $ListQuery) = $this->getQueryProductlist($ColumnList, $OrderBy, $SortOrder);
		//echo "<br>ListQuery=$ListQuery";
		//die;
		//print_r($ColumnKey);

		$RecordList = Yii::$app->db->createCommand($ListQuery)
			//->select("$ColumnKey")
			//->from($getTableName)
			->queryAll();
		/*echo "<br>Total Record List=";
												  print"<pre>";
												  print_r($RecordList);
												  die;*/
		//return $RecordList;
		return array($Column, $RecordList);
	}
	/*
						  public function getQueryProductlist($ColumnList,$OrderBy='',$SortOrder='')
						  {
							  $FieldId=$this->fieldId;
							  $getTableName="`".$this->getTableName()."`";
							  //die;
							  $RecordId=$this->_members[$FieldId];
							  $ColumnKey="";	
							  $Orderecid=$_REQUEST['recid'];	

							  $Column=array();
							  foreach($ColumnList as $arrColumn)
								  {
									  $Column[$arrColumn[fieldname]]=$arrColumn[fieldlabel];			

								  }	
							  $ColumnKey=substr($ColumnKey,0,-1);		
							  $ColumnKey="$FieldId as RecordId,".$ColumnKey;
							  $divisonid=$_REQUEST['divisonid'];
							  $depot_code=$_SESSION[Yii::$app->params['dirName'].'_depot_code'];

						  $Query="select Product.productname as productid, Product.productid as productidid, OrderProduct.pending_qty as sold_qty,((OrderProduct.order_qty DIV Scheme.min_qty)*Scheme.free_qty) as freeqty from Product inner join OrderProduct on (`Product`.productid=OrderProduct.productid) LEFT outer join Scheme on(OrderProduct.productid=Scheme.product_id and Scheme.divisionid=$divisonid and Scheme.depotname=$depot_code)where OrderProduct.orderid=$Orderecid and Product.deleted=0 order by Product.productid DESC";


							//die;
							  return array($Column,$Query);
						  }
					  */
	/////////pro list end

	public function getColumnList_pop($ModuleName)
	{
		$table_name = $this->getTableName();
		$connection = Yii::$app->db;

		// $ColumnList = Yii::$app->db->createCommand()
		//                        ->selectDistinct('field.fieldid,field.columnname as fieldname,field.fieldlabel,field.uitype,field.getTableName')
		//                        ->from('customview')
		//                        ->JOIN('cvcolumnlist','customview.cvid=cvcolumnlist.cvid')
		//                        ->JOIN('field','cvcolumnlist.columnname=field.columnname')
		//                        ->where('customview.entitytype = :entitytype and field.getTableName = :getTableName and customview.setdefault = :setdefault', array(':entitytype' =>$table_name,':getTableName' =>$table_name,':setdefault' =>1))
		//                       ->order("columnindex")
		//                        ->queryAll();
		$ColumnList = $rows = Yii::$app->db->createCommand('select DISTINCT field.fieldid,
			 	field.columnname as fieldname, field.fieldlabel, field.uitype, field.tablename ,columnindex
				from customview 
				INNER JOIN cvcolumnlist on customview.cvid = cvcolumnlist.cvid
				INNER JOIN field on cvcolumnlist.columnname = field.columnname 
				where customview.entitytype = "' . $ModuleName . '" and field.tablename =  "' . $table_name . '" and customview.setdefault = 1
				And customview.userid = 1
				order By columnindex')
			->queryAll();

		//code added by ptpatel to resolve issue of no sometime autogenerate no was removed from customview than issu arise in related field
		if(isset($_REQUEST['current_fieldid'])){
		$display = Yii::$app->db->createCommand('select * from entityname 
				where modulename = "' . $ModuleName . '" and targettable =  "' . $table_name . '" and fieldid = '.$_REQUEST['current_fieldid'])
			->queryOne();
		// Extract only 'fieldname' values
		$fieldNames = array_column($ColumnList, 'fieldname');
		//not found in Columnlist add it in columnlist
			if (!in_array($display['shownetityfields'], $fieldNames)) {
				$field_vals = Yii::$app->db->createCommand('select * from field 
					where tablename =  "' . $table_name . '" and columnname ="'.$display['shownetityfields'].'"')
				->queryOne();
				if (!empty($field_vals)) {
				// Field not found — add new one
					$ColumnList[] = [
						'fieldid' => $field_vals['fieldid'],
						'fieldname' => $field_vals['fieldname'],
						'fieldlabel' => $field_vals['fieldlabel'],
						'uitype' => $field_vals['uitype'],
						'tablename' => $table_name,
						'columnindex' => count($ColumnList) + 1
					];
				}
			}
			// echo "<pre>";print_r($ColumnList);die;
		}
		//code end added by ptpatel to resolve issue of no sometime autogenerate no was removed from customview than issu arise in related field
		return $ColumnList;
		/*$ColumnList=array();			
													 foreach($rows as $rows_val)
													 $ColumnList[$rows_val['columnname']]=$rows_val['fieldlabel'];
													 return 	$ColumnList;*/
	}

	public function getListRecord_pop($OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '', $ModuleName, $sourcemodule = '', $sourceid = '',$childSearchConfig = [],$searchparam_child = [])
	{
		$ColumnList = $this->getColumnList_pop($ModuleName);
		//echo "<br><pre>";
		// print_r($ColumnList);die;

		list($Column, $ListQuery, $totalitemcount) = $this->getQuery_pop($ColumnList, $OrderBy, $SortOrder, $rolebasedrecord, $modulepermission, $sourcemodule, $sourceid,$childSearchConfig,
        $searchparam_child);
		//print_r($ColumnKey);

		$RecordList = Yii::$app->db->createCommand($ListQuery)
			//->select("$ColumnKey")
			//->from($getTableName)
			->queryAll();
		$count = count($RecordList);
		/*echo "<br>Total Record List=";
												  print"<pre>";
												  print_r($RecordList);
												  die;*/
		//return $RecordList;
		return array($Column, $RecordList, $totalitemcount, $count);
	}


	public function getListRecord_related($OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '', $ModuleName)
	{
		$ColumnList = $this->getColumnList_pop($ModuleName);
		// $sourcemodule = Yii::$app->request->get('sourcemodule');
		//       $sourceid = Yii::$app->request->get('sourceid');

		//echo "<br><pre>";
		// print_r($ColumnList);die;
		list($Column, $ListQuery, $totalitemcount) = $this->getQuery_pop($ColumnList, $OrderBy, $SortOrder, $rolebasedrecord, $modulepermission);
		//echo "<br>ListQuery=$ListQuery";
		//die;
		//print_r($ColumnKey);

		$RecordList = Yii::$app->db->createCommand($ListQuery)
			//->select("$ColumnKey")
			//->from($getTableName)
			->queryAll();
		$count = count($RecordList);
		/*echo "<br>Total Record List=";
												  print"<pre>";
												  print_r($RecordList);
												  die;*/
		//return $RecordList;
		return array($Column, $RecordList, $totalitemcount, $count);
	}



	public function getQuery_pop($ColumnList, $OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '', $sourcemodule = '', $sourceid = '' ,$childSearchConfig = [],$searchparam_child = [])
	{
		$FieldId = $this->fieldId;
		$getTableName = $TableName = "`" . $this->getTableName() . "`";
		$RecordId = $this->_members[$FieldId];
		$ColumnKey = "";
		$roleid = $rolebasedrecord;
		$join = "from $getTableName";
		$Column = array();
		$Query = '';
		$groupby = '';
		$indexr = 1;
		$searchcondition = '';
		foreach ($ColumnList as $arrColumn) {  //echo "<pre>"; print_r($arrColumn); die;
			$indexr++;
			$Column[$arrColumn['fieldname']] = $arrColumn['fieldlabel'];
			if ($arrColumn['uitype'] == 8) {
				/*$PickList=new PickList;   
																							$PickList->fieldid=$Field->fieldid;
																							$BlockDetail->Fields[$FieldKey]->fieldoptions=$PickList->getPickListValue();*/

				$PickListDetail = $this->getPickListDetail($arrColumn['fieldid']);
				if (!empty($PickListDetail)) {
					$targettable = $PickListDetail['targettable'];
					$targetfield = $PickListDetail['targetfield'];
					$dispfield = $PickListDetail['dispfield'];
					if ($arrColumn['fieldname'] == "ownerid" || $PickListDetail['targettable'] == 'user') {

						$ColumnKey .= "concat(user" . $arrColumn['fieldname'] . '.first_name," ",user' . $arrColumn['fieldname'] . ".last_name) as " . $arrColumn['fieldname'] . ",";
						$join .= " left join `user` as user" . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=user" . $arrColumn['fieldname'] . ".id)";
						if (isset($_POST['searchparam']) && !empty($_POST['searchparam'])) {
							foreach ($searchparam as $key) {
								if ($key[0] == $arrColumn['fieldname'])
									$searchcondition .= "concat(user" . $arrColumn['fieldname'] . '.first_name," ",user' . $arrColumn['fieldname'] . ".last_name) LIKE '%" . $key[1] . "%' AND ";
							}
						}
						//echo $searchcondition;die;


					} else if ($PickListDetail['targettable'] == 'tab') {


						$ColumnKey .= 'UPPER(' . $PickListDetail['targettable'] . '.' . $PickListDetail['dispfield'] . ') as ' . $arrColumn['fieldname'] . ",";
						$join .= " left join " . $PickListDetail['targettable'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $PickListDetail['targettable'] . "." . $PickListDetail["targetfield"] . ")";


						if (isset($_POST['searchparam']) && !empty($_POST['searchparam'])) {
							$searchparam = $_POST['searchparam'];

							foreach ($searchparam as $key) {
								if ($key[0] == $arrColumn['fieldname'])
									$searchcondition .= 'UPPER(' . $PickListDetail['targettable'] . '.' . $PickListDetail['dispfield'] . ") LIKE '%" . $key[1] . "%' AND ";
							}
							//echo $searchcondition;die;


						}
					} else {

						$target_table = $PickListDetail['targettable'] . "_" . $indexr;
						$ColumnKey .= $target_table . '.' . $PickListDetail['dispfield'] . ' as ' . $arrColumn['fieldname'] . ",";
						$join .= " left join " . $PickListDetail['targettable'] . " as $target_table  on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $target_table . "." . $PickListDetail["targetfield"] . ")";


						if (isset($_POST['searchparam']) && !empty($_POST['searchparam'])) {
							$searchparam = $_POST['searchparam'];

							foreach ($searchparam as $key) {
								if ($key[0] == $arrColumn['fieldname'])
									$searchcondition .= $target_table . '.' . $PickListDetail['dispfield'] . " LIKE '%" . $key[1] . "%' AND ";
							}
							//echo $searchcondition;die;


						}
					}


				}
			} else if ($arrColumn['uitype'] == 53) {
				/*$PickList=new PickList;   
																							$PickList->fieldid=$Field->fieldid;
																							$BlockDetail->Fields[$FieldKey]->fieldoptions=$PickList->getPickListValue();*/


				$ColumnKey .= "user" . $arrColumn['fieldname'] . '.username as ' . $arrColumn['fieldname'] . ",";
				$join .= " left join `user` as user" . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=user" . $arrColumn['fieldname'] . ".id)";

				if (isset($_POST['searchparam']) && !empty($_POST['searchparam'])) {
					$searchparam = $_POST['searchparam'];

					foreach ($searchparam as $key) {
						if ($key[0] == $arrColumn['fieldname'])
							$searchcondition .= "user" . $arrColumn['fieldname'] . ".username LIKE '%" . $key[1] . "%' AND ";
					}
					//echo $searchcondition;die;


				}


			} else if ($arrColumn['uitype'] == 22) {
				$PickListDetail = $this->getPickListDetail($arrColumn['fieldid']);
				$targettable = $PickListDetail['targettable'];
				$targetfield = $PickListDetail['targetfield'];
				$dispfield = $PickListDetail['dispfield'];
				if ($PickListDetail['targettable'] != 'user') {
					$ColumnKey .= "GROUP_CONCAT(" . $PickListDetail['targettable'] . "." . $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
				} else {
					$ColumnKey .= "GROUP_CONCAT(" . $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
				}
				$join .= " left join " . $PickListDetail['targettable'] . " on FIND_IN_SET(" . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . "," . $TableName . "." . $arrColumn['fieldname'] . ")";



				//added $arrColumn['tablename'] in opportunity while selecting sa,sf,or procurement team added by ptpatel on date 11-02-2026
				$groupby = "Group By " .$arrColumn['tablename'] .". $FieldId";


				if (isset($_POST['searchparam']) && !empty($_POST['searchparam'])) {
					$searchparam = $_POST['searchparam'];

					foreach ($searchparam as $key) {
						if ($key[0] == $arrColumn['fieldname'])
							$searchcondition .= $PickListDetail['targettable'] . "." . $PickListDetail['dispfield'] . " LIKE '%" . $key[1] . "%' AND ";
					}
					//echo $searchcondition;die;


				}
			} else if ($arrColumn['uitype'] == 12 || $arrColumn['uitype'] == 27 || $arrColumn['uitype'] == 28 || $arrColumn['uitype'] == 29) {



				$getEntityNameDetail = $this->getReferenceEntityNameDetail($arrColumn['fieldid']);
				if (!empty($getEntityNameDetail)) {
					$targettable = $getEntityNameDetail['targettable'];
					$targetfield = $getEntityNameDetail['entityidfield'];
					$dispfield = $getEntityNameDetail['fieldname'];

					// $ColumnKey .= $getEntityNameDetail['targettable'] . "." . $dispfield . " as " . $arrColumn['fieldname'] . ",";
					// $join .= " LEFT OUTER JOIN " . $getEntityNameDetail['targettable'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $getEntityNameDetail['targettable'] . "." . $getEntityNameDetail['entityidfield'] . ")";


					$ColumnKey .= $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . "." . $dispfield . " as " . $arrColumn['fieldname'] . ",";

					$join .= " LEFT OUTER JOIN " . $getEntityNameDetail['targettable'] . " as " . $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . "." . $getEntityNameDetail['entityidfield'] . ")";

					$roled = Yii::$app->request->get('roled');

					if ($roled == 1)
						$arrColumn['fieldname'] . "=" . " and role in (select roleid from role where showinaccounts=1)";



					if (isset($_POST['searchparam']) && !empty($_POST['searchparam'])) {
						$searchparam = $_POST['searchparam'];

						foreach ($searchparam as $key) {
							if ($key[0] == $arrColumn['fieldname'])
								// $searchcondition .= $getEntityNameDetail['targettable'] . "." . $dispfield . " LIKE '%" . $key[1] . "%' AND ";
								$searchcondition .= $getEntityNameDetail['targettable'] . $arrColumn['fieldname'] . "." . $dispfield . " LIKE '%" . $key[1] . "%' AND ";
						}
						//echo $searchcondition;die;


					}
				}
			} else if ($arrColumn['uitype'] == 26) {
				$ColumnKey .=
					"CASE ";
				$getEntityNameDetailval = $this->getReferenceEntityNameDetailMultiple($arrColumn['fieldid']);
				foreach ($getEntityNameDetailval as $getEntityNameDetail) {
					$modulename = $getEntityNameDetail['modulename'];
					$targettable = $getEntityNameDetail['targettable'];
					$targetfield = $getEntityNameDetail['entityidfield'];
					$dispfield = $getEntityNameDetail['fieldname'];



					if ($modulename == 'opportunities') {
						$ColumnKey .=
							"
					WHEN $TableName.related_to = (select tabid from tab where tab.name = '$modulename') THEN opportunity.$dispfield
					";
					} else {
						$ColumnKey .=
							"
					WHEN $TableName.related_to = (select tabid from tab where tab.name = '$modulename') THEN $targettable.$dispfield
					";

					}



					// $ColumnKey .= $getEntityNameDetail['targettable'] . "." . $dispfield . " as " . $arrColumn['fieldname'] . ",";


					$join .= " LEFT OUTER JOIN " . $getEntityNameDetail['targettable'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $getEntityNameDetail['targettable'] . "." . $getEntityNameDetail['entityidfield'] . ")";
				}
				$ColumnKey .= "ELSE NULL
						END AS " . $arrColumn['fieldname'] . ",";

				if (isset($_POST['searchparam']) && !empty($_POST['searchparam'])) {
					$searchparam = $_POST['searchparam'];

					foreach ($searchparam as $key) {
						if ($key[0] == $arrColumn['fieldname'])
							$searchcondition .= $getEntityNameDetail['targettable'] . "." . $dispfield . " LIKE '%" . $key[1] . "%' AND ";
					}
					//echo $searchcondition;die;


				}
				// echo $ColumnKey;die;
			} else if ($arrColumn['uitype'] == 25) {

				// $ColumnKey .= 'mrelated_to.mrelatedto_value ' . " as " . $arrColumn['fieldname'] . ",";
				// $join .= " LEFT OUTER JOIN `mrelated_to` "  . " on (" . $TableName . "." . $arrColumn['fieldname'] . "= mrelated_to.mrelatedtoid)";
				$ColumnKey .= 'tab.tablabel ' . " as " . $arrColumn['fieldname'] . ",";
				$join .= " LEFT OUTER JOIN `tab` " . " on (" . $TableName . "." . $arrColumn['fieldname'] . "= tab.tabid)";

				if (isset($_POST['searchparam']) && !empty($_POST['searchparam'])) {
					$searchparam = $_POST['searchparam'];

					foreach ($searchparam as $key) {
						if ($key[0] == $arrColumn['fieldname'])
							$searchcondition .= "tab.tablabel LIKE '%" . $key[1] . "%' AND ";
					}
					//echo $searchcondition;die;


				}

			} else if ($arrColumn['uitype'] == 5) {
				$unique_alias = "attachments" . $arrColumn['fieldname'];
				$ColumnKey .= "$unique_alias.name " . " as " . $arrColumn['fieldname'] . ",";


				$join .= " LEFT OUTER JOIN `attachments` as $unique_alias " . " on (" . $TableName . "." . $arrColumn['fieldname'] . "= $unique_alias.attachmentsid)";

				if (isset($_POST['searchparam']) && !empty($_POST['searchparam'])) {
					$searchparam = $_POST['searchparam'];

					foreach ($searchparam as $key) {
						if ($key[0] == $arrColumn['fieldname'])
							$searchcondition .= "$unique_alias.name " . " LIKE '%" . $key[1] . "%' AND ";
					}
					//echo $searchcondition;die;


				}
			} elseif ($arrColumn['uitype'] == 6) {
				//added on 15 jan 2025 for user reference
				if ($arrColumn['fieldname'] == 'is_admin' && $arrColumn['tablename'] == "user")
					$ColumnKey .= "if(user.is_admin is not null,if(user.is_admin=0,'No','Yes'),'') as is_admin,";
				else
					$ColumnKey .= str_replace("$arrColumn[fieldname]", "if($arrColumn[fieldname]=0,'No','Yes') as $arrColumn[fieldname], ", $arrColumn['fieldname']);


				if (isset($_POST['searchparam']) && !empty($_POST['searchparam'])) {
					$searchparam = $_POST['searchparam'];

					foreach ($searchparam as $key) {
						if ($key[0] == $arrColumn['fieldname'])
							$searchcondition .= "if($arrColumn[fieldname]=0,'No','Yes') LIKE '%" . $key[1] . "%' AND ";
					}
					//echo $searchcondition;die;


				}


			} elseif ($arrColumn['uitype'] == 13) {
				$ColumnKey .= 'DATE_FORMAT(' . $arrColumn['fieldname'] . ',' . "'%d-%m-%Y H:i:s'" . ') as ' . $arrColumn['fieldname'] . ',';

				if (isset($_POST['searchparam']) && !empty($_POST['searchparam'])) {
					$searchparam = $_POST['searchparam'];

					foreach ($searchparam as $key) {
						if ($key[0] == $arrColumn['fieldname'])
							$searchcondition .= $arrColumn[fieldname] . " LIKE '%" . $key[1] . "%' AND ";
					}
					//echo $searchcondition;die;


				}
			} elseif ($arrColumn['uitype'] == 15) {
				$ColumnKey .= 'DATE_FORMAT(' . $arrColumn['fieldname'] . ',' . "'%m-%Y'" . ') as ' . $arrColumn['fieldname'] . ',';
				if (isset($_POST['searchparam']) && !empty($_POST['searchparam'])) {
					$searchparam = $_POST['searchparam'];

					foreach ($searchparam as $key) {
						if ($key[0] == $arrColumn['fieldname'])
							$searchcondition .= $arrColumn[fieldname] . " LIKE '%" . $key[1] . "%' AND ";
					}
					//echo $searchcondition;die;


				}
			} elseif ($arrColumn['uitype'] == 17) {
				$ColumnKey .= 'DATE_FORMAT(' . $arrColumn['fieldname'] . ',' . "'%d-%m-%Y'" . ') as ' . $arrColumn['fieldname'] . ',';
				if (isset($_POST['searchparam']) && !empty($_POST['searchparam'])) {
					$searchparam = $_POST['searchparam'];

					foreach ($searchparam as $key) {
						if ($key[0] == $arrColumn['fieldname'])
							$searchcondition .= $arrColumn[fieldname] . " LIKE '%" . $key[1] . "%' AND ";
					}
					//echo $searchcondition;die;


				}
			} elseif ($arrColumn['uitype'] == 19) {
				$ColumnKey .= 'DATE_FORMAT(' . $arrColumn['fieldname'] . ',' . "'%m-%d-%Y'" . ') as ' . $arrColumn['fieldname'] . ',';
				if (isset($_POST['searchparam']) && !empty($_POST['searchparam'])) {
					$searchparam = $_POST['searchparam'];

					foreach ($searchparam as $key) {
						if ($key[0] == $arrColumn['fieldname'])
							$searchcondition .= $arrColumn[fieldname] . " LIKE '%" . $key[1] . "%' AND ";
					}
					//echo $searchcondition;die;


				}
			} //code added by ptpatel on date 11-01-2026 for refrence number with , seperated value
            else if ($arrColumn['uitype'] == 31) {

                    $getEntityNameDetail = $this->getReferenceEntityNameDetail($arrColumn['fieldid']);
                    if ($getEntityNameDetail) {
                        $targettable = $getEntityNameDetail['targettable'];        // e.g. salesorder_dit
                        $targetfield = $getEntityNameDetail['entityidfield'];      // e.g. salesorder_dit_id
                        $dispfield   = $getEntityNameDetail['fieldname'];          // e.g. salesorder_dit_no
                        $alias       = $targettable . $arrColumn['fieldname'];     // e.g. salesorder_ditreference_number

                        // SELECT COLUMN with GROUP_CONCAT (for SO numbers)
                        $ColumnKey .= "GROUP_CONCAT(DISTINCT {$alias}.{$dispfield} 
                                            ORDER BY {$alias}.{$dispfield} 
                                            SEPARATOR ', ') AS {$arrColumn['fieldname']},";

                        // JOIN CONDITION using FIND_IN_SET for multi-IDs
                        $join .= " LEFT JOIN {$targettable} AS {$alias}
                                    ON FIND_IN_SET({$alias}.{$targetfield}, {$TableName}.{$arrColumn['fieldname']})";
                    }

            } 
            //end code added by ptpatel on date 11-01-2026
			else {
				$ColumnKey .= $arrColumn['tablename'] . "." . $arrColumn['fieldname'] . ",";
				if (isset($_POST['searchparam']) && !empty($_POST['searchparam'])) {
					$searchparam = $_POST['searchparam'];

					foreach ($searchparam as $key) {
						if ($key[0] == $arrColumn['fieldname'])
							$searchcondition .= $arrColumn['tablename'] . "." . $arrColumn['fieldname'] . " LIKE '%" . $key[1] . "%' AND ";
					}
					//echo $searchcondition;die;


				}
			}
			if ($OrderBy == $arrColumn['fieldname'])
				$OrderBy = $arrColumn['tablename'] . "." . $OrderBy;
		}
		$ColumnKey = substr($ColumnKey, 0, -1);
		if ($OrderBy == '') {
			$OrderBy = "$getTableName.$FieldId";
			$SortOrder = "DESC";
		}
		$ColumnKey = "DISTINCT(" . $TableName . ".$FieldId) as RecordId," . $ColumnKey;

		//start of dynamic search based on values for some other dependent field type spacially ui type 12 (27/03/2025)
		if (isset($_REQUEST['dynamic_dependent']) && !empty($_REQUEST['dynamic_dependent'])) {
			$dynamic_dependent = trim($_REQUEST['dynamic_dependent']);
			$searchcondition .= " FIND_IN_SET('$dynamic_dependent', " . $TableName . "." . $FieldId . ") > 0 AND ";
		}
		// end of dynamic search
		//echo "<br>ColumnKey=$ColumnKey";

		$modtable = $this->getTableName();
		// $searchcondition = '';
		// print_r($_REQUEST);
		if (isset($_REQUEST["current_fieldid"]) && (int) $_REQUEST["current_fieldid"]) {
			$ClickedOnFieldId = (int) $_REQUEST["current_fieldid"];
			//fetch its entityname entry
			$entity_based_query_conditon = $this->ReferenceEntityBasedSearchCondition($getTableName, $ClickedOnFieldId);
			if ($entity_based_query_conditon) {
				//below line commented by ptpatel to resolve search issue in popup on date 19-01-2026 and add $searchcondition .=
				// $searchcondition = "$entity_based_query_conditon AND";
				$searchcondition .= "$entity_based_query_conditon AND";
			}
		} else if (isset($_REQUEST["maintabid"]) && (int) $_REQUEST["maintabid"]) {
			$ClickedOnFieldId = (int) $_REQUEST["maintabid"];
			//fetch its entityname entry
			$entity_based_query_conditon = $this->ReferenceEntityBasedSearchCondition($getTableName, $ClickedOnFieldId);
			if ($entity_based_query_conditon) {
				//below line commented by ptpatel to resolve search issue in popup on date 19-01-2026 and add $searchcondition .=
				// $searchcondition = "$entity_based_query_conditon AND ";
				$searchcondition .= "$entity_based_query_conditon AND ";
			}
		}
		if (isset($_REQUEST['textsearch'])) {

			$textsearch = $_REQUEST['textsearch'];
			$textoption = $_REQUEST['textoption'];
			$searchcondition = "$getTableName.$textoption LIKE '%$textsearch%' AND ";
		}
		// print_r($_POST);die;
		// if (isset($_POST['searchparam']) && !empty($_POST['searchparam'])) {
		// 	$searchparam = $_POST['searchparam'];

		// 	foreach ($searchparam as $key) {
		// 		$searchcondition .= "$getTableName." . $key[0] . " LIKE '%" . $key[1] . "%' AND ";
		// 	}
			//echo $searchcondition;die;


		// }
		// check if vendor account show only active account added on 13 jan 2025
		// echo $TableName;die;
		// if ($TableName == "`vendor_account`") {
		// 	$searchcondition .= " $TableName.acc_status = 2 AND ";
		// }
		if (isset($_POST['conditionfield']) && !empty($_POST['conditionfield']) && isset($_POST['dependentval']) && !empty($_POST['dependentval'])) {
			$searchparam = $_POST['conditionfield'];
			$dependentval = $_POST['dependentval'];
			if (isset($_REQUEST["maintabid"]) && (int) $_REQUEST["maintabid"] == 2687) {//fetching inspection location from service to location of service detail

				$searchcondition .= " $TableName.vendorloc_id in (select service_to_location from servicedetail 
				inner join sourcingdeal as sd on (servicedetail.related_to = 51 and servicedetail.related_to_id=sd.sourcingdeal_id )
				inner join servicedetail_details sdd on sdd.servicedetail_id = servicedetail.servicedetail_id
				where service_type = 3 and sd.sourcingdeal_id = $dependentval ) AND ";
			} else {

				$searchcondition .= $TableName . "." . $searchparam . " = '" . $dependentval . "' AND ";
			}
			// echo $searchcondition;die;
		}
		/////////get multi condition from entitytable;
		$maintabid = $_REQUEST['maintabid'];
		$multiconsition_modulename = $_REQUEST['mname'];
		$multiconsition_field = $_REQUEST['field'];//
		if (!empty($maintabid)) {
			//if condition added to resolve acc_status issue in document,call,meeting,task module
			if($multiconsition_field == 'related_to_id' && isset($multiconsition_modulename))
			{
				$Columnsmulti = Yii::$app->db->createCommand("select multi_condition	from entityname
		where fieldid =:fieldid and modulename = :modulename")
				->bindParam(':fieldid', $maintabid)
				->bindParam(':modulename', $multiconsition_modulename)
				->queryOne();
			}
			else
			{
			$Columnsmulti = Yii::$app->db->createCommand("select multi_condition	from entityname
		where fieldid =:fieldid ")
				->bindParam(':fieldid', $maintabid)
				->queryOne();
			}
			if (!empty($Columnsmulti)) {
				$multi_condition = $Columnsmulti['multi_condition'];
				// if (!empty($multi_condition)) {

				// 	// Decode the JSON data into an associative array
				// 	$multiconditions = json_decode($multi_condition);
				// 	//print_r($multiconditions);die;
				// 	foreach ($multiconditions as $key => $value) {
				// 		//check if text has != sign
				// 		$arr = explode("!=", $value);
				// 		// print_r($arr);die;
				// 		if (!empty($arr[0])) {
				// 			// Use FIND_IN_SET for key in the list of values
				// 			$searchcondition .= " FIND_IN_SET('" . $value . "', " . $TableName . "." . $key . ") > 0 AND ";
				// 		} else {
				// 			$value = $arr['1'];
				// 			if (!empty($value))
				// 				$searchcondition .= " FIND_IN_SET('" . $value . "', " . $TableName . "." . $key . ") = 0 AND ";

				// 		}
				// 	}

				// 	// Remove the trailing " AND " from the query string
				// 	$searchcondition .= rtrim($searchcondition, " AND ");
				// 	$searchcondition .= ' AND ';
				// }

				// added on 21 july 2025 by deepika

				// Example usage
				// $conditions = [
				// 	"and" => [
				// 		["status" => "5"],
				// 		["invoice_created" => ["in" => ["3", "4", "5"]]]
				// 	]
				// ];
				if (!empty($multi_condition)) {

					// 	// Decode the JSON data into an associative array
					$multiconditions = json_decode($multi_condition,true);
					//print_r($multiconditions);
					// var_dump($multiconditions);die;
					$searchcondition .= $this->parseConditions($multiconditions,$TableName);
					$searchcondition .= " AND ";
				}
			if (!empty($childSearchConfig) && !empty($searchparam_child)) {
    $parentFk = $this->getEntityFkField($this->getTableName());
    
    $byTable = [];
    foreach ($childSearchConfig as $cfg) {
        if (!isset($cfg['child_table'])) continue;
        $byTable[$cfg['child_table']][] = $cfg;
    }

    foreach ($byTable as $childTable => $cols) {
        $alias = strtolower($childTable) . '_child';
        $childJoinAdded = false;

        foreach ($cols as $cfg) {
            if (!isset($cfg['columnname'])) continue;
            
            $fullKey = $childTable . '.' . $cfg['columnname'];

            // Check if user searched on this child field
            if (!isset($searchparam_child[$fullKey]) || $searchparam_child[$fullKey] === '') {
                continue;
            }

            $val = addslashes($searchparam_child[$fullKey]);

            // Add LEFT JOIN only once per child table
            if (!$childJoinAdded) {
                $join .= " LEFT JOIN `{$childTable}` AS {$alias} ON {$TableName}.`{$parentFk}` = {$alias}.`{$parentFk}` ";
                $childJoinAdded = true;
            }

            // Add WHERE condition with proper spacing
            $searchcondition .= " {$alias}.`{$cfg['columnname']}` LIKE '%{$val}%' AND ";
        }
    }
}
			}
		}

		$relatedcond = '';

		// if(isset($_REQUEST['sourceid']) && isset($_REQUEST['sourcemodule']))
		// {
		// 	$related_to = $_REQUEST['sourcemodule'];
		// 	$related_to_id = $_REQUEST['sourceid'];
		// 	//foreach ($searchparam as $key) {
		// 		$relatedcond .="related_to=".$related_to." AND related_to_id=".$related_to_id." AND";
		// 	//}
		// 	//echo $searchcondition;die;


		// }
		if (isset($_REQUEST['division']) && !empty($_REQUEST['division'])) {
			$division = $_REQUEST['division'];

			$join .= " INNER JOIN Customer2Division ON (Customer2Division.customer_id=`Customer`.customerid and Customer2Division.division_id=$division)";
		}
		//echo "<pre>";	 
		//print_r($Column);
		//die;
		// seems good place for special join // need to move it to some other place in future
		if (isset($_REQUEST["current_fieldid"]) && (int) $_REQUEST["current_fieldid"] == 395) {
			// assuming that service_type=5 for data wiping
			$join .= "inner join servicedetail as sd on (sd.related_to = 51 and sd.related_to_id=`sourcingdeal`.sourcingdeal_id)
					inner join servicedetail_details as sd_details on (sd_details.servicedetail_id = sd.servicedetail_id and sd_details.service_type=5)";
		} else if (isset($_REQUEST["current_fieldid"]) && (int) $_REQUEST["current_fieldid"] == 349) {
			// assuming that service_type=1 for degaussing
			$join .= "inner join servicedetail as sd on (sd.related_to = 51 and sd.related_to_id=`sourcingdeal`.sourcingdeal_id)
					inner join servicedetail_details as sd_details on (sd_details.servicedetail_id = sd.servicedetail_id and sd_details.service_type=1)";
		} else if (isset($_REQUEST["current_fieldid"]) && (int) $_REQUEST["current_fieldid"] == 2772) {//fetching inspection sourcing deal in inspection
			$join .= "inner join servicedetail as sd on (sd.related_to = 51 and sd.related_to_id=`sourcingdeal`.sourcingdeal_id)
					inner join servicedetail_details as sd_details on (sd_details.servicedetail_id = sd.servicedetail_id and sd_details.service_type=3)";
		} else if (isset($_REQUEST["current_fieldid"]) && (int) $_REQUEST["current_fieldid"] == 2848) {
			// assuming that service_type=2 for drilling
			$join .= "inner join servicedetail as sd on (sd.related_to = 51 and sd.related_to_id=`sourcingdeal`.sourcingdeal_id)
					inner join servicedetail_details as sd_details on (sd_details.servicedetail_id = sd.servicedetail_id and sd_details.service_type=2)";
		} else if (isset($_REQUEST["current_fieldid"]) && (int) $_REQUEST["current_fieldid"] == 2963) {
			// assuming that service_type=4 for shredding
			$join .= "inner join servicedetail as sd on (sd.related_to = 51 and sd.related_to_id=`sourcingdeal`.sourcingdeal_id)
					inner join servicedetail_details as sd_details on (sd_details.servicedetail_id = sd.servicedetail_id and sd_details.service_type=4)";
		}
		


		if (!empty($RecordId)) {
			$join .= " inner join user on (user.id=$getTableName.ownerid)";
			$Query = "select $ColumnKey $join where $relatedcond $searchcondition $getTableName.deleted=0 and 
			$FieldId=$RecordId";
			$Query = str_replace(",$getTableName.ownerid", ",concat(first_name,' ',last_name) as ownerid", $Query);
		} else {

			$where = '';

			///added for grn dit condition, exclude po whose grn is created
			if (isset($_REQUEST["current_fieldid"]) && (int) $_REQUEST["current_fieldid"] == 3452) {


				$where .= "  purchase_order_dit.purchaseorder_dit_id not in (select purchase_order_number from grn_dit where status = 2)";
			}
			//added on 15 oct 2025 for removing completed so products by deep
			// if (isset($_REQUEST["current_fieldid"]) && (int) $_REQUEST["current_fieldid"] == 3397) {
			 if (isset($_REQUEST["maintabid"]) && (int) $_REQUEST["maintabid"] == 3397) {


				$where .= "  salesorder_dit.salesorder_dit_id not in (select salesorder_id from po_completed_so_dit)";
			}
			//added on 16 oct 2025 for removing completed quote products by ptpatel
			if (isset($_REQUEST["maintabid"]) && (int) $_REQUEST["maintabid"] == 3233) {//3233 - quote_name 
				$where .= "  quotes_dit.quotes_dit_id not in (select quote_dit_id from so_completed_quote_dit)";
			}
			//end added on 16 oct 2025 for removing completed quote products by ptpatel
			//added for contract module on 23 june 2025 by deepika
			if (
				isset($_REQUEST["current_fieldid"]) && (int) $_REQUEST["current_fieldid"] == 540
				&& isset($sourcemodule) && isset($sourceid)
			) {

				//get account_name from sourcing deal
				$sqlsd = "select billing_type,vendor_account_name from sourcingdeal sd join vendor_account va on sd.vendor_account_name = va.vendoraccid where sourcingdeal_id = :id";
				$ressd = Yii::$app->db->createCommand($sqlsd)->bindValue(":id", $sourceid)->
					queryOne();
				$billingtype = $ressd['billing_type'];
				$account_name = $ressd['vendor_account_name'];
				if ($billingtype == 1)
					$where .= "   (`products`.subcategory in (select product_name from product_price_book join contracts on contracts.contract_id = product_price_book.contractid where contracts.account_name=$account_name and CURDATE() between contract_start_date and contract_end_date group by product_name ))";
			}
			//code added by ptpatel on date 24-06-25
			//service price book
			else if (
				isset($_REQUEST["current_fieldid"]) && (int) $_REQUEST["current_fieldid"] == 1859 //service_type = tbl servicemaster //1859 //service_type = tbl servicedetail_details 
				&& isset($sourcemodule) && isset($sourceid)
			) {
				//get account_name from sourcing deal
				$sqlsd = "select billing_type,vendor_account_name from sourcingdeal sd join vendor_account va on sd.vendor_account_name = va.vendoraccid where sourcingdeal_id = :id";
				$ressd = Yii::$app->db->createCommand($sqlsd)->bindValue(":id", $sourceid)->
					queryOne();
				$billingtype = $ressd['billing_type'];
				$account_name = $ressd['vendor_account_name'];
				if ($billingtype == 1)
					$where .= "   (`servicemaster`.servicemaster_id in (select service_name from service_price_book join contracts on contracts.contract_id = service_price_book.contractid where contracts.account_name=$account_name and CURDATE() between contract_start_date and contract_end_date group by service_name ))";
			}
			//pickup address		
			// Pickup Location (product detail) = pickup address (contract)
			else if (
				isset($_REQUEST["maintabid"]) && (int) $_REQUEST["maintabid"] == 1819 //pickup location  
				&& isset($sourcemodule) && isset($_REQUEST["sourceid"])
			) {
				//get account_name from sourcing deal
				$sqlsd = "select billing_type,vendor_account_name from sourcingdeal sd join vendor_account va on sd.vendor_account_name = va.vendoraccid where sourcingdeal_id = :id";
				$ressd = Yii::$app->db->createCommand($sqlsd)->bindValue(":id", $_REQUEST["sourceid"])->
					queryOne();
				$billingtype = $ressd['billing_type'];
				$account_name = $ressd['vendor_account_name'];
				if ($billingtype == 1)
					$where .= "   (`vendor_locations`.vendorloc_id in (select location_name from pickup_address join contracts on contracts.contract_id = pickup_address.contractid where contracts.account_name=$account_name and CURDATE() between contract_start_date and contract_end_date group by location_name ))";
			}
			// Billing from Location(product detail) = bill from address(contract)
			else if (
				isset($_REQUEST["maintabid"]) && (int) $_REQUEST["maintabid"] == 1820 //billing_from_location
				&& isset($sourcemodule) && isset($_REQUEST["sourceid"])
			) {
				//get account_name from sourcing deal
				$sqlsd = "select billing_type,vendor_account_name from sourcingdeal sd join vendor_account va on sd.vendor_account_name = va.vendoraccid where sourcingdeal_id = :id";
				$ressd = Yii::$app->db->createCommand($sqlsd)->bindValue(":id", $_REQUEST["sourceid"])->
					queryOne();
				$billingtype = $ressd['billing_type'];
				$account_name = $ressd['vendor_account_name'];
				if ($billingtype == 1)
					$where .= "   (`vendor_locations`.vendorloc_id in (select location_name from billing_from_address join contracts on contracts.contract_id = billing_from_address.contractid where contracts.account_name=$account_name and CURDATE() between contract_start_date and contract_end_date group by location_name ))";
			}
			// bill to location(service detail)  = bill to address (contract )
			else if (
				isset($_REQUEST["maintabid"]) && (int) $_REQUEST["maintabid"] == 1863 //billing_to_location
				&& isset($sourcemodule) && isset($_REQUEST["sourceid"])
			) {
				//get account_name from sourcing deal
				$sqlsd = "select billing_type,vendor_account_name from sourcingdeal sd join vendor_account va on sd.vendor_account_name = va.vendoraccid where sourcingdeal_id = :id";
				$ressd = Yii::$app->db->createCommand($sqlsd)->bindValue(":id", $_REQUEST["sourceid"])->
					queryOne();
				$billingtype = $ressd['billing_type'];
				$account_name = $ressd['vendor_account_name'];
				if ($billingtype == 1)
					$where .= "   (`vendor_locations`.vendorloc_id in (select location_name from billing_to_address join contracts on contracts.contract_id = billing_to_address.contractid where contracts.account_name=$account_name and CURDATE() between contract_start_date and contract_end_date group by location_name ))";
			}
			//end code added by ptpatel on date 24-06-25
			//code added for delivery challan on date 11-07-25
			else if (isset($_REQUEST["maintabid"]) && (int) $_REQUEST["maintabid"] == 3703) {  //transpoter name
				//get account_name 
				if (isset($_REQUEST["dctype"]) && $_REQUEST["dctype"] == 1) //billable type of DC  - Account function = "Logistics" and Type is "Vendor"
					$where .= " FIND_IN_SET('2', vendor_function) and FIND_IN_SET('1', account_category)";
				else
					$where .= " FIND_IN_SET('2', vendor_function)";
			}
			//end code addded for delivery challan on date 11-07-25
			//code added for delivery challan to find SO which DC is not created on date 22-07-25
			else if (isset($_REQUEST["maintabid"]) && (int) $_REQUEST["maintabid"] == 3695) {  //so_number
				//get account_name 
				$soNumbers = DeliveryChallandit::find()
							->select('so_number')
							->distinct()
							->column();
				if (!empty($soNumbers)) {
					// Escape and implode for SQL
					$escapedList = implode(",", array_map('intval', $soNumbers)); // if numeric
					$where .= " salesorder_dit_id NOT IN ($escapedList)";
				}
			}
			//end code addded for delivery challan on date 22-07-25
			//code added for pickup on date 05-01-26 to resolve v11-180 issue
			//to get pickup which Qty is not fully used against GRN
			else if (isset($_REQUEST["maintabid"]) && (int) $_REQUEST["maintabid"] == 708) {  //pickup
						$join.= "LEFT JOIN (
									SELECT 
										p.pickup_id
									FROM pickup_asset_detail p
									LEFT JOIN grn g 
										ON g.pickup_id = p.pickup_id
									LEFT JOIN grn_asset_detail gad 
										ON gad.grn_id = g.grn_id
									AND gad.porduct_name = p.porduct_name
									GROUP BY p.pickup_id
									HAVING 
										SUM(DISTINCT p.picked_qty) > COALESCE(SUM(gad.received_qty), 0)
								) qty_mismatch 
								ON qty_mismatch.pickup_id = pickup.pickup_id
							";
							$where .= " qty_mismatch.pickup_id IS NOT NULL ";
			}
			//end code addded for pickup on date 05-01-26 to resolve v11-180 issue
			//echo $where ;die;
			//end on 23 june 2025
	// echo $searchcondition;die;
			//$groupby = '';
			// added on 14 jan 2025 to open reference to all users   
			$isreference = 1;
			$recordlisting = new ListHire();
			$Query = $recordlisting->listing($roleid, $modulepermission, $Query, $ColumnKey, $join, $OrderBy, $SortOrder, $getTableName, $groupby, $isreference, '', $where);
			$Query = str_replace("where", "where $relatedcond  $searchcondition", $Query);

			$pagination = new Pageination();
			$totalitemcount = $pagination->TotalRecords($Query);
			$pageEndRange = $totalitemcount['defaultrecord'];
			if (isset($_REQUEST['pageNumber']) && $_REQUEST['pageNumber'] != '') {
				$pageStartRange = $totalitemcount['pageStartRange'];
			} else if (isset($_REQUEST['pageNumberpre']) && $_REQUEST['pageNumberpre'] != '') {
				$pageStartRange = $totalitemcount['pageStartRange'];
			} else if (isset($_REQUEST['pagejump']) && $_REQUEST['pagejump'] != '') {
				$pageStartRange = $totalitemcount['pageStartRange'];
			} else if (isset($_REQUEST['textsearch']) && $_REQUEST['textsearch'] != '') {
				$pageStartRange = $totalitemcount['pageStartRange'];
			} else {
				$pageStartRange = '0';
			}
			$query_res = $Query;
			// echo $query_res;die;
			$Query = $query_res . " limit $pageStartRange,$pageEndRange";
		}
		// echo $getTableName;
		// echo "<br>Query=$Query";
		// exit;
		// print_r($totalitemcount);
		// die;
		return array($Column, $Query, $totalitemcount);
	}

	// added on 21 july 2025 by deepika
	function parseConditions($conditions, $TableName) {
    if (!is_array($conditions)) {
        return "\n\n🚨 Not an array! Type: " . gettype($conditions);
    }

    // Flat conditions (no "and"/"or")
    if (!isset($conditions['and']) && !isset($conditions['or'])) {
        $parts = [];
        foreach ($conditions as $field => $value) {
            $parts[] = $this->buildCondition($field, $value, $TableName);
        }
        return implode(" AND ", $parts);
    }

    // Nested AND/OR logic
    $parts = [];
    foreach ($conditions as $logic => $subConditions) {
        $subParts = [];
        foreach ($subConditions as $cond) {
            if (!is_array($cond)) continue;

            if (isset($cond['and']) || isset($cond['or'])) {
                $subParts[] = '(' . parseConditions($cond, $TableName) . ')';
            } else {
                foreach ($cond as $field => $value) {
                    $subParts[] =  $this->buildCondition($field, $value, $TableName);
                }
            }
        }

        $glue = strtoupper($logic);
        if (!empty($subParts)) {
            $parts[] = '(' . implode(" $glue ", $subParts) . ')';
        }
    }

    return implode(' AND ', $parts);
}

	function buildCondition($field, $value, $TableName) {
    // Handle IN condition
    if (is_array($value) && isset($value['in'])) {
        $inValues = array_map(function ($v) {
            return "'" . addslashes($v) . "'";
        }, $value['in']);
        return "$TableName.$field IN (" . implode(", ", $inValues) . ")";
    }

    // Handle operator conditions like '!=27'
    if (preg_match('/^(<=|>=|!=|<>|=|<|>)(.*)$/', $value, $matches)) {
        $operator = $matches[1];
        $val = trim($matches[2]);

        // Use FIND_IN_SET with operator
        if ($operator === '!=' || $operator === '<>') {
            return "NOT FIND_IN_SET('" . addslashes($val) . "', $TableName.$field)";
        } elseif ($operator === '=') {
            return "FIND_IN_SET('" . addslashes($val) . "', $TableName.$field)";
        } else {
            return "$TableName.$field $operator '" . addslashes($val) . "'";
        }
    }

    // Default: FIND_IN_SET for string equality
    return "FIND_IN_SET('" . addslashes($value) . "', $TableName.$field)";
}

	public function getChildSearchConfig($parentFieldId)
	{
		$entityRow = Yii::$app->db->createCommand("
			SELECT targettable 
			FROM entityname 
			WHERE fieldid = :fid 
			LIMIT 1
		")->bindValue(':fid', $parentFieldId)->queryOne();
		
		if (empty($entityRow['targettable'])) {
			return [];
		}
		
		$parentModule = $entityRow['targettable'];
		return Yii::$app->db->createCommand("
			SELECT 
				child_table, 
				child_tabid, 
				columnname, 
				fieldlabel, 
				uitype, 
				sequence
			FROM popup_child_search_module_wise
			WHERE parent_module = :module 
			AND active = 1
			ORDER BY sequence
		")->bindValue(':module', $parentModule)->queryAll();
	}
	private function getEntityFkField($tableName)
	{
		$row = Yii::$app->db->createCommand("
			SELECT entityidfield 
			FROM entityname 
			WHERE targettable = :table 
			LIMIT 1
		")->bindValue(':table', $tableName)->queryOne();

		return !empty($row['entityidfield']) ? $row['entityidfield'] : 'id';
	}

	//added on 29 jan 2025 for related side menu
	public function getListRecord_relatedsidemenu($OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '', $ModuleName)
	{
		$ColumnList = $this->getColumnListRelatedsidemenu($ModuleName);
		//echo "<br><pre>";
		// print_r($ColumnList);die;
		if (!empty($ColumnList)) {
			list($Column, $ListQuery, $totalitemcount) = $this->getQuery_relatedsidemenu($ColumnList, $OrderBy, $SortOrder, $rolebasedrecord, $modulepermission);
			//print_r($ColumnKey);

			$RecordList = Yii::$app->db->createCommand($ListQuery)
				//->select("$ColumnKey")
				//->from($getTableName)
				->queryOne();
			$count = count($RecordList);
		} else {
			$RecordList = '';
			$count = '';
			$Column = '';
			$totalitemcount = '';
		}
		/*echo "<br>Total Record List=";
												  print"<pre>";
												  print_r($RecordList);
												  die;*/
		//return $RecordList;
		return array($Column, $RecordList, $totalitemcount, $count);
	}
	public function getColumnListRelatedsidemenu()
	{
		$table_name = $this->getTableName();
		$connection = Yii::$app->db;


		$ColumnList = $rows = Yii::$app->db->createCommand('select DISTINCT field.fieldid,
			 	field.columnname as fieldname, field.fieldlabel, field.uitype, field.tablename 
				from  field 
				where field.tablename =  "' . $table_name . '" and kanbanviewfield = 1')
			->queryAll();
		// print_r($ColumnList);die;

		return $ColumnList;

	}
	public function getQuery_relatedsidemenu($ColumnList, $OrderBy = '', $SortOrder = '', $rolebasedrecord = '', $modulepermission = '')
	{
		$FieldId = $this->fieldId;
		$getTableName = $TableName = "`" . $this->getTableName() . "`";
		$RecordId = $this->_members[$FieldId];
		$ColumnKey = "";
		$roleid = $rolebasedrecord;
		$join = "from $getTableName";
		$Column = array();
		$Query = '';
		$groupby = '';
		$indexr = 1;
		foreach ($ColumnList as $arrColumn) {  //echo "<pre>"; print_r($arrColumn); die;
			$indexr++;
			$Column[$arrColumn['fieldname']] = $arrColumn['fieldlabel'];
			if ($arrColumn['uitype'] == 8) {
				/*$PickList=new PickList;   
																							$PickList->fieldid=$Field->fieldid;
																							$BlockDetail->Fields[$FieldKey]->fieldoptions=$PickList->getPickListValue();*/

				$PickListDetail = $this->getPickListDetail($arrColumn['fieldid']);
				if (!empty($PickListDetail)) {
					$targettable = $PickListDetail['targettable'];
					$targetfield = $PickListDetail['targetfield'];
					$dispfield = $PickListDetail['dispfield'];
					if ($arrColumn['fieldname'] == "ownerid" || $PickListDetail['targettable'] == 'user') {

						$ColumnKey .= "concat(user" . $arrColumn['fieldname'] . '.first_name," ",user' . $arrColumn['fieldname'] . ".last_name) as " . $arrColumn['fieldname'] . ",";
						$join .= " left join `user` as user" . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=user" . $arrColumn['fieldname'] . ".id)";
					} else if ($PickListDetail['targettable'] == 'tab') {


						$ColumnKey .= 'UPPER(' . $PickListDetail['targettable'] . '.' . $PickListDetail['dispfield'] . ') as ' . $arrColumn['fieldname'] . ",";
						$join .= " left join " . $PickListDetail['targettable'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $PickListDetail['targettable'] . "." . $PickListDetail["targetfield"] . ")";
					} else {

						$target_table = $PickListDetail['targettable'] . "_" . $indexr;
						$ColumnKey .= $target_table . '.' . $PickListDetail['dispfield'] . ' as ' . $arrColumn['fieldname'] . ",";
						$join .= " left join " . $PickListDetail['targettable'] . " as $target_table  on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $target_table . "." . $PickListDetail["targetfield"] . ")";
					}
				}
			} else if ($arrColumn['uitype'] == 53) {
				/*$PickList=new PickList;   
																							$PickList->fieldid=$Field->fieldid;
																							$BlockDetail->Fields[$FieldKey]->fieldoptions=$PickList->getPickListValue();*/


				$ColumnKey .= "user" . $arrColumn['fieldname'] . '.username as ' . $arrColumn['fieldname'] . ",";
				$join .= " left join `user` as user" . $arrColumn['fieldname'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=user" . $arrColumn['fieldname'] . ".id)";
			} else if ($arrColumn['uitype'] == 22) {
				$PickListDetail = $this->getPickListDetail($arrColumn['fieldid']);
				$targettable = $PickListDetail['targettable'];
				$targetfield = $PickListDetail['targetfield'];
				$dispfield = $PickListDetail['dispfield'];
				if ($PickListDetail['targettable'] != 'user') {
					$ColumnKey .= "GROUP_CONCAT(" . $PickListDetail['targettable'] . "." . $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
				} else {
					$ColumnKey .= "GROUP_CONCAT(" . $PickListDetail['dispfield'] . " order by " . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . " ) as " . $arrColumn['fieldname'] . ",";
				}
				$join .= " left join " . $PickListDetail['targettable'] . " on FIND_IN_SET(" . $PickListDetail['targettable'] . "." . $PickListDetail['targetfield'] . "," . $TableName . "." . $arrColumn['fieldname'] . ")";



				$groupby = "Group By $FieldId";
			} else if ($arrColumn['uitype'] == 12 || $arrColumn['uitype'] == 27 || $arrColumn['uitype'] == 28 || $arrColumn['uitype'] == 29) {
				$getEntityNameDetail = $this->getReferenceEntityNameDetail($arrColumn['fieldid']);
				if (!empty($getEntityNameDetail)) {
					$targettable = $getEntityNameDetail['targettable'];
					$targetfield = $getEntityNameDetail['entityidfield'];
					$dispfield = $getEntityNameDetail['fieldname'];
					$ColumnKey .= $getEntityNameDetail['targettable'] . "." . $dispfield . " as " . $arrColumn['fieldname'] . ",";


					$join .= " LEFT OUTER JOIN " . $getEntityNameDetail['targettable'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $getEntityNameDetail['targettable'] . "." . $getEntityNameDetail['entityidfield'] . ")";
					$roled = Yii::$app->request->get('roled');
					if ($roled == 1)
						$arrColumn['fieldname'] . "=" . " and role in (select roleid from role where showinaccounts=1)";
				}
			} else if ($arrColumn['uitype'] == 26) {
				$ColumnKey .=
					"CASE ";
				$getEntityNameDetailval = $this->getReferenceEntityNameDetailMultiple($arrColumn['fieldid']);
				foreach ($getEntityNameDetailval as $getEntityNameDetail) {
					$modulename = $getEntityNameDetail['modulename'];
					$targettable = $getEntityNameDetail['targettable'];
					$targetfield = $getEntityNameDetail['entityidfield'];
					$dispfield = $getEntityNameDetail['fieldname'];


					$ColumnKey .=
						"
        WHEN $TableName.related_to = (select tabid from tab where tab.name = '$modulename') THEN $dispfield
        ";



					// $ColumnKey .= $getEntityNameDetail['targettable'] . "." . $dispfield . " as " . $arrColumn['fieldname'] . ",";


					$join .= " LEFT OUTER JOIN " . $getEntityNameDetail['targettable'] . " on (" . $TableName . "." . $arrColumn['fieldname'] . "=" . $getEntityNameDetail['targettable'] . "." . $getEntityNameDetail['entityidfield'] . ")";
				}
				$ColumnKey .= "ELSE NULL
    END AS " . $arrColumn['fieldname'] . ",";
				// echo $ColumnKey;die;
			} else if ($arrColumn['uitype'] == 25) {

				// $ColumnKey .= 'mrelated_to.mrelatedto_value ' . " as " . $arrColumn['fieldname'] . ",";
				// $join .= " LEFT OUTER JOIN `mrelated_to` "  . " on (" . $TableName . "." . $arrColumn['fieldname'] . "= mrelated_to.mrelatedtoid)";
				$ColumnKey .= 'tab.tablabel ' . " as " . $arrColumn['fieldname'] . ",";
				$join .= " LEFT OUTER JOIN `tab` " . " on (" . $TableName . "." . $arrColumn['fieldname'] . "= tab.tabid)";
			} else if ($arrColumn['uitype'] == 5) {
				$unique_alias = "attachments" . $arrColumn['fieldname'];
				$ColumnKey .= "$unique_alias.name " . " as " . $arrColumn['fieldname'] . ",";


				$join .= " LEFT OUTER JOIN `attachments` as $unique_alias " . " on (" . $TableName . "." . $arrColumn['fieldname'] . "= $unique_alias.attachmentsid)";
			} elseif ($arrColumn['uitype'] == 6) {
				//added on 15 jan 2025 for user reference
				if ($arrColumn['fieldname'] == 'is_admin' && $arrColumn['tablename'] == "user")
					$ColumnKey .= "if(user.is_admin is not null,if(user.is_admin=0,'No','Yes'),'') as is_admin,";
				else
					$ColumnKey .= str_replace("$arrColumn[fieldname]", "if($arrColumn[fieldname]=0,'No','Yes') as $arrColumn[fieldname], ", $arrColumn['fieldname']);


			} elseif ($arrColumn['uitype'] == 13) {
				$ColumnKey .= 'DATE_FORMAT(' . $arrColumn['fieldname'] . ',' . "'%d-%m-%Y H:i:s'" . ') as ' . $arrColumn['fieldname'] . ',';
			} elseif ($arrColumn['uitype'] == 15) {
				$ColumnKey .= 'DATE_FORMAT(' . $arrColumn['fieldname'] . ',' . "'%m-%Y'" . ') as ' . $arrColumn['fieldname'] . ',';
			} elseif ($arrColumn['uitype'] == 17) {
				$ColumnKey .= 'DATE_FORMAT(' . $arrColumn['fieldname'] . ',' . "'%d-%m-%Y'" . ') as ' . $arrColumn['fieldname'] . ',';
			} elseif ($arrColumn['uitype'] == 19) {
				$ColumnKey .= 'DATE_FORMAT(' . $arrColumn['fieldname'] . ',' . "'%m-%d-%Y'" . ') as ' . $arrColumn['fieldname'] . ',';
			} else {
				$ColumnKey .= $arrColumn['tablename'] . "." . $arrColumn['fieldname'] . ",";
			}
			if ($OrderBy == $arrColumn['fieldname'])
				$OrderBy = $arrColumn['tablename'] . "." . $OrderBy;
		}
		$ColumnKey = substr($ColumnKey, 0, -1);
		if ($OrderBy == '') {
			$OrderBy = "$getTableName.$FieldId";
			$SortOrder = "DESC";
		}
		$ColumnKey = "DISTINCT(" . $TableName . ".$FieldId) as RecordId," . $ColumnKey;
		//echo "<br>ColumnKey=$ColumnKey";

		$modtable = $this->getTableName();
		$searchcondition = '';
		// print_r($_REQUEST);
		if (isset($_REQUEST["current_fieldid"]) && (int) $_REQUEST["current_fieldid"]) {
			$ClickedOnFieldId = (int) $_REQUEST["current_fieldid"];
			//fetch its entityname entry
			$entity_based_query_conditon = $this->ReferenceEntityBasedSearchCondition($getTableName, $ClickedOnFieldId);
			if ($entity_based_query_conditon) {
				$searchcondition = "$entity_based_query_conditon AND ";
			}
		}
		if (isset($_REQUEST['textsearch'])) {

			$textsearch = $_REQUEST['textsearch'];
			$textoption = $_REQUEST['textoption'];
			$searchcondition = "$getTableName.$textoption LIKE '%$textsearch%' AND ";
		}
		// print_r($_POST);die;
		if (isset($_POST['searchparam']) && !empty($_POST['searchparam'])) {
			$searchparam = $_POST['searchparam'];

			foreach ($searchparam as $key) {
				$searchcondition .= "$getTableName." . $key[0] . " LIKE '%" . $key[1] . "%' AND ";
			}
			//echo $searchcondition;die;


		}
		// check if vendor account show only active account added on 13 jan 2025
		// echo $TableName;die;
		//this condition removed 22-09-2025
		// if ($TableName == "`vendor_account`") {
		// 	$searchcondition .= " $TableName.acc_status = 2 AND ";
		// }

		if (isset($_POST['conditionfield']) && !empty($_POST['conditionfield']) && isset($_POST['dependentval']) && !empty($_POST['dependentval'])) {

			$searchparam = $_POST['conditionfield'];
			$dependentval = $_POST['dependentval'];


			$searchcondition .= $TableName . "." . $searchparam . " = '" . $dependentval . "' AND ";
			// echo $searchcondition;die;
		}

		$relatedcond = '';

		// if(isset($_REQUEST['sourceid']) && isset($_REQUEST['sourcemodule']))
		// {
		// 	$related_to = $_REQUEST['sourcemodule'];
		// 	$related_to_id = $_REQUEST['sourceid'];
		// 	//foreach ($searchparam as $key) {
		// 		$relatedcond .="related_to=".$related_to." AND related_to_id=".$related_to_id." AND";
		// 	//}
		// 	//echo $searchcondition;die;


		// }
		if (isset($_REQUEST['division']) && !empty($_REQUEST['division'])) {
			$division = $_REQUEST['division'];

			$join .= " INNER JOIN Customer2Division ON (Customer2Division.customer_id=`Customer`.customerid and Customer2Division.division_id=$division)";
		}
		//echo "<pre>";	 
		//print_r($Column);
		//die;
		if (!empty($RecordId)) {
			$join .= " inner join user on (user.id=$getTableName.ownerid)";
			$Query = "select $ColumnKey $join where $relatedcond $searchcondition $getTableName.deleted=0 and 
			$FieldId=$RecordId";
			$Query = str_replace(",$getTableName.ownerid", ",concat(first_name,' ',last_name) as ownerid", $Query);
		} else {

			//$groupby = '';
			// added on 14 jan 2025 to open reference to all users   
			$isreference = 1;
			$recordlisting = new ListHire();
			$Query = $recordlisting->listing($roleid, $modulepermission, $Query, $ColumnKey, $join, $OrderBy, $SortOrder, $getTableName, $groupby, $isreference);
			$Query = str_replace("where", "where $relatedcond  $searchcondition", $Query);

			$pagination = new Pageination();
			$totalitemcount = $pagination->TotalRecords($Query);
			$pageEndRange = $totalitemcount['defaultrecord'];
			if (isset($_REQUEST['pageNumber']) && $_REQUEST['pageNumber'] != '') {
				$pageStartRange = $totalitemcount['pageStartRange'];
			} else if (isset($_REQUEST['pageNumberpre']) && $_REQUEST['pageNumberpre'] != '') {
				$pageStartRange = $totalitemcount['pageStartRange'];
			} else if (isset($_REQUEST['pagejump']) && $_REQUEST['pagejump'] != '') {
				$pageStartRange = $totalitemcount['pageStartRange'];
			} else if (isset($_REQUEST['textsearch']) && $_REQUEST['textsearch'] != '') {
				$pageStartRange = $totalitemcount['pageStartRange'];
			} else {
				$pageStartRange = '0';
			}
			$query_res = $Query;
			// echo $query_res;die;
			$Query = $query_res . " limit 1";
		}
		// echo "<br>Query=$Query";
		// print_r($totalitemcount);
		// die;
		return array($Column, $Query, $totalitemcount);
	}
	//end for related side menu





	function getuser($userid)
	{

		$connection = Yii::$app->db;
		// $command=$connection->createCommand("select targettable,targetfield,dispfield  from picklist where     fieldid=:fieldid")->bindParam(':fieldid'=>$fieldid);


		$command = $connection->createCommand("select id,email,concat(first_name,' ',last_name) as showfield from user  where deleted =0")->bindValue("id", $userid);
		$Columns = $command->queryAll();

		return $Columns;

	}

}
