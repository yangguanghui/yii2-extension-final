<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yangguanghui\extFinal\components\ActiveField;
use yangguanghui\extFinal\CodeFile;

/* @var $this yii\web\View */
/* @var $generator yangguanghui\extFinal\Generator */
/* @var $id string panel ID */
/* @var $form yii\widgets\ActiveForm */
/* @var $results string */
/* @var $hasError boolean */
/* @var $files CodeFile[] */
/* @var $answers array */

$this->title = $generator->getName();
$templates = [];
foreach ($generator->templates as $name => $path) {
    $templates[$name] = "$name ($path)";
}
?>
<div class="default-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= $generator->getDescription() ?></p>

    <?php $form = ActiveForm::begin([
        'id' => "$id-generator",
        'successCssClass' => '',
        'fieldConfig' => ['class' => ActiveField::className()],
    ]); ?>
        <div class="row">
            <div class="col-lg-8 col-md-10">
                <?= $this->renderFile($generator->formView(), [
                    'generator' => $generator,
                    'form' => $form,
                ]) ?>
                <div class="form-group">
                    <?= Html::submitButton('Save', ['name' => 'save', 'class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>

        <?php
        if (isset($results)) {
            echo $this->render('view/results', [
                'generator' => $generator,
                'results' => $results,
                'hasError' => $hasError,
            ]);
        }
        ?>
    <?php ActiveForm::end(); ?>
</div>
