<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'=>'Coordenador NPI',
            'email'=>'coordenador@npi.test',
            'role'=>'coordenador',
            'password'=>Hash::make('password123')
        ]);

        User::create([
            'name'=>'Aluno Exemplo',
            'email'=>'aluno1@npi.test',
            'role'=>'aluno',
            'password'=>Hash::make('password123')
        ]);

        User::create([
            'name'=>'Aluno Dois',
            'email'=>'aluno2@npi.test',
            'role'=>'aluno',
            'password'=>Hash::make('password123')
        ]);
    }
}
