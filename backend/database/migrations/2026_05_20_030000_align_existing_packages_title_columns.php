<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('packages')) {
            return;
        }

        Schema::table('packages', function (Blueprint $table) {
            if (! Schema::hasColumn('packages', 'title_fr')) {
                $table->string('title_fr')->nullable()->after('id');
            }

            if (! Schema::hasColumn('packages', 'title_en')) {
                $after = Schema::hasColumn('packages', 'title_fr') ? 'title_fr' : 'id';
                $table->string('title_en')->nullable()->after($after);
            }
        });

        if (Schema::hasColumn('packages', 'title')) {
            DB::table('packages')
                ->whereNull('title_fr')
                ->update(['title_fr' => DB::raw('title')]);

            DB::table('packages')
                ->whereNull('title_en')
                ->update(['title_en' => DB::raw('title')]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('packages') || ! Schema::hasColumn('packages', 'title')) {
            return;
        }

        Schema::table('packages', function (Blueprint $table) {
            if (Schema::hasColumn('packages', 'title_en')) {
                $table->dropColumn('title_en');
            }

            if (Schema::hasColumn('packages', 'title_fr')) {
                $table->dropColumn('title_fr');
            }
        });
    }
};
