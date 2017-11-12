CREATE DATABASE gemini;
USE gemini;

-- Drop the tables
DROP TABLE IF EXISTS `Awards_Given`;
DROP TABLE IF EXISTS `UserType`;
DROP TABLE IF EXISTS `Awards`;
DROP TABLE IF EXISTS `Employees`;
DROP TABLE IF EXISTS `Recovery_Email`;

--
-- Table structure for table `Awards`
--
CREATE TABLE `Awards` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `AwardType` varchar(255) DEFAULT NULL,
  `AwardLabel` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Insert
INSERT INTO `Awards` (`AwardType`, `AwardLabel`)
VALUES ('employee-of-the-week','Employee of the Week'),
  ('employee-of-the-month','Employee of the Month'),
  ('peer','Honorable Mention');

--
-- Table structure for table `Employees`
--
CREATE TABLE `Employees` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `fName` varchar(255) DEFAULT NULL,
  `lName` varchar(255) DEFAULT NULL,
  `hireDate` date DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `CreatedOn` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Awards_Given`
--
CREATE TABLE `Awards_Given` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `AwardID` int(11) DEFAULT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `AwardDate` date DEFAULT NULL,
  `AwardedByID` int(11) DEFAULT NULL,
  `AwardDuration` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_Awards_Given_EmployeeID_idx` (`EmployeeID`),
  KEY `fk_Awards_Given_AwardedByID_idx` (`AwardedByID`),
  CONSTRAINT `fk_Awards_Given_AwardedByID` FOREIGN KEY (`AwardedByID`) REFERENCES `Employees` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Awards_Given_EmployeeID` FOREIGN KEY (`EmployeeID`) REFERENCES `Employees` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Awards_Given_AwardID` FOREIGN KEY (`AwardID`) REFERENCES `Awards` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `UserType`
--
CREATE TABLE `UserType` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `EmployeeID` int(11) DEFAULT NULL,
  `Type` enum('user','admin') DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_UserType_EmployeeID_idx` (`EmployeeID`),
  CONSTRAINT `fk_UserType_EmployeeID` FOREIGN KEY (`EmployeeID`) REFERENCES `Employees` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
--  Table structure for table '

CREATE TABLE `Recovery_Email` (
  `ID` bigint(20) unsigned zerofill NOT NULL auto_increment,
  `Employee_Email` varchar(255) NOT NULL,
  `Key` varchar(32) NOT NULL,
  `expDate` datetime NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;