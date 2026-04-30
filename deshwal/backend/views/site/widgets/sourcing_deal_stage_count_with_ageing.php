 <!-- <div class="grid-stack-item-content scroll-body"> -->
 <div class="scroll-custom-table-container">
   <!-- <div class="table-scroll"> -->
     <table class="custom-table">
       <thead>
         <tr>
           <th class="cell-text-wrap">Sourcing Deal Stages</th>
           <th>0–3 Days</th>
           <th>4–7 Days</th>
           <th>8–15 Days</th>
           <th>16–30 Days</th>
           <th>31–60 Days</th>
           <th>61–90 Days</th>
           <th>90+ Days</th>
         </tr>
       </thead>
     <!-- </table> -->

     <?php
      // AGEING BUCKET QUERY
      // if ($isAdmin == 1) {

      //     $sql = "SELECT
      //           r.stage_id,st.stage_value,
      //           SUM(CASE WHEN DATEDIFF(CURDATE(), DATE(r.createdtime)) BETWEEN 0 AND 3 THEN 1 ELSE 0 END) AS `0-3 Days`,
      //           SUM(CASE WHEN DATEDIFF(CURDATE(), DATE(r.createdtime)) BETWEEN 4 AND 7 THEN 1 ELSE 0 END) AS `4-7 Days`,
      //           SUM(CASE WHEN DATEDIFF(CURDATE(), DATE(r.createdtime)) BETWEEN 8 AND 15 THEN 1 ELSE 0 END) AS `8-15 Days`,
      //           SUM(CASE WHEN DATEDIFF(CURDATE(), DATE(r.createdtime)) BETWEEN 16 AND 30 THEN 1 ELSE 0 END) AS `16-30 Days`,
      //           SUM(CASE WHEN DATEDIFF(CURDATE(), DATE(r.createdtime)) BETWEEN 31 AND 60 THEN 1 ELSE 0 END) AS `31-60 Days`,
      //           SUM(CASE WHEN DATEDIFF(CURDATE(), DATE(r.createdtime)) BETWEEN 61 AND 90 THEN 1 ELSE 0 END) AS `61-90 Days`,
      //           SUM(CASE WHEN DATEDIFF(CURDATE(), DATE(r.createdtime)) > 90 THEN 1 ELSE 0 END) AS `90+ Days`
      //       FROM rep_soucingdeal_stage_log r
      //       INNER JOIN sourcingdeal sd ON sd.sourcingdeal_id = r.sourcingdeal_id
      //       LEFT JOIN sourcingdeal_stage st ON st.stage_id = r.stage_id
      //       WHERE r.updatetime IS NULL
      //         AND sd.deleted = 0
      //         AND sd.is_temp = 0
      //       GROUP BY r.stage_id
      //       ORDER BY r.stage_id";
      //   $resultArray = Yii::$app->db->createCommand($sql)->queryAll();
      // } else {

      //   $uid = Yii::$app->user->id;

      //   $sql = "
      //   SELECT
      //       r.stage_id,st.stage_value,
      //       SUM(CASE WHEN DATEDIFF(CURDATE(), DATE(r.createdtime)) BETWEEN 0 AND 3 THEN 1 ELSE 0 END) AS `0-3 Days`,
      //       SUM(CASE WHEN DATEDIFF(CURDATE(), DATE(r.createdtime)) BETWEEN 4 AND 7 THEN 1 ELSE 0 END) AS `4-7 Days`,
      //       SUM(CASE WHEN DATEDIFF(CURDATE(), DATE(r.createdtime)) BETWEEN 8 AND 15 THEN 1 ELSE 0 END) AS `8-15 Days`,
      //       SUM(CASE WHEN DATEDIFF(CURDATE(), DATE(r.createdtime)) BETWEEN 16 AND 30 THEN 1 ELSE 0 END) AS `16-30 Days`,
      //       SUM(CASE WHEN DATEDIFF(CURDATE(), DATE(r.createdtime)) BETWEEN 31 AND 60 THEN 1 ELSE 0 END) AS `31-60 Days`,
      //       SUM(CASE WHEN DATEDIFF(CURDATE(), DATE(r.createdtime)) BETWEEN 61 AND 90 THEN 1 ELSE 0 END) AS `61-90 Days`,
      //       SUM(CASE WHEN DATEDIFF(CURDATE(), DATE(r.createdtime)) > 90 THEN 1 ELSE 0 END) AS `90+ Days`
      //   FROM rep_soucingdeal_stage_log r
      //   INNER JOIN sourcingdeal sd ON sd.sourcingdeal_id = r.sourcingdeal_id
      //   LEFT JOIN sourcingdeal_stage st ON st.stage_id = r.stage_id
      //   WHERE r.updatetime IS NULL
      //     AND sd.deleted = 0
      //     AND sd.is_temp = 0
      //     AND r.creatorid =:uid
      //   GROUP BY r.stage_id
      //   ORDER BY r.stage_id";

      //   $resultArray = Yii::$app->db->createCommand($sql)
      //     ->bindValue(':uid', $uid)
      //     ->queryAll();
      // }
      ?>

     <!-- <table class="custom-table"> -->
       <tbody>
         <?php if (!empty($widgetData)) {
            foreach ($widgetData as $row): ?>
             <tr>
               <!-- <td><a href='<?= $baseUrl . $modulename ?>/list?widgetid=<?= $filterid ?>_<?= $row["stage_id"] ?>'><?= $row["stage_value"] ?></a></td> -->
              <td><a href='<?= $baseUrl . $modulename ?>/list?widgetid=<?= urlencode($filterid . "_" . $row["stage_id"]) ?>'><?= $row["stage_value"] ?></a></td>
               <td><?= $row["0-3 Days"] ?></td>
               <td><?= $row["4-7 Days"] ?></td>
               <td><?= $row["8-15 Days"] ?></td>
               <td><?= $row["16-30 Days"] ?></td>
               <td><?= $row["31-60 Days"] ?></td>
               <td><?= $row["61-90 Days"] ?></td>
               <td><?= $row["90+ Days"] ?></td>
             </tr>
           <?php endforeach;
          } else { ?>
           <td colspan="8">No record Found.</td>
         <?php } ?>
       </tbody>
     </table>

   <!-- </div> -->
 </div>
 <!-- </div> -->