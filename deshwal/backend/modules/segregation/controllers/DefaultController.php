<?php

namespace backend\modules\segregation\controllers;

use common\controllers\ModuleController;
use Yii;
use yii\base\Exception;
/**
 * Default controller for the `grn` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='multiple';
    public $ModuleName='segregation';
    public $FieldId='segregation_id';
    public $TableName='segregation';
    public $TabLabel='Segregation';
    public $TabId='67';

    public function actionExample()
    {
        return $this->render('index');
    }

    public function actionGetgrndata()
    {
        $Recordid = Yii::$app->request->post('Recordid');
        $connection = Yii::$app->db;
        $totalweight = 0;
        $command = $connection->createCommand("
                    SELECT 
                        grn.*, 
                        grn_asset_detail.*,
                        pickup.pickup_no,pickup.pickup_id
                    FROM grn
                    INNER JOIN grn_asset_detail ON grn.grn_id = grn_asset_detail.grn_id
                    INNER JOIN pickup ON grn.pickup_id = pickup.pickup_id
                    WHERE grn_asset_detail.grn_asset_detail_id = :Recordid
                ")->bindValue(":Recordid", $Recordid);
        $columns = $command->queryOne();
        if(isset($columns['uom']) && strtolower(trim($columns['uom'])) === 'kg'){
            $productweight = $connection->createCommand("
                    SELECT weight_kg
                    FROM products
                    WHERE products_id = :Recordid
                ")->bindValue(":Recordid", $columns['porduct_name']);
            $product_weight = $productweight->queryOne();
            // echo "<pre>";print_r($product_weight);die;
            if (empty($product_weight['weight_kg'])) {
                return $this->asJson([
                    'status' => 'error',
                    'message' => 'Weight not added for GRN product.'
                ]);
            }

            if (!empty($columns['received_qty'])) {
                $totalweight = (float)$columns['received_qty'] * (float)$product_weight['weight_kg'];
            }
        }
        $columns['total_weight'] = $totalweight;
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
                        pc.*
                    FROM products p
                    LEFT JOIN prod_sub_catagory ps ON p.subcategory = ps.sub_catagory_id
                    LEFT JOIN prod_model pm ON p.model = pm.prod_model_id 
                    LEFT JOIN prod_make m ON p.make = m.prod_make_id
                    LEFT JOIN prod_category pc ON p.category = pc.prod_category_id
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

    public function actionGetlocationcde()
    {
        $Recordid = Yii::$app->request->post('Recordid');
        $connection = Yii::$app->db;
        // $command = $connection->createCommand("
        //             SELECT * 
        //             FROM seg_location_code
        //             WHERE locationfloor_id = :Recordid AND is_active = 1
        //         ")->bindValue(":Recordid", $Recordid);
        // $columns = $command->queryAll();
        // if (!empty($columns)) {
        //         return $this->asJson([
        //             'status' => 'success',
        //             'data' => $columns,
        //         ]);
        // } else {
        //     return $this->asJson([
        //         'status' => 'error',
        //         'message' => 'No Contact found.',
        //         'data'=>''
        //     ]);
        // }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {

            $db = Yii::$app->db;

            // Fetch subcategories where prod_catagory_id matches the selected category
            $command = $connection->createCommand("
                    SELECT * 
                    FROM seg_location_code
                    WHERE locationfloor_id = :Recordid AND is_active = 1
                ")->bindValue(":Recordid", $Recordid);
        $columns = $command->queryAll();

            return ['status' => 'success', 'locations' => $columns];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
        }
    }
}
