<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "yii_crmentity".
 *
 * @property int $crmid
 * @property int $smcreatorid
 * @property int $smownerid
 * @property int $modifiedby
 * @property string|null $setype
 * @property string|null $description
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $viewedtime
 * @property string|null $status
 * @property int $version
 * @property int|null $presence
 * @property int $deleted
 * @property int|null $smgroupid
 * @property string|null $source
 * @property string|null $label
 *
 * @property YiiLeaddetails $yiiLeaddetails
 */
class Crmentity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'crmentity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['crmid', 'createdtime', 'modifiedtime'], 'required'],
            [['crmid', 'smcreatorid', 'smownerid', 'modifiedby', 'version', 'presence', 'deleted', 'smgroupid'], 'integer'],
            [['description'], 'string'],
            [['createdtime', 'modifiedtime', 'viewedtime'], 'safe'],
            [['setype', 'source'], 'string', 'max' => 100],
            [['status'], 'string', 'max' => 50],
            [['label'], 'string', 'max' => 255],
            [['crmid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'crmid' => 'Crmid',
            'smcreatorid' => 'Smcreatorid',
            'smownerid' => 'Smownerid',
            'modifiedby' => 'Modifiedby',
            'setype' => 'Setype',
            'description' => 'Description',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'viewedtime' => 'Viewedtime',
            'status' => 'Status',
            'version' => 'Version',
            'presence' => 'Presence',
            'deleted' => 'Deleted',
            'smgroupid' => 'Smgroupid',
            'source' => 'Source',
            'label' => 'Label',
        ];
    }

    /**
     * Gets query for [[YiiLeaddetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaddetails()
    {
        return $this->hasOne(Leaddetails::class, ['leadid' => 'crmid']);
    }
}
