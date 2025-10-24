INSERT INTO `sys_tables` (`id`, `table`, `group`, `menuText`, `columns`, `link`, `icon`, `order_1`, `order_2`) VALUES

(20, 'data_files', 'Importación', 'Importar archivos', 0, '', 'upload', 100, 100),

(14, '', 'Importación', 'Procesar Todo', 0, 'process.php', 'cogs', 100, 60),
(15, '', 'Importación', 'Procesar Solo Vinos', 0, 'process_wines.php', 'cogs', 100, 50),
(16, '', 'Importación', 'Procesar Solo Aceites', 0, 'process_oils.php', 'cogs', 100, 40),
(17, '', 'Importación', 'Procesar Solo Destilados', 0, 'process_destillates.php', 'cogs', 100, 30);

INSERT INTO `sys_tables` (`id`, `table`, `group`, `menuText`, `columns`, `link`, `icon`, `order_1`, `order_2`) VALUES

(21, 'wines', 'Productos', 'Vinos', 0, '', 'th-list', 120, 100),
(22, 'oils', 'Productos', 'Aceites', 0, '', 'th-list', 120, 90),
(23, 'distillates', 'Productos', 'Destilados', 0, '', 'th-list', 120, 80);