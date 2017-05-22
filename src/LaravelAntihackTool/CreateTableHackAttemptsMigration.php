<?php

namespace LaravelAntihackTool;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

abstract class CreateTableHackAttemptsMigration extends Migration {

    public function up() {
        if (!\Schema::hasTable($this->getTableName())) {
            \Schema::create($this->getTableName(), function (Blueprint $table) {
                $table->increments('id');
                $table->string('ip', 40)->nullable();
                $table->string('user_agent')->nullable();
                $table->timestampTz('created_at')->default(\DB::raw('NOW()'));

                $table->index('ip');
            });
        }
    }

    public function down() {
        \Schema::dropIfExists($this->getTableName());
    }

    protected function getTableName() {
        return config('antihack.table_name', 'hack_attempts');
    }
}