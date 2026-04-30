<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tagging_product_detail".
 *
 * @property int $product_detail_id
 * @property int $taggingid
 * @property string|null $product_name
 * @property int|null $sub_category
 * @property string|null $serial_number
 * @property string|null $tag_number
 * @property int|null $bin_number
 * @property int|null $status
 *
 * @property Tagging $tagging
 */
class TaggingProductDetail extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tagging_product_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_name', 'sub_category', 'serial_number', 'tag_number', 'bin_number', 'status'], 'default', 'value' => null],
            [['taggingid'], 'required'],
            [['taggingid', 'sub_category', 'bin_number', 'status'], 'integer'],
            [['product_name', 'serial_number', 'tag_number'], 'string', 'max' => 200],
            [['taggingid'], 'exist', 'skipOnError' => true, 'targetClass' => Tagging::class, 'targetAttribute' => ['taggingid' => 'tagging_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_detail_id' => 'Product Detail ID',
            'taggingid' => 'Taggingid',
            'product_name' => 'Product Name',
            'sub_category' => 'Sub Category',
            'serial_number' => 'Serial Number',
            'tag_number' => 'Tag Number',
            'bin_number' => 'Bin Number',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[Tagging]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTagging()
    {
        return $this->hasOne(Tagging::class, ['tagging_id' => 'taggingid']);
    }


    public function saveTaggingproductdetailDetail($entityId)
    {
        // echo $entityId."<pre>";print_r($_POST['tagging_product_detail']);
        $items = $_POST['tagging_product_detail'] ?? [];
        if (count($items) > 0) {
            $sticker_removal_items = [];
            $cleninag_items = [];
            $i = 1;
            foreach ($items as $rec) {
                $rec['taggingid'] = $entityId;
                $rec_obj = new TaggingProductDetail();
                $rec_obj->attributes = $rec;
                $rec_obj->validate();
                $rec_obj->save(false);

                if ($rec_obj->status == 2) {
                    array_push($cleninag_items, $rec);
                }
                if ($rec_obj->status == 1) {
                    array_push($sticker_removal_items, $rec);
                }
                $i++;
            }
            $_POST['tagging']['ownerid'] = Yii::$app->user->id;
            $_POST['tagging']['grn_number'] = $_POST['tagging']['grn_no'] ?? ''; 
            $_POST['tagging']['lot_number'] = $_POST['tagging']['lot_no'] ?? '';
            unset($_POST['tagging']['grn_no']);
            unset($_POST['tagging']['lot_no']);
            if (count($sticker_removal_items) > 0) {
                $sticker_removal = new StickerRemoval();
                $sticker_removal->saveStickerremovalDetail($_POST['tagging'], $sticker_removal_items);
            }
            if (count($cleninag_items) > 0) {
                $cleaning = new Cleaning();
                $cleaning->saveCleaningDetail($_POST['tagging'], $cleninag_items);
            }
        }
    }
}
