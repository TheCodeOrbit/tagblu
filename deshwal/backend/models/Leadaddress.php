<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "leadaddress".
 *
 * @property int $leadaddressid
 * @property string|null $city
 * @property string|null $code
 * @property string|null $state
 * @property string|null $pobox
 * @property string|null $country
 * @property string|null $phone
 * @property string|null $mobile
 * @property string|null $fax
 * @property string|null $lane
 * @property string|null $leadaddresstype
 *
 * @property YiiLeaddetails $leadaddress
 */
class Leadaddress extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leadaddress';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['leadaddressid'], 'required'],
            [['leadaddressid'], 'integer'],
            [['city', 'code', 'state', 'pobox', 'country', 'leadaddresstype'], 'string', 'max' => 30],
            [['phone', 'mobile', 'fax'], 'string', 'max' => 50],
            [['lane'], 'string', 'max' => 250],
            [['leadaddressid'], 'unique'],
            [['leadaddressid'], 'exist', 'skipOnError' => true, 'targetClass' => Leaddetails::class, 'targetAttribute' => ['leadaddressid' => 'leadid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'leadaddressid' => 'Leadaddressid',
            'city' => 'City',
            'code' => 'Code',
            'state' => 'State',
            'pobox' => 'Pobox',
            'country' => 'Country',
            'phone' => 'Phone',
            'mobile' => 'Mobile',
            'fax' => 'Fax',
            'lane' => 'Lane',
            'leadaddresstype' => 'Leadaddresstype',
        ];
    }

    /**
     * Gets query for [[Leadaddress]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeadaddress()
    {
        return $this->hasOne(Leaddetails::class, ['leadid' => 'leadaddressid']);
    }
}
