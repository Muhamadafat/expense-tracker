-- Buat database
CREATE DATABASE IF NOT EXISTS expense_tracker;
USE expense_tracker;

-- Tabel untuk menyimpan pengeluaran
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expense_date DATE NOT NULL,
    description VARCHAR(255) NOT NULL,
    payment_method VARCHAR(100) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Index untuk performa lebih baik
CREATE INDEX idx_expense_date ON expenses(expense_date);
CREATE INDEX idx_payment_method ON expenses(payment_method);