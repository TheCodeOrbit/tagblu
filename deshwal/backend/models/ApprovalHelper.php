<?php

namespace backend\models;

use app\models\QuotesDit;
use Yii;

class ApprovalHelper
{
    public static function afterApproval($recordId, $status, $className)
    {
        //this is common method name which need to write each model
        $method = 'handleRelatedModuleStageChange';
        // echo "in approvalhelper".$module ."--M--".$method."--c--".$class;
        // Convert object to class name because we pass class object from arguments and it give error when checking class is exists or not
        if (is_object($className)) {
            $className = get_class($className);
        }

        $className = trim($className);

        if (!class_exists($className)) {
            // echo "\nClass $className not found";
            return;
        }

        if (method_exists($className, $method)) {
           $className::$method($recordId, $status);
        } else {
            // echo "\n $method NOT found in $className";
        }
    }
}
?>