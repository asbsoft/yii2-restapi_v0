<?php

namespace asb\yii2\modules\restapi_v0\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%post}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $text
 * @property string $create_time
 * @property string $update_time
 */
class Post extends ActiveRecord
{
    const TABLE_NAME = 'test_restapi_post';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%' . self::TABLE_NAME . '}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['text'], 'string'],
            [['user_id', 'text'], 'required'],
            [['create_time', 'update_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'text' => 'Text',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
