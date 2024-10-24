USE `%( DB_NAME )%`;



INSERT INTO `hierarchy` (`id`, `type`, `color`, `datetime.insert`) VALUES
(1, 'admin', '#b37528', CURRENT_TIMESTAMP),
(2, 'manager', '#4b884b', CURRENT_TIMESTAMP),
(3, 'viewer', '#5c9ad0', CURRENT_TIMESTAMP);