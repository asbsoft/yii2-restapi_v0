<?php

namespace asb\yii2\modules\restapi_v0\controllers;

use asb\yii2\modules\restapi_v0\models\User;
use asb\yii2\modules\restapi_v0\controllers\user\CreateAction;

use Yii;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

/**
 * @author    Alexandr Belogolovsky <ab2014box@gmail.com>
 */
class UserController extends ActiveController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        //parent::init(); // throw exception here
        $this->modelClass = User::className();
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions(); // not support

        // index, create only
        unset($actions['delete']
          , $actions['update']
          , $actions['view']
          , $actions['options']
        );

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        $actions['create'] = [
            'class' => CreateAction::className(),
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
            'scenario' => $this->createScenario,
        ];

        return $actions;
    }

    /**
     * @inheritdoc
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        switch ($action) {
            case 'index':
            case 'create':
                break;
            default:
                throw new ForbiddenHttpException('Unknown action');
        }
    }

    /**
     * @inheritdoc
     */
    public function prepareDataProvider()
    {
        $params = Yii::$app->request->queryParams;
        if (empty($params['login']) && empty($params['password'])) return null;

        $queryBase = User::find()
            ->where([
                'login_email' => $params['login'],
                'password_hash' => User::getPasswordHash($params['password'], $params['login']),
            ]);

        // update token if user exists:
        $user = $queryBase->one();
        if (empty($user)) {
            throw new NotFoundHttpException('User with such login and password not found');
        } else {
            $expiredTime = $this->module->params['tokenExpiredPeriod'];
            $user->token_expired = new Expression(sprintf('DATE_ADD(NOW(), INTERVAL %d SECOND)', $expiredTime));
            $user->access_token = $user->generateToken($user->password_hash);
            $user->scenario = User::SCENARIO_SAVE;
            $user->save();
        }

        $query = $queryBase->select(['id', 'access_token', 'token_expired']); // don't return all users info
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }
}
