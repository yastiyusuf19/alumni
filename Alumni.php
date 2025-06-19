<?php
class Alumni {
    private $conn;
    private $table_name = "alumni";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, nim=:nim, nama_lengkap=:nama_lengkap, 
                      angkatan=:angkatan, program_studi=:program_studi, 
                      tahun_lulus=:tahun_lulus, alamat=:alamat, 
                      no_telepon=:no_telepon, email_alternatif=:email_alternatif";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":user_id", $data['user_id']);
        $stmt->bindParam(":nim", $data['nim']);
        $stmt->bindParam(":nama_lengkap", $data['nama_lengkap']);
        $stmt->bindParam(":angkatan", $data['angkatan']);
        $stmt->bindParam(":program_studi", $data['program_studi']);
        $stmt->bindParam(":tahun_lulus", $data['tahun_lulus']);
        $stmt->bindParam(":alamat", $data['alamat']);
        $stmt->bindParam(":no_telepon", $data['no_telepon']);
        $stmt->bindParam(":email_alternatif", $data['email_alternatif']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getAll() {
        $query = "SELECT a.*, s.status_saat_ini, s.nama_instansi, s.jabatan, s.bidang_kerja 
                  FROM " . $this->table_name . " a 
                  LEFT JOIN status_alumni s ON a.id = s.alumni_id 
                  ORDER BY a.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT a.*, s.status_saat_ini, s.nama_instansi, s.jabatan, s.bidang_kerja, s.gaji_range 
                  FROM " . $this->table_name . " a 
                  LEFT JOIN status_alumni s ON a.id = s.alumni_id 
                  WHERE a.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nama_lengkap=:nama_lengkap, angkatan=:angkatan, 
                      program_studi=:program_studi,tahun_lulus=:tahun_lulus,pekerjaan=:pekerjaan, 
                      alamat=:alamat, no_telepon=:no_telepon, 
                      email_alternatif=:email_alternatif 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nama_lengkap", $data['nama_lengkap']);
        $stmt->bindParam(":angkatan", $data['angkatan']);
        $stmt->bindParam(":program_studi", $data['program_studi']);
        $stmt->bindParam(":tahun_lulus", $data['tahun_lulus']);
        $stmt->bindParam(":pekerjaan", $data['pekerjaan']);
        $stmt->bindParam(":alamat", $data['alamat']);
        $stmt->bindParam(":no_telepon", $data['no_telepon']);
        $stmt->bindParam(":email_alternatif", $data['email_alternatif']);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function getStatistics() {
        // Statistik berdasarkan angkatan
        $query1 = "SELECT angkatan, COUNT(*) as jumlah FROM " . $this->table_name . " GROUP BY angkatan ORDER BY angkatan";
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->execute();
        $stats_angkatan = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        // Statistik berdasarkan program studi
        $query2 = "SELECT program_studi, COUNT(*) as jumlah FROM " . $this->table_name . " GROUP BY program_studi";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->execute();
        $stats_prodi = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // Statistik berdasarkan status pekerjaan
        $query3 = "SELECT s.status_saat_ini, COUNT(*) as jumlah 
                   FROM status_alumni s 
                   JOIN " . $this->table_name . " a ON s.alumni_id = a.id 
                   GROUP BY s.status_saat_ini";
        $stmt3 = $this->conn->prepare($query3);
        $stmt3->execute();
        $stats_status = $stmt3->fetchAll(PDO::FETCH_ASSOC);

        return [
            'angkatan' => $stats_angkatan,
            'program_studi' => $stats_prodi,
            'status_pekerjaan' => $stats_status
        ];
    }
}
?>