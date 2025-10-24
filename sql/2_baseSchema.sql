DROP TABLE IF EXISTS aguaviva_vinum.`data_files`;
DROP TABLE IF EXISTS aguaviva_vinum.`wines_import`;
DROP TABLE IF EXISTS aguaviva_vinum.`oils_import`;
DROP TABLE IF EXISTS aguaviva_vinum.`distillates_import`;
DROP TABLE IF EXISTS aguaviva_vinum.`wines`;
DROP TABLE IF EXISTS aguaviva_vinum.`oils`;
DROP TABLE IF EXISTS aguaviva_vinum.`distillates`;

###FILE IMPORTS

CREATE TABLE IF NOT EXISTS `data_files` (
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


CREATE TABLE IF NOT EXISTS `wines_import` (
  `id` int(10) NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'Nombre',
  `brand` varchar(255) DEFAULT NULL COMMENT 'Marca',
  `grape` varchar(255) DEFAULT NULL COMMENT 'Uva',
  `type` varchar(255) DEFAULT NULL COMMENT 'Tipo',
  `country` varchar(255) DEFAULT NULL COMMENT 'País',
  `region` varchar(255) DEFAULT NULL COMMENT 'Región',
  `subregion` varchar(255) DEFAULT NULL COMMENT 'Sub-Región',
  `amount` int(10) DEFAULT NULL COMMENT 'mL.',
  `segment` varchar(255) DEFAULT NULL COMMENT 'Segmento',
  `wine_stopper` varchar(255) DEFAULT NULL COMMENT 'Tipo de Tapón',
  `is_organic` varchar(255) DEFAULT NULL COMMENT 'Orgánico',
  `other` varchar(255) DEFAULT NULL COMMENT 'Otros',
  `sku` int(30) DEFAULT NULL COMMENT 'SKU',
  `barcode` varchar(255) DEFAULT NULL COMMENT 'Código de barras',
  `active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Activo',
  `order` int(10) DEFAULT NULL COMMENT 'Orden'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE IF NOT EXISTS `oils_import` (
  `id` int(10) NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'Nombre',
  `brand` varchar(255) DEFAULT NULL COMMENT 'Marca',
  `country` varchar(255) DEFAULT NULL COMMENT 'País',
  `region` varchar(255) DEFAULT NULL COMMENT 'Región',
  `amount` int(10) DEFAULT NULL COMMENT 'mL.',
  `segment` varchar(255) DEFAULT NULL COMMENT 'Segmento',
  `sku` int(30) DEFAULT NULL COMMENT 'SKU',
  `barcode` varchar(255) DEFAULT NULL COMMENT 'Código de barras',
  `active` tinyint(1) DEFAULT 1 COMMENT 'Activo',
  `order` int(10) DEFAULT NULL COMMENT 'Orden'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE IF NOT EXISTS `distillates_import` (
  `id` int(10) NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'Nombre',
  `brand` varchar(255) DEFAULT NULL COMMENT 'Marca',
  `type` varchar(255) DEFAULT NULL COMMENT 'Tipo',
  `amount` int(10) DEFAULT NULL COMMENT 'mL.',
  `segment` varchar(255) DEFAULT NULL COMMENT 'Segmento',
  `other` varchar(255) DEFAULT NULL COMMENT 'Otros',
  `sku` int(30) DEFAULT NULL COMMENT 'SKU',
  `barcode` varchar(255) DEFAULT NULL COMMENT 'Código de barras',
  `active` tinyint(1) DEFAULT 1 COMMENT 'Activo',
  `order` int(10) DEFAULT NULL COMMENT 'Orden'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


###DATA
CREATE TABLE `wines` (
  `id` int(10) NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'Nombre',
  `brand` varchar(255) DEFAULT NULL COMMENT 'Marca',
  `grape` varchar(255) DEFAULT NULL COMMENT 'Uva',
  `type` varchar(255) DEFAULT NULL COMMENT 'Tipo',
  `country` varchar(255) DEFAULT NULL COMMENT 'País',
  `region` varchar(255) DEFAULT NULL COMMENT 'Región',
  `subregion` varchar(255) DEFAULT NULL COMMENT 'Sub-Región',
  `amount` int(10) DEFAULT NULL COMMENT 'mL.',
  `segment` varchar(255) DEFAULT NULL COMMENT 'Segmento',
  `wine_stopper` varchar(255) DEFAULT NULL COMMENT 'Tipo de Tapón',
  `is_organic` varchar(255) DEFAULT NULL COMMENT 'Orgánico',
  `other` varchar(255) DEFAULT NULL COMMENT 'Otros',
  `sku` int(30) DEFAULT NULL COMMENT 'SKU',
  `barcode` varchar(255) DEFAULT NULL COMMENT 'Código de barras',
  `active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Activo',
  `order` int(10) DEFAULT NULL COMMENT 'Orden'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `wines`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `wines`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


CREATE TABLE `oils` (
  `id` int(10) NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'Nombre',
  `brand` varchar(255) DEFAULT NULL COMMENT 'Marca',
  `country` varchar(255) DEFAULT NULL COMMENT 'País',
  `region` varchar(255) DEFAULT NULL COMMENT 'Región',
  `amount` int(10) DEFAULT NULL COMMENT 'mL.',
  `segment` varchar(255) DEFAULT NULL COMMENT 'Segmento',
  `sku` int(30) DEFAULT NULL COMMENT 'SKU',
  `barcode` varchar(255) DEFAULT NULL COMMENT 'Código de barras',
  `active` tinyint(1) DEFAULT 1 COMMENT 'Activo',
  `order` int(10) DEFAULT NULL COMMENT 'Orden'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `oils`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `oils`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


###Destilados
CREATE TABLE `distillates` (
  `id` int(10) NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'Nombre',
  `brand` varchar(255) DEFAULT NULL COMMENT 'Marca',
  `type` varchar(255) DEFAULT NULL COMMENT 'Tipo',
  `amount` int(10) DEFAULT NULL COMMENT 'mL.',
  `segment` varchar(255) DEFAULT NULL COMMENT 'Segmento',
  `other` varchar(255) DEFAULT NULL COMMENT 'Otros',
  `sku` int(30) DEFAULT NULL COMMENT 'SKU',
  `barcode` varchar(255) DEFAULT NULL COMMENT 'Código de barras',
  `active` tinyint(1) DEFAULT 1 COMMENT 'Activo',
  `order` int(10) DEFAULT NULL COMMENT 'Orden'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `distillates`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `distillates`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


###INSERTS

INSERT INTO `wines` (`id`, `name`, `brand`, `grape`, `type`, `country`, `region`, `subregion`, `amount`, `segment`, `wine_stopper`, `is_organic`, `other`, `sku`, `barcode`, `active`, `order`) VALUES (1, 'BARBERA D\'ALBA D.O.C. SOVRANA', 'BATASIOLO', 'BARBERA', 'VINO TINTO', 'ITALIA', 'PIAMONTE', 'BARBERA D\'ALBA D.O.C', 750, 'UPPER MAINSTREAM', 'CORCHO', 'SI', 'N/A', 184552, '89744765604', 1, NULL);

INSERT INTO `oils` ( `id`, `name`, `brand`, `country`, `region`, `amount`, `segment`, `sku`, `barcode`, `active`, `order` ) VALUES ( 1, 'PAGO DE VALDECUEVAS ACEITE DE OLIVA EXTRA VIRGEN', 'VALDECUEVAS', 'ESPAÑA', 'CASTILLA Y LEÓN', 500, 'PREMIUM', 195201, '8437013217019', 1, NULL );

INSERT INTO `distillates` ( `id`, `name`, `brand`, `type`, `amount`, `segment`, `other`, `sku`, `barcode`, `active`, `order` ) VALUES ( 1, 'DON JULIO TEQUILA REAL', 'DON JULIO DELUXE', 'TEQUILA', 750, 'ULTRA PREMIUM', 'N/A', 84577, '674545000124', 1, NULL );