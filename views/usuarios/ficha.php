<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */
$rolesUsuario = Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id);
$parametrot = array_key_exists('profesor', $rolesUsuario) ? 'a' :'u';

$this->title = '';
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index', 't' => $parametrot]];
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="usuarios-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'username',
            'nombre',
            'apellido',
            [
                'attribute' => 'fechanacimiento',
                'label' => 'Fecha de Nacimiento',
                'value' => function($data) {
                    list($year, $mes, $dia) = explode("-", $data->fechanacimiento);
                    return substr($dia, 0, 2) . "/$mes/$year";
                },
            ],
            'estiloaprendizaje',
            'email',            
        ],
    ]) ?>

</div>
