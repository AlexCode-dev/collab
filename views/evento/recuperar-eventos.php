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

            <?php if ($evento->pregunta !== null && $evento->tipo_evento == 'pregunta'): ?>
                <p><strong>Pregunta:</strong> <?= Html::encode($evento->pregunta) ?></p>
            <?php endif; ?>

            <?php if ($evento->imagen !== null): ?>
                <p><strong>Imagen:</strong> <img src="<?= Html::encode($evento->imagen) ?>" alt="Imagen del evento" /></p>
            <?php endif; ?>

            <?php if ($evento->link !== null): ?>
                <p><strong>Link:</strong> <a href="<?= Html::encode($evento->link) ?>" target="_blank"><?= Html::encode($evento->link) ?></a></p>
            <?php endif; ?>

            <?php if ($esProfesor): ?>
                <?= Html::beginForm(['evento/desactivar'], 'post') ?>
                    <?= Html::hiddenInput('evento_id', $evento->id) ?>
                    <?= Html::submitButton('Terminar evento', ['class' => 'btn btn-danger']) ?>
                <?= Html::endForm() ?>
            <?php endif; ?>

            <hr>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
