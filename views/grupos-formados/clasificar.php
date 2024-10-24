<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\GruposFormados */
/* @var $tarea app\models\Tareas */
<<<<<<< HEAD
/* @var $chat app\models\Chats */

$this->title = 'Clasificar Grupo: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Grupos Formados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

=======

$this->title = 'Clasificar Grupo: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Grupos Formados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


>>>>>>> 738f8d5f4e3524f9b29eacb1792cac1dc4cdf247
?>

<div class="grupos-formados-clasificar">

    <h1><?= Html::encode($this->title) ?></h1>

<<<<<<< HEAD
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
=======
    <p>Grupo <?= Html::encode($model->nombre) ?> - ID: <?= Html::encode($model->id) ?></p>
    <?php $integrantes = \app\models\GruposFormados::getDetalleGrupo($model->id); ?>
    <!-- <?php //var_dump($integrantes)?> -->
    <p>Integrantes:</p>
    <ul>
        <?php foreach ($integrantes as $integrante): ?>
            <li><?= Html::encode($integrante['nombreAlumno'] . ' ') ?></li>
        <?php endforeach; ?>
    </ul>
    <p>Consigna de tarea:</p>
    <p><?= $tareaExistente ? Html::encode($tareaExistente->consigna) : 'No hay consigna disponible.' ?></p>

    <?php $form = ActiveForm::begin(); ?>

<?php var_dump($tarea->grupos_id)?>
<?= $form->field($tarea, 'nota')->textInput() ?>
<?= $form->field($tarea, 'descripcion_nota')->textarea(['rows' => 6]) ?>

<div class="form-group">
    <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
>>>>>>> 738f8d5f4e3524f9b29eacb1792cac1dc4cdf247

</div>
