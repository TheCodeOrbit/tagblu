<?php

/* ===== DB CONNECTION ===== */


require_once("comman.inc.php");
$pdo = db_connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* =====================================
   CLASS
===================================== */

class StockCalculation {

    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    /* ===== Dummy opening stock function ===== */

    public function getOpeningStockForTodayDeshwal($productId,$location){

        $stmt = $this->pdo->prepare("
            SELECT quantity
            FROM openingstock_prod
            WHERE productid=? AND location=?
            ORDER BY stock_date DESC
            LIMIT 1
        ");

        $stmt->execute([$productId,$location]);

        $qty = $stmt->fetchColumn();

        return [
            'quantity' => $qty ?: 0
        ];
    }


    /* =====================================
    YOUR FUNCTION (UNCHANGED)
    ===================================== */

    public function getTodayStockSingleProductdeshwal($productId,$location)
    {

        $today = date('Y-m-d');

        $openingData = $this->getOpeningStockForTodayDeshwal($productId,$location);
        $openingQty = $openingData['quantity'] ?? 0;


    /* ===== INWARD ===== */

    $stmt = $this->pdo->prepare("
        SELECT IFNULL(SUM(qty),0)
        FROM inventory
        WHERE product_name=? 
        AND location=? 
        AND status=1
        AND modifiedtime BETWEEN ? AND ?
    ");

    $stmt->execute([
        $productId,
        $location,
        $today.' 00:00:00',
        $today.' 23:59:59'
    ]);

    $inwardQty = $stmt->fetchColumn();



    /* ===== OUTWARD ===== */

    $stmt = $this->pdo->prepare("
        SELECT IFNULL(SUM(sid.qty),0)
        FROM salesorder_items_detail sid
        JOIN sales_order so 
        ON so.salesorder_id = sid.salesorder_id
        WHERE sid.product_name=? 
        AND so.ship_wh_location=? 
        AND so.modifiedtime BETWEEN ? AND ?
    ");

    $stmt->execute([
        $productId,
        $location,
        $today.' 00:00:00',
        $today.' 23:59:59'
    ]);

    $outwardQty = $stmt->fetchColumn();



    /* ===== FINAL STOCK ===== */

    $closingStock = $openingQty + $inwardQty - $outwardQty;



    /* ===== DELETE OLD STOCK ===== */

    $stmt = $this->pdo->prepare("
        DELETE FROM rep_prodstock
        WHERE productid=? 
        AND location=? 
        AND stockdate=?
    ");

    $stmt->execute([$productId,$location,$today]);



    /* ===== INSERT NEW STOCK ===== */

    $stmt = $this->pdo->prepare("
        INSERT INTO rep_prodstock
        (productid,stock_quantity,total_in,total_out,location,stockdate,created_at)
        VALUES (?,?,?,?,?,?,NOW())
    ");

    $stmt->execute([
        $productId,
        $closingStock,
        $inwardQty,
        $outwardQty,
        $location,
        $today
    ]);

    }

} // ← class closed correctly



/* =====================================
   CRON PART
===================================== */

$grn_id = $_GET['grn_id'] ?? $_POST['grn_id'] ?? 0;

if(!$grn_id){
    echo "Provide GRN ID\n";
    exit;
}


/* ===== FETCH PRODUCTS ===== */

$stmt = $pdo->prepare("
    SELECT DISTINCT product_name,location
    FROM inventory
    WHERE grn_id=?
");

$stmt->execute([$grn_id]);

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stock = new StockCalculation($pdo);

foreach($products as $row){

    $stock->getTodayStockSingleProductdeshwal(
        $row['product_name'],
        $row['location']
    );

    echo "Updated stock for product ".$row['product_name']." location ".$row['location']."\n";
}

echo "Stock recalculation completed\n";