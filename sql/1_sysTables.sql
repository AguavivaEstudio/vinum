--
-- Sys tables
--

DROP TABLE IF EXISTS aguaviva_vinum.`sys_files`;
DROP TABLE IF EXISTS aguaviva_vinum.`sys_languages`;
DROP TABLE IF EXISTS aguaviva_vinum.`sys_logs`;
DROP TABLE IF EXISTS aguaviva_vinum.`sys_permissions`;
DROP TABLE IF EXISTS aguaviva_vinum.`sys_profiles`;
DROP TABLE IF EXISTS aguaviva_vinum.`sys_tables`;
DROP TABLE IF EXISTS aguaviva_vinum.`sys_tags`;
DROP TABLE IF EXISTS aguaviva_vinum.`sys_users`;
DROP TABLE IF EXISTS aguaviva_vinum.`sys_config`;

CREATE TABLE IF NOT EXISTS `sys_config` (
  `id` int(10) NOT NULL,
  `key` varchar(30) NOT NULL COMMENT 'Key',
  `value` varchar(200) NOT NULL COMMENT 'Value'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sys_config` (`id`, `key`, `value`) VALUES
(1, 'ImageMaxWidth', '1200'),
(2, 'ImageMaxHeight', '900'),
(3, 'ImageResize', 'true'),
(4, 'ProjectName', 'Vinum'),
(5, 'DefaultLanguage', 'sp');


CREATE TABLE IF NOT EXISTS `sys_files` (
  `id` int(10) NOT NULL,
  `tableName` varchar(255) NOT NULL,
  `columnName` varchar(255) NOT NULL,
  `rowId` int(10) NOT NULL DEFAULT '0',
  `fileName` varchar(255) NOT NULL,
  `publish` tinyint(1) DEFAULT '0',
  `order` int(10) DEFAULT '0',
  `comment` varchar(255) DEFAULT NULL,
  `insertDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sys_files` (`id`, `tableName`, `columnName`, `rowId`, `fileName`, `publish`, `order`, `comment`, `insertDateTime`) VALUES
(2, 'equipo', 'file_imagen', 1, 'fotoperfil.jpeg', 1, 0, NULL, '2024-08-22 15:48:12');

CREATE TABLE IF NOT EXISTS `sys_languages` (
  `id` int(10) NOT NULL,
  `language` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sys_logs` (
  `id` int(10) NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user` varchar(255) DEFAULT NULL,
  `log` varchar(255) DEFAULT NULL,
  `log_detail` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sys_logs` (`id`, `timestamp`, `user`, `log`, `log_detail`) VALUES
(1, '2024-08-22 15:16:19', 'emiber@gmail.com', 'sign in', '::1 - ');

CREATE TABLE IF NOT EXISTS `sys_permissions` (
  `id` int(10) NOT NULL,
  `sys_profiles` int(10) NOT NULL,
  `sys_tables` int(10) DEFAULT '0',
  `sys_tags` int(10) DEFAULT '0',
  `view` tinyint(1) DEFAULT '0',
  `add` tinyint(1) DEFAULT '0',
  `update` tinyint(1) DEFAULT '0',
  `remove` tinyint(1) DEFAULT '0',
  `full` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sys_profiles` (
  `id` int(10) NOT NULL,
  `profile` varchar(255) NOT NULL,
  `sysadmin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sys_profiles` (`id`, `profile`, `sysadmin`) VALUES
(1, 'Sys Admin', 1);

CREATE TABLE IF NOT EXISTS `sys_tables` (
  `id` int(10) NOT NULL,
  `table` varchar(255) DEFAULT NULL COMMENT 'Tabla',
  `group` varchar(30) NOT NULL COMMENT 'Grupo',
  `menuText` varchar(30) NOT NULL COMMENT 'Menú',
  `columns` int(4) DEFAULT '0' COMMENT 'Columnas',
  `link` varchar(50) DEFAULT NULL COMMENT 'Link',
  `icon` varchar(100) DEFAULT NULL COMMENT 'Ícono',
  `order_1` int(5) NOT NULL COMMENT 'Órden 1',
  `order_2` int(5) NOT NULL COMMENT 'Órden 2'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sys_tables` (`id`, `table`, `group`, `menuText`, `columns`, `link`, `icon`, `order_1`, `order_2`) VALUES
(1, 'sys_files', 'Admin', 'Archivos', 0, '', 'files-o', 1000, 60),
(2, 'sys_languages', 'Admin', 'Idiomas', 0, '', 'language', 1000, 50),
(3, 'sys_logs', 'Admin', 'Logs', 0, '', 'registered', 1000, 70),
(4, 'sys_permissions', 'Admin', 'Permisos', 0, '', 'key', 1000, 40),
(5, 'sys_profiles', 'Admin', 'Perfiles', 0, '', 'users', 1000, 30),
(6, 'sys_tables', 'Admin', 'Tablas', 5, '', 'code', 1000, 80),
(7, 'sys_tags', 'Admin', 'Etiquetas', 0, '', 'tag', 1000, 20),
(8, 'sys_users', 'Admin', 'Usuarios', 0, '', 'user', 1000, 10),
(9, 'sys_config', 'Admin', 'Configuración', 0, '', 'cog', 1000, 10);

CREATE TABLE IF NOT EXISTS `sys_tags` (
  `id` int(10) NOT NULL,
  `tag` varchar(255) DEFAULT NULL,
  `description` varchar(2000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sys_tags` (`id`, `tag`, `description`) VALUES
(1, 'full', 'full');

CREATE TABLE IF NOT EXISTS `sys_users` (
  `id` int(10) NOT NULL,
  `name` varchar(150) NOT NULL COMMENT 'Nombre',
  `email` varchar(255) NOT NULL COMMENT 'email',
  `password` varchar(255) NOT NULL COMMENT 'Password',
  `profile` int(10) DEFAULT '0' COMMENT 'Perfíl',
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sys_users` (`id`, `name`, `email`, `password`, `profile`, `active`) VALUES
(1, 'Aguaviva', 'webmaster@aguaviva.com.ar', 'AguaViva00', 1, 1);

CREATE TABLE IF NOT EXISTS `vwuserspermissions` (
`id` int(10)
,`email` varchar(255)
,`password` varchar(255)
,`profile` int(10)
,`sysadmin` tinyint(1)
,`sys_tables` int(10)
,`sys_tags` int(10)
,`view` tinyint(1)
,`add` tinyint(1)
,`update` tinyint(1)
,`remove` tinyint(1)
,`full` tinyint(1)
);

DROP TABLE IF EXISTS `vwuserspermissions`;


ALTER TABLE `sys_config`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sys_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rowId` (`rowId`);

ALTER TABLE `sys_languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `language` (`language`);

ALTER TABLE `sys_logs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sys_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sys_profiles` (`sys_profiles`),
  ADD KEY `sys_tables` (`sys_tables`),
  ADD KEY `sys_tags` (`sys_tags`);

ALTER TABLE `sys_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `profile` (`profile`);

ALTER TABLE `sys_tables`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sys_tags`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sys_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `profile` (`profile`);

ALTER TABLE `sys_config`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `sys_files`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `sys_languages`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `sys_logs`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `sys_permissions`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `sys_profiles`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `sys_tables`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

ALTER TABLE `sys_tags`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `sys_users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `sys_permissions`
  ADD CONSTRAINT `sys_permissions_ibfk_1` FOREIGN KEY (`sys_profiles`) REFERENCES `sys_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sys_permissions_ibfk_2` FOREIGN KEY (`sys_tables`) REFERENCES `sys_tables` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sys_permissions_ibfk_3` FOREIGN KEY (`sys_tags`) REFERENCES `sys_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sys_users`
  ADD CONSTRAINT `sys_users_ibfk_1` FOREIGN KEY (`profile`) REFERENCES `sys_profiles` (`id`);