<?php
namespace backend\components;

use Yii;
use yii\base\Behavior;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class ActiveProfileValidator extends Behavior
{
    /**
     * Attach event before any controller action executes.
     */
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'validateActiveProfile'
        ];
    }

    /**
     * Validate Active Profile Session and Ownership.
     */
    public function validateActiveProfile($event)
    {
        $route = Yii::$app->controller->route;

        // Skip checks for guests
        if (Yii::$app->user->isGuest) {
            return true;
        }

        // Routes to ignore validation
        $skipRoutes = [
            'site/login',
            'site/logout',
            'site/select-profile',
            'site/set-profile',
            'debug/default/toolbar',      // Debug toolbar
            'debug/default/view',         // Debug view
        ];

        if (in_array($route, $skipRoutes)) {
            return true;
        }

        // Get active profile from session
        $activeProfileId = Yii::$app->session->get('active_profile_id');

        // If no active profile → redirect to selection page
        if (!$activeProfileId || $activeProfileId == '' && $activeProfileId == null) {
            return $this->handleMissingProfile($route);
        }

        // Validate ownership: profile must belong to logged-in user
        $userId = Yii::$app->user->id;

        $isValid = (new \yii\db\Query())
            ->from('user2role')  // Change if your table name differs
            ->where(['roleid' => $activeProfileId, 'userid' => $userId])
            ->exists();

        if (!$isValid) {
            Yii::$app->session->remove('active_profile_id');
            throw new ForbiddenHttpException("Invalid or tampered profile session.");
        }

        return true;
    }

    /**
     * If active profile missing, redirect user (including AJAX-safe handling)
     */
    private function handleMissingProfile($route)
    {
        $response = Yii::$app->response;

        // For AJAX requests → return JSON error
        if (Yii::$app->request->isAjax) {
            $response->format = Response::FORMAT_JSON;
            $response->data = [
                'error' => true,
                'message' => 'Active profile not selected.',
                'redirect' => Yii::$app->urlManager->createUrl(['site/select-profile'])
            ];
            return $response->send();
        }
        // For normal requests → redirect to profile selection
        Yii::$app->session->setFlash('warning', 'Please select an active profile.');
        $response->redirect(['site/select-profile']); 
        $response->send();                         
        Yii::$app->end();
        
    }
}
