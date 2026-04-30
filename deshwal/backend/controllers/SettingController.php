<?php

namespace backend\controllers;
use Yii;
use yii\web\Controller;
use yii\db\Query;

class SettingController extends Controller
{
    public function actionIndex()
    {
        $this->layout = '@app/views/layouts/main-one';

        $themes = (new \yii\db\Query())
                ->select(['id', 'name'])
                ->from('theme')
                ->where(['active' => 1])
                ->all();
        
        $themeId = Yii::$app->session->get('_theme_id');

        if (!$themeId && !Yii::$app->user->isGuest) {
            $themeId = (new Query())
                ->select('theme')
                ->from('user')
                ->where(['id' => Yii::$app->user->id])
                ->scalar();
        }

        if (!$themeId) {
            $themeId = (new Query())
                ->select('id')
                ->from('theme')
                ->where(['active' => 1])
                ->orderBy(['id' => SORT_ASC])
                ->scalar();
        }

        if ($themeId && !array_key_exists((int)$themeId, $themes)) {
            $themeId = null;
        }

        return $this->render('index', [
            'themes'       => $themes,  
            'currentTheme' => (int)$themeId,
        ]);
    }

    public function getActiveThemes()
    {
        return (new Query())
            ->select(['id', 'name'])
            ->from('theme')
            ->where(['active' => 1])
            ->indexBy('id')
            ->column();
    }

    public function getCurrentTheme()
    {
        $themeId = Yii::$app->session->get('_theme_id');
        if (!$themeId) {
            $themeId = (new Query())
                ->select(['id'])
                ->from('theme')
                ->where(['active' => 1])
                ->scalar();
        }
        if (!$themeId) return null;

        return (new Query())
            ->select(['primary', 'secondary', 'tertiary'])
            ->from('theme')
            ->where(['id' => $themeId, 'active' => 1])
            ->one();
    }

    private function setCurrentTheme($themeId)
    {
        $exists = (new Query())
            ->from('theme')
            ->where(['id' => $themeId, 'active' => 1])
            ->exists();

        if (!$exists) {
            return;
        }

        Yii::$app->session->set('_theme_id', $themeId);

        if (!Yii::$app->user->isGuest) {
            $userId = Yii::$app->user->id;
            Yii::$app->db->createCommand()
                ->update('user', ['theme' => $themeId], ['id' => $userId])
                ->execute();
        }
    }

    public function actionChangeTheme()
    {
        if (Yii::$app->request->isPost) {
            $id = (int)Yii::$app->request->post('id');
            $this->setCurrentTheme($id);
        }

        return $this->redirect(['setting/index']);
    }
}
