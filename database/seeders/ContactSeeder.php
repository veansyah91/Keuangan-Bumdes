<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contacts = [
            [
                'no_ref' => 'CUST-001',
                'name' => 'General Customer',
                'type' => 'Customer'
            ],
            [
                'no_ref' => 'SUPP-001',
                'name' => 'General Supplier',
                'type' => 'Supplier'
            ],
        ];

        foreach ($contacts as $contact) {
            Contact::create($contact);
        }
    }
}
