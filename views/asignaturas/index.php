<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AsignaturasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Asignaturas';
$this->params['breadcrumbs'][] = $this->title;
$rolesUsuario = Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id);
$usuario = Yii::$app->user->identity->id;
$oUser = \app\models\Usuarios::findOne(['id' => $usuario]); // Obtén los datos del usuario una vez

?>

<div class="asignaturas-index">

    <div class="title-container">
        <h2 class="perfil-title"><span>Mis</span> Asignaturas 📚</h2>
        <p>En esta sección, puedes gestionar y consultar todas tus asignaturas actuales. Aquí encontrarás las materias en las que estás inscrito, y podrás acceder a las actividades, grupos de trabajo y más.</p>
    </div>

    <?php if ($esAdministrador): ?>
        <div class="text-right mb-3">
            <?= Html::a('Crear Asignatura', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($dataProvider->models as $model): ?>
            <div class="col-md-5 mb-4">
                <div class="card asignatura-card">
                    <div class="card-body">
                    <?php if (array_key_exists('administrador', $rolesUsuario)): ?>
                    <h5 class="card-title"><?= Html::encode($model->nombre) ?></h5>
                    <?php endif; ?>

                        <div class="buttons">
                        <?= Html::a('Ver Asignatura', ['asignaturas/view', 'id' => Yii::$app->security->encryptByPassword($model->id, $oUser->password)], ['class' => 'btn btn-primary btn-block mb-2']) ?>

                            
                            <?php if (array_key_exists('administrador', $rolesUsuario)): ?>
                                <?= Html::a('Editar nombre de asignatura', ['asignaturas/update', 'id' => Yii::$app->security->encryptByPassword($model->id, $oUser->password)], ['class' => 'btn btn-warning btn-block mb-2']) ?>


                                <?= Html::a('Eliminar de asignatura', ['asignaturas/delete', 'id' => Yii::$app->security->encryptByPassword($model->id, $oUser->password)], ['class' => 'btn btn-danger btn-block mb-2', 'data-method' => 'post']) ?>

                            <?php endif; ?>

                            <?= Html::a('Asociar alumnos a la asignatura', ['asignaturas-alumnos/index', 'asigid' => Yii::$app->security->encryptByPassword($model->id, $oUser->password)], ['class' => 'btn btn-info btn-block mb-2']) ?>

                            <?= Html::a('Manejar Grupos', ['grupos/index', 'asigid' => Yii::$app->security->encryptByPassword($model->id, $oUser->password)], ['class' => 'btn btn-primary btn-block mb-2']) ?>

                            <?= Html::a('Actividades y Eventos', ['tareas/index', 'asigid' => Yii::$app->security->encryptByPassword($model->id, $oUser->password)], ['class' => 'btn btn-success btn-block']) ?>

                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Añadir paginador -->
    <div class="text-center">
        <?= LinkPager::widget([
            'pagination' => $dataProvider->pagination,
        ]) ?>
    </div>

<<<<<<< HEAD
=======
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
                            return Html::a('Ver', ['asignaturas/view', 'id' => Yii::$app->security->encryptByPassword($model->asignaturas_id, $oUser->password)]);
                        },
                        'update' => function($url, $model) {
                            $usuario = Yii::$app->user->identity->id;
                            $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
                            return Html::a('Editar', ['asignaturas/update', 'id' => Yii::$app->security->encryptByPassword($model->asignaturas_id, $oUser->password)]);
                        },
                        'delete' => function($url, $model) {
                            $usuario = Yii::$app->user->identity->id;
                            $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
                            return Html::a('Eliminar', ['asignaturas/delete', 'id' => Yii::$app->security->encryptByPassword($model->asignaturas_id, $oUser->password)]);
                        },
                        'alumnos' => function($url, $model) {
                            $usuario = Yii::$app->user->identity->id;
                            $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
                            return Html::a('Asociar Alumnos', ['asignaturas-alumnos/index', 'asigid' => Yii::$app->security->encryptByPassword($model->asignaturas_id, $oUser->password)]);
                        },
                        'grupos' => function($url, $model) {
                            $usuario = Yii::$app->user->identity->id;
                            $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
                            return Html::a('Grupos', ['grupos/index', 'asigid' => Yii::$app->security->encryptByPassword($model->asignaturas_id, $oUser->password)]);
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
>>>>>>> 738f8d5f4e3524f9b29eacb1792cac1dc4cdf247
</div>

<?php

// Añadir el estilo CSS directamente en la vista o cargarlo desde un archivo CSS
$this->registerCss('
    .asignatura-card {
        border: 1px solid #ddd;
        border-radius: 10px;
        transition: transform 0.3s, box-shadow 0.3s;
        margin-bottom: 20px;
        padding: 20px;
        background-color: #ffffff;
    }
    .asignatura-card:hover {
        transform: scale(1.03);
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    .asignatura-card h5 {
        font-size: 1.4rem;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .asignatura-card .btn {
        margin-bottom: 10px;
    }
');
