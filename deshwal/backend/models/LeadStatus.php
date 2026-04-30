<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lead_status".
 *
 * @property int $leadstatusid
 * @property string $leadstatus_value
 * @property int $is_active
 * @property int|null $seq_no
 * @property int $picklist_valueid
 * @property int $lead_pipeline_status
 * @property string $description
 */
class LeadStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['leadstatus_value', 'picklist_valueid', 'lead_pipeline_status', 'description'], 'required'],
            [['is_active', 'seq_no', 'picklist_valueid', 'lead_pipeline_status'], 'integer'],
            [['leadstatus_value'], 'string', 'max' => 200],
            [['description'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'leadstatusid' => 'Leadstatusid',
            'leadstatus_value' => 'Leadstatus Value',
            'is_active' => 'Is Active',
            'seq_no' => 'Seq No',
            'picklist_valueid' => 'Picklist Valueid',
            'lead_pipeline_status' => 'Lead Pipeline Status',
            'description' => 'Description',
        ];
    }
}
