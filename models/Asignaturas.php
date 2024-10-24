<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "asignaturas".
 *
 * @property int $id
 * @property string $nombre
 * @property int $carreras_id
 *
 * @property AsignaturasDocentes[] $asignaturasDocentes
 * @property Grupos[] $grupos
 */
class Asignaturas extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'asignaturas';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['nombre'], 'string', 'max' => 100],
            [['carreras_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'carreras_id' => 'ID Carrera',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsignaturasDocentes() {
        return $this->hasMany(AsignaturasDocentes::className(), ['asignaturas_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrupos() {
        return $this->hasMany(Grupos::className(), ['asignaturas_id' => 'id']);
    }
    
     public function getNombreCompleto() {
        $objCarrera = Carreras::findOne(['id' => $this->carreras_id]);
        return $this->nombre . ', ' . $objCarrera->nombre . ', ' . $objCarrera->universidad;
    }

    public static function getListaAsignaturas() {
        return yii\helpers\ArrayHelper::map(Asignaturas::find()->all(), 'id', 'nombrecompleto');
    }

    public static function getNombrePorId($id) {
        $objAsignatura = static::findOne(['id' => $id]);
        $objCarrera = Carreras::findOne(['id' => $objAsignatura->carreras_id]);
        return $objAsignatura->nombre . ', ' . $objCarrera->nombre . ', ' . $objCarrera->universidad;
    }

}
