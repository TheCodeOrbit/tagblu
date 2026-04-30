<?php 
namespace console\controllers;
use yii\console\Controller;
use Yii;

class AttachmentGarbageController extends Controller
{
  
    public function actionClean()
    {
        try {
            $isFirstRun = !Yii::$app->db->createCommand('SELECT 1 FROM attachment_log LIMIT 1')->queryScalar();
            
            $fields = Yii::$app->db->createCommand('SELECT columnname, tablename FROM field WHERE uitype=5')->queryAll();
            
            if ($isFirstRun) {
                $timeCondition = '';
            } else {
                $dateToday = date('Y-m-d 00:00:00');
                $timeCondition = " AND createdtime >= '{$dateToday}' ";
            }
            $usedAttachmentIds = [];
            foreach ($fields as $field) {
                $column = $field['columnname'];
                $table = $field['tablename'];
                try {
                    $fieldSql = "SELECT {$column} FROM {$table}";
                    // if (!$isFirstRun && $table !== 'attachments') {
                    //     $fieldSql .= " WHERE createdtime >= '{$dateToday}'";
                    // }
                    $ids = Yii::$app->db->createCommand($fieldSql)->queryColumn();
                    $usedAttachmentIds = array_merge($usedAttachmentIds, array_filter($ids));
                } catch (\Exception $e) {
                    echo "Error reading {$table}.{$column}: " . $e->getMessage() . "\n";
                    continue; 
                }
            }
            $usedAttachmentIds = array_unique($usedAttachmentIds);

            $attachmentSql = "SELECT attachmentsid, name, description, type, path, storedname, subject, createdtime 
                            FROM attachments WHERE status != 1" . $timeCondition;
            $attachments = Yii::$app->db->createCommand($attachmentSql)->queryAll();

            $basePath = Yii::getAlias('@backend/web/');
            $deletedCount = 0;

            foreach ($attachments as $attachment) {
                if (!in_array($attachment['attachmentsid'], $usedAttachmentIds)) {

                    if (!empty($attachment['path'])) {
                        $fullPath = $basePath . $attachment['path'];
                        if (file_exists($fullPath)) {
                            $weekName = 'week_' . date('W'); 
                            $binDir = $basePath . 'uploads/bin/' . $weekName . '/';
                            print_r($fullPath. ' \n');
                            if (!is_dir($binDir)) {
                                mkdir($binDir, 0775, true);
                            }
                            
                            $originalFilename = basename($attachment['path']);
                            $newPath = 'uploads/bin/' . $weekName . '/' . $originalFilename;
                            $newFullPath = $binDir . $originalFilename;
                            
                            if (rename($fullPath, $newFullPath)) {
                                echo "Moved: {$attachment['path']} -> {$newPath}\n";
                            } else {
                                echo "Failed to move: {$fullPath}\n";
                            }
                        }
                    }

                    Yii::$app->db->createCommand()->insert('attachment_log', [
                        'attachment_id' => $attachment['attachmentsid'],
                        'name' => $attachment['name'],
                        'description' => $attachment['description'],
                        'type' => $attachment['type'],
                        'path' => $attachment['path'], 
                        'original_full_path' => isset($fullPath) ? $fullPath : null,
                        'moved_to_path' => isset($newPath) ? $newPath : null, 
                        'storedname' => $attachment['storedname'],
                        'subject' => $attachment['subject'],
                        'createdtime' => date('Y-m-d H:i:s'),
                        'deleted_at' => date('Y-m-d H:i:s'),
                        'deleted_reason' => 'Moved to bin: Not referenced in any recent field (uitype=5)',
                        'file_size' => (!empty($newFullPath) && file_exists($newFullPath)) ? filesize($newFullPath) : 0, 
                        'deleted_by_cron' => 1,
                    ])->execute();

                    Yii::$app->db->createCommand()->update('attachments', ['status' => 1], ['attachmentsid' => $attachment['attachmentsid']])->execute();
                    $deletedCount++;
                }
            }
            echo "Cron executed. {$deletedCount} unused attachments cleaned from today.\n"; 

        } catch (\Exception $e) {
            echo "Cron error: " . $e->getMessage() . "\n";
        }
    }

}
