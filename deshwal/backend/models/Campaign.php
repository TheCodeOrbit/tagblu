<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "campaign".
 *
 * @property int $campaign_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $campaign_no
 * @property string|null $campaign_subject
 * @property int|null $sender_address
 * @property string|null $sender_name
 * @property int|null $reply_to_address
 * @property string $campaign_name
 * @property float $budgeted_cost
 * @property int $currency
 * @property int $type
 * @property int $status
 * @property float $actual_cost
 * @property int $exchange_rate
 * @property string|null $description
 * @property int|null $related_to
 * @property int|null $related_to_id
 * @property int $deleted
 */
class Campaign extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'campaign';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'campaign_name', 'budgeted_cost', 'currency', 'type', 'status', 'actual_cost', 'exchange_rate'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'sender_address', 'reply_to_address', 'currency', 'type', 'status', 'exchange_rate', 'related_to', 'related_to_id', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['budgeted_cost', 'actual_cost'], 'number'],
            [['campaign_no', 'campaign_subject', 'sender_name', 'campaign_name', 'description'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'campaign_id' => 'Campaign ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'campaign_no' => 'Campaign No',
            'campaign_subject' => 'Campaign Subject',
            'sender_address' => 'Sender Address',
            'sender_name' => 'Sender Name',
            'reply_to_address' => 'Reply To Address',
            'campaign_name' => 'Campaign Name',
            'budgeted_cost' => 'Budgeted Cost',
            'currency' => 'Currency',
            'type' => 'Type',
            'status' => 'Status',
            'actual_cost' => 'Actual Cost',
            'exchange_rate' => 'Exchange Rate',
            'description' => 'Description',
            'related_to' => 'Related To',
            'related_to_id' => 'Related To ID',
            'deleted' => 'Deleted',
        ];
    }
}
