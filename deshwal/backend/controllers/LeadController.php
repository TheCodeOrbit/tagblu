<?php

namespace backend\controllers;

use app\models\Leaddetails;
use app\models\Leadinformation;
use app\models\LeadStatus;
use app\models\ModtrackerBasic as ModelsModtrackerBasic;
use app\models\ModtrackerDetail as ModelsModtrackerDetail;
use app\models\UserFilter as ModelsUserFilter;
use yii\web\Controller;
use yii\web\Response;
use backend\assets\AdminAsset;
use backend\models\KanbanCard;
use backend\models\ModtrackerBasic;
use backend\models\ModtrackerDetail;
use backend\models\TableList;
use backend\models\Task;
use backend\models\UserFilter;
use DateTime;
use Yii;
use yii\web\BadRequestHttpException;

/**
 * Site controller
 */
class LeadController extends Controller
{

    public function actionTest()
    {
        $this->layout = "main-one";
        return $this->render('test');
    }
    public function actionIndex()
    {

        $openCards = KanbanCard::find()->where(['pipeline_stage' => 'open'])->orderBy(['position' => SORT_ASC])->all();
        $inProgressCards = KanbanCard::find()->where(['pipeline_stage' => 'in_progress'])->orderBy(['position' => SORT_ASC])->all();
        $doneCards = KanbanCard::find()->where(['pipeline_stage' => 'done'])->orderBy(['position' => SORT_ASC])->all();

        // Query to get the columns where tablename is 'leadinformation'
        $columns = (new \yii\db\Query())
            ->select('*')
            ->from('field')
            ->where(['tablename' => 'leadinformation'])
            ->all();

        // Query to get saved columns from 'cvcolumnlist' table
        $savedColumns = (new \yii\db\Query())
            ->select(['fieldid'])
            ->from('cvcolumnlist')
            ->column();

        // Adding visibility for each column based on saved columns
        foreach ($columns as &$column) {
            // Check if column field_id is in savedColumns
            $column['visible'] = in_array($column['fieldid'], $savedColumns);
        }


        $filed_name = (new \yii\db\Query())
            ->select('*')
            ->from('field')
            ->where(['tablename' => 'leadinformation'])
            ->all();


        // Fetch all lead statuses
        $leadStatuses = LeadStatus::find()->where(['>=', 'pipeline_status', 1])->all();

        // Fetch all lead information
        $leadInformation = LeadInformation::find()->all();


        AdminAsset::register(Yii::$app->view);
        $this->layout = "main-one";
        return $this->render('index', [
            'leadStatuses' => $leadStatuses,
            'leadInformation' => $leadInformation,
            'openCards' => $openCards,
            'inProgressCards' => $inProgressCards,
            'doneCards' => $doneCards,
            'columns' => $columns,
            'filed_name' => $filed_name,
        ]);
    }

    public function actionLeadDetails()
    {
        $leadid = Yii::$app->request->get('leadid');

        $user_id = Yii::$app->user->id;


        $leadstatus_value = (new \yii\db\Query())->select('*')->from('lead_status')->where(['>=', 'lead_pipeline_status', 1])->all();
        $active_status_pipeline = (new \yii\db\Query())
            ->select('*')
            ->from('leadinformation')
            ->where(['leadid' => $leadid])
            ->one(); // Use `one()` to fetch a single record if appropriate.

        $visited_link = (new \yii\db\Query())
            ->select([
                'md.prevalue',
                'md.postvalue'
            ])
            ->from(['mb' => 'modtracker_basic']) // Alias for modtracker_basic
            ->innerJoin(
                ['md' => 'modtracker_detail'], // Alias for modtracker_detail
                'mb.id = md.id' // Join condition
            )
            ->where([
                'mb.crmid' => $leadid, // Filter by crmid
                'mb.whodid' => $user_id,
                'md.fieldname' => 'leadstatus', // Filter by fieldname

            ])
            ->all();

        AdminAsset::register(Yii::$app->view);
        $this->layout = "main-new";
        return $this->render('lead-details', [
            'leadstatus_value' => $leadstatus_value,
            'leadid' => $leadid,
            'active_status_pipeline' => $active_status_pipeline,
            'visited_link' => $visited_link,
        ]);
    }

    public function actionUpdateLeadDatetime()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Get POST data
        $leadId = Yii::$app->request->post('lead_id');
        $leadStatus = Yii::$app->request->post('stage');
        $leadstatusid = Yii::$app->request->post('leadstatusid');
        $user_id = Yii::$app->request->post('user_id');


        if (!$leadId || !$leadstatusid) {
            return ['status' => 'error', 'message' => 'Invalid input data.'];
        }

        // Fetch the latest change date and time for the stage
        $lead_datetime = (new \yii\db\Query())
            ->select([
                'mb.changedon',
                'md.fieldname',
                'md.prevalue',
                'md.postvalue'
            ])
            ->from(['mb' => 'modtracker_basic']) // Alias for modtracker_basic
            ->innerJoin(
                ['md' => 'modtracker_detail'], // Alias for modtracker_detail
                'mb.id = md.id' // Join condition
            )
            ->where([
                'mb.crmid' => $leadId, // Filter by crmid
                'md.fieldname' => 'leadstatus', // Filter by fieldname
                'md.postvalue' => $leadstatusid, // Filter by postvalue
            ])
            ->orderBy(['mb.changedon' => SORT_DESC]) // Order by changedon descending
            ->limit(1) // Limit to 1 row
            ->one(); // Fetch a single row


        // Fetch the latest change date and time for the stage
        $visited_link = (new \yii\db\Query())
            ->select([
                'md.prevalue',
                'md.postvalue'
            ])
            ->from(['mb' => 'modtracker_basic']) // Alias for modtracker_basic
            ->innerJoin(
                ['md' => 'modtracker_detail'], // Alias for modtracker_detail
                'mb.id = md.id' // Join condition
            )
            ->where([
                'mb.crmid' => $leadId, // Filter by crmid
                'mb.whodid' => $user_id,
                'md.fieldname' => 'leadstatus', // Filter by fieldname

            ])
            ->all();
        // Limit to row

        if ($lead_datetime) {
            $changedon = new \DateTime($lead_datetime['changedon']);
            $now = new \DateTime();

            // Calculate duration
            $interval = $changedon->diff($now);
            $duration = $interval->format('%d days %h hours %i minutes');

            return [
                'status' => 'success',
                'changedon' => $changedon->format('Y-m-d H:i:s'),
                'duration' => $duration,
                'visited_link' => $visited_link,
                'message' => 'Lead datetime fetched successfully.'
            ];
        }

        return ['status' => 'error', 'message' => 'No data found for the given stage.'];
    }

    public function actionUpdateLeadStatus()
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Get POST data
        $leadId = Yii::$app->request->post('lead_id');
        $leadStatus = Yii::$app->request->post('stage');
        $leadstatusid = Yii::$app->request->post('leadstatusid');
        $userId = Yii::$app->request->post('user_id');

        $changedon = date('Y-m-d H:i:s');
        $module = 'Leads';
        $status = '2';


        if ($leadId && $leadStatus) {
            // Update the leadstatus in the leadinformation table

            if ($leadstatusid) {
                $get_pre_leadstatus_value = (new \yii\db\Query())
                    ->select('leadstatus') // Select leadstatusid from leadinformation
                    ->from('leadinformation')
                    ->where(['leadid' => $leadId])
                    ->scalar(); // Fetch a single scalar value (leadstatusid)
            } else {
                $get_pre_leadstatus_value = null; // Handle case where leadstatusid is not found
            }

            $update = Yii::$app->db->createCommand()
                ->update(
                    'leadinformation', // Table name
                    [
                        'leadstatus' => $leadstatusid,  // Update leadstatus
                    ],
                    ['leadid' => $leadId] // Condition to match the row
                )
                ->execute();
            // Insert into modtracker_basic using Active Record
            $modtracker = new ModelsModtrackerBasic();
            $modtracker->crmid = $leadId;
            $modtracker->module = $module;
            $modtracker->whodid = $userId;
            $modtracker->changedon = $changedon;
            $modtracker->status = $status;
            $modtracker->save();

            $Modtracker_detail = new ModelsModtrackerDetail();
            $Modtracker_detail->id = $modtracker->id;
            $Modtracker_detail->fieldname = 'leadstatus';
            $Modtracker_detail->prevalue = $get_pre_leadstatus_value;
            $Modtracker_detail->postvalue = $leadstatusid;
            $Modtracker_detail->save();

            if ($update && $modtracker->save()) {
                return ['status' => 'success', 'message' => 'Lead status updated and modtracker_basic record created successfully.'];
            } else {
                return ['status' => 'error', 'message' => 'Failed to update or create modtracker_basic record.'];
            }
        }

        return ['status' => 'error', 'message' => 'Invalid input data.'];
    }


    public function actionFilterByLead()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $labelValue = Yii::$app->request->post('labelValue');
            $inputValue = Yii::$app->request->post('inputValue');
            $fieldId = Yii::$app->request->post('fieldId');
            $filterOperator = Yii::$app->request->post('filteroperator');

            if (empty($fieldId)) {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Field ID is required for this filter.',
                ]);
            }

            // Retrieve column name from field table
            $field = (new \yii\db\Query())
                ->select('columnname')
                ->from('field')
                ->where(['fieldid' => $fieldId])
                ->one();

            if (!$field || !isset($field['columnname'])) {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Column not found for the provided field ID.',
                ]);
            }

            $columnName = $field['columnname'];
            // Initialize the query
            $query = Leadinformation::find();

            try {
                // Apply filter based on the operator
                switch ($filterOperator) {
                    case 'Equals':
                        $query->andWhere([$columnName => $inputValue]);
                        break;
                    case 'Not_Equals':
                        $query->andWhere(['<>', $columnName, $inputValue]);
                        break;
                    case 'Contains':
                        $query->andWhere(['like', $columnName, $inputValue]);
                        break;
                    case 'Not_Contains':
                        $query->andWhere(['not like', $columnName, $inputValue]);
                        break;
                    case 'In':
                        $query->andWhere(['in', $columnName, (array)$inputValue]);
                        break;
                    case 'Not_In':
                        $query->andWhere(['not in', $columnName, (array)$inputValue]);
                    case 'is_Empty':
                        $query->andWhere([$columnName => '']);
                        break;
                    case 'is_Not_Empty':
                        $query->andWhere(['<>', $columnName => '']);
                        break;
                    case 'Begins_with':
                        $query->andWhere(['like', $columnName, "$inputValue%", false]);
                        break;
                    default:
                        return $this->asJson([
                            'success' => false,
                            'message' => 'Invalid filter operator.',
                        ]);
                }

                // Execute the query and fetch results
                $filteredLeads = $query->asArray()->all();

                return $this->asJson([
                    'success' => true,
                    'data' => $filteredLeads,
                ]);
            } catch (\Exception $e) {
                // Handle and log errors
                Yii::error("Filter error: " . $e->getMessage(), __METHOD__);
                return $this->asJson([
                    'success' => false,
                    'message' => 'An error occurred while applying the filter: ' . $e->getMessage(),
                ]);
            }
        } else {
            return $this->asJson([
                'success' => false,
                'message' => 'Invalid request.',
            ]);
        }
    }

    public function actionDeleteSelectedLeads()
    {
        if (Yii::$app->request->isAjax) {

            $leadIds = Yii::$app->request->post('leadIds', []);

            // Update the 'deleted' column for the selected leads
            $result = Leadinformation::updateAll(['deleted' => 1], ['leadid' => $leadIds]);

            if ($result) {
                return $this->asJson([
                    'status' => 'success',
                ]);
            } else {
                return $this->asJson([
                    'status' => 'error',
                    'message' => 'Not Delected.',
                ]);
            }
        }

        return $this->asJson([
            'status' => 'error',
            'message' => 'Invalid request method.',
        ]);
    }

    public function actionBulkUpdate()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; // Set response format as JSON

        $request = \Yii::$app->request;

        // Check if the request is POST
        if ($request->isPost) {
            // Retrieve data from the POST request
            $leadIds = $request->post('hiddenLeadIds'); // Comma-separated IDs (e.g., "1,2,4")
            $fieldId = $request->post('selectedValue'); // Field ID
            $userInput = $request->post('userInput'); // User-provided value to update

            // Validate input
            if (empty($leadIds) || empty($fieldId) || $userInput === null) {
                return [
                    'success' => false,
                    'message' => 'Invalid input data.',
                ];
            }

            // Convert lead IDs to an array
            $leadIdsArray = explode(',', $leadIds);
            $leadIdsArray = array_map('intval', $leadIdsArray); // Ensure the values are integers

            try {
                // Fetch the column name from the `field` table based on `fieldid`
                $field = (new \yii\db\Query())
                    ->select('columnname')
                    ->from('field')
                    ->where(['fieldid' => $fieldId, 'tablename' => 'leadinformation'])
                    ->scalar();

                if (!$field) {
                    return [
                        'success' => false,
                        'message' => 'Invalid field ID.',
                    ];
                }

                // Perform the bulk update
                $affectedRows = \Yii::$app->db->createCommand()
                    ->update(
                        'leadinformation',        // Table name
                        [$field => $userInput],   // Column to update and its new value
                        ['leadid' => $leadIdsArray] // Condition: leadid in array
                    )
                    ->execute();

                return [
                    'success' => true,
                    'message' => "Bulk update successful. $affectedRows rows updated.",
                ];
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'Invalid request method.',
        ];
    }


    public function actionSaveFilterByLead()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $labelValue = Yii::$app->request->post('labelValue');
            $inputValue = Yii::$app->request->post('inputValue');
            $fieldId = Yii::$app->request->post('fieldId');
            $filterOperator = Yii::$app->request->post('filteroperator');
            $user_id = Yii::$app->user->id;
            $filterLableName = Yii::$app->request->post('filterLableName');


            // Remove any quotes from the value, if present
            $filterOperator = trim($filterOperator, "'\"");

            // Insert into user_filter using Active Record
            $save_filter = new ModelsUserFilter();
            $save_filter->fieldlabel = $labelValue;
            $save_filter->userinput = $inputValue;
            $save_filter->fieldid = $fieldId;
            $save_filter->userid = $user_id;
            $save_filter->filteroperator = $filterOperator;
            $save_filter->filtername = $filterLableName;

            if ($save_filter->save()) {
                return $this->asJson([
                    'status' => 'success',
                    'message' => 'Lead filter values created successfully.',
                ]);
            } else {
                return $this->asJson([
                    'status' => 'error',
                    'message' => 'Failed to update or create Lead filter record.',
                    'errors' => $save_filter->getErrors(),
                ]);
            }
        }

        return $this->asJson([
            'status' => 'error',
            'message' => 'Invalid request method.',
        ]);
    }


    public function actionShowSaveFilterFeilds()
    {

        if (Yii::$app->request->isAjax) {
            $user_id = Yii::$app->user->id;
            $filterLableName = Yii::$app->request->get('filterLableName'); // Use get() since AJAX uses GET

            // Fetch filter fields from the database
            $filters = (new \yii\db\Query())
                ->select('*')
                ->from('user_filter')
                ->where(['userid' => $user_id, 'filtername' => $filterLableName])
                ->all();

            if ($filters) {
                return $this->asJson([
                    'status' => 'success',
                    'filters' => $filters,
                ]);
            } else {
                return $this->asJson([
                    'status' => 'error',
                    'message' => 'No filter fields found for the given filter name.',
                ]);
            }
        }

        return $this->asJson([
            'status' => 'error',
            'message' => 'Invalid request method.',
        ]);
    }




    public function actionSaveSelectedColumns()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Get selected and deselected columns from the POST request
        $selectedColumns = Yii::$app->request->post('selectedColumns', []);
        $deselectedColumns = Yii::$app->request->post('deselectedColumns', []);

        $deleted = false;

        // Process selected columns: insert if they don't exist
        foreach ($selectedColumns as $column) {
            $exists = (new \yii\db\Query())
                ->from('cvcolumnlist')
                ->where(['fieldid' => $column['field_id']])
                ->exists();

            if (!$exists) {
                Yii::$app->db->createCommand()->insert('cvcolumnlist', [
                    'cvid' => '1', // Update with relevant ID
                    'columnindex' => $column['field_id'],
                    'columnname' => $column['columnname'],
                    'fieldid' => $column['field_id'],
                ])->execute();
            }
        }

        // Process deselected columns: delete if they exist
        foreach ($deselectedColumns as $field_id) {
            $deletedRows = Yii::$app->db->createCommand()->delete('cvcolumnlist', [
                'fieldid' => $field_id,
            ])->execute();

            if ($deletedRows > 0) {
                $deleted = true;
            }
        }

        // Return different messages based on actions performed
        if ($deleted) {
            return ['status' => 'success', 'message' => 'Deselected columns deleted successfully.'];
        } else {
            return ['status' => 'success', 'message' => 'Selected columns saved successfully.'];
        }
    }

    public function actionGetLeadDetailsFields()
    {
        // Get the column names from the `leadinformation` table
        $columns = Yii::$app->db->schema->getTableSchema('leadinformation')->columnNames;

        // Return the column names as a JSON response
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $columns;
    }


    public function actionGetColumnFields()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;


        // Get the columns saved for the current user
        $savedColumns = (new \yii\db\Query())
            ->select(['fieldid'])
            ->from('cvcolumnlist')
            ->where(['cvid' => '1'])
            ->column();
        // print_r($savedColumns);die;

        // Get all columns for the 'leadinformation' table
        $columns = (new \yii\db\Query())
            ->select(['columnname AS field', 'fieldlabel AS headerName', 'fieldid'])
            ->from('field')
            ->where(['tablename' => 'leadinformation'])
            ->all();

        // Set visibility based on whether column is in saved columns
        foreach ($columns as &$column) {
            $column['visible'] = in_array($column['fieldid'], $savedColumns);
        }

        return $columns;
    }

    public function actionGetLeads()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Get pagination parameters from the request
        $start = Yii::$app->request->get('start', 0); // Start index, default to 0
        $limit = Yii::$app->request->get('limit', 10); // Limit (number of records), default to 10

        try {
            // Fetch total count for pagination
            $totalCount = Leadinformation::find()
                ->where(['deleted' => 0])
                ->count();

            // Fetch data with pagination
            $leads = Leadinformation::find()
                ->where(['deleted' => 0]) // Only fetch non-deleted records
                ->offset($start)
                ->limit($limit)
                ->asArray()
                ->all();

            // Return leads and total count
            return [
                'data' => $leads,
                'total' => $totalCount, // Send total count for frontend pagination calculation
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    // public function actionGetLeads()
    // {
    //     \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    //     $leads = Leadinformation::find()
    //         ->where(['deleted' => 0]) // Add condition to filter by deleted == 0
    //         ->asArray()
    //         ->all();

    //     return $leads;
    // }



    public function actionAgTable()
    {
        AdminAsset::register(Yii::$app->view);
        $this->layout = "main-new";
        return $this->render('ag-table');
    }

    public function actionTable()
    {
        AdminAsset::register(Yii::$app->view);
        $this->layout = "main-new";
        return $this->render('table');
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

    public function actionGetTable()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $models = TableList::find()->all();
            $data = [];

            foreach ($models as $model) {
                $data[] = [
                    'first_name' => $model->first_name,
                    'last_name' => $model->last_name,
                    'email' => $model->email,
                    'phone' => $model->phone,
                    'city' => $model->city,
                    'company_name' => $model->company_name,
                ];
            }

            return $data;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function actionUpdateStage()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $leadId = Yii::$app->request->post('leadId');
        $newStatusId = Yii::$app->request->post('newStatusId');

        if ($leadId && $newStatusId) {
            $lead = Leadinformation::findOne($leadId);
            if ($lead) {
                $lead->leadstatus = $newStatusId; // Update the status
                if ($lead->save()) {
                    return ['success' => true];
                } else {
                    return ['success' => false, 'errors' => $lead->getErrors()];
                }
            }
        }

        return ['success' => false];
    }
}
