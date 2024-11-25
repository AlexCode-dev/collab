<?php
use yii\widgets\LinkPager;
use yii\helpers\Html;

?>

<h2 class="perfil-title"><span>Tabla de </span>Posiciones 🎯</h2>
<p>¡Consulta la tabla de posiciones para ver quién lidera el camino! 🚀💥 Cada participación, actividad y desafío te acerca más a la cima. ¿Estás listo para competir, subir de rango y ser el mejor? ¡Mantente motivado, sigue participando y escala hasta lo más alto!</p>

<div class="leaderboard-container">
    <?php foreach ($dataProvider->models as $index => $usuario): ?>
        <div class="leaderboard-entry <?= $index < 3 ? 'top-three' : '' ?>"> <!-- Clase especial para los primeros 3 -->
            <div class="leaderboard-position"><?= $index + 1 + ($dataProvider->pagination->page * $dataProvider->pagination->pageSize) ?></div>
            <div class="leaderboard-photo">
                <?php if (!empty($usuario['foto_perfil'])): ?>
                    <img src="<?= Yii::getAlias('@web/' . $usuario['foto_perfil']) ?>" alt="Foto de perfil" class="profile-picture">
                <?php else: ?>
                    <img src="<?= Yii::getAlias('@web/uploads/default_profile.png') ?>" alt="Sin foto" class="profile-picture">
                <?php endif; ?>
            </div>
            <div class="leaderboard-name"><?= Html::encode($usuario['nombre'] . ' ' . $usuario['apellido']) ?></div>
            <div class="leaderboard-rank">
                <?php if (!empty($usuario['rango_imagen'])): ?>
                    <img src="<?= Yii::getAlias('@web/' . $usuario['rango_imagen']) ?>" alt="Imagen del rango" class="rank-image">
                <?php endif; ?>
                <?= Html::encode($usuario['rango_nombre']) ?>
            </div>
            <div class="leaderboard-score"><?= Html::encode($usuario['puntaje']) ?> pts</div>
        </div>
    <?php endforeach; ?>
</div>


<!-- Paginación -->
<div class="pagination-container text-center">
    <?= LinkPager::widget([
        'pagination' => $dataProvider->pagination,
    ]) ?>
</div>

<style>
    /* Los estilos se mantienen iguales */
    .leaderboard-container {
        display: flex;
        flex-direction: column;
        width: 100%;
        margin: 0 auto;
    }
    .leaderboard-entry {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f7f7f7;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease;
    }
    .leaderboard-entry.top-three {
        background-color: #FD8916; /* Cambia los primeros tres con un color dorado */
    }
    .leaderboard-position {
        font-size: 24px;
        font-weight: bold;
        color: #2c3e50;
    }
    .leaderboard-photo .profile-picture {
        border-radius: 50%;
        width: 50px;
        height: 50px;
        margin-left:10px;
    }
    .leaderboard-name {
        flex-grow: 1;
        padding-left: 15px;
        font-size: 18px;
        font-weight: 500;
    }
    .leaderboard-score {
        font-size: 18px;
        font-weight: bold;
        color: #2c3e50;
    }
    .leaderboard-entry:hover {
        background-color: #ecf0f1;
    }
    .leaderboard-rank {
        margin-right: 10px;
        padding: 20px;
        padding-top:10px;
        padding-bottom: 10px;
        background: #fff;
        border-radius: 10px;
    }
    .pagination-container {
        margin-top: 20px;
    }
    .leaderboard-rank .rank-image {
    width: 41px;
    height: 41px;
    margin-right: 10px;
    vertical-align: middle;
}
</style>
