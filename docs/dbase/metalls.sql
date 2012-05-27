-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 17, 2012 at 02:10 AM
-- Server version: 5.1.40
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `metalls`
--

-- --------------------------------------------------------

--
-- Table structure for table `alloys`
--

CREATE TABLE IF NOT EXISTS `alloys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alloy_name` varchar(45) NOT NULL COMMENT 'Название сплава (металла)',
  `grade` varchar(45) NOT NULL COMMENT 'Марка сплава',
  `density` double DEFAULT NULL COMMENT 'Плотность',
  `resistivity` double DEFAULT NULL COMMENT 'Удельное электрическое сопротивление сплава, мкОм*м (номинальное значение по ГОСТ 12766.1-90). Максимальное среди значений для разных диаметров.',
  `heater` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Указывает, использовать ли сплав для расчета нагревателей',
  PRIMARY KEY (`id`),
  UNIQUE KEY `grade_UNIQUE` (`grade`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Таблица, содержащая названия и марки сплавов, свойства.' AUTO_INCREMENT=9 ;

--
-- Dumping data for table `alloys`
--

INSERT INTO `alloys` (`id`, `alloy_name`, `grade`, `density`, `resistivity`, `heater`) VALUES
(1, 'Нихром', 'Х20Н80', 8.4, 1.13, 1),
(2, 'Нихром', 'Х15Н60', 8.4, 1.12, 1),
(3, 'Вольфрам', 'ВА', 13, NULL, 0),
(4, 'Титан', 'ВТ1-0', 10.6, NULL, 0),
(5, 'Никель', 'Н1У', 8.9, NULL, 0),
(6, 'Никель', 'НПА1', 8.9, NULL, 0),
(7, 'Никель', 'НП2', 8.9, NULL, 0),
(8, 'Фехраль', 'Х23Ю5Т', 7.21, 1.39, 1);

-- --------------------------------------------------------

--
-- Table structure for table `general_price`
--

CREATE TABLE IF NOT EXISTS `general_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alloy_id` int(11) NOT NULL COMMENT 'Ссылка на таблицу alloys. Содержит название и марку сплава.',
  `product_id` int(11) NOT NULL COMMENT 'Ссылка на таблицу production. Содержит наименование продукции и ее размер.',
  `quantity` int(11) DEFAULT NULL COMMENT 'Количество единиц продукции. Например, для листов - штуки.',
  `mass` double NOT NULL COMMENT 'Общая масса продукции',
  `price` double DEFAULT NULL COMMENT 'Цена',
  `packing` varchar(45) DEFAULT NULL COMMENT 'Как поставляется продукция. Например, бухты, катушки, пакеты, банки.',
  PRIMARY KEY (`id`),
  KEY `product_fk` (`product_id`),
  KEY `alloy_fk` (`alloy_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Таблица, содержащая записи общего прайс-листа' AUTO_INCREMENT=8 ;

--
-- Dumping data for table `general_price`
--

INSERT INTO `general_price` (`id`, `alloy_id`, `product_id`, `quantity`, `mass`, `price`, `packing`) VALUES
(1, 1, 1, NULL, 250, 1230, 'Бухта'),
(2, 4, 3, 10, 300, 1750, NULL),
(3, 5, 6, NULL, 235, 950, NULL),
(4, 7, 8, NULL, 100, 1235, NULL),
(5, 5, 5, NULL, 300, 1450, NULL),
(6, 7, 1, NULL, 150, 2800, 'NULL'),
(7, 6, 1, NULL, 100, 1500, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `heater_surface_power`
--

CREATE TABLE IF NOT EXISTS `heater_surface_power` (
  `id` double NOT NULL AUTO_INCREMENT,
  `temp_solid` double NOT NULL COMMENT 'Температура нагреваемого тела (тепловоспринимающей поверхности), °С',
  `temp_heater` double NOT NULL COMMENT 'Температура нагревателя, °С',
  `surface_power` double NOT NULL COMMENT 'Удельная поверхностная мощность нагревателя, Вт/см^2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Удельная поверхностная мощность нагревателей в зависимости о' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `heater_surface_power`
--

INSERT INTO `heater_surface_power` (`id`, `temp_solid`, `temp_heater`, `surface_power`) VALUES
(1, 100, 800, 6.1),
(2, 200, 800, 5.9),
(3, 300, 800, 5.65),
(4, 400, 800, 5.2),
(5, 500, 800, 4.5),
(6, 600, 800, 3.5),
(7, 700, 800, 2),
(8, 100, 850, 7.3),
(9, 200, 850, 7.15),
(10, 300, 850, 6.85),
(11, 400, 850, 6.45),
(12, 500, 850, 5.7),
(13, 600, 850, 4.7),
(14, 700, 850, 3.2),
(15, 800, 850, 1.25),
(16, 100, 900, 8.7),
(17, 200, 900, 8.55),
(18, 300, 900, 8.3),
(19, 400, 900, 7.85),
(20, 500, 900, 7.15),
(21, 600, 900, 6.1),
(22, 700, 900, 4.6),
(23, 800, 900, 2.65),
(24, 850, 900, 1.4),
(25, 100, 950, 10.3),
(26, 200, 950, 10.15),
(27, 300, 950, 9.9),
(28, 400, 950, 9.45),
(29, 500, 950, 8.8),
(30, 600, 950, 7.7),
(31, 700, 950, 6.25),
(32, 800, 950, 4.2),
(33, 850, 950, 3),
(34, 900, 950, 1.55),
(35, 100, 1000, 12.5),
(36, 200, 1000, 12),
(37, 300, 1000, 11.7),
(38, 400, 1000, 11.25),
(39, 500, 1000, 10.55),
(40, 600, 1000, 9.5),
(41, 700, 1000, 8.05),
(42, 800, 1000, 6.05),
(43, 850, 1000, 4.8),
(44, 900, 1000, 3.4),
(45, 950, 1000, 1.8),
(46, 100, 1050, 14.15),
(47, 200, 1050, 14),
(48, 300, 1050, 13.75),
(49, 400, 1050, 13.3),
(50, 500, 1050, 12.6),
(51, 600, 1050, 11.5),
(52, 700, 1050, 10),
(53, 800, 1050, 8.1),
(54, 850, 1050, 6.85),
(55, 900, 1050, 5.45),
(56, 950, 1050, 3.85),
(57, 1000, 1050, 2.05),
(58, 100, 1100, 16.4),
(59, 200, 1100, 16.25),
(60, 300, 1100, 16),
(61, 400, 1100, 15.55),
(62, 500, 1100, 14.85),
(63, 600, 1100, 13.8),
(64, 700, 1100, 12.4),
(65, 800, 1100, 10.4),
(66, 850, 1100, 9.1),
(67, 900, 1100, 7.75),
(68, 950, 1100, 6.15),
(69, 1000, 1100, 4.3),
(70, 1050, 1100, 2.3),
(71, 100, 1150, 19),
(72, 200, 1150, 18.85),
(73, 300, 1150, 18.6),
(74, 400, 1150, 18.1),
(75, 500, 1150, 17.4),
(76, 600, 1150, 16.4),
(77, 700, 1150, 14.9),
(78, 800, 1150, 12.9),
(79, 850, 1150, 11.7),
(80, 900, 1150, 10.3),
(81, 950, 1150, 8.65),
(82, 1000, 1150, 6.85),
(83, 1050, 1150, 4.8),
(84, 1100, 1150, 2.55),
(85, 100, 1200, 21.8),
(86, 200, 1200, 21.65),
(87, 300, 1200, 21.35),
(88, 400, 1200, 20.9),
(89, 500, 1200, 20.2),
(90, 600, 1200, 19.3),
(91, 700, 1200, 17.7),
(92, 800, 1200, 15.7),
(93, 850, 1200, 14.5),
(94, 900, 1200, 13),
(95, 950, 1200, 11.5),
(96, 1000, 1200, 9.7),
(97, 1050, 1200, 7.65),
(98, 1100, 1200, 5.35),
(99, 1150, 1200, 2.85),
(100, 100, 1250, 24.9),
(101, 200, 1250, 24.75),
(102, 300, 1250, 24.5),
(103, 400, 1250, 24),
(104, 500, 1250, 23.3),
(105, 600, 1250, 22.3),
(106, 700, 1250, 20.8),
(107, 800, 1250, 18.8),
(108, 850, 1250, 17.6),
(109, 900, 1250, 16.2),
(110, 950, 1250, 14.5),
(111, 1000, 1250, 12.75),
(112, 1050, 1250, 10.75),
(113, 1100, 1250, 8.5),
(114, 1150, 1250, 5.95),
(115, 1200, 1250, 3.15),
(116, 100, 1300, 28.4),
(117, 200, 1300, 28.2),
(118, 300, 1300, 27.9),
(119, 400, 1300, 27.45),
(120, 500, 1300, 26.8),
(121, 600, 1300, 25.7),
(122, 700, 1300, 24.3),
(123, 800, 1300, 22.3),
(124, 850, 1300, 21),
(125, 900, 1300, 19.6),
(126, 950, 1300, 18.1),
(127, 1000, 1300, 16.25),
(128, 1050, 1300, 14.25),
(129, 1100, 1300, 12),
(130, 1150, 1300, 9.4),
(131, 1200, 1300, 6.55),
(132, 100, 1350, 36.3),
(133, 200, 1350, 36.1),
(134, 300, 1350, 35.8),
(135, 400, 1350, 35.4),
(136, 500, 1350, 34.6),
(137, 600, 1350, 33.7),
(138, 700, 1350, 32.2),
(139, 800, 1350, 30.2),
(140, 850, 1350, 29),
(141, 900, 1350, 27.6),
(142, 950, 1350, 26),
(143, 1000, 1350, 24.2),
(144, 1050, 1350, 22.2),
(145, 1100, 1350, 19.8),
(146, 1150, 1350, 17.55),
(147, 1200, 1350, 14.55),
(148, 1300, 1350, 7.95);

-- --------------------------------------------------------

--
-- Table structure for table `max_heater_temp`
--

CREATE TABLE IF NOT EXISTS `max_heater_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alloy_id` int(11) NOT NULL COMMENT 'Ссылка на сплав нагревателя',
  `diameter` double NOT NULL COMMENT 'Диаметр проволоки, мм',
  `max_temp` double NOT NULL COMMENT 'Максимальная рабочая температура нагревателя для соответствующего диаметра, °С',
  PRIMARY KEY (`id`),
  KEY `max_heater_temp_alloy_fk` (`alloy_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Содержит значения максимальных рабочих температур нагревател' AUTO_INCREMENT=11 ;

--
-- Dumping data for table `max_heater_temp`
--

INSERT INTO `max_heater_temp` (`id`, `alloy_id`, `diameter`, `max_temp`) VALUES
(1, 1, 0.2, 950),
(2, 1, 0.4, 1000),
(3, 1, 1, 1100),
(4, 1, 3, 1150),
(5, 1, 6, 1200),
(6, 2, 0.2, 900),
(7, 2, 0.4, 950),
(8, 2, 1, 1000),
(9, 2, 3, 1075),
(10, 2, 6, 1125);

-- --------------------------------------------------------

--
-- Table structure for table `prices_mapping`
--

CREATE TABLE IF NOT EXISTS `prices_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sprice_id` int(11) NOT NULL COMMENT 'Ссылка на конкретный прайс-лист',
  `gprice_id` int(11) NOT NULL COMMENT 'Ссылка на запись общего прайс-листа',
  `order` int(11) NOT NULL DEFAULT '0' COMMENT 'Порядок записи прайс-листа при отображении',
  PRIMARY KEY (`id`),
  KEY `sprice_fkey` (`sprice_id`),
  KEY `qprice_fkey` (`gprice_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Таблица связывает конкретные прайс-листы с общим' AUTO_INCREMENT=10 ;

--
-- Dumping data for table `prices_mapping`
--

INSERT INTO `prices_mapping` (`id`, `sprice_id`, `gprice_id`, `order`) VALUES
(5, 3, 3, 1),
(6, 4, 2, 1),
(7, 3, 4, 2),
(8, 3, 5, 3),
(9, 3, 6, 4);

-- --------------------------------------------------------

--
-- Table structure for table `production`
--

CREATE TABLE IF NOT EXISTS `production` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prod_name` varchar(45) NOT NULL COMMENT 'Название продукции (проволока, лента и т.д.)',
  `diameter` double unsigned DEFAULT NULL,
  `length` double unsigned DEFAULT NULL,
  `width` double unsigned DEFAULT NULL,
  `thickness` double unsigned DEFAULT NULL,
  `other_dim` varchar(45) DEFAULT NULL,
  `prod_type` smallint(5) unsigned NOT NULL DEFAULT '5' COMMENT 'Указывает тип продукции по размерам. 1 - диаметр; 2 - ширина, толщина; 3 - ширина, толщина, длина; 4 - диаметр, толщина; 5 - другой размер.',
  `prod_note` varchar(45) DEFAULT NULL COMMENT 'Примечание к продукции. Например, мягкая, твердая, сварочная и т.д.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Таблица, содержащая типы продукции, размеры' AUTO_INCREMENT=10 ;

--
-- Dumping data for table `production`
--

INSERT INTO `production` (`id`, `prod_name`, `diameter`, `length`, `width`, `thickness`, `other_dim`, `prod_type`, `prod_note`) VALUES
(1, 'Проволока', 1.2, NULL, NULL, NULL, '', 1, ''),
(2, 'Пруток', 20, NULL, NULL, NULL, NULL, 1, ''),
(3, 'Лист', NULL, 1250, 500, 0.3, NULL, 3, ''),
(4, 'Труба', 20, NULL, NULL, 2, NULL, 4, ''),
(5, 'Лист', NULL, NULL, NULL, NULL, 'Рубленый', 5, 'катод'),
(6, 'Лист', NULL, 1000, 200, 10, NULL, 3, ''),
(7, 'Лист', NULL, 200, 200, 8, NULL, 3, 'анод'),
(8, 'Лента', NULL, NULL, 10, 1, NULL, 2, ''),
(9, 'Порошок', NULL, NULL, NULL, NULL, 'Металлические банки по 6-10 кг', 5, '');

-- --------------------------------------------------------

--
-- Table structure for table `rad_eff_coef`
--

CREATE TABLE IF NOT EXISTS `rad_eff_coef` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `heater_placement` text NOT NULL COMMENT 'Размещение нагревателей',
  `min_coef` double NOT NULL COMMENT 'Минимальное значение коэффициента',
  `max_coef` double NOT NULL COMMENT 'Максимальное значение коэффициента',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Коэффициент эффективности излучения (Дьяков)' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `rad_eff_coef`
--

INSERT INTO `rad_eff_coef` (`id`, `heater_placement`, `min_coef`, `max_coef`) VALUES
(1, 'Проволочные спирали полузакрытые', 0.16, 0.24),
(2, 'Проволочные спирали на полочках в трубках', 0.3, 0.36),
(3, 'Проволочные зигзагообразные нагреватели', 0.6, 0.72);

-- --------------------------------------------------------

--
-- Table structure for table `special_prices`
--

CREATE TABLE IF NOT EXISTS `special_prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `price_name` varchar(45) NOT NULL COMMENT 'Имя прайс-листа. Например, nihrom-price, volfram-price.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `price_name_UNIQUE` (`price_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Таблица содержит список всех имеющихся прайс-листов.' AUTO_INCREMENT=5 ;

--
-- Dumping data for table `special_prices`
--

INSERT INTO `special_prices` (`id`, `price_name`) VALUES
(1, 'nihrom-price'),
(3, 'nikel-price'),
(4, 'titan-price'),
(2, 'volfram-price');

-- --------------------------------------------------------

--
-- Table structure for table `standart_nom_diameters`
--

CREATE TABLE IF NOT EXISTS `standart_nom_diameters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `standart_diameter` double NOT NULL COMMENT 'Стандартный диаметр, мм',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Содержит список стандартных номинальных диаметров проволки, ' AUTO_INCREMENT=81 ;

--
-- Dumping data for table `standart_nom_diameters`
--

INSERT INTO `standart_nom_diameters` (`id`, `standart_diameter`) VALUES
(1, 0.1),
(2, 0.105),
(3, 0.11),
(4, 0.115),
(5, 0.12),
(6, 0.13),
(7, 0.14),
(8, 0.15),
(9, 0.16),
(10, 0.17),
(11, 0.18),
(12, 0.19),
(13, 0.2),
(14, 0.21),
(15, 0.22),
(16, 0.24),
(17, 0.25),
(18, 0.26),
(19, 0.28),
(20, 0.3),
(21, 0.32),
(22, 0.34),
(23, 0.36),
(24, 0.38),
(25, 0.4),
(26, 0.42),
(27, 0.45),
(28, 0.48),
(33, 0.5),
(34, 0.53),
(35, 0.56),
(36, 0.6),
(37, 0.63),
(38, 0.67),
(39, 0.7),
(40, 0.75),
(41, 0.8),
(42, 0.85),
(43, 0.9),
(44, 0.95),
(45, 1),
(46, 1.06),
(47, 1.1),
(48, 1.15),
(49, 1.2),
(50, 1.3),
(51, 1.4),
(52, 1.5),
(53, 1.6),
(54, 1.7),
(55, 1.8),
(56, 1.9),
(57, 2),
(58, 2.1),
(59, 2.2),
(60, 2.4),
(61, 2.5),
(62, 2.6),
(63, 2.8),
(64, 3),
(65, 3.2),
(66, 3.4),
(67, 3.6),
(68, 3.8),
(69, 4),
(70, 4.2),
(71, 4.5),
(72, 4.8),
(73, 5),
(74, 5.3),
(75, 5.6),
(76, 6.1),
(77, 6.3),
(78, 6.7),
(79, 7),
(80, 7.5);

-- --------------------------------------------------------

--
-- Table structure for table `var_resistent_coef`
--

CREATE TABLE IF NOT EXISTS `var_resistent_coef` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alloy_id` int(11) NOT NULL COMMENT 'Ссылка на сплав',
  `temp` double NOT NULL COMMENT 'Температура проволоки, °С',
  `correction_coef` double NOT NULL COMMENT 'Поправочный коэффициент',
  PRIMARY KEY (`id`),
  KEY `var_res_coef_alloy_fk` (`alloy_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Поправочный коэффициент для расчета изменения электрического' AUTO_INCREMENT=38 ;

--
-- Dumping data for table `var_resistent_coef`
--

INSERT INTO `var_resistent_coef` (`id`, `alloy_id`, `temp`, `correction_coef`) VALUES
(1, 1, 20, 1),
(2, 1, 100, 1.006),
(3, 1, 200, 1.015),
(4, 1, 300, 1.022),
(5, 1, 400, 1.029),
(6, 1, 500, 1.032),
(7, 1, 600, 1.023),
(8, 1, 700, 1.016),
(9, 1, 800, 1.015),
(10, 1, 900, 1.017),
(11, 1, 1000, 1.025),
(12, 1, 1100, 1.033),
(13, 1, 1200, 1.04),
(26, 2, 20, 1),
(27, 2, 100, 1.013),
(28, 2, 200, 1.029),
(29, 2, 300, 1.046),
(30, 2, 400, 1.062),
(31, 2, 500, 1.074),
(32, 2, 600, 1.083),
(33, 2, 700, 1.083),
(34, 2, 800, 1.089),
(35, 2, 900, 1.097),
(36, 2, 1000, 1.105),
(37, 2, 1100, 1.114);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `general_price`
--
ALTER TABLE `general_price`
  ADD CONSTRAINT `alloy_fk` FOREIGN KEY (`alloy_id`) REFERENCES `alloys` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `product_fk` FOREIGN KEY (`product_id`) REFERENCES `production` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `max_heater_temp`
--
ALTER TABLE `max_heater_temp`
  ADD CONSTRAINT `max_heater_temp_alloy_fk` FOREIGN KEY (`alloy_id`) REFERENCES `alloys` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `prices_mapping`
--
ALTER TABLE `prices_mapping`
  ADD CONSTRAINT `qprice_fkey` FOREIGN KEY (`gprice_id`) REFERENCES `general_price` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `sprice_fkey` FOREIGN KEY (`sprice_id`) REFERENCES `special_prices` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `var_resistent_coef`
--
ALTER TABLE `var_resistent_coef`
  ADD CONSTRAINT `var_res_coef_alloy_fk` FOREIGN KEY (`alloy_id`) REFERENCES `alloys` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
