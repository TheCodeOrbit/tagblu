<?php
// common/components/MyPDF.php
namespace common\components;
require_once \Yii::getAlias('@tcpdf') . '/tcpdf.php';
use TCPDF;
use yii\db\Query;
class MyPDF extends TCPDF
{
    public $customHeaderHtml = '';
    public $customHeaderCss = '';
    public $customFooterHtml = '';
    public $isWaterMark = false;
    public $stampData = [];
    public $stampUrl = '';
    private $headerContentHeight = 0;

    /**
     * Header method - minimal, only for watermark
     * Stamp will be drawn separately with flexible positioning
     */
    public function Header()
    {
        if ($this->getPage() <= 0) {
            return;
        }

        $pageW = $this->getPageWidth();
        $pageH = $this->getPageHeight();

        if ($pageW > 0 && $pageH > 0) {
            $margins = $this->getMargins();
            $left = isset($margins['left']) ? $margins['left'] : 0;
            $right = isset($margins['right']) ? $margins['right'] : 0;
            $top = isset($margins['top']) ? $margins['top'] : 0;
            $bottom = isset($margins['bottom']) ? $margins['bottom'] : 0;
            $usableWidth = max(1, $pageW - $left - $right);
            $usableHeight = max(1, $pageH - $top - $bottom);

            // Watermark only
            if ($this->isWaterMark) {
                $this->SetAlpha(0.4);
                $this->SetFont('dejavusans', 'B', 40);
                $this->SetTextColor(200, 200, 200);
                $text = 'DRAFT';
                $stepX = 100;
                $stepY = 60;
                $this->StartTransform();
                $this->Rotate(45, $left + $usableWidth / 2, $top + $usableHeight / 2);
                for ($y = $top; $y < ($pageH - $bottom); $y += $stepY) {
                    for ($x = $left; $x < ($pageW - $right); $x += $stepX) {
                        $this->Text($x, $y, $text);
                    }
                }
                $this->StopTransform();
                $this->SetAlpha(1);
            }
        }
    }

    /**
     * Draw header content after AddPage()
     * This ensures header stays above body content
     */
    public function drawHeaderContent()
    {
        if (empty($this->customHeaderHtml) && empty($this->customHeaderCss)) {
            return;
        }

        $margins = $this->getMargins();
        $left = isset($margins['left']) ? $margins['left'] : 0;
        $top = isset($margins['top']) ? $margins['top'] : 0;

        $currentX = $this->GetX();
        $currentY = $this->GetY();

        $this->SetXY($left, $top);

        if (!empty($this->customHeaderCss)) {
            $this->writeHTML($this->customHeaderCss, true, false, true, false, '');
        }

        if (!empty($this->customHeaderHtml)) {
            $this->writeHTML($this->customHeaderHtml, true, false, true, false, '');
        }

        $headerEndY = $this->GetY();
        $this->headerContentHeight = $headerEndY - $top;

        $this->SetXY($left, $headerEndY + 2);
    }

    public function drawStamp()
    {
        if (empty($this->stampData) || empty($this->stampData['imagePath'])) {
            return;
        }

        if (!file_exists($this->stampData['imagePath'])) {
            return;
        }

        $imagePath = $this->stampData['imagePath'];
        $width = $this->stampData['width'] ?? 45;
        $height = $this->stampData['height'] ?? 45;
        $opacity = $this->stampData['opacity'] ?? 0.8;
        $position = $this->stampData['position'] ?? 'top-right';

        $x = $this->stampData['x'] ?? null;
        $y = $this->stampData['y'] ?? null;

        if ($x === null || $y === null) {
            $pageW = $this->getPageWidth();
            $pageH = $this->getPageHeight();
            $margins = $this->getMargins();
            $left = isset($margins['left']) ? $margins['left'] : 0;
            $right = isset($margins['right']) ? $margins['right'] : 0;
            $top = isset($margins['top']) ? $margins['top'] : 0;
            $bottom = isset($margins['bottom']) ? $margins['bottom'] : 0;

            switch ($position) {
                case 'top-right':
                    $x = $pageW - $right - $width - 5;
                    $y = $top + 5;
                    break;

                case 'top-left':
                    $x = $left + 5;
                    $y = $top + 5;
                    break;

                case 'bottom-right':
                    $x = $pageW - $right - $width - 5;
                    $y = $pageH - $bottom - $height - 5;
                    break;

                case 'bottom-left':
                    $x = $left + 5;
                    $y = $pageH - $bottom - $height - 5;
                    break;

                case 'center':
                    $x = ($pageW - $width) / 2;
                    $y = ($pageH - $height) / 2;
                    break;

                case 'above-header':
                    $x = $pageW - $right - $width - 5;
                    $y = $top - $height - 5;
                    break;

                case 'above-footer':
                    $x = $pageW - $right - $width - 5;
                    $y = $pageH - $bottom - $height - 15;
                    break;

                case 'custom':
                default:
                    $x = $x ?? ($pageW - $right - $width - 5);
                    $y = $y ?? ($top + 5);
                    break;
            }
        }

        $currentAlpha = $this->GetAlpha();
        $currentX = $this->GetX();
        $currentY = $this->GetY();

        $this->SetAlpha($opacity);

        $this->Image(
            $imagePath,
            $x,
            $y,
            $width,
            $height,
            '',   
            '',   
            'T',    
            false,
            300,
            '',
            false,
            false,
            0,
            false,
            false,
            false
        );

        // Restore state
        $this->SetAlpha($currentAlpha);
        $this->SetXY($currentX, $currentY);
    }

    public function Footer()
    {
        $this->SetY(-25);
        $this->SetFont('dejavusans', '', 7);
        if (!empty($this->customFooterHtml)) {
            $this->writeHTML($this->customFooterHtml, true, false, true, false, '');
        }
    }
}