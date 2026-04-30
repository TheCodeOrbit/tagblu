<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use backend\assets\AppAsset;

AppAsset::register($this);
// <!-- choices css -->

$this->registerCssFile('@web/theme/libs/choice-js/choice-js.min.css', ['depends' => [AppAsset::class]]);

// <!-- datepicker css -->

$this->registerCssFile('@web/theme/libs/flatpickr/flatpickr.min.css', ['depends' => [AppAsset::class]]);




/** @var yii\web\View $this */
/** @var backend\models\Uiinputs $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="uiinputs-form">
    <?php $form = ActiveForm::begin([
        'id' => 'pristine-valid-example', // Ensure this ID matches the JavaScript
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>


    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput([
                'maxlength' => true,
                'id' => 'name',
                'data-pristine-required' => 'true',
                'data-pristine-required-message' => 'Name is required'
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'hidden_field')->textInput([
                'maxlength' => true,
                'id' => 'hidden_field',
                'data-pristine-required' => 'true',
                'data-pristine-required-message' => 'hidden field is required'
            ]) ?>

        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'password')->passwordInput([
                'maxlength' => true,
                'id' => 'password',
                'data-pristine-required' => 'true',
                'data-pristine-required-message' => 'password is required'
            ]) ?>

        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'textarea')
                ->textarea([
                    'rows' => 6,
                    'id' => 'textarea',
                    'data-pristine-required' => 'true',
                    'data-pristine-required-message' => 'Description is required'
                ])
                ->label('Please provide a detailed description') // Customized label text
                ->hint('You can write up to 500 words here.') // Additional hint text below the field
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'file')->fileInput([
                'maxlength' => true,
                'id' => 'file', // Changed the ID for accuracy
                'data-pristine-required' => 'true',
                'data-pristine-required-message' => 'Please upload the image'
            ])->label('Image') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'checkbox')->checkbox([
                'data-pristine-required' => 'true',
                'data-pristine-required-message' => 'You must agree to the terms and conditions.'
            ]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'listbox')->textInput([
                'maxlength' => true,
                'id' => 'listbox', // Changed the ID for accuracy
                'data-pristine-required' => 'true',
                'data-pristine-required-message' => 'Please enter the listbox'
            ]) ?>
        </div>
        <div class="col-lg-4 col-md-6">
            <?= $form->field($model, 'dropdown_single')->dropDownList(
                [
                    '' => 'This is a placeholder', // Placeholder option
                    'Choice 1' => 'Choice 1',
                    'Choice 2' => 'Choice 2',
                    'Choice 3' => 'Choice 3',
                ],
                [
                    'class' => 'form-control',
                    'id' => 'choices-single-default',
                    'data-trigger' => true, // Custom data attribute for triggering
                    'data-pristine-required' => 'true', // Validation attribute
                    'data-pristine-required-message' => 'Please select an option.', // Custom validation message
                ]
            )->label('Normal Dropdown') ?>
        </div>

        <div class=" col-md-6">
            <?= $form->field($model, 'checkboxlist')->checkboxList(
                [
                    'Option 1' => 'Option 1',
                    'Option 2' => 'Option 2',
                    'Option 3' => 'Option 3'
                ],
                [
                    'class' => 'form-control',
                    'itemOptions' => [
                        'class' => 'form-check-input mb-2', // Styling each checkbox
                        'data-pristine-required' => 'true',
                        'data-pristine-required-message' => 'Please select at least one checkbox.'
                    ],
                    'separator' => '<br>', // Ensure each checkbox is on a new line

                ]
            )->label('Checkboxes') ?>
        </div>




        <div class="col-md-6">
            <?= $form->field($model, 'radio_button')->radioList(
                [
                    'Option 1' => 'Option 1',
                    'Option 2' => 'Option 2',
                    'Option 3' => 'Option 3'
                ],
                [
                    'class' => 'form-control',
                    'itemOptions' => [
                        'class' => 'form-check-input mb-2',
                        'data-pristine-required' => 'true',
                        'data-pristine-required-message' => 'Please select an option.'
                    ],
                    'separator' => '<br>', // Ensure each radio button is on a new line
                ]
            )->label('Radio Options') ?>
        </div>


        <div class="col-md-6">
            <?= $form->field($model, 'referencetype')->textInput([
                'maxlength' => true,

                'id' => 'referencetype', // Changed the ID for accuracy
                'data-pristine-required' => 'true',
                'data-pristine-required-message' => 'Please enter the referencetype'
            ]) ?>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'DateTimePicker', [])->textInput([
                    'type' => 'text', // Ensures it's a text input for the date picker
                    'class' => 'form-control', // Add Bootstrap styling
                    'id' => 'datepicker-datetime', // Your custom ID for JavaScript targeting
                    'data-pristine-required' => 'true', // PristineJS required attribute
                    'data-pristine-required-message' => 'Please Select Date Time' // Custom validation message
                ])->label('DateTime') ?>
            </div>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'dropdown_multipe')->dropDownList(
                [
                    'Choice 1' => 'Choice 1',
                    'Choice 2' => 'Choice 2',
                    'Choice 3' => 'Choice 3',
                    'Choice 4' => 'Choice 4',
                    'Choice 5' => 'Choice 5',
                ],
                [
                    'class' => 'form-control',
                    'id' => 'choices-multiple-default',
                    'multiple' => true,
                    'data-trigger' => 'true',
                    'data-placeholder' => 'This is a placeholder',
                    'options' => [
                        'Choice 4' => ['disabled' => true], // Disable Choice 4
                    ],
                ]
            )->label('Multiple select dropdown') ?>
        </div>


        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'MonthYearPicker', [])->textInput([
                    'maxlength' => 7,
                    'type' => 'text', // Ensures it's a text input for the date picker
                    'class' => 'form-control', // Add Bootstrap styling
                    'id' => 'datepicker-humanfd', // Your custom ID for JavaScript targeting
                    'data-pristine-required' => 'true', // PristineJS required attribute
                    'data-pristine-required-message' => 'Please Select month year' // Custom validation message
                ])->label('MonthYearPicker') ?>
            </div>
        </div>




        <div class="col-md-6">

            <?= $form->field($model, 'date', [])->textInput([
                'type' => 'text', // Ensures it's a text input for the date picker
                'class' => 'form-control', // Add Bootstrap styling
                'id' => 'datepicker-basic', // Your custom ID for JavaScript targeting
                'data-pristine-required' => 'true', // PristineJS required attribute
                'data-pristine-required-message' => 'Please Select Date ' // Custom validation message
            ])->label('Date') ?>

        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'BatchList')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-md-6">

            <?= $form->field($model, 'maskingdate', [])->textInput([
                'type' => 'text', // Ensures it's a text input for the date picker
                'class' => 'form-control', // Add Bootstrap styling
                'id' => 'date-mask', // Your custom ID for JavaScript targeting
                'data-pristine-required' => 'true', // PristineJS required attribute
                'data-pristine-required-message' => 'Please Select Masked date ' // Custom validation message
            ])->label('Mask Date')->hint('"dd.mm.yyyy" in range [01.01.1990, 01.01.2020]')  ?>

        </div>
    </div>

    <div class="form-group">
        <?= Html::Button('Save', ['class' => 'btn btn-success savebutton']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php


$this->registerJsFile('@web/theme/libs/pristinejs/pristinejs.min.js', ['depends' => [AppAsset::class]]);
$this->registerJsFile('@web/theme/js/pages/form-validation.init.js', ['depends' => [AppAsset::class]]);
$this->registerJsFile('@web/theme/js/app.min.js', ['depends' => [AppAsset::class]]);

//   choices js -->
$this->registerJsFile('@web/theme/libs/choice-js/choice-js.min.js', ['depends' => [AppAsset::class]]);

// <!-- datepicker js -->
$this->registerJsFile('@web/theme/libs/flatpickr/flatpickr.min.js', ['depends' => [AppAsset::class]]);
// <!-- init js -->
$this->registerJsFile('@web/theme/js/pages/form-advanced.init.js', ['depends' => [AppAsset::class]]);

// <!-- form mask -->
$this->registerJsFile('@web/theme/libs/imask/imask.min.js', ['depends' => [AppAsset::class]]);

// <!-- form mask init -->
$this->registerJsFile('@web/theme/js/pages/form-mask.init.js', ['depends' => [AppAsset::class]]);



?>