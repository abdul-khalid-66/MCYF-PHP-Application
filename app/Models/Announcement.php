<?php
/**
 * app/Models/Announcement.php
 */

class Announcement
{
    public static function all(): array
    {
        return DB::connection()
            ->query("SELECT * FROM announcements ORDER BY posted_at DESC, id DESC")
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = DB::connection()->prepare("SELECT * FROM announcements WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data, ?int $createdBy): int
    {
        $stmt = DB::connection()->prepare(
            "INSERT INTO announcements (title, description, priority, posted_at, created_by)
             VALUES (:title, :description, :priority, :posted_at, :created_by)"
        );
        $stmt->execute([
            ':title'       => $data['title'],
            ':description' => $data['description'],
            ':priority'    => $data['priority'],
            ':posted_at'   => $data['posted_at'] ?: date('Y-m-d'),
            ':created_by'  => $createdBy,
        ]);
        return (int) DB::connection()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = DB::connection()->prepare(
            "UPDATE announcements SET title = :title, description = :description,
             priority = :priority, posted_at = :posted_at WHERE id = :id"
        );
        $stmt->execute([
            ':title'       => $data['title'],
            ':description' => $data['description'],
            ':priority'    => $data['priority'],
            ':posted_at'   => $data['posted_at'] ?: date('Y-m-d'),
            ':id'          => $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = DB::connection()->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->execute([$id]);
    }
}
