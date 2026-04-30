<?php

namespace backend\modules\inspection\controllers;

use common\controllers\ModuleController;
use Yii;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='single';
    public $ModuleName='inspection';
    public $FieldId='inspection_id';
    public $TableName='inspection';
    public $TabLabel='Inspection';

   
    public $TabId='2';
    /**
     * Renders the index view for the module
     * @return string
     */
//  public function beforeAction($action)
// {
//     $this->layout = '@app/views/layouts/main';  // Set the main layout before each action
//     return parent::beforeAction($action);
// }

    public function actionExample()
    {
        return $this->render('index');
    }
    public function actionGetsourcingdetail(){
        $data = $_POST;
        $sourcingdeal_id = Yii::$app->request->post('sourcingdeal');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                         SELECT  acc_name,vendoraccid,service_to_location,vendor_loc_name  from sourcingdeal sd
                         join sourcingdeal_stage on sourcingdeal_stage.stage_id = sd.stage
                         join vendor_account va on va.vendoraccid = sd.vendor_account_name
                         join servicedetail on servicedetail.related_to = 51 and servicedetail.related_to_id=sd.sourcingdeal_id and servicedetail.deleted = 0
                         join servicedetail_details on servicedetail_details.servicedetail_id = servicedetail.servicedetail_id
                         join vendor_locations on vendor_locations.vendorloc_id = servicedetail_details.service_to_location and vendor_locations.deleted = 0
                         WHERE sourcingdeal_id = :sourcingdeal_id and service_type=3
                    ")->bindValue(":sourcingdeal_id", $sourcingdeal_id);
        $columns = $command->queryOne();
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Info found.',
                'data'=>''
            ]);
        }
    }

    public function actionGetspocdetail(){
        $data = $_POST;
        $contactid = Yii::$app->request->post('contactid');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                         SELECT  mobile,email from contacts
                         WHERE contacts_id = :contactid
                    ")->bindValue(":contactid", $contactid);
        $columns = $command->queryOne();
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Info found.',
                'data'=>''
            ]);
        }
    }
    public function actionGetinspectiondetail()
    {
        $data = $_POST;
        $loationid = Yii::$app->request->post('locationid');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                         SELECT  address,state_value,city_name,vendor_locations.state,city,pincode from vendor_locations
                         left join state on state.state_id = vendor_locations.state
                         left join city on city.cityid = vendor_locations.city
                         WHERE vendorloc_id = :loationid
                    ")->bindValue(":loationid", $loationid);
        $columns = $command->queryOne();
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Info found.',
                'data'=>''
            ]);
        } 
    }
    public function actionGetfedetail()
    {
        $data = $_POST;
        $feid = Yii::$app->request->post('feid');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                         SELECT mobile FROM `user` 
                         WHERE id = :feid
                    ")->bindValue(":feid", $feid);
        $columns = $command->queryOne();
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Info found.',
                'data'=>''
            ]);
        } 
    }

    public function actionGetvendordetail()
    {
        $data = $_POST;
        $vendorid = Yii::$app->request->post('vendorid');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                         SELECT mobile FROM `contacts` 
                         WHERE contacts_id = :vendoraccid
                    ")->bindValue(":vendoraccid", $vendorid);
        $columns = $command->queryOne();
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Info found.',
                'data'=>''
            ]);
        } 
    }

    //code added by ptpatel to get subcategory and uom based on product
    public function actionGetproductdetails()
    {
        $Recordid = Yii::$app->request->post('Recordid');
        $connection = Yii::$app->db;
        
                
                // AND pc.product_category_id = p.category
        $command = $connection->createCommand("
                SELECT 
                        p.*, 
                        ps.* ,
                        pm.*, 
                        m.*,
                        pc.*,
                        pu.*
                    FROM products p
                    LEFT JOIN prod_sub_catagory ps ON p.subcategory = ps.sub_catagory_id
                    LEFT JOIN prod_model pm ON p.model = pm.prod_model_id 
                    LEFT JOIN prod_make m ON p.make = m.prod_make_id
                    LEFT JOIN prod_category pc ON p.category = pc.prod_category_id
                    LEFT JOIN prod_uom pu ON pu.uom_id = p.uom
                    WHERE p.products_id = :Recordid;

            ")->bindValue(":Recordid", $Recordid);

        $columns = $command->queryOne();

        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data'=>''
            ]);
        }
    }
}
