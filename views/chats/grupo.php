<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ChatsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$miembrosChat = app\models\Chats::getMiembrosChat($grupo_id);
$usuarioid = Yii::$app->user->identity->id;

// URLs para hacer llamados AJAX
$recuperarChat = Yii::$app->urlManager->createUrl(['chats/recuperar-chat', 'chatid' => $chatid]);
$recuperarEventosUrl = Yii::$app->urlManager->createUrl(['evento/recuperar-eventos', 'chatid' => $chatid]);
$recuperarUltimaSentenciaChat = Yii::$app->urlManager->createUrl(['chats/recuperar-ultima-sentencia-chat', 'chatid' => $chatid]);
$enviarSentencia = Yii::$app->urlManager->createUrl(['sentencias/crear-con-ajax']);
$enviarReporteEstadoAnimo = Yii::$app->urlManager->createUrl(['emociones/crear-con-ajax']);
$sentenciasApertura = Yii::$app->urlManager->createUrl(['sentencias-apertura/recuperar-sentencias']);
$rEstadoAnimo = ($tarea->reportar_estado_animo) ? 1 : 0;
$rConflicto = ($tarea->reportar_conflicto) ? 1 : 0;
$enviarReporteConflicto = Yii::$app->urlManager->createUrl(['conflictos/crear-con-ajax']);
$urlUploads = Yii::$app->request->baseUrl . "/uploads/$directorio/";
$this->title = $asignatura . " - " . $tarea->nombre_t . " / Grupo " . $miembrosChat[0]["grupos_formados_id"] . " - " . $miembrosChat[0]["alumnos"];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(Yii::$app->request->baseUrl . '/js/jquery.rateyo.min.js', ['depends' => [\yii\jui\JuiAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/emoji-picker/js/config.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/emoji-picker/js/util.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/emoji-picker/js/jquery.emojiarea.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl . '/emoji-picker/js/emoji-picker.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$base = Yii::$app->request->baseUrl;

// Define las variables PHP primero
$username = Yii::$app->user->identity->username;
$puntos = Yii::$app->user->identity->puntaje;

$script = <<< JS
    function enviarArchivo(nombreArchivo){        
        sentenciaEnviar = "<a href='$urlUploads" + nombreArchivo + "' target='_blank'>" + nombreArchivo + "</a>";
        $.ajax({
            method: 'GET',
            url: "$enviarSentencia",
            data: {sentencia: sentenciaEnviar, usuarios_id: $usuarioid, chats_id: $chatid},
        }).done(function (data) {           
            return true;
        });         
    }
        
    $(function () {
        var bSeleccionSentencia = 0;
        var rEstadoAnimo = $rEstadoAnimo;
        var rConflicto = $rConflicto;
        var scrollTopBefore = 0;
        var lastScrollHeight = 0;
        var ultimaSentencia = "";
        var eventosMostrados = new Set(); // Conjunto para almacenar los IDs de eventos mostrados
        var ultimoEventoId = null; // Almacena el último ID de evento cargado

        // Variables PHP en JavaScript
        var username = "$username"; // Asigna el nombre de usuario desde PHP
        var puntos = "$puntos"; // Asigna los puntos del usuario desde PHP

        // Función para procesar y agregar eventos únicos al chat
        function agregarEventosUnicos(eventosHtml) {
            $(eventosHtml).each(function(index, eventoHtml) {
                var eventoId = $(eventoHtml).data("evento-id");
                if (eventoId && !eventosMostrados.has(eventoId)) {
                    $('#divChat').append(eventoHtml); 
                    eventosMostrados.add(eventoId); 
                    ultimoEventoId = eventoId;
                    var objDiv = document.getElementById("divChat");
                    objDiv.scrollTop = objDiv.scrollHeight;
                }
            });
        }

        // Cargar eventos iniciales y actualizar el último evento ID cargado
        function cargarEventosIniciales() {
            $.ajax({
                url: "$recuperarEventosUrl",
                method: "GET"
            }).done(function(data) {
                console.log("Eventos iniciales cargados:", data); 
                agregarEventosUnicos(data); 
                if ($(data).length > 0) {
                    ultimoEventoId = $(data).last().data("evento-id");
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar eventos iniciales:", textStatus, errorThrown);
            });
        }

        // Verificar si hay nuevos eventos comparando con el último evento cargado
        function verificarNuevosEventos() {
            $.ajax({
                url: "$recuperarEventosUrl",
                method: "GET"
            }).done(function(data) {
                var nuevoEventoId = $(data).last().data("evento-id");
                if (nuevoEventoId && nuevoEventoId !== ultimoEventoId) {
                    $('#nuevoEvento').show();
                    console.log("Nuevo evento detectado.");
                }
            });
        }

        // Cargar sentencias del chat
        $.ajax({
            url: "$recuperarChat",
        }).done(function (data) {
            $('#divChat').append(data);
            var objDiv = document.getElementById("divChat");
            objDiv.scrollTop = objDiv.scrollHeight; 
            lastScrollHeight = objDiv.scrollHeight;                      
            // Cargar eventos después de las sentencias
            cargarEventosIniciales();
        });

        $('#divChat').scroll(function(){
            var objDiv = document.getElementById("divChat");
            scrollTopBefore = objDiv.scrollTop;
        });
        
        $('#btnGoBottom').click(function(){
            var objDiv = document.getElementById("divChat");
            objDiv.scrollTop = objDiv.scrollHeight;
            lastScrollHeight = objDiv.scrollHeight;
            $('#goBottom').hide();
        });
        
        $.ajax({
            url: "$recuperarUltimaSentenciaChat",
        }).done(function (data) {
            ultimaSentencia = data;                    
        });
        
        setInterval(function(){
            $.ajax({
                url: "$recuperarUltimaSentenciaChat",
            }).done(function (data) {
                if (ultimaSentencia !== data){
                    var objDiv = document.getElementById("divChat");
                    $('#divChat').append(data);
                    ultimaSentencia = data;  
                    diferencia = objDiv.scrollHeight - scrollTopBefore;
                    if( diferencia > 300 && diferencia < 490 ){
                        objDiv.scrollTop = objDiv.scrollHeight;                    
                        lastScrollHeight = objDiv.scrollHeight;
                    } else {                        
                        objDiv.scrollTop = scrollTopBefore;
                        if (lastScrollHeight < objDiv.scrollHeight){
                            $('#goBottom').show();
                        }                        
                    }
                }           
            });
        }, 1000);

        // Verificar nuevos eventos cada 10 segundos
        setInterval(verificarNuevosEventos, 10000);

        // Manejar el clic en el botón para cargar el nuevo evento
        $('#btnNuevoEvento').click(function() {
            $.ajax({
                url: "$recuperarEventosUrl",
                method: "GET"
            }).done(function(data) {
                agregarEventosUnicos(data); 
                $('#nuevoEvento').hide(); 
            });
        });

        $('#cbxSubhabilidad').change(function () {
            $.ajax({
                url: "$sentenciasApertura",
                data: {idsubhab: $('#cbxSubhabilidad').val()},
            }).done(function (data) {
                sentencias = JSON.parse(data);        
                string = "";
                for(var i = 0; i < sentencias.length; i++){
                    string +='<option value="' + sentencias[i].id + '">' + sentencias[i].sentencia +'</option>'                
                }
                $('#cbxSentencias').html(string);            
            });
        });         
            
        $('#cbxSentencias').change(function(){
            bSeleccionSentencia = 1;
            $('#txtSentencia').prop('disabled', false);
        });      
        
        $('#frmChat').submit(function (e) {
    e.preventDefault();                

    if ($('#txtSentencia').val().length != 0) {
        var sentenciaApertura = '';
        if (bSeleccionSentencia == 1){                    
            sentenciaApertura = '<b>' + $('#cbxSentencias :selected').text() + '</b> ';
        }
        var idhidden = $('#txtSentencia').data('id');
        var raw = ($("*[data-id=" + idhidden + "]").val());        
        var sentenciaEnviar = sentenciaApertura + raw;

        // Crear el formato del mensaje sin incluir el nombre de usuario ni los puntos
        var mensajeFormateado = sentenciaEnviar; // Solo envía el contenido del mensaje

        // Se envía el mensaje
        $.ajax({
            method: 'GET',
            url: "$enviarSentencia",
            data: {sentencia: mensajeFormateado, usuarios_id: $usuarioid, chats_id: $chatid},
        }).done(function (data) {
            $('#txtSentencia').val('');
            $('.emoji-wysiwyg-editor').html("");  
            return true;
        });        
    }        
});
           
        
        if (rEstadoAnimo == 1){
            if ($('#lblEmocionSeleccionada').html().length == 0){
                $('#pleasure').val('0.000');
                $('#arousal').val('0.000');
                $('#dominance').val('0.000');
                $('#imgEmocionSeleccionada').attr('class', 'neutral');
                $('#lblEmocionSeleccionada').html('Neutral');
            }
        
            $('input[type="button"].btnEmociones').click(function(){
                $('#pleasure').val($(this).data('pleasure'));
                $('#arousal').val($(this).data('arousal'));
                $('#dominance').val($(this).data('dominance'));
                $('#imgEmocionSeleccionada').attr('class', $(this).attr('class'));
                $('#lblEmocionSeleccionada').html($(this).data('emocion'));

                $.ajax({
                    method: 'GET',
                    url: "$enviarReporteEstadoAnimo",
                    data: {id: $chatid, valence: $('#pleasure').val(), arousal: $('#arousal').val(), dominance: $('#dominance').val(), usuarios_id: $usuarioid},
                }).done(function () {
                    return true;
                });   
            });                
        }   
        
        if (rConflicto == 1){
            $('#btnReporteConflicto').click(function () {
                $.ajax({
                    method: 'GET',
                    url: "$enviarReporteConflicto",
                    data: {idChat: $chatid, usuarios_id: $usuarioid},
                }).done(function () {
                    return true;
                });
            });        
        }        
        
        window.emojiPicker = new EmojiPicker({
          emojiable_selector: '[data-emojiable=true]',
          assetsPath: '$base/emoji-picker/img/',
          popupButtonClasses: 'fa fa-smile-o'
        });
        window.emojiPicker.discover();
    });                             
JS;

$this->registerJs($script, yii\web\View::POS_END);
$sentenciaApertura = new app\models\SentenciasApertura();
?>

<div class="chats-index">
    <div class="consPointsNote">
        <div class="chat-tarea">
            <h1 class="chat-first-p"><span>Actividad a realizar:</span><br /></h1>
            <p class="tareaConsigna"><?= $tarea->nombre_t ?></p>
            <p class="tareaConsigna"><?= $tarea->consigna ?></p>
        </div>
    </div>
    <?php if($tarea->actividad_gamificada):?>
    <h1 style="padding-top:10px; padding-bottom:10px; font-weight:700;">Esta es una actividad Gamificada.</h1>
    <p >¡Participa en el chat y lleva tus ideas al siguiente nivel! 💬 Cada mensaje cuenta para que puedas completar la actividad, no olvides seguir la consigna para que tu aporte sea relevante y útil para el equipo. 🚀 Con cada participación, no solo contribuyes al trabajo en grupo, ¡también sumas puntos y escalas posiciones en el leaderboard! 🏆 Cuanto más activo seas, más cerca estarás de desbloquear nuevos logros y ganar recompensas. 🌟 CADA MENSAJE ENVIADO SUMA +10 puntos.</p>
    <p>Puede ser que cuando la pagina se recargue las preguntas o debates pasen arribas de la notificacion de el evento. </p>
    <p>Para responder a una pregunta usa:<span style="color:#EB6500;"> /pregunta IDPregunta respuesta.</span> por ejemplo: /pregunta 18 la respuesta es..</p>
    <p>Para responder a un Debate usa:<span style="color:#EB6500;"> /debate IDdebate respuesta. </span> por ejemplo: /debate 18 yo creo que ..</p>
    <p><span style="color:#EB6500;">Las actividades y juegos</span> no otorgan puntaje son actividades extras dentro del chat.</p>
    <?php endif;?>
    <h1>CHAT <span style="color:#EB6500;">.</span></h1>
    <div class="chats-events-emotions">
        <div id='divChat' style=" height: 400px; overflow-y: scroll;"></div>
        <br />
        <div id="goBottom" style="margin: 0 auto; width: 200px; display: none;">
            <input id="btnGoBottom" type="button" style="background-color: #FEE300;  text-align: center; padding: 5px;"
                value="Tienes nuevos mensajes" />
        </div>
        <div id="nuevoEvento" style="margin: 0 auto; width: 200px; display: none;">
            <input id="btnNuevoEvento" type="button"
                style="background-color: #BDE5F8; text-align: center; padding: 5px;" value="Tienes un nuevo evento" />
        </div>

        <form id="frmChat">
            <?php if ($tarea->usar_sentencias_apertura == 1): ?>
            <label><b>Empeza tu aporte con alguna de estas frases:</b></label><br />
            <label for="cbxSubhabilidades">Tipo de aporte:</label>
            <select id="cbxSubhabilidad" name="cbxSubhabilidades">
                <?php foreach ($sentenciaApertura->a_subhabilidad as $id => $subhabilidad): ?>
                <option value="<?php echo $id; ?>"><?php echo $subhabilidad; ?></option>
                <?php
                    endforeach;
                    ?>
            </select><br />
            <label>Frases disponibles:</label>
            <select id="cbxSentencias" name="cbxSentencias">
            </select><br />
            <?php endif; ?>
            <p class="lead emoji-picker-container">
                <input id="txtSentencia" name="txtSentencia" value=""
                    <?php echo ($tarea->usar_sentencias_apertura == 1) ? 'disabled="disabled"' : ''; ?>
                    class="form-control" style="height: 60px; width: 100px;" data-emojiable="true" />
            </p>
            <div class="input-drop">
                <input type="submit" id="btnEnviar" name="btnEnviar" value="Enviar Mensaje" />
                <?php
        $userid = Yii::$app->user->identity->id;
        $oUser = \app\models\Usuarios::findOne(['id' => $userid]);
        echo \kato\DropZone::widget([
            'options' => [
                'url' => Yii::$app->urlManager->createUrl(['chats/grupo', 'chatid' => Yii::$app->security->encryptByPassword($chatid, $oUser->password)]),
                'maxFilesize' => '2',
                'dictDefaultMessage' => "Coloque aquí los archivos para compartir",
            ],
            'clientEvents' => [
                'complete' => "function(file){console.log(file); if (file.status!='error') enviarArchivo(file.name);}",
                'removedfile' => "function(file){alert(file.name + ' is removed')}"
            ],
        ]);
        ?>
            </div>

        </form>
    </div>

    
    <div style=" width:70%; margin:20px 0px;">
   
     
    <?php if ($tarea->reportar_estado_animo == 1): ?>
        <h1>Estado de Animo <span style="color:#EB6500;">.</span></h1>
    <div class="texto-estado-animo">
    <p class="texto-estado-animo-p"> Selecciona tu estado de ánimo para expresar cómo te sientes durante la actividad. 🎭 Esto ayudará a tu equipo a comprender mejor tu perspectiva y promoverá un ambiente de colaboración más efectivo. ¡Cada emoción cuenta!</p>
        <p class="estado-seleccionado"><img id='imgEmocionSeleccionada'
                style="border:0px;" /> <label id='lblEmocionSeleccionada'></label></p>
    </div>
        <b class="estado-b" >¿Cómo te sientes?</b>
        <br />
        <div class="btns-emociones">
        <input type="button" id="btnNeutral" class='btnEmociones neutral' data-arousal="0" data-pleasure="0"
            data-dominance="0" data-emocion='Neutral' title="Neutral" />
        <input type="button" id="btnAngry" class='btnEmociones angry' data-arousal="0.59" data-pleasure="-0.51"
            data-dominance="0.25" data-emocion='Enojado' title="Enojado" />
        <input type="button" id="btnFear" class='btnEmociones fear' data-arousal="0.60" data-pleasure="-0.64"
            data-dominance="-0.43" data-emocion='Preocupado' title="Preocupado" />
        <input type="button" id="btnJoy" class='btnEmociones joy' data-arousal="0.2" data-pleasure="0.4"
            data-dominance=0.1 data-emocion='Alegre' title="Alegre" />
        <input type="button" id="btnSadness" class='btnEmociones sadness' data-arousal="-0.2" data-pleasure="-0.4"
            data-dominance="-0.1" data-emocion='Cansado' title="Cansado" />
        <input type="button" id="btnSurprise" class='btnEmociones surprise' data-arousal="0.59" data-pleasure="0.87"
            data-dominance="-0.87" data-emocion='Sorprendido' title="Sorprendido" />
        <input id="pleasure" type="hidden" value="0" min="-1" max="1" step="0.05" size="4" />
        <input id="arousal" type="hidden" value="0" min="-1" max="1" step="0.05" />
        <input id="dominance" type="hidden" value="0" min="-1" max="1" step="0.05" />
        <br /><br />
        <?php endif; ?>
        </div>
        
        <?php if ($tarea->reportar_conflicto == 1): ?>
            <h1>Estado de Animo del grupo<span style="color:#EB6500;">.</span></h1>
            <p class="texto-estado-animo-p">Descubre cómo se siente tu grupo para mejorar la dinámica de trabajo en equipo. 👥💬 Conoce el estado de ánimo general y trabaja en conjunto para resolver cualquier conflicto o potenciar la motivación. ¡La clave del éxito está en cómo nos sentimos y colaboramos!</p>
        <div>
            <input type="button" id="btnReporteConflicto" name="btnReporteConflicto"
                value="Siento que estamos con diferencias en el grupo"
                style="background-color: #FCE9C3; padding:10px;" /><br />
        </div>
        <?php endif; ?>


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

            ¿Listo para conquistar la cima? Revisa tu tabla de puntuacion de cada actividad.
        </p>

    </div>
    
    <div class="perfil-logros">
        <h2 class="perfil-title"><span>¿Como consigo</span> puntos? 🎯</h2>
        <p>¡Ganar puntos en la plataforma es muy sencillo y está en tus manos! 💪 A medida que participas y te
            involucras en las actividades, tus puntos irán creciendo. Aquí te mostramos cómo puedes acumular puntos y
            destacar en la tabla de posiciones:</br>
            <strong>1. Participación en el chat:</strong> Cada mensaje que envíes te otorga +10 puntos. ¡No dudes en interactuar y colaborar
            con tu equipo!</br>
            <strong>2. Completando desafíos:</strong> Cada desafío que completes te acercará a nuevos logros y te recompensará con
            puntos.</br>
            <strong>3. Actividades individuales y grupales:</strong> Realiza tanto tareas individuales como en equipo para ganar puntos por
            tu esfuerzo.</br>
            <strong>4. Notas en tareas:</strong> Las notas que obtengas en tus tareas se multiplicarán por 100 y se sumarán a tu puntaje
            total. ¡Así que da lo mejor de ti en cada trabajo!</br>
            <strong>5. Eventos especiales:</strong> Ya sea respondiendo preguntas, participando en actividades especiales o en juegos
            asignados, cada evento te recompensará con puntos adicionales.
            </br>
            </br>
            ¡Participa, suma puntos y escala hasta la cima! 🎉</p>
    </div>
</div>
