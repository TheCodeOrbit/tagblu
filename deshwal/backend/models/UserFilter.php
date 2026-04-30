<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_filter".
 *
 * @property int $id
 * @property int $filter_id
 * @property int|null $fieldid
 * @property string|null $fieldlabel
 * @property string|null $filteroperator
 * @property string|null $userinput
 * @property int $userid
 * @property int $deleted
 */
class UserFilter extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_filter';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fieldid', 'filter_id', 'userid'], 'required'],
            [['fieldid', 'filter_id', 'userid', 'deleted', 'created_by', 'modified_by'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['fieldlabel', 'filteroperator', 'userinput'], 'string', 'max' => 255],
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
            'filter_id' => 'Filter ID',
            'fieldid' => 'Fieldid',
            'fieldlabel' => 'Fieldlabel',
            'filteroperator' => 'Filteroperator',
            'userinput' => 'Userinput',
            'userid' => 'Userid',
            'deleted' => 'Deleted',
        ];
    }
}
