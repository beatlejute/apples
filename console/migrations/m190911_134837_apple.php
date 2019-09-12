<?php

use yii\db\Migration;

/**
 * Class m190911_134837_apples
 */
class m190911_134837_apple extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("apple", [
            "id" => $this->primaryKey(),
            "color" => $this->string(6),
            "dateCreate" => $this->integer(),
            "dateFall" => $this->integer(),
            "fallen" => $this->boolean(),
            "rotten" => $this->boolean(),
            "remainderPercent" => $this->integer(3)->defaultValue(100),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('apple');
    }
}
