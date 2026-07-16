-- Run this once in phpMyAdmin (or via mysql CLI) against your makanqr database.
-- Adds the two columns placeOrder.php now needs to actually save the table
-- number and remarks that checkout.php collects (previously read but discarded).

ALTER TABLE orders
    ADD COLUMN table_number VARCHAR(20) NULL AFTER status,
    ADD COLUMN remarks TEXT NULL AFTER table_number;
