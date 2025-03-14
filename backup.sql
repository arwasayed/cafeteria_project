-- MySQL dump 10.13  Distrib 8.0.41, for Linux (x86_64)
--
-- Host: localhost    Database: cafeteria
-- ------------------------------------------------------
-- Server version	8.0.41-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Category`
--

DROP TABLE IF EXISTS `Category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Category` (
  `c_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`c_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Category`
--

LOCK TABLES `Category` WRITE;
/*!40000 ALTER TABLE `Category` DISABLE KEYS */;
INSERT INTO `Category` VALUES (1,'Hot Drinks'),(2,'Snacks'),(3,'Desserts'),(4,'Sandwiches'),(5,'Ice Drinks');
/*!40000 ALTER TABLE `Category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Order_Contents`
--

DROP TABLE IF EXISTS `Order_Contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Order_Contents` (
  `o_id` int NOT NULL,
  `P_id` int NOT NULL,
  `amount` int NOT NULL,
  PRIMARY KEY (`o_id`,`P_id`),
  KEY `P_id` (`P_id`),
  CONSTRAINT `Order_Contents_ibfk_1` FOREIGN KEY (`o_id`) REFERENCES `Orders` (`O_id`) ON DELETE CASCADE,
  CONSTRAINT `Order_Contents_ibfk_2` FOREIGN KEY (`P_id`) REFERENCES `Products` (`P_id`),
  CONSTRAINT `Order_Contents_chk_1` CHECK ((`amount` > 0))
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Order_Contents`
--

LOCK TABLES `Order_Contents` WRITE;
/*!40000 ALTER TABLE `Order_Contents` DISABLE KEYS */;
INSERT INTO `Order_Contents` VALUES (17,5,1),(19,3,1),(24,1,1),(24,2,1);
/*!40000 ALTER TABLE `Order_Contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Orders`
--

DROP TABLE IF EXISTS `Orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Orders` (
  `O_id` int NOT NULL AUTO_INCREMENT,
  `u_id` int NOT NULL,
  `statuse` enum('done','out of delivery','processing') NOT NULL,
  `note` text,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  `room_number` int NOT NULL,
  PRIMARY KEY (`O_id`,`u_id`),
  KEY `u_id` (`u_id`),
  CONSTRAINT `Orders_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `User_Table` (`u_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Orders`
--

LOCK TABLES `Orders` WRITE;
/*!40000 ALTER TABLE `Orders` DISABLE KEYS */;
INSERT INTO `Orders` VALUES (17,18,'processing','','2025-03-12 08:39:14',77),(19,18,'processing','','2025-03-12 08:43:08',77),(24,19,'processing','hjdhf','2025-03-12 17:11:49',12);
/*!40000 ALTER TABLE `Orders` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `before_insert_order` BEFORE INSERT ON `Orders` FOR EACH ROW BEGIN
    IF NEW.room_number IS NULL THEN
        SET NEW.room_number = (SELECT room_number FROM User_Rooms WHERE u_id = NEW.u_id LIMIT 1);
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `Products`
--

DROP TABLE IF EXISTS `Products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Products` (
  `P_id` int NOT NULL AUTO_INCREMENT,
  `c_id` int DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `price` float NOT NULL,
  `available` enum('available','not available') NOT NULL,
  PRIMARY KEY (`P_id`),
  KEY `c_id` (`c_id`),
  CONSTRAINT `Products_ibfk_1` FOREIGN KEY (`c_id`) REFERENCES `Category` (`c_id`) ON DELETE CASCADE,
  CONSTRAINT `Products_chk_1` CHECK ((`price` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Products`
--

LOCK TABLES `Products` WRITE;
/*!40000 ALTER TABLE `Products` DISABLE KEYS */;
INSERT INTO `Products` VALUES (1,1,'Coffee','/images/coffee.jpg',2.5,'available'),(2,2,'Chips','/images/chips.jpg',1.5,'available'),(3,3,'Cake','/images/cake.jpg',3,'available'),(4,4,'Burger','/images/burger.jpg',5,'not available'),(5,5,'Ice Coffe','/images/salad.jpg',4,'available'),(6,1,'hjh','1741794357_67d1ac350671e.jpeg',0.04,'available');
/*!40000 ALTER TABLE `Products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `UserOrderSummary`
--

DROP TABLE IF EXISTS `UserOrderSummary`;
/*!50001 DROP VIEW IF EXISTS `UserOrderSummary`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `UserOrderSummary` AS SELECT 
 1 AS `user_name`,
 1 AS `order_id`,
 1 AS `order_date`,
 1 AS `product_names`,
 1 AS `total_quantity`,
 1 AS `total_price`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `User_Rooms`
--

DROP TABLE IF EXISTS `User_Rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Rooms` (
  `u_id` int NOT NULL,
  `room_number` int NOT NULL,
  `EXT` varchar(50) NOT NULL,
  PRIMARY KEY (`u_id`,`room_number`,`EXT`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User_Rooms`
--

LOCK TABLES `User_Rooms` WRITE;
/*!40000 ALTER TABLE `User_Rooms` DISABLE KEYS */;
INSERT INTO `User_Rooms` VALUES (1,101,'1234'),(2,102,'5678'),(3,103,'9876'),(4,104,'5432'),(5,105,'1111'),(7,11,'22'),(8,33,'22'),(9,77,'88'),(10,12,'55'),(11,99,'88'),(12,33,'33'),(13,60,'10'),(14,88,'99'),(15,77,'99'),(16,22,'33'),(17,22,'123'),(18,77,'991'),(19,333,'8787'),(20,11,'22');
/*!40000 ALTER TABLE `User_Rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User_Table`
--

DROP TABLE IF EXISTS `User_Table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Table` (
  `u_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL,
  PRIMARY KEY (`u_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User_Table`
--

LOCK TABLES `User_Table` WRITE;
/*!40000 ALTER TABLE `User_Table` DISABLE KEYS */;
INSERT INTO `User_Table` VALUES (4,'Hager Hussien','hager@yahoo.com','$2y$10$iUHOYF6APyW3NlH7xR.so.AH9wXakKqNMsIk15WsDuh1HB7GRCX5S','/images/hh.jpg','user'),(6,'admin','admin@gmail.com','$2y$10$WwsB8AwTv/R4Nhvufp48p.AHk3x4BKSabmnqh6qfi8V3vkegMZ37K','/images/admin.jpg','admin'),(18,'maha','maha@gmail.com','$2y$10$cy13gTg8.ts1tI3cJMMPAOqUBZD4qDzfD9IrvDgFREiOVc3.fzh7K','./images/67d1299fabc52-ee1c3586-fd15-4907-8dcd-8b25b6a042cf.jpeg','user'),(19,'mariam','mariam@gmail.com','$2y$10$TMpbt3TzCfZFXPCcPMy4uuSU8p6rKsKgZOMCXmIn3mcw34hMaBpcC','./images/67d12f24622c0-c2b0f1fd-1405-4015-b0c7-72154161610f.jpeg','user'),(20,'dd','dd@gmail.com','$2y$10$JdmsPEeOl4O40pFy9YGBNeUuEmhHwHiHpeh04JzR4z283bPBt4CLu','./images/67d1ad6520ab4-megs (@megs_io) on X.jpeg','user');
/*!40000 ALTER TABLE `User_Table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `my_order_view`
--

DROP TABLE IF EXISTS `my_order_view`;
/*!50001 DROP VIEW IF EXISTS `my_order_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `my_order_view` AS SELECT 
 1 AS `user_id`,
 1 AS `order_id`,
 1 AS `order_date`,
 1 AS `order_status`,
 1 AS `total_price_of_order`,
 1 AS `total_price_of_all_orders`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `orders_view`
--

DROP TABLE IF EXISTS `orders_view`;
/*!50001 DROP VIEW IF EXISTS `orders_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `orders_view` AS SELECT 
 1 AS `order_date`,
 1 AS `user_name`,
 1 AS `room_number`,
 1 AS `EXT`,
 1 AS `product_name`,
 1 AS `amount`,
 1 AS `price`,
 1 AS `product_total`,
 1 AS `order_total`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `UserOrderSummary`
--

/*!50001 DROP VIEW IF EXISTS `UserOrderSummary`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb3 */;
/*!50001 SET character_set_results     = utf8mb3 */;
/*!50001 SET collation_connection      = utf8mb3_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `UserOrderSummary` AS select `u`.`name` AS `user_name`,`o`.`O_id` AS `order_id`,`o`.`date` AS `order_date`,group_concat(distinct `p`.`name` separator ', ') AS `product_names`,sum(`oc`.`amount`) AS `total_quantity`,sum((`p`.`price` * `oc`.`amount`)) AS `total_price` from (((`Orders` `o` join `User_Table` `u` on((`o`.`u_id` = `u`.`u_id`))) join `Order_Contents` `oc` on((`o`.`O_id` = `oc`.`o_id`))) join `Products` `p` on((`oc`.`P_id` = `p`.`P_id`))) group by `o`.`O_id`,`o`.`date`,`u`.`name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `my_order_view`
--

/*!50001 DROP VIEW IF EXISTS `my_order_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb3 */;
/*!50001 SET character_set_results     = utf8mb3 */;
/*!50001 SET collation_connection      = utf8mb3_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `my_order_view` AS select `u`.`u_id` AS `user_id`,`o`.`O_id` AS `order_id`,`o`.`date` AS `order_date`,`o`.`statuse` AS `order_status`,sum((`p`.`price` * `oc`.`amount`)) AS `total_price_of_order`,sum(sum((`p`.`price` * `oc`.`amount`))) OVER (PARTITION BY `u`.`u_id` )  AS `total_price_of_all_orders` from (((`Orders` `o` join `User_Table` `u` on((`o`.`u_id` = `u`.`u_id`))) join `Order_Contents` `oc` on((`o`.`O_id` = `oc`.`o_id`))) join `Products` `p` on((`oc`.`P_id` = `p`.`P_id`))) group by `o`.`O_id`,`o`.`date`,`o`.`statuse`,`u`.`u_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `orders_view`
--

/*!50001 DROP VIEW IF EXISTS `orders_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb3 */;
/*!50001 SET character_set_results     = utf8mb3 */;
/*!50001 SET collation_connection      = utf8mb3_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `orders_view` AS select `o`.`date` AS `order_date`,`u`.`name` AS `user_name`,`ur`.`room_number` AS `room_number`,`ur`.`EXT` AS `EXT`,`p`.`name` AS `product_name`,`oc`.`amount` AS `amount`,`p`.`price` AS `price`,(`p`.`price` * `oc`.`amount`) AS `product_total`,sum((`p`.`price` * `oc`.`amount`)) OVER (PARTITION BY `o`.`O_id` )  AS `order_total` from ((((`Orders` `o` join `User_Table` `u` on((`o`.`u_id` = `u`.`u_id`))) left join `User_Rooms` `ur` on((`u`.`u_id` = `ur`.`u_id`))) join `Order_Contents` `oc` on((`o`.`O_id` = `oc`.`o_id`))) join `Products` `p` on((`oc`.`P_id` = `p`.`P_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-13  3:45:31
