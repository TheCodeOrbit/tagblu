<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "profile_modtracker_basic".
 *
 * @property int $id
 * @property int|null $crmid
 * @property string|null $module
 * @property int|null $whodid
 * @property string|null $changedon
 * @property int|null $status 0-create,1-bulkupdate,2-update,3-added,4-convert lead,5-import,10-singleedit
 */
class ProfileModtrackerBasic extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profile_modtracker_basic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['crmid', 'module', 'whodid', 'changedon'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 0],
            [['crmid', 'whodid', 'status'], 'integer'],
            [['changedon'], 'safe'],
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
            'crmid' => 'Crmid',
            'module' => 'Module',
            'whodid' => 'Whodid',
            'changedon' => 'Changedon',
            'status' => 'Status',
        ];
    }

    public function auditlog($oldAttributes, $newattributes, $ModuleName, $crmid, $auditstatus, $whodid,  $relationmodule = '', $relationid = '')
    {

        // Proceed with your code if the result is valid
        $tableName = `profile`;
        $fieldId = `profileid`;
        // echo $auditstatus;die;
        if ($auditstatus == 0) { //insert
            //insert in modtrackerbasic table
            // echo "insert into `modtracker_basic` set crmid=$crmid,module='$ModuleName',whodid=$whodid,status=0,changedon='".date("Y-m-d H:i:s")."'";
            Yii::$app->db->createCommand("insert into `profile_modtracker_basic` set crmid=:crmid,module=:module,whodid=:whodid,status=:status,changedon=:changedon")
                ->bindValue("crmid", $crmid)
                ->bindValue("module", $ModuleName)
                ->bindValue("whodid", $whodid)
                ->bindValue("status", $auditstatus)
                ->bindValue("changedon", date("Y-m-d H:i:s"))
                ->execute();
            $lastid = Yii::$app->db->getLastInsertID();
            foreach ($newattributes as $column => $value) {
                // if (is_array($value)) {
                //     $value = implode(",", $value);// don't know how to deal with multiselect logs
                // }
                // echo "<pre>";print_r($column);print_r($value);

                # code...
                Yii::$app->db->createCommand("insert into `profile_modtracker_detail` set id=:id,fieldid=:fieldid,fieldlabel=:fieldlabel,fieldname=:fieldname,prevalue=:prevalue,postvalue=:postvalue")
                    ->bindValue("id", $lastid)
                    ->bindValue("fieldid", $column)
                    ->bindValue("fieldlabel", $value['fieldlabel'])
                    ->bindValue("fieldname", $value['fieldname'])
                    ->bindValue("prevalue", '')
                    ->bindValue("postvalue", $value['visible'] . "," . $value['readonly'])
                    ->execute();
            }
            // echo "history added";die;
        } else if ($auditstatus == 2) { //|| $auditstatus == 1 || $auditstatus == 4 || $auditstatus == 7 || $auditstatus == 8 || $auditstatus == 9 || $auditstatus == 10) {//update
            if (!$oldAttributes) {
                // die("Record not found!");
            }
            // echo "new";
            // echo $crmid;die;

            //  Compare old and new values
            $changes = [];
            foreach ($newattributes as $column => $newValue) {
                //echo $column;die;
                if (
                    $column != "modifiedtime" && $column != "modifiedby" && $column != "creatorid" &&
                    $column != "createdtime"
                ) {
                    $oldValue = $oldAttributes ?? null;
                    $fieldid = $newValue['fieldid'];
                    // print_r($oldValue);print_r($newValue);die; 
                    if ($oldValue[$fieldid] != $newValue['fieldid']) {
                        // echo "<pre>";print_r($newValue);die;
                        $changes[$column] = [
                            "fieldid" => $column,
                            "fieldlabel" => $newValue['fieldlabel'],
                            "fieldname" => $newValue['fieldname'],
                            'prevalue' => $oldValue[$fieldid]['visible'] . "," . $oldValue[$fieldid]['readonly'],
                            'postvalue' => $newValue['visible'] . "," . $newValue['readonly']
                        ];
                    }
                    //         print_r($changes);
                    // die;
                }
            }

            //Log changes
            if (!empty($changes)) {
                $transaction = Yii::$app->db->beginTransaction();

                try {
                    //insert in modtrackerbasic table
                    Yii::$app->db->createCommand("insert into `profile_modtracker_basic` set crmid=:crmid,module=:module,whodid=:whodid,status=:status,changedon=:changedon")
                        ->bindValue("crmid", $crmid)
                        ->bindValue("status", $auditstatus)
                        ->bindValue("module", $ModuleName)
                        ->bindValue("whodid", $whodid)
                        ->bindValue("changedon", date("Y-m-d H:i:s"))
                        ->execute();

                    $lastid = Yii::$app->db->getLastInsertID();

                    foreach ($changes as $column => $values) {

                        Yii::$app->db->createCommand("insert into `profile_modtracker_detail` set id=:id,fieldid=:fieldid,fieldlabel=:fieldlabel,fieldname=:fieldname,prevalue=:prevalue,postvalue=:postvalue")
                            ->bindValue("id", $lastid)
                            ->bindValue("fieldid", $column)
                            ->bindValue("fieldlabel", $values['fieldlabel'])
                            ->bindValue("fieldname", $values['fieldname'])
                            ->bindValue("prevalue", $values['prevalue'])
                            ->bindValue("postvalue",  $values['postvalue'])
                            ->execute();
                    }
                    $transaction->commit();
                    // echo "his added";die;
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    echo 'An error occurred: ' . $e->getMessage();
                    die;
                }

                //echo "Changes logged successfully.";
            }
        }
    }
}
