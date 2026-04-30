<?php

namespace backend\modules\inventory\controllers;

use common\controllers\ModuleController;
use Yii;
use yii\base\Exception;
/**
 * Default controller for the `grn` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout='single';
    public $ModuleName='inventory';
    public $FieldId='inventory_id';
    public $TableName='inventory';
    public $TabLabel='Inventory';
    public $TabId='33';

    public function actionExample()
    {
        return $this->render('index');
    }

    public function actionGetgrndata()
    {
        $Recordid = Yii::$app->request->post('Recordid');
        $connection = Yii::$app->db;
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
                        ps.sub_catagory_value ,
                        pm.prod_model_value, 
                        m.prod_make_value,
                        pc.prod_category_value
                    FROM products p
                    LEFT JOIN prod_sub_catagory ps ON p.subcategory = ps.sub_catagory_id
                    LEFT JOIN prod_model pm ON p.model = pm.prod_model_id 
                    LEFT JOIN prod_make m ON p.make = m.prod_make_id
                    LEFT JOIN prod_category pc ON p.category = pc.prod_category_id
                    WHERE p.products_id = :Recordid;

            ")->bindValue(":Recordid", $Recordid);

            // if (isset($_POST['product_group_id'])) {
        //     $productGroupId = intval($_POST['product_group_id']); // Sanitize input
        //     $db = Yii::$app->db;

        //     // Fetch categories based on product_group_id
        //     $query = "SELECT prod_catagory_id AS id, prod_catagory_value AS name FROM prod_catagory WHERE FIND_IN_SET(:product_group_id, prod_group_id) AND is_active = 1 ORDER BY seq_no ASC";
        //     $command = $db->createCommand($query);
        //     $command->bindValue(':product_group_id', $productGroupId);
        //     $categories = $command->queryAll();

        //     // Return categories in JSON format
        //     return ['status' => 'success', 'categories' => $categories];
        // } else {
        //     return ['status' => 'error', 'message' => 'Product Group ID is required.'];
        // }
        $columns = $command->queryOne();

        // $query = "
        //         SELECT sub_catagory_id AS id, sub_catagory_value AS name 
        //         FROM prod_sub_catagory 
        //         WHERE FIND_IN_SET(:category_id, prod_catagory_id) AND is_active = 1";
        //     $command = $connection->createCommand($query);
        //     $command->bindValue(':category_id', $columns['category']);
        //     $subcategories = $command->queryAll();
        //     $columns['subcategories'] = $subcategories;
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
