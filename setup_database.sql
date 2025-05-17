-- Admin users already created, this is just for reference
-- Username: admin
-- Password: admin123
-- Email: admin@troolife.com

-- If you need to create another admin, uncomment and run this:
-- INSERT INTO `admin` (`username`, `email`, `first_name`, `last_name`, `password`) 
-- VALUES ('newadmin', 'newadmin@troolife.com', 'New', 'Admin', '$2y$10$GV1XUJFbKEHLAe4Vy6c8eeRQr8Lj6tS5mK7XfcizjwHrQmKRNMBgG');

-- View existing admin users
SELECT * FROM `admin`;
