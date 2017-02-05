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
    public $moduleUid;

    public function bootstrap($app)
    {
        $paramsHere = include(__DIR__ . '/config/params.php');//var_dump($paramsHere);
        $params = [];

        if (empty($this->moduleUid)) {
            // find only in 1st-level submodules of application:
            foreach($app->modules as $moduleId => $module) {//echo"<br>{$moduleId}:";if($module instanceof \yii\base\Module)echo"{$module->uniqueId}";else var_dump($module);
                if (is_array($module) && isset($module['class']) && $module['class'] == Module::className()) {
                    $params = $module['params'];
                    $this->moduleUid = $moduleId; //??
                    break;
                } else if ($module instanceof Module) {
                    $params = $module->params;
                    $this->moduleUid = $module->module->uniqueId . '/' . $moduleId;
                    break;
                }
            }
        } else {
            $module = $app->getModule($this->moduleUid);
            $params = $module->params;
        }
        $params = ArrayHelper::merge($paramsHere, $params);//var_dump($params);

        $this->urlPrefix = empty($params['urlPrefix']) ? $this->urlPrefix : $params['urlPrefix'];

        if (empty($this->moduleUid)) {
            $message = "Not found 'moduleUid' in configuration of module " . Module::className();//
            //throw new \Exception($message);
            echo "{$message}<br>";
            Yii::error($message);
            return;
        }

        if (!empty($params['changeStartPage']) && $params['changeStartPage']) {
            $app->defaultRoute = $this->moduleUid . '/frontend/auth';
        }//var_dump($app->defaultRoute);

        $app->urlManager->enablePrettyUrl = true;
        $urlPrefix = $this->urlPrefix;
        $moduleUid = $this->moduleUid;
        $routes = include(__DIR__ . '/config/routes-front.php'); // file use vars $urlPrefix, $moduleUid
        $app->urlManager->addRules($routes, false);//var_dump($app->urlManager->rules);

        $app->request->parsers = ArrayHelper::merge(
            $app->request->parsers
          , ['application/json' => 'yii\web\JsonParser']
        );

    }

}
