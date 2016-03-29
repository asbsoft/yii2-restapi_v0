<?php
/**
 * @author    Alexandr Belogolovsky <ab2014box@gmail.com>
 * @copyright Copyright (c) 2016, Alexandr Belogolovsky
 */

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model asb\yii2\modules\restapi_v0\models\User */
/* @var $params array */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

use asb\yii2\modules\restapi_v0\assets\FrontAsset;

$assets = FrontAsset::register($this);

$patternId = '0000000000';

$this->title = 'Simple register/login with get token';
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
    'url' => [$this->context->action->id],
];

?>
<div class="front-auth">

    <?php if (empty($model->id)): ?>
        <h1><?= Html::encode($this->title) ?></h1>
    <?php else: ?>
        <h1>Simple REST API tests</h1>
    <?php endif; ?>

    <?php if (!empty($params['message'])): ?>
        <p class="alert-success"><?= $params['message'] ?></p>
    <?php endif; ?>
    
    <?php if (empty($model->id)): ?>
        <div class="row">
            <div class="col-lg-5">

                <?php $form = ActiveForm::begin([
                    'id' => 'auth-form',
                    'enableClientValidation' => false,
                    'enableClientScript' => false,
                ]); ?>
                    <?= $form->field($model, 'login_email') ?>
                    <?= $form->field($model, 'password')->passwordInput() ?>

                    <?= $form->field($model, 'captcha_code')->widget(Captcha::className(), [
                        'template' => include(__DIR__ . '/captcha.php'),
                        'captchaAction' => 'captcha',
                        'imageOptions' => [
                            'id' => 'auth-captcha',
                            'title' => 'Click to refresh code',
                        ],
                    ]) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Get', ['class' => 'btn btn-primary', 'name' => 'auth-submit']) ?>
                    </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    <?php else: ?>
        <?php if (!empty($params['error'])): ?>
            <p class="alert-danger"><?= $params['error'] ?></p>
        <?php else: ?>

            <img id="progress" src="<?= $assets->baseUrl ?>/img/wait.gif"
                 style="display: none; position: fixed; top: 45%; left: 45%" />
            
            <p>Your ID: <?= $model->id ?></p>
            <p>
                Token: <?= $model->access_token ?> <br />
                expired in <span id="expired-period"><?= $this->context->module->params['tokenExpiredPeriod'] ?></span> seconds
                <br />
                <span id="expired-time" data-period="<?= $this->context->module->params['tokenExpiredPeriod'] ?>"></span>
            </p>
            <br />
            <p>
                Your posts list (first page) show <?= Html::a('here', [
                        "/{$this->context->module->uniqueId}/post/index",
                        'access-token' => $model->access_token,
                        'page' => 1,
                    ], ['target' => '_blank']) ?>
                (or same in <?= Html::a('JSON', [
                        "/{$this->context->module->uniqueId}/post/index",
                        'access-token' => $model->access_token,
                        'page' => 1,
                    ], ['id' => 'list-json-button']) ?>)
            </p>
            <p>
                Page # <?php $page = 2; ?>
                <?= Html::textInput('page', $page, [
                    'id' => 'page-number',
                    'class' => 'form-control',
                    'style' => 'width:10%;display:inline',
                ]) ?>
                <?= Html::a('show in JSON', [
                        "/{$this->context->module->uniqueId}/post/index",
                        'access-token' => $model->access_token,
                    ], ['id' => 'list-page-json-button']) ?>
                (default sorting by creation time descend)
            </p>
            <br />
            <p>
                <?= Html::beginForm([
                        "/{$this->context->module->uniqueId}/post/index", // not 'post/create'
                        'access-token' => $model->access_token,
                    ], 'post', [
                        'id' => 'create-form',
                        'target' => '_blank',
                    ]) ?>
                    Create new post with text
                    <?= Html::textarea('text', '', [
                        'class' => 'form-control',
                    ]) ?>
                    <?= Html::submitButton('create', ['class' => 'btn btn-success']) ?>
                <?= Html::endForm() ?>
            </p>
            <br />
            <p>
                Post ID
                <?= Html::textInput('id', '', [
                        'id' => 'post-id',
                        'class' => 'form-control',
                        'style' => 'width:10%;display:inline',
                    ]) ?>
                <?= Html::hiddenInput('view-post-url-pattern', Url::toRoute([
                        "/{$this->context->module->uniqueId}/post/view",
                        'access-token' => $model->access_token,
                        'id' => $patternId,
                    ]), [
                        'id' => 'view-post-url-pattern',
                    ]) ?>
                <?= Html::a('view', '#', [
                        'id' => 'view-button',
                        //'class' => 'btn btn-default',
                        'target' => '_blank',
                    ]) ?>
                <?= Html::a('view JSON', '#', [
                        'id' => 'view-json-button',
                        'class' => 'btn btn-default',
                        'target' => '_blank',
                    ]) ?>
                <?= Html::a('delete', '#', [
                        'id' => 'delete-json-button',
                        'class' => 'btn btn-danger',
                        'target' => '_blank',
                    ]) ?>
                <?= Html::a('edit', '#', [
                        'id' => 'load-form-button',
                        'class' => 'btn btn-info',
                    ]) ?>
                <?= Html::a('C', '#', [
                        'id' => 'clean-form-button',
                        'title' => 'Clean # and hide edit form',
                        'class' => 'btn btn-warning',
                        'style' => 'display: none',
                    ]) ?>
            </p>
            <p>
                <?= Html::beginForm([
                        "/{$this->context->module->uniqueId}/post/view", // not 'post/update'
                        'access-token' => $model->access_token,
                        'id' => $patternId,
                    ], 'post', [
                        'id' => 'edit-form',
                        'target' => '_blank',
                        'style' => 'display:none',
                    ]) ?>
                    Edit post #<span id="post-id-edit"></span> with text
                    <?= Html::textarea('text', '', [
                        'id' => 'text-edit',
                        'class' => 'form-control',
                    ]) ?>
                    <?= Html::submitButton('save', ['class' => 'btn btn-info']) ?>
                <?= Html::endForm() ?>
            </p>
        <?php endif; ?>

    <?php endif; ?>

</div>

<?php include __DIR__ . '/auth.js.php' ?>
