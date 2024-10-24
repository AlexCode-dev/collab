<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GruposSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$usuario = Yii::$app->user->identity->id;
$oUser = \app\models\Usuarios::findOne(['id' => $usuario]);

$this->title = 'Grupos Formados en ' . app\models\Asignaturas::findOne(['id' => $asigid])->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Asignaturas', 'url' => ['asignaturas/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grupos-index">

    <h2 class="perfil-title"><?= Html::encode($this->title) ?><span>.</span></h2>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        En esta sección, se crean los códigos de los grupos a los cuales se les asignarán las actividades
        correspondientes. Es importante
        <span style="color:#FD8916;">tener en cuenta el año</span> asignado a cada grupo, ya que, dependiendo del año,
        se podrán seleccionar los alumnos asociados a ese grupo.
        Los alumnos pueden asociarse a la materia en el año actual en el que estamos, o bien pueden hacerlo desde el
        menú de "Asignaturas" utilizando el botón
        <strong>"Asociar alumnos a asignaturas"</strong>.
    </p>
    <p>
        <?= Html::a('Crear Grupos', ['create', 'asigid' => Yii::$app->security->encryptByPassword($asigid, $oUser->password)], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'codigo',
            'year',
            [
                'attribute' => 'metodos_formacion_id',
                'label' => 'Método de Formación',
                'value' => function($data) {
                    return app\models\MetodosFormacion::getNombrePorId($data->metodos_formacion_id);
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model) {
                        $contenido = '<span class="glyphicon glyphicon-eye-open"></span>';
                        return Html::a($contenido, ['grupos/view', 'id' => $model->id], ['title' => 'Ver']);
                    },
                ],
            ],
        ],
    ]);
    ?>
</div>