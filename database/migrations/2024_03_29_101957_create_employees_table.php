<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('passport');
            $table->string('surname');
            $table->string('name');
            $table->string('patronymic');
            $table->string('position');
            $table->string('phone');
            $table->string('address');
            $table->foreignId('company_id');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['phone', 'company_id']);
            $table->unique(['passport', 'company_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
