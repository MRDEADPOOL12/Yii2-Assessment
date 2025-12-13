<?php
/* @var $row array */
use yii\helpers\Html;
?>
<h1>Subscription #<?= (int)$row['id'] ?></h1>
<ul>
    <li>User ID: <?= Html::encode($row['user_id']) ?></li>
    <li>Plan ID: <?= Html::encode($row['plan_id']) ?></li>
    <li>Status: <?= Html::encode($row['status']) ?></li>
    <li>Type: <?= Html::encode($row['type']) ?></li>
    <li>Trial End: <?= Html::encode($row['trial_end_at']) ?></li>
    <li>Created At: <?= Html::encode($row['created_at']) ?></li>
    <li>Updated At: <?= Html::encode($row['updated_at']) ?></li>
</ul>
<p><em>Note:</em> This view expects the same array keys after your refactor. Keep output shape stable.</p>
