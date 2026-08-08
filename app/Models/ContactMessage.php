<?php
/**
 * app/Models/ContactMessage.php
 */

class ContactMessage
{
    public static function create(string $name, string $contactInfo, string $subject, string $message): int
    {
        $stmt = DB::connection()->prepare(
            "INSERT INTO contact_messages (name, contact_info, subject, message) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$name, $contactInfo, $subject, $message]);
        return (int) DB::connection()->lastInsertId();
    }

    public static function all(): array
    {
        return DB::connection()
            ->query("SELECT * FROM contact_messages ORDER BY is_read ASC, created_at DESC")
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = DB::connection()->prepare("SELECT * FROM contact_messages WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function unreadCount(): int
    {
        return (int) DB::connection()
            ->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")
            ->fetchColumn();
    }

    public static function markRead(int $id): void
    {
        DB::connection()->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([$id]);
    }

    public static function delete(int $id): void
    {
        DB::connection()->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$id]);
    }
}
