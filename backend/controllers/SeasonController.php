<?php

namespace backend\controllers;

use Yii;
use common\models\Season;
use common\models\SeasonSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\db\Query;

/**
 * SeasonController implements the CRUD actions for Season model.
 */
class SeasonController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->enableCsrfValidation = false;
        if (!empty(Yii::$app->user) && !Yii::$app->user->can("admin")) {
            throw new \yii\web\ForbiddenHttpException('Вы не можете выполнить это действие.');
        }

        parent::init();
    }

    /**
     * Lists all Season models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SeasonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Season model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Season model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Season();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if(strpos($model->name, '/') !== false) {
                $model->window = 1;
            }

            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Season model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    
    /**
     * Display list of arbiters in json format
     *
     * @param string $q Query for search
     * @return mixed Json data
     */
    public function actionSeasonList($query = null) {
        if($query == null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $search = urldecode($query);
        $query = new Query;
        $query->select('id as value, name as text')
            ->from(Season::tableName())
            ->where(['like', 'name', $search])
            ->orderBy('name')
            ->limit(10);
        $command = $query->createCommand();
        $data = $command->queryAll();
        $out = array_values($data);
        header("Content-type: text/html; charset=utf-8");
        echo Json::encode($out);
    }

    /**
     * Deletes an existing Season model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Season model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Season the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Season::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
