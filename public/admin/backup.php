<?php
require_once __DIR__ . '/../../bootstrap.php';
requireAuth();

// Backups contain everything — including password hashes — so this is
// restricted directly to super_admin, regardless of the permissions matrix.
if (authUserRole() !== 'super_admin') {
    redirect(BASE_URL . '/errors/access-denied');
}

/**
 * Generates a full SQL dump of the database using pure PHP (no mysqldump
 * binary or shell_exec needed — works on any host, including restricted
 * shared hosting where exec() is disabled).
 */
function generateSqlDump(PDO $pdo, string $dbName): string
{
    $output = "-- MCYF Database Backup\n-- Generated: " . date('Y-m-d H:i:s') . "\n-- Database: {$dbName}\n\n";
    $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        // Structure
        $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch();
        $output .= "-- ----------------------------\n-- Table: {$table}\n-- ----------------------------\n";
        $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
        $output .= $createStmt['Create Table'] . ";\n\n";

        // Data
        $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) {
            $columns = array_keys($rows[0]);
            $columnList = '`' . implode('`, `', $columns) . '`';
            $output .= "INSERT INTO `{$table}` ({$columnList}) VALUES\n";

            $valueLines = [];
            foreach ($rows as $row) {
                $values = array_map(function ($v) use ($pdo) {
                    if ($v === null) return 'NULL';
                    return $pdo->quote((string) $v);
                }, $row);
                $valueLines[] = '(' . implode(', ', $values) . ')';
            }
            $output .= implode(",\n", $valueLines) . ";\n\n";
        }
    }

    $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
    return $output;
}

$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = post('action');

    if ($action === 'download_sql') {
        try {
            $pdo  = DB::connection();
            $sql  = generateSqlDump($pdo, DB_NAME);
            $name = 'mcyf_backup_' . date('Y-m-d_His') . '.sql';

            header('Content-Type: application/sql');
            header('Content-Disposition: attachment; filename="' . $name . '"');
            header('Content-Length: ' . strlen($sql));
            echo $sql;
            exit;
        } catch (Throwable $e) {
            $errors[] = 'بیک اپ بنانے میں خرابی: ' . $e->getMessage();
        }
    }

    if ($action === 'download_uploads') {
        if (!class_exists('ZipArchive')) {
            $errors[] = 'آپ کے سرور پر PHP zip extension فعال نہیں ہے۔ php.ini میں "extension=zip" کو فعال کریں۔';
        } else {
        $uploadsDir = ROOT_PATH . '/public/assets/uploads';
        $zipName    = 'mcyf_uploads_' . date('Y-m-d_His') . '.zip';
        $zipPath    = sys_get_temp_dir() . '/' . $zipName;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($uploadsDir, RecursiveDirectoryIterator::SKIP_DOTS)
            );
            foreach ($files as $file) {
                if ($file->isFile() && $file->getFilename() !== '.gitkeep') {
                    $relativePath = substr($file->getPathname(), strlen($uploadsDir) + 1);
                    $zip->addFile($file->getPathname(), $relativePath);
                }
            }
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zipName . '"');
            header('Content-Length: ' . filesize($zipPath));
            readfile($zipPath);
            unlink($zipPath);
            exit;
        } else {
            $errors[] = 'ZIP فائل بنانے میں خرابی۔';
        }
        }
    }
}

// Quick stats for the page
$pdo        = DB::connection();
$tableCount = count($pdo->query("SHOW TABLES")->fetchAll());
$dbSizeRow  = $pdo->query(
    "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
     FROM information_schema.TABLES WHERE table_schema = DATABASE()"
)->fetch();
$dbSizeMb   = $dbSizeRow['size_mb'] ?? 0;

$uploadsDir  = ROOT_PATH . '/public/assets/uploads';
$uploadsSize = 0;
if (is_dir($uploadsDir)) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploadsDir, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($files as $file) {
        if ($file->isFile()) $uploadsSize += $file->getSize();
    }
}
$uploadsSizeMb = round($uploadsSize / 1024 / 1024, 2);

$pageTitle  = 'بیک اپ';
$pageHero   = 'ڈیٹا بیک اپ';
$pageHeroSub= 'ایک کلک میں مکمل ڈیٹا بیس اور تصاویر ڈاؤن لوڈ کریں';
$activePage = 'admin';
$content    = function () use ($errors, $tableCount, $dbSizeMb, $uploadsSizeMb) { ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
  <?php foreach ($errors as $err): ?><div><i class="bi bi-exclamation-triangle me-1"></i><?= e($err) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-md-6">
    <div class="card-forum p-4 text-center h-100">
      <i class="bi bi-database text-forum-gold" style="font-size:2.5rem;"></i>
      <h5 class="mt-2">ڈیٹا بیس بیک اپ</h5>
      <p class="text-muted small">
        <?= $tableCount ?> ٹیبلز، تقریباً <?= $dbSizeMb ?> MB
      </p>
      <p class="small text-muted">تمام ممبران، اعلانات، تقریبات، کمیٹیاں — سب کچھ ایک <code>.sql</code> فائل میں</p>
      <form method="POST" action="">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="download_sql">
        <button class="btn btn-forum">
          <i class="bi bi-download me-1"></i>Database ڈاؤن لوڈ کریں (.sql)
        </button>
      </form>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card-forum p-4 text-center h-100">
      <i class="bi bi-images text-forum-gold" style="font-size:2.5rem;"></i>
      <h5 class="mt-2">تصاویر / اپ لوڈز بیک اپ</h5>
      <p class="text-muted small">
        تقریباً <?= $uploadsSizeMb ?> MB
      </p>
      <p class="small text-muted">ممبران کی تصاویر، لوگو، گیلری، ایونٹ فوٹوز — سب ایک <code>.zip</code> فائل میں</p>
      <form method="POST" action="">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="download_uploads">
        <button class="btn btn-forum">
          <i class="bi bi-download me-1"></i>تصاویر ڈاؤن لوڈ کریں (.zip)
        </button>
      </form>
    </div>
  </div>
</div>

<div class="alert alert-warning mt-4 d-flex gap-2">
  <i class="bi bi-shield-exclamation fs-4"></i>
  <div>
    <strong>محفوظ رکھیں:</strong> ان فائلز میں تمام ممبران کی معلومات (بشمول رابطہ نمبر، پتے) موجود ہوتی ہیں۔
    ڈاؤن لوڈ کے بعد کسی محفوظ جگہ رکھیں اور غیر ضروری طور پر شیئر نہ کریں۔
    ہمیشہ کے لیے خودکار روزانہ بیک اپ کے لیے <code>scripts/BACKUP-GUIDE.md</code> دیکھیں۔
  </div>
</div>

<?php };
require ROOT_PATH . '/views/layouts/main.php';
