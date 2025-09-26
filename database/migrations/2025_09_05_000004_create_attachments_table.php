<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable'); // attachable_id, attachable_type
            $table->string('filename');
            $table->string('path');
            $table->string('mime')->nullable();
            $table->bigInteger('size')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('attachments');
    }
};
