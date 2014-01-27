SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `ad-service-web` DEFAULT CHARACTER SET utf8 ;
USE `ad-service-web` ;

-- -----------------------------------------------------
-- Table `ad-service-web`.`region`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`region` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `region` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 20
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`province`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`province` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `region_id` INT(11) NULL DEFAULT NULL ,
  `province` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `IDX_4ADAD40B98260155` (`region_id` ASC) ,
  CONSTRAINT `FK_4ADAD40B98260155`
    FOREIGN KEY (`region_id` )
    REFERENCES `ad-service-web`.`region` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 53
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(255) NOT NULL ,
  `password` VARCHAR(255) NOT NULL ,
  `salt` VARCHAR(255) NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `surname` VARCHAR(255) NOT NULL ,
  `city` VARCHAR(255) NOT NULL ,
  `phone_number_1` VARCHAR(9) NOT NULL ,
  `phone_number_2` VARCHAR(9) NOT NULL ,
  `movile_number_1` VARCHAR(9) NOT NULL ,
  `movile_number_2` VARCHAR(9) NOT NULL ,
  `fax` VARCHAR(9) NOT NULL ,
  `email_1` VARCHAR(255) NOT NULL ,
  `email_2` VARCHAR(255) NOT NULL ,
  `dni` VARCHAR(9) NOT NULL ,
  `active` TINYINT(1) NOT NULL ,
  `sessionID` VARCHAR(50) NOT NULL ,
  `language` VARCHAR(2) NOT NULL ,
  `region_id` INT(11) NULL DEFAULT NULL ,
  `province_id` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `IDX_8D93D64998260155` (`region_id` ASC) ,
  INDEX `IDX_8D93D649E946114A` (`province_id` ASC) ,
  CONSTRAINT `FK_8D93D649E946114A`
    FOREIGN KEY (`province_id` )
    REFERENCES `ad-service-web`.`province` (`id` ),
  CONSTRAINT `FK_8D93D64998260155`
    FOREIGN KEY (`region_id` )
    REFERENCES `ad-service-web`.`region` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`ticket`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`ticket` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NULL DEFAULT NULL ,
  `userModified_id` INT(11) NULL DEFAULT NULL ,
  `title` VARCHAR(255) NOT NULL ,
  `status` INT(11) NOT NULL ,
  `importance` INT(11) NOT NULL ,
  `date_created` DATE NOT NULL ,
  `date_modified` DATE NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `IDX_97A0ADA3A76ED395` (`user_id` ASC) ,
  INDEX `IDX_97A0ADA3E7BB4453` (`userModified_id` ASC) ,
  CONSTRAINT `FK_97A0ADA3A76ED395`
    FOREIGN KEY (`user_id` )
    REFERENCES `ad-service-web`.`user` (`id` ),
  CONSTRAINT `FK_97A0ADA3E7BB4453`
    FOREIGN KEY (`userModified_id` )
    REFERENCES `ad-service-web`.`user` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 15
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`post`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`post` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `ticket_id` INT(11) NULL DEFAULT NULL ,
  `user_id` INT(11) NULL DEFAULT NULL ,
  `message` LONGTEXT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `IDX_5A8A6C8D700047D2` (`ticket_id` ASC) ,
  INDEX `IDX_5A8A6C8DA76ED395` (`user_id` ASC) ,
  CONSTRAINT `FK_5A8A6C8D700047D2`
    FOREIGN KEY (`ticket_id` )
    REFERENCES `ad-service-web`.`ticket` (`id` ),
  CONSTRAINT `FK_5A8A6C8DA76ED395`
    FOREIGN KEY (`user_id` )
    REFERENCES `ad-service-web`.`user` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 15
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`file`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`file` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `post_id` INT(11) NULL DEFAULT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `type` VARCHAR(255) NOT NULL ,
  `url` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `IDX_8C9F36104B89032C` (`post_id` ASC) ,
  CONSTRAINT `FK_8C9F36104B89032C`
    FOREIGN KEY (`post_id` )
    REFERENCES `ad-service-web`.`post` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`status`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`status` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `status` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`incidence`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`incidence` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `ticket_id` INT(11) NULL DEFAULT NULL ,
  `status_id` INT(11) NULL DEFAULT NULL ,
  `importance` INT(11) NOT NULL ,
  `solution` VARCHAR(255) NOT NULL ,
  `description` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `IDX_17060417700047D2` (`ticket_id` ASC) ,
  INDEX `IDX_170604176BF700BD` (`status_id` ASC) ,
  CONSTRAINT `FK_170604176BF700BD`
    FOREIGN KEY (`status_id` )
    REFERENCES `ad-service-web`.`status` (`id` ),
  CONSTRAINT `FK_17060417700047D2`
    FOREIGN KEY (`ticket_id` )
    REFERENCES `ad-service-web`.`ticket` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`role` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`user_role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`user_role` (
  `user_id` INT(11) NOT NULL ,
  `role_id` INT(11) NOT NULL ,
  PRIMARY KEY (`user_id`, `role_id`) ,
  INDEX `IDX_2DE8C6A3A76ED395` (`user_id` ASC) ,
  INDEX `IDX_2DE8C6A3D60322AC` (`role_id` ASC) ,
  CONSTRAINT `FK_2DE8C6A3A76ED395`
    FOREIGN KEY (`user_id` )
    REFERENCES `ad-service-web`.`user` (`id` ),
  CONSTRAINT `FK_2DE8C6A3D60322AC`
    FOREIGN KEY (`role_id` )
    REFERENCES `ad-service-web`.`role` (`id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
