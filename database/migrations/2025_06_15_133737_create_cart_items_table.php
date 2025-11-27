<?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::create('cart_items', function (Blueprint $table) {
//             $table->id();
//             $table->string('session_id')->nullable();
//             $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
//             $table->foreignId('product_id')->constrained()->onDelete('cascade');
//             $table->string('product_name');
//             $table->decimal('price', 10, 2);
//             $table->integer('quantity');
//             $table->json('product_options')->nullable(); // size, color, etc.
//             $table->timestamps();
            
//             $table->index(['session_id', 'user_id']);
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('cart_items');
//     }
// };

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('carts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('session_id')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'session_id']);
            });

            Schema::create('cart_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cart_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->integer('quantity')->default(1);
                $table->decimal('price', 10, 2);
                $table->timestamps();
                
                $table->unique(['cart_id', 'product_id']);
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('cart_items');
            Schema::dropIfExists('carts');
        }
    };
