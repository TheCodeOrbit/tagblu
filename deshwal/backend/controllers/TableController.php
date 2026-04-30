<?php

namespace backend\controllers;

use backend\models\TableList;
// use yii\web\Controller;
use common\components\Controller;
use yii\web\Response;
use Yii;

/**
 * Site controller
 */
class TableController extends Controller
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionTableList()
    {
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => TableList::find(),
        ]);

        return $this->render('table_list', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetData()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $models = TableList::find()->all();
            $data = [];

            foreach ($models as $model) {
                $data[] = [
                    'id' => $model->id,
                    'first_name' => $model->first_name,
                    'last_name' => $model->last_name,
                    'email' => $model->email,
                    'phone' => $model->phone,
                    'country' => $model->country,
                    'city' => $model->city,
                    'owner' => is_array($model->owner) ? implode(', ', $model->owner) : $model->owner,  // Example of array handling
                    'company_name' => $model->company_name,
                    'address' => $model->address,
                    'company_address' => $model->company_address,
                    'company_website' => $model->company_website,
                    'employee_age' => $model->employee_age,
                    'employee_name' => $model->employee_name,
                    'created_at' => $model->created_at,
                ];
            }

            return $data;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }


    public function actionUpdateCell()
    {
        $id = Yii::$app->request->post('id');  // Row ID
        $column = Yii::$app->request->post('column');  // Column index
        $value = Yii::$app->request->post('value');  // New value

        // Find the row by ID
        $model = TableList::findOne($id);

        if ($model) {
            // Update the specific column based on the index
            if ($column == 1) {
                $model->first_name = $value;
            } elseif ($column == 2) {
                $model->last_name = $value;
            } elseif ($column == 3) {
                $model->email = $value;
            } elseif ($column == 4) {
                $model->phone = $value;
            }

            if ($model->save()) {
                return json_encode(['success' => true]);
            } else {
                return json_encode(['success' => false]);
            }
        }

        return json_encode(['success' => false]);
    }
}
