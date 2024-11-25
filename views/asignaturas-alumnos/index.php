<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AsignaturasAlumnosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$usuario = Yii::$app->user->identity->id;
$oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
$this->title = 'Asociar Alumnos en ' . app\models\Asignaturas::findOne(['id' => $asigid])->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Asignaturas', 'url' => ['asignaturas/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="asignaturas-alumnos-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Asociar Alumno', ['create', 'asigid' => Yii::$app->security->encryptByPassword($asigid, $oUser->password)], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'usuarios_id',
                'label' => 'Alumno',
                'value' => function($data) {
                    return app\models\Usuarios::getNombrePorId($data->usuarios_id);
                },
            ],
            'year',            

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
