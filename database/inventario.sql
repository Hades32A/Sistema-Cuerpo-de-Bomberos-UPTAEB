-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 16-06-2026 a las 00:02:52
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bomberos-proyecto`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `centros-salud`
--

CREATE TABLE `centros-salud` (
  `id_centro` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `direccion` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_insumos_llamda`
--

CREATE TABLE `detalles_insumos_llamda` (
  `Id_detalles` int(11) NOT NULL,
  `Id_LLamadas` int(11) NOT NULL,
  `Id_insumos` int(11) NOT NULL,
  `Cantidad_gastada` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalles_insumos_llamda`
--

INSERT INTO `detalles_insumos_llamda` (`Id_detalles`, `Id_LLamadas`, `Id_insumos`, `Cantidad_gastada`) VALUES
(1, 1, 1, 3),
(2, 1, 2, 1),
(3, 2, 3, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `donante_insumo`
--

CREATE TABLE `donante_insumo` (
  `id_donacion` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `cantidad` int(11) NOT NULL,
  `id_ente` int(11) NOT NULL,
  `id_insumo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ente_donante`
--

CREATE TABLE `ente_donante` (
  `id_ente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos`
--

CREATE TABLE `insumos` (
  `Id_insumos` int(11) NOT NULL,
  `Tipo_insumos` varchar(50) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Fecha_vencimiento` date NOT NULL,
  `Estado` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `insumos`
--

INSERT INTO `insumos` (`Id_insumos`, `Tipo_insumos`, `Nombre`, `Cantidad`, `Fecha_vencimiento`, `Estado`) VALUES
(1, 'Médico', 'Gasa Estéril', 100, '2028-05-12', 0),
(2, 'Médico', 'Solución Fisiol', 50, '2027-10-30', 0),
(3, 'Seguridad', 'Guantes Látex', 500, '2029-01-15', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llamada`
--

CREATE TABLE `llamada` (
  `Id_llamadas` int(11) NOT NULL,
  `Tipo_emergencia` varchar(50) NOT NULL,
  `Fecha` date NOT NULL,
  `Hora` time NOT NULL,
  `Observaciones` varchar(500) NOT NULL,
  `Ubicacion` varchar(200) NOT NULL,
  `Cedula_paciente` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `llamada`
--

INSERT INTO `llamada` (`Id_llamadas`, `Tipo_emergencia`, `Fecha`, `Hora`, `Observaciones`, `Ubicacion`, `Cedula_paciente`) VALUES
(1, 'Médica', '2026-06-01', '09:30:00', 'Paciente con desmayo y baja tensión en pasillo B.', 'Sede Central UPTAEB', 14253647),
(2, 'Accidente', '2026-06-20', '14:15:00', 'Colisión menor de moto cerca de la entrada principal.', 'Av. Los Horcones', 23456789);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paciente`
--

CREATE TABLE `paciente` (
  `Cedula` int(8) NOT NULL,
  `Nombre` varchar(50) DEFAULT NULL,
  `Apellido` varchar(50) DEFAULT NULL,
  `Direccion` varchar(150) NOT NULL,
  `PNF` varchar(50) NOT NULL,
  `Cargo` varchar(50) NOT NULL,
  `tipo_paciente` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paciente`
--

INSERT INTO `paciente` (`Cedula`, `Nombre`, `Apellido`, `Direccion`, `PNF`, `Cargo`, `tipo_paciente`) VALUES
(14253647, 'Carlos', 'Mendoza', 'Av. Los Abogados', 'Informática', 'Estudiante', 'Interno'),
(18529634, 'María', 'Rodríguez', 'Calle 25 con 19', 'Contaduría', 'Docente', 'Interno'),
(23456789, 'Juan', 'Pérez', 'Carrera 15, Sector Centro', 'Ninguno', 'Ninguno', 'Externo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal`
--

CREATE TABLE `personal` (
  `Cedula` int(8) NOT NULL,
  `Rango` varchar(30) DEFAULT NULL,
  `Nombre` varchar(50) DEFAULT NULL,
  `Apellido` varchar(50) DEFAULT NULL,
  `Telefono` varchar(20) NOT NULL,
  `estado_personal` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personal`
--

INSERT INTO `personal` (`Cedula`, `Rango`, `Nombre`, `Apellido`, `Telefono`, `estado_personal`) VALUES
(15668992, 'Sargento', 'Luis', 'Gómez', '0412555123', 'Activo'),
(19334556, 'Cabo Primero', 'Ana', 'Martínez', '0424555987', 'Activo'),
(21554778, 'Distinguido', 'Pedro', 'Torres', '0416555456', 'De Vacaciones');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_asignado`
--

CREATE TABLE `personal_asignado` (
  `Id_LLamadas` int(11) NOT NULL,
  `cedula_personal` int(8) NOT NULL,
  `Rol_emergencia` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personal_asignado`
--

INSERT INTO `personal_asignado` (`Id_LLamadas`, `cedula_personal`, `Rol_emergencia`) VALUES
(1, 15668992, 'Paramédico Principal'),
(1, 19334556, 'Chofer de Ambulancia'),
(2, 15668992, 'Comandante de Incidente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `Id_servicio` int(11) NOT NULL,
  `id_llamada` int(11) NOT NULL,
  `cedula_paciente` int(8) NOT NULL,
  `tipo_servicio` varchar(50) NOT NULL,
  `hora_servicio` time NOT NULL,
  `id_centro` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculo`
--

CREATE TABLE `vehiculo` (
  `Placa` varchar(15) NOT NULL,
  `Tipo` varchar(50) NOT NULL,
  `Marca` varchar(50) NOT NULL,
  `Año` int(4) NOT NULL,
  `Modelo` varchar(50) NOT NULL,
  `Estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehiculo`
--

INSERT INTO `vehiculo` (`Placa`, `Tipo`, `Marca`, `Año`, `Modelo`, `Estado`) VALUES
('1010', 'Ambulancia', 'Toyota', 2018, 'Land Cruiser', 'Operativo'),
('2020', 'Unidad Rescate', 'Ford', 2015, 'F-350', 'Operativo'),
('3030', 'Logística', 'Chevrolet', 2012, 'Luv D-Max', 'En Mantenimiento');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos_asignados`
--

CREATE TABLE `vehiculos_asignados` (
  `Id_vehiculo_asignad` int(11) NOT NULL,
  `Id_llamadas` int(11) NOT NULL,
  `Placa` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehiculos_asignados`
--

INSERT INTO `vehiculos_asignados` (`Id_vehiculo_asignad`, `Id_llamadas`, `Placa`) VALUES
(1, 1, '1010'),
(2, 2, '2020');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `rol` enum('administrador','bombero') NOT NULL DEFAULT 'bombero'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes`
--

CREATE TABLE `reportes` (
  `id_reporte` int(11) NOT NULL,
  `tipo_informe` varchar(50) NOT NULL,
  `periodo` varchar(20) NOT NULL,
  `creado_por` varchar(50) NOT NULL,
  `formato_exportado` enum('EXCEL','WORD') NOT NULL DEFAULT 'EXCEL'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `centros-salud`
--
ALTER TABLE `centros-salud`
  ADD PRIMARY KEY (`id_centro`);

--
-- Indices de la tabla `detalles_insumos_llamda`
--
ALTER TABLE `detalles_insumos_llamda`
  ADD PRIMARY KEY (`Id_detalles`),
  ADD KEY `Id_LLamadas` (`Id_LLamadas`),
  ADD KEY `Id_insumos` (`Id_insumos`);

--
-- Indices de la tabla `donante_insumo`
--
ALTER TABLE `donante_insumo`
  ADD PRIMARY KEY (`id_donacion`),
  ADD KEY `id_ente` (`id_ente`),
  ADD KEY `id_insumo` (`id_insumo`);

--
-- Indices de la tabla `ente_donante`
--
ALTER TABLE `ente_donante`
  ADD PRIMARY KEY (`id_ente`);

--
-- Indices de la tabla `insumos`
--
ALTER TABLE `insumos`
  ADD PRIMARY KEY (`Id_insumos`);

--
-- Indices de la tabla `llamada`
--
ALTER TABLE `llamada`
  ADD PRIMARY KEY (`Id_llamadas`),
  ADD KEY `Fk_Cedula` (`Cedula_paciente`);

--
-- Indices de la tabla `paciente`
--
ALTER TABLE `paciente`
  ADD PRIMARY KEY (`Cedula`);

--
-- Indices de la tabla `personal`
--
ALTER TABLE `personal`
  ADD PRIMARY KEY (`Cedula`);

--
-- Indices de la tabla `personal_asignado`
--
ALTER TABLE `personal_asignado`
  ADD KEY `cedula_personal` (`cedula_personal`),
  ADD KEY `Id_LLamadas` (`Id_LLamadas`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`Id_servicio`),
  ADD KEY `id_llamada` (`id_llamada`),
  ADD KEY `cedula_paciente` (`cedula_paciente`),
  ADD KEY `id_centro` (`id_centro`);

--
-- Indices de la tabla `vehiculo`
--
ALTER TABLE `vehiculo`
  ADD PRIMARY KEY (`Placa`);

--
-- Indices de la tabla `vehiculos_asignados`
--
ALTER TABLE `vehiculos_asignados`
  ADD PRIMARY KEY (`Id_vehiculo_asignad`),
  ADD KEY `Id_LLamadas` (`Id_llamadas`),
  ADD KEY `Placa` (`Placa`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `reportes`
--
ALTER TABLE `reportes`
  ADD PRIMARY KEY (`id_reporte`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalles_insumos_llamda`
--
ALTER TABLE `detalles_insumos_llamda`
  MODIFY `Id_detalles` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `donante_insumo`
--
ALTER TABLE `donante_insumo`
  MODIFY `id_donacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ente_donante`
--
ALTER TABLE `ente_donante`
  MODIFY `id_ente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `insumos`
--
ALTER TABLE `insumos`
  MODIFY `Id_insumos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `llamada`
--
ALTER TABLE `llamada`
  MODIFY `Id_llamadas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `Id_servicio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `vehiculos_asignados`
--
ALTER TABLE `vehiculos_asignados`
  MODIFY `Id_vehiculo_asignad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reportes`
--
ALTER TABLE `reportes`
  MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalles_insumos_llamda`
--
ALTER TABLE `detalles_insumos_llamda`
  ADD CONSTRAINT `detalles_insumos_llamda_ibfk_1` FOREIGN KEY (`Id_insumos`) REFERENCES `insumos` (`Id_insumos`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalles_insumos_llamda_ibfk_2` FOREIGN KEY (`Id_LLamadas`) REFERENCES `llamada` (`Id_llamadas`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `donante_insumo`
--
ALTER TABLE `donante_insumo`
  ADD CONSTRAINT `donante_insumo_ibfk_1` FOREIGN KEY (`id_ente`) REFERENCES `ente_donante` (`id_ente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `donante_insumo_ibfk_2` FOREIGN KEY (`id_insumo`) REFERENCES `insumos` (`Id_insumos`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `llamada`
--
ALTER TABLE `llamada`
  ADD CONSTRAINT `llamada_ibfk_1` FOREIGN KEY (`Cedula_paciente`) REFERENCES `paciente` (`Cedula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `personal_asignado`
--
ALTER TABLE `personal_asignado`
  ADD CONSTRAINT `personal_asignado_ibfk_2` FOREIGN KEY (`cedula_personal`) REFERENCES `personal` (`Cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `personal_asignado_ibfk_3` FOREIGN KEY (`Id_LLamadas`) REFERENCES `llamada` (`Id_llamadas`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD CONSTRAINT `servicio_ibfk_2` FOREIGN KEY (`cedula_paciente`) REFERENCES `paciente` (`Cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `servicio_ibfk_3` FOREIGN KEY (`id_llamada`) REFERENCES `llamada` (`Id_llamadas`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `servicio_ibfk_4` FOREIGN KEY (`id_centro`) REFERENCES `centros-salud` (`id_centro`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `vehiculos_asignados`
--
ALTER TABLE `vehiculos_asignados`
  ADD CONSTRAINT `vehiculos_asignados_ibfk_2` FOREIGN KEY (`Placa`) REFERENCES `vehiculo` (`Placa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vehiculos_asignados_ibfk_3` FOREIGN KEY (`Id_llamadas`) REFERENCES `llamada` (`Id_llamadas`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

--
-- Usuarios de prueba para el módulo de login (descomentar para insertar)
-- Administrador: admin123 / admin12345
-- Bombero: bombero123 / bombero12345
--
-- INSERT INTO `usuarios` (`id`, `usuario`, `password`, `nombre`, `rol`) VALUES
-- (1, 'admin123', '$2y$10$BlnnMLzWippWSe1sRL.nS.Gr3Jvl/R.xzgaIX7coQHpSi0ISp8/xm', 'Administrador General', 'administrador'),
-- (2, 'bombero123', '$2y$10$uDwncMMeakjU6c98882RJefL81k42TECU4zy0JwX72f5ZpRorWUf6', 'Bombero Operativo', 'bombero');

--
-- Si ya existen filas con hashes incorrectos, ejecutar estos UPDATE:
--
-- UPDATE `usuarios` SET `password` = '$2y$10$BlnnMLzWippWSe1sRL.nS.Gr3Jvl/R.xzgaIX7coQHpSi0ISp8/xm' WHERE `usuario` = 'admin123';
-- UPDATE `usuarios` SET `password` = '$2y$10$uDwncMMeakjU6c98882RJefL81k42TECU4zy0JwX72f5ZpRorWUf6' WHERE `usuario` = 'bombero123';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
