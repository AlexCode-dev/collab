<?php

namespace app\controllers;

use Yii;
use app\models\Grupos;
use app\models\GruposSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * GruposController implements the CRUD actions for Grupos model.
 */
class GruposController extends Controller {

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
     * Lists all Grupos models.
     * @return mixed
     */
    public function actionIndex($asigid) {
        $usuario = Yii::$app->user->identity->id;
        $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
        $asigid = Yii::$app->security->decryptByPassword($asigid, $oUser->password);

        $searchModel = new GruposSearch();
        $searchModel->asignaturas_id = $asigid;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'asigid' => $asigid,
        ]);
    }

    /**
     * Displays a single Grupos model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    function getEstilo($estilo) {
        $nestilo = '';
        switch (substr(trim($estilo), 0, 3)) {
            case 'ACT':
                $nestilo = 'activo';
                break;
            case 'REF':
                $nestilo = 'reflexivo';
                break;
            case 'NAR':
                // Neutral Activo Reflexivo
                $nestilo = 'neutral-ar';
                break;
            case 'SEN':
                $nestilo = 'sensitivo';
                break;
            case 'INT':
                $nestilo = 'intuitivo';
                break;
            case 'NSI':
                // Neutral Sensitivo Intuitivo
                $nestilo = 'neutral-is';
                break;
            case 'VIS':
                $nestilo = 'visual';
                break;
            case 'VER':
                $nestilo = 'verbal';
                break;
            case 'NVV':
                // Neutral Visual Verbal
                $nestilo = 'neutral-vv';
                break;
            case 'SEC':
                $nestilo = 'secuencial';
                break;
            case 'GLO':
                $nestilo = 'global';
                break;
            case 'NSG':
                //Neutral Secuencial Global
                $nestilo = 'neutral-sg';
                break;
        }
        return $nestilo;
    }

    /**
     * Creates a new Grupos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($asigid) {
        $usuario = Yii::$app->user->identity->id;
        $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
        $asigid = Yii::$app->security->decryptByPassword($asigid, $oUser->password);

        $model = new Grupos();
        $model->asignaturas_id = $asigid;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if ($model->metodos_formacion_id == 1) {
                $grupos = json_decode($model->alumnosPorGrupo);
                foreach ($grupos as $id => $alumnosGrupo) {
                    $objGrupo = new \app\models\GruposFormados();
                    $objGrupo->nombre = "Grupo " . ($id + 1);
                    $objGrupo->grupos_id = $model->id;
                    $objGrupo->save();
                    foreach ($alumnosGrupo as $indice => $miembro) {
                        $objAlumnoGrupo = new \app\models\GruposAlumnos();
                        $objAlumnoGrupo->usuarios_id = $miembro;
                        $objAlumnoGrupo->grupos_formados_id = $objGrupo->id;
                        $objAlumnoGrupo->save();
                    }
                }
            } elseif ($model->metodos_formacion_id == 2) {
                // Invocar al algoritmo genetico
                $alumnosporyear = \app\models\AsignaturasAlumnos::getListaAlumnosPorYear($model->year, $model->asignaturas_id);
                $alumnos = array();
                $grupos = array();
                foreach ($alumnosporyear as $alumnoInscripto) {
                    list($e1, $e2, $e3, $e4) = explode('-', $alumnoInscripto['estiloaprendizaje']);
                    $estilo = $this->getEstilo($e1) . "," . $this->getEstilo($e2);
                    $estilo .= "," . $this->getEstilo($e3) . "," . $this->getEstilo($e4);
                    $alumnos[] = array('nombre' => $alumnoInscripto['usuarios_id'], 'ea' => $estilo);
                }

                $grupos = $model->optimizarAG($alumnos, $model->cantidadintegrantes);
                $cont = 1;
                foreach ($grupos["grupos"] as $grupo) {
                    $objGrupo = new \app\models\GruposFormados();
                    $objGrupo->nombre = "Grupo $cont";
                    $objGrupo->grupos_id = $model->id;
                    $objGrupo->save();
                    foreach ($grupo as $miembro) {
                        $objAlumnoGrupo = new \app\models\GruposAlumnos();
                        $objAlumnoGrupo->usuarios_id = $alumnos[$miembro]["nombre"];
                        $objAlumnoGrupo->grupos_formados_id = $objGrupo->id;
                        $objAlumnoGrupo->save();
                    }
                    $cont += 1;
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
                    'model' => $model,
                    'asigid' => $asigid,
        ]);
    }

    /**
     * Updates an existing Grupos model.
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
     * Deletes an existing Grupos model.
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
     * Finds the Grupos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Grupos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Grupos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
