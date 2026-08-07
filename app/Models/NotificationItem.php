<?php
/**
 * app/Models/NotificationItem.php
 * (Named NotificationItem, not Notification, to avoid any collision with
 * PHP/PECL extension classes that sometimes define a global Notification class.)
 */

class NotificationItem
{
    public static function all(): array
    {
        return DB::connection()
            ->query("SELECT * FROM notifications ORDER BY posted_at DESC, id DESC")
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = DB::connection()->prepare("SELECT * FROM notifications WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function unreadCount(): int
    {
        return (int) DB::connection()
            ->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0")
            ->fetchColumn();
    }

    public static function create(array $data, ?int $createdBy): int
    {
        $stmt = DB::connection()->prepare(
            "INSERT INTO notifications (title, message, type, posted_at, created_by)
             VALUES (:title, :message, :type, :posted_at, :created_by)"
        );
        $stmt->execute([
            ':title'      => $data['title'],
            ':message'    => $data['message'],
            ':type'       => $data['type'],
            ':posted_at'  => $data['posted_at'] ?: date('Y-m-d'),
            ':created_by' => $createdBy,
        ]);
        return (int) DB::connection()->lastInsertId();
    }

    public static function toggleRead(int $id): void
    {
        $stmt = DB::connection()->prepare(
            "UPDATE notifications SET is_read = NOT is_read WHERE id = ?"
        );
        $stmt->execute([$id]);
    }

    public static function markAllRead(): void
    {
        DB::connection()->exec("UPDATE notifications SET is_read = 1");
    }

    public static function delete(int $id): void
    {
        $stmt = DB::connection()->prepare("DELETE FROM notifications WHERE id = ?");
        $stmt->execute([$id]);
    }
}
