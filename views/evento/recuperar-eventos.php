<?php
use yii\helpers\Html;
use yii\helpers\Url;

// Verificar si el usuario tiene el rol de profesor
$rolesUsuario = Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id);
$esProfesor = array_key_exists('profesor', $rolesUsuario);
?>

<?php foreach ($eventos as $evento): ?>
    <?php if ($evento->estado === 'activado'): ?>
       
        <div class="evento" data-evento-id="<?= Html::encode($evento->id) ?>" data-tipo-evento="<?= Html::encode($evento->tipo_evento) ?>">
            <h2>Evento en curso 📅. Tipo de evento: <span style="text-transform:uppercase;"><?= Html::encode($evento->tipo_evento) ?></span></h2>
            
            <p><strong>ID:</strong> <?= Html::encode($evento->id) ?></p>
            <p><strong>Estado:</strong> <span class="estado-texto"><?= Html::encode($evento->estado) ?></span></p>

            <!-- Condición para mostrar Pregunta si no es null -->
            <?php if ($evento->pregunta !== null && $evento->tipo_evento == 'pregunta'): ?>
                <p><strong>Pregunta:</strong> <?= Html::encode($evento->pregunta) ?></p>
            <?php endif; ?>

            <!-- Condición para mostrar Imagen si no es null -->
            <?php if ($evento->imagen !== null): ?>
                <p><strong>Imagen:</strong> <img src="<?= Html::encode($evento->imagen) ?>" alt="Imagen del evento" /></p>
            <?php endif; ?>

            <!-- Condición para mostrar Link si no es null -->
            <?php if ($evento->link !== null): ?>
                <p><strong>Link:</strong> <a href="<?= Html::encode($evento->link) ?>" target="_blank"><?= Html::encode($evento->link) ?></a></p>
            <?php endif; ?>

            <!-- Condición para mostrar descripción de la pregunta si no es null -->
            <?php if ($evento->descripcion_pregunta !== null && $evento->tipo_evento == 'pregunta'): ?>
                <p><strong>Descripción de la Pregunta:</strong> <?= Html::encode($evento->descripcion_pregunta) ?></p>
            <?php endif; ?>
            <!-- Condición para mostrar descripción si no es null -->
            <?php if ($evento->descripcion !== null && ($evento->tipo_evento == 'juego' || $evento->tipo_evento == 'debate' )): ?>
                <p><strong>Descripción :</strong> <?= Html::encode($evento->descripcion) ?></p>
            <?php endif; ?>
            <!-- Si es pregunta, muestra la información adicional para responder -->
            <?php if ($evento->tipo_evento === 'pregunta'): ?>
                <p>🖋️Para responder una pregunta en un evento: usa /pregunta idEvento tu respuesta. Por ejemplo: /pregunta 17 Yo creo que la respuesta es..</p>
            <?php endif; ?>
             <!-- Si es debate, muestra la información adicional para responder -->
             <?php if ($evento->tipo_evento === 'debate'): ?>
                <p class="debate-p">🖋️Para responder un debate en un evento: usa /debate idEvento tu respuesta. Por ejemplo: /debate 17 Yo creo que la verdad es..</p>
            <?php endif; ?>
            
            <?php if ($esProfesor): ?>
                <button class="btn-desactivar" data-evento-id="<?= Html::encode($evento->id) ?>">
                    Terminar evento
                </button>
            <?php endif; ?>
            
            <hr>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<script>
$(document).on('click', '.btn-desactivar', function() {
    console.log('Botón clicado');
    var eventoId = $(this).data('evento-id');
    var boton = $(this);

    $.ajax({
        url: '<?= Yii::$app->urlManager->createUrl('evento/desactivar') ?>',
        type: 'POST',
        data: {
            evento_id: eventoId
        },
        success: function(response) {
    console.log(response); // <-- Agrega esto para ver la respuesta en la consola
    if (response.success) {
        boton.closest('.evento').find('.estado-texto').text(response.newStatus);
        boton.closest('.evento').hide();
    } else {
        alert(response.message || 'No se pudo cambiar el estado del evento.');
    }
},
        error: function(xhr, status, error) {
            console.error('Error en la solicitud AJAX:', error);
            alert('Hubo un problema con la solicitud. Revisa la consola para más detalles.');
        }
    });
});
</script>
