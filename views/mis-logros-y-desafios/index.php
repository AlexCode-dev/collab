<?php
use yii\helpers\Html;

/* @var $usuario app\models\Usuarios */
/* @var $rangoUsuario app\models\RangosUsuarios */
/* @var $logros app\models\Logros[] */
/* @var $desafios app\models\Desafios[] */
/* @var $progresoDesafios app\models\DesafiosUsuarios[] */
/* @var $rangos app\models\Rangos[] */
?>

<div class="perfil-container">
    <div class="perfil-header">
        <div class="perfil-info">
            <?php if ($usuario->foto_perfil): ?>
            <img src="<?= Yii::getAlias('@web/' . $usuario->foto_perfil) ?>" alt="Foto de perfil" width="50"
                height="50">
            <?php else: ?>
            <img src="<?= Yii::getAlias('@web/uploads/default_profile.png') ?>" alt="Sin foto" width="50" height="50">
            <?php endif; ?>
            <div class="info-usuario">
                <h2>Hola, <span> <?= Html::encode($usuario->nombre . ' ' . $usuario->apellido) ?>.</span></h2>
                <!-- Muestra el rango del usuario -->
                <!-- Muestra el nombre del rango si está disponible -->
                <p class="rango"> Rango <span  style="font-weight:600; color:#042940;">
                    <?php if ($rangoUsuario && $rangoUsuario->rango): ?>  
                        <?= Html::encode($rangoUsuario->rango->nombre) ?>
                    <?php else: ?>
                    Sin rango asignado
                    <?php endif; ?>
                    </span></p>

            </div>
        </div>
        <div class="cont-actividades">
            <div class="puntaje">
                <p><?= Html::encode($usuario->puntaje) ?></p>
                <h5><strong>Puntaje obtenido </strong></h5>
            </div>
            <div class="individuales">
                <p><?= Html::encode($usuario->cont_actividades_individuales) ?></p>
                <h5><strong>Actividades Individuales</strong></h5>
            </div>
            <div class="grupales">
                <p><?= Html::encode($usuario->cont_actividades_grupales) ?></p>
                <h5><strong>Actividades Grupales</strong></h5>
            </div>
        </div>
    </div>

    <div class="perfil-desafios-logros">
        <div class="desafios-text">
            <p>¡Obtén tu próximo rango y demuestra de lo que eres capaz! 🚀 Cada desafío completado te acerca más a
                nuevos logros y te ayuda a escalar en la tabla de posiciones. Cuanto más participas, más recompensas y
                prestigio obtienes. ¡La cima te espera! 💪🔥 <span style="font-weight:600; color:#FD8916;">CADA DESAFIO TE DA UN TOTAL DE +1000 PUNTOS 😜</span></p>
        </div>

        <div class="rango-desafios-container" style="display: flex; flex-wrap: wrap; gap: 20px;">
            <?php foreach ($rangos as $rango): ?>
            <div class="rango-desafios" style="flex: 1; border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
                <img src="<?= Yii::getAlias('@web/' . $rango->imagen) ?>" alt="Foto de perfil" width="150" height="150">
                <h3> <?= Html::encode($rango->nombre) ?> </br>(Nivel <?= Html::encode($rango->nivel) ?>)</h3>
                <p><?= Html::encode($rango->descripcion) ?></p>

                <?php foreach ($desafios as $desafio): ?>
                <?php if ($desafio->rangos_id == $rango->id): ?>
                <?php
                            $progresoActual = isset($progresoDesafios[$desafio->id]) ? Html::encode($progresoDesafios[$desafio->id]->contador_desafio_completado) : '0';
                            $metaDesafio = Html::encode($desafio->contador);
                            $completado = $progresoActual >= $metaDesafio ? '<span style="color:green;"> (¡Completado!)</span>' : '<span style="color:red;"> (No completado aun!)</span>';
                            ?>
                <p>
                    <?= Html::encode($desafio->nombre) ?> (<?= $progresoActual ?>/<?= $metaDesafio ?>)
                    <?= $completado ?>
                </p>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="perfil-leaderboard">
        <h2 class="perfil-title leader"><span>Leader</span>board<span>.</span></h2>
        <p>
            ¡La cima te espera! 💥 Escalar en el leaderboard no solo es un desafío, es una oportunidad para demostrar tu
            habilidad, dedicación y esfuerzo. Cada punto que sumas, cada desafío que completas, te acerca más a la cima
            y al reconocimiento de toda la comunidad. ¡Imagina ver tu nombre en los primeros lugares, destacando entre
            los mejores! 🚀

            No importa dónde estés ahora, lo importante es tu determinación para avanzar. ¡Sigue completando tareas,
            gana puntos, sube de rango y demuestra lo que puedes lograr! 💪 ¡El camino al éxito comienza ahora!

            ¿Listo para conquistar la cima?
        </p>
        <?= Html::a('Ver leaderboard', ['leaderboard/index'], ['class' => 'button-g2']) ?>
    </div>

    <div class="perfil-logros">
        <h2 class="perfil-title"><span>¿Como consigo</span> puntos? 🎯</h2>
        <p>¡Ganar puntos en la plataforma es muy sencillo y está en tus manos! 💪 A medida que participas y te
            involucras en las actividades, tus puntos irán creciendo. Aquí te mostramos cómo puedes acumular puntos y
            destacar en la tabla de posiciones:</br>
            <strong>1. Participación en el chat:</strong> Cada mensaje que envíes te otorga +10 puntos. ¡No dudes en
            interactuar y colaborar
            con tu equipo!</br>
            <strong>2. Completando desafíos:</strong> Cada desafío que completes te acercará a nuevos logros y te
            recompensará con
            puntos.</br>
            <strong>3. Actividades individuales y grupales:</strong> Realiza tanto tareas individuales como en equipo
            para ganar puntos por
            tu esfuerzo.</br>
            <strong>4. Notas en tareas:</strong> Las notas que obtengas en tus tareas se multiplicarán por 100 y se
            sumarán a tu puntaje
            total. ¡Así que da lo mejor de ti en cada trabajo!</br>
            <strong>5. Eventos especiales:</strong> Ya sea respondiendo preguntas, participando en actividades
            especiales o en juegos
            asignados, cada evento te recompensará con puntos adicionales.
            </br>
            </br>
            ¡Participa, suma puntos y escala hasta la cima! 🎉
        </p>
    </div>

</div>