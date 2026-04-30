<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "terms_and_conditions".
 *
 * @property int $terms_conditions_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $owner
 * @property string|null $content
 * @property string|null $title
 * @property int|null $moduleid
 * @property int $status 1 - active, 0 inaive
 * @property int $deleted
 */
class TermsAndConditions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'terms_and_conditions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'moduleid', 'status', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['owner', 'title'], 'string', 'max' => 200],
            [['content'], 'string', 'max' => 5000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'terms_conditions_id' => 'Terms Conditions ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'owner' => 'Owner',
            'content' => 'Content',
            'title' => 'Title',
            'moduleid' => 'Moduleid',
            'status' => 'Status',
            'deleted' => 'Deleted',
        ];
    }
}
