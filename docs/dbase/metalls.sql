-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 06, 2011 at 11:44 PM
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `grade_UNIQUE` (`grade`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Таблица, содержащая названия и марки сплавов, свойства.' AUTO_INCREMENT=8 ;

--
-- Dumping data for table `alloys`
--

INSERT INTO `alloys` (`id`, `alloy_name`, `grade`, `density`) VALUES
(1, 'Нихром', 'Х20Н80', 8.4),
(2, 'Нихром', 'Х15Н60', 8.4),
(3, 'Вольфрам', 'ВА', 13),
(4, 'Титан', 'ВТ1-0', 10.5),
(5, 'Никель', 'Н1У', 8.9),
(6, 'Никель', 'НПА1', 8.9),
(7, 'Никель', 'НП2', 8.9);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Таблица, содержащая записи общего прайс-листа' AUTO_INCREMENT=6 ;

--
-- Dumping data for table `general_price`
--

INSERT INTO `general_price` (`id`, `alloy_id`, `product_id`, `quantity`, `mass`, `price`, `packing`) VALUES
(1, 1, 1, NULL, 250, 1230, 'Бухта'),
(2, 4, 3, 10, 300, 1750, NULL),
(3, 5, 5, NULL, 235, 950, NULL),
(4, 5, 7, NULL, 100, 1450, NULL),
(5, 7, 1, NULL, 250, 1300, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `prices_mapping`
--

CREATE TABLE IF NOT EXISTS `prices_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sprice_id` int(11) NOT NULL COMMENT 'Ссылка на конкретный прайс-лист',
  `gprice_id` int(11) NOT NULL COMMENT 'Ссылка на запись общего прайс-листа',
  PRIMARY KEY (`id`),
  KEY `sprice_fkey` (`sprice_id`),
  KEY `qprice_fkey` (`gprice_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Таблица связывает конкретные прайс-листы с общим' AUTO_INCREMENT=10 ;

--
-- Dumping data for table `prices_mapping`
--

INSERT INTO `prices_mapping` (`id`, `sprice_id`, `gprice_id`) VALUES
(5, 3, 3),
(6, 4, 2),
(8, 3, 4),
(9, 3, 5);

-- --------------------------------------------------------

--
-- Table structure for table `production`
--

CREATE TABLE IF NOT EXISTS `production` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prod_name` varchar(45) NOT NULL COMMENT 'Название продукции (проволока, лента и т.д.)',
  `diameter` double DEFAULT NULL,
  `length` double DEFAULT NULL,
  `width` double DEFAULT NULL,
  `thickness` double DEFAULT NULL,
  `other_dim` varchar(45) DEFAULT NULL,
  `prod_type` smallint(6) NOT NULL DEFAULT '5',
  `prod_note` varchar(45) NOT NULL COMMENT 'Примечание к продукции. Например, мягкая, твердая, сварочная и т.д.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Таблица, содержащая типы продукции, размеры' AUTO_INCREMENT=8 ;

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
(7, 'Лист', NULL, 200, 200, 8, NULL, 3, 'анод');

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
