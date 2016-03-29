<?php
namespace asb\yii2\modules\restapi_v0\assets;

use yii\web\AssetBundle;
use yii\web\View;

class FrontAsset extends AssetBundle
{
    //public $css = [];
    //public $js = [];
    //public $jsOptions = ['position' => View::POS_BEGIN];

    public function init()
    {
        parent::init();
        $this->sourcePath = __DIR__ . '/front';
    }

    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
