<?php
// ==== KONFIGURASI TRIPAY ====
$apiKey       = 'DEV-vuVGfZYIwhmwC9XyPcp0Of3plcyKl9UCL1y3lpfA'; // Ganti dengan API key kamu
$privateKey   = '6EweE-LeCmr-iAS2w-h8Sjd-Ywv96';    // Ganti dengan private key kamu
$merchantCode = T46829'';               // Ganti dengan kode merchant kamu
$apiUrl       = 'https://tripay.co.id/api-sandbox/transaction/create';

// ==== AMBIL DATA DARI FORM ====
$method = $_POST['method'];
$amount = $_POST['amount'];
$name   = $_POST['name'];
$email  = $_POST['email'];
$phone  = $_POST['phone'];
$invoice = 'INV' . time();

// ==== DATA TRANSAKSI ====
$data = [
    'method'         => $method,
    'merchant_ref'   => $invoice,
    'amount'         => $amount,
    'customer_name'  => $name,
    'customer_email' => $email,
    'customer_phone' => $phone,
    'order_items'    => [
        [
            'sku'         => 'TOPUP-' . rand(100, 999),
            'name'        => 'Topup Saldo',
            'price'       => $amount,
            'quantity'    => 1,
        ]
    ],
    'callback_url'   => 'https://indorobux.com/callback.php', // Ganti dengan URL callback kamu
    'return_url'     => 'https://indorobux.com/success.html', // Ganti dengan URL setelah pembayaran
    'expired_time'   => (time() + (24 * 60 * 60)), // 24 jam
    'signature'      => hash_hmac('sha256', $merchantCode . $invoice . $amount, $privateKey)
];

// ==== CURL REQUEST ====
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_FRESH_CONNECT  => true,
    CURLOPT_URL            => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER         => false,
    CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
    CURLOPT_FAILONERROR    => false,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query($data),
    CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
]);

$response = curl_exec($curl);
$error = curl_error($curl);
curl_close($curl);

if ($error) {
    echo "Curl Error: " . $error;
} else {
    $result = json_decode($response, true);
    echo json_encode($result, JSON_PRETTY_PRINT);
}
?>
