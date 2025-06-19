<?php
class StatusAlumni {
    private $conn;
    private $table_name = "status_alumni";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getByAlumniId($alumni_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE alumni_id = :alumni_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":alumni_id", $alumni_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
            (alumni_id, status_saat_ini, nama_instansi, jabatan, bidang_kerja, alamat_instansi, tahun_mulai, tahun_selesai, gaji_range, deskripsi) 
            VALUES 
            (:alumni_id, :status_saat_ini, :nama_instansi, :jabatan, :bidang_kerja, :alamat_instansi, :tahun_mulai, :tahun_selesai, :gaji_range, :deskripsi)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':alumni_id', $data['alumni_id']);
        $stmt->bindParam(':status_saat_ini', $data['status_saat_ini']);
        $stmt->bindParam(':nama_instansi', $data['nama_instansi']);
        $stmt->bindParam(':jabatan', $data['jabatan']);
        $stmt->bindParam(':bidang_kerja', $data['bidang_kerja']);
        $stmt->bindParam(':alamat_instansi', $data['alamat_instansi']);
        $stmt->bindParam(':tahun_mulai', $data['tahun_mulai']);
        $stmt->bindParam(':tahun_selesai', $data['tahun_selesai']);
        $stmt->bindParam(':gaji_range', $data['gaji_range']);
        $stmt->bindParam(':deskripsi', $data['deskripsi']);
        return $stmt->execute();
    }

    public function update($alumni_id, $data) {
        $query = "UPDATE " . $this->table_name . " SET 
            status_saat_ini = :status_saat_ini,
            nama_instansi = :nama_instansi,
            jabatan = :jabatan,
            bidang_kerja = :bidang_kerja,
            alamat_instansi = :alamat_instansi,
            tahun_mulai = :tahun_mulai,
            tahun_selesai = :tahun_selesai,
            gaji_range = :gaji_range,
            deskripsi = :deskripsi,
            updated_at = CURRENT_TIMESTAMP
            WHERE alumni_id = :alumni_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':alumni_id', $alumni_id);
        $stmt->bindParam(':status_saat_ini', $data['status_saat_ini']);
        $stmt->bindParam(':nama_instansi', $data['nama_instansi']);
        $stmt->bindParam(':jabatan', $data['jabatan']);
        $stmt->bindParam(':bidang_kerja', $data['bidang_kerja']);
        $stmt->bindParam(':alamat_instansi', $data['alamat_instansi']);
        $stmt->bindParam(':tahun_mulai', $data['tahun_mulai']);
        $stmt->bindParam(':tahun_selesai', $data['tahun_selesai']);
        $stmt->bindParam(':gaji_range', $data['gaji_range']);
        $stmt->bindParam(':deskripsi', $data['deskripsi']);
        return $stmt->execute();
    }
}
?>
