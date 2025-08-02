-- Add password column to stationery table for login
ALTER TABLE `stationery`
  ADD COLUMN `password` VARCHAR(255) NOT NULL AFTER `price`;
