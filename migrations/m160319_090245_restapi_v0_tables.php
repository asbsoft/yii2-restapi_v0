<?php

use asb\yii2\modules\restapi_v0\models\User;
use asb\yii2\modules\restapi_v0\models\Post;

use yii\db\Schema;
use yii\db\Migration;

//Yii::setAlias('@asb/yii2/modules', '@vendor/asbsoft/yii2modules');

/**
 * @author Alexandr Belogolovsky <ab2014box@gmail.com>
 */
class m160319_090245_restapi_v0_tables extends Migration
{
    protected $tableUser;
    protected $idxUser;

    protected $tablePost;
    protected $idxPost;

    public function init()
    {
        parent::init();

        $this->tableUser = User::tableName();
        $this->idxUser   = 'idx-' . User::TABLE_NAME;

        $this->tablePost = Post::tableName();
        $this->idxPost   = 'idx-' . Post::TABLE_NAME;
    }

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->tableUser, [
            'id' => $this->primaryKey(),
            'login_email' => $this->string(25)->unique()->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'auth_key' => $this->string(255)->unique()->notNull(),
            'access_token' => $this->string(255)->unique()->notNull(),
            'token_expired' => $this->datetime()->notNull(),
            'create_time' => $this->datetime()->notNull(),
            'update_time' => $this->timestamp(),
        ], $tableOptions);
        $this->createIndex("{$this->idxUser}-login_email",   $this->tableUser, 'login_email');
        $this->createIndex("{$this->idxUser}-password_hash", $this->tableUser, 'password_hash');
        $this->createIndex("{$this->idxUser}-access_token",  $this->tableUser, 'access_token');

        $this->createTable($this->tablePost, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->defaultValue(0),
            'text' => $this->string(255)->notNull()->defaultValue(''),
            'create_time' => $this->datetime()->notNull(),
            'update_time' => $this->timestamp(),
        ], $tableOptions);
        $this->createIndex("{$this->idxPost}-user_id",  $this->tablePost, 'user_id');
    }

    public function safeDown()
    {
        //echo basename(__FILE__, '.php') . " cannot be reverted.\n";
        //return false;
        $this->dropTable($this->tablePost);
        $this->dropTable($this->tableUser);
    }

}
