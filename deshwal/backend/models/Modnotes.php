<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "modnotes".
 *
 * @property int $modnotesid
 * @property string|null $notes_no
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string|null $createdtime
 * @property string|null $modifiedtime
 * @property string|null $notecontent
 * @property int|null $related_to
 * @property int $related_to_id
 * @property int|null $parent_note
 * @property int|null $userid
 * @property int|null $is_private
 * @property int|null $filename
 * @property int|null $related_email_id
 * @property int $deleted
 */
class Modnotes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'modnotes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'related_to_id'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'related_to', 'related_to_id', 'parent_note', 'userid', 'is_private', 'filename', 'related_email_id', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['notecontent'], 'string'],
            [['notes_no'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'modnotesid' => 'Modnotesid',
            'notes_no' => 'Notes No',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'notecontent' => 'Notecontent',
            'related_to' => 'Related To',
            'related_to_id' => 'Related To ID',
            'parent_note' => 'Parent Note',
            'userid' => 'Userid',
            'is_private' => 'Is Private',
            'filename' => 'Filename',
            'related_email_id' => 'Related Email ID',
            'deleted' => 'Deleted',
        ];
    }
    public function getModtrackerBasic()
    {
        return $this->hasOne(ModtrackerBasic::className(), ['crmid' => 'modnotesid'])
                    ->andOnCondition(['module' => 'modnotes']);
    }
    public function getAttachments()
    {
        return $this->hasOne(Attachments::className(), ['attachmentsid' => 'filename']);
    }
}
