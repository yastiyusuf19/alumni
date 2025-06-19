<?php
class Lowongan {
    private $conn;
    private $table_name = "lowongan_kerja";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Dapatkan semua data
    public function getAll() {
        $query = "SELECT id, posisi, nama_perusahaan, lokasi, deskripsi, created_at 
                  FROM " . $this->table_name . " 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cari berdasarkan keyword
    public function search($keyword) {
        $query = "SELECT id, posisi, nama_perusahaan, lokasi, deskripsi, created_at 
                  FROM " . $this->table_name . " 
                  WHERE posisi LIKE :kw 
                     OR nama_perusahaan LIKE :kw 
                     OR deskripsi LIKE :kw
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $like = '%' . $keyword . '%';
        $stmt->bindParam(':kw', $like);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Detail lowongan berdasarkan ID
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT id, posisi, nama_perusahaan, lokasi, deskripsi, created_at 
                                      FROM " . $this->table_name . " 
                                      WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tambah lowongan baru
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (posisi, nama_perusahaan, lokasi, deskripsi) 
                  VALUES (:posisi, :nama_perusahaan, :lokasi, :deskripsi)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':posisi', $data['posisi']);
        $stmt->bindParam(':nama_perusahaan', $data['nama_perusahaan']);
        $stmt->bindParam(':lokasi', $data['lokasi']);
        $stmt->bindParam(':deskripsi', $data['deskripsi']);

        return $stmt->execute();
    }

    // Update data lowongan
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET posisi = :posisi, 
                      nama_perusahaan = :nama_perusahaan, 
                      lokasi = :lokasi, 
                      deskripsi = :deskripsi 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':posisi', $data['posisi']);
        $stmt->bindParam(':nama_perusahaan', $data['nama_perusahaan']);
        $stmt->bindParam(':lokasi', $data['lokasi']);
        $stmt->bindParam(':deskripsi', $data['deskripsi']);

        return $stmt->execute();
    }

    // Hapus data lowongan
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table_name . " WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
