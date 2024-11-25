<?php

namespace app\controllers;

use Yii;
use app\models\Chats;
use app\models\ChatsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ChatsController implements the CRUD actions for Chats model.
 */
class ChatsController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'update', 'delete', 'create', 'grupo', 'recuperar-chat'],
                'rules' => [
                    [
                        'actions' => ['grupo', 'recuperar-chat'],
                        'allow' => true,
                        'roles' => ['estudiante'],
                    ],
                    [
                        'actions' => ['index', 'view', 'update', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['profesor'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Chats models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ChatsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    function crearCarpetaChat($objChat, $objTarea) {
        
    }

    public function actionGrupo($chatid)
    {
        $userid = Yii::$app->user->identity->id; // ID del usuario actual
        $oUser = \app\models\Usuarios::findOne(['id' => $userid]);
        $chatiddecoded = Yii::$app->security->decryptByPassword($chatid, $oUser->password);
    
        $oChat = Chats::findOne(['id' => $chatiddecoded]);
        $grupo = \app\models\GruposAlumnos::findOne(['grupos_formados_id' => $oChat->grupos_formados_id, 'usuarios_id' => $userid]);
        if (!$grupo && $oUser->tipo != 1) {
            throw new \yii\web\ForbiddenHttpException("No puede acceder a esta página");
        }
    
        // Llamada a getSentenciasChat con ambos parámetros
        $chat = \app\models\Sentencias::getSentenciasChat($chatiddecoded, $userid);
        $datosChat = Chats::findOne(['id' => $chatiddecoded]);
        $tarea = \app\models\Tareas::findOne(['id' => $datosChat->tareas_id]);
        $asignatura = \app\models\Asignaturas::findOne(['id' => $tarea->asignaturas_id])->nombre;
    
        // Directorio para subir archivos
        $fileName = 'file';
        $uploadPath = 'uploads/';
        $directorio = md5($asignatura . " - " . $oChat->grupos_formados_id);
    
        // Crear la carpeta si no existe
        if (!is_dir($uploadPath . $directorio)) {
            mkdir($uploadPath . $directorio, 0777);
        }
    
        // Procesar el archivo si se ha subido
        if (isset($_FILES[$fileName])) {
            $file = \yii\web\UploadedFile::getInstanceByName($fileName);
            if ($file && $file->saveAs($uploadPath . $directorio . '/' . $file->name)) {
                // Guardar los datos del archivo en la base de datos si es necesario
                echo \yii\helpers\Json::encode($file);
            }
        }
    
        // Generar URL para recuperar eventos
        $recuperarEventosUrl = Yii::$app->urlManager->createUrl(['eventos/recuperar-eventos', 'chatid' => $chatiddecoded]);
    
        // Renderizar la vista
        return $this->render('grupo', [
            'chat' => $chat,
            'chatid' => $chatiddecoded,
            'tarea' => $tarea,
            'grupo_id' => $oChat->grupos_formados_id,
            'asignatura' => $asignatura,
            'directorio' => $directorio,
            'recuperarEventosUrl' => $recuperarEventosUrl
        ]);
    }
 
    

    public function actionPuntuar()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $usuarioId = Yii::$app->request->post('usuario_id');
        $puntos = Yii::$app->request->post('puntos');

        // Buscar al usuario y actualizar el puntaje
        $usuario = \app\models\Usuarios::findOne($usuarioId);
        if ($usuario) {
            $usuario->puntaje += $puntos;
            if ($usuario->save()) {
                return ['success' => true];
            }
        }
        return ['success' => false];
    }
    
    public function actionPuntuarRespuesta()
    {
        $request = Yii::$app->request;
        $evento_id = $request->post('evento_id');
        $usuario_id = $request->post('usuario_id');
        $respuesta = 'Puntuado con OK';
        $puntos = 100; // Puntos asignados para respuestas
    
        // Verificar si ya existe una respuesta para este usuario y evento
        $respuestaExiste = (new \yii\db\Query())
            ->from('respuesta_preguntas')
            ->where(['evento_id' => $evento_id, 'usuario_id' => $usuario_id])
            ->exists();
    
        if ($respuestaExiste) {
            return $this->asJson([
                'success' => false,
                'message' => 'Ya has puntuado esta respuesta.',
            ]);
        }
    
        // Insertar la respuesta en la tabla respuesta_preguntas
        $command = Yii::$app->db->createCommand()->insert('respuesta_preguntas', [
            'evento_id' => $evento_id,
            'usuario_id' => $usuario_id,
            'respuesta' => $respuesta,
        ]);
    
        if ($command->execute()) {
            // Obtener el ID de la tarea asociada al evento
            $tareaId = (new \yii\db\Query())
                ->select('id_tarea')
                ->from('eventos')
                ->where(['id' => $evento_id])
                ->scalar();
    
            if ($tareaId) {
                // Actualizar o insertar en tarea_usuario_puntaje
                $this->actualizarPuntajeTareaUsuario($tareaId, $usuario_id, $puntos);
            }
    
            return $this->asJson(['success' => true]);
        } else {
            return $this->asJson(['success' => false, 'message' => 'No se pudo puntuar la respuesta.']);
        }
    }
    
    public function actionPuntuarDebate()
    {
        $request = Yii::$app->request;
        $evento_id = $request->post('evento_id');
        $usuario_id = $request->post('usuario_id');
        $puntos = 150; // Puntos asignados para debates
    
        // Verificar si el evento es del tipo 'debate'
        $evento = (new \yii\db\Query())
            ->from('eventos')
            ->where(['id' => $evento_id, 'tipo_evento' => 'debate'])
            ->one();
    
        if (!$evento) {
            return $this->asJson([
                'success' => false,
                'message' => 'El evento no es un debate o no existe.',
            ]);
        }
    
        // Verificar si ya existe una puntuación para este usuario en este evento
        $puntuacionExiste = (new \yii\db\Query())
            ->from('respuesta_preguntas')
            ->where(['evento_id' => $evento_id, 'usuario_id' => $usuario_id])
            ->exists();
    
        if ($puntuacionExiste) {
            return $this->asJson([
                'success' => false,
                'message' => 'Ya has puntuado este debate.',
            ]);
        }
    
        // Insertar la puntuación del debate en la tabla respuesta_preguntas
        $command = Yii::$app->db->createCommand()->insert('respuesta_preguntas', [
            'evento_id' => $evento_id,
            'usuario_id' => $usuario_id,
            'respuesta' => 'Puntuado con OK',
        ]);
    
        if ($command->execute()) {
            // Obtener el ID de la tarea asociada al evento
            $tareaId = (new \yii\db\Query())
                ->select('id_tarea')
                ->from('eventos')
                ->where(['id' => $evento_id])
                ->scalar();
    
            if ($tareaId) {
                // Actualizar o insertar en tarea_usuario_puntaje
                $this->actualizarPuntajeTareaUsuario($tareaId, $usuario_id, $puntos);
            }
    
            return $this->asJson(['success' => true]);
        } else {
            return $this->asJson(['success' => false, 'message' => 'No se pudo registrar la puntuación del debate.']);
        }
    }
    
    /**
     * Método para actualizar la tabla tarea_usuario_puntaje.
     */
    private function actualizarPuntajeTareaUsuario($tareaId, $usuario_id, $puntos)
    {
        // Verificar si ya existe un registro para esta tarea y usuario
        $registroExiste = (new \yii\db\Query())
            ->from('tarea_usuario_puntaje')
            ->where(['id_tarea' => $tareaId, 'id_usuario' => $usuario_id])
            ->exists();
    
        if ($registroExiste) {
            // Actualizar el puntaje existente
            Yii::$app->db->createCommand()
                ->update('tarea_usuario_puntaje', [
                    'puntaje' => new \yii\db\Expression('puntaje + :puntos'),
                ], [
                    'id_tarea' => $tareaId,
                    'id_usuario' => $usuario_id,
                ], [
                    ':puntos' => $puntos,
                ])
                ->execute();
        } else {
            // Insertar un nuevo registro
            Yii::$app->db->createCommand()
                ->insert('tarea_usuario_puntaje', [
                    'id_tarea' => $tareaId,
                    'id_usuario' => $usuario_id,
                    'puntaje' => $puntos,
                ])
                ->execute();
        }
    }
    

  
 


       
    
    public function actionRecuperarChat($chatid) {
        $currentUserId = Yii::$app->user->identity->id;  // Obtener el ID del usuario actual
        $chat = \app\models\Sentencias::getSentenciasChat($chatid, $currentUserId);  // Pasar ambos argumentos
    
        if (empty($chat)) {
            throw new \yii\web\NotFoundHttpException('No se encontraron sentencias en el chat.');
        }
    
        $rolesUsuario = Yii::$app->authManager->getRolesByUser($currentUserId);
        $esProfesor = array_key_exists('profesor', $rolesUsuario);
    
        foreach ($chat as &$sentencia) {
            if (isset($sentencia['usuarios_id'])) {
                $usuario_id = $sentencia['usuarios_id'];
    
                // Obtener el puntaje del usuario
                $usuario = \app\models\Usuarios::findOne($usuario_id);
                $sentencia['puntaje'] = isset($usuario->puntaje) ? $usuario->puntaje : 0;
    
                // Obtener el rango del usuario
                $rangoUsuario = \app\models\RangosUsuarios::find()
                    ->where(['usuarios_id' => $usuario_id])
                    ->orderBy(['rangos_id' => SORT_DESC]) // Obtener el rango más alto
                    ->one();
    
                // Asignar el nombre del rango
                $sentencia['rango_nombre'] = $rangoUsuario ? \app\models\Rangos::findOne($rangoUsuario->rangos_id)->nombre : 'Sin Rango';
            } else {
                $sentencia['puntaje'] = 0;  // En caso de que no se haya encontrado usuario
                $sentencia['rango_nombre'] = 'Sin Rango';
            }
        }
    
        // Pasar el chat a la vista
        return $this->renderAjax('recuperar-chat', [
            'chat' => $chat,
            'esProfesor' => $esProfesor,
        ]);
    }
    
    
    

    public function actionRecuperarUltimaSentenciaChat($chatid) {
        $currentUserId = Yii::$app->user->identity->id;  // Obtener el ID del usuario actual

        // Obtener la última sentencia del chat
        $chat = \app\models\Sentencias::getUltimaSentenciaChat($chatid);
        $rolesUsuario = Yii::$app->authManager->getRolesByUser($currentUserId);
        $esProfesor = array_key_exists('profesor', $rolesUsuario);
        // Asegurarse de que el array $chat no esté vacío antes de acceder a él
        if (!empty($chat) && isset($chat[0]['usuarios_id'])) {
            $usuario_id = $chat[0]['usuarios_id'];
    
            // Verificar primer mensaje del desafío
            Yii::$app->runAction('desafios/verificar-primer-mensaje', ['usuario_id' => $usuario_id]);
    
            // Obtener el puntaje del usuario
            $usuario = \app\models\Usuarios::findOne($usuario_id);
            $puntaje = isset($usuario->puntaje) ? $usuario->puntaje : 0;  // Asignar 0 si no tiene puntaje
    
            // Obtener el rango del usuario de la tabla rangos_usuarios
            $rangoUsuario = \app\models\RangosUsuarios::find()
                ->where(['usuarios_id' => $usuario_id])
                ->orderBy(['rangos_id' => SORT_DESC]) // Obtener el rango más alto
                ->one();
    
            // Obtener el nombre del rango si existe
            $rangoNombre = $rangoUsuario ? \app\models\Rangos::findOne($rangoUsuario->rangos_id)->nombre : 'Sin Rango';
        } else {
            $puntaje = 0;  // En caso de que no se haya encontrado usuario
            $rangoNombre = 'Sin Rango';
        }
    
        // Renderizar la vista con los datos del chat, puntaje y rango
        return $this->renderAjax('recuperar-ultima-sentencia-chat', [
            'chat' => $chat,
            'puntaje' => $puntaje,
            'rangoNombre' => $rangoNombre,
            'esProfesor' => $esProfesor,
        ]);
    }
    
    
    

    /**
     * Displays a single Chats model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Chats model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Chats();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing Chats model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Chats model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Chats model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Chats the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Chats::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
