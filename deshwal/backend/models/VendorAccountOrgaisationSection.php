<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vendor_account_orgaisation_section".
 *
 * @property int $va_org_id
 * @property int $vendoraccid
 * @property string $roleid
 * @property int $userid
 */
class VendorAccountOrgaisationSection extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vendor_account_orgaisation_section';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vendoraccid', 'roleid', 'userid'], 'required'],
            [['vendoraccid', 'userid'], 'integer'],
            [['roleid'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'va_org_id' => 'Va Org ID',
            'vendoraccid' => 'Vendoraccid',
            'roleid' => 'Roleid',
            'userid' => 'Userid',
        ];
    }

   /* public function saveVendorAccountOrgaisationSection($entityId)
    {
        if (empty($_REQUEST['vendor_account_orgaisation_section'])) {
            return false;
        }
        $po_items = $_REQUEST['vendor_account_orgaisation_section'];
        if (count($po_items) > 0) {
            foreach ($po_items as $product_detail) {
                if (!empty($product_detail['userid'])) {
                    $product_detail['vendoraccid'] = $entityId;
                    $product_detail_obj = new VendorAccountOrgaisationSection;
                    $product_detail_obj->attributes = $product_detail;
                    // print_r($product_detail_obj->attributes);die;
                    $product_detail_obj->validate();
                    $product_detail_obj->save(false);
                }
            }
        }
    }*/


    public function saveVendorAccountOrgaisationSection($entityId, $oldattributes_va_orgaisation_section)
    {
        $modlog = new ModtrackerBasic();
        $auditstatus = 2; // 2 - update
        $mode = $_POST["mode"];
        $module = $_POST["module"];
        $customtablename = $module . "cf";

        if (empty($_REQUEST['vendor_account_orgaisation_section'])) {
            return false;
        }

        $po_items = $_REQUEST['vendor_account_orgaisation_section'];

        // Step 1 — Build simplified data: [roleid => userid]
        $oldData = [];
        foreach ($oldattributes_va_orgaisation_section as $old) {
            if (!empty($old['roleid']) && !empty($old['userid'])) {
                $oldData[$old['roleid']] = $old['userid'];
            }
        }

        $newData = [];
        foreach ($po_items as $new) {
            if (!empty($new['roleid']) && !empty($new['userid'])) {
                $newData[$new['roleid']] = $new['userid'];
            }
        }

        // Step 2 — Detect deleted, added, and updated roles
        $deletedRoles = array_diff(array_keys($oldData), array_keys($newData));
        $addedRoles   = array_diff(array_keys($newData), array_keys($oldData));
        $commonRoles  = array_intersect(array_keys($oldData), array_keys($newData));

        // Step 3 — Prepare grouped arrays for modtracker
        $oldRecords = [];
        $newRecords = [];

        // Deleted roles → log as blank new value
        if (!empty($deletedRoles)) {
            foreach ($deletedRoles as $roleid) {
                $oldRecords[$roleid] = $oldData[$roleid];
                $newRecords[$roleid] = ''; // removed
            }
        }

        // Added roles → log as blank old value
        if (!empty($addedRoles)) {
            foreach ($addedRoles as $roleid) {
                $oldRecords[$roleid] = ''; // previously not assigned
                $newRecords[$roleid] = $newData[$roleid];
            }
        }

        // Updated roles → old != new
        if (!empty($commonRoles)) {
            foreach ($commonRoles as $roleid) {
                $oldUserid = $oldData[$roleid];
                $newUserid = $newData[$roleid];
                if ($oldUserid != $newUserid) {
                    $oldRecords[$roleid] = $oldUserid;
                    $newRecords[$roleid] = $newUserid;
                }
            }
        }

        //  Step 4 — Call auditlog only once if any change found
        // if (!empty($oldRecords) || !empty($newRecords)) {
        //     $modlog->auditlog(
        //         $oldRecords,
        //         $newRecords,
        //         'vendoraccount',
        //         $entityId,
        //         2, // update
        //         Yii::$app->user->id
        //     );
        // }

        // Step 5 — Save new/updated records
        if (!empty($newData)) {
            foreach ($newData as $roleid => $userid) {
                $record = [
                    'vendoraccid' => $entityId,
                    'roleid' => $roleid,
                    'userid' => $userid
                ];
                $obj = new VendorAccountOrgaisationSection();
                $obj->attributes = $record;
                $obj->validate();
                $obj->save(false);
            }
        }

        // return true;
        return [$oldRecords, $newRecords];
    }




}
