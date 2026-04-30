<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "site_setting".
 *
 * @property int $id
 * @property string $company
 * @property string|null $logo_path
 */
class SiteSetting extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'site_setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company'], 'required'],
            [['company'], 'in', 'range' => ['deshwal', 'oxypc']],
            [['logo_path'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company' => 'Company',
            'logo_path' => 'Logo Path',
        ];
    }
}
