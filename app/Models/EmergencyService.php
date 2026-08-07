<?php
/**
 * app/Models/EmergencyService.php
 */

class EmergencyService
{
    public static function all(): array
    {
        return DB::connection()
            ->query("SELECT * FROM emergency_services ORDER BY sort_order ASC, id ASC")
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = DB::connection()->prepare("SELECT * FROM emergency_services WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = DB::connection();
        $nextOrder = (int) $pdo->query("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM emergency_services")->fetchColumn();

        $stmt = $pdo->prepare(
            "INSERT INTO emergency_services (title, icon, description, sort_order)
             VALUES (:title, :icon, :description, :sort_order)"
        );
        $stmt->execute([
            ':title'       => $data['title'],
            ':icon'        => $data['icon'],
            ':description' => $data['description'],
            ':sort_order'  => $nextOrder,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = DB::connection()->prepare(
            "UPDATE emergency_services SET title = :title, icon = :icon, description = :description WHERE id = :id"
        );
        $stmt->execute([
            ':title'       => $data['title'],
            ':icon'        => $data['icon'],
            ':description' => $data['description'],
            ':id'          => $id,
        ]);
    }

    public static function delete(int $id): void
    {
        DB::connection()->prepare("DELETE FROM emergency_services WHERE id = ?")->execute([$id]);
    }
}
