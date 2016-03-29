<?php
/**
 * @author    Alexandr Belogolovsky <ab2014box@gmail.com>
 * @copyright Copyright (c) 2016, Alexandr Belogolovsky
 */

namespace asb\yii2\modules\restapi_v0;

use Yii;
use yii\base\Module as BaseModule;
use yii\helpers\ArrayHelper;

use asb\yii2\modules\restapi_v0\models\UserIdentity;

class Module extends BaseModule
{
    public function init()
    {
        parent::init();

        $params = include(__DIR__ . '/config/params.php');
        $this->params = ArrayHelper::merge($params, $this->params);

        Yii::$app->user->enableSession = false;
        Yii::$app->user->identityClass = UserIdentity::className();

    }
}
