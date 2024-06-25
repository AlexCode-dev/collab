<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sentencias".
 *
 * @property int $id
 * @property resource $sentencia
 * @property string $fecha_hora
 * @property int $usuarios_id
 * @property int $chats_id
 *
 * @property Chats $chats
 * @property Usuarios $usuarios
 * @property Cuestionariosconflicto[] $cuestionariosconflictos
 * @property Emociones[] $emociones
 */
class Sentencias extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'sentencias';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['sentencia'], 'string'],
            [['fecha_hora', 'usuarios_id', 'chats_id'], 'required'],
            [['fecha_hora'], 'safe'],
            [['usuarios_id', 'chats_id'], 'integer'],
            [['chats_id'], 'exist', 'skipOnError' => true, 'targetClass' => Chats::className(), 'targetAttribute' => ['chats_id' => 'id']],
            [['usuarios_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['usuarios_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'sentencia' => 'Sentencia',
            'usuarios_id' => 'Usuarios ID',
            'chats_id' => 'Chats ID',
            'fecha_hora' => 'Fecha y Hora',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChats() {
        return $this->hasOne(Chats::className(), ['id' => 'chats_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarios() {
        return $this->hasOne(Usuarios::className(), ['id' => 'usuarios_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCuestionariosconflictos()
    {
        return $this->hasMany(Cuestionariosconflicto::className(), ['sentencias_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmociones()
    {
        return $this->hasMany(Emociones::className(), ['sentencias_id' => 'id']);
    }    
    
    public static function getSentenciasChat($chatid){
        $query = (new \yii\db\Query())
                ->select(['s.id', 's.sentencia','s.fecha_hora', 's.chats_id', 'u.username'])
                ->from('sentencias as s')
                ->innerJoin('usuarios as u', 's.usuarios_id = u.id')
                ->where(['s.chats_id' => $chatid])
                ->orderBy('s.id ASC');

        //echo $query->createCommand()->sql; die();
        return $query->all();
    }
    
    public static function getUltimaSentenciaChat($chatid){
        $query = (new \yii\db\Query())
                ->select(['s.id', 's.sentencia','s.fecha_hora', 's.chats_id', 'u.username'])
                ->from('sentencias as s')
                ->innerJoin('usuarios as u', 's.usuarios_id = u.id')
                ->where(['s.chats_id' => $chatid])
                ->orderBy('s.id DESC')
                ->limit(1);

        //echo $query->createCommand()->sql; die();
        return $query->all();
    }

}
