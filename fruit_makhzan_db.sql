CREATE TABLE `fruit` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `selling_price` int(11) NOT NULL,
  `total_sales` int(11) DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `fruit` (`name`, `quantity`, `selling_price`, `total_sales`) VALUES
('Orangines', 50, 5, 20);

INSERT INTO `fruit` (`name`, `quantity`, `selling_price`) VALUES
('Kiwiwoos', 50, 5);

INSERT INTO `fruit` (`name`, `quantity`, `selling_price`) VALUES
('Datey-wateys', 30, 10);

INSERT INTO `fruit` (`name`, `quantity`, `selling_price`) VALUES
('Ugli fruit', 200, 2);

INSERT INTO `fruit` (`name`, `quantity`, `selling_price`) VALUES
('Gripes', 1000, 500);