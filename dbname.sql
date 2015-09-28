--
-- Table structure for table `log_errors`
--

CREATE TABLE IF NOT EXISTS `log_errors` (
  `type` int(2) unsigned NOT NULL,
  `error_type` varchar(100) NOT NULL,
  `called` varchar(1000) NOT NULL,
  `script` varchar(255) NOT NULL,
  `line` int(5) unsigned NOT NULL,
  `message` text NOT NULL,
  `count` int(6) unsigned NOT NULL,
  `last_seen` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_error` (`script`,`line`,`type`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `setting_id` varchar(100) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `role` int(1) unsigned NOT NULL DEFAULT '1',
  `full_name` varchar(255) NOT NULL,
  `gender` enum('male','female','none','') NOT NULL,
  `language` varchar(2) NOT NULL DEFAULT 'en',
  `last_login_time` datetime DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `create_user_id` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user_id` int(11) DEFAULT NULL,
  `activation` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `signature` text NOT NULL,
  `avatar` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;