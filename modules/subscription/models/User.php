<?php
namespace app\modules\subscription\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName(){ return '{{%user}}'; }

    public static function findIdentity($id){ return static::findOne($id); }
    public static function findIdentityByAccessToken($token, $type = null){ return null; }
    public function getId(){ return $this->id; }
    public function getAuthKey(){ return $this->auth_key ?? null; }
    public function validateAuthKey($authKey){ return $this->auth_key === $authKey; }
}
