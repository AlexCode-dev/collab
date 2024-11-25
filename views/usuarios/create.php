<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

if (!isset(Yii::$app->user->identity->id)) {
    $this->title = "Completa el siguiente formulario";
} else {    
    $tipoUsuario = ($tipo == 'a') ? 'Alumnos' : (($tipo == 'd') ? 'Docentes' : 'Administradores');
    $this->title = "Crear $tipoUsuario";
    $this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index', 't' => $tipo]];
}

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuarios-create">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <div style="margin:20px 0; padding: 30px; text-align: justify; background-color: #F0DFD5">
        
        <h3>Aviso de Privacidad</h3>
        <p>Integrantes del proyecto de investigación 23/C138 “Mejorando escenarios de aprendizaje colaborativo soportado por computadora” perteneciente al Instituto de Investigación en Informática y Sistemas de Información (IIISI) de la Universidad Nacional de Santiago del Estero (Argentina) son los responsables del tratamiento de los datos personales que nos proporcione.</p>
        <p>Los datos personales que recabamos de usted, los utilizaremos exclusivamente con fines académicos y de investigación. Nuestra finalidad es generar el conocimiento necesario para generar artículos científicos en colaboración con la Universidad Autónoma de Zacatecas (México), la Universidad Militar Nueva Granada (Colombia) y la Universidad del Cauca (Colombia). En caso de que no desee que sus datos personales sean tratados para finalidad expuesta, usted puede manifestarlo al correo electrónico <a href="mailto://infoiiiisi@unse.edu.ar">infoiiiisi@unse.edu.ar</a> (casilla de correo del IIIISI). Si usted no manifiesta su negativa, se entenderá que ha otorgado su consentimiento.</p>
        <p>Se informa que no se realizarán transferencias a terceras partes de los datos recabados, y que en nuestros artículos siempre se conservarán en secreto datos sensibles como el nombre y el  apellido de nuestras fuentes.</p>
        
    </div>
    


    <?=
    $this->render('_form', [
        'model' => $model,
        'operacion' => 'alta',
    ])
    ?>

</div>
