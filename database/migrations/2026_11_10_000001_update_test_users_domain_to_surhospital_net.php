<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update existing users with domain @surhospital.com to @surhospital.net
        if (Schema::hasTable('users')) {
            DB::statement("UPDATE users SET email = REPLACE(email, '@surhospital.com', '@surhospital.net') WHERE email LIKE '%@surhospital.com'");
        }

        // 2. Update existing doctors with domain @surhospital.com to @surhospital.net
        if (Schema::hasTable('doctors')) {
            DB::statement("UPDATE doctors SET email = REPLACE(email, '@surhospital.com', '@surhospital.net') WHERE email LIKE '%@surhospital.com'");
        }

        // 3. Ensure info@surhospital.net exists as administrator if not present
        if (Schema::hasTable('users')) {
            $infoUser = User::where('email', 'info@surhospital.net')->first();
            if (!$infoUser) {
                // If admin@surhospital.net exists, create info@surhospital.net with same credentials
                $adminUser = User::where('email', 'admin@surhospital.net')->first();
                $password = $adminUser ? $adminUser->password : bcrypt('password');

                $infoUser = User::create([
                    'name' => 'Dirección General SurHospital',
                    'email' => 'info@surhospital.net',
                    'phone' => '+593 99 123 4567',
                    'password' => $password,
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]);

                if (class_exists(Role::class) && Role::where('name', 'SUPER_ADMIN')->exists()) {
                    $infoUser->assignRole('SUPER_ADMIN');
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            DB::statement("UPDATE users SET email = REPLACE(email, '@surhospital.net', '@surhospital.com') WHERE email IN ('admin@surhospital.net', 'recepcion@surhospital.net', 'doctor@surhospital.net', 'paciente@surhospital.net')");
        }

        if (Schema::hasTable('doctors')) {
            DB::statement("UPDATE doctors SET email = REPLACE(email, '@surhospital.net', '@surhospital.com') WHERE email = 'doctor@surhospital.net'");
        }
    }
};

