<?php
require_once 'config.php';
$conn = getDBConnection();

// Proses tambah pengeluaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $expense_date = cleanInput($_POST['expense_date']);
    $description = cleanInput($_POST['description']);
    $payment_method = cleanInput($_POST['payment_method']);
    $amount = floatval($_POST['amount']);
    
    $stmt = $conn->prepare("INSERT INTO expenses (expense_date, description, payment_method, amount) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $expense_date, $description, $payment_method, $amount);
    
    if ($stmt->execute()) {
        $success = "Pengeluaran berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan pengeluaran!";
    }
    $stmt->close();
}

// Proses hapus pengeluaran
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM expenses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

// Filter berdasarkan bulan/tahun
$filter_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

// Ambil data pengeluaran
$query = "SELECT * FROM expenses WHERE DATE_FORMAT(expense_date, '%Y-%m') = ? ORDER BY expense_date DESC, id DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $filter_month);
$stmt->execute();
$result = $stmt->get_result();
$expenses = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Hitung total pengeluaran
$total_query = "SELECT SUM(amount) as total FROM expenses WHERE DATE_FORMAT(expense_date, '%Y-%m') = ?";
$stmt = $conn->prepare($total_query);
$stmt->bind_param("s", $filter_month);
$stmt->execute();
$total_result = $stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_expense = $total_row['total'] ?? 0;
$stmt->close();

// Hitung per metode pembayaran
$payment_query = "SELECT payment_method, SUM(amount) as total FROM expenses WHERE DATE_FORMAT(expense_date, '%Y-%m') = ? GROUP BY payment_method";
$stmt = $conn->prepare($payment_query);
$stmt->bind_param("s", $filter_month);
$stmt->execute();
$payment_result = $stmt->get_result();
$payment_methods = $payment_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Hitung pembagian per orang (dibagi 3)
$per_person = $total_expense / 3;

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracker Pengeluaran - Split Bill</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        h1 {
            color: #3a7bd5;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2.5em;
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: transform 0.3s;
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
        }
        
        .summary-card.split {
            background: linear-gradient(135deg, #00b4d8 0%, #0077b6 100%);
        }
        
        .summary-card h3 {
            font-size: 0.9em;
            opacity: 0.9;
            margin-bottom: 10px;
        }
        
        .summary-card .amount {
            font-size: 2em;
            font-weight: bold;
        }
        
        .summary-card .small-text {
            font-size: 0.8em;
            opacity: 0.9;
            margin-top: 5px;
        }
        
        .form-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        
        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #3a7bd5;
        }
        
        .btn {
            background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-delete {
            background: #dc3545;
            padding: 8px 15px;
            font-size: 14px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        th {
            background: #3a7bd5;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        .payment-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .payment-item {
            background: white;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #3a7bd5;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .filter-section {
            margin-bottom: 20px;
        }
        
        .split-info {
            background: linear-gradient(135deg, #00b4d8 0%, #0077b6 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .split-info h2 {
            margin-bottom: 20px;
            font-size: 1.5em;
        }
        
        .split-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .person-box {
            background: rgba(255,255,255,0.2);
            padding: 15px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        
        .person-box h4 {
            font-size: 0.9em;
            margin-bottom: 5px;
            opacity: 0.9;
        }
        
        .person-box .person-amount {
            font-size: 1.8em;
            font-weight: bold;
        }
        
        /* Tablet & Mobile */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 15px;
                border-radius: 15px;
            }

            h1 {
                font-size: 1.8em;
                margin-bottom: 20px;
            }

            h2 {
                font-size: 1.3em;
            }

            .summary {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .summary-card .amount {
                font-size: 1.8em;
            }

            .split-info {
                padding: 20px;
            }

            .split-info h2 {
                font-size: 1.2em;
            }

            .split-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .person-box .person-amount {
                font-size: 1.5em;
            }

            .form-section {
                padding: 15px;
            }

            .form-section h2 {
                font-size: 1.2em;
            }

            .btn {
                width: 100%;
                margin-bottom: 10px;
            }

            .filter-section form {
                flex-direction: column;
                align-items: stretch !important;
            }

            .filter-section .form-group {
                width: 100% !important;
                min-width: 100% !important;
            }

            .payment-list {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 12px;
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            th, td {
                padding: 8px;
                font-size: 12px;
            }

            .btn-delete {
                font-size: 12px;
                padding: 6px 10px;
            }
        }

        /* Mobile Kecil */
        @media (max-width: 480px) {
            body {
                padding: 5px;
            }

            .container {
                padding: 10px;
                border-radius: 10px;
            }

            h1 {
                font-size: 1.5em;
                margin-bottom: 15px;
            }

            h2 {
                font-size: 1.1em;
            }

            .summary-card {
                padding: 15px;
            }

            .summary-card h3 {
                font-size: 0.8em;
            }

            .summary-card .amount {
                font-size: 1.5em;
            }

            .split-info {
                padding: 15px;
            }

            .split-info h2 {
                font-size: 1em;
                margin-bottom: 15px;
            }

            .person-box {
                padding: 12px;
            }

            .person-box h4 {
                font-size: 0.8em;
            }

            .person-box .person-amount {
                font-size: 1.3em;
            }

            .form-section {
                padding: 12px;
            }

            input, select {
                padding: 10px;
                font-size: 14px;
            }

            .btn {
                padding: 10px 20px;
                font-size: 14px;
            }

            table {
                font-size: 11px;
            }

            th, td {
                padding: 6px;
                font-size: 11px;
            }

            .alert {
                padding: 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>💰 Tracker Pengeluaran & Split Bill</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="summary">
            <div class="summary-card">
                <h3>💵 Total Pengeluaran</h3>
                <div class="amount">Rp <?php echo number_format($total_expense, 0, ',', '.'); ?></div>
                <div class="small-text"><?php echo count($expenses); ?> transaksi</div>
            </div>
            <div class="summary-card split">
                <h3>👤 Per Orang (÷ 3)</h3>
                <div class="amount">Rp <?php echo number_format($per_person, 0, ',', '.'); ?></div>
                <div class="small-text">Masing-masing bayar</div>
            </div>
        </div>
        
        <div class="split-info">
            <h2>📊 Pembagian Biaya (3 Orang)</h2>
            <div class="split-grid">
                <div class="person-box">
                    <h4>Orang 1</h4>
                    <div class="person-amount">Rp <?php echo number_format($per_person, 0, ',', '.'); ?></div>
                </div>
                <div class="person-box">
                    <h4>Orang 2</h4>
                    <div class="person-amount">Rp <?php echo number_format($per_person, 0, ',', '.'); ?></div>
                </div>
                <div class="person-box">
                    <h4>Orang 3</h4>
                    <div class="person-amount">Rp <?php echo number_format($per_person, 0, ',', '.'); ?></div>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h2 style="margin-bottom: 20px;">➕ Tambah Pengeluaran Baru</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div class="form-group">
                        <label>📅 Tanggal</label>
                        <input type="date" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>📝 Keterangan</label>
                        <input type="text" name="description" required placeholder="Contoh: Makan siang bersama">
                    </div>
                    <div class="form-group">
                        <label>💳 Metode Pembayaran</label>
                        <select name="payment_method" required>
                            <option value="">Pilih Metode</option>
                            <option value="Cash">Cash / Tunai</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="E-Wallet">E-Wallet (GoPay/OVO/Dana)</option>
                            <option value="Kartu Kredit">Kartu Kredit</option>
                            <option value="Kartu Debit">Kartu Debit</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>💰 Jumlah (Rp)</label>
                        <input type="number" name="amount" step="0.01" required placeholder="150000" min="0">
                    </div>
                </div>
                <button type="submit" class="btn">Tambah Pengeluaran</button>
            </form>
        </div>
        
        <div class="filter-section">
            <form method="GET" style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">
                <div class="form-group" style="margin-bottom: 0; min-width: 200px;">
                    <label>🔍 Filter Bulan</label>
                    <input type="month" name="month" value="<?php echo $filter_month; ?>">
                </div>
                <button type="submit" class="btn">Filter</button>
                <a href="index.php" class="btn" style="text-decoration: none; display: inline-block;">Reset</a>
            </form>
        </div>
        
        <?php if (!empty($payment_methods)): ?>
        <div style="margin-bottom: 30px;">
            <h2 style="margin-bottom: 15px;">💳 Pengeluaran per Metode Pembayaran</h2>
            <div class="payment-list">
                <?php foreach ($payment_methods as $pm): ?>
                <div class="payment-item">
                    <strong><?php echo htmlspecialchars($pm['payment_method']); ?></strong><br>
                    <span style="font-size: 1.2em; color: #3a7bd5; font-weight: bold;">Rp <?php echo number_format($pm['total'], 0, ',', '.'); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px;">
            <h2 style="margin-bottom: 15px;">📋 Riwayat Pengeluaran</h2>
            <?php if (!empty($expenses)): ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>📅 Tanggal</th>
                            <th>📝 Keterangan</th>
                            <th>💳 Metode Pembayaran</th>
                            <th>💰 Jumlah</th>
                            <th>👤 Per Orang</th>
                            <th>🗑️ Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expenses as $expense): 
                            $split_amount = $expense['amount'] / 3;
                        ?>
                        <tr>
                            <td style="white-space: nowrap;"><?php echo date('d/m/Y', strtotime($expense['expense_date'])); ?></td>
                            <td><?php echo htmlspecialchars($expense['description']); ?></td>
                            <td><?php echo htmlspecialchars($expense['payment_method']); ?></td>
                            <td style="font-weight: bold; color: #dc3545;">Rp <?php echo number_format($expense['amount'], 0, ',', '.'); ?></td>
                            <td style="color: #3a7bd5; font-weight: 600;">Rp <?php echo number_format($split_amount, 0, ',', '.'); ?></td>
                            <td>
                                <a href="?delete=<?php echo $expense['id']; ?>&month=<?php echo $filter_month; ?>" 
                                   onclick="return confirm('Yakin ingin menghapus pengeluaran ini?')" 
                                   class="btn btn-delete">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr style="background: #f8f9fa; font-weight: bold;">
                            <td colspan="3" style="text-align: right;">TOTAL:</td>
                            <td style="color: #dc3545; font-size: 1.1em;">Rp <?php echo number_format($total_expense, 0, ',', '.'); ?></td>
                            <td style="color: #3a7bd5; font-size: 1.1em;">Rp <?php echo number_format($per_person, 0, ',', '.'); ?></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p style="text-align: center; color: #666; padding: 40px; background: #f8f9fa; border-radius: 10px;">
                📭 Belum ada pengeluaran di bulan ini.
            </p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
