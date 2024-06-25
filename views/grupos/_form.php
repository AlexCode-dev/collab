<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Grupos */
/* @var $form yii\widgets\ActiveForm */
$recuperarAlumnos = Yii::$app->urlManager->createUrl(['asignaturas-alumnos/recuperar-alumnos']);
$script = <<< JS
    $(function () {
        var idasignatura = $asigid;
        var cantidadContenedoresGrupo = 0;
        function recuperarAlumnos(){        
            if ($('#grupos-year').val().length != 0 && $.isNumeric($('#grupos-year').val()) &&
                $('#grupos-cantidadintegrantes').val().length != 0 && $.isNumeric($('#grupos-cantidadintegrantes').val()) && 
                $('#grupos-cantidadintegrantes').val() > 1){
                $('#contenedorAlumnos').html('');
                $.ajax({
                    url: "$recuperarAlumnos",
                    data:{asigid: idasignatura, year: $('#grupos-year').val()},
                }).done(function (data) {
                    var aAlumnos = JSON.parse(data);
                    var i = 0;
                    while (i < aAlumnos.length){
                        $('#contenedorAlumnos').append('<div id="' + aAlumnos[i].alumno.idUsuario + '" class="divAlumno draggable">' + aAlumnos[i].alumno.apellido + ', ' + aAlumnos[i].alumno.nombre + '</div>');
                        i = i + 1;
                    }
                    $(".draggable").draggable();
        
                    var j = 0;
                    var cantidadGrupos = Math.round(aAlumnos.length / $('#grupos-cantidadintegrantes').val());
                    cantidadContenedoresGrupo = cantidadGrupos;
                    while(j < cantidadGrupos){
                        $('#contenedorGrupos').append('<div id="grupo' + (j + 1) + '" class="contenedorGrupos droppable">Grupo ' + (j + 1) + '<input type="hidden" id="inputgrupo' + (j + 1) + '" value="[]"/></div>');
                        j = j + 1;
                    }                            
        
                    $(".droppable").droppable({
                        drop: function (event, ui) {
                            idsAlumnos =  JSON.parse($('#input' + $(this).attr('id')).val() );  
                            if (idsAlumnos.indexOf($(ui.draggable).attr('id')) == -1){
                                idsAlumnos.push($(ui.draggable).attr('id'));
                            }                              
                            $('#input' + $(this).attr('id')).val(JSON.stringify(idsAlumnos));
                            $('#grupos-alumnosporgrupo').val();
                            establecerGrupos();
                        },
                        out: function(event, ui){
                            idsAlumnos =  JSON.parse($('#input' + $(this).attr('id')).val() );                                                                         
                            var index = idsAlumnos.indexOf($(ui.draggable).attr('id'));
                            if (index > -1) {
                                idsAlumnos.splice(index, 1);
                            }
        
                            $('#input' + $(this).attr('id')).val(JSON.stringify(idsAlumnos));
                            establecerGrupos();
                        }
                    });                              
                    
                });                
            } else {
                if ($('#grupos-cantidadintegrantes').val().length > 0 && $.isNumeric($('#grupos-cantidadintegrantes').val()) && 
                $('#grupos-cantidadintegrantes').val() <= 1){
                    alert('La cantidad de integrantes debe ser mayor a 1');
                } else {
                    //alert('No entro...');
                }                
            }
            
        }
        
        function establecerGrupos(){
            var i = 0;
            var aGrupos = [];
            while ( i < cantidadContenedoresGrupo){                
                aGrupos[i] = JSON.parse($('#inputgrupo' + (i+1)).val());
                i = i + 1;
            }
            $('#grupos-alumnosporgrupo').val(JSON.stringify(aGrupos));
        }
        
        $('#grupos-year').change(function(){
            if ($('#grupos-metodos_formacion_id').val() == 1){
                recuperarAlumnos();
            }
        });
            
        $('#grupos-cantidadintegrantes').change(function(){
            if ($('#grupos-metodos_formacion_id').val() == 1){
                recuperarAlumnos();
            }
        });        
        
        $('#grupos-metodos_formacion_id').change(function(){
            if ($('#grupos-metodos_formacion_id').val() == 1){
                recuperarAlumnos();
            } else {
                $('#contenedorAlumnos').html('');
                $('#contenedorGrupos').html('');
            }
        });                     
        
    });                             
JS;
$this->registerJs($script, yii\web\View::POS_END);
?>

<div class="grupos-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'year')->textInput() ?>

    <?= $form->field($model, 'codigo')->textInput() ?>

    <?= $form->field($model, 'cantidadintegrantes')->textInput() ?>        

    <?= $form->field($model, 'metodos_formacion_id')->dropDownList(app\models\MetodosFormacion::getListaMetodosFormacion()); ?>

    <?= $form->field($model, 'alumnosPorGrupo')->hiddenInput()->label(''); ?>   

    <div>
        <div id='contenedorAlumnos' style="width: 180px;float:left;"></div>

        <div id='contenedorGrupos' style="float:left;"></div>    

        <br style='clear: both;'/>
    </div>



    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
