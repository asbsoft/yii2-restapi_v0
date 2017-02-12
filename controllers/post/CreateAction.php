<?php

namespace asb\yii2\modules\restapi_v0\controllers\post;

use Yii;
use yii\db\Expression;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;
use yii\rest\CreateAction as RestCreateAction;

/**
 * @author    Alexandr Belogolovsky <ab2014box@gmail.com>
 */
class CreateAction extends RestCreateAction
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass([
            'scenario' => $this->scenario,
        ]);

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        $userId = $this->controller->getUserId();
        if (empty($userId)) return null;
        $model->user_id = $userId;
        $model->create_time = new Expression('NOW()');

        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }
}
