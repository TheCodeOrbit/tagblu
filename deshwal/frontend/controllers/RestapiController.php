<?php
namespace frontend\controllers;

// use app\models\IqcLaptop;

// use app\models\AutoNo;

use frontend\models\ModtrackerBasic;
use Yii;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\models\User;
use DateTime;
use DateTimeZone;
use frontend\models\IqcLaptop;
use yii\db\Expression;

class RestapiController extends Controller
{
    public $enableCsrfValidation = false; // Disable CSRF validation for API
    private $apiToken;

    public function init()
    {
        $this->apiToken = Yii::$app->params['apiToken'];
    }
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        return $behaviors;
    }

    public function beforeAction($action)
    {
        //allow CORS
        Yii::$app->response->headers->set('Access-Control-Allow-Origin', '*');
        Yii::$app->response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        Yii::$app->response->headers->set('Access-Control-Allow-Headers', 'Authorization, Content-Type');

        Yii::$app->response->format = Response::FORMAT_JSON;

        $headers = Yii::$app->request->headers;
        $authHeader = $headers->get('Authorization');

        if (!$authHeader || !preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            Yii::$app->response->statusCode = 401;
            echo json_encode([
                'status' => 'error',
                'message' => 'Authorization header missing or invalid.',
                'data' => null
            ]);
            return false; // Stop execution
        }

        $token = $matches[1];
        if ($token !== $this->apiToken) {
            Yii::$app->response->statusCode = 401;
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid API token.',
                'data' => null
            ]);
            return false; // Stop execution
        }

        return parent::beforeAction($action);
    }

    // Handle API responses
    protected function apiResponse($status = 'success', $data = [], $message = '')
    {
        return [
            'status' => $status,
            'data' => $data,
            'message' => $message,
        ];
    }

    public function actionUsers()
    {
        $users = User::find()
            ->select(['id', new Expression('CONCAT(first_name, " ", last_name) AS name')])
            ->where(['deleted' => 0])
            ->andWhere(['!=', 'id', 1])
            ->asArray()
            ->all();

        return $this->apiResponse('success', $users, 'Users fetched successfully');
    }

    public function actionUser($id)
    {
        $user = User::find()
            ->select(['id', 'first_name', 'last_name', 'email'])
            ->where(['id' => $id, 'deleted' => 0])
            ->asArray()
            ->one();

        if ($user === null) {
            return $this->apiResponse('error', null, 'User not found');
        }

        return $this->apiResponse('success', $user, 'User fetched successfully');
    }

    public function actionSaveiqc()
    {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $data = json_decode($request->getRawBody(), true);
    
        if ($data === null) {
            return ['error' => 'Invalid JSON or empty body'];
        }
    
        // // Now you can access $data['key1'], etc.
        // return [
        //     'status' => 'success',
        //     'data' => $data
        // ];
        // $request = Yii::$app->request;
        // $data = $request->bodyParams;
        
        // $data = json_decode(file_get_contents("php://input"), true);
        // echo "<pre>";print_r($data);die;
        if (!is_array($data)) {
            return [
                'success' => false,
                'message' => 'Invalid or missing JSON body',
            ];
        }

        $Makeid = (new \yii\db\Query())
            ->select('makeid')
            ->from('iqc_make')
            ->where(['make_value' => $data['Make']])
            ->scalar();
        if (!$Makeid) {
            return [
                'success' => false,
                'message' => 'Invalid Make'
            ];
        }

        $modelValue = ucfirst(trim($data['Model']));
        $Modelid = (new \yii\db\Query())
            ->select('modelid')
            ->from('iqc_model')
            ->where(['model_value' => $modelValue, 'makeid' => $Makeid])
            ->scalar();
        if (!$Modelid) {
            // return [
            //     'success' => false,
            //     'message' => 'Invalid Model'
            // ];
            Yii::$app->db->createCommand()
            ->insert('iqc_model', [
                'model_value' => $modelValue,
                'makeid' => $Makeid,
            ])
            ->execute();
        }
        $ram1 = $data['RAM'];
        $ram = !empty($ram1) ? '1' : '0';

        $CPUid = (new \yii\db\Query())
            ->select('cpuid')
            ->from('iqc_cpu')
            ->where(['cpu_value' => $data['CPU']])
            ->scalar();
        if (!$CPUid) {
            return [
                'success' => false,
                'message' => 'Invalid CPU'
            ];
        }

        $serial_no = $data['Serial_No'];

        $Generationid = (new \yii\db\Query())
            ->select('generationid')
            ->from('iqc_generation')
            ->where(['generation_value' => $data['Generation']])
            ->scalar();
        if (!$Generationid) {
            return [
                'success' => false,
                'message' => 'Invalid Generation'
            ];
        }

        $HDD1 = $data['HDD'];
        $HDD = !empty($HDD1) ? '1' : '0';

        $battery_health1 = $data['Battery_Health'];
        $battery_health = !empty($battery_health1) ? '1' : '2';

        $Sound = (new \yii\db\Query())
            ->select('soundid')
            ->from('iqc_sound')
            ->where(['sound_value' => $data['Sound']])
            ->scalar();
        if (!$Sound) {
            return [
                'success' => false,
                'message' => 'Invalid Sound'
            ];
        }
        $Tag_No = $data['Tag_No'];
        $screenIssues = [];
        if (isset($data['Screen']) && is_array($data['Screen'])) {
            foreach ($data['Screen'] as $issue => $status) {
                // Example: Save each issue if status is 'Yes'
                if (strtolower($status) === 'yes') {
                        $scr = (new \yii\db\Query())
                        ->select('screenid')
                        ->from('iqc_screen')
                        ->where(['screen_value' => $issue])
                        ->scalar();
                    $screenIssues[] = $scr;
                }
            }
        }
        // Create comma-separated string
        if(!empty($screenIssues))
            $screenStr = implode(',', $screenIssues);
        else
            $screenStr  =   NULL;
        
        // $hinge = (new \yii\db\Query())
        //     ->select('hingeid')
        //     ->from('iqc_hinge')
        //     ->where(['hinge_value' => $data['Hinge']])
        //     ->scalar();
        // if (!$hinge) {
        //     return [
        //         'success' => false,
        //         'message' => 'Invalid Hinge'
        //     ];
        // }

        $hinge = NULL;
        if (isset($data['Hinge']) && is_array($data['Hinge'])) {
            foreach ($data['Hinge'] as $issue => $status) {
                // Example: Save each issue if status is 'Yes'
                if (strtolower($status) === 'yes') {

                    // $hinge[] = $issue;
                    $hinge = (new \yii\db\Query())
                        ->select('hingeid')
                        ->from('iqc_hinge')
                        ->where(['hinge_value' => $issue])
                        ->scalar();
                    // if (!$hinge) {
                    //     return [
                    //         'success' => false,
                    //         'message' => 'Invalid Sound'
                    //     ];
                    // }
                }
            }
        }

        $panel =  $front_panel = $palmrest_panel = $base_panel = [];
        if (isset($data['Panel']) && is_array($data['Panel'])) {
            foreach ($data['Panel'] as $Key => $Value) { 
                             
                    if (strtolower($Key) === 'panel') {
                        foreach ($Value as $keys => $values) {  
                            // echo "<pre>";print_r($Value);die;
                            if (strtolower($values) === 'yes') {
                                $_panel = (new \yii\db\Query())
                                    ->select('panelid')
                                    ->from('iqc_panel')
                                    ->where(['panel_value' => $keys])
                                    ->scalar();
                                $panel[] = $_panel;
                            }
                        }
                    }
                    else if (strtolower($Key) === 'front_panel_bazel') 
                    {
                        foreach ($Value as $keys => $values) {  
                            if (strtolower($values) === 'yes') {
                                $_front_panel = (new \yii\db\Query())
                                ->select('front_panelid')
                                ->from('iqc_front_panel')
                                ->where(['front_panel_value' => $keys])
                                ->scalar();
                                $front_panel[] = $_front_panel;
                            }
                        }
                    }
                    else if (strtolower($Key) === 'palmrest_panel') 
                    {
                        foreach ($Value as $keys => $values) {  
                            if (strtolower($values) === 'yes') {
                                $_palmrest_panel = (new \yii\db\Query())
                                ->select('palmrest_panel_id')
                                ->from('iqc_palmrest_panel')
                                ->where(['palmrest_panel_value' => $keys])
                                ->scalar();
                                $palmrest_panel[] = $_palmrest_panel;
                            }
                        }
                    }
                    else if (strtolower($Key) === 'base_panel') 
                    {
                        foreach ($Value as $keys => $values) {  
                            if (strtolower($values) === 'yes') {
                                $_base_panel = (new \yii\db\Query())
                                ->select('base_panel_id')
                                ->from('iqc_base_panel')
                                ->where(['base_panel_value' => $keys])
                                ->scalar();
                                $base_panel[] = $_base_panel;
                            }
                        }
                    }
            }
        }
        if(!empty($panel))
            $str_panel = implode(',', $panel);
        else
            $str_panel  =   NULL;

        if(!empty($front_panel))
            $str_front_panel = implode(',', $front_panel);
        else
            $str_front_panel  =   NULL;

        if(!empty($palmrest_panel))
            $str_palmrest_panel = implode(',', $palmrest_panel);
        else
            $str_palmrest_panel  =   NULL;

        if(!empty($base_panel))
            $str_base_panel = implode(',', $base_panel);
        else
            $str_base_panel  =   NULL;

    
        $keyboard = NULL;
        if (isset($data['Keyboard']) && is_array($data['Keyboard'])) {
                foreach ($data['Keyboard'] as $Key => $Value) {
                    if (strtolower($Value) === 'yes') {
                        $keyboard = (new \yii\db\Query())
                        ->select('keyboardid')
                        ->from('iqc_keyboard')
                        ->where(['keyboard_value' => $Key])
                        ->scalar();
                    }
                }
            }


        $touchpad = [];
        if (isset($data['Touchpad']) && is_array($data['Touchpad'])) {
                foreach ($data['Touchpad'] as $Key => $Value) {
                    if (strtolower($Value) === 'yes') {
                        $touchpad_ = (new \yii\db\Query())
                        ->select('touchpadid')
                        ->from('iqc_touchpad')
                        ->where(['touchpad_value' => $Key])
                        ->scalar();
                        $touchpad[] = $touchpad_;
                    }
                }
            }
            if(!empty($touchpad))
                $touchpadStr = implode(',', $touchpad);
            else
                $touchpadStr = NULL;
        $port = '0';
        if (isset($data['Port']) && is_array($data['Port'])) {
            foreach ($data['Port'] as $Key => $Value) {
                if (strtolower($Value) === 'yes') {
                    $port = (new \yii\db\Query())
                    ->select('port_id')
                    ->from('iqc_port')
                    ->where(['port_value' => $Key])
                    ->scalar();
                }
            }
        }

        $usb = 0;
        if (isset($data['USB']) && is_array($data['USB'])) {
            foreach ($data['USB'] as $Key => $Value) {
                if (strtolower($Value) === 'yes') {
                    $usb = (new \yii\db\Query())
                    ->select('usbid')
                    ->from('iqc_usb')
                    ->where(['usb_value' => $Key])
                    ->scalar();
                }
            }
        }


        $iqc_laptop = new IqcLaptop();

        // if ($autoField = $this->checkAutoNo()) {
        //     $iqc_laptop->{$autoField} = $this->getAutoNo(34);
        // }

        $iqc_laptop->make = $Makeid;
        $iqc_laptop->sub_category = "1"; //send bydefault laptop subcategory  fix on date 13-05-25 in meeting
        $iqc_laptop->model = $Modelid;
        $iqc_laptop->ram = $ram;
        $iqc_laptop->ram1 =$ram1;
        $iqc_laptop->hdd =$HDD;
        $iqc_laptop->hdd1 =$HDD1;
        $iqc_laptop->cpu = $CPUid;
        $iqc_laptop->serial_no = $serial_no;
        $iqc_laptop->generation = $Generationid;
        $iqc_laptop->hdd = $HDD;
        $iqc_laptop->battery_health = $battery_health;
        // //find user from user table and store id 
        // $iqc_laptop->ownerid = $data['Account_Owner'];

        $iqc_laptop->sound = $Sound;
        $iqc_laptop->tag_no = $Tag_No;

        // //check this for store data
        $iqc_laptop->screen = $screenStr;

        $iqc_laptop->hinge = $hinge;

        $iqc_laptop->panel = $str_panel;
        $iqc_laptop->front_panel = $str_front_panel;
        $iqc_laptop->palmrest_panel = $str_palmrest_panel;
        $iqc_laptop->base_panel = $str_base_panel;

        $iqc_laptop->keyboard = $keyboard;
    
        $iqc_laptop->touch_pad = $touchpadStr;

        $iqc_laptop->port = $port;

        $iqc_laptop->usb = $usb;

        $iqc_laptop->deleted = 0;
        $iqc_laptop->ownerid = 188;//added trcplant@dwmpl.com id as per client on 13 august 2025
        $iqc_laptop->modifiedby  = 188;//added trcplant@dwmpl.com id as per client on 13 august 2025
        $iqc_laptop->creatorid = 188;//added trcplant@dwmpl.com id as per client on 13 august 2025
        $iqc_laptop->createdtime = date('Y-m-d H:i:s');
        $iqc_laptop->modifiedtime = date('Y-m-d H:i:s');
        if ($autoField = $this->checkAutoNo()){
                    $iqc_laptop->{$autoField}  = $this->getAutoNo(34);  // 34 tabid for iqc_laptop
        }
        
        // print_r($iqc_laptop->attributes);die;
        if ($iqc_laptop->save()) {
            $this->makeauditlog($iqc_laptop,'iqclaptop',$iqc_laptop->iqclaptop_id,'iqclaptop_id');
            return $this->apiResponse('success', ['id' => $iqc_laptop->iqclaptop_id], 'IQC Laptop created successfully');
        } else {
            return $this->apiResponse('error', $iqc_laptop->getErrors(), 'Failed to create IQC Lpatop');
        }
    }

    public function makeauditlog($modelleadetail,$ModuleName,$id,$keyname)
    {
        $modlog = new ModtrackerBasic();
        $auditstatus = 0;
        $mode = 'create';//$_POST["mode"];
        $module = $ModuleName;//$_POST["module"];
        $customtablename = $module . "cf";
        $CS = array();
        if (isset($_POST[$customtablename]))
            $CS = $_POST[$customtablename];
        else
            $CS = '';
        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $ModuleName, $id, $auditstatus, 1);
        //this is not added because API not create auto no
                        $this->updateCRMSequence($module, $id);
        //                 //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$keyname => $id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $ModuleName, $id, $auditstatus, 1);
                        }
        //                 if ($autoField = $this->checkAutoNo())
        //                     $this->setAutoNo($tabs);
                 
    }


     public function checkAutoNo()
    {

        $table_name = "iqc_laptop";//$this->tableName();
        $autoField = Yii::$app->db->createCommand("SELECT columnname
            FROM field 
            WHERE tablename = :tablename AND uitype = :uitype")
            ->bindValue(':tablename', $table_name)
            ->bindValue(':uitype', 11)
            ->queryOne();
        if (empty($autoField))
            return false; // if does not exist;
        if (count($autoField) < 1)
            return false;
        else
            return $autoField['columnname'];
    }

    public function getAutoNo($tabs)
    {
        $table_name = 'iqc_laptop';
        $orderno = $this->getautomoduleno($tabs, $table_name);
        return $orderno;
    }

    public function getautomoduleno($tabs, $table_name)
    {
        if ($table_name == "iqc_laptop")
            $table_name = "iqclaptop";

        // Get the current number
        $autoNo = Yii::$app->db->createCommand("SELECT prefix, cur_id 
        FROM modentity_num 
        WHERE semodule = :semodule AND active = 1 FOR UPDATE")
            ->bindValue(':semodule', $table_name)
            ->queryOne(); // use queryOne instead of queryAll

        if (!$autoNo) {
            throw new \Exception("Auto number config not found for module: $table_name");
        }

        $prefix = $autoNo['prefix'];
        $cur_id = $autoNo['cur_id'];

        // Build the final order number
        $autoNoStr = sprintf("%04d", $cur_id);
        $cyear = date('Y');
        $orderno = $prefix . '-' . $cyear . '-' . $autoNoStr;

        // Now increment the current ID in DB immediately
        Yii::$app->db->createCommand("UPDATE modentity_num 
        SET cur_id = cur_id + 1 
        WHERE semodule = :semodule AND active = 1")
            ->bindValue(':semodule', $table_name)
            ->execute();

        return $orderno;
    }
    
    //update crmentity sequence
    function updateCRMSequence($semodule, $crmid)
    {
        // echo "UPDATE `modentity_num` SET cur_id = $crmid where semodule='$semodule'" ;die;
        try {
            Yii::$app->db->createCommand("UPDATE `modentity_num` SET cur_id = :crmid where semodule=:semodule")
                ->bindParam(":crmid", $crmid)
                ->bindParam(":semodule", $semodule)
                ->execute();
        } catch (\Exception $e) {
            // Handle the error, e.g. log it or display a message
            Yii::error($e->getMessage());
        }
    }
}
