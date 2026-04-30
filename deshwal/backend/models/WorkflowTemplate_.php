<?php

// namespace app\models;
namespace  backend\models;

use Yii;

/**
 * This is the model class for table "workflow_template".
 *
 * @property int $id
 * @property string $name
 * @property string $subject
 * @property string $body
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class WorkflowTemplate extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'workflow_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'subject', 'body'], 'required'],
            [['body'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'subject' => 'Subject',
            'body' => 'Body',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
