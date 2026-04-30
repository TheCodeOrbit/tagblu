<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "approval_rule".
 *
 * @property int $id
 * @property int $tabid
 * @property string $module
 * @property string|null $stage_column
 * @property int $source_stage
 * @property string $send_stage
 * @property int $sequence_no
 * @property string $action_code
 * @property string $action_label
 * @property string $source_role
 * @property string $destination_role
 * @property string|null $condition_json
 * @property string|null $approve_status
 * @property string|null $reject_status
 * @property string|null $modify_status
 * @property string|null $on_reject_target
 * @property string|null $on_modify_target
 * @property int|null $is_final
 * @property int|null $is_active
 * @property string|null $meta
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class ApprovalRule extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'approval_rule';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tabid', 'module', 'source_stage', 'send_stage', 'sequence_no', 'action_code', 'action_label', 'source_role', 'destination_role'], 'required'],
            [['tabid', 'source_stage', 'sequence_no', 'is_final', 'is_active'], 'integer'],
            [['condition_json', 'meta', 'created_at', 'updated_at'], 'safe'],
            [['module', 'source_role', 'destination_role', 'approve_status', 'reject_status', 'modify_status', 'on_reject_target', 'on_modify_target'], 'string', 'max' => 100],
            [['stage_column', 'send_stage', 'action_label','condition_not_matched_status','condition_not_matched_target'], 'string', 'max' => 255],
            [['action_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tabid' => 'Tabid',
            'module' => 'Module',
            'stage_column' => 'Stage Column',
            'source_stage' => 'Source Stage',
            'send_stage' => 'Send Stage',
            'sequence_no' => 'Sequence No',
            'action_code' => 'Action Code',
            'action_label' => 'Action Label',
            'source_role' => 'Source Role',
            'destination_role' => 'Destination Role',
            'condition_json' => 'Condition Json',
            'approve_status' => 'Approve Status',
            'reject_status' => 'Reject Status',
            'modify_status' => 'Modify Status',
            'on_reject_target' => 'On Reject Target',
            'on_modify_target' => 'On Modify Target',
            'is_final' => 'Is Final',
            'is_active' => 'Is Active',
            'meta' => 'Meta',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
