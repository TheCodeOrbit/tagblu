<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ReportController extends Controller
{
    public function actionDaily()
    {
        $slotCode = 'slot_code_po_so_report';
        
        if ($this->checkStatus($slotCode) > 0) {
            echo "Cron already run today for {$slotCode}. Skipping.                      \n ";
            return;
        }
        
        $this->insertStatus($slotCode);
        echo "Starting daily report cron...                   \n";
        
        try {
            $reportDate = date('Y-m-d', strtotime('yesterday'));   
            $reportDateDisplay = date('d/m/Y', strtotime('yesterday')); 

            $start = $reportDate . ' 00:00:00';
            $end   = $reportDate . ' 23:59:59';

            $soRows = $this->getApprovedSoRows($start, $end);
            $poRows = $this->getReleasedPoRows($start, $end);

            
            $soFile = $this->generateSoReport($soRows, $reportDate);
            $poFile = $this->generatePoReport($poRows, $reportDate);
            
            if (empty($soRows) && empty($poRows)) {
                $this->updateStatus($slotCode, 1, 1);
                $this->sendEmail(null, null, false, $reportDateDisplay, $reportDate);
                return;
            }
            
            $this->sendEmail($soFile, $poFile, true, $reportDateDisplay, $reportDate);
            $this->updateStatus($slotCode, 1, 1); 
            echo "Daily reports sent successfully.           \n";
            
        } catch (Exception $e) {
            $this->updateStatus($slotCode, 2, 1); 
            echo "Cron failed: " . $e->getMessage() . "\n";
        }
    }

   public function checkStatus($slotCode)
    {
        $today = date('Y-m-d');
        $count = Yii::$app->db->createCommand("
            SELECT COUNT(*) FROM mail_status 
            WHERE slot_code = :slot 
            AND mail_run_date = :today 
            AND status = 1  
        ")
        ->bindValues([
            ':slot' => $slotCode,
            ':today' => $today
        ])->queryScalar();
        
        return $count;
    }

    public function insertStatus($slotCode)
    {
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        
        Yii::$app->db->createCommand()->insert('mail_status', [
            // 'mail_status_id' => time(), // omit if AUTO_INCREMENT
            'mail_type' => 0,      // 0 = not sent
            'slot_code' => $slotCode,
            'mail_run_date' => $today,
            'status' => 0,         // 0 = pending
            'created_time' => $now,
            'modified_time' => $now
        ])->execute();
        
        return Yii::$app->db->getLastInsertID('mail_status');
    }

    public function updateStatus($slotCode, $status = 1, $mailType = 1)
    {
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        
        Yii::$app->db->createCommand("
            UPDATE mail_status 
            SET status = :status, mail_type = :mailType, modified_time = :now
            WHERE slot_code = :slot AND mail_run_date = :today
        ")
        ->bindValues([
            ':status' => $status,
            ':mailType' => $mailType,
            ':now' => $now,
            ':slot' => $slotCode,
            ':today' => $today
        ])->execute();
    }
    protected function getApprovedSoRows($start, $end)
    {
        $db = Yii::$app->db;

        $soRows = (new \yii\db\Query())
            ->from(['so_dit' => 'salesorder_dit'])
            ->innerJoin(['op' => 'opportunity'], 'op.opportunity_id = so_dit.deal_name')
            ->where(['so_dit.deleted' => 0])
            ->andWhere(['between', 'so_dit.so_approval_date', $start, $end])
            ->andWhere(['so_dit.so_stage' => 4])
            ->select([
                'so_dit.*',
                'op.opportunity_stage AS op_stage_id',
                'op.product_category AS product_category',
                'op.vendor_account_name AS vendor_account_name',
                'op.devit_vertical_manager AS vm_mag',
                'op.ownerid AS opownerid'
            ])
            ->all($db);

        if (!$soRows) {
            return [];
        }

        $ownerIds     = [];
        $vmIds        = [];
        $accountIds   = [];
        $stageIds     = [];
        $dealIds      = [];
        $prodCatIds   = [];
        $vendorAccIds = [];

        foreach ($soRows as $r) {
            $ownerIds[]     = $r['opownerid'];
            $vmIds[]        = $r['vm_mag'];
            $accountIds[]   = $r['account_name'];
            $stageIds[]     = $r['op_stage_id'];
            $dealIds[]      = $r['deal_name'];
            $prodCatIds[]   = $r['product_category'];
            $vendorAccIds[] = $r['vendor_account_name'];
        }
        $owners     = $this->getRefMap('user', 'id', "CONCAT(first_name,' ',last_name)", $ownerIds);
        $vManagers  = $this->getRefMap('user', 'id', "CONCAT(first_name,' ',last_name)", $vmIds);
        $accounts   = $this->getRefMap('vendor_account', 'vendoraccid', 'acc_name', $accountIds);
        $stages     = $this->getRefMap('oppr_stage', 'stage_id', 'stage_value', $stageIds);
        $prodCats = $this->getRefMap('oppr_product_category', 'product_category_id', 'product_category_value', $prodCatIds);
        $vendorAccNames = $this->getRefMap('vendor_account', 'vendoraccid', 'cust_code', $vendorAccIds);

        $oppRows = (new \yii\db\Query())
            ->from('opportunity')
            ->select([
                'opportunity_id',
                'deal_name',
                'closing_date',
                'product_category',
                'vendor_account_name'
            ])
            ->where(['opportunity_id' => array_values(array_unique(array_filter($dealIds)))])
            ->indexBy('opportunity_id')
            ->all($db);

        $rows = [];
        foreach ($soRows as $r) {
            $opp = $oppRows[$r['deal_name']] ?? null;

            $rows[] = [
                'deal_owner'               => $owners[$r['opownerid']] ?? '',
                'deal_name'                => $opp['deal_name'] ?? '',
                'account_name'             => $accounts[$r['account_name']] ?? '',
                'vertical_manager'         => $vManagers[$r['vm_mag']] ?? '',
                'opportunity_stage'        => $stages[$r['op_stage_id']] ?? '',
                'category'                 => $prodCats[$r['product_category']] ?? '',
                'closing_date'             => $opp['closing_date'] ? date('d-m-Y', strtotime($opp['closing_date'])) : '',
                'so_approved_date'         => $r['so_approval_date'] ? date('d-m-Y', strtotime($r['so_approval_date'])) : '',
                'total_opportunity_price'  => $r['grand_total'],
                'customer_po_number'       => $r['customer_po_num'],
                'layout'                   => 'DEVIT',
                'so_number'                => $r['salesorder_dit_no'],
                'account_code'             => $vendorAccNames[$r['vendor_account_name']] ?? '',
            ];
        }

        return $rows;
    }

    protected function getRefMap(string $table, string $idColumn, string $labelExpr, array $ids): array
    {
        $ids = array_values(array_unique(array_filter($ids)));
        if (!$ids) {
            return [];
        }

        $rows = (new \yii\db\Query())
            ->from($table)
            ->select([
                $idColumn,
                'label' => new \yii\db\Expression($labelExpr),
            ])
            ->where([$idColumn => $ids])
            ->indexBy($idColumn)
            ->all(Yii::$app->db);

        $map = [];
        foreach ($rows as $id => $row) {
            $map[$id] = $row['label'];
        }

        return $map;
    }


    protected function generateSoReport(array $rows, string $reportDateYmd)
    {
        $filePath = Yii::getAlias("@runtime/reports/SO_Approve_Report_{$reportDateYmd}.csv");
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }

        $fp = fopen($filePath, 'w');

        fputcsv($fp, [
            'Deal Owner',
            'Deal Name',
            'Account Name',
            'Vertical Manger',
            'Opportunity Stage',
            'Category',
            'Closing Date',
            'SO Approved Date',
            'Total Opportunity Price',
            'Customer PO Number',
            'Layout',
            'SO Number',
            'Account Name'
        ]);

        foreach ($rows as $r) {
            fputcsv($fp, [
                $r['deal_owner'],
                $r['deal_name'],
                $r['account_name'],
                $r['vertical_manager'],
                $r['opportunity_stage'],
                $r['category'],
                $r['closing_date'],
                $r['so_approved_date'],
                $r['total_opportunity_price'],
                $r['customer_po_number'],
                $r['layout'],
                $r['so_number'],
                $r['account_code']
            ]);
        }

        fclose($fp);

        return $filePath;
    }

    protected function calcLineTaxAmount($line)
    {
        $base = (float)$line['basic_cost_price'] * (float)$line['qty'];
        $gst  = (float)$line['gst'];
        return round($base * $gst / 100, 2);
    }

    protected function getReleasedPoRows($start, $end)
    {
        $db = Yii::$app->db;

        $poRows = (new \yii\db\Query())
            ->from(['po' => 'purchase_order_dit'])
            ->where(['po.deleted' => 0])
            ->andWhere(['between', 'po.po_approve_date', $start, $end])
            ->andWhere(['po.stage' => 4])
            ->all($db);

        if (!$poRows) {
            return [];
        }

        $stageIds      = array_column($poRows, 'stage');
        $poIds         = array_column($poRows, 'purchaseorder_dit_id');
        $vendorIds     = array_column($poRows, 'vendor_name');
        $deliveryWhIds = array_column($poRows, 'bill_entitiy_name');  
        $issuedWhIds   = array_column($poRows, 'po_Issued_entity_name');  

        $poStages = $this->getRefMap(
            'purchaseorder_stage',
            'purchaseorder_stage_id',
            'purchaseorder_value',
            $stageIds
        );

        $allLineRefs = (new \yii\db\Query())
            ->from('purchaseorderdit_product_details')
            ->select('reference_no')
            ->where([
                'deleted' => 0,
                'purchaseorder_dit_id' => array_unique(array_filter($poIds))
            ])
            ->column($db);

        $allSoIds = [];
        foreach ($allLineRefs as $refsStr) {
            if ($refsStr) {
                $refs = array_map('trim', explode(',', $refsStr));
                $allSoIds = array_merge($allSoIds, $refs);
            }
        }
        $allSoIds = array_unique(array_filter($allSoIds));

        $soRows = [];
        if ($allSoIds) {
            $soRows = (new \yii\db\Query())
                ->from(['so' => 'salesorder_dit'])
                ->select(['so.salesorder_dit_id', 'so.salesorder_dit_no', 'so.deal_name_auto'])
                ->where(['so.salesorder_dit_id' => $allSoIds])
                ->indexBy('salesorder_dit_id')
                ->all($db);
        }
        $vendorRows = (new \yii\db\Query())
            ->from('vendor_account')
            ->select(['vendoraccid', 'acc_name', 'payment_terms'])
            ->where(['vendoraccid' => array_unique(array_filter($vendorIds))])
            ->indexBy('vendoraccid')
            ->all($db);
        
        $deliveryWhIdsClean = array_unique(array_filter($deliveryWhIds));
        $issuedWhIdsClean   = array_unique(array_filter($issuedWhIds));
        $deliveryWhMap      = [];  
        $issuedWhMap        = [];
        if ($deliveryWhIdsClean) {
            $deliveryWhMap = (new \yii\db\Query())
                ->from('warehouse')
                ->select(['warehouse_id', 'warehouse_name'])
                ->where(['warehouse_id' => $deliveryWhIdsClean])
                ->indexBy('warehouse_id')
                ->all($db);
        }
        if ($issuedWhIdsClean) {
            $issuedWhMap = (new \yii\db\Query())
                ->from('warehouse')
                ->select(['warehouse_id', 'warehouse_name'])
                ->where(['warehouse_id' => $issuedWhIdsClean])
                ->indexBy('warehouse_id')
                ->all($db);
        }

        $lineRows = (new \yii\db\Query())
            ->from('purchaseorderdit_product_details')
            ->where([
                'deleted'               => 0,
                'purchaseorder_dit_id'  => array_unique(array_filter($poIds)),
            ])
            ->all($db);

        if (!$lineRows) {
            return [];
        }

        $productIds = array_column($lineRows, 'product_name');
        $productIdsClean = array_unique(array_filter($productIds));

        $productRows = [];
        if ($productIdsClean) {
            $productRows = (new \yii\db\Query())
                ->from('product_dit')
                ->select(['productdit_id', 'product_name'])
                ->where(['productdit_id' => $productIdsClean])
                ->indexBy('productdit_id')
                ->all($db);
        }

        $rows = [];
        foreach ($lineRows as $line) {
            $po = null;
            foreach ($poRows as $poRow) {
                if ($poRow['purchaseorder_dit_id'] == $line['purchaseorder_dit_id']) {
                    $po = $poRow;
                    break;
                }
            }
            if (!$po) continue;

            $lineRefs = array_map('trim', explode(',', $line['reference_no'] ?? ''));
            $matchingSORows = [];
            foreach ($lineRefs as $refId) {
                if (isset($soRows[$refId])) {
                    $matchingSORows[] = $soRows[$refId];
                }
            }

            $refSo = !empty($matchingSORows) ? reset($matchingSORows) : null;

            $opportunityNames = [];
            foreach ($matchingSORows as $soRow) {
                if (!empty($soRow['deal_name_auto'])) {
                    $opportunityNames[] = $soRow['deal_name_auto'];
                }
            }
            $opportunityNames = array_unique($opportunityNames);
            $opportunityName = implode(', ', $opportunityNames);

            $product = $productRows[$line['product_name']] ?? null;
            $vendor  = $vendorRows[$po['vendor_name']] ?? null;
            $deliveryWh = $deliveryWhMap[$po['bill_entitiy_name']] ?? null;
            $issuedWh = $issuedWhMap[$po['po_Issued_entity_name']] ?? null;

            $rows[] = [
                'po_number'             => $po['purchaseorder_dit_no'],
                'po_date'               => $po['purchase_order_date'] ? date('d-m-Y', strtotime($po['purchase_order_date'])) : '',
                'delivery_date'         => $po['estimate_time_delivery'] ? date('d-m-Y', strtotime($po['estimate_time_delivery'])) : '',
                'reference'             => $refSo['salesorder_dit_no'] ?? '',
                'po_status'             => $poStages[$po['stage']] ?? '',
                'source_of_supply'      => $po['source_of_supply'],
                'destination_of_supply' => $po['destination_of_supply'],
                'vendor_name'           => $vendor['acc_name'] ?? '',
                'currency_code'         => 'Rupee',
                'item_name'             => $product['product_name'] ?? $line['product_name'],
                'item_description'      => $line['product_description'],
                'qty_ordered'           => $line['qty'],
                'warehouse_name'        => $deliveryWh['warehouse_name'] ?? '', 
                'item_tax_amount'       => $line['qty'] * ($line['basic_cost_price'] * (($line['sgst'] + $line['cgst'] + $line['igst'])/100)),
                'item_price'            => $line['basic_cost_price'],
                'po_amount'             => $po['total'],
                'payment_terms_label'   => $vendor['payment_terms'] ?? '',
                'attention'             => $issuedWh['warehouse_name'] ?? '',    
                'opportunity_name'      => $opportunityName, 
            ];
        }

        return $rows;
    }



    protected function generatePoReport(array $rows, string $reportDateYmd)
    {
        $filePath = Yii::getAlias("@runtime/reports/PO_Approve_Report_{$reportDateYmd}.csv");
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }

        $fp = fopen($filePath, 'w');

        fputcsv($fp, [
            'PO Number',
            'PO Date',
            'Delivery Date',
            'Reference',
            'PO Status',
            'Source of Supply',
            'Destination Of Supply',
            'Vendor Name',
            'Currency Code',
            'Item Name',
            'Item Description',
            'Quantity Ordered',
            'Warehouse Name',
            'Item Tax Amount',
            'Item Price',
            'PO Amount',
            'Payment Terms Label',
            'Attention',
            'Opportunity Name',
        ]);

        foreach ($rows as $r) {
            fputcsv($fp, [
                $r['po_number'],
                $r['po_date'],
                $r['delivery_date'],
                $r['reference'],
                $r['po_status'],
                $r['source_of_supply'],
                $r['destination_of_supply'],
                $r['vendor_name'],
                $r['currency_code'],
                $r['item_name'],
                $r['item_description'],
                $r['qty_ordered'],
                $r['warehouse_name'],
                $r['item_tax_amount'],
                $r['item_price'],
                $r['po_amount'],
                $r['payment_terms_label'],
                $r['attention'],
                $r['opportunity_name'],
            ]);
        }

        fclose($fp);

        return $filePath;
    }

    protected function getPurchaseReqRows($start, $end)
    {
        $db = Yii::$app->db;

        $soRows = (new \yii\db\Query())
            ->from(['op' => 'opportunity'])
            ->innerJoin(['opd' => 'opportunity_product_detail'], 'op.opportunity_id = opd.opportunity_id')
            ->where(['opd.deleted' => 0])
            ->andWhere(['between', 'op.modifiedtime', $start, $end])
            // ->andWhere(['so_dit.so_stage' => 4])
            ->select([
                'opd.*',
                'op.opportunity_stage AS op_stage_id',
                'op.product_category AS product_category',
                'op.vendor_account_name AS vendor_account_name',
                'op.devit_vertical_manager AS vm_mag',
                'op.ownerid AS opownerid'
            ])
            ->all($db);

        if (!$soRows) {
            return [];
        }

        $ownerIds     = [];
        $vmIds        = [];
        $accountIds   = [];
        $stageIds     = [];
        $dealIds      = [];
        $prodCatIds   = [];
        $vendorAccIds = [];

        foreach ($soRows as $r) {
            $ownerIds[]     = $r['opownerid'];
            $vmIds[]        = $r['vm_mag'];
            $accountIds[]   = $r['account_name'];
            $stageIds[]     = $r['op_stage_id'];
            $dealIds[]      = $r['deal_name'];
            $prodCatIds[]   = $r['product_category'];
            $vendorAccIds[] = $r['vendor_account_name'];
        }
        $owners     = $this->getRefMap('user', 'id', "CONCAT(first_name,' ',last_name)", $ownerIds);
        $vManagers  = $this->getRefMap('user', 'id', "CONCAT(first_name,' ',last_name)", $vmIds);
        $accounts   = $this->getRefMap('vendor_account', 'vendoraccid', 'acc_name', $accountIds);
        $stages     = $this->getRefMap('oppr_stage', 'stage_id', 'stage_value', $stageIds);
        $prodCats = $this->getRefMap('oppr_product_category', 'product_category_id', 'product_category_value', $prodCatIds);
        $vendorAccNames = $this->getRefMap('vendor_account', 'vendoraccid', 'cust_code', $vendorAccIds);

        $oppRows = (new \yii\db\Query())
            ->from('opportunity')
            ->select([
                'opportunity_id',
                'deal_name',
                'closing_date',
                'product_category',
                'vendor_account_name'
            ])
            ->where(['opportunity_id' => array_values(array_unique(array_filter($dealIds)))])
            ->indexBy('opportunity_id')
            ->all($db);

        $rows = [];
        foreach ($soRows as $r) {
            $opp = $oppRows[$r['deal_name']] ?? null;

            $rows[] = [
                'deal_owner'               => $owners[$r['opownerid']] ?? '',
                'deal_name'                => $opp['deal_name'] ?? '',
                'account_name'             => $accounts[$r['account_name']] ?? '',
                'vertical_manager'         => $vManagers[$r['vm_mag']] ?? '',
                'opportunity_stage'        => $stages[$r['op_stage_id']] ?? '',
                'category'                 => $prodCats[$r['product_category']] ?? '',
                'closing_date'             => $opp['closing_date'] ? date('d-m-Y', strtotime($opp['closing_date'])) : '',
                'so_approved_date'         => $r['so_approval_date'] ? date('d-m-Y', strtotime($r['so_approval_date'])) : '',
                'total_opportunity_price'  => $r['grand_total'],
                'customer_po_number'       => $r['customer_po_num'],
                'layout'                   => 'DEVIT',
                'so_number'                => $r['salesorder_dit_no'],
                'account_code'             => $vendorAccNames[$r['vendor_account_name']] ?? '',
            ];
        }

        return $rows;
    }
    protected function getPurchaseReqRowsReport(array $rows, string $reportDateYmd)
    {
        $filePath = Yii::getAlias("@runtime/reports/PurchaseReq_{$reportDateYmd}.csv");
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }

        $fp = fopen($filePath, 'w');

        fputcsv($fp, [
            'Deal Owner',
            'Deal Name',
            'Account Name',
            'Vertical Manger',
            'Opportunity Stage',
            'Category',
            'Closing Date',
            'SO Approved Date',
            'Total Opportunity Price',
            'Customer PO Number',
            'Layout',
            'SO Number',
            'Account Name'
        ]);

        foreach ($rows as $r) {
            fputcsv($fp, [
                $r['deal_owner'],
                $r['deal_name'],
                $r['account_name'],
                $r['vertical_manager'],
                $r['opportunity_stage'],
                $r['category'],
                $r['closing_date'],
                $r['so_approved_date'],
                $r['total_opportunity_price'],
                $r['customer_po_number'],
                $r['layout'],
                $r['so_number'],
                $r['account_code']
            ]);
        }

        fclose($fp);

        return $filePath;
    }

    protected function sendEmail($soFile = null,$poFile = null,bool $hasData = true,string $reportDateDisplay,string $reportDateYmd) {
        $todayDisplay = date('d/m/Y'); 

        if ($hasData) {
            $subject = "Daily Approved SO & PO Report - {$todayDisplay}";
            $body = "Please find attached reports for {$reportDateDisplay}.\n\n"
                . "- SO Approved Report\n"
                . "- PO Approved Report\n";
        } else {
            $subject = "Daily Approved SO & PO Report - {$todayDisplay}";
            $body = "Hello Team,\n\n"
                . "No approved data was found for the SO and PO report dated {$reportDateDisplay}.";
        }

        $mailer = Yii::$app->mailer->compose()
            ->setFrom([Yii::$app->params['SMTP_USER'] => 'DevIT Reports'])
            ->setTo([
                'vanita.kwatra@bluemonk.com',
                'Yaduraj.singh@ditserv.com',
                'nobel@ditserv.com',
                'manvi.chauhan@ditserv.com',
            ])
            ->setCc(['deepa@tetrain.com', 'rakeshdubey@tetrain.com'])
            ->setBcc(['deepika.tetra@gmail.com'])
            ->setSubject($subject)
            ->setTextBody($body);

        if ($hasData && is_file($soFile)) {
            $mailer->attach($soFile, [
                'fileName'    => "SO_Approve_Report_{$reportDateYmd}.csv",
                'contentType' => 'text/csv',
            ]);
        }

        if ($hasData && is_file($poFile)) {
            $mailer->attach($poFile, [
                'fileName'    => "PO_Approve_Report_{$reportDateYmd}.csv",
                'contentType' => 'text/csv',
            ]);
        }

        $mailer->send();
    }


}
