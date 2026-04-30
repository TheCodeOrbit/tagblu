<?php

namespace app\models;

use app\models\ApprovalRule;
use app\models\ApprovalInstance;
use app\models\ApprovalHistory;
use Yii;

class ApprovalEngine
{
    public function sendForApproval($module, $column, $level, $data)
    {
        $rule = ApprovalRule::find()->where(['module' => $module, 'action_code' => $level, 'action_label' => $column])->orderBy('id ASC')->one();
        if (!$rule) {
            return '';
            // throw new \Exception("No approval rule found for module: $module");
        } else {
            $rule_data = $rule->toArray();


            if (isset($rule_data['destination_role']) && !empty($rule_data['destination_role'])) {
                if ($rule_data['destination_role'] == 'creatorid') {
                    $rule_data['ownerid'] = $data['creatorid'];
                } else if ($rule_data['destination_role'] == 'modifiedby') {
                    $rule_data['ownerid'] = $data['modifiedby'];
                } else {
                    $reports = "-- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = '" . $rule_data['destination_role'] . "'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = '" . $rule_data['destination_role'] . "'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $module . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = '" . $rule_data['destination_role'] . "'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $module . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;";
                    // echo $reports;
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $ownerid = $rest['id'];
                        $rule_data['ownerid'] = $ownerid;
                    }
                }

            }

        }

        // print_r($rule_data);die;

        return $rule_data;
    }

    // public function approve(ApprovalInstance $instance, $userId, $data, $action, $remarks = null)
    // {
    //     $rule = $instance->rule;
    //     $nextRule = ApprovalRule::find()
    //         ->where(['module' => $rule->module])
    //         ->andWhere(['>', 'level', $instance->level])
    //         ->orderBy('level ASC')
    //         ->one();

    //     $this->saveHistory($instance->id, $userId, 'APPROVE', $remarks);

    //     if ($nextRule) {
    //         $instance->level = $nextRule->level;
    //         $instance->status = 'PENDING';
    //     } else {
    //         $instance->status = 'APPROVED';
    //     }

    //     $instance->updated_at = date('Y-m-d H:i:s');
    //     $instance->save(false);

    //     return $instance;
    // }

    // public function reject(ApprovalInstance $instance, $userId, $remarks = null)
    // {
    //     $instance->status = 'REJECTED';
    //     $instance->updated_at = date('Y-m-d H:i:s');
    //     $instance->save(false);

    //     $this->saveHistory($instance->id, $userId, 'REJECT', $remarks);
    //     return $instance;
    // }

    // public function modify(ApprovalInstance $instance, $userId, $remarks = null)
    // {
    //     $instance->status = 'MODIFY';
    //     $instance->save(false);

    //     $this->saveHistory($instance->id, $userId, 'MODIFY', $remarks);
    //     return $instance;
    // }

    // private function saveHistory($instanceId, $userId, $action, $remarks)
    // {
    //     $history = new ApprovalHistory();
    //     $history->instance_id = $instanceId;
    //     $history->action_by = $userId;
    //     $history->action = $action;
    //     $history->remarks = $remarks;
    //     $history->created_at = date('Y-m-d H:i:s');
    //     $history->save(false);
    // }
}