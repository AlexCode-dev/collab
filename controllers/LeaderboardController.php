<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Usuarios;
use yii\data\ActiveDataProvider;

class LeaderboardController extends Controller
{
    public function actionIndex()
    {
        // Crear un ActiveDataProvider para la paginación
        $dataProvider = new ActiveDataProvider([
            'query' => (new \yii\db\Query())
                ->select(['usuarios.*', 'rango.nombre AS rango_nombre', 'rango.imagen AS rango_imagen']) // Seleccionamos también la imagen del rango
                ->from('usuarios')
                ->leftJoin(
                    '(SELECT usuarios_id, MAX(rangos_id) as rangos_id FROM rangos_usuarios GROUP BY usuarios_id) as usuario_rango',
                    'usuarios.id = usuario_rango.usuarios_id'
                )
                ->leftJoin('rangos rango', 'rango.id = usuario_rango.rangos_id')
                ->orderBy(['puntaje' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10, // Número de usuarios por página
            ],
        ]);
    
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    


}
