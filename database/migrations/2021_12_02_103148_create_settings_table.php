<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fav_name')->nullable();
            $table->string('fav_icon')->nullable();
            $table->string('logo_name')->nullable();
            $table->string('logo_image')->nullable();
            $table->longText('sms_text')->nullable();
            $table->longText('reminder_text')->nullable();
            $table->longText('member_sms_text')->nullable();
            $table->longText('voter_annouce')->nullable();
            $table->longText('member_annouce')->nullable();

            $table->enum('otp_enable',[0,1])->default(0);
            $table->enum('result_enable',[0,1])->default(0);

            $table->string('otp_valid_time')->nullable();
            $table->string('otp_valid_time_type')->nullable();

            $table->enum('voting_record',[0,1])->default(0);
            $table->enum('result_detail',[0,1])->default(0);

            $table->string('sms_api_url')->nullable();
            $table->string('sms_url_method')->nullable();        
            $table->string('sms_token')->nullable();
            $table->string('sms_sender')->nullable();

            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('mail_username')->nullable();
            $table->string('mail_password')->nullable();
            $table->string('mail_encryption')->nullable();
            $table->string('mail_from_address')->nullable();
            $table->string('mail_from_name')->nullable();
            $table->string('mail_subject')->nullable();
            $table->longText('mail_footer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
