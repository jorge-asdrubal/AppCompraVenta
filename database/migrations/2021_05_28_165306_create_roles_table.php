<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 20)->unique();
            $table->string('descripcion', 100)->nullable();
            $table->boolean('condicion')->default(1);
        });
        DB::table('roles')->insert(
            ['id' => '1', 'nombre' => 'Administrador', 'descripcion' => 'Administradores de area'],
        );
        DB::table('roles')->insert(
            ['id' => '2', 'nombre' => 'Vendedor', 'descripcion' => 'Vendedor de area ventas'],
        );
        DB::table('roles')->insert(
            ['id' => '3', 'nombre' => 'Almacenero', 'descripcion' => 'Almacenero de area compras'],
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
