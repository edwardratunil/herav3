<?php

namespace App\Domain\Disbursement;

use App\Models\PayrollRun;
use RuntimeException;

/**
 * Philippine Bank Payroll Advice File Generator
 * Supports Landbank (LBP weAccess / Findes), BDO Unibank, BPI BizLink, and Metrobank.
 */
class BankExportEngine
{
    public const BANK_LANDBANK = 'landbank';
    public const BANK_BDO      = 'bdo';
    public const BANK_BPI      = 'bpi';
    public const BANK_METROBANK = 'metrobank';

    /**
     * Generate bank payroll advice file content.
     *
     * @param PayrollRun $run
     * @param string $bankCode
     * @param array{company_code?: string, batch_number?: string} $options
     * @return array{filename: string, content: string, mime_type: string, record_count: int, total_amount: string}
     */
    public function generate(PayrollRun $run, string $bankCode, array $options = []): array
    {
        $items = $run->items()->with('employee')->get();
        if ($items->isEmpty()) {
            throw new RuntimeException("Cannot generate bank advice file for empty payroll run {$run->id}");
        }

        return match (strtolower($bankCode)) {
            self::BANK_LANDBANK  => $this->generateLandbank($run, $items, $options),
            self::BANK_BDO       => $this->generateBdo($run, $items, $options),
            self::BANK_BPI       => $this->generateBpi($run, $items, $options),
            self::BANK_METROBANK => $this->generateMetrobank($run, $items, $options),
            default              => throw new RuntimeException("Unsupported bank format: {$bankCode}"),
        };
    }

    /**
     * Landbank of the Philippines (LBP) weAccess / Findes standard CSV/TXT format.
     * Common format: Account Number, Employee Name, Amount (2 decimals), Status/Remarks
     */
    protected function generateLandbank(PayrollRun $run, $items, array $options): array
    {
        $lines = [];
        $totalCents = 0;
        $dateFormatted = $run->payout_date ? $run->payout_date->format('Ymd') : now()->format('Ymd');
        $batchNo = $options['batch_number'] ?? '01';

        // Header line: H, CompanyCode, PayoutDate, BatchNo
        $companyCode = str_pad($options['company_code'] ?? 'LBP-001', 10, ' ', STR_PAD_RIGHT);
        $lines[] = "H,{$companyCode},{$dateFormatted},{$batchNo}";

        foreach ($items as $item) {
            $employee = $item->employee;
            $accountNumber = preg_replace('/[^0-9]/', '', (string)$employee->bank_account_encrypted);
            $accountNumber = str_pad($accountNumber ?: '0000000000', 10, '0', STR_PAD_LEFT);
            $name = str_pad(substr($employee->full_name, 0, 30), 30, ' ', STR_PAD_RIGHT);
            $netPay = number_format((float)$item->net_pay, 2, '.', '');
            $cents = (int) round(((float)$item->net_pay) * 100);
            $totalCents += $cents;

            // Detail record: D, AccountNumber, Name, Amount, EmployeeNumber
            $lines[] = "D,{$accountNumber},{$name},{$netPay},{$employee->employee_number}";
        }

        // Trailer record: T, RecordCount, TotalAmount
        $totalFormatted = number_format($totalCents / 100, 2, '.', '');
        $lines[] = "T," . count($items) . ",{$totalFormatted}";

        $filename = "LBP_PAYROLL_{$run->id}_{$dateFormatted}.txt";

        return [
            'filename'     => $filename,
            'content'      => implode("\r\n", $lines),
            'mime_type'    => 'text/plain',
            'record_count' => count($items),
            'total_amount' => $totalFormatted,
        ];
    }

    /**
     * BDO Corporate Payroll CSV format.
     */
    protected function generateBdo(PayrollRun $run, $items, array $options): array
    {
        $lines = [];
        $totalAmount = '0.0000';
        $payoutDate = $run->payout_date ? $run->payout_date->format('m/d/Y') : now()->format('m/d/Y');

        // BDO Standard Header
        $lines[] = "Account Number,Employee Name,Amount,Remarks,Payout Date";

        foreach ($items as $item) {
            $employee = $item->employee;
            $accountNumber = preg_replace('/[^0-9]/', '', (string)$employee->bank_account_encrypted) ?: '000000000000';
            $netPay = number_format((float)$item->net_pay, 2, '.', '');
            $totalAmount = bcadd($totalAmount, $netPay, 4);

            $cleanName = str_replace(',', ' ', $employee->full_name);
            $lines[] = "\"{$accountNumber}\",\"{$cleanName}\",{$netPay},\"Salary Payout\",{$payoutDate}";
        }

        $filename = "BDO_PAYROLL_{$run->id}.csv";

        return [
            'filename'     => $filename,
            'content'      => implode("\r\n", $lines),
            'mime_type'    => 'text/csv',
            'record_count' => count($items),
            'total_amount' => number_format((float)$totalAmount, 2, '.', ''),
        ];
    }

    /**
     * BPI BizLink Standard Payroll CSV format.
     */
    protected function generateBpi(PayrollRun $run, $items, array $options): array
    {
        $lines = [];
        $totalAmount = '0.0000';

        // BPI BizLink format: Account Number, Amount, Employee ID, Employee Name
        $lines[] = "ACCOUNT_NUMBER,AMOUNT,EMPLOYEE_ID,EMPLOYEE_NAME";

        foreach ($items as $item) {
            $employee = $item->employee;
            $accountNumber = preg_replace('/[^0-9]/', '', (string)$employee->bank_account_encrypted) ?: '0000000000';
            $netPay = number_format((float)$item->net_pay, 2, '.', '');
            $totalAmount = bcadd($totalAmount, $netPay, 4);

            $cleanName = str_replace('"', '""', $employee->full_name);
            $lines[] = "\"{$accountNumber}\",{$netPay},\"{$employee->employee_number}\",\"{$cleanName}\"";
        }

        $filename = "BPI_BIZLINK_{$run->id}.csv";

        return [
            'filename'     => $filename,
            'content'      => implode("\r\n", $lines),
            'mime_type'    => 'text/csv',
            'record_count' => count($items),
            'total_amount' => number_format((float)$totalAmount, 2, '.', ''),
        ];
    }

    /**
     * Metrobank Direct Banking format.
     */
    protected function generateMetrobank(PayrollRun $run, $items, array $options): array
    {
        $lines = [];
        $totalAmount = '0.0000';

        $lines[] = "MB_ACCOUNT,AMOUNT,NAME,REFERENCE";

        foreach ($items as $item) {
            $employee = $item->employee;
            $accountNumber = preg_replace('/[^0-9]/', '', (string)$employee->bank_account_encrypted) ?: '000000000000';
            $netPay = number_format((float)$item->net_pay, 2, '.', '');
            $totalAmount = bcadd($totalAmount, $netPay, 4);

            $lines[] = "{$accountNumber},{$netPay},\"{$employee->full_name}\",\"PR-{$run->id}\"";
        }

        $filename = "METROBANK_PAYROLL_{$run->id}.csv";

        return [
            'filename'     => $filename,
            'content'      => implode("\r\n", $lines),
            'mime_type'    => 'text/csv',
            'record_count' => count($items),
            'total_amount' => number_format((float)$totalAmount, 2, '.', ''),
        ];
    }
}

