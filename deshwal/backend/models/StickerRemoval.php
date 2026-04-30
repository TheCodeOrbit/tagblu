<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sticker_removal".
 *
 * @property int $sticker_removal_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int|null $sticker_removal_no
 * @property string|null $grn_number
 * @property string|null $grn_date
 * @property string|null $lot_number
 * @property string|null $pickup_id
 * @property string|null $tag_number
 * @property string|null $bin_number
 * @property string|null $cleaning_require
 * @property string|null $removal_required
 * @property int $deleted
 */
class StickerRemoval extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sticker_removal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sticker_removal_no', 'grn_number', 'grn_date', 'lot_number', 'pickup_id', 'tag_number', 'bin_number', 'cleaning_require', 'removal_required'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby',  'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'grn_date','sticker_removal_no',], 'safe'],
            [['grn_number', 'lot_number', 'pickup_id', 'tag_number', 'bin_number'], 'string', 'max' => 200],
            [['cleaning_require', 'removal_required'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'sticker_removal_id' => 'Sticker Removal ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'sticker_removal_no' => 'Sticker Removal No',
            'grn_number' => 'Grn Number',
            'grn_date' => 'Grn Date',
            'lot_number' => 'Lot Number',
            'pickup_id' => 'Pickup ID',
            'tag_number' => 'Tag Number',
            'bin_number' => 'Bin Number',
            'cleaning_require' => 'Cleaning Require',
            'removal_required' => 'Removal Required',
            'deleted' => 'Deleted',
        ];
    }
    public function saveStickerremovalDetail($main_item, $item)
    {
        if (count($item) > 0) {
            $i = 1;
            foreach ($item as $rec) {
                $combined = array_merge($rec, $main_item);  
                $rec_obj = new StickerRemoval();
                if ($autoField = $this->checkAutoNo()) {
                    $rec_obj->{$autoField} = $this->getAutoNo(69);
                }  
                $rec_obj->attributes = $combined;
                $rec_obj->validate();
                if (!$rec_obj->save()) 
                    print_r($rec_obj->getErrors());
                $i++;
            }
        }
    }

    public function checkAutoNo()
    {

        $table_name = $this->tableName();
        $autoField = Yii::$app->db->createCommand("SELECT columnname
            FROM field 
            WHERE tablename = :tablename AND uitype = :uitype")
            ->bindValue(':tablename', $table_name)
            ->bindValue(':uitype', 11)
            ->queryOne();
        if (empty($autoField))
            return false; // if does not exist;
        if (count($autoField) < 1)
            return false;
        else
            return $autoField['columnname'];
    }

    public function getAutoNo($tabs)
    {
        $table_name = 'stickerremoval';//StickerRemoval::tableName();
        $model = new AutoNo();
        $orderno = $model->getautomoduleno($tabs, $table_name);
        return $orderno;
    }

}
