<!-- relatedmodulesidebar-card -->
<!-- <div class="relatedmodulesidebar-scroll">
    <div class="relatedmodulesidebar-card">
        <p class="rel-rec">
            <a href="<?= $baseUrl; ?><?= $value['modulename'] ?>/detail?Record=<?= $pkey; ?>&sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>">
                <?= $showfieldval; ?>
            </a>
        </p>
        <div class="relatedmodulesidebar-three-col">
            <?php if (!empty($collbl1)) { ?>
                <div class="relatedmodulesidebar-row">
                    <div class="relatedmodulesidebar-label"><?= $collbl1; ?></div>
                    <div class="relatedmodulesidebar-value"><?= $col1; ?></div>
                </div>
            <?php } ?>

            <?php if (!empty($collbl2)) { ?>
                <div class="relatedmodulesidebar-row middle-row">
                    <div class="relatedmodulesidebar-label"><?= $collbl2; ?></div>
                    <div class="relatedmodulesidebar-value"><?= $col2; ?></div>
                </div>
            <?php } ?>

            <?php if (!empty($collbl3)) { ?>
                <div class="relatedmodulesidebar-row">
                    <div class="relatedmodulesidebar-label"><?= $collbl3; ?></div>
                    <div class="relatedmodulesidebar-value"><?= $col3; ?></div>
                </div>
            <?php } ?>
        </div>
        <div class="relatedmodulesidebar-two-col creator-block">
            <div>
                <div class="creator-block-lbl">Created By</div>
                <div class=""><?= $record_creatorid; ?></div>
            </div>
            <div class="text-end">
                <div class="creator-block-lbl">Created At</div>
                <div class=""><?= $record_createdat; ?></div>
            </div>
        </div>
    </div>
</div> -->
<!-- relatedmodulesidebar-card -->
<!-- FLEX fallback (Bootstrap utilities) -->
<div class="relatedmodulesidebar-scroll">
  <div class="relatedmodulesidebar-card">

    <p class="rel-rec mb-2">
      <a href="<?= $baseUrl; ?><?= $value['modulename'] ?>/detail?Record=<?= $pkey; ?>&sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>">
        <?= $showfieldval; ?>
      </a>
    </p>

    <!-- flex container: column on xs, row from md upwards -->
    <div class="relatedmodulesidebar-three-col d-flex flex-column flex-md-row gap-2">
      <?php if (!empty($collbl1)) { ?>
        <div class="relatedmodulesidebar-row flex-fill">
          <div class="relatedmodulesidebar-label"><?= $collbl1; ?></div>
          <div class="relatedmodulesidebar-value"><?= $col1; ?></div>
        </div>
      <?php } ?>

      <?php if (!empty($collbl2)) { ?>
        <div class="relatedmodulesidebar-row middle-row flex-fill">
          <div class="relatedmodulesidebar-label"><?= $collbl2; ?></div>
          <div class="relatedmodulesidebar-value"><?= $col2; ?></div>
        </div>
      <?php } ?>

      <?php if (!empty($collbl3)) { ?>
        <div class="relatedmodulesidebar-row flex-fill">
          <div class="relatedmodulesidebar-label"><?= $collbl3; ?></div>
          <div class="relatedmodulesidebar-value"><?= $col3; ?></div>
        </div>
      <?php } ?>
    </div>

    <div class="creator-block d-flex flex-column flex-md-row justify-content-between align-items-start mt-2">
      <div class="mb-2 mb-md-0">
        <div class="creator-block-lbl">Created By</div>
        <div><?= $record_creatorid; ?></div>
      </div>

      <div class="text-md-end">
        <div class="creator-block-lbl">Created At</div>
        <div><?= $record_createdat; ?></div>
      </div>
    </div>

  </div>
</div>

