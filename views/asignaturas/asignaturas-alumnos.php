<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AsignaturasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Asignaturas';
$this->params['breadcrumbs'][] = $this->title;

$usuario = Yii::$app->user->identity->id;
$oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
$rolesUsuario = Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id);
?>
<div class="asignaturas-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <p>
        <?= (array_key_exists('estudiante', $rolesUsuario)) ? Html::a('Asociarse a asignatura', ['asignaturas-alumnos/create-asociation'], ['class' => 'btn btn-success']) : '' ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'asignaturas_id',
                'label' => 'Asignaturas',
                'value' => function($data) {
                    return app\models\Asignaturas::getNombrePorId($data->asignaturas_id);
                },
            ],
            'year',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{practicos}',
                'buttons' => [
                    'practicos' => function($url, $model) {
                        $usuario = Yii::$app->user->identity->id;
                        $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
                        return Html::a('Actividades', ['tareas/tareas-alumnos', 'asigid' => Yii::$app->security->encryptByPassword($model->asignaturas_id, $oUser->password), 'year' => Yii::$app->security->encryptByPassword($model->year, $oUser->password)]);
                    },
                ],
            ],
        ],
    ]);
    ?>
</div>
