<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\EmocionesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="emociones-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'valencia') ?>

    <?= $form->field($model, 'activacion') ?>

    <?= $form->field($model, 'dominancia') ?>

<<<<<<< HEAD
    <?= $form->field($model, 'chats_id') ?>
=======
    <?= $form->field($model, 'sentencias_id') ?>
>>>>>>> 05b434acad30769acee29f0a6d2da576e66b11f2

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
