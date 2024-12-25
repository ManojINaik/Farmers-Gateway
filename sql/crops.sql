CREATE TABLE IF NOT EXISTS `crops` (
  `Crop_id` int(11) NOT NULL AUTO_INCREMENT,
  `Crop_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Crop_id`),
  UNIQUE KEY `unique_crop_name` (`Crop_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
