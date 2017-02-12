<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m170211_045628_create_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB ROW_FORMAT=DYNAMIC';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey()->unsigned(),
            'email' => $this->string()->unique(),
            'username' => $this->string()->unique(),
            'password' => $this->string()->null(),
            'confirmation' => $this->string()->null(),
            'auth_key' => $this->string()->null(),
            'created_at' => $this->timestamp()->null(),
            'updated_at' => $this->timestamp()->null(),
        ], $tableOptions);
        $this->createTable('{{%password_reset}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'token' => $this->string()->unique(),
            'created_at' => $this->timestamp()->null(),
            'consumed_at' => $this->timestamp()->null(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-password_reset-user_id',
            '{{%password_reset}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%password_reset}}');
        $this->dropTable('{{%user}}');
    }
}
