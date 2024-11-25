<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AsignaturasAlumnos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="asignaturas-alumnos-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'usuarios_id')->dropDownList(app\models\Usuarios::getListaAlumnos())->label('Alumno');?>

    <?= $form->field($model, 'year')->textInput() ?>    

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
