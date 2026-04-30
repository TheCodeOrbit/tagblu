<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "opportunity_contact_role".
 *
 * @property int $contact_roleid
 * @property int|null $creatorid
 * @property int|null $ownerid
 * @property int|null $modifiedby
 * @property string|null $createdtime
 * @property string|null $modifiedtime
 * @property int $contacts_id
 * @property int $contact_role
 * @property int $opportunity_id
 * @property int $is_temp
 * @property int $deleted
 */
class OpportunityContactRole extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'opportunity_contact_role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['creatorid', 'ownerid', 'modifiedby', 'contacts_id', 'contact_role', 'opportunity_id', 'is_temp', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['contacts_id', 'contact_role', 'opportunity_id'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'contact_roleid' => 'Contact Roleid',
            'creatorid' => 'Creatorid',
            'ownerid' => 'Ownerid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'contacts_id' => 'Contacts ID',
            'contact_role' => 'Contact Role',
            'opportunity_id' => 'Opportunity ID',
            'is_temp' => 'Is Temp',
            'deleted' => 'Deleted',
        ];
    }
}
