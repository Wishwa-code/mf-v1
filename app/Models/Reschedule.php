<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reschedule extends Model
{
    use HasFactory;

    protected $table = 'reshedule';

    // ⚠️ Ideally this table should have its own PK (e.g., idReschedule).
    // Using a loan PK prevents multiple reschedules per loan.
    // protected $primaryKey = 'idReschedule';

    // Turn timestamps on if you added both created_at/updated_at.
    // If you only added created_at, keep timestamps = false.
    public $timestamps = false; // <- set to true if you also added `updated_at`

    protected $fillable = [
        'Loan_No',
        'Loan_Category_idLoan_Category',
        'Customer_idCustomer',
        'Leasing_type',
        'Vehicle_No',
        'Date_Time',
        'Amount',
        'Interest_Rate',
        'Panalty_Rate',
        'Installment_Count',
        'Interest_Amount',
        'Total_Other_Amount',
        'Other_Amount_Balance',
        'Total_Loan_Amount',
        'Installment_Amount',
        'Collection_Type',
        'Collection_Date',
        'Panalty_Date',
        'Balance_Amount',
        'Status',
        'reason',
        'User_idUser',
        'capital_balance',
        'installment_balance',
        'type',
        'Interest_period',
        'cus_bank_account',
        'company_bank_account',
        'lending_officer_id',
        'collector_id',

        // NEW
        'loan_id',       // back reference to customer_loan.idCustomer_Loan
        'created_by',    // user id who did the reschedule
        'created_at',    // if timestamps=false and you manage it manually
        // 'updated_at',  // include only if you added it and use timestamps
    ];
}

