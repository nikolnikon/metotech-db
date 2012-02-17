-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 17, 2012 at 04:50 PM
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

DROP TABLE IF EXISTS `alloys`;
CREATE TABLE IF NOT EXISTS `alloys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alloy_name` varchar(45) CHARACTER SET cp1251 COLLATE cp1251_general_cs NOT NULL COMMENT 'Название сплава (металла)',
  `grade` varchar(45) CHARACTER SET cp1251 COLLATE cp1251_general_cs NOT NULL COMMENT 'Марка сплава',
  `density` double DEFAULT NULL COMMENT 'Плотность',
  `resistivity` double DEFAULT NULL COMMENT 'Удельное электрическое сопротивление сплава (номинальное значение по ГОСТ 12766.1-90). Максимальное среди значений для разных диаметров.',
  `heater` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Указывает, использовать ли сплав для расчета нагревателей',
  `max_heater_temp` double DEFAULT NULL COMMENT 'Максимальная рабочая температура нагревателя (по ГОСТ 12766.1-90). Максимальное среди значений для разных диаметров.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `grade_UNIQUE` (`grade`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Таблица, содержащая названия и марки сплавов, свойства.' AUTO_INCREMENT=9 ;

--
-- Dumping data for table `alloys`
--

INSERT INTO `alloys` (`id`, `alloy_name`, `grade`, `density`, `resistivity`, `heater`, `max_heater_temp`) VALUES
(1, 'Нихром', 'Х20Н80', 8.4, 1.13, 1, 1200),
(2, 'Нихром', 'Х15Н60', 8.4, 1.12, 1, 1125),
(3, 'Вольфрам', 'ВА', 13, NULL, 0, NULL),
(4, 'Титан', 'ВТ1-0', 10.6, NULL, 0, NULL),
(5, 'Никель', 'Н1У', 8.9, NULL, 0, NULL),
(6, 'Никель', 'НПА1', 8.9, NULL, 0, NULL),
(7, 'Никель', 'НП2', 8.9, NULL, 0, NULL),
(8, 'Фехраль', 'Х23Ю5Т', 7.21, 1.39, 1, 1400);

-- --------------------------------------------------------

--
-- Table structure for table `general_price`
--

DROP TABLE IF EXISTS `general_price`;
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

DROP TABLE IF EXISTS `heater_surface_power`;
CREATE TABLE IF NOT EXISTS `heater_surface_power` (
  `id` double NOT NULL AUTO_INCREMENT,
  `temp_solid` double NOT NULL COMMENT 'Температура нагреваемого тела (тепловоспринимающей поверхности), °С',
  `temp_heater` double NOT NULL COMMENT 'Температура нагревателя, °С',
  `surface_power` double NOT NULL COMMENT 'Удельная поверхностная мощность нагревателя, Вт/м^2 * 10^4',
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
(24, 850, 900, 1.4);

-- --------------------------------------------------------

--
-- Table structure for table `prices_mapping`
--

DROP TABLE IF EXISTS `prices_mapping`;
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

DROP TABLE IF EXISTS `production`;
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

DROP TABLE IF EXISTS `rad_eff_coef`;
CREATE TABLE IF NOT EXISTS `rad_eff_coef` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `heater_placement` text NOT NULL COMMENT 'Размещение нагревателей',
  `min_coef` double NOT NULL COMMENT 'Минимальное значение коэффициента',
  `max_coef` double NOT NULL COMMENT 'Максимальное значение коэффициента',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Коэффициент эффективности излучения (Дьяков)' AUTO_INCREMENT=6 ;

--
-- Dumping data for table `rad_eff_coef`
--

INSERT INTO `rad_eff_coef` (`id`, `heater_placement`, `min_coef`, `max_coef`) VALUES
(1, 'Проволочные спирали, полузакрытые в пазах футеровки', 0.16, 0.24),
(2, 'Проволочные спирали на полочках в трубках', 0.3, 0.36),
(3, 'Проволочные зигзагообразные (стержневые) нагреватели', 0.6, 0.72),
(4, 'Ленточные зигзагообразные нагреватели', 0.38, 0.44),
(5, 'Ленточные профилированные (ободовые) нагреватели', 0.56, 0.7);

-- --------------------------------------------------------

--
-- Table structure for table `special_prices`
--

DROP TABLE IF EXISTS `special_prices`;
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
-- Table structure for table `var_resistent_coef`
--

DROP TABLE IF EXISTS `var_resistent_coef`;
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
