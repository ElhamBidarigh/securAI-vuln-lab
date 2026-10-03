CREATE DATABASE IF NOT EXISTS securAI_lab
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE securAI_lab;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(64) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(128) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  bio TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  body TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO users (username, password, email, role, bio) VALUES
  ('admin', 'admin123', 'admin@securAI.local', 'admin', 'Site administrator'),
  ('alice', 'alice123', 'alice@securAI.local', 'user', 'Regular user'),
  ('bob', 'bob123', 'bob@securAI.local', 'user', 'Another regular user');

INSERT INTO comments (user_id, body) VALUES
  (1, 'Welcome to SecurAI Vuln Lab!'),
  (2, 'This app is intentionally vulnerable. Please do not deploy it.'),
  (3, 'Try the SQL injection on the login page.');
