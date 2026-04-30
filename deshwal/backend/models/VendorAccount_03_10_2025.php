<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vendor_account".
 *
 * @property int $vendoraccid
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string|null $createdtime
 * @property string|null $modifiedtime
 * @property string $vendor_no
 * @property string|null $acc_name
 * @property string|null $account_short_name
 * @property string|null $parent_account
 * @property int|null $acc_source
 * @property int|null $acc_status
 * @property int|null $acc_type
 * @property string|null $vendor_function
 * @property int|null $industry
 * @property int|null $billing_type
 * @property int|null $zone_region
 * @property int|null $team_name
 * @property float|null $total_investment
 * @property int|null $no_of_ITuser
 * @property string|null $pan_num
 * @property int|null $emp_size
 * @property float|null $annual_revenue
 * @property int|null $deshwal_acc_type
 * @property string|null $fin_year_start
 * @property string|null $cust_code
 * @property string|null $address
 * @property int|null $HO_city
 * @property string|null $country
 * @property string|null $state
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $website
 * @property int|null $rel_manager
 * @property int|null $account_director_rsm
 * @property int|null $bus_manager
 * @property int|null $dell_acc_manager
 * @property int|null $hp_acc_manager
 * @property int|null $sam_acc_manager
 * @property int|null $len_acc_manager
 * @property int|null $ms_acc_manager
 * @property int|null $oem_manager
 * @property int|null $tech_decision_maker
 * @property int|null $comm_decision_maker
 * @property int|null $influencer
 * @property int|null $comp_type
 * @property string|null $credit_limit
 * @property int|null $credit_days
 * @property string|null $pan_no
 * @property string|null $cin
 * @property string|null $IEC_code
 * @property string|null $business_potential
 * @property float|null $outstanding
 * @property int|null $currency
 * @property float|null $exchange_rate
 * @property int|null $kyc_completed
 * @property int|null $submitted_for_kyc
 * @property string|null $kyc_date
 * @property string|null $kyc_completed_by
 * @property string|null $kyc_submitted_by
 * @property string|null $kyc_submitted_date
 * @property int|null $recheck_kyc
 * @property string|null $recheck_kyc_date
 * @property int|null $kyc_msme_status
 * @property string|null $msme_certificate
 * @property int|null $bankaccount_number
 * @property string|null $bank_name
 * @property string|null $ifsc_code
 * @property string|null $payment_terms
 * @property string|null $swift_code
 * @property int|null $login_type
 * @property string|null $password
 * @property string|null $account_category
 * @property string|null $description
 * @property string|null $sub_industry
 * @property string|null $sub_industry_type
 * @property string|null $india_head_office
 * @property string|null $global_head_office
 * @property string|null $external_infulencer
 * @property string|null $credit_rating
 * @property string|null $credit_stage
 * @property string|null $credit_day
 * @property string|null $last_credit_review_date
 * @property string|null $next_credit_review_date
 * @property string|null $account_acquisition_cost
 * @property string|null $account_acquisition_budget
 * @property string|null $account_retention_cost
 * @property string|null $account_retention_budget
 * @property string|null $annual_gross_profit_closed
 * @property string|null $annual_gross_profit_billed
 * @property string|null $devit_registration
 * @property string|null $devit_registration_code
 * @property string|null $devit_cx_manager
 * @property string|null $deshwal_registration
 * @property string|null $deshwal_registration_code
 * @property string|null $deshwal_isr
 * @property string|null $deshwal_account_manager
 * @property string|null $deshwal_cx_manager
 * @property string|null $deshwal_account_stage
 * @property string|null $dell_isr
 * @property string|null $dell_ae
 * @property string|null $dell_cx_manager
 * @property string|null $dell_isg_manager
 * @property string|null $hpi_isr
 * @property string|null $hpi_ae
 * @property string|null $hpi_cx_manager
 * @property string|null $lenovo_isr
 * @property string|null $lenovo_ae
 * @property string|null $lenovo_cx_manager
 * @property string|null $lenovo_isg_manager
 * @property string|null $samsung_isr
 * @property string|null $samsung_ae
 * @property string|null $samsung_cx_manager
 * @property string|null $microsoft_isr
 * @property string|null $microsoft_ae
 * @property string|null $microsoft_cx_manager
 * @property string|null $apple_isr
 * @property string|null $apple_ae
 * @property string|null $apple_cx_manager
 * @property string|null $account_annual_revenue
 * @property string|null $business_potential1
 * @property string|null $india_it_users
 * @property string|null $global_it_users
 * @property string|null $annual_business_target
 * @property string|null $annual_business_closed
 * @property string|null $annual_business_billed
 * @property string|null $account_financial_year
 * @property string|null $allow_lead_generation
 * @property int|null $euc_refresh_policy_id
 * @property string|null $finance_remarks
 * @property string|null $finance_detail_submitted_by
 * @property string|null $finance_detail_submitted_date
 * @property int $finance_detail_completed
 * @property int|null $payment_days
 * @property string|null $acc_receivable_days
 * @property float|null $exposure
 * @property string|null $gst_number
 * @property string|null $state_code
 * @property string|null $legal_entity
 * @property string|null $bank_names
 * @property string|null $account_name
 * @property string|null $account_number
 * @property string|null $bank_ifsc_code
 * @property string|null $bank_swift_code
 * @property string|null $remarks
 * @property string|null $upload_gst_number1
 * @property string|null $pan_number
 * @property string|null $cancelled_cheque
 * @property string|null $vrf_form
 * @property string|null $ca_certified_last_3years
 * @property string|null $3years_financial_statement
 * @property string|null $6months_gst_return
 * @property string|null $6months_bank_statement
 * @property int $deleted
 * @property int $is_temp
 */
class VendorAccount extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vendor_account';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['createdtime', 'modifiedtime', 'acc_name', 'account_short_name', 'parent_account', 'acc_source', 'acc_status', 'acc_type', 'vendor_function', 'industry', 'billing_type', 'zone_region', 'team_name', 'total_investment', 'no_of_ITuser', 'pan_num', 'emp_size', 'annual_revenue', 'deshwal_acc_type', 'fin_year_start', 'cust_code', 'address', 'HO_city', 'country', 'state', 'phone', 'email', 'website', 'rel_manager', 'account_director_rsm', 'bus_manager', 'dell_acc_manager', 'hp_acc_manager', 'sam_acc_manager', 'len_acc_manager', 'ms_acc_manager', 'oem_manager', 'tech_decision_maker', 'comm_decision_maker', 'influencer', 'comp_type', 'credit_limit', 'credit_days', 'pan_no', 'cin', 'IEC_code', 'business_potential', 'outstanding', 'currency', 'exchange_rate', 'kyc_completed', 'kyc_date', 'kyc_completed_by', 'kyc_submitted_by', 'kyc_submitted_date', 'recheck_kyc_date', 'kyc_msme_status', 'msme_certificate', 'bankaccount_number', 'bank_name', 'ifsc_code', 'payment_terms', 'swift_code', 'login_type', 'password', 'account_category', 'description', 'sub_industry', 'sub_industry_type', 'india_head_office', 'global_head_office', 'external_infulencer', 'credit_rating', 'credit_stage', 'credit_day', 'last_credit_review_date', 'next_credit_review_date', 'account_acquisition_cost', 'account_acquisition_budget', 'account_retention_cost', 'account_retention_budget', 'annual_gross_profit_closed', 'annual_gross_profit_billed', 'devit_registration', 'devit_registration_code', 'devit_cx_manager', 'deshwal_registration', 'deshwal_registration_code', 'deshwal_isr', 'deshwal_account_manager', 'deshwal_cx_manager', 'deshwal_account_stage', 'dell_isr', 'dell_ae', 'dell_cx_manager', 'dell_isg_manager', 'hpi_isr', 'hpi_ae', 'hpi_cx_manager', 'lenovo_isr', 'lenovo_ae', 'lenovo_cx_manager', 'lenovo_isg_manager', 'samsung_isr', 'samsung_ae', 'samsung_cx_manager', 'microsoft_isr', 'microsoft_ae', 'microsoft_cx_manager', 'apple_isr', 'apple_ae', 'apple_cx_manager', 'account_annual_revenue', 'business_potential1', 'india_it_users', 'global_it_users', 'annual_business_target', 'annual_business_closed', 'annual_business_billed', 'account_financial_year', 'allow_lead_generation', 'euc_refresh_policy_id', 'finance_remarks', 'finance_detail_submitted_by', 'finance_detail_submitted_date', 'payment_days', 'acc_receivable_days', 'exposure', 'gst_number', 'state_code', 'legal_entity', 'bank_names', 'account_name', 'account_number', 'bank_ifsc_code', 'bank_swift_code', 'remarks', 'upload_gst_number1', 'pan_number', 'cancelled_cheque', 'vrf_form', 'ca_certified_last_3years', '3years_financial_statement', '6months_gst_return', '6months_bank_statement'], 'default', 'value' => null],
            [['address'], 'string', 'max' => 3000],
            [['is_temp'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'vendor_no'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'acc_source', 'acc_status', 'acc_type', 'industry', 'billing_type', 'zone_region', 'team_name', 'no_of_ITuser', 'emp_size', 'deshwal_acc_type', 'HO_city', 'rel_manager', 'account_director_rsm', 'bus_manager', 'dell_acc_manager', 'hp_acc_manager', 'sam_acc_manager', 'len_acc_manager', 'ms_acc_manager', 'oem_manager', 'tech_decision_maker', 'comm_decision_maker', 'influencer', 'comp_type', 'credit_days', 'currency', 'kyc_completed', 'submitted_for_kyc', 'recheck_kyc', 'kyc_msme_status', 'bankaccount_number', 'login_type', 'euc_refresh_policy_id', 'finance_detail_completed', 'payment_days', 'deleted', 'is_temp'], 'integer'],
            [['createdtime', 'modifiedtime', 'kyc_date', 'kyc_submitted_date', 'recheck_kyc_date', 'last_credit_review_date', 'next_credit_review_date', 'finance_detail_submitted_date'], 'safe'],
            [['total_investment', 'annual_revenue', 'outstanding', 'exchange_rate', 'exposure'], 'number'],
            [['description'], 'string'],
            [['vendor_no', 'credit_limit', 'pan_no', 'ifsc_code'], 'string', 'max' => 50],
            [['acc_name', 'parent_account', 'pan_num', 'email', 'website', 'IEC_code', 'password', 'acc_receivable_days', 'legal_entity', 'bank_names', 'account_name', 'account_number', 'bank_ifsc_code', 'bank_swift_code'], 'string', 'max' => 100],
            [['account_short_name', 'fin_year_start', 'cust_code', 'country', 'state', 'cin', 'business_potential', 'kyc_completed_by', 'kyc_submitted_by', 'msme_certificate', 'bank_name', 'payment_terms', 'swift_code', 'account_category', 'sub_industry', 'sub_industry_type', 'india_head_office', 'global_head_office', 'external_infulencer', 'credit_rating', 'credit_stage', 'credit_day', 'account_acquisition_cost', 'account_acquisition_budget', 'account_retention_cost', 'account_retention_budget', 'annual_gross_profit_closed', 'annual_gross_profit_billed', 'devit_registration', 'devit_registration_code', 'devit_cx_manager', 'deshwal_registration', 'deshwal_registration_code', 'deshwal_isr', 'deshwal_account_manager', 'deshwal_cx_manager', 'deshwal_account_stage', 'dell_isr', 'dell_ae', 'dell_cx_manager', 'dell_isg_manager', 'hpi_isr', 'hpi_ae', 'hpi_cx_manager', 'lenovo_isr', 'lenovo_ae', 'lenovo_cx_manager', 'lenovo_isg_manager', 'samsung_isr', 'samsung_ae', 'samsung_cx_manager', 'microsoft_isr', 'microsoft_ae', 'microsoft_cx_manager', 'apple_isr', 'apple_ae', 'apple_cx_manager', 'account_annual_revenue', 'business_potential1', 'india_it_users', 'global_it_users', 'annual_business_target', 'annual_business_closed', 'annual_business_billed', 'account_financial_year', 'allow_lead_generation', 'finance_remarks', 'finance_detail_submitted_by', 'gst_number', 'state_code', 'remarks', 'upload_gst_number1', 'pan_number', 'cancelled_cheque', 'vrf_form', 'ca_certified_last_3years', '3years_financial_statement', '6months_gst_return', '6months_bank_statement'], 'string', 'max' => 200],
            [['vendor_function'], 'string', 'max' => 25],
            [['phone'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'vendoraccid' => 'Vendoraccid',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'vendor_no' => 'Vendor No',
            'acc_name' => 'Acc Name',
            'account_short_name' => 'Account Short Name',
            'parent_account' => 'Parent Account',
            'acc_source' => 'Acc Source',
            'acc_status' => 'Acc Status',
            'acc_type' => 'Acc Type',
            'vendor_function' => 'Vendor Function',
            'industry' => 'Industry',
            'billing_type' => 'Billing Type',
            'zone_region' => 'Zone Region',
            'team_name' => 'Team Name',
            'total_investment' => 'Total Investment',
            'no_of_ITuser' => 'No Of I Tuser',
            'pan_num' => 'Pan Num',
            'emp_size' => 'Emp Size',
            'annual_revenue' => 'Annual Revenue',
            'deshwal_acc_type' => 'Deshwal Acc Type',
            'fin_year_start' => 'Fin Year Start',
            'cust_code' => 'Cust Code',
            'address' => 'Address',
            'HO_city' => 'Ho City',
            'country' => 'Country',
            'state' => 'State',
            'phone' => 'Phone',
            'email' => 'Email',
            'website' => 'Website',
            'rel_manager' => 'Rel Manager',
            'account_director_rsm' => 'Account Director Rsm',
            'bus_manager' => 'Bus Manager',
            'dell_acc_manager' => 'Dell Acc Manager',
            'hp_acc_manager' => 'Hp Acc Manager',
            'sam_acc_manager' => 'Sam Acc Manager',
            'len_acc_manager' => 'Len Acc Manager',
            'ms_acc_manager' => 'Ms Acc Manager',
            'oem_manager' => 'Oem Manager',
            'tech_decision_maker' => 'Tech Decision Maker',
            'comm_decision_maker' => 'Comm Decision Maker',
            'influencer' => 'Influencer',
            'comp_type' => 'Comp Type',
            'credit_limit' => 'Credit Limit',
            'credit_days' => 'Credit Days',
            'pan_no' => 'Pan No',
            'cin' => 'Cin',
            'IEC_code' => 'Iec Code',
            'business_potential' => 'Business Potential',
            'outstanding' => 'Outstanding',
            'currency' => 'Currency',
            'exchange_rate' => 'Exchange Rate',
            'kyc_completed' => 'Kyc Completed',
            'submitted_for_kyc' => 'Submitted For Kyc',
            'kyc_date' => 'Kyc Date',
            'kyc_completed_by' => 'Kyc Completed By',
            'kyc_submitted_by' => 'Kyc Submitted By',
            'kyc_submitted_date' => 'Kyc Submitted Date',
            'recheck_kyc' => 'Recheck Kyc',
            'recheck_kyc_date' => 'Recheck Kyc Date',
            'kyc_msme_status' => 'Kyc Msme Status',
            'msme_certificate' => 'Msme Certificate',
            'bankaccount_number' => 'Bankaccount Number',
            'bank_name' => 'Bank Name',
            'ifsc_code' => 'Ifsc Code',
            'payment_terms' => 'Payment Terms',
            'swift_code' => 'Swift Code',
            'login_type' => 'Login Type',
            'password' => 'Password',
            'account_category' => 'Account Category',
            'description' => 'Description',
            'sub_industry' => 'Sub Industry',
            'sub_industry_type' => 'Sub Industry Type',
            'india_head_office' => 'India Head Office',
            'global_head_office' => 'Global Head Office',
            'external_infulencer' => 'External Infulencer',
            'credit_rating' => 'Credit Rating',
            'credit_stage' => 'Credit Stage',
            'credit_day' => 'Credit Day',
            'last_credit_review_date' => 'Last Credit Review Date',
            'next_credit_review_date' => 'Next Credit Review Date',
            'account_acquisition_cost' => 'Account Acquisition Cost',
            'account_acquisition_budget' => 'Account Acquisition Budget',
            'account_retention_cost' => 'Account Retention Cost',
            'account_retention_budget' => 'Account Retention Budget',
            'annual_gross_profit_closed' => 'Annual Gross Profit Closed',
            'annual_gross_profit_billed' => 'Annual Gross Profit Billed',
            'devit_registration' => 'Devit Registration',
            'devit_registration_code' => 'Devit Registration Code',
            'devit_cx_manager' => 'Devit Cx Manager',
            'deshwal_registration' => 'Deshwal Registration',
            'deshwal_registration_code' => 'Deshwal Registration Code',
            'deshwal_isr' => 'Deshwal Isr',
            'deshwal_account_manager' => 'Deshwal Account Manager',
            'deshwal_cx_manager' => 'Deshwal Cx Manager',
            'deshwal_account_stage' => 'Deshwal Account Stage',
            'dell_isr' => 'Dell Isr',
            'dell_ae' => 'Dell Ae',
            'dell_cx_manager' => 'Dell Cx Manager',
            'dell_isg_manager' => 'Dell Isg Manager',
            'hpi_isr' => 'Hpi Isr',
            'hpi_ae' => 'Hpi Ae',
            'hpi_cx_manager' => 'Hpi Cx Manager',
            'lenovo_isr' => 'Lenovo Isr',
            'lenovo_ae' => 'Lenovo Ae',
            'lenovo_cx_manager' => 'Lenovo Cx Manager',
            'lenovo_isg_manager' => 'Lenovo Isg Manager',
            'samsung_isr' => 'Samsung Isr',
            'samsung_ae' => 'Samsung Ae',
            'samsung_cx_manager' => 'Samsung Cx Manager',
            'microsoft_isr' => 'Microsoft Isr',
            'microsoft_ae' => 'Microsoft Ae',
            'microsoft_cx_manager' => 'Microsoft Cx Manager',
            'apple_isr' => 'Apple Isr',
            'apple_ae' => 'Apple Ae',
            'apple_cx_manager' => 'Apple Cx Manager',
            'account_annual_revenue' => 'Account Annual Revenue',
            'business_potential1' => 'Business Potential1',
            'india_it_users' => 'India It Users',
            'global_it_users' => 'Global It Users',
            'annual_business_target' => 'Annual Business Target',
            'annual_business_closed' => 'Annual Business Closed',
            'annual_business_billed' => 'Annual Business Billed',
            'account_financial_year' => 'Account Financial Year',
            'allow_lead_generation' => 'Allow Lead Generation',
            'euc_refresh_policy_id' => 'Euc Refresh Policy ID',
            'finance_remarks' => 'Finance Remarks',
            'finance_detail_submitted_by' => 'Finance Detail Submitted By',
            'finance_detail_submitted_date' => 'Finance Detail Submitted Date',
            'finance_detail_completed' => 'Finance Detail Completed',
            'payment_days' => 'Payment Days',
            'acc_receivable_days' => 'Acc Receivable Days',
            'exposure' => 'Exposure',
            'gst_number' => 'Gst Number',
            'state_code' => 'State Code',
            'legal_entity' => 'Legal Entity',
            'bank_names' => 'Bank Names',
            'account_name' => 'Account Name',
            'account_number' => 'Account Number',
            'bank_ifsc_code' => 'Bank Ifsc Code',
            'bank_swift_code' => 'Bank Swift Code',
            'remarks' => 'Remarks',
            'upload_gst_number1' => 'Upload Gst Number1',
            'pan_number' => 'Pan Number',
            'cancelled_cheque' => 'Cancelled Cheque',
            'vrf_form' => 'Vrf Form',
            'ca_certified_last_3years' => 'Ca Certified Last 3years',
            '3years_financial_statement' => '3years Financial Statement',
            '6months_gst_return' => '6months Gst Return',
            '6months_bank_statement' => '6months Bank Statement',
            'deleted' => 'Deleted',
            'is_temp' => 'Is Temp',
        ];
    }

}
