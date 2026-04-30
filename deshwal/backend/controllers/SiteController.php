<?php

namespace backend\controllers;

use app\models\User;
use common\models\Forgotpassword;
use app\models\MeetingInformation;
use app\models\Notifications;
use backend\models\AccessCheck;
use common\controllers\ModuleController;
use common\models\LoginForm;
use Yii;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
// use yii\web\Controller;
use yii\web\Response;
use common\components\Controller;
use common\models\Resetpassword;
use app\models\Field;
use app\models\ListHire;
use app\models\ListHireWidgets;
use app\models\Widget;
use app\models\SiteSetting;
use Exception;
use yii\db\Query;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'forgotpassword', 'resetpassword'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'logout',
                            'index',
                            'phpinfo',
                            'getwidgets',
                            'updatewidgetsview',
                            'refreshwidgetdropdowan',
                            'getchartdata',
                            'dispalywidgets',
                            'getnotifications',
                            'marknotificationsseen',
                            'updatereadstatus',
                            'Marknotificationread',
                            'searchinallmodule',
                            'generatepassword',
                            'getdynamicquery',
                            'select-profile',
                            'set-profile',
                            'change-theme', 
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['get'], //['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
                'layout' => false, // Remove the layout for error page
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // echo "deep";die;
        // redirect to lead list
        // return $this->redirect(array("leads/list"));
        $this->layout = '@app/views/layouts/main-one';

        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {

            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // return $this->goBack();
            return $this->redirect(['site/select-profile']);
        }


        $model->password = '';
        $this->layout = "login";
        $siteSetting = SiteSetting::find()
            ->where(['active' => 1])->one();

        return $this->render('login', [
            'model' => $model,
            'siteSetting' => $siteSetting,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        $userId = Yii::$app->user->id;
        Yii::$app->user->logout();
        //code added for logout activity of user by ptpatel on date 25-08-2025
        // Log logout activity
        Yii::$app->db->createCommand()->insert('user_activity_log', [
            'user_id' => $userId,
            'activity' => 'Logout',
            'ip_address' => Yii::$app->request->userIP,
            'user_agent' => Yii::$app->request->userAgent,
            'created_at' => date('Y-m-d H:i:s'),
        ])->execute();
        //end code added for user activlity log

        return $this->goHome();
    }
    public function actionPhpinfo()
    {
        echo phpinfo();
        die;
    }


    public function actionGetnotifications()
    {
        $uid = Yii::$app->user->id; // Get logged-in user ID

        // Fetch unread notifications
        $notifications = Yii::$app->db->createCommand("
        SELECT id, message, source_link, createdtime 
        FROM notification 
        WHERE read_status = 0 AND userid = :uid 
        ORDER BY createdtime DESC
        ")->bindValue(':uid', $uid)->queryAll();

        // Count only notifications with display_status = 0
        $unreadCount = Yii::$app->db->createCommand("
        SELECT COUNT(*) FROM notification 
        WHERE display_status = 0 AND userid = :uid
        ")->bindValue(':uid', $uid)->queryScalar();

        return json_encode([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    // Mark notifications as seen when opening the dropdown
    public function actionMarknotificationsseen()
    {
        $uid = Yii::$app->user->id;

        Yii::$app->db->createCommand("
        UPDATE notification 
        SET display_status = 1 
        WHERE display_status = 0 AND userid = :uid
        ")->bindValue(':uid', $uid)->execute();

        return json_encode(['status' => 'success']);
    }

    // Mark a notification as read when clicked
    public function actionMarknotificationread()
    {
        $uid = Yii::$app->user->id;
        $id = Yii::$app->request->post('id');

        Yii::$app->db->createCommand("
            UPDATE notification 
            SET read_status = 1 
            WHERE id = :id AND userid = :uid
            ")->bindValues([':id' => $id, ':uid' => $uid])->execute();

        return json_encode(['status' => 'success']);
    }

    public function actionUpdatereadstatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Retrieve POST parameters
        $id = Yii::$app->request->post('notifId');
        $readStatus = Yii::$app->request->post('read_status');

        if ($id === null) {
            throw new BadRequestHttpException("Missing notification id.");
        }

        // Find the notification record
        $notification = Notifications::findOne($id);
        if (!$notification) {
            return ['success' => false, 'message' => 'Notification not found.'];
        }

        // Update the read status
        $notification->read_status = $readStatus;

        if ($notification->save()) {
            return ['success' => true];
        } else {
            // Return error messages if saving fails
            return ['success' => false, 'message' => 'Unable to update notification.', 'errors' => $notification->getErrors()];
        }
    }

    //code added by ptpatel on date 15-05-25
    public function actionGetwidgets()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        //for refresh widget
        $refresh_widgetId = Yii::$app->request->get('refresh_widgetId');
        if (isset($refresh_widgetId) && $refresh_widgetId != '') {
            $sql = "select * from widget where id = :widgetId";
            $resultArray = Yii::$app->db->createCommand($sql)
                ->bindValue(':widgetId', $refresh_widgetId)
                ->queryOne();
            $widgeturl = $resultArray['widgeturl'];
            $position = $resultArray['position'];
            $widgetid = $refresh_widgetId;
            $title = $resultArray['title'];
            $widgetname = $resultArray['name'];
            $widgetType = $resultArray['type'];
            $ModuleName = $resultArray['modulename'];
            $filterid = $resultArray['filterid'] ?? 0; //? $resultArray['filterid'] : '';
        }
        //widget will add from dropdowan
        else {
            $widgeturl = Yii::$app->request->get('widgeturl');
            $position = Yii::$app->request->get('position');
            $widgetid = Yii::$app->request->get('widgetId');
            $title = Yii::$app->request->get('title');
            $widgetname = Yii::$app->request->get('name');
            $widgetType = Yii::$app->request->get('widgetType');
            $ModuleName = Yii::$app->request->get('modulename');
            $filterid = Yii::$app->request->get('filterid');
            $this->updatewidgetview($widgetid, 0);
        }

        $isAdmin = $this->isAdmin($ModuleName);
        
        if ($widgetType == "2") { //2 count
            $widgetData = $this->getWidgetData($widgetname);
        }
        else if($widgetType == "1"){
            $widgetData = $this->getWidgetBodyData($widgetname,$isAdmin,$ModuleName);
        }
        // it create problem on after refresh 
        if (isset($refresh_widgetId) && $refresh_widgetId != '')
            $this->layout = false;
        else
            $this->layout = '@app/views/layouts/main-one';

        return $this->renderPartial('widgets/main-widget-view.php', [
            'widgeturl' => $widgeturl,
            'widgetId' => $widgetid,
            'position' => $position,
            'title' => $title,
            'widgetType' => $widgetType,
            'widgetData' => isset($widgetData) ? $widgetData : '',
            'modulename' => $ModuleName,
            'isAdmin' => $isAdmin,
            'filterid' => $filterid,
        ]);
    }

    protected function updatewidgetview($widgetid, $view)
    {
        $sql = "update widget SET `view` = :view where id = :widgetid";
        $resultArray = Yii::$app->db->createCommand($sql)
            ->bindValue(':widgetid', $widgetid)
            ->bindValue(':view', $view)
            ->execute();
        return $resultArray;
    }
    //this required on close button view should be updated it call ajax
    public function actionUpdatewidgetsview($widgetid, $view)
    {
        $widgetid = Yii::$app->request->get('widgetid') ? Yii::$app->request->get('widgetid') : $widgetid;
        $view = Yii::$app->request->get('view') ? Yii::$app->request->get('view') : $view;
        return $this->updatewidgetview($widgetid, $view);
    }
    public function getwidgetsforuser()
    {

        // JOIN widget ON widget.id = profile2widget.widgetid
        $user_id = Yii::$app->user->id; // Get logged-in user ID
        $activeroleId = Yii::$app->session->get('active_profile_id');

        $widgetids = Yii::$app->db->createCommand("
                SELECT * 
                FROM profile2widget
                JOIN role2profile ON role2profile.profileid = profile2widget.profileid
                JOIN role ON role.roleid = role2profile.roleid
                JOIN user2role ON user2role.roleid = role.roleid
                JOIN user ON user.id = user2role.userid
                WHERE user.id = :uid
                AND user2role.roleid = :roleid
            ")
            ->bindValue(':uid', $user_id)
            ->bindValue(':roleid', $activeroleId)
            ->queryOne(); // Use queryAll() if expecting multiple records
        if (isset($widgetids['widgetid'])) {
            $idsString = $widgetids['widgetid']; // e.g., '1,2'
            $ids = array_map('intval', explode(',', $idsString));
            $placeholders = [];
            $params = [];

            foreach ($ids as $index => $id) {
                $key = ':id' . $index;
                $placeholders[] = $key;
                $params[$key] = $id;
            }
            $sql = "SELECT * FROM widget WHERE id IN (" . implode(',', $placeholders) . ") AND deleted = 0 AND is_active = 1 AND view = 1";
            $allwidgets = Yii::$app->db->createCommand($sql)
                ->bindValues($params)
                ->queryAll();
            if (!empty($allwidgets)) {
                return $allwidgets;
            }
        } else {
            return "";
        }
    }

    public function actionDispalywidgets()
    {
        $activeroleId = Yii::$app->session->get('active_profile_id');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // JOIN widget ON widget.id = profile2widget.widgetid
        $user_id = Yii::$app->user->id; // Get logged-in user ID
        $widgetids = Yii::$app->db->createCommand("
                SELECT * 
                FROM profile2widget
                JOIN role2profile ON role2profile.profileid = profile2widget.profileid
                JOIN role ON role.roleid = role2profile.roleid
                JOIN user2role ON user2role.roleid = role.roleid
                JOIN user ON user.id = user2role.userid
                WHERE user.id = :uid
                AND user2role.roleid = :roleid
            ")
            ->bindValue(':uid', $user_id)
            ->bindValue(':roleid', $activeroleId)
            ->queryOne(); // Use queryAll() if expecting multiple records
        if (isset($widgetids['widgetid'])) {
            $idsString = $widgetids['widgetid']; // e.g., '1,2'
            $ids = array_map('intval', explode(',', $idsString));
            $placeholders = [];
            $params = [];

            foreach ($ids as $index => $id) {
                $key = ':id' . $index;
                $placeholders[] = $key;
                $params[$key] = $id;
            }
            //dispaly widgets by default placed on dashboard by user
            $sql = "SELECT * FROM widget WHERE id IN (" . implode(',', $placeholders) . ") AND deleted = 0 AND is_active = 1 AND view = 0";
            $allwidgets = Yii::$app->db->createCommand($sql)
                ->bindValues($params)
                ->queryAll();
            $html = '';

            foreach ($allwidgets as $widget) {
                // echo '<pre>';print_r($widget);die;
                $isAdmin = $this->isAdmin($widget['modulename']);

                if ($widget['type'] == 2) //2 = count,1=summery
                    $widgetData = $this->getWidgetData($widget['name']);
                else if ($widget['type'] == 1) //2 = count,1=summery
                    $widgetData = $this->getWidgetBodyData($widget['name'],$isAdmin,$widget['modulename']);
                $html .= $this->renderPartial('widgets/main-widget-view.php', [
                    'widgeturl' => $widget['widgeturl'],
                    'widgetId' => $widget['id'],
                    'position' => $widget['position'],
                    'title' => $widget['title'],
                    'widgetType' => $widget['type'],
                    'widgetData' => $widgetData ?? '',
                    'modulename' => $widget['modulename'],
                    'isAdmin' => $isAdmin,
                    'filterid' => $widget['filter_id'],
                ]);

            }
            return $html;
        } else {
            return "";
        }
    }

    public function actionRefreshwidgetdropdowan()
    {
        $activeroleId = Yii::$app->session->get('active_profile_id');

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // JOIN widget ON widget.id = profile2widget.widgetid
        $user_id = Yii::$app->user->id; // Get logged-in user ID
        $widgetids = Yii::$app->db->createCommand(" 
                SELECT * 
                FROM profile2widget
                JOIN role2profile ON role2profile.profileid = profile2widget.profileid
                JOIN role ON role.roleid = role2profile.roleid
                JOIN user2role ON user2role.roleid = role.roleid
                JOIN user ON user.id = user2role.userid
                WHERE user.id = :uid
                and user2role.roleid=:roleid
            ")
            ->bindValue(':uid', $user_id)
            ->bindValue(':roleid', $activeroleId)
            ->queryOne(); // Use queryAll() if expecting multiple records
        if (isset($widgetids['widgetid'])) {
            $idsString = $widgetids['widgetid']; // e.g., '1,2'
            $ids = array_map('intval', explode(',', $idsString));
            $placeholders = [];
            $params = [];

            foreach ($ids as $index => $id) {
                $key = ':id' . $index;
                $placeholders[] = $key;
                $params[$key] = $id;
            }
            $sql = "SELECT * FROM widget WHERE id IN (" . implode(',', $placeholders) . ") AND deleted = 0 AND is_active = 1 AND view = 1";
            $allwidgets = Yii::$app->db->createCommand($sql)
                ->bindValues($params)
                ->queryAll();
            if (!empty($allwidgets)) {
                return $allwidgets;
            }
        } else {
            return "";
        }
    }

    //working fucntion without add common question rules on date 17-12-2025
    /* protected function getWidgetData($name)
    {
        $name = trim($name);
        $uid = Yii::$app->user->id;
        $data = '';
        $modulename = Yii::$app->request->get('modulename');
        $hasadminpower = $this->isAdmin($modulename);
        if ($name == 'count_first_approval_pending_amount' || $name == 'count_second_approval_pending_amount') {
            $sql = "SELECT SUM(total_invoice_amount) FROM ";
        } elseif ($name == 'count_deal_won_in_last_7_day_amount') {
            $sql = "SELECT SUM(total_sourcing_deal_amount) FROM ";
        } 
        elseif ($name == 'my_opportunities_amount') {
            $sql = "SELECT SUM(total_oppr_amount_tax_include) FROM ";
        } 
        else {
            $sql = "SELECT COUNT(*) FROM ";
        }
        $where = " AND deleted = 0 ";

        $connection = Yii::$app->db;

        //sales dashboard
        if ($name == "count_meeting_done_in_last_7_day") {
            $sql .= "meeting_information 
                 WHERE (`from` BETWEEN (CURDATE() - INTERVAL 7 DAY) AND CURDATE() )";
                //  AND (
                //      FIND_IN_SET(:userId, internal_participants)
                //      OR FIND_IN_SET(:userId, external_participants)
                //  )";
        } else if ($name == "count_meeting_planned_for_next_7_day") {
            $sql .= "meeting_information 
                 WHERE (`from` BETWEEN CURDATE() AND (CURDATE() + INTERVAL 7 DAY))";
                //  AND (
                //      FIND_IN_SET(:userId, internal_participants)
                //      OR FIND_IN_SET(:userId, external_participants)
                //  )";
        } else if ($name == "count_call_done_in_last_7_day") {
            $sql .= "call_information 
                 WHERE outgoing_call_status = 2 
                 AND (call_start_time BETWEEN (CURDATE() - INTERVAL 7 DAY) AND CURDATE()  )";
                //  AND call_start_time BETWEEN (CURDATE() - INTERVAL 7 DAY) AND CURDATE()";
                //  AND ownerid = :userId";
        } else if ($name == "count_call_sch_in_last_7_day") {
            // outgoing_call_status = 1 =Scheduled
            $sql .= "call_information 
                 WHERE outgoing_call_status = 1 
                 AND (call_start_time BETWEEN CURDATE() AND (CURDATE() + INTERVAL 7 DAY))";
                //  AND call_start_time BETWEEN (CURDATE() + INTERVAL 7 DAY) AND CURDATE()";
                //  AND ownerid = :userId";
        } else if ($name == "count_quote_send_in_last_7_days") {
            // quote_stage = 5 quote created
            $sql .= "quotes 
                    WHERE quote_stage = 5 
                    AND (quote_creation_date BETWEEN (CURDATE() - INTERVAL 7 DAY) AND CURDATE() )";
                    // AND ownerid = :userId";
        } else if ($name == "count_quote_expire_in_last_7_days") {
            $sql .= "quotes 
                    WHERE (expiry_date BETWEEN CURDATE() AND (CURDATE() + INTERVAL 7 DAY))";
                    // AND ownerid = :userId";
        } else if ($name == "count_first_approval_pending") {
            // stage_id =2 first approval pending 
            $sql .= "payments 
                    WHERE stage = 2";
                    // AND ownerid = :userId";
        } else if ($name == "count_second_approval_pending") {
            // stage_id =3 second approval pending
            $sql .= "payments 
                    WHERE stage = 3";
                    //  AND ownerid = :userId";
        } else if ($name == "count_first_approval_pending_amount") {
            // stage_id =2 first approval pending
            $sql .= "payments 
                    WHERE stage = 2";
                    // AND ownerid = :userId ";
        } else if ($name == "count_second_approval_pending_amount") {
            // stage_id =3 second approval pending
            $sql .= "payments 
                    WHERE stage = 3";
                    //  AND ownerid = :userId";
        } else if ($name == "count_deal_won_in_last_7_day") {
            // 14 WON
            $sql .= "sourcingdeal 
                    WHERE (stage = 14 AND `closing_date` >= (CURDATE() - INTERVAL 7 DAY))";
                    // AND ownerid = :userId ";
        } else if ($name == "count_deal_won_in_last_7_day_amount") {
            // 14 = WON
            $sql .= "sourcingdeal 
                    WHERE (stage = 14 AND `closing_date` >= (CURDATE() - INTERVAL 7 DAY))";
                    // AND ownerid = :userId";
        }
        //finanace  dashboard
        else if ($name == "total_count_for_payment_approval_pending") {
            $sql .= "payments 
                    WHERE stage IN (2,3)";
                    // AND ownerid = :userId ";
        } else if ($name == "total_count_for_payment_approved") {
            $sql .= "payments 
                    WHERE stage = 5";
                    // AND ownerid = :userId ";
        } else if ($name == "my_today_meetings") {
            $sql .= "meeting_information 
                 WHERE (DATE(`from`) = CURDATE())";
        } else if ($name == "my_today_calls") {
            $sql .= "call_information 
                 WHERE (DATE(`call_start_time`) = CURDATE())";
        } else if ($name == "my_today_tasks") {
            $sql .= "task_information 
                 WHERE (DATE(`due_date`) = CURDATE())";
        } else if ($name == "my_opportunities") {
            $sql .= "opportunity WHERE ";
                //  WHERE (creatorid = )";
        } 
        else if ($name == "my_opportunities_amount") {
            $sql .= "opportunity WHERE ";
                //  WHERE (creatorid = )";
        }
        else if ($name == "my_sourcingdeal") {
            $sql .= "sourcingdeal WHERE is_temp = 0 AND ";
                //  WHERE (creatorid = )";
        }
         if ($hasadminpower != 1){
                // if($name == "count_meeting_planned_for_next_7_day" || $name == "count_meeting_done_in_last_7_day")
                // {
                //         $sql .= "  AND (
                //         FIND_IN_SET(:userId, internal_participants)
                //         OR FIND_IN_SET(:userId, external_participants)
                //     ) OR ownerid = :userId ";
                // }
                // else
                // echo $name;
                if($name == "my_opportunities" || $name == "my_sourcingdeal" || $name == "my_opportunities_amount")
                    $sql .= " ( ownerid = :userId OR creatorid = :userId)";
                else
                    $sql .= "  AND ownerid = :userId ";
            }
        // sourcingdeal_total_sourcing_dealamount
        // echo $name."-->".$sql."====";
        if ($sql !== "SELECT COUNT(*) FROM ") {  
            if(($name == "my_opportunities" || $name == "my_sourcingdeal") && $hasadminpower == 1)
                $sql .= ' deleted = 0 '; // append 'AND deleted = 0' at the end 
            else{
                // if($name == "my_opportunities_amount")
                //     $sql .= ' deleted = 0 '; // append 'AND deleted = 0' at the end 
                // else
                    $sql .= $where; // append 'AND deleted = 0' at the end

            }
            // echo $sql;die;
            $command = $connection->createCommand($sql);
            if ($hasadminpower != 1) {
                $command->bindValue(':userId', $uid);
            }
            
            $count = $command->queryScalar();
            // echo $count."<br/>";
            if (
                $name == 'count_first_approval_pending_amount' ||
                $name == 'count_second_approval_pending_amount' ||
                $name == 'count_deal_won_in_last_7_day_amount'  
                // $name == 'my_opportunities_amount'
            ) {
                return (isset($count)) ? $count . " L" : "0 L";
            } else {
                return isset($count) ? $count : 0;
            }
        } else {
            echo "query building fail";
        }
    } */

    public function getWidgetData($name)
    {
        $name = trim($name);
        $uid = Yii::$app->user->id;
        $data = '';
        $modulename =(new \yii\db\Query())
            ->select('modulename')
            ->from('widget')
            ->where(['name' => $name])
            ->scalar();
        $TableName = '';
        $hasadminpower = $this->isAdmin($modulename);
        $connection = Yii::$app->db;
        $sql = $where = "";
        if ($name == 'count_first_approval_pending_amount' || $name == 'count_second_approval_pending_amount') {
            $ColumnKey = " SUM(total_invoice_amount) FROM ";
        } 
        elseif ($name == 'count_deal_won_in_last_7_day_amount') {
            $ColumnKey = " SUM(total_sourcing_deal_amount) FROM ";
        } 
        elseif ($name == 'my_opportunities_amount') {
            $ColumnKey = " SUM(total_oppr_amount_tax_include) FROM ";
        } 
        else {
            $ColumnKey = " COUNT(*) FROM ";
        }

        //sales dashboard
         if ($name == "count_meeting_done_in_last_7_day") {
            $TableName ='meeting_information';
            $where .=" ( $TableName.`from` BETWEEN (CURDATE() - INTERVAL 7 DAY) AND CURDATE() )";
        
        } else if ($name == "count_meeting_planned_for_next_7_day") {
            $TableName ='meeting_information';
            $where .=" ( $TableName.`from` BETWEEN CURDATE() AND (CURDATE() + INTERVAL 7 DAY) )";

        } else if ($name == "count_call_done_in_last_7_day") {
            $TableName ='call_information';
            $where .=" $TableName.outgoing_call_status = 2 
                 AND ($TableName.call_start_time BETWEEN (CURDATE() - INTERVAL 7 DAY) AND CURDATE()  )";

        } else if ($name == "count_call_sch_in_last_7_day") {
            $TableName ='call_information';
            $where .=" $TableName.outgoing_call_status = 2 
                 AND ($TableName.call_start_time BETWEEN CURDATE() AND (CURDATE() + INTERVAL 7 DAY))";

        } else if ($name == "count_quote_send_in_last_7_days") {
            // quote_stage = 5 quote created
            $TableName ='quotes';
            $where .="  $TableName.quote_stage = 5 
                    AND ( $TableName.quote_creation_date BETWEEN (CURDATE() - INTERVAL 7 DAY) AND CURDATE() )";
        
        } else if ($name == "count_quote_expire_in_last_7_days") {
            $TableName ='quotes';
            $where .=" ( $TableName.expiry_date BETWEEN CURDATE() AND (CURDATE() + INTERVAL 7 DAY))"; 
        } else if ($name == "count_first_approval_pending") {
            // stage_id =2 first approval pending
            $TableName ='payments'; 
            $where .= " $TableName.stage = 2 ";
        } else if ($name == "count_second_approval_pending") {
            // stage_id =3 second approval pending
            $TableName ='payments'; 
            $where .= " $TableName.stage = 3";
        } else if ($name == "count_first_approval_pending_amount") {
            // stage_id =2 first approval pending
             $TableName ='payments';
             $where .= " $TableName.stage = 2";
        } else if ($name == "count_second_approval_pending_amount") {
            // stage_id =3 second approval pending
             $TableName ='payments';
             $where .= " $TableName.stage = 3";
        } else if ($name == "count_deal_won_in_last_7_day") {
            // 14 WON
            $TableName ='sourcingdeal';
            $where .=" ( $TableName.stage = 14 AND  $TableName.`closing_date` >= (CURDATE() - INTERVAL 7 DAY))";
        } else if ($name == "count_deal_won_in_last_7_day_amount") {
            // 14 = WON
            $TableName ='sourcingdeal';
            $where .=" ( $TableName.stage = 14 AND $TableName.`closing_date` >= (CURDATE() - INTERVAL 7 DAY))";
        }
        //finanace  dashboard
        else if ($name == "total_count_for_payment_approval_pending") {
            $TableName ='payments';
            $where .= "$TableName.stage IN (2,3)";
        } else if ($name == "total_count_for_payment_approved") {
            $TableName ='payments';
            $where .= "$TableName.stage = 5";
        } else if ($name == "my_today_meetings") {
            $TableName ='meeting_information';
            $where .= " (DATE($TableName.`from`) = CURDATE())";
        } else if ($name == "my_today_calls") {
            $TableName .= "call_information";
            $where .= " (DATE($TableName.`call_start_time`) = CURDATE())";
        } else if ($name == "my_today_tasks") {
            $TableName .= "task_information";
            $where .= " (DATE($TableName.`due_date`) = CURDATE())";
        } else if ($name == "my_opportunities") {
            $TableName .= "opportunity";
            $where .= "";
        } else if ($name == "my_opportunities_amount") {
            $TableName .="opportunity";
            $where .= "";
        }
        else if ($name == "my_sourcingdeal") {
            $TableName .="sourcingdeal";
            $where .= "$TableName.is_temp = 0";
        }
        if($TableName != ''){
            
            $ColumnKey .= $TableName; 
            $OrderBy = $this->getTableKey($TableName);
            $sql = $this->getCommonAccessCond($modulename,$TableName,$sql, $ColumnKey, $join= '',$OrderBy, $SortOrder= '', $groupby= '', $where);
            // echo $sql;die;
            $command = $connection->createCommand($sql);
            $count = $command->queryScalar();
        }
        else
        {
            echo "Query not generated";
        }
        
        // echo $count."<br/>";
        if (
            $name == 'count_first_approval_pending_amount' ||
            $name == 'count_second_approval_pending_amount' ||
            $name == 'count_deal_won_in_last_7_day_amount'  
        ) {
            return (isset($count)) ? $count . " L" : "0 L";
        } else {
            return isset($count) ? $count : 0;
        }        
    }

    /*public function actionGetchartdata()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $url = Yii::$app->request->get('widgetUrl');
        // $modulename = Yii::$app->request->get('modulename');
        $modulename = (new \yii\db\Query())
            ->select('modulename')
            ->from('widget')
            ->where(['widgeturl' => $url])
            ->scalar();
        $uid = Yii::$app->user->id;
        if(isset($modulename) && $modulename != '')
            $hasadminpower = $this->isAdmin($modulename);
        else 
            $hasadminpower = 0;
        if ($url == 'count_total_rc_acc_vs_non_rc_acc.php') {
            $rcCount = (new \yii\db\Query())
                ->from('vendor_account')
                ->where(['billing_type' => 1])
                ->count();

            $nonRcCount = (new \yii\db\Query())
                ->from('vendor_account')
                ->where(['billing_type' => 2])
                ->count();

            return [
                'rc' => $rcCount,
                'non_rc' => $nonRcCount
            ];
        } else if ($url == 'target-achivment.php') {
            // $data = (new \yii\db\Query())
            //     ->select([
            //         'targets' => 'SUM(user_targets.targets)',
            //         'total_achievement' => 'SUM(sd.total_sourcing_deal_amount)'
            //     ])
            //     ->from('user_targets')
            //     ->leftJoin('sourcingdeal sd', 'sd.ownerid = user_targets.userid');
            // if ($hasadminpower != 1)
            //     $data->where(['user_targets.userid' => $uid]);
            // $data->one();


                if ($hasadminpower != 1) {
                    // Non-admin = filter by userid
                    $sql = "
                        SELECT 
                            (SELECT SUM(targets) 
                            FROM user_targets 
                            WHERE userid = :userid) AS targets,

                            (SELECT SUM(total_sourcing_deal_amount)
                            FROM sourcingdeal
                            WHERE ownerid = :userid AND stage = 14) AS total_achievement
                    ";
                    
                    $params = [':userid' => $uid];
                } else {
                    // Admin = no userid filter
                    $sql = "
                        SELECT 
                            (SELECT SUM(targets) 
                            FROM user_targets) AS targets,

                            (SELECT SUM(total_sourcing_deal_amount)
                            FROM sourcingdeal
                            WHERE stage = 14) AS total_achievement
                    ";
                    $params = []; 
                }

                // Execute
                $data = Yii::$app->db
                    ->createCommand($sql, $params)
                    ->queryOne();
                // echo "<pre>";print_r($data);die;
            return [
                'target' => (int)($data['targets'] ?? 0),
                'achievement' => (int)($data['total_achievement'] ?? 0)
            ];
        } else if ($url == 'deal_won_in_last_7_days_amount.php') {
            // $sql = "SELECT 
            //         DATE_FORMAT(sd.createdtime, '%b') AS month_name, 
            //         ROUND(u.targets / 12) AS monthly_target, 
            //         u.targets AS yearly_target, 
            //         SUM(sd.total_sourcing_deal_amount) AS user_achievement 
            //     FROM sourcingdeal sd 
            //     JOIN user_targets u ON u.userid = sd.ownerid 
            //     JOIN fyear fy ON fy.yearid = u.year 
            //     WHERE  fy.is_active = 1 ";
            // if ($hasadminpower != 1)
            //     $sql .= "AND sd.ownerid = :uid";
            // $sql .= " GROUP BY MONTH(sd.createdtime), u.targets 
            //     ORDER BY MONTH(sd.createdtime) ASC";

            $sql='';
                if ($hasadminpower != 1) {
                   $sql = "
                        SELECT
                            DATE_FORMAT(sd.createdtime, '%b') AS month_name,
                            COALESCE(t.yearly_target, 0) AS yearly_target,
                            ROUND(COALESCE(t.yearly_target, 0) / 12) AS monthly_target,
                            SUM(sd.total_sourcing_deal_amount) AS user_achievement
                        FROM sourcingdeal sd
                        -- sum this user's targets for the active financial year (only once)
                        LEFT JOIN (
                            SELECT ut.userid, SUM(ut.targets) AS yearly_target
                            FROM user_targets ut
                            JOIN fyear fy ON fy.yearid = ut.year AND fy.is_active = 1
                            WHERE ut.userid = :uid
                            GROUP BY ut.userid
                        ) t ON t.userid = sd.ownerid
                        WHERE sd.ownerid = :uid
                        AND sd.createdtime IS NOT NULL
                        AND sd.stage = 14    -- keep only won deals; remove this line if not required
                        GROUP BY MONTH(sd.createdtime)
                        ORDER BY MONTH(sd.createdtime)
                        ";
                }
                else
                {
                    $sql = "
                        SELECT
                            DATE_FORMAT(sd.createdtime, '%b') AS month_name,
                            COALESCE(allt.yearly_target_sum, 0) AS yearly_target,
                            ROUND(COALESCE(allt.yearly_target_sum, 0) / 12) AS monthly_target,
                            SUM(sd.total_sourcing_deal_amount) AS user_achievement
                        FROM sourcingdeal sd
                        -- compute sum of yearly targets across all users (active financial year)
                        LEFT JOIN (
                            SELECT SUM(ut.targets) AS yearly_target_sum
                            FROM user_targets ut
                            JOIN fyear fy ON fy.yearid = ut.year AND fy.is_active = 1
                        ) allt ON 1=1
                        WHERE sd.createdtime IS NOT NULL
                        AND sd.stage = 14    -- keep only won deals; remove if not required
                        GROUP BY MONTH(sd.createdtime)
                        ORDER BY MONTH(sd.createdtime)
                        ";
                }
                // echo $sql;die;

            if ($hasadminpower == 1) {
                $monthlyStats = Yii::$app->db->createCommand($sql)
                    ->queryAll();
            } else {
                $monthlyStats = Yii::$app->db->createCommand($sql)
                    ->bindValue(':uid', $uid)
                    ->queryAll();
            }

            $allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            $targetData = [];
            $achievementData = [];

            foreach ($allMonths as $month) {
                $match = null;
                foreach ($monthlyStats as $row) {
                    if (trim($row['month_name']) === $month) {
                        $match = $row;
                        break;
                    }
                }

                if ($match) {
                    $targetData[] = (int) $match['monthly_target'];
                    $achievementData[] = isset($match['user_achievement']) ? (float) $match['user_achievement'] : 0;
                } else {
                    $targetData[] = 0;
                    $achievementData[] = 0;
                }
            }

            return [
                'targetData' => $targetData,
                'achievementData' => $achievementData,
                'allMonths' => $allMonths,
            ];
        } else if ($url == "payment_approval_pending_second_stage.php") {
            $sql = "SELECT  
                        account_name, COUNT(*) AS total_count, 
                        SUM(total_invoice_amount) AS total_amount, 
                        SUM(CASE WHEN DATEDIFF(CURDATE(), modifiedtime) <= 2 THEN 1 ELSE 0 END) AS age_0_2_days, 
                        SUM(CASE WHEN DATEDIFF(CURDATE(), modifiedtime) > 2 THEN 1 ELSE 0 END) AS age_3_plus_days 
                    FROM payments WHERE stage = 3";
            if ($hasadminpower != 1)
                $sql .= " AND ownerid = :uid ";
            $sql .= " AND deleted= 0 GROUP BY account_name";
            if ($hasadminpower == 1) {
                $monthlyStats = Yii::$app->db->createCommand($sql)
                    ->queryAll();
            } else {
                $monthlyStats = Yii::$app->db->createCommand($sql)
                    ->bindValue(':uid', $uid)
                    ->queryAll();
            }

            $categories = [];
            $data0to2 = [];
            $data3plus = [];

            foreach ($monthlyStats as $row) {
                $categories[] = $row['account_name'];
                $data0to2[] = (int) $row['age_0_2_days'];
                $data3plus[] = (int) $row['age_3_plus_days'];
            }

            return [
                'series' => [
                    [
                        'name' => '0-2 Days',
                        'data' => $data0to2,
                        'color' => '#FF9061'
                    ],
                    [
                        'name' => '3+ Days',
                        'data' => $data3plus,
                        'color' => '#DF4E5C'
                    ]
                ],
                'categories' => $categories
            ];

        } else if ($url == "lot_pending_for_segregation.php") {
            $sql = "
                SELECT 
                    SUM(CASE WHEN DATEDIFF(CURDATE(), modifiedtime) <= 3 THEN 1 ELSE 0 END) AS age_0_3_days,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), modifiedtime) > 3 THEN 1 ELSE 0 END) AS age_3_plus_days
                FROM grn_asset_detail gad
                INNER JOIN grn g ON g.grn_id = gad.grn_id
                WHERE gad.grn_status = 1";
            if ($hasadminpower != 1)
                $sql .= " AND g.ownerid = :uid ";
            $sql .= " AND gad.deleted= 0";
            if ($hasadminpower == 1) {
                $result = Yii::$app->db->createCommand($sql)
                    ->queryOne();
            } else {
                $result = Yii::$app->db->createCommand($sql)
                    ->bindValue(':uid', $uid)
                    ->queryOne();
            }
            // $result = Yii::$app->db->createCommand($sql)->queryOne();

            return [
                'labels' => ['0–3', '>3'],
                'values' => [
                    (int) $result['age_0_3_days'],
                    (int) $result['age_3_plus_days']
                ]
            ];
        } else if (
            $url == "lot_pending_for_tagging.php" || $url == "lot_pending_for_cleaning.php"
            || $url == "lot_pending_for_iqc.php" || $url == "lot_pending_for_sticker_removal.php"
        ) {
            $sql = "
                SELECT 
                    SUM(CASE WHEN DATEDIFF(CURDATE(), modifiedtime) <= 1 THEN 1 ELSE 0 END) AS age_0_1_days,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), modifiedtime) BETWEEN 2 AND 3 THEN 1 ELSE 0 END) AS age_2_3_days,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), modifiedtime) > 3 THEN 1 ELSE 0 END) AS age_3_plus_days
                FROM inventory 
                WHERE ";
            if ($url == "lot_pending_for_tagging.php")
                $sql .= "`status` = 2";
            else if ($url == "lot_pending_for_cleaning.php")
                $sql .= "status = 4";
            else if ($url == "lot_pending_for_iqc.php")
                $sql .= "status = 5";
            else if ($url == "lot_pending_for_sticker_removal.php")
                $sql .= "status = 3";
            if ($hasadminpower != 1)
                $sql .= " AND ownerid = :uid ";
            $sql .= " AND deleted= 0";
            if ($hasadminpower == 1) {
                $result = Yii::$app->db->createCommand($sql)
                    ->queryOne();
            } else {
                $result = Yii::$app->db->createCommand($sql)
                    ->bindValue(':uid', $uid)
                    ->queryOne();
            }
            // $result = Yii::$app->db->createCommand($sql)->queryOne();

            return [
                'labels' => ['0–1', '2–3', '>3'],
                'values' => [
                    (int) $result['age_0_1_days'],
                    (int) $result['age_2_3_days'],
                    (int) $result['age_3_plus_days']
                ]
            ];

        }


    } */

    public function actionGetchartdata()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $url = Yii::$app->request->get('widgetUrl');
        // $modulename = Yii::$app->request->get('modulename');
        $modulename = (new \yii\db\Query())
            ->select('modulename')
            ->from('widget')
            ->where(['widgeturl' => $url])
            ->scalar();
        $connection = Yii::$app->db;
        if ($url == 'count_total_rc_acc_vs_non_rc_acc.php') {
            $rcCount = (new \yii\db\Query())
                ->from('vendor_account')
                ->where(['billing_type' => 1])
                ->count();

            $nonRcCount = (new \yii\db\Query())
                ->from('vendor_account')
                ->where(['billing_type' => 2])
                ->count();

            $TableName = 'vendor_account';

            $rcwhere = 'billing_type = 1';
            $nonrcwhere = 'billing_type = 2';

            $ColumnKey = 'count(*) FROM '.$TableName ; 

            $OrderBy = $this->getTableKey($TableName);

            $rcsql = $this->getCommonAccessCond($modulename,$TableName,$sql= '', $ColumnKey, $join= '',$OrderBy, $SortOrder= '', $groupby= '', $rcwhere);
            $nonrcsql = $this->getCommonAccessCond($modulename,$TableName,$sql= '', $ColumnKey, $join= '',$OrderBy, $SortOrder= '', $groupby= '', $nonrcwhere);
            
            $rccommand = $connection->createCommand($rcsql);
            $nonrccommand = $connection->createCommand($nonrcsql);
            
            $rcCount = $rccommand->queryScalar();
            $nonRcCount = $nonrccommand->queryScalar();

            return [
                'rc' => $rcCount,
                'non_rc' => $nonRcCount
            ];
        } else if ($url == 'target-achivment.php') {
            $total_achievementwhere = ' stage = 14';

            $targetTableName = 'user_targets';
            $total_achievementTableName = 'sourcingdeal';

            $targetColumnKey = 'SUM(targets) AS targets FROM '.$targetTableName; 
            $total_achievementColumnKey = 'SUM(total_sourcing_deal_amount) AS total_achievement FROM '.$total_achievementTableName; 
            
            
            $targetOrderBy = $this->getTableKey($targetTableName);
            $total_achievementOrderBy = $this->getTableKey($total_achievementTableName);

            $targetsql = $this->getCommonAccessCond($modulename,$targetTableName,$sql= '', $targetColumnKey, $join= '',$targetOrderBy, $SortOrder= '', $groupby= '', $where= '');
            $total_achievementsql = $this->getCommonAccessCond($modulename,$total_achievementTableName,$sql= '', $total_achievementColumnKey, $join= '',$total_achievementOrderBy, $SortOrder= '', $groupby= '', $total_achievementwhere);
            //this need because this user_targets table dose not have deleted column
            // $targetsql = str_replace('inner join user as owner on (owner.id=user_targets.ownerid) where user_targets.deleted=0', '', $targetsql);
            // echo $targetsql;die;
            $targetcommand = $connection->createCommand($targetsql);
            $total_achievementcommand = $connection->createCommand($total_achievementsql);
            
            $targetCount = $targetcommand->queryScalar();
            $total_achievementCount = $total_achievementcommand->queryScalar();
            return [
                'target' => (int)($targetCount ?? 0),
                'achievement' => (int)($total_achievementCount ?? 0)
            ];
        } else if ($url == 'deal_won_in_last_7_days_amount.php') {

            $TableName = 'sourcingdeal';
             
            $where = " $TableName.createdtime IS NOT NULL AND $TableName.stage = 14";

            $ColumnKey = " DATE_FORMAT($TableName.createdtime, '%b') AS month_name,
                             COALESCE(allt.yearly_target_sum, 0) AS yearly_target,
                             ROUND(COALESCE(allt.yearly_target_sum, 0) / 12) AS monthly_target,
                             SUM($TableName.total_sourcing_deal_amount) AS user_achievement FROM $TableName"; 
            
            $OrderBy = " MONTH($TableName.createdtime) ";
            $groupby = "GROUP BY MONTH($TableName.createdtime) ";

            $join = "LEFT JOIN (
                             SELECT SUM(ut.targets) AS yearly_target_sum
                             FROM user_targets ut
                             JOIN fyear fy ON fy.yearid = ut.year AND fy.is_active = 1
                         ) allt ON 1=1";

            $sql = $this->getCommonAccessCond($modulename,$TableName,$sql= '', $ColumnKey, $join,$OrderBy, $SortOrder= '', $groupby, $where);
            // echo $sql;die;
            $command = $connection->createCommand($sql);
            
            $monthlyStats = $command->queryAll();

            $allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            $targetData = [];
            $achievementData = [];

            foreach ($allMonths as $month) {
                $match = null;
                foreach ($monthlyStats as $row) {
                    if (trim($row['month_name']) === $month) {
                        $match = $row;
                        break;
                    }
                }

                if ($match) {
                    $targetData[] = (int) $match['monthly_target'];
                    $achievementData[] = isset($match['user_achievement']) ? (float) $match['user_achievement'] : 0;
                } else {
                    $targetData[] = 0;
                    $achievementData[] = 0;
                }
            }

            return [
                'targetData' => $targetData,
                'achievementData' => $achievementData,
                'allMonths' => $allMonths,
            ];
        } else if ($url == "payment_approval_pending_second_stage.php") {

            $TableName = 'payments';
             
            $where = " $TableName.stage = 3";

            $ColumnKey = " account_name, COUNT(*) AS total_count, 
                        SUM(total_invoice_amount) AS total_amount, 
                        SUM(CASE WHEN DATEDIFF(CURDATE(), $TableName.modifiedtime) <= 2 THEN 1 ELSE 0 END) AS age_0_2_days, 
                        SUM(CASE WHEN DATEDIFF(CURDATE(), $TableName.modifiedtime) > 2 THEN 1 ELSE 0 END) AS age_3_plus_days FROM $TableName"; 
            
            $groupby = " GROUP BY $TableName.account_name ";

            $OrderBy = $this->getTableKey($TableName);
            
            $sql = $this->getCommonAccessCond($modulename,$TableName,$sql= '', $ColumnKey, $join='',$OrderBy, $SortOrder= '', $groupby, $where);
            
            $command = $connection->createCommand($sql);
            
            $monthlyStats = $command->queryAll();

            $categories = [];
            $data0to2 = [];
            $data3plus = [];

            foreach ($monthlyStats as $row) {
                $categories[] = $row['account_name'];
                $data0to2[] = (int) $row['age_0_2_days'];
                $data3plus[] = (int) $row['age_3_plus_days'];
            }

            return [
                'series' => [
                    [
                        'name' => '0-2 Days',
                        'data' => $data0to2,
                        'color' => '#FF9061'
                    ],
                    [
                        'name' => '3+ Days',
                        'data' => $data3plus,
                        'color' => '#DF4E5C'
                    ]
                ],
                'categories' => $categories
            ];

        } else if ($url == "lot_pending_for_segregation.php") {
            $TableName = 'grn_asset_detail';
             
            $where = " $TableName.grn_status = 1";

            $ColumnKey = "  SUM(CASE WHEN DATEDIFF(CURDATE(), g.modifiedtime) <= 3 THEN 1 ELSE 0 END) AS age_0_3_days,SUM(CASE WHEN DATEDIFF(CURDATE(), g.modifiedtime) > 3 THEN 1 ELSE 0 END) AS age_3_plus_days FROM $TableName"; 

            $join = "INNER JOIN grn g ON g.grn_id = $TableName.grn_id";

            $OrderBy = $this->getTableKey($TableName);
            
            $sql = $this->getCommonAccessCond($modulename,$TableName,$sql= '', $ColumnKey, $join ,$OrderBy, $SortOrder= '', $groupby= '', $where);
            //this need because this user_targets table dose not have deleted column
            // $sql = str_replace('inner join user as owner on (owner.id=grn_asset_detail.ownerid)', '', $sql);
            
            // echo $sql;die;
            $command = $connection->createCommand($sql);
            
            $result = $command->queryOne();

            // $result = Yii::$app->db->createCommand($sql)->queryOne();

            return [
                'labels' => ['0–3', '>3'],
                'values' => [
                    (int) $result['age_0_3_days'],
                    (int) $result['age_3_plus_days']
                ]
            ];
        } else if (
            $url == "lot_pending_for_tagging.php" || $url == "lot_pending_for_cleaning.php"
            || $url == "lot_pending_for_iqc.php" || $url == "lot_pending_for_sticker_removal.php"
        ) {
            $TableName = 'inventory';
             
            if ($url == "lot_pending_for_tagging.php")
                $where = " $TableName.status = 2";
            else if ($url == "lot_pending_for_cleaning.php")
                $where = " $TableName.status = 4";
            else if ($url == "lot_pending_for_iqc.php")
                $where = " $TableName.status = 5";
            else if ($url == "lot_pending_for_sticker_removal.php")
                $where = " $TableName.status = 3";

            $ColumnKey = "SUM(CASE WHEN DATEDIFF(CURDATE(), $TableName.modifiedtime) <= 1 THEN 1 ELSE 0 END) AS age_0_1_days,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), $TableName.modifiedtime) BETWEEN 2 AND 3 THEN 1 ELSE 0 END) AS age_2_3_days,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), $TableName.modifiedtime) > 3 THEN 1 ELSE 0 END) AS age_3_plus_days
                FROM $TableName"; 

            $OrderBy = $this->getTableKey($TableName);
            
            $sql = $this->getCommonAccessCond($modulename,$TableName,$sql= '', $ColumnKey, $join='' ,$OrderBy, $SortOrder= '', $groupby= '', $where);
            //this need because this user_targets table dose not have deleted column
            // $sql = str_replace('inner join user as owner on (owner.id=grn_asset_detail.ownerid)', '', $sql);
            
            // echo $sql;die;
            $command = $connection->createCommand($sql);
            
            $result = $command->queryOne();

            return [
                'labels' => ['0–1', '2–3', '>3'],
                'values' => [
                    (int) $result['age_0_1_days'],
                    (int) $result['age_2_3_days'],
                    (int) $result['age_3_plus_days']
                ]
            ];

        }
    }

    protected function getWidgetBodyData($name,$isAdmin,$modulename)
    {
         $name = trim($name);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $url = Yii::$app->request->get('widgetUrl');
        $connection = Yii::$app->db;
        if ($name == 'payment_approved_accwise_agewise') {           

            $TableName = 'rep_payment_approve_stage_log';

            $ColumnKey = " payments.account_name AS account,payments.account_id as acc_id,
                        ROUND(SUM(CASE WHEN DATEDIFF(CURDATE(), $TableName.createdtime) BETWEEN 0 AND 7 THEN $TableName.total_invoice_amount ELSE 0 END), 1) AS '0-7',
                        ROUND(SUM(CASE WHEN DATEDIFF(CURDATE(), $TableName.createdtime) BETWEEN 8 AND 15 THEN $TableName.total_invoice_amount ELSE 0 END), 1) AS '8-15',
                        ROUND(SUM(CASE WHEN DATEDIFF(CURDATE(), $TableName.createdtime) > 15 THEN $TableName.total_invoice_amount ELSE 0 END), 1) AS '>15' FROM $TableName"; 
            
            $join = "JOIN payments payments ON $TableName.payment_id = payments.payments_id";
            //this join require because need to check for org or oem user
            $join .= " LEFT JOIN vendor_account a ON a.vendoraccid = payments.account_id";
            $OrderBy = 'payments.account_name';
            $groupby = "GROUP BY payments.account_name ";
            $sql = $this->getCommonAccessCond($modulename,$TableName,$sql= '', $ColumnKey, $join,$OrderBy, $SortOrder= '', $groupby, $where='');

            // $sql = str_replace('inner join user as owner on (owner.id=rep_payment_approve_stage_log.ownerid) where rep_payment_approve_stage_log.deleted=0', '', $sql);
            // echo $sql;die;
            $command = $connection->createCommand($sql);
            
            $result = $command->queryAll();

            return $result;
        } else if ($name == 'payment_approved_with_clientname_count_and_amount') {           
            $TableName = 'payments';
            $where = "$TableName.stage = 5";
            $ColumnKey = "account_name,account_id as acc_id,
                            -- 0–10 days 
                            SUM(CASE WHEN DATEDIFF(CURDATE(), $TableName.modifiedtime) BETWEEN 0 AND 10 THEN 1 ELSE 0 END) AS day_0_10_count, 
                            SUM(CASE WHEN DATEDIFF(CURDATE(), $TableName.modifiedtime) BETWEEN 0 AND 10 THEN total_invoice_amount ELSE 0 END) AS day_0_10_amount,
                            -- 11–15 days 
                            SUM(CASE WHEN DATEDIFF(CURDATE(), $TableName.modifiedtime) BETWEEN 11 AND 15 THEN 1 ELSE 0 END) AS day_11_15_count, 
                            SUM(CASE WHEN DATEDIFF(CURDATE(), $TableName.modifiedtime) BETWEEN 11 AND 15 THEN total_invoice_amount ELSE 0 END) AS day_11_15_amount, 
                            -- >15 days 
                            SUM(CASE WHEN DATEDIFF(CURDATE(), $TableName.modifiedtime) > 15 THEN 1 ELSE 0 END) AS day_15_plus_count, 
                            SUM(CASE WHEN DATEDIFF(CURDATE(), $TableName.modifiedtime) > 15 THEN total_invoice_amount ELSE 0 END) AS day_15_plus_amount, 
                            -- Totals 
                            COUNT(*) AS total_count, SUM(total_invoice_amount) AS total_amount FROM $TableName"; 
            $OrderBy = "$TableName.account_name";
            $groupby = "GROUP BY $TableName.account_name";
            $sql = $this->getCommonAccessCond($modulename,$TableName,$sql= '', $ColumnKey, $join='',$OrderBy, $SortOrder= '', $groupby, $where);

           $command = $connection->createCommand($sql);
            
            $result = $command->queryAll();

            return $result;
        } else if ($name == 'sourcing_deal_stage_count_againg') { 
            $TableName = 'rep_soucingdeal_stage_log';
            $where = "$TableName.updatetime IS NULL
              AND sourcingdeal.deleted = 0
              AND sourcingdeal.is_temp = 0";
            //   if($isAdmin != 1)
            //     $where .= " AND $TableName.creatorid =".Yii::$app->user->id;
            $ColumnKey = "$TableName.stage_id,st.stage_value,
                SUM(CASE WHEN DATEDIFF(CURDATE(), DATE($TableName.createdtime)) BETWEEN 0 AND 3 THEN 1 ELSE 0 END) AS `0-3 Days`,
                SUM(CASE WHEN DATEDIFF(CURDATE(), DATE($TableName.createdtime)) BETWEEN 4 AND 7 THEN 1 ELSE 0 END) AS `4-7 Days`,
                SUM(CASE WHEN DATEDIFF(CURDATE(), DATE($TableName.createdtime)) BETWEEN 8 AND 15 THEN 1 ELSE 0 END) AS `8-15 Days`,
                SUM(CASE WHEN DATEDIFF(CURDATE(), DATE($TableName.createdtime)) BETWEEN 16 AND 30 THEN 1 ELSE 0 END) AS `16-30 Days`,
                SUM(CASE WHEN DATEDIFF(CURDATE(), DATE($TableName.createdtime)) BETWEEN 31 AND 60 THEN 1 ELSE 0 END) AS `31-60 Days`,
                SUM(CASE WHEN DATEDIFF(CURDATE(), DATE($TableName.createdtime)) BETWEEN 61 AND 90 THEN 1 ELSE 0 END) AS `61-90 Days`,
                SUM(CASE WHEN DATEDIFF(CURDATE(), DATE($TableName.createdtime)) > 90 THEN 1 ELSE 0 END) AS `90+ Days` FROM $TableName"; 
            $join = "INNER JOIN sourcingdeal sourcingdeal ON sourcingdeal.sourcingdeal_id = $TableName.sourcingdeal_id
            LEFT JOIN sourcingdeal_stage st ON st.stage_id = $TableName.stage_id";
            //this join require because need to check for org or oem user
            $join .= " LEFT JOIN vendor_account a ON a.vendoraccid = sourcingdeal.vendor_account_name";
            $OrderBy = "$TableName.stage_id";
            $groupby = "GROUP BY $TableName.stage_id";
            $sql = $this->getCommonAccessCond($modulename,$TableName,$sql= '', $ColumnKey, $join,$OrderBy, $SortOrder= '', $groupby, $where);
            // echo $sql;die;
            $command = $connection->createCommand($sql);            
            $result = $command->queryAll();

            return $result;
        } else if ($name == 'top_20_product_name_qt_value_lying_inventory') { 
            // SELECT 
            //             rep_inventory_ageing.subcategory,
            //             prod_sub_catagory.sub_catagory_value,
            //             SUM(rep_inventory_ageing.qty) AS qty,
            //             SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 0 AND 15 THEN rep_inventory_ageing.qty ELSE 0 END) AS amt_0_15,
            //             SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 16 AND 30 THEN rep_inventory_ageing.qty ELSE 0 END) AS amt_16_30,
            //             SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 31 AND 60 THEN rep_inventory_ageing.qty ELSE 0 END) AS amt_31_60,
            //             SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) > 60 THEN rep_inventory_ageing.qty ELSE 0 END) AS amt_60_plus
            //         FROM rep_inventory_ageing
            //         LEFT JOIN prod_sub_catagory 
            //             ON prod_sub_catagory.sub_catagory_id = rep_inventory_ageing.subcategory
            //         GROUP BY rep_inventory_ageing.subcategory
            //         LIMIT 20
            $TableName = 'rep_inventory_ageing';
            $ColumnKey = "rep_inventory_ageing.subcategory,
                        prod_sub_catagory.sub_catagory_value,
                        SUM(rep_inventory_ageing.qty) AS qty,
                        SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 0 AND 15 THEN rep_inventory_ageing.qty ELSE 0 END) AS amt_0_15,
                        SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 16 AND 30 THEN rep_inventory_ageing.qty ELSE 0 END) AS amt_16_30,
                        SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 31 AND 60 THEN rep_inventory_ageing.qty ELSE 0 END) AS amt_31_60,
                        SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) > 60 THEN rep_inventory_ageing.qty ELSE 0 END) AS amt_60_plus
                    FROM  $TableName"; 
                    //no other join require for user because it show all no owner or creator in inventory also
            $join = "LEFT JOIN prod_sub_catagory 
                        ON prod_sub_catagory.sub_catagory_id = $TableName.subcategory";
            $groupby = "GROUP BY $TableName.subcategory";
            $OrderBy = $this->getTableKey($TableName)."  LIMIT 20";
            $sql = $this->getCommonAccessCond($modulename,$TableName,$sql= '', $ColumnKey, $join,$OrderBy, $SortOrder= '', $groupby, $where='');
            $command = $connection->createCommand($sql);
            
            $result = $command->queryAll();

            return $result;
        }    
    }

    protected function isAdmin($ModuleName)
    {
        $id = Yii::$app->user->id;
        $model = new AccessCheck();
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $hasadminpower = $model->hasadminpower($profile);
        return $hasadminpower;
    }


    public function actionForgotpassword()
    {
        $model = new Forgotpassword();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                // Success
                Yii::$app->session->setFlash('success', 'Please check your email to reset password.');
                return $this->refresh(); // refresh the page to show flash
            } else {
                // Failure
                Yii::$app->session->setFlash('error', 'Username not found or email is not registered.');
            }
        }

        $this->layout = "login";
        $siteSetting = SiteSetting::find()
            ->where(['active' => 1])->one();

        return $this->render('forgotpassword', [
            'model' => $model,
            'siteSetting' => $siteSetting,

        ]);
    }


    public function actionResetpassword($token = null)
    {
        $this->layout = 'login';

        if (empty($token)) {
            // no token in URL
            Yii::$app->session->setFlash('error', 'Password reset token is missing.');
            return $this->redirect(['site/forgot-password']);
        }

        $model = new Resetpassword($token);

        // validate token
        $model->validate(['token']);

        if ($model->hasErrors()) {
            return $this->render('resetpassword', ['model' => $model]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->resetPassword()) {
                Yii::$app->session->setFlash('success', 'Password reset successfully.');
                return $this->goHome();
            }
        }
        $siteSetting = SiteSetting::find()
            ->where(['active' => 1])->one();

        return $this->render('resetpassword', ['model' => $model,
            'siteSetting' => $siteSetting,
        
        ]);
    }
    public function actionCustomrror()
    {
        echo $message = Yii::$app->session->getFlash('error', 'An unknown error occurred.');
        die;
        return $this->render('customerror', ['message' => $message]);
    }


    public function actionGeneratepassword($password)
    {
        // Check if you're in a development environment
        if (YII_ENV_PROD) {
            throw new \yii\web\ForbiddenHttpException('Not allowed in production.');
        }

        $hash = Yii::$app->security->generatePasswordHash($password);
        return "<pre>Password: {$password}\nHash: {$hash}</pre>";
    }
    public function actionGetdynamicquery()
    {
        $fields = Field::find()
            ->select(['columnname', 'fieldlabel'])
            ->where([
                'tablename' => 'leadinformation',
            ])
            ->andWhere([
                'or',
                ['edit_view' => 1],
                ['create_view' => 1],
                ['detail_view' => 1],
            ])
            ->orderBy(['sequence' => SORT_ASC])
            ->asArray()
            ->all();

        $selectParts = [];
        foreach ($fields as $field) {
            $selectParts[] = "{$field['columnname']} AS `{$field['fieldlabel']}`";
        }

        $selectClause = implode(", ", $selectParts);

        // Now you can use this in a raw SQL query
        echo $sql = "SELECT $selectClause FROM leadinformation";

    }

    protected function getCommonAccessCond($ModuleName,$TableName,$Query, $ColumnKey, $join= '',$OrderBy= '', $SortOrder= '', $groupby= '', $where)
    {
        // added on 14 jan 2025 to open reference to all users   
        $isreference = 0;
        $recordlisting = new ListHireWidgets();
        //code added by ptpatel start from here on date 22-03-25
        $model = new AccessCheck();
        $id = Yii::$app->user->id;
        $tabs = $model->tabs($id, $ModuleName);
        $profile = $model->profile($id, $tabs, $ModuleName);
        $rolebasedrecord = $model->rolebasedrecord($id, $profile);            
        $modulepermission = $model->modulepermission($profile, $tabs);
        //code added by ptpatel end here on date 22-03-25
        // $Query = $recordlisting->listing($roleid, $modulepermission, $Query, $ColumnKey, $join, $OrderBy, $SortOrder, $TableName, $groupby, $isreference, $ModuleName, $where
        $Query = $recordlisting->listing($rolebasedrecord, $modulepermission, $Query, $ColumnKey, $join, $OrderBy, $SortOrder, $TableName, $groupby, $isreference, $ModuleName, $where);
        //code added by ptpatel on date 01-11-2025
        return $Query;
    }
    protected function getTableKey($TableName)
    {
        $pk = Yii::$app->db
        ->getTableSchema($TableName)
        ->primaryKey;
        return  $pk[0] ?? null;
    }

    // added by deepika on 11 feb 2026 for multiprofile
    public function actionSelectProfile()
    {
        $response = Yii::$app->response;

    $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    $response->headers->set('Pragma', 'no-cache');
    $response->headers->set('Expires', '0');
        // If role already selected, redirect to dashboard
        if (Yii::$app->session->has('active_profile_id')) {
                        return $this->goHome();

        }

        $userId = Yii::$app->user->id;

        // Fetch profiles of logged-in user
       $profiles = (new \yii\db\Query())
        ->from('user2role')
        ->innerJoin('role', 'role.roleid = user2role.roleid')
        ->where(['user2role.userid' => $userId])
        ->all();
        //print_r($profiles);die;
        $user = (new \yii\db\Query())
         ->select(['first_name','last_name'])
         ->from('user')
         ->where(['id' => $userId])
         ->one();
        
        // If no profile or only one → auto-continue
        if (count($profiles) === 0) {
            throw new \yii\web\ForbiddenHttpException("No profiles linked to this user.");
        }

        if (count($profiles) === 1) {
            Yii::$app->session->set('active_profile_id', $profiles[0]['roleid']);
            return $this->goHome();
        }
        // echo count($profiles);die;

        $this->layout = false;
        $siteSetting = SiteSetting::find()
            ->where(['active' => 1])->one();


        return $this->render('select-profile', [
            'profiles' => $profiles,
            'siteSetting' =>$siteSetting,
            'user' => $user
        ]);
    }

    public function actionSetProfile()
    {
        if (Yii::$app->session->has('active_profile_id')) {
                        return $this->goHome();

        }
        $this->enableCsrfValidation = true;

        $profileId = Yii::$app->request->post('profile_id');

        $profile = (new \yii\db\Query())
        ->from('user2role')
            ->where([
                'roleid' => $profileId,
                'userid' => Yii::$app->user->id
            ])
            ->one();

        // print_r($profile);die;

           

        if (!$profile) {
            throw new \yii\web\ForbiddenHttpException("Invalid profile selection.");
        }

        Yii::$app->session->set('active_profile_id', $profile['roleid']);
        Yii::$app->session->regenerateID(true);

        // return $this->redirect(['site/index']);
            return $this->goHome();

    }

    public function getActiveThemes()  
    {
        return (new Query())
            ->select(['id', 'name'])
            ->from('theme')
            ->where(['active' => 1])
            ->indexBy('id')
            ->column();
    }

    /**
     * Get current theme colors
     */
    public function getCurrentTheme()
    {
        $themeId = Yii::$app->session->get('_theme_id');
        if (!$themeId) {
            $themeId = (new Query())
                ->select(['id'])
                ->from('theme')
                ->where(['active' => 1])
                ->scalar();
        }
        if (!$themeId) return null;

        return (new Query())
            ->select(['primary', 'secondary', 'tertiary'])
            ->from('theme')
            ->where(['id' => $themeId, 'active' => 1])
            ->one();
    }

    /**
     * Set theme to session
     */
   private function setCurrentTheme($themeId)
{
    // ensure theme exists and is active
    $exists = (new \yii\db\Query())
        ->from('theme')
        ->where(['id' => $themeId, 'active' => 1])
        ->exists();

    if (!$exists) {
        return;
    }

    // store in session
    \Yii::$app->session->set('_theme_id', $themeId);

    // if user logged in, store in user table
    if (!\Yii::$app->user->isGuest) {
        $userId = \Yii::$app->user->id;
        \Yii::$app->db->createCommand()
            ->update('user', ['theme' => $themeId], ['id' => $userId])
            ->execute();
    }
}


    /**
     * Change theme action (for dropdown form)
     */
    public function actionChangeTheme()
    {
        if (Yii::$app->request->isGet && isset($_GET['id'])) {
            $this->setCurrentTheme((int)$_GET['id']);
        }
        return $this->redirect(Yii::$app->request->referrer ?: ['site/index']);
    }
}
