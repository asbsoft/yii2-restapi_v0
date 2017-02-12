<?php

use asb\yii2\modules\restapi_v0\models\User;
use asb\yii2\modules\restapi_v0\models\Post;

use yii\db\Schema;
use yii\db\Migration;
use yii\db\Expression;

/**
 * @author Alexandr Belogolovsky <ab2014box@gmail.com>
 */
class m160319_090250_restapi_v0_test_data extends Migration
{
    protected $tableUser;
    protected $tablePost;

    public $users = [
        [ 'login' => 'tester@example.com', 'password' => 'test1234', 'posts' => 25 ],
        [ 'login' => 'user@example.com',   'password' => 'user1234', 'posts' => 15 ],
    ];

    public function init()
    {
        parent::init();

        Yii::setAlias('@asb/yii2/modules', '@vendor/asb/yii2modules');

        $this->tableUser = User::tableName();
        $this->tablePost = Post::tableName();
    }

    public function safeUp()
    {
        $userId = 10;  // start value
        $postId = 125; // start value for countdown
        $expiredTime = 600; //sec

        Yii::setAlias('@asb/yii2', '@vendor/asbsoft');
        $model = new User();
        $now = new Expression('NOW()');
        $tokenExpired = new Expression(sprintf('DATE_ADD(NOW(), INTERVAL %d SECOND)', $expiredTime));

        foreach ($this->users as $user) {
            $password_hash = User::getPasswordHash($user['password'], $user['login']);
            $this->insert($this->tableUser, [
                'id' => $userId,
                'login_email' => $user['login'],
                'password_hash' => $password_hash,
                'auth_key' => $model->generateAuthKey($password_hash),
                'access_token' =>  $model->generateToken($password_hash),
                'token_expired' => $tokenExpired,
                'create_time' => $now,
            ]);
            for ($i = 0; $i < $user['posts']; $i++) {
                $this->insert($this->tablePost, [
                    'id' => --$postId, // countdown to have different sort orders on id and on create_time
                    'user_id' => $userId,
                    'text' => "Some text #{$postId} from user #{$userId}...",
                    'create_time' => $now,
                ]);
                sleep(1);
            }
            $userId++;
        }
    }

    public function safeDown()
    {
        //echo basename(__FILE__, '.php') . " cannot be reverted.\n";
        //return false;
        $this->truncateTable($this->tablePost);
        $this->truncateTable($this->tableUser);
    }
}
