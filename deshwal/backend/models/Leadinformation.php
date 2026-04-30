<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "leadinformation".
 *
 * @property int $leadid
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string $lead_no
 * @property int|null $customer_type
 * @property int|null $vendor
 * @property string|null $salutation
 * @property string $firstname
 * @property string|null $lastname
 * @property string $leadname
 * @property int|null $departments
 * @property string|null $designation
 * @property string|null $website
 * @property string|null $description
 * @property string|null $leadstatus
 * @property string|null $contact_future_date
 * @property string|null $not_contacted_reason
 * @property string|null $disqualified_reason
 * @property string|null $not_interested_reason
 * @property int|null $vertical_manager
 * @property string|null $currency
 * @property float|null $exchange_rate
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $mobile
 * @property string|null $address
 * @property string|null $city
 * @property string|null $noofemployees
 * @property string|null $annualrevenue
 * @property int|null $lead_source
 * @property int|null $industry
 * @property int|null $category
 * @property int|null $lead_org
 * @property string|null $vm_comment
 * @property int|null $send_for_approval
 * @property string|null $tags
 * @property int $converted
 * @property string|null $account_name
 * @property string|null $dnd
 * @property string|null $email_opted_out
 * @property int|null $pincode
 * @property string $duplicate_lead_reference_id
 * @property int|null $reject_reason
 * @property string|null $other_reject_reason
 * @property string|null $deal_type
 * @property int $deleted
 */
class Leadinformation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leadinformation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'lead_no', 'firstname', 'leadname'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'customer_type', 'vendor', 'departments', 'vertical_manager', 'lead_source', 'industry', 'category', 'lead_org', 'send_for_approval', 'converted', 'pincode', 'reject_reason','data_validated','ready_to_pitch', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'contact_future_date'], 'safe'],
            [['exchange_rate'], 'number'],
            [['lead_no', 'salutation', 'designation', 'website', 'city', 'noofemployees', 'tags', 'deal_type'], 'string', 'max' => 100],
            [['firstname', 'lastname', 'leadstatus', 'email', 'account_name', 'dnd', 'email_opted_out', 'duplicate_lead_reference_id', 'other_reject_reason'], 'string', 'max' => 200],
            [['leadname', 'description'], 'string', 'max' => 1000],
            [['address'], 'string', 'max' => 3000],
            [['not_contacted_reason', 'disqualified_reason', 'not_interested_reason', 'vm_comment'], 'string', 'max' => 500],
            [['currency', 'phone', 'mobile', 'annualrevenue'], 'string', 'max' => 50],
            // added for handling blank values saving in by ptpatel on date 24-01-2026
            // [['vendor'], 'trim'],
            // [['vendor'], 'required', 'message' => 'Vendor cannot be blank.'],
            // [['vendor'], 'integer', 'message' => 'Vendor must be a number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'leadid' => 'Leadid',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'lead_no' => 'Lead No',
            'customer_type' => 'Customer Type',
            'vendor' => 'Vendor',
            'salutation' => 'Salutation',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'leadname' => 'Leadname',
            'departments' => 'Departments',
            'designation' => 'Designation',
            'website' => 'Website',
            'description' => 'Description',
            'leadstatus' => 'Leadstatus',
            'contact_future_date' => 'Contact Future Date',
            'not_contacted_reason' => 'Not Contacted Reason',
            'disqualified_reason' => 'Disqualified Reason',
            'not_interested_reason' => 'Not Interested Reason',
            'vertical_manager' => 'Vertical Manager',
            'currency' => 'Currency',
            'exchange_rate' => 'Exchange Rate',
            'email' => 'Email',
            'phone' => 'Phone',
            'mobile' => 'Mobile',
            'address' => 'Address',
            'city' => 'City',
            'noofemployees' => 'Noofemployees',
            'annualrevenue' => 'Annualrevenue',
            'lead_source' => 'Lead Source',
            'industry' => 'Industry',
            'category' => 'Category',
            'lead_org' => 'Lead Org',
            'vm_comment' => 'Vm Comment',
            'send_for_approval' => 'Send For Approval',
            'tags' => 'Tags',
            'converted' => 'Converted',
            'account_name' => 'Account Name',
            'dnd' => 'Dnd',
            'email_opted_out' => 'Email Opted Out',
            'pincode' => 'Pincode',
            'duplicate_lead_reference_id' => 'Duplicate Lead Reference ID',
            'reject_reason' => 'Reject Reason',
            'other_reject_reason' => 'Other Reject Reason',
            'deal_type' => 'Deal Type',
            'deleted' => 'Deleted',
        ];
    }
    public function savemultiplecontacts($RecordId, $type, $vendor_account_name, $typeid)
    {

        if (!empty($type)) {
            //first save to contacts
            $sql = "Select * from lead_contacts_detail where leadid = :leadid";
            $result = Yii::$app->db->createCommand($sql)->bindValue(":leadid", $RecordId)->queryAll();
            $lead_contacts_detail = $result;
            // print_r(($lead_contacts_detail));
            // die;
            $cont =  $_POST["contacts"];
            $userid =  Yii::$app->user->id;
            // print_r($cont);die;
            if (!empty($lead_contacts_detail)) {
                foreach ($lead_contacts_detail as $lead_contact) {

                    $data = array();
                    $modelleadetail = new Contacts();
                    // $data = $cont["contacts"];
                    $data['creatorid'] = $userid;
                    $data['ownerid'] = $userid;
                    $data['modifiedby'] = $userid;
                    $data['createdtime'] = date('Y-m-d H:i:s');
                    $data['modifiedtime'] = $data['createdtime'];

                    $data['contact_role'] = ''; //requester
                    $data['status'] = '2'; //actve
                    $data['mobile'] = $lead_contact['mobile'];
                    $data['first_name'] = $lead_contact['first_name'];
                    $data['last_name'] = $lead_contact['last_name'];
                    $data['email'] = $lead_contact['email'];
                    $data['vendor_account_name'] = $vendor_account_name;
                    $data['is_temp'] = 1;
                    // $data['department'] = $cont['departments'];
                    // print_r($data);die;
                    $modelleadetail->attributes = $data;
                    // print_r($modelleadetail->attributes);die;
                    $modelleadetail->validate();
                    $modelleadetail->save(false);
                    $contacts_id = $modelleadetail->contacts_id;

                    $modlog = new ModtrackerBasic();
                    $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, 'contacts', $modelleadetail->contacts_id, '0', Yii::$app->user->id);
                    echo $type;
                    if ($type == 'sd') {
                        //save in sourcing deal contact role
                        $sql = "INSERT INTO `sourcingdeal_contact_role`(`contacts_id`, `contact_role`, `sourcingdeal_id`,`is_temp`, `creatorid`, `createdtime`) VALUES (:contacts_id,:contact_role,:sourcingdeal_id,1,:creatorid,:createdtime)";
                        Yii::$app->db->createCommand($sql)
                            ->bindValue(":contacts_id", $contacts_id)
                            // ->bindValue(":contact_role", 6) //requestor
                            ->bindValue(":contact_role", '') //requestor
                            ->bindValue(":sourcingdeal_id", $typeid)
                            ->bindValue(":creatorid", Yii::$app->user->id)
                            ->bindValue(":createdtime", date("Y-m-d H:i:s"))
                            ->execute();
                    } else if ($type == 'op') {
                        //save in opportunity contact role
                        $sql = "INSERT INTO `opportunity_contact_role`(`contacts_id`, `contact_role`, `opportunity_id`,`is_temp`, `creatorid`, `createdtime`) VALUES (:contacts_id,:contact_role,:opportunity_id,1,:creatorid,:createdtime)";
                        Yii::$app->db->createCommand($sql)
                            ->bindValue(":contacts_id", $contacts_id)
                            // ->bindValue(":contact_role", 6) //requestor
                            ->bindValue(":contact_role", '') //requestor
                            ->bindValue(":opportunity_id", $typeid)
                            ->bindValue(":creatorid", Yii::$app->user->id)
                            ->bindValue(":createdtime", date("Y-m-d H:i:s"))
                            ->execute();
                    }
                }
            }

        }
    }
}
