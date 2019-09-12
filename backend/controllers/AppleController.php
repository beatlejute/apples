<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use backend\models\apple;
use yii\helpers\Json;

/**
 * Apple controller
 */
class AppleController extends Controller {
    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays page.
     *
     * @return string
     */
    public function actionIndex() {
        return $this->render('index', [
            "apples" => Apple::getAll()
        ]);
    }

    /**
     * Сгенерировать несколько яблок
     */
    public function actionGenerate() {
        if (Yii::$app->request->isAjax) {
            for ($i = 0; $i < rand(1, 5); $i++) {
                $apple = new Apple();
                $apple->save();
            }
        }
    }

    /**
     * Скинуть яблоко
     *
     * @return string
     */
    public function actionFall() {
        if (Yii::$app->request->isAjax) {
            $id = (int)Yii::$app->request->get("id");

            $apple = Apple::get($id);

            if ($apple->isOnTree()) {
                $apple->fall();
                return Json::encode([
                    "fallen" => $apple->{Apple::FIELD_DATE_FALL}
                ]);
            } else {
                return Json::encode([
                    "errorMessage" => "Яблоко уже упало!"
                ]);
            }
        }
    }

    /**
     * Укусить яблоко
     *
     * @return string
     */
    public function actionBite() {
        if (Yii::$app->request->isAjax) {
            $id = (int)Yii::$app->request->get("id");

            $apple = Apple::get($id);

            if (!$apple) {
                return Json::encode([
                    "errorMessage" => "Яблока уже нет!"
                ]);
            }
            if ($apple->isOnTree()) {
                return Json::encode([
                    "errorMessage" => "Яблоко еще не упало!"
                ]);
            }

            if ($apple->isRotten()) {
                return Json::encode([
                    "errorMessage" => "Яблока уже сгнирло!"
                ]);
            }

            $percent = $apple->bite();
            return Json::encode([
                "percent" => $percent
            ]);
        }
    }

    /**
     * Выкинуть яблоко
     *
     * @return string
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionRemove() {
        if (Yii::$app->request->isAjax) {
            $id = (int) Yii::$app->request->get("id");

            $apple = Apple::get($id);

            $apple->delete();

            return Json::encode([
                "remove" => true
            ]);
        }
    }
}
