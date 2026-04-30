<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "data_wiping_asset_details".
 *
 * @property int $datawiping_asset_id
 * @property int|null $datawiping_id
 * @property string|null $laptop_serial_no
 * @property string|null $hdd_sdd_serial_no
 * @property string|null $make
 * @property string|null $type
 * @property string|null $capacity
 * @property string|null $software_name
 * @property string|null $certificate
 * @property string|null $wiping_date
 * @property int|null $wiping_completed
 * @property int|null $creatorid
 * @property int|null $modifiedby
 * @property string|null $createdtime
 * @property string|null $modifiedtime
 */
class DataWipingAssetDetails extends \yii\db\ActiveRecord
{
    public $wiping_date_from;
    public $wiping_date_to;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'data_wiping_asset_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datawiping_id', 'wiping_completed', 'creatorid', 'modifiedby','deleted'], 'integer'],
            [['wiping_date', 'createdtime', 'modifiedtime'], 'safe'],
            [['laptop_serial_no', 'hdd_sdd_serial_no', 'make', 'type', 'capacity', 'software_name', 'certificate'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'datawiping_asset_id' => 'Datawiping Asset ID',
            'datawiping_id' => 'Datawiping ID',
            'laptop_serial_no' => 'Laptop Serial No',
            'hdd_sdd_serial_no' => 'Hdd Sdd Serial No',
            'make' => 'Make',
            'type' => 'Type',
            'capacity' => 'Capacity',
            'software_name' => 'Software Name',
            'certificate' => 'Certificate',
            'wiping_date' => 'Wiping Date',
            'wiping_completed' => 'Wiping Completed',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'deleted' => 'deleted'
        ];
    }

    public function saveDataWippingAssets($entityId)
    {
        $asset_list = $_POST['data_wiping_asset_details']??[];
        $hdd_completed = 0;
        if (!empty($asset_list)) {
            if (count($asset_list) > 0) {
                foreach ($asset_list as $sd) {
                    $sd['datawiping_id'] = $entityId;
                    if($sd["wiping_completed"] == 1 && !empty($sd["certificate"])){
                        $hdd_completed++;
                    }
                    $sd_obj = new DataWipingAssetDetails();
                    $sd_obj->attributes = $sd;
                    $sd_obj->validate();
                    $sd_obj->save(false);
                }
            }
        }
        return $hdd_completed;
    }

    public function search($params)
    {
        $query = DataWipingAssetDetails::find()->where(['deleted' => 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50],
            'sort' => ['defaultOrder' => ['wiping_date' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->datawiping_id) {
          $query->andWhere(['datawiping_id' => $this->datawiping_id]);
        }

        $query->andFilterWhere(['like', 'laptop_serial_no', $this->laptop_serial_no])
              ->andFilterWhere(['like', 'hdd_sdd_serial_no', $this->hdd_sdd_serial_no])
              ->andFilterWhere(['make' => $this->make])
              ->andFilterWhere(['type' => $this->type])
              ->andFilterWhere(['capacity' => $this->capacity])
              ->andFilterWhere(['software_name' => $this->software_name])
              ->andFilterWhere(['wiping_completed' => $this->wiping_completed]);

        if ($this->wiping_date_from || $this->wiping_date_to) {
            $from = $this->wiping_date_from ? date('Y-m-d 00:00:00', strtotime($this->wiping_date_from)) : null;
            $to   = $this->wiping_date_to   ? date('Y-m-d 23:59:59', strtotime($this->wiping_date_to))   : null;

            if ($from) {
                $query->andWhere(['>=', 'wiping_date', $from]);
            }
            if ($to) {
                $query->andWhere(['<=', 'wiping_date', $to]);
            }
        }

        return $dataProvider;
    }

    /**
     * Recalculate hdd_completed from child assets.
     */
    public static function recalcParentHddCompleted($datawipingId)
    {
        $parent = DataWiping::findOne($datawipingId);
        if ($parent === null) {
            return false;
        }

        $oldAttrs        = $parent->oldAttributes;   
        $count = self::find()
            ->where([
                'datawiping_id'    => $datawipingId,
                'wiping_completed' => 1,
                'deleted'          => 0,
            ])
            ->andWhere(['<>', 'certificate', ''])
            ->count();

        $newHddCompleted = (int)$count;
        $parent->hdd_completed = $newHddCompleted;

        if (!$parent->save(false, ['hdd_completed'])) {
            return false;
        }

        $data = [
            'hdd_completed' => $newHddCompleted,
        ];

        $modlog = new ModtrackerBasic();

        $status = 2; 

        $modlog->auditlog(
            $oldAttrs,
            $data,
            'datawiping',
            $parent->datawiping_id,
            $status,
            Yii::$app->user->id
        );

        return true;
    }


}