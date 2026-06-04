<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add the new columns safely as nullable first (so existing rows don't crash)
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
        });

        // 2. Safely migrate existing data (if any)
        $users = DB::table('users')->select('id', 'name')->get();
        foreach ($users as $user) {
            if (! empty($user->name)) {
                // Split the name by the first space found
                $parts = explode(' ', trim($user->name), 2);

                DB::table('users')->where('id', $user->id)->update([
                    'first_name' => $parts[0],
                    'last_name' => $parts[1] ?? '', // Default to empty string if no last name exists
                ]);
            }
        }

        // 3. Now that data is safe, drop the old 'name' column and make new columns required
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });

        // Recombine names if rolling back
        $users = DB::table('users')->select('id', 'first_name', 'last_name')->get();
        foreach ($users as $user) {
            DB::table('users')->where('id', $user->id)->update([
                'name' => trim($user->first_name.' '.$user->last_name),
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
            $table->string('name')->nullable(false)->change();
        });
    }
};
