<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Asignaturas */

$this->title = html_entity_decode('Actualizaci&oacute;n de Datos de la Asignatura');
$this->params['breadcrumbs'][] = ['label' => 'Asignaturas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="asignaturas-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
