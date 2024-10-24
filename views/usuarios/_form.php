<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="usuarios-form">

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'apellido')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'fechanacimiento')->widget(\yii\jui\DatePicker::class, [
    'language' => 'es',
    'dateFormat' => 'dd/MM/yyyy',
    'clientOptions' => [
        'changeMonth' => 'true',
        'changeYear' => 'true',
    ],
]) ?>

<?= $form->field($model, 'pais_idpais')->dropDownList(app\models\Pais::getListaPaises())->label('País') ?>

<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

<?= ($operacion == 'alta') ? $form->field($model, 'username')->textInput(['maxlength' => true]) : '' ?>

<?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

<!-- Campo para subir la foto de perfil -->
<?= $form->field($model, 'foto_perfil')->fileInput(['id' => 'uploadFotoPerfil']) ?>

<?php if ($model->foto_perfil): ?>
<img id="preview" src="<?= Yii::getAlias('@web') . '/' . $model->foto_perfil ?>" alt="Foto de perfil" width="150">
<?php else: ?>
<img id="preview" src="" alt="Previsualización de la foto de perfil" width="150" style="display:none;">
<?php endif; ?>

<div class="form-group mt-5" >
<?= Html::submitButton('Guardar', ['class' => 'button-g', 'style' => 'width:100%;']) ?>

</div>

<?php ActiveForm::end(); ?>

</div>

<script>
// JavaScript para previsualizar la imagen seleccionada
document.getElementById('uploadFotoPerfil').onchange = function (evt) {
    const [file] = this.files;
    if (file) {
        document.getElementById('preview').src = URL.createObjectURL(file);
        document.getElementById('preview').style.display = 'block';
    }
};
</script>

