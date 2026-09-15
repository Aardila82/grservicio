<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $user = User::firstOrCreate(
            ['email' => 'admin@facturacion.com'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('password123'),
            ]
        );

        // Create default settings (one global record for the app)
        if (Setting::count() === 0) {
            Setting::create([
                'company_name'     => 'Técnico Express',
                'company_phone'    => '+57 300 123 4567',
                'company_email'    => 'contacto@tecnicoexpress.com',
                'company_address'  => 'Bogotá, Colombia',
                'currency'         => 'COP',
                'invoice_prefix'   => 'FAC',
                'whatsapp_message' => 'Hola. Adjuntamos la factura correspondiente al servicio realizado. Muchas gracias.',
            ]);
        }

        $this->command->info('✅ Usuario admin creado: admin@facturacion.com / password123');
    }
}
