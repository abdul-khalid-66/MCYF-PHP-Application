<?php
/**
 * app/Models/Gallery.php
 * Handles both gallery_images and gallery_videos tables.
 */

class Gallery
{
    // ── Images ───────────────────────────────────────────────────────────────

    public static function allImages(): array
    {
        return DB::connection()
            ->query("SELECT * FROM gallery_images ORDER BY created_at DESC, id DESC")
            ->fetchAll();
    }

    public static function findImage(int $id): ?array
    {
        $stmt = DB::connection()->prepare("SELECT * FROM gallery_images WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function createImage(string $url, string $caption, string $category, ?int $createdBy): int
    {
        $stmt = DB::connection()->prepare(
            "INSERT INTO gallery_images (url, caption, category, created_by) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$url, $caption, $category, $createdBy]);
        return (int) DB::connection()->lastInsertId();
    }

    public static function updateImage(int $id, string $caption, string $category, ?string $url = null): void
    {
        if ($url !== null) {
            $stmt = DB::connection()->prepare(
                "UPDATE gallery_images SET url = ?, caption = ?, category = ? WHERE id = ?"
            );
            $stmt->execute([$url, $caption, $category, $id]);
        } else {
            $stmt = DB::connection()->prepare(
                "UPDATE gallery_images SET caption = ?, category = ? WHERE id = ?"
            );
            $stmt->execute([$caption, $category, $id]);
        }
    }

    public static function deleteImage(int $id): void
    {
        DB::connection()->prepare("DELETE FROM gallery_images WHERE id = ?")->execute([$id]);
    }

    // ── Videos ───────────────────────────────────────────────────────────────

    public static function allVideos(): array
    {
        return DB::connection()
            ->query("SELECT * FROM gallery_videos ORDER BY created_at DESC, id DESC")
            ->fetchAll();
    }

    public static function findVideo(int $id): ?array
    {
        $stmt = DB::connection()->prepare("SELECT * FROM gallery_videos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function createVideo(array $data, ?int $createdBy): int
    {
        $stmt = DB::connection()->prepare(
            "INSERT INTO gallery_videos (type, youtube_id, video_path, caption, category, created_by)
             VALUES (:type, :youtube_id, :video_path, :caption, :category, :created_by)"
        );
        $stmt->execute([
            ':type'       => $data['type'],
            ':youtube_id' => $data['youtube_id'] ?? '',
            ':video_path' => $data['video_path'] ?? '',
            ':caption'    => $data['caption'],
            ':category'   => $data['category'],
            ':created_by' => $createdBy,
        ]);
        return (int) DB::connection()->lastInsertId();
    }

    public static function updateVideo(int $id, array $data): void
    {
        $stmt = DB::connection()->prepare(
            "UPDATE gallery_videos SET caption = :caption, category = :category WHERE id = :id"
        );
        $stmt->execute([
            ':caption'  => $data['caption'],
            ':category' => $data['category'],
            ':id'       => $id,
        ]);
    }

    public static function deleteVideo(int $id): void
    {
        DB::connection()->prepare("DELETE FROM gallery_videos WHERE id = ?")->execute([$id]);
    }

    // ── Shared ───────────────────────────────────────────────────────────────

    /** Distinct categories across both images and videos, for the filter dropdown. */
    public static function categories(): array
    {
        $pdo = DB::connection();
        $cats = $pdo->query(
            "SELECT category FROM gallery_images WHERE category != ''
             UNION
             SELECT category FROM gallery_videos WHERE category != ''
             ORDER BY category ASC"
        )->fetchAll(PDO::FETCH_COLUMN);
        return $cats;
    }
}
