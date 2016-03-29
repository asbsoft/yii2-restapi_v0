<?php
/**
 * @author    Alexandr Belogolovsky <ab2014box@gmail.com>
 * @copyright Copyright (c) 2016, Alexandr Belogolovsky
 */

use yii\db\Schema;
use yii\db\Migration;
use yii\db\Expression;

class m160319_090245_restapi_v0_tables extends Migration
{
    protected $tableUser = '{{%user}}';
    protected $idxUser   = 'idx-user';

    protected $tablePost = '{{%post}}';
    protected $idxPost   = 'idx-post';

    public function safeUp()
    {
        $tableOptions = null;
        $now = new Expression('NOW()');

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
