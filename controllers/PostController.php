<?php
/**
 * @author    Alexandr Belogolovsky <ab2014box@gmail.com>
 * @copyright Copyright (c) 2016, Alexandr Belogolovsky
 */

namespace asb\yii2\modules\restapi_v0\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use yii\web\ForbiddenHttpException;

use asb\yii2\modules\restapi_v0\models\UserIdentity;
use asb\yii2\modules\restapi_v0\models\Post;
use asb\yii2\modules\restapi_v0\models\PostSearch;

use asb\yii2\modules\restapi_v0\controllers\post\CreateAction;

class PostController extends ActiveController
{
    public $pageSize = 10; // default if not define in config

    /**
     * @inheritdoc
     */
    public function init()
    {
        //parent::init(); // throw exception here
        $this->modelClass = Post::className();
        parent::init();

        if (!empty($this->module->params['pageSize']) && intval($this->module->params['pageSize']) > 0) {
            $this->pageSize = intval($this->module->params['pageSize']);
        }
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();

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
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
        ];
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        $userId = Yii::$app->user->identity->id;

        switch ($action) {
            case 'index':
                //var_dump($model);//?? null
                break;
            case 'view':
            case 'create':
            case 'update':
            case 'delete':
                if (!empty($model->user_id) && $model->user_id != $userId) {
                    throw new ForbiddenHttpException('Alien post');
                }            
                break;
            default:
                throw new ForbiddenHttpException('Unknown action');
        }
    }

    /**
     * Get user id by access token.
     * @return integer|false user id or false if auth fail
     */
    public function getUserId()
    {
        $params = Yii::$app->request->queryParams;
        if (empty($params['access-token'])) return false;
        $userIdentity = new UserIdentity();
        $uid = $userIdentity->findIdentityByAccessToken($params['access-token']);
        if (empty($uid->id)) return false;
        else return $uid->id;
    }
    
    /**
     * @inheritdoc
     */
    public function prepareDataProvider()
    {
        $params = Yii::$app->request->queryParams;

        $userId = $this->getUserId();
        if (empty($userId)) return null;

        $modelSearch = new PostSearch();
        $params[$modelSearch->formName()] = [
            'user_id' => $userId, // required
        ];
        foreach (array_keys($modelSearch->attributes) as $field) {
            if (array_key_exists($field, $params)) {
                $params[$modelSearch->formName()][$field] = $params[$field];
            }
        }

        if (isset($params['sort'])) {
            if (empty($params['sort'])) {
                $params['sort'] = false; // false means unsorted
            } else {
                $sort = $params['sort'];
                $first = substr($sort, 0, 1);
                if (in_array($first, ['-', '+', ' '])) { // '%2B' -> '+', '+' -> ' '
                    $sortField = substr($sort, 1);
                    $sortDir = $first == '-' ? SORT_DESC : SORT_ASC;
                } else {
                    $sortField = $sort;
                    $sortDir = SORT_ASC;
                }
                if (array_key_exists($sortField, $modelSearch->attributes)) {
                    $params['sort'] = ['defaultOrder' => [$sortField => $sortDir]];
                } else {
                    unset($params['sort']);
                }
            }
        }

        $dataProvider = $modelSearch->search($params);

        $page = empty($params['page']) ? 1 : intval($params['page']);
        if ($page == 0) $page = 1;
        $pager = $dataProvider->getPagination();
        $pager->pageSize = $this->pageSize;
        $pager->totalCount = $dataProvider->getTotalCount();
        $pager->page = $page - 1; //! from 0

        return $dataProvider;
    }

}
