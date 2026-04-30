<?php
namespace backend\modules\quotesdit\controllers;
use common\components\TcpdfHelper;

class MyPDF extends TcpdfHelper {
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-25);
        $this->SetFont('dejavusans', '', 7);
        
        $html = <<<EOD
                <table width="100%" cellpadding="6" cellspacing="0" style="border-top: 1px solid #000; font-family: Arial, sans-serif; font-size: 9px;">
                  <tr>
                    <td style="text-align: center; line-height: 1;">
                      <strong style="font-size: 9px;">DEV IT Serv Private Limited</strong><br>
                      <span style="font-size: 7px;line-height:12px;font-weight:bold">Email:info@ditserv.com,web: https://devitserv.com/ ,CIN:U72200DL2005PTC137515<br>3rd Floor, Plot No-79, Bluemonk House, Jaipur Road, Sector 34, Gurugram, Gurugram, Haryana, 122001</span>
                    </td>
                    </tr>
                    <tr>
                    <td style="text-align: center;font-size: 7px;">This is a system generated quotation and does not require sign and stamp
                    </td>
                  </tr>
                </table>
                EOD;

        $this->writeHTML($html);
    }
}
