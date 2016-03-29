<?php
/**
 * @author    Alexandr Belogolovsky <ab2014box@gmail.com>
 * @copyright Copyright (c) 2016, Alexandr Belogolovsky
 */

namespace asb\yii2\modules\restapi_v0;

use Yii;
use yii\base\BootstrapInterface;
use yii\helpers\ArrayHelper;

use asb\yii2\modules\restapi_v0\Module;
use asb\yii2\modules\restapi_v0\models\User;

class Bootstrap implements BootstrapInterface
{
    public $urlPrefix = 'restapi-v0'; // default
    public $moduleId;

    public function bootstrap($app)
    {
        $paramsHere = include(__DIR__ . '/config/params.php');
        $params = [];
        foreach($app->modules as $moduleId => $module) {
            if (is_array($module) && isset($module['class']) && $module['class'] == Module::className()) {
                $params = $module['params'];
                $this->moduleId = $moduleId;
                break;
            } else if ($module instanceof Module) {
                $params = $module->params;
                $this->moduleId = $moduleId;
                break;
            }
        }
        if (empty($this->moduleId)) throw new \Exception('Not found in configuration: module ' . Module::className());
        $params = ArrayHelper::merge($paramsHere, $params);
        $this->urlPrefix = empty($params['urlPrefix']) ? $this->urlPrefix : $params['urlPrefix'];

        if (!empty($params['changeStartPage']) && $params['changeStartPage']) {
            Yii::$app->defaultRoute = $this->moduleId . '/frontend/auth';
        }

        Yii::$app->urlManager->enablePrettyUrl = true;

        $urlPrefix = $this->urlPrefix;
        $moduleId  = $this->moduleId;
        $routes = include(__DIR__ . '/config/routes-front.php');
        Yii::$app->urlManager->addRules($routes, false);
        //var_dump(Yii::$app->urlManager->rules[2]->rules['posts']);

        Yii::$app->request->parsers = ArrayHelper::merge(
            Yii::$app->request->parsers
          , ['application/json' => 'yii\web\JsonParser']
        );

    }

}
