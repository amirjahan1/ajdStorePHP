<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE SEQUENCE IF NOT EXISTS users_id_seq');

        DB::statement('ALTER TABLE users ALTER COLUMN id SET DEFAULT nextval(\'users_id_seq\')');

        DB::statement('ALTER SEQUENCE users_id_seq OWNED BY users.id');

        DB::statement("SELECT setval('users_id_seq', COALESCE((SELECT MAX(id) FROM users), 1), false)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        DB::statement('ALTER TABLE users ALTER COLUMN id DROP DEFAULT');
    }
};