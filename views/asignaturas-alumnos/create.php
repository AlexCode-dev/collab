<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\AsignaturasAlumnos */

$this->title = 'Inscripción de Alumnos';
$this->params['breadcrumbs'][] = ['label' => 'Asociar Alumnos', 'url' => ['asignaturas-alumnos/index', 'asigid' => $asigid]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="asignaturas-alumnos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
