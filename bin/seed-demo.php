<?php

declare(strict_types=1);

/*
 * Resets the database and fills it with realistic DEMO data for local testing:
 * admin + customer logins, ~30 days of orders, bookings and contact messages.
 *
 *   php bin/seed-demo.php           # asks for confirmation
 *   php bin/seed-demo.php --force
 *
 * Every demo account uses the password: password123
 * NEVER run this against a production database.
 */

if (PHP_SAPI !== 'cli') {
    exit("Run this script from the command line.\n");
}

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Config;
use App\Core\Database;
use App\Support\BookingCalendar;

const DEMO_PASSWORD = 'password123';

if (Config::get('app.env') === 'production') {
    fwrite(STDERR, "Refusing to seed demo data when APP_ENV=production.\n");
    exit(1);
}

if (!in_array('--force', $argv, true)) {
    echo "This DELETES all data in `" . Config::get('database.database') . "` and loads demo data.\nType 'yes' to continue: ";
    if (trim((string) fgets(STDIN)) !== 'yes') {
        exit("Aborted.\n");
    }
}

mt_srand(2005); // same demo data every run

// ---- 1. Fresh schema + catalogue -------------------------------------------

$pdo = Database::connection();
foreach (['schema.sql', 'seed.sql'] as $file) {
    $pdo->exec((string) file_get_contents(BASE_PATH . '/database/' . $file));
}
echo "✓ Schema and catalogue loaded\n";

// ---- 1b. Product photos (database/demo-images/<product-slug>.jpg|png) -----------

$uploadDir = BASE_PATH . '/public/' . config('uploads.products_dir');
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
foreach (glob($uploadDir . '/demo-*') ?: [] as $old) {
    unlink($old);
}

$photos = 0;
foreach (glob(BASE_PATH . '/database/demo-images/*.{jpg,png}', GLOB_BRACE) ?: [] as $src) {
    $slug = pathinfo($src, PATHINFO_FILENAME);
    $file = 'demo-' . basename($src);
    if (copy($src, $uploadDir . '/' . $file)) {
        $photos += Database::query('UPDATE products SET image = ? WHERE slug = ?', [$file, $slug])->rowCount();
    }
}
echo "✓ $photos product photos\n";

$hash = password_hash(DEMO_PASSWORD, PASSWORD_DEFAULT);
$ts = static fn (int $daysAgo, int $hour = 12, int $min = 0): string
    => date('Y-m-d H:i:s', strtotime("-$daysAgo days " . sprintf('%02d:%02d', $hour, $min)));

// ---- 2. Users ---------------------------------------------------------------

Database::insert('users', [
    'name' => 'Hafiz Taikon', 'email' => 'admin@taikon.test', 'phone' => '012-555 0101',
    'password_hash' => $hash, 'role' => 'admin', 'created_at' => $ts(400),
]);

$customers = [
    ['Ali Hassan',          'ali@taikon.test',       '012-345 6789', 'No. 12, Jalan Mawar 3, Taman Bunga Raya', 'Rawang',        '48000', 'Selangor'],
    ['Nurul Aisyah',        'aisyah@example.test',   '013-221 8890', '8, Jalan Kenanga 2, Bandar Country Homes', 'Rawang',       '48000', 'Selangor'],
    ['Muhammad Firdaus',    'firdaus@example.test',  '017-889 1203', '22, Lorong Kuang Jaya 5',                  'Kuang',        '48050', 'Selangor'],
    ['Tan Wei Ming',        'weiming@example.test',  '016-772 3341', '15, Jalan SS2/24',                         'Petaling Jaya','47300', 'Selangor'],
    ['Rajesh Kumar',        'rajesh@example.test',   '019-334 5521', '3A, Jalan Sungai Buloh 7',                 'Sungai Buloh', '47000', 'Selangor'],
    ['Siti Hajar',          'hajar@example.test',    '011-2345 6677','47, Jalan Desa Jaya 9',                    'Kepong',       '52100', 'Kuala Lumpur'],
    ['Amirul Hakim',        'amirul@example.test',   '014-667 2210', '9, Jalan Seri Kundang 4',                  'Kuang',        '48020', 'Selangor'],
    ['Lim Kah Seng',        'kahseng@example.test',  '012-908 7765', '31, Jalan Bukit Beruntung 2',              'Rawang',       '48300', 'Selangor'],
];

$userIds = [];
foreach ($customers as $i => [$name, $email, $phone, $addr, $city, $pc, $state]) {
    $userIds[] = Database::insert('users', [
        'name' => $name, 'email' => $email, 'phone' => $phone, 'password_hash' => $hash, 'role' => 'customer',
        'address_line' => $addr, 'city' => $city, 'postcode' => $pc, 'state' => $state,
        'created_at' => $ts(120 - $i * 12, 10),
    ]);
}
echo '✓ ' . (count($userIds) + 1) . " users\n";

// ---- 3. Orders ----------------------------------------------------------------

$products = Database::all('SELECT id, name, sku, price, stock FROM products');
$stock = array_column($products, 'stock', 'id');

// [daysAgo, customerIndex, status, fulfilment, payment, [[productId, qty], …], notes]
$orders = [
    [29, 0, 'completed', 'delivery', 'cod',           [[1, 2], [5, 2]],   null],
    [27, 3, 'completed', 'delivery', 'bank_transfer', [[10, 1]],          'Please call before delivery.'],
    [25, 2, 'completed', 'pickup',   'pay_at_store',  [[2, 1], [3, 1]],   'Will collect after work.'],
    [23, 1, 'completed', 'delivery', 'cod',           [[12, 1], [14, 1]], null],
    [21, 4, 'cancelled', 'delivery', 'bank_transfer', [[6, 1]],           'Ordered wrong size, sorry.'],
    [19, 5, 'completed', 'delivery', 'bank_transfer', [[13, 1], [15, 2]], null],
    [17, 6, 'completed', 'pickup',   'pay_at_store',  [[16, 1]],          null],
    [15, 7, 'completed', 'delivery', 'cod',           [[7, 1], [11, 1]],  'Y15ZR 2020 model.'],
    [13, 0, 'completed', 'pickup',   'pay_at_store',  [[4, 2], [17, 1]],  null],
    [11, 3, 'completed', 'delivery', 'bank_transfer', [[9, 1], [8, 1]],   null],
    [9,  1, 'shipped',   'delivery', 'cod',           [[15, 1], [14, 2]], null],
    [8,  2, 'completed', 'pickup',   'pay_at_store',  [[18, 1]],          null],
    [6,  4, 'shipped',   'delivery', 'bank_transfer', [[12, 1], [13, 1]], 'Leave with guard house.'],
    [5,  5, 'processing','delivery', 'cod',           [[1, 1], [2, 1]],   null],
    [4,  7, 'processing','delivery', 'bank_transfer', [[6, 1], [3, 1]],   'Sport Bonus SR 2019.'],
    [3,  6, 'shipped',   'delivery', 'cod',           [[11, 2]],          null],
    [2,  0, 'processing','pickup',   'pay_at_store',  [[5, 4], [4, 1]],   null],
    [1,  2, 'pending',   'delivery', 'bank_transfer', [[7, 1]],           'Can install too? Will book service.'],
    [1,  3, 'pending',   'delivery', 'cod',           [[10, 1], [8, 1]],  null],
    [0,  1, 'pending',   'pickup',   'pay_at_store',  [[17, 1], [16, 1]], null],
    [0,  5, 'pending',   'delivery', 'cod',           [[14, 1]],          null],
];

$priceOf = array_column($products, null, 'id');
$freeOver = (float) config('shipping.free_over');
$flat = (float) config('shipping.flat_rate');

foreach ($orders as $n => [$daysAgo, $ci, $status, $fulfilment, $payment, $lines, $notes]) {
    [$name, $email, $phone, $addr, $city, $pc, $state] = $customers[$ci];
    $created = $ts($daysAgo, 9 + ($n % 11), ($n * 17) % 60);

    $subtotal = 0.0;
    foreach ($lines as [$pid, $qty]) {
        $subtotal += (float) $priceOf[$pid]['price'] * $qty;
    }
    $shipping = $fulfilment === 'pickup' || $subtotal >= $freeOver ? 0.0 : $flat;
    $pickup = $fulfilment === 'pickup';

    $orderId = Database::insert('orders', [
        'order_number'   => 'TM' . date('ymd', strtotime($created)) . '-' . strtoupper(bin2hex(random_bytes(2))),
        'user_id'        => $userIds[$ci],
        'customer_name'  => $name, 'email' => $email, 'phone' => $phone,
        'fulfilment'     => $fulfilment,
        'address_line'   => $pickup ? null : $addr,
        'city'           => $pickup ? null : $city,
        'postcode'       => $pickup ? null : $pc,
        'state'          => $pickup ? null : $state,
        'payment_method' => $payment,
        'subtotal'       => $subtotal, 'shipping_fee' => $shipping, 'total' => $subtotal + $shipping,
        'status'         => $status,
        'notes'          => $notes,
        'created_at'     => $created,
        'updated_at'     => $created,
    ]);

    foreach ($lines as [$pid, $qty]) {
        $p = $priceOf[$pid];
        Database::insert('order_items', [
            'order_id' => $orderId, 'product_id' => $pid, 'product_name' => $p['name'], 'sku' => $p['sku'],
            'unit_price' => $p['price'], 'quantity' => $qty, 'line_total' => (float) $p['price'] * $qty,
        ]);
        if ($status !== 'cancelled') {
            $stock[$pid] = max(0, $stock[$pid] - $qty);
        }
    }
}

foreach ($stock as $pid => $qty) {
    Database::query('UPDATE products SET stock = ? WHERE id = ?', [$qty, $pid]);
}
echo '✓ ' . count($orders) . " orders (stock adjusted)\n";

// ---- 4. Bookings ---------------------------------------------------------------

$services = array_column(Database::all('SELECT id, name FROM services'), 'name', 'id');
$bikes = [
    ['Yamaha Y15ZR 2021', 'BME 4521'], ['Honda RS150R', 'WXY 8812'], ['Yamaha LC135 2018', 'BKT 3307'],
    ['Kawasaki Z1000 2016', 'W 1000 Z'], ['SYM Sport Bonus SR', 'BPA 6620'], ['Honda C70 (1982)', 'B 7070'],
    ['Benelli TNT600', 'VBA 600'], ['Modenas Kriss 110', 'BJN 1102'],
];

// [dayOffset (negative = past), customerIndex or null for guest, serviceId, slotIndex, status, notes]
$bookings = [
    [-26, 0, 1, 0, 'completed', 'Engine oil 10W-40 please.'],
    [-22, 3, 2, 1, 'completed', 'Valve noise when cold.'],
    [-19, 6, 4, 2, 'completed', 'Install slip-on bought here.'],
    [-15, null, 1, 0, 'completed', null],
    [-12, 4, 3, 3, 'cancelled', null],
    [-9,  5, 5, 0, 'completed', 'Consultation for cafe racer build.'],
    [-6,  1, 1, 4, 'completed', null],
    [-3,  7, 3, 1, 'completed', 'Rear shock feels too soft.'],
    [-1,  2, 2, 2, 'completed', null],
    [1,   0, 4, 0, 'confirmed', 'Fitting LED headlight from order.'],
    [1,   null, 1, 1, 'pending', 'First service after purchase.'],
    [2,   3, 6, 0, 'confirmed', 'Top-end rebuild, smoking on startup.'],
    [3,   1, 1, 2, 'pending', null],
    [4,   6, 3, 3, 'pending', 'Brake lever spongy.'],
    [6,   5, 1, 1, 'pending', null],
    [9,   7, 5, 0, 'pending', 'Street cub project — bring photos.'],
];
$guests = [['Zulkifli Omar', 'zul@example.test', '018-776 5432'], ['Kavitha Devi', 'kavitha@example.test', '012-443 9876']];
$g = 0;

foreach ($bookings as $n => [$offset, $ci, $sid, $slotIdx, $status, $notes]) {
    $date = date('Y-m-d', strtotime(($offset >= 0 ? '+' : '') . $offset . ' days'));
    $slots = BookingCalendar::slotsFor($date);
    $time = $slots[$slotIdx % count($slots)];
    [$bike, $plate] = $bikes[$n % count($bikes)];

    if ($ci === null) {
        [$name, $email, $phone] = $guests[$g++ % count($guests)];
    } else {
        [$name, $email, $phone] = $customers[$ci];
    }

    $created = $ts(max(0, -$offset) + 3, 20, $n * 3);
    Database::insert('bookings', [
        'booking_number' => 'BK' . date('ymd', strtotime($created)) . '-' . strtoupper(bin2hex(random_bytes(2))),
        'user_id'        => $ci === null ? null : $userIds[$ci],
        'service_id'     => $sid,
        'service_name'   => $services[$sid],
        'customer_name'  => $name, 'email' => $email, 'phone' => $phone,
        'motorcycle'     => $bike, 'plate_number' => $plate,
        'booking_date'   => $date, 'booking_time' => $time . ':00',
        'notes'          => $notes, 'status' => $status,
        'created_at'     => $created, 'updated_at' => $created,
    ]);
}
echo '✓ ' . count($bookings) . " bookings\n";

// ---- 5. Contact messages --------------------------------------------------------

$messages = [
    [12, 1, 'Faizal Rahman',  'faizal@example.test', '013-998 1122', 'Sprocket 15T for LC135?', "Salam boss, ada stock front sprocket 15T untuk LC135? Nak naikkan top speed sikit. Terima kasih."],
    [8,  1, 'Jason Lee',      'jason@example.test',  null,           'Quote for Z900 frame sliders', "Hi, do you carry frame sliders for a 2022 Kawasaki Z900? If not, can you order them in and how long would it take?"],
    [5,  1, 'Aminah Yusof',   'aminah@example.test', '017-334 2219', 'Helmet sizing',          "My head is about 57cm. Which size of the MHR full-face helmet should I take? Can I try it at the shop?"],
    [2,  0, 'Daniel Wong',    'daniel@example.test', '016-552 7781', 'Custom build budget',    "I have a 1985 Honda C70 sitting at home. Roughly what budget should I prepare for a street cub build like the one on your site? Would love to come by on Saturday."],
    [1,  0, 'Suresh Pillai',  'suresh@example.test', null,           'Order delivery time',    "Hi, how many days does delivery to Ipoh usually take? Thinking of ordering the torque wrench and chain cleaning kit."],
    [0,  0, 'Hanis Zulaikha', 'hanis@example.test',  '011-1234 5566','Wholesale for workshop', "We run a small workshop in Batang Kali. Do you offer trade prices for brake pads and oil filters in bulk? Please email me a price list."],
];
foreach ($messages as [$daysAgo, $read, $name, $email, $phone, $subject, $body]) {
    Database::insert('messages', [
        'name' => $name, 'email' => $email, 'phone' => $phone, 'subject' => $subject, 'body' => $body,
        'is_read' => $read, 'created_at' => $ts($daysAgo, 8 + $daysAgo % 10, 25),
    ]);
}
echo '✓ ' . count($messages) . " messages\n";

echo <<<TXT

Demo data ready. Password for every account: password123

  Admin     admin@taikon.test   → /admin
  Customer  ali@taikon.test     → has orders and bookings
  Others    aisyah@example.test, firdaus@example.test, weiming@example.test, …

TXT;
