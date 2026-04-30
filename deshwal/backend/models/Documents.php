<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "documents".
 *
 * @property int $docid
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string $doc_no
 * @property string $title
 * @property int $filename
 * @property string|null $notecontent
 * @property int $related_to
 * @property int $related_to_id
 * @property int $folderid
 * @property string|null $filetype
 * @property string|null $filelocationtype
 * @property int|null $filedownloadcount
 * @property int|null $filestatus
 * @property int $filesize
 * @property string|null $fileversion
 * @property string|null $tags
 * @property int $deleted
 */
class Documents extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime', 'title', 'filename', 'related_to', 'related_to_id'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'related_to', 'related_to_id', 'folderid', 'filedownloadcount', 'filestatus', 'filesize', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime'], 'safe'],
            [['notecontent'], 'string'],
            [['doc_no'], 'string', 'max' => 100],
            [['title', 'filetype', 'fileversion'], 'string', 'max' => 50],
            [['filelocationtype'], 'string', 'max' => 5],
            [['tags'], 'string', 'max' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'docid' => 'Docid',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'doc_no' => 'Doc No',
            'title' => 'Title',
            'filename' => 'Filename',
            'notecontent' => 'Notecontent',
            'related_to' => 'Related To',
            'related_to_id' => 'Related To ID',
            'folderid' => 'Folderid',
            'filetype' => 'Filetype',
            'filelocationtype' => 'Filelocationtype',
            'filedownloadcount' => 'Filedownloadcount',
            'filestatus' => 'Filestatus',
            'filesize' => 'Filesize',
            'fileversion' => 'Fileversion',
            'tags' => 'Tags',
            'deleted' => 'Deleted',
        ];
    }
}
