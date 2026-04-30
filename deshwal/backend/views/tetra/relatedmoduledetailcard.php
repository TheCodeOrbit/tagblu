
<div class="relatedmodulesidebar-scroll">
  <div class="relatedmodulesidebar-card">

    <p class="rel-rec mb-2">
      <a href="<?= $baseUrl; ?><?= $value['modulename'] ?>/detail?Record=<?= $pkey; ?>&sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>">
        <?= $showfieldval; ?>
      </a>
    </p>

    <?php if (!empty($dynamicLabels) && !empty($dynamicValues)) { ?>
      <div class="relatedmodulesidebar-three-col d-flex flex-column flex-md-row gap-2">

        <?php foreach ($dynamicLabels as $colName => $label) { ?>
          <div class="relatedmodulesidebar-row flex-fill">
            <div class="relatedmodulesidebar-label"><?= $label; ?></div>
            <div class="relatedmodulesidebar-value">
              <?= $dynamicValues[$colName] ?? '-'; ?>
            </div>
          </div>
        <?php } ?>

      </div>
    <?php } ?>
  </div>
</div>
<div class="creator-block d-flex flex-column flex-md-row justify-content-between align-items-start pt-3 pb-3">
      
      <div class="mb-2 mb-md-0">
        <div class="creator-block-lbl">Created By</div>
        <div><?= $record_creatorid; ?></div>
      </div>

      <div class="text-md-end">
        <div class="creator-block-lbl">Created At</div>
        <div><?= $record_createdat; ?></div>
      </div>

  </div>  