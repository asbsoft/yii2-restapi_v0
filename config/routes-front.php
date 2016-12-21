<?php
/**
 * @var $urlPrefix string
 * @var $moduleUid string 
 */
return [
/*
    'front' => [
        'class'   => 'yii\web\UrlRule',
        'pattern' => $urlPrefix . '/<action:(auth|captcha)>',
        'route'   => $moduleUid . '/frontend/<action>',
    ],
*/
    "{$urlPrefix}/<action:(auth|captcha)>" => "{$moduleUid}/frontend/<action>",
    "{$urlPrefix}/?" => "{$moduleUid}/frontend/auth",

    'posts' => [
        'class' => 'yii\rest\UrlRule',
        'controller' => ['posts' => $moduleUid . '/post'],
        'prefix' => $urlPrefix,
        //'suffix' => '.json',
    ],
    'users' => [
        'class' => 'yii\rest\UrlRule',
        'controller' => ['users' => $moduleUid . '/user'],
        'prefix' => $urlPrefix,
        //'suffix' => '.json',
    ],
];
