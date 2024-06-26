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

    public function actionClasificar($id)
    {
        $usuario = Yii::$app->user->identity->id;
        $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
        $id = Yii::$app->security->decryptByPassword($id, $oUser->password);
        $model = $this->findModel($id);
        $tareaExistente = Tareas::findOne(['grupos_id' => $model->grupos_id]);
        $tarea = Tareas::findOne(['grupos_id' => $model->grupos_id]);
       
        if ($tarea === null) {
            throw new NotFoundHttpException('La tarea no existe.');
        }
       
        if (Yii::$app->request->post()) {
            // Cargar solo los campos específicos
            $tarea->nota = Yii::$app->request->post('Tareas')['nota'];
            $tarea->descripcion_nota = Yii::$app->request->post('Tareas')['descripcion_nota'];
    
            // Guardar solo los campos específicos sin validar los demás
            if ($tarea->save(false, ['nota', 'descripcion_nota'])) {
                Yii::$app->session->setFlash('success', 'Nota actualizada correctamente.');
                return $this->redirect(['grupos/view', 'id' => $model->grupos_id]);
            } else {
                Yii::$app->session->setFlash('error', 'No se pudo actualizar la tarea.');
                Yii::error($tarea->errors); // Registro de errores en el log
                // Mostrar errores de validación en la vista
                echo '<pre>';
                print_r($tarea->errors);
                echo '</pre>';
                exit;
            }
        }
        return $this->render('clasificar', [
            'model' => $model,  
            'tarea' => $tarea,
            'tareaExistente' => $tareaExistente,
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
