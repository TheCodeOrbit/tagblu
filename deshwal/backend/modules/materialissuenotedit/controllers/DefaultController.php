<?php

namespace backend\modules\materialissuenotedit\controllers;

use common\controllers\ModuleController;
use Yii;
use yii\db\Query;

/**
 * Default controller for the `materialissuenotedit` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'materialissuenotedit';
    public $FieldId = 'mindit_id';
    public $TableName = 'materialissuenote_dit';
    public $TabLabel = 'Material Issue Note';
    public $TabId = '120';

    //  public function beforeAction($action)
    // {
    //     $this->layout = '@app/views/layouts/main';  // Set the main layout before each action
    //     return parent::beforeAction($action);
    // }

   
    public function actionGetwarehouseval()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $compId = Yii::$app->request->post('comp_name');
        $data = null;

        $warehouse = \app\models\Warehouse::findOne($compId);

        if ($warehouse) {
            $compName = $warehouse->warehouse_name;
            $comp_address = $warehouse->address;
            $gstNo = $warehouse->gstn;
            $panNo = $warehouse->pan_number;
            $contactNo = $warehouse->contact_number;

            return [
                'status' => 'success',
                'compName' => $compName,
                'comp_address' => $comp_address,
                'gstNo' => $gstNo,
                'panNo' => $panNo,
                'contactNo' => $contactNo
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Warehouse not found'
            ];
        }
    }

     public function actionGetsodetail()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $soId = Yii::$app->request->post('so_number');
        $data = null;


        $query = (new \yii\db\Query())
            ->select([
                'spd.hsn_code',
                'spd.product_description',
                'spd.product_name AS prod_id',
                'p.product_name',
            ])
            ->from(['spd' => 'salesorderdit_product_details'])
            ->innerJoin(
                ['p' => 'product_dit'],
                'p.productdit_id = spd.product_name'
            )
            ->where(['salesorder_dit_id' => $soId]);

        $salesOrderDitProducts = $query->all();
        if ($salesOrderDitProducts) {
            
            return [
                'status' => 'success',
               'product' => $salesOrderDitProducts
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Not FOund'
            ];
        }
    }

    public function actionGetproductdetail(){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $productId = Yii::$app->request->post('product_id',null);
        $result = [];
        if($productId == null){
            return ['success' =>false];
        }

        $query = new Query();
        $query->select(['product_name','product_description','hsn_code']);
        $query->from('product_dit');
        $query->where(['=','productdit_id',$productId]);
        $result = $query->one();
        if(count($result) > 0){
            return ['status'=>'success','result' =>$result];
        }
        return ['success' =>false];
        
    }
}
