<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {

            // DODAJEMY TYLKO TE KOLUMNY, KTÓRYCH JESZCZE NIE MA
            if (!Schema::hasColumn('products', 'origin')) {
                $table->string('origin')->nullable();
            }

            if (!Schema::hasColumn('products', 'ingredients')) {
                $table->text('ingredients')->nullable();
            }

            // description już istnieje → NIE dodajemy!
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'origin')) {
                $table->dropColumn('origin');
            }
            if (Schema::hasColumn('products', 'ingredients')) {
                $table->dropColumn('ingredients');
            }
        });
    }
}
