<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Tareas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tareas-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'nombre_t')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'consigna')->textarea() ?>

    <?= $form->field($model, 'descripcion')->textarea() ?>

    <?= $form->field($model, 'year')->textInput() ?>
    
    <?= $form->field($model, 'usar_sentencias_apertura')->radioList(array('1'=>'Sí','0'=>'No')) ?>
    
    <?= $form->field($model, 'reportar_estado_animo')->radioList(array('1'=>'Sí','0'=>'No')) ?>
    
    <?= $form->field($model, 'reportar_conflicto')->radioList(array('1'=>'Sí','0'=>'No')) ?>
    
    <?= $form->field($model, 'grupos_id')->dropDownList(app\models\Grupos::getListaGrupos()) ?>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
