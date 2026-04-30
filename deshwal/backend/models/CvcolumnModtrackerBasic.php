<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cvcolumn_modtracker_basic".
 *
 * @property int $id
 * @property int $cvid
 * @property string $module
 * @property int $whodid
 * @property string $changedon
 * @property int $status 0-create,1-update
 */
class CvcolumnModtrackerBasic extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cvcolumn_modtracker_basic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cvid', 'module', 'whodid', 'changedon', 'oldsetofcolumn','newsetofcolumn',], 'required'],
            [['cvid', 'whodid', 'status'], 'integer'],
            [['changedon','oldsetofcolumn','newsetofcolumn'], 'safe'],
            [['module'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cvid' => 'cvid',
            'module' => 'Module',
            'whodid' => 'Whodid',
            'changedon' => 'Changedon',
            // 'status' => 'Status',
        ];
    }

    public function cvcolumnauditlog($oldCols,$newCols, $ModuleName, $whodid, $cvid)
    {
        // if ($auditstatus == 0) { 
            // INSERT
            Yii::$app->db->createCommand("
                INSERT INTO cvcolumn_modtracker_basic 
                SET module=:module, whodid=:whodid, cvid=:cvid, changedon=:changedon, oldsetofcolumn = :oldsetofcolumn,newsetofcolumn =:newsetofcolumn
            ")
            ->bindValues([
                ":module" => $ModuleName,
                ":whodid" => $whodid,
                ":cvid"   => $cvid,
                ":changedon" => date("Y-m-d H:i:s"),
                ":oldsetofcolumn" => $oldCols,
                ":newsetofcolumn" => $newCols,
            ])->execute();
            return;
    }

}
