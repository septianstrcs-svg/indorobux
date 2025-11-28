<?php
// ==== KONFIGURASI ====
$privateKey = '6EweE-LeCmr-iAS2w-h8Sjd-Ywv96'; // Ganti dengan private key kamu

// ==== AMBIL DATA DARI TRIPAY ====
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    http_response_code(400);
    exit('Invalid callback data');
}

// ==== VERIFIKASI SIGNATURE ====
$signature = hash_hmac('sha256', $data['merchant_ref'] . $data['status'], $privateKey);
if ($signature !== $data['signature']) {
    http_response_code(403);
    exit('Invalid signature');
}

// ==== CEK STATUS PEMBAYARAN ====
if ($data['status'] === 'PAID') {
    // Di sini kamu bisa update saldo user, ubah status di database, dll.
    // Contoh log sederhana:
    file_put_contents('callback_log.txt', date('Y-m-d H:i:s') . " - Pembayaran sukses: {$data['merchant_ref']}\n", FILE_APPEND);
} else {
    file_put_contents('callback_log.txt', date('Y-m-d H:i:s') . " - Status: {$data['status']}\n", FILE_APPEND);
}

http_response_code(200);
echo json_encode(['success' => true]);
?>
