<?php

namespace backend\modules\packinglistdit\controllers;

use common\controllers\ModuleController;
use Yii;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'packinglistdit';
    public $FieldId = 'packinglist_id';
    public $TableName = 'packing_list_dit';
    public $TabLabel = 'Packing List';
    public $TabId = '90';
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
        $warehouse_id = Yii::$app->request->get('company_id');
        $connection = Yii::$app->db;
        $command = $connection->createCommand("
                        SELECT warehouse_name,address,gstn,contact_number,pan_number from warehouse 
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

    public function actionGetdeliverychallandetail()
    {
        $dc_id = Yii::$app->request->get('dc_id');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT dcit.*,CONCAT(c.first_name, ' ', c.last_name) AS mrn_full_name,
                        dcmt.value as mode_of_transport_value
                        FROM delivery_challandit dcit
                        join contacts c on c.contacts_id = dcit.material_receiver_name
                        join dc_modoftransport dcmt on dcmt.id = dcit.mod_of_transport
                        WHERE deliverychallan_id = :dc_id;
                    ")->bindValue(":dc_id", $dc_id);
        $columns = $command->queryOne();

        $prod_command = $connection->createCommand("
                         SELECT dpd.*,product_dit.product_name as product_name  
                         FROM deliverychallandit_product_details dpd 
                         join product_dit on product_dit.productdit_id = dpd.poduct_description
                         WHERE deliverychallan_id = :dc_id;
                    ")->bindValue(":dc_id", $dc_id);
        $prod_columns = $prod_command->queryAll();
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
                'product_details' => $prod_columns,
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
