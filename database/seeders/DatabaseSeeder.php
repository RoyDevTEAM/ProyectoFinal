<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        DB::table('roles')->insert([
            ['nombre' => 'Profesor', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Estudiante', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Usuarios de prueba
        DB::table('usuarios')->insert([
            [
                'username' => 'profesor1',
                'email' => 'profesor1@example.com',
                'password_hash' => Hash::make('Password123'),
                'rol_id' => 1, // Profesor
                'verificado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'estudiante1',
                'email' => 'estudiante1@example.com',
                'password_hash' => Hash::make('Password123'),
                'rol_id' => 2, // Estudiante
                'verificado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Permisos de prueba
        DB::table('permisos')->insert([
            ['nombre' => 'gestionar_cursos', 'descripcion' => 'Gestionar cursos propios', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'inscribir_cursos', 'descripcion' => 'Inscribirse a cursos', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Asignar permisos a roles
        DB::table('roles_permisos')->insert([
            ['rol_id' => 1, 'permiso_id' => 1], // Profesor: gestionar_cursos
            ['rol_id' => 2, 'permiso_id' => 2], // Estudiante: inscribir_cursos
        ]);
    }
}