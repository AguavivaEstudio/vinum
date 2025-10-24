--TABLE CREATION
DROP TABLE IF EXISTS aguaviva_vinum.`data_files`;
DROP TABLE IF EXISTS aguaviva_vinum.`wines_import`;
DROP TABLE IF EXISTS aguaviva_vinum.`oils_import`;
DROP TABLE IF EXISTS aguaviva_vinum.`destillates_import`;

CREATE TABLE `data_files` (
  `id` int(10) NOT NULL,
  `nombre` varchar(50) NOT NULL COMMENT 'Nombre',
  `file_clientes` int(1) DEFAULT NULL COMMENT 'Archivo'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `data_files` (`id`, `nombre`, `file_clientes`) VALUES
(1, 'wines', NULL),
(2, 'oils', NULL),
(3, 'distillates', NULL);

ALTER TABLE `data_files`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `data_files`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;


CREATE TABLE `wines_import` (
  `titulo` varchar(350) NOT NULL,
  `precio` varchar(255) DEFAULT NULL,
  `codigo` varchar(255) DEFAULT NULL,
  `stock` int(6) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `oils_import` (
  `titulo` varchar(350) NOT NULL,
  `precio` varchar(255) DEFAULT NULL,
  `codigo` varchar(255) DEFAULT NULL,
  `stock` int(6) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `destillates_import` (
  `titulo` varchar(350) NOT NULL,
  `precio` varchar(255) DEFAULT NULL,
  `codigo` varchar(255) DEFAULT NULL,
  `stock` int(6) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


INSERT INTO `sys_tables` (`id`, `table`, `group`, `menuText`, `columns`, `link`, `icon`, `order_1`, `order_2`) VALUES

(20, 'data_files', 'Importación', 'Importar archivos', 0, '', 'upload', 100, 100),
-- (11, 'wines_files', 'Importación', 'Importar Vinos', 0, '', 'upload', 100, 90),
-- (12, 'oils_files', 'Importación', 'Importar Aceites', 0, '', 'upload', 100, 80),
-- (13, 'distillates_files', 'Importación', 'Importar Destilados', 0, '', 'upload', 100, 70),

(14, '', 'Importación', 'Procesar Vinos, Aceites y Destilados', 0, 'process.php', 'cogs', 100, 60),
(15, '', 'Importación', 'Procesar Solo Vinos', 0, 'process_wines.php', 'cogs', 100, 50),
(16, '', 'Importación', 'Procesar Solo Aceites', 0, 'process_precio.php', 'cogs', 100, 40),
(17, '', 'Importación', 'Procesar Solo Destilados', 0, 'process_precio.php', 'cogs', 100, 30);

ALTER TABLE `sys_tables`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sys_tables`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;