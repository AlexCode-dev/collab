<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Grupos */
$usuario = Yii::$app->user->identity->id;
$oUser = \app\models\Usuarios::findOne(['id' => $usuario]);

$asignatura = app\models\Asignaturas::findOne(['id' => $model->asignaturas_id])->nombre;
$this->title = "Grupos Formados en $asignatura";
$this->params['breadcrumbs'][] = ['label' => 'Grupos', 'url' => ['index', 'asigid' => Yii::$app->security->encryptByPassword($model->asignaturas_id, $oUser->password)]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grupos-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'codigo',
            'year',            
            [
                'attribute' => 'metodos_formacion_id',
                'label' => 'Método de Formación',
                'value' => function($data) {
                    return app\models\MetodosFormacion::getNombrePorId($data->metodos_formacion_id);
                },
            ],
        ],
    ]) ?>
    
    <?php 
  $grupos = app\models\GruposFormados::getDetalleGrupos($model->id);
  $titulo = "";
  foreach ($grupos as $gr) {
      if ($titulo != $gr["nombre"]) {
          $varID = Yii::$app->security->encryptByPassword($gr["id"], $oUser->password);
          echo "<h2>" . Html::encode($gr["nombre"]) . "-" . Html::encode($gr["id"]) . " " . Html::a('Eliminar', ['grupos-formados/delete', 'id' => $varID, 'model_id' => $model->id], [
              'class' => 'btn btn-danger',
              'data' => [
                  'confirm' => 'Está seguro que desea eliminar este grupo?',
                  'method' => 'post',
              ],
          ]) . "</h2>";
          $titulo = $gr["nombre"];
      }
      echo Html::encode($gr["apellidoAlumno"]) . ", " . Html::encode($gr["nombreAlumno"]) . "<br/>";
  }
    ?>

</div>
