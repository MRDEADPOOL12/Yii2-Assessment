-- Minimal schema & data for the assessment
-- NOTE: Adjust table options/engine/charset as needed.
CREATE TABLE IF NOT EXISTS `user` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `username` varchar(64) NULL,
  `auth_key` varchar(64) NULL,
  `is_admin` tinyint(1) DEFAULT 0
);

CREATE TABLE IF NOT EXISTS `plan` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
);

INSERT INTO `plan` (`name`, `price`) VALUES
('Basic', 9.99),
('Pro', 19.99),
('Enterprise', 99.00);

INSERT INTO `user` (`username`, `is_admin`) VALUES
('alice', 0), ('bob', 1), ('charlie', 0);

-- You will run the migration to create `subscription`
-- Below are optional seed rows; if table exists manually insert:
-- INSERT INTO `subscription`(...) VALUES (...);
