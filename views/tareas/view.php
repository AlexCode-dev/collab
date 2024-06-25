<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Tareas */
$usuario = Yii::$app->user->identity->id;
$oUser = \app\models\Usuarios::findOne(['id' => $usuario]);

$this->title = 'Actividad Correspondiente a ' . app\models\Asignaturas::findOne(['id' => $model->asignaturas_id])->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Tareas', 'url' => ['index', 'asigid' => Yii::$app->security->encryptByPassword($model->asignaturas_id, $oUser->password)]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tareas-view">

    <h1><?= Html::encode($this->title) ?></h1>    

    <p>
        <?= Html::a('Actualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a('Eliminar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Está seguro que desea eliminar esta tarea?',
                'method' => 'post',
            ],
        ])
        ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'nombre_t',
                'label' => 'Actividad',
            ],
            'consigna',
            'descripcion',            
            'year',
            [
                'attribute' => 'usar_sentencias_apertura',
                'label' => 'Usa Sentencias de Apertura',
                'value' => function($data) {
                    return ($data->usar_sentencias_apertura) ? 'Sí' : 'No';
                },
            ],
            [
                'attribute' => 'reportar_estado_animo',
                'label' => 'Permite Reportar Estado de Ánimo',
                'value' => function($data) {
                    return ($data->reportar_estado_animo) ? 'Sí' : 'No';
                },
            ],
            [
                'attribute' => 'reportar_conflicto',
                'label' => 'Permite Reportar Conflictos',
                'value' => function($data) {
                    return ($data->reportar_conflicto) ? 'Sí' : 'No';
                },
            ],
        ],
    ])
    ?>

    <?php
    $chatsxGrupo = app\models\Chats::getChatsGrupos($model->id);
    ?>

    <h2>Chats asociados a los grupos</h2>
    <?php foreach ($chatsxGrupo as $alumno): ?>
        Grupo <?= $alumno["grupos_formados_id"] ?> - <?= $alumno["alumnos"] ?> [<?= Html::a('ver chat', ['chats/grupo', 'chatid' => Yii::$app->security->encryptByPassword($alumno["id"], $oUser->password)]) ?>]<br/>
    <?php endforeach; ?>

</div>
