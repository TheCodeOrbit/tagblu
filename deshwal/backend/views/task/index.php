<!-- views/task/index.php -->
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

use backend\assets\AppAsset;

AppAsset::register($this);

$this->registerCssFile('https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css', ['depends' => [AppAsset::class]]);

$this->title = 'Task Management';
$this->params['breadcrumbs'][] = $this->title;


?>

<div class="task-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-4">
            <h3>High Priority</h3>
            <ul id="high-priority" class="task-list">
                <?php foreach ($highTasks as $task): ?>
                    <li data-id="<?= $task->id ?>" class="task-item"><?= Html::encode($task->title) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="col-md-4">
            <h3>Medium Priority</h3>
            <ul id="medium-priority" class="task-list">
                <?php foreach ($mediumTasks as $task): ?>
                    <li data-id="<?= $task->id ?>" class="task-item"><?= Html::encode($task->title) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="col-md-4">
            <h3>Low Priority</h3>
            <ul id="low-priority" class="task-list">
                <?php foreach ($lowTasks as $task): ?>
                    <li data-id="<?= $task->id ?>" class="task-item"><?= Html::encode($task->title) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<?php
$this->registerJsFile( 'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', ['depends' => [AppAsset::class]]);

$updatePriorityUrl = Url::to(['task/update-priority']);
$js = <<<JS
    $('.task-list').sortable({
        connectWith: '.task-list',
        placeholder: 'ui-state-highlight',
        update: function(event, ui) {
            var priority = $(this).attr('id').split('-')[0]; // high, medium, low
            var taskId = ui.item.data('id');

            // Capitalize the first letter of priority
            priority = priority.charAt(0).toUpperCase() + priority.slice(1);

            $.ajax({
                url: '{$updatePriorityUrl}',
                type: 'POST',
                data: {
                    id: taskId,
                    priority: priority,
                    _csrf: yii.getCsrfToken()
                },
                success: function(response) {
                    if (response.success) {
                        // Optionally, show a success message
                        console.log('Priority updated successfully.');
                    } else {
                        var errorMsg = 'Failed to update priority.';
                        if (response.message) {
                            if (typeof response.message === 'string') {
                                errorMsg += ' ' + response.message;
                            } else if (typeof response.message === 'object') {
                                // If it's an array of errors
                                var errors = [];
                                $.each(response.message, function(key, messages) {
                                    errors.push(key + ': ' + messages.join(', '));
                                });
                                errorMsg += ' ' + errors.join('; ');
                            }
                        }
                        alert(errorMsg);
                        // Optionally, reload the page to reset
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error);
                    location.reload();
                }
            });
        }
    }).disableSelection();
JS;
$this->registerJs($js, View::POS_READY);
?>

<style>
.task-list {
    list-style-type: none;
    padding: 0;
    min-height: 200px;
    border: 1px solid #ddd;
    background-color: #f9f9f9;
}

.task-item {
    padding: 8px;
    margin: 5px;
    background-color: #e0e0e0;
    border: 1px solid #c0c0c0;
    cursor: move;
}
</style>
