<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUsersTableColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ログイン用のユーザテーブルに拡張
        Schema::table('users', function (Blueprint $table) {
            // カラム追加
            $table->tinyInteger('locked_flg')->default(0)->after('remember_token');
            $table->integer('error_count')->unsigned()->default(0)->after('locked_flg');
            // カラム削除
            $table->dropColumn('email_verified_at');
            $table->dropColumn('remember_token');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->dropColumn('locked_flg');
            $table->dropColumn('error_count');
        });
    }
}
