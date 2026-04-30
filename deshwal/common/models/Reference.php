<?php
namespace common\models;
/**
 * EditModel class.
 * EditModel is the data structure for keeping
 * EditModel form data. It is used by the 'Module' action of 'Controller'.
 */
 session_start(); 
 use Yii;
class Reference extends \yii\db\ActiveRecord
{
	public $_members = array();
	public $getTableName;
	public $fieldId;
	public $Multiple_Records=array();
	function __construct($getTableName,$fieldid='')
	{
		$this->fieldId=$fieldid;
		$this->setgetTableName($getTableName);
		$Columns=$this->getProperty();
	//print_r($Columns);
		//die;	
		foreach($Columns as $Column)
		$this->_members[$Column[columnname]] = null;
              
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
		$this->getTableName=$getTableName;
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
		$arr_rules[$validator_key][0]="mode,getTableName,fieldId,$fieldId,Multiple_Records";
		$arr_rules[$validator_key][1]='safe';
		return $arr_rules;
		
	}
	public function getValidation()
	{
		$table_name=$this->getTableName();
		$connection=Yii::$app->db;
             $validator = Yii::$app->db->createCommand()
                        ->selectDistinct('validator_name')
                        ->from('field')
                        ->where('getTableName=:getTableName', array(':getTableName' =>$table_name))
                        ->queryAll();
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
		$table_name=$this->getTableName();
		$connection=Yii::$app->db;
		$command= $connection->createCommand("select columnname from field where getTableName='$table_name' and (validator_name like '%$validator%')");
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
		$arr_lable[$Column[columnname]]=$Column[fieldlabel];
		/*echo "<br>Lable=";
		print_r($arr_lable);
		die;*/
		return 	$arr_lable;
	}
	public function getProperty()
	{
		$Columns = (new \yii\db\Query())
	    ->select(['field.columnname', 'field.fieldlabel'])
	    ->from('field')
	    ->where('tablename = :tablename')
	    ->addParams([':tablename' => $table_name])
	    ->all(Yii::$app->db);
		return 	$Columns;		
	}

	public function getRelatedNoduleName($fieldid)
	{
		$query="select modulename from entityname where fieldid=$fieldid";
		$connection=Yii::$app->db;
		$Columns = Yii::$app->db->createCommand()
		->select('modulename')
		->from('entityname')
		->where('fieldid =:fieldid', array(':fieldid' =>$fieldid))
		->queryRow();

		$relatedmod=$Columns['modulename'];			
		return $relatedmod;
	}
	public function getRelatedDisplayFieldName($fieldid)
	{
	
		$Columns = Yii::$app->db->createCommand()
		->select('fieldname')
		->from('entityname')
		->where('fieldid =:fieldid', array(':fieldid' =>$fieldid))
		->queryRow();
		$relatedDField=$Columns['fieldname'];			
		return $relatedDField;
	}
	public function getRelatedConditionFieldName($fieldid)
	{
	
		$Columns = Yii::$app->db->createCommand()
		->select('condition_field')
		->from('entityname')
		->where('fieldid =:fieldid', array(':fieldid' =>$fieldid))
		->queryRow();
		$relatedDField=$Columns['fieldname'];			
		return $relatedDField;
	}

	public function getRelatedFieldId($modulename,$fieldname)
	{
		$Columns = Yii::$app->db->createCommand()
		->select('fieldid')
		->from('field')
		->where('getTableName =:getTableName and fieldname =:fieldname', array(':getTableName' =>$modulename,':fieldname' =>$fieldname))
		->queryRow();
		$fieldid=$Columns['fieldid'];			
		return $fieldid;
	}


	public function getReferenceEntityNameDetail($fieldid)
	{
	
	$Columns = Yii::$app->db->createCommand()
		->select('targettable,entityidfield,fieldname')
		->from('entityname')
		->where('fieldid =:fieldid', array(':fieldid' =>$fieldid))
		->queryRow();

		return $Columns;
	}

	public function getPickListDetail($fieldid)
	{
	

	$Columns = Yii::$app->db->createCommand()
		->select('targettable,targetfield,dispfield')
		->from('picklist')
		->where('fieldid =:fieldid', array(':fieldid' =>$fieldid))
		->queryRow();

		return $Columns;
	}
	public function getPickListValue($fieldid)
	{
		$Columns=$this->getPickListDetail($fieldid);
		$targettable=$Columns['targettable'];
		$targetfield=$Columns['targetfield'];
		$dispfield=$Columns['dispfield'];
		$q_picklist="select $dispfield,$targetfield from $targettable";
		$connection=Yii::$app->db;
		$command= $connection->createCommand($q_picklist);
		$arr_picklist = $command->queryAll();	
		$picklistDetail=array();
		$i=0;
		foreach($arr_picklist as $picklist)
		$picklistDetail[$picklist[$targetfield]]=$picklist[$dispfield];
		return 	$picklistDetail;
	}

	public function getReferenceEntityValue($fieldid,$fieldIdvalue,$fieldid1)
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
		return 	$rFieldValue;	
		//return $arr_picklist1;
	}
	public function getRefEntityValue($fieldId,$recordId)
	{
		//echo "<br>public function getRefEntityValue";
		$Columns=$this->getReferenceEntityNameDetail($fieldId);
		$targettable=$Columns['targettable'];
		$targetfield=$Columns['entityidfield'];
		$dispfield=$Columns['fieldname'];
		
		$q_ref="select $dispfield from $targettable where $targetfield='$recordId'";
		//echo "<br>q_ref=$q_ref";
		//if($targettable!="Product")
		//die;
		$connection1=Yii::$app->db;
		$command1= $connection1->createCommand($q_ref);
		$arr_row = $command1->queryRow();
		return $arr_row[$dispfield];
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

	public function reffieldvaluemul($pid='')
	{
		
		return $pid->result();
	}


	
	public function getActionList($ModuleName)
	{
		$ActionList=array();
		$actionName=$ModuleName;
		$ActionList['ActionName']=$actionName;
		$ActionList['ModuleName']=$ModuleName;
		return $ActionList;
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

public function getProductListInv($OrderBy='',$SortOrder='')
	{
		$ColumnList=$this->getColumnList_Prod();
		//echo "<br><pre>";
		//print_r($ColumnList);
		list($Column,$ListQuery)=$this->getQueryProductlist($ColumnList,$OrderBy,$SortOrder);
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
		return array($Column,$RecordList);
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

	public function getColumnList_pop()
		{
			$table_name=$this->getTableName();
			$connection=Yii::$app->db;
		
	$ColumnList = Yii::$app->db->createCommand()
                        ->selectDistinct('field.fieldid,field.columnname as fieldname,field.fieldlabel,field.uitype,field.getTableName')
                        ->from('customview')
                        ->JOIN('cvcolumnlist','customview.cvid=cvcolumnlist.cvid')
                        ->JOIN('field','cvcolumnlist.columnname=field.columnname')
                        ->where('customview.entitytype = :entitytype and field.getTableName = :getTableName and customview.setdefault = :setdefault', array(':entitytype' =>$table_name,':getTableName' =>$table_name,':setdefault' =>1))
                       ->order("columnindex")
                        ->queryAll();
			return 	$ColumnList;
			/*$ColumnList=array();			
			foreach($rows as $rows_val)
			$ColumnList[$rows_val['columnname']]=$rows_val['fieldlabel'];
			return 	$ColumnList;*/	
		}

public function getListRecord_pop($OrderBy='',$SortOrder='',$rolebasedrecord='',$modulepermission='')
	{
		$ColumnList=$this->getColumnList_pop();
		//echo "<br><pre>";
		//print_r($ColumnList);
		list($Column,$ListQuery,$totalitemcount)=$this->getQuery_pop($ColumnList,$OrderBy,$SortOrder,$rolebasedrecord,$modulepermission);
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
		return array($Column,$RecordList,$totalitemcount,$count);
	}


	public function getQuery_pop($ColumnList,$OrderBy='',$SortOrder='',$rolebasedrecord='',$modulepermission='')
	{
		$FieldId=$this->fieldId;
		$getTableName="`".$this->getTableName()."`";		
		$RecordId=$this->_members[$FieldId];
		$ColumnKey="";
		$roleid = $rolebasedrecord;
		$join="from $getTableName";
		$Column=array();
		if($getTableName == 'Customer' AND $getTableName == 'PriceBook' AND $getTableName == 'Depot' AND $getTableName == 'Bank' AND $getTableName == 'Product' AND $getTableName == 'Transporter' AND $getTableName == 'Supplier' AND $getTableName == 'MainAccount' AND $getTableName == 'SubAccount')
		{ $active="  $getTableName.is_active='1' AND "; }
		foreach($ColumnList as $arrColumn)
			{
				$Column[$arrColumn[fieldname]]=$arrColumn[fieldlabel];
				if($arrColumn['uitype']==8)
				{
					$PickListDetail=$this->getPickListDetail($arrColumn['fieldid']);
					$targettable=$PickListDetail['targettable'];
					$targetfield=$PickListDetail['targetfield'];
					$dispfield=$PickListDetail['dispfield'];
					$ColumnKey.="$PickListDetail[targettable].$PickListDetail[dispfield] as $arrColumn[fieldname],";
					$join.=" left join $PickListDetail[targettable] on ($getTableName.$arrColumn[fieldname]=$PickListDetail[targettable].$PickListDetail[targetfield])";
				}
				else if($arrColumn['uitype']==22)
				{
					$PickListDetail=$this->getPickListDetail($arrColumn['fieldid']);
					$targettable=$PickListDetail['targettable'];
					$targetfield=$PickListDetail['targetfield'];
					$dispfield=$PickListDetail['dispfield'];
					$ColumnKey.="GROUP_CONCAT($PickListDetail[targettable].$PickListDetail[dispfield] order by $PickListDetail[targettable].$PickListDetail[targetfield] ) as $arrColumn[fieldname],";
					$join.=" left join $PickListDetail[targettable] on FIND_IN_SET($PickListDetail[targettable].$PickListDetail[targetfield],$getTableName.$arrColumn[fieldname])";
					$groupby	= "Group By $FieldId";
				}
				elseif($arrColumn['uitype']==12)
				{
                 		//getEntityNameDetail

					$getEntityNameDetail=$this->getReferenceEntityNameDetail($arrColumn['fieldid']);
					//print_r($getEntityNameDetail);
					//die;
					$targettable=$getEntityNameDetail['targettable'];
					$targetfield=$getEntityNameDetail['entityidfield'];
					$dispfield=$getEntityNameDetail['fieldname'];
					$ColumnKey.="$getEntityNameDetail[targettable].$getEntityNameDetail[fieldname] as $arrColumn[fieldname],";
					if(isset($_REQUEST['MRVCustomer']) && !empty($_REQUEST['MRVCustomer']))
					{
					$join.=" INNER JOIN $getEntityNameDetail[targettable] on ($getTableName.$arrColumn[fieldname]=$getEntityNameDetail[targettable].$getEntityNameDetail[entityidfield] and $getTableName.$arrColumn[fieldname]='$_REQUEST[MRVCustomer]') left join `MCN` on (`MCN`.mrvno=`MRV`.mrv_id)";
					//echo "<br>Join detail=$join";
					}
					elseif(isset($_REQUEST['SalesReturnCustomer']) && !empty($_REQUEST['SalesReturnCustomer']))
					{
					$join.=" INNER JOIN $getEntityNameDetail[targettable] on ($getTableName.$arrColumn[fieldname]=$getEntityNameDetail[targettable].$getEntityNameDetail[entityidfield] and $getTableName.$arrColumn[fieldname]='$_REQUEST[SalesReturnCustomer]' and $getTableName.invoicestatus='2')";
					//echo "<br>Join detail=$join";
					}
					else
					$join.=" LEFT OUTER JOIN $getEntityNameDetail[targettable] on ($getTableName.$arrColumn[fieldname]=$getEntityNameDetail[targettable].$getEntityNameDetail[entityidfield])";
				
				}
				else
				{
					$ColumnKey.=$arrColumn['getTableName'].".".$arrColumn['fieldname'].",";
				}
				if($OrderBy==$arrColumn['fieldname'])
				$OrderBy=$arrColumn['getTableName'].".".$OrderBy;
			}	
		$ColumnKey=substr($ColumnKey,0,-1);
		if($OrderBy=='')
		{
			$OrderBy="$getTableName.$FieldId";
			$SortOrder="DESC";
		}
		$ColumnKey="DISTINCT($FieldId) as RecordId,".$ColumnKey;
		//echo "<br>ColumnKey=$ColumnKey";

		$modtable=$this->getTableName();
		If($modtable == 'Order')
		{        
         	$custname1=Yii::$app->request->getParam('custid');		 
		 $custname1=" Order.invoice_status !='1' AND  Order.order_status !='3' AND Order.customername=".$custname1." and";
 		
		}

		//print_r($_REQUEST);
		if($_REQUEST['textsearch'])
		{
		
		$textsearch=$_REQUEST['textsearch'];
		$textoption=$_REQUEST['textoption'];
	        $searchcondition="$getTableName.$textoption LIKE '%$textsearch%' AND";
		}
		if(isset($_REQUEST['division']) && !empty($_REQUEST['division'])) 
		{ 
			$division = $_REQUEST['division']; 

			$join .=" INNER JOIN Customer2Division ON (Customer2Division.customer_id=`Customer`.customerid and Customer2Division.division_id=$division)"; 
		} 
		//echo "<pre>";	 
		//print_r($Column);
		//die;
		if(!empty($RecordId)){
			$join.=" inner join users on (users.id=$getTableName.ownerid)";
			$Query="select $ColumnKey $join where $custname1 $searchcondition $getTableName.deleted=0 and 
			$FieldId=$RecordId";
			$Query = str_replace(",$getTableName.ownerid",",concat(first_name,' ',last_name) as ownerid",$Query);
		}else{    
			
			$recordlisting	= new ListHire();
			$Query		= $recordlisting->listing($roleid,$modulepermission,$Query,$ColumnKey,$join,$OrderBy,$SortOrder,$getTableName,$groupby);
			$Query 		= str_replace("where","where $active $custname1 $searchcondition",$Query);
			
			$pagination = new Pageination();
			$totalitemcount = $pagination->TotalRecords($Query);
			$pageEndRange = $totalitemcount['defaultrecord'];
			if($_POST['pageNumber'] !='' or $_REQUEST['pageNumber'] !='' ){
			$pageStartRange = $totalitemcount['pageStartRange'];
			}else if($_POST['pageNumberpre'] !='' or $_REQUEST['pageNumberpre'] !=''){
			$pageStartRange = $totalitemcount['pageStartRange'];
			}else if($_POST['pagejump'] !='' or $_REQUEST['pagejump'] !=''){
			$pageStartRange = $totalitemcount['pageStartRange'];
			}else if($_REQUEST['textsearch'] !=''){
			$pageStartRange = $totalitemcount['pageStartRange'];
			}else{
			$pageStartRange = '0';
			}
			$query_res .= $Query;
			$Query = "$query_res limit $pageStartRange,$pageEndRange";
		}
			//echo "<br>Query=$Query";
			return array($Column,$Query,$totalitemcount);
	}
/*
	public function getColumnList_Batchno()
		{
			$table_name=$this->getTableName();
			$connection=Yii::$app->db;			

			// $q_column_list="select field.fieldid,field.columnname as fieldname,field.fieldlabel,field.uitype,field.getTableName from field where (field.getTableName='Stock' AND fieldname IN ('batch_no','productname','qty')) or (field.getTableName='receiptproduct' and fieldname='expiry_date') order by columnname";

			 $q_column_list="select field.fieldid,field.columnname as fieldname,field.fieldlabel,field.uitype,field.getTableName from field where (field.getTableName='Stock' AND fieldname IN ('batch_no','mfg_date','expiry_date','qty')) order by columnname";
			
    		$command= $connection->createCommand($q_column_list);
			$ColumnList = $command->queryAll();
			return 	$ColumnList;		
		}

*/

	public function getListRecord_BatchNo($OrderBy='',$SortOrder='')
	{
		$ColumnList=$this->getColumnList_Batchno();		
		list($Column,$ListQuery)=$this->getQuery_BatchNo($ColumnList,$OrderBy,$SortOrder);		
		
		$RecordList = Yii::$app->db->createCommand($ListQuery)
    		//->select("$ColumnKey")
    		//->from($getTableName)
	 	->queryAll();

		
		return array($Column,$RecordList);
	}
/*
	public function getDepotDetail($depotid)
	{
		$querydepot="select * from Depot where depotid='$depotid' ";
		$connectiondepot=Yii::$app->db;
		$commanddepot= $connectiondepot->createCommand($querydepot);
		$Columnsdepot = $commanddepot->queryRow();
		return $Columnsdepot;
	}
*/

	public function getQuery_BatchNo($ColumnList,$OrderBy='',$SortOrder='')
	{
		$FieldId=$this->fieldId;
		$getTableName="`".$this->getTableName()."`";
		//die;
		$RecordId=$this->_members[$FieldId];
		$ColumnKey="";      
		
	   
				
		//$ColumnKey=implode(",",$Column[]['fieldname']);
		//echo "<br>Column List=<pre>";
		//print_r($ColumnList);
		//die;
		$join="from $getTableName";
		$Column=array();
		foreach($ColumnList as $arrColumn)
			{
				$Column[$arrColumn[fieldname]]=$arrColumn[fieldlabel];
				if($arrColumn['uitype']==8)
				{
					$PickListDetail=$this->getPickListDetail($arrColumn['fieldid']);
					$targettable=$PickListDetail['targettable'];
					$targetfield=$PickListDetail['targetfield'];
					$dispfield=$PickListDetail['dispfield'];
					$ColumnKey.="$PickListDetail[targettable].$PickListDetail[dispfield] as $arrColumn[fieldname],";
					$join.=" left join $PickListDetail[targettable] on ($getTableName.$arrColumn[fieldname]=$PickListDetail[targettable].$PickListDetail[targetfield])";
				}

				elseif($arrColumn['uitype']==12)
				{
                 		//getEntityNameDetail
						$getEntityNameDetail=$this->getReferenceEntityNameDetail($arrColumn['fieldid']);
					//print_r($getEntityNameDetail);
					//die;
					$targettable=$getEntityNameDetail['targettable'];
					$targetfield=$getEntityNameDetail['entityidfield'];
					$dispfield=$getEntityNameDetail['fieldname'];
					$ColumnKey.="$getEntityNameDetail[targettable].$getEntityNameDetail[fieldname] as $arrColumn[fieldname],";
					$join.=" LEFT OUTER JOIN $getEntityNameDetail[targettable] on ($getTableName.$arrColumn[fieldname]=$getEntityNameDetail[targettable].$getEntityNameDetail[entityidfield])";
				
				}
				else
				{
					$ColumnKey.=$arrColumn['getTableName'].".".$arrColumn['fieldname'].",";
				}
				if($OrderBy==$arrColumn['fieldname'])
				$OrderBy=$arrColumn['getTableName'].".".$OrderBy;
			}	
		$ColumnKey=substr($ColumnKey,0,-1);
		if($OrderBy=='')
		{
			$OrderBy="$getTableName.$FieldId";
			$SortOrder="DESC";
		}
		$ColumnKey="$FieldId as RecordId,".$ColumnKey;
		//echo "<br>ColumnKey=$ColumnKey";

		$modtable=$this->getTableName();
		If($modtable == 'Order')
		{        
         $custname1=Yii::$app->request->getParam('custid');
		 //$custname1='66';
		 //$custname1='Order.customername=$custname1 and';
		 $custname1=" Order.order_status !='3' AND  Order.customername=".$custname1." and";
		 
		}
		

		if(!empty($RecordId))
		$Query="select $ColumnKey $join where $custname1 $getTableName.deleted=0 and $FieldId=$RecordId";
		else
		$Query="select $ColumnKey $join where $custname1 $getTableName.deleted=0 order by $OrderBy $SortOrder";
		//echo "<br>Query=$Query";
		
        $prodid_value=$_REQUEST['prodid_value']; 
		$depotcode=$_REQUEST['depotcode'];
		
		$depotdetail=$this->getDepotDetail($depotcode);		
		$invmngrule = $depotdetail['invmngrule'];
		$taxapplyon = $depotdetail['taxapplyon'];
		$divisonid=$_REQUEST['divisonid'];
		$materialtype=$_REQUEST['materialtype'];

		if($invmngrule == '1')
		{
         $limit="DESC limit 0,1";
		}
		elseif($invmngrule == '2')
		{
         $limit="ASC limit 0,1";
		}
		else
		{
		
		}		

 $Query="select distinct(Stock.batch_no),Stock.product_id,Stock.qty,Stock.mfg_date,Stock.expiry_date from Stock
 where Stock.product_id='$prodid_value' AND Stock.depot_code='$depotcode' AND Stock.divisionid='$divisonid' AND Stock.stock_type_id='$materialtype' ORDER BY Stock.stock_id $limit"; 
		//die;
		return array($Column,$Query);


	}

	

	public function getInvoiceProdField()

	{		
		//$blockid=$_REQUEST['blockid'];		
		$blockid=18;
	$Columnslist = Yii::$app->db->createCommand()
                        ->select('fieldname, uitype,getTableName,tabid, fieldlabel')
                        ->from('field')
                        ->where('blockid = :blockid and edit_view = :edit_view', array(':blockid' =>$blockid,':edit_view' =>1))
                       ->order("sequence ASC")
                        ->queryAll();


		$Column=array();
		
		foreach($Columnslist as $arrColumn)
			{
				$Column[$arrColumn[fieldname]]=$arrColumn[uitype];			
				
			}	
			
		return $Column;	
	}
/*
	public function getBatchPtsMrp($batchno,$product_id,$depotcode,$divison)
	{

        $queryd="select * from Depot where depotid='$depotcode'";
		$connectiond=Yii::$app->db;
		$commandd= $connectiond->createCommand($queryd);
		$Columnsd = $commandd->queryRow();
		$taxapplyon=$Columnsd['taxapplyon'];
		//$relatedDFieldd=$Columnsd['fieldname'];	
		if($taxapplyon == "1")
		{ $ptsmrp="pts"; }
		else
		{  $ptsmrp="mrp";	}


       //$query="select $ptsmrp as mrppts from PriceBook where productname='$product_id' AND batch_no='$batchno' AND depotname='$depotcode' AND division='$divison' ";
	   $query="select pts,mrp from PriceBook where productname='$product_id' AND batch_no='$batchno' AND depotname='$depotcode' AND division='$divison' ";
		$connection=Yii::$app->db;
		$command= $connection->createCommand($query);
		$Columns = $command->queryRow();
		//$relatedDField=$Columns['fieldname'];			
		return $Columns;
		//return $query;
	}



public function getUserState($userid)
	{
		$querydusers="select * from users where id='$userid' ";
		$connectiondusers=Yii::$app->db;
		$commanddusers= $connectiondusers->createCommand($querydusers);
		$Columnsdusers = $commanddusers->queryRow();
		return $userstate=$Columnsdusers['address_state'];		
	}

	public function getCustomerState($cust)
	{
		$querydcust="select * from Customer where customerid='$cust' ";
		$connectiondcust=Yii::$app->db;
		$commanddcust= $connectiondcust->createCommand($querydcust);
		$Columnsdcust = $commanddcust->queryRow();
		return $custstate=$Columnsdcust['customerstate'];			
	}

	public function getTaxPer($batchno,$product_id,$depotcode,$divison,$cust)
	{

	    $cstapplied=$_REQUEST['cstapplied'];
		$userid=$_SESSION[Yii::$app->params['dirName'].'_id'];
		 $userstate=$this->getUserState($userid);
		 $custstate=$this->getCustomerState($cust); 		
		  if($cstapplied == 'false')
			{	
        $queryd="select count(*) as count,tax_per from ProductTaxEx where product_id='$product_id' AND division='$divison' AND state='$custstate' AND tax_type!='CST' ";
		    }
			else
		{
		$queryd="select count(*) as count,tax_per from ProductTaxEx where product_id='$product_id' AND division='$divison' AND state='$custstate' AND tax_type='CST' ";			
		}
		$connectiond=Yii::$app->db;
		$commandd= $connectiond->createCommand($queryd);
		$Columnsd = $commandd->queryRow();
		$count=$Columnsd['count'];				
		if($count == "1")
		{ 
			return $tax_per=$Columnsd['tax_per']; }
		else
		{        	
			//if($userstate==$custstate)
			if($cstapplied == 'false')
			{				
		$query="select `tax_per` as tot from Tax where division='$divison' AND state='$custstate' AND tax_type!='CST' GROUP BY tax_type,tax_on order by effective_date DESC  ";
			}
		else
			{            
			$query="select `tax_per` as tot from Tax where division='$divison' AND state='$custstate' AND tax_type='CST' order by effective_date DESC  ";
			}
		$connection=Yii::$app->db;
		$command= $connection->createCommand($query);
		$Columns = $command->queryAll();		
		$sum = 0;
		foreach($Columns as $value)
		{		
			 $value['tot'];
			 $sum+= $value['tot'];		
		}		
		return $sum;		
		}		
	}

*/
/*
public function getTaxPerDetails()
	{	
		$product_id = $_REQUEST['Prod_id'];
		$orderid = $_REQUEST['orderid'];
		$divison  = $_REQUEST['divisonid'];
		$counter1  = $_REQUEST['counter'];
		$recordno=$_REQUEST['record'];
		$pretaxvalue  = $_REQUEST['pretaxvalue'];		
		$cust  = $_REQUEST['custid'];
		$userid=$_SESSION[Yii::$app->params['dirName'].'_id'];
		$cstapplied=$_REQUEST['cstapplied'];

		$userstate=$this->getUserState($userid);
		$custstate=$this->getCustomerState($cust); 	
		if($recordno <> 'record')
		{
		$query="select  tax_type, tax_per, tax_value as taxpervalue11 from InvoiceTax where invoiceno='$recordno' AND productid='$product_id' AND 	orderno='$orderid'";
		}
		else
		{
		if($cstapplied == 'false')
			{		
        $queryd="select * from ProductTaxEx where product_id='$product_id' AND division='$divison' AND state='$custstate' AND tax_type!='CST'";
		    }
		else
		{
          $queryd="select * from ProductTaxEx where product_id='$product_id' AND division='$divison' AND state='$custstate' AND tax_type='CST' ";
		}
		$connectiond=Yii::$app->db;
		$commandd= $connectiond->createCommand($queryd);
		$Columnsd = $commandd->queryRow();
		$count=$Columnsd['count'];		
		if($count == "1")
		{ 
			return $tax_per_details=$Columnsd; 
			}
		else
		{  	
		
		if($cstapplied == 'false')
			{
		$query="select tax_type,tax_on, tax_per, round(((tax_per/100)*$pretaxvalue),2) as taxpervalue, '$counter1' as counter from Tax where division='$divison' AND state='$custstate' AND tax_type!='CST' GROUP BY tax_type,tax_on order by effective_date DESC  ";
		    }
		else {
			$query="select tax_type,tax_on, tax_per, round(((tax_per/100)*$pretaxvalue),2) as taxpervalue, '$counter1' as counter from Tax where division='$divison' AND state='$custstate' AND tax_type='CST' order by effective_date DESC  ";		
		}
				
		}
        }
		//echo "===".$query;
		$connection=Yii::$app->db;
		$command= $connection->createCommand($query);
		$Columns = $command->queryAll();
		return $Columns;
	}
*/
/*
	public function getColumnList_CreditNote()
		{
			$table_name=$this->getTableName();
			$connection=Yii::$app->db;		
			 
			 $q_column_list="select field.fieldid,field.columnname as fieldname,field.fieldlabel,field.uitype,field.getTableName from field where (field.getTableName='salesreturn' AND fieldname IN ('customername','credit_no','credit_date','balance','flag')) order by columnname";

			//die;
    		$command= $connection->createCommand($q_column_list);
			$ColumnList = $command->queryAll();
			return 	$ColumnList;		
		}
*/
	public function getCreditAmount($OrderBy='',$SortOrder='')
	{
		$ColumnList=$this->getColumnList_CreditNote();		
		list($Column,$ListQuery)=$this->getQuery_CreditNote($ColumnList,$OrderBy,$SortOrder);		
		
		$RecordList = Yii::$app->db->createCommand($ListQuery)
    		//->select("$ColumnKey")
    		//->from($getTableName)
	 	->queryAll();
		
		return array($Column,$RecordList);
	}

	public function getCstEnabled()
	{			
		$cust=$_REQUEST['customerid'];		

		$userid=$_SESSION[Yii::$app->params['dirName'].'_id'];

		$userstate=$this->getUserState($userid);
		$custstate=$this->getCustomerState($cust); 
		
		if($custstate == $userstate)
		{   $value=0;
		}
		else
		{  $value=1;
		}
		return $value;
	}
/*

public function getTaxDetailsHiddenField()
	{	
		
		$product_id = $_REQUEST['Prod_id'];;
		$divison  = $_REQUEST['divisonid'];
		$counter1  = $_REQUEST['counter'];
		$pretaxvalue  = $_REQUEST['pretaxvalue'];		
		$cust  = $_REQUEST['custid'];
		$userid=$_SESSION[Yii::$app->params['dirName'].'_id'];
		$cstapplied=$_REQUEST['cstapplied'];

		$userstate=$this->getUserState($userid);
		$custstate=$this->getCustomerState($cust); 		
		if($cstapplied == 'false')
			{		
        $queryd="select * from ProductTaxEx where product_id='$product_id' AND division='$divison' AND state='$custstate' AND tax_type!='CST'";
		    }
		else
		{
          $queryd="select * from ProductTaxEx where product_id='$product_id' AND division='$divison' AND state='$custstate' AND tax_type='CST' ";
		}
		$connectiond=Yii::$app->db;
		$commandd= $connectiond->createCommand($queryd);
		$Columnsd = $commandd->queryRow();
		$count=$Columnsd['count'];		
		if($count == "1")
		{ 
			return $tax_per_details=$Columnsd; 
			}
		else
		{  	
		
		if($cstapplied == 'false')
			{
		$query="select tax_type, tax_per,tax_on, round(((tax_per/100)*$pretaxvalue),2) as taxpervalue, '$counter1' as counter from Tax where division='$divison' AND state='$custstate' AND tax_type!='CST' GROUP BY tax_type,tax_on order by effective_date DESC  ";
		    }
		else {
			$query="select tax_type, tax_on,tax_per, round(((tax_per/100)*$pretaxvalue),2) as taxpervalue, '$counter1' as counter from Tax where division='$divison' AND state='$custstate' AND tax_type='CST' order by effective_date DESC  ";		
		}
		$connection=Yii::$app->db;
		$command= $connection->createCommand($query);
		$Columns = $command->queryAll();
		return $Columns;		
		}		
	}
*/
	
/*

	public function getQuery_CreditNote($ColumnList,$OrderBy='',$SortOrder='')
	{
		$FieldId=$this->fieldId;
		$getTableName="`".$this->getTableName()."`";
		//die;
		$RecordId=$this->_members[$FieldId];
		$ColumnKey="";   	   
		$join="from Entity inner join $getTableName on(Entity.entityid=$getTableName.$FieldId)";
		$Column=array();
		foreach($ColumnList as $arrColumn)
			{
				$Column[$arrColumn[fieldname]]=$arrColumn[fieldlabel];
				
				if($OrderBy==$arrColumn['fieldname'])
				$OrderBy=$arrColumn['getTableName'].".".$OrderBy;
			}	
		$ColumnKey=substr($ColumnKey,0,-1);		
		$ColumnKey="$FieldId as RecordId,".$ColumnKey;
		//echo "<br>ColumnKey=$ColumnKey";
		$modtable=$this->getTableName();		
		//echo "<PRE>";
		//print_r($_REQUEST);
	     //die;
		 $creditrecordid=$_REQUEST['creditrecordid'];
		 $custide=$_REQUEST['custide'];

		 if($_REQUEST['creditrecordid'])
		{		
			$upCredit	= Yii::$app->db->createCommand()
						->update('SalesReturn',array('flag'=>'1'),"salesreturn_id='".$creditrecordid."'");
		}	
		if($_REQUEST['creditDelete'])
		{		
			echo "deleted";
			$upCredit1	= Yii::$app->db->createCommand()
						->update('SalesReturn',array('flag'=>'0'),"customername='".$custide."'");
		}			
		 $Query="select * from SalesReturn where customername = '$custide'"; 		
		//die;
		return array($Column,$Query);
	}
*/
/*
	public function getSaveTaxHiddenF()
	{
		$count	= $_REQUEST['total'];	
		$connection	= Yii::$app->db; 		
		for($i=0;$i<$count;$i++) {
			for($coun=0;$coun<$count;$coun++)
			{
		$productid = $_REQUEST['InvoiceProduct'][$i]['productid'];		
		$tax_type = $_REQUEST['InvoiceTax'][$i][$coun]['tax_type'];		
		$tax_per = $_REQUEST['InvoiceTax'][$i][$coun]['tax_per'];
		$tax_on = $_REQUEST['InvoiceTax'][$i][$coun]['tax_on'];
		$tax_value = $_REQUEST['InvoiceTax'][$i][$coun]['tax_value'];
		$orderno1= $_REQUEST['EditModel']['orderno'];		
       
	   if($productid != '' and $tax_type != '')
				{
		$query="INSERT INTO `InvoiceTax`(`productid`, `price`, `tax_type`, `tax_on`, `tax_per`, `tax_value`,`orderno`) VALUES ('".$productid."','','".$tax_type."','$tax_on','$tax_per','$tax_value','$orderno1')";		
		$command	= $connection->createCommand($query);		
		$command->execute();
				}
			}	
	}	
	}
*/
/*
	public function getEditTaxHiddenField($productid,$counter)
	{		
		$invoiceid=$_REQUEST['Record'];
		$query="select * from InvoiceTax where 	invoiceno='$invoiceid' and 	productid='$productid' limit 0,1  ";
		$connection=Yii::$app->db;
		$command= $connection->createCommand($query);
		$Columns = $command->queryAll();
		$count=count($Columns);
		$count=1;
		foreach($Columns as $Columns1)
			{				
			$taxtype=$Columns1['tax_type'];
			$tax_per=$Columns1['tax_per'];
			$tax_on=$Columns1['tax_on'];
			$taxpervalue=$Columns1['taxpervalue'];			
			$aa.="<input type='hidden' name='InvoiceTax[$counter][$count][tax_type]' value='$taxtype' >";			
			$aa.="<input type='hidden' name='InvoiceTax[$counter][$count][tax_per]' value='$tax_per' >";
			$aa.="<input type='hidden' name='InvoiceTax[$counter][$count][tax_on]' value='$tax_on' >";
			$aa.="<input type='hidden' name='InvoiceTax[$counter][$count][tax_value]' value='$taxpervalue' >";
			$count++;
			}			
			$aa.="<input type='hidden' name='total' value='$count' >";
			$aa.="<input type='hidden' name='totaltaxvalue$counter' value='' >";
			return $aa;	
	}
*/

/*
	public function getSchemeQty($productid,$depo,$divison)
	{
	$orderqty=$_REQUEST['sold_qtyy1'];	
	$query="SELECT * FROM `Scheme` WHERE `product_id` = '$productid' AND `divisionid`='$divison' AND `depotname` ='$depo'";
	$connection=Yii::$app->db;
	$command= $connection->createCommand($query);		
	$Columns = $command->queryRow();	
	$min_qty=$Columns['min_qty'];
	$free_qty=$Columns['free_qty'];
	if($free_qty != '')
	{//$schemeqty=intval(($free_qty/$min_qty)*$orderqty);
	$schemeqty=intval(($orderqty / $min_qty)*$free_qty);	
	return $schemeqty;
		}		
	}

*/


}
