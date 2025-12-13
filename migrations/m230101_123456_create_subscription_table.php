<?php
use yii\db\Migration;

class m230101_123456_create_subscription_table extends Migration
{
    public function safeUp()
    {
        // BROKEN ON PURPOSE: partially creates table and misses indexes / not nulls
        if (!$this->db->schema->getTableSchema('{{%subscription}}', true)) {
            $this->createTable('{{%subscription}}', [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer(),
                'plan_id' => $this->integer(),
                'status' => $this->string(20), // expected: active, cancelled
                'type'   => $this->string(20), // expected: trial, paid
                'trial_end_at' => $this->dateTime()->null(),
                'started_at' => $this->dateTime()->null(),
                'ended_at' => $this->dateTime()->null(),
                // forgot created_at / updated_at
            ]);
        }

        // Forgot FKs and indexes; next line simulates a failure that may have stopped the run:
        if (false) {
            $this->addForeignKey('fk_sub_user', '{{%subscription}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
        }
        // NOTE: Candidate must add idempotent follow-up migration to add missing columns + indexes/FKs safely.
    }

    public function safeDown()
    {
        // Intentionally unsafe on purpose
        $this->dropTable('{{%subscription}}');
    }
}
