<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pickup_document_details".
 *
 * @property int $pickup_doc_details_id
 * @property int $pickup_id
 * @property int|null $document_for_pickup
 * @property int|null $document_attached
 * @property string|null $attachment
 * @property int $deleted
 *
 * @property Pickup $pickup
 */
class PickupDocumentDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pickup_document_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pickup_id'], 'required'],
            [['pickup_id', 'document_for_pickup', 'document_attached', 'deleted'], 'integer'],
            [['attachment'], 'string', 'max' => 200],
            [['pickup_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pickup::class, 'targetAttribute' => ['pickup_id' => 'pickup_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pickup_doc_details_id' => 'Pickup Doc Details ID',
            'pickup_id' => 'Pickup ID',
            'document_for_pickup' => 'Document For Pickup',
            'document_attached' => 'Document Attached',
            'attachment' => 'Attachment',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[Pickup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPickup()
    {
        return $this->hasOne(Pickup::class, ['pickup_id' => 'pickup_id']);
    }

    public function savePickupDocumentDetails($entityId)
    {

        $savePickupDocumentDetails = $_POST['pickup_document_details']??[];
        // print_r($savePickupDocumentDetails);die;
        if (!empty($savePickupDocumentDetails)) {
            if (count($savePickupDocumentDetails) > 0) {
                foreach ($savePickupDocumentDetails as $product_detail) {
                    $product_detail['pickup_id'] = $entityId;
                    $product_detail_obj = new PickupDocumentDetails();
                    $product_detail_obj->attributes = $product_detail;
                    // print_r($product_detail_obj->attributes);die;
                    $product_detail_obj->validate();
                    $product_detail_obj->save(false);
                    // $modlog = new ModtrackerBasic();
                    // $modlog->auditlog($oldAttributes = '', $product_detail_obj, 'productdetail', $product_detail_obj->$product_costing_detail_id, 0, Yii::$app->user->id);
                }
            }
        }
    }
}
