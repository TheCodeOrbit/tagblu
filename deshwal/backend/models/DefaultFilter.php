<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "default_filter".
 *
 * @property int $id
 * @property string $filter_name
 * @property string $description
 * @property int $tabid
 * @property string|null $default_condition
 * @property int $userid
 * @property int $deleted
 */
class DefaultFilter extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'default_filter';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['filter_name', 'tabid', 'userid'], 'required'],
            [['tabid', 'userid', 'deleted', 'created_by', 'modified_by'], 'integer'],
            [['description', 'default_condition'], 'string'],
            [['created_at', 'modified_at'], 'safe'],
            [['filter_name'], 'string', 'max' => 255],
            [['action'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filter_name' => 'Filter Name',
            'description' => 'Description',
            'tabid' => 'Tabid',
            'default_condition' => 'Default Condition',
            'userid' => 'Userid',
            'deleted' => 'Deleted',
        ];
    }
}
