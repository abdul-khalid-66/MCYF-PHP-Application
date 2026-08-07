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
}
