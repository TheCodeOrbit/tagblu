<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cleaning".
 *
 * @property int $cleaning_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property int|null $cleaning_no
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
class Cleaning extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cleaning';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cleaning_no', 'grn_number', 'grn_date', 'lot_number', 'pickup_id', 'tag_number', 'bin_number', 'cleaning_require', 'removal_required'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby',  'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'grn_date','cleaning_no'], 'safe'],
            [['grn_number', 'lot_number', 'pickup_id', 'tag_number', 'bin_number', 'cleaning_require', 'removal_required'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cleaning_id' => 'Cleaning ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'cleaning_no' => 'Cleaning No',
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

    public function saveCleaningDetail($main_item, $item)
    {
        if (count($item) > 0) {
            $i = 1;
            foreach ($item as $rec) {
                $combined = array_merge($rec, $main_item);  
                $rec_obj = new Cleaning();
                if ($autoField = $this->checkAutoNo()) {
                    $rec_obj->{$autoField} = $this->getAutoNo(70);
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
        $table_name = Cleaning::tableName();
        $model = new AutoNo();
        $orderno = $model->getautomoduleno($tabs, $table_name);
        return $orderno;
    }

}
