<?php

namespace backend\controllers;

use backend\models\Employee;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


class EmployeeController extends \yii\web\Controller
{
    // Lists all employees
    public function actionIndex()
    {
        $employees = Employee::find()->all();
        return $this->render('index', [
            'employees' => $employees,
        ]);
    }

    // Displays a single employee
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    // Creates a new employee
    public function actionCreate()
    {
        if (Yii::$app->request->isPost) {
            $firstName = Yii::$app->request->post('first_name');
            $lastName = Yii::$app->request->post('last_name');
            $email = Yii::$app->request->post('email');
            $phoneNumber = Yii::$app->request->post('phone_number');
            $status = Yii::$app->request->post('status');
            $dateTime = Yii::$app->request->post('date_time', null);

            // Handle saving the data to the database
            $employee = new Employee(); // Assuming Employee is your model
            $employee->first_name = $firstName;
            $employee->last_name = $lastName;
            $employee->email = $email;
            $employee->phone_number = $phoneNumber;
            $employee->status = $status;

            // Set the selected date and time to the model
            $employee->date_time = $dateTime;




            if ($employee->save()) {
                // Redirect or do something upon success
                Yii::$app->session->setFlash('success', 'Employee created successfully!');
                return $this->redirect(['employee/index']);
            } else {
                // Handle errors
                Yii::$app->session->setFlash('error', 'There was an error creating the employee.');
            }
        }

        return $this->render('create', [
            // Pass necessary data to view if required
        ]);
    }

    public function actionChangeStatus($id)
    {
        $employee = Employee::findOne($id);

        if ($employee !== null) {
            $employee->status = $employee->status === 'active' ? 'inactive' : 'active'; // Toggle the status
            if ($employee->save()) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => true, 'status' => $employee->status];
            }
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['success' => false];
    }


    // Updates an existing employee
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Employee updated successfully');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    // Deletes an employee
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Employee deleted successfully');
        return $this->redirect(['index']);
    }

    // Finds an employee by ID
    protected function findModel($id)
    {
        if (($model = Employee::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested employee does not exist.');
    }
}
