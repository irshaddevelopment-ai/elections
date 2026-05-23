<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove pre-existing duplicate votes BEFORE adding the constraint,
        // otherwise CREATE UNIQUE INDEX fails. Keeps the lowest idvote_master
        // for each (user_code, election_code, round_number) and deletes the rest.
        $duplicates = DB::select("
            SELECT user_code, election_code, round_number, MIN(idvote_master) AS keep_id
            FROM vote_master
            WHERE user_code IS NOT NULL
              AND election_code IS NOT NULL
              AND round_number IS NOT NULL
            GROUP BY user_code, election_code, round_number
            HAVING COUNT(*) > 1
        ");

        foreach ($duplicates as $dup) {
            DB::table('vote_master')
                ->where('user_code', $dup->user_code)
                ->where('election_code', $dup->election_code)
                ->where('round_number', $dup->round_number)
                ->where('idvote_master', '!=', $dup->keep_id)
                ->delete();
        }

        Schema::table('vote_master', function ($table) {
            $table->unique(
                ['user_code', 'election_code', 'round_number'],
                'uniq_vote_user_round'
            );
        });
    }

    public function down(): void
    {
        Schema::table('vote_master', function ($table) {
            $table->dropUnique('uniq_vote_user_round');
        });
    }
};
