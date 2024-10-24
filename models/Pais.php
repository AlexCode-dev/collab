<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pais".
 *
 * @property int $idpais
 * @property string $nombre
 */
class Pais extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pais';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idpais' => 'Idpais',
            'nombre' => 'País',
        ];
    }
    
    public static function getListaPaises() {
        return yii\helpers\ArrayHelper::map(Pais::find()->all(), 'idpais', 'nombre');
    }
    
    public static function getNombrePorId($id) {
        $objPais = static::findOne(['idpais' => $id]);
<<<<<<< HEAD
        
        // Verifica si el país existe
        if ($objPais !== null) {
            return $objPais->nombre;
        } else {
            return 'Desconocido'; // O cualquier otro valor predeterminado
        }
    }
    
=======
        return $objPais->nombre;
    }
>>>>>>> 738f8d5f4e3524f9b29eacb1792cac1dc4cdf247
}
