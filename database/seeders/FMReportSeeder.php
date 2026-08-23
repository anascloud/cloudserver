<?php

namespace Database\Seeders;

use App\Models\FM\Company;
use App\Models\FM\Payment;
use Illuminate\Database\Seeder;

class FMReportSeeder extends Seeder
{
    /**
     * Seed demo companies and payments so the Accounts Receivable and
     * Accounts Payable reports show meaningful data.
     *
     * @return void
     */
    public function run()
    {
        // Reset existing rows first (children before parents for FKs)
        Payment::query()->delete();
        Company::query()->delete();

        $companies = [
            [
                'company_name' => 'NovaTech Solutions',
                'abbreviation' => 'NTS',
                'currency_id' => 1,
                'default_payable_account_id' => 2001,
                'default_receivable_account_id' => 1001,
                'domain' => 'novatech.com',
                'phone_number' => '+1-555-0101',
                'email' => 'accounts@novatech.com',
                'date_of_establishment' => '2015-03-12',
                'is_enabled' => true,
            ],
            [
                'company_name' => 'Global Trade Ltd',
                'abbreviation' => 'GTL',
                'currency_id' => 2,
                'default_payable_account_id' => 2002,
                'default_receivable_account_id' => 1002,
                'domain' => 'globaltrade.com',
                'phone_number' => '+44-20-7946-0102',
                'email' => 'accounts@globaltrade.com',
                'date_of_establishment' => '2012-07-01',
                'is_enabled' => true,
            ],
            [
                'company_name' => 'Crescent Textiles',
                'abbreviation' => 'CTX',
                'currency_id' => 3,
                'default_payable_account_id' => 2003,
                'default_receivable_account_id' => 1003,
                'domain' => 'crescenttextiles.com',
                'phone_number' => '+880-2-5512-0103',
                'email' => 'accounts@crescenttextiles.com',
                'date_of_establishment' => '2018-11-20',
                'is_enabled' => true,
            ],
            [
                'company_name' => 'BlueWave Logistics',
                'abbreviation' => 'BWL',
                'currency_id' => 1,
                'default_payable_account_id' => 2004,
                'default_receivable_account_id' => 1004,
                'domain' => 'bluewavelogistics.com',
                'phone_number' => '+61-2-9374-0104',
                'email' => 'accounts@bluewavelogistics.com',
                'date_of_establishment' => '2016-05-09',
                'is_enabled' => true,
            ],
            [
                'company_name' => 'Sunrise Foods',
                'abbreviation' => 'SFD',
                'currency_id' => 2,
                'default_payable_account_id' => 2005,
                'default_receivable_account_id' => 1005,
                'domain' => 'sunrisefoods.com',
                'phone_number' => '+971-4-555-0105',
                'email' => 'accounts@sunrisefoods.com',
                'date_of_establishment' => '2019-02-15',
                'is_enabled' => true,
            ],
        ];

        $companyIds = [];
        foreach ($companies as $index => $companyData) {
            $company = Company::create($companyData);
            $companyIds[] = $company->id;
        }

        $payments = [
            // ---- Customer payments (Accounts Receivable) ----
            ['party_type' => 'Customer', 'party_name' => 'Acme Corporation',       'payment_amount' => 12500.00, 'total_allocation_amount' => 5000.00,  'unallocated_amount' => 7500.00,  'different_amount' => 0.00,   'payment_status' => 'Partial', 'posting_date' => '2026-01-05', 'reference_date' => '2026-01-20'],
            ['party_type' => 'Customer', 'party_name' => 'Beta Industries',         'payment_amount' => 8400.50,  'total_allocation_amount' => 8400.50,  'unallocated_amount' => 0.00,     'different_amount' => 0.00,   'payment_status' => 'Paid',    'posting_date' => '2026-01-12', 'reference_date' => '2026-02-10'],
            ['party_type' => 'Customer', 'party_name' => 'Gamma Retail Group',      'payment_amount' => 22300.00, 'total_allocation_amount' => 10000.00, 'unallocated_amount' => 12300.00, 'different_amount' => 500.00,  'payment_status' => 'Partial', 'posting_date' => '2026-02-02', 'reference_date' => '2026-02-28'],
            ['party_type' => 'Customer', 'party_name' => 'Delta Wholesale',         'payment_amount' => 15750.75, 'total_allocation_amount' => 15750.75, 'unallocated_amount' => 0.00,     'different_amount' => 0.00,   'payment_status' => 'Paid',    'posting_date' => '2026-02-18', 'reference_date' => '2026-03-15'],
            ['party_type' => 'Customer', 'party_name' => 'Epsilon Electronics',     'payment_amount' => 31200.00, 'total_allocation_amount' => 12000.00, 'unallocated_amount' => 19200.00, 'different_amount' => 1200.00, 'payment_status' => 'Partial', 'posting_date' => '2026-03-03', 'reference_date' => '2026-03-30'],
            ['party_type' => 'Customer', 'party_name' => 'Zeta Pharma',             'payment_amount' => 9800.25,  'total_allocation_amount' => 0.00,     'unallocated_amount' => 9800.25,  'different_amount' => 0.00,   'payment_status' => 'Unpaid',  'posting_date' => '2026-03-20', 'reference_date' => '2026-04-15'],
            ['party_type' => 'Customer', 'party_name' => 'Eta Construction',        'payment_amount' => 45600.00, 'total_allocation_amount' => 20000.00, 'unallocated_amount' => 25600.00, 'different_amount' => 600.00,  'payment_status' => 'Partial', 'posting_date' => '2026-04-01', 'reference_date' => '2026-04-28'],
            ['party_type' => 'Customer', 'party_name' => 'Theta Auto Parts',        'payment_amount' => 6730.40,  'total_allocation_amount' => 6730.40,  'unallocated_amount' => 0.00,     'different_amount' => 0.00,   'payment_status' => 'Paid',    'posting_date' => '2026-04-14', 'reference_date' => '2026-05-10'],
            ['party_type' => 'Customer', 'party_name' => 'Iota Beverages',          'payment_amount' => 18900.90, 'total_allocation_amount' => 9000.00,  'unallocated_amount' => 9900.90,  'different_amount' => 300.00,  'payment_status' => 'Partial', 'posting_date' => '2026-05-06', 'reference_date' => '2026-05-31'],

            // ---- Supplier payments (Accounts Payable) ----
            ['party_type' => 'Supplier', 'party_name' => 'Vertex Raw Materials',    'payment_amount' => 27500.00, 'total_allocation_amount' => 15000.00, 'unallocated_amount' => 12500.00, 'different_amount' => 0.00,   'payment_status' => 'Partial', 'posting_date' => '2026-01-08', 'reference_date' => '2026-01-25'],
            ['party_type' => 'Supplier', 'party_name' => 'Prime Packaging Co.',     'payment_amount' => 11400.60, 'total_allocation_amount' => 11400.60, 'unallocated_amount' => 0.00,     'different_amount' => 0.00,   'payment_status' => 'Paid',    'posting_date' => '2026-01-22', 'reference_date' => '2026-02-12'],
            ['party_type' => 'Supplier', 'party_name' => 'SteelCore Fabricators',   'payment_amount' => 38200.00, 'total_allocation_amount' => 18000.00, 'unallocated_amount' => 20200.00, 'different_amount' => 800.00,  'payment_status' => 'Partial', 'posting_date' => '2026-02-10', 'reference_date' => '2026-03-01'],
            ['party_type' => 'Supplier', 'party_name' => 'ChemPro Supplies',        'payment_amount' => 9300.35,  'total_allocation_amount' => 9300.35,  'unallocated_amount' => 0.00,     'different_amount' => 0.00,   'payment_status' => 'Paid',    'posting_date' => '2026-02-25', 'reference_date' => '2026-03-18'],
            ['party_type' => 'Supplier', 'party_name' => 'TransGlobal Freight',     'payment_amount' => 22100.00, 'total_allocation_amount' => 8000.00,  'unallocated_amount' => 14100.00, 'different_amount' => 400.00,  'payment_status' => 'Partial', 'posting_date' => '2026-03-11', 'reference_date' => '2026-04-02'],
            ['party_type' => 'Supplier', 'party_name' => 'GreenTech Energy',        'payment_amount' => 15600.80, 'total_allocation_amount' => 0.00,     'unallocated_amount' => 15600.80, 'different_amount' => 0.00,   'payment_status' => 'Unpaid',  'posting_date' => '2026-03-28', 'reference_date' => '2026-04-20'],
            ['party_type' => 'Supplier', 'party_name' => 'Nordic Timber Importers', 'payment_amount' => 49700.00, 'total_allocation_amount' => 25000.00, 'unallocated_amount' => 24700.00, 'different_amount' => 1000.00, 'payment_status' => 'Partial', 'posting_date' => '2026-04-07', 'reference_date' => '2026-04-30'],
            ['party_type' => 'Supplier', 'party_name' => 'Apex Machinery Works',    'payment_amount' => 7350.25,  'total_allocation_amount' => 7350.25,  'unallocated_amount' => 0.00,     'different_amount' => 0.00,   'payment_status' => 'Paid',    'posting_date' => '2026-04-21', 'reference_date' => '2026-05-12'],
            ['party_type' => 'Supplier', 'party_name' => 'BlueLane Stationery',     'payment_amount' => 12400.90, 'total_allocation_amount' => 6000.00,  'unallocated_amount' => 6400.90,  'different_amount' => 200.00,  'payment_status' => 'Partial', 'posting_date' => '2026-05-09', 'reference_date' => '2026-06-01'],
        ];

        foreach ($payments as $index => $paymentData) {
            $companyIndex = $index % count($companyIds);

            Payment::create(array_merge([
                'payment_no' => 'PAY-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                'serial_number' => 'INV-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'company_id' => $companyIds[$companyIndex],
                'from_currency_id' => $companies[$companyIndex]['currency_id'],
                'to_currency_id' => $companies[$companyIndex]['currency_id'],
                'account_paid_from_id' => 3000 + $companyIndex,
                'account_paid_to_id' => 2000 + (($index % 5) + 1),
                'reference_number' => 'REF-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'transaction_type' => 'Sales Invoice',
                'total_tax' => round($paymentData['payment_amount'] * 0.05, 2),
            ], $paymentData));
        }

        $this->command->info('Demo FM report data seeded successfully!');
    }
}
