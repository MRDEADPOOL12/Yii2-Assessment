<?php
namespace app\modules\subscription\models;

use yii\db\ActiveRecord;

class Plan extends ActiveRecord
{
    public static function tableName(){ return '{{%plan}}'; }
}
