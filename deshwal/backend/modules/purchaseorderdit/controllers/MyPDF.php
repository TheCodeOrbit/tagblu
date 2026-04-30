<?php
namespace backend\modules\purchaseorderdit\controllers;
use common\components\TcpdfHelper;

class MyPDF extends TcpdfHelper {
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-25);
        $this->SetFont('dejavusans', '', 7);
        
        $html = <<<EOD
                <table width="100%" cellpadding="6" cellspacing="0" style="border-top: 1px solid #000; font-family: Arial, sans-serif; font-size: 6px;">
                  <tr>
                    <td style="text-align: center; line-height: 1;">Organization Address (from Business entity in PO) Phone: <strong>Dev IT Serv Private Limited</strong>, <strong>Email:</strong>info@ditserv.com <strong>Web:</strong> http://www.ditserv.com, <strong>PAN No.:</strong>AACCD2388K<br>**This is a computer-generated statement and does not require Sign and Stamp
                    </td>
                  </tr>
                </table>
                EOD;

        $this->writeHTML($html);
    }
}
