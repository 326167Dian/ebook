<?php

namespace App\Console\Commands;

use App\Services\GdcPayService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Runs the 18 UAT/certification test cases GDC Pay requires before production
 * access ("Test Case API.pdf"). This is a one-off verification tool, not part
 * of the e-book app itself - the app only ever uses QRIS (TC.012/TC.013).
 */
class GdcUatTestCases extends Command
{
    protected $signature = 'gdc:uat';

    protected $description = 'Run GDC Pay UAT test cases TC.001-TC.018 against the sandbox and produce a report';

    /** @var array<int, array<string, mixed>> */
    private array $rows = [];

    public function handle(GdcPayService $service): int
    {
        $this->info('Menjalankan 18 test case GDC Pay ke sandbox...');
        $this->newLine();

        $this->tcToken($service);
        $this->tcBalanceAndBank($service);
        $this->tcTransferBank($service);
        $this->tcQris($service);
        $this->tcVirtualAccount($service);
        $this->tcTransactionHistory($service);

        $this->table(
            ['TC', 'Module', 'Scenario', 'Expected', 'Actual', 'Match?'],
            array_map(fn ($r) => [
                $r['tc'], $r['module'], $r['scenario'], $r['expected_code'],
                $r['actual_code'] . ($r['actual_message'] ? ' / ' . $r['actual_message'] : ''),
                $r['match'] ? 'YA' : 'TIDAK',
            ], $this->rows)
        );

        $this->writeReport();

        return self::SUCCESS;
    }

    private function tcToken(GdcPayService $service): void
    {
        // TC.001 - valid credentials
        $resp = $service->rawAccessTokenCall();
        $this->record('TC.001', 'Token', 'Generate Access Token dengan credential yang valid', '2007300', $resp, $service);

        // TC.002 - invalid Client ID
        $resp = $service->rawAccessTokenCall(clientKeyOverride: (string) Str::uuid());
        $this->record('TC.002', 'Token', 'Generate Access Token dengan Client ID tidak valid', '4017300', $resp, $service);

        // TC.003 - invalid signature
        $resp = $service->rawAccessTokenCall(signatureOverride: base64_encode('not-a-real-signature'));
        $this->record('TC.003', 'Token', 'Invalid Signature', '4017300', $resp, $service);
    }

    private function tcBalanceAndBank(GdcPayService $service): void
    {
        $accountNumber = config('gdcpay.account_number');

        // TC.004 - balance inquiry
        $ref = (string) Str::uuid();
        $resp = $service->rawSnapCall('/v1.0/balance-inquiry', [
            'partnerReferenceNo' => $ref,
            'accountNo' => $accountNumber,
            'additionalInfo' => (object) [],
        ], (string) Str::uuid());
        $this->record('TC.004', 'Balance Inquiry', 'Check Balance Inquiry', '2001100', $resp, $service);

        // TC.005 - bank account inquiry success. Our own e-money account number is NOT
        // a real external bank account, so GDC's switch rejects it ("Invalid Card/Account
        // /Customer"). TC.005/006/008-011 all need a real sandbox test bank account
        // number from GDC support - there is none in the docs to fall back to.
        $ref = (string) Str::uuid();
        $resp = $service->rawSnapCall('/v1.0/emoney/bank-account-inquiry', [
            'partnerReferenceNo' => $ref,
            'customerNumber' => $accountNumber,
            'amount' => ['value' => '10000.00', 'currency' => 'IDR'],
            'beneficiaryAccountNumber' => $accountNumber,
            'additionalInfo' => ['beneficiaryBankCode' => '014', 'remarks' => 'UAT test'],
        ], (string) Str::uuid());
        $this->record('TC.005', 'Inquiry Bank Account', 'Account Inquiry berhasil', '2004200', $resp, $service);

        // TC.006 - unregistered bank code (999 per GDC's own test notes)
        $ref = (string) Str::uuid();
        $resp = $service->rawSnapCall('/v1.0/emoney/bank-account-inquiry', [
            'partnerReferenceNo' => $ref,
            'customerNumber' => $accountNumber,
            'amount' => ['value' => '10000.00', 'currency' => 'IDR'],
            'beneficiaryAccountNumber' => $accountNumber,
            'additionalInfo' => ['beneficiaryBankCode' => '999', 'remarks' => 'UAT test - invalid bank'],
        ], (string) Str::uuid());
        $this->record('TC.006', 'Inquiry Bank Account', 'Kode bank tidak terdaftar', '5004201', $resp, $service);

        // TC.007 - expired/invalid token
        $ref = (string) Str::uuid();
        $resp = $service->rawSnapCall('/v1.0/emoney/bank-account-inquiry', [
            'partnerReferenceNo' => $ref,
            'customerNumber' => $accountNumber,
            'amount' => ['value' => '10000.00', 'currency' => 'IDR'],
            'beneficiaryAccountNumber' => $accountNumber,
            'additionalInfo' => ['beneficiaryBankCode' => '014', 'remarks' => 'UAT test - expired token'],
        ], (string) Str::uuid(), tokenOverride: 'expired-or-invalid-token-' . Str::random(20));
        $this->record('TC.007', 'Inquiry Bank Account', 'Inquiry bank dengan token expired', '4014201', $resp, $service);
    }

    private function tcTransferBank(GdcPayService $service): void
    {
        $accountNumber = config('gdcpay.account_number');

        // TC.008 - transfer success
        $ref = (string) Str::uuid();
        $resp = $service->rawSnapCall('/v1.0/emoney/transfer-bank', [
            'partnerReferenceNo' => $ref,
            'customerNumber' => $accountNumber,
            'amount' => ['value' => '10000.00', 'currency' => 'IDR'],
            'beneficiaryAccountNumber' => $accountNumber,
            'beneficiaryBankCode' => '014',
            'additionalInfo' => ['remarks' => 'UAT test transfer'],
        ], (string) Str::uuid());
        $this->record('TC.008', 'Transfer Bank', 'Transfer Berhasil', '2004300', $resp, $service);

        // TC.009 - amount below minimum
        $ref = (string) Str::uuid();
        $resp = $service->rawSnapCall('/v1.0/emoney/transfer-bank', [
            'partnerReferenceNo' => $ref,
            'customerNumber' => $accountNumber,
            'amount' => ['value' => '1.00', 'currency' => 'IDR'],
            'beneficiaryAccountNumber' => $accountNumber,
            'beneficiaryBankCode' => '014',
            'additionalInfo' => ['remarks' => 'UAT test - below minimum'],
        ], (string) Str::uuid());
        $this->record('TC.009', 'Transfer Bank', 'nominal transaksi dibawah minimum amount transaksi', '4004300', $resp, $service);

        // TC.010 - unregistered bank code
        $ref = (string) Str::uuid();
        $resp = $service->rawSnapCall('/v1.0/emoney/transfer-bank', [
            'partnerReferenceNo' => $ref,
            'customerNumber' => $accountNumber,
            'amount' => ['value' => '10000.00', 'currency' => 'IDR'],
            'beneficiaryAccountNumber' => $accountNumber,
            'beneficiaryBankCode' => '999',
            'additionalInfo' => ['remarks' => 'UAT test - invalid bank'],
        ], (string) Str::uuid());
        $this->record('TC.010', 'Transfer Bank', 'kode bank tidak terdaftar', '5004301', $resp, $service);

        // TC.011 - exceeds balance
        $ref = (string) Str::uuid();
        $resp = $service->rawSnapCall('/v1.0/emoney/transfer-bank', [
            'partnerReferenceNo' => $ref,
            'customerNumber' => $accountNumber,
            'amount' => ['value' => '999999999999.00', 'currency' => 'IDR'],
            'beneficiaryAccountNumber' => $accountNumber,
            'beneficiaryBankCode' => '014',
            'additionalInfo' => ['remarks' => 'UAT test - exceeds balance'],
        ], (string) Str::uuid());
        $this->record('TC.011', 'Transfer Bank', 'transfer melebihi balance', '4034314', $resp, $service);
    }

    private function tcQris(GdcPayService $service): void
    {
        $partnerRef = (string) Str::uuid();
        $externalId = (string) Str::uuid();

        // TC.012 - generate dynamic QRIS
        $resp = $service->rawSnapCall('/v1.0/qr/qr-mpm-generate', [
            'partnerReferenceNo' => $partnerRef,
            'amount' => ['value' => '10000.00', 'currency' => 'IDR'],
            'merchantId' => config('gdcpay.merchant_id'),
            'additionalInfo' => ['accountNumber' => config('gdcpay.account_number')],
        ], $externalId);
        $this->record('TC.012', 'QRIS', 'Generate QRIS DYNAMIC Berhasil', '2004700', $resp, $service);

        $gdcReferenceNo = (string) ($resp->json('referenceNo') ?? '');

        // TC.013 - check QRIS status
        $resp = $service->rawSnapCall('/v1.0/qr/qr-mpm-query', [
            'originalReferenceNo' => $gdcReferenceNo,
            'originalPartnerReferenceNo' => $partnerRef,
            'originalExternalId' => $externalId,
            'serviceCode' => '47',
            'additionalInfo' => (object) [],
        ], (string) Str::uuid());
        $this->record('TC.013', 'QRIS', 'Check Status QRIS', '2005100', $resp, $service);
    }

    private function tcVirtualAccount(GdcPayService $service): void
    {
        $trxId = (string) Str::uuid();

        // TC.014 - create VA success
        $resp = $service->rawSnapCall('/v1.0/transfer-va/create-va', [
            'virtualAccountName' => 'UAT Test Account',
            'expiredDate' => now()->addDay()->format('Y-m-d\TH:i:sP'),
            'trxId' => $trxId,
            'virtualAccountTrxType' => 'C',
            'totalAmount' => ['value' => '25000.00', 'currency' => 'IDR'],
            'additionalInfo' => ['accountNumber' => config('gdcpay.account_number'), 'bankCode' => '022'],
        ], (string) Str::uuid());
        $this->record('TC.014', 'Virtual Account', 'Create virtual account berhasil', '2002700', $resp, $service);

        $virtualAccountNo = (string) ($resp->json('virtualAccountData.virtualAccountNo') ?? '');
        $paymentRequestId = (string) ($resp->json('virtualAccountData.additionalInfo.referenceNo') ?? '');

        // TC.015 - create VA fail (missing required totalAmount)
        $resp = $service->rawSnapCall('/v1.0/transfer-va/create-va', [
            'virtualAccountName' => 'UAT Test Account Invalid',
            'expiredDate' => now()->addDay()->format('Y-m-d\TH:i:sP'),
            'trxId' => (string) Str::uuid(),
            'virtualAccountTrxType' => 'C',
            'additionalInfo' => ['accountNumber' => config('gdcpay.account_number'), 'bankCode' => '022'],
        ], (string) Str::uuid());
        $this->record('TC.015', 'Virtual Account', 'Crate Virtual account gagal', '4002700', $resp, $service);

        // TC.016 - check VA status
        $resp = $service->rawSnapCall('/v1.0/transfer-va/status', [
            'virtualAccountNo' => $virtualAccountNo,
            'paymentRequestId' => $paymentRequestId,
            'inquiryRequestId' => '',
        ], (string) Str::uuid());
        $this->record('TC.016', 'Virtual Account', 'Check Status Virtual account', '2002600', $resp, $service);
    }

    private function tcTransactionHistory(GdcPayService $service): void
    {
        $accountNumber = config('gdcpay.account_number');

        // TC.017 - transaction history list
        // NOTE: GDC requires additionalInfo.accountType but its accepted values are
        // undocumented - every common SNAP enum we tried (CASA, SAVINGS, 01, etc.)
        // was rejected with "Bad Request [AccountType]". Needs confirmation from GDC.
        $resp = $service->rawSnapCall('/v1.0/transaction-history-list', [
            'fromDateTime' => now()->subDays(7)->format('Y-m-d\TH:i:sP'),
            'toDateTime' => now()->format('Y-m-d\TH:i:sP'),
            'pageSize' => 10,
            'pageNumber' => 1,
            'additionalInfo' => ['accountNumber' => $accountNumber, 'accountType' => 'CASA', 'status' => '', 'type' => 'PAYMENT'],
        ], (string) Str::uuid());
        $this->record('TC.017', 'Transaction History', 'Transaction history List', '2001200', $resp, $service);

        $firstRefNo = (string) ($resp->json('detailData.0.additionalInfo.referenceNo') ?? '');

        // TC.018 - transaction history detail
        $resp = $service->rawSnapCall('/v1.0/transaction-history-detail', [
            'originalPartnerReferenceNo' => $firstRefNo,
            'originalReferenceNo' => '',
            'additionalInfo' => ['accountNumber' => $accountNumber, 'accountType' => 'CASA'],
        ], (string) Str::uuid());
        $this->record('TC.018', 'Transaction History', 'Transaction history Detail', '2001300', $resp, $service);
    }

    private function record(string $tc, string $module, string $scenario, string $expectedCode, Response $response, GdcPayService $service): void
    {
        $data = $response->json() ?? [];
        $actualCode = (string) ($data['responseCode'] ?? '');
        $actualMessage = (string) ($data['responseMessage'] ?? '');

        $this->rows[] = [
            'tc' => $tc,
            'module' => $module,
            'scenario' => $scenario,
            'expected_code' => $expectedCode,
            'actual_code' => $actualCode ?: ('HTTP ' . $response->status()),
            'actual_message' => $actualMessage,
            'match' => $actualCode === $expectedCode,
            'http_status' => $response->status(),
            'raw_response' => $response->body(),
            'request' => $service->lastRequestDebug,
        ];

        $icon = $actualCode === $expectedCode ? '✓' : '✗';
        $this->line("[{$icon}] {$tc} {$scenario}: expected {$expectedCode}, got {$actualCode} {$actualMessage}");
    }

    private function writeReport(): void
    {
        $lines = [];
        $lines[] = '# Laporan UAT GDC Pay - ' . now()->format('Y-m-d H:i');
        $lines[] = '';
        $lines[] = 'Setiap test case di bawah mengikuti kolom Doc UAT GDC: No/Module/Scenario/Expected Result/Request/Response/Result/Notes.';
        $lines[] = '';

        foreach ($this->rows as $r) {
            $req = $r['request'];

            $lines[] = "## {$r['tc']} - {$r['module']}: {$r['scenario']}";
            $lines[] = '';
            $lines[] = '**Expected Result**: responseCode `' . $r['expected_code'] . '`';
            $lines[] = '';
            $lines[] = '**Request**';
            $lines[] = '';

            if ($req) {
                $lines[] = '- URL Endpoint: `' . $req['url'] . '`';
                $lines[] = '- Header Request:';
                $lines[] = '```';
                foreach ($req['headers'] as $name => $value) {
                    $lines[] = "{$name}: {$value}";
                }
                $lines[] = '```';
                $lines[] = '- Request Body:';
                $lines[] = '```json';
                $lines[] = $this->prettyJson($req['body']);
                $lines[] = '```';
            } else {
                $lines[] = '_(request tidak tercatat)_';
            }

            $lines[] = '';
            $lines[] = '**Response**';
            $lines[] = '';
            $lines[] = '- HTTP Status: ' . $r['http_status'];
            $lines[] = '- Response Body:';
            $lines[] = '```json';
            $lines[] = $this->prettyJson($r['raw_response']);
            $lines[] = '```';
            $lines[] = '';
            $lines[] = '**Result**: ' . ($r['match'] ? 'PASS' : 'BLOCKED/TIDAK SESUAI');
            $lines[] = '';
            $lines[] = '**Notes**: Actual responseCode `' . $r['actual_code'] . '` (' . $r['actual_message'] . ')';
            $lines[] = '';
            $lines[] = '---';
            $lines[] = '';
        }

        $path = 'gdc-uat-report-' . now()->format('Ymd-His') . '.md';
        Storage::disk('local')->put($path, implode("\n", $lines));

        $this->newLine();
        $this->info('Laporan lengkap tersimpan di: storage/app/private/' . $path);
    }

    private function prettyJson(string $json): string
    {
        $decoded = json_decode($json);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $json;
        }

        return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
