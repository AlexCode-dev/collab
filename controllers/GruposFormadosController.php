<?php

namespace app\controllers;

use Yii;
use app\models\GruposFormados;
use app\models\GruposFormadosSearch;
use app\models\Tareas;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * GruposFormadosController implements the CRUD actions for GruposFormados model.
 */
class GruposFormadosController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'update', 'delete', 'create'],
                'rules' => [
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
                    'eliminar' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all GruposFormados models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GruposFormadosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GruposFormados model.
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
     * Creates a new GruposFormados model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GruposFormados();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing GruposFormados model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing GruposFormados model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $model_id) {
        $usuario = Yii::$app->user->identity->id;
        $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
        $id = Yii::$app->security->decryptByPassword($id, $oUser->password);
        $model = $this->findModel($id);
        $this->findModel($id)->delete();

        return $this->redirect(['grupos/view', 'id' => $model_id]);
    }

    public function actionClasificar($id, $tareas_id)
    {
        $usuario = Yii::$app->user->identity->id;
        $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
        $id = Yii::$app->security->decryptByPassword($id, $oUser->password);
    
        // Obtener el modelo del grupo formado
        $model = $this->findModel($id);
        
        // Buscar el chat asociado al grupo
        $chat = \app\models\Chats::findOne(['grupos_formados_id' => $id]);
        
        // Obtener la tarea
        $tarea = \app\models\Tareas::findOne($tareas_id);
    
        // Verificar si el formulario ha sido enviado
        if (Yii::$app->request->post()) {
            // Cargar los datos enviados al modelo chat
            if ($chat->load(Yii::$app->request->post()) && $chat->save()) {
                // Obtener el valor de 'nota' desde el modelo Chat
                $nota = $chat->nota; // Se obtiene la nota del modelo
    
                // Calcular el puntaje nuevo
                $tarea_puntaje = $tarea->puntaje_tarea ?: 0;  // Si es nulo, se asume 0
                $nuevo_puntaje = ($nota * 10) + $tarea_puntaje;
    
                // Actualizar puntaje en el modelo de grupo
                $model->puntaje = $nuevo_puntaje;
    
                // Guardar el modelo del grupo
                if ($model->save()) {
                    // Obtener todos los miembros del grupo (grupos_alumnos)
                    $miembrosGrupo = \app\models\GruposAlumnos::find()
                        ->where(['grupos_formados_id' => $id]) // Buscar por grupos_formados_id
                        ->all();
    
                    // Para cada miembro, actualizar la tabla tarea_usuario_puntaje
                    foreach ($miembrosGrupo as $miembro) {
                        $usuarioModel = \app\models\Usuarios::findOne($miembro->usuarios_id);
                        
                        if ($usuarioModel) {
                            // Incrementar cont_actividades_grupales
                            $usuarioModel->cont_actividades_grupales += 1;
    
                            // Verificar y asignar desafíos relacionados
                            Yii::$app->runAction('desafios/verificar-cantidad-actividades-realizadas', ['usuario_id' => $miembro->usuarios_id]);
                            Yii::$app->runAction('desafios/verificar-nota-mayor-a8-en-actividad-grupal', [
                                'usuario_id' => $miembro->usuarios_id,
                                'grupos_formados_id' => $id,
                            ]);
    
                            // Guardar el usuario
                            if (!$usuarioModel->save()) {
                                Yii::$app->session->setFlash('error', 'Error al actualizar las actividades grupales para el usuario: ' . $usuarioModel->id);
                            }
    
                            // Actualizar o insertar puntaje en la tabla tarea_usuario_puntaje
                            $registroPuntaje = \app\models\TareaUsuarioPuntaje::findOne([
                                'id_usuario' => $miembro->usuarios_id,
                                'id_tarea' => $tareas_id,
                            ]);
    
                            if (!$registroPuntaje) {
                                // Crear nuevo registro si no existe
                                $registroPuntaje = new \app\models\TareaUsuarioPuntaje();
                                $registroPuntaje->id_usuario = $miembro->usuarios_id;
                                $registroPuntaje->id_tarea = $tareas_id;
                                $registroPuntaje->puntaje = $nuevo_puntaje;
                            } else {
                                // Actualizar el puntaje existente
                                $registroPuntaje->puntaje += $nuevo_puntaje;
                            }
    
                            if (!$registroPuntaje->save()) {
                                Yii::$app->session->setFlash('error', 'Error al guardar el puntaje para el usuario: ' . $miembro->usuarios_id);
                            }
                        }
                    }
    
                    Yii::$app->session->setFlash('success', 'Datos guardados correctamente, puntaje y actividades grupales actualizados.');
                    return $this->redirect(['tareas/view', 'id' => $tareas_id]);
                } else {
                    Yii::$app->session->setFlash('error', 'Error al actualizar el puntaje.');
                    var_dump($model->errors); // Muestra errores al guardar
                }
            } else {
                Yii::$app->session->setFlash('error', 'Error al guardar los datos del chat.');
            }
        }
    
        return $this->render('clasificar', [
            'model' => $model,
            'tarea' => $tarea,
            'chat' => $chat,
        ]);
    }
    
    
    
    
    
    
    /**
     * Finds the GruposFormados model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GruposFormados the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GruposFormados::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
  
}
