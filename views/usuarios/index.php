<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UsuariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
$tipoUsuario = ($tipo == 'a') ? 'Alumnos' : (($tipo == 'd') ? 'Docentes' : (($tipo == 'm') ? 'Administradores' : 'Usuarios'));
$this->title = $tipoUsuario;
$rolesUsuario = Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id);
?>
<div class="usuarios-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a("Crear " . $tipoUsuario, ['create', 't' => $tipo], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
            //'password',
            'nombre',
            'apellido',
            //'tipo:boolean',
            (array_key_exists('administrador', $rolesUsuario)) ? 'tipo' : 
            'email',
            'estiloaprendizaje',
            [
                'attribute' => 'personalidad',
                'label' => 'Personalidad',
                'format' => 'html',
                'value' => function($data){
                    $personalidad = '';                    
                    if (isset($data->personalidad) && strlen($data->personalidad) > 0) {
                        list($extra, $agrea, $consc, $neuro, $openn) = explode(",", $data->personalidad);
                        $extra = explode(':', $extra);
                        $agrea = explode(':', $agrea);
                        $consc = explode(':', $consc);
                        $neuro = explode(':', $neuro);
                        $openn = explode(':', $openn);
                        $personalidad = "Extroversión: " . $extra[1] . "<br/>";
                        $personalidad .= "Afabilidad: " . $agrea[1] . "<br/>";
                        $personalidad .= "Excrupulosidad: " . $consc[1] . "<br/>";
                        $personalidad .= "Neuroticismo: " . $neuro[1] . "<br/>";
                        $personalidad .= "Apertura: " . $openn[1] . "<br/>";
                    }
                    return $personalidad;
                }
            ],
            [
                'attribute' => 'pais_idpais',
                'label' => 'País',
                'value' => function($data) {
                    return app\models\Pais::getNombrePorId($data->pais_idpais);
                },
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
