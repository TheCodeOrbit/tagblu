<?php

use yii\helpers\Url;
$ActionName=$ActionList['ActionName'];
$OrderBy=$ActionList['OrderBy'];
$SortOrder=$ActionList['SortOrder'];
$val = explode(",",$operation['opt']);
$permod = $operation['name'];
$module = $ModName;

$sourcemodule=$_REQUEST['sourcemodule'];
$sourceid=$_REQUEST['sourceid'];
$baseUrl = Yii::$app->HomeUrl;

// print_r($getnotes);die;
	
// print_r($ColumnList);die;

// print_r($totalitemcount);//die;
// Array ( [noofpages] => 1 [defaultrecord] => 10 [totrecords] => 1 [nextPageNumber] => 2 [pageEndRange] => 19 [pageStartRange] => 10 [previousPageExists] => FALSE [nextPageExists] => FALSE [pagejumps] => 2 [pageStartRangepagejump] => [pageStartRanges] => 11 [pageEndRanges] => 1 [orderby] => [nextorder] => ) 
?>
<style type="text/css">


</style>
<link rel="stylesheet" href="<?= $baseUrl;?>/thememain/css/relatednotes.css">

<div class="comments-container">
        <h3>Notes</h3>
        <!-- Input Area -->
          <div class="notes-input-area">
            <input type="hidden" id="notesTabid" value="<?= $TabId;?>">
                <input type="hidden" id="notessourceid" value="<?= $sourceid;?>">
                <input type="hidden" id="notessourcemodule" value="<?= $sourcemodule;?>">

            <textarea placeholder="Write your notes here..." class="notes-editor2" id="modnotesval"></textarea>
            <div class="notes-input-footer">
              <input type="file" class="notes-attach-btn" id="attach-notes1">
              <!-- Attach Document</button> -->
              <button class="notes-post-btn post-btn1">Post</button>
            </div>
            <span id="upload-status1"></span>

          </div>
          <div class="notescontentmain">
          <?php
            foreach ($getnotes as $key => $value) {
              # code...
              // print_r($value);die;
              if (!empty($value['filepath'])) {
                $filenamenotes = $value['filename'];
                $filenamepath = $baseUrl . $value['filepath'];
                $fileid = $value['fileid'];
                $p = "<br><a href='" . $baseUrl . $module . "/download?fileid=" . $fileid . "'>" . $filenamenotes . "</a>";
              } else {
                $filenamenotes = '';
                $filenamepath = '';
                $p = '';
              }
              $notedesc = strip_tags($value['notecontent']);
              $notedesc = substr($notedesc, 0, 50);
              $username = $value['notebyuser'];
              substr($username, 0, 1);
            ?>
        <div class="comment">
            <div class="comment-header">
               
                <div class="comment-details">
                
                    <span class="username">  <div class="avatar"><?= $username[0] ?? null; ?></div> <?= $value['notebyuser']; ?></span>
                    <span class="comment-text"><?= $notedesc; ?></span>
                    <span class="file-name"><?= $p; ?></span>
                    <span class="timestamp"><?= $value['notedon'] ?></span>
                </div>
                <button class="reply-btn" onclick="toggleReply(this)">Reply</button>
            </div>
            <div class="reply-section hidden">
                <textarea class="reply-text" placeholder=""></textarea>
                <button class="post-btn">Post</button>
            </div>
        </div>
         <?php
            }
            ?>
        </div>
		    
    </div>

    <script>
        // Toggle the reply text area
        function toggleReply(button) {
            const replySection = button.closest('.comment').querySelector('.reply-section');
            replySection.classList.toggle('hidden');
        }

    </script>

<?php
die;
?>