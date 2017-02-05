<?php
/**
 * @author    Alexandr Belogolovsky <ab2014box@gmail.com>
 * @copyright Copyright (c) 2016, Alexandr Belogolovsky
 */

namespace asb\yii2\modules\restapi_v0\controllers;

use Yii;
use yii\db\Expression;

use asb\yii2\modules\restapi_v0\models\User;

class FrontendController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'testLimit' => 2, // how many times should the same CAPTCHA be displayed
                'minLength' => 3,
                'maxLength' => 5,
                //'fixedVerifyCode' => '123',
            ],
        ];
    }

    public function actionAuth()
    {
        $model = new User();
        $params = [];

        if ($model->load(Yii::$app->request->post()) && $model->validate(['login_email', 'password', 'captcha_code'])) {
            $login = $model->login_email;
            $passwordHash = User::getPasswordHash($model->password, $model->login_email);
            $exists = $model->findOne([
                'login_email' => $model->login_email,
                'password_hash' => $passwordHash,
            ]);

            if (!empty($exists)) { // only generate token
                $model = $exists;
                $model->access_token = $model->generateToken($model->password_hash);
                $model->token_expired = new Expression(sprintf(
                    'DATE_ADD(NOW(), INTERVAL %d SECOND)', $this->module->params['tokenExpiredPeriod']
                ));

                $model->scenario = User::SCENARIO_SAVE;
                $ok = $model->save(false, ['access_token', 'token_expired']);
                if ($ok) {
                    $params['message'] = 'New token for existing user has been generated';
                } else {
                    $params['error'] = 'Internal error, try again';
                }
            } else { // create new login and generate token
                $model->login_email = $login;
                $model->password_hash = $passwordHash;
                $model->auth_key = $model->generateAuthKey($passwordHash);
                $model->create_time = new Expression('NOW()');

                $model->access_token = $model->generateToken();
                $model->token_expired = new Expression(sprintf(
                    'DATE_ADD(NOW(), INTERVAL %d SECOND)', $this->module->params['tokenExpiredPeriod']
                ));
                $model->scenario = 'save';
                $ok = $model->save();
                if ($ok) {
                    $params['message'] = 'New user has been created and generated token for this user';
                } else {
                    $params['error'] = 'Internal error, try again';
                }
            }
        }

        return $this->render('auth', [
            'model' => $model,
            'params' => $params,
        ]);
    }

}
