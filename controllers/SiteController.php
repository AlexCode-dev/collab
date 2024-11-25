<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\RecuperarPasswordForm;

class SiteController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
                    'model' => $model,
        ]);
    }

    public function actionInstalar() {
        $roles = $query = (new \yii\db\Query())
                        ->select(['name', 'type'])
                        ->from('auth_item')->all();

        if (count($roles) == 0) {
            $rbac = Yii::$app->authManager;

            $guest = $rbac->createRole("guest");
            $guest->description = "Usuario invitado";
            $rbac->add($guest);

            $administrador = $rbac->createRole("administrador");
            $administrador->description = "Administrador";
            $rbac->add($administrador);

            $profesor = $rbac->createRole("profesor");
            $profesor->description = "Profesor";
            $rbac->add($profesor);

            $estudiante = $rbac->createRole("estudiante");
            $estudiante->description = "Estudiante";
            $rbac->add($estudiante);

            $rbac->addChild($administrador, $profesor);
            $rbac->addChild($profesor, $estudiante);
            $rbac->addChild($estudiante, $guest);

            $usuario = new \app\models\Usuarios();
            $usuario->nombre = "Administrador";
            $usuario->apellido = "General";
            $usuario->username = "admin";
            $usuario->password = "123456";
            $usuario->tipo = 2;
            $usuario->save();

            $rbac->assign($administrador, $usuario->id);

            $mFormacionGrupos = new \app\models\MetodosFormacion();
            $mFormacionGrupos->descripcion = "Manual";
            $mFormacionGrupos->save();

            $mFormacionGrupos1 = new \app\models\MetodosFormacion();
            $mFormacionGrupos1->descripcion = "Algoritmo Genético";
            $mFormacionGrupos1->save();
        }

        return $this->render('instalar', [
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact() {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
                    'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout() {
        return $this->render('about');
    }

    public function actionRecuperarPassword() {
        $model = new RecuperarPasswordForm();
        $mensajeError = "";
        if ($model->load(Yii::$app->request->post())) {            //&& $model->aceptaterminos == 1
            // Se debe verificar que exista el nombre de usuario
            // Si existe se actualiza la contraseña
            $objUsuario = \app\models\Usuarios::findOne(['username' => $model->username]);
            if (isset($objUsuario)) {
                $objUsuario->password = $model->password;
                $objUsuario->save();
                return $this->redirect(['restablecimiento-exitoso']);
            } else {
                // Caso contrario se notifica el error.
                $mensajeError = $model->username . " no figura como usuario del sistema.";
            }
        }

        return $this->render('recuperar-password', [
                    'model' => $model,
                    'mensajeError' => $mensajeError,
        ]);
    }

    public function actionRestablecimientoExitoso() {
        return $this->render('restablecimiento-exitoso');
    }

}
