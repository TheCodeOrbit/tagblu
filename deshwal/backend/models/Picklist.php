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
class Picklist extends \yii\db\ActiveRecord
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

    public function getPickListOption($ModuleName = '')
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
        $q_picklist = "SELECT $targetfield, $dispfield FROM $targettable order by seq_no";

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
    public function getPickListValue($PickListVal)
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
            $q_picklist="select $targetfield,$dispfield from $PickListDetail[targettable] where $targetfield='$PickListVal'";
            //echo "<br>q_picklist=$q_picklist";
            $connection = Yii::$app->db;
            $command = $connection->createCommand($q_picklist);
            $arr_picklist = $command->queryOne();
            print_r($arr_picklist);
            die;
            if(count($arr_picklist)>0)
            return  $arr_picklist[$dispfield];
            else
            return '';
            }
            else
            return '';
        }
        
    public function getVerticalManager($ModuleName = '')
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
        $q_picklist = "SELECT $targetfield, $dispfield FROM $targettable inner join  user2role on user2role.userid = $targettable.$targetfield order by seq_no";

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

}

