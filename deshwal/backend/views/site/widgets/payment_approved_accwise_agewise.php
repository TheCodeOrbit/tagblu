<div class="scroll-custom-table-container">
  <table class="custom-table">
    <thead>
      <tr>
        <th>Accounts</th>
        <th>0 - 7</th>
        <th>8 - 15</th>
        <th>&gt; 15</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($widgetData)): ?>
      <?php foreach ($widgetData as $row): ?>
            <tr style="text-align: center;">
                <!-- <td><?= htmlspecialchars($row['account']) ?></td> -->
                <td><a href='<?= $baseUrl . $modulename ?>/list?widgetid=<?= urlencode($filterid. "_" .$row["acc_id"] ); ?>'><?= htmlspecialchars($row['account']) ?></td>
                <td><?= $row['0-7'] ?></td>
                <td><?= $row['8-15'] ?></td>
                <td><?= $row['>15'] ?></td>
            </tr>
        <?php endforeach; ?>
        <?php else: ?>
      <tr>
        <td colspan="4">No records found.</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>