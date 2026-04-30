<?php

namespace backend\modules\opportunities\controllers;

use common\controllers\ModuleController;
use Yii;

/**
 * Default controller for the `vendoraccount` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='single';
    public $ModuleName='opportunities';
    public $FieldId='opportunity_id';
    public $TableName='opportunity';
    public $TabLabel='Opportunities';
   
    public $TabId='8';
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
    
    public function actionGetcontacts()
    {   
        $data = $_POST;
        $contact_name = Yii::$app->request->post('contact_name');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT contacts_id,first_name,mobile,email FROM contacts WHERE contacts_id = :contacts_id
                    ")->bindValue(":contacts_id", $contact_name);
        $columns = $command->queryOne();
        // echo "<pre>";print_r($columns);die;
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
   
    public function actionGetvendorlocation()
    {   
        $data = $_POST;
        $bill_location = Yii::$app->request->post('bill_location');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT vendor_locations.legal_entity_name,vendor_locations.address,state_value as state,vendor_locations.state_code,vendor_locations.gstin_no_uin,vendor_account.pan_no FROM vendor_locations 
                        join vendor_account on vendor_account.vendoraccid = vendor_locations.vendor_account
                        join state on state.state_id = vendor_locations.state
                         WHERE vendorloc_id = :bill_location
                    ")->bindValue(":bill_location", $bill_location);
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
    public function actionGetwarehouselocation()
    {   
        $data = $_POST;
        $warehouse_location = Yii::$app->request->post('warehouse_location');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT address,state,statecode FROM warehouse
                         WHERE warehouse_id = :warehouse_location
                    ")->bindValue(":warehouse_location", $warehouse_location);
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

    public function actionGetaccountdetail()
    {   
        $data = $_POST;
        $vendor_account_name = Yii::$app->request->post('vendor_account_name');
        $connection = Yii::$app->db;

        
        $command = $connection->createCommand("
                        SELECT 
    zone_region,
    team_name,

    -- Account Manager
    (SELECT userid 
     FROM vendor_account_orgaisation_section 
     WHERE roleid = 'H58' AND vendoraccid = :vendor_account_name LIMIT 1) AS account_manager,

    (SELECT CONCAT(first_name, ' ', if(last_name is null,'',last_name)) 
     FROM user 
     WHERE id = (SELECT userid 
                 FROM vendor_account_orgaisation_section 
                 WHERE roleid = 'H58' AND vendoraccid = :vendor_account_name LIMIT 1)) AS account_manager_name,

    -- Business Manager
    (SELECT userid 
     FROM vendor_account_orgaisation_section 
     WHERE roleid = 'H61' AND vendoraccid = :vendor_account_name LIMIT 1) AS business_manager,

    (SELECT CONCAT(first_name, ' ', if(last_name is null,'',last_name)) 
     FROM user 
     WHERE id = (SELECT userid 
                 FROM vendor_account_orgaisation_section 
                 WHERE roleid = 'H61' AND vendoraccid = :vendor_account_name LIMIT 1)) AS business_manager_name,

    -- Account Director RSM
    (SELECT userid 
     FROM vendor_account_orgaisation_section 
     WHERE roleid = 'H72' AND vendoraccid = :vendor_account_name LIMIT 1) AS account_director_rsm,

    (SELECT CONCAT(first_name, ' ', if(last_name is null,'',last_name)) 
     FROM user 
     WHERE id = (SELECT userid 
                 FROM vendor_account_orgaisation_section 
                 WHERE roleid = 'H72' AND vendoraccid = :vendor_account_name LIMIT 1)) AS account_director_rsm_name,

    -- DevIT ISR
    (SELECT userid 
     FROM vendor_account_orgaisation_section 
     WHERE roleid = 'H59' AND vendoraccid = :vendor_account_name LIMIT 1) AS devit_isr,

    (SELECT CONCAT(first_name, ' ', if(last_name is null,'',last_name)) 
     FROM user 
     WHERE id = (SELECT userid 
                 FROM vendor_account_orgaisation_section 
                 WHERE roleid = 'H59' AND vendoraccid = :vendor_account_name LIMIT 1)) AS devit_isr_name,

    -- DevIT Vertical Manager
    (SELECT userid 
     FROM vendor_account_orgaisation_section 
     WHERE roleid = 'H60' AND vendoraccid = :vendor_account_name LIMIT 1) AS devit_vertical_manager,

    (SELECT CONCAT(first_name, ' ', if(last_name is null,'',last_name)) 
     FROM user 
     WHERE id = (SELECT userid 
                 FROM vendor_account_orgaisation_section 
                 WHERE roleid = 'H60' AND vendoraccid = :vendor_account_name LIMIT 1)) AS devit_vertical_manager_name

FROM vendor_account 
WHERE vendoraccid =  :vendor_account_name
                    ")->bindValue(":vendor_account_name", $vendor_account_name);
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

    public function actionGetshipinfo()
    {
         $data = $_POST;
        $locationid = Yii::$app->request->post('locationid');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT vendor_account.legal_entity as legal_entity_name,vendor_locations.address,state_value as state,vendor_locations.state_code,vendor_locations.gstin_no_uin,vendor_account.pan_no FROM vendor_locations 
                        join vendor_account on vendor_account.vendoraccid = vendor_locations.vendor_account 
                        join state on state.state_id = vendor_locations.state WHERE vendorloc_id = :locationid
                    ")->bindValue(":locationid", $locationid);
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

    public function actionGetproductinfo()
    {   
        $data = $_POST;
        $productid = Yii::$app->request->post('productid');
        $connection = Yii::$app->db;
        // echo "
        //                 SELECT product_category_value as category,sub_catagory_value as subcategory,uom_value FROM `products` 
        //                 join product_category on product_category.product_category_id = products.category
        //                 join prod_sub_catagory on prod_sub_catagory.sub_catagory_id = products.subcategory
        //                 join prod_uom on prod_uom.uom_id = products.uom
        //                   WHERE products_id = 36
        //             ";die;

        $command = $connection->createCommand("
                        SELECT hsn_code,gst_percentage  FROM `product_dit` 
                        WHERE productdit_id = :products_id
                    ")->bindValue(":products_id", $productid);
        $columns = $command->queryOne();
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product info found.',
                'data'=>''
            ]);
        }
    }

    public function actionGetsubcategory()
    {
         $data = $_POST;
        $master_category = intval(Yii::$app->request->post('master_category'));
        $connection = Yii::$app->db;
       
        $command = $connection->createCommand("
                        SELECT sub_category_id as id,sub_category_value as subval  FROM `proddit_sub_category` 
                        WHERE master_category_id = :master_category
                    ")->bindValue(":master_category", $master_category);
        $columns = $command->queryAll();
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product info found.',
                'data'=>''
            ]);
        }

    }

    public function actionGetproduct()
    {
         $data = $_POST;
        $master_category = intval(Yii::$app->request->post('master_category'));
        $sub_category = intval(Yii::$app->request->post('sub_category'));
        $connection = Yii::$app->db;
       
        $command = $connection->createCommand("
                        SELECT `productdit_id` as id,`product_name` as name FROM `product_dit`
                        WHERE master_category = :master_category and sub_category=:sub_category
                    ")
                    ->bindValue(":master_category", $master_category)
                    ->bindValue(":sub_category", $sub_category);
        $columns = $command->queryAll();
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product info found.',
                'data'=>''
            ]);
        }

    }
    public function actionGetproductinformation()
    {   
        $data = $_POST;
        $productid = Yii::$app->request->post('productid');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT * FROM `product_dit` 
                        WHERE productdit_id = :products_id
                    ")->bindValue(":products_id", $productid);
        $columns = $command->queryOne();
        if (!empty($columns)) {
              return $this->asJson([
                    'status' => 'success',
                    'data' => $columns,
                ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Product info found.',
                'data'=>''
            ]);
        }
    }
    public function actionCheckpricingdone($opportunityId)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $userId = Yii::$app->user->id;

        /*  Check if user is in assigned team */
       $isTeamMember = (new \yii\db\Query())
            ->from(['o' => 'opportunity'])
            ->where(['o.opportunity_id' => $opportunityId])
            ->andWhere([
                'or',
                [
                    'and',
                    new \yii\db\Expression('FIND_IN_SET(1, o.team_responsible)'),
                    [
                        'or',
                        new \yii\db\Expression('FIND_IN_SET(:uid, o.sa_assigned)'),
                        new \yii\db\Expression('FIND_IN_SET(:uid, o.sf_assigned)')
                    ]
                ],
                [
                    'and',
                    new \yii\db\Expression('FIND_IN_SET(2, o.team_responsible)'),
                    new \yii\db\Expression('FIND_IN_SET(:uid, o.procurement_team_member)')
                ],
                [
                    'and',
                    new \yii\db\Expression('FIND_IN_SET(3, o.team_responsible)'),
                    new \yii\db\Expression('FIND_IN_SET(:uid, o.sf_assigned)')
                ],
            ])
            ->addParams([':uid' => $userId])
            ->exists();


        /*  Not a team member → cannot submit */
        if (!$isTeamMember) {
            return ['canSubmit' => false];
        }

        /*  Check if pricing already done by this user */
        $pricingExists = (new \yii\db\Query())
            ->from('opportunity_pricing_done')
            ->where([
                'opportunity_id' => $opportunityId,
                'userid' => $userId,
            ])
            ->exists();

        /*  Pricing already done → cannot submit */
        if ($pricingExists) {
            return ['canSubmit' => false];
        }

        /* Team member & pricing not done */
        return ['canSubmit' => true];
    }

    
}
