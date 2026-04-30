<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "exportrequest".
 *
 * @property int $export_request_id
 * @property int $ownerid
 * @property int $creatorid
 * @property int $modifiedby
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string|null $export_request_no
 * @property string|null $from_date
 * @property string|null $to_date
 * @property int|null $status 1-pending,2-complete,3-no_records
 * @property string|null $module_name
 * @property int|null $export_all
 * @property int $deleted
 */
class Exportrequest extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exportrequest';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['export_request_no', 'from_date', 'to_date', 'status', 'module_name', 'export_all'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['ownerid', 'creatorid', 'modifiedby', 'createdtime', 'modifiedtime'], 'required'],
            [['ownerid', 'creatorid', 'modifiedby', 'status', 'export_all', 'deleted'], 'integer'],
            [['createdtime', 'modifiedtime', 'from_date', 'to_date'], 'safe'],
            [['export_request_no', 'module_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'export_request_id' => 'Export Request ID',
            'ownerid' => 'Ownerid',
            'creatorid' => 'Creatorid',
            'modifiedby' => 'Modifiedby',
            'createdtime' => 'Createdtime',
            'modifiedtime' => 'Modifiedtime',
            'export_request_no' => 'Export Request No',
            'from_date' => 'From Date',
            'to_date' => 'To Date',
            'status' => 'Status',
            'module_name' => 'Module Name',
            'export_all' => 'Export All',
            'deleted' => 'Deleted',
        ];
    }

}
