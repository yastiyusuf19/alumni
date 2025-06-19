<?php
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

function formatTanggal($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $pecahkan = explode('-', $tanggal);
    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}

function uploadFile($file, $targetDir = "uploads/") {
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileName = basename($file["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    
    // Validasi file
    $allowedTypes = array('jpg', 'png', 'jpeg', 'gif', 'pdf', 'doc', 'docx');
    if (in_array(strtolower($fileType), $allowedTypes)) {
        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            return $targetFilePath;
        }
    }
    return false;
}
?>