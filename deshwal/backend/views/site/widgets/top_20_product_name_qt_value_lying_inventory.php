<style>
  .custom-table {
    width: 100%;
    border-collapse: collapse;
    font-family: Arial, sans-serif;
    text-align: center;
  }

  .custom-table th,
  .custom-table td {
    padding: 12px;
    border: 1px solid #ddd;
  }

  .custom-table thead th {
    background-color: #4299e1; /* Blue */
    color: white;
    font-weight: bold;
  }

  .custom-table .group-header th {
    background-color: #4299e1;
    color: white;
    border-right: 1px solid #fff;
  }

  .custom-table tbody td {
    background-color: #f8f8f8;
    font-weight: 500;
  }

  .title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 16px;
  }
  .first-td{
    font-weight: 500;
  }
  </style>
  <table class="custom-table">
    <thead>
      <tr class="group-header">
        <th>Product Name</th>
        <th>0-15 Days</th>
        <th>16-30 Days</th>
        <th>31-60 Days</th>
        <th>&gt;60 Days</th>
      </tr>
    </thead>
    <tbody>
     <?php 
      if ($isAdmin == 1) {
          $command = Yii::$app->db->createCommand("
                    SELECT 
                        rep_inventory_ageing.subcategory,
                        prod_sub_catagory.sub_catagory_value,
                        SUM(rep_inventory_ageing.qty) AS qty,
                        SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 0 AND 15 THEN rep_inventory_ageing.qty ELSE 0 END) AS amt_0_15,
                        SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 16 AND 30 THEN rep_inventory_ageing.qty ELSE 0 END) AS amt_16_30,
                        SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 31 AND 60 THEN rep_inventory_ageing.qty ELSE 0 END) AS amt_31_60,
                        SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) > 60 THEN rep_inventory_ageing.qty ELSE 0 END) AS amt_60_plus
                    FROM rep_inventory_ageing
                    LEFT JOIN prod_sub_catagory 
                        ON prod_sub_catagory.sub_catagory_id = rep_inventory_ageing.subcategory
                    GROUP BY rep_inventory_ageing.subcategory
                    LIMIT 20;
                ");
                $results = $command->queryAll();
      }
      else
      {
          $command = Yii::$app->db->createCommand("
                    SELECT 
                        rep_inventory_ageing.subcategory,
                        prod_sub_catagory.sub_catagory_value,
                        SUM(rep_inventory_ageing.qty) AS qty,
                        SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 0 AND 15 THEN rep_inventory_ageing.qty ELSE 0 END) AS amt_0_15,
                        SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 16 AND 30 THEN rep_inventory_ageing.qty ELSE 0 END) AS amt_16_30,
                        SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 31 AND 60 THEN rep_inventory_ageing.qty ELSE 0 END) AS amt_31_60,
                        SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) > 60 THEN rep_inventory_ageing.qty ELSE 0 END) AS amt_60_plus
                    FROM rep_inventory_ageing
                    WHERE ownerid = :uid
                    LEFT JOIN prod_sub_catagory 
                        ON prod_sub_catagory.sub_catagory_id = rep_inventory_ageing.subcategory
                    GROUP BY rep_inventory_ageing.subcategory
                    LIMIT 20
                ");
                
              $results = $command->bindValue(':uid', $uid)->queryAll();
      }

      ?>
      <?php if (!empty($widgetData)) { ?>
      <?php foreach ($widgetData as $row): ?>
            <tr style="text-align: center;">
                <td><?= htmlspecialchars($row['sub_catagory_value']) ?></td>
                <td><?= $row['amt_0_15'] ?></td>
                <td><?= $row['amt_16_30'] ?></td>
                <td><?= $row['amt_31_60'] ?></td>
                <td><?= $row['amt_60_plus'] ?></td>
            </tr>
        <?php endforeach; ?>
        <?php } else { ?>
           <td colspan="5">No record Found.</td>
         <?php } ?>
      <!-- Add remaining 17 products similarly -->
    </tbody>
  </table>
