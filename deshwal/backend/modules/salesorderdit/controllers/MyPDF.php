<?php
namespace backend\modules\salesorderdit\controllers;
use common\components\TcpdfHelper;

class MyPDF extends TcpdfHelper {
   public function Footer() {
    // Position at 15 mm from bottom
    $this->SetY(-15);
    
    // Set font for the page number
    $this->SetFont('dejavusans', '', 7);
    
    // Current page number
    $currentPage = $this->getPage();
    
    // Total pages (this will be replaced by the actual total number of pages)
    $totalPages = $this->getAliasNbPages();

    // Move to the right bottom corner
    // $this->SetX(0);  // Adjust position to the right
     // Set font size and font family for the page number
    $this->SetFont('dejavusans', '', 6);  // Change '10' to the desired font size
     // Set the line color to RGB(208, 208, 208)
    $this->SetDrawColor(208, 208, 208);  // Set line color (RGB)
    // Set font color for the page number (RGB format)
    $this->SetTextColor(208, 208, 208);  //  (you can change the RGB values as needed)
     // Draw a horizontal line above the page number
    $this->Line(10, $this->GetY() , $this->getPageWidth() - 10, $this->GetY() ); // Draw a line 5 units above the current Y-position

    // Move to the correct Y position after the line (below the line)
    $this->SetY($this->GetY() );  // Move a little lower to ensure there's space between the line and the page number
    
    
    // Display page number in "Page X of Y" format
    // $this->Cell(0, 10, 'Page ' . $currentPage . ' of ' . $totalPages, 0, false, 'R');
    $this->Cell(0, 10, $currentPage, 0, false, 'R');

    // // Optionally, you can still keep the company info in the footer if you want it in a smaller font above the page number
    // // If you want to keep your previous company info at the top of the footer, you can do this:

    // $this->SetY(-50);  // Move up to the space for the company info
    // $this->SetFont('dejavusans', '', 7);
    
    // $html = <<<EOD
    // <table cellpadding="1">
    //   <tr>
    //     <td align="center">
    //       <h4>DESHWAL WASTE MANAGEMENT PRIVATE LIMITED</h4>
    //     </td>
    //   </tr>
    //   <tr>
    //     <td align="center">
    //       Registered Address: Plot No 15, Sector 5 IMT MANESAR, Manesar Gurgaon HR 122050 IN
    //     </td>
    //   </tr>
    //   <tr>
    //     <td align="center">
    //       Corporate Address: Plot No 79, 1st Floor, Sector 34, Hero Honda Chowk, Gurugram, Haryana 122004
    //     </td>
    //   </tr>
    //   <tr>
    //     <td align="center">CIN: U74900HR2013PTC049334</td>
    //   </tr>
    // </table>
    // EOD;

    // $this->writeHTML($html); // Display company info in the footer
}

}
