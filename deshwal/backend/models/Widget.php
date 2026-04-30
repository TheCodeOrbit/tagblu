<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "widget".
 *
 * @property int $id
 * @property int $ownerid
 * @property string $dashbordname
 * @property string $name
 * @property string $title
 * @property int $related_module
 * @property string $modulename
 * @property string $view 1-not show dashboard,0-show on dashboard
 
 * @property string $layout
 * @property string $type
 * @property string $widgeturl
 * @property string $position for layout in front
 * @property int|null $filter_id
 * @property int $is_active 1-active,0 inactive
 
 * @property int $deleted
 */
class Widget extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'widget';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['filter_id'], 'default', 'value' => null],
            [['is_active'], 'default', 'value' => 1],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'related_module', 'filter_id', 'is_active', 'deleted'], 'integer'],
            [['dashbordname', 'name', 'title', 'modulename', 'type', 'widgeturl', 'position'], 'required'],
            [['dashbordname', 'name', 'view', 'layout', 'type', 'widgeturl'], 'string', 'max' => 100],
            [['title'], 'string', 'max' => 250],
            [['modulename', 'position'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ownerid' => 'Ownerid',
            'dashbordname' => 'Dashbordname',
            'name' => 'Name',
            'title' => 'Title',
            'related_module' => 'Related Module',
            'modulename' => 'Modulename',
            'view' => 'View',
            'layout' => 'Layout',
            'type' => 'Type',
            'widgeturl' => 'Widgeturl',
            'position' => 'Position',
            'filter_id' => 'Filter ID',
            'is_active' => 'Is Active',
            'deleted' => 'Deleted',
        ];
    }

}
