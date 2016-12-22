<?php
/**
 * @copyright Copyright (c) 2016, Alexandr Belogolovsky
 */

namespace asb\yii2\modules\restapi_v0;

use Yii;
use yii\base\Module as BaseModule;
use yii\helpers\ArrayHelper;

use asb\yii2\modules\restapi_v0\models\UserIdentity;

/**
 * @author Alexandr Belogolovsky <ab2014box@gmail.com>
 */
class Module extends BaseModule
{
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
}
