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
        if($targettable != 'user'){
        // $q_picklist = "SELECT $targetfield, $dispfield FROM $targettable order by seq_no";
        //add is_active = 0 by ptpatel to remove is_active = 0 from table on date 02-09-2025
        $schema = Yii::$app->db->getTableSchema($targettable, true);

        $q_picklist = "SELECT $targetfield, $dispfield FROM $targettable";

        if ($schema && isset($schema->columns['is_active'])) {
            // table has is_active, so add condition
            $q_picklist .= " WHERE is_active = 1";
        }

        $q_picklist .= " ORDER BY seq_no";
        //code added by ptpatel end here
        }
        else{
            $q_picklist = "SELECT $targetfield, $dispfield FROM $targettable WHERE `status` = 10 AND deleted = 0";
        }

        // Execute the query
        $connection = Yii::$app->db;
        $command = $connection->createCommand($q_picklist);
        $arr_picklist = $command->queryAll();

        // Initialize the result array
        // added on 13 jan 2025 for default select blank option
        $picklistDetail = [''=>'Select'];

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
            //print_r($arr_picklist);
            //die;
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
        $q_picklist = "SELECT $targetfield, $dispfield FROM $targettable inner join  user2role on user2role.userid = $targettable.$targetfield  inner join  role on role.roleid = user2role.roleid where role.roleid='H6' || role.rolename like '%Vertical Manager%'  ";
        // SELECT id, concat(first_name,' ',last_name) FROM user inner join  user2role on user2role.userid = user.id  inner join  role on role.roleid = user2role.roleid where role.roleid='H6' || role.rolename like '%Vertical Manager%';

        // Execute the query
        $connection = Yii::$app->db;
        $command = $connection->createCommand($q_picklist);
        $arr_picklist = $command->queryAll();

        // Initialize the result array
        $picklistDetail = [];
         // added on 13 jan 2025 for default select blank option
         $picklistDetail = [''=>'Select'];

        // Loop through the results and prepare the picklist array
        foreach ($arr_picklist as $picklist) {
            $picklistDetail[$picklist[$targetfield]] = $picklist[$dispfield];
        }

        // Return the picklist array
        return $picklistDetail;
    }

    public function getusers($fieldid,$uitype,$uid,$owner = '')
    {
        $model = new \app\models\UsersDetails();

		$userDetail=$model->users($fieldid,$uitype,$uid,$owner);
		return $userDetail;
    }

}

