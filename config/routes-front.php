<?php
/**
 * @var $urlPrefix string
 * @var $moduleId string 
 */
return [
/*
    'front' => [
        'class'   => 'yii\web\UrlRule',
        'pattern' => $urlPrefix . '/<action:(auth|captcha)>',
        'route'   => $moduleId . '/frontend/<action>',
    ],
*/
    "{$urlPrefix}/<action:(auth|captcha)>" => "{$moduleId}/frontend/<action>",
    "{$urlPrefix}/?" => "{$moduleId}/frontend/auth",

    'posts' => [
        'class' => 'yii\rest\UrlRule',
        'controller' => ['posts' => $moduleId . '/post'],
        'prefix' => $urlPrefix,
        //'suffix' => '.json',
    ],
    'users' => [
        'class' => 'yii\rest\UrlRule',
        'controller' => ['users' => $moduleId . '/user'],
        'prefix' => $urlPrefix,
        //'suffix' => '.json',
    ],
];
