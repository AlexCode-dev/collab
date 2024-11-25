<?php
use yii\helpers\Html;

$patron = '^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)^';
?>
<?php $username = ''; ?>
<?php foreach ($chat as $sentencia): ?>
    <?php if ($sentencia["username"] != $username): ?>
        <b> <?= Html::encode($sentencia["username"]) ?> <?= " (" . Html::encode($puntaje) . " puntos)"; ?> - Rango Global: <span style="color:#005C53;"><?= Html::encode($rangoNombre) ?></span></b><br/>
        <?php $username = $sentencia["username"]; ?>
    <?php endif; ?>

    <?php
    // Detectar si la sentencia es una respuesta a una pregunta
    if (preg_match('/^\/pregunta (\d+)\s+(.+)$/', $sentencia["sentencia"], $matches)):
        $eventoId = $matches[1];
        $respuesta = $matches[2];

        // Inicializar respuesta_dada si no existe
        $sentencia['respuesta_dada'] = $sentencia['respuesta_dada'] ?? false;

        if (!$sentencia['respuesta_dada']): ?>
            <div class="respuesta-pregunta">
                &nbsp;&nbsp;<strong>Respuesta a la pregunta ID <?= Html::encode($eventoId) ?>:</strong> <?= Html::encode($respuesta) ?>
                
                <!-- Mostrar el botón OK solo si el usuario es profesor -->
                <?= $esProfesor ? 
                    Html::button('Valorar Respuesta. Otorgará 100 puntos.', [
                        'class' => 'btn-valorar',
                        'data-evento-id' => Html::encode($eventoId),
                        'data-usuario-id' => Html::encode($sentencia['usuarios_id'])
                    ]) : '' 
                ?>
            </div>
        <?php elseif ($sentencia['respuesta_dada']): ?>
            <div class="respuesta-pregunta">
                &nbsp;&nbsp;<strong>Respuesta a la pregunta ID <?= Html::encode($eventoId) ?>:</strong> <?= Html::encode($respuesta) ?>
                <span>Puntuado con 100 puntos</span>
            </div>
        <?php endif; ?>

    <?php 
    // Detectar si la sentencia es parte de un debate
    elseif (preg_match('/^\/debate (\d+)\s+(.+)$/', $sentencia["sentencia"], $matches)): 
        $eventoId = $matches[1];
        $opinion = $matches[2];
        ?>

        <div class="respuesta-debate">
            &nbsp;&nbsp;<strong>Opinión en el debate ID <?= Html::encode($eventoId) ?>:</strong> <?= Html::encode($opinion) ?>
            
            <!-- Mostrar el botón de valorar solo si el usuario es profesor -->
            <?= $esProfesor ? 
                Html::button('Valorar Opinión. Otorgará 150 puntos.', [
                    'class' => 'btn-valorar-debate',
                    'data-evento-id' => Html::encode($eventoId),
                    'data-usuario-id' => Html::encode($sentencia['usuarios_id'])
                ]) : '' 
            ?>
        </div>

    <?php else: ?>
        &nbsp;&nbsp;<?= Html::encode($sentencia["sentencia"]); ?>
    <?php endif; ?>

    - <?= Html::encode($sentencia["fecha_hora"]); ?>
    <br/><br/>
<?php endforeach; ?>

<script>
    // Añadir funcionalidad para valorar la respuesta con AJAX
    $(document).on('click', '.btn-valorar', function() {
        var eventoId = $(this).data('evento-id');
        var usuarioId = $(this).data('usuario-id');
        var boton = $(this);

        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl('chats/puntuar-respuesta') ?>',
            type: 'POST',
            data: {
                evento_id: eventoId,
                usuario_id: usuarioId,
                puntos: 100
            },
            success: function(response) {
                if (response.success) {
                    boton.replaceWith('<span>Puntuado con 100 puntos</span>');
                } else {
                    alert(response.message || 'No se pudo puntuar la respuesta.');
                }
            }
        });
    });

    // Añadir funcionalidad para valorar la opinión de debate con AJAX
    $(document).on('click', '.btn-valorar-debate', function() {
        var eventoId = $(this).data('evento-id');
        var usuarioId = $(this).data('usuario-id');
        var boton = $(this);

        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl('chats/puntuar-debate') ?>',
            type: 'POST',
            data: {
                evento_id: eventoId,
                usuario_id: usuarioId,
                puntos: 150 // Por ejemplo, se le pueden dar 150 puntos por el debate
            },
            success: function(response) {
                if (response.success) {
                    boton.replaceWith('<span>Opinión valorada con 150 puntos</span>');
                } else {
                    alert(response.message || 'No se pudo valorar la opinión.');
                }
            }
        });
    });
</script>
