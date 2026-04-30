<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "leaddetails".
 *
 * @property int $leadid
 * @property string $lead_no
 * @property string|null $email
 * @property string|null $interest
 * @property string|null $firstname
 * @property string|null $salutation
 * @property string $lastname
 * @property string $company
 * @property float|null $annualrevenue
 * @property string|null $industry
 * @property string|null $campaign
 * @property string|null $rating
 * @property string|null $leadstatus
 * @property string|null $leadsource
 * @property int|null $converted
 * @property string|null $designation
 * @property string|null $licencekeystatus
 * @property string|null $space
 * @property string|null $comments
 * @property string|null $priority
 * @property string|null $demorequest
 * @property string|null $partnercontact
 * @property string|null $productversion
 * @property string|null $product
 * @property string|null $maildate
 * @property string|null $nextstepdate
 * @property string|null $fundingsituation
 * @property string|null $purpose
 * @property string|null $evaluationstatus
 * @property string|null $transferdate
 * @property string|null $revenuetype
 * @property int|null $noofemployees
 * @property string|null $secondaryemail
 * @property int|null $assignleadchk
 * @property string|null $emailoptout
 * @property string|null $tags
 *
 * @property YiiCrmentity $lead
 * @property YiiLeadaddress $yiiLeadaddress
 */
class Leaddetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leaddetails';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['leadid', 'lead_no', 'lastname', 'company'], 'required'],
            [['leadid', 'converted', 'noofemployees', 'assignleadchk'], 'integer'],
            [['annualrevenue'], 'number'],
            [['comments'], 'string'],
            [['maildate', 'nextstepdate', 'transferdate'], 'safe'],
            [['lead_no', 'email', 'company', 'secondaryemail'], 'string', 'max' => 100],
            [['interest', 'designation', 'licencekeystatus', 'priority', 'demorequest', 'partnercontact', 'product', 'fundingsituation', 'purpose', 'evaluationstatus', 'revenuetype'], 'string', 'max' => 50],
            [['firstname'], 'string', 'max' => 40],
            [['salutation', 'industry', 'rating', 'leadstatus', 'leadsource'], 'string', 'max' => 200],
            [['lastname'], 'string', 'max' => 80],
            [['campaign'], 'string', 'max' => 30],
            [['space'], 'string', 'max' => 250],
            [['productversion'], 'string', 'max' => 20],
            [['emailoptout'], 'string', 'max' => 3],
            [['tags'], 'string', 'max' => 1],
            [['leadid'], 'unique'],
            [['leadid'], 'exist', 'skipOnError' => true, 'targetClass' => Crmentity::class, 'targetAttribute' => ['leadid' => 'crmid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'leadid' => 'Leadid',
            'lead_no' => 'Lead No',
            'email' => 'Email',
            'interest' => 'Interest',
            'firstname' => 'Firstname',
            'salutation' => 'Salutation',
            'lastname' => 'Lastname',
            'company' => 'Company',
            'annualrevenue' => 'Annualrevenue',
            'industry' => 'Industry',
            'campaign' => 'Campaign',
            'rating' => 'Rating',
            'leadstatus' => 'Leadstatus',
            'leadsource' => 'Leadsource',
            'converted' => 'Converted',
            'designation' => 'Designation',
            'licencekeystatus' => 'Licencekeystatus',
            'space' => 'Space',
            'comments' => 'Comments',
            'priority' => 'Priority',
            'demorequest' => 'Demorequest',
            'partnercontact' => 'Partnercontact',
            'productversion' => 'Productversion',
            'product' => 'Product',
            'maildate' => 'Maildate',
            'nextstepdate' => 'Nextstepdate',
            'fundingsituation' => 'Fundingsituation',
            'purpose' => 'Purpose',
            'evaluationstatus' => 'Evaluationstatus',
            'transferdate' => 'Transferdate',
            'revenuetype' => 'Revenuetype',
            'noofemployees' => 'Noofemployees',
            'secondaryemail' => 'Secondaryemail',
            'assignleadchk' => 'Assignleadchk',
            'emailoptout' => 'Emailoptout',
            'tags' => 'Tags',
        ];
    }

    /**
     * Gets query for [[Lead]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLead()
    {
        return $this->hasOne(Crmentity::class, ['crmid' => 'leadid']);
    }

    /**
     * Gets query for [[YiiLeadaddress]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeadaddress()
    {
        return $this->hasOne(Leadaddress::class, ['leadaddressid' => 'leadid']);
    }
}
