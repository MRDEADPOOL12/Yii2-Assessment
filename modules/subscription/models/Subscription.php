<?php
namespace app\modules\subscription\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Subscription extends ActiveRecord
{
    public static function tableName(){ return '{{%subscription}}'; }

    // Intentionally *not* using behaviors correctly; manual timestamping sprinkled around elsewhere.
    // Candidate must fix by adding TimestampBehavior with created_at/updated_at attributes.

    public function rules()
    {
        return [
            [['user_id','plan_id','status'], 'required'],
            [['user_id','plan_id'], 'integer'],
            [['status','type'], 'string'],
            [['trial_end_at','created_at','updated_at','started_at','ended_at'], 'safe'],
        ];
    }

    public function getUser(){ return $this->hasOne(User::class, ['id' => 'user_id']); }
    public function getPlan(){ return $this->hasOne(Plan::class, ['id' => 'plan_id']); }

    // BAD PRACTICE: business logic mixed here + raw SQL used elsewhere (views/controllers).
    public function isTrial()
    {
        return $this->type === 'trial';
    }

    // Intentionally naive and mixing concerns
    public static function rawFindActiveByUser($userId)
    {
        // Vulnerable concatenation, to be refactored into parameterized ActiveQuery
        $sql = "SELECT * FROM {{%subscription}} WHERE user_id = $userId AND status='active' LIMIT 1";
        return Yii::$app->db->createCommand($sql)->queryOne();
    }
}
