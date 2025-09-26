<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        // Se a tabela não existir, criar normalmente
        if (!\Schema::hasTable('users')) {
            \Schema::create('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('role')->default('aluno');
                $table->string('password')->nullable();
                $table->string('registration')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
            return;
        }

        // Se já existir, adicionar apenas colunas que faltam (não recriar a tabela)
        \Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
            if (!\Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('aluno')->after('email');
            }
            if (!\Schema::hasColumn('users', 'registration')) {
                $table->string('registration')->nullable()->after('password');
            }
            if (!\Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        // Para evitar problemas em SQLite (que tem suporte limitado a dropColumn),
        // o down() tenta restaurar ao estado anterior somente se possível.
        if (\Schema::hasTable('users')) {
            // tente remover as colunas que adicionamos se existirem
            try {
                \Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (\Schema::hasColumn('users', 'role')) {
                        $table->dropColumn('role');
                    }
                    if (\Schema::hasColumn('users', 'registration')) {
                        $table->dropColumn('registration');
                    }
                    if (\Schema::hasColumn('users', 'deleted_at')) {
                        $table->dropSoftDeletes();
                    }
                });
            } catch (\Exception $e) {
                // Em SQLite, dropColumn pode falhar. Nesse caso ignoramos o erro
                // porque migrate:fresh vai dropar todas as tabelas de qualquer forma.
            }
        }
    }

};
