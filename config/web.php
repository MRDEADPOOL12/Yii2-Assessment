<?php
return [
    'components' => [
        // Merge these into your main config
        'db' => require __DIR__ . '/db.php', // adjust to your project
        'user' => [
            'identityClass' => 'app\modules\subscription\models\User',
            'enableAutoLogin' => true,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        // Enable queue if not already
        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'db' => 'db',
            'tableName' => '{{%queue}}',
            'channel' => 'default',
            'mutex' => \yii\mutex\MysqlMutex::class,
        ],
    ],
    'modules' => [
        'subscription' => [
            'class' => 'app\modules\subscription\Module',
        ],
    ],
];
