-- Add role and stationary_id columns to admin table
ALTER TABLE `admin`
  ADD COLUMN `role` VARCHAR(20) NOT NULL DEFAULT 'admin' AFTER `password`,
  ADD COLUMN `stationary_id` INT(11) DEFAULT NULL AFTER `role`;
-- Example: role='stationary' for stationary admins, role='admin' for regular admins
