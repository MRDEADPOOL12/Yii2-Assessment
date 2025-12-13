<?php
namespace app\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class SendSubscriptionEmailJob extends BaseObject implements JobInterface
{
    public $userId;
    public $subscriptionId;
    public $subject;
    public $body;

    public function execute($queue)
    {
        // INTENTIONALLY naive; replace with your mailer integration (Yii::$app->mailer->compose...)
        // but keep it queued (do not call mail() directly).
        Yii::info("Email to user {$this->userId} about subscription {$this->subscriptionId}: {$this->subject}", __METHOD__);
    }
}
