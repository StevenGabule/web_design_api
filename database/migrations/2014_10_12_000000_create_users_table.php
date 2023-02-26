<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique()->index();
            $table->enum('user_type', [0,1,2,])
                ->default(1)
                ->comment('0-admin,1-customer,2-web-designer');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone_number')->unique();
            $table->string('telephone_number')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('tagline')->nullable();
            $table->text('about')->nullable();
            $table->text('location')->nullable();
            $table->text('format_address')->nullable();
            $table->dateTimeTz('last_password_changed')->nullable();
            $table->string('verification_code')->nullable();
            $table->string('verification_limit')->nullable();
            $table->boolean('two_fa_setup')->default(false);
            $table->string('two_fa_setup_type')->nullable();
            $table->dateTimeTz('expiration_date')->nullable();
            $table->enum('status', [1, 2, 3, 4])
                ->default(1)
                ->comment('indicate the status of the user')
                ->comment('1 - pending/confirming, 2 - active, 3 - deactivate, 4 - cancel/expire');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletesTz();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
