<?php
namespace backend\modules\quotes\controllers;
use common\components\TcpdfHelper;

class MyPDF extends TcpdfHelper {
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-25);
        $this->SetFont('dejavusans', '', 7);
        
        $html = <<<EOD
        <table cellpadding="1">
          <tr>
            
            <td align="center">
              <h4>DESHWAL WASTE MANAGEMENT PRIVATE LIMITED</h4>
              </td>
              </tr> <tr><td align="center">
              Registered Address: Plot No 15, Sector 5 IMT MANESAR, Manesar Gurgaon HR 122050 IN
              </td>
              </tr> <tr><td align="center">
              Corporate Address: Plot No 79, 1st Floor, Sector 34, Hero Honda Chowk, Gurugram, Haryana 122004
            </td>
            </tr>
            <tr>
            <td align="center">CIN: U74900HR2013PTC049334
            </td>
          </tr>
        </table>
        EOD;

        $this->writeHTML($html);
    }
}
