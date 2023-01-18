<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguagesTable extends Migration
{
    protected string $tableName = 'languages';
    protected string $tableNamePhrase = 'phrases';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->tableNamePhrase)) {
            Schema::create($this->tableNamePhrase, function (Blueprint $table) {
                $table->id();
                $table->string('key')->index();
                $table->string('group')->index();
                $table->longText('text');
                $table->timestamps();
            });
        }


        if (!Schema::hasTable($this->tableName)) {
            Schema::create($this->tableName, function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('language_code');
                $table->string('charset')->default('utf-8');
                $table->string('direction')->default('ltr');
                $table->smallInteger('is_default')->default(0);
                $table->smallInteger('is_active')->default(1);
                $table->smallInteger('is_master')->default(0);
                $table->string('version')->default('5.0.1');
                $table->integer('store_id')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tableNamePhrase);
        Schema::dropIfExists($this->tableName);
    }
}