<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;

class AuditController extends Controller
{
    public function actionRun()
    {
        $this->updateOpportunitySubmitForPricingDates();
        $this->updateOpportunityPricingDoneDates();
        // $this->updateSalesOrderApprovalDates();
        // $this->updatePurchaseOrderApprovalDates();
    }

    protected function updateSalesOrderApprovalDates()
    {
        $moduleInfo = Yii::$app->db->createCommand("
            SELECT tablename, tablekeyid 
            FROM tab 
            WHERE name = 'salesorderdit'
        ")->queryOne();
        if (!$moduleInfo) {
            echo "salesorder_dit module not found in tab\n";
            return;
        }

        $tableName = $moduleInfo['tablename'];   
        $pk        = $moduleInfo['tablekeyid'];  

        $sql = "
            UPDATE {$tableName} so
            JOIN modtracker_basic mb 
              ON so.{$pk} = mb.crmid
            JOIN modtracker_detail md 
              ON mb.id = md.id
            SET so.so_approval_date = mb.changedon
            WHERE mb.module = 'salesorderdit'
              AND md.fieldname = 'so_stage'
              AND md.postvalue = '4'
              AND (so.so_approval_date IS NULL 
                   OR so.so_approval_date = '0000-00-00 00:00:00')
        ";

        $count = Yii::$app->db->createCommand($sql)->execute();
        echo "salesorder_dit: updated {$count} rows\n";
    }

    
    protected function updatePurchaseOrderApprovalDates()
    {
        $moduleInfo = Yii::$app->db->createCommand("
            SELECT tablename, tablekeyid 
            FROM tab 
            WHERE name = 'purchaseorderdit'
        ")->queryOne();

        if (!$moduleInfo) {
            echo "purchaseorder_dit module not found in tab\n";
            return;
        }

        $tableName = $moduleInfo['tablename']; 
        $pk        = $moduleInfo['tablekeyid'];  

        $sql = "
            UPDATE {$tableName} po
            JOIN modtracker_basic mb 
              ON po.{$pk} = mb.crmid
            JOIN modtracker_detail md 
              ON mb.id = md.id
            SET po.po_approve_date = mb.changedon
            WHERE mb.module = 'purchaseorderdit'
              AND md.fieldname = 'stage'
              AND md.postvalue = '4'
              AND (po.po_approve_date IS NULL 
                   OR po.po_approve_date = '0000-00-00 00:00:00')
        ";

        $count = Yii::$app->db->createCommand($sql)->execute();
        echo "purchaseorder_dit: updated {$count} rows\n";
    }

    protected function updateOpportunitySubmitForPricingDates()
    {
        $moduleInfo = Yii::$app->db->createCommand("
            SELECT tablename, tablekeyid 
            FROM tab 
            WHERE name = 'opportunities'
        ")->queryOne();
        // print_r($moduleInfo); exit;
        if (!$moduleInfo) {
            echo "Opportunity module not found in tab\n";
            return;
        }

        $tableName = $moduleInfo['tablename']; 
        $pk        = $moduleInfo['tablekeyid'];  

        $sql = "
            UPDATE {$tableName} oppr
            JOIN modtracker_basic mb 
              ON oppr.{$pk} = mb.crmid
            JOIN modtracker_detail md 
              ON mb.id = md.id
            SET oppr.submit_pricing_date = DATE(mb.changedon)
            WHERE mb.module = 'opportunities'
              AND md.fieldname = 'submit_for_pricing'
              AND md.postvalue = '1'
              AND (oppr.submit_pricing_date IS NULL 
                   OR oppr.submit_pricing_date = '0000-00-00')
        ";

        $count = Yii::$app->db->createCommand($sql)->execute();
        echo "Submit For Pricing  Date Updated: updated {$count} rows\n";
    }

     protected function updateOpportunityPricingDoneDates()
    {
        $moduleInfo = Yii::$app->db->createCommand("
            SELECT tablename, tablekeyid 
            FROM tab 
            WHERE name = 'opportunities'
        ")->queryOne();

        if (!$moduleInfo) {
            echo "Opportunity module not found in tab\n";
            return;
        }

        $tableName = $moduleInfo['tablename']; 
        $pk        = $moduleInfo['tablekeyid'];  

        $sql = "
            UPDATE {$tableName} oppr
            JOIN modtracker_basic mb 
              ON oppr.{$pk} = mb.crmid
            JOIN modtracker_detail md 
              ON mb.id = md.id
            SET oppr.prodpricing_done_date = DATE(mb.changedon)
            WHERE mb.module = 'opportunities'
              AND md.fieldname = 'pricing_done'
              AND md.postvalue = '1'
              AND md.prevalue = '0'
              AND md.fieldname = 'pricing_done'
              AND (oppr.prodpricing_done_date IS NULL 
                   OR oppr.prodpricing_done_date = '0000-00-00')
        ";

        $count = Yii::$app->db->createCommand($sql)->execute();
        echo "Pricing Done  Date Updated: updated {$count} rows\n";
    }
}
