<?php
/**
 * @author    Alexandr Belogolovsky <ab2014box@gmail.com>
 * @copyright Copyright (c) 2016, Alexandr Belogolovsky
 */

namespace asb\yii2\modules\restapi_v0\controllers\user;

use Yii;
use yii\db\Expression;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\rest\CreateAction as RestCreateAction;

use asb\yii2\modules\restapi_v0\models\User;

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

        $params = Yii::$app->getRequest()->getBodyParams();
        if (empty($params['login']))    throw new BadRequestHttpException('Login required');
        if (empty($params['password'])) throw new BadRequestHttpException('Password required');

        $password_hash = User::getPasswordHash($params['password'], $params['login']);

        $model = new $this->modelClass();
        $result = $model::find()
            ->where([
                'login_email' => $params['login'],
            ])->one();
        if (empty($result)) {
            $model->login_email = $params['login'];
            $model->password = $params['password']; // for validation
            $model->password_hash = $password_hash;
            $model->auth_key = $model->generateAuthKey($password_hash);
            $model->create_time = new Expression('NOW()');
            $expiredTime = $this->controller->module->params['tokenExpiredPeriod'];
            $model->token_expired = new Expression(sprintf('DATE_ADD(NOW(), INTERVAL %d SECOND)', $expiredTime));
            $model->access_token = $model->generateToken($model->password_hash);
            $model->scenario = User::SCENARIO_SAVE;
        } else {
            throw new ForbiddenHttpException('Login already exists');
        }

        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));
        } elseif ($model->hasErrors()) {
            return $model;
        } else {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        $model = $model::find()
            ->select(['id', 'access_token', 'token_expired']) // don't return all users info
            ->where([
                'login_email' => $params['login'],
                'password_hash' => $password_hash,
            ])->one();
        return $model;
    }
}
