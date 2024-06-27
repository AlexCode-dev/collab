<?php

namespace app\controllers;

use Yii;
use app\models\Grupos;
use app\models\GruposAlumnos;
use app\models\GruposFormados;
use app\models\GruposSearch;
use app\models\Usuarios;
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
                // Lógica para formar grupos manualmente
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
                // Invocar al algoritmo genético
                $alumnosporyear = \app\models\AsignaturasAlumnos::getListaAlumnosPorYear($model->year, $model->asignaturas_id);
                $alumnos = [];
                foreach ($alumnosporyear as $alumnoInscripto) {
                    $estilos = explode('-', $alumnoInscripto['estiloaprendizaje']);
                    // Asegurar que siempre haya 4 elementos en el array
                    while (count($estilos) < 4) {
                        $estilos[] = 'NSG'; // o cualquier valor predeterminado
                    }
                    list($e1, $e2, $e3, $e4) = $estilos;
                    $estilo = $this->getEstilo($e1) . "," . $this->getEstilo($e2);
                    $estilo .= "," . $this->getEstilo($e3) . "," . $this->getEstilo($e4);
                    $alumnos[] = ['nombre' => $alumnoInscripto['usuarios_id'], 'ea' => $estilo];
                }
    
                if (!empty($alumnos)) {
                    $grupos = $model->optimizarAG($alumnos, $model->cantidadintegrantes);
                    if ($grupos && !empty($grupos["grupos"])) {
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
                    } else {
                        Yii::$app->session->setFlash('error', 'No se pudieron formar grupos.');
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'No hay alumnos disponibles para formar grupos.');
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
        $usuario = Yii::$app->user->identity->id;
        $oUser = \app\models\Usuarios::findOne(['id' => $usuario]);
        $id = Yii::$app->security->decryptByPassword($id, $oUser->password);

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
    public function actionCambiarAlumno($alumno_id, $grupo_id, $view)
    {
        $alumno = Usuarios::findOne($alumno_id);
        $grupoActual = GruposFormados::findOne($grupo_id); // Encuentra el modelo del grupo actual
    
        if ($alumno && $grupoActual) {
            // Encuentra todos los grupos formados disponibles con el mismo grupos_id
            $gruposDisponibles = GruposFormados::find()->where(['grupos_id' => $grupoActual->grupos_id])->all();
    
            $dynamicModel = new \yii\base\DynamicModel(['nuevo_grupo_formado_id']);
            $dynamicModel->addRule(['nuevo_grupo_formado_id'], 'required');
    
            if (Yii::$app->request->isPost) {
                $dynamicModel->load(Yii::$app->request->post());
                if ($dynamicModel->validate()) {
                    $nuevoGrupoFormadoId = $dynamicModel->nuevo_grupo_formado_id;
    
                    // Encuentra el registro del alumno en la tabla GruposAlumnos
                    $grupoAlumno = GruposAlumnos::findOne(['usuarios_id' => $alumno_id, 'grupos_formados_id' => $grupo_id]);
                    if ($grupoAlumno) {
                        $grupoAlumno->grupos_formados_id = $nuevoGrupoFormadoId;
                        if ($grupoAlumno->save()) {
                            // Redirigir a la vista del grupo original después de guardar
                            return $this->redirect(['grupos/view', 'id' =>  $view]);
                        }
                    }
                }
            }
    
            return $this->render('cambiar-alumno', [
                'alumno' => $alumno,
                'gruposDisponibles' => $gruposDisponibles,
                'grupoActual' => $grupoActual,
                'dynamicModel' => $dynamicModel,
            ]);
        } else {
            throw new NotFoundHttpException('El alumno o grupo no existe.');
        }
    }
    
    
    


}
