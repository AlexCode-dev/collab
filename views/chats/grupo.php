<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ChatsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$miembrosChat = app\models\Chats::getMiembrosChat($grupo_id);
$usuarioid = Yii::$app->user->identity->id;

// urls para hacer llamados AJAX
$recuperarChat = Yii::$app->urlManager->createUrl(['chats/recuperar-chat', 'chatid' => $chatid]);
$recuperarUltimaSentenciaChat = Yii::$app->urlManager->createUrl(['chats/recuperar-ultima-sentencia-chat', 'chatid' => $chatid]);
$enviarSentencia = Yii::$app->urlManager->createUrl(['sentencias/crear-con-ajax']);
$enviarReporteEstadoAnimo = Yii::$app->urlManager->createUrl(['emociones/crear-con-ajax']);
$sentenciasApertura = Yii::$app->urlManager->createUrl(['sentencias-apertura/recuperar-sentencias']);
$rEstadoAnimo = ($tarea->reportar_estado_animo) ? 1 : 0;
$rConflicto = ($tarea->reportar_conflicto) ? 1 : 0;
$enviarReporteConflicto = Yii::$app->urlManager->createUrl(['conflictos/crear-con-ajax']);
$urlUploads = Yii::$app->request->baseUrl . "/uploads/$directorio/";

$this->title = $asignatura . " - " . $tarea->nombre_t . " / Grupo " . $miembrosChat[0]["grupos_formados_id"] . " - " . $miembrosChat[0]["alumnos"] ;
$this->params['breadcrumbs'][] = $this->title;


/* $this->registerCssFile("https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css");
  $this->registerCssFile(Yii::$app->request->baseUrl . "/emoji-picker/css/emoji.css"); */


$this->registerJsFile(Yii::$app->request->baseUrl . '/js/jquery.rateyo.min.js', ['depends' => [
        \yii\jui\JuiAsset::className(),
]]);

$this->registerJsFile(Yii::$app->request->baseUrl . '/emoji-picker/js/config.js', ['depends' => [
        \yii\web\JqueryAsset::className(),
]]);

$this->registerJsFile(Yii::$app->request->baseUrl . '/emoji-picker/js/util.js', ['depends' => [
        \yii\web\JqueryAsset::className(),
]]);

$this->registerJsFile(Yii::$app->request->baseUrl . '/emoji-picker/js/jquery.emojiarea.js', ['depends' => [
        \yii\web\JqueryAsset::className(),
]]);

$this->registerJsFile(Yii::$app->request->baseUrl . '/emoji-picker/js/emoji-picker.js', ['depends' => [
        \yii\web\JqueryAsset::className(),
]]);

$base = Yii::$app->request->baseUrl;
$script = <<< JS
    function enviarArchivo(nombreArchivo){        
        sentenciaEnviar = "<a href='$urlUploads" + nombreArchivo + "' target='_blank'>" + nombreArchivo + "</a>";
        $.ajax({
            method: 'GET',
            url: "$enviarSentencia",
            data: {sentencia: sentenciaEnviar, usuarios_id:$usuarioid, chats_id: $chatid},
        }).done(function (data) {           
            return true;
        });         
    }
        
    $(function () {
        var bSeleccionSentencia = 0;
        var rEstadoAnimo = $rEstadoAnimo;
        var rConflicto = $rConflicto;
        var presenciaConflicto = 0;
        var scrollTopBefore = 0;
        var cargaInicial = 0;
        var lastScrollHeight = 0;
        var ultimaSentencia = "";

        
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
            url: "$recuperarChat",
        }).done(function (data) {
            $('#divChat').append(data);
            var objDiv = document.getElementById("divChat");
            objDiv.scrollTop = objDiv.scrollHeight; 
            lastScrollHeight = objDiv.scrollHeight;                      
        });
        
        $.ajax({
            url: "$recuperarUltimaSentenciaChat",
        }).done(function (data) {
            ultimaSentencia = data;                    
        });
        
        var interval = setInterval(function(){
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
        
            if ($('#txtSentencia').val().length != 0){
                var sentenciaApertura = '';
                if (bSeleccionSentencia == 1){                    
                    sentenciaApertura = '<b>' + $('#cbxSentencias :selected').text() + '</b> ';
                }
                var idhidden = $('#txtSentencia').data('id');
                var raw = ($("*[data-id=" + idhidden + "]").val());        
                var sentenciaEnviar = sentenciaApertura + raw;

                // Se envia el mensaje
                $.ajax({
                    method: 'GET',
                    url: "$enviarSentencia",
                    data: {sentencia: sentenciaEnviar, usuarios_id:$usuarioid, chats_id: $chatid},
                }).done(function (data) {
                    $('#txtSentencia').val('');
                    $('.emoji-wysiwyg-editor').html("");  

                    return true;
                });        
            }        
        });                
        
        if (rEstadoAnimo == 1){
            // Se establece una emoción por defecto
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
                    data: {id: $chatid, valence: $('#pleasure').val(), arousal: $('#arousal').val(), dominance: $('#dominance').val(), usuarios_id:$usuarioid},
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
                    data: {idChat: $chatid, usuarios_id:$usuarioid},
                }).done(function () {
                    return true;
                });
            });        
        }        
        
        // Initializes and creates emoji set from sprite sheet
        window.emojiPicker = new EmojiPicker({
          emojiable_selector: '[data-emojiable=true]',
          assetsPath: '$base/emoji-picker/img/',
          popupButtonClasses: 'fa fa-smile-o'
        });
        // Finds all elements with `emojiable_selector` and converts them to rich emoji input fields
        // You may want to delay this step if you have dynamically created input fields that appear later in the loading process
        // It can be called as many times as necessary; previously converted input fields will not be converted again
        window.emojiPicker.discover();
    });                             
JS;
$this->registerJs($script, yii\web\View::POS_END);
$sentenciaApertura = new app\models\SentenciasApertura();
?>


<div class="chats-index">    
    <p style="background-color: #BEF7F8; padding: 10px; font-size: 1.3em;"><b>Consigna:</b><br/><?= $tarea->consigna ?></p>

    <div style="float:left; margin: 0px auto 10px auto;">
        <div id='divChat' style="width: 700px; height: 350px; overflow-y: scroll;"></div>
        <br/>
        <div id="goBottom" style="margin: 0 auto; width: 200px; display: none;">
            <input id="btnGoBottom" type="button" style="background-color: #FEE300;  text-align: center; padding: 5px;" value="Tienes nuevos mensajes"/>            
        </div>

        <form id="frmChat">
            <?php if ($tarea->usar_sentencias_apertura == 1): ?>
                <label><b>Empeza tu aporte con alguna de estas frases:</b></label><br/>
                <label for="cbxSubhabilidades">Tipo de aporte:</label>
                <select id="cbxSubhabilidad" name="cbxSubhabilidades">
                    <?php foreach ($sentenciaApertura->a_subhabilidad as $id => $subhabilidad): ?>
                        <option value="<?php echo $id; ?>"><?php echo $subhabilidad; ?></option>
                        <?php
                    endforeach;
                    ?>
                </select><br/>
                <label>Frases disponibles:</label>
                <select id="cbxSentencias" name="cbxSentencias">                    
                </select><br/>
            <?php endif; ?>
            <p class="lead emoji-picker-container">
                <input id="txtSentencia" name="txtSentencia" value="" <?php echo ($tarea->usar_sentencias_apertura == 1) ? 'disabled="disabled"' : ''; ?> class="form-control" style="height: 60px; width: 100px;" data-emojiable="true" />
            </p>

            <input type="submit" id="btnEnviar" name="btnEnviar" value="Enviar"/>
        </form>  
    </div>



    <div style=" width:400px; margin:0 20px; float:left;">
        <?php if ($tarea->reportar_estado_animo == 1): ?>
            <p>Estado de &aacute;nimo seleccionado: <br/><img id='imgEmocionSeleccionada' style="border:0px;"/> <label id='lblEmocionSeleccionada'></label></p>
            <b>Me siento...</b><br/>
            <input type="button" id="btnNeutral" class='btnEmociones neutral' data-arousal="0" data-pleasure="0" data-dominance="0" data-emocion='Neutral' title="Neutral"/>
            <input type="button" id="btnAngry" class='btnEmociones angry' data-arousal="0.59" data-pleasure="-0.51" data-dominance="0.25" data-emocion='Enojado' title="Enojado"/>
            <input type="button" id="btnFear" class='btnEmociones fear' data-arousal="0.60" data-pleasure="-0.64" data-dominance="-0.43" data-emocion='Preocupado' title="Preocupado"/>
            <input type="button" id="btnJoy" class='btnEmociones joy' data-arousal="0.2" data-pleasure="0.4" data-dominance=0.1 data-emocion='Alegre' title="Alegre"/>
            <input type="button" id="btnSadness" class='btnEmociones sadness' data-arousal="-0.2" data-pleasure="-0.4" data-dominance="-0.1" data-emocion='Cansado' title="Cansado"/>
            <input type="button" id="btnSurprise" class='btnEmociones surprise' data-arousal="0.59" data-pleasure="0.87" data-dominance="-0.87" data-emocion='Sorprendido' title="Sorprendido"/>
            <input id="pleasure" type="hidden" value="0" min="-1" max="1" step="0.05" size="4" />
            <input id="arousal" type="hidden" value="0" min="-1" max="1" step="0.05" />
            <input id="dominance" type="hidden" value="0" min="-1" max="1" step="0.05" />
            <br/><br/>
        <?php endif; ?>

        <?php if ($tarea->reportar_conflicto == 1): ?>
            <div style="padding: 10px;">
                <input type="button" id="btnReporteConflicto" name="btnReporteConflicto" value="Siento que estamos con diferencias en el grupo" style="background-color: #FCE9C3; padding:10px;"/><br/>                        
            </div>
        <?php endif; ?>

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

        <br/>
    </div>


    <div style="clear:both;">

    </div>
</div>