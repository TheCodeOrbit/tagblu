<?php

namespace common\components;

use Yii;
use yii\db\Query;

/**
 * PdfHeaderFooterHelper
 * 
 * Single static helper to handle ALL header/footer logic from DB
 * Just call: PdfHeaderFooterHelper::setupPdfWithTemplate($pdf, $tabId, $vars)
 * 
 * Usage in controller:
 * $pdf = new TcpdfHelper();
 * PdfHeaderFooterHelper::setupPdfWithTemplate($pdf, $this->TabId, $tplVars);
 * $pdf->AddPage();
 */
class PdfHeaderFooterHelper
{
    /**
     * Load header/footer from DB by tabId, apply vars, and configure PDF object
     * 
     * @param TcpdfHelper|MyPDF $pdf - The PDF instance to configure
     * @param int $tabId - The tab_id to fetch template from pdf_headers_footers table
     * @param array $vars - Template variables to replace in header/footer HTML
     *                      Example: ['purchase_order_no' => 'PO-001', 'vendor_name' => 'ACME Inc']
     * @return void - Modifies $pdf object directly
     */
    public static function setupPdfWithTemplate($pdf, int $tabId, array $vars, $pdfName, $isWaterMark = false)
    {
        $pdfTpl = (new Query())
            ->from('pdf_headers_footers')
            ->where([
                'tab_id' => $tabId,
                'status' => 1,
                'name'   => $pdfName
            ])
            ->orderBy(['id' => SORT_ASC])
            ->one();

        if (!$pdfTpl) {
            return;
        }
        // if (!empty($pdfTpl['stamp']) && $pdfTpl['stamp'] != '' && isset($pdfTpl['stamp_status']) && $pdfTpl['stamp_status'] == 1) {
        //     self::addStampToPdf($pdf, $pdfTpl['stamp'], $pdfName,true);
        // }
        $tplHeaderHtml = $pdfTpl['header_content'] ?? '';
        $tplFooterHtml = $pdfTpl['footer_content'] ?? '';

        $tplHeaderHtml = preg_replace('/<script.*?<\/script>/is', '', $tplHeaderHtml);

        $headerCss = '';
        if (preg_match('/<style.*?<\/style>/is', $tplHeaderHtml, $matches)) {
            $headerCss     = $matches[0];
            $tplHeaderHtml = str_replace($matches[0], '', $tplHeaderHtml);
        }
        // if(isset($pdf->stampUrl) && $pdf->stampUrl != ''){
        //     $vars['deshwal_stamp'] = $pdf->stampUrl;
        // }
        $headerHtml = self::renderPdfTemplateVars($tplHeaderHtml, $vars);
        $footerHtml = self::renderPdfTemplateVars($tplFooterHtml, $vars);

        $pdf->customHeaderHtml = $headerHtml;
        $pdf->customFooterHtml = $footerHtml;
        $pdf->customHeaderCss  = $headerCss;

        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);
        if ($isWaterMark) {
            $pdf->isWaterMark = true;
            if (method_exists($pdf, 'enableWatermark')) {
                $pdf->enableWatermark(true);
            }
        }
        // if (!empty($pdfTpl['stamp']) && $pdfTpl['stamp'] != '' && isset($pdfTpl['stamp_status']) && $pdfTpl['stamp_status'] == 1) {
        //     self::addStampToPdf($pdf, $pdfTpl['stamp'], $pdfName);
        // }
        
    }


    /**
     * Simple template variable replacer
     * Replaces {variable_name} with value from array
     * 
     * @param string $html - HTML with {variable} placeholders
     * @param array $vars - Variables to replace
     * @return string - HTML with variables replaced
     */
    private static function renderPdfTemplateVars(string $html, array $vars): string
    {
        if ($html === '' || empty($vars)) {
            return $html;
        }

        $search  = [];
        $replace = [];

        foreach ($vars as $key => $value) {
            $search[]  = '{' . $key . '}';
            $replace[] = (string)$value;
        }

        return str_replace($search, $replace, $html);
    }

    private static function addStampToPdf($pdf, int $stampAttachmentId, string $pdfType, $justUrl = false): void
    {
        $stampAttachment = (new Query())
            ->from('attachments')
            ->where(['attachmentsid' => $stampAttachmentId])
            ->one();

        if ($stampAttachment && !empty($stampAttachment['path'])) {
            $baseUploadPath = Yii::getAlias('@webroot/');
            $stampPath = $baseUploadPath . $stampAttachment['path'];

            if (file_exists($stampPath)) {
                if ($justUrl) {
                    $pdf->stampUrl = $stampPath;
                    return;
                }
                $stampConfig = self::getStampConfigByPdfType($pdfType);

                if (empty($stampConfig)) {
                    return;
                }

                $pdf->stampData = [
                    'imagePath' => $stampPath,
                    'x' => $stampConfig['x'] ?? null,           // optional: use for custom x
                    'y' => $stampConfig['y'] ?? null,           // optional: use for custom y
                    'width' => $stampConfig['width'] ?? 45,
                    'height' => $stampConfig['height'] ?? 45,
                    'opacity' => $stampConfig['opacity'] ?? 0.8,
                    'position' => $stampConfig['position'] ?? 'top-right',  // preset position
                ];
            }
        }
    }

    private static function getStampConfigByPdfType(string $pdfType): array
    {
        $stampConfigs = [
            'delivery_challan' => [
                'position' => 'top-right',   
                'width' => 45,
                'height' => 45,
                'opacity' => 0.8,
            ],
            'invoice_dit' => [
                'position' => 'top-right',
                'width' => 45,
                'height' => 45,
                'opacity' => 0.8,
            ],
            'purchase_order' => [
                'position' => 'top-right',
                'width' => 45,
                'height' => 45,
                'opacity' => 0.8,
            ],
            'quotes_devit' => [
                'position' => 'top-right',
                'width' => 45,
                'height' => 45,
                'opacity' => 0.8,
            ],
            'quotes_cr' => [
                'position' => 'top-right',
                'width' => 45,
                'height' => 45,
                'opacity' => 0.8,
            ],
            'quotes' => [
                'position' => 'top-right',
                'width' => 45,
                'height' => 45,
                'opacity' => 0.8,
            ],
        ];

        return $stampConfigs[$pdfType] ?? [];
    }
}
