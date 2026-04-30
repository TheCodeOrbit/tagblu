<?php
namespace frontend\controllers;
use yii\web\NotFoundHttpException;
define('ALL_ACOUNTS', false);

use frontend\models\Forgotpassword;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\LoginForm;  // The login form model
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\data\Pagination;
use DateTime;
use frontend\models\Contacts;
use frontend\models\ModtrackerBasic;
use frontend\models\Resetpassword;

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
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup', 'error'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'about', 'contact', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
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
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }


    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            $static_logic = false;
            $is_admin = Yii::$app->user->identity->is_admin;
            $vendor_account_name = Yii::$app->user->identity->vendor_account_name ?? null;
            $first_name = Yii::$app->user->identity->first_name ?? "";
            $last_name = Yii::$app->user->identity->last_name ?? "";
            if ($is_admin) {
                $_SESSION["is_admin"] = 1;
            } else {
                $_SESSION["is_admin"] = 0;
            }
            if (empty($_SESSION["loggedin_user_name"])) {
                $_SESSION["loggedin_user_name"] = trim($first_name . " " . $last_name);
            }
            if (!empty($vendor_account_name)) {
                if (empty($_SESSION["vendor_name"])) {
                    $connection = Yii::$app->db;
                    $command = $connection->createCommand("SELECT acc_name FROM vendor_account WHERE vendoraccid =:vendoraccid")
                        ->bindValues([":vendoraccid" => "$vendor_account_name"]);
                    $data = $command->queryOne();
                    $vendor_name = $data["acc_name"] ?? "  ";
                    $_SESSION["vendor_name"] = $vendor_name;
                }
            }
            if (!empty($_SESSION["user_roles"])) {

            } else if (empty($_SESSION["user_roles"]) && Yii::$app->user->identity->contact_role) {
                $contact_role = Yii::$app->user->identity->contact_role;
                $connection = Yii::$app->db;
                $command = $connection->createCommand("SELECT contactrole_value FROM contact_role WHERE FIND_IN_SET(contactroleid,'$contact_role')");
                $data = $command->queryAll();
                if (!empty($data)) {
                    $_SESSION["user_roles"] = implode(", ", array_column($data, 'contactrole_value'));
                }
            } else {
                $_SESSION["user_roles"] = null;
            }
            // Redirect logged-in users to home

            $vendor_account_id = Yii::$app->user->identity->vendor_account_name ?? null;
            //fetch payment data,
            //fetch grn data

            $pageSize = 10;
            $currentPage = Yii::$app->request->get('page', 0);

            $page = isset($currentPage) && !empty($currentPage) && is_numeric($currentPage) ? $currentPage : 1;
            $page = (int) $page;
            $size = 10;
            $offset = (int) ($page - 1) * $size;

            $connection = Yii::$app->db;
            if ($static_logic) {
                $command = $connection->createCommand("SELECT count(*) FROM dummy_payment");
            } else {
                if (ALL_ACOUNTS == true) {
                    $command = $connection->createCommand("SELECT count(*) FROM rep_vp_payments");
                } else {
                    $command = $connection->createCommand("SELECT count(*) FROM rep_vp_payments where account_id=:account_id")
                        ->bindValues([":account_id" => $vendor_account_id]);
                }
            }

            // Get the total count of records (needed for pagination)
            $totalCount = $command->queryScalar();
            // Create the Pagination object
            $pagination = [
                'defaultPageSize' => $size,
                'totalCount' => $totalCount,
                'numberOfPages' => ceil($totalCount / $pageSize),
                'page' => $page
            ];

            if ($static_logic) {
                $command = $connection->createCommand("SELECT * FROM dummy_payment LIMIT $offset, $size");
            } else {
                if (ALL_ACOUNTS == true) {
                    $command = $connection->createCommand("SELECT * FROM rep_vp_payments LIMIT $offset, $size");
                } else {
                    $command = $connection->createCommand("SELECT * FROM rep_vp_payments where account_id=:account_id order by id desc LIMIT $offset, $size")
                        ->bindValues([":account_id" => $vendor_account_id]);
                }
            }


            // Fetch the data with pagination
            $paymentData = $command->queryAll();

            if (empty($paymentData))
                $paymentData = [];
            $payments_status_counts = $this->paymentStatusCount($connection, $vendor_account_id);
            $approved_count = $payments_status_counts["approved_count"] ?? 0;
            $transferred_count = $payments_status_counts["transferred_count"] ?? 0;
            $partial_payment_count = $payments_status_counts["partial_payment_count"] ?? 0;
            $pending_count = $payments_status_counts["pending_count"] ?? 0;
            return $this->render('index', [
                'paymentData' => $paymentData,
                // 'total_grn' => $totalCount??0,
                'approved_count' => $approved_count,
                'transferred_count' => $transferred_count,
                'partial_payment_count' => $partial_payment_count,
                'pending_count' => $pending_count,
                'pagination' => $pagination
            ]);

        }
    }

    public function validateAndFormatDate($input)
    {
        if (empty($input))
            return false;
        $date = DateTime::createFromFormat('Y-m-d', $input) ?:
            DateTime::createFromFormat('d-m-Y', $input) ?:
            DateTime::createFromFormat('m/d/Y', $input) ?:
            DateTime::createFromFormat('Y-m-d H:i:s', $input);
        if ($date && $date->format('Y-m-d') === date('Y-m-d', strtotime($input))) {
            return $date->format('d/m/Y');
        }
        return $input;
    }

    public function paymentStatusCount($connection, $account_id)
    {
        if (ALL_ACOUNTS == true) {
            $command = $connection->createCommand("SELECT status,count(*) as cnt FROM rep_vp_payments group by status");
        } else {
            $command = $connection->createCommand("SELECT status,count(*) as cnt FROM rep_vp_payments where account_id=:account_id group by status")
                ->bindValues([":account_id" => $account_id]);
        }
        $datawipingData = $command->queryAll();
        $approved_count = 0;
        $transferred_count = 0;
        $partial_payment_count = 0;
        $pending_count = 0;
        $total_count = 0;
        foreach ($datawipingData as $row) {
            $status = $row["status"];
            $count = $row["cnt"] ? $row["cnt"] : 0;
            $total_count += $count;
            if ($status == 5) {
                $approved_count += $count; //Payment Approved
            } else if ($status == 6) {
                $transferred_count += $count; //Payment Transferred
            } else if ($status == 7) {
                $partial_payment_count += $count; // Partial Payment Done
            } else if ($status == 2 || $status == 3) {
                $pending_count += $count;
            }
        }
        return ["total_count" => $total_count, "approved_count" => $approved_count, "transferred_count" => $transferred_count, "partial_payment_count" => $partial_payment_count, "pending_count" => $pending_count];
    }
    /**Pickup related */
    public function actionPickup()
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            // Redirect logged-in users to pickup dashboard
            //fetch pickup data
            $vendor_account_name = Yii::$app->user->identity->vendor_account_name ?? "xxxx";
            $pageSize = 10;
            $currentPage = Yii::$app->request->get('page', 0);

            $page = isset($currentPage) && !empty($currentPage) && is_numeric($currentPage) ? $currentPage : 1;
            $page = (int) $page;
            $size = 10;
            $offset = (int) ($page - 1) * $size;

            $connection = Yii::$app->db;
            $command = $connection->createCommand("SELECT count(*) FROM pickup WHERE account_name=:account_name AND deleted = :deleted AND pickup_status is not null")
                ->bindValues([":deleted" => 0, ":account_name" => "$vendor_account_name"]);
            // Get the total count of records (needed for pagination)
            $totalCount = $command->queryScalar();
            // Create the Pagination object
            $pagination = [
                'defaultPageSize' => $size,
                'totalCount' => $totalCount,
                'numberOfPages' => ceil($totalCount / $pageSize),
                'page' => $page
            ];


            $command = $connection->createCommand("SELECT * FROM pickup WHERE account_name=:account_name AND deleted = :deleted AND pickup_status is not null LIMIT $offset, $size")
                ->bindValues([":deleted" => 0, ":account_name" => "$vendor_account_name"]);

            // Fetch the data with pagination
            $pickupData = $command->queryAll();

            $command = $connection
                ->createCommand("SELECT * FROM pick_pickup_status WHERE is_active = :is_active")
                ->bindValue(":is_active", 1);
            $pickup_status_values = $command->queryAll();
            if (empty($pickup_status_values))
                $pickup_status_values = [];
            if (empty($pickupData))
                $pickupData = [];

            foreach ($pickupData as $key => $p_data) {
                $pickup_status = $this->getPickupStatusValue($p_data['pickup_status'], $pickup_status_values);
                $pickupData[$key]['pickup_status_text'] = $pickup_status;
            }

            $total_pickups = $this->totalPickupCount($vendor_account_name);
            $pickup_delivered = $this->totalPickupDeliveredCount($vendor_account_name);
            $total_assets_processed = $this->totalPickupAssetsProcessed($vendor_account_name);
            return $this->render('pickup', [
                'pickupData' => $pickupData,
                'total_pickups' => $total_pickups ?? 0,
                'pickup_delivered' => $pickup_delivered ?? 0,
                'total_assets_processed' => $total_assets_processed ?? 0,
                'pagination' => $pagination
            ]);
        }
    }

    public function actionSearch()
    {
        $search_value = Yii::$app->request->get('query');
        if (Yii::$app->user->isGuest) {
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            if (empty($search_value)) {
                return $this->render('search', [
                    'pickupRequestData' => [],
                    'dataWipingResults' => [],
                    'pagination' => null,
                    'paginationWiping' => null,
                    'error' => "Search string is required"
                ]);
            }
            $vendor_account_name = Yii::$app->user->identity->vendor_account_name ?? "xxxx";
            $user_id = Yii::$app->user->id;
            $pageSize = 5;
            $currentPage = Yii::$app->request->get('page', 0);

            $page = isset($currentPage) && !empty($currentPage) && is_numeric($currentPage) ? $currentPage : 1;
            $page = (int) $page;
            $size = 10;
            $offset = (int) ($page - 1) * $size;
            $dataWipingPage = (int)Yii::$app->request->get('dataWipingPage', 1);
            $dataWipingOffset = ($dataWipingPage - 1) * $pageSize;
            $connection = Yii::$app->db;
            $command = $connection->createCommand("SELECT count(*) FROM customer_pickup_request WHERE ownerid=:ownerid AND deleted = :deleted and pickup_request like :pickup_request")
                ->bindValues([":deleted" => 0, ":ownerid" => "$user_id", ":pickup_request" => "%$search_value%"]);
            
            $totalCount = $command->queryScalar();
            
            $pagination = [
                'defaultPageSize' => $size,
                'totalCount' => $totalCount,
                'numberOfPages' => ceil($totalCount / $pageSize),
                'page' => $page
            ];


            $command = $connection->createCommand("SELECT * FROM customer_pickup_request WHERE ownerid=:ownerid AND deleted = :deleted and pickup_request like :pickup_request ORDER BY 1 DESC LIMIT $offset, $size")
                ->bindValues([":deleted" => 0, ":ownerid" => "$user_id", ":pickup_request" => "%$search_value%"]);
            $pickupRequestData = $command->queryAll();


            if (empty($pickupRequestData))
                $pickupRequestData = [];

            $pickupRequest = new PickuprequestController('site', Yii::$app);
            foreach ($pickupRequestData as $index => $value) {
                $location = $value["location"];
                $preferred_pickup_date = $value["preferred_pickup_date"];
                if ($location) {
                    $location_value = $pickupRequest->vendorLocationValue($connection, $location);
                    $pickupRequestData[$index]["location"] = $location_value;
                }
                if ($preferred_pickup_date) {
                    $preferred_pickup_date_value = $pickupRequest->validateAndFormatDate($preferred_pickup_date);
                    $pickupRequestData[$index]["preferred_pickup_date"] = $preferred_pickup_date_value;
                }
                $pickupRequestData[$index]["status_value"] = $pickupRequest->pickupRequestStatusValue($connection, $value["status"]);
            }

            $dataWipingCountCommand = $connection->createCommand("
                    SELECT COUNT(*)
                    FROM data_wiping_asset_details dwad
                    INNER JOIN rep_vp_data_wiping rvdw ON rvdw.datawiping_id = dwad.datawiping_id
                    WHERE dwad.laptop_serial_no LIKE :serial_no AND dwad.deleted = 0
                ")->bindValue(':serial_no', "%$search_value%");
                $dataWipingTotalCount = $dataWipingCountCommand->queryScalar();

                // Get Data Wiping Page Data
                $dataWipingCommand = $connection->createCommand("
                    SELECT
                        dwad.laptop_serial_no,
                        rvdw.req_reference_no,
                        rvdw.status_name
                    FROM data_wiping_asset_details dwad
                    INNER JOIN rep_vp_data_wiping rvdw ON rvdw.datawiping_id = dwad.datawiping_id
                    WHERE dwad.laptop_serial_no LIKE :serial_no AND dwad.deleted = 0
                    ORDER BY dwad.laptop_serial_no DESC
                    LIMIT :offset, :limit
                ")->bindValues([
                    ':serial_no' => "%$search_value%",
                    ':offset' => $dataWipingOffset,
                    ':limit' => $pageSize,
                ]);
                $dataWipingResults = $dataWipingCommand->queryAll();

                $paginationDataWiping = [
                    'currentPage' => $dataWipingPage,
                    'totalCount' => $dataWipingTotalCount,
                    'pageSize' => $pageSize,
                    'pageCount' => ceil($dataWipingTotalCount / $pageSize),
                ];


            return $this->render('search', [
                'pickupRequestData' => $pickupRequestData,
                'dataWipingResults' => $dataWipingResults,
                'paginationDataWiping' => $paginationDataWiping,
                'pagination' => $pagination,
                'error' => ""
            ]);
        }
    }

    public function getPickupStatusValue($pickupStatusId, $statusArray)
    {
        foreach ($statusArray as $status) {
            if ($status['pickup_status_id'] == $pickupStatusId) {
                return $status['pickup_status_value'];
            }
        }
        return $pickupStatusId;
    }

    public function totalPickupCount($vendor_account_name)
    {
        $connection = Yii::$app->db;
        $command = $connection->createCommand("SELECT count(*) FROM pickup WHERE account_name=:account_name AND deleted = :deleted AND pickup_status is not null")
            ->bindValues([":deleted" => 0, ":account_name" => "$vendor_account_name"]);
        $totalCount = $command->queryScalar();
        return $totalCount ?? 0;
    }

    public function totalPickupDeliveredCount($vendor_account_name)
    {
        $connection = Yii::$app->db;
        $command = $connection->createCommand("SELECT count(*) FROM pickup WHERE account_name=:account_name AND deleted = :deleted AND pickup_status = :pickup_status")
            ->bindValues([
                ':deleted' => 0,
                ':pickup_status' => 8,
                ':account_name' => "$vendor_account_name"
            ]);
        $totalCount = $command->queryScalar();
        return $totalCount ?? 0;
    }

    public function totalPickupAssetsProcessed($vendor_account_name)
    {
        $connection = Yii::$app->db;
        $totalPickupQty = (new \yii\db\Query())
            ->select(['sum(pickup_asset_detail.pickup_qty)'])
            ->from('pickup_asset_detail')
            ->leftJoin('pickup', 'pickup_asset_detail.pickup_id = pickup.pickup_id')
            ->where([
                'pickup_asset_detail.deleted' => 0,
                'pickup.deleted' => 0,
                'pickup.account_name' => "$vendor_account_name"
            ])
            ->andWhere(['is not', 'pickup_status', null])
            ->scalar();
        return $totalPickupQty ?? 0;
    }

    /**Purchase order related */
    public function actionPurchaseorder()
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            $vendor_account_id = Yii::$app->user->identity->vendor_account_name ?? null;
            if ($vendor_account_id) {
                $vendor_account_name = $this->getVendorName($vendor_account_id);
            } else {
                $vendor_account_name = "xxxx";
            }
            //fetch  data
            $static_logic = false;
            $pageSize = 10;
            $currentPage = Yii::$app->request->get('page', 0);

            $page = isset($currentPage) && !empty($currentPage) && is_numeric($currentPage) ? $currentPage : 1;
            $page = (int) $page;
            $size = 10;
            $offset = (int) ($page - 1) * $size;

            $connection = Yii::$app->db;
            if ($static_logic) {
                $command = $connection->createCommand("SELECT count(*) FROM dummy_po");
            } else {

                $command = $connection->createCommand("SELECT count(*) FROM rep_vp_purchase_order where status != 5 and account_id = :account_id")->bindValue(":account_id", $vendor_account_id);//not show po cancelled added by deepika on 16 oct 2025
            }
            // Get the total count of records (needed for pagination)
            $totalCount = $command->queryScalar();
            // Create the Pagination object
            $pagination = [
                'defaultPageSize' => $size,
                'totalCount' => $totalCount,
                'numberOfPages' => ceil($totalCount / $pageSize),
                'page' => $page
            ];

            if ($static_logic) {
                $command = $connection->createCommand("SELECT * FROM dummy_po LIMIT $offset, $size");
            } else {
                if (ALL_ACOUNTS == true) {
                    $command = $connection->createCommand("SELECT * FROM rep_vp_purchase_order LIMIT $offset, $size");
                } else {
                    //update status of PO added by deepika on 16 oct 2025
    //                 $sql_up = " UPDATE rep_vp_purchase_order r
    // JOIN purchase_order p ON p.purchase_order_id = r.purchase_order_id
    // JOIN purchase_order_stage pos ON pos.po_stage_id = p.stage
    // SET r.status = p.stage, r.status_name = pos.stage_name
    // WHERE r.account_id = :account_id";

    //                 $command = $connection->createCommand($sql_up)
    //                     ->bindValue(":account_id", $vendor_account_id)
    //                     ->execute();



                    //commented on 16 oct by deepika


                    // $command = $connection->createCommand("SELECT * FROM rep_vp_purchase_order where account_id=:account_id order by id desc LIMIT $offset, $size")
                    //added on 16 oct by deepika
                    //not show po cancelled stage
                    $command = $connection->createCommand("SELECT * FROM rep_vp_purchase_order where account_id=:account_id and status != 5 order by id desc LIMIT $offset, $size")
                        ->bindValues([":account_id" => $vendor_account_id]);
                }
            }
            // Fetch the data with pagination
            $purchaseOrderData = $command->queryAll();

            if (empty($purchaseOrderData))
                $purchaseOrderData = [];
            foreach ($purchaseOrderData as $k => $v) {
                if (isset($v["po_expiry_date"]) && !empty($v["po_expiry_date"])) {
                    $purchaseOrderData[$k]["po_expiry_date"] = $this->validateAndFormatDate($v["po_expiry_date"]);
                }
            }

            $po_status_counts = $this->purchaseOrderStatusCount($connection, $vendor_account_id);
            $approved_count = $po_status_counts["approved_count"] ?? 0;
            $pending_count = $po_status_counts["pending_count"] ?? 0;
            $total_count = $po_status_counts["total_count"] ?? 0;
            return $this->render('po', [
                'purchaseOrderData' => $purchaseOrderData,
                'total_count' => $total_count,
                'approved_count' => $approved_count,
                'pending_count' => $pending_count,
                'pagination' => $pagination
            ]);
        }
    }
    public function purchaseOrderStatusCount($connection, $account_id)
    {
        if (ALL_ACOUNTS == true) {
            //removed po cancelled on 16 oct 2025 by deepika
            $command = $connection->createCommand("SELECT status,count(*) as cnt FROM rep_vp_purchase_order where  status != 5 group by status");
        } else {
            //removed po cancelled on 16 oct 2025 by deepika
            $command = $connection->createCommand("SELECT status,count(*) as cnt FROM rep_vp_purchase_order where account_id=:account_id and status != 5 group by status")
                ->bindValues([":account_id" => $account_id]);
        }
        $poData = $command->queryAll();
        $approved_count = 0;
        $pending_count = 0;
        $total_count = 0;
        foreach ($poData as $row) {
            $status = $row["status"];
            $count = $row["cnt"] ? $row["cnt"] : 0;
            $total_count += $count;
            if ($status == 3) {
                $approved_count += $count;
            } else if ($status == 2) {
                $pending_count += $count;
            }
        }
        return ["total_count" => $total_count, "approved_count" => $approved_count, "pending_count" => $pending_count];
    }
    public function actionPurchaseorderOld()
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            //fetch po data
            $user_id = Yii::$app->user->id;
            $vendor_account_name = Yii::$app->user->identity->vendor_account_name ?? "xxxx";

            $pageSize = 10;
            $currentPage = Yii::$app->request->get('page', 0);

            $page = isset($currentPage) && !empty($currentPage) && is_numeric($currentPage) ? $currentPage : 1;
            $page = (int) $page;
            $size = 10;
            $offset = (int) ($page - 1) * $size;

            $connection = Yii::$app->db;
            $command = $connection->createCommand("SELECT count(*) FROM purchase_order WHERE vendor_name=:vendor_name AND deleted = :deleted")
                ->bindValues([":deleted" => 0, ":vendor_name" => "$vendor_account_name"]);
            // Get the total count of records (needed for pagination)
            $totalCount = $command->queryScalar();
            // Create the Pagination object
            $pagination = [
                'defaultPageSize' => $size,
                'totalCount' => $totalCount,
                'numberOfPages' => ceil($totalCount / $pageSize),
                'page' => $page
            ];


            $command = $connection->createCommand("SELECT * FROM purchase_order WHERE vendor_name=:vendor_name AND deleted = :deleted LIMIT $offset, $size")
                ->bindValues([":deleted" => 0, ":vendor_name" => "$vendor_account_name"]);

            // Fetch the data with pagination
            $purchaseOrderData = $command->queryAll();

            $command = $connection
                ->createCommand("SELECT * FROM purchase_order_stage WHERE is_active = :is_active")
                ->bindValue(":is_active", 1);
            $purchase_order_stage_values = $command->queryAll();
            if (empty($purchase_order_stage_values))
                $purchase_order_stage_values = [];
            if (empty($purchaseOrderData))
                $purchaseOrderData = [];

            foreach ($purchaseOrderData as $key => $p_data) {
                $stage_status = $this->getPurchaseOrderStageValue($p_data['stage'], $purchase_order_stage_values);
                $purchaseOrderData[$key]['stage_text'] = $stage_status;

                $purchaseOrderData[$key]['po_expiry_date'] = $this->validateAndFormatDate($p_data["po_expiry_date"]);
            }

            $purchase_order_approved = $this->totalPurcheaeOrdereApprovedCount($vendor_account_name);
            $total_assets_processed = $this->totalPurchaseOrderAssetsProcessed($vendor_account_name);
            return $this->render('purchaseOrder', [
                'purchaseOrderData' => $purchaseOrderData,
                'total_purchase_order' => $totalCount ?? 0,
                'purchase_order_approved' => $purchase_order_approved ?? 0,
                'total_assets_processed' => $total_assets_processed ?? 0,
                'pagination' => $pagination
            ]);
        }
    }

    public function getPurchaseOrderStageValue($id, $statusArray)
    {
        foreach ($statusArray as $status) {
            if ($status['po_stage_id'] == $id) {
                return $status['stage_name'];
            }
        }
        return $id;
    }


    public function totalPurcheaeOrdereApprovedCount($vendor_account_name)
    {
        $connection = Yii::$app->db;
        $command = $connection->createCommand("SELECT count(*) FROM purchase_order WHERE vendor_name=:vendor_name AND deleted = :deleted AND stage = :stage")
            ->bindValues([
                ':deleted' => 0,
                ':stage' => 3,
                ":vendor_name" => "$vendor_account_name"
            ]);
        $totalCount = $command->queryScalar();
        return $totalCount ?? 0;
    }

    public function totalPurchaseOrderAssetsProcessed($vendor_account_name)
    {
        $connection = Yii::$app->db;
        $totalPickupQty = (new \yii\db\Query())
            ->select(['sum(purchase_order_itemsdetail.quantity)'])
            ->from('purchase_order_itemsdetail')
            ->leftJoin('purchase_order', 'purchase_order_itemsdetail.purchase_order_id  = purchase_order.purchase_order_id ')
            ->where([
                'purchase_order_itemsdetail.deleted' => 0,
                'purchase_order.deleted' => 0,
                'purchase_order.vendor_name' => "$vendor_account_name"
            ])
            ->scalar();
        return $totalPickupQty ?? 0;
    }

    /**GRN related */
    public function getVendorName($vendor_account_id)
    {
        if (empty($vendor_account_id))
            return "xxxx";
        $connection = Yii::$app->db;
        $command = $connection->createCommand("SELECT acc_name FROM vendor_account WHERE vendoraccid = :vendor_account_id  AND deleted = :deleted")
            ->bindValues([
                ':deleted' => 0,
                ':vendor_account_id' => $vendor_account_id
            ]);
        $acc_name = $command->queryScalar();
        return $acc_name ?? "xxxx";
    }
    public function actionGrnOld()
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            //fetch grn data
            $vendor_account_id = Yii::$app->user->identity->vendor_account_name ?? null;
            if ($vendor_account_id) {
                $vendor_account_name = $this->getVendorName($vendor_account_id);
            } else {
                $vendor_account_name = "xxxx";
            }
            $pageSize = 10;
            $currentPage = Yii::$app->request->get('page', 0);

            $page = isset($currentPage) && !empty($currentPage) && is_numeric($currentPage) ? $currentPage : 1;
            $page = (int) $page;
            $size = 10;
            $offset = (int) ($page - 1) * $size;

            $connection = Yii::$app->db;
            $command = $connection->createCommand("SELECT count(*) FROM grn WHERE vendor_name=:vendor_name AND deleted = :deleted")
                ->bindValues([":deleted" => 0, ":vendor_name" => "$vendor_account_name"]);
            // Get the total count of records (needed for pagination)
            $totalCount = $command->queryScalar();
            // Create the Pagination object
            $pagination = [
                'defaultPageSize' => $size,
                'totalCount' => $totalCount,
                'numberOfPages' => ceil($totalCount / $pageSize),
                'page' => $page
            ];


            $command = $connection->createCommand("SELECT * FROM grn WHERE vendor_name=:vendor_name AND deleted = :deleted LIMIT $offset, $size")
                ->bindValues([":deleted" => 0, ":vendor_name" => "$vendor_account_name"]);

            // Fetch the data with pagination
            $grnData = $command->queryAll();

            if (empty($grnData))
                $grnData = [];

            $total_assets_processed = $this->totalGrnPhysicalAssetsProcessed($vendor_account_name);

            foreach ($grnData as $key => $p_data) {
                $purchase_order = $this->getPOID($p_data["purchase_order"]);
                $grnData[$key]['purchase_order'] = $purchase_order ?? $p_data["purchase_order"];

                $grnData[$key]['invoice_date'] = $this->validateAndFormatDate($p_data["invoice_date"]);
                $grnData[$key]['date_material_received'] = $this->validateAndFormatDate($p_data["date_material_received"]);
            }
            return $this->render('grn', [
                'grnData' => $grnData,
                'total_grn' => $totalCount ?? 0,
                'purchase_order_approved' => $purchase_order_approved ?? 0,
                'total_assets_processed' => $total_assets_processed ?? 0,
                'pagination' => $pagination
            ]);
        }
    }
    public function actionGrn()
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            $vendor_account_id = Yii::$app->user->identity->vendor_account_name ?? null;
            if ($vendor_account_id) {
                $vendor_account_name = $this->getVendorName($vendor_account_id);
            } else {
                $vendor_account_name = "xxxx";
            }
            //fetch  data
            $static_logic = false;
            $pageSize = 10;
            $currentPage = Yii::$app->request->get('page', 0);

            $page = isset($currentPage) && !empty($currentPage) && is_numeric($currentPage) ? $currentPage : 1;
            $page = (int) $page;
            $size = 10;
            $offset = (int) ($page - 1) * $size;

            $connection = Yii::$app->db;
            if ($static_logic) {
                $command = $connection->createCommand("SELECT count(*) FROM dummy_grn");
            } else {
                $command = $connection->createCommand("SELECT count(*) FROM rep_vp_grn");
            }
            // Get the total count of records (needed for pagination)
            $totalCount = $command->queryScalar();
            // Create the Pagination object
            $pagination = [
                'defaultPageSize' => $size,
                'totalCount' => $totalCount,
                'numberOfPages' => ceil($totalCount / $pageSize),
                'page' => $page
            ];

            if ($static_logic) {
                $command = $connection->createCommand("SELECT * FROM dummy_grn LIMIT $offset, $size");
            } else {
                if (ALL_ACOUNTS == true) {
                    $command = $connection->createCommand("SELECT * FROM rep_vp_grn LIMIT $offset, $size");
                } else {
                    $command = $connection->createCommand("SELECT * FROM rep_vp_grn where account_id=:account_id order by id desc LIMIT $offset, $size")
                        ->bindValues([":account_id" => $vendor_account_id]);
                }
            }
            $grn_data = $command->queryAll();

            if (empty($grn_data))
                $grn_data = [];
            foreach ($grn_data as $k => $v) {
                if (isset($v["invoice_date"]) && !empty($v["invoice_date"])) {
                    $grn_data[$k]["invoice_date"] = $this->validateAndFormatDate($v["invoice_date"]);
                }
                if (isset($v["date_material_received"]) && !empty($v["date_material_received"])) {
                    $grn_data[$k]["date_material_received"] = $this->validateAndFormatDate($v["date_material_received"]);
                }
            }
            $grn_dashboard_counts = $this->grnDashboardCount($connection, $vendor_account_id);
            $total_grn = $grn_dashboard_counts["total_grn"] ?? 0;
            $pending_invoices = $grn_dashboard_counts["pending_invoices"] ?? 0;
            $total_assets_processed = $grn_dashboard_counts["total_assets_processed"] ?? 0;

            return $this->render('grn_new', [
                'grnData' => $grn_data,
                'total_grn' => $total_grn,
                'pending_invoices' => $pending_invoices,
                'total_assets_processed' => $total_assets_processed,
                'pagination' => $pagination
            ]);
        }
    }
    public function grnDashboardCount($connection, $account_id)
    {
        if (ALL_ACOUNTS == true) {
            $total_grn_sql = "SELECT count(*) as cnt FROM rep_vp_grn";
            $pending_invoices_sql = "SELECT count(*) as cnt FROM rep_vp_grn where payment_stage !=5";
            $total_assets_processed_sql = "SELECT sum(assets_physical_qty) as cnt FROM rep_vp_grn";

            $command1 = $connection->createCommand($total_grn_sql);
            $total_grn_data = $command1->queryOne();
            $total_grn = $total_grn_data["cnt"] ? $total_grn_data["cnt"] : 0;

            $command2 = $connection->createCommand($pending_invoices_sql);
            $pending_invoices_data = $command2->queryOne();
            $pending_invoices = $pending_invoices_data["cnt"] ? $pending_invoices_data["cnt"] : 0;

            $command3 = $connection->createCommand($total_assets_processed_sql);
            $total_assets_processed_data = $command3->queryOne();
            $total_assets_processed = $total_assets_processed_data["cnt"] ? $total_assets_processed_data["cnt"] : 0;
        } else {
            $total_grn_sql = "SELECT count(*) as cnt FROM rep_vp_grn where account_id=:account_id";
            $pending_invoices_sql = "SELECT count(*) as cnt FROM rep_vp_grn where payment_stage !=5 and account_id=:account_id";
            $total_assets_processed_sql = "SELECT sum(assets_physical_qty) as cnt FROM rep_vp_grn where account_id=:account_id";

            $command1 = $connection->createCommand($total_grn_sql)->bindValues([":account_id" => $account_id]);
            $total_grn_data = $command1->queryOne();
            $total_grn = $total_grn_data["cnt"] ? $total_grn_data["cnt"] : 0;

            $command2 = $connection->createCommand($pending_invoices_sql)->bindValues([":account_id" => $account_id]);
            $pending_invoices_data = $command2->queryOne();
            $pending_invoices = $pending_invoices_data["cnt"] ? $pending_invoices_data["cnt"] : 0;

            $command3 = $connection->createCommand($total_assets_processed_sql)->bindValues([":account_id" => $account_id]);
            $total_assets_processed_data = $command3->queryOne();
            $total_assets_processed = $total_assets_processed_data["cnt"] ? $total_assets_processed_data["cnt"] : 0;
        }
        return ["total_grn" => $total_grn, "pending_invoices" => $pending_invoices, "total_assets_processed" => $total_assets_processed];
    }
    public function getPOID($purchase_order_id)
    {
        if (empty($purchase_order_id))
            return "";
        $connection = Yii::$app->db;
        $command = $connection->createCommand("SELECT purchase_order_no FROM purchase_order WHERE purchase_order_id = :purchase_order_id AND deleted = :deleted")
            ->bindValues([
                ':deleted' => 0,
                ':purchase_order_id' => $purchase_order_id
            ]);
        $poID = $command->queryScalar();
        return $poID ?? "";
    }
    public function totalGrnPhysicalAssetsProcessed($vendor_name)
    {
        $connection = Yii::$app->db;
        $totalPickupQty = (new \yii\db\Query())
            ->select(['sum(grn_item_detail.physical_quantity)'])
            ->from('grn_item_detail')
            ->leftJoin('grn', 'grn_item_detail.grn_id  = grn.grn_id ')
            ->where([
                'grn_item_detail.deleted' => 0,
                'grn.deleted' => 0,
                'grn.vendor_name' => "$vendor_name"
            ])
            ->scalar();
        return $totalPickupQty ?? 0;
    }

    /**data_sanitization related */
    public function dataWipingStatusCount($connection, $account_id)
    {
        if (ALL_ACOUNTS == true) {
            $command = $connection->createCommand("SELECT status,count(*) as cnt FROM rep_vp_data_wiping group by status");
        } else {
            $command = $connection->createCommand("SELECT status,count(*) as cnt FROM rep_vp_data_wiping where account_id=:account_id group by status")
                ->bindValues([":account_id" => $account_id]);
        }
        $datawipingData = $command->queryAll();
        $completed_count = 0;
        $pending_count = 0;
        $in_process_count = 0;
        $total_count = 0;
        foreach ($datawipingData as $row) {
            $status = $row["status"];
            $count = $row["cnt"] ? $row["cnt"] : 0;
            $total_count += $count;
            if ($status == 5) {
                $completed_count += $count;
            } else if ($status == 4) {
                $in_process_count += $count;
            } else {
                $pending_count += $count;
            }
        }
        return ["total_count" => $total_count, "completed_count" => $completed_count, "pending_count" => $pending_count, "in_process_count" => $in_process_count];
    }
    public function actionDatawiping()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            $vendor_account_id = Yii::$app->user->identity->vendor_account_name ?? null;
            if ($vendor_account_id) {
                $vendor_account_name = $this->getVendorName($vendor_account_id);
            } else {
                $vendor_account_name = "xxxx";
            }
            //fetch  data
            $static_logic = false;
            $pageSize = 10;
            $currentPage = Yii::$app->request->get('page', 0);

            $page = isset($currentPage) && !empty($currentPage) && is_numeric($currentPage) ? $currentPage : 1;
            $page = (int) $page;
            $size = 10;
            $offset = (int) ($page - 1) * $size;

            $connection = Yii::$app->db;
            if ($static_logic) {
                $command = $connection->createCommand("SELECT count(*) FROM dummy_data_sanitization");
            } else {
                $command = $connection->createCommand("SELECT count(*) FROM rep_vp_data_wiping");
            }
            // Get the total count of records (needed for pagination)
            $totalCount = $command->queryScalar();
            // Create the Pagination object
            $pagination = [
                'defaultPageSize' => $size,
                'totalCount' => $totalCount,
                'numberOfPages' => ceil($totalCount / $pageSize),
                'page' => $page
            ];

            if ($static_logic) {
                $command = $connection->createCommand("SELECT * FROM dummy_data_sanitization LIMIT $offset, $size");
            } else {
                if (ALL_ACOUNTS == true) {
                    $command = $connection->createCommand("SELECT * FROM rep_vp_data_wiping LIMIT $offset, $size");
                } else {
                    $command = $connection->createCommand("SELECT * FROM rep_vp_data_wiping where account_id=:account_id order by id desc LIMIT $offset, $size")
                        ->bindValues([":account_id" => $vendor_account_id]);
                }
            }
            // Fetch the data with pagination
            $dsData = $command->queryAll();

            if (empty($dsData))
                $dsData = [];
            $datawiping_status_counts = $this->dataWipingStatusCount($connection, $vendor_account_id);
            $completed_count = $datawiping_status_counts["completed_count"] ?? 0;
            $pending_count = $datawiping_status_counts["pending_count"] ?? 0;
            $in_process_count = $datawiping_status_counts["in_process_count"] ?? 0;

            return $this->render('data_sanitization', [
                'dsData' => $dsData,
                'completed_count' => $completed_count,
                'pending_count' => $pending_count,
                'in_process_count' => $in_process_count,
                'pagination' => $pagination
            ]);
        }
    }
      /**
       * For fetching wiping asset details
       * @return array{data: array, status: string|array{message: string, status: string}}
       */
      public function actionGetDataWipingAssetDetails()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $datawiping_id = Yii::$app->request->post('datawiping_id');

        if (!$datawiping_id) {
            return ['status' => 'error', 'message' => 'Missing datawiping_id'];
        }

        $details = (new \yii\db\Query())
            ->select(['dwad.laptop_serial_no', 'dwad.hdd_sdd_serial_no', 'sn.value AS software_name',
             'wiping_date' => new \yii\db\Expression("DATE_FORMAT(dwad.wiping_date, '%d-%m-%Y')"),
              'dwad.certificate'])
            ->from('data_wiping_asset_details dwad')
            ->innerJoin('wiping_software sn', 'sn.id = dwad.software_name')
            ->innerJoin('rep_vp_data_wiping rvdw', 'rvdw.datawiping_id = dwad.datawiping_id ')
            ->where(['dwad.datawiping_id' => $datawiping_id, 'dwad.deleted' => 0])
            ->all();
        
        return ['status' => 'success', 'data' => $details];
    }
    /**
     * For getting certificate donwnloading path
     * @return array{file_url: string, status: string}
     */
    public function actionGetattachmentpath()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $attachmentId = Yii::$app->request->post('attachment_id');
        $download_url = Yii::$app->urlManager->createUrl(['/site/downloadfile', 'fileid' => $attachmentId]);
        return [
            'status' => 'success',
            'file_url' => $download_url
        ];
    }
    /**
     * For donwloading file
     * @param mixed $fileid
     * @throws \yii\web\NotFoundHttpException
     * @return Yii\web\Response
     */
    public function actionDownloadfile($fileid)
    {
        if($fileid == ''){
            ['success' => false,'msg' => 'File id is required'];
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
         $record = (new \yii\db\Query())
            ->select(['path'])
            ->from('attachments')
            ->where(['attachmentsid' => $fileid])
            ->one();
        $filePath =  Yii::getAlias('@backend/web/') . $record['path'];
        if (!$record || !file_exists($filePath)) {
            return ['success' => false, 'msg' => 'File not found'];
        }
        return Yii::$app->response->sendFile($filePath);
    }

    public function actionSearchLaptop()
    {
        $search_value = Yii::$app->request->post('query');
        $page = Yii::$app->request->post('page', 1);
        $pageSize = 10;
        $offset = ($page - 1) * $pageSize;

        if (Yii::$app->user->isGuest) {
            return $this->redirect(Yii::$app->user->loginUrl);
        }

        if (empty($search_value)) {
            return $this->renderAjax('search_laptop', [
                'results' => [],
                'pagination' => null,
                'error' => 'Search string is required',
            ]);
        }

        $connection = Yii::$app->db;

        // Count total matching records for pagination
        $countCommand = $connection->createCommand(
            "SELECT COUNT(*) FROM data_wiping_asset_details dwad
        INNER JOIN software_name sn ON sn.software_nameid = dwad.software_name
        INNER JOIN rep_vp_data_wiping rvdw ON rvdw.datawiping_id = dwad.datawiping_id
        WHERE dwad.laptop_serial_no LIKE :serial_no AND dwad.deleted = 0"
        )->bindValue(':serial_no', '%' . $search_value . '%');

        $totalCount = $countCommand->queryScalar();
        $numberOfPages = ceil($totalCount / $pageSize);

        // Fetch paginated results
        $queryCommand = $connection->createCommand(
            "SELECT dwad.laptop_serial_no, dwad.hdd_sdd_serial_no, sn.software_name_value AS software_name, 
         DATE_FORMAT(dwad.wiping_date, '%d-%m-%Y') as wiping_date, dwad.certificate
         FROM data_wiping_asset_details dwad
         INNER JOIN software_name sn ON sn.software_nameid = dwad.software_name
         INNER JOIN rep_vp_data_wiping rvdw ON rvdw.datawiping_id = dwad.datawiping_id
         WHERE dwad.laptop_serial_no LIKE :serial_no AND dwad.deleted = 0
         ORDER BY dwad.laptop_serial_no
         LIMIT :offset, :limit"
        );
        $queryCommand->bindValues([
            ':serial_no' => '%' . $search_value . '%',
            ':offset' => $offset,
            ':limit' => $pageSize
        ]);
        $results = $queryCommand->queryAll();

        return $this->renderAjax('search_laptop', [
            'results' => $results,
            'pagination' => [
                'page' => (int)$page,
                'pageSize' => $pageSize,
                'totalCount' => $totalCount,
                'numberOfPages' => $numberOfPages
            ],
            'error' => ''
        ]);
    }


    public function actionServicesdashboard()
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            $vendor_account_id = Yii::$app->user->identity->vendor_account_name ?? null;
            if ($vendor_account_id) {
                $vendor_account_name = $this->getVendorName($vendor_account_id);
            } else {
                $vendor_account_name = "xxxx";
            }
            $connection = Yii::$app->db;

            $degaussing_status_counts = $this->degaussingStatusCount($connection, $vendor_account_id);
            $drilling_status_counts = $this->drillingStatusCount($connection, $vendor_account_id);
            $datawiping_status_counts = $this->dataWipingStatusCount($connection, $vendor_account_id);
            $shredding_status_counts = $this->shreddingStatusCount($connection, $vendor_account_id);
            $weighing_status_counts = ["total_count" => 0, "completed_count" => 0, "pending_count" => 0, "in_process_count" => 0];
            $pagination = [
                'defaultPageSize' => 0,
                'totalCount' => 0,
                'numberOfPages' => 0,
                'page' => 0
            ];

            $data = [];
            return $this->render('services_dashboard', [
                'data' => [
                    "degaussing" => $degaussing_status_counts,
                    "drilling" => $drilling_status_counts,
                    "datawiping" => $datawiping_status_counts,
                    "shredding" => $shredding_status_counts,
                    "weighing" => $weighing_status_counts
                ],
                'pagination' => $pagination
            ]);
        }
    }

    public function drillingStatusCount($connection, $account_id)
    {
        if (ALL_ACOUNTS == true) {
            $command = $connection->createCommand("SELECT status,count(*) as cnt FROM rep_vp_drilling group by status");
        } else {
            $command = $connection->createCommand("SELECT status,count(*) as cnt FROM rep_vp_drilling where account_id=:account_id group by status")
                ->bindValues([":account_id" => $account_id]);
        }
        $drillingData = $command->queryAll();
        $completed_count = 0;
        $pending_count = 0;
        $in_process_count = 0;
        $total_count = 0;
        foreach ($drillingData as $row) {
            $status = $row["status"];
            $count = $row["cnt"] ? $row["cnt"] : 0;
            $total_count += $count;
            if ($status == 5) {
                $completed_count += $count;
            } else if ($status == 4) {
                $in_process_count += $count;
            } else {
                $pending_count += $count;
            }
        }
        return ["total_count" => $total_count, "completed_count" => $completed_count, "pending_count" => $pending_count, "in_process_count" => $in_process_count];
    }
    public function actionDrilling()
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            $vendor_account_id = Yii::$app->user->identity->vendor_account_name ?? null;
            if ($vendor_account_id) {
                $vendor_account_name = $this->getVendorName($vendor_account_id);
            } else {
                $vendor_account_name = "xxxx";
            }
            //fetch  data
            $static_logic = false;
            $pageSize = 10;
            $currentPage = Yii::$app->request->get('page', 0);

            $page = isset($currentPage) && !empty($currentPage) && is_numeric($currentPage) ? $currentPage : 1;
            $page = (int) $page;
            $size = 10;
            $offset = (int) ($page - 1) * $size;

            $connection = Yii::$app->db;

            $command = $connection->createCommand("SELECT count(*) FROM rep_vp_drilling");

            // Get the total count of records (needed for pagination)
            $totalCount = $command->queryScalar();
            // Create the Pagination object
            $pagination = [
                'defaultPageSize' => $size,
                'totalCount' => $totalCount,
                'numberOfPages' => ceil($totalCount / $pageSize),
                'page' => $page
            ];

            if (ALL_ACOUNTS == true) {
                $command = $connection->createCommand("SELECT * FROM rep_vp_drilling LIMIT $offset, $size");
            } else {
                $command = $connection->createCommand("SELECT * FROM rep_vp_drilling where account_id=:account_id order by id desc LIMIT $offset, $size")
                    ->bindValues([":account_id" => $vendor_account_id]);
            }

            // Fetch the data with pagination
            $drilling_data = $command->queryAll();

            if (empty($drilling_data))
                $drilling_data = [];
            $drilling_status_counts = $this->drillingStatusCount($connection, $vendor_account_id);
            $completed_count = $drilling_status_counts["completed_count"] ?? 0;
            $pending_count = $drilling_status_counts["pending_count"] ?? 0;
            $in_process_count = $drilling_status_counts["in_process_count"] ?? 0;
            return $this->render('drilling', [
                'data' => $drilling_data,
                'completed_count' => $completed_count,
                'pending_count' => $pending_count,
                'in_process_count' => $in_process_count,
                'pagination' => $pagination
            ]);
        }
    }

    public function degaussingStatusCount($connection, $account_id)
    {
        if (ALL_ACOUNTS == true) {
            $command = $connection->createCommand("SELECT status,count(*) as cnt FROM rep_vp_degaussing group by status");
        } else {
            $command = $connection->createCommand("SELECT status,count(*) as cnt FROM rep_vp_degaussing where account_id=:account_id group by status")
                ->bindValues([":account_id" => $account_id]);
        }
        $degaussingData = $command->queryAll();
        $completed_count = 0;
        $pending_count = 0;
        $in_process_count = 0;
        $total_count = 0;
        foreach ($degaussingData as $row) {
            $status = $row["status"];
            $count = $row["cnt"] ? $row["cnt"] : 0;
            $total_count += $count;
            if ($status == 5) {
                $completed_count += $count;
            } else if ($status == 4) {
                $in_process_count += $count;
            } else {
                $pending_count += $count;
            }
        }
        return ["total_count" => $total_count, "completed_count" => $completed_count, "pending_count" => $pending_count, "in_process_count" => $in_process_count];
    }
    public function actionDegaussing()
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            $vendor_account_id = Yii::$app->user->identity->vendor_account_name ?? null;
            if ($vendor_account_id) {
                $vendor_account_name = $this->getVendorName($vendor_account_id);
            } else {
                $vendor_account_name = "xxxx";
            }
            //fetch  data
            $static_logic = false;
            $pageSize = 10;
            $currentPage = Yii::$app->request->get('page', 0);

            $page = isset($currentPage) && !empty($currentPage) && is_numeric($currentPage) ? $currentPage : 1;
            $page = (int) $page;
            $size = 10;
            $offset = (int) ($page - 1) * $size;

            $connection = Yii::$app->db;

            $command = $connection->createCommand("SELECT count(*) FROM rep_vp_degaussing");

            // Get the total count of records (needed for pagination)
            $totalCount = $command->queryScalar();
            // Create the Pagination object
            $pagination = [
                'defaultPageSize' => $size,
                'totalCount' => $totalCount,
                'numberOfPages' => ceil($totalCount / $pageSize),
                'page' => $page
            ];

            if (ALL_ACOUNTS == true) {
                $command = $connection->createCommand("SELECT * FROM rep_vp_degaussing LIMIT $offset, $size");
            } else {
                $command = $connection->createCommand("SELECT * FROM rep_vp_degaussing where account_id=:account_id order by id desc LIMIT $offset, $size")
                    ->bindValues([":account_id" => $vendor_account_id]);
            }

            // Fetch the data with pagination
            $degaussing_data = $command->queryAll();

            if (empty($degaussing_data))
                $degaussing_data = [];
            $degaussing_status_counts = $this->degaussingStatusCount($connection, $vendor_account_id);
            $completed_count = $degaussing_status_counts["completed_count"] ?? 0;
            $pending_count = $degaussing_status_counts["pending_count"] ?? 0;
            $in_process_count = $degaussing_status_counts["in_process_count"] ?? 0;
            return $this->render('degaussing', [
                'data' => $degaussing_data,
                'completed_count' => $completed_count,
                'pending_count' => $pending_count,
                'in_process_count' => $in_process_count,
                'pagination' => $pagination
            ]);
        }
    }

    public function shreddingStatusCount($connection, $account_id)
    {
        if (ALL_ACOUNTS == true) {
            $command = $connection->createCommand("SELECT status,count(*) as cnt FROM rep_vp_shredding group by status");
        } else {
            $command = $connection->createCommand("SELECT status,count(*) as cnt FROM rep_vp_shredding where account_id=:account_id group by status")
                ->bindValues([":account_id" => $account_id]);
        }
        $shreddingData = $command->queryAll();
        $completed_count = 0;
        $pending_count = 0;
        $in_process_count = 0;
        $total_count = 0;
        foreach ($shreddingData as $row) {
            $status = $row["status"];
            $count = $row["cnt"] ? $row["cnt"] : 0;
            $total_count += $count;
            if ($status == 5) {
                $completed_count += $count;
            } else if ($status == 4) {
                $in_process_count += $count;
            } else {
                $pending_count += $count;
            }
        }
        return ["total_count" => $total_count, "completed_count" => $completed_count, "pending_count" => $pending_count, "in_process_count" => $in_process_count];
    }

    public function actionShredding()
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            $vendor_account_id = Yii::$app->user->identity->vendor_account_name ?? null;
            if ($vendor_account_id) {
                $vendor_account_name = $this->getVendorName($vendor_account_id);
            } else {
                $vendor_account_name = "xxxx";
            }
            //fetch  data
            $static_logic = false;
            $pageSize = 10;
            $currentPage = Yii::$app->request->get('page', 0);

            $page = isset($currentPage) && !empty($currentPage) && is_numeric($currentPage) ? $currentPage : 1;
            $page = (int) $page;
            $size = 10;
            $offset = (int) ($page - 1) * $size;

            $connection = Yii::$app->db;

            $command = $connection->createCommand("SELECT count(*) FROM rep_vp_shredding");

            // Get the total count of records (needed for pagination)
            $totalCount = $command->queryScalar();
            // Create the Pagination object
            $pagination = [
                'defaultPageSize' => $size,
                'totalCount' => $totalCount,
                'numberOfPages' => ceil($totalCount / $pageSize),
                'page' => $page
            ];

            if (ALL_ACOUNTS == true) {
                $command = $connection->createCommand("SELECT * FROM rep_vp_shredding LIMIT $offset, $size");
            } else {
                $command = $connection->createCommand("SELECT * FROM rep_vp_shredding where account_id=:account_id order by id desc LIMIT $offset, $size")
                    ->bindValues([":account_id" => $vendor_account_id]);
            }

            // Fetch the data with pagination
            $shredding_data = $command->queryAll();

            if (empty($shredding_data))
                $shredding_data = [];
            $shredding_status_counts = $this->shreddingStatusCount($connection, $vendor_account_id);
            $completed_count = $shredding_status_counts["completed_count"] ?? 0;
            $pending_count = $shredding_status_counts["pending_count"] ?? 0;
            $in_process_count = $shredding_status_counts["in_process_count"] ?? 0;
            return $this->render('shredding', [
                'data' => $shredding_data,
                'completed_count' => $completed_count,
                'pending_count' => $pending_count,
                'in_process_count' => $in_process_count,
                'pagination' => $pagination
            ]);
        }
    }

    public function actionWeighing()
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            //fetch  data
            $pageSize = 10;
            $currentPage = Yii::$app->request->get('page', 0);

            $page = isset($currentPage) && !empty($currentPage) && is_numeric($currentPage) ? $currentPage : 1;
            $page = (int) $page;
            $size = 10;
            $offset = (int) ($page - 1) * $size;

            $connection = Yii::$app->db;
            //$command = $connection->createCommand("SELECT count(*) FROM dummy_certificate_generated");
            // Get the total count of records (needed for pagination)
            $totalCount = 0;//$command->queryScalar();
            // Create the Pagination object
            $pagination = [
                'defaultPageSize' => $size,
                'totalCount' => $totalCount,
                'numberOfPages' => ceil($totalCount / $pageSize),
                'page' => $page
            ];


            // $command = $connection->createCommand("SELECT * FROM dummy_certificate_generated LIMIT $offset, $size");
            // // Fetch the data with pagination
            // $certificateData = $command->queryAll();

            // if(empty($certificateData)) $certificateData = [];
            $data = [];
            return $this->render('weighing', [
                'data' => $data,
                'pagination' => $pagination
            ]);
        }
    }

    public function certificateStatusCount($connection, $account_id)
    {
        if (ALL_ACOUNTS == true) {
            $command = $connection->createCommand("SELECT 
                    CASE 
                        WHEN green_certificate IS NULL OR green_certificate = '' THEN 'empty_or_null'
                        ELSE 'not_empty'
                    END AS certificate_status,
                    COUNT(*) AS cnt
                FROM rep_vp_certificates
                GROUP BY certificate_status");
        } else {
            $command = $connection->createCommand("SELECT 
                    CASE 
                        WHEN green_certificate IS NULL OR green_certificate = '' THEN 'empty_or_null'
                        ELSE 'not_empty'
                    END AS certificate_status,
                    COUNT(*) AS cnt
                FROM rep_vp_certificates where account_id=:account_id 
                GROUP BY certificate_status")->bindValues([":account_id" => $account_id]);
        }
        $certificate_data = $command->queryAll();
        $completed_count = 0;
        $pending_count = 0;
        $total_count = 0;
        foreach ($certificate_data as $row) {
            $status = $row["certificate_status"];
            $count = $row["cnt"] ? $row["cnt"] : 0;
            $total_count += $count;
            if ($status == "not_empty") {
                $completed_count += $count;
            } else {
                $pending_count += $count;
            }
        }
        return ["total_count" => $total_count, "completed_count" => $completed_count, "pending_count" => $pending_count];
    }
    public function actionCertificate()
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            $vendor_account_id = Yii::$app->user->identity->vendor_account_name ?? null;
            if ($vendor_account_id) {
                $vendor_account_name = $this->getVendorName($vendor_account_id);
            } else {
                $vendor_account_name = "xxxx";
            }
            //fetch  data
            $static_logic = false;
            $pageSize = 10;
            $currentPage = Yii::$app->request->get('page', 0);

            $page = isset($currentPage) && !empty($currentPage) && is_numeric($currentPage) ? $currentPage : 1;
            $page = (int) $page;
            $size = 10;
            $offset = (int) ($page - 1) * $size;

            $connection = Yii::$app->db;

            $command = $connection->createCommand("SELECT count(*) FROM rep_vp_certificates");

            // Get the total count of records (needed for pagination)
            $totalCount = $command->queryScalar();
            // Create the Pagination object
            $pagination = [
                'defaultPageSize' => $size,
                'totalCount' => $totalCount,
                'numberOfPages' => ceil($totalCount / $pageSize),
                'page' => $page
            ];

            if (ALL_ACOUNTS == true) {
                $command = $connection->createCommand("SELECT * FROM rep_vp_certificates LIMIT $offset, $size");
            } else {
                $command = $connection->createCommand("SELECT * FROM rep_vp_certificates where account_id=:account_id order by id desc LIMIT $offset, $size")
                    ->bindValues([":account_id" => $vendor_account_id]);
            }

            $certificate_data = $command->queryAll();

            if (empty($certificate_data))
                $certificate_data = [];
            foreach ($certificate_data as $k => $v) {
                if (empty($v["green_certificate"])) {
                    $certificate_data[$k]["link"] = "";
                } else {
                    $certificate_data[$k]["link"] = Yii::$app->request->hostInfo . "/deshwal/admin/file/download?fileid={$v['green_certificate']}";
                }
            }
            $certificate_status_counts = $this->certificateStatusCount($connection, $vendor_account_id);
            $completed_count = $certificate_status_counts["completed_count"] ?? 0;
            $pending_count = $certificate_status_counts["pending_count"] ?? 0;
            return $this->render('certificate', [
                'data' => $certificate_data,
                'completed_count' => $completed_count,
                'pending_count' => $pending_count,
                'pagination' => $pagination
            ]);
        }
    }
    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        $this->layout = "loginmain";
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->login()) {
                return $this->goBack();
            } else {
                Yii::$app->session->setFlash('error', 'Invalid username or password.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'Please fill in the login form.');
        }

        // print_r($model);die;

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    // public function actionAbout()
    // {
    //     return $this->render('about');
    // }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    // this function commented by ptpatel on date 05-09-2025 and created new function
    /* public function actionResetPassword($token)
     {
         try {
             $model = new ResetPasswordForm($token);
         } catch (InvalidArgumentException $e) {
             throw new BadRequestHttpException($e->getMessage());
         }

         if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
             Yii::$app->session->setFlash('success', 'New password saved.');

             return $this->goHome();
         }

         return $this->render('resetPassword', [
             'model' => $model,
         ]);
     }
     */

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if (($user = $model->verifyEmail()) && Yii::$app->user->login($user)) {
            Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }
    // Optionally override the error action or error handling for more customization
    public function actionInvaliderror()
    {
        // Log that we're entering the error action
        Yii::info('Entering actionError', __METHOD__);

        $exception = Yii::$app->errorHandler->exception;

        // Check if there's an exception
        if ($exception !== null) {
            // Log exception details
            Yii::error($exception->getMessage(), __METHOD__);

            // Set a custom layout based on the exception type
            if ($exception instanceof NotFoundHttpException) {
                $this->layout = 'error_404';
            } else {
                $this->layout = 'error_404';
            }

            return $this->render('error', ['exception' => $exception]);
        }
    }

    public function actionDownload($file_name)
    {
        $path = Yii::getAlias('@webroot') . '/uploads/' . $file_name;
        if (file_exists($path)) {
            return Yii::$app->response->sendFile($path);
        } else {
            echo "File not found";
        }
    }

    public function actionGetBackendFileLink($fieldid)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Fetch your main data (e.g., DB query)
        $data = [
            'fieldid' => $fieldid,
        ];
        $clientId = 'a6e3f5c2-91cd-4df7-9d3b-402b07d843f4';
        $host = Yii::$app->request->hostInfo;
        $backendUrl = "$host/deshwal/admin/file/get-file-link?fileid=$fieldid";

        $ch = curl_init($backendUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "X-Client-Id: $clientId"
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $fileLink = json_decode($response, true);

        if ($fileLink['success']) {
            $data['fileUrl'] = $fileLink['fileUrl'];
        } else {
            $data['fileUrl'] = null;
            $data['fileError'] = $fileLink['message'] ?? 'Error';
        }

        return $data;
    }

    public function actionGetModuleAssets()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $record = Yii::$app->request->post('record');
        $module = Yii::$app->request->post('module');

        if (!$module || !$record) {
            return ['success' => false, 'message' => 'Missing parameters.'];
        }

        $connection = Yii::$app->db;

        // Apply module-specific query
        switch ($module) {
            case 'datawiping':
                $command = $connection->createCommand("SELECT laptop_serial_no as serial_number,certificate as attchment FROM data_wiping_asset_details where datawiping_id=:record_id and deleted = :deleted")
                    ->bindValues([":record_id" => "$record", ":deleted" => 0]);
                $rows = $command->queryAll();
                break;
            case 'degaussing':
                $command = $connection->createCommand("SELECT laptop_serial_no as serial_number,image_after_activity as attchment FROM degaussing_asset_details where degaussinginfo_id=:record_id and deleted = :deleted")
                    ->bindValues([":record_id" => "$record", ":deleted" => 0]);
                $rows = $command->queryAll();
                break;
            case 'drilling':
                $command = $connection->createCommand("SELECT laptop_serial_no as serial_number,certificate as attchment FROM drilling_asset_details where drilling_id=:record_id and deleted = :deleted")
                    ->bindValues([":record_id" => "$record", ":deleted" => 0]);
                $rows = $command->queryAll();
                break;
            case 'shredding':
                $command = $connection->createCommand("SELECT laptop_serial_no as serial_number,certificate as attchment FROM shredding_asset_details where shredding_id=:record_id and deleted = :deleted")
                    ->bindValues([":record_id" => "$record", ":deleted" => 0]);
                $rows = $command->queryAll();
                break;
            default:
                return ['success' => false, 'message' => 'Unknown module.'];
        }

        $attachments = [];

        foreach ($rows as $row) {
            $attachments[] = [
                'serialNumber' => $row['serial_number'],
                'fileUrl' => Yii::$app->request->hostInfo . "/deshwal/admin/file/download?fileid={$row['attchment']}"
            ];
        }

        return ['success' => true, 'attachments' => $attachments];
    }

    public function actionSustainibility()
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            $recycle = [
                'weight' => 0.475,
                'co2' => 0.95,
                'energy' => 0.35,
                'water' => 451.25,
                'rawmaterail' => 332.5,
                'trees' => 43
            ];

            $resale = [
                'weight' => 0.285,
                'co2' => 38.475,
                'energy' => 2.67,
                'water' => 2565,
                'rawmaterail' => 256.5,
                'trees' => 1909
            ];

            $recycleComponents = [
                ['label' => 'Plastics', 'value' => 132.51],
                ['label' => 'Metals', 'value' => 6.25],
                ['label' => 'Glass', 'value' => 2.10],
                ['label' => 'Debris/Landfill', 'value' => 1.20],
                ['label' => 'Mixed', 'value' => 0.85],
                ['label' => 'Paper', 'value' => 0.50],
            ];

            $resaleComponents = [
                ['label' => 'Metals', 'value' => 96.96],
                ['label' => 'Plastics', 'value' => 7.8],
                ['label' => 'Paper', 'value' => 4.6],
                ['label' => 'Mixed', 'value' => 3.2],
                ['label' => 'Glass', 'value' => 1.2],
            ];

            $wasteSegments = [
                ['label' => 'eWaste', 'recycle' => 3.69, 'resale' => 1.2],
                ['label' => 'Metal', 'recycle' => 76.46, 'resale' => 5.5],
                ['label' => 'General', 'recycle' => 24.69, 'resale' => 3.4],
                ['label' => 'Plastic', 'recycle' => 121.19, 'resale' => 8.6],
                ['label' => 'Battery', 'recycle' => 23.23, 'resale' => 2.1],
            ];

            $impactSavings = [
                ['label' => 'Weight (MT)', 'value' => 253.88],
                ['label' => 'CO2 (kg)', 'value' => 410.97],
                ['label' => 'Energy (KL)', 'value' => 330.89],
                ['label' => 'Water (KL)', 'value' => 13010.83],
                ['label' => 'Raw Material (KL)', 'value' => 152.59],
                ['label' => 'Tree (No.)', 'value' => 68],
                ['label' => 'Landfill Avoided (KL)', 'value' => 7006.64],
                ['label' => 'Controlled Landfill (KL)', 'value' => 128089.87],
            ];

            $landfillPie = [
                ['label' => 'Total Landfill Avoided (KL)', 'value' => 11994.25],
                ['label' => 'Total Controlled Landfill (KL)', 'value' => 250.03]
            ];

            return $this->render('sustainibility', [
                'recycle' => $recycle,
                'resale' => $resale,
                'recycleComponents' => $recycleComponents,
                'resaleComponents' => $resaleComponents,
                'wasteSegments' => $wasteSegments,
                'impactSavings' => $impactSavings,
                'landfillPie' => $landfillPie
            ]);



        }
    }

    public function actionEscalation()
    {

        $vendor_account_name = Yii::$app->user->identity->vendor_account_name ?? "";
        if (empty($vendor_account_name))
            return [];
        $connection = Yii::$app->db;
        //first get deshwal isr
        $command = $connection->createCommand("Select concat(user.first_name,' ',user.last_name) as fullname,userid,user.email,user.mobile,user_department_value,user_designation_value  FROM `vendor_account_orgaisation_section` 
        join user on user.id = vendor_account_orgaisation_section.userid  
        left join user_department on user_department.user_department_id = user.department  
        left join user_designation on user_designation.user_designation_id = user.designation  
        WHERE vendoraccid=:vendor_account AND roleid in ('H50') limit 1")
            ->bindValues([":vendor_account" => "$vendor_account_name"]);
        $deshwal_isr = $command->queryOne();

        $command = $connection->createCommand("SELECT concat(user.first_name,' ',user.last_name) as fullname,userid,user.email,user.mobile,user_department_value,user_designation_value  FROM `vendor_account_orgaisation_section` 
        join user on user.id = vendor_account_orgaisation_section.userid  
        left join user_department on user_department.user_department_id = user.department  
        left join user_designation on user_designation.user_designation_id = user.designation  
        WHERE vendoraccid=:vendor_account AND roleid in ('H25') limit 1")
            ->bindValues([":vendor_account" => "$vendor_account_name"]);
        $acc_manager = $command->queryOne();
        //print_r($acc_manager);die;

        $result = array("acc_manager" => $acc_manager, "deshwal_isr" => $deshwal_isr);

        return $this->render('escalation', [
            "acc_manager" => $acc_manager,
            "deshwal_isr" => $deshwal_isr
        ]);
    }

    //code added by ptpatel on date 05-09-2025
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

        $this->layout = "loginmain";

        return $this->render('forgotpassword', [
            'model' => $model,
        ]);
    }

    public function actionResetpassword($token = null)
    {
        $this->layout = "loginmain";

        if (empty($token)) {
            // no token in URL
            Yii::$app->session->setFlash('error', 'Password reset token is missing.');
            return $this->redirect(['site/forgotpassword']);
        }

        $model = new Resetpassword($token);

        // validate token
        $model->validate(['token']);

        if ($model->hasErrors()) {
            return $this->render('resetPassword', ['model' => $model]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->resetPassword()) {
                Yii::$app->session->setFlash('success', 'Password reset successfully.');
                // return $this->goHome();
                return $this->redirect(Yii::$app->user->loginUrl);
            }
        }

        return $this->render('resetPassword', ['model' => $model]);
    }

    public function actionChangepassword()
    {

        // echo Yii::$app->user->isGuest;die;
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            $model = Contacts::find()->where(['contacts_id' => Yii::$app->user->id])->one();
            if (Yii::$app->request->post()) {
                // echo "<pre>";print_r($model);die;
                $password = Yii::$app->request->post('password');
                $hash_pass = Yii::$app->security->generatePasswordHash($password);
                $oldAttributes = $model;
                // echo "<pre>";print_r($model);die;
                if ($model) {
                    // $user = $this->_user;
                    $model->setPassword($password);
                    $model->removePasswordResetToken();

                    if ($model->save(false)) {
                        $modlog = new ModtrackerBasic();

                        $newattributes = array("password" => $hash_pass);
                        $modlog->auditlog($oldAttributes, $newattributes, 'contacts', $model->contacts_id, 7, Yii::$app->user->id);
                        Yii::$app->session->setFlash('success', 'Password changed successfully.');
                    } else
                        Yii::$app->session->setFlash('error', 'Something went wrong.');

                    return $this->refresh();

                }
            } else {
                return $this->render('changepassword', ['model' => $model]);
            }
        }
    }

    //code added by ptpatel on date 05-09-2025
    //added by deepika on 16 oct 2025
    public function actionDownloadpo($file)
    {
        $filePath = Yii::getAlias('@backend/web/uploads/purchase_orders/') . $file;

        if (!file_exists($filePath)) {
            throw new NotFoundHttpException('File not found.');
        }

        return Yii::$app->response->sendFile($filePath, $file);
    }
}
