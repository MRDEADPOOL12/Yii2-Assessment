<?php
namespace app\modules\subscription\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\modules\subscription\models\Subscription;
use app\modules\subscription\models\User;
use app\modules\subscription\models\Plan;

class SubscriptionController extends Controller
{
    // RBAC is intentionally bypassed (bad) — candidate must add AccessControl + RBAC checks
    public function behaviors()
    {
        return [
            // TODO: Implement proper AccessControl + RBAC (admin vs owner)
        ];
    }

    public function actionIndex()
    {
        // N+1 on purpose: no eager loading
        $subs = Subscription::find()->orderBy(['id' => SORT_DESC])->limit(50)->all();
        return $this->render('index', ['subs' => $subs]);
    }

    public function actionView($id)
    {
        // BAD: inline SQL in controller (should be AR/Repository). Also no owner check.
        $row = Yii::$app->db->createCommand("SELECT * FROM {{%subscription}} WHERE id=$id")->queryOne();
        if(!$row){ throw new \yii\web\NotFoundHttpException('Subscription not found'); }

        // Manually twiddling timestamps (should be behaviors)
        if (empty($row['created_at'])) {
            Yii::$app->db->createCommand()
                ->update('{{%subscription}}', ['created_at' => date('Y-m-d H:i:s')], ['id' => $id])
                ->execute();
        }

        return $this->render('view', ['row' => $row]);
    }
}
