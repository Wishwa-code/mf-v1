<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class AgreementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $smsContent=$request->smsContent;
        $smsTypeSelect=$request->smsTypeSelect;

        updateWithBranch('agreement_type', 'type', $smsTypeSelect, [
            'template' => $smsContent
        ]);


        return response()->json(['message' => 'Data saved successfully','id'=>'1'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $smsTypeSelect=$request->smsTypeSelect;
        $sms_template = tableWithBranch('agreement_type')
            ->where('type', '=', $smsTypeSelect)
            ->first();
        return response()->json(['sms_template' =>$sms_template,'id'=>'1'], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $smsSendStatus=$request->smsSendStatus;
        $smsTypeSelect=$request->smsTypeSelect;

        updateWithBranch('sms_template', 'type', $smsTypeSelect, [
            'status' => $smsSendStatus
        ]);


        return response()->json(['message' => 'Data saved successfully','id'=>'1'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function agreement_view($type, $id)
    {



        // Fetch agreement type with branch_id
        $agreement = tableWithBranch('agreement_type')
            ->where('type', '=', $type)
            ->first();


        // Fetch the loan and associated data (similar to loan_view_Np)
        $loan = tableWithBranch('customer_loan')
            ->where('idCustomer_Loan', $id)
            ->first();

        if (!$loan) {
            return Redirect::back()->with('error', 'Loan not found.');
        }

        $installments = tableWithBranch('installments')
            ->where('Customer_Loan_idCustomer_Loan', $id)
            ->get();

        $installments_first_date = tableWithBranch('installments')
            ->where('Customer_Loan_idCustomer_Loan', $id)
            ->first();

        $last_installment = tableWithBranch('installments')
            ->where('Customer_Loan_idCustomer_Loan', $id)
            ->orderBy('idInstallments', 'desc') // Replace 'id' with your ordering column if different
            ->first();


        $customer_payments = tableWithBranch('customer_payments')
            ->where('Customer_Loan_idCustomer_Loan', $id)
            ->get();
        $total_paid_amount = $customer_payments->sum('Amount');
        $customers = tableWithBranch('customer')
            ->where('idCustomer', $loan->Customer_idCustomer)
            ->first();

        $User = tableWithBranch('user')
            ->where('id', $loan->User_idUser)
            ->first();

        $Lending_Officer = tableWithBranch('user')
            ->where('id', $loan->lending_officer_id)
            ->first();

        $Loan_Category = tableWithBranch('loan_category')
            ->where('idLoan_Category', $loan->Loan_Category_idLoan_Category)
            ->first();

        $Customer_Bank = tableWithBranch('customer_has_bank')
            ->where([
                ['id', $loan->cus_bank_account],
                ['cus_id', $loan->Customer_idCustomer]
            ])->first();

        $Other_Charges = tableWithBranch('loan_other_charges')
            ->where('Customer_Loan_idCustomer_Loan', $id)
            ->get();

        $documents = tableWithBranch('documents')
            ->where('Customer_Loan_idCustomer_Loan', $id)
            ->get();

// Fetch witnesses
        $witness = tableWithBranch('witness')
            ->where('Customer_Loan_idCustomer_Loan', $id)
            ->get();

        $witnessDetails = [];
        foreach ($witness as $item) {
            if ($item->type === "Guarantor") {
                $guarantor = tableWithBranch('guardian')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address','Address_02','Address_03')
                    ->where('idGuardian', $item->cus_id)->first();

                $witnessDetails[] = [
                    'type' => 'Guarantor',
                    'details' => $guarantor ?? (object)['First_Name' => null, 'Last_Name' => null, 'Contact_No' => null, 'Nic' => null, 'Address' => null, 'Address_02' => null, 'Address_03' => null]
                ];
            } else {
                $customer = tableWithBranch('customer')
                    ->select('First_Name', 'Last_Name', 'Contact_No', 'Nic', 'Address','Address_02','Address_03')
                    ->where('idCustomer', $item->cus_id)->first();

                $witnessDetails[] = [
                    'type' => 'Customer',
                    'details' => $customer ?? (object)['First_Name' => null, 'Last_Name' => null, 'Contact_No' => null, 'Nic' => null, 'Address' => null, 'Address_02' => null, 'Address_03' => null]
                ];
            }
        }






        // Define a mapping of placeholder keys to their corresponding database values
        $placeholders = [
            '@Loan_Number@' => $loan->Loan_No,
            '@Loan_Issue_Date@' => $loan->Date_Time,
            '@First_Installment_Date@' => $installments_first_date->Installment_Date,
            '@Maturity_Date@' => $last_installment->Installment_Date,
            '@Installment_Period_Type@' => $loan->Collection_Type,
            '@Product_Name@' => $Loan_Category->Name ?? '-', // Assuming 'name' is a column in the loan_category table
            '@Date@' => date('Y-m-d'),
            '@Loan_Amount@' => number_format($loan->Amount, 2, '.', ','),
            '@Interest_Rate@' => $loan->Interest_Rate . '%',
            '@Penalty_Rate@' => $loan->Panalty_Rate, // Assuming 'Penalty_Rate' is a column in the customer_loan table
            '@Installment_Count@' => $loan->Installment_Count, // Assuming 'Installment_Count' is a column in the customer_loan table
            '@Interest_Amount@' => $loan->Interest_Amount,
            '@Loan_Other_Charges_Total@' => $loan->Total_Other_Amount, // Assuming 'Other_Charges_Total' is a column in the customer_loan table
            '@Total_Loan_Amount@' => $loan->Total_Loan_Amount, // Assuming 'Total_Amount' is a column in the customer_loan table
            '@Installment_Amount@' => $loan->Installment_Amount,
            '@Collection_Type@' => $loan->Collection_Type, // Assuming 'Collection_Type' is a column in the customer_loan table
            '@Created_User@' => $User->Full_Name ?? 'Admin', // Assuming 'Created_User' is a column in the customer_loan table
            '@Lending_Officer@' => $Lending_Officer->Full_Name, // Assuming 'Lending_Officer' is a column in the customer_loan table
            '@Customer_No@' => $customers->cus_number,
            '@Customer_Title@' => $customers->Title, // Assuming 'Title' is a column in the customer table
            '@Customer_Full_Name@' => $customers->First_Name . ' ' . $customers->Last_Name,
            '@Customer_Email@' => $customers->Email, // Assuming 'Email' is a column in the customer table
            '@Customer_NIC@' => $customers->Nic,
            '@Customer_Contact_No@' => $customers->Contact_No, // Assuming 'Contact_No' is a column in the customer table
            '@Customer_Lan_No@' => $customers->contact_number_2, // Assuming 'Lan_No' is a column in the customer table
            '@Customer_Gender@' => $customers->Gender, // Assuming 'Gender' is a column in the customer table
            '@Customer_DOB@' => $customers->Dob, // Assuming 'DOB' is a column in the customer table
            '@Customer_Current_Address_Line_01@' => $customers->Address,
            '@Customer_Current_Address_Line_02@' => $customers->Address_02,
            '@Customer_Current_Address_Line_03@' => $customers->Address_03,
            '@Customer_Permanent_Address_Line_01@' => $customers->Per_Address_01, // Assuming 'Permanent_Address_Line_01' is a column in the customer table
            '@Customer_Permanent_Address_Line_02@' => $customers->Per_Address_02, // Assuming 'Permanent_Address_Line_02' is a column in the customer table
            '@Customer_Permanent_Address_Line_03@' => $customers->Per_Address_03, // Assuming 'Permanent_Address_Line_03' is a column in the customer table
            '@Customer_City@' => $customers->City, // Assuming 'City' is a column in the customer table
            '@Customer_State@' => $customers->State, // Assuming 'State' is a column in the customer table
            '@Customer_Civil_Status@' => $customers->civil_status, // Assuming 'Civil_Status' is a column in the customer table
            '@Customer_Occupation@' => $customers->occu_job_position, // Assuming 'Occupation' is a column in the customer table
            '@Guardian_First_Name@' => $customers->Gua_name, // Assuming 'Occupation' is a column in the customer table
            '@Guardian_Title@' => $customers->Gua_title, // Assuming 'Guardian_Title' is a column in the customer table
            '@Guardian_Relation@' => $customers->Gua_relation, // Assuming 'Guardian_Relation' is a column in the customer table
            '@Guardian_NIC@' => $customers->Gua_nic, // Assuming 'Guardian_NIC' is a column in the customer table
            '@Guardian_Address@' => $customers->Gua_address, // Assuming 'Guardian_Address_Line_01' is a column in the customer table
            '@Guardian_Occupation@' => $customers->Gua_occu, // Assuming 'Guardian_Occupation' is a column in the customer table
            '@Guardian_Contact_No@' => $customers->Gua_contact,
            'class="ql-align-center"' => 'style="text-align: center;"',
            'class="ql-align-right"' => 'style="text-align: right;"',
            'class="ql-align-justify"' => 'style="text-align: justify;"',
            'class="ql-size-large"' => 'style="font-size: 18px;"',
            'class="ql-size-small"' => 'style="font-size: 14px;"',
            'class="ql-size-huge"' => 'style="font-size: 26px;"'
        ];

        $placeholders['@Guarantee_First_Name@'] = "";
        $placeholders['@Guarantee_Last_Name@'] = "";
        $placeholders['@Guarantee_Contact_No@'] = "";
        $placeholders['@Guarantee_NIC@'] = "";
        $placeholders['@Guarantee_Address_Line_01@'] = "";
        $placeholders['@Guarantee_Address_Line_02@'] = "";
        $placeholders['@Guarantee_Address_Line_03@'] = "";

        for ($i = 1; $i <= 4; $i++) {
            $suffix = $i === 1 ? '' : "_$i";
            $placeholders["@Guarantee_First_Name$suffix@"] = '';
            $placeholders["@Guarantee_Last_Name$suffix@"] = '';
            $placeholders["@Guarantee_Contact_No$suffix@"] = '';
            $placeholders["@Guarantee_NIC$suffix@"] = '';
            $placeholders["@Guarantee_Address_Line_01$suffix@"] = '';
            $placeholders["@Guarantee_Address_Line_02$suffix@"] = '';
            $placeholders["@Guarantee_Address_Line_03$suffix@"] = '';
        }

        $Guarantor = 1;
        foreach ($witnessDetails as $witness) {
            $suffix = $Guarantor === 1 ? '' : "_$Guarantor";
            $placeholders["@Guarantee_First_Name$suffix@"] = $witness['details']->First_Name ?? '';
            $placeholders["@Guarantee_Last_Name$suffix@"] = $witness['details']->Last_Name ?? '';
            $placeholders["@Guarantee_Contact_No$suffix@"] = $witness['details']->Contact_No ?? '';
            $placeholders["@Guarantee_NIC$suffix@"] = $witness['details']->Nic ?? '';
            $placeholders["@Guarantee_Address_Line_01$suffix@"] = $witness['details']->Address ?? '';
            $placeholders["@Guarantee_Address_Line_02$suffix@"] = $witness['details']->Address_02 ?? '';
            $placeholders["@Guarantee_Address_Line_03$suffix@"] =  $witness['details']->Address_03 ?? '';

            $Guarantor++;

        }



//
        // Replace placeholders in the template
        $loan_number_txt = $agreement->template;
        foreach ($placeholders as $placeholder => $value) {
            $loan_number_txt = str_replace($placeholder, $value, $loan_number_txt);
        }

        // Fetch company details and encode images
        $company = DB::table('company')->first();
        $header_image = $footer_image = '';

        if ($company->company_header && Storage::disk('public')->exists($company->company_header)) {
            $header_image_path = storage_path('app/public/' . $company->company_header);
            $header_image = 'data:image/' . pathinfo($header_image_path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($header_image_path));
        }

        if ($company->company_footer && Storage::disk('public')->exists($company->company_footer)) {
            $footer_image_path = storage_path('app/public/' . $company->company_footer);
            $footer_image = 'data:image/' . pathinfo($footer_image_path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($footer_image_path));
        }


        return view('pages.AgreementView',compact('loan_number_txt', 'company', 'header_image', 'footer_image'));
    }




    public function load_agreement_doc(){
        $agreement = tableWithBranch('agreement_type')
            ->where('status', '=', '1')
            ->get();
        return response()->json(['agreement' => $agreement], 200);
    }


}
