<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "grn_document_details".
 *
 * @property int $grn_doc_details_id
 * @property int $grn_id
 * @property int|null $document_for_pickup
 * @property int|null $document_attached
 * @property string|null $attachment
 * @property int|null $received_at_warehouse
 * @property int $deleted
 */
class GrnDocumentDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grn_document_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['grn_id'], 'required'],
            [['grn_id', 'document_for_pickup', 'document_attached', 'received_at_warehouse', 'deleted'], 'integer'],
            [['attachment'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'grn_doc_details_id' => 'Grn Doc Details ID',
            'grn_id' => 'Grn ID',
            'document_for_pickup' => 'Document For Pickup',
            'document_attached' => 'Document Attached',
            'attachment' => 'Attachment',
            'received_at_warehouse' => 'Received At Warehouse',
            'deleted' => 'Deleted',
        ];
    }

    public function saveGrnDocumentsDetails($entityId)
    {
        $items=$_POST['grn_document_details']??[];
		if(count($items)>0)
		{
			foreach($items as $rec)
			{
                $rec['grn_id']=$entityId;
                $attachment = $rec["attachment"]??null;
                $attachment_hidden = $rec["attachment_hidden"]??null;
                if(empty($attachment)) $attachment = $attachment_hidden;
                $rec["attachment"] = $attachment;
                $rec_obj=new GrnDocumentDetails;	
                $rec_obj->attributes=$rec;
                $rec_obj->validate();
                $rec_obj->save(false);
			}
		}
    }
}
