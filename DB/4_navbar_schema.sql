CREATE TABLE IF NOT EXISTS `navbar_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `section` varchar(20) NOT NULL DEFAULT 'main',
  `auth_status` tinyint(1) NOT NULL DEFAULT 0,
  `css_class` varchar(100) DEFAULT '',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `navbar_items` (`label`, `url`, `display_order`, `is_active`, `section`, `auth_status`, `css_class`) VALUES
('Home', 'index.php', 1, 1, 'main', 0, ''),
('About Screening', 'about-genetic-screening.php', 2, 1, 'main', 0, ''),
('How It Works', 'how-it-works.php', 3, 1, 'main', 0, ''),
('Resources', 'resources.php', 4, 1, 'main', 0, ''),
('Contact', 'contact.php', 5, 1, 'main', 0, ''),
('Track Order', 'track-order.php', 6, 1, 'main', 0, ''),
('Interest List', 'intrest-list.php', 7, 1, 'main', 0, ''),
('Patient Login', 'user-portal/login.php', 10, 1, 'actions', 2, 'btn-nav btn-nav-outline'),
('Order Kit', 'request-kit.php', 11, 1, 'actions', 2, 'btn-nav btn-nav-primary'),
('Dashboard', 'user-portal/index.php', 10, 1, 'actions', 1, 'btn-nav btn-nav-outline'),
('Sign Out', 'user-portal/logout.php', 11, 1, 'actions', 1, 'btn-nav btn-nav-primary');