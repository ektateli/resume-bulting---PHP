-- CREATE DATABASE resume_db;

-- USE resume_db;

CREATE TABLE resumes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    skills TEXT,
    education TEXT,
    experience TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
