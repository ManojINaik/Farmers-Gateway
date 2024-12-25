--
-- Table structure for table `farmtube_videos`
--

CREATE TABLE IF NOT EXISTS `farmtube_videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `filePath` varchar(255) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `uploadedBy` varchar(255) NOT NULL,
  `userType` enum('farmer','customer','admin') NOT NULL,
  `category` varchar(100) NOT NULL,
  `privacy` tinyint(1) NOT NULL DEFAULT '1',
  `views` int(11) NOT NULL DEFAULT '0',
  `likes` int(11) NOT NULL DEFAULT '0',
  `dislikes` int(11) NOT NULL DEFAULT '0',
  `uploadDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `farmtube_likes`
--

CREATE TABLE `farmtube_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `videoId` int(11) NOT NULL,
  `userId` varchar(255) NOT NULL,
  `userType` enum('farmer','customer','admin') NOT NULL,
  `liked` tinyint(1) NOT NULL DEFAULT '0',
  `dateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`videoId`,`userId`,`userType`),
  KEY `videoId` (`videoId`),
  CONSTRAINT `farmtube_likes_ibfk_1` FOREIGN KEY (`videoId`) REFERENCES `farmtube_videos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `farmtube_comments`
--

CREATE TABLE IF NOT EXISTS `farmtube_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `videoId` int(11) NOT NULL,
  `postedBy` varchar(255) NOT NULL,
  `userType` enum('farmer','customer','admin') NOT NULL,
  `body` text NOT NULL,
  `datePosted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `videoId` (`videoId`),
  KEY `postedBy` (`postedBy`),
  CONSTRAINT `farmtube_comments_ibfk_1` FOREIGN KEY (`videoId`) REFERENCES `farmtube_videos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
