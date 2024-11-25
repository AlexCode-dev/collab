<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\models\Tareas */
$usuario = Yii::$app->user->identity->id;
$oUser = \app\models\Usuarios::findOne(['id' => $usuario]);

$this->title = 'Actividad Correspondiente a ' . app\models\Asignaturas::findOne(['id' => $model->asignaturas_id])->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Tareas', 'url' => ['index', 'asigid' => Yii::$app->security->encryptByPassword($model->asignaturas_id, $oUser->password)]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tareas-view">

    <h2 class="perfil-title"><?= Html::encode($this->title) ?><span>.</span></h2>
    <p>En esta sección, podrás visualizar todos los detalles relacionados con la actividad seleccionada. Aquí se incluye
        la información clave, como la consigna, el tipo de tarea, el año y si la actividad permite el uso de
        herramientas adicionales como reportes de estado de ánimo o conflictos. También encontrarás el puntaje asignado
        a la tarea, lo que te ayudará a planificar mejor tu participación y trabajo.

        Además, se muestra una lista de los grupos asociados a esta actividad, donde podrás clasificar individualmente a
        los miembros o gestionar las actividades grupales. </p>

    <p>
        <?= Html::a('Actualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Está seguro que desea eliminar esta tarea?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'nombre_t',
                'label' => 'Actividad',
            ],
            'consigna',
            'descripcion',            
            'year',
            [
                'attribute' => 'usar_sentencias_apertura',
                'label' => 'Usa Sentencias de Apertura',
                'value' => function($data) {
                    return ($data->usar_sentencias_apertura) ? 'Sí' : 'No';
                },
            ],
            [
                'attribute' => 'reportar_estado_animo',
                'label' => 'Permite Reportar Estado de Ánimo',
                'value' => function($data) {
                    return ($data->reportar_estado_animo) ? 'Sí' : 'No';
                },
            ],
            [
                'attribute' => 'reportar_conflicto',
                'label' => 'Permite Reportar Conflictos',
                'value' => function($data) {
                    return ($data->reportar_conflicto) ? 'Sí' : 'No';
                },
            ],
            [
                'attribute' => 'actividad_gamificada',
                'label' => 'Permite Actividad Gamificacada 🏆',
                'value' => function($data) {
                    return ($data->actividad_gamificada) ? 'Sí' : 'No';
                },
            ],
            'puntaje_tarea',
            'tipo_tarea',
        ],
    ]) ?>

    <?php
    $chatsxGrupo = app\models\Chats::getChatsGrupos($model->id);
    $grupos = app\models\GruposFormados::getDetalleGrupos($model->grupos_id);
    ?>

    <h2 class="perfil-title">Chats asociados a los grupos <span>.</span></h2>
    <p>En cada asignatura, existe la posibilidad de crear configuraciones de grupo con un código específico. Estas
        configuraciones permiten crear varios grupos bajo una misma asignatura, lo cual se gestiona desde el menú
        "Manejar Grupos" dentro de la sección de la asignatura.

        Posteriormente, cuando se crea una actividad, se asigna a uno de esos códigos de configuración de grupo. Esta
        acción genera automáticamente un chat para cada grupo asociado a esa configuración. En esta sección, podrás
        visualizar los chats correspondientes a los grupos para cada actividad, interactuar con los integrantes y
        gestionar el progreso y participación dentro del grupo.</p>

    <?php if($model->actividad_gamificada): ?>
    <div class="info-evento">
        <p><strong>Evento:</strong> Los eventos son interacciones especiales que ocurren dentro de las <span
                style="color:#FD8916">actividades</span> y que fomentan la participación inmediata y activa. Los eventos
            incluyen cuestionarios en tiempo real, juegos o debates, donde los estudiantes responden preguntas o
            interactúan directamente. Estos eventos suelen tener un carácter más dinámico y permiten que los estudiantes
            obtengan puntos adicionales para mejorar su posición en la tabla de clasificación (leaderboard). Los eventos
            están diseñados para hacer el aprendizaje más interactivo y entretenido.</p>
        <p>Existen diferentes tipos de eventos:</p>
        <ul>
            <li><span style="color:#FD8916;">Preguntas</span>: Permiten a los alumnos responder a ciertas inquietudes y
                a los profesores puntuar sus respuestas. No se califica, sino que se brindan puntos (+100).</li>
            <li><span style="color:#FD8916;">Debates</span>: Los alumnos pueden debatir sobre ciertos temas relacionados
                con la actividad. (+150).</li>
            <li><span style="color:#FD8916;">Juegos y actividades</span>: Son para aquellas actividades o juegos que se
                realicen fuera de la plataforma, por ejemplo, un juego en Kahoot, un cuestionario en Google, un video de
                YouTube o cualquier otra plataforma.</li>
        </ul>
        <p>Recomendación:<span style="color:#FD8916;"> Crear un evento a la vez</span> en el que los alumnos participen
            y luego, en cualquier chat, dar de baja al evento con el botón "terminar evento". Los eventos aparecen
            siempre primero y se responden dependiendo de si es una pregunta o un debate con /pregunta id respuesta o,
            si es debate, /debate id respuesta. </p>
    </div>



    <?= Html::button('Crear Evento interactivo dentro de chat 🎮', [
        'class' => 'button-g2',
        'style' => 'border:none; background:#FD8916;',
        'id' => 'open-modal-btn',
        'data-toggle' => 'modal',
        'data-target' => '#createEventModal'
    ]) ?>

    <?= Html::a('Tabla de puntuación de actividad gamificada 🎯', ['/leaderboard/index', 'tarea_id' => $model->id], [
        'class' => 'button-g2',
    ]) ?>

    <?php endif; ?>


    <div class="grupos-container">
        <?php foreach ($chatsxGrupo as $alumno): ?>
        <?php 
                $usuario = Yii::$app->user->identity->id;
                $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
                $varID = Yii::$app->security->encryptByPassword($alumno["grupos_formados_id"], $oUser->password);
                $tareas_id = $model->id;

                // Filtrar los alumnos de acuerdo al grupo actual
                $integrantesDelGrupo = array_filter($grupos, function($gr) use ($alumno) {
                    return $gr['id'] == $alumno['grupos_formados_id'];
                });
            ?>

        <div class="grupo-card">
            <h3 class="grupo-title">Grupo <?= Html::encode($alumno["grupos_formados_id"]) ?></h3>
            <div class="grupo-content">
                <ul>
                    <?php foreach ($integrantesDelGrupo as $gr): ?>
                    <li><?= Html::encode($gr['nombreAlumno']) .' ' .$gr['apellidoAlumno'].' (ID: ' . Html::encode($gr['alumnoId']) . ')'. ' '. Html::a('Clasificar individual', ['tareas-alumno/asignar-nota', 'tareas_id' => $tareas_id, 'alumno_id' => $gr['alumnoId']])  ?>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <div class="grupo-actions">
                    <?= Html::a('ver chat', ['chats/grupo', 'chatid' => Yii::$app->security->encryptByPassword($alumno["id"], $oUser->password)], ['class' => 'btn btn-info']) ?>
                    <?= Html::a('Clasificar Grupo', ['grupos-formados/clasificar', 'id' => $varID, 'tareas_id' => $tareas_id], ['class' => 'btn btn-info']) ?>


                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

<!-- Modal para Crear Evento -->
<div class="modal fade" id="createEventModal" tabindex="-1" aria-labelledby="createEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createEventModalLabel">Crear Evento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'create-event-form',
                    'action' => ['tareas/create-evento', 'asigid' => $model->asignaturas_id],
                    'options' => ['enctype' => 'multipart/form-data'],
                ]); ?>

                <?= $form->field($modelEvento, 'tipo_evento')->dropDownList([
                    'Pregunta' => 'pregunta',
                    'Juego' => 'juego',
                    'Actividad' => 'actividad',
                    'Debate' => 'debate',
                ], ['prompt' => 'Selecciona el tipo de evento', 'id' => 'tipoEvento'])->label('Tipo de Evento') ?>

                <?= $form->field($modelEvento, 'id_tarea')->dropDownList(
                    ArrayHelper::map([$model], 'id', 'nombre_t'),
                    ['prompt' => 'Seleccionar actividad relacionada']
                )->label('Actividad Relacionada') ?>

                <div id="kahootOtroFields" style="display: none;">
                    <?= $form->field($modelEvento, 'titulo')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($modelEvento, 'descripcion')->textarea(['rows' => 3]) ?>
                    <?= $form->field($modelEvento, 'link')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($modelEvento, 'imagen')->fileInput() ?>
                </div>

                <div id="cuestionarioFields" style="display: none;">
                    <?= $form->field($modelEvento, 'pregunta')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($modelEvento, 'descripcion_pregunta')->textarea(['rows' => 3]) ?>
                    <?= $form->field($modelEvento, 'imagen')->fileInput() ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="save-event-btn">Guardar Evento</button>
            </div>
        </div>
    </div>
</div>

<?php
$script = <<< JS
$('#tipoEvento').on('change', function() {
    var tipoEvento = $(this).val();
    if (tipoEvento === 'Juego' || tipoEvento === 'Actividad' || tipoEvento === 'Debate') {
        $('#kahootOtroFields').show();
        $('#cuestionarioFields').hide();
    } else if (tipoEvento === 'Pregunta') {
        $('#cuestionarioFields').show();
        $('#kahootOtroFields').hide();
    } else {
        $('#kahootOtroFields, #cuestionarioFields').hide();
    }
});

$('#save-event-btn').on('click', function() {
    var form = $('#create-event-form');
    var formData = new FormData(form[0]);
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#createEventModal').modal('hide');
                location.reload();
            } else {
                var errorMsg = response.errors ? JSON.stringify(response.errors) : 'Error desconocido';
                alert('Error al guardar el evento: ' + errorMsg);
            }
        },
        error: function() {
            alert('Error al guardar el evento');
        }
    });
});
JS;

$this->registerJs($script);
?>


<style>
.grupos-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 20px;
}

.grupo-card {
    background-color: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 450px;
    padding: 20px;

    transition: transform 0.2s;
}

.grupo-card:hover {
    transform: translateY(-5px);
}

.grupo-title {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
}

.grupo-content ul {
    list-style-type: none;
    padding-left: 0;
}

.grupo-content ul li {
    margin-bottom: 5px;
}

.grupo-actions {
    margin-top: 10px;
    display: flex;
    gap: 10px;
}

.btn {
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 5px;
}

.btn-info {
    background-color: #3498db;
    color: #fff;
}

.btn-info:hover {
    background-color: #2980b9;
}
</style>