<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$tipoUsuario = ($tipo == 'a') ? 'Alumnos' : (($tipo == 'd') ?'Docentes' : 'Administradores');
$this->title = "Crear $tipoUsuario";
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index', 't' => $tipo]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuarios-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
