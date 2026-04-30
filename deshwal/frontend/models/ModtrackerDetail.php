<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "modtracker_detail".
 *
 * @property int|null $id
 * @property string|null $fieldname
 * @property string|null $prevalue
 * @property string|null $postvalue
 */
class ModtrackerDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'modtracker_detail';
    }

    /**
     * {@inheritdoc}
     */
   
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fieldname' => 'Fieldname',
            'prevalue' => 'Prevalue',
            'postvalue' => 'Postvalue',
        ];
    }
}
