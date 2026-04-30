<div class="table-container">
  <div class="table-scroll">
    <table class="custom-table">
      <thead>
        <tr>
          <th class="cell-text-wrap">Sourcing Deal Stages</th>
          <th>0–3 Days</th>
          <th>4–7 Days</th>
          <th>7–15 Days</th>
          <th>15–30 Days</th>
          <th>31–60 Days</th>
          <th>61–90 Days</th>
          <th>90 Days</th>
        </tr>
      </thead>
    </table>
    <?php
    if ($isAdmin == 1) {
      $sql = "SELECT
            stage_id,
                SUM(CASE WHEN no_of_days BETWEEN 0 AND 3 THEN 1 ELSE 0 END) AS '0-3 Days',
                SUM(CASE WHEN no_of_days BETWEEN 4 AND 7 THEN 1 ELSE 0 END) AS '4-7 Days',
                SUM(CASE WHEN no_of_days BETWEEN 8 AND 15 THEN 1 ELSE 0 END) AS '7-15 Days',
                SUM(CASE WHEN no_of_days BETWEEN 16 AND 30 THEN 1 ELSE 0 END) AS '15-30 Days',
                SUM(CASE WHEN no_of_days BETWEEN 31 AND 60 THEN 1 ELSE 0 END) AS '31-60 Days',
                SUM(CASE WHEN no_of_days BETWEEN 61 AND 90 THEN 1 ELSE 0 END) AS '61-90 Days',
                SUM(CASE WHEN no_of_days > 90 THEN 1 ELSE 0 END) AS '90+ Days'
            FROM rep_soucingdeal_stage_log
            GROUP BY stage_id
            ORDER BY stage_id";
      $resultArray = Yii::$app->db->createCommand($sql)->queryAll();
    } else {
      
      $uid = Yii::$app->user->id;
      $sql = "SELECT
            stage_id,
                SUM(CASE WHEN no_of_days BETWEEN 0 AND 3 THEN 1 ELSE 0 END) AS '0-3 Days',
                SUM(CASE WHEN no_of_days BETWEEN 4 AND 7 THEN 1 ELSE 0 END) AS '4-7 Days',
                SUM(CASE WHEN no_of_days BETWEEN 8 AND 15 THEN 1 ELSE 0 END) AS '7-15 Days',
                SUM(CASE WHEN no_of_days BETWEEN 16 AND 30 THEN 1 ELSE 0 END) AS '15-30 Days',
                SUM(CASE WHEN no_of_days BETWEEN 31 AND 60 THEN 1 ELSE 0 END) AS '31-60 Days',
                SUM(CASE WHEN no_of_days BETWEEN 61 AND 90 THEN 1 ELSE 0 END) AS '61-90 Days',
                SUM(CASE WHEN no_of_days > 90 THEN 1 ELSE 0 END) AS '90+ Days'
            FROM rep_soucingdeal_stage_log
            WHERE creatorid = :uid
            GROUP BY stage_id 
            ORDER BY stage_id";
      $resultArray = Yii::$app->db->createCommand($sql)
        ->bindValue(':uid', $uid)
        ->queryAll();
    }
    $stageMapSql = "SELECT stage_id, stage_value FROM sourcingdeal_stage";
    $stageMapResult = Yii::$app->db->createCommand($stageMapSql)->queryAll();

    // Convert to [id => name] format
    $stageMap = [];
    foreach ($stageMapResult as $row) {
      $stageMap[$row['stage_id']] = $row['stage_value'];
    }

    ?>
    <table class="custom-table">
      <tbody>
        <?php
        if (!empty($resultArray)) {
          foreach ($resultArray as $row): ?>
            <tr>
              <td><a href='<?= $baseUrl . $modulename ?>/list?widgetid=<?= $filterid; ?>_<?=  $row['stage_id']; ?>'><?= $stageMap[$row['stage_id']] ?? 'Unknown' ?></a></td>
              <td><?= $row['0-3 Days'] ?></td>
              <td><?= $row['4-7 Days'] ?></td>
              <td><?= $row['7-15 Days'] ?></td>
              <td><?= $row['15-30 Days'] ?></td>
              <td><?= $row['31-60 Days'] ?></td>
              <td><?= $row['61-90 Days'] ?></td>
              <td><?= $row['90+ Days'] ?></td>
            </tr>
        <?php endforeach;
        } else {
          echo '<td colspan="8">No record Found.</td>';
        } ?>
      </tbody>
    </table>

  </div>
</div>