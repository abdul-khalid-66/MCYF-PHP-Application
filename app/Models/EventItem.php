<?php
/**
 * app/Models/EventItem.php
 * (Named EventItem, not Event, since PECL's ext-event defines a global
 * Event class that could collide on some hosting environments.)
 */

class EventItem
{
    public static function all(): array
    {
        return DB::connection()
            ->query("SELECT * FROM events ORDER BY event_date ASC, id DESC")
            ->fetchAll();
    }

    public static function upcoming(int $limit = 3): array
    {
        $stmt = DB::connection()->prepare(
            "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT " . (int)$limit
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = DB::connection()->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Most recently added photo for this event, or null if none. */
    public static function thumbnail(int $eventId): ?string
    {
        $stmt = DB::connection()->prepare(
            "SELECT image FROM event_gallery WHERE event_id = ? ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute([$eventId]);
        $img = $stmt->fetchColumn();
        return $img ?: null;
    }

    public static function create(array $data, ?int $createdBy): int
    {
        $stmt = DB::connection()->prepare(
            "INSERT INTO events (name, event_date, venue, organizer, description, created_by)
             VALUES (:name, :event_date, :venue, :organizer, :description, :created_by)"
        );
        $stmt->execute([
            ':name'        => $data['name'],
            ':event_date'  => $data['event_date'] ?: null,
            ':venue'       => $data['venue'] ?? '',
            ':organizer'   => $data['organizer'] ?? '',
            ':description' => $data['description'] ?? '',
            ':created_by'  => $createdBy,
        ]);
        return (int) DB::connection()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = DB::connection()->prepare(
            "UPDATE events SET name = :name, event_date = :event_date, venue = :venue,
             organizer = :organizer, description = :description WHERE id = :id"
        );
        $stmt->execute([
            ':name'        => $data['name'],
            ':event_date'  => $data['event_date'] ?: null,
            ':venue'       => $data['venue'] ?? '',
            ':organizer'   => $data['organizer'] ?? '',
            ':description' => $data['description'] ?? '',
            ':id'          => $id,
        ]);
    }

    public static function addPhoto(int $eventId, string $imagePath): void
    {
        $stmt = DB::connection()->prepare(
            "INSERT INTO event_gallery (event_id, image) VALUES (?, ?)"
        );
        $stmt->execute([$eventId, $imagePath]);
    }

    public static function delete(int $id): void
    {
        $stmt = DB::connection()->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$id]); // event_gallery rows cascade via FK
    }
}
