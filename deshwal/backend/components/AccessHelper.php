<?php
namespace backend\components;

use Yii;

class AccessHelper
{
    /**
     * Build a dynamic SQL condition for user visibility.
     *
     * @param int $userId          Current logged-in user ID
     * @param string $moduleName   Module name (like 'opportunity', 'quote', etc.)
     * @param array $allowedRoles  Optional: filter by roles in account_user_relation
     * @return string              SQL condition for WHERE clause
     */
    public static function getVisibilityCondition($userId, $moduleName,$tableAlias = 't',$cond, $allowedRoles = [])
    {
        // 🔹 Map module name → account column
        // $accountColumnMap = [
        //     // deshwal modules
        //     'sourcingdeal'  => 'vendor_account_name',
        //     'servicedetail'=>'vendor_account_name',
        //     'productdetail'=>'vendor_account_name',
        //     'quotes'        => 'account_name',
        //     'purchaseorder' => 'vendor_name',
        //     'payments'      => 'account_id',//not available //added new
        //     'pickup'      => 'account_name',
        //     'datawiping'      => 'account_name',
        //     'degaussing'      => 'account_name',
        //     'drilling'      => 'account_name',
        //     'inspection'      => 'account_name',
        //     'shredding'      => 'account_name',
        //     'grn'      => 'account_name',
        //     'salesorder'      => 'vendor_name',
        //     'paymentupdate'      => 'vendor_name',
        //     'vehicleloading'      => 'account_name',
        //     'generatepi'      => 'vendor_name',
        //     // 'gateoutward'      => '',//not available//not required
        //     // devit modules            
        //     'opportunities'  => 'vendor_account_name',
        //     'quotesdit'  => 'account_name',
        //     'salesorderdit'  => 'account_name',
        //     'purchaseorderdit'  => 'vendor_name',
        //     // 'deliverychallandit'=>'',//not available //not required
        //     // 'invoicedit'  => '',//not available //not required
        //     // 'paymentdit'  => '',//not available //not required
        //     // 'packinglistdit'  => '',//not available //not required
        //     // 'focdit'  => '',//need to check // not required
        //     'grndit'  => 'vendor_name',

        //     // add more module mappings here
        // ];

        //replaced with dynamic table fetch on 26 nov 2025 so that on adding new module it can be done  from table
        // 🔹 Fetch column name from DB instead of hard-coded array
        $accountColumn = \app\models\ModuleAccountColumnMap::find()
        ->select('column_name')
        ->where(['module_name' => $moduleName])
        ->andWhere(['status' => 1])
        ->scalar();
        

        // echo "<pre>";
        // print_r($accountColumn);die;
         // Determine which column to use
        //$accountColumn = $accountColumnMap[$moduleName] ?? null;

        // No mapping found → return empty condition
        if (!$accountColumn) {
            return '';
        }

        // Optional role filtering
        $roleCondition = '';
        // if (!empty($allowedRoles)) {
        //     $quoted = array_map(fn($r) => "'" . addslashes($r) . "'", $allowedRoles);
        //     $roleCondition = "AND aur.role_type IN (" . implode(',', $quoted) . ")";
        // }

        
       
        // Build SQL safely
        $userId = (int) $userId; // ensure integer
        $tableAlias = preg_replace('/[^a-zA-Z0-9_]/', '', $tableAlias); // sanitize alias

        // Build the dynamic condition
    //    return "
    //     (
    //         {$tableAlias}.{$accountColumn} IN (
    //             SELECT a.vendoraccid
    //             FROM vendor_account a
    //             INNER JOIN vendor_account_orgaisation_section aur 
    //                 ON aur.vendoraccid = a.vendoraccid
    //             WHERE aur.userid = {$userId}
    //             AND aur.va_org_id = (
    //                 SELECT MAX(va_org_id)
    //                 FROM vendor_account_orgaisation_section aur2
    //                 WHERE aur2.vendoraccid = aur.vendoraccid
    //                     AND aur2.userid = aur.userid
    //                     {$roleCondition}
    //             )
    //             {$roleCondition}
    //         )
    //     )";

    // ✅ EXISTS-based latest-only condition
        return " OR EXISTS (
            SELECT 1
            FROM (
                    SELECT vendoraccid, MAX(va_org_id) AS latest_id
                FROM vendor_account_orgaisation_section
                WHERE userid = {$userId}
                {$roleCondition}
                GROUP BY vendoraccid, userid
            ) latest
            INNER JOIN vendor_account_orgaisation_section aur_final 
                ON aur_final.va_org_id = latest.latest_id
            WHERE aur_final.vendoraccid = {$tableAlias}.{$accountColumn}
            
        )";

    }

    public static function getModuleViewRightsCondition($userId, $moduleName, $tableAlias)
    {
        $profile = $activeroleId = Yii::$app->session->get('active_profile_id');

        // $row = (new \yii\db\Query())
        // ->from('detaileditsetting')
        // ->where([
        //     'module_name' => $moduleName,
        //     'view_allow'  => 1
        // ])
        // ->andWhere(new \yii\db\Expression('FIND_IN_SET(:role, user_role)'))
        // ->addParams([':role' => $profile])
        // ->one();

        $row = (new \yii\db\Query())
        ->from('detaileditsetting')
        ->where([
            'module_name' => $moduleName,
            'view_allow'  => 1
        ])
        ->andWhere("
            FIND_IN_SET(:role, user_role)
            OR FIND_IN_SET(:uid, user_id)
        ")
        ->addParams([
            ':role' => $profile,
            ':uid'  => $userId,
        ])
        ->one();

        if (!$row) {
            return '';  // no special visibility
        }

        // Example: stage_field = so_status, stage_value = Approved
        $field = $row['stage_field'];
        $value = trim($row['stage_value']);
        //code added by ptpatel to resolve issue if stage_field and stage_value are blank in detaileditsetting table on date 31-03-2026
        if($field == '' && $value == ''){
            return '1';
        }else
        //end code added by ptpatel
        return "  $tableAlias.$field = '$value'";
    }
}
