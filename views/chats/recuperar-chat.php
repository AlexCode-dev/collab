<?php 
use yii\helpers\Html;

$username = ''; 
?>
<?php foreach ($chat as $sentencia): ?>
    <?php if ($sentencia["username"] != $username): ?>
        <b> <?= Html::encode($sentencia["username"]); ?> (<?= Html::encode($sentencia["puntaje"]); ?> puntos en actividades gamificadas)  
        <?php if (!empty($sentencia["rango_nombre"])): ?>
                - Rango Global: <span style="color:#005C53;"><?= Html::encode($sentencia["rango_nombre"]); ?></span>
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

        // Verificar si la pregunta ya fue puntuada
        $respuestaDada = (new \yii\db\Query())
            ->from('respuesta_preguntas')
            ->where(['evento_id' => $eventoId, 'usuario_id' => $sentencia['usuarios_id']])
            ->exists();
        ?>

        <div class="respuesta-pregunta">
            &nbsp;&nbsp;<strong>Respuesta a la pregunta ID <?= Html::encode($eventoId); ?>:</strong> <?= Html::encode($respuesta); ?>
            
            <!-- Mostrar el botón de valorar o deshabilitarlo según el estado -->
            <?php if ($esProfesor): ?>
                <?= $respuestaDada
                    ? Html::button('Ya fue puntuada', [
                        'class' => 'btn btn-secondary',
                        'disabled' => true,
                    ])
                    : Html::button('Valorar Respuesta. Otorgará 100 puntos.', [
                        'class' => 'btn-valorar',
                        'data-evento-id' => Html::encode($eventoId),
                        'data-usuario-id' => Html::encode($sentencia['usuarios_id']),
                    ]);
                ?>
            <?php endif; ?>
        </div>

    <?php 
    // Detectar si la sentencia es parte de un debate
    elseif (preg_match('/^\/debate (\d+)\s+(.+)$/', $sentencia["sentencia"], $matches)): 
        $eventoId = $matches[1];
        $opinion = $matches[2];

        // Verificar si el debate ya fue puntuado
        $respuestaDada = (new \yii\db\Query())
            ->from('respuesta_preguntas')
            ->where(['evento_id' => $eventoId, 'usuario_id' => $sentencia['usuarios_id']])
            ->exists();
        ?>

        <div class="respuesta-debate">
            &nbsp;&nbsp;<strong>Opinión en el debate ID <?= Html::encode($eventoId); ?>:</strong> <?= Html::encode($opinion); ?>
            
            <!-- Mostrar el botón de valorar o deshabilitarlo según el estado -->
            <?php if ($esProfesor): ?>
                <?= $respuestaDada
                    ? Html::button('Ya fue puntuado', [
                        'class' => 'btn btn-secondary',
                        'disabled' => true,
                    ])
                    : Html::button('Valorar Opinión. Otorgará 150 puntos.', [
                        'class' => 'btn-valorar-debate',
                        'data-evento-id' => Html::encode($eventoId),
                        'data-usuario-id' => Html::encode($sentencia['usuarios_id']),
                    ]);
                ?>
            <?php endif; ?>
        </div>

    <?php else: ?>
        &nbsp;&nbsp;<?= Html::encode($sentencia["sentencia"]); ?>
    <?php endif; ?>

    - <?= Html::encode($sentencia["fecha_hora"]); ?>
    <br/><br/>
<?php endforeach; ?>
