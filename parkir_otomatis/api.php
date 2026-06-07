<?php
// api.php
// Backend API untuk Sistem Parkir Otomatis

// Mulai session PHP
session_start();

header('Content-Type: application/json');
require_once 'config.php';

// Fungsi Auto-Setup: memastikan kolom 'plat', 'jenis', dan 'nama' tersedia di tabel 'parkir'
try {
    $check = $pdo->query("SHOW COLUMNS FROM `parkir` LIKE 'plat'");
    if ($check->rowCount() == 0) {
        $pdo->exec("ALTER TABLE `parkir` ADD COLUMN `plat` VARCHAR(20) DEFAULT NULL AFTER `id_slot`");
        $pdo->exec("ALTER TABLE `parkir` ADD COLUMN `jenis` VARCHAR(20) DEFAULT NULL AFTER `plat`");
        $pdo->exec("ALTER TABLE `parkir` ADD COLUMN `nama` VARCHAR(100) DEFAULT NULL AFTER `jenis`");
    }
} catch (Exception $e) {
    // Abaikan atau log jika perlu
}

// Menentukan action dari request
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Cek Autentikasi (kecuali untuk action 'login')
if ($action !== 'login' && !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Silakan login terlebih dahulu.']);
    exit();
}

// Tarif per jam berdasarkan jenis kendaraan
$TARIF = [
    'Motor' => 2000,
    'Mobil' => 5000,
    'Truk'  => 10000
];

switch ($action) {
    case 'login':
        try {
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';

            if (!$username || !$password) {
                throw new Exception('Username dan password wajib diisi.');
            }

            $stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user) {
                // Mendukung password plaintext (admin123 bawaan dump) dan bcrypt (untuk user baru)
                if ($password === $user['password'] || password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id_user'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['nama'] = $user['nama'];
                    $_SESSION['role'] = $user['role'];

                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Login berhasil',
                        'data' => [
                            'id_user' => $user['id_user'],
                            'nama' => $user['nama'],
                            'username' => $user['username'],
                            'role' => $user['role']
                        ]
                    ]);
                    exit();
                }
            }
            throw new Exception('Username atau password salah.');
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'logout':
        session_destroy();
        echo json_encode(['status' => 'success', 'message' => 'Logout berhasil.']);
        break;

    case 'get_slots':
        try {
            // Ambil semua slot parkir beserta kendaraan yang sedang terparkir jika ada
            $stmt = $pdo->query("
                SELECT s.id_slot, s.kode_slot, s.status AS slot_status, 
                       p.id_parkir, p.qr_code, p.plat, p.jenis, p.nama, p.waktu_masuk,
                       u.nama AS nama_petugas
                FROM slot_parkir s
                LEFT JOIN parkir p ON s.id_slot = p.id_slot AND p.status = 'Masuk'
                LEFT JOIN user u ON p.id_user = u.id_user
                ORDER BY s.kode_slot ASC
            ");
            $slots = $stmt->fetchAll();
            echo json_encode(['status' => 'success', 'data' => $slots]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'masuk':
        try {
            // Input data kendaraan masuk
            $id_slot = isset($_POST['id_slot']) ? intval($_POST['id_slot']) : 0;
            $plat = isset($_POST['plat']) ? strtoupper(trim($_POST['plat'])) : '';
            $jenis = isset($_POST['jenis']) ? trim($_POST['jenis']) : 'Mobil';
            $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';

            if (!$id_slot || !$plat) {
                throw new Exception('Data slot dan nomor plat harus diisi.');
            }

            // Validasi apakah slot masih kosong
            $stmt = $pdo->prepare("SELECT status FROM slot_parkir WHERE id_slot = ?");
            $stmt->execute([$id_slot]);
            $slot = $stmt->fetch();
            if (!$slot) {
                throw new Exception('Slot tidak ditemukan.');
            }
            if ($slot['status'] === 'Terisi') {
                throw new Exception('Slot parkir sudah terisi.');
            }

            // Validasi apakah plat sudah parkir (mencegah plat ganda di dalam parkiran)
            $stmt = $pdo->prepare("SELECT id_parkir FROM parkir WHERE plat = ? AND status = 'Masuk'");
            $stmt->execute([$plat]);
            if ($stmt->fetch()) {
                throw new Exception("Kendaraan dengan plat $plat sudah berada di dalam parkiran.");
            }

            // Generate QR Code unik (format: SP-XXXX-XXXXXXXX)
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $r = '';
            for ($i = 0; $i < 8; $i++) {
                $r .= $chars[rand(0, strlen($chars) - 1)];
            }
            $qr_code = 'SP-' . substr($r, 0, 4) . '-' . substr($r, 4);

            // Mulai transaksi database
            $pdo->beginTransaction();

            // Cari atau buat record di tabel kendaraan jika kolom id_kendaraan ada di tabel parkir
            $id_kendaraan = null;
            try {
                $hasKendaraanColumn = $pdo->query("SHOW COLUMNS FROM `parkir` LIKE 'id_kendaraan'")->rowCount() > 0;
                if ($hasKendaraanColumn) {
                    // Cari kendaraan berdasarkan plat
                    $stmtVeh = $pdo->prepare("SELECT id_kendaraan FROM kendaraan WHERE nomor_plat = ?");
                    $stmtVeh->execute([$plat]);
                    $veh = $stmtVeh->fetch();
                    if ($veh) {
                        $id_kendaraan = $veh['id_kendaraan'];
                    } else {
                        // Insert kendaraan baru
                        $stmtInsertVeh = $pdo->prepare("INSERT INTO kendaraan (nomor_plat, jenis_kendaraan, nama_pemilik) VALUES (?, ?, ?)");
                        $stmtInsertVeh->execute([$plat, $jenis, $nama]);
                        $id_kendaraan = $pdo->lastInsertId();
                    }
                }
            } catch (Exception $e) {
                // Abaikan jika tabel/kolom tidak ada
            }

            // Insert ke tabel parkir dengan id_user dari session
            $id_user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
            if ($id_kendaraan !== null) {
                $stmt = $pdo->prepare("
                    INSERT INTO parkir (id_slot, id_kendaraan, plat, jenis, nama, qr_code, waktu_masuk, status, id_user)
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), 'Masuk', ?)
                ");
                $stmt->execute([$id_slot, $id_kendaraan, $plat, $jenis, $nama, $qr_code, $id_user]);
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO parkir (id_slot, plat, jenis, nama, qr_code, waktu_masuk, status, id_user)
                    VALUES (?, ?, ?, ?, ?, NOW(), 'Masuk', ?)
                ");
                $stmt->execute([$id_slot, $plat, $jenis, $nama, $qr_code, $id_user]);
            }
            $id_parkir = $pdo->lastInsertId();

            // Update status slot_parkir menjadi 'Terisi'
            $stmt = $pdo->prepare("UPDATE slot_parkir SET status = 'Terisi' WHERE id_slot = ?");
            $stmt->execute([$id_slot]);

            $pdo->commit();

            // Ambil data yang baru disimpan untuk dikirim kembali ke frontend
            $stmt = $pdo->prepare("SELECT * FROM parkir WHERE id_parkir = ?");
            $stmt->execute([$id_parkir]);
            $parkirData = $stmt->fetch();

            echo json_encode(['status' => 'success', 'message' => 'Kendaraan berhasil masuk', 'data' => $parkirData]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'hitung_keluar':
        try {
            $qr_code = isset($_GET['qr_code']) ? trim($_GET['qr_code']) : '';
            if (!$qr_code) {
                throw new Exception('Kode QR harus diisi.');
            }

            // Cari kendaraan yang aktif berdasarkan qr_code
            $stmt = $pdo->prepare("
                SELECT p.id_parkir, p.id_slot, p.plat, p.jenis, p.nama, p.waktu_masuk, p.qr_code, s.kode_slot
                FROM parkir p
                JOIN slot_parkir s ON p.id_slot = s.id_slot
                WHERE p.qr_code = ? AND p.status = 'Masuk'
            ");
            $stmt->execute([$qr_code]);
            $parkir = $stmt->fetch();

            if (!$parkir) {
                throw new Exception('Tiket QR tidak ditemukan atau kendaraan sudah keluar.');
            }

            // Hitung durasi dan biaya
            $waktu_masuk = new DateTime($parkir['waktu_masuk']);
            $waktu_keluar = new DateTime(); // waktu sekarang

            // Gunakan timestamp untuk menghitung selisih menit
            $diff_seconds = $waktu_keluar->getTimestamp() - $waktu_masuk->getTimestamp();
            $total_menit = max(0, floor($diff_seconds / 60)); // pastikan tidak negatif

            // Total durasi dalam jam (pembulatan ke atas, minimal 1 jam)
            $total_jam = max(1, ceil($total_menit / 60));

            // Tarif per jam sesuai jenis kendaraan
            $tarif_per_jam = $TARIF[$parkir['jenis']] ?? 5000;
            $total_bayar = $total_jam * $tarif_per_jam;

            echo json_encode([
                'status' => 'success',
                'data' => [
                    'id_parkir' => $parkir['id_parkir'],
                    'id_slot' => $parkir['id_slot'],
                    'kode_slot' => $parkir['kode_slot'],
                    'plat' => $parkir['plat'],
                    'jenis' => $parkir['jenis'],
                    'nama' => $parkir['nama'],
                    'waktu_masuk' => $parkir['waktu_masuk'],
                    'waktu_keluar' => $waktu_keluar->format('Y-m-d H:i:s'),
                    'durasi_menit' => $total_menit,
                    'durasi_jam' => $total_jam,
                    'tarif_per_jam' => $tarif_per_jam,
                    'total_bayar' => $total_bayar,
                    'qr_code' => $parkir['qr_code']
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'konfirmasi_keluar':
        try {
            $id_parkir = isset($_POST['id_parkir']) ? intval($_POST['id_parkir']) : 0;
            $total_bayar = isset($_POST['total_bayar']) ? floatval($_POST['total_bayar']) : 0;
            $metode_bayar = isset($_POST['metode_bayar']) ? trim($_POST['metode_bayar']) : 'Cash';

            if (!$id_parkir) {
                throw new Exception('ID parkir tidak valid.');
            }

            // Ambil data parkir aktif
            $stmt = $pdo->prepare("SELECT id_slot, plat FROM parkir WHERE id_parkir = ? AND status = 'Masuk'");
            $stmt->execute([$id_parkir]);
            $parkir = $stmt->fetch();
            if (!$parkir) {
                throw new Exception('Data parkir aktif tidak ditemukan.');
            }

            $id_slot = $parkir['id_slot'];

            $pdo->beginTransaction();

            // 1. Update data keluar di tabel parkir
            $stmt = $pdo->prepare("
                UPDATE parkir 
                SET waktu_keluar = NOW(), status = 'Selesai' 
                WHERE id_parkir = ?
            ");
            $stmt->execute([$id_parkir]);

            // 2. Update status slot_parkir menjadi 'Kosong'
            $stmt = $pdo->prepare("UPDATE slot_parkir SET status = 'Kosong' WHERE id_slot = ?");
            $stmt->execute([$id_slot]);

            // 3. Masukkan transaksi pembayaran ke tabel transaksi
            $stmt = $pdo->prepare("
                INSERT INTO transaksi (id_parkir, total_bayar, metode_bayar, tanggal_bayar)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$id_parkir, $total_bayar, $metode_bayar]);

            $pdo->commit();

            echo json_encode([
                'status' => 'success', 
                'message' => "Kendaraan {$parkir['plat']} berhasil keluar. Pembayaran Rp " . number_format($total_bayar, 0, ',', '.') . " diterima."
            ]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'get_riwayat':
        try {
            // Mengambil 50 transaksi riwayat parkir terakhir yang sudah selesai
            $stmt = $pdo->query("
                SELECT t.id_transaksi, t.total_bayar, t.metode_bayar, t.tanggal_bayar,
                       p.plat, p.jenis, p.nama, p.waktu_masuk, p.waktu_keluar, p.qr_code,
                       s.kode_slot, u.nama AS nama_petugas
                FROM transaksi t
                JOIN parkir p ON t.id_parkir = p.id_parkir
                JOIN slot_parkir s ON p.id_slot = s.id_slot
                LEFT JOIN user u ON p.id_user = u.id_user
                ORDER BY t.tanggal_bayar DESC
                LIMIT 50
            ");
            $riwayat = $stmt->fetchAll();
            
            // Hitung total pendapatan hari ini
            $stmtToday = $pdo->query("
                SELECT SUM(total_bayar) AS total 
                FROM transaksi 
                WHERE DATE(tanggal_bayar) = DATE(NOW())
            ");
            $todayRev = $stmtToday->fetch();
            $pendapatan_hari_ini = $todayRev['total'] ? floatval($todayRev['total']) : 0;

            echo json_encode([
                'status' => 'success', 
                'data' => $riwayat,
                'revenue_today' => $pendapatan_hari_ini
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'get_users':
        try {
            if ($_SESSION['role'] !== 'admin') {
                throw new Exception('Akses ditolak. Hanya admin yang diperbolehkan.');
            }
            $stmt = $pdo->query("SELECT id_user, nama, username, role FROM user ORDER BY id_user ASC");
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'save_user':
        try {
            if ($_SESSION['role'] !== 'admin') {
                throw new Exception('Akses ditolak.');
            }
            $id_user = isset($_POST['id_user']) ? intval($_POST['id_user']) : 0;
            $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            $role = isset($_POST['role']) ? trim($_POST['role']) : 'petugas';

            if (!$nama || !$username || !$role) {
                throw new Exception('Semua kolom (Nama, Username, Role) wajib diisi.');
            }

            if ($id_user === 0) {
                // Tambah baru
                if (!$password) {
                    throw new Exception('Password wajib diisi untuk user baru.');
                }
                // Cek username unik
                $check = $pdo->prepare("SELECT id_user FROM user WHERE username = ?");
                $check->execute([$username]);
                if ($check->fetch()) {
                    throw new Exception('Username sudah digunakan.');
                }

                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO user (nama, username, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nama, $username, $hashedPassword, $role]);
                echo json_encode(['status' => 'success', 'message' => 'User berhasil ditambahkan.']);
            } else {
                // Edit
                $check = $pdo->prepare("SELECT id_user FROM user WHERE username = ? AND id_user != ?");
                $check->execute([$username, $id_user]);
                if ($check->fetch()) {
                    throw new Exception('Username sudah digunakan oleh user lain.');
                }

                if ($password) {
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("UPDATE user SET nama = ?, username = ?, password = ?, role = ? WHERE id_user = ?");
                    $stmt->execute([$nama, $username, $hashedPassword, $role, $id_user]);
                } else {
                    $stmt = $pdo->prepare("UPDATE user SET nama = ?, username = ?, role = ? WHERE id_user = ?");
                    $stmt->execute([$nama, $username, $role, $id_user]);
                }
                echo json_encode(['status' => 'success', 'message' => 'User berhasil diubah.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'delete_user':
        try {
            if ($_SESSION['role'] !== 'admin') {
                throw new Exception('Akses ditolak.');
            }
            $id_user = isset($_POST['id_user']) ? intval($_POST['id_user']) : 0;
            if ($id_user === intval($_SESSION['user_id'])) {
                throw new Exception('Anda tidak dapat menghapus akun Anda sendiri.');
            }

            // Set id_user di tabel parkir menjadi NULL untuk menghindari foreign key constraint error
            $pdo->prepare("UPDATE parkir SET id_user = NULL WHERE id_user = ?")->execute([$id_user]);

            $stmt = $pdo->prepare("DELETE FROM user WHERE id_user = ?");
            $stmt->execute([$id_user]);
            echo json_encode(['status' => 'success', 'message' => 'User berhasil dihapus.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'add_slot':
        try {
            if ($_SESSION['role'] !== 'admin') {
                throw new Exception('Akses ditolak.');
            }
            $kode_slot = isset($_POST['kode_slot']) ? strtoupper(trim($_POST['kode_slot'])) : '';
            if (!$kode_slot) {
                throw new Exception('Kode slot tidak boleh kosong.');
            }

            $check = $pdo->prepare("SELECT id_slot FROM slot_parkir WHERE kode_slot = ?");
            $check->execute([$kode_slot]);
            if ($check->fetch()) {
                throw new Exception("Kode slot $kode_slot sudah terdaftar.");
            }

            $stmt = $pdo->prepare("INSERT INTO slot_parkir (kode_slot, status) VALUES (?, 'Kosong')");
            $stmt->execute([$kode_slot]);
            echo json_encode(['status' => 'success', 'message' => "Slot $kode_slot berhasil ditambahkan."]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'delete_slot':
        try {
            if ($_SESSION['role'] !== 'admin') {
                throw new Exception('Akses ditolak.');
            }
            $id_slot = isset($_POST['id_slot']) ? intval($_POST['id_slot']) : 0;
            if (!$id_slot) {
                throw new Exception('ID slot tidak valid.');
            }

            $check = $pdo->prepare("SELECT status FROM slot_parkir WHERE id_slot = ?");
            $check->execute([$id_slot]);
            $slot = $check->fetch();
            if ($slot && $slot['status'] === 'Terisi') {
                throw new Exception('Slot tidak dapat dihapus karena sedang terisi kendaraan.');
            }

            $pdo->beginTransaction();
            // Ambil semua transaksi yang terkait dengan parkir di slot ini, lalu hapus
            $stmtParkir = $pdo->prepare("SELECT id_parkir FROM parkir WHERE id_slot = ?");
            $stmtParkir->execute([$id_slot]);
            $parkirIds = $stmtParkir->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($parkirIds)) {
                $placeholders = implode(',', array_fill(0, count($parkirIds), '?'));
                $pdo->prepare("DELETE FROM transaksi WHERE id_parkir IN ($placeholders)")->execute($parkirIds);
                $pdo->prepare("DELETE FROM parkir WHERE id_slot = ?")->execute([$id_slot]);
            }

            // Hapus slot
            $stmt = $pdo->prepare("DELETE FROM slot_parkir WHERE id_slot = ?");
            $stmt->execute([$id_slot]);

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Slot berhasil dihapus beserta riwayatnya.']);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'get_kendaraan':
        try {
            // Ambil semua data kendaraan yang pernah parkir (aktif dan selesai)
            $status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';
            $sql = "
                SELECT p.id_parkir, p.plat, p.jenis, p.nama, p.qr_code,
                       p.waktu_masuk, p.waktu_keluar, p.status,
                       s.kode_slot, u.nama AS nama_petugas
                FROM parkir p
                JOIN slot_parkir s ON p.id_slot = s.id_slot
                LEFT JOIN user u ON p.id_user = u.id_user
            ";
            if ($status_filter === 'Masuk') {
                $sql .= " WHERE p.status = 'Masuk'";
            } elseif ($status_filter === 'Selesai') {
                $sql .= " WHERE p.status = 'Selesai'";
            }
            $sql .= " ORDER BY p.waktu_masuk DESC LIMIT 100";

            $stmt = $pdo->query($sql);
            $kendaraan = $stmt->fetchAll();
            echo json_encode(['status' => 'success', 'data' => $kendaraan]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'update_kendaraan':
        try {
            $id_parkir = isset($_POST['id_parkir']) ? intval($_POST['id_parkir']) : 0;
            $plat      = isset($_POST['plat'])      ? strtoupper(trim($_POST['plat'])) : '';
            $jenis     = isset($_POST['jenis'])     ? trim($_POST['jenis']) : '';
            $nama      = isset($_POST['nama'])      ? trim($_POST['nama'])  : '';

            if (!$id_parkir) {
                throw new Exception('ID parkir tidak valid.');
            }
            if (!$plat) {
                throw new Exception('Nomor plat tidak boleh kosong.');
            }

            // Cegah duplikasi plat untuk kendaraan yang masih aktif (kecuali kendaraan itu sendiri)
            $check = $pdo->prepare("SELECT id_parkir FROM parkir WHERE plat = ? AND status = 'Masuk' AND id_parkir != ?");
            $check->execute([$plat, $id_parkir]);
            if ($check->fetch()) {
                throw new Exception("Plat $plat sudah terdaftar pada kendaraan yang sedang parkir.");
            }

            $stmt = $pdo->prepare("UPDATE parkir SET plat = ?, jenis = ?, nama = ? WHERE id_parkir = ?");
            $stmt->execute([$plat, $jenis, $nama, $id_parkir]);

            echo json_encode(['status' => 'success', 'message' => "Data kendaraan $plat berhasil diperbarui."]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'delete_kendaraan':
        try {
            if ($_SESSION['role'] !== 'admin') {
                throw new Exception('Akses ditolak. Hanya admin yang dapat menghapus data kendaraan.');
            }
            $id_parkir = isset($_POST['id_parkir']) ? intval($_POST['id_parkir']) : 0;
            if (!$id_parkir) {
                throw new Exception('ID parkir tidak valid.');
            }

            // Cek apakah kendaraan masih aktif
            $check = $pdo->prepare("SELECT status, id_slot FROM parkir WHERE id_parkir = ?");
            $check->execute([$id_parkir]);
            $record = $check->fetch();

            if (!$record) {
                throw new Exception('Data kendaraan tidak ditemukan.');
            }

            $pdo->beginTransaction();

            // Jika masih aktif, bebaskan slot-nya
            if ($record['status'] === 'Masuk') {
                $pdo->prepare("UPDATE slot_parkir SET status = 'Kosong' WHERE id_slot = ?")->execute([$record['id_slot']]);
            }

            // Hapus transaksi terkait terlebih dahulu (FK constraint)
            $pdo->prepare("DELETE FROM transaksi WHERE id_parkir = ?")->execute([$id_parkir]);

            // Hapus record parkir
            $pdo->prepare("DELETE FROM parkir WHERE id_parkir = ?")->execute([$id_parkir]);

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Data kendaraan berhasil dihapus.']);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'get_rekap_harian':
        try {
            $tanggal = isset($_GET['tanggal']) ? trim($_GET['tanggal']) : date('Y-m-d');

            // Ringkasan keuangan hari ini
            $stmtKeu = $pdo->prepare("
                SELECT 
                    COUNT(t.id_transaksi) AS total_transaksi,
                    COALESCE(SUM(t.total_bayar), 0) AS total_pendapatan,
                    COALESCE(SUM(CASE WHEN p.jenis='Motor' THEN t.total_bayar ELSE 0 END), 0) AS motor_pendapatan,
                    COALESCE(SUM(CASE WHEN p.jenis='Mobil' THEN t.total_bayar ELSE 0 END), 0) AS mobil_pendapatan,
                    COALESCE(SUM(CASE WHEN p.jenis='Truk'  THEN t.total_bayar ELSE 0 END), 0) AS truk_pendapatan,
                    COUNT(CASE WHEN p.jenis='Motor' THEN 1 END) AS motor_count,
                    COUNT(CASE WHEN p.jenis='Mobil' THEN 1 END) AS mobil_count,
                    COUNT(CASE WHEN p.jenis='Truk'  THEN 1 END) AS truk_count
                FROM transaksi t
                JOIN parkir p ON t.id_parkir = p.id_parkir
                WHERE DATE(t.tanggal_bayar) = ?
            ");
            $stmtKeu->execute([$tanggal]);
            $keuangan = $stmtKeu->fetch();

            // Slot info
            $stmtSlot = $pdo->query("
                SELECT COUNT(*) AS total_slot,
                       SUM(CASE WHEN status='Terisi' THEN 1 ELSE 0 END) AS terisi,
                       SUM(CASE WHEN status='Kosong' THEN 1 ELSE 0 END) AS kosong
                FROM slot_parkir
            ");
            $slotInfo = $stmtSlot->fetch();

            // Kendaraan masuk hari ini
            $stmtMasuk = $pdo->prepare("
                SELECT COUNT(*) AS total_masuk
                FROM parkir
                WHERE DATE(waktu_masuk) = ?
            ");
            $stmtMasuk->execute([$tanggal]);
            $masukInfo = $stmtMasuk->fetch();

            // Kendaraan sedang parkir aktif
            $stmtAktif = $pdo->query("
                SELECT COUNT(*) AS total_aktif FROM parkir WHERE status='Masuk'
            ");
            $aktifInfo = $stmtAktif->fetch();

            // Detail transaksi hari ini (semua)
            $stmtDetail = $pdo->prepare("
                SELECT t.id_transaksi, t.total_bayar, t.metode_bayar, t.tanggal_bayar,
                       p.plat, p.jenis, p.nama, p.waktu_masuk, p.waktu_keluar,
                       s.kode_slot, u.nama AS nama_petugas
                FROM transaksi t
                JOIN parkir p ON t.id_parkir = p.id_parkir
                JOIN slot_parkir s ON p.id_slot = s.id_slot
                LEFT JOIN user u ON p.id_user = u.id_user
                WHERE DATE(t.tanggal_bayar) = ?
                ORDER BY t.tanggal_bayar ASC
            ");
            $stmtDetail->execute([$tanggal]);
            $detailTransaksi = $stmtDetail->fetchAll();

            // Kendaraan aktif saat ini
            $stmtParkirAktif = $pdo->query("
                SELECT p.plat, p.jenis, p.nama, p.waktu_masuk, p.qr_code,
                       s.kode_slot, u.nama AS nama_petugas
                FROM parkir p
                JOIN slot_parkir s ON p.id_slot = s.id_slot
                LEFT JOIN user u ON p.id_user = u.id_user
                WHERE p.status = 'Masuk'
                ORDER BY p.waktu_masuk ASC
            ");
            $parkirAktif = $stmtParkirAktif->fetchAll();

            echo json_encode([
                'status'        => 'success',
                'tanggal'       => $tanggal,
                'keuangan'      => $keuangan,
                'slot'          => $slotInfo,
                'masuk_hari_ini'=> $masukInfo['total_masuk'],
                'aktif_sekarang'=> $aktifInfo['total_aktif'],
                'transaksi'     => $detailTransaksi,
                'parkir_aktif'  => $parkirAktif,
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Aksi API tidak valid.']);
        break;
}
?>
