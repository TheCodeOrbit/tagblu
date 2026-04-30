 <?php $baseUrl = Yii::$app->HomeUrl;?>
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
                              <h2 class="title" style="color: var(--color-primary) !important;">
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
                  <?php
                  die;?>