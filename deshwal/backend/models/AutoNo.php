<?php
namespace app\models;
use Yii;
/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
//class ContactForm extends CFormModel
class AutoNo {

	function getautomoduleno($tabs,$table_name){
		if($table_name == "vendor_account")
		$table_name = "vendoraccount";
		// echo "Select prefix,cur_id
		// from modentity_num 
		// where semodule = $table_name and active = 1";die;

		$autoNo = Yii::$app->db->createCommand("Select prefix,cur_id
		from modentity_num 
		where semodule = :semodule and active = :active")
		->bindValue(':semodule',$table_name)
		->bindValue(':active',1)
		->queryAll();
// echo "Select prefix,cur_id
// 		from modentity_num 
// 		where semodule = $table_name and active = 1";die;

	 	$prefix		= $autoNo[0]['prefix'];
		$cur_id		= $autoNo[0]['cur_id'];
		$autoNo 	= sprintf("%06d", $cur_id);
		//current year
		$cyear = date('Y');
		$orderno	= $prefix.'-'.$cyear.'-'.$autoNo;
		return $orderno;
	}


	function setAutomoduleno($tabs,$semodule){
		$curorder= $this->getautomoduleno($tabs,$semodule);
		//old technique
		// echo $cur_id= (int)filter_var($this->getautomoduleno($tabs,$semodule), FILTER_SANITIZE_NUMBER_INT)+1;die;
		//explode "-"
		$explodedvr = explode("-",$curorder);
		if(!empty($explodedvr))
		$cur_id= (int)filter_var( $explodedvr[2])+1;
		else  
			$cur_id= (int)filter_var($this->getautomoduleno($tabs,$semodule), FILTER_SANITIZE_NUMBER_INT)+1;
// echo $cur_id;die;
// echo "UPDATE `modentity_num` SET cur_id = $cur_id where semodule=$semodule";die;
		//if both id will same then need to do +1 this situaton occure when once bulk upload and after create acc from frontend 
		$existing_currId = (int)filter_var($this->getautomoduleno($tabs,$semodule), FILTER_SANITIZE_NUMBER_INT);
		if($existing_currId == $cur_id)
		{
				$cur_id++;
		}
          $upAutoNo = Yii::$app->db->createCommand("UPDATE `modentity_num` SET cur_id = :crmid where semodule=:semodule")
                ->bindValue(":crmid", $cur_id)
		        ->bindValue(":semodule", $semodule)
		        ->execute();


		return $upAutoNo;
	}
// added on 27 jan 2025 for account code
	function getautomodulecode($tabs,$table_name){

		

		$autoNo = Yii::$app->db->createCommand("Select prefix,cur_id
		from modentity_num 
		where semodule = :semodule and active = :active")
		->bindValue(':semodule',$table_name)
		->bindValue(':active',1)
		->queryAll();
// echo "Select prefix,cur_id
// 		from modentity_num 
// 		where semodule = $table_name and active = 1";die;

	 	$prefix		= $autoNo[0]['prefix'];
		$cur_id		= $autoNo[0]['cur_id'];
		$autoNo 	= sprintf("%06d", $cur_id);
		$orderno	= $autoNo;
		return $orderno;
	}
}
