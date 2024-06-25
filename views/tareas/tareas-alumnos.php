<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TareasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$asignatura = app\models\Asignaturas::findOne(['id' => $asigid])->nombre;
$this->title = "Actividades";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tareas-index">

    <h1><?= Html::encode($asignatura) ?></h1>
    <h2><?= Html::encode($this->title) ?></h2>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'nombre_t',
                'label' => 'Actividad',
            ],
            'consigna',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{chat}',
                'buttons' => [
                    'chat' => function($url, $model) {
                        $usuario = Yii::$app->user->identity->id;
                        $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
                        
                        $chats = \app\models\Chats::find()->where(['tareas_id' => $model->id])->all();
                        $idChat = 0;
                        foreach ($chats as $ch) {                            
                            $grupo = \app\models\GruposAlumnos::findOne(['grupos_formados_id' => $ch->grupos_formados_id, 'usuarios_id' => $usuario]);
                            if ($grupo){
                                $idChat = $ch->id;
                            } 
                        }
                        return Html::a('Chat', ['chats/grupo', 'chatid' => Yii::$app->security->encryptByPassword($idChat, $oUser->password)]);
                    },
                ],
            ],
        ],
    ]);
    ?>
</div>
