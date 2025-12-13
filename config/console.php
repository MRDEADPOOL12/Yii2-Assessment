<?php
return [
    'controllerMap' => [
        'trial' => [
            'class' => 'app\console\controllers\TrialController',
        ],
    ],
    'components' => [
        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'db' => 'db',
            'tableName' => '{{%queue}}',
            'channel' => 'default',
            'mutex' => \yii\mutex\MysqlMutex::class,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'db' => require __DIR__ . '/db.php',
    ],
];
