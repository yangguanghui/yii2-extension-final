<?php

use yangguanghui\extFinal\generators\db\Generator;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yangguanghui\extFinal\generators\model\Generator */

echo $form->field($generator, 'dbDriver')->dropDownList(
    $generator->getValidDriver()
);
echo $form->field($generator, 'dbHost');
echo $form->field($generator, 'dbPort');
echo $form->field($generator, 'dbName');
