<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\GruposFormados */
/* @var $tarea app\models\Tareas */
/* @var $chat app\models\Chats */

$this->title = 'Clasificar Grupo: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Grupos Formados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="grupos-formados-clasificar">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $integrantes = \app\models\GruposFormados::getDetalleGrupo($model->id); ?>
    
    <p>Integrantes:</p>
    <ul>
        <?php foreach ($integrantes as $integrante): ?>
            <li><?= Html::encode($integrante['nombreAlumno'] . ' '. $integrante['apellidoAlumno']) ?></li>
        <?php endforeach; ?>
    </ul>
    
    <p>Consigna de tarea:</p>
    <p><?= Html::encode($tarea->consigna) ?></p>
    <p>Descripción de tarea:</p>
    <p><?= Html::encode($tarea->descripcion) ?></p>
    <?php $form = ActiveForm::begin(); ?>
    
    <div class="form-group">
        <?php if ($chat): ?>
            <?= $form->field($chat, 'nota')->textInput() ?>
            <?= $form->field($chat, 'descripcion_nota')->textarea(['rows' => 6]) ?>
        <?php else: ?>
            <p>No se encontró chat asociado a este grupo.</p>
        <?php endif; ?>
        <?= Html::submitButton('Calificar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
