<?php
/**
 * InvoiceProduct class.
 * InvoiceProduct is the data structure for keeping
 * InvoiceProduct form data. It is used by the 'Order' action of 'JPLController'.
 */
class treefelling_data extends CActiveRecord
{
	public $treefelling_id;	
	public $felling_area;
	public $manpower;		
	public $trees_felled;
	public $trees_logged;
	public $trees_debarked;
	public $sent_forest;
	public $felling_constraits;
  public $machine_no;
	public $machine_type;
	public $blades_consumed;
	public $diesel_consumed;
	public $twot_oil_consumed;
	public $twentyw_oil_consumed;
  public $stalking_volume;
	public $treefellingid;
	
	
	
	public function tableName(){
	    return 'treefelling_data';
	}
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			
			array('treefelling_id,felling_area,manpower,trees_felled,trees_logged,trees_debarked,sent_forest,felling_constraits,machine_no,machine_type,blades_consumed,diesel_consumed,twot_oil_consumed,twentyw_oil_consumed,stalking_volume,treefellingid', 'length', 'max'=>25),
			array('productname','safe')
			
		);
	}

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'productid'=>'Product Name',
			'sold_qty'=>'Order Qty',	
		);
	}
	
	/*public function savetreefelling_data($entityId,$InvoiceNo='')
	{		
        echo "====****==".$entityId;
		die;
		
		$treefellingdataa=$_POST['treefelling_data'];
		
		//print_r($_POST);
		
		if(count($treefellingdataa)>0)
		{
		foreach($treefellingdataa as $TreefelData)
			{			
			$TreefelData['treefellingid']=$entityId;			
			$TreefellingData=new treefelling_data;
			$TreefellingData->attributes=$TreefelData;			
			$TreefellingData->save();
			
			}
		}
	}*/

	public function savetreefelling_data($entityId)
	{		        
		
		$treefellingdataa=$_POST['treefelling_data'];		
		if(count($treefellingdataa)>0)
		{
		foreach($treefellingdataa as $TreefelData)
			{			
			$TreefelData['treefellingid']=$entityId;			
			$TreefellingData=new treefelling_data;
			$TreefellingData->attributes=$TreefelData;			
			$TreefellingData->save();
			
			}
		}
	}

	public function dropdownList(){

                      $Column_dropdown = Yii::app()->db->createCommand()
			                ->select('equipment_id,serial_no')
			                ->from('equipment')
			                ->where("equipment_purpose=:equipment_purpose",array(':equipment_purpose'=>2))
			                ->queryAll();
			         return $Column_dropdown;
		}
		public function openingstockDelete($entityId)
		{
                $command = Yii::app()->db->createCommand();
	        $command->delete('treefelling_data', 'treefellingid=:treefellingid', array(':treefellingid'=>$entityId));
	        
			return $DelColumn;
		}	


/*
	public function manageInvoiceProduct($depot_code,$RecordId)
	{
		$InvoiceProductRecords=$this->findAll("invoiceid=$RecordId");
		if(count($InvoiceProductRecords)>0)
		{
		$this->deleteAll("invoiceid=$RecordId");
		}	
	}*/
	/*public function getInvoiceProducts($Record)
		{
			$InvoiceProducts=$_POST['InvoiceProduct'];
			//echo "<pre>Product Request Detail";
			//print_r($_POST);
			//print_r($InvoiceProducts);
			
			if(count($InvoiceProducts)>0)
			{
				foreach($InvoiceProducts as $InvoiceProductKey=>$InvoiceProduct)
				{
					$InvoiceProductObj=new InvoiceProduct;
					$product_id='productid'.$InvoiceProductKey;
					$product_name=$_POST[$product_id];
					//echo "product_id=$product_id and product_name=$product_name";
					$InvoiceProduct['productname']=$product_name;
					//print_r($InvoiceProduct);
					$InvoiceProductObj->attributes=$InvoiceProduct;
					$Record->Multiple_Records[33][]=$InvoiceProductObj;		
				}
			}
			//die;
			return($Record);
		}*/
/*
		public function getdaily_blasting_data($Record)
		{
			$daily_blasting_data=$_POST['daily_blasting_data'];
			echo "<pre>Product Request Detail";
			print_r($_POST);
			print_r($InvoiceProducts);
			die;
			if(count($daily_blasting_data)>0)
			{
				foreach($daily_blasting_data as $daily_blasting_dataKey=>$daily_blasting_data)
				{
					$InvoiceProductObj=new daily_blasting_data;
					$product_id='productid'.$InvoiceProductKey;
					$product_name=$_POST[$product_id];
					//echo "product_id=$product_id and product_name=$product_name";
					$InvoiceProduct['productname']=$product_name;
					//print_r($InvoiceProduct);
					$InvoiceProductObj->attributes=$InvoiceProduct;
					$Record->Multiple_Records[33][]=$InvoiceProductObj;		
				}
			}
			//die;
			return($Record);
		}*/
}
