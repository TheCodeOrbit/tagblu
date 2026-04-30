<?php

use yii\helpers\Url;

$baseUrl = Yii::$app->HomeUrl;

?>
<style type="text/css">
  .col-6 {
    width: 50%;
    display: contents;
  }

  /* Popup */
  .popup {
    width: 393px;
    height: 350px;
    background: #fff;
    border-radius: 0px;
    box-shadow: 0px 0px 6px -1px #646060;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    padding: 17px;
  }

  /* Popup Header */
  .popup-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    background-color: #f1f1f1;
    border-bottom: 1px solid #ddd;
  }

  .popup-header h3 {
    font-size: 12px;
    font-weight: bold;
  }

  .close-btn {
    background: none;
    border: none;
    font-size: 10px;
    cursor: pointer;
    color: #555;
  }

  /* Popup Form */
  .popup-form {
    padding: 15px;
    flex: 1;
  }

  .form-group {
    margin-bottom: 10px;
  }

  .form-group label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 5px;
  }

  .form-group input,
  .form-group textarea,
  .form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 10px;
  }

  textarea {
    resize: none;
    height: 60px;
  }

  small {
    font-size: 8px;
    color: #888;
  }

  /* Row Layout for Date & Time */
  .form-row {
    display: block;
    /* display: flex;
    justify-content: space-between;
    gap: 10px; */
  }

  .input-group {
    display: flex;
    gap: 10px;
  }

  .input-group input {
    flex: 1;
  }

  /* Attendees Section */
  .attendees-list {
    margin-top: 10px;
  }

  .attendee {
    display: inline-block;
    padding: 5px 10px;
    background: #eaf4ff;
    color: #007bff;
    font-size: 8px;
    border-radius: 4px;
  }

  #dropdownList {
    list-style: none;
    padding: 0;
    margin: 0;
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #ccc;
  }

  #dropdownList li {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid #ddd;
  }

  #dropdownList li:hover {
    background-color: #f0f0f0;
  }

  .validation-error {
    color: red;
    font-size: 10px;
  }

  #call_information_call-duration {
    background-color: #f8f9fa;
    /* Light grey background */
    border: 1px solid #ced4da;
    /* Keep border styling */
    color: #6c757d;
    /* Muted text color */
    cursor: not-allowed;
    /* Disabled cursor */
  }

  /**added by ptpatel on date 03-05-25 */
  .dropdown-wrapper {
    position: relative;
  }

  #attendees {
    width: 100%;
    box-sizing: border-box;
  }

  .meetingdropdownList {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid #ccc;
    border-top: none;
    z-index: 1000;
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .meetingdropdownList li {
    padding: 7px 12px;
    cursor: pointer;
  }

  .meetingdropdownList li:hover {
    background-color: #f0f0f0;
  }
  /**end added by ptpatel on date 03-05-25 */
</style>
<?php
if ($ModuleName == "calls") { ?>
  <div class="modal-header">
    <h3><img src="<?= $baseUrl; ?>thememain/img/phone.png" style="width: 20%;margin-right: 5px;"> Call</h3>
    <button class="close-btn" id="close-modal-btn">✖</button>
  </div>
  <div class="popup-container">
    <div class="popup">


      <input type="hidden" id="call_information_related_to" name="call_information['related_to']" value="<?= $TabId; ?>">
      <input type="hidden" id="call_information_related_to_id" value="<?= $Recordid; ?>">
      <input type="hidden" id="call_information_creatorid" value="<?= Yii::$app->user->id; ?>">
      <input type="hidden" id="call_information_createdtime" value="<?= date("Y:m:d H:i:s") ?>">


      <!-- Subject -->
      <div class="form-group col-12">
        <label for="subject">Subject</label>
        <input type="text" id="call_information_subject" placeholder="Enter subject">
        <span class="validation-error" id="subject-error"></span>
      </div>
      <!-- call type -->


      <div class="form-group col-6">
        <label for="call_type">Call Type</label>
        <div class="input-group">
          <select id="call_information_calltypeid" placeholder="Select Call Type">
            <option value="">Select Call Type</option>
            <?php foreach ($calltypelist as $key => $value): ?>
              <option value="<?= $value['id']; ?>"><?= $value['showfield']; ?></option>
            <?php endforeach; ?>
          </select>
          <span class="validation-error" id="call-type-error"></span>
        </div>

      </div>
      <div class="form-group col-6">
        <label for="call_type">Outgoing Call Status</label>
        <div class="input-group">
          <select id="call_information_outgoing_call_status" placeholder="Select Call Type">
            <option value="">Select Call Status</option>
            <?php foreach ($OutgoingCallStatusList as $key => $value): ?>
              <option value="<?= $value['id']; ?>"><?= $value['showfield']; ?></option>
            <?php endforeach; ?>
          </select>

          <span class="validation-error" id="call-status-error"></span>
        </div>

      </div>

      <div class="form-group col-6">
        <label for="start-date">Call Start time</label>
        <div class="input-group">
          <input type="date" id="call_information_start-date">
          <input type="time" id="call_information_start-time">
        </div>
        <span class="validation-error" id="start-time-error"></span>
      </div>

      <div class="form-group col-6">
        <label for="start-date">Call End time</label>
        <div class="input-group">
          <input type="date" id="call_information_end-date">
          <input type="time" id="call_information_end-time">
        </div>
        <span class="validation-error" id="end-time-error"></span>
      </div>

      <div class="form-group col-12">
        <label for="call-duration">Call Duration</label>
        <input type="text" id="call_information_call-duration" readonly placeholder="Enter Call Duration">
        <span class="validation-error" id="call-duration-error"></span>
      </div>


      <!-- Description -->
      <div class="form-group col-12">
        <label for="description">Comments</label>
        <!-- <small>Tip: Type control + period to insert quick text</small> -->
        <textarea id="call_information_comments" placeholder="Enter description"></textarea>
        <span class="validation-error" id="comments-error"></span>
      </div>


    </div>

  </div>
  <div class="save-footer">
    <button type="button" class="sv-btn savecall">Save</button>
  </div>
<?php
} else if ($ModuleName == "meeting") {
?>

  <div class="modal-header">
    <h3><img src="<?= $baseUrl; ?>thememain/img/add-meeting.png" style="width: 20%;margin-right: 5px;">Meeting</h3>
    <button class="close-btn" id="close-modal-btn">✖</button>
  </div>
  <div class="popup-container">
    <div class="popup">


      <!-- Form -->
      <input type="hidden" id="meeting_information_related_to" name="call_information['related_to']" value="<?= $TabId; ?>">
      <input type="hidden" id="meeting_information_related_to_id" value="<?= $Recordid; ?>">
      <input type="hidden" id="meeting_information_creatorid" value="<?= Yii::$app->user->id; ?>">
      <input type="hidden" id="meeting_information_createdtime" value="<?= date("Y:m:d H:i:s") ?>">

      <!-- Subject -->
      <div class="form-group col-12">
        <label for="subject">Subject</label>
        <input type="text" id="meeting_information_subject" placeholder="Enter subject">
        <span class="validation-error" id="meeting_information_subject_error"></span>
      </div>

      <!-- Description -->
      <div class="form-group col-12">
        <label for="description">Description</label>
        <!-- <small>Tip: Type control + period to insert quick text</small> -->
        <textarea id="meeting_information_description" placeholder="Enter description"></textarea>
        <span class="validation-error" id="meeting_information_description_error"></span>

      </div>

      <!-- Start and End Date/Time -->

      <div class="form-group col-12">
        <label for="start-date">Start</label>
        <div class="input-group">
          <input type="date" id="meeting_information_start-date">
          <input type="time" id="meeting_information_start-time">
        </div>
        <span class="validation-error" id="meeting_information_start_error"></span>
      </div>

      <div class="form-group col-12">
        <label for="end-date">End</label>
        <div class="input-group">
          <input type="date" id="meeting_information_end-date">
          <input type="time" id="meeting_information_end-time">
        </div>
        <span class="validation-error" id="meeting_information_end_error"></span>
      </div>



      <!-- Name -->
      <!--  <div class="form-row">
        <div class="form-group col-6">
          <label for="name">Name</label>
          <input type="text" id="name" placeholder="Enter name">
        </div>

        <div class="form-group col-6">
          <label for="related-to">Related To</label>
          <input type="text" id="related-to" placeholder="Enter related to">
        </div>
        </div> -->
      <!-- Attendees -->
       

      <link href="<?= $baseUrl; ?>/thememain/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?= $baseUrl; ?>/thememain/css/multiple.css" rel="stylesheet">
        <link href="<?= $baseUrl; ?>/thememain/css/select2.min.css" rel="stylesheet">
        <link href="<?= $baseUrl; ?>/thememain/css/multilist-dd.css" rel="stylesheet">

        <script type="text/javascript" src="<?= $baseUrl; ?>thememain/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="<?= $baseUrl; ?>thememain/bootstrap/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/select2.min.js"></script>
        <script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/tetra/multilist-dd.js"></script>
        <div class="form-group col-12">
          <label for="attendees">Attendees</label>
          <!-- code added by ptaptel on date 05-05-25 -->
          <select class="form-control multySelect" id="attendees"  multiple="true"></select>
          <!-- end code added by ptaptel on date 05-05-25 -->
        </div>

      <span class="validation-error" id="meeting_information_attendees_error"></span>
    </div>



  </div>
  </div>
  <!-- Save Button -->
  <div class="save-footer">
    <button type="button" class="sv-btn savemeeting">Save</button>
  </div>

<?php
} else if ($ModuleName == "task") {
?>

  <div class="modal-header">
    <h3><img src="<?= $baseUrl; ?>thememain/img/add-task.png" style="width: 20%;margin-right: 5px;">Task</h3>
    <button class="close-btn" id="close-modal-btn">✖</button>
  </div>
  <div class="popup-container">
    <div class="popup">


      <!-- Form -->

      <input type="hidden" id="task_information_related_to" value="<?= $TabId; ?>">
      <input type="hidden" id="task_information_related_to_id" value="<?= $Recordid; ?>">
      <input type="hidden" id="task_information_creatorid" value="<?= Yii::$app->user->id; ?>">
      <input type="hidden" id="task_information_createdtime" value="<?= date("Y:m:d H:i:s") ?>">
      <!-- Subject -->
      <div class="form-group col-12">
        <label for="subject">Subject</label>
        <input type="text" id="task_information_subject" placeholder="Enter subject">
        <span class="validation-error" id="task_information_subject_error"></span>
      </div>

      <!-- Description -->
      <div class="form-group col-12">
        <label for="description">Description</label>
        <!-- <small>Tip: Type control + period to insert quick text</small> -->
        <textarea id="task_information_description" placeholder="Enter description"></textarea>
        <span class="validation-error" id="task_information_description_error"></span>

      </div>

      <!-- Start and End Date/Time -->

      <div class="form-group col-12">
        <label for="start-date">Due Date</label>
        <div class="input-group">
          <input type="date" id="task_information_due_date">
          <!-- <input type="time" id="meeting_information_start-time"> -->
        </div>
        <span class="validation-error" id="task_information_due_date_error"></span>
      </div>
      <!-- Attendees -->
      <div class="form-group col-12">
        <label for="attendees">Assign to</label>
        <div class="input-group">
          <select id="task_information_ownerid" placeholder="Search People">
            <option value="">Select User</option>
            <?php
            foreach ($userlist as $key => $value) {
              # code...
              echo "<option value='" . $value['id'] . "'>" . $value['showfield'] . "</option>";
            }
            ?>
            <?php
            ?>
          </select>
        </div>
        <!-- <input type="hidden" id="meeting_information_participants"> -->
        <span class="validation-error" id="task_information_ownerid_error"></span>
      </div>



    </div>
  </div>
  <!-- Save Button -->
  <div class="save-footer">
    <button type="button" class="sv-btn savetask">Save</button>
  </div>

<?php
} else if ($ModuleName == "documents") {
?>

  <div class="modal-header">
    <h3><img src="<?= $baseUrl; ?>thememain/img/add-task.png" style="width: 20%;margin-right: 5px;">Attach File</h3>
    <button class="close-btn" id="close-modal-btn">✖</button>
  </div>
  <div class="popup-container">
    <div class="popup">


      <!-- Form -->

      <input type="hidden" id="documents_related_to" value="<?= $TabId; ?>">
      <input type="hidden" id="documents_related_to_id" value="<?= $Recordid; ?>">
      <input type="hidden" id="documents_creatorid" value="<?= Yii::$app->user->id; ?>">
      <input type="hidden" id="documents_createdtime" value="<?= date("Y:m:d H:i:s") ?>">
      <!-- Subject -->
      <!-- Drag & Drop Area -->
      <div class="drag-drop-area">
        <p>Drag & Drop Files Here <br> or</p>
        <label for="dragfileInput" class="drag-file-btn">Select File From my Computer</label><br><br>
        <span id="dragfilenameid"></span>
        <input type="file" id="dragfileInput" hidden>
      </div>

      <div class="form-group col-12">
        <label for="subject">Title</label>
        <input type="text" id="documents_title" placeholder="Enter Title">
      </div>

      <!-- Description -->
      <div class="form-group col-12">
        <label for="description">Note</label>
        <!-- <small>Tip: Type control + period to insert quick text</small> -->
        <textarea id="documents_note_content" placeholder="Enter description"></textarea>

      </div>

      <!-- Start and End Date/Time -->


      <!-- Attendees -->
      <div class="form-group col-12">
        <label for="attendees">Assign to</label>
        <div class="input-group">
          <select id="documents_note_ownerid" placeholder="Search People">
            <option value="">Select User</option>
            <?php
            foreach ($userlist as $key => $value) {
              # code...
              echo "<option value='" . $value['id'] . "'>" . $value['showfield'] . "</option>";
            }
            ?>
            <?php
            ?>
          </select>
        </div>
        <!-- <input type="hidden" id="meeting_information_participants"> -->

      </div>
      <div class="form-group col-12">
        <label for="start-date">Folder</label>
        <div class="input-group">
          <select id="documents_note_folderid">
            <option value="">Select Folder</option>
            <?php
            foreach ($folderlist as $key => $value) {
              # code...
              echo "<option value='" . $value['folderid'] . "'>" . $value['foldername'] . "</option>";
            }
            ?>
            <?php
            ?>
          </select>
        </div>
      </div>



    </div>
  </div>
  <!-- Save Button -->
  <div class="save-footer">
    <button type="button" class="sv-btn savedoc">Save</button>
  </div>
  <script type="text/javascript">
    // file input shw drag file name
    const dragfileInput = document.getElementById('dragfileInput');
    console.log(dragfileInput);
    if (dragfileInput) {
      dragfileInput.addEventListener('change', () => {
        if (dragfileInput.files.length > 0) {
          document.getElementById("dragfilenameid").innerHTML = `Selected File: ${dragfileInput.files[0].name}`;


          console.log(`File selected: ${dragfileInput.files[0].name}`);
        }
      });
    }
  </script>

<?php
}
//echo "xvcv";
die;
?>