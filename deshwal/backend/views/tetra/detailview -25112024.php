<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;
use app\models\ListHire;
use app\models\Reference;

AdminAsset::register($this);
$this->title = Yii::t('app', $Tabname. " Detail");

$url =Url::to(['Edit']);
$urlApprove =Url::to(['approvelead']);
$this->registerCssFile('@web/thememain/css/listview.css', ['depends' => [AdminAsset::class]]);
$baseUrl = Yii::$app->HomeUrl; 
$module = strtolower($ModuleName);
$Recordid = $Recordid;
// echo "<pre>";
// print_r($Record);
$fullname = $Record["firstname"]." ".$Record["lastname"];
$csrfTokenName = Yii::$app->request->csrfParam;  // This replaces csrfTokenName
$csrfToken = Yii::$app->request->csrfToken;      // Get the CSRF token itself

//get lead stages
if($Tabname == "Leads")
{
    //get lead status
    $sql = "select * from lead_status where is_active =1 and leadstatusid not in (6,7,8,9) order by seq_no ";
    $getleads = Yii::$app->db->createCommand($sql)->queryAll();

}
?>

<div class="page-content">
         <div class="records table-responsive">
            <div class="record-header">
               <div class="add">
                  <img src="<?= $baseUrl;?>/thememain/img/lead_svgrepo.com.svg" class=" head-img">
                  <span class="sm-modname">Leads</span>
                  <br>
                  <span class="fullname"><?= $fullname; ?></span>
               </div>

              
               <div class="">
                  
                <?php
                if($Record['vertical_manager'] == Yii::$app->user->id)
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
                else if($Record['ownerid'] == Yii::$app->user->id)
                {
                ?>
                <div class="div-regroup">
                        
                            <?php
                            if($Record['leadstatus'] == 13)//show only when qualified
                            {?>
                            <button class="button-frame"><span class="span-convert">Convert</span></button>
                                <?php
                            }?>
                             <?php
                            if($Record['leadstatus'] != 4 && $Record['leadstatus'] != 13)//cant edit if send for approval or qualified
                            {?>
                            <button class="button-frame-38" id="add-lead-btn"><span class="span-edit" id="add-lead-btn">Edit</span> </button>
                            <?php
                            }?>
                       
                         <div class="div-frame">
                        <span class="span-more">More</span>
                        <div class="div-mdi-menu-down">
                            <div class="div-vector-39"></div>
                        </div>
                    </div>
                 
                </div>
                <?php
                }?>

               </div>
            </div>
         </div>
</div>
<div class="select-1">
   <div class="container-d">
      <div class="col-md-12">
         <div class="pipeline-container">
            <div class="rectangle-54">
               <div class="pip-1">
                  <h2 class="lead-pipeline-status">Lead pipeline Status</h2>
                  <div class="flex-row-e">
                    <!-- get lead stages -->
                    <?php
                    $cn = 1;
                    foreach ($getleads as $key => $value) {
                        # code...
                        if($Record['leadstatus'] == $value['leadstatusid'])
                        $st = "green";
                        else $st ="gray"; 
                        $bt = "mid";
                        if($cn == 1)
                        {
                            $bt="start";
                            $class_ini = "rectangle-start-";
                        }
                        else
                            $class_ini = "rectangle-mid-";

                        $finclass = $class_ini.$st;

                        $cn++;
                        ?>
                       <!--  <div class="col-sm-2">
                        <div class="rectangle-start-gray">
                           <span class="not-contacted">New </span>
                        </div>
                         </div> -->
                         <div class="col-sm-2">
                            <button class="<?= $finclass;?> leaddurationparent <?= $value['leadstatus_value'];?>" data-id="<?= $value['leadstatusid'];?>" data-bt="<?= $bt; ?>" data-cl="<?= $finclass; ?>" style="border: none;">
                               <span class="not-contacted"><?= $value['leadstatus_value'];?></span>
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
                    foreach ($getleads as $key => $value) {
                        if($Record['leadstatus'] == $value['leadstatusid'])
                        $stclass = "";
                        else $stclass ="tr-hidden";
                        //lead history
                        //get prevalue

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
                            ->bindValue(':fieldname1', 'leadstatus') // Bind the fieldname parameter
                            ->bindValue(':prevalue', $value['leadstatusid']) // Bind the prevalue parameter
                            ->bindValue(':module', $module) // Bind the module parameter
                            ->bindValue(':recordid', $Recordid) // Bind the crmid parameter
                            ->queryOne();

                        //get postvalue 
                        $postvaluearr = Yii::$app->db->createCommand("select changedon from modtracker_basic 
                          join modtracker_detail on modtracker_basic.id = modtracker_detail.id where fieldname='leadstatus' and postvalue=:postvalue  and module=:module and crmid=:Recordid order by changedon desc ")
                        ->bindValue(":module",$module)
                        ->bindValue(":Recordid",$Recordid)
                        ->bindValue(":postvalue",$value['leadstatusid'])
                        ->queryOne();
                        if($stclass == "")
                        {
                          //cureent stage
                          $today = date("Y-m-d H:i:s");
                          $enterdate = $postvaluearr['changedon'];
                          
                          $date1 = new DateTime($enterdate); // Replace with your first date
                          $date2 = new DateTime($today); // Replace with your second date

                          // Get the difference as a DateInterval object
                          $interval = $date1->diff($date2);

                          // Format the difference
                          $days = $interval->days; // Total days
                          $hours = $interval->h; // Remaining hours
                          $minutes = $interval->i; // Remaining minutes

                          // Output in desired format
                          $duration= "$days Days | $hours hours $minutes min";
                          // Convert the string into a timestamp
                          $timestamp = strtotime($enterdate);

                          // Format the date
                          $enteredat = date('M d, Y \a\t g.i a', $timestamp); // Format: Oct 22, 2024 at 9.16 am


                        }
                        else if(!empty($prevalue))
                        {
                          $today = $prevalue['changedon'];
                          $enterdate = $postvaluearr['changedon'];
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

                         
                        
                        }
                        else{
                          $duration = '';
                          $enteredat= '';
                        }

                        ?>
                  <div class="flex-row-f-5f leaddurationbox leadduration<?= $value['leadstatusid']." ".$stclass;?> ">
                     <div class="col-md-4">
                        <span class="new-60 lead-satge-name"><?= $value['leadstatus_value'];?> </span>
                     </div>
                     <div class="col-md-4">
                        <span class="oct lead-enered-at"><?= $enteredat; ?> </span>
                     </div>
                     <div class="col-md-4">
                        <span class="text-48 lead-enter-duration"><?=$duration;?></span>
                     </div>
                  </div>
                   <?php
              }?>
                  </div>
                 
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="container-d">
    <?php
    foreach ($getleads as $key => $value) {
         if($Record['leadstatus'] == $value['leadstatusid'])
                        $stclass = "";
                        else $stclass ="tr-hidden"; 
        ?>
      <div class="rectangle-61 leaddescbox leaddesc<?= $value['leadstatusid']?> <?= $stclass;?>">
         <h2 class="description">Description</h2>
         <p><?=$value['description'];?>
         </p>
      </div>
      <?php
  }?>
   </div>
   <div class="container-d">
      <div class="col-md-12">

          <div class="flex-row-ee">

             <nav class="nav__container">
    <div class="nav__logo">
      <button class="tab active" data-tab="summary">Summary</button>
          <button class="tab" data-tab="history">History</button>
    </div>
  
    <ul class="nav__links">
      <li class="nav__link">
        <a href="#" style="background: #b178fb;padding: 7px 12px;"><img src="<?= $baseUrl;?>/thememain/img/calender.png" style="background: #b178fb;
  width: 17px;"></a>
      </li>
      <li class="nav__link">
       <a href="#" style="background: #5c9cff;padding: 7px 12px;"><img src="<?= $baseUrl;?>/thememain/img/email-white.png" style="width: 17px;"></a>
      </li>
      <li class="nav__link">
        <a href="#" style="background: #a0a0a0;padding: 7px 10px;"><img src="<?= $baseUrl;?>/thememain/img/pdf.png" style="background: #a0a0a0;
  width: 17px;"></a>
      </li>
      <li class="nav__link">
       <a href="#" style="background: #f8af92;padding: 7px 10px;"><img src="<?= $baseUrl;?>/thememain/img/33a94905-7956-4a9e-bd74-7ffb3b1d2b08.png" style="background: #f8af92;
  width: 17px;"></a>
      </li>
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
                if (!empty($Block->fields))
                    {
                ?>
                <div class="accordion-item">
                  <div class="accordion-header">
                    <span><?= $Block->blocklabel ?></span>
                    <button class="accordion-toggle">
                      <i class="las la-angle-down"></i>
                      <i class="las la-angle-down" style="display: none;"></i>
                      <!-- <i class="arrow-icon down">↓</i><i class="arrow-icon up" style="display: none;">↑</i>  -->
                    </button>
                  </div>

                  <div class="accordion-content">
                    <?php
                    foreach ($Block->fields as $field) 
                    {?>
                        <div>
                        <label><b><?= $field["fieldlabel"]; ?></b>:</label>
                              <?php
                     if($field["uitype"] == 12)
                     {
                         $ref_hid_value = isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : '';
                         $model1 = new Reference($TableName,$FieldId);
                         $relatedmodulename = $model1->getRelatedNoduleName($field["fieldid"]);
                         $getRelatedDisplayFieldName=$model1->getRelatedDisplayFieldName($field["fieldid"]);
                            if(isset($Record->{$field["columnname"]}) && $Record->{$field["columnname"]} !='')
                                $Record->{$field["columnname"]}=$model1->getRefEntityValue($field["fieldid"],$ref_hid_value);
                            else  $Record->{$field["columnname"]}='';
                     }
                     else if($field["uitype"] == 8)
                         {
                             $modellist = new Listhire;
                             if(isset($Record->{$field["columnname"]}))
                             $Record->{$field["columnname"]} = $modellist->getPickListDetailvalue($field["fieldid"],$Record->{$field["columnname"]});
                         else $Record->{$field["columnname"]} ;
                     
                     
                         }
                          else if($field["uitype"] == 53)
                         {
                             $modellist = new Listhire;
                             if(isset($Record->{$field["columnname"]}))
                             $Record->{$field["columnname"]} = $modellist->getuser($field["fieldid"],$Record->{$field["columnname"]});
                         else $Record->{$field["columnname"]} ;
                     
                     
                         }?>
                         <span><?= isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : ""?></span>
                     </div>
                    <?php
                    }?>
                  </div>
                </div>
               <?php
                }
            }?>
              </div>
            </div>
            <!-- History Section -->
            <div id="history" class="tab-content-detail-view">
              <div class="history-section">
                <div class="activity-1">History</div>
                <div class="timeline-1w">
        <!-- Event 1 -->
        <div class="timeline-event">
            <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i></div>
            <div class="timeline-details">
                <p>Vijay Singh <span class="timeline-tsk">Updated Existing Account</span></p>
                <p>Was From Yes To Oct 12, 2024 By Oct 22, 2024 At 5.45 PM</p>
            </div>
        </div>

        <!-- Event 2 -->
        <div class="timeline-event">
            <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i></div>
            <div class="timeline-details">
                <p>Amit Dhingra <span class="timeline-tsk">Added Task - TT1</span></p>
                <p>By Oct 1, 2024 At 5.45 PM</p>
            </div>
        </div>

        <!-- Event 3 -->
        <div class="timeline-event">
            <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i></div>
            <div class="timeline-details">
                <p>Ravi Sharma <span class="timeline-tsk">Attachment Added</span></p>
                <p>For Design Code <a href="#">5629433934178188-1.Zip</a>, Oct 1, 2024 At 5.45 PM</p>
            </div>
        </div>

        <!-- Event 4 -->
        <div class="timeline-event">
            <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i></div>
            <div class="timeline-details">
                <p>Tushar Varma <span class="timeline-tsk">Added A Notes2</span></p>
                <p>Oct 28, 2024 At 5.45 PM</p>
            </div>
        </div>

        <!-- Event 5 -->
        <div class="timeline-event">
            <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i></div>
            <div class="timeline-details">
                <p>Amit Dhingra <span class="timeline-tsk">New Lead Created</span></p>
                <p>Oct 28, 2024 At 5.45 PM</p>
            </div>
        </div>
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
            <button class="frame-50-1">
              <span class="add-calls"><img src="<?= $baseUrl;?>/thememain/img/phone.png"> Add Calls </span>
        </button>
        <button class="frame-52-1">
              <span class="add-meeting"><img src="<?= $baseUrl;?>/thememain/img/add-meeting.png"> Add Meeting</span>
        </button>
        <button class="frame-54-1">
              <span class="add-task"><img src="<?= $baseUrl;?>/thememain/img/add-task.png"> Add Task</span>
            </button>
          </div>
      <section class="c-faqs">
  <div class="c-faqs__items" style="margin-top: 10px;">
    <details class="c-faqs__item">
      <summary class="c-faqs__item-question">
       Upcoming & Overdue <i class="fa-solid fa-angle-down"></i>
      </summary>
      
      <div class="col-xs-12">
        <ul class="event-list">
          <li>
      
            <img alt="" src="<?= $baseUrl;?>/thememain/img/pajamas_task-done.png" />
            <div class="info">
              <h2 class="title" style="color: #5c9cff;">Email</h2>
              <p class="desc">You have an Upcoming events</p>
            </div>
            
            <div class="info-1">
              <span>7:00 PM | Tomorrow </span>
            </div>
          
          </li>

           <div class="detail-heightline"></div>

          <li class="phone-event-detail" style="height:auto">
          
            <img alt="My 24th Birthday!" src="<?= $baseUrl;?>/thememain/img/uil_calender.png" />
            <div class="info">
              <h2 class="title" style="color: #5c9cff;">Call</h2>
              <p class="desc">You have an event call</p>
            
            </div>
          <div class="info-2">
              <span>7:00 PM | Tomorrow </span><i class="fa-regular fa-circle-caret-down"></i>
            </div>
          </li>

        </ul>
      </div>
    </details>

    <details class="c-faqs__item">
      <summary class="c-faqs__item-question">
        October . 2024 <i class="fa-solid fa-angle-down"></i>
      </summary>
      
      <p class="c-faqs__item-answer">
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Fugit quis iure magnam cupiditate commodi, velit deleniti cum explicabo mollitia dolor assumenda facilis debitis tempore doloremque quibusdam maxime voluptatem culpa illo.
      </p>
    </details>

 
  
  
  </div>
</section>
      
      
      </div>
          </div>
          <!-- Right Section -->
          <div class="right-section">
            <div class="notes">
              <div class="notes-1">Notes</div>
              <textarea placeholder="Write your notes here..."></textarea>
              <button class="btn post-btn">Post</button>
              <div class="note-entry">
                <p>Lead status required to be changed...</p>
                <span>Amit Dhingra · Oct 22, 2024 at 9:16 am</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
   </div>
  
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
                <option value="<?= $value['id'];?>"><?= $value['showfield'];?> (<?= $value['email'];?>)</option>
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
            <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken;?>">
            <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName;?>">
            <input type="hidden" name="leadstatus_v" id="leadstatus_d" value="3">
        <textarea id="delegate_comment"></textarea>
        <button type="button" id="delegatesubmit">Submit</button>
       
    </div>
  </div>
</div> -->
<!-- <div class="modal fade" id="reject-modal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
       
            <input type="hidden" id="Recordid" value="<?php //$_REQUEST['Record'] ?>">
            <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken;?>">
            <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName;?>">
            <input type="hidden" name="leadstatus_v" id="leadstatus_r" value="5">
        <textarea id="reject_comment"></textarea>
        <button type="button" id="rejectsubmit">Submit</button>
       
    </div>
  </div>
</div> -->

<?php
$this->registerJs("
   
    $('.btn-close, .btn-secondary').click(function() {
       $('#add-lead-modal').modal('hide');
    });
     

    //modal create
    
    $('#add-lead-btn').on('click', function () {
         $.get('edit?Record={$_REQUEST['Record']}', function(data) {

       
            $('#add-lead-modal').modal('show')
                .find('.modal-content')
                .html(data);
        });
    });

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

$this->registerJsFile('@web/thememain/js/lead-details.js', ['depends' => [AdminAsset::class]]);

?>
<script type="text/javascript">

</script>