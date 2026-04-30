<script>
  $(document).ready(function () {
    $('.read-more-btn').click(function () {
      var contentItem = $(this).closest('.content-item'); // Get the parent content item
      var moreContent = contentItem.find('.more-content');
      var lessContent = contentItem.find('.less-content');
      var dots = contentItem.find('.dots');
      // alert('hi');
      // Debugging: Check if moreContent is correctly selected
      console.log(moreContent.length); // Should log 1 (or more) if the element is found

      if (moreContent.is(':visible')) {
        moreContent.hide();  // Hide the full content
        dots.show();         // Show the ellipsis
        lessContent.show();         // Show the ellipsis
        $(this).text('Read More');  // Change button text
      } else {
        moreContent.fadeIn();  // Use fadeIn to show the content
        dots.hide();           // Hide the ellipsis
        lessContent.hide();           // Hide the ellipsis
        $(this).text('Read Less');  // Change button text
      }
    });
  });
</script>
<?php $baseUrl = Yii::$app->HomeUrl; ?>
<?php
$index = 1;
foreach ($getnotes as $key => $value) {
  # code...
  // print_r($value);die;
  if (!empty($value['filepath'])) {
    $filenamenotes = $value['filename'];
    $filenamepath = $baseUrl . $value['filepath'];
    $fileid = $value['fileid'];
    $p = "<br><a href='" . $baseUrl . $ModuleName . "/download?fileid=" . $fileid . "'>" . $filenamenotes . "</a>";
  } else {
    $filenamenotes = '';
    $filenamepath = '';
    $p = '';
  }
  // $notedesc = strip_tags($value['notecontent']);
  $notedescfull = $value['notecontent'];
  $notedesc = substr($notedescfull, 0, 50);
  ?>
  <div class="notes-content">
    <div class="note-item">
      <span class="ntitem">
        <a href="#">
          <img src="<?= $baseUrl; ?>thememain/img/33a94905-7956-4a9e-bd74-7ffb3b1d2b08.png" class="noteicon" />
        </a>

        <div class="content-item">
          <?php
          // Check if the full note is longer than the truncated version
          if (strlen($notedescfull) > strlen($notedesc)) {
            $notedescshort = strip_tags($notedesc);
            ?>
            <div class="less-content">
              <?= $notedescshort; ?>
              <p class="dots">...</p>
            </div>

            <div class="more-content"><?php echo $notedescfull; ?></div>
            <button class="btn btn-primary read-more-btn">Read More</button>
            <?php
          } else
            echo $notedesc; ?>

        </div>
      </span>
      <?= $p; ?>
    </div>

    <div class="note-meta">
      <span class="author"><?= $value['notebyuser']; ?></span>
      <span class="timestamp"> <?= $value['notedon'] ?></span>
      <!-- <span class="elapsed-time"> | 8 hours ago</span> -->
    </div>

  </div>
  </div>
  <?php
}
?>
<?php
die; ?>