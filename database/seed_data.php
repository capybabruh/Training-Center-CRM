<?php
// database/seed_data.php
// Sinh 200 leads + 200 orders de test pagination, EXPLAIN, index
// Chay: php database/seed_data.php

require __DIR__ . '/../app/Core/Database.php';
$config = require __DIR__ . '/../config/database.php';
$pdo = Database::connect($config);

$firstNames = ['An','Binh','Chi','Dung','Ha','Hung','Lan','Mai','Nam','Phuong',
               'Quang','Thu','Tuan','Uyen','Viet','Xuan','Yen','Khoa','Linh','Minh',
               'Bao','Cam','Duc','Giang','Hieu','Khanh','Long','My','Nga','Oanh'];
$lastNames  = ['Nguyen','Tran','Le','Pham','Hoang','Huynh','Phan','Vu','Dang','Bui',
               'Do','Ho','Ngo','Duong','Ly','Dinh','Truong','Vo','Dao','Luu'];
$courses    = ['PHP Web Development','Java Backend','Python AI','UI/UX Design',
               'Data Analytics','DevOps Essentials','Mobile Flutter','React Frontend'];
$sources    = ['facebook','google','zalo','walk-in','referral','tiktok'];
$leadStatuses = ['new','contacted','qualified','lost'];
$orderStatuses = ['pending','paid','partial','cancelled'];
$coursePrices  = [8500000,9200000,12000000,7500000,11000000,9800000,10500000,8800000];
$payMethods    = ['cash','bank_transfer','card'];

$pdo->exec("DELETE FROM order_payments WHERE order_id IN (SELECT id FROM orders WHERE order_code LIKE 'SEED-%')");
$pdo->exec("DELETE FROM orders WHERE order_code LIKE 'SEED-%'");
$pdo->exec("DELETE FROM leads WHERE email LIKE '%@seed.test'");
echo "Cleaned old seed data.\n";

$stmtLead = $pdo->prepare(
    "INSERT INTO leads (name, email, phone, course_interest, status, note, source, created_at)
     VALUES (:name, :email, :phone, :course_interest, :status, :note, :source, :created_at)"
);
for ($i = 1; $i <= 200; $i++) {
    $first  = $firstNames[array_rand($firstNames)];
    $last   = $lastNames[array_rand($lastNames)];
    $days   = rand(0, 365);
    $stmtLead->execute([
        'name'            => "$last $first",
        'email'           => "lead{$i}@seed.test",
        'phone'           => '09' . str_pad((string)rand(0,99999999), 8, '0', STR_PAD_LEFT),
        'course_interest' => $courses[array_rand($courses)],
        'status'          => $leadStatuses[array_rand($leadStatuses)],
        'note'            => "Auto seed record #$i",
        'source'          => $sources[array_rand($sources)],
        'created_at'      => date('Y-m-d H:i:s', strtotime("-{$days} days")),
    ]);
}
echo "Inserted 200 leads.\n";

$stmtOrder = $pdo->prepare(
    "INSERT INTO orders (order_code, customer_name, customer_email, course_name, total_amount, paid_amount, status, created_at)
     VALUES (:order_code, :customer_name, :customer_email, :course_name, :total_amount, :paid_amount, :status, :created_at)"
);
$stmtPay = $pdo->prepare(
    "INSERT INTO order_payments (order_id, amount, method, paid_at)
     VALUES (:order_id, :amount, :method, :paid_at)"
);

for ($i = 1; $i <= 200; $i++) {
    $first  = $firstNames[array_rand($firstNames)];
    $last   = $lastNames[array_rand($lastNames)];
    $idx    = array_rand($coursePrices);
    $total  = $coursePrices[$idx];
    $status = $orderStatuses[array_rand($orderStatuses)];
    $paid   = match($status) {
        'paid'    => $total,
        'partial' => (int)($total * 0.5),
        default   => 0,
    };
    $days = rand(0, 365);
    $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));

    $pdo->beginTransaction();
    try {
        $stmtOrder->execute([
            'order_code'     => 'SEED-' . str_pad((string)$i, 5, '0', STR_PAD_LEFT),
            'customer_name'  => "$last $first",
            'customer_email' => "order{$i}@seed.test",
            'course_name'    => $courses[$idx],
            'total_amount'   => $total,
            'paid_amount'    => $paid,
            'status'         => $status,
            'created_at'     => $date,
        ]);
        $orderId = (int)$pdo->lastInsertId();
        if ($paid > 0) {
            $stmtPay->execute([
                'order_id' => $orderId,
                'amount'   => $paid,
                'method'   => $payMethods[array_rand($payMethods)],
                'paid_at'  => $date,
            ]);
        }
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error at order $i: " . $e->getMessage() . "\n";
    }
}
echo "Inserted 200 orders with payment records.\n";
echo "Done! Total: 220 leads, 220 orders (including 20 from seed.sql).\n";
