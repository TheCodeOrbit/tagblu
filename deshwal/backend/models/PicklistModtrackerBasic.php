<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "picklist_modtracker_basic".
 *
 * @property int $id
 * @property string|null $picklist_table
 * @property int|null $whodid
 * @property string|null $changedon
 * @property int|null $status 0-create,2-update,3-delete
 
 */
class PicklistModtrackerBasic extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'picklist_modtracker_basic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['picklist_table', 'whodid', 'changedon', 'status'], 'default', 'value' => null],
            [['whodid', 'status'], 'integer'],
            [['changedon'], 'safe'],
            [['picklist_table'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'picklist_table' => 'Picklist Table',
            'whodid' => 'Whodid',
            'changedon' => 'Changedon',
            'status' => 'Status',
        ];
    }

    public function picklistauditlog($oldAttributes,$newattributes,$ModuleName,$auditstatus,$whodid,$crmid = '',$relationmodule='',$relationid=''){
        // echo $auditstatus;die;
        if($auditstatus == 0 ){//insert
            //insert in modtrackerbasic table
             Yii::$app->db->createCommand("insert into `picklist_modtracker_basic` set picklist_table=:picklist_table,whodid=:whodid,crmid=:crmid,status=:status,changedon=:changedon")
            ->bindValue("picklist_table",$ModuleName)
            ->bindValue("whodid",$whodid)
            ->bindValue("crmid",$crmid)
            ->bindValue("status",$auditstatus)
            ->bindValue("changedon",date("Y-m-d H:i:s"))
            ->execute();
            $lastid = Yii::$app->db->getLastInsertID();
                foreach ($newattributes as $column => $value) {
                   if(is_array($value)){
                    $value = implode(",",$value);// don't know how to deal with multiselect logs
                   }
                    # code...
                    Yii::$app->db->createCommand("insert into `picklist_modtracker_detail` set id=:id,fieldname=:fieldname,prevalue=:prevalue,postvalue=:postvalue")
                        ->bindValue("id",$lastid)
                        ->bindValue("fieldname",$column)
                        ->bindValue("prevalue",'')
                        ->bindValue("postvalue",$value)
                        ->execute();
                    
                }
                // echo "hi from tracker";die;
        }
        else if($auditstatus == 2 || $auditstatus == 3 )
        {//update
            if (!$oldAttributes) 
                {
                   // die("Record not found!");
                }
                // echo "new";
                // echo $crmid;die;

                //  Compare old and new values
                $changes = [];
                foreach ($newattributes as $column => $newValue) {
                    //echo $column;die;
                    if($column !="modifiedtime" && $column !="modifiedby" && $column !="creatorid" && 
                        $column !="createdtime")
                    {
                        $oldValue = $oldAttributes[$column] ?? null;
                        //echo $oldValue." ".$newValue;die; 
                        if ($oldValue != $newValue) {
                            $changes[$column] = [
                                'prevalue' => $oldValue,
                                'postvalue' => $newValue
                            ];
                        }
                    }
                }
                // print_r($changes);die;
                    //Log changes
                if (!empty($changes)) {
                     $transaction = Yii::$app->db->beginTransaction();

                    try {
                        //insert in modtrackerbasic table
                            Yii::$app->db->createCommand("insert into `picklist_modtracker_basic` set picklist_table=:picklist_table,crmid=:crmid,whodid=:whodid,status=:status,changedon=:changedon")
                            ->bindValue("status",$auditstatus)
                            ->bindValue("picklist_table",$ModuleName)
                            ->bindValue("whodid",$whodid)
                            ->bindValue("crmid",$crmid)
                            ->bindValue("changedon",date("Y-m-d H:i:s"))
                            ->execute();

                            $lastid = Yii::$app->db->getLastInsertID();

                            foreach ($changes as $column => $values) {
                      
                                Yii::$app->db->createCommand("insert into `picklist_modtracker_detail` set id=:id,fieldname=:fieldname,prevalue=:prevalue,postvalue=:postvalue")
                                        ->bindValue("id",$lastid)
                                        ->bindValue("fieldname",$column)
                                        ->bindValue("prevalue", $values['prevalue'])
                                        ->bindValue("postvalue",$values['postvalue'])
                                        ->execute();
                            }
                            $transaction->commit();
                        }
                        catch (\Exception $e) {
                            $transaction->rollBack();
                            echo 'An error occurred: ' . $e->getMessage();die;
               
                        }

                //echo "Changes logged successfully.";
                }
          
               
        }
    }

}
