<?php

namespace backend\modules\purchaseorder\controllers;

use common\components\TcpdfHelper;

class MyPDF extends TcpdfHelper
{

    // Add watermark on every page
    public function Header1()
    {
        // Transparency
        $this->SetAlpha(0.3);

        // Font & color
        $fontSize = 100;
        $this->SetFont('dejavusans', 'B', $fontSize);
        $this->SetTextColor(150, 150, 150);

        $text = 'DRAFT';

        // Calculate text width using same font & size
        $textWidth = $this->GetStringWidth($text, 'dejavusans', 'B', $fontSize);

        // Center position
        $pageWidth  = $this->getPageWidth();
        $pageHeight = $this->getPageHeight();
        $x = ($pageWidth - $textWidth) / 2;
        $y = ($pageHeight / 2) + ($fontSize / 4); // adjust for vertical centering

        // Rotate and print watermark
        $this->StartTransform();
        $this->Rotate(45, $pageWidth / 2, $pageHeight / 2);
        $this->Text($x, $y, $text);
        $this->StopTransform();

        $this->SetAlpha(1);
    }

    public function Header2() {
        $this->SetAlpha(0.3);
        $fontSize = 100;
        $this->SetFont('dejavusans', 'B', $fontSize);
        $this->SetTextColor(150, 150, 150);

        $text = 'DRAFT';
        $pageWidth  = $this->getPageWidth();
        $pageHeight = $this->getPageHeight();

        // calculate string width
        $textWidth = $this->GetStringWidth($text);

        $this->StartTransform();
        // rotate around the page center
        $this->Rotate(20, $pageWidth/2, $pageHeight/2);

        // X should be center minus half text width
        $x = ($pageWidth - $textWidth) / 2;
        // Y should be middle of the page (adjust a little because baseline is used)
        $y = $pageHeight / 2;

        $this->Text($x, $y, $text);

        $this->StopTransform();
        $this->SetAlpha(1);
    }

    public function Header() {
    $this->SetAlpha(0.4); // lighter transparency for repeats
    $this->SetFont('dejavusans', 'B', 40);
    $this->SetTextColor(200, 200, 200);

    $text = 'DRAFT';

    // Page & margin values
    $margins = $this->getMargins();
    $left   = $margins['left'];
    $right  = $margins['right'];
    $top    = $margins['top'];
    $bottom = $margins['bottom'];

    $usableWidth  = $this->getPageWidth()  - $left - $right;
    $usableHeight = $this->getPageHeight() - $top - $bottom;

    // Watermark placement step (adjust for density)
    $stepX = 100; // horizontal gap
    $stepY = 60;  // vertical gap

    $this->StartTransform();
    // Rotate whole grid
    $this->Rotate(45, $left + ($usableWidth/2), $top + ($usableHeight/2));

    // Loop to repeat watermark text
    for ($y = $top; $y < ($this->getPageHeight() - $bottom); $y += $stepY) {
        for ($x = $left; $x < ($this->getPageWidth() - $right); $x += $stepX) {
            $this->Text($x, $y, $text);
        }
    }

    $this->StopTransform();
    $this->SetAlpha(1);
}



    // Yo
}
