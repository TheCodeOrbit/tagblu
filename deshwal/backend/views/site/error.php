<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception*/

use yii\helpers\Html;

$this->title = $name;
// echo "deep";die;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
       <h3> <?= nl2br(Html::encode($message)) ?></h3>
    </div>

    <p>
        The above error occurred while the Web server was processing your request.
    </p>
    <p>
        Please contact us if you think this is a server error. Thank you.
    </p>
    <p><?= Html::a('Go back to homepage', Yii::$app->homeUrl) ?></p>

</div>
