<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\Tareas;
use yii\widgets\ActiveForm;

<<<<<<< HEAD
=======
/* @var $this yii\web\View */
/* @var $searchModel app\models\TareasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

>>>>>>> 738f8d5f4e3524f9b29eacb1792cac1dc4cdf247
$this->title = 'Actividades de ' . app\models\Asignaturas::findOne(['id' => $asigid])->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Asignaturas', 'url' => ['asignaturas/index', 'asigid' => $asigid]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="tareas-index">

    <h2 class="perfil-title"><?= Html::encode($this->title) ?><span>.</span></h2>
    <p>En esta sección, podrás crear y gestionar actividades y eventos para tus asignaturas. Cada actividad puede ser utilizada para promover la participación en clase, fomentar la colaboración en equipo y reforzar los conceptos clave de la materia. Además, podrás generar eventos interactivos, como cuestionarios y juegos, que incentivarán a los estudiantes a participar de manera activa. No olvides que cada evento y actividad suma puntos en el leaderboard, ayudando a los estudiantes a mejorar su rango y motivar su progreso académico dentro de la plataforma.</p>
    <p><strong>Actividad:</strong> Las actividades son tareas o trabajos asignados a los estudiantes con el fin de reforzar el aprendizaje y evaluar su comprensión. Estas pueden incluir trabajos prácticos, ejercicios de investigación o discusiones en clase. Las actividades suelen tener una consigna clara y un plazo de entrega, permitiendo que los estudiantes trabajen de manera autónoma o en grupos. Las calificaciones obtenidas en las actividades se reflejan directamente en el puntaje total del estudiante.</p>

<p><strong>Evento:</strong> Los eventos son interacciones especiales que ocurren dentro de las <span style="color:#FD8916">actividades</span> y que fomentan la participación inmediata y activa. Los eventos incluyen cuestionarios en tiempo real, juegos o debates, donde los estudiantes responden preguntas o interactúan directamente. Estos eventos suelen tener un carácter más dinámico y permiten que los estudiantes obtengan puntos adicionales para mejorar su posición en la tabla de clasificación (leaderboard). Los eventos están diseñados para hacer el aprendizaje más interactivo y entretenido.</p>
<p>Existen diferentes tipos de eventos:</p>
<ul>
    <li><span style="color:#FD8916;">Preguntas</span>: Permiten a los alumnos responder a ciertas inquietudes y a los profesores puntuar sus respuestas. No se califica, sino que se brindan puntos (+150).</li>
    <li><span style="color:#FD8916;">Debates</span>: Los alumnos pueden debatir sobre ciertos temas relacionados con la actividad.</li>
    <li><span style="color:#FD8916;">Juegos y actividades</span>: Son para aquellas actividades o juegos que se realicen fuera de la plataforma, por ejemplo, un juego en Kahoot, un cuestionario en Google, un video de YouTube o cualquier otra plataforma.</li>
</ul>
<p>Recomendación:<span style="color:#FD8916;"> Crear un evento a la vez</span> en el que los alumnos participen y luego, en cualquier chat, dar de baja al evento con el botón "terminar evento". Los eventos aparecen siempre primero y se responden dependiendo de si es una pregunta o un debate con /pregunta id respuesta o, si es debate, /debate id respuesta. </p>


    <!-- <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <?= Yii::$app->session->getFlash('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?> -->
    <div class="btns-tareas-index">
    <?= Html::button('Crear Evento', [
        'class' => 'button-g2',
        'style' => 'border:none; background:#FD8916;',
        'id' => 'open-modal-btn',
        'data-toggle' => 'modal',
        'data-target' => '#createEventModal'
    ]) ?>

    <?= Html::a('Crear Actividad', ['elegir-actividad', 'asigid' => $asigid], ['class' => 'button-g2']) ?>

    </div>
  
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'nombre_t',
                'label' => 'Actividad',
            ],
            'consigna',
            'descripcion',
            'year',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
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
                    'action' => ['tareas/create-evento', 'asigid' => $asigid],
                    'options' => ['enctype' => 'multipart/form-data'],
                ]); ?>

                <?= $form->field($modelEvento, 'tipo_evento')->dropDownList([
                    'Pregunta' => 'pregunta',
                    'Juego' => 'juego',
                    'Actividad' => 'actividad',
                    'Debate' => 'debate',
                ], ['prompt' => 'Selecciona el tipo de evento', 'id' => 'tipoEvento'])->label('Tipo de Evento') ?>

                <?= $form->field($modelEvento, 'id_tarea')->dropDownList(
                    ArrayHelper::map($tareas, 'id', 'nombre_t'),
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
