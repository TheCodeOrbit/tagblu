<?php

// $sourcemodule=$_REQUEST['sourcemodule'];
// $sourceid=$_REQUEST['sourceid'];
$baseUrl = Yii::$app->HomeUrl;

// print_r($ColumnList);die;

// print_r($totalitemcount);//die;
// Array ( [noofpages] => 1 [defaultrecord] => 10 [totrecords] => 1 [nextPageNumber] => 2 [pageEndRange] => 19 [pageStartRange] => 10 [previousPageExists] => FALSE [nextPageExists] => FALSE [pagejumps] => 2 [pageStartRangepagejump] => [pageStartRanges] => 11 [pageEndRanges] => 1 [orderby] => [nextorder] => ) 
?>

<link rel="stylesheet" href="<?= $baseUrl; ?>/thememain/css/relatedlistdoc.css">

<div class="comments-container">
    <h5>Documents</h5>


    <div class="table-wrapper">

        <table id="data-table" class="doctd">
            <thead>
                <tr>
                    <th width="3%">SNo.</th>
                    <th>Doc No</th>
                    <th>Title</th>
                    <th>Note</th>
                    <th>File</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                if(count($docrecords) > 0)
                {
                foreach ($docrecords as $key => $value) {
                    $records = \app\models\Attachments::find()
                                  ->where(['attachmentsid' => $value['filename']])
                                  ->one();
                    ?>
                    <tr>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= $value['doc_no']; ?></td>
                        <td><?= $value['title']; ?></td>
                        <td><?= $value['notecontent']; ?></td>
                        <!-- <td>< $records->name; ?> <a href='<$baseUrl .$ModuleName. "/download?fileid=" . $value['filename'] ?>'><i class="fa fa-download" aria-hidden="true"></i> -->
                        <td><?= $records->name; ?> <a href='<?= $baseUrl . "documents/download?fileid=" . $value['filename'] ?>'><i class="fa fa-download" aria-hidden="true"></i>
                        </a></td>
                    </tr>
                    </tr>
                    <?php
                }
            }
            else echo "<tr><td colspan='5'>No Record found</td></tr>";
                ?>
            </tbody>
        </table>


    </div>

</div>

<?php
// die;
?>