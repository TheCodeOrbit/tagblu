<?php

namespace backend\modules\deliverychallan\controllers;

use common\controllers\ModuleController;
use Yii;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'deliverychallan';
    public $FieldId = 'deliverychallan_id';
    public $TableName = 'delivery_challandit';
    public $TabLabel = 'Delivery Challan';
    public $TabId = '88';
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



    public function actionGetcompanydetail()
    {
        $warehouse_id = Yii::$app->request->get('dc_location_id');
        $connection = Yii::$app->db;
        $command = $connection->createCommand("
                        SELECT warehouse_name,address from warehouse 
                          WHERE warehouse_id = :warehouse_id
                    ")->bindValue(":warehouse_id", $warehouse_id);
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
                'data' => ''
            ]);
        }
    }

    public function actionGetcustomerpodetail()
    {
        $so_number = Yii::$app->request->get('so_number');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                         SELECT 
                            so.customer_po_num,
                            so.customer_po_date,
                            so.bill_to_legal_name,
                            so.address,
                            so.gst,
                            so.pan,
                            so.customer_payment_terms
                        FROM 
                            salesorder_dit so
                        WHERE 
                            so.salesorder_dit_id = :so_number;
                    ")->bindValue(":so_number", $so_number);
        $prod_command = $connection->createCommand("
                         SELECT spd.*,product_dit.product_description as prod_name  
                         FROM salesorderdit_product_details spd 
                         join product_dit on product_dit.productdit_id = spd.product_name
                         WHERE salesorder_dit_id = :so_number;
                    ")->bindValue(":so_number", $so_number);
        $ship_command = $connection->createCommand("
                         SELECT ssa.* ,v.vendor_loc_name
                         FROM 
                         salesorderdit_ship_to_address ssa 
                         join vendor_locations v on v.vendorloc_id = ssa.ship_delivery_location
                         WHERE salesorder_dit_id = :so_number;
                    ")->bindValue(":so_number", $so_number);
                    
        $columns = $command->queryOne();
        $prod_columns = $prod_command->queryAll();
        $ship_columns = $ship_command->queryOne();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
                'product_details' => $prod_columns,
                'ship_details' => $ship_columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Info found.',
                'data' => ''
            ]);
        }
    }


    public function actionGetmaterialreceiverdetail()
    {
        $contact = Yii::$app->request->get('contact');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                         SELECT 
                            email,
                            mobile,
                            home_mobile
                        FROM 
                            contacts
                        WHERE 
                            contacts_id = :contact;
                    ")->bindValue(":contact", $contact);
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
                'data' => ''
            ]);
        }
    }
}
