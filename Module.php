<?php

namespace asb\yii2\modules\restapi_v0;

use asb\yii2\modules\restapi_v0\models\UserIdentity;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;
use yii\helpers\ArrayHelper;

/**
 * @author Alexandr Belogolovsky <ab2014box@gmail.com>
 */
class Module extends BaseModule implements BootstrapInterface
{
    public $urlPrefix = 'restapi-v0'; // default

    public $user;
    
    public function init()
    {
        parent::init();

        $params = include(__DIR__ . '/config/params.php');
        $this->params = ArrayHelper::merge($params, $this->params);

        $this->user = clone Yii::$app->user;
        $this->user->enableSession = false;
        $this->user->identityClass = UserIdentity::className();
    }

    public function bootstrap($app)
    {
        $moduleUid = (empty($this->module->uniqueId) ? '' : ($this->module->uniqueId . '/')) . $this->id;

        $paramsHere = include(__DIR__ . '/config/params.php'); // default
        $this->params = ArrayHelper::merge($paramsHere, $this->params);

        if (!empty($this->params['changeStartPage']) && $this->params['changeStartPage']) {
            $app->defaultRoute = $moduleUid . '/frontend/auth';
        }

        $this->urlPrefix = empty($this->params['urlPrefix']) ? $this->urlPrefix : $this->params['urlPrefix'];

        $app->urlManager->enablePrettyUrl = true;
        $urlPrefix = $this->urlPrefix;
        $routes = include(__DIR__ . '/config/routes-front.php'); // file use vars $urlPrefix, $moduleUid
        $app->urlManager->addRules($routes, false);

        $app->request->parsers = ArrayHelper::merge(
            $app->request->parsers
          , ['application/json' => 'yii\web\JsonParser']
        );

    }

}
