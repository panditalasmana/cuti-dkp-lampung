<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $pegawais = User::where('role', 'pegawai')->whereNotNull('nip')->get();

        foreach ($pegawais as $user) {
            $nip = preg_replace('/\s+/', '', trim($user->nip));
            if (strlen($nip) >= 4) {
                $prefix = substr($nip, 0, 4);
                $user->password = Hash::make($prefix);
                $user->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse action needed
    }
};
