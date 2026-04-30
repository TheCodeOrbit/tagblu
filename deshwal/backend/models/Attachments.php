<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "attachments".
 *
 * @property int $attachmentsid
 * @property string $name
 * @property string|null $description
 * @property string|null $type
 * @property string|null $path
 * @property string|null $storedname
 * @property string|null $subject
 */
class Attachments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attachments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description', 'path'], 'string'],
            [['name', 'storedname', 'subject'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'attachmentsid' => 'Attachmentsid',
            'name' => 'Name',
            'description' => 'Description',
            'type' => 'Type',
            'path' => 'Path',
            'storedname' => 'Storedname',
            'subject' => 'Subject',
        ];
    }
}
