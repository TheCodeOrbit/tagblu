<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "kanban_cards".
 *
 * @property int $id
 * @property string $title
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $city
 * @property string|null $pipeline_stage
 * @property int $position
 */
class KanbanCard extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kanban_cards';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'position'], 'required'],
            [['position'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['first_name', 'last_name', 'city', 'pipeline_stage'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'city' => 'City',
            'pipeline_stage' => 'Pipeline Stage',
            'position' => 'Position',
        ];
    }
}
