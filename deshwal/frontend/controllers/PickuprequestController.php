<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\Pagination;
use yii\data\ActiveDataProvider;
use frontend\models\CustomerPickupRequest;
use frontend\models\CustomerPickupAssets;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use yii\web\UploadedFile;
// Manually include the model file from the backend folder
require_once Yii::getAlias('@backend') . '/models/Sourcingdeal.php';
require_once Yii::getAlias('@backend') . '/models/ModtrackerBasic.php';
require_once Yii::getAlias('@backend') . '/models/Notifications.php';
require_once Yii::getAlias('@backend') . '/models/Pickup.php';


use DateTime;
use PHPMailer\PHPMailer\PHPMailer;

$rootDir = dirname(__DIR__);
// echo $rootDir;die;
require_once($rootDir . '/../PHPMailer/src/Exception.php');
require_once($rootDir . '/../PHPMailer/src/PHPMailer.php');
require_once($rootDir . '/../PHPMailer/src/SMTP.php');
require_once($rootDir . '/../api/params.php');

/**
 * Pickuprequest Controller
 */
class PickuprequestController extends Controller
{

    public function actionIndex_old()
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            $vendor_account_name = Yii::$app->user->identity->vendor_account_name ?? "xxxx";
            $user_id = Yii::$app->user->id;
            $pageSize = 10;
            $currentPage = Yii::$app->request->get('page', 0);

            $page = isset($currentPage) && !empty($currentPage) && is_numeric($currentPage) ? $currentPage : 1;
            $page = (int) $page;
            $size = 10;
            $offset = (int) ($page - 1) * $size;

            $connection = Yii::$app->db;
            $command = $connection->createCommand("SELECT count(*) FROM customer_pickup_request WHERE ownerid=:ownerid AND deleted = :deleted")
                ->bindValues([":deleted" => 0, ":ownerid" => "$user_id"]);
            // Get the total count of records (needed for pagination)
            $totalCount = $command->queryScalar();
            // Create the Pagination object
            $pagination = [
                'defaultPageSize' => $size,
                'totalCount' => $totalCount,
                'numberOfPages' => ceil($totalCount / $pageSize),
                'page' => $page
            ];


            $command = $connection->createCommand("SELECT * FROM customer_pickup_request WHERE ownerid=:ownerid AND deleted = :deleted ORDER BY 1 DESC LIMIT $offset, $size")
                ->bindValues([":deleted" => 0, ":ownerid" => "$user_id"]);
            $pickupRequestData = $command->queryAll();

            $command = $connection->createCommand("SELECT count(*) FROM customer_pickup_request WHERE ownerid=:ownerid AND status=1 AND deleted = :deleted")
                ->bindValues([":deleted" => 0, ":ownerid" => "$user_id"]);
            $totalDrafts = $command->queryScalar();

            $command = $connection->createCommand("SELECT count(*) FROM customer_pickup_request WHERE ownerid=:ownerid AND status=2 AND deleted = :deleted")
                ->bindValues([":deleted" => 0, ":ownerid" => "$user_id"]);
            $totalPickupRequested = $command->queryScalar();
            if (empty($pickupRequestData))
                $pickupRequestData = [];

            foreach ($pickupRequestData as $index => $value) {
                $location = $value["location"];
                $preferred_pickup_date = $value["preferred_pickup_date"];
                $sourcingdeal_stage = $value["sourcingdeal_stage"];
                if ($location) {
                    $location_value = $this->vendorLocationValue($connection, $location);
                    $pickupRequestData[$index]["location"] = $location_value;
                }
                if ($preferred_pickup_date) {
                    $preferred_pickup_date_value = $this->validateAndFormatDate($preferred_pickup_date);
                    $pickupRequestData[$index]["preferred_pickup_date"] = $preferred_pickup_date_value;
                }
                if ($sourcingdeal_stage) {
                    $sourcingdeal_stage_value = $this->getSourcingdealstage($sourcingdeal_stage);
                    $pickupRequestData[$index]["sourcingdeal_stage"] = $sourcingdeal_stage_value;

                }
                $pickupRequestData[$index]["status_value"] = $this->pickupRequestStatusValue($connection, $value["status"]);
            }

            return $this->render('index', [
                'pickupRequestData' => $pickupRequestData,
                'pagination' => $pagination,
                'total_drafts' => $totalDrafts ?? 0,
                'pickup_requested' => $totalPickupRequested ?? 0
            ]);
        }
    }
    //added by deepika on 31 oct 2025
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        } else {
            $vendor_account_name = Yii::$app->user->identity->vendor_account_name ?? "xxxx";
            $user_id = Yii::$app->user->id;
            $pageSize = 10;
            $currentPage = Yii::$app->request->get('page', 0);

            $page = isset($currentPage) && !empty($currentPage) && is_numeric($currentPage) ? $currentPage : 1;
            $page = (int) $page;
            $size = 10;
            $offset = (int) ($page - 1) * $size;

            $connection = Yii::$app->db;
            $command = $connection->createCommand("SELECT count(*) FROM customer_pickup_request WHERE ownerid=:ownerid AND deleted = :deleted")
                ->bindValues([":deleted" => 0, ":ownerid" => "$user_id"]);
            // Get the total count of records (needed for pagination)
            $totalCount = $command->queryScalar();
            // Create the Pagination object
            $pagination = [
                'defaultPageSize' => $size,
                'totalCount' => $totalCount,
                'numberOfPages' => ceil($totalCount / $pageSize),
                'page' => $page
            ];


            $command = $connection->createCommand("SELECT * FROM customer_pickup_request WHERE ownerid=:ownerid AND deleted = :deleted ORDER BY 1 DESC LIMIT $offset, $size")
                ->bindValues([":deleted" => 0, ":ownerid" => "$user_id"]);
            $pickupRequestData = $command->queryAll();

            $command = $connection->createCommand("SELECT count(*) FROM customer_pickup_request WHERE ownerid=:ownerid AND status=1 AND deleted = :deleted")
                ->bindValues([":deleted" => 0, ":ownerid" => "$user_id"]);
            $totalDrafts = $command->queryScalar();

            $command = $connection->createCommand("SELECT count(*) FROM customer_pickup_request WHERE ownerid=:ownerid AND status=2 AND deleted = :deleted")
                ->bindValues([":deleted" => 0, ":ownerid" => "$user_id"]);
            $totalPickupRequested = $command->queryScalar();
            if (empty($pickupRequestData))
                $pickupRequestData = [];

            foreach ($pickupRequestData as $index => $value) {
                $location = $value["location"];
                $preferred_pickup_date = $value["preferred_pickup_date"];
                $sourcingdeal_stage = $value["sourcingdeal_stage"];
                if ($location) {
                    $location_value = $this->vendorLocationValue($connection, $location);
                    $pickupRequestData[$index]["location"] = $location_value;
                }
                if ($preferred_pickup_date) {
                    $preferred_pickup_date_value = $this->validateAndFormatDate($preferred_pickup_date);
                    $pickupRequestData[$index]["preferred_pickup_date"] = $preferred_pickup_date_value;
                }
                if ($sourcingdeal_stage) {
                    $sourcingdeal_stage_value = $this->getSourcingdealstage($sourcingdeal_stage);
                    $pickupRequestData[$index]["sourcingdeal_stage"] = $sourcingdeal_stage_value;

                }
                $pickupRequestData[$index]["status_value"] = $this->pickupRequestStatusValue($connection, $value["status"]);
            }
            $sql = "
             SELECT *
FROM (
  
    SELECT 
        pr.account_name,
        pr.pickup_request_id,
        pr.pickup_request,
        DATE(pr.createdtime) AS pickup_request_date,
        prs.value AS pickup_request_status,
        pr.location,
        pr.preferred_pickup_date,
        pr.status,

        sd.sourcingdeal_id,
        sd.sourcingdeal_no,
        sds.stage_value AS sourcing_deal_status,
        DATE(sd.createdtime) AS sourcing_date,

        p.pickup_id,
        p.pickup_no,
        pps.pickup_status_value AS pickup_status,
        p.pickup_status AS pickup_status_id,
        DATE(p.createdtime) AS pickup_date,

        -- identifier priority: pickup_request → pickup_no
        CASE 
            WHEN pr.pickup_request IS NOT NULL AND pr.pickup_request <> '' 
                THEN pr.pickup_request 
            ELSE p.pickup_no 
        END AS pickup_identifier,

        -- overall status priority: pickup → sourcing deal → pickup request
        CASE 
            WHEN pps.pickup_status_value IS NOT NULL THEN pps.pickup_status_value
            -- WHEN sds.stage_value IS NOT NULL THEN sds.stage_value
            WHEN prs.value IS NOT NULL THEN prs.value
            ELSE '' 
        END AS overall_status,
        p.pickup_address as pickup_location


    FROM customer_pickup_request pr
    JOIN sourcingdeal sd ON sd.pickup_request = pr.pickup_request_id     
    LEFT JOIN sourcingdeal_stage sds ON sds.stage_id = sd.stage
    LEFT JOIN pickup p ON p.opportuity_name = sd.sourcingdeal_id
    LEFT JOIN pick_pickup_status pps ON pps.pickup_status_id = p.pickup_status
    LEFT JOIN pickup_request_status prs ON prs.id = pr.status
    WHERE pr.account_name = :account_name  
      AND pr.ownerid = :ownerid 
      AND pr.deleted = :deleted

 
    UNION ALL
  
    SELECT
        sd.vendor_account_name AS account_name,
        NULL AS pickup_request_id,
        NULL AS pickup_request,
        NULL AS pickup_request_date,
        NULL AS pickup_request_status,
        NULL AS location,
        NULL AS preferred_pickup_date,
        NULL AS status,

        sd.sourcingdeal_id,
        sd.sourcingdeal_no,
        sds.stage_value AS sourcing_deal_status,
        DATE(sd.createdtime) AS sourcing_date,

        p.pickup_id,
        p.pickup_no,
        pps.pickup_status_value AS pickup_status,
        p.pickup_status AS pickup_status_id,
        DATE(p.createdtime) AS pickup_date,

        p.pickup_no AS pickup_identifier,
       
        CASE 
            WHEN pps.pickup_status_value IS NOT NULL THEN pps.pickup_status_value
            -- WHEN sds.stage_value IS NOT NULL THEN sds.stage_value
            ELSE '' 
        END AS overall_status,
        p.pickup_address as pickup_location


    FROM sourcingdeal sd
    LEFT JOIN sourcingdeal_stage sds ON sds.stage_id = sd.stage
    LEFT JOIN pickup p ON p.opportuity_name = sd.sourcingdeal_id
    LEFT JOIN pick_pickup_status pps ON pps.pickup_status_id = p.pickup_status
    WHERE (sd.pickup_request IS NULL OR sd.pickup_request = '')
      AND sd.vendor_account_name = :account_name 
      AND sd.deleted = :deleted 


   
    UNION ALL
   
    SELECT 
        pr.account_name,
        pr.pickup_request_id,
        pr.pickup_request,
        DATE(pr.createdtime) AS pickup_request_date,
        prs.value AS pickup_request_status,
        pr.location,
        pr.preferred_pickup_date,
        pr.status,

        NULL AS sourcingdeal_id,
        NULL AS sourcingdeal_no,
        NULL AS sourcing_deal_status,
        NULL AS sourcing_date,

        NULL AS pickup_id,
        NULL AS pickup_no,
        NULL AS pickup_status,
        NULL AS pickup_status_id,
        NULL AS pickup_date,

        pr.pickup_request AS pickup_identifier,
        prs.value AS overall_status,
        '' as pickup_location


    FROM customer_pickup_request pr
    LEFT JOIN pickup_request_status prs ON prs.id = pr.status
    WHERE pr.account_name = :account_name  
      AND pr.ownerid = :ownerid 
      AND pr.deleted = :deleted
      AND pr.pickup_request_id NOT IN (
          SELECT sd.pickup_request 
          FROM sourcingdeal sd 
          WHERE sd.pickup_request IS NOT NULL AND sd.pickup_request <> ''
      )
) AS combined
 ORDER BY GREATEST(
        IFNULL(pickup_request_date, '0000-00-00'),
        IFNULL(sourcing_date, '0000-00-00')
    ) DESC

";
$sql1 = $sql." LIMIT $offset, $size;";
            $command = $connection->createCommand($sql1)
                ->bindValues([":deleted" => 0, ":ownerid" => "$user_id", ":account_name" => "$vendor_account_name"]);
            $pickupRequestData2 = $command->queryAll();
            //echo $sql."<br><br>";
            $countSql = "SELECT COUNT(*) AS total FROM ($sql) AS combined";

            $countCommand = $connection->createCommand($countSql)
                ->bindValues([
                    ":deleted" => 0,
                    ":ownerid" => $user_id,
                    ":account_name" => $vendor_account_name,
                ]);

            
            $totalCount = $countCommand->queryScalar();

            ///get pickup created number
                $countSql = "
        SELECT COUNT(*) AS total_pickup_status_2
        FROM (
            $sql
        ) AS all_data
        WHERE pickup_status_id = 2
        ";

        $countCommand = $connection->createCommand($countSql)
            ->bindValues([
                ':account_name' => $vendor_account_name,
                ':ownerid' => $user_id,
                ':deleted' => 0,
            ]);
        // echo $countCommand->getRawSql();die;

        $totalPickcreatedCount = $countCommand->queryScalar();
         

            // Create the Pagination object
            $pagination = [
                'defaultPageSize' => $size,
                'totalCount' => $totalCount,
                'numberOfPages' => ceil($totalCount / $pageSize),
                'page' => $page
            ];

            if (empty($pickupRequestData2))
                $pickupRequestData2 = [];

            foreach ($pickupRequestData2 as $index => $value) {
                $location = $value["location"];
                $preferred_pickup_date = $value["preferred_pickup_date"];
                // $sourcingdeal_stage = $value["sourcingdeal_stage"];
                if ($location) {
                    $location_value = $this->vendorLocationValue($connection, $location);
                    $pickupRequestData2[$index]["location"] = $location_value;
                }
                if ($preferred_pickup_date) {
                    $preferred_pickup_date_value = $this->validateAndFormatDate($preferred_pickup_date);
                    $pickupRequestData2[$index]["preferred_pickup_date"] = $preferred_pickup_date_value;
                }
                // if ($sourcingdeal_stage) {
                //     $sourcingdeal_stage_value = $this->getSourcingdealstage($sourcingdeal_stage);
                //     $pickupRequestData[$index]["sourcingdeal_stage"] = $sourcingdeal_stage_value;

                // }
                $pickupRequestData2[$index]["status_value"] = $this->pickupRequestStatusValue($connection, $value["status"]);
            }
            // echo "<pre>";
            // print_r($pickupRequestData2);die;

            return $this->render('detail', [
                'pickupRequestData' => $pickupRequestData2,
                'pagination' => $pagination,
                'total_drafts' => $totalDrafts ?? 0,
                'pickup_requested' => $totalPickupRequested ?? 0,
                'totalPickcreatedCount'=>$totalPickcreatedCount ?? 0
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

            foreach ($pickupRequestData as $index => $value) {
                $location = $value["location"];
                $preferred_pickup_date = $value["preferred_pickup_date"];
                if ($location) {
                    $location_value = $this->vendorLocationValue($connection, $location);
                    $pickupRequestData[$index]["location"] = $location_value;
                }
                if ($preferred_pickup_date) {
                    $preferred_pickup_date_value = $this->validateAndFormatDate($preferred_pickup_date);
                    $pickupRequestData[$index]["preferred_pickup_date"] = $preferred_pickup_date_value;
                }
                $pickupRequestData[$index]["status_value"] = $this->pickupRequestStatusValue($connection, $value["status"]);
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

    public function actionView($pickup_request_id)
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        }
        $connection = Yii::$app->db;
        $user_id = Yii::$app->user->id;
        $command = $connection->createCommand("SELECT * FROM customer_pickup_request WHERE pickup_request_id=:pickup_request_id AND ownerid=:ownerid AND deleted = :deleted")
            ->bindValues([":deleted" => 0, ":ownerid" => "$user_id", ":pickup_request_id" => "$pickup_request_id"]);
        $pickupRequestData = $command->queryOne();

        if (empty($pickupRequestData))
            $pickupRequestData = [];
        if (!empty($pickupRequestData)) {
            $location = $pickupRequestData["location"] ?? null;
            $location_type = $pickupRequestData["location_type"] ?? null;
            $additional_info = $pickupRequestData["additional_info"] ?? null;
            $doc_received = $pickupRequestData["doc_received"] ?? null;
            $pickup_document = $pickupRequestData["pickup_document"] ?? null;
            $preferred_pickup_date = $pickupRequestData["preferred_pickup_date"] ?? null;
            $sourcingdeal_stage = $pickupRequestData["sourcingdeal_stage"] ?? null;
            if ($location) {
                $location_value = $this->vendorLocationValue($connection, $location);
                $pickupRequestData["location"] = $location_value;
            }
            if ($location_type) {
                $location_type_value = $this->locationTypeValue($connection, $location_type);
                $pickupRequestData["location_type"] = $location_type_value;
            }

            if ($additional_info) {
                $additional_info_value = $this->additionalInfoValue($connection, $additional_info);
                $pickupRequestData["additional_info"] = $additional_info_value;
            }
            if ($sourcingdeal_stage) {
                $sourcingdeal_stage_value = $this->getSourcingdealstage($sourcingdeal_stage);
                $pickupRequestData["sourcingdeal_stage"] = $sourcingdeal_stage_value;

            }
            // if($doc_received){

            //     $doc_received_value = $this->documentReceivedValue($connection,$doc_received);
            //     $pickupRequestData["doc_received"] = $doc_received_value;
            // }
            if ($pickup_document) {
                $pickup_document_value = $this->pickupDocumentTypeValue($connection, $pickup_document);
                $pickupRequestData["pickup_document"] = $pickup_document_value;
            }
            if ($preferred_pickup_date) {
                $preferred_pickup_date_value = $this->validateAndFormatDate($preferred_pickup_date);
                $pickupRequestData["preferred_pickup_date"] = $preferred_pickup_date_value;
            }

            $working_timings = $pickupRequestData["working_timings"] ?? null;
            if ($working_timings) {
                $working_timings_value = $this->workingTimingsValue($connection, $working_timings);
                $pickupRequestData["working_timings"] = $working_timings_value;
            }

            $pickupRequestData["status_value"] = $this->pickupRequestStatusValue($connection, $pickupRequestData["status"]);
            $pickupRequestData["assigned_to"] = $this->pickupRequestAssignedto($connection, $pickupRequestData["assigned_to"]);

            $pickupRequestData["extend_time_provision"] = $this->provisionToExtendTimingValue($pickupRequestData["extend_time_provision"]);
            $pickupRequestData["extension_provision"] = $this->extensionProvisionValue($pickupRequestData["extension_provision"]);
            $pickupRequestData["entry_formalities_person"] = $this->entryFormalitiesPersonValue($pickupRequestData["entry_formalities_person"]);
            $pickupRequestData["material_location_floor"] = $this->materialLocationFloorValue($pickupRequestData["material_location_floor"]);
            $pickupRequestData["service_lift"] = $this->serviceLiftValue($pickupRequestData["service_lift"]);
            $pickupRequestData["stairs_space"] = $this->stairsSpaceValue($pickupRequestData["stairs_space"]);
            $pickupRequestData["segregation"] = $this->segregationValue($pickupRequestData["segregation"]);
            $pickupRequestData["space_for_segregation"] = $this->spaceForSegregationValue($pickupRequestData["space_for_segregation"]);
            $pickupRequestData["movement_from_premises"] = $this->movementFromPremisesValue($pickupRequestData["movement_from_premises"]);
            $pickupRequestData["space_for_vehicle"] = $this->spaceForVehicleValue($pickupRequestData["space_for_vehicle"]);
            $pickupRequestData["small_vehicle"] = $this->smallVehicleValue($pickupRequestData["small_vehicle"]);

            $pickupRequestData["vehicle_as_per_height"] = $this->vehicleAsPerHeightValue($pickupRequestData["vehicle_as_per_height"]);
            $pickupRequestData["vehicle_entry_formalities"] = $this->vehicleEntryFormalitiesValue($pickupRequestData["vehicle_entry_formalities"]);
            $pickupRequestData["vehicle_inside_premises"] = $this->vehicleInsidePremisesValue($pickupRequestData["vehicle_inside_premises"]);
        }

        //get history
        $command = $connection->createCommand("SELECT * FROM customer_pickup_request_log WHERE pickup_request_id=:pickup_request_id AND deleted = :deleted order by id desc")
            ->bindValues([":deleted" => 0, ":pickup_request_id" => "$pickup_request_id"]);
        $historyData = $command->queryAll();
        if (!empty($historyData)) {
            foreach ($historyData as $idx => $val) {
                $historyData[$idx]["created_on"] = $this->formatDateTime($val["created_on"]);
                $historyData[$idx]["status"] = $this->pickupRequestStatusValue($connection, $val["status"]);
                $historyData[$idx]["created_by"] = $this->pickupRequestUpdateBy($connection, $val["created_by"]);
            }
        }
        $model = $this->findModel($pickup_request_id);
        $pickupItemsTransformed = [];
        foreach ($model->pickupItems as $item) {
            $itemData = $item->attributes; // Convert to array
            $itemData['product_name_value'] = $this->productNameValue($connection, $itemData["product_name"]);
            $pickupItemsTransformed[] = $itemData;
        }
        return $this->render('view', [
            'model' => $model,
            'pickupRequestData' => $pickupRequestData,
            'pickupItemsTransformed' => $pickupItemsTransformed ?? [],
            'history' => $historyData ?? []
        ]);
    }
     public function actionViewpickup($pickup_id)
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        }
        $connection = Yii::$app->db;
        $user_id = Yii::$app->user->id;
       
      $model = \app\models\Pickup::find()->where(['pickup_id' => $pickup_id])->one();


     
        return $this->render('viewpickup', [
            'model' => $model
        ]);
    }

    public function actionCreate()
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        }
        $vendor_account_name = Yii::$app->user->identity->vendor_account_name ?? "xxxx";
        $model = new CustomerPickupRequest();
        $pickupItems = [new CustomerPickupAssets()]; // Default one item row
        if ($model->load(Yii::$app->request->post())) {
            $model->ownerid = Yii::$app->user->id;
            $model->creatorid = Yii::$app->user->id;
            $model->modifiedby = Yii::$app->user->id;
            $model->createdtime = date('Y-m-d H:i:s');
            $model->modifiedtime = date('Y-m-d H:i:s');
            $model->account_name = $vendor_account_name;
            $action = Yii::$app->request->post('action');
            if ($action === 'draft') {
                $model->status = 1;
            } else if ($action == "submit") {
                $model->status = 2;
            }
            if ($model->pickup_document && is_array($model->pickup_document)) {
                $model->pickup_document = implode(',', $model->pickup_document);
            }
            if ($model->additional_info && is_array($model->additional_info)) {
                $model->additional_info = implode(',', $model->additional_info);
            }

            $uploadedFile = UploadedFile::getInstance($model, 'doc_received');

            if ($uploadedFile) {
                $fileDbName = $uploadedFile->baseName . '.' . $uploadedFile->extension;
                $filePath = 'uploads/' . $fileDbName;

                $uploadedFile->saveAs($filePath);
                $model->doc_received = $fileDbName;
            } else {
                $model->doc_received = $model->getOldAttribute('doc_received');
            }

            $pickupItems = [];
            foreach (Yii::$app->request->post('CustomerPickupAssets', []) as $index => $data) {
                $pickupItem = new CustomerPickupAssets();
                $pickupItem->load([$pickupItem->formName() => $data]);
                $pickupItems[] = $pickupItem;
            }
            $valid = $model->validate();
            $valid = Model::validateMultiple($pickupItems) && $valid;
            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                $model->pickup_request = $this->generatePickupRequestId($transaction);
                try {
                    if ($model->save(false)) {
                        foreach ($pickupItems as $item) {
                            $item->pickup_request_id = $model->pickup_request_id;
                            if (!$item->save(false)) {
                                $transaction->rollBack();
                                return $this->render('create', [
                                    'model' => $model,
                                    'pickupItems' => $pickupItems
                                ]);
                            }
                        }
                        // **Check if 'add_to_permanent_data' is checked**
                        if ($model->add_to_permanent_data && $model->alternate_name && $model->alternate_email && $model->alternate_mobile && $model->location) {
                            $vendorAccount = Yii::$app->user->identity->vendor_account_name ?? null;
                            $location = $model->location;
                            $alternateName = $model->alternate_name;
                            $alternateEmail = $model->alternate_email;
                            $alternateMobile = $model->alternate_mobile;

                            // **Check if the user already exists for given mobile and email**
                            $userExists = Yii::$app->db->createCommand("SELECT COUNT(*) FROM contacts 
                            WHERE vendor_account_name=:vendor_account AND vendor_location = :vendor_location 
                            AND mobile = :mobile and email=:email and deleted=0")
                                ->bindValue(':vendor_location', $location)
                                ->bindValue(':vendor_account', $vendorAccount)
                                ->bindValue(':mobile', $alternateMobile)
                                ->bindValue(':email', $alternateEmail)
                                ->queryScalar();
                            // **If user does not exist, insert a new user**
                            if ($userExists == 0) {
                                //gen contact seq no first
                                $seq_no = $this->generateContactSequenceNo($transaction);
                                Yii::$app->db->createCommand("INSERT INTO contacts (contact_no,vendor_account_name, vendor_location, first_name,mobile,email,deleted,ownerid,createdtime,contact_role,
                                creatorid,modifiedby,modifiedtime) 
                                VALUES (:contact_no,:vendor_account_name, :vendor_location, :first_name,:mobile,:email,0,:ownerid,NOW(),15,:creatorid,:modifiedby,NOW())")
                                    ->bindValue(':contact_no', $seq_no)
                                    ->bindValue(':vendor_location', $location)
                                    ->bindValue(':vendor_account_name', $vendorAccount)
                                    ->bindValue(':first_name', $alternateName)
                                    ->bindValue(':mobile', $alternateMobile)
                                    ->bindValue(':email', $alternateEmail)
                                    ->bindValue(':ownerid', 75)
                                    ->bindValue(':creatorid', 75)
                                    ->bindValue(':modifiedby', 75)
                                    ->execute();
                                $contacts_id = Yii::$app->db->getLastInsertID();
                                $this->UpdateSDSequenceNo("contacts", $contacts_id);

                            }
                        }

                        //start of status log
                        Yii::$app->db->createCommand("INSERT INTO customer_pickup_request_log (pickup_request_id,status, created_on, created_by) 
                        VALUES (:pickup_request_id,:status, :created_on, :created_by)")
                            ->bindValue(':pickup_request_id', $model->pickup_request_id)
                            ->bindValue(':status', $model->status)
                            ->bindValue(':created_on', date('Y-m-d H:i:s'))
                            ->bindValue(':created_by', Yii::$app->user->id)
                            ->execute();
                        // end of status log
                        //save to sourcing deal
                        if ($model->status == 2)//final submit
                        {

                            //get loc name

                            $location = $_POST['CustomerPickupRequest']['location'];
                            $sql = "SELECT vendor_loc_name FROM `vendor_locations` where vendorloc_id=:vendorloc_id";
                            $loc = Yii::$app->db->createCommand($sql)
                                ->bindValue(':vendorloc_id', $location)
                                ->queryOne();
                            $locationname = $loc['vendor_loc_name'] ?? '';

                            //get vendor account
                            $sql = "SELECT acc_name,vendoraccid FROM `vendor_account` join contacts on contacts. 	vendor_account_name=vendor_account.vendoraccid where contacts_id=:contacts_id";
                            $loc = Yii::$app->db->createCommand($sql)
                                ->bindValue(':contacts_id', Yii::$app->user->id)
                                ->queryOne();
                            $vendorAccountname = $loc['acc_name'] ?? '';
                            $vendorAccount = $loc['vendoraccid'] ?? '';

                            //get contact from spoc
                            //Find location spoc
                            $command = Yii::$app->db->createCommand("SELECT contacts_id FROM contacts WHERE FIND_IN_SET(15, contact_role) and vendor_account_name=:vendor_account 
            and vendor_location = :vendor_location and deleted=:deleted")
                                ->bindValues([":vendor_account" => $vendorAccount, ":vendor_location" => "$location", ":deleted" => 0]);
                            $location_spoc_data = $command->queryOne();
                            $contacts_id = $location_spoc_data['contacts_id'] ?? '';


                            $modelleadetail = new \app\models\Sourcingdeal();
                            $data = array();
                            $data['creatorid'] = Yii::$app->user->id;
                            //assign to Deshwal ISR
                            $reports = "SELECT userid as id FROM `vendor_account_orgaisation_section` where roleid = 'H50' and vendoraccid = :vendorAccount";
                            $rest = Yii::$app->db->createCommand($reports)->bindParam(":vendorAccount", $vendorAccount)->queryOne();
                            // print_r($rest);die;
                            if (isset($rest['id']) && !empty($rest['id'])) {
                                $data['ownerid'] = $rest['id'];


                            } else
                                $data['ownerid'] = 143;



                            $data['modifiedby'] = Yii::$app->user->id;
                            $data['createdtime'] = date('Y-m-d H:i:s');
                            $data['modifiedtime'] = date('Y-m-d H:i:s');
                            $data['contact_name'] = (string) $contacts_id;
                            $data['vendor_account_name'] = (string) $vendorAccount;
                            //$data['contact_mobile'] = isset($_POST['CustomerPickupRequest']['spoc_number']) ?? '';
                            $data['stage'] = 30;//Pickup Requested
                            $data['lead_source'] = (string) 14;//customer portal
                            $data['deal_name'] = "Online Request/$vendorAccountname/$locationname";
                            $data['pickup_request'] = $model->pickup_request_id;
                            $data['pickup_request_id'] = $model->pickup_request;
                            //update sourcing deal stage in pickup requset
                            $squpd = "Update `customer_pickup_request` set sourcingdeal_stage = 30 where    pickup_request_id = :pickup_request_id";
                            Yii::$app->db->createCommand($squpd)->bindValue(":pickup_request_id", $model->pickup_request_id)->execute();
                            $data['sourcingdeal_no'] = $this->generateSDSequenceNo();
                            //print_r($data);
                            //die;
                            $modelleadetail->attributes = $data;

                            $modlog = new \app\models\ModtrackerBasic();
                            if ($modelleadetail->validate()) {
                                //audit log

                                if ($modelleadetail->save()) {
                                    $sourcingdeal_id = $modelleadetail->sourcingdeal_id;
                                    $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, 'sourcingdeal', $modelleadetail->sourcingdeal_id, '0', Yii::$app->user->id);

                                    $this->UpdateSDSequenceNo("sourcingdeal", $modelleadetail->sourcingdeal_id);


                                    //send notifications
                                    $notification = new \app\models\Notifications();
                                    $notification->userid = $data['ownerid'];
                                    $notification->message = "A new Customer pickup request ($model->pickup_request) has been created. Please check";
                                    $notification->read_status = 0; // Unread notification
                                    $notification->display_status = 0;
                                    $notification->source_link = Yii::$app->request->baseUrl . "/admin/sourcingdeal/detail?Record=" . $modelleadetail->sourcingdeal_id;
                                    ;
                                    $notification->createdtime = date('Y-m-d H:i:s');
                                    $notification->modifiedtime = date('Y-m-d H:i:s');
                                    if (!$notification->save()) {
                                        //echo 'save failed';
                                        //exit;
                                    }
                                    // echo "<pre>";
                                    // print_r($notification);die;

                                    //send email to ISR
                                    //get mail address
                                    $reportssql = "select `email` from `user` WHERE `user`.`id` =:id ";
                                    $reportsemail = Yii::$app->db->createCommand($reportssql)->bindParam(":id", $data['ownerid'])->queryOne();
                                    $reporttoemail = $reportsemail['email'];
                                    $bodyemail = "Hi,
<br><br>
New request has been raised through customer portal. Please review and submit the same in ERP.
<br><br>
Thanks,<br>
ERP Team";
                                    // code commented and added new code by ptpatel on date 20-08-2025 to send email this code not sent email
                                    /* try {
                                         $result = Yii::$app->mailer->compose()
                                             ->setFrom('erp@Dwmpl.com')
                                             ->setTo(["$reporttoemail",'deepika.tetra@gmail.com', 'rakeshdubey@tetrain.com'])  // multiple recipients
                                             //->setTo([$reporttoemail])  //isr email
                                             // ->setCc(['cc1@example.com', 'cc2@example.com'])      // optional
                                             // ->setBcc(['bcc1@example.com', 'bcc2@example.com'])   // optional
                                             ->setSubject('New Pickup Request (' . $model->pickup_request . ')')
                                             // ->setTextBody('This is the plain text version of the email')
                                             ->setHtmlBody($bodyemail)
                                             ->send();

                                         if ($result) {
                                             // echo "Email sent successfully.";die;
                                         } else {
                                             //echo "Failed to send email.";die;
                                         }
                                     } catch (\Throwable $e) {
                                         echo "Error sending email: " . $e->getMessage();
                                         Yii::error($e->getMessage(), __METHOD__);
                                         //die;
                                     }*/
                                    try {


                                        $mail = new PHPMailer();
                                        $mail->IsSMTP();
                                        $mail->Host = SMTP_HOST;
                                        $mail->Port = SMTP_PORT;
                                        $mail->SMTPAuth = true;
                                        $mail->Username = SMTP_USER;
                                        $mail->Password = SMTP_PASS;
                                        $mail->SMTPSecure = 'tls';     // Enable TLS encryption

                                        $mail->MsgHTML($bodyemail);


                                        $mail->SetFrom('erp@Dwmpl.com');
                                        $mail->isHTML(true);
                                        $mail->Subject = 'New Pickup Request (' . $model->pickup_request . ')';

                                        //$mail->AddAddress(["$reporttoemail",'deepika.tetra@gmail.com', 'rakeshdubey@tetrain.com']);
                                        $mail->addAddress("$reporttoemail");
                                        // $mail->addAddress("deepika.tetra@gmail.com");
                                        $mail->addAddress("rakeshdubey@tetrain.com");
                                        if (!$mail->Send()) {
                                            // echo "Mailer Error: " . $mail->ErrorInfo;

                                            // return false;
                                        } else {
                                            // echo "<br>Mail sent successfully";
                                            // die;
                                            // return true;
                                        }
                                    } catch (\Throwable $e) {
                                        echo "Error sending email: " . $e->getMessage();
                                        Yii::error($e->getMessage(), __METHOD__);
                                        // die;
                                    }
                                    // code modified by ptpatel on date 20 aug 2025 end here
                                } else {
                                    print_r($modelleadetail->getErrors());

                                    die();

                                    Yii::error(
                                        "Failed to save model: " .
                                        json_encode(
                                            $modelleadetail->getErrors()
                                        )
                                    );
                                    return false; // Indicate failure
                                }
                            } else {

                                print_r($modelleadetail->getErrors());

                                die();

                                Yii::error(
                                    "Validation errors: " .
                                    json_encode($modelleadetail->getErrors())
                                );
                                return false; // Indicate validation failure
                            }


                        }
                        //end sourcing deal save



                    }
                    $transaction->commit();

                    return $this->redirect(['index']);
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::error($e->getMessage());
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            } else {
                Yii::error($model->errors);
            }
        }
        $vendorLocations = $this->vendorLocations();
        $locationType = $this->locationType();
        $additionalInfo = $this->additionalInfo();
        $documentReceivedOptions = $this->documentReceivedOptions();
        $pickupDocumentType = $this->pickupDocumentType();
        $connection = Yii::$app->db;
        $workingTimingsOptions = $this->workingTimingsOptions($connection);
        $provisionToExtendTiming = $this->provisionToExtendTiming();
        $products_list = $this->products($connection);
        return $this->render('create', [
            'model' => $model,
            'pickupItems' => $pickupItems,
            'vendorLocations' => $vendorLocations,
            'locationType' => $locationType,
            'additionalInfo' => $additionalInfo,
            'documentReceivedOptions' => $documentReceivedOptions,
            'pickupDocumentType' => $pickupDocumentType,

            'workingTimingsOptions' => $workingTimingsOptions,
            'provisionToExtendTiming' => $provisionToExtendTiming,
            'extensionProvisionOptions' => $this->extensionProvisionOptions(),
            'entryFormalitiesPersonOptions' => $this->entryFormalitiesPersonOptions(),
            'materialLocationFloorOptiond' => $this->materialLocationFloorOptiond(),
            'serviceLiftOptions' => $this->serviceLiftOptions(),
            'stairsSpaceOptions' => $this->stairsSpaceOptions(),
            'segregationOptions' => $this->segregationOptions(),
            'spaceForSegregationOptions' => $this->spaceForSegregationOptions(),
            'movementFromPremisesOptions' => $this->movementFromPremisesOptions(),
            'spaceForVehicleOptions' => $this->spaceForVehicleOptions(),
            'smallVehicleOptions' => $this->smallVehicleOptions(),
            'vehicleAsPerHeightOptions' => $this->vehicleAsPerHeightOptions(),
            'vehicleEntryFormalitiesOptions' => $this->vehicleEntryFormalitiesOptions(),
            'vehicleInsidePremisesOptions' => $this->vehicleInsidePremisesOptions(),
            'products_list' => $products_list ?? []
        ]);
    }

    public function actionUpdate($pickup_request_id)
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        }
        $model = $this->findModel($pickup_request_id);
        if ($model->status == 2) {
            return $this->redirect(['index']);
        }
        if ($model->additional_info) {
            $model->additional_info = explode(",", $model->additional_info);
        }
        if ($model->pickup_document) {
            $model->pickup_document = explode(",", $model->pickup_document);
        }
        $pickupItems = $model->pickupItems;
        // If the pickup items are empty, initialize an empty array for them
        if (empty($pickupItems)) {
            $pickupItems = [new CustomerPickupAssets()];
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->modifiedby = Yii::$app->user->id;
            $model->modifiedtime = date('Y-m-d H:i:s');
            $model->account_name = Yii::$app->user->identity->vendor_account_name ?? null;
            $action = Yii::$app->request->post('action');
            if ($action === 'draft') {
                $model->status = 1;
            } else if ($action == "submit") {
                $model->status = 2;
                $model->assigned_to = 1;
            }
            if ($model->pickup_document && is_array($model->pickup_document)) {
                $model->pickup_document = implode(',', $model->pickup_document);
            }
            if ($model->additional_info && is_array($model->additional_info)) {
                $model->additional_info = implode(',', $model->additional_info);
            }
            $uploadedFile = UploadedFile::getInstance($model, 'doc_received');
            if ($uploadedFile) {
                $fileDbName = $uploadedFile->baseName . '.' . $uploadedFile->extension;
                $filePath = 'uploads/' . $fileDbName;

                $uploadedFile->saveAs($filePath);
                $model->doc_received = $fileDbName;
            } else {
                $model->doc_received = $model->getOldAttribute('doc_received');
            }
            $pickupItems = [];
            foreach (Yii::$app->request->post('CustomerPickupAssets', []) as $index => $data) {
                $pickupItem = new CustomerPickupAssets();
                $pickupItem->load([$pickupItem->formName() => $data]);
                $pickupItems[] = $pickupItem;
            }
            $valid = $model->validate();
            $valid = Model::validateMultiple($pickupItems) && $valid;
            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($model->save(false)) {
                        CustomerPickupAssets::deleteAll(['pickup_request_id' => $model->pickup_request_id]);
                        foreach ($pickupItems as $item) {
                            $item->pickup_request_id = $model->pickup_request_id;
                            $item->save(false);
                        }
                        // **Check if 'add_to_permanent_data' is checked**
                        if ($model->add_to_permanent_data && $model->alternate_name && $model->alternate_email && $model->alternate_mobile && $model->location) {
                            $vendorAccount = Yii::$app->user->identity->vendor_account_name ?? null;
                            $location = $model->location;
                            $alternateName = $model->alternate_name;
                            $alternateEmail = $model->alternate_email;
                            $alternateMobile = $model->alternate_mobile;

                            // **Check if the user already exists for given mobile and email**
                            $userExists = Yii::$app->db->createCommand("SELECT COUNT(*) FROM contacts 
                            WHERE vendor_account_name=:vendor_account AND vendor_location = :vendor_location 
                            AND mobile = :mobile and email=:email and deleted=0")
                                ->bindValue(':vendor_location', $location)
                                ->bindValue(':vendor_account', $vendorAccount)
                                ->bindValue(':mobile', $alternateMobile)
                                ->bindValue(':email', $alternateEmail)
                                ->queryScalar();
                            // **If user does not exist, insert a new user**
                            if ($userExists == 0) {
                                //gen contact seq no first
                                $seq_no = $this->generateContactSequenceNo($transaction);
                                Yii::$app->db->createCommand("INSERT INTO contacts (contact_no,vendor_account_name, vendor_location, first_name,mobile,email,deleted,ownerid,createdtime,contact_role,
                                creatorid,modifiedby,modifiedtime) 
                                VALUES (:contact_no,:vendor_account_name, :vendor_location, :first_name,:mobile,:email,0,:ownerid,NOW(),15,:creatorid,:modifiedby,NOW())")
                                    ->bindValue(':contact_no', $seq_no)
                                    ->bindValue(':vendor_location', $location)
                                    ->bindValue(':vendor_account_name', $vendorAccount)
                                    ->bindValue(':first_name', $alternateName)
                                    ->bindValue(':mobile', $alternateMobile)
                                    ->bindValue(':email', $alternateEmail)
                                    ->bindValue(':ownerid', 75)
                                    ->bindValue(':creatorid', 75)
                                    ->bindValue(':modifiedby', 75)
                                    ->execute();
                            }
                        }

                        //start of status log
                        Yii::$app->db->createCommand("INSERT INTO customer_pickup_request_log (pickup_request_id,status, created_on, created_by) 
                        VALUES (:pickup_request_id,:status, :created_on, :created_by)")
                            ->bindValue(':pickup_request_id', $model->pickup_request_id)
                            ->bindValue(':status', $model->status)
                            ->bindValue(':created_on', date('Y-m-d H:i:s'))
                            ->bindValue(':created_by', Yii::$app->user->id)
                            ->execute();
                        // end of status log
                        //save to sourcing deal
                        if ($model->status == 2)//final submit
                        {

                            //get loc name

                            $location = $_POST['CustomerPickupRequest']['location'];
                            $sql = "SELECT vendor_loc_name FROM `vendor_locations` where vendorloc_id=:vendorloc_id";
                            $loc = Yii::$app->db->createCommand($sql)
                                ->bindValue(':vendorloc_id', $location)
                                ->queryOne();
                            $locationname = $loc['vendor_loc_name'] ?? '';

                            //get vendor account
                            $sql = "SELECT acc_name,vendoraccid FROM `vendor_account` join contacts on contacts. 	vendor_account_name=vendor_account.vendoraccid where contacts_id=:contacts_id";
                            $loc = Yii::$app->db->createCommand($sql)
                                ->bindValue(':contacts_id', Yii::$app->user->id)
                                ->queryOne();
                            $vendorAccountname = $loc['acc_name'] ?? '';
                            $vendorAccount = $loc['vendoraccid'] ?? '';

                            //get contact from spoc
                            //Find location spoc
                            $command = Yii::$app->db->createCommand("SELECT contacts_id FROM contacts WHERE FIND_IN_SET(15, contact_role) and vendor_account_name=:vendor_account 
            and vendor_location = :vendor_location and deleted=:deleted")
                                ->bindValues([":vendor_account" => $vendorAccount, ":vendor_location" => "$location", ":deleted" => 0]);
                            $location_spoc_data = $command->queryOne();
                            $contacts_id = $location_spoc_data['contacts_id'] ?? '';


                            $modelleadetail = new \app\models\Sourcingdeal();
                            $data = array();
                            $data['creatorid'] = Yii::$app->user->id;
                            //assign to Deshwal ISR
                            $reports = "SELECT userid as id FROM `vendor_account_orgaisation_section` where roleid = 'H50' and vendoraccid = :vendorAccount";
                            $rest = Yii::$app->db->createCommand($reports)->bindParam(":vendorAccount", $vendorAccount)->queryOne();
                            // print_r($rest);die;
                            if (isset($rest['id']) && !empty($rest['id'])) {
                                $data['ownerid'] = $rest['id'];



                            } else
                                $data['ownerid'] = 143;
                            $data['modifiedby'] = Yii::$app->user->id;
                            $data['createdtime'] = date('Y-m-d H:i:s');
                            $data['modifiedtime'] = date('Y-m-d H:i:s');
                            $data['contact_name'] = (string) $contacts_id;
                            $data['vendor_account_name'] = (string) $vendorAccount;
                            // $data['contact_mobile'] = isset($_POST['CustomerPickupRequest']['spoc_number']) ?? '';
                            $data['stage'] = 30;//Pickup Requested
                            $data['lead_source'] = (string) 14;//customer portal
                            $data['deal_name'] = "Online Request/$vendorAccountname/$locationname";
                            $data['pickup_request'] = $model->pickup_request_id;
                            $data['pickup_request_id'] = $model->pickup_request;
                            //update sourcing deal stage in pickup requset
                            $squpd = "Update `customer_pickup_request` set sourcingdeal_stage = 30 where    pickup_request_id = :pickup_request_id";
                            Yii::$app->db->createCommand($squpd)->bindValue(":pickup_request_id", $model->pickup_request_id)->execute();

                            $data['sourcingdeal_no'] = $this->generateSDSequenceNo();
                            // print_r($data);
                            // die;
                            $modelleadetail->attributes = $data;

                            $modlog = new \app\models\ModtrackerBasic();
                            if ($modelleadetail->validate()) {
                                //audit log

                                if ($modelleadetail->save()) {
                                    $sourcingdeal_id = $modelleadetail->sourcingdeal_id;
                                    $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, 'sourcingdeal', $modelleadetail->sourcingdeal_id, '0', Yii::$app->user->id);

                                    $this->UpdateSDSequenceNo("sourcingdeal", $modelleadetail->sourcingdeal_id);

                                    //send notifications
                                    $notification = new \app\models\Notifications();
                                    $notification->userid = $data['ownerid'];
                                    $notification->message = "A new Customer pickup request ($model->pickup_request) has been created. Please check";
                                    $notification->read_status = 0; // Unread notification
                                    $notification->display_status = 0;
                                    $notification->source_link = Yii::$app->request->baseUrl . "/admin/sourcingdeal/detail?Record=" . $modelleadetail->sourcingdeal_id;
                                    ;
                                    $notification->createdtime = date('Y-m-d H:i:s');
                                    $notification->modifiedtime = date('Y-m-d H:i:s');
                                    if (!$notification->save()) {
                                        //echo 'save failed';
                                        //exit;
                                    }

                                    //send email to ISR
                                    //get mail address
                                    $reportssql = "select `email` from `user` WHERE `user`.`id` =:id ";
                                    $reportsemail = Yii::$app->db->createCommand($reportssql)->bindParam(":id", $data['ownerid'])->queryOne();
                                    $reporttoemail = $reportsemail['email'];
                                    $bodyemail = "Hi,
<br><br>
New request has been raised through customer portal. Please review and submit the same in ERP.
<br><br>
Thanks,<br>
ERP Team";
                                    //code commented and added by ptpatel on date 20 aug 2025
                                    /*try {
                                        $result = Yii::$app->mailer->compose()
                                            ->setFrom('erp@Dwmpl.com')
                                            ->setTo(["$reporttoemail",'deepika.tetra@gmail.com', 'rakeshdubey@tetrain.com'])  // multiple recipients
                                             //->setTo([$reporttoemail])  //isr email
                                            // ->setCc(['cc1@example.com', 'cc2@example.com'])      // optional
                                            // ->setBcc(['bcc1@example.com', 'bcc2@example.com'])   // optional
                                            ->setSubject('New Pickup Request (' . $model->pickup_request . ')')
                                            // ->setTextBody('This is the plain text version of the email')
                                            ->setHtmlBody($bodyemail)
                                            ->send();

                                        if ($result) {
                                            // echo "Email sent successfully.";die;
                                        } else {
                                            //echo "Failed to send email.";die;
                                        }
                                    } catch (\Throwable $e) {
                                        echo "Error sending email: " . $e->getMessage();
                                        Yii::error($e->getMessage(), __METHOD__);
                                        //die;
                                    }*/
                                    try {
                                        $mail = new PHPMailer();
                                        $mail->IsSMTP();
                                        $mail->Host = SMTP_HOST;
                                        $mail->Port = SMTP_PORT;
                                        $mail->SMTPAuth = true;
                                        $mail->Username = SMTP_USER;
                                        $mail->Password = SMTP_PASS;
                                        $mail->SMTPSecure = 'tls';     // Enable TLS encryption

                                        $mail->MsgHTML($bodyemail);


                                        $mail->SetFrom('erp@Dwmpl.com');
                                        $mail->isHTML(true);
                                        $mail->Subject = 'New Pickup Request (' . $model->pickup_request . ')';

                                        //$mail->AddAddress(["$reporttoemail",'deepika.tetra@gmail.com', 'rakeshdubey@tetrain.com']);
                                        $mail->addAddress("$reporttoemail");
                                        $mail->addAddress("deepika.tetra@gmail.com");
                                        $mail->addAddress("rakeshdubey@tetrain.com");
                                        if (!$mail->Send()) {
                                            // echo "Mailer Error: " . $mail->ErrorInfo;

                                            // return false;
                                        } else {
                                            // echo "<br>Mail sent successfully";
                                            // die;
                                            // return true;
                                        }
                                    } catch (\Throwable $e) {
                                        echo "Error sending email: " . $e->getMessage();
                                        Yii::error($e->getMessage(), __METHOD__);
                                        // die;
                                    }
                                    // code commented and added by ptpatel end here
                                } else {
                                    print_r($modelleadetail->getErrors());

                                    die();

                                    Yii::error(
                                        "Failed to save model: " .
                                        json_encode(
                                            $modelleadetail->getErrors()
                                        )
                                    );
                                    return false; // Indicate failure
                                }
                            } else {

                                print_r($modelleadetail->getErrors());

                                die();

                                Yii::error(
                                    "Validation errors: " .
                                    json_encode($modelleadetail->getErrors())
                                );
                                return false; // Indicate validation failure
                            }

                        }
                        //end sourcing deal save
                    }
                    $transaction->commit();
                    return $this->redirect(['view', 'pickup_request_id' => $model->pickup_request_id]);
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::error($e->getMessage()); // Log the errors
                }
            } else {
                Yii::error($model->errors);
            }
        }

        $vendorLocations = $this->vendorLocations();
        $locationType = $this->locationType();
        $additionalInfo = $this->additionalInfo();
        $documentReceivedOptions = $this->documentReceivedOptions();
        $pickupDocumentType = $this->pickupDocumentType();

        $connection = Yii::$app->db;
        $workingTimingsOptions = $this->workingTimingsOptions($connection);
        $provisionToExtendTiming = $this->provisionToExtendTiming();
        $products_list = $this->products($connection);
        return $this->render('update', [
            'model' => $model,
            'pickupItems' => $pickupItems,
            'vendorLocations' => $vendorLocations ?? [],
            'locationType' => $locationType,
            'additionalInfo' => $additionalInfo,
            'documentReceivedOptions' => $documentReceivedOptions,
            'pickupDocumentType' => $pickupDocumentType,

            'workingTimingsOptions' => $workingTimingsOptions,
            'provisionToExtendTiming' => $provisionToExtendTiming,
            'extensionProvisionOptions' => $this->extensionProvisionOptions(),
            'entryFormalitiesPersonOptions' => $this->entryFormalitiesPersonOptions(),
            'materialLocationFloorOptiond' => $this->materialLocationFloorOptiond(),
            'serviceLiftOptions' => $this->serviceLiftOptions(),
            'stairsSpaceOptions' => $this->stairsSpaceOptions(),
            'segregationOptions' => $this->segregationOptions(),
            'spaceForSegregationOptions' => $this->spaceForSegregationOptions(),
            'movementFromPremisesOptions' => $this->movementFromPremisesOptions(),
            'spaceForVehicleOptions' => $this->spaceForVehicleOptions(),
            'smallVehicleOptions' => $this->smallVehicleOptions(),
            'vehicleAsPerHeightOptions' => $this->vehicleAsPerHeightOptions(),
            'vehicleEntryFormalitiesOptions' => $this->vehicleEntryFormalitiesOptions(),
            'vehicleInsidePremisesOptions' => $this->vehicleInsidePremisesOptions(),
            'products_list' => $products_list ?? []
        ]);
    }
    public function actionGetlocation()
    {
        if (Yii::$app->user->isGuest) {
            // Redirect to login page if user is not logged in
            return $this->redirect(Yii::$app->user->loginUrl);
        }
        $vendor_account = Yii::$app->user->identity->vendor_account_name ?? null;
        $location = Yii::$app->request->post('location');
        $connection = Yii::$app->db;

        $command = $connection
            ->createCommand("SELECT * FROM vendor_locations WHERE vendorloc_id = :vendorloc_id")
            ->bindValue(":vendorloc_id", $location);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            $country = $columns["country"] ?? "";
            $state = $columns["state"] ?? "";
            $city = $columns["city"] ?? "";
            $spoc_id = $columns["spoc_name"] ?? "";
            if ($city) {
                $command = $connection
                    ->createCommand("SELECT city_name FROM city WHERE cityid = :cityid")
                    ->bindValue(":cityid", $city);
                $city_data = $command->queryOne();
                $columns["city"] = $city_data["city_name"] ?? "";
            }
            if ($state) {
                $command = $connection
                    ->createCommand("SELECT state_value FROM state WHERE state_id = :state_id")
                    ->bindValue(":state_id", $state);
                $city_data = $command->queryOne();
                $columns["state"] = $city_data["state_value"] ?? "";
            }
            if ($country) {
                $command = $connection
                    ->createCommand("SELECT country_value FROM country WHERE country_id = :country")
                    ->bindValue(":country", $country);
                $city_data = $command->queryOne();
                $columns["country"] = $city_data["country_value"] ?? "";
            }
            //Find location spoc
            $command = $connection
                ->createCommand("SELECT * FROM contacts WHERE FIND_IN_SET(15, contact_role) and vendor_account_name=:vendor_account 
            and vendor_location = :vendor_location and deleted=:deleted")
                ->bindValues([":vendor_account" => $vendor_account, ":vendor_location" => "$location", ":deleted" => 0]);
            $location_spoc_data = $command->queryOne();
            if (!empty($location_spoc_data)) {
                $spoc_name = $location_spoc_data["first_name"] . " " . $location_spoc_data["last_name"];
                $spoc_email = $location_spoc_data["email"];
                $spoc_mobile = $location_spoc_data["mobile"];
            }
            //Find location escalation spoc
            $command = $connection
                ->createCommand("SELECT * FROM contacts WHERE FIND_IN_SET(16, contact_role) and vendor_account_name=:vendor_account 
            and vendor_location = :vendor_location and deleted=:deleted")
                ->bindValues([":vendor_account" => $vendor_account, ":vendor_location" => "$location", ":deleted" => 0]);
            $location_escalation_spoc_data = $command->queryOne();
            if (!empty($location_escalation_spoc_data)) {
                $escalation_spoc_name = $location_escalation_spoc_data["first_name"] . " " . $location_escalation_spoc_data["last_name"];
                $escalation_spoc_email = $location_escalation_spoc_data["email"];
                $escalation_spoc_mobile = $location_escalation_spoc_data["mobile"];
            }
            $columns["spoc_name"] = $spoc_name ?? "";
            $columns["spoc_email"] = $spoc_email ?? "";
            $columns["spoc_mobile"] = $spoc_mobile ?? "";
            $columns["escalation_spoc_name"] = $escalation_spoc_name ?? "";
            $columns["escalation_spoc_email"] = $escalation_spoc_email ?? "";
            $columns["escalation_spoc_mobile"] = $escalation_spoc_mobile ?? "";
            return $this->asJson([
                'status' => 'success',
                'data' => $columns,
            ]);
        } else {
            return $this->asJson([
                'status' => 'error',
                'message' => 'No Data found.',
                'data' => ''
            ]);
        }
    }
    protected function findModel($pickup_request_id)
    {
        if (($model = CustomerPickupRequest::findOne($pickup_request_id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested item does not exist.');
    }

    public function actionDownload($id)
    {
        $download = CustomerPickupRequest::findOne($id);
        $path = Yii::getAlias('@webroot') . '/uploads/' . $download->doc_received;

        if (file_exists($path)) {
            return Yii::$app->response->sendFile($path);
        } else {
            echo "File not found";
        }
    }
    public function actionSample()
    {
        $filePath = Yii::getAlias('@webroot/files/sample_pickup_items.csv'); // Path outside web directory
        if (file_exists($filePath)) {
            return Yii::$app->response->sendFile($filePath);
        } else {
            throw new \yii\web\NotFoundHttpException('The file does not exist.');
        }
    }
    public function generatePickupRequestId($transaction)
    {
        //RQ-2025-JAN-1019
        $connection = Yii::$app->db;
        $qry1 = "SELECT *,concat(prefix,'-',YEAR(CURDATE()),'-',UPPER(DATE_FORMAT(CURDATE(), '%b')),'-',cur_id) as generated_id from modentity_num  where semodule ='complainer_pickuprequest'";
        $command = $connection->createCommand($qry1);
        $data = $command->queryOne();
        $generated_id = $data["generated_id"];

        Yii::$app->db->createCommand(
            "UPDATE modentity_num SET cur_id = cur_id + 1 WHERE semodule = :semodule"
        )
            ->bindValue(':semodule', 'complainer_pickuprequest')
            ->execute();
        return $generated_id ?? null;
    }
    public function generateContactSequenceNo($transaction)
    {
        $connection = Yii::$app->db;
        $qry1 = "SELECT *,concat(prefix,cur_id) as generated_id from modentity_num  where semodule ='contacts'";
        $command = $connection->createCommand($qry1);
        $data = $command->queryOne();
        $generated_id = $data["generated_id"];

        Yii::$app->db->createCommand(
            "UPDATE modentity_num SET cur_id = cur_id + 1 WHERE semodule = :semodule"
        )
            ->bindValue(':semodule', 'contacts')
            ->execute();
        return $generated_id ?? null;
    }
    public function generateSDSequenceNo()
    {
        $connection = Yii::$app->db;
        $qry1 = "SELECT prefix,cur_id from modentity_num  where semodule ='sourcingdeal'";
        $command = $connection->createCommand($qry1);
        $data = $command->queryOne();
        // $generated_id = $data["generated_id"];

        // Yii::$app->db->createCommand(
        //     "UPDATE modentity_num SET cur_id = cur_id + 1 WHERE semodule = :semodule"
        // )
        //     ->bindValue(':semodule', 'sourcingdeal')
        //     ->execute();
        $prefix = $data['prefix'];
        $cur_id = $data['cur_id'];
        $autoNo = sprintf("%06d", $cur_id);
        //current year
        $cyear = date('Y');
        $orderno = $prefix . '-' . $cyear . '-' . $autoNo;

        return $orderno ?? null;
    }
    public function UpdateSDSequenceNo($module, $crmid)
    {
        $connection = Yii::$app->db;
        $crmid += 1;
        // echo "UPDATE `modentity_num` SET cur_id = $crmid where semodule='$semodule'" ;die;
        try {
            Yii::$app->db->createCommand("UPDATE `modentity_num` SET cur_id = :crmid where semodule=:semodule")
                ->bindParam(":crmid", $crmid)
                ->bindParam(":semodule", $module)
                ->execute();
        } catch (\Exception $e) {
            // Handle the error, e.g. log it or display a message
            Yii::error($e->getMessage());
        }
    }
    public function productNameValue($connection, $selected)
    {
        if (empty($selected))
            return "";
        $command = $connection->createCommand("SELECT sub_catagory_value FROM prod_sub_catagory WHERE sub_catagory_id=:id")
            ->bindValues([":id" => $selected]);
        $data = $command->queryOne();
        return $data && $data["sub_catagory_value"] ? $data["sub_catagory_value"] : "";
    }
    public function pickupRequestStatusValue($connection, $selected)
    {
        if (empty($selected))
            return "";
        $command = $connection->createCommand("SELECT id ,value FROM pickup_request_status WHERE id=:id")
            ->bindValues([":id" => $selected]);
        $data = $command->queryOne();
        return $data && $data["value"] ? $data["value"] : "";
    }
    public function pickupRequestAssignedto($connection, $selected)
    {
        if (empty($selected))
            return "";
        $command = $connection->createCommand("SELECT concat(first_name,' ',last_name) as name FROM user WHERE id=:id")
            ->bindValues([":id" => $selected]);
        $data = $command->queryOne();
        return $data && $data["name"] ? $data["name"] : "";
    }
    public function products($connection)
    {
        $command = $connection->createCommand("SELECT sub_catagory_id ,sub_catagory_value FROM prod_sub_catagory WHERE is_active=:is_active")
            ->bindValues([":is_active" => 1]);
        $products = $command->queryAll();
        $data = ArrayHelper::map($products, 'sub_catagory_id', 'sub_catagory_value');
        return $data ?? [];
    }
    public function pickupRequestUpdateBy($connection, $created_by)
    {
        if (empty($created_by))
            return "";
        $command = $connection->createCommand("SELECT concat(first_name,' ',last_name) as name FROM contacts WHERE contacts_id=:id")
            ->bindValues([":id" => $created_by]);
        $data = $command->queryOne();
        return $data && $data["name"] ? $data["name"] : "";
    }
    public function vendorLocations()
    {
        $vendor_account_name = Yii::$app->user->identity->vendor_account_name ?? "";
        if (empty($vendor_account_name))
            return [];
        $connection = Yii::$app->db;
        $command = $connection->createCommand("SELECT vendorloc_id ,vendor_loc_name FROM vendor_locations WHERE vendor_account=:vendor_account AND deleted = :deleted")
            ->bindValues([":deleted" => 0, ":vendor_account" => "$vendor_account_name"]);
        $locations = $command->queryAll();
        $vendorLocations = ArrayHelper::map($locations, 'vendorloc_id', 'vendor_loc_name');

        return $vendorLocations ?? [];
    }
    public function vendorLocationValue($connection, $selected)
    {
        if (empty($selected))
            return "";
        $command = $connection->createCommand("SELECT vendorloc_id ,vendor_loc_name FROM vendor_locations WHERE vendorloc_id=:vendorloc_id")
            ->bindValues([":vendorloc_id" => "$selected"]);
        $data = $command->queryOne();
        return $data && $data["vendor_loc_name"] ? $data["vendor_loc_name"] : "";
    }
    public function locationType()
    {
        $connection = Yii::$app->db;
        $command = $connection->createCommand("SELECT locationtypeid ,locationtype_value FROM pick_location_type WHERE is_active = :is_active ORDER BY seq_no")
            ->bindValues([":is_active" => 1]);
        $locations = $command->queryAll();
        $locationTypes = ArrayHelper::map($locations, 'locationtypeid', 'locationtype_value');

        return $locationTypes ?? [];
    }
    public function locationTypeValue($connection, $selected)
    {
        if (empty($selected))
            return "";
        $command = $connection->createCommand("SELECT locationtypeid ,locationtype_value FROM pick_location_type WHERE locationtypeid = :locationtypeid")
            ->bindValues([":locationtypeid" => $selected]);
        $data = $command->queryOne();
        return $data && $data["locationtype_value"] ? $data["locationtype_value"] : "";
    }
    public function additionalInfo()
    {
        $connection = Yii::$app->db;
        $command = $connection->createCommand("SELECT additionalinfoid ,additionalinfo_value FROM pick_additional_info WHERE is_active = :is_active ORDER BY seq_no")
            ->bindValues([":is_active" => 1]);
        $additionalInfo = $command->queryAll();
        $locationInfoOptions = ArrayHelper::map($additionalInfo, 'additionalinfoid', 'additionalinfo_value');

        return $locationInfoOptions ?? [];
    }
    public function additionalInfoValue($connection, $selected)
    {
        if (empty($selected))
            return "";
        $command = $connection->createCommand("SELECT additionalinfoid ,additionalinfo_value FROM pick_additional_info WHERE FIND_IN_SET(additionalinfoid,'$selected')");
        $data = $command->queryAll();
        if (empty($data))
            return "";
        return implode(", ", array_column($data, 'additionalinfo_value'));
    }
    public function documentReceivedOptions()
    {
        $connection = Yii::$app->db;
        $command = $connection->createCommand("SELECT documentrecid ,documentrec_value FROM pick_document_received WHERE is_active = :is_active ORDER BY seq_no")
            ->bindValues([":is_active" => 1]);
        $data = $command->queryAll();
        $documentReceivedOptions = ArrayHelper::map($data, 'documentrecid', 'documentrec_value');

        return $documentReceivedOptions ?? [];
    }
    public function documentReceivedValue($connection, $selected)
    {
        if (empty($selected))
            return "";
        $command = $connection->createCommand("SELECT documentrecid ,documentrec_value FROM pick_document_received WHERE FIND_IN_SET(documentrecid,'$selected')");
        $data = $command->queryAll();
        if (empty($data))
            return "";
        return implode(", ", array_column($data, 'documentrec_value'));
    }
    public function pickupDocumentType()
    {
        $connection = Yii::$app->db;
        $command = $connection->createCommand("SELECT documentid ,document_value FROM pickup_document WHERE is_active = :is_active ORDER BY seq_no")
            ->bindValues([":is_active" => 1]);
        $data = $command->queryAll();
        $pickupDocumentTypeOptions = ArrayHelper::map($data, 'documentid', 'document_value');

        return $pickupDocumentTypeOptions ?? [];
    }
    public function pickupDocumentTypeValue($connection, $selected)
    {
        if (empty($selected))
            return "";
        $command = $connection->createCommand("SELECT documentid ,document_value FROM pickup_document WHERE FIND_IN_SET(documentid,'$selected')");
        $data = $command->queryAll();
        if (empty($data))
            return "";
        return implode(", ", array_column($data, 'document_value'));
    }
    public function validateAndFormatDate($input)
    {
        if (empty($input))
            return false;
        $date = DateTime::createFromFormat('Y-m-d', $input) ?:
            DateTime::createFromFormat('d-m-Y', $input) ?:
            DateTime::createFromFormat('m/d/Y', $input);
        if ($date && $date->format('Y-m-d') === date('Y-m-d', strtotime($input))) {
            return $date->format('d/m/Y');
        }
        return $input;
    }
    public function getSourcingdealstage($sourcingdeal_stage)
    {
        $command = Yii::$app->db->createCommand("SELECT stage_value FROM sourcingdeal_stage WHERE stage_id=:sourcingdeal_stage")->bindValue(":sourcingdeal_stage", $sourcingdeal_stage);
        $data = $command->queryOne();
        if (empty($data))
            return "";
        return $data['stage_value'];
    }
    public function formatDateTime($input)
    {
        if (empty($input))
            return "";
        $formattedDate = date('d/m/Y h:i:s a', strtotime($input));
        if ($formattedDate)
            return $formattedDate;
        return $input;
    }
    public function workingTimingsOptions($connection)
    {
        $command = $connection->createCommand("SELECT id ,value FROM pickup_equipment_working_timings WHERE is_active = :is_active ORDER BY seq_no")
            ->bindValues([":is_active" => 1]);
        $data = $command->queryAll();
        $options = ArrayHelper::map($data, 'id', 'value');
        return $options ?? [];
    }
    public function provisionToExtendTiming()
    {
        return [1 => "Yes", 2 => "No"];
    }
    public function extensionProvisionOptions()
    {
        return [1 => "Approval Require from Management over email with details", 2 => "We can extend the timings witout any prior approval"];
    }
    public function entryFormalitiesPersonOptions()
    {
        return [
            1 => "24 hours prior information over email with Aadhar card and mobile number for all team members",
            2 => "Members to show any govt. id at the security gate for entry"
        ];
    }
    public function materialLocationFloorOptiond()
    {
        return [1 => "Single", 2 => "Multiple"];
    }
    public function serviceLiftOptions()
    {
        return [1 => "Yes", 2 => "No"];
    }
    public function stairsSpaceOptions()
    {
        return [1 => "Yes", 2 => "No"];
    }
    public function segregationOptions()
    {
        return [1 => "Yes", 2 => "No"];
    }
    public function spaceForSegregationOptions()
    {
        return [1 => "Yes", 2 => "No"];
    }
    public function movementFromPremisesOptions()
    {
        return [1 => "Ground Floor", 2 => "Basement"];
    }
    public function spaceForVehicleOptions()
    {
        return [1 => "Yes", 2 => "No"];
    }
    public function smallVehicleOptions()
    {
        return [1 => "Yes", 2 => "No"];
    }
    public function vehicleAsPerHeightOptions()
    {
        return [1 => "Bolero", 2 => "Tata Ace", 3 => "Ecco", 4 => "Tractor with Trolley"];
    }
    public function vehicleEntryFormalitiesOptions()
    {
        return [
            1 => "24 hours prior information over email with Vehicle Number, Driver Aadhar card and mobile Number",
            2 => "Driver need to show any Govt ID and follow the entry procedure"
        ];
    }
    public function vehicleInsidePremisesOptions()
    {
        return [1 => "Yes", 2 => "No"];
    }

    public function workingTimingsValue($connection, $selected)
    {
        if (empty($selected))
            return "";
        $command = $connection->createCommand("SELECT id ,value FROM pickup_equipment_working_timings WHERE id = :id")
            ->bindValues([":id" => $selected]);
        $data = $command->queryOne();
        return $data && $data["value"] ? $data["value"] : "";
    }
    public function provisionToExtendTimingValue($selected)
    {
        if ($selected == 1) {
            return "Yes";
        } else if ($selected == 2) {
            return "No";
        }
        return "";
    }
    public function extensionProvisionValue($selected)
    {
        if ($selected == 1) {
            return "Approval Require from Management over email with details";
        } else if ($selected == 2) {
            return "We can extend the timings witout any prior approval";
        }
        return "";
    }
    public function entryFormalitiesPersonValue($selected)
    {
        if ($selected == 1) {
            return "24 hours prior information over email with Aadhar card and mobile number for all team members";
        } else if ($selected == 2) {
            return "Members to show any govt. id at the security gate for entry";
        }
        return "";
    }
    public function materialLocationFloorValue($selected)
    {
        if ($selected == 1) {
            return "Single";
        } else if ($selected == 2) {
            return "Multiple";
        }
        return "";
    }
    public function serviceLiftValue($selected)
    {
        if ($selected == 1) {
            return "Yes";
        } else if ($selected == 2) {
            return "No";
        }
        return "";
    }
    public function stairsSpaceValue($selected)
    {
        if ($selected == 1) {
            return "Yes";
        } else if ($selected == 2) {
            return "No";
        }
        return "";
    }
    public function segregationValue($selected)
    {
        if ($selected == 1) {
            return "Yes";
        } else if ($selected == 2) {
            return "No";
        }
        return "";
    }
    public function spaceForSegregationValue($selected)
    {
        if ($selected == 1) {
            return "Yes";
        } else if ($selected == 2) {
            return "No";
        }
        return "";
    }
    public function movementFromPremisesValue($selected)
    {
        if ($selected == 1) {
            return "Ground Floor";
        } else if ($selected == 2) {
            return "Basement";
        }
        return "";
    }
    public function spaceForVehicleValue($selected)
    {
        if ($selected == 1) {
            return "Yes";
        } else if ($selected == 2) {
            return "No";
        }
        return "";
    }
    public function smallVehicleValue($selected)
    {
        if ($selected == 1) {
            return "Yes";
        } else if ($selected == 2) {
            return "No";
        }
        return "";
    }
    public function vehicleAsPerHeightValue($selected)
    {
        if ($selected == 1) {
            return "Bolero";
        } else if ($selected == 2) {
            return "Tata Ace";
        } else if ($selected == 3) {
            return "Ecco";
        } else if ($selected == 4) {
            return "Tractor with Trolley";
        }
        return "";
    }
    public function vehicleEntryFormalitiesValue($selected)
    {
        if ($selected == 1) {
            return "24 hours prior information over email with Vehicle Number, Driver Aadhar card and mobile Number";
        } else if ($selected == 2) {
            return "Driver need to show any Govt ID and follow the entry procedure";
        }
        return "";
    }
    public function vehicleInsidePremisesValue($selected)
    {
        if ($selected == 1) {
            return "Yes";
        } else if ($selected == 2) {
            return "No";
        }
        return "";
    }

    public function equipmentBoxedOptions($connection)
    {
        $command = $connection->createCommand("SELECT id ,value FROM pickup_equipment_boxed WHERE is_active = :is_active ORDER BY seq_no")
            ->bindValues([":is_active" => 1]);
        $data = $command->queryAll();
        $options = ArrayHelper::map($data, 'id', 'value');
        return $options ?? [];
    }
    public function equipmentGroundFloorOptions($connection)
    {
        $command = $connection->createCommand("SELECT id ,value FROM pickup_equipment_ground_floor WHERE is_active = :is_active ORDER BY seq_no")
            ->bindValues([":is_active" => 1]);
        $data = $command->queryAll();
        $options = ArrayHelper::map($data, 'id', 'value');
        return $options ?? [];
    }
    public function equipmentElevatorsOptions($connection)
    {
        $command = $connection->createCommand("SELECT id ,value FROM pickup_equipment_have_elevators WHERE is_active = :is_active ORDER BY seq_no")
            ->bindValues([":is_active" => 1]);
        $data = $command->queryAll();
        $options = ArrayHelper::map($data, 'id', 'value');
        return $options ?? [];
    }
    public function liftGateTruckOptions($connection)
    {
        $command = $connection->createCommand("SELECT id ,value FROM pickup_equipment_lift_gate_truck WHERE is_active = :is_active ORDER BY seq_no")
            ->bindValues([":is_active" => 1]);
        $data = $command->queryAll();
        $options = ArrayHelper::map($data, 'id', 'value');
        return $options ?? [];
    }
    public function palletsOptions($connection)
    {
        $command = $connection->createCommand("SELECT id ,value FROM pickup_equipment_pallets WHERE is_active = :is_active ORDER BY seq_no")
            ->bindValues([":is_active" => 1]);
        $data = $command->queryAll();
        $options = ArrayHelper::map($data, 'id', 'value');
        return $options ?? [];
    }
    public function pickupPointsOptions($connection)
    {
        $command = $connection->createCommand("SELECT id ,value FROM pickup_equipment_pickup_points WHERE is_active = :is_active ORDER BY seq_no")
            ->bindValues([":is_active" => 1]);
        $data = $command->queryAll();
        $options = ArrayHelper::map($data, 'id', 'value');
        return $options ?? [];
    }

}
