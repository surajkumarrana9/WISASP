-- WISASP Database Schema - Final Build (PPT: Unified Schema)
-- Run in phpMyAdmin: DROP DATABASE IF EXISTS wisasp_db; CREATE DATABASE wisasp_db; USE wisasp_db; then source this.

CREATE DATABASE IF NOT EXISTS wisasp_db;
USE wisasp_db;

-- Users table (PPT: Role-based auth, hashed passwords, professional fields)
DROP TABLE IF EXISTS interviews;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- Hashed with password_hash()
    role ENUM('candidate', 'recruiter') NOT NULL,
    phone VARCHAR(20),
    experience VARCHAR(100),
    company_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Interviews table (PPT: JSON chat_log, topic from lobby, status tracking)
CREATE TABLE interviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    chat_log JSON DEFAULT JSON_ARRAY(),  -- Appends {"u":user_msg, "a":ai_reply}
    topic VARCHAR(255) NOT NULL,          -- From localStorage (Python, Java, etc.)
    status ENUM('active', 'completed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Indexes for performance (PPT: Optimized queries)
CREATE INDEX idx_user_email ON users(email);
CREATE INDEX idx_interview_user ON interviews(user_id);
CREATE INDEX idx_interview_status ON interviews(status);

-- Demo data (PPT: Ready for testing)
INSERT INTO users (fullname, email, password, role, phone, experience, company_name) VALUES
('Test Candidate', 'candidate@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'candidate', '+1-555-0123', '2 years', NULL),
('Test Recruiter', 'recruiter@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'recruiter', '+1-555-0456', '5 years', 'Tech Corp');

-- Password for both: 'password' (hashed)
