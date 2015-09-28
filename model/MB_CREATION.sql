DROP DATABASE IF EXISTS MeinBlog;

CREATE SCHEMA `MeinBlog` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin ;

CREATE TABLE `MeinBlog`.`mb_file_header` (
  `file_id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `title` VARCHAR(120) NOT NULL COMMENT '',
  `abstract` VARCHAR(200) NULL COMMENT '',
  `main_editor_id` INT NOT NULL COMMENT '',
  `category_id` INT NOT NULL COMMENT '',
  `create_time` DATETIME NOT NULL COMMENT '',
  `update_time` DATETIME NOT NULL COMMENT '',
  PRIMARY KEY (`file_id`)  COMMENT '')
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

CREATE TABLE `MeinBlog`.`mb_file_content` (
  `file_id` INT NOT NULL COMMENT '',
  `content` MEDIUMTEXT NOT NULL COMMENT '',
  `editor_id` INT NOT NULL COMMENT '',
  `create_time` DATETIME NOT NULL COMMENT '',
  `update_time` DATETIME NOT NULL COMMENT '',
  PRIMARY KEY (`file_id`)  COMMENT '')
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

CREATE TABLE `mb_file_tag` (
  `file_id` int(11) NOT NULL,
  `tag` varchar(45) COLLATE utf8_bin NOT NULL,
  `editor_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  UNIQUE KEY `FTE` (`file_id`,`tag`,`editor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `MeinBlog`.`mb_category` (
  `category_id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `category_name` VARCHAR(45) NOT NULL COMMENT '',
  `open_level` ENUM('ADMIN', 'USER', 'GUEST', 'OUTSIDER') NULL COMMENT '',
  PRIMARY KEY (`category_id`)  COMMENT '')
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

CREATE TABLE `MeinBlog`.`mb_user` (
  `user_id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(45) NOT NULL COMMENT '',
  `password` VARCHAR(45) NOT NULL COMMENT '',
  `email` VARCHAR(45) NOT NULL COMMENT '',
  `role` ENUM('ADMIN', 'USER', 'GUEST', 'OUTSIDER') NOT NULL COMMENT '',
  `create_time` DATETIME NOT NULL COMMENT '',
  PRIMARY KEY (`user_id`)  COMMENT '',
  UNIQUE INDEX `name_UNIQUE` (`name` ASC)  COMMENT '',
  UNIQUE INDEX `email_UNIQUE` (`email` ASC)  COMMENT '')
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

CREATE TABLE `MeinBlog`.`mb_file_comment` (
  `comment_id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `file_id` INT NOT NULL COMMENT '',
  `to_comment_id` INT NULL COMMENT '',
  `editor_id` INT NOT NULL COMMENT '',
  `content` TEXT NOT NULL COMMENT '',
  `create_time` DATETIME NOT NULL COMMENT '',
  `update_time` DATETIME NOT NULL COMMENT '',
  PRIMARY KEY (`comment_id`)  COMMENT '')
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;
