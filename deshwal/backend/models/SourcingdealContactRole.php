<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sourcingdeal_contact_role".
 *
 * @property int $contact_roleid
 * @property int|null $ownerid
 * @property int|null $creatorid
 * @property int|null $modifiedby
 * @property string|null $createdtime
 * @property string|null $modifiedtime
 * @property int $contacts_id
 * @property int $contact_role
 * @property int $sourcingdeal_id
 * @property int $is_temp
 * @property int $deleted
 */
class SourcingdealContactRole extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sourcingdeal_contact_role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby',  'contacts_id', 'contact_role', 'sourcingdeal_id', 'is_temp', 'deleted'], 'integer'],
            [['modifiedtime','createdtime'], 'safe'],
            [['contacts_id', 'contact_role', 'sourcingdeal_id'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'contact_roleid' => 'Contact Roleid',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'contacts_id' => 'Contacts ID',
            'contact_role' => 'Contact Role',
            'sourcingdeal_id' => 'Sourcingdeal ID',
            'is_temp' => 'Is Temp',
            'deleted' => 'Deleted',
        ];
    }
}
