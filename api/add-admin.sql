
INSERT INTO `user` (`full_name`, `email`, `password`, `role`, `is_active`) 
SELECT 'Free Lotto1', 'totalfreelotto494@gmail.com', '$2y$12$ePPDySbeBsVlCI/GXavv8e/IMJzRxTQhUMJKys1bv0.LZDYWBT5xy', 'admin', 1
WHERE NOT EXISTS (
    SELECT 1 FROM `user` WHERE `email` = 'totalfreelotto494@gmail.com'
);

SELECT id, full_name, email, role FROM `user` WHERE role = 'admin';
