<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "picklist".
 *
 * @property int $id
 * @property int $fieldid
 * @property string $targettable
 * @property string $targetfield
 * @property string $dispfield
 * @property string|null $default_value
 */
class Multilist extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'picklist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fieldid', 'targettable', 'targetfield', 'dispfield'], 'required'],
            [['fieldid'], 'integer'],
            [['targettable', 'targetfield', 'dispfield', 'default_value'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fieldid' => 'Fieldid',
            'targettable' => 'Targettable',
            'targetfield' => 'Targetfield',
            'dispfield' => 'Dispfield',
            'default_value' => 'Default Value',
        ];
    }

    public function getMultiListOption($ModuleName = '')
    {
        // Find the pick list details based on the fieldid
        $PickListDetail = self::find()->where(['fieldid' => $this->fieldid])->asArray()->one();

        if (!$PickListDetail) {
            return []; // Handle if no records are found
        }

        // Extract the details from the pick list
        $targetfield = $PickListDetail['targetfield'];
        $dispfield = $PickListDetail['dispfield'];
        $targettable = $PickListDetail['targettable'];
        $fieldid = $PickListDetail['fieldid'];

        // Prepare the SQL query
        // $q_picklist = "SELECT $targetfield, $dispfield FROM $targettable";
        //add is_active = 0 by ptpatel to remove is_active = 0 from table on date 02-09-2025
        if($targettable != 'user'){
            $schema = Yii::$app->db->getTableSchema($targettable, true);

            $q_picklist = "SELECT $targetfield, $dispfield FROM $targettable";

            if ($schema && isset($schema->columns['is_active'])) {
                // ✅ table has is_active, so add condition
                $q_picklist .= " WHERE is_active = 1 ";
            }

            $q_picklist .= " ORDER BY seq_no";
        }
        else{
            $q_picklist = "SELECT $targetfield, $dispfield FROM $targettable WHERE `status` = 10 AND deleted = 0";
        }
        //code added by ptpatel end here

        // Execute the query
        $connection = Yii::$app->db;
        $command = $connection->createCommand($q_picklist);
        $arr_picklist = $command->queryAll();

        // Initialize the result array
        $picklistDetail = [];

        // Loop through the results and prepare the picklist array
        foreach ($arr_picklist as $picklist) {
            $picklistDetail[$picklist[$targetfield]] = $picklist[$dispfield];
        }

        // Return the picklist array
        return $picklistDetail;
    }
    public function getMultiSelectListValue($PickListVal)
        {
             // Find the pick list details based on the fieldid
            $PickListDetail = self::find()->where(['fieldid' => $this->fieldid])->asArray()->one();

            if (!$PickListDetail) {
                return []; // Handle if no records are found
            }
            $targetfield=$PickListDetail['targetfield'];
            $dispfield=$PickListDetail['dispfield'];
            
            if(trim($PickListVal)!="")
            {
            $PickListVals   = str_replace(",","','",$PickListVal);
            //$PickListVals = "'".$vals."'";
            $q_picklist="select $targetfield,group_concat($dispfield) as $dispfield  from $PickListDetail[targettable] where $targetfield in ('$PickListVals')";
            //echo "<br>q_picklist=$q_picklist";die;
            $connection = Yii::$app->db;
            $command = $connection->createCommand($q_picklist);
            $arr_picklist = $command->queryOne();
            if(count($arr_picklist)>0)
            //print_r($arr_picklist[$dispfield]);die;
            return  $arr_picklist[$dispfield];
            else
            return '';
            }
            else
            return '';
        }
}

