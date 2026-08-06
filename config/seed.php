<?php
/**
 * Database Seeder
 * Run once from CLI:  php config/seed.php
 *
 * Seeds all demo data that was previously hard-coded in data.js.
 * Passwords are hashed with bcrypt.
 */

require_once __DIR__ . '/../bootstrap.php';

$pdo = DB::connection();

echo "🌱  Seeding MCYF database...\n\n";

// ── 1. Settings ───────────────────────────────────────────────────────────────
$settings = [
    'app_lang'       => 'ur',
    'app_name_ur'    => '(abc) کمیونٹی یوتھ فورم',
    'app_name_en'    => 'Masood Community Youth Forum',
    'app_subtitle'   => 'ایک غیر سیاسی، غیر منافع بخش تنظیم',
    'app_icon'       => 'bi-mosque',
    'theme_primary'  => '#145A32',
    'theme_secondary'=> '#0D3D22',
    'theme_accent'   => '#C9A227',
    'theme_extra'    => '',
];

$stmtSetting = $pdo->prepare(
    "INSERT INTO settings (`key`, `value`) VALUES (:k, :v)
     ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)"
);
foreach ($settings as $k => $v) {
    $stmtSetting->execute([':k' => $k, ':v' => $v]);
}
echo "✔  Settings seeded\n";

// ── 2. Members ────────────────────────────────────────────────────────────────
$members = [
    [
        'name'       => 'احمد علی (abc)',
        'father'     => 'محمد اسلم (abc)',
        'photo'      => 'https://i.pravatar.cc/150?img=12',
        'cnic'       => '42101-1234567-1',
        'mobile'     => '0300-1234567',
        'email'      => 'superadmin@masoodforum.org',
        'password'   => 'Super@123',
        'role'       => 'super_admin',
        'address'    => 'محلہ گلشن، جامشورو',
        'district'   => 'جامشورو',
        'tehsil'     => 'جامشورو',
        'village'    => '(abc) آباد',
        'education'  => 'ایم بی اے',
        'occupation' => 'بینکر',
        'blood'      => 'B+',
        'joined'     => '2021-03-10',
        'status'     => 'active',
        'position'   => 'چیئرمین',
    ],
    [
        'name'       => 'بلال حسین (abc)',
        'father'     => 'غلام حسین (abc)',
        'photo'      => 'https://i.pravatar.cc/150?img=13',
        'cnic'       => '42101-2234567-2',
        'mobile'     => '0301-2234567',
        'email'      => 'admin@masoodforum.org',
        'password'   => 'Admin@123',
        'role'       => 'admin',
        'address'    => 'وارڈ نمبر 3، جامشورو',
        'district'   => 'جامشورو',
        'tehsil'     => 'کوٹری',
        'village'    => 'حسین آباد',
        'education'  => 'بی ایس سی',
        'occupation' => 'ٹیچر',
        'blood'      => 'O+',
        'joined'     => '2021-05-22',
        'status'     => 'active',
        'position'   => 'وائس چیئرمین',
    ],
    [
        'name'       => 'زین العابدین (abc)',
        'father'     => 'عابدین (abc)',
        'photo'      => 'https://i.pravatar.cc/150?img=17',
        'cnic'       => '42101-6234567-6',
        'mobile'     => '0305-6234567',
        'email'      => 'committee@masoodforum.org',
        'password'   => 'Committee@123',
        'role'       => 'committee_head',
        'address'    => 'گرین ٹاؤن، جامشورو',
        'district'   => 'جامشورو',
        'tehsil'     => 'سہون',
        'village'    => 'عابد نگر',
        'education'  => 'بی ایس سی آئی ٹی',
        'occupation' => 'سافٹ ویئر انجینئر',
        'blood'      => 'O-',
        'joined'     => '2022-02-20',
        'status'     => 'active',
        'position'   => 'آئی ٹی سیکرٹری',
    ],
    [
        'name'       => 'طلحہ یوسف (abc)',
        'father'     => 'یوسف علی (abc)',
        'photo'      => 'https://i.pravatar.cc/150?img=18',
        'cnic'       => '42101-7234567-7',
        'mobile'     => '0306-7234567',
        'email'      => 'member@masoodforum.org',
        'password'   => 'Member@123',
        'role'       => 'member',
        'address'    => '(abc) روڈ، سہون',
        'district'   => 'جامشورو',
        'tehsil'     => 'سہون',
        'village'    => 'یوسف آباد',
        'education'  => 'انٹرمیڈیٹ',
        'occupation' => 'طالب علم',
        'blood'      => 'A-',
        'joined'     => '2022-04-05',
        'status'     => 'active',
        'position'   => 'رضاکار',
    ],
    [
        'name'       => 'عمران خالد (abc)',
        'father'     => 'خالد محمود (abc)',
        'photo'      => 'https://i.pravatar.cc/150?img=32',
        'cnic'       => '42101-1134567-1',
        'mobile'     => '0310-1134567',
        'email'      => 'pending@masoodforum.org',
        'password'   => 'Pending@123',
        'role'       => 'pending',
        'address'    => 'نئی آبادی، جامشورو',
        'district'   => 'جامشورو',
        'tehsil'     => 'جامشورو',
        'village'    => 'خالد پور',
        'education'  => 'بی ایس سی',
        'occupation' => 'طالب علم',
        'blood'      => 'B+',
        'joined'     => '2026-07-28',
        'status'     => 'pending',
        'position'   => 'درخواست گزار',
    ],
];

$stmtMember = $pdo->prepare(
    "INSERT IGNORE INTO members
       (name, father_name, photo, cnic, mobile, email, password, role,
        address, district, tehsil, village, education, occupation, blood_group,
        position, status, joined_at)
     VALUES
       (:name, :father, :photo, :cnic, :mobile, :email, :password, :role,
        :address, :district, :tehsil, :village, :education, :occupation, :blood,
        :position, :status, :joined)"
);
foreach ($members as $m) {
    $stmtMember->execute([
        ':name'      => $m['name'],
        ':father'    => $m['father'],
        ':photo'     => $m['photo'],
        ':cnic'      => $m['cnic'],
        ':mobile'    => $m['mobile'],
        ':email'     => $m['email'],
        ':password'  => password_hash($m['password'], PASSWORD_BCRYPT),
        ':role'      => $m['role'],
        ':address'   => $m['address'],
        ':district'  => $m['district'],
        ':tehsil'    => $m['tehsil'],
        ':village'   => $m['village'],
        ':education' => $m['education'],
        ':occupation'=> $m['occupation'],
        ':blood'     => $m['blood'],
        ':position'  => $m['position'],
        ':status'    => $m['status'],
        ':joined'    => $m['joined'],
    ]);
}
echo "✔  Members seeded (" . count($members) . ")\n";

// ── 3. Announcements ──────────────────────────────────────────────────────────
$announcements = [
    ['سالانہ عمومی اجلاس', 'تمام ممبران کو مطلع کیا جاتا ہے کہ فورم کا سالانہ عمومی اجلاس آئندہ ہفتے منعقد ہوگا۔', 'important', '2026-08-10'],
    ['خون کے عطیہ کیمپ', 'اگلے اتوار کو مقامی ہسپتال کے تعاون سے خون کے عطیہ کا کیمپ لگایا جا رہا ہے۔', 'general', '2026-08-15'],
    ['سیلاب متاثرین کے لیے ہنگامی امداد', 'حالیہ بارشوں سے متاثرہ خاندانوں کے لیے فوری امدادی مہم شروع کی جا رہی ہے۔', 'urgent', '2026-07-28'],
    ['تعلیمی وظائف کے فارم', 'غریب اور مستحق طلبہ کے وظائف کے فارم جمع کروانے کی آخری تاریخ قریب ہے۔', 'important', '2026-08-05'],
];
$stmtAnn = $pdo->prepare(
    "INSERT INTO announcements (title, description, priority, posted_at) VALUES (:t, :d, :p, :dt)"
);
foreach ($announcements as [$t, $d, $p, $dt]) {
    $stmtAnn->execute([':t' => $t, ':d' => $d, ':p' => $p, ':dt' => $dt]);
}
echo "✔  Announcements seeded\n";

// ── 4. Events ─────────────────────────────────────────────────────────────────
$events = [
    ['سالانہ یوتھ کنونشن', '2026-09-05', 'کمیونٹی ہال، جامشورو', 'ایگزیکٹو کمیٹی', 'کمیونٹی کے نوجوانوں کے لیے سالانہ اجتماع۔'],
    ['مفت میڈیکل کیمپ', '2026-08-20', '(abc) ہیلتھ سینٹر', 'ویلفیئر کمیٹی', 'کمیونٹی کے افراد کے لیے مفت طبی معائنہ۔'],
    ['یوتھ اسپورٹس گالا', '2026-09-18', 'اسپورٹس گراؤنڈ، کوٹری', 'یوتھ کمیٹی', 'نوجوانوں کے لیے کرکٹ اور والی بال کے مقابلے۔'],
    ['تعلیمی سیمینار', '2026-08-30', '(abc) لائبریری ہال', 'ایجوکیشن کمیٹی', 'طلبہ اور والدین کے لیے کیریئر گائیڈنس سیمینار۔'],
];
$stmtEvent = $pdo->prepare(
    "INSERT INTO events (name, event_date, venue, organizer, description) VALUES (:n, :d, :v, :o, :desc)"
);
foreach ($events as [$n, $d, $v, $o, $desc]) {
    $stmtEvent->execute([':n' => $n, ':d' => $d, ':v' => $v, ':o' => $o, ':desc' => $desc]);
}
echo "✔  Events seeded\n";

// ── 5. Emergency Services ─────────────────────────────────────────────────────
$services = [
    ['ہسپتال امداد', 'bi-hospital', 'کمیونٹی کے مریضوں کو ہسپتال میں داخلے اور علاج میں معاونت فراہم کی جاتی ہے۔', 1],
    ['تدفین کی امداد', 'bi-flower1', 'انتقال کی صورت میں تدفین کے انتظامات میں خاندان کی مدد کی جاتی ہے۔', 2],
    ['آفات میں امداد', 'bi-house-heart', 'سیلاب، آگ یا دیگر آفات سے متاثرہ خاندانوں کی بحالی میں مدد۔', 3],
    ['خون کا عطیہ', 'bi-droplet-half', 'ضرورت مند مریضوں کے لیے فوری خون کے عطیہ دہندگان کا نیٹ ورک۔', 4],
    ['مالی امداد', 'bi-cash-coin', 'مستحق خاندانوں کو ماہانہ یا یکمشت مالی معاونت فراہم کی جاتی ہے۔', 5],
];
$stmtSvc = $pdo->prepare(
    "INSERT INTO emergency_services (title, icon, description, sort_order) VALUES (:t, :i, :d, :s)"
);
foreach ($services as [$t, $i, $d, $s]) {
    $stmtSvc->execute([':t' => $t, ':i' => $i, ':d' => $d, ':s' => $s]);
}
echo "✔  Emergency services seeded\n";

// ── 6. Notifications ──────────────────────────────────────────────────────────
$notifications = [
    ['کمیٹی اجلاس کل شام 6 بجے', 'ایگزیکٹو کمیٹی کا اجلاس کل شام 6 بجے دفتر میں ہوگا۔', 'میٹنگ', '2026-08-01', 0],
    ['نیا ایونٹ شامل کیا گیا', 'یوتھ اسپورٹس گالا کی تاریخ کا اعلان کر دیا گیا ہے۔', 'ایونٹ', '2026-07-30', 0],
    ['ہنگامی الرٹ', 'ایک رکن خاندان کو فوری طبی امداد درکار ہے۔', 'ہنگامی', '2026-07-29', 0],
    ['کمیونٹی صفائی مہم', 'اگلے ہفتے محلے میں صفائی مہم چلائی جائے گی۔', 'کمیونٹی کام', '2026-07-27', 1],
    ['نئی ویب سائٹ لانچ', 'فورم کی نئی آفیشل ویب سائٹ لانچ کر دی گئی ہے۔', 'عمومی', '2026-07-20', 1],
];
$stmtNotif = $pdo->prepare(
    "INSERT INTO notifications (title, message, type, posted_at, is_read) VALUES (:t, :m, :ty, :d, :r)"
);
foreach ($notifications as [$t, $m, $ty, $d, $r]) {
    $stmtNotif->execute([':t' => $t, ':m' => $m, ':ty' => $ty, ':d' => $d, ':r' => $r]);
}
echo "✔  Notifications seeded\n";

// ── 7. Gallery Images ─────────────────────────────────────────────────────────
$images = [
    ['https://picsum.photos/seed/gal1/500/350', 'تقریبات', 'سالانہ کنونشن 2025'],
    ['https://picsum.photos/seed/gal2/500/350', 'فلاحی کام', 'خون کے عطیہ کیمپ'],
    ['https://picsum.photos/seed/gal3/500/350', 'تعلیم', 'تعلیمی وظائف کی تقسیم'],
    ['https://picsum.photos/seed/gal4/500/350', 'کمیونٹی کام', 'صفائی مہم'],
    ['https://picsum.photos/seed/gal5/500/350', 'تقریبات', 'یوتھ اسپورٹس ڈے'],
    ['https://picsum.photos/seed/gal6/500/350', 'فلاحی کام', 'راشن کی تقسیم'],
];
$stmtImg = $pdo->prepare(
    "INSERT INTO gallery_images (url, category, caption) VALUES (:u, :c, :cap)"
);
foreach ($images as [$u, $c, $cap]) {
    $stmtImg->execute([':u' => $u, ':c' => $c, ':cap' => $cap]);
}
echo "✔  Gallery images seeded\n";

// ── 8. Gallery Videos ─────────────────────────────────────────────────────────
$videos = [
    ['youtube', 'dQw4w9WgXcQ', 'تقریبات', 'سالانہ اجلاس کی جھلکیاں'],
    ['youtube', '5qap5aO4i9A', 'فلاحی کام', 'میڈیکل کیمپ رپورٹ'],
];
$stmtVid = $pdo->prepare(
    "INSERT INTO gallery_videos (type, youtube_id, category, caption) VALUES (:ty, :yi, :c, :cap)"
);
foreach ($videos as [$ty, $yi, $c, $cap]) {
    $stmtVid->execute([':ty' => $ty, ':yi' => $yi, ':c' => $c, ':cap' => $cap]);
}
echo "✔  Gallery videos seeded\n";

// ── 9. About Content ──────────────────────────────────────────────────────────
$about = [
    'us'           => '(abc) کمیونٹی یوتھ فورم ایک غیر سیاسی، غیر منافع بخش تنظیم ہے جسے کمیونٹی کے نوجوانوں نے مل کر قائم کیا۔',
    'vision'       => 'ایک ایسی خودمختار، تعلیم یافتہ اور باہم متحد کمیونٹی کی تشکیل جو اپنے مسائل خود حل کرنے کی صلاحیت رکھتی ہو۔',
    'mission'      => 'تعلیم، صحت، فلاح و بہبود اور رضاکارانہ خدمات کے ذریعے کمیونٹی کے ہر فرد کی زندگی میں مثبت تبدیلی لانا۔',
    'objectives'   => "کمیونٹی کے نوجوانوں کو تعلیمی مواقع فراہم کرنا\nضرورت مند خاندانوں کی مالی و طبی امداد\nرضاکارانہ خدمات کے کلچر کو فروغ دینا\nہنگامی حالات میں فوری امدادی کارروائی\nکمیونٹی کے اندر اتحاد اور بھائی چارہ برقرار رکھنا",
    'charter'      => 'فورم اپنے تحریری آئین اور چارٹر کے مطابق کام کرتا ہے، جس میں رکنیت کی شرائط، عہدیداران کے انتخاب کا طریقہ کار اور کمیٹیوں کے دائرہ اختیار کا تعین کیا گیا ہے۔',
    'constitution' => 'آئین کے مطابق فورم کی سب سے بڑی مجاز اتھارٹی سالانہ عمومی اجلاس ہے، جہاں تمام اہم فیصلے ممبران کی اکثریت سے کیے جاتے ہیں۔',
];
$stmtAbout = $pdo->prepare(
    "INSERT INTO about_content (`key`, `value`) VALUES (:k, :v)
     ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)"
);
foreach ($about as $k => $v) {
    $stmtAbout->execute([':k' => $k, ':v' => $v]);
}
echo "✔  About content seeded\n";

// ── 10. Contact Info ──────────────────────────────────────────────────────────
$contacts = [
    ['phone',   '+92 3340673401',    1],
    ['phone',   '+92 300 1234567',   2],
    ['email',   'info@masoodforum.org', 3],
    ['address', 'دفتر: (abc) جامشورو، سندھ', 4],
    ['map',     'https://www.google.com/maps?q=Jamshoro,Sindh,Pakistan&output=embed', 5],
];
$stmtContact = $pdo->prepare(
    "INSERT INTO contact_info (type, value, sort_order) VALUES (:t, :v, :s)"
);
foreach ($contacts as [$t, $v, $s]) {
    $stmtContact->execute([':t' => $t, ':v' => $v, ':s' => $s]);
}
echo "✔  Contact info seeded\n";

echo "\n✅  All done! You can now run the app.\n";
echo "   Login credentials:\n";
echo "   super_admin : superadmin@masoodforum.org / Super@123\n";
echo "   admin       : admin@masoodforum.org / Admin@123\n";
echo "   committee   : committee@masoodforum.org / Committee@123\n";
echo "   member      : member@masoodforum.org / Member@123\n";
echo "   pending     : pending@masoodforum.org / Pending@123\n";
