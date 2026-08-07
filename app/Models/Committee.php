<?php
/**
 * app/Models/Committee.php
 */

class Committee
{
    /** All committees with chairman name joined in. */
    public static function all(): array
    {
        return DB::connection()->query(
            "SELECT c.*, m.name AS chairman_name
             FROM committees c
             LEFT JOIN members m ON m.id = c.chairman_id
             ORDER BY c.name ASC"
        )->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = DB::connection()->prepare(
            "SELECT c.*, m.name AS chairman_name
             FROM committees c
             LEFT JOIN members m ON m.id = c.chairman_id
             WHERE c.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Member rows (id, name) belonging to a committee. */
    public static function members(int $committeeId): array
    {
        $stmt = DB::connection()->prepare(
            "SELECT m.id, m.name FROM committee_members cm
             JOIN members m ON m.id = cm.member_id
             WHERE cm.committee_id = ?
             ORDER BY m.name ASC"
        );
        $stmt->execute([$committeeId]);
        return $stmt->fetchAll();
    }

    /** @param int[] $memberIds */
    public static function create(array $data, array $memberIds): int
    {
        $pdo = DB::connection();
        $stmt = $pdo->prepare(
            "INSERT INTO committees (name, description, chairman_id) VALUES (:name, :description, :chairman_id)"
        );
        $stmt->execute([
            ':name'        => $data['name'],
            ':description' => $data['description'] ?? '',
            ':chairman_id' => $data['chairman_id'] ?: null,
        ]);
        $id = (int) $pdo->lastInsertId();
        self::syncMembers($id, $memberIds);
        return $id;
    }

    /** @param int[] $memberIds */
    public static function update(int $id, array $data, array $memberIds): void
    {
        $stmt = DB::connection()->prepare(
            "UPDATE committees SET name = :name, description = :description, chairman_id = :chairman_id WHERE id = :id"
        );
        $stmt->execute([
            ':name'        => $data['name'],
            ':description' => $data['description'] ?? '',
            ':chairman_id' => $data['chairman_id'] ?: null,
            ':id'          => $id,
        ]);
        self::syncMembers($id, $memberIds);
    }

    /** @param int[] $memberIds */
    private static function syncMembers(int $committeeId, array $memberIds): void
    {
        $pdo = DB::connection();
        $pdo->prepare("DELETE FROM committee_members WHERE committee_id = ?")->execute([$committeeId]);

        if (empty($memberIds)) return;

        $stmt = $pdo->prepare("INSERT IGNORE INTO committee_members (committee_id, member_id) VALUES (?, ?)");
        foreach ($memberIds as $mid) {
            $stmt->execute([$committeeId, (int)$mid]);
        }
    }

    public static function delete(int $id): void
    {
        $stmt = DB::connection()->prepare("DELETE FROM committees WHERE id = ?");
        $stmt->execute([$id]); // committee_members rows cascade via FK
    }
}
