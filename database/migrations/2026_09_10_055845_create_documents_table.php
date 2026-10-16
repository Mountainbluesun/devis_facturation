<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['quote', 'invoice']);
            $table->string('number')->unique();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'paid', 'overdue'])
                  ->default('draft');
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
