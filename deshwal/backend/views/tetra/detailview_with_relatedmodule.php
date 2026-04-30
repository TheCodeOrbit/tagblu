<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;
use app\models\ListHire;
use app\models\Reference;

AdminAsset::register($this);
$this->title = Yii::t('app', $Tabname . " Detail");

$url = Url::to(['Edit']);
$urlApprove = Url::to(['approvelead']);
$this->registerCssFile('@web/thememain/css/listview.css', ['depends' => [AdminAsset::class]]);
$baseUrl = Yii::$app->HomeUrl;
$module = strtolower($ModuleName);
$Recordid = $Recordid;
// echo "<pre>";
// print_r($Record);
$fullname = $headerfullname;
$csrfTokenName = Yii::$app->request->csrfParam;  // This replaces csrfTokenName
$csrfToken = Yii::$app->request->csrfToken;      // Get the CSRF token itself

//get lead stages
// if ($Tabname == "Leads") {
//   //get lead status
//   $sql = "select * from lead_status where is_active =1 and leadstatusid not in (6,7,8,9) order by seq_no ";
//   $pipelinetatuses = Yii::$app->db->createCommand($sql)->queryAll();
// }
// print_r($pipelinetatuses);
// echo "<br>";
// print_r($pipelinetatuses);
?>
<input type="hidden" value="<?php echo $ModuleName; ?>" id="module" name="module" />
<input type="hidden" value="<?php echo $Recordid; ?>" id="recordid"  />

<div class="page-content">
  <div class="records table-responsive">
    <div class="record-header">
      <div class="add">
        <img src="<?= $baseUrl; ?>/thememain/img/module-icon/<?= $ModuleName; ?>.png" class=" head-img">
        <span class="sm-modname"><?=$Tabname;?></span>
        <br>
        <span class="fullname"><?= $fullname; ?></span>
      </div>


      <div class="">

        <?php
        if (isset($Record['vertical_manager']) && $Record['vertical_manager'] == Yii::$app->user->id) { ?>
          <div class="div-regroup">
            <button class="approve">
              <span class="">Approve</span></button>
            <button class="delegate" id="delegate">
              <span class="">Delegate</span>
            </button>
            <button class="modify" id="modify">
              <span class="">Modify</span>
            </button>
            <button class="reject" id="reject">
              <span class="">Reject</span>
            </button>


          </div>
        <?php
        } else if ($Record['ownerid'] == Yii::$app->user->id) {
        ?>
          <div class="div-regroup">

            <?php
            if (isset($Record['leadstatus']) && $Record['leadstatus'] == 13) //show only when qualified
            { ?>
              <button class="button-frame"><span class="span-convert">Convert</span></button>
            <?php
            } ?>
            <?php
            if (isset($Record['leadstatus']) && $Record['leadstatus'] != 4 && $Record['leadstatus'] != 13) //cant edit if send for approval or qualified
            { ?>
              <button class="button-frame-38" id="edit-lead-btn"><span class="span-edit" id="edit-lead-btn">Edit</span> </button>
            <?php
            } ?>

            <div class="div-frame">
              <span class="span-more">More</span>
              <div class="div-mdi-menu-down">
                <div class="div-vector-39"></div>
              </div>
            </div>

          </div>
        <?php
        } ?>

      </div>
    </div>
  </div>
</div>

<div class="select-1">
  <?php
if(isset($pipelinetatuses))//start pipeline
  {?>
  <div class="container-d">
    <div class="col-md-12">
      <div class="pipeline-container">
        <div class="rectangle-54">
          <div class="pip-1">
            <h2 class="lead-pipeline-status"><?=$Tabname;?> pipeline Status</h2>
            <div class="flex-row-e">
              <!-- get lead stages -->
              <?php
              $cn = 1;
              // print_r($pipelinetatuses);
              foreach ($pipelinetatuses as $key => $value) {
                # code...
                if ($Record[$pipelinecolumn] == $value[$pipelinestatusid])
                  $st = "green";
                else $st = "gray";
                $bt = "mid";
                if ($cn == 1) {
                  $bt = "start";
                  $class_ini = "rectangle-start-";
                } else
                  $class_ini = "rectangle-mid-";

                $finclass = $class_ini . $st;

                $cn++;
              ?>
                <!--  <div class="col-sm-2">
                        <div class="rectangle-start-gray">
                           <span class="not-contacted">New </span>
                        </div>
                         </div> -->
                <div class="col-sm-2">
                  <button class="<?= $finclass; ?> leaddurationparent <?= $value['leadstatus_value']; ?>" data-id="<?= $value[$pipelinestatusid]; ?>" data-bt="<?= $bt; ?>" data-cl="<?= $finclass; ?>" style="border: none;">
                    <span class="not-contacted"><?= $value[$pipelinestatusvalue]; ?></span>
                  </button>
                </div>
              <?php
              }
              ?>

            </div>

            <div class="vector-5e">
              <div class="flex-row-f">
                <span class="stage-name">Stage Name</span><span class="entered-at">Entered At</span><span class="duration">Duration</span>
              </div>
              <?php
              $cn = 1;
              foreach ($pipelinetatuses as $key => $value) {
                 if ($Record[$pipelinecolumn] == $value[$pipelinestatusid])
                  $stclass = "";
                else $stclass = "tr-hidden";
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
                          join modtracker_detail on modtracker_basic.id = modtracker_detail.id where fieldname='leadstatus' and postvalue=:postvalue  and module=:module and crmid=:Recordid order by changedon desc ")
                  ->bindValue(":module", $module)
                  ->bindValue(":Recordid", $Recordid)
                  ->bindValue(":postvalue", $value[$pipelinestatusid])
                  ->queryOne();
                if ($stclass == "") {
                  //cureent stage
                  $today = date("Y-m-d H:i:s");
                  if ($postvaluearr)
                    $enterdate = $postvaluearr['changedon'];
                  else $enterdate = '';

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
                  if(isset($postvaluearr['changedon']))
                  $enterdate = $postvaluearr['changedon'];
                else $enterdate = '';
                  $date1 = new DateTime($enterdate); // Replace with your first date
                  $date2 = new DateTime($today); // Replace with your second date

                  // Get the difference as a DateInterval object
                  $interval = $date1->diff($date2);

                  // Format the difference
                  $days = $interval->days; // Total days
                  $hours = $interval->h; // Remaining hours
                  $minutes = $interval->i; // Remaining minutes

                  // Output in desired format
                  $duration =  "$days Days | $hours hours $minutes min";
                  // Convert the string into a timestamp
                  $timestamp = strtotime($enterdate);

                  // Format the date
                  $enteredat = date('M d, Y \a\t g.i a', $timestamp); // Format: Oct 22, 2024 at 9.16 am



                } else {
                  $duration = '';
                  $enteredat = '';
                }

              ?>
                <div class="flex-row-f-5f leaddurationbox leadduration<?= $value[$pipelinestatusid] . " " . $stclass; ?> ">
                  <div class="col-md-4">
                    <span class="new-60 lead-satge-name"><?= $value[$pipelinestatusvalue]; ?> </span>
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
  <div class="container-d">
    <?php
    foreach ($pipelinetatuses as $key => $value) {
      if ($Record['leadstatus'] == $value['leadstatusid'])
        $stclass = "";
      else $stclass = "tr-hidden";
    ?>
      <div class="rectangle-61 leaddescbox leaddesc<?= $value['leadstatusid'] ?> <?= $stclass; ?>">
        <h2 class="description">Description</h2>
        <p><?= $value['description']; ?>
        </p>
      </div>
    <?php
    } ?>
  </div>
  <?php
}//end pipeline
?>
  <div class="container-d">
    <div class="col-md-12">

      <div class="flex-row-ee">

        <nav class="nav__container">
          <div class="nav__logo">
            <button class="tab active" data-tab="summary">Summary</button>
            <button class="tab" data-tab="history">History</button>
          </div>

          <ul class="nav__links">
            <?php
  if(!empty($relatemodules))
  {?>

      <?php
      $i=1;
      foreach ($relatemodules as $key => $value) {?>
        <!-- # code... -->
        
       <li class="nav__link" title="Attach Email">
              <a href="#"class="tabid <?php //if($i==1) echo 'active';?>" onclick="showRelatedModulelist('<?= $value['modulename'];?>',<?= $TabId;?>,<?= $Recordid; ?>,'<?=$value['related_module'].$value['modulename'];?>')" data-tabid="<?=$value['related_module'].$value['modulename'];?>"><?=ucfirst($value['modulename']);?></a>
            </li>
        <?php
        $i++;
      }
    }?>
            <!-- <li class="nav__link">
        <a href="#" style="background: #b178fb;padding: 7px 12px;"><img src="<?= $baseUrl; ?>/thememain/img/calender.png" style="background: #b178fb;
  width: 17px;"></a>
      </li> -->
          <!--   <li class="nav__link" title="Attach Email">
              <a href="#" style="background: var(--color-primary) !important;padding: 7px 12px;"><img src="<?= $baseUrl; ?>/thememain/img/email-white.png" style="width: 17px;"></a>
            </li>
            <li class="nav__link" title="Attch Document">
              <a href="#" style="background: #a0a0a0;padding: 7px 10px;"><img src="<?= $baseUrl; ?>/thememain/img/pdf.png" style="background: #a0a0a0;
  width: 17px;"></a>
            </li>
            <li class="nav__link" title="Attach Note">
              <a href="#" style="background: #f8af92;padding: 7px 10px;"><img src="<?= $baseUrl; ?>thememain/img/33a94905-7956-4a9e-bd74-7ffb3b1d2b08.png" style="background: #f8af92;
  width: 17px;"></a>
            </li> -->
          </ul>
          <div>
          </div>
          <div>
          </div>
        </nav>

      </div>
    </div>
  </div>
  <div class="container-d">
    <div class="col-md-12">
      <!-- Main Content -->
      <div class="main-content">
        <!-- Summary Section -->
        <div class="container-m">
          <!-- Left Section -->
          <div class="left-section">
            <div id="summary" class="tab-content-detail-view active">
              <div class="accordion">
                <?php
                foreach ($ColumnList->blocks as $BlockKey => $Block) {
                  if (!empty($Block->detailfields)) {
                ?>
                    <details class="c-faqs__item">
                      <summary class="c-faqs__item-question">
                        <?= $Block->blocklabel ?> <i class="fa-solid fa-angle-down"></i>
                      </summary>

                      <div class="details-container">

                        <?php
                        foreach ($Block->detailfields as $field) { ?>

                          <?php
                          if ($field["uitype"] == 12) {
                            $ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '';
                            $model1 = new Reference($TableName, $FieldId);
                            $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                            $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                            if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != '')
                              $Record->{$field["columnname"]} = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
                            else  $Record->{$field["columnname"]} = '';
                          } else if ($field["uitype"] == 8) {
                            $modellist = new Listhire;
                            if (isset($Record->{$field["columnname"]}))
                              $Record->{$field["columnname"]} = $modellist->getPickListDetailvalue($field["fieldid"], $Record->{$field["columnname"]});
                            else $Record->{$field["columnname"]};
                          } else if ($field["uitype"] == 53) {
                            $modellist = new Listhire;
                            if (isset($Record->{$field["columnname"]}))
                              $Record->{$field["columnname"]} = $modellist->getuser($field["fieldid"], $Record->{$field["columnname"]});
                            else $Record->{$field["columnname"]};
                          }
                          if ($field["columnname"] == "firstname") {
                            if (isset($Record->{$field["columnname"]})) {
                              // echo "deepika ".$Record['salutation'];die;   
                              if (!empty($Record['salutation'])) {
                                $modellist = new Listhire;

                                $salutation = $modellist->getSalutation($Record['salutation']);

                                $Record->{$field["columnname"]} = $salutation . " " . $Record->{$field["columnname"]};
                              }
                            }
                          }
                          if ($field['columnname'] != "salutation") {
                            //$Record->{$field["columnname"]} ='';
                          ?>

                            <div class="detail-group Details-1">
                              <label><?= $field["fieldlabel"]; ?></label>
                              <span><?= isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "-" ?></span>
                            </div>

                        <?php
                          }
                        } ?>
                      </div>
                    </details>

                <?php
                  }
                } ?>
              </div>
            </div>
            <!-- History Section -->
            <!-- History Section -->
            <div id="history" class="tab-content-detail-view">
              <div class="history-section">
                <div class="activity-1">History</div>
                <div class="timeline-1w">
                  <?php foreach ($Detailhistory as $key): ?>
                    <?php if (isset($key['basic']['status']) && ($key['basic']['status'] == 'Created')): ?>
                      <div class="timeline-event">
                        <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i></div>
                        <div class="timeline-details">
                          <p>
                            <?php echo htmlspecialchars($key['basic']['whodid']); ?>
                            <span class="timeline-tsk"><?php echo htmlspecialchars($key['basic']['status']); ?></span><br>
                            This <?php echo htmlspecialchars($key['basic']['module']); ?>
                          </p>
                          <p>
                            <?php
                            $datetime = new DateTime($key['basic']['changedon']);
                            echo $datetime->format('M d, Y \A\t g.i A');
                            ?>
                          </p>
                        </div>
                      </div>
                    <?php else: ?>
                      <?php if (isset($key['details'][0])): ?>
                        <?php
                        $ids = explode(',', $key['details'][0]['ids']);
                        $fieldnames = explode(',', $key['details'][0]['fieldnames']);
                        $fieldlabels = explode(',', $key['details'][0]['fieldlabels']);
                        $prevalues = explode(',', $key['details'][0]['prevalues']);
                        $postvalues = explode(',', $key['details'][0]['postvalues']);
                        $uitypes = explode(',', $key['details'][0]['uitypes']);
                        $fieldids = explode(',', $key['details'][0]['fieldids']);
                        ?>
                        <div class="timeline-event">
                          <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i></div>
                          <div class="timeline-details">
                            <p>
                              <?php echo htmlspecialchars($key['basic']['whodid']); ?>
                              <span class="timeline-tsk"><?php echo htmlspecialchars($key['basic']['status']); ?></span>
                            </p>
                            <?php foreach ($fieldlabels as $index => $label): ?>
                              <?php
                              $prevalue = isset($prevalues[$index]) ? $prevalues[$index] : 'N/A';
                              $postvalue = isset($postvalues[$index]) ? $postvalues[$index] : 'N/A';
                              $uitype = isset($uitypes[$index]) ? $uitypes[$index] : 'N/A';
                              $fieldid = isset($fieldids[$index]) ? $fieldids[$index] : 'N/A';

                              if ($uitype == 12) {

                                //prevlaue
                                $ref_hid_value = isset($prevalue) ? $prevalue : '';
                                $model1 = new Reference($TableName, $FieldId);
                                $relatedmodulename = $model1->getRelatedNoduleName($fieldid);
                                $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($fieldid);
                                if (isset($prevalue) && $prevalue != '')
                                  $prevalue = $model1->getRefEntityValue($fieldid, $ref_hid_value);
                                else  $prevalue = '';

                                //postvalue
                                //prevlaue
                                $ref_hid_value = isset($postvalue) ? $postvalue : '';
                                $model1 = new Reference($TableName, $FieldId);
                                $relatedmodulename = $model1->getRelatedNoduleName($fieldid);
                                $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($fieldid);
                                if (isset($postvalue) && $postvalue != '')
                                  $postvalue = $model1->getRefEntityValue($fieldid, $ref_hid_value);
                                else  $postvalue = '';
                          


                          } else if ($uitype == 8) {
                            $modellist = new Listhire;
                            if (isset($$prevalue))
                              $prevalue = $modellist->getPickListDetailvalue($fieldid, $prevalue);
                            else $prevalue = '';
                            //postvalue
                            if (isset($postvalue))
                              $postvalue = $modellist->getPickListDetailvalue($fieldid, $postvalue);
                            else $postvalue = '';
                          } else if ($uitype == 53) {

                            $modellist = new Listhire;
                            if (isset($prevalue))
                              $prevalue = $modellist->getuser($fieldid, $prevalue);
                            else $prevalue;
                            if (isset($postvalue))
                              $postvalue = $modellist->getuser($fieldid, $postvalue);
                            else $postvalue;
                          }

                              // Handle picklist values
                              // if ($uitype == 8 && $fieldid) {
                              //   // Query the picklist table to fetch the target table, display field, and target field
                              //   $picklistData = Yii::$app->db->createCommand(
                              //     "
                              //        SELECT targettable, dispfield, targetfield 
                              //        FROM picklist 
                              //        WHERE fieldid = :fieldid 
                              //         LIMIT 1",
                              //     [':fieldid' => $fieldid]
                              //   )->queryOne();

                              //   if ($picklistData) {
                              //     $targetTable = htmlspecialchars($picklistData['targettable'], ENT_QUOTES);
                              //     $displayField = htmlspecialchars($picklistData['dispfield'], ENT_QUOTES);
                              //     $targetField = htmlspecialchars($picklistData['targetfield'], ENT_QUOTES);

                              //     // Fetch text values for prevalue and postvalue
                              //     $prevalue = Yii::$app->db->createCommand(
                              //       "
                              //           SELECT $displayField 
                              //            FROM $targetTable 
                              //            WHERE $targetField = :prevalue 
                              //             LIMIT 1",
                              //       [':prevalue' => $prevalue]
                              //     )->queryScalar() ?: $prevalue;

                              //     $postvalue = Yii::$app->db->createCommand(
                              //       "
                              //          SELECT $displayField 
                              //          FROM $targetTable 
                              //          WHERE $targetField = :postvalue 
                              //          LIMIT 1",
                              //          [':postvalue' => $postvalue]
                              //     )->queryScalar() ?: $postvalue;
                              //   }
                              // }
                              ?>
                              <p>
                                <strong><?php echo htmlspecialchars($label); ?>:</strong> from
                                "<strong><?php echo htmlspecialchars($prevalue); ?></strong>" to
                                "<strong><?php echo htmlspecialchars($postvalue); ?></strong>"
                              </p>
                            <?php endforeach; ?>
                            <p>
                              <?php
                              $datetime = new DateTime($key['basic']['changedon']);
                              echo $datetime->format('M d, Y \A\t g.i A');
                              ?>
                            </p>
                          </div>
                        </div>
                      <?php endif; ?>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>

              </div>
            </div>
          </div>
          <!-- Center Section -->
          <div class="center-section">
            <!-- <div class="col-md-4 w-2-section"> -->
            <div>
              <div class="activity-1">Activity</div>
              <div class="flex-row-f-section-w">

                <button class="frame-50-1" id="open-call-btn" title="Add Calls">

                  <span class="add-calls"><img src="<?= $baseUrl; ?>/thememain/img/phone.png"> Add Calls </span>
                </button>
                <button class="frame-52-1" id="open-meeting-btn" title="Add Meeting">
                  <span class="add-meeting"><img src="<?= $baseUrl; ?>/thememain/img/add-meeting.png"> Add Meeting</span>
                </button>
                <button class="frame-54-1" id="open-task-btn" title="Add Task">
                  <span class="add-task"><img src="<?= $baseUrl; ?>/thememain/img/add-task.png"> Add Task</span>
                </button>
              </div>
              <section class="c-faqs">
                <div class="c-faqs__items" style="margin-top: 10px;">
                  <details class="c-faqs__item" <?= (!empty($allactivities)) ? 'open' : ''; ?>>
                    <summary class="c-faqs__item-question">
                      Upcoming & Overdue <i class="fa-solid fa-angle-down"></i>
                    </summary>


                    <div class="col-xs-12">
                      <ul class="event-list">
                        <?php foreach ($allactivities as $activity): ?>
                          <li class="phone-event-detail" style="height:auto">
                            <?php if ($activity['activity_type'] === 'call'): ?>
                              <img alt="Call" src="<?= $baseUrl; ?>/thememain/img/call-icon.png" />
                            <?php elseif ($activity['activity_type'] === 'meeting'): ?>
                              <img alt="Meeting" src="<?= $baseUrl; ?>/thememain/img/meeting-icon.png" />
                            <?php elseif ($activity['activity_type'] === 'task'): ?>
                              <img alt="Task" src="<?= $baseUrl; ?>/thememain/img/task-icon.png" />
                            <?php endif; ?>

                            <div class="info">
                              <h2 class="title" style="color: var(--color-primary) !important;">
                                <?= ucfirst($activity['activity_type']); ?>
                              </h2>
                              <p class="desc"><?= $activity['activity_description']; ?></p>
                            </div>

                            <div class="info-2">
                              <?php if ($activity['activity_type'] === 'call'): ?>
                                <span><?= date('M d, Y \a\t g:i a', strtotime($activity['activity_date'])); ?></span>
                              <?php elseif ($activity['activity_type'] === 'meeting'): ?>
                                <span><?= date('M d, Y \a\t g:i a', strtotime($activity['activity_date'])); ?></span>
                              <?php elseif ($activity['activity_type'] === 'task'): ?>
                                <span><?= date('M d, Y \a\t g:i a', strtotime($activity['activity_date'])); ?></span>

                              <?php endif; ?>
                            </div>
                          </li>
                          <div class="detail-heightline"></div>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  </details>

                </div>
              </section>


            </div>
          </div>
          <!-- Right Section -->
          <div class="right-section">
            <div class="notes">
              <div class="notes-1">Notes</div>


              <div class="notes-container">
                <!-- Notes Header -->


                <!-- Input Area -->
                <div class="notes-input-area">

                  <textarea placeholder="Write your notes here..." class="notes-editor" id="modnotes"></textarea>
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
                    $notedesc = strip_tags($value['notecontent']);
                    $notedesc = substr($notedesc, 0, 50);
                  ?>
                    <div class="notes-content">
                      <div class="note-item">
                        <span>
                          <a href="#">
                            <img src="<?= $baseUrl; ?>thememain/img/33a94905-7956-4a9e-bd74-7ffb3b1d2b08.png" style="background: #f8af92;width: 6%;padding:3px;" />
                          </a> <?= $notedesc; ?><?= $p; ?>
                        </span>
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

                </div>
              </div>
            </div>


          </div>
        </div>
      </div>
    </div>
  </div>


  <!-- related module section open -->
  <?php
  if(!empty($relatemodules))
  {
      $i=1;
      foreach ($relatemodules as $key => $value) {
        
          ?>
      <div class="container-d tab-content-detail-viewmodule <?php if($i==1) echo 'active';?>" id="<?=$value['related_module'].$value['modulename'];?>">
        
      </div>
  <?php
  $i++;
    }
  }?>
  <!-- related module section close -->

</div>






<!-- Add Modal Structure -->

<div class="modal fade" id="add-lead-modal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">



    </div>
  </div>
</div>

<!-- end add model -->
<div class="modal fade " id="approve-modal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
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
          <input type="hidden" name="leadstatus_v" id="leadstatus_v" value="13">

          <div class="mb-3">
            <label for="approve_comment" class="form-label">Comment</label>
            <textarea id="approve_comment" class="form-control" rows="4" placeholder="Add your comment here..."></textarea>
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

<div class="modal fade" id="delegate-modal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
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
              <option>-select-</option>

              <?php
              foreach ($userlist as $key => $value) {
                # code...

              ?>
                <option value="<?= $value['id']; ?>"><?= $value['showfield']; ?> (<?= $value['email']; ?>)</option>
              <?php
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="delegate_comment" class="form-label">Comment</label>
            <textarea id="delegate_comment" class="form-control" rows="4" placeholder="Add your comment here..."></textarea>
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
<div class="modal fade" id="modify-modal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
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
            <textarea id="modify_comment" class="form-control" rows="4" placeholder="Add your comment here..."></textarea>
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
<div class="modal fade" id="reject-modal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
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
            <textarea id="reject_comment" class="form-control" rows="5" placeholder="Add your comment here..."></textarea>
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
<?php
// <script src=""></script>
//$this->registerJsFile('https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js', ['depends' => [AdminAsset::class]]);

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


 
");


?>
<script type="text/javascript">

</script>