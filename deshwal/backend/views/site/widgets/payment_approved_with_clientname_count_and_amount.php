<div class="scroll-custom-table-container">
<table class="custom-table">
  <thead>
    <tr class="group-header">
      <th rowspan="2">Client Name</th>
      <th colspan="2">0–10 Days</th>
      <th colspan="2">11–15 Days</th>
      <th colspan="2">&gt;15 Days</th>
    </tr>
    <tr>
      <th>Count</th>
      <th>Amount</th>
      <th>Count</th>
      <th>Amount</th>
      <th>Count</th>
      <th>Amount</th>
    </tr>
  </thead>
  <tbody>
 <?php if (!empty($widgetData)): ?>
      <?php foreach ($widgetData as $r): ?>
        <tr>
          <!-- <td><?= htmlspecialchars($r['account_name']) ?></td> -->
           <td><a href='<?= $baseUrl . $modulename ?>/list?widgetid=<?= urlencode($filterid. "_" .$r["acc_id"] ); ?>'><?= htmlspecialchars($r['account_name']) ?></a></td>
          <td><?= $r['day_0_10_count'] ?></td>
          <td><?= $r['day_0_10_amount'] ?></td>
          <td><?= $r['day_11_15_count'] ?></td>
          <td><?= $r['day_11_15_amount'] ?></td>
          <td><?= $r['day_15_plus_count'] ?></td>
          <td><?= $r['day_15_plus_amount'] ?></td>
          <!-- <td><?= $r['total_count'] ?></td>
          <td><?= $r['total_amount'] ?></td> -->
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="9">No records found.</td>
      </tr>
      <?php endif; ?>
  </tbody>
</table>
</div>
