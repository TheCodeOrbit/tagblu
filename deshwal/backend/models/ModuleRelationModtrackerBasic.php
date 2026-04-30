<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "module_relation_modtracker_basic".
 *
 * @property int $id
 * @property int $module_relation_id
 * @property int $whodid
 * @property string $changedon
 * @property string $oldsetofcolumn
 * @property string $newsetofcolumn
 */
class ModuleRelationModtrackerBasic extends \yii\db\ActiveRecord
{

 
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'module_relation_modtracker_basic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['module_relation_id', 'whodid', 'changedon', 'newsetofcolumn'], 'required'],
            [['module_relation_id', 'whodid'], 'integer'],
            [['changedon','oldsetofcolumn', ], 'safe'],
            [['oldsetofcolumn', 'newsetofcolumn'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'module_relation_id' => 'Module Relation ID',
            'whodid' => 'Whodid',
            'changedon' => 'Changedon',
            'oldsetofcolumn' => 'Oldsetofcolumn',
            'newsetofcolumn' => 'Newsetofcolumn',
        ];
    }

     public function modulerelationauditlog($oldCols,$newCols, $whodid, $module_relation_id)
    {
        // if ($auditstatus == 0) { 
            // INSERT
            Yii::$app->db->createCommand("
                INSERT INTO module_relation_modtracker_basic 
                SET  whodid=:whodid, module_relation_id=:module_relation_id, changedon=:changedon, oldsetofcolumn = :oldsetofcolumn,newsetofcolumn =:newsetofcolumn
            ")
            ->bindValues([
                ":whodid" => $whodid,
                ":module_relation_id"   => $module_relation_id,
                ":changedon" => date("Y-m-d H:i:s"),
                ":oldsetofcolumn" => $oldCols,
                ":newsetofcolumn" => $newCols,
            ])->execute();
            return;
    }

}
