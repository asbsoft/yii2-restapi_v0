<?php
namespace asb\yii2\modules\restapi_v0\models;

use Yii;

use asb\yii2\modules\restapi_v0\Module;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $login_email
 * @property string $password_hash
 * @property string $auth_key
 * @property string $access_token
 * @property string $token_expired
 * @property string $create_time
 * @property string $update_time
 */
class User extends \yii\db\ActiveRecord
{
    const TABLE_NAME = 'user';

    const SCENARIO_SAVE = 'save';

    public $captcha_code;
    public $password;

    public $captchaAction;

    public function init()
    {
        parent::init();

        $module = Module::getInstance();
        $mid = empty($module) ? null : $module->uniqueId;
        if (empty($mid)) { // in migration
            foreach(Yii::$app->modules as $mid => $module) {
                if ($module instanceof Module) {
                    break;
                }
            }
            if (empty($mid)) throw new \Exception('Not found in configuration: module ' . Module::className());
        }
        $this->captchaAction = $mid . '/frontend/captcha';
    }
    
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
            ['login_email', 'email'],
            [['login_email', 'password'], 'string', 'min' => 6, 'max' => 25],
            ['login_email', 'required'],
            ['password', 'required', 'except' => self::SCENARIO_SAVE],
            [['auth_key', 'access_token', 'token_expired', 'create_time'], 'required', 'on' => self::SCENARIO_SAVE],

            [['login_email'], 'unique',
                'on' => self::SCENARIO_SAVE,
                'message' => 'Such login already exists or you forgot password',
            ],
            [['auth_key', 'access_token'], 'unique'],
            ['update_time', 'safe'],
            ['captcha_code', 'captcha',
                'skipOnEmpty' => false,
                'caseSensitive' => false,
                'captchaAction' => $this->captchaAction,
                'except' => self::SCENARIO_SAVE,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'login_email'   => 'Login (e-mail)',
            'password_hash' => 'Password',
            'auth_key'      => 'Auth Key',
            'access_token'  => 'Access Token',
            'token_expired' => 'Token Expired',
            'create_time'   => 'Create Time',
            'update_time'   => 'Update Time',
        ];
    }

    /**
     * Generate password hash
     * @param string $password
     * @param string $salt
     * @return string password hash
     */
    public static function getPasswordHash($password, $salt = null)
    {
        return md5(md5($password) . md5($salt)); //toDo
    }

    /**
     * Generate token
     * @param string $salt
     * @return string token
     */
    public function generateToken($salt = null)
    {
        return md5(md5(time()) . md5($salt));
    }

    /**
     * Generate auth key
     * @param string $salt
     * @return string auth key
     */
    public function generateAuthKey($salt = null)
    {
        return md5(time()) . md5($salt);
    }

}
