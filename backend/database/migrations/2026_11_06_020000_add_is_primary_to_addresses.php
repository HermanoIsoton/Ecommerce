<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->boolean('is_primary')->default(false);
        });

        DB::table('addresses')
            ->select('user_id')
            ->distinct()
            ->orderBy('user_id')
            ->pluck('user_id')
            ->each(function (int $userId): void {
                $firstAddressId = DB::table('addresses')
                    ->where('user_id', $userId)
                    ->orderBy('id')
                    ->value('id');

                if ($firstAddressId !== null) {
                    DB::table('addresses')
                        ->where('id', $firstAddressId)
                        ->update(['is_primary' => true]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('is_primary');
        });
    }
};
