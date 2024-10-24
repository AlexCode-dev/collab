<?php

namespace app\controllers;
use Yii;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Evento;


class EventoController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    public function actionRecuperarEventos($chatid)
    {
        // Obtener la tarea asociada al chat
        $chat = \app\models\Chats::findOne($chatid);
        $eventos = Evento::find()
            ->where(['id_tarea' => $chat->tareas_id]) // Solo eventos activos
            ->orderBy(['fecha_creacion' => SORT_ASC])
            ->all();

        return $this->renderAjax('recuperar-eventos', [
            'eventos' => $eventos,
        ]);
    }
    public function actionDesactivar()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
        $eventoId = Yii::$app->request->post('evento_id');
    
        // Encuentra el evento y cambia su estado a 'desactivado'
        $evento = \app\models\Evento::findOne($eventoId);
        if ($evento) {
            $evento->estado = 'desactivado';
            if ($evento->save()) {
                return ['success' => true, 'newStatus' => 'desactivado'];
            }
        }
    
        return ['success' => false, 'message' => 'No se pudo cambiar el estado del evento.'];
    }
    


   
    
    
    
    
    
    
    
    
    
    



}
