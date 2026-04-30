<h3>CSV Validation Result</h3>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong>Found errors. Fix CSV and re-upload.</strong>
    </div>

    <table class="table table-bordered">
        <tr>
            <th>Row No</th>
            <th>Errors</th>
        </tr>

        <?php foreach ($errors as $rowIndex => $errList): ?>
            <tr>
                <td><?= $rowIndex + 2 ?></td>
                <td><?= implode("<br>", $errList) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

<?php else: ?>
    <div class="alert alert-success">
        No errors found! You can proceed to UPDATE inventory records.
    </div>

    <form method="post">
        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <button class="btn btn-primary">Confirm Update</button>
    </form>

<?php endif; ?>
