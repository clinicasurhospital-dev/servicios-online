<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update existing users with domain @surhospital.com to @surhospital.net
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'email')) {
            DB::statement("UPDATE users SET email = REPLACE(email, '@surhospital.com', '@surhospital.net') WHERE email LIKE '%@surhospital.com'");
        }

        // 2. Update existing patients with domain @surhospital.com to @surhospital.net if email column exists
        if (Schema::hasTable('patients') && Schema::hasColumn('patients', 'email')) {
            DB::statement("UPDATE patients SET email = REPLACE(email, '@surhospital.com', '@surhospital.net') WHERE email LIKE '%@surhospital.com'");
        }

        // 3. Update existing doctors ONLY if an email column exists (in this schema doctors references users.id)
        if (Schema::hasTable('doctors') && Schema::hasColumn('doctors', 'email')) {
            DB::statement("UPDATE doctors SET email = REPLACE(email, '@surhospital.com', '@surhospital.net') WHERE email LIKE '%@surhospital.com'");
        }

        // 4. Ensure info@surhospital.net exists as administrator if not present
        try {
            if (Schema::hasTable('users')) {
                $infoUser = DB::table('users')->where('email', 'info@surhospital.net')->first();
                if (!$infoUser) {
                    $adminUser = DB::table('users')->where('email', 'admin@surhospital.net')->first();
                    $password = $adminUser ? $adminUser->password : bcrypt('password');

                    $userId = DB::table('users')->insertGetId([
                        'name' => 'Dirección General SurHospital',
                        'email' => 'info@surhospital.net',
                        'phone' => '+593 99 123 4567',
                        'password' => $password,
                        'email_verified_at' => now(),
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if (Schema::hasTable('roles') && Schema::hasTable('model_has_roles')) {
                        $role = DB::table('roles')->where('name', 'SUPER_ADMIN')->first();
                        if ($role) {
                            DB::table('model_has_roles')->insertOrIgnore([
                                'role_id' => $role->id,
                                'model_type' => User::class,
                                'model_id' => $userId,
                            ]);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silently continue to prevent migration failure
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'email')) {
            DB::statement("UPDATE users SET email = REPLACE(email, '@surhospital.net', '@surhospital.com') WHERE email IN ('admin@surhospital.net', 'recepcion@surhospital.net', 'doctor@surhospital.net', 'paciente@surhospital.net')");
        }

        if (Schema::hasTable('patients') && Schema::hasColumn('patients', 'email')) {
            DB::statement("UPDATE patients SET email = REPLACE(email, '@surhospital.net', '@surhospital.com') WHERE email = 'paciente@surhospital.net'");
        }

        if (Schema::hasTable('doctors') && Schema::hasColumn('doctors', 'email')) {
            DB::statement("UPDATE doctors SET email = REPLACE(email, '@surhospital.net', '@surhospital.com') WHERE email = 'doctor@surhospital.net'");
        }
    }
};
