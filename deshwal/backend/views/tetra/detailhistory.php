<?php
use app\models\ListHire;
use app\models\Reference;
use app\models\User;

?>
<!-- History Section -->
                                <!-- History Section -->
                                <div id="history" class="tab-content-detail-view">
                                    <div class="history-section">
                                        <div class="activity-1">History</div>
                                        <div class="timeline-1w">
                                            <?php //echo "<pre>"; print_r($Detailhistory);die;
                                                foreach ($Detailhistory as $key): ?>
                                                <?php if (isset($key['basic']['status']) && (($key['basic']['status'] == 'Created') || ($key['basic']['status'] == 'Imported'))): ?>
                                                    <div class="timeline-event">
                                                        <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i>
                                                        </div>
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
                                                        <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i>
                                                        </div>
                                                        <div class="timeline-details">
                                                            <p>
                                                                <?php echo htmlspecialchars($key['basic']['whodid']); ?>
                                                                <span
                                                                    class="timeline-tsk"><?php echo htmlspecialchars($key['basic']['status']); ?></span>
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
                                                 <?php elseif (isset($key['basic']['status']) && ($key['basic']['status'] == 'Imported Serial No')): ?>
                                                    <div class="timeline-event">
                                                        <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i>
                                                        </div>
                                                        <div class="timeline-details">
                                                            <p>
                                                                <?php echo htmlspecialchars($key['basic']['whodid']); ?>
                                                                <span
                                                                    class="timeline-tsk"><?php echo htmlspecialchars($key['basic']['status']); ?></span>
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
                                                            <div class="timeline-icon"><i class="fa-regular fa-circle-user"></i>
                                                            </div>
                                                            <div class="timeline-details">
                                                                <p>
                                                                    <?php echo htmlspecialchars($key['basic']['whodid']); ?>
                                                                    <span
                                                                        class="timeline-tsk"><?php if($key['basic']['status'] == "singleedit")
                                                                                                        echo "Update via <i class='fa-solid fa-pen'></i>";
                                                                                                    else
                                                                                                        echo htmlspecialchars($key['basic']['status']); ?></span>
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
                                                                    } else if ($uitype == 31) {

                                                                        $model1 = new Reference($TableName, $FieldId);

                                                                        // ---------- PRE VALUE ----------
                                                                        if (!empty($prevalue)) {

                                                                            // Convert string → array → clean → unique
                                                                            $ids = array_unique(array_filter(
                                                                                array_map('trim', explode(',', $prevalue)),
                                                                                fn($v) => $v !== ''
                                                                            ));

                                                                            $preDisplayValues = [];

                                                                            foreach ($ids as $id) {
                                                                                $preDisplayValues[] = $model1->getRefEntityValue($fieldid, $id);
                                                                            }

                                                                            // Final display value (comma separated names)
                                                                            $prevalue = implode(', ', $preDisplayValues);
                                                                        } else {
                                                                            $prevalue = '';
                                                                        }

                                                                        // ---------- POST VALUE ----------
                                                                        if (!empty($postvalue)) {

                                                                            $ids = array_unique(array_filter(
                                                                                array_map('trim', explode(',', $postvalue)),
                                                                                fn($v) => $v !== ''
                                                                            ));

                                                                            $postDisplayValues = [];

                                                                            foreach ($ids as $id) {
                                                                                $postDisplayValues[] = $model1->getRefEntityValue($fieldid, $id);
                                                                            }

                                                                            $postvalue = implode(', ', $postDisplayValues);
                                                                        } else {
                                                                            $postvalue = '';
                                                                        }
                                                                    }

                                                                    else if ($uitype == 17) {

                                                                        if (isset($prevalue) && !empty($prevalue))
                                                                        {
                                                                            $date = $prevalue; // original date in Y-m-d format
                                                        
                                                                            // Convert to a timestamp
                                                                            $timestamp = strtotime($date);
                                        
                                                                            // Format the timestamp to d-m-Y
                                                                            $prevalue = date('d-m-Y', $timestamp);
                                                                        } 
                                                                        else
                                                                          $prevalue = '';
                                    
                                                                          if (isset($postvalue) && !empty($postvalue))
                                                                          {
                                                                          $date = $postvalue; // original date in Y-m-d format
                                                      
                                                                          // Convert to a timestamp
                                                                          $timestamp = strtotime($date);
                                      
                                                                          // Format the timestamp to d-m-Y
                                                                          $postvalue = date('d-m-Y', $timestamp);
                                                                      } 
                                                                      else
                                                                        $prevalue = '';
                                                                    } 
                                                                    else if ($uitype == 13) {
                                        
                                                                      if (isset($prevalue) && !empty($prevalue))
                                                                      {
                                                                          $date = $prevalue; // original date in Y-m-d format
                                                      
                                                                          // Convert to a timestamp
                                                                          $timestamp = strtotime($date);
                                      
                                                                          // Format the timestamp to d-m-Y
                                                                          $prevalue = date('d-m-Y H:is', $timestamp);
                                                                      } 
                                                                      else
                                                                        $prevalue = '';
                                    
                                                                        if (isset($postvalue) && !empty($postvalue))
                                                                        {
                                                                        $date = $postvalue; // original date in Y-m-d format
                                                    
                                                                        // Convert to a timestamp
                                                                        $timestamp = strtotime($date);
                                    
                                                                        // Format the timestamp to d-m-Y
                                                                        $postvalue = date('d-m-Y H:is', $timestamp);
                                                                    } 
                                                                    else
                                                                      $prevalue = '';
                                                                    } 
                                                                    else if ($uitype == 8 || $uitype == 10) {
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
                                                                    //uitype = 5 condition added by ptpatel to show file name in history on date 06-04-2026
                                                                        else if ($uitype == 5) {
                                                                        // if (!empty($prevalue) && !empty($postvalue)) {
                                                                            $modellist = new Listhire;
                                                                            if (isset($prevalue) && !empty($prevalue))
                                                                                $prevalue = $modellist->getfilename($prevalue);
                                                                            else
                                                                                $prevalue = '';
                                                                            
                                                                        // echo $prevalue;die;
                                                                            //postvalue
                                                                            if (isset($postvalue) && !empty($postvalue))
                                                                                $postvalue = $modellist->getfilename($postvalue);
                                                                            else
                                                                                $postvalue = '';
                                                                        // }
                                                                    } 
                                                                    //uitype = 5 condition added by ptpatel to show file name in history on date 06-04-2026  
                                                                    //code added by ptpatel on date 03-11-2025
                                                                    else if ($fieldid == '' && !empty($fieldlabels[$index])) {
                                                                            // Handle role-based fields like H28, H50 → use rolename as label, userid as pre/post values

                                                                            // // Convert prevalue user ID to name (if available)
                                                                            if (!empty($prevalue)) {
                                                                                $prevalue = User::find()
                                                                                    ->select([
                                                                                        new \yii\db\Expression("CONCAT(first_name, IF(last_name != '', CONCAT(' ', last_name), '')) AS fullname")
                                                                                    ])
                                                                                    ->where(['id' => $prevalue])
                                                                                    ->scalar() ?? ''; // fallback if user not found
                                                                            } else {
                                                                                $prevalue = '';
                                                                            }


                                                                            // Convert postvalue user ID to name (if available)
                                                                            if (!empty($postvalue)) {
                                                                                $postvalue = User::find()
                                                                                    ->select([
                                                                                        new \yii\db\Expression("CONCAT(first_name, IF(last_name != '', CONCAT(' ', last_name), '')) AS fullname")
                                                                                    ])
                                                                                    ->where(['id' => $postvalue])
                                                                                    ->scalar() ?? '';
                                                                            } else {
                                                                                $postvalue = '';
                                                                            }
                                                                        }
                                                                    //end code added by ptpatel on date 03-11-2025

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