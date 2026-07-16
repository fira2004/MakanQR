-- Run this in phpMyAdmin against the makanqr database (in addition to
-- schema_update.sql from before, if you haven't already run that one too).

CREATE TABLE IF NOT EXISTS vendors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE foods
    ADD COLUMN availability VARCHAR(20) NOT NULL DEFAULT 'Available' AFTER category;
