<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ConflictosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Conflictos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conflictos-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Conflictos', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'time',
            'chats_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
