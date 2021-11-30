-- mysql -p -u toto mydatabase < events.sql


CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `categ` varchar(255) NOT NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

-- INSERT INTO `events` (`id`, `text`, `host`, `categ`) VALUES
-- (1, 'dummy event', 'host', 'mytype'),
-- (2, 'event2', 'host2', 'mytype2');

INSERT INTO `events` (`text`, `host`, `categ`) VALUES
('event1', 'host2', 'mytype1'),
('event2', 'host2', 'mytype2');


