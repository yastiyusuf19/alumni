<?php
class Tracer {
    private $conn;
    private $table_name = "tracer_study";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Menyimpan data tracer baru
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (alumni_id, kepuasan_program_studi, relevansi_pekerjaan, saran_perbaikan, 
                   kesediaan_rekrutmen, kesediaan_mentor)
                  VALUES 
                  (:alumni_id, :kepuasan_program_studi, :relevansi_pekerjaan, :saran_perbaikan, 
                   :kesediaan_rekrutmen, :kesediaan_mentor)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':alumni_id', $data['alumni_id']);
        $stmt->bindParam(':kepuasan_program_studi', $data['kepuasan_program_studi']);
        $stmt->bindParam(':relevansi_pekerjaan', $data['relevansi_pekerjaan']);
        $stmt->bindParam(':saran_perbaikan', $data['saran_perbaikan']);
        $stmt->bindParam(':kesediaan_rekrutmen', $data['kesediaan_rekrutmen']);
        $stmt->bindParam(':kesediaan_mentor', $data['kesediaan_mentor']);

        return $stmt->execute();
    }

    // Ambil data berdasarkan ID alumni
    public function getByAlumniId($alumni_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE alumni_id = :alumni_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':alumni_id', $alumni_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ambil data berdasarkan alumni + tahun survey
    public function getByAlumniIdAndYear($alumni_id, $tahun) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE alumni_id = :alumni_id AND tahun_survey = :tahun";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':alumni_id', $alumni_id);
        $stmt->bindParam(':tahun', $tahun);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ambil semua data tracer study untuk admin
    public function getAll() {
    $query = "SELECT t.*, a.nama_lengkap, a.tahun_lulus 
              FROM tracer_study t 
              JOIN alumni a ON t.alumni_id = a.id 
              ORDER BY t.created_at DESC";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Update data tracer study berdasarkan ID
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET kepuasan_program_studi = :kepuasan_program_studi,
                      relevansi_pekerjaan = :relevansi_pekerjaan,
                      saran_perbaikan = :saran_perbaikan,
                      kesediaan_rekrutmen = :kesediaan_rekrutmen,
                      kesediaan_mentor = :kesediaan_mentor
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':kepuasan_program_studi', $data['kepuasan_program_studi']);
        $stmt->bindParam(':relevansi_pekerjaan', $data['relevansi_pekerjaan']);
        $stmt->bindParam(':saran_perbaikan', $data['saran_perbaikan']);
        $stmt->bindParam(':kesediaan_rekrutmen', $data['kesediaan_rekrutmen']);
        $stmt->bindParam(':kesediaan_mentor', $data['kesediaan_mentor']);
        return $stmt->execute();
    }

    // Hapus data tracer study
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    public function getStatistics() {
    $query = "SELECT 
                COUNT(*) as jumlah_data,
                AVG(kepuasan_program_studi) as rata_kepuasan,
                AVG(relevansi_pekerjaan) as rata_relevansi
              FROM tracer_study";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


}
?>
