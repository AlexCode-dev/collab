<?php 
use yii\helpers\Html;

$username = ''; 
?>
<?php foreach ($chat as $sentencia): ?>
    <?php if ($sentencia["username"] != $username): ?>
        <b> <?php echo $sentencia["username"]; ?> (<?php echo Html::encode($sentencia["puntaje"]); ?> puntos)  
        <?php if (!empty($sentencia["rango_nombre"])): ?>
                - <span style="color:#005C53;"><?= Html::encode($sentencia["rango_nombre"]) ?></span>
            <?php else: ?>
                - <span style="color:#FF0000;">Sin Rango</span>
            <?php endif; ?>
        </b> <br/>                      
        <?php $username = $sentencia["username"]; ?>
    <?php endif; ?>
    
    <?php
    // Detectar si la sentencia es una respuesta a una pregunta
    if (preg_match('/^\/pregunta (\d+)\s+(.+)$/', $sentencia["sentencia"], $matches)):
        $eventoId = $matches[1];
        $respuesta = $matches[2];

        // Verificar si ya respondió la pregunta
        if (!$sentencia['respuesta_dada']): ?>
            <div class="respuesta-pregunta">
                &nbsp;&nbsp;<strong>Respuesta a la pregunta ID <?= Html::encode($eventoId) ?>:</strong> <?= Html::encode($respuesta) ?>
                
                <!-- Mostrar el botón OK solo si el usuario es profesor -->
                <?= $esProfesor ? 
                    Html::button('Valorar Respuesta. Otorgara 100 puntos.', [
                        'class' => 'btn-valorar',
                        'data-evento-id' => Html::encode($eventoId),
                        'data-usuario-id' => Html::encode($sentencia['usuarios_id'])
                    ]) : '' 
                ?>
            </div>
        <?php else: ?>
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
                Html::button('Valorar Opinión. Otorgara 100 puntos.', [
                    'class' => 'btn-valorar-debate',
                    'data-evento-id' => Html::encode($eventoId),
                    'data-usuario-id' => Html::encode($sentencia['usuarios_id'])
                ]) : '' 
            ?>
        </div>

    <?php else: ?>
        &nbsp;&nbsp;<?= $sentencia["sentencia"];?>
    <?php endif; ?>

    - <?= Html::encode($sentencia["fecha_hora"]); ?>
    <br/><br/>
<?php endforeach; ?>
