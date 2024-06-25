<?php

namespace app\controllers;

use Yii;
use app\models\Tareas;
use app\models\TareasSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * TareasController implements the CRUD actions for Tareas model.
 */
class TareasController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'update', 'delete', 'create'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'update', 'create'],
                        'allow' => true,
                        'roles' => ['profesor'],
                    ],
                    [
                        'actions' => ['index', 'view', 'update', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['administrador'],
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
     * Lists all Tareas models.
     * @return mixed
     */
    public function actionIndex($asigid) {
        $usuario = Yii::$app->user->identity->id;
        $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
        $asigid = Yii::$app->security->decryptByPassword($asigid, $oUser->password);
        
        $searchModel = new TareasSearch();
        $searchModel->asignaturas_id = $asigid;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'asigid' => $asigid,
        ]);
    }

    public function actionTareasAlumnos($asigid, $year) {      
        $userid = Yii::$app->user->identity->id;   
        $oUser = \app\models\Usuarios::findOne(['id' => $userid]);
        $asigid_decoded = Yii::$app->security->decryptByPassword($asigid, $oUser->password);
        $year_decoded = Yii::$app->security->decryptByPassword($year, $oUser->password);
        
        
        $searchModel = new TareasSearch();
        $searchModel->asignaturas_id = $asigid_decoded;
        $searchModel->year = $year_decoded;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('tareas-alumnos', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'asigid' => $asigid_decoded,
        ]);
    }

    /**
     * Displays a single Tareas model.
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
     * Creates a new Tareas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($asigid) {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $model = new Tareas();
        $model->asignaturas_id = $asigid;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $grupos = \app\models\GruposFormados::getDetalleGrupos($model->grupos_id);
            $titulo = "";

            foreach ($grupos as $gr) {
                if ($titulo != $gr["nombre"]) {
                    $objChat = new \app\models\Chats();
                    $objChat->descripcion = 'Chat correspondiente a la tarea ' . $model->descripcion . ' que emplea la configuración de grupos ' . $gr['codigo'];
                    $objChat->fecha = date('Y-m-d h:i:s', time());
                    $objChat->tareas_id = $model->id;
                    $objChat->grupos_formados_id = $gr['id'];
                    $objChat->save();
                    $titulo = $gr["nombre"];
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing Tareas model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->usar_sentencias_apertura = ($model->usar_sentencias_apertura) ? 1 : 0;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Tareas model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['asignaturas/index']);
    }

    /**
     * Finds the Tareas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tareas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Tareas::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
