<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AsignaturasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Asignaturas';
$this->params['breadcrumbs'][] = $this->title;
$rolesUsuario = Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id);
?>
<div class="asignaturas-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= (array_key_exists('administrador', $rolesUsuario)) ? Html::a('Crear Asignatura', ['create'], ['class' => 'btn btn-success']) : '' ?>
    </p>

    <?php
    if ($esAdministrador) {
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'nombre',
                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]);
    } else {
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                ($esAdministrador) ?
                        'nombre' :
                        [
                    'attribute' => 'asignaturas_id',
                    'label' => 'Nombre',
                    'value' => function($data) {
                        return app\models\Asignaturas::getNombrePorId($data->asignaturas_id);
                    },
                        ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} | {update} | {delete} | {alumnos} | {grupos} | {practicos}',
                    'buttons' => [
                        'view' => function($url, $model) {
                            $usuario = Yii::$app->user->identity->id;
                            $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
                            return Html::a('Ver Asignatura', ['asignaturas/view', 'id' => Yii::$app->security->encryptByPassword($model->asignaturas_id, $oUser->password)]);
                        },
                        'update' => function($url, $model) {
                            $usuario = Yii::$app->user->identity->id;
                            $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
                            return Html::a('Editar Asignatura', ['asignaturas/update', 'id' => Yii::$app->security->encryptByPassword($model->asignaturas_id, $oUser->password)]);
                        },
                        'delete' => function($url, $model) {
                            $usuario = Yii::$app->user->identity->id;
                            $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
                            return Html::a('Eliminar Asignatura', ['asignaturas/delete', 'id' => Yii::$app->security->encryptByPassword($model->asignaturas_id, $oUser->password)]);
                        },
                        'alumnos' => function($url, $model) {
                            $usuario = Yii::$app->user->identity->id;
                            $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
                            return Html::a('Asociar Alumnos a Asignatura', ['asignaturas-alumnos/index', 'asigid' => Yii::$app->security->encryptByPassword($model->asignaturas_id, $oUser->password)]);
                        },
                        'grupos' => function($url, $model) {
                            $usuario = Yii::$app->user->identity->id;
                            $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
                            return Html::a('Grupos Formados', ['grupos/index', 'asigid' => Yii::$app->security->encryptByPassword($model->asignaturas_id, $oUser->password)]);
                        },
                        'practicos' => function($url, $model) {
                            $usuario = Yii::$app->user->identity->id;
                            $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
                            return Html::a('Actividades', ['tareas/index', 'asigid' => Yii::$app->security->encryptByPassword($model->asignaturas_id, $oUser->password)]);
                        },
                    ],
                ],
            ],
        ]);
    }
    ?>
</div>
