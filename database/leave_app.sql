CREATE DATABASE IF NOT EXISTS db_leave_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_leave_v2;
CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  fullname VARCHAR(120) NOT NULL,
  role ENUM('staff','supervisor') NOT NULL DEFAULT 'staff'
);
CREATE TABLE IF NOT EXISTS leave_forms (
  leave_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  leave_type VARCHAR(50) NOT NULL,
  date_from DATE NOT NULL,
  date_to DATE NOT NULL,
  reason TEXT NOT NULL,
  status ENUM('รออนุมัติ','อนุมัติ','ไม่อนุมัติ') NOT NULL DEFAULT 'รออนุมัติ',
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);
INSERT INTO users (username, password, fullname, role) VALUES
('staff01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'อภิสิทธิ์ รุ่งเรือง', 'staff'),
('boss01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ศิริกัญญา บุญมี', 'supervisor');
