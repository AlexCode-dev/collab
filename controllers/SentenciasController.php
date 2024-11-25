<?php

namespace app\controllers;

use Yii;
use app\models\Sentencias;
use app\models\SentenciasSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * SentenciasController implements the CRUD actions for Sentencias model.
 */
class SentenciasController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'update', 'delete', 'create', 'crear-con-ajax'],
                'rules' => [
                    [
                        'actions' => ['crear-con-ajax'],
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
     * Lists all Sentencias models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SentenciasSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sentencias model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Sentencias model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $model = new Sentencias();
        $model->fecha_hora = date('Y-m-d h:i:s', time());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    
    public function actionCrearConAjax($usuarios_id, $chats_id, $sentencia)
    {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        
        // Crear una nueva instancia del modelo Sentencias
        $model = new Sentencias();
        $model->fecha_hora = date('Y-m-d h:i:s', time());
        $model->usuarios_id = $usuarios_id;
        $model->chats_id = $chats_id;
        $model->sentencia = $sentencia;
    
        // Llamar al método para actualizar el puntaje del usuario en general
        Yii::$app->runAction('logros/actualizar-puntaje-por-mensaje', ['usuario_id' => $usuarios_id]);
    
        // Obtener la tarea asociada al chat
        $chat = \app\models\Chats::findOne($chats_id);
    
        if ($chat && $chat->tareas_id) {
            // Buscar o crear un registro en tarea_usuario_puntaje
            $tareaUsuarioPuntaje = \app\models\TareaUsuarioPuntaje::findOne(['id_usuario' => $usuarios_id, 'id_tarea' => $chat->tareas_id]);
    
            if (!$tareaUsuarioPuntaje) {
                // Si no existe, crear un nuevo registro
                $tareaUsuarioPuntaje = new \app\models\TareaUsuarioPuntaje();
                $tareaUsuarioPuntaje->id_usuario = $usuarios_id;
                $tareaUsuarioPuntaje->id_tarea = $chat->tareas_id;
                $tareaUsuarioPuntaje->puntaje = 10; // Inicializamos con los 10 puntos
            } else {
                // Si existe, incrementar el puntaje
                $tareaUsuarioPuntaje->puntaje += 10;
            }
    
            // Guardar los cambios en la tabla tarea_usuario_puntaje
            if (!$tareaUsuarioPuntaje->save(false)) {
                Yii::error('Error al actualizar el puntaje en tarea_usuario_puntaje: ' . print_r($tareaUsuarioPuntaje->getErrors(), true), __METHOD__);
            }
        } else {
            Yii::error('No se encontró un chat o tarea válida para el chat_id: ' . $chats_id, __METHOD__);
        }
    
        // Guardar la sentencia
        $model->save();
    
        return $model->id;
    }
    
    /**
     * Updates an existing Sentencias model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $model = $this->findModel($id);
        $model->fecha_hora = date('Y-m-d h:i:s', time());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
       

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Sentencias model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Sentencias model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sentencias the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sentencias::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
