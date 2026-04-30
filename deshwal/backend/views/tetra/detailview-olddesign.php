<?php
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

AdminAsset::register($this);

$baseUrl = Yii::$app->HomeUrl;
$scriptPath = $baseUrl . "js/$ModuleName/edit.js";

$this->title = Yii::t('app', $TabLabel . " Detail");

$url = Url::to(['Edit']);
$urlApprove = Url::to(['approvelead']);
$this->registerCssFile('@web/thememain/css/listview.css', ['depends' => [AdminAsset::class]]);
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

$role = Yii::$app->user->identity->role??null;
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

<div class="row detail-row">
  <div class="col-8 detail-left">
    <div class="page-content">
      <div class="records table-responsive records-div-rs">
        <div class="record-header">
          <div class="add">
            <img src="<?= $baseUrl; ?>/thememain/img/module-icon/<?= $ModuleName; ?>.png" class=" head-img">
            <span class="sm-modname"><?= $TabLabel; ?></span>
            <br>
            <span class="fullname"><?= $fullname; ?></span>
          </div>


          <div class="">

            <?php
            if ($TabId == 7 && isset($Record['vertical_manager']) && $Record['vertical_manager'] == Yii::$app->user->id && isset($Record['leadstatus'])) { 
              if( $Record['leadstatus'] == 4)//approval pending
              {?>
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
              }
              else if( $Record['leadstatus'] == 5) // Disqualified
              {
                ?>
                 <div class="div-regroup">
               
                <button class="reactivate" id="reactivate">
                  <span class="">Reactivate</span>
                </button>


              </div>
                <?php
              }
            }else if($TabId == 13 && ($Record['stage'] && $Record['stage'] == 5 && $role == "H8")){ ?>
              <div class="div-regroup">
                <button class="approve">
                  <span class="">Approve</span></button>
                </button>
                <button class="reject" id="reject">
                  <span class="">Reject</span>
                </button>
              </div>
              <?php
            } else if ($Record['ownerid'] == Yii::$app->user->id || Yii::$app->user->id == 1 || $hasadminpower == 1) {

              ?>
                <div class="div-regroup">

                  <?php
                  if (isset($Record['leadstatus']) && $Record['leadstatus'] == 13 && $Record['converted'] == 0) //show only when qualified
                  { ?>
                    <button class="button-frame convert-btn"><span class="span-convert">Convert</span></button>
                  <?php
                  }

                  if ($layout == 'multiple' || $layout == 'single') {
                    $sourcemodule = '';
                    $sourceid = '';
                    if (isset($_GET['sourceid']))
                      $sourceid = filter_var($_GET['sourceid'], FILTER_VALIDATE_INT);
                    if (isset($_GET['sourcemodule']))
                      $sourcemodule = filter_var($_GET['sourcemodule'], FILTER_VALIDATE_INT);
                    if (!empty($sourceid) && !empty($sourcemodule))
                      $urledit = "edit?Record=" . $Recordid . "&sourcemodule=" . $sourcemodule . "&sourceid=" . $sourceid;
                    else {
                      $urledit = "edit?Record=" . $Recordid;
                    }
                    if ($TabId == 7) {
                      if (($Record['leadstatus'] != '4' && $Record['leadstatus'] != '13') && $Record['converted'] != 1 && $Record['leadstatus'] != '5' && $Record['leadstatus'] != '9') //can't edit if send for approval or qualified
                      { ?>

                        <a href="<?= $urledit; ?>" class="add-lead-btn2"> <button class="button-frame-38"><span
                              class="span-edit">Edit</span>
                          </button></a>
                      <?php
                      }
                    }else if($TabId == 13 && (($Record['stage']) && ($Record['stage'] == 3 || $Record['stage'] == 4 || $Record['stage'] == 5))){
                      
                    } else {

                      ?>
                      <a href="<?= $urledit; ?>" class="add-lead-btn2"> <button class="button-frame-38"><span
                            class="span-edit">Edit</span>
                        </button></a>
                    <?php
                    }

                  } else {

                    if ($TabId == 7) {
                      if (($Record['leadstatus'] != '4' && $Record['leadstatus'] != '13') && $Record['converted'] != 1) //can't edit if send for approval or qualified
                      { ?>
                        <button class="button-frame-38" id="edit-lead-btn"><span class="span-edit" id="edit-lead-btn">Edit</span>
                        </button>
                      <?php
                      }
                    }else if($TabId == 13 && (($Record['stage']) && ($Record['stage'] == 3 || $Record['stage'] == 4 || $Record['stage'] == 5))){
                      
                    } else {
                      ?>
                      <button class="button-frame-38" id="edit-lead-btn"><span class="span-edit" id="edit-lead-btn">Edit</span>
                      </button>

                    <?php
                    }
                  } ?>

                  <!-- <div class="div-frame">
                <span class="span-more">More</span>
                <div class="div-mdi-menu-down">
                  <div class="div-vector-39"></div>
                </div>
              </div> -->

                </div>
              <?php
            } ?>

          </div>
        </div>
      </div>
    </div>

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
                      <li class="<?= $finclass; ?>  stage text-capitalize leaddurationparent <?= $value[$pipelinestatusvalue]; ?>" data-id="<?= $value[$pipelinestatusid]; ?>" data-bt="<?= $bt; ?>" data-cl="<?= $finclass; ?>">
                          <a href="#"><?= $value[$pipelinestatusvalue]; ?></a>
                      </li>
                      <?php
                    }
                    ?>
                    </ul>

                  </div>

                  <div class="vector-5e">
                    <div class="flex-row-f">
                      <span class="stage-name">Stage Name</span><span class="entered-at">Entered At</span><span
                        class="duration">Duration</span>
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

          <div class="flex-row-ee">

            <nav class="nav__container">
              <div class="nav__logo">
                <button class="tab active" data-tab="summary">Summary</button>
                <button class="tab" data-tab="history">History</button>
              </div>


              <?php
              if (!empty($relatemodules)) {
                // print_r($relatemodules);die;
                ?>
                <ul class="nav__links">

                  <?php
                  $i = 1;
                  foreach ($relatemodules as $key => $value) { ?>

                    <li class="nav__link" title="<?= ucfirst($value['modulename']); ?>">
                      <a href="<?= $baseUrl; ?><?= $value['modulename']; ?>/list?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>"
                        style="background: #f2f2f2;padding: 7px 12px;"><?= $value['modulelabel']; ?></a>
                    </li>
                    <!-- # code... -->


                    <?php
                    $i++;
                  } ?>
                </ul>
                <?php
              } else {
                echo "<div>
          </div><div>
          </div><div>
          </div>";
              } ?>
              <!-- <li class="nav__link">
        <a href="#" style="background: #b178fb;padding: 7px 12px;"><img src="<?= $baseUrl; ?>/thememain/img/calender.png" style="background: #b178fb;
  width: 17px;"></a>
      </li> -->
              <!--   <li class="nav__link" title="Attach Email">
              <a href="#" style="background: #5c9cff;padding: 7px 12px;"><img src="<?= $baseUrl; ?>/thememain/img/email-white.png" style="width: 17px;"></a>
            </li>
            <li class="nav__link" title="Attch Document">
              <a href="#" style="background: #a0a0a0;padding: 7px 10px;"><img src="<?= $baseUrl; ?>/thememain/img/pdf.png" style="background: #a0a0a0;
  width: 17px;"></a>
            </li>
            <li class="nav__link" title="Attach Note">
              <a href="#" style="background: #f8af92;padding: 7px 10px;"><img src="<?= $baseUrl; ?>thememain/img/33a94905-7956-4a9e-bd74-7ffb3b1d2b08.png" style="background: #f8af92;
  width: 17px;"></a>
            </li> -->

              <!-- <div> -->
              <!-- </div> -->
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
            <?php if (isset($anyMultipleBlockInModule) && $anyMultipleBlockInModule == "Multiple") { ?>
              <div class="container-mulitple-rec-module">
                <div id="summary" class="tab-content-detail-view active"><!-- Summary Section start-->
                  <div class="accordion">
                    <?php
                    foreach ($ColumnList->blocks as $BlockKey => $Block) {

                      // if (!empty($Block->detailfields) && $Block->blocktype != "Multiple") {
                      // adde on 14 jan 2025 to hide block with display_status == 0
                      if (!empty($Block->detailfields) && $Block->blocktype != "Multiple" && $Block->display_status = 1) {
                        ?>
                        <details class="c-faqs__item">
                          <summary class="c-faqs__item-question">
                            <?= $Block->blocklabel ?> <i class="fa-solid fa-angle-down"></i>
                          </summary>

                          <div class="row">

                            <?php
                            foreach ($Block->detailfields as $field) { ?>

                              <?php
                              if ($field["uitype"] == 12 || $field["uitype"] == 27 || $field["uitype"] == 28) {
                                $ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '';
                                $model1 = new Reference($TableName, $FieldId);
                                $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                                if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != '')
                                  $Record->{$field["columnname"]} = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
                                else
                                  $Record->{$field["columnname"]} = '';
                              } else if ($field["uitype"] == 8 || $field["uitype"] == 10) {
                                $modellist = new Listhire;
                                if (isset($Record->{$field["columnname"]}))
                                  $Record->{$field["columnname"]} = $modellist->getPickListDetailvalue($field["fieldid"], $Record->{$field["columnname"]});
                                else
                                  $Record->{$field["columnname"]};
                              } else if ($field["uitype"] == 6) { //checkbox
                                $modellist = new Listhire;
                                if (isset($Record->{$field["columnname"]})) {
                                  if ($Record->{$field["columnname"]} == 1)
                                    $Record->{$field["columnname"]} = "Yes";
                                  else if ($Record->{$field["columnname"]} == 0)
                                    $Record->{$field["columnname"]} = "No";
                                } else
                                  $Record->{$field["columnname"]};
                              } else if ($field["uitype"] == 22 || $field["uitype"] == 9) { //comma separated value
                                $modellist = new Listhire;
                                if (isset($Record->{$field["columnname"]}))
                                  $Record->{$field["columnname"]} = $modellist->getPickListDetailMultiple($field["fieldid"], $Record->{$field["columnname"]});
                                else
                                  $Record->{$field["columnname"]};
                              } else if ($field["uitype"] == 53) {
                                $modellist = new Listhire;
                                if (isset($Record->{$field["columnname"]}))
                                  $Record->{$field["columnname"]} = $modellist->getuser($field["fieldid"], $Record->{$field["columnname"]});
                                else
                                  $Record->{$field["columnname"]};
                              } else if ($field["uitype"] == 5) {
                                if ($Record->{$field["columnname"]}) {
                                  $records = \app\models\Attachments::find()
                                    ->where(['attachmentsid' => $Record->{$field["columnname"]}])
                                    ->one();
                                  //print_r($records);die;
                                  if ($records) {
                                    $Record->{$field["columnname"]} = "<br><a href='" . $baseUrl . $ModuleName . "/download?fileid=" . $Record->{$field["columnname"]} . "'>" . $records->name . "</a>";
                                  } else {
                                    $Record->{$field["columnname"]} = "";
                                  }
                                }
                              }
                              if ($field["columnname"] == "firstname") {
                                if (isset($Record->{$field["columnname"]})) {
                                  if (!empty($Record['salutation'])) {
                                    $modellist = new Listhire;

                                    $salutation = $modellist->getSalutation($Record['salutation']);

                                    $Record->{$field["columnname"]} = $salutation . " " . $Record->{$field["columnname"]};
                                  }
                                }
                              }
                              if ($field['columnname'] != "salutation") {
                                //check for related modules
                                if ($field['columnname'] == 'related_to') {
                                  //get modulename
                                  $module = \app\models\Tab::find()
                                    ->where(['tabid' => $Record->{$field["columnname"]}])
                                    ->one();
                                  $Record->{$field["columnname"]} = ucfirst($module->name);
                                }
                                if ($field['columnname'] == 'related_to_id') {
                                  $related = \app\models\Tab::find()
                                    ->where(['name' => strtolower($Record['related_to'])])
                                    ->one();
                                  $relatedtab = $related['tabid'];
                                  //get modulename
                                  $module = \app\models\Field::find()
                                    ->where(['tabid' => $relatedtab])
                                    ->andWhere(['headerview' => 1])
                                    ->one();
                                  $tablename = $module['tablename'];
                                  $model1 = new Reference($TableName, $FieldId);
                                  if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != '')
                                    $Record->{$field["columnname"]} = $model1->getMultiRefEntityValue($field["fieldid"], $Record->{$field["columnname"]}, $tablename);
                                  else
                                    $Record->{$field["columnname"]} = '';
                                }
                                $clsssdet = '';
                                if ($field['is_conditional'] == 1 && empty($Record->{$field["columnname"]})) {
                                  // $clsssdet = "tr-hidden";
                                  continue;
                                }
                                ?>

                                <div class="col-md-6 mb-3">
                                  <div class="d-flex">
                                    <div class="field-lable col-4" title="<?= !empty($field["description"]) ? $field["description"] : $field["fieldlabel"]?>"><?= $field["fieldlabel"]; ?></div>
                                    <div class="field-value col-8 ms-3">
                                      <?= isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "-" ?>
                                    </div>
                                  </div>
                                </div>
                                <?php
                              }
                            } ?>
                          </div>
                        </details>

                        <?php
                      } else {
                        //for multi record
                        if ($Block->blocktype == "Multiple" and !empty($Record)) {
                          // echo "<pre>";
                          // print_r($Block->detailfields);die;
                          $Multiple_table = $Block->detailfields[0]->tablename;
                          $modelname = convertToUcfirstOrPascalCase($Multiple_table);

                          $tbl = "app\models\\" . $modelname;
                          $newmod = new $tbl();
                          $MultiRecord = [];
                          if ($Multiple_table == 'product_costing_detail' && $Recordid) {
                            $MultiRecord = $newmod->find()->where(['product_costing_id' => $Recordid])->all();
                          } else if ($Multiple_table == 'grn_item_detail' && $Recordid) {
                            $MultiRecord = $newmod->find()->where(['grn_id' => $Recordid])->all();
                          } else if ($Multiple_table == 'purchase_order_itemsdetail' && $Recordid) {
                            $MultiRecord = $newmod->find()->where(['purchase_order_id' => $Recordid])->all();
                          } else {
                            $MultiRecord = $newmod->find()->where([$FieldId => $Recordid])->all();
                          }
                          $cnt_multiple_product = count($MultiRecord);
                        }

                        ?>
                        <details class="c-faqs__item">
                          <summary class="c-faqs__item-question">
                            <?= $Block->blocklabel ?> <i class="fa-solid fa-angle-down"></i>
                          </summary>

                          <div class="details-container">
                            <table class="table table-striped">
                              <thead>
                                <!-- figure out table headings -->
                                <tr>
                                  <?php
                                  if (isset($MultiRecord)) {
                                    foreach ($Block->detailfields as $field) { ?>
                                      <th>
                                        <?= $field["fieldlabel"] ?? ""; ?>
                                      </th>
                                      <?php
                                    }
                                  } ?>
                                </tr>
                                <!-- end ot thead  -->
                              </thead>
                              <tbody>
                                <?php
                                if (isset($MultiRecord)) {
                                  foreach ($MultiRecord as $MRecord) { ?>
                                    <tr>
                                      <?php
                                      foreach ($Block->detailfields as $field) { ?>

                                        <?php
                                        if ($field["uitype"] == 12 || $field["uitype"] == 27 || $field["uitype"] == 28) {
                                          $ref_hid_value = isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : '';
                                          $model1 = new Reference($TableName, $FieldId);
                                          $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                          $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                                          if (isset($MRecord->{$field["columnname"]}) && $MRecord->{$field["columnname"]} != '')
                                            $MRecord->{$field["columnname"]} = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
                                          else
                                            $MRecord->{$field["columnname"]} = '';
                                        } else if ($field["uitype"] == 8) {
                                          $modellist = new Listhire;
                                          if (isset($MRecord->{$field["columnname"]}))
                                            $MRecord->{$field["columnname"]} = $modellist->getPickListDetailvalue($field["fieldid"], $MRecord->{$field["columnname"]});
                                          else
                                            $MRecord->{$field["columnname"]};
                                        } else if ($field["uitype"] == 6) { //checkbox
                                          $modellist = new Listhire;
                                          if (isset($MRecord->{$field["columnname"]})) {
                                            if ($MRecord->{$field["columnname"]} == 1)
                                              $MRecord->{$field["columnname"]} = "Yes";
                                            else if ($MRecord->{$field["columnname"]} == 0)
                                              $MRecord->{$field["columnname"]} = "No";
                                          } else
                                            $MRecord->{$field["columnname"]};
                                        } else if ($field["uitype"] == 22 || $field["uitype"] == 9 || $field["uitype"] == 10) { //comma separated value
                                          $modellist = new Listhire;
                                          if (isset($MRecord->{$field["columnname"]}))
                                            $MRecord->{$field["columnname"]} = $modellist->getPickListDetailMultiple($field["fieldid"], $MRecord->{$field["columnname"]});
                                          else
                                            $MRecord->{$field["columnname"]};
                                        } else if ($field["uitype"] == 53) {
                                          $modellist = new Listhire;
                                          if (isset($MRecord->{$field["columnname"]}))
                                            $MRecord->{$field["columnname"]} = $modellist->getuser($field["fieldid"], $MRecord->{$field["columnname"]});
                                          else
                                            $MRecord->{$field["columnname"]};
                                          // } else if ($field["uitype"] == 5 && $TabLabel == 'Documents') {
                                        } else if ($field["uitype"] == 5) {
                                          if ($MRecord->{$field["columnname"]}) {
                                            $MRecords = \app\models\Attachments::find()
                                              ->where(['attachmentsid' => $MRecord->{$field["columnname"]}])
                                              ->one();
                                            //print_r($MRecords);die;
                                            $MRecord->{$field["columnname"]} = "<br>" . $MRecords->name . " <a href='" . $baseUrl . $ModuleName . "/download?fileid=" . $MRecord->{$field["columnname"]} . "'><i class='fa fa-download' aria-hidden='true' title='download'></i></a>";
                                          }
                                        }
                                        if ($field["columnname"] == "firstname") {
                                          if (isset($MRecord->{$field["columnname"]})) {
                                            // echo "deepika ".$MRecord['salutation'];die;   
                                            if (!empty($MRecord['salutation'])) {
                                              $modellist = new Listhire;

                                              $salutation = $modellist->getSalutation($MRecord['salutation']);

                                              $MRecord->{$field["columnname"]} = $salutation . " " . $MRecord->{$field["columnname"]};
                                            }
                                          }
                                        }
                                        if ($field['columnname'] != "salutation") {
                                          //$MRecord->{$field["columnname"]} ='';
                            
                                          //check for related modules
                                          if ($field['columnname'] == 'related_to') {
                                            //get modulename
                                            $module = \app\models\Tab::find()
                                              ->where(['tabid' => $MRecord->{$field["columnname"]}])
                                              ->one();
                                            $MRecord->{$field["columnname"]} = ucfirst($module->name);
                                          }
                                          if ($field['columnname'] == 'related_to_id') {
                                            $related = \app\models\Tab::find()
                                              ->where(['name' => strtolower($MRecord['related_to'])])
                                              ->one();
                                            $relatedtab = $related['tabid'];
                                            //get modulename
                                            $module = \app\models\Field::find()
                                              ->where(['tabid' => $relatedtab])
                                              ->andWhere(['headerview' => 1])
                                              ->one();
                                            //print_r($module);die;
                                            $tablename = $module['tablename'];
                                            //$columnname = $module['tablename'];
                            
                                            // $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                            // $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                                            $model1 = new Reference($TableName, $FieldId);
                                            if (isset($MRecord->{$field["columnname"]}) && $MRecord->{$field["columnname"]} != '')
                                              $MRecord->{$field["columnname"]} = $model1->getMultiRefEntityValue($field["fieldid"], $MRecord->{$field["columnname"]}, $tablename);
                                            else
                                              $MRecord->{$field["columnname"]} = '';

                                            // //get primary key
                                            // Yii::$app->db->createCommand("select $columnname from $tablename where ");
                                            //$MRecord->{$field["columnname"]}= ucfirst($module->tablename);
                                          }
                                          ?>
                                          <td>
                                            <?= isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : "-" ?>
                                          </td>
                                          <?php
                                        }
                                      } ?>
                                    </tr>
                                    <?php
                                  }
                                } ?>
                              </tbody>
                            </table>
                          </div>
                        </details>
                        <?php
                      }
                    } ?>
                  </div>
                </div><!-- Summary Section end-->
                <div id="history" class="tab-content-detail-view"><!-- History Section start-->
                  <div class="history-section">
                    <div class="activity-1">History</div>
                    <div class="timeline-1w">
                      <?php //echo "<pre>"; print_r($Detailhistory);die;
                        foreach ($Detailhistory as $key): ?>
                        <?php if (isset($key['basic']['status']) && ($key['basic']['status'] == 'Created' || $key['basic']['status'] == 'Lead Converted')): ?>
                          <div class="timeline-event">
                            <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i></div>
                            <div class="timeline-details">
                              <p>
                                <?php echo htmlspecialchars($key['basic']['whodid']); ?>
                                <span class="timeline-tsk"><?php echo htmlspecialchars($key['basic']['status']); ?></span><br>
                                This <?php echo $TabLabel; ?>
                              </p>
                              <p>
                                <?php
                                $datetime = new DateTime($key['basic']['changedon']);
                                echo $datetime->format('M d, Y \A\t g.i A');
                                ?>
                              </p>
                            </div>
                          </div>
                        <?php elseif (isset($key['basic']['status']) && ($key['basic']['status'] == 'Added')): ?>
                          <div class="timeline-event">
                            <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i></div>
                            <div class="timeline-details">
                              <p>
                                <?php echo htmlspecialchars($key['basic']['whodid']); ?>
                                <span class="timeline-tsk"><?php echo htmlspecialchars($key['basic']['status']); ?></span>
                                <?php echo htmlspecialchars(ucfirst($key['basic']['targetmodule'])); ?>
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
                          <?php if (isset($key['details'][0])):
                            //print_r($key['details'][0]['postvalues']); 
                            ?>
                            <?php
                            $ids = explode('~', $key['details'][0]['ids'] ?? '');
                            $fieldnames = explode('~', $key['details'][0]['fieldnames'] ?? '');
                            $fieldlabels = explode('~', $key['details'][0]['fieldlabels'] ?? '');
                            $prevalues = explode('~', $key['details'][0]['prevalues'] ?? '');
                            $postvalues = explode('~', $key['details'][0]['postvalues'] ?? '');
                            $uitypes = explode('~', $key['details'][0]['uitypes'] ?? '');
                            $fieldids = explode('~', $key['details'][0]['fieldids'] ?? '');
                            ?>
                            <div class="timeline-event">
                              <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i></div>
                              <div class="timeline-details">
                                <p>
                                  <?php echo htmlspecialchars($key['basic']['whodid']); ?>
                                  <span class="timeline-tsk"><?php echo htmlspecialchars($key['basic']['status']); ?></span>
                                </p>
                                <?php

                                foreach ($fieldlabels as $index => $label): ?>
                                  <?php
                                  // echo  $label;
                                  // print_r($prevalues);die;
                                  $prevalue = isset($prevalues[$index]) ? $prevalues[$index] : 'N/A';
                                  $postvalue = isset($postvalues[$index]) ? $postvalues[$index] : 'N/A';
                                  $uitype = isset($uitypes[$index]) ? $uitypes[$index] : 'N/A';
                                  $fieldid = isset($fieldids[$index]) ? $fieldids[$index] : 'N/A';

                                  if ($uitype == 12 || $uitype == 28 || $uitype == 27) {

                                    //prevlaue
                                    $ref_hid_value = isset($prevalue) ? $prevalue : '';
                                    $model1 = new Reference($TableName, $FieldId);
                                    $relatedmodulename = $model1->getRelatedNoduleName($fieldid);
                                    $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($fieldid);
                                    if (isset($prevalue) && $prevalue != '')
                                      $prevalue = $model1->getRefEntityValue($fieldid, $ref_hid_value);
                                    else
                                      $prevalue = '';

                                    //postvalue
                                    //prevlaue
                                    $ref_hid_value = isset($postvalue) ? $postvalue : '';
                                    $model1 = new Reference($TableName, $FieldId);
                                    $relatedmodulename = $model1->getRelatedNoduleName($fieldid);
                                    $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($fieldid);
                                    if (isset($postvalue) && $postvalue != '')
                                      $postvalue = $model1->getRefEntityValue($fieldid, $ref_hid_value);
                                    else
                                      $postvalue = '';
                                  } else if ($uitype == 6) { //checkbox
                                    $modellist = new Listhire;
                                    if (isset($prevalue)) {
                                      if ($prevalue == 0)
                                        $prevalue = "No";
                                      else if ($prevalue == 1)
                                        $prevalue = "Yes";
                                    } else
                                      $prevalue = '';
                                    //postvalue
                                    if (isset($postvalue)) {
                                      if ($postvalue == 0)
                                        $postvalue = "No";
                                      else if ($postvalue == 1)
                                        $postvalue = "Yes";
                                    } else
                                      $postvalue = '';
                                  } else if ($uitype == 8 || $uitype == 10) {
                                    $modellist = new Listhire;
                                    if (isset($$prevalue))
                                      $prevalue = $modellist->getPickListDetailvalue($fieldid, $prevalue);
                                    else
                                      $prevalue = '';
                                    //postvalue
                                    if (isset($postvalue))
                                      $postvalue = $modellist->getPickListDetailvalue($fieldid, $postvalue);
                                    else
                                      $postvalue = '';
                                  } else if ($uitype == 53) {

                                    $modellist = new Listhire;
                                    if (isset($prevalue))
                                      $prevalue = $modellist->getuser($fieldid, $prevalue);
                                    else
                                      $prevalue;
                                    if (isset($postvalue))
                                      $postvalue = $modellist->getuser($fieldid, $postvalue);
                                    else
                                      $postvalue;
                                  } else if ($uitype == 22 || $uitype == 9) { //comma separated value
                                    // echo $postvalue;die;
                                    $modellist = new Listhire;
                                    if (isset($prevalue))
                                      $prevalue = $modellist->getPickListDetailMultiple($fieldid, $prevalue);
                                    else
                                      $prevalue;
                                    if (isset($postvalue))
                                      $postvalue = $modellist->getPickListDetailMultiple($fieldid, $postvalue);
                                    else
                                      $postvalue;
                                  }

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
                      <?php endforeach; //die; 
                        ?>
                    </div>

                  </div>
                </div><!-- History Section end-->
              </div><!-- end of container-mulitple-rec-module -->
              <?php
              if ($module_type != "master") { ?>
                <div class="row">
                  <?php
                  if (!empty($centerModules)) { ?>
                    <div class="col-md-12">
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
                              <span class="add-meeting"><img src="<?= $baseUrl; ?>/thememain/img/add-meeting.png"> Add
                                Meeting</span>
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
                                    <?php foreach ($allactivities as $activity):

                                      ?>

                                      <li class="phone-event-detail" style="height:auto">
                                        <?php if ($activity['activity_type'] === 'call'): ?>
                                          <img alt="Call" src="<?= $baseUrl; ?>/thememain/img/call-icon.png" />
                                        <?php elseif ($activity['activity_type'] === 'meeting'): ?>
                                          <img alt="Meeting" src="<?= $baseUrl; ?>/thememain/img/meeting-icon.png" />
                                        <?php elseif ($activity['activity_type'] === 'task'): ?>
                                          <img alt="Task" src="<?= $baseUrl; ?>/thememain/img/task-icon.png" />
                                        <?php endif; ?>

                                        <div class="info">
                                          <h2 class="title" style="color: #5c9cff;">
                                            <?= ucfirst($activity['activity_type']); ?>
                                          </h2>
                                          <p class="desc"><?= $activity['activity_description']; ?></p>
                                        </div>

                                        <div class="info-2">
                                          <?php
                                          $currentDate = date('Y-m-d');
                                          $tomorrowDate = date('Y-m-d', strtotime('+1 day'));
                                          $activityDate = date('Y-m-d', strtotime($activity['activity_date']));
                                          $formattedTime = date('g:i a', strtotime($activity['activity_date']));

                                          if ($activityDate === $currentDate): ?>
                                            <span>Today at <?= $formattedTime; ?></span>
                                          <?php elseif ($activityDate === $tomorrowDate): ?>
                                            <span>Tomorrow at <?= $formattedTime; ?></span>
                                          <?php else: ?>
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
                    </div>
                    <?php
                  } ?>
                
                </div>
                <?php
              } ?>
            <?php } else { ?>
              <!-- Summary Section -->
              <div class="container-m">
                <!-- Left Section -->
                <div class="left-section">
                  <div id="summary" class="tab-content-detail-view active">
                    <div class="accordion">
                      <?php
                      foreach ($ColumnList->blocks as $BlockKey => $Block) {

                        // if (!empty($Block->detailfields) && $Block->blocktype != "Multiple") {
                        // added on 14 jan 2025 to hide block with display_status == 0
                        if (!empty($Block->detailfields) && $Block->blocktype != "Multiple" && 
                        $Block->display_status == '1') {
                        
                          ?>
                          <details class="c-faqs__item">
                            <summary class="c-faqs__item-question">
                              <?= $Block->blocklabel ?> <i class="fa-solid fa-angle-down"></i>
                            </summary>

                            <div class="details-container">

                              <?php
                              foreach ($Block->detailfields as $field) { ?>

                                <?php
                                if ($field["uitype"] == 12 || $field["uitype"] == 27 || $field["uitype"] == 28) {
                                  $ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '';
                                  $model1 = new Reference($TableName, $FieldId);
                                  $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                  $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                                  if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != '')
                                    $Record->{$field["columnname"]} = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
                                  else
                                    $Record->{$field["columnname"]} = '';
                                } else if ($field["uitype"] == 8 || $field["uitype"] == 10) {
                                  $modellist = new Listhire;
                                  if (isset($Record->{$field["columnname"]}))
                                    $Record->{$field["columnname"]} = $modellist->getPickListDetailvalue($field["fieldid"], $Record->{$field["columnname"]});
                                  else
                                    $Record->{$field["columnname"]};
                                } else if ($field["uitype"] == 6) { //checkbox
                                  $modellist = new Listhire;
                                  if (isset($Record->{$field["columnname"]})) {
                                    if ($Record->{$field["columnname"]} == 1)
                                      $Record->{$field["columnname"]} = "Yes";
                                    else if ($Record->{$field["columnname"]} == 0)
                                      $Record->{$field["columnname"]} = "No";
                                  } else
                                    $Record->{$field["columnname"]};
                                } else if ($field["uitype"] == 22 || $field["uitype"] == 9) { //comma separated value
                                  $modellist = new Listhire;
                                  if (isset($Record->{$field["columnname"]}))
                                    $Record->{$field["columnname"]} = $modellist->getPickListDetailMultiple($field["fieldid"], $Record->{$field["columnname"]});
                                  else
                                    $Record->{$field["columnname"]};
                                } else if ($field["uitype"] == 53) {
                                  $modellist = new Listhire;
                                  if (isset($Record->{$field["columnname"]}))
                                    $Record->{$field["columnname"]} = $modellist->getuser($field["fieldid"], $Record->{$field["columnname"]});
                                  else
                                    $Record->{$field["columnname"]};
                                  // } else if ($field["uitype"] == 5 && $TabLabel == 'Documents') {
                                } else if ($field["uitype"] == 5) {

                                  if ($Record->{$field["columnname"]}) {
                                    if ($field["columnname"] == 'profilepic' && !empty($Record->{$field["columnname"]})) {
                                      $Record->{$field["columnname"]} = "<br><img src='" . $baseUrl . $Record->{$field["columnname"]} . "' height ='150' width = '150'/>";
                                    } else {
                                      $records = \app\models\Attachments::find()
                                        ->where(['attachmentsid' => $Record->{$field["columnname"]}])
                                        ->one();
                                      //print_r($records);die;
                                      $Record->{$field["columnname"]} = "<br>" . $records->name . " <a href='" . $baseUrl . $ModuleName . "/download?fileid=" . $Record->{$field["columnname"]} . "'><i class='fa fa-download' aria-hidden='true' title='download'></i></a>";
                                    }
                                  }
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
                        
                                  //check for related modules
                                  if ($field['columnname'] == 'related_to') {
                                    //get modulename
                                    $module = \app\models\Tab::find()
                                      ->where(['tabid' => $Record->{$field["columnname"]}])
                                      ->one();
                                    $Record->{$field["columnname"]} = ucfirst($module->name);
                                  }
                                  if ($field['columnname'] == 'related_to_id') {
                                    $related = \app\models\Tab::find()
                                      ->where(['name' => strtolower($Record['related_to'])])
                                      ->one();
                                    $relatedtab = $related['tabid'];
                                    //get modulename
                                    $module = \app\models\Field::find()
                                      ->where(['tabid' => $relatedtab])
                                      ->andWhere(['headerview' => 1])
                                      ->one();
                                    //print_r($module);die;
                                    $tablename = $module['tablename'];
                                    //$columnname = $module['tablename'];
                        
                                    // $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                    // $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                                    $model1 = new Reference($TableName, $FieldId);
                                    if (isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} != '')
                                      $Record->{$field["columnname"]} = $model1->getMultiRefEntityValue($field["fieldid"], $Record->{$field["columnname"]}, $tablename);
                                    else
                                      $Record->{$field["columnname"]} = '';

                                    // //get primary key
                                    // Yii::$app->db->createCommand("select $columnname from $tablename where ");
                                    //$Record->{$field["columnname"]}= ucfirst($module->tablename);
                                  }
                                  //added on 21/dec/2024 by deepika show if conditional
                                  $clsssdet = '';
                                  if ($field['is_conditional'] == 1 && empty($Record->{$field["columnname"]})) {
                                    // $clsssdet = "tr-hidden";
                                    continue;
                                  }

                                  ?>

                                  <div class="detail-group Details-1  detail-<?= $field["columnname"]; ?> <?= $clsssdet; ?>">
                                    <label title="<?= !empty($field["description"]) ? $field["description"] : $field["fieldlabel"]?>"><?= $field["fieldlabel"]; ?></label>
                                    <span><?= isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "-" ?></span>
                                  </div>

                                  <?php
                                }
                              } ?>
                            </div>
                          </details>

                          <?php
                        } elseif($Block->display_status == '1')  {
                          //for multi record
                          
                          if ($Block->blocktype == "Multiple" and !empty($Record)) {
                            // echo "<pre>";
                            // print_r($Block->detailfields);die;
                            $Multiple_table = $Block->detailfields[0]->tablename;
                            $modelname = convertToUcfirstOrPascalCase($Multiple_table);

                            $tbl = "app\models\\" . $modelname;
                            $newmod = new $tbl();
                            $MultiRecord = [];
                            if ($Multiple_table == 'product_costing_detail') {
                              $MultiRecord = $newmod->find()->where(['product_costing_id' => $Record])->all();
                            } else if ($Multiple_table == 'grn_item_detail') {
                              $MultiRecord = $newmod->find()->where(['grn_id' => $Record])->all();
                            } else if ($Multiple_table == 'purchase_order_itemsdetail') {
                              $MultiRecord = $newmod->find()->where(['purchase_order_id' => $Record])->all();
                            }
                            $cnt_multiple_product = count($MultiRecord);
                            // echo $cnt_multiple_product;die;
                    
                          }

                          ?>
                          <details class="c-faqs__item">
                            <summary class="c-faqs__item-question">
                              <?= $Block->blocklabel ?> <i class="fa-solid fa-angle-down"></i>
                            </summary>

                            <div class="details-container">

                              <?php
                              if (isset($MultiRecord)) {
                                foreach ($MultiRecord as $MRecord) {
                                  foreach ($Block->detailfields as $field) { ?>

                                    <?php
                                    if ($field["uitype"] == 12 || $field["uitype"] == 27 || $field["uitype"] == 28) {
                                      $ref_hid_value = isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : '';
                                      $model1 = new Reference($TableName, $FieldId);
                                      $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                      $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                                      if (isset($MRecord->{$field["columnname"]}) && $MRecord->{$field["columnname"]} != '')
                                        $MRecord->{$field["columnname"]} = $model1->getRefEntityValue($field["fieldid"], $ref_hid_value);
                                      else
                                        $MRecord->{$field["columnname"]} = '';
                                    } else if ($field["uitype"] == 8) {
                                      $modellist = new Listhire;
                                      if (isset($MRecord->{$field["columnname"]}))
                                        $MRecord->{$field["columnname"]} = $modellist->getPickListDetailvalue($field["fieldid"], $MRecord->{$field["columnname"]});
                                      else
                                        $MRecord->{$field["columnname"]};
                                    } else if ($field["uitype"] == 6) { //checkbox
                                      $modellist = new Listhire;
                                      if (isset($MRecord->{$field["columnname"]})) {
                                        if ($MRecord->{$field["columnname"]} == 1)
                                          $MRecord->{$field["columnname"]} = "Yes";
                                        else if ($MRecord->{$field["columnname"]} == 0)
                                          $MRecord->{$field["columnname"]} = "No";
                                      } else
                                        $MRecord->{$field["columnname"]};
                                    } else if ($field["uitype"] == 22 || $field["uitype"] == 9 || $field["uitype"] == 10) { //comma separated value
                                      $modellist = new Listhire;
                                      if (isset($MRecord->{$field["columnname"]}))
                                        $MRecord->{$field["columnname"]} = $modellist->getPickListDetailMultiple($field["fieldid"], $MRecord->{$field["columnname"]});
                                      else
                                        $MRecord->{$field["columnname"]};
                                    } else if ($field["uitype"] == 53) {
                                      $modellist = new Listhire;
                                      if (isset($MRecord->{$field["columnname"]}))
                                        $MRecord->{$field["columnname"]} = $modellist->getuser($field["fieldid"], $MRecord->{$field["columnname"]});
                                      else
                                        $MRecord->{$field["columnname"]};
                                    } else if ($field["uitype"] == 5 && $TabLabel == 'Documents') {
                                      if ($MRecord->{$field["columnname"]}) {
                                        $MRecords = \app\models\Attachments::find()
                                          ->where(['attachmentsid' => $MRecord->{$field["columnname"]}])
                                          ->one();
                                        //print_r($MRecords);die;
                                        $MRecord->{$field["columnname"]} = "<br><a href='" . $baseUrl . $ModuleName . "/download?fileid=" . $MRecord->{$field["columnname"]} . "'>" . $MRecords->name . "</a>";
                                      }
                                    }
                                    if ($field["columnname"] == "firstname") {
                                      if (isset($MRecord->{$field["columnname"]})) {
                                        // echo "deepika ".$MRecord['salutation'];die;   
                                        if (!empty($MRecord['salutation'])) {
                                          $modellist = new Listhire;

                                          $salutation = $modellist->getSalutation($MRecord['salutation']);

                                          $MRecord->{$field["columnname"]} = $salutation . " " . $MRecord->{$field["columnname"]};
                                        }
                                      }
                                    }
                                    if ($field['columnname'] != "salutation") {
                                      //$MRecord->{$field["columnname"]} ='';
                        
                                      //check for related modules
                                      if ($field['columnname'] == 'related_to') {
                                        //get modulename
                                        $module = \app\models\Tab::find()
                                          ->where(['tabid' => $MRecord->{$field["columnname"]}])
                                          ->one();
                                        $MRecord->{$field["columnname"]} = ucfirst($module->name);
                                      }
                                      if ($field['columnname'] == 'related_to_id') {
                                        $related = \app\models\Tab::find()
                                          ->where(['name' => strtolower($MRecord['related_to'])])
                                          ->one();
                                        $relatedtab = $related['tabid'];
                                        //get modulename
                                        $module = \app\models\Field::find()
                                          ->where(['tabid' => $relatedtab])
                                          ->andWhere(['headerview' => 1])
                                          ->one();
                                        //print_r($module);die;
                                        $tablename = $module['tablename'];
                                        //$columnname = $module['tablename'];
                        
                                        // $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                                        // $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($field["fieldid"]);
                                        $model1 = new Reference($TableName, $FieldId);
                                        if (isset($MRecord->{$field["columnname"]}) && $MRecord->{$field["columnname"]} != '')
                                          $MRecord->{$field["columnname"]} = $model1->getMultiRefEntityValue($field["fieldid"], $MRecord->{$field["columnname"]}, $tablename);
                                        else
                                          $MRecord->{$field["columnname"]} = '';

                                        // //get primary key
                                        // Yii::$app->db->createCommand("select $columnname from $tablename where ");
                                        //$MRecord->{$field["columnname"]}= ucfirst($module->tablename);
                                      }
                                      ?>

                                      <div class="detail-group Details-1">
                                        <label title="<?= !empty($field["description"]) ? $field["description"] : $field["fieldlabel"]?>"><?= $field["fieldlabel"]; ?></label>
                                        <span><?= isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : "-" ?></span>
                                      </div>

                                      <?php
                                    }
                                  }
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
                        <?php //echo "<pre>"; print_r($Detailhistory);die;
                          foreach ($Detailhistory as $key): ?>
                          <?php if (isset($key['basic']['status']) && ($key['basic']['status'] == 'Created')): ?>
                            <div class="timeline-event">
                              <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i></div>
                              <div class="timeline-details">
                                <p>
                                  <?php echo htmlspecialchars($key['basic']['whodid']); ?>
                                  <span
                                    class="timeline-tsk"><?php echo htmlspecialchars($key['basic']['status']); ?></span><br>
                                  This <?php echo $TabLabel; ?>
                                </p>
                                <p>
                                  <?php
                                  $datetime = new DateTime($key['basic']['changedon']);
                                  echo $datetime->format('M d, Y \A\t g.i A');
                                  ?>
                                </p>
                              </div>
                            </div>
                          <?php elseif (isset($key['basic']['status']) && ($key['basic']['status'] == 'Added')): ?>
                            <div class="timeline-event">
                              <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i></div>
                              <div class="timeline-details">
                                <p>
                                  <?php echo htmlspecialchars($key['basic']['whodid']); ?>
                                  <span class="timeline-tsk"><?php echo htmlspecialchars($key['basic']['status']); ?></span>
                                  <?php echo htmlspecialchars(ucfirst($key['basic']['targetmodule'])); ?>
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
                            <?php if (isset($key['details'][0])):
                              //print_r($key['details'][0]['postvalues']); 
                              ?>
                              <?php
                              $ids = explode('~', $key['details'][0]['ids'] ?? '');
                              $fieldnames = explode('~', $key['details'][0]['fieldnames'] ?? '');
                              $fieldlabels = explode('~', $key['details'][0]['fieldlabels'] ?? '');
                              $prevalues = explode('~', $key['details'][0]['prevalues'] ?? '');
                              $postvalues = explode('~', $key['details'][0]['postvalues'] ?? '');
                              $uitypes = explode('~', $key['details'][0]['uitypes'] ?? '');
                              $fieldids = explode('~', $key['details'][0]['fieldids'] ?? '');
                              ?>
                              <div class="timeline-event">
                                <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i></div>
                                <div class="timeline-details">
                                  <p>
                                    <?php echo htmlspecialchars($key['basic']['whodid']); ?>
                                    <span class="timeline-tsk"><?php echo htmlspecialchars($key['basic']['status']); ?></span>
                                  </p>
                                  <?php

                                  foreach ($fieldlabels as $index => $label): ?>
                                    <?php
                                    // echo  $label;
                                    // print_r($prevalues);die;
                                    $prevalue = isset($prevalues[$index]) ? $prevalues[$index] : 'N/A';
                                    $postvalue = isset($postvalues[$index]) ? $postvalues[$index] : 'N/A';
                                    $uitype = isset($uitypes[$index]) ? $uitypes[$index] : 'N/A';
                                    $fieldid = isset($fieldids[$index]) ? $fieldids[$index] : 'N/A';

                                    if ($uitype == 12 || $uitype == 28 || $uitype == 27) {

                                      //prevlaue
                                      $ref_hid_value = isset($prevalue) ? $prevalue : '';
                                      $model1 = new Reference($TableName, $FieldId);
                                      $relatedmodulename = $model1->getRelatedNoduleName($fieldid);
                                      $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($fieldid);
                                      if (isset($prevalue) && $prevalue != '')
                                        $prevalue = $model1->getRefEntityValue($fieldid, $ref_hid_value);
                                      else
                                        $prevalue = '';

                                      //postvalue
                                      //prevlaue
                                      $ref_hid_value = isset($postvalue) ? $postvalue : '';
                                      $model1 = new Reference($TableName, $FieldId);
                                      $relatedmodulename = $model1->getRelatedNoduleName($fieldid);
                                      $getRelatedDisplayFieldName = $model1->getRelatedDisplayFieldName($fieldid);
                                      if (isset($postvalue) && $postvalue != '')
                                        $postvalue = $model1->getRefEntityValue($fieldid, $ref_hid_value);
                                      else
                                        $postvalue = '';
                                    } else if ($uitype == 6) { //checkbox
                                      $modellist = new Listhire;
                                      if (isset($prevalue)) {
                                        if ($prevalue == 0)
                                          $prevalue = "No";
                                        else if ($prevalue == 1)
                                          $prevalue = "Yes";
                                      } else
                                        $prevalue = '';
                                      //postvalue
                                      if (isset($postvalue)) {
                                        if ($postvalue == 0)
                                          $postvalue = "No";
                                        else if ($postvalue == 1)
                                          $postvalue = "Yes";
                                      } else
                                        $postvalue = '';
                                    } else if ($uitype == 8 || $uitype == 10) {
                                      $modellist = new Listhire;
                                      if (isset($prevalue))
                                        $prevalue = $modellist->getPickListDetailvalue($fieldid, $prevalue);
                                      else
                                        $prevalue = '';
                                      //postvalue
                                      if (isset($postvalue))
                                        $postvalue = $modellist->getPickListDetailvalue($fieldid, $postvalue);
                                      else
                                        $postvalue = '';
                                    } else if ($uitype == 53) {

                                      $modellist = new Listhire;
                                      if (isset($prevalue))
                                        $prevalue = $modellist->getuser($fieldid, $prevalue);
                                      else
                                        $prevalue;
                                      if (isset($postvalue))
                                        $postvalue = $modellist->getuser($fieldid, $postvalue);
                                      else
                                        $postvalue;
                                    } else if ($uitype == 22 || $uitype == 9) { //comma separated value
                                      // echo $postvalue;die;
                                      $modellist = new Listhire;
                                      if (isset($prevalue))
                                        $prevalue = $modellist->getPickListDetailMultiple($fieldid, $prevalue);
                                      else
                                        $prevalue;
                                      if (isset($postvalue))
                                        $postvalue = $modellist->getPickListDetailMultiple($fieldid, $postvalue);
                                      else
                                        $postvalue;
                                    } else if ($uitype == 13) {
                                      $postvalue = str_replace("T", " ", $postvalue);
                                    }

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
                        <?php endforeach; //die; 
                          ?>
                      </div>

                    </div>
                  </div>
                </div>
                <?php
                if ($module_type != "master") {
                  if (!empty($centerModules)) { ?>
                    <!-- Center Section -->
                    <div class="center-section">
                      <!-- <div class="col-md-4 w-2-section"> -->
                      <div>
                        <div class="activity-1">Activity</div>
                        <div class="flex-row-f-section-w">

                          <button class="frame-50-1" id="open-call-btn" title="Add Calls">

                            <span class="add-calls"><img src="<?= $baseUrl; ?>/thememain/img/phone.png"> Calls </span>
                          </button>
                          <button class="frame-52-1" id="open-meeting-btn" title="Add Meeting">
                            <span class="add-meeting"><img src="<?= $baseUrl; ?>/thememain/img/add-meeting.png"> 
                              Meeting</span>
                          </button>
                          <button class="frame-54-1" id="open-task-btn" title="Add Task">
                            <span class="add-task"><img src="<?= $baseUrl; ?>/thememain/img/add-task.png"> Task</span>
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
                                  <?php foreach ($allactivities as $activity):

                                    ?>

                                    <li class="phone-event-detail" style="height:auto">
                                      <?php if ($activity['activity_type'] === 'call'): ?>
                                        <img alt="Call" src="<?= $baseUrl; ?>/thememain/img/call-icon.png" />
                                      <?php elseif ($activity['activity_type'] === 'meeting'): ?>
                                        <img alt="Meeting" src="<?= $baseUrl; ?>/thememain/img/meeting-icon.png" />
                                      <?php elseif ($activity['activity_type'] === 'task'): ?>
                                        <img alt="Task" src="<?= $baseUrl; ?>/thememain/img/task-icon.png" />
                                      <?php endif; ?>

                                      <div class="info">
                                        <h2 class="title" style="color: #5c9cff;">
                                          <?= ucfirst($activity['activity_type']); ?>
                                        </h2>
                                        <p class="desc"><?= $activity['activity_description']; ?></p>
                                      </div>

                                      <div class="info-2">
                                        <?php
                                        $currentDate = date('Y-m-d');
                                        $tomorrowDate = date('Y-m-d', strtotime('+1 day'));
                                        $activityDate = date('Y-m-d', strtotime($activity['activity_date']));
                                        $formattedTime = date('g:i a', strtotime($activity['activity_date']));

                                        if ($activityDate === $currentDate): ?>
                                          <span>Today at <?= $formattedTime; ?></span>
                                        <?php elseif ($activityDate === $tomorrowDate): ?>
                                          <span>Tomorrow at <?= $formattedTime; ?></span>
                                        <?php else: ?>
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
                    <?php
                  }

                } ?>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>

      <!-- related documents section open -->
      <?php
      if (!empty($docModules)) {
        ?>
        <div class="container-d <?php //if($i==1) echo 'active'; 
          ?>" id="<?php //$value['related_module'].$value['modulename']; 
            ?>">

          <?php
          echo $this->render('Relateddocs', ['baseUrl' => $baseUrl, 'docrecords' => $docrecords, 'ModuleName' => $ModuleName]);



          ?>
        </div>

        <?php

      } ?>
      <!-- related documents section close -->

    </div>
  </div>

  <div class="col-4 container-d mt-3 detail-right">
    <!-- quill notes bhavitha -->
    <h2>Quill.js with @Mentions</h2>
    <div id="editor-container"></div>
    <div id="mention-list" class="mention-list"></div>
    <!-- end quill notes bhavitha -->
    <?php
    // if (!empty($rightModules)) { ?>

    <!-- Right Section -->
    <div>
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
                  <span>
                    <a href="#">
                      <img src="<?= $baseUrl; ?>thememain/img/33a94905-7956-4a9e-bd74-7ffb3b1d2b08.png"
                        style="background: #f8af92;width: 6%;padding:3px;" />
                    </a>

                    <div class="less-content">
                      <?= $notedesc; ?>
                      <?php
                      // Check if the full note is longer than the truncated version
                      if (strlen($notedescfull) > strlen($notedesc)) {
                        ?>
                        <p class="dots">...</p>
                      </div>

                      <div class="more-content"><?php echo $notedescfull; ?></div>
                      <button class="btn btn-primary read-more-btn">Read More</button>
                      <?php
                      } ?>
                    <br>
                    <?= $p; ?>
                </div>
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

  <?php
  // } ?>
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
          <input type="hidden" name="leadstatus_v" id="leadstatus_v" value="13">

          <div class="mb-3">
            <label for="approve_comment" class="form-label">Comment</label>
            <textarea id="approve_comment" class="form-control" rows="4"
              placeholder="Add your comment here..."></textarea>
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
                <option value="<?= $value['id']; ?>"><?= $value['showfield']; ?> (<?= $value['email']; ?>)</option>
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
            <textarea id="modify_comment" class="form-control" rows="4"
              placeholder="Add your comment here..."></textarea>
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

 
");


?>
<script type="text/javascript">

</script>