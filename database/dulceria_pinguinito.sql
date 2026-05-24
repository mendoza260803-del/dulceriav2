-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-05-2026 a las 19:59:58
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dulceria_pinguinito`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_venta`
--

CREATE TABLE `detalles_venta` (
  `id` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalles_venta`
--

INSERT INTO `detalles_venta` (`id`, `id_venta`, `nombre_producto`, `cantidad`, `precio_unitario`, `total`) VALUES
(12, 10, 'paleta', 3, 3.00, 9.00),
(13, 11, 'paleta', 9, 3.00, 27.00),
(14, 11, 'bubu lubu', 5, 10.00, 50.00),
(15, 12, 'paleta', 1, 3.00, 3.00),
(16, 12, 'bubu lubu', 1, 10.00, 10.00),
(17, 12, 'muegano', 1, 12.00, 12.00),
(18, 13, 'paleta', 4, 3.00, 12.00),
(19, 14, 'bubu lubu', 2, 10.00, 20.00),
(20, 14, 'muegano', 2, 12.00, 24.00),
(21, 15, 'bubu lubu', 2, 10.00, 20.00),
(22, 15, 'muegano', 2, 12.00, 24.00),
(23, 16, 'bubu lubu', 1, 10.00, 10.00),
(24, 16, 'muegano', 1, 12.00, 12.00),
(25, 17, 'bubu lubu', 1, 10.00, 10.00),
(26, 17, 'muegano', 1, 12.00, 12.00),
(27, 18, 'muegano', 2, 12.00, 24.00),
(28, 18, 'bubu lubu', 2, 10.00, 20.00),
(29, 19, 'bubu lubu', 3, 10.00, 30.00),
(30, 19, 'muegano', 2, 12.00, 24.00),
(31, 20, 'bubu lubu', 17, 10.00, 170.00),
(32, 20, 'muegano', 2, 12.00, 24.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `stock` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `stock`, `precio`) VALUES
(1, 'paleta', 0, 3.00),
(2, 'bubu lubu', 0, 10.00),
(3, 'muegano', 67, 12.00),
(4, 'Paleta Payaso', 50, 7.50),
(5, 'Chicle Bubbaloo', 100, 3.00),
(6, 'Mazapan de la Rosa', 75, 5.00),
(7, 'Carlos V', 60, 10.00),
(8, 'Snickers', 80, 12.00),
(9, 'M&M\'s (Bolsa)', 90, 15.00),
(10, 'Skittles', 110, 14.00),
(11, 'Dulce Vero Mango', 65, 6.00),
(12, 'Pelon Pelo Rico', 55, 8.00),
(13, 'Lucas Muecas', 70, 9.00),
(14, 'Chocolatina Milky Way', 85, 11.00),
(15, 'Gomitas Panditas', 120, 13.00),
(16, 'Tamarindo Rellerindo', 45, 6.50),
(17, 'Pulparindo', 95, 4.50),
(18, 'Kranky', 78, 10.50),
(19, 'Kit Kat', 105, 12.50),
(20, 'Bubulubu', 58, 8.50),
(21, 'Cacahuates Japoneses', 130, 11.50),
(22, 'Papas Sabritas', 40, 18.00),
(23, 'Doritos', 72, 17.50),
(24, 'Cheetos', 88, 16.00),
(25, 'Ruffles', 62, 19.00),
(26, 'Fritos', 115, 15.50),
(27, 'Churrumais', 52, 14.50),
(28, 'Takis', 98, 20.00),
(29, 'Palomitas Act II', 77, 22.00),
(30, 'Galletas Oreo (Paquete)', 102, 25.00),
(31, 'Emperador (Sabor)', 68, 16.50),
(32, 'Principe (Sabor)', 82, 17.00),
(33, 'Marías', 125, 12.00),
(34, 'Gamesa (Variedad)', 57, 19.50),
(35, 'Chokis', 93, 18.50),
(36, 'Pingüinos', 74, 21.00),
(37, 'Submarinos', 108, 23.00),
(38, 'Donas Bimbo', 61, 15.00),
(39, 'Pan Dulce (Variedad)', 112, 14.00),
(40, 'Refresco Coca-Cola (Lata)', 87, 16.00),
(41, 'Refresco Pepsi (Lata)', 79, 15.50),
(42, 'Refresco Fanta (Lata)', 101, 15.00),
(43, 'Agua Embotellada', 140, 10.00),
(44, 'Jugo (Variedad)', 59, 13.50),
(45, 'Chocolates Turín', 84, 30.00),
(46, 'Ferrero Rocher', 67, 35.00),
(47, 'Kinder Bueno', 91, 28.00),
(48, 'Paleta Magnum', 76, 26.00),
(49, 'Helado (Vaso)', 103, 24.00),
(50, 'Chicles Trident', 118, 11.00),
(51, 'Mentos', 54, 9.50),
(52, 'Skwinkles', 99, 7.00),
(53, 'Banderilla', 69, 10.00),
(54, 'Churrito', 81, 12.00),
(55, 'Tostitos', 122, 17.00),
(56, 'Botana (Variedad)', 56, 16.00),
(57, 'Cacahuates Salados', 107, 13.00),
(58, 'Semillas (Variedad)', 63, 14.50),
(59, 'Dulce de Leche', 89, 12.50),
(60, 'Gomitas (Ácidas)', 111, 15.00),
(61, 'Paleta de Hielo', 73, 5.50),
(62, 'Chicle Clásico', 106, 2.50),
(63, 'Caramelos Macizos', 66, 4.00),
(64, 'Dulce de Tamarindo (Bolsa)', 92, 8.00),
(65, 'Chocolates Abuelita', 78, 18.00),
(66, 'Galletas Saladas', 119, 13.50),
(67, 'Papas Fritas (Bolsa Grande)', 51, 25.00),
(68, 'Botana Mixta', 97, 21.50),
(69, 'Paleta de Caramelo', 64, 3.50),
(70, 'Dulce de Coco', 86, 6.00),
(71, 'Chicle Canel\'s', 109, 4.50),
(72, 'Galletas (Sabor)', 53, 15.50),
(73, 'Dulce de Leche Quemada', 94, 7.50),
(74, 'Cacahuates Enchilados', 71, 12.00),
(75, 'Skwinkles Rellenos', 104, 8.50),
(76, 'Banderilla Tamarindo', 58, 9.00),
(77, 'Churrito Enchilado', 83, 11.50),
(78, 'Tostitos Salsa', 121, 18.00),
(79, 'Botana (Picante)', 55, 17.50),
(80, 'Cacahuates con Chile', 96, 14.00),
(81, 'Semillas de Girasol', 70, 10.50),
(82, 'Dulce de Tamarindo (Cajita)', 113, 9.50),
(83, 'Chocolates Hershey\'s', 60, 19.50),
(84, 'Galletas (Chocolate)', 88, 16.50),
(85, 'Papas Fritas (Bolsa Mediana)', 110, 22.00),
(86, 'Botana con Queso', 52, 18.50),
(87, 'Paleta de Limón', 98, 4.00),
(88, 'Dulce de Frutas', 75, 6.50),
(89, 'Chicle de Menta', 105, 3.00),
(90, 'Caramelos de Leche', 62, 5.00),
(91, 'Dulce de Cajeta', 89, 7.00),
(92, 'Gomitas de Osito', 115, 14.00),
(93, 'Paleta de Nata', 57, 8.00),
(94, 'Chicle de Fresa', 93, 3.00),
(95, 'Caramelos Masticables', 78, 5.50),
(96, 'Dulce de Piña', 102, 6.00),
(97, 'Chocolates con Almendras', 68, 25.00),
(98, 'Galletas con Crema', 82, 15.00),
(99, 'Papas Fritas (Sabor Limón)', 120, 19.00),
(100, 'Botana con Chile y Limón', 59, 20.00),
(101, 'Cacahuates con Sal y Limón', 91, 13.50),
(102, 'Semillas con Chile', 74, 11.00),
(103, 'Dulce de Mango', 108, 7.50),
(104, 'Chocolates Oscuros', 65, 32.00),
(105, 'Galletas Integrales', 85, 17.50),
(106, 'Palomitas (Bolsa)', 111, 16.00),
(107, 'Botana de Maíz', 51, 14.00),
(108, 'Paleta de Sandía', 97, 4.50),
(109, 'Dulce de Guayaba', 64, 5.50),
(110, 'Chicle con Fruta', 100, 3.50),
(111, 'Caramelos de Café', 79, 4.00),
(112, 'Dulce de Fresa', 114, 6.00),
(113, 'Chocolates con Nueces', 56, 28.00),
(114, 'Galletas de Avena', 90, 18.00),
(115, 'Papas Fritas (Sabor Queso)', 77, 19.50),
(116, 'Botana de Cacahuate', 103, 15.00),
(117, 'Paleta de Tamarindo', 61, 6.00),
(118, 'Dulce de Ciruela', 87, 7.00),
(119, 'Chicle Sin Azúcar', 117, 4.00),
(120, 'Caramelos de Menta Fuerte', 53, 5.00),
(121, 'Dulce de Coco Rallado', 95, 6.50),
(122, 'Chocolates Blancos', 72, 26.00),
(123, 'Galletas Rellenas', 109, 20.00),
(124, 'Palomitas (Microondas)', 67, 24.00),
(125, 'Botana de Garbanzo', 84, 12.50),
(126, 'Paleta de Grosella', 112, 5.00),
(127, 'Dulce de Durazno', 58, 7.00),
(128, 'Chicle de Menta Verde', 99, 3.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','empleado') NOT NULL DEFAULT 'empleado',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `apellidos`, `nombre`, `email`, `password`, `rol`, `fecha_creacion`) VALUES
(2, 'Demo', 'Administrador', 'admin@demo.com', '$2y$10$2/QYruYUQKeg/wmcUzwK7OLUL6ObgVl5w5Bsq6yW5iVfih5kL4Zja', 'admin', '2025-03-15 01:28:18'),
(6, 'Demo', 'Empleado Uno', 'empleado@demo.com', '$2y$10$jtC5sGPuKju7fTUz6D0hC.UxZgeFeWftpTadBGz6ZsAPyCoEMaTKO', 'empleado', '2025-03-18 23:57:24'),
(7, 'Demo', 'Empleado Dos', 'empleado2@demo.com', '$2y$10$jtC5sGPuKju7fTUz6D0hC.UxZgeFeWftpTadBGz6ZsAPyCoEMaTKO', 'empleado', '2025-03-25 20:17:46'),
(13, 'Demo', 'Administrador Dos', 'admin2@demo.com', '$2y$10$2/QYruYUQKeg/wmcUzwK7OLUL6ObgVl5w5Bsq6yW5iVfih5kL4Zja', 'admin', '2025-03-25 22:32:29'),
(16, 'Demo', 'Administrador Tres', 'admin3@demo.com', '$2y$10$2/QYruYUQKeg/wmcUzwK7OLUL6ObgVl5w5Bsq6yW5iVfih5kL4Zja', 'admin', '2025-03-25 23:46:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `id_usuario`, `fecha`, `total`) VALUES
(10, 2, '2025-03-14 20:21:27', 9.00),
(11, 2, '2025-03-14 20:55:44', 77.00),
(12, 6, '2025-03-18 19:50:07', 25.00),
(13, 2, '2025-03-21 21:06:44', 12.00),
(14, 2, '2025-03-25 14:50:05', 44.00),
(15, 2, '2025-03-25 14:50:25', 44.00),
(16, 2, '2025-03-25 14:58:20', 22.00),
(17, 2, '2025-03-25 15:07:18', 22.00),
(18, 6, '2025-03-25 15:14:59', 44.00),
(19, 6, '2025-03-25 15:15:22', 54.00),
(20, 7, '2025-03-25 15:15:48', 194.00);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalles_venta`
--
ALTER TABLE `detalles_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_venta` (`id_venta`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalles_venta`
--
ALTER TABLE `detalles_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalles_venta`
--
ALTER TABLE `detalles_venta`
  ADD CONSTRAINT `detalles_venta_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
