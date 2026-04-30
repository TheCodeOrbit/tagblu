<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contracts".
 *
 * @property int $contract_id
 * @property int|null $ownerid
 * @property int|null $creatorid
 * @property int|null $modifiedby
 * @property string|null $createdtime
 * @property string|null $modifiedtime
 * @property string|null $contract_no
 * @property string|null $access_logs
 * @property string|null $created_date
 * @property string|null $lastmodified_by
 * @property string|null $modification_history
 * @property string|null $payment_terms
 * @property string|null $audit_requirements
 * @property string|null $compliance_checklist
 * @property string|null $insurance_requirements
 * @property string|null $penalties
 * @property string|null $risk_assessment
 * @property string|null $special_terms
 * @property string|null $termination_clause
 * @property string|null $juridiction
 * @property string|null $renewal_terms
 * @property string|null $owner_expiration_notice
 * @property string|null $activated_by
 * @property string|null $activated_date
 * @property string|null $contract_start_date
 * @property string|null $contract_end_date
 * @property string|null $contract_term_months
 * @property string|null $contract_value
 * @property string|null $description
 * @property string|null $budget_allocation
 * @property string|null $contract_category
 * @property string|null $contractid
 * @property string|null $contract_description
 * @property string|null $contract_keywords
 * @property string|null $contract_priority
 * @property string|null $contract_status
 * @property string|null $contract_title
 * @property string|null $contract_type
 * @property string|null $parent_contract_id
 * @property string|null $account_code
 * @property string|null $account_category
 * @property string|null $account_name
 * @property string|null $scope_of_work
 * @property string|null $contact_designation
 * @property string|null $contact_email
 * @property string|null $contact_person_name
 * @property string|null $contact_phone_number
 * @property string|null $hqcorporate_address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $gst_Number
 * @property string|null $pan_number
 * @property string|null $compliance_alert
 * @property int|null $performance_review_cycle
 * @property string|null $renewal_reminder_date
 * @property string|null $contract_name
 * @property int $contract_attached
 * @property int $send_for_review
 * @property string|null $comments
 * @property int $deleted
 */
class Contracts extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contracts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'contract_no', 'access_logs', 'created_date', 'lastmodified_by', 'modification_history', 'payment_terms', 'audit_requirements', 'compliance_checklist', 'insurance_requirements', 'penalties', 'risk_assessment', 'special_terms', 'termination_clause', 'juridiction', 'renewal_terms', 'owner_expiration_notice', 'activated_by', 'activated_date', 'contract_start_date', 'contract_end_date', 'contract_term_months', 'contract_value', 'description', 'budget_allocation', 'contract_category', 'contractid', 'contract_description', 'contract_keywords', 'contract_priority', 'contract_status', 'contract_title', 'contract_type', 'parent_contract_id', 'account_code', 'account_category', 'account_name', 'scope_of_work', 'contact_designation', 'contact_email', 'contact_person_name', 'contact_phone_number', 'hqcorporate_address', 'city', 'state', 'gst_Number', 'pan_number', 'compliance_alert', 'performance_review_cycle', 'renewal_reminder_date', 'contract_name', 'comments'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'performance_review_cycle', 'contract_attached', 'send_for_review', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'created_date', 'activated_date', 'contract_start_date', 'contract_end_date', 'compliance_alert', 'renewal_reminder_date'], 'safe'],
            [['description', 'comments'], 'string'],
            [['contract_no', 'access_logs', 'lastmodified_by', 'modification_history', 'payment_terms', 'audit_requirements', 'compliance_checklist', 'insurance_requirements', 'penalties', 'risk_assessment', 'special_terms', 'termination_clause', 'juridiction', 'renewal_terms', 'owner_expiration_notice', 'activated_by', 'contract_term_months', 'contract_value', 'budget_allocation', 'contract_category', 'contractid', 'contract_description', 'contract_keywords', 'contract_priority', 'contract_status', 'contract_title', 'contract_type', 'account_name', 'scope_of_work', 'contact_designation', 'contact_person_name', 'contact_phone_number', 'hqcorporate_address'], 'string', 'max' => 200],
            [['parent_contract_id', 'account_code', 'account_category', 'contact_email', 'city', 'state', 'contract_name'], 'string', 'max' => 100],
            [['gst_Number', 'pan_number'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'contract_id' => 'Contract ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'contract_no' => 'Contract No',
            'access_logs' => 'Access Logs',
            'created_date' => 'Created Date',
            'lastmodified_by' => 'Lastmodified By',
            'modification_history' => 'Modification History',
            'payment_terms' => 'Payment Terms',
            'audit_requirements' => 'Audit Requirements',
            'compliance_checklist' => 'Compliance Checklist',
            'insurance_requirements' => 'Insurance Requirements',
            'penalties' => 'Penalties',
            'risk_assessment' => 'Risk Assessment',
            'special_terms' => 'Special Terms',
            'termination_clause' => 'Termination Clause',
            'juridiction' => 'Juridiction',
            'renewal_terms' => 'Renewal Terms',
            'owner_expiration_notice' => 'Owner Expiration Notice',
            'activated_by' => 'Activated By',
            'activated_date' => 'Activated Date',
            'contract_start_date' => 'Contract Start Date',
            'contract_end_date' => 'Contract End Date',
            'contract_term_months' => 'Contract Term Months',
            'contract_value' => 'Contract Value',
            'description' => 'Description',
            'budget_allocation' => 'Budget Allocation',
            'contract_category' => 'Contract Category',
            'contractid' => 'Contractid',
            'contract_description' => 'Contract Description',
            'contract_keywords' => 'Contract Keywords',
            'contract_priority' => 'Contract Priority',
            'contract_status' => 'Contract Status',
            'contract_title' => 'Contract Title',
            'contract_type' => 'Contract Type',
            'parent_contract_id' => 'Parent Contract ID',
            'account_code' => 'Account Code',
            'account_category' => 'Account Category',
            'account_name' => 'Account Name',
            'scope_of_work' => 'Scope Of Work',
            'contact_designation' => 'Contact Designation',
            'contact_email' => 'Contact Email',
            'contact_person_name' => 'Contact Person Name',
            'contact_phone_number' => 'Contact Phone Number',
            'hqcorporate_address' => 'Hqcorporate Address',
            'city' => 'City',
            'state' => 'State',
            'gst_Number' => 'Gst Number',
            'pan_number' => 'Pan Number',
            'compliance_alert' => 'Compliance Alert',
            'performance_review_cycle' => 'Performance Review Cycle',
            'renewal_reminder_date' => 'Renewal Reminder Date',
            'contract_name' => 'Contract Name',
            'contract_attached' => 'Contract Attached',
            'send_for_review' => 'Send For Review',
            'comments' => 'Comments',
            'deleted' => 'Deleted',
        ];
    }

}
