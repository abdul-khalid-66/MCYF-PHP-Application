<?php
/**
 * app/Models/Member.php
 * Simple data-access wrapper around the `members` table.
 * Not a full ORM — just centralizes the queries used across
 * dashboard, members, profile and (later) admin/users pages.
 */

class Member
{
    public static function find(int $id): ?array
    {
        $stmt = DB::connection()->prepare("SELECT * FROM members WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = DB::connection()->prepare("SELECT * FROM members WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * @param string $search  Matches name / position / district
     * @param string $status  '' = all, or 'active' | 'inactive' | 'pending'
     */
    public static function all(string $search = '', string $status = ''): array
    {
        $sql    = "SELECT * FROM members WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (name LIKE :s OR position LIKE :s OR district LIKE :s)";
            $params[':s'] = '%' . $search . '%';
        }
        if ($status !== '') {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }
        $sql .= " ORDER BY FIELD(role,'super_admin','admin','committee_head','member','pending'), name ASC";

        $stmt = DB::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function activeCount(): int
    {
        return (int) DB::connection()
            ->query("SELECT COUNT(*) FROM members WHERE status = 'active'")
            ->fetchColumn();
    }

    public static function pendingCount(): int
    {
        return (int) DB::connection()
            ->query("SELECT COUNT(*) FROM members WHERE role = 'pending'")
            ->fetchColumn();
    }

    public static function create(array $data): int
    {
        $stmt = DB::connection()->prepare(
            "INSERT INTO members
               (name, father_name, photo, cnic, mobile, email, password, role,
                address, district, tehsil, village, education, occupation,
                blood_group, position, status, joined_at)
             VALUES
               (:name, :father_name, :photo, :cnic, :mobile, :email, :password, :role,
                :address, :district, :tehsil, :village, :education, :occupation,
                :blood_group, :position, :status, :joined_at)"
        );
        $stmt->execute([
            ':name'        => $data['name'],
            ':father_name' => $data['father_name'] ?? '',
            ':photo'       => $data['photo'] ?? '',
            ':cnic'        => $data['cnic'] ?? '',
            ':mobile'      => $data['mobile'] ?? '',
            ':email'       => $data['email'],
            ':password'    => $data['password'],
            ':role'        => $data['role'] ?? 'member',
            ':address'     => $data['address'] ?? '',
            ':district'    => $data['district'] ?? '',
            ':tehsil'      => $data['tehsil'] ?? '',
            ':village'     => $data['village'] ?? '',
            ':education'   => $data['education'] ?? '',
            ':occupation'  => $data['occupation'] ?? '',
            ':blood_group' => $data['blood_group'] ?? '',
            ':position'    => $data['position'] ?? '',
            ':status'      => $data['status'] ?? 'active',
            ':joined_at'   => $data['joined_at'] ?: date('Y-m-d'),
        ]);
        return (int) DB::connection()->lastInsertId();
    }

    /** Updates only the fields present in $data (whitelist-based). */
    public static function update(int $id, array $data): void
    {
        $allowed = [
            'name', 'father_name', 'photo', 'cnic', 'mobile', 'email',
            'address', 'district', 'tehsil', 'village', 'education',
            'occupation', 'blood_group', 'position', 'status', 'joined_at', 'role',
        ];
        $sets   = [];
        $params = [':id' => $id];
        foreach ($data as $key => $value) {
            if (!in_array($key, $allowed, true)) continue;
            $sets[]        = "`$key` = :$key";
            $params[":$key"] = $value;
        }
        if (empty($sets)) return;

        $sql  = "UPDATE members SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = DB::connection()->prepare($sql);
        $stmt->execute($params);
    }

    public static function updatePassword(int $id, string $plainPassword): void
    {
        $stmt = DB::connection()->prepare("UPDATE members SET password = ? WHERE id = ?");
        $stmt->execute([password_hash($plainPassword, PASSWORD_BCRYPT), $id]);
    }

    public static function delete(int $id): void
    {
        $stmt = DB::connection()->prepare("DELETE FROM members WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function approve(int $id): void
    {
        $stmt = DB::connection()->prepare(
            "UPDATE members SET role = 'member', status = 'active' WHERE id = ?"
        );
        $stmt->execute([$id]);
    }
}
