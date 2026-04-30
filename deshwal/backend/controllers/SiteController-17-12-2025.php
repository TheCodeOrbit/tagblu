<?php

namespace backend\controllers;

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
use app\models\Widget;
use Exception;

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

        // return $this->render('index');
        return $this->render('index', ['widgets' => $this->getwidgetsforuser()]);
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
            return $this->goBack();
        }


        $model->password = '';
        $this->layout = "login";

        return $this->render('login', [
            'model' => $model,
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
        // else if($widgetType != "summery"){
        //     $widgetData = $this->getChartData($widgetname);
        // }
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
        $widgetids = Yii::$app->db->createCommand("
                SELECT * 
                FROM profile2widget
                JOIN role2profile ON role2profile.profileid = profile2widget.profileid
                JOIN role ON role.roleid = role2profile.roleid
                JOIN user2role ON user2role.roleid = role.roleid
                JOIN user ON user.id = user2role.userid
                WHERE user.id = :uid
            ")
            ->bindValue(':uid', $user_id)
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
            ")
            ->bindValue(':uid', $user_id)
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
                $isAdmin = $this->isAdmin($widget['modulename']);

                if ($widget['type'] == 2) //2 = count,1=summery
                    $widgetData = $this->getWidgetData($widget['name']);

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
            ")
            ->bindValue(':uid', $user_id)
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

    protected function getWidgetData($name)
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
    }

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

        return $this->render('forgotpassword', [
            'model' => $model,
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

        return $this->render('resetpassword', ['model' => $model]);
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



}
