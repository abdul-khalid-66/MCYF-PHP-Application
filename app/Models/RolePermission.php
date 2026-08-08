<?php
/**
 * app/Models/RolePermission.php
 * Manages the role_permissions DB table (editable permissions matrix).
 */

class RolePermission
{
    /** Returns ['member' => ['dashboard','members',...], ...] from the DB. */
    public static function allGrouped(): array
    {
        $rows = DB::connection()->query("SELECT role, permission FROM role_permissions")->fetchAll();
        $grouped = [];
        foreach (EDITABLE_ROLES as $role) {
            $grouped[$role] = [];
        }
        foreach ($rows as $r) {
            $grouped[$r['role']][] = $r['permission'];
        }
        return $grouped;
    }

    public static function isSeeded(): bool
    {
        $count = (int) DB::connection()->query("SELECT COUNT(*) FROM role_permissions")->fetchColumn();
        return $count > 0;
    }

    /** Seeds the table from the ROLE_PERMISSIONS constant default (only editable roles). */
    public static function seedDefaults(): void
    {
        $pdo = DB::connection();
        $stmt = $pdo->prepare(
            "INSERT IGNORE INTO role_permissions (role, permission) VALUES (?, ?)"
        );
        foreach (EDITABLE_ROLES as $role) {
            foreach (ROLE_PERMISSIONS[$role] ?? [] as $perm) {
                $stmt->execute([$role, $perm]);
            }
        }
    }

    /**
     * Replace ALL permissions for one role with the given list.
     * @param string[] $permissions
     */
    public static function setForRole(string $role, array $permissions): void
    {
        if (!in_array($role, EDITABLE_ROLES, true)) return;

        $pdo = DB::connection();
        $pdo->prepare("DELETE FROM role_permissions WHERE role = ?")->execute([$role]);

        if (empty($permissions)) return;

        $stmt = $pdo->prepare("INSERT INTO role_permissions (role, permission) VALUES (?, ?)");
        foreach ($permissions as $perm) {
            if (in_array($perm, ALL_PERMISSION_KEYS, true)) {
                $stmt->execute([$role, $perm]);
            }
        }
    }
}
