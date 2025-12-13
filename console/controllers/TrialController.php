<?php
namespace app\console\controllers;

use Yii;
use yii\console\Controller;
use app\modules\subscription\models\Subscription;
use app\jobs\SendSubscriptionEmailJob;

class TrialController extends Controller
{
    /**
     * TODO: Implement logic:
     * - Find expired trials
     * - Convert to paid (unless cancelled)
     * - Push SendSubscriptionEmailJob onto queue
     */
    public function actionRun()
    {
        $this->stdout("Trial job stub — implement me.\n");
        return self::EXIT_CODE_NORMAL;
    }
}
