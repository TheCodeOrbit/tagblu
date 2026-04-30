<?php

namespace backend\modules\cleaning\controllers;

use common\controllers\ModuleController;
use Yii;
use yii\base\Exception;
use yii\db\Expression;

// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'cleaning';
    public $FieldId = 'cleaning_id';
    public $TableName = 'cleaning';
    public $TabLabel = 'Cleaning';
    public $TabId = '70';

    public function actionExample()
    {
        return $this->render('index');
    }

    public function actionGetdatafrominventory()
    {
        $Recordid = Yii::$app->request->post('Recordid');
        $Productid = Yii::$app->request->post('Productid');
        $Subcategory = Yii::$app->request->post('Subcategory');
        $connection = Yii::$app->db;
        $columns = (new \yii\db\Query())
              ->select([
                'inventory.*',
                'vendor_account.acc_name',
                // 'vendor_locations.vendor_loc_name',
                'warehouse.warehouse_name',
                'prod_sub_catagory.sub_catagory_value',
                'prod_model.prod_model_value',
                'prod_make.prod_make_value',
                'prod_category.prod_category_value',
                'products.product_name',
            ])
            ->from('inventory')
              ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = inventory.subcategory')
              ->leftJoin('prod_model', 'prod_model.prod_model_id = inventory.model')
              ->leftJoin('prod_make', 'prod_make.prod_make_id = inventory.make')
              ->leftJoin('prod_category', 'prod_category.prod_category_id = inventory.category')
              ->innerJoin('vendor_account', 'inventory.account_name = vendor_account.vendoraccid')
            //   ->innerJoin('vendor_locations', 'inventory.location = vendor_locations.vendorloc_id')
              ->leftJoin('warehouse', 'inventory.location = warehouse.warehouse_id')
              ->innerJoin('products', 'products.products_id = inventory.product_name')
              ->where(['inventory.grn_no' => $Recordid])
              ->andWhere(['inventory.product_name' => $Productid])
              ->andWhere(['inventory.subcategory' => $Subcategory])
              ->andWhere(['inventory.status' => 4])
              ->all();  
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Contact found.',
                'data' => ''
            ]);
        }
    }


    public function actionCheckgrnandtagnoininventory()
    {
        // $Recordid = Yii::$app->request->post('Recordid');
        // $Productid = Yii::$app->request->post('Productid');
        // $Subcategory = Yii::$app->request->post('Subcategory');
        $TagNumber = Yii::$app->request->post('TagNumber');
        $columns = (new \yii\db\Query())
              ->select(['inventory_id',
              'bin_number',
              'tag_number',
              'status',
            ])
            ->from('inventory')
            //   ->where(['inventory.grn_no' => $Recordid])
            //   ->where(['inventory.tag_number' => $TagNumber])
             ->where(new Expression('TRIM(tag_number) = TRIM(:tag_number)'), [':tag_number' => $TagNumber])
            //   ->andWhere(['inventory.status' => 3])
              ->one(); 
        if (!empty($columns)) {
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No data found.',
                'data' => ''
            ]);
        }
    }

}
