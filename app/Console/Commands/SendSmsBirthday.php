<?php

namespace App\Console\Commands;

use App\Http\Controllers\SmsController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendSmsBirthday extends Command
{
    protected $smsLogController;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-sms-birthday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS on birthday';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SmsController $smsLogController)
    {
        $this->smsLogController = $smsLogController;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://e-sms.dialog.lk/api/v1/',
        ]);

        $response = $client->post('login', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'username' => 'ASIPIYA',
                'password' => 'Dialog@123',
            ],
        ]);

        $responseData = json_decode($response->getBody()->getContents(), true);

        // Check if token exists in the response data
        if (isset($responseData['token'])) {
            // Store token in session
            $session = app('session');
            $session->put('token', $responseData['token']);

            // Optionally, store other relevant data in session
            $session->put('userData', $responseData['userData']);
        }

        $today = Carbon::now()->format('m-d'); // Format as month-day
        Log::info($today);
        // Query the SMS template
        $sms_template = DB::table('sms_template')
            ->where('type', '=', 'birthday_greeting')
            ->where('status', '=', '1')
            ->first();

        if ($sms_template) {
            $customer_table = DB::table('customer')->get();

            foreach ($customer_table as $customer) {
                $Dob = $customer->Dob;
                $First_Name = $customer->First_Name;

                // Log the Dob value to check for issues
                Log::info('Customer DOB:', ['dob' => $First_Name]);

                try {
                    // Ensure $Dob is in a valid format
                    $dob = Carbon::parse($Dob); // Convert $Dob to Carbon object
                    $birthday = $dob->format('m-d'); // Format the birthday as month-day
                } catch (\Exception $e) {
                    // Log the error and skip this entry if parsing fails
                    Log::error('Failed to parse DOB for customer ID ' . $customer->idCustomer . ': ' . $e->getMessage());
                    continue;
                }

                if ($birthday === $today) {
                    // Calculate the age
                    $age = $dob->age; // Directly get the age using Carbon

                    // Prepare the placeholders
                    $placeholders = [
                        '@Age@' => $age,
                        '@Member_Name@' => $customer->First_Name . ' ' . $customer->Last_Name,
                    ];

                    // Replace placeholders in the SMS template
                    $loan_number_txt = $sms_template->template;
                    foreach ($placeholders as $placeholder => $value) {
                        $loan_number_txt = str_replace($placeholder, $value, $loan_number_txt);
                    }

                    // Log the SMS message
                    $this->smsLogController->index($customer->idCustomer, $loan_number_txt, "Customer Birthday Greeting");
                }
            }

        } else {
            Log::info('No SMS template found for today’s date.');
        }

        return 0;
    }
}
