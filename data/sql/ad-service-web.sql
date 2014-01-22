SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `ad-service-web` DEFAULT CHARACTER SET utf8 ;
USE `ad-service-web` ;

-- -----------------------------------------------------
-- Table `ad-service-web`.`archivo`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`archivo` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`marca`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`marca` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`modelo`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`modelo` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `marca_id` INT(11) NULL DEFAULT NULL ,
  `nombre` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `IDX_F0D76C4681EF0041` (`marca_id` ASC) ,
  CONSTRAINT `FK_F0D76C4681EF0041`
    FOREIGN KEY (`marca_id` )
    REFERENCES `ad-service-web`.`marca` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`gama`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`gama` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `modelo_id` INT(11) NULL DEFAULT NULL ,
  `nombre` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `IDX_2446F595C3A9576E` (`modelo_id` ASC) ,
  CONSTRAINT `FK_2446F595C3A9576E`
    FOREIGN KEY (`modelo_id` )
    REFERENCES `ad-service-web`.`modelo` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 13
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`coche`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`coche` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `gama_id` INT(11) NULL DEFAULT NULL ,
  `nombre` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `IDX_A1981CD46BED4E52` (`gama_id` ASC) ,
  CONSTRAINT `FK_A1981CD46BED4E52`
    FOREIGN KEY (`gama_id` )
    REFERENCES `ad-service-web`.`gama` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`groper`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`groper` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`operacion`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`operacion` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `groper_id` INT(11) NULL DEFAULT NULL ,
  `nombre` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `IDX_D44FC94B4F46D3EB` (`groper_id` ASC) ,
  CONSTRAINT `FK_D44FC94B4F46D3EB`
    FOREIGN KEY (`groper_id` )
    REFERENCES `ad-service-web`.`groper` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`pedidoelec`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`pedidoelec` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`rol`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`rol` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`subsistema`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`subsistema` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`sistema`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`sistema` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `subsistema_id` INT(11) NULL DEFAULT NULL ,
  `nombre` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `IDX_91C2AB61485C45AB` (`subsistema_id` ASC) ,
  CONSTRAINT `FK_91C2AB61485C45AB`
    FOREIGN KEY (`subsistema_id` )
    REFERENCES `ad-service-web`.`subsistema` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`socio`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`socio` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `pedidoelec_id` INT(11) NULL DEFAULT NULL ,
  `nombre` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `IDX_38B6530987339930` (`pedidoelec_id` ASC) ,
  CONSTRAINT `FK_38B6530987339930`
    FOREIGN KEY (`pedidoelec_id` )
    REFERENCES `ad-service-web`.`pedidoelec` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`taller`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`taller` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `socio_id` INT(11) NULL DEFAULT NULL ,
  `nombre` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `IDX_139F4584DA04E6A9` (`socio_id` ASC) ,
  CONSTRAINT `FK_139F4584DA04E6A9`
    FOREIGN KEY (`socio_id` )
    REFERENCES `ad-service-web`.`socio` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`usuario`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`usuario` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `rol_id` INT(11) NULL DEFAULT NULL ,
  `nombre` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `IDX_2265B05D4BAB96C` (`rol_id` ASC) ,
  CONSTRAINT `FK_2265B05D4BAB96C`
    FOREIGN KEY (`rol_id` )
    REFERENCES `ad-service-web`.`rol` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ad-service-web`.`ticket`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ad-service-web`.`ticket` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `usuario_id` INT(11) NULL DEFAULT NULL ,
  `operacion_id` INT(11) NULL DEFAULT NULL ,
  `taller_id` INT(11) NULL DEFAULT NULL ,
  `coche_id` INT(11) NULL DEFAULT NULL ,
  `sistema_id` INT(11) NULL DEFAULT NULL ,
  `archivo_id` INT(11) NULL DEFAULT NULL ,
  `nombre` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `IDX_97A0ADA3DB38439E` (`usuario_id` ASC) ,
  INDEX `IDX_97A0ADA3E6D597C3` (`operacion_id` ASC) ,
  INDEX `IDX_97A0ADA36DC343EA` (`taller_id` ASC) ,
  INDEX `IDX_97A0ADA3F4621E56` (`coche_id` ASC) ,
  INDEX `IDX_97A0ADA317CDA208` (`sistema_id` ASC) ,
  INDEX `IDX_97A0ADA346EBF93B` (`archivo_id` ASC) ,
  CONSTRAINT `FK_97A0ADA346EBF93B`
    FOREIGN KEY (`archivo_id` )
    REFERENCES `ad-service-web`.`archivo` (`id` ),
  CONSTRAINT `FK_97A0ADA317CDA208`
    FOREIGN KEY (`sistema_id` )
    REFERENCES `ad-service-web`.`sistema` (`id` ),
  CONSTRAINT `FK_97A0ADA36DC343EA`
    FOREIGN KEY (`taller_id` )
    REFERENCES `ad-service-web`.`taller` (`id` ),
  CONSTRAINT `FK_97A0ADA3DB38439E`
    FOREIGN KEY (`usuario_id` )
    REFERENCES `ad-service-web`.`usuario` (`id` ),
  CONSTRAINT `FK_97A0ADA3E6D597C3`
    FOREIGN KEY (`operacion_id` )
    REFERENCES `ad-service-web`.`operacion` (`id` ),
  CONSTRAINT `FK_97A0ADA3F4621E56`
    FOREIGN KEY (`coche_id` )
    REFERENCES `ad-service-web`.`coche` (`id` ))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
