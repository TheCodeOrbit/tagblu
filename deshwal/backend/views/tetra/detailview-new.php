<?php

use backend\components\SvgRenderHelper;
$anyMultipleBlockInModule = null;
foreach ($ColumnList->blocks as $BlockKey => $Block) {
    $anyMultipleBlockInModule = $Block->blocktype ?? null;
    if (isset($anyMultipleBlockInModule) && $anyMultipleBlockInModule == "Multiple") {
        break;
    }
}

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;
use app\models\ListHire;
use app\models\Reference;
use yii\db\Query;

AdminAsset::register($this);

$baseUrl = Yii::$app->HomeUrl;
$scriptPath = $baseUrl . "js/$ModuleName/edit.js";

$this->title = Yii::t('app', $TabLabel . " Detail");
if($ModuleName === 'datawiping'){
    $this->registerJsFile(Url::to($baseUrl . "thememain/js/tetra/papaparse.min.js"), ['depends' => [yii\web\JqueryAsset::class]]);
}
$url = Url::to(['Edit']);
$urlApprove = Url::to(['approvelead']);
$this->registerCssFile('@web/thememain/css/listview.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/detail-view.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/quill.css', ['depends' => [AdminAsset::class]]);
if ($TabId == 53 || $TabId == 58)
    $this->registerJsFile('@web/thememain/js/contactrole.js', ['depends' => [AdminAsset::class]]);

$this->registerJsFile(Url::to($scriptPath), ['depends' => [yii\web\JqueryAsset::class]]);

$baseUrl = Yii::$app->HomeUrl;
$module = strtolower($ModuleName);
$Recordid = $Recordid;
//start of  custom logic for iqc modules for display
if (strpos($module, "iqc") === 0)
    $anyMultipleBlockInModule = true;
// end of custom logic for iqc modules for display
// echo "<pre>";
// print_r($Record);die;
$fullname = $headerfullname;
$csrfTokenName = Yii::$app->request->csrfParam;  // This replaces csrfTokenName
$csrfToken = Yii::$app->request->csrfToken;      // Get the CSRF token itself

function convertToUcfirstOrPascalCase($string)
{
    // Check if the string contains underscores
    if (strpos($string, '_') !== false) {
        // Convert to PascalCase by splitting, capitalizing each part, and joining
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    } else {
        // Capitalize the first letter of the string
        return ucfirst($string);
    }
}

// $role = Yii::$app->user->identity->role ?? null;
$role = Yii::$app->session->get('active_profile_id') ?? null;

//get module type
$moduledet = \app\models\Tab::find()
    ->where(['tabid' => $TabId])
    ->one();
$module_type = $moduledet->module_type;

// added on 23/12/2024 by deepika
$centerModules = '';
$rightModules = '';
if (!empty($relatemodules)) {
    $targetModules = [20, 21, 22];
    //check if call,task ,meeting is related
    $centerModules = array_filter($relatemodules, function ($item) use ($targetModules) {
        return in_array($item['related_module'], $targetModules);
    });
    $targetModules = [23];
    //check if document is related
    $rightModules = array_filter($relatemodules, function ($item) use ($targetModules) {
        return in_array($item['related_module'], $targetModules);
    });
}
?>

<input type="hidden" value="<?php echo $ModuleName; ?>" id="module" name="module" />
<input type="hidden" value="<?php echo $Recordid; ?>" id="recordid" />
<table id="previewTablecsvUploadSample" style="display:none;"><tbody></tbody></table>
<?php
// Retrieve the 'sourcemodule' parameter from the URL

$sourceModule = Yii::$app->request->get('sourcemodule');
$sourceId = Yii::$app->request->get('sourceid');

if (isset($sourceModule)) {


    $query = new Query();
    $tabData = $query->select('name,tablabel')
        ->from('tab')
        ->where(['tabid' => $sourceModule])
        ->one();
}


// $tableName = 'leadinformation';

// // Get the table schema
// $tableSchema = Yii::$app->db->schema->getTableSchema($tableName);

// // Check if the table exists and has a primary key
// if ($tableSchema !== null && !empty($tableSchema->primaryKey)) {
//     echo "Primary key column(s) for table '$tableName': " . implode(', ', $tableSchema->primaryKey);
//     exit;
// } else {
//     echo "The table '$tableName' does not exist or does not have a primary key.";
//     exit;
// }

?>

<div class="row detail-row">
    <div class="col-8 detail-left">
        <div class="page-content">
            <div class="records table-responsive records-div-rs">
                <div class="record-header te">
                   <div class="add" style="display:flex; align-items:center; gap:10px;">

    <div class="icons-coll" style="width:50px; height:50px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <?= SvgRenderHelper::renderIcon($ModuleName . '.svg', true); ?>
    </div>

    <div style="display:flex; flex-direction:column; align-items:flex-start; justify-content:center; text-align:left;">
        <span style="display:block; width:100%; line-height:1.2; margin:0;">
            <strong><?= $TabLabel; ?></strong>
        </span>
        <span style="display:block; width:100%; line-height:1.2; margin:0;">
            <?= $fullname; ?>
        </span>
    </div>

</div>

                    <div class="divbtngrp"><!-- here class name added by ptpatel to resolve pickup buttons issue on date 14-08-25-->
                        <?php
                        // echo $editpermission;die;
                        //if($editpermission == 1){
                        // echo $TabId;
                            if ($TabId != 58 && $TabId != 53) { //hide edit button in sourcing deal contact role and opportunity contact role
                                include("detaileditbutton.php");
                            } else {
                                include("contactroleeditbutton.php");
                            } 
                        //}?>
                    </div>
                </div>
                 <!-- show source module name -->
                        <?php if (isset($tabData['name'])) { ?>
                            <div class="reletedname" style="margin-left:12px">
                                Related To: <a
                                    href="<?= $baseUrl; ?><?= isset($tabData['name']) ? $tabData['name'] : ''; ?>/detail?Record=<?= $sourceId; ?>">

                                    <span
                                        class="sourcemodule"><?= isset($srcheaderfullname) ? $srcheaderfullname : ''; ?></span>
                                </a>
                            </div>
                            <?php
                        } ?>

            </div>
        </div>
        <?php if($TabId== 2 && isset($AddLaptopDetail) && !empty($AddLaptopDetail) && $Record->inspection_started == 1 && $Record->inspection_completed ==0): ?>

        <div class="select-1">
            <div class="container-d">
                <?php
                  include("detailextrabuttons.php");
                
                ?>
                        </div>
        </div>
        <?php
        endif;?>

        <div class="select-1">
            <?php
            if (isset($pipelinetatuses)) //start pipeline
            { ?>
                <div class="container-d">
                    <div class="col-md-12">
                        <div class="pipeline-container">
                            <div class="rectangle-54">
                                <div class="pip-1">
                                    <h2 class="lead-pipeline-status"><?= $TabLabel; ?> Pipeline Stages</h2>
                                    <div class="flex-row-e ovx">
                                        <ul class="btn-pipeline">
                                            <!-- get lead stages -->
                                            <?php
                                            $cn = 1;
                                            // print_r($pipelinetatuses);
                                            foreach ($pipelinetatuses as $key => $value) {
                                                # code...
                                                if ($Record[$pipelinecolumn] == $value[$pipelinestatusid])
                                                    $finclass = "completed-stage";
                                                else
                                                    $finclass = "explored-stage";

                                                $bt = "mid";

                                                $clvv = 'd-pipe-view';
                                                // else $clvv = '';
                                        
                                                $cn++;
                                                ?>
                                                <!--  <div class="col-sm-2">
                        <div class="rectangle-start-gray">
                           <span class="not-contacted">New </span>
                        </div>
                         </div> -->
                                                <!-- <div class="col-sm-4 <= $clvv; ?>">
                        <button class="<= $finclass; ?> leaddurationparent <= $value[$pipelinestatusvalue]; ?>"
                          data-id="<= $value[$pipelinestatusid]; ?>" data-bt="<= $bt; ?>" data-cl="<= $finclass; ?>"
                          style="border: none;">
                          <span class="not-contacted"><= $value[$pipelinestatusvalue]; ?></span>
                        </button>
                      </div> -->
                                                <li class="<?= $finclass; ?>  stage text-capitalize leaddurationparent <?= $value[$pipelinestatusvalue]; ?>"
                                                    data-id="<?= $value[$pipelinestatusid]; ?>" data-bt="<?= $bt; ?>"
                                                    data-cl="<?= $finclass; ?>">
                                                    <a href="#"><?= $value[$pipelinestatusvalue]; ?></a>
                                                </li>
                                                <?php
                                            }
                                            ?>
                                        </ul>

                                    </div>

                                    <div class="vector-5e">
                                        <div class="flex-row-f">
                                            <span class="stage-name">Stage Name</span><span class="entered-at">Entered
                                                At</span><span class="duration">Duration</span>
                                        </div>
                                        <?php
                                        $cn = 1;
                                        foreach ($pipelinetatuses as $key => $value) {
                                            if ($Record[$pipelinecolumn] == $value[$pipelinestatusid])
                                                $stclass = "";
                                            else
                                                $stclass = "tr-hidden";
                                            //lead history
                                            //get prevalue
                                            // echo  "
                                            //             SELECT changedon 
                                            //             FROM modtracker_basic 
                                            //             JOIN modtracker_detail ON modtracker_basic.id = modtracker_detail.id 
                                            //             WHERE fieldname = $pipelinecolumn 
                                            //               AND prevalue = '".$value[$pipelinestatusid]."' 
                                            //               AND module = '".$module."' 
                                            //               AND crmid = $Recordid 
                                            //             ORDER BY changedon DESC
                                            //         ";
                                            //         echo "<br>";
                                    
                                            $sql1 = "
                            SELECT changedon 
                            FROM modtracker_basic 
                            JOIN modtracker_detail ON modtracker_basic.id = modtracker_detail.id 
                            WHERE fieldname = :fieldname1 
                              AND prevalue = :prevalue 
                              AND module = :module 
                              AND crmid = :recordid 
                            ORDER BY changedon DESC
                        ";

                                            $prevaluear = Yii::$app->db->createCommand($sql1)
                                                ->bindValue(':fieldname1', $pipelinecolumn) // Bind the fieldname parameter
                                                ->bindValue(':prevalue', $value[$pipelinestatusid]) // Bind the prevalue parameter
                                                ->bindValue(':module', $module) // Bind the module parameter
                                                ->bindValue(':recordid', $Recordid) // Bind the crmid parameter
                                                ->queryOne();
                                            // echo $value[$pipelinestatusid]."<br>";
                                            // print_r($prevaluear);
                                    
                                            //get postvalue 
                                    
                                            $postvaluearr = Yii::$app->db->createCommand("select changedon from modtracker_basic 
                          join modtracker_detail on modtracker_basic.id = modtracker_detail.id where fieldname=:pipelinecolumn and postvalue=:postvalue  and module=:module and crmid=:Recordid order by changedon desc ")
                                                ->bindValue(":module", $module)
                                                ->bindValue(":pipelinecolumn", $pipelinecolumn)
                                                ->bindValue(":Recordid", $Recordid)
                                                ->bindValue(":postvalue", $value[$pipelinestatusid])
                                                ->queryOne();
                                            if ($stclass == "") {
                                                //cureent stage
                                                $today = date("Y-m-d H:i:s");
                                                if ($postvaluearr)
                                                    $enterdate = $postvaluearr['changedon'];
                                                else
                                                    $enterdate = '';

                                                $date1 = new DateTime($enterdate); // Replace with your first date
                                                $date2 = new DateTime($today); // Replace with your second date
                                    
                                                // Get the difference as a DateInterval object
                                                $interval = $date1->diff($date2);

                                                // Format the difference
                                                $days = $interval->days; // Total days
                                                $hours = $interval->h; // Remaining hours
                                                $minutes = $interval->i; // Remaining minutes
                                    
                                                // Output in desired format
                                                $duration = "$days Days | $hours hours $minutes min";
                                                // Convert the string into a timestamp
                                                $timestamp = strtotime($enterdate);

                                                // Format the date
                                                $enteredat = date('M d, Y \a\t g.i a', $timestamp); // Format: Oct 22, 2024 at 9.16 am
                                    

                                            } else if (!empty($prevaluear)) {
                                                $today = $prevaluear['changedon'];
                                                // print_r($postvaluearr);die;
                                                if (isset($postvaluearr['changedon']))
                                                    $enterdate = $postvaluearr['changedon'];
                                                else
                                                    $enterdate = '';
                                                $date1 = new DateTime($enterdate); // Replace with your first date
                                                $date2 = new DateTime($today); // Replace with your second date
                                    
                                                // Get the difference as a DateInterval object
                                                $interval = $date1->diff($date2);

                                                // Format the difference
                                                $days = $interval->days; // Total days
                                                $hours = $interval->h; // Remaining hours
                                                $minutes = $interval->i; // Remaining minutes
                                    
                                                // Output in desired format
                                                $duration = "$days Days | $hours hours $minutes min";
                                                // Convert the string into a timestamp
                                                $timestamp = strtotime($enterdate);

                                                // Format the date
                                                $enteredat = date('M d, Y \a\t g.i a', $timestamp); // Format: Oct 22, 2024 at 9.16 am
                                    


                                            } else {
                                                $duration = '';
                                                $enteredat = '';
                                            }

                                            ?>
                                            <div
                                                class="flex-row-f-5f leaddurationbox leadduration<?= $value[$pipelinestatusid] . " " . $stclass; ?> ">
                                                <div class="col-md-4">
                                                    <span class="new-60 lead-satge-name"><?= $value[$pipelinestatusvalue]; ?>
                                                    </span>
                                                </div>
                                                <div class="col-md-4">
                                                    <span class="oct lead-enered-at"><?= $enteredat; ?> </span>
                                                </div>
                                                <div class="col-md-4">
                                                    <span class="text-48 lead-enter-duration"><?= $duration; ?></span>
                                                </div>
                                            </div>
                                            <?php
                                        } ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if (isset($value['description'])) { ?>
                    <div class="container-d">
                        <?php
                        foreach ($pipelinetatuses as $key => $value) {
                            if ($Record[$pipelinecolumn] == $value[$pipelinestatusid])
                                $stclass = "";
                            else
                                $stclass = "tr-hidden";
                            ?>
                            <div class="rectangle-61 leaddescbox leaddesc<?= $value[$pipelinestatusid] ?> <?= $stclass; ?>">
                                <h2 class="description">Description</h2>
                                <p><?= isset($value['description']) ? $value['description'] : ''; ?>
                                </p>
                            </div>
                            <?php
                        } ?>
                    </div>
                    <?php
                } //end description
            } //end pipeline
            ?>

            <div class="container-d">
                <div class="col-md-12">
                    <!-- Main Content -->
                    <div class="container-m">
                        <!-- Left Section -->
                        <div class="left-section">
                            <div class="col-md-12">

                                <div class="flex-row-ee">

                                    <nav class="nav__container">
                                        <div class="nav__logo">
                                            <button class="tab active" data-tab="summary"
                                                fdprocessedid="p3ub0u">Summary</button>
                                            <button class="tab" data-tab="history"
                                                fdprocessedid="eolv9l">History</button>
                                        </div>




                                        <div>
                                        </div>
                                    </nav>

                                </div>
                            </div>
                            <?php if (isset($anyMultipleBlockInModule) && $anyMultipleBlockInModule == "Multiple") { ?>


                                <?php include("detailsummarymultiple.php"); ?>



                            <?php } else { ?>

                                <?php include("detailsummary.php"); ?>



                            <?php } ?>
                            <?php include("detailhistory.php"); ?>
                            <!-- History Section end-->
                        </div>
                        <?php
                        if ($module_type != "master") {
                            if (!empty($centerModules)) { ?>
                                <!-- Center Section -->
                                <?php //include("detailcenter.php"); ?>

                                <?php
                            }
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-4  mt-3 detail-right">



        <!-- Right Section -->
        <!-- Right Panel -->
        <div class="right-panel">
            <div class="collapse-container">
                <!-- Notes Section -->
                <label class="collapse-header" for="toggle-notes">
                    <span class="icons-coll">
                        <?= SvgRenderHelper::renderIcon('note' . '.svg', true); ?>
</span> Notes
                    
                    <i class="fa-solid fa-angle-down icon-right"></i>
                </label>
                <input type="checkbox" id="toggle-notes" onchange="toggleCollapseIcon(this)">
                <div class="collapse-content">
                    <!-- quill notes bhavitha -->
                    <!-- <h2>Quill.js with @Mentions</h2> -->
                    <!-- <div id="editor-container"></div>
                    <div id="mention-list" class="mention-list"></div> -->
                    <!-- end quill notes bhavitha -->
                    <div class="notes">
                        <!-- <div class="notes-1">Notes</div> -->


                        <div class="notes-container">
                            <!-- Notes Header -->


                            <!-- Input Area -->
                            <div class="notes-input-area">

                                <!-- <textarea placeholder="Write your notes here..." class="notes-editor"          id="modnotes"></textarea> -->
                                <div id="editor-container"></div>
                                <div id="mention-list" class="mention-list"></div>
                                <div class="notes-input-footer">
                                    <input type="file" class="notes-attach-btn" id="attach-notes" title="Attach File">
                                    <!-- Attach Document</button> -->
                                    <button class="notes-post-btn post-btn" title="Post Notes">Post</button>
                                </div>
                                <span id="upload-status"></span>

                            </div>




                            <!-- Notes Content -->
                            <div class="notes-main-section">
                                <?php
                                // prin_r($getnotes);
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
                                    $notedesc = $value['notecontent'];
                                    $notedescfull = $value['notecontent'];
                                    $notedesc = substr($notedescfull, 0, 50);
                                    ?>
                                    <div class="notes-content">
                                        <div class="note-item">
                                            <span class="ntitem">
                                                <a href="#">
                                                    <img src="<?= $baseUrl; ?>thememain/img/33a94905-7956-4a9e-bd74-7ffb3b1d2b08.png"
                                                        class="noteicon" />
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

                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relatedmodules">
                <?php
                include("relatedmoduels.php");
                ?>
            </div>




        </div>


    </div>

    <?php
    // } 
    ?>
</div>
</div>
</div>






<!-- Add Modal Structure -->

<div class="modal fade" id="add-lead-modal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">



        </div>
    </div>
</div>

<!-- end add model -->
<div class="modal fade " id="approve-modal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Approve Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="approve-form">
                    <input type="hidden" id="Recordid" value="<?= $_REQUEST['Record'] ?>">
                    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
                    <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
                    <input type="hidden" name="leadstatus_v" id="leadstatus_v" value="6">

                    <div class="mb-3">
                        <label for="approve_comment" class="form-label">Comment</label>
                        <textarea id="approve_comment" class="form-control" rows="4" maxlength="200" 
                            placeholder="Add your comment here..."></textarea>
                        <p class="mt-2 text-danger approve-error-msg"></p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="approvesubmit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <style>
  #approve_comment {
    resize: none;
  }
</style> -->

<div class="modal fade" id="delegate-modal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Delegate Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="approve-form">
                    <input type="hidden" id="Recordid" value="<?= $_REQUEST['Record'] ?>">
                    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
                    <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">

                    <div class="mb-3" style="width: 50%">
                        <label for="delegate_comment" class="form-label">Delegate User</label>

                        <select class="form-control" id="new_vm">
                            <option value="">-select-</option>

                            <?php
                            foreach ($userlist as $key => $value) {
                                # code...
                            
                                ?>
                                <option value="<?= $value['id']; ?>"><?= $value['showfield']; ?> (<?= $value['email']; ?>)
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="delegate_comment" class="form-label">Comment</label>
                        <textarea id="delegate_comment" class="form-control" rows="4"
                            placeholder="Add your comment here..."></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="delegatesubmit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modify-modal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modifyModalLabel">Modify Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="approve-form">
                    <input type="hidden" id="Recordid" value="<?= $_REQUEST['Record'] ?>">
                    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
                    <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
                    <input type="hidden" name="leadstatus_v" id="leadstatus_m" value="3">


                    <div class="mb-3">
                        <label for="modify_comment" class="form-label">Comment</label>
                        <textarea id="modify_comment" class="form-control" rows="4" maxlength="200" 
                            placeholder="Add your comment here..."></textarea>
                        <p class="mt-2 text-danger modify-error-msg"></p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="modifysubmit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reject-modal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Reject Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="approve-form">
                    <input type="hidden" id="Recordid" value="<?= $_REQUEST['Record'] ?>">
                    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
                    <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
                    <input type="hidden" name="leadstatus_r" id="leadstatus_r" value="5">

                    <div class="mb-3">
                        <label for="reject_comment" class="form-label">Comment</label>
                        <textarea id="reject_comment" class="form-control" rows="5"
                            placeholder="Add your comment here..."></textarea>
                    </div>
                    <?php
                    if($TabId == 7)
                    {?>
                    <div class="mb-3">
                        <label for="reject_Reason" class="form-label">Reject Reason</label>
                        <select class="form-control" id="reject_Reason" name="reject_Reason">
                            <option>Select the Reason</option>
                            <option value="1">Pricing Issue</option>
                            <option value="2">Authorization not available</option>
                            <option value="3">Other</option>
                        </select>
                    </div>
                    <?php
                    }?>

                    <div id="otherInputContainer">
                        <input type="text" id="otherInput" name="otherInput" class="form-control"
                            placeholder="Enter the reason" />
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="rejectsubmit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reactivate-modal" tabindex="-1" role="dialog" aria-labelledby="reactivateModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Reactivate Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="approve-form">
                    <input type="hidden" id="Recordid" value="<?= $_REQUEST['Record'] ?>">
                    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
                    <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
                    <input type="hidden" name="leadstatus_reactivate" id="leadstatus_reactivate" value="1">

                    <div class="mb-3">
                        <label for="reactivate_comment" class="form-label">Comment</label>
                        <textarea id="reactivate_comment" class="form-control" rows="5"
                            placeholder="Add your comment here..."></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="reactivatesubmit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reject-general-modal" tabindex="-1" role="dialog" aria-labelledby="rejectgeneralModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="arejectgeneralModalLabel">Reject Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rejectgeneral-form">
                    <input type="hidden" id="Recordid" value="<?= $_REQUEST['Record'] ?>">
                    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
                    <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
                    <!-- <input type="hidden" name="leadstatus_r" id="leadstatus_r" value="5"> -->

                    <div class="mb-3">
                        <label for="reject_general_comment" class="form-label">Comment</label>
                        <textarea id="reject_general_comment" class="form-control" rows="5" maxlength="200"
                            placeholder="Add your comment here..."></textarea>
                        <p class="mt-2 text-danger reject-general-error-msg"></p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="rejectgeneralsubmit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="detail-view-general-info" tabindex="-1" role="dialog" aria-labelledby="detailViewGeneralLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailViewGeneralLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-dynamic-content">

                </div>
                <div class="text-danger detail-view-general-error"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="detail-view-general-submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- start modeal for data import  -->
<div class="modal fade " id="dataimport-modal" tabindex="-1" role="dialog" aria-labelledby="dataimportModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataimportModalLabel">Bulk Data Upload</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="dataimport-form">
                    <input type="hidden" id="dataImportRecordid" value="<?= $_REQUEST['Record'] ?>">
                    <input type="hidden" name="csrfToken" id="dataImportCsrfToken" value="<?= $csrfToken; ?>">
                    <input type="hidden" name="csrfTokenName" id="dataImportcsrfTokenName" value="<?= $csrfTokenName; ?>">
                    <input type="hidden" name="dataImportBlockid" id="data-import-blockid" value="">

                    <div class="mb-3 row justify-content-center">
                        <div class="col-md-6 col-lg-4">
                            <label for="dataimport-file" class="form-label">Upload Excel File</label>
                            <input type="file" id="dataimport-file" class="form-control" accept=".xlsx,.xls" />
                            
                        </div>
                        <p class="mt-2 text-danger dataimport-error-msg"></p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="dataimport-submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- end modal for data import  -->
<!-- <div class="modal fade" id="delegate-modal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
       
            <input type="hidden" id="Recordid" value="">
            <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
            <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
            <input type="hidden" name="leadstatus_v" id="leadstatus_d" value="3">
        <textarea id="delegate_comment"></textarea>
        <button type="button" id="delegatesubmit">Submit</button>
       
    </div>
  </div>
</div> -->
<!-- <div class="modal fade" id="reject-modal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
       
            <input type="hidden" id="Recordid" value="<?php //$_REQUEST['Record'] 
            ?>">
            <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
            <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
            <input type="hidden" name="leadstatus_v" id="leadstatus_r" value="5">
        <textarea id="reject_comment"></textarea>
        <button type="button" id="rejectsubmit">Submit</button>
       
    </div>
  </div>
</div> -->
<div class="modal fade" id="csvPreviewModal" tabindex="-1" role="dialog" style="max-width: 95%; margin: auto;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">CSV Data Preview (<span id="previewTotalRecords">0</span> records)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body" style="max-height: 70vh; overflow: auto;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="mb-2">
                <input type="text" id="previewSearch" class="form-control form-control-sm"
                    placeholder="Search by Serial, Make, Type, Capacity, Certificate...">
            </div>
                </div>  
                <div id="previewTableContainer">
                    <table id="previewTablecsvUpload" class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Laptop Serial No*</th>
                                <th>HDD/SDD Serial No</th>
                                <th>Make</th>
                                <th>Type</th>
                                <th>Capacity</th>
                                <th>Software Name</th>
                                <th>Wiping Completed*</th>
                                <th>Wiping Date</th>
                                <th>Certificate</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                
                <div id="previewPagination" class="d-flex justify-content-between align-items-center mb-3" style="display: none;">
                    <div id="previewRecordInfo"></div>
                    <div>
                        <button id="previewFirstPage" class="btn btn-sm btn-primary me-1">First</button>
                        <button id="previewPrevPage" class="btn btn-sm btn-primary me-1">Prev</button>
                        <div id="previewPageNumbers" class="btn-group me-2" style="display: inline-block;"></div>
                        <button id="previewNextPage" class="btn btn-sm btn-primary me-1">Next</button>
                        <button id="previewLastPage" class="btn btn-sm btn-primary">Last</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelPreviewBtn">Cancel</button>
                <button type="button" class="btn btn-success" id="saveCsvToDbBtn">Save All to Database</button>
                <input type="file" id="popup-zip-upload-file" accept=".zip" style="display:none">
                <button type="button" class="btn btn-primary" id="popup-choose-zip-btn">Proceed to ZIP Upload</button>
                <button type="button" class="btn btn-success" id="popup-upload-zip-btn" style="display:none;">Upload ZIP</button>
                <span id="popup-zip-status" style="margin-left:10px;font-weight:bold;"></span>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="soBulkImportModal" tabindex="-1" role="dialog" aria-labelledby="soBulkImportModalLabel" aria-hidden="true" style="max-width: 95%; margin: auto;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Sales Order Items Bulk Import
                    (<span id="soPreviewTotalRecords">0</span> records)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" style="max-height: 70vh; overflow: auto;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <input type="file" id="so-bulk-file-input" accept=".csv" style="display:none;">
                        <button type="button" class="btn btn-primary btn-sm" id="so-select-file-btn">
                            Choose CSV File
                        </button>
                        <button type="button" class="btn btn-link btn-sm" id="so-download-sample-btn">
                            Download Sample
                        </button>
                        <span id="so-selected-file-name" class="ms-2 text-muted"></span>
                    </div>
                    <div class="mb-2">
                        <input type="text"
                               id="soPreviewSearch"
                               class="form-control form-control-sm"
                               placeholder="Search by Tag, Product, Category...">
                    </div>
                </div>

                <div id="soPreviewTableContainer" style="overflow-x:auto;">
                    <table id="soPreviewTable" class="table table-bordered table-striped table-sm">
                        <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Tag Number</th>
                            <th>Qty</th>
                            <th>Qty In Stock</th>
                            <th>Selling Price</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>HSN</th>
                            <th>GST %</th>
                            <th>SP (GST Excl)</th>
                            <th>Base Price (Excl)</th>
                            <th>CGST %</th>
                            <th>SGST %</th>
                            <th>IGST %</th>
                            <th>CGST Amt</th>
                            <th>SGST Amt</th>
                            <th>IGST Amt</th>
                            <th>Total Amount</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>


                <div id="soPreviewPagination"
                     class="d-flex justify-content-between align-items-center mb-3"
                     style="display: none;">
                    <div id="soPreviewRecordInfo"></div>
                    <div>
                        <button id="soPreviewFirstPage" class="btn btn-sm btn-primary me-1">First</button>
                        <button id="soPreviewPrevPage" class="btn btn-sm btn-primary me-1">Prev</button>
                        <div id="soPreviewPageNumbers" class="btn-group me-2" style="display: inline-block;"></div>
                        <button id="soPreviewNextPage" class="btn btn-sm btn-primary me-1">Next</button>
                        <button id="soPreviewLastPage" class="btn btn-sm btn-primary">Last</button>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <input type="hidden" id="so-bulk-salesorder-id" value="<?= (int)$_REQUEST['Record'] ?>">
                <label class="field-flag" id="soBulkReplaceWrap" >
                        <input type="checkbox" id="soBulkReplaceAll"  style="display:inline-block !important;width:16px;height:16px;
                   border:1px solid #333;background:#fff;margin-top:3px;">
                        <span class="text-danger"><strong>Delete existing record.. </strong></span>
                </label>
                <button type="button" class="btn btn-secondary mod-close" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success savebutton" id="so-bulk-save-btn">
                    Save All
                </button>
                 <button type="button" class="btn btn-danger" id="so-bulk-delete-btn" style="display:none;">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Import Error Modal -->
<div class="modal fade" id="soImportErrorModal" tabindex="-1" role="dialog" aria-labelledby="soImportErrorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--color-primary);color:white;">
                <h5 class="modal-title" id="soImportErrorModalLabel">Bulk Import Error</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Row:</strong> <span id="soErrRow"></span></p>
                <p><strong>Details:</strong></p>
                <ul id="soErrList" style="padding-left: 20px;"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mod-close" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
<div>
    
</div>
<?php
// <script src=""></script>
//$this->registerJsFile('https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('@web/thememain/js/quill.min.js', ['depends' => [AdminAsset::class]]);

$this->registerJs("

  

        $('.approve').on('click', function () {
        

       
            $('#approve-modal').modal('show')
                .find('.modal-content')
                .html();

    });
       $('.reject').on('click', function () {
        

       
            $('#reject-modal').modal('show')
                .find('.modal-content')
                .html();

    });
       $('.delegate').on('click', function () {
        
// alert('cxbx');
       
            $('#delegate-modal').modal('show')
                .find('.modal-content')
                .html();

    });

      $('.modify').on('click', function () {
        
// alert('cxbx');
       
            $('#modify-modal').modal('show')
                .find('.modal-content')
                .html();

    });
    // added on 18 jan 2025 Reactivate
     $('.reactivate').on('click', function () {
        

       
            $('#reactivate-modal').modal('show')
                .find('.modal-content')
                .html();

    });
    //end

     $(document).ready(function() {
            $('.read-more-btn').click(function() {
                var contentItem = $(this).closest('.content-item'); // Get the parent content item
                var moreContent = contentItem.find('.more-content');
                var lessContent = contentItem.find('.less-content');
                var dots = contentItem.find('.dots');
            //   alert(moreContent.length);
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

 
");


?>
<script type="text/javascript">

</script>


<!-- added by ptpatel -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalSummeryLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <label id="modal_label_name"> </label>  
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>   
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <!-- <button type="button" class="btn btn-primary savebutton singleeditsavebtn" onclick="saveModalData()">Save</button> -->
                        <button type="button" class="btn btn-primary savebutton singleeditsavebtn">Save</button>
                    </div>
                </div>
            </div>
</div>
<?php if($TabId == 19 || $TabId == 41) { ?>
<!-- this model is added by ptpatel on date 03-09-2025 for reset password from detailview in contact module -->
<div class="modal fade" id="contactresetpasswordModal" tabindex="-1" aria-labelledby="editModalSummeryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <label id="modal_label_name"> Reset Password </label>  
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>   
            <div class="modal-body">
                <div class="form-group required form-field-cst section-password col-lg-12">
                    <label class="control-label" for="password">Password</label>
                    <input type="password" id="password" class="form-control PS~M" name="password" placeholder="New Password" maxlength="100">
                    <div class="help-block"></div>
                </div>


                <div class="form-group required form-field-cst section-confirm_password col-lg-12">
                    <label class="control-label" for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" class="form-control" name="confirm_password" placeholder="Confirm Password" maxlength="100">
                    <div class="help-block"></div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary contactResetPassword">Save</button>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<!-- end model added by ptpatel on date 03-09-2025 for reset password from detailview in contact module -->

<div class="modal fade" id="importErrorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Import Aborted</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="errRowWrapper"><b>Row: </b><span id="errRow"></span></div>
        <div id="errFieldWrapper"><b>Field: </b><span id="errField"></span></div>
        <div id="errValueWrapper"><b>Value: </b><span id="errValue"></span></div>
        <p><b>Reason: </b> <span id="errReason"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
