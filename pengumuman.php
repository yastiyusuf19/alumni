<?php
class Pengumuman {
    private $conn;
    private $table = "pengumuman";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (judul, konten, tanggal_mulai, tanggal_selesai, status, created_by)
                VALUES (:judul, :konten, :tanggal_mulai, :tanggal_selesai, :status, :created_by)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':judul', $data['judul']);
        $stmt->bindParam(':konten', $data['konten']);
        $stmt->bindParam(':tanggal_mulai', $data['tanggal_mulai']);
        $stmt->bindParam(':tanggal_selesai', $data['tanggal_selesai']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':created_by', $data['created_by']);

        return $stmt->execute();
    }

    public function update($id, $data) {
        $fields = [];
        foreach ($data as $key => $value) {
            if (!in_array($key, ['id', 'edit_pengumuman'])) {
                $fields[] = "$key = :$key";
            }
        }
        if (empty($fields)) return false;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            if (!in_array($key, ['edit_pengumuman'])) {
                $stmt->bindValue(":$key", $value);
            }
        }

        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    public function getAllPublished() {
    $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE status = 'published' ORDER BY created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Optional: For getting only currently active published announcements
public function getActivePublished() {
    $currentDate = date('Y-m-d');
    $stmt = $this->conn->prepare("SELECT * FROM {$this->table} 
                                WHERE status = 'published' 
                                AND tanggal_mulai <= :currentDate 
                                AND (tanggal_selesai IS NULL OR tanggal_selesai >= :currentDate)
                                ORDER BY created_at DESC");
    $stmt->bindParam(':currentDate', $currentDate);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>
