<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AsignaturasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mis Asignaturas';
$this->params['breadcrumbs'][] = $this->title;

$usuario = Yii::$app->user->identity->id;
$oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
$rolesUsuario = Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id);
?>
<div class="asignaturas-index">

    <div class="title-container">
      
            <?= (array_key_exists('estudiante', $rolesUsuario)) ? Html::a('Inscribite a una asignatura', ['asignaturas-alumnos/create-asociation'], ['class' => 'button-g4']) : '' ?>
            <?= (array_key_exists('estudiante', $rolesUsuario)) ? Html::a(' Progreso en todas las actividades gamificadas🎯', ['mis-logros-y-desafios/index'], ['class' => 'button-g']) : '' ?>
           

       
    </div>

    <div class="card-container">
        <?php foreach ($dataProvider->models as $model): ?>
        <div class="card">
            <div class="card-header">
                <h3><?= Html::encode(app\models\Asignaturas::getNombrePorId($model->asignaturas_id)) ?></h3>
            </div>
            <div class="card-body">
                <p><strong>Año: </strong><?= Html::encode($model->year) ?></p>
            </div>
            <div class="card-footer">
                <?= Html::a('Ver Actividades', ['tareas/tareas-alumnos', 'asigid' => Yii::$app->security->encryptByPassword($model->asignaturas_id, $oUser->password), 'year' => Yii::$app->security->encryptByPassword($model->year, $oUser->password)], ['class' => 'button-g3 ']) ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

<style>

.title-container{
    margin-top: 30px;
    margin-bottom:30px;

}
.card-container {
    font-family: "Poppins", sans-serif;
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start;
    align-content: center;
    gap: 30px;
}

.card {
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    width: 23%;
    margin-bottom: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
}

.card-header {
    background-color:#EB6500;
    color: white;
    padding: 30px;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    text-align: center;
    height: 200px;
    
 
}

.card-header h3 {
    font-size: 14px !important;
    font-weight:400;
}

.card-body {
    padding: 15px;
    flex-grow: 1;
}

.card-footer {
    padding: 10px;
    text-align: center;
}

.card-footer .btn {
    width: 100%;
}

.button-g3 {
    background-color: #38928D;
    padding: 20px 30px 20px 30px;
    border-radius: 10px;
    text-decoration: none;
    color: #ffffff;
    font-size: 16px;
    font-weight: 600;
}
.button-g3:hover {
    color: #fff;
    text-decoration: none;
}

.button-g4 {
    background: #38928D;
    padding: 20px 30px 20px 30px;
    border-radius: 10px;
    text-decoration: none;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
  
}
.button-g4:hover {
    color: #fff !important;
    background:#00103C !important;
    text-decoration: none;
}


</style>