<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reschedule extends Model
{
    use HasFactory;

    protected $table = 'reschedule';        // exact table name
    protected $primaryKey = 'idReschedule'; // PK of reschedule table
    public $timestamps = false;

    // columns you allow to be mass assigned
    protected $fillable = [
        'original_loan_id',
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
        'repayment_duration',
        'loan_broker',
        'loan_broker_commission',
        'saving_amount',
        'branch_id',

        // audit
        'rescheduled_by',
        'rescheduled_at',
        'reschedule_type',
    ];
}
