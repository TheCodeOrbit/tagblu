 <?php $baseUrl = Yii::$app->HomeUrl;?>
 <?php foreach ($allactivities as $activity):

?>
                                                                    <div class="task-item">
                                                                        <div class="task-icon">
                                                                        <?php if ($activity['activity_type'] === 'call'): ?>
                                                                            <span><img src="<?= $baseUrl;?>/thememain/img/detail/Addcall.svg"></span>
                                                                            <?php elseif ($activity['activity_type'] === 'meeting'): ?>
                                                                            <span><img src="<?= $baseUrl;?>/thememain/img/detail/AddMeeting.svg"></span>
                                                                             <?php elseif ($activity['activity_type'] === 'task'): ?>
                                                                                <span><img src="<?= $baseUrl;?>/thememain/img/detail/Addtask.svg"></span>
                                                                            <?php endif; ?>


                                                                        </div>
                                                                        <div class="task-details">
                                                                            <div class="task-title">
                                                                                <input type="checkbox" id="<?= $activity['activity_type']; ?>">
                                                                                <label for="<?= $activity['activity_type']; ?>"><?= ucfirst($activity['activity_type']); ?></label>
                                                                            </div>
                                                                            <p><?= $activity['activity_description']; ?></p>
                                                                        </div>
                                                                        <div class="task-right">
                                                                        <?php
                                                                            $currentDate = date('Y-m-d');
                                                                            $tomorrowDate = date('Y-m-d', strtotime('+1 day'));
                                                                            $activityDate = date('Y-m-d', strtotime($activity['activity_date']));
                                                                            $formattedTime = date('g:i a', strtotime($activity['activity_date']));

                                                                            if ($activityDate === $currentDate): ?>
                                                                            <span  class="task-date overdue">Today at <?= $formattedTime; ?></span>
                                                                            <?php elseif ($activityDate === $tomorrowDate): ?>
                                                                            <span class="task-date overdue">Tomorrow at <?= $formattedTime; ?></span>
                                                                            <?php else: ?>
                                                                            <span class="task-date overdue"><?= date('M d, Y \a\t g:i a', strtotime($activity['activity_date'])); ?></span>
                                                                            <?php endif; ?>
                                                                            <!-- <span class="task-date overdue">10-Jan</span> -->
                                                                            <div class="dropdown">
                                            <i
                                                class="fa-regular fa-circle-play"></i>
                                            <button class="dropdown-btn"></button>
                                            <ul class="dropdown-menu">
                                                <a href="<?= $baseUrl; ?><?= $activity['activity_type']; ?>/detail?Record=<?= $activity['activity_id']; ?>&sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>">
                                                    <li><button>Detail</button></li>
                                                </a>

                                                <a href="<?= $baseUrl; ?><?= $activity['activity_type']; ?>/edit?Record=<?= $activity['activity_id']; ?>&sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>">
                                                    <li><button>Edit</button></li>
                                                </a>

                                            </ul>
                                        </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <?php endforeach; ?>
                  <?php
                  die;?>