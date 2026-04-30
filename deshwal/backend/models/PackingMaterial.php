<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "packing_material".
 *
 * @property int $packing_material_id
 * @property int $material_name
 * @property int $qty
 * @property float $price
 * @property float $total
 * @property int $pickup_id
 * @property int $deleted
 *
 * @property Pickup $pickup
 */
class PackingMaterial extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'packing_material';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pickup_id'], 'required'],
            [['pickup_id', 'deleted'], 'integer'],
            [['material_name', 'qty', 'price', 'total', ], 'safe'],
            [['pickup_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pickup::class, 'targetAttribute' => ['pickup_id' => 'pickup_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'packing_material_id' => 'Packing Material ID',
            'material_name' => 'Material Name',
            'qty' => 'Qty',
            'price' => 'Price',
            'total' => 'Total',
            'pickup_id' => 'Pickup ID',
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

    public function savePackingMaterials($entityId)
    {
        $packing_material = $_POST['packing_material']??[];
        // print_r($savePickupDocumentDetails);die;
        if (!empty($packing_material)) {
            if (count($packing_material) > 0) {
                foreach ($packing_material as $pm) {
                    $pm['pickup_id'] = $entityId;
                    $pm_obj = new PackingMaterial();
                    $pm_obj->attributes = $pm;
                    $pm_obj->validate();
                    $pm_obj->save(false);
                }
            }
        }
    }
}
