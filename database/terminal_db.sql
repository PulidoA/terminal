-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:8889
-- Tiempo de generación: 17-10-2025 a las 14:57:50
-- Versión del servidor: 8.0.40
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `terminal_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(60) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$8ID1ZVQwyg02bnePCXB4ee1c4l/Ogrd3Jim3ngAItCT7lXRI6X2UG', '2025-10-17 14:04:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `id` int UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `telefono` varchar(60) DEFAULT NULL,
  `web` varchar(255) DEFAULT NULL,
  `terminal` varchar(120) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id`, `nombre`, `telefono`, `web`, `terminal`, `created_at`) VALUES
(1, 'Expreso Brasilia', '01 8000 51 8001', 'https://www.expresobrasilia.com', 'Cartagena', '2025-10-17 00:48:45'),
(3, 'Copetran', '01 8000 112 422', 'https://www.copetran.com', 'Cartagena', '2025-10-17 01:01:13'),
(4, 'Rapido Ochoa', '01 8000 129 999', 'https://www.rapidoochoa.com', 'Medellín', '2025-10-17 01:01:13'),
(5, 'Bolivariano', '01 8000 123 222', 'https://www.bolivariano.com.co', 'Bogotá', '2025-10-17 01:01:13'),
(6, 'Berlinas del Fonce', '01 8000 112 600', 'https://www.berlinasdelfonce.com', 'Bucaramanga', '2025-10-17 01:01:13'),
(7, 'Expreso Palmira', '01 8000 114 444', 'https://www.expresopalmira.com', 'Cali', '2025-10-17 01:01:13'),
(8, 'Flota Magdalena', '01 8000 123 989', 'https://www.flotamagdalena.com.co', 'Santa Marta', '2025-10-17 01:01:13'),
(9, 'Cootranshuila', '01 8000 113 113', 'https://www.cootranshuila.com', 'Neiva', '2025-10-17 01:01:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutas`
--

CREATE TABLE `rutas` (
  `id` int UNSIGNED NOT NULL,
  `empresa_id` int UNSIGNED NOT NULL,
  `servicio` varchar(80) NOT NULL,
  `origen` varchar(120) NOT NULL,
  `destino` varchar(120) NOT NULL,
  `salida` time NOT NULL,
  `llegada` time NOT NULL,
  `disponibilidad` int UNSIGNED NOT NULL DEFAULT '0',
  `precio` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `rutas`
--

INSERT INTO `rutas` (`id`, `empresa_id`, `servicio`, `origen`, `destino`, `salida`, `llegada`, `disponibilidad`, `precio`, `created_at`) VALUES
(1, 1, 'Preferencial', 'Cartagena', 'Bogotá', '07:00:00', '15:00:00', 20, 120000.00, '2025-10-17 00:48:45'),
(3, 1, 'Lujo', 'Cartagena', 'Bogotá', '11:00:00', '19:00:00', 10, 180000.00, '2025-10-17 00:48:45'),
(5, 1, 'Preferencial', 'Cartagena', 'Barranquilla', '06:30:00', '08:00:00', 30, 25000.00, '2025-10-17 01:01:34'),
(6, 1, 'Ejecutivo', 'Cartagena', 'Santa Marta', '09:00:00', '12:00:00', 25, 35000.00, '2025-10-17 01:01:34'),
(7, 1, 'Lujo', 'Cartagena', 'Montería', '14:00:00', '18:00:00', 15, 45000.00, '2025-10-17 01:01:34'),
(11, 3, 'Lujo', 'Bogotá', 'Cali', '08:00:00', '18:00:00', 50, 90000.00, '2025-10-17 01:01:34'),
(12, 3, 'Preferencial', 'Bogotá', 'Medellín', '09:30:00', '18:30:00', 45, 85000.00, '2025-10-17 01:01:34'),
(13, 3, 'Ejecutivo', 'Bogotá', 'Pereira', '14:00:00', '22:00:00', 30, 75000.00, '2025-10-17 01:01:34'),
(14, 4, 'Preferencial', 'Bucaramanga', 'Bogotá', '05:30:00', '15:00:00', 40, 80000.00, '2025-10-17 01:01:34'),
(15, 4, 'Ejecutivo', 'Bucaramanga', 'Cúcuta', '09:00:00', '13:00:00', 25, 40000.00, '2025-10-17 01:01:34'),
(16, 5, 'Premium', 'Cali', 'Bogotá', '06:00:00', '16:00:00', 35, 88000.00, '2025-10-17 01:01:34'),
(17, 5, 'Preferencial', 'Cali', 'Medellín', '09:00:00', '19:00:00', 40, 95000.00, '2025-10-17 01:01:34'),
(18, 6, 'Express', 'Santa Marta', 'Cartagena', '06:00:00', '09:00:00', 30, 30000.00, '2025-10-17 01:01:34'),
(19, 6, 'Lujo', 'Santa Marta', 'Barranquilla', '08:00:00', '10:00:00', 28, 20000.00, '2025-10-17 01:01:34'),
(20, 7, 'Preferencial', 'Neiva', 'Bogotá', '05:00:00', '13:00:00', 32, 70000.00, '2025-10-17 01:01:34'),
(21, 7, 'Ejecutivo', 'Neiva', 'Florencia', '09:00:00', '12:30:00', 20, 45000.00, '2025-10-17 01:01:34');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rutas`
--
ALTER TABLE `rutas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rutas_empresas` (`empresa_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `rutas`
--
ALTER TABLE `rutas`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `rutas`
--
ALTER TABLE `rutas`
  ADD CONSTRAINT `fk_rutas_empresas` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
