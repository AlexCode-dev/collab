<?php

namespace app\controllers;

use Yii;
use app\models\Asignaturas;
use app\models\AsignaturasSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * AsignaturasController implements the CRUD actions for Asignaturas model.
 */
class AsignaturasController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'update', 'delete', 'create', 'asignaturas-alumnos'],
                'rules' => [
                    [
                        'actions' => ['asignaturas-alumnos'],
                        'allow' => true,
                        'roles' => ['estudiante'],
                    ],
                    [
                        'actions' => ['index', 'view', 'update', 'delete', 'create', 'asignaturas-alumnos'],
                        'allow' => true,
                        'roles' => ['profesor'],
                    ],
                    [
                        'actions' => ['index', 'view', 'update', 'delete', 'create', 'asignaturas-alumnos'],
                        'allow' => true,
                        'roles' => ['administrador'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST', 'GET'],
                ],
            ],
        ];
    }

    /**
     * Lists all Asignaturas models.
     * @return mixed
     */
    public function actionIndex()
    {
        $docente = Yii::$app->user->identity->id;
        $rolesUsuario = Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id);
        $esAdministrador = false;
    
        if (!array_key_exists('administrador', $rolesUsuario)) {
            $searchModel = new \app\models\AsignaturasDocentesSearch();
            $searchModel->usuarios_id = $docente;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        } else {
            $esAdministrador = true;
            $searchModel = new \app\models\AsignaturasSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }
    
        // Configurar la paginación para mostrar 5 elementos por página
        $dataProvider->pagination->pageSize = 5;
    
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'esAdministrador' => $esAdministrador,
        ]);
    }
    
    

    public function actionAsignaturasAlumnos() {
        $usuario = Yii::$app->user->identity->id;
        $searchModel = new \app\models\AsignaturasAlumnosSearch();
        $searchModel->usuarios_id = $usuario;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('asignaturas-alumnos', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Asignaturas model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        $usuario = Yii::$app->user->identity->id;
        $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
        $id = Yii::$app->security->decryptByPassword($id, $oUser->password);

        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Asignaturas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Asignaturas();
        $userid = Yii::$app->user->identity->id;
        $oUser = \app\models\Usuarios::findOne(['id' => $userid]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => Yii::$app->security->encryptByPassword($model->id, $oUser->password)]);
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing Asignaturas model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $usuario = Yii::$app->user->identity->id;
        $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
        $id = Yii::$app->security->decryptByPassword($id, $oUser->password);

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Asignaturas model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $usuario = Yii::$app->user->identity->id;
        $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
        $id = Yii::$app->security->decryptByPassword($id, $oUser->password);

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Asignaturas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Asignaturas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Asignaturas::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
