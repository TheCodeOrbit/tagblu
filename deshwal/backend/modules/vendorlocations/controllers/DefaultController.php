<?php

namespace backend\modules\vendorlocations\controllers;

use common\controllers\ModuleController;
use Yii;

/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'single';
    public $ModuleName = 'vendorlocations';
    public $FieldId = 'vendorloc_id';
    public $TableName = 'vendor_locations';
    public $TabLabel = 'Account Locations';


    public $TabId = '29';
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

    public function actionGetvendordetail()
    {
        $data = $_POST;
        $vendor_account = Yii::$app->request->post('vendor_account');
        $connection = Yii::$app->db;

        $command = $connection->createCommand("
                        SELECT currency,exchange_rate,account_short_name FROM vendor_account WHERE vendoraccid = :vendoraccid
                    ")->bindValue(":vendoraccid", $vendor_account);
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
                'data' => ''
            ]);
        }
    }

    public function actionGetcitycode()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    try {
        if (isset($_POST['city'])) {
            $city = intval($_POST['city']); // Sanitize input
            $db = Yii::$app->db;

            // Fetch city short_name
            $query = "SELECT short_name FROM city WHERE cityid = :city AND is_active = 1";
            $command = $db->createCommand($query);
            $command->bindValue(':city', $city);
            $short_name = $command->queryOne(); // Use queryOne() instead of queryAll()

            if ($short_name) {
                return ['status' => 'success', 'short_name' => $short_name['short_name']];
            } else {
                return ['status' => 'error', 'message' => 'No city found'];
            }
        } else {
            return ['status' => 'error', 'message' => 'City is required.'];
        }
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
    }
}

 public function actionIsaccountlocationduplicate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $field = Yii::$app->request->post('field');
        $value = Yii::$app->request->post('value');
        $acc_id = Yii::$app->request->post('acc_id');

        // if (!in_array($field, ['vendor_loc_name'])) {
        //     return ['exists' => false];
        // }


        $exists = (new \yii\db\Query())
            ->from($this->TableName)   
            ->where([$field => $value])
            ->andWhere(['vendor_account'=>$acc_id])
            ->exists();

        return ['exists' => $exists];
    }
}
