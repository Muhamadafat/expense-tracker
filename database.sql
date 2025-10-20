-- Database untuk Expense Tracker
-- Jalankan script ini di Railway MySQL Database

CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `expense_date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `payment_method` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_expense_date` (`expense_date`),
  KEY `idx_payment_method` (`payment_method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data (optional, bisa dihapus jika tidak diperlukan)
INSERT INTO `expenses` (`expense_date`, `description`, `payment_method`, `amount`) VALUES
('2025-10-15', 'Makan siang bersama', 'Cash', 150000.00),
('2025-10-16', 'Belanja mingguan', 'E-Wallet', 350000.00),
('2025-10-18', 'Transport', 'Cash', 50000.00);
