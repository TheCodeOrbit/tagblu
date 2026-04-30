<?php
namespace backend\modules\deliverychallandit\controllers;
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
                      <strong style="font-size: 13px;">DEV IT SERV PRIVATE LIMITED</strong><br>
                      Registered Address: B1/14 Basement Eros Apartment 56, Nehru Place Near Market, New Delhi, New Delhi DL - 110019<br>
                      Corporate Address: Plot No-79, 1st Floor, Bluemonk House, Jaipur Road, Sector 34, Gurugram, Haryana, 122001<br>
                      Mumbai Address: Gate No-520, Behind Shell Petrol Pump, Pune Nagar Road, Wagholi, Pune, Maharashtra, 412207<br>
                      Bangalore Address: #3503/A, 14th Main Road, HAL 2nd Stage, Bangalore, Karnataka, 560038<br>
                      <strong>CIN:</strong> U72200DL2005PTC137515
                    </td>
                  </tr>
                </table>
                EOD;

        // $this->writeHTML($html);
    }
}
