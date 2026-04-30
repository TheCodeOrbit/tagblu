<?php
namespace common\components;

use Yii;
use yii\db\Query;
use common\models\WorkflowRule;
use common\models\WorkflowRuleRecipient;
use common\models\WorkflowTemplate;
use common\models\WorkflowEmailLog;

class WorkflowService
{
    // in-memory queue for after-commit delivery (avoid sending on rollback)
    private static $queue = [];

    /**
     * Run workflow evaluation for a single record.
     * @param string $module  module string (use same key as saved in rules e.g. 'opportunities')
     * @param int|string $recordId
     * @param array $oldData   associative old DB row (empty for create)
     * @param array $newData   associative new DB row (what DB will contain after update)
     */



    public static function run($module, $recordId, $oldData, $newData, $tablename, $fieldId)
    {
        // load active rules for module
        $rules = WorkflowRule::find()->where(['module' => $module, 'active' => 1])->all();
        if (empty($rules))
            return;

        foreach ($rules as $rule) {
            // if event is create and oldData empty -> trigger
            // if ($rule->trigger_event === 'create' && empty($oldData)) {
            if ($rule->trigger_event === 'create') {
                $oldData='';
                self::queueOrExecute($rule, $recordId, $oldData, $newData, $module, $tablename, $fieldId);
                continue;
            }

            // if event is update and not create
            if ($rule->trigger_event === 'update' && !empty($oldData)) {
                self::queueOrExecute($rule, $recordId, $oldData, $newData, $module, $tablename, $fieldId);
                continue;
            }

            // if event is change -> check fields list
            if ($rule->trigger_event === 'change') {
                $watch = array_map('trim', explode(',', $rule->trigger_fields));
                $changed = self::getChangedFields($oldData, $newData);
                foreach ($watch as $f) {
                    if ($f === '')
                        continue;
                    if (array_key_exists($f, $changed)) {
                        // rule matches if any of the watch fields changed
                        self::queueOrExecute($rule, $recordId, $oldData, $newData, $module, $tablename, $fieldId);
                        break;
                    }
                }
            }

            //code added by ptpatel on date 29-01-2026
            // if event is approve -> check fields list 
            if ($rule->trigger_event === 'approve') {
                // echo "in approve";die;
                $watch = array_map('trim', explode(',', $rule->trigger_fields));
                // $watch = trim($rule->trigger_fields);
                //check new stage with stag_id
                // if($newData[$rule->trigger_fields] == $rule->stage_id){ //if stage_id is single
                $stageIds = array_map('intval',
                        is_array($rule->stage_id)
                            ? $rule->stage_id
                            : explode(',', $rule->stage_id)
                    );

                    $currentValue = (int) $newData[$rule->trigger_fields];

                    if (in_array($currentValue, $stageIds, true)) {
                    // echo "in fi";die;
                    $changed = self::getChangedFields($oldData, $newData);
                    foreach ($watch as $f) {
                        if ($f === '')
                            continue;
                        if (array_key_exists($f, $changed)) {
                            // rule matches if any of the watch fields changed
                            self::queueOrExecute($rule, $recordId, $oldData, $newData, $module, $tablename, $fieldId);
                            break;
                        }
                    }
                }
                // echo "out is";die;
            }
            //end code added by ptpatel on date 29-01-2026
        }
    }

    // Compare old and new, return list of changed fields => ['field'=>['old'=>..,'new'=>..], ...]
    private static function getChangedFields(array $old, array $new)
    {
        $changes = [];
        foreach ($new as $k => $v) {
            if (array_key_exists($k, $old)) {
                // loose compare but handle null properly
                if ((string) $old[$k] !== (string) $v) {
                    $changes[$k] = ['old' => $old[$k], 'new' => $v];
                }
            } else {
                // new key not in old (create or new column) -> consider changed
                $changes[$k] = ['old' => null, 'new' => $v];
            }
        }
        return $changes;
    }

    // Enqueue if inside transaction, else execute immediately.
    private static function queueOrExecute($rule, $recordId, $oldData, $newData, $module, $tablename, $fieldId)
    {
        // detect if there is an active DB transaction by checking if connection has transaction ID
        // Yii2 doesn't expose a single global way to check commit status easily.
        // Simpler pattern: always queue and let caller flush after commit.
        // But to keep backward compat, if caller didn't flush we will also execute at the end of request.
        self::$queue[] = [$rule, $recordId, $oldData, $newData, $module, $tablename, $fieldId];
    }

    // Call this after DB commit to actually execute queued actions.
    public static function flushQueue()
    {
        while ($item = array_shift(self::$queue)) {
            // Must have exactly 7 items
            if (count($item) < 7) {
                echo "Invalid Workflow Queue Item: " . json_encode($item);
                die;
                Yii::error("Invalid Workflow Queue Item: " . json_encode($item));
                continue;
            }
            list($rule, $recordId, $oldData, $newData, $module, $tablename, $fieldId) = $item;
            self::executeRule($rule, $recordId, $oldData, $newData, $module, $tablename, $fieldId);
        }
    }

    // If for some reason you want immediate execution, call this:
    public static function execNow($rule, $recordId, $oldData, $newData, $module, $tablename, $fieldId)
    {
        self::executeRule($rule, $recordId, $oldData, $newData, $module, $tablename, $fieldId);
    }

    // Build recipients and execute action
    private static function executeRule($rule, $recordId, $oldData, $newData, $module, $tablename, $fieldId)
    {
        // get template (if any)
        $template = null;
        if ($rule->template_id) {
            $template = WorkflowTemplate::findOne($rule->template_id);
        }

        // build recipient emails/ids
        $recipientRows = WorkflowRuleRecipient::find()->where(['rule_id' => $rule->id])->all();
        $emails = [];
        $userIds = [];

        foreach ($recipientRows as $r) {
            if ($r->recipient_type === 'module_field') {
                $field = $r->module_field;
                if (!empty($newData[$field])) {
                    $uid = $newData[$field];
                    // try to fetch user email
                    $email = self::getUserEmailById($uid);
                    if ($email)
                        $emails[] = $email;
                    $userIds[] = $uid;
                }
            } elseif ($r->recipient_type === 'user') {
                if (!empty($r->user_id)) {
                    $uid = $r->user_id;
                    $email = self::getUserEmailById($uid);
                    if ($email)
                        $emails[] = $email;
                    $userIds[] = $uid;
                }
            } elseif ($r->recipient_type === 'manual') {
                if (!empty($r->email)) {
                    $parts = preg_split('/[,;]+/', $r->email);
                    foreach ($parts as $p) {
                        $e = trim($p);
                        if (filter_var($e, FILTER_VALIDATE_EMAIL))
                            $emails[] = $e;
                    }
                }
            }
        }

        $emails = array_values(array_unique($emails));
        $userIds = array_values(array_unique($userIds));

        // execute based on type
        if ($rule->trigger_type === 'email') {
            // foreach ($emails as $to) {
            //self::sendEmail($to, $template, $recordId, $oldData, $newData);
            self::sendEmail($module, $tablename, $fieldId, $emails, $template, $recordId, $oldData, $newData);
            // }
        } elseif ($rule->trigger_type === 'notification') {
            foreach ($userIds as $uid) {
                self::sendNotification($uid, $template, $recordId, $oldData, $newData);
            }
        } elseif ($rule->trigger_type === 'sms') {
            // you may want to map emails=>phones or userIds=>phones
            // implement SMS provider call here
        }
    }

    private static function getUserEmailById($id)
    {
        if (empty($id))
            return null;
        return Yii::$app->db->createCommand('SELECT email FROM `user` WHERE id = :id')
            ->bindValue(':id', $id)->queryScalar();
    }

    private static function sendEmail($module, $tablename, $fieldId, $emails, $template, $recordId, $oldData, $newData)
    {
        // render template body (either DB stored or file-based)
        $body = '';
        $subject = 'Notification';
        if ($template) {
            $subject = $template->subject;
            // we render template->body via simple token replacement if you want,
            // or treat body as HTML and send raw.
            //$body = self::renderTemplateBody($template->body, $recordId, $oldData, $newData);
            $subject = WorkflowTemplateRenderer::renderWithFieldLabels(
                $template->id,//added by ptpatel
                $template->subject,
                $module,
                $tablename,
                $fieldId,
                $recordId,
                $oldData,
                $newData
            );

            $body = WorkflowTemplateRenderer::renderWithFieldLabels(
                $template->id,//added by ptpatel
                $template->body,
                $module,
                $tablename,
                $fieldId,
                $recordId,
                $oldData,
                $newData
            );
        } else {
            // fallback: minimal body
            $body = 'Record ' . $recordId . ' updated.';
        }
        // echo $subject;
        // echo $body;die;
        // print_r($to);die;
        try {
            // Fetch the send_email flag from the database
            $mailSentFlag = Yii::$app->db->createCommand('SELECT send_email FROM workflow_mail_sent WHERE id = 1')->queryScalar();

            if ($mailSentFlag === 'yes') {
                // Proceed with sending the email
                $result = Yii::$app->mailer->compose()
                    ->setFrom('erp@dwmpl.com')
                    ->setTo($emails)
                    ->setSubject($subject)
                    ->setHtmlBody($body)
                    ->send();

                $status = $result ? 'success' : 'failed';
            } else {
                // Email sending is disabled
                $result = false;
                $status = 'restricted';  // Custom status
            }

            // Log result
            $log = new WorkflowEmailLog();
            $log->module = $module;
            $log->record_id = $recordId;
            $log->email_to = is_array($emails) ? implode(',', $emails) : $emails;
            $log->subject = $subject;
            $log->body = $body;
            $log->status = $status;
            $log->save(false);

        } catch (\Exception $e) {

            // Log failure
            $log = new WorkflowEmailLog();
            $log->module = $module;
            $log->record_id = $recordId;
            $log->email_to = is_array($emails) ? implode(',', $emails) : $emails;
            $log->subject = $subject;
            $log->body = $body;
            $log->status = 'failed';
            $log->error = $e->getMessage();
            $log->save(false);
        }

    }

    private static function sendNotification($userId, $template, $recordId, $oldData, $newData)
    {
        $message = ($template ? strip_tags(self::renderTemplateBody($template->body, $recordId, $oldData, $newData)) : "Record {$recordId} updated");
        Yii::$app->db->createCommand()->insert('notification', [
            'user_id' => $userId,
            'message' => $message,
            'url' => null,
            'created_at' => date('Y-m-d H:i:s')
        ])->execute();
    }

    private static function renderTemplateBody($body, $recordId, $old, $new)
    {
        // Very light token replacement: {{id}}, {{old.field}}, {{new.field}}
        // Replace {{id}}
        $out = str_replace('{{id}}', $recordId, $body);

        // replace simple tokens {{old.xyz}} and {{new.xyz}}
        $out = preg_replace_callback('/\{\{\s*old\.([a-z0-9_]+)\s*\}\}/i', function ($m) use ($old) {
            $f = $m[1];
            return isset($old[$f]) ? htmlspecialchars((string) $old[$f]) : '';
        }, $out);

        $out = preg_replace_callback('/\{\{\s*new\.([a-z0-9_]+)\s*\}\}/i', function ($m) use ($new) {
            $f = $m[1];
            return isset($new[$f]) ? htmlspecialchars((string) $new[$f]) : '';
        }, $out);

        return $out;
    }
}
