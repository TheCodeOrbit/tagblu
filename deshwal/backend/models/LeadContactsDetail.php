<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lead_contacts_detail".
 *
 * @property int $contacts_detail_id
 * @property int $leadid
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $mobile
 * @property string|null $email
 * @property int|null $designation
 * @property int|null $contact_validation
 * @property int $deleted
 *
 * @property Leadinformation $lead
 */
class LeadContactsDetail extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_contacts_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'mobile', 'email', 'designation', 'contact_validation'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['leadid'], 'required'],
            [['leadid', 'designation', 'contact_validation', 'deleted'], 'integer'],
            [['first_name', 'last_name'], 'string', 'max' => 200],
            [['mobile'], 'string', 'max' => 15],
            [['email'], 'string', 'max' => 100],
            [['leadid'], 'exist', 'skipOnError' => true, 'targetClass' => Leadinformation::class, 'targetAttribute' => ['leadid' => 'leadid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'contacts_detail_id' => 'Contacts Detail ID',
            'leadid' => 'Leadid',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'designation' => 'Designation',
            'contact_validation' => 'Contact Validation',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[Lead]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLead()
    {
        return $this->hasOne(Leadinformation::class, ['leadid' => 'leadid']);
    }

         public function saveLeadContactsDetail($entityId)
    {
        if(empty($_REQUEST['lead_contacts_detail'])){
            return false;
        }
        else{
             //delete old record from child table            
             $sql = "Delete from lead_contacts_detail where leadid = :leadid";
             Yii::$app->db->createCommand($sql)->bindValue(":leadid", $entityId)->execute();
        }
        $lead_contacts_detail=$_REQUEST['lead_contacts_detail'];
		if(count($lead_contacts_detail)>0)
		{
			foreach($lead_contacts_detail as $product_detail)
			{
			$product_detail['leadid']=$entityId;
			$product_detail_obj=new LeadContactsDetail;	
			$product_detail_obj->attributes=$product_detail;
            // print_r($product_detail_obj->attributes);die;
			$product_detail_obj->validate();
			$product_detail_obj->save(false);
			}
		}
        
    }
}
