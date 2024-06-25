<?php
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'COLLAB';
$completarEstiloAprendizaje = false;

if (isset(Yii::$app->user->identity->id)) {
    $rolesUsuario = Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id);
} else {
    $rolesUsuario = [];
}

if (array_key_exists('estudiante', $rolesUsuario)) {
    // Se verifica si el usuario completo el test de estilos de aprendizaje
    $objEstudiante = \app\models\Usuarios::findOne(['id', Yii::$app->user->identity->id]);
    if (empty($objEstudiante->estiloaprendizaje)) {
        $completarEstiloAprendizaje = true;
    }
}
?>

<div class="site-index">

    <div class="jumbotron">
        <h1>COLLAB</h1>

        <p class="lead">Una herramienta para soportar la comunicación en entornos de Aprendizaje Colaborativo Soportado por Computadora (ACSC).</p>

        <?= !(isset(Yii::$app->user->identity->id)) ? "<h2>¿Sos estudiante?</h2>" . Html::a('Create una Cuenta', ['usuarios/create', 't' => 'a'], ['class' => 'btn btn-success']) : ''; ?>

    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-12">
                <?php if ($completarEstiloAprendizaje): ?>
                    <div style='margin: 0px auto 20px auto; width: 35%; text-align: center;padding: 10px; border:#FDD900 1px solid; background-color: #FEFEB4'>
                        <p>Recuerda completar el test de estilos de aprendizaje para tener un perfil completo en el sistema.</p>
                        <?= Html::a('Completar ahora...', ['usuarios/test-felder-silverman'], ['class' => 'btn btn-success']) ?>
                    </div>
                    <img src="<?= Yii::$app->request->baseUrl . "/images/colab.png" ?>" style="display: block;margin: 0 auto;"/>  
                <?php else: ?>
                    <img src="<?= Yii::$app->request->baseUrl . "/images/colab.png" ?>" style="display: block;margin: 0 auto;"/>  
                    <div style="margin:20px 0; padding: 30px; text-align: justify; background-color: #F0DFD5">                        
                        <h3>Aviso de Privacidad</h3>
						<p>Integrantes del proyecto de investigación 23/C176-A-2022 “DESARROLLO DE APLICACIONES PARA COLABORACIÓN EN E-LEARNING” perteneciente al Instituto de Investigación en Informática y Sistemas de Información (IIISI) de la Universidad Nacional de Santiago del Estero (Argentina) son los responsables del tratamiento de los datos personales que nos proporcione.</p>
						<p>Los datos personales que recabamos de usted, los utilizaremos exclusivamente con fines académicos y de investigación. Nuestra finalidad es generar el conocimiento necesario para generar artículos científicos. En caso de que no desee que sus datos personales sean tratados para finalidad expuesta, usted puede manifestarlo al correo electrónico rosanna@unse.edu.ar (casilla de correo de la directora del proyecto de investigación IIISI antes mencionado). Si usted no manifiesta su negativa, se entenderá que ha otorgado su consentimiento.</p>
						<p>Se informa que no se realizarán transferencias a terceras partes de los datos recabados, y que en nuestros artículos siempre se conservarán en secreto datos sensibles como el nombre y el apellido de nuestras fuentes.</p>
                    </div>
                <?php endif; ?>
                          
            </div>
        </div>

    </div>
</div>
