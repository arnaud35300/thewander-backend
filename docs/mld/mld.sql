CREATE DATABASE IF NOT EXISTS `TYPES` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `TYPES`;

CREATE TABLE `ROLE` (
  `id` VARCHAR(42),
  `name` VARCHAR(42),
  `created_at` VARCHAR(42),
  `updated_at` VARCHAR(42),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `COMMENT` (
  `id` VARCHAR(42),
  `user_id` VARCHAR(42),
  `star_id` VARCHAR(42),
  `body` VARCHAR(42),
  `created_at` VARCHAR(42),
  `updated_at` VARCHAR(42),
  `id_1` VARCHAR(42),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `USER` (
  `id` VARCHAR(42),
  `role_id` VARCHAR(42),
  `rank_id` VARCHAR(42),
  `nickname` VARCHAR(42),
  `slug` VARCHAR(42),
  `email` VARCHAR(42),
  `password` VARCHAR(42),
  `avatar` VARCHAR(42),
  `firstname` VARCHAR(42),
  `birthday` VARCHAR(42),
  `bio` VARCHAR(42),
  `status` VARCHAR(42),
  `created_at` VARCHAR(42),
  `updated_at` VARCHAR(42),
  `id_1` VARCHAR(42),
  `id_2` VARCHAR(42),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `STAR` (
  `id` VARCHAR(42),
  `user_id` VARCHAR(42),
  `type_id` VARCHAR(42),
  `subtype_id` VARCHAR(42),
  `name` VARCHAR(42),
  `slug` VARCHAR(42),
  `picture` VARCHAR(42),
  `nb_stars` VARCHAR(42),
  `description` VARCHAR(42),
  `created_at` VARCHAR(42),
  `updated_at` VARCHAR(42),
  `id_1` VARCHAR(42),
  `id_2` VARCHAR(42),
  `id_3` VARCHAR(42),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `SUBTYPE` (
  `id` VARCHAR(42),
  `type_id` VARCHAR(42),
  `name` VARCHAR(42),
  `created_at` VARCHAR(42),
  `updated_at` VARCHAR(42),
  `id_1` VARCHAR(42),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `RANK` (
  `id` VARCHAR(42),
  `name` VARCHAR(42),
  `badge` VARCHAR(42),
  `created_at` VARCHAR(42),
  `updated_at` VARCHAR(42),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `TYPE` (
  `id` VARCHAR(42),
  `name` VARCHAR(42),
  `created_at` VARCHAR(42),
  `updated_at` VARCHAR(42),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `COMMENT` ADD FOREIGN KEY (`id_1`) REFERENCES `STAR` (`id`);
ALTER TABLE `USER` ADD FOREIGN KEY (`id_2`) REFERENCES `RANK` (`id`);
ALTER TABLE `USER` ADD FOREIGN KEY (`id_1`) REFERENCES `ROLE` (`id`);
ALTER TABLE `STAR` ADD FOREIGN KEY (`id_3`) REFERENCES `USER` (`id`);
ALTER TABLE `STAR` ADD FOREIGN KEY (`id_2`) REFERENCES `SUBTYPE` (`id`);
ALTER TABLE `STAR` ADD FOREIGN KEY (`id_1`) REFERENCES `TYPE` (`id`);
ALTER TABLE `SUBTYPE` ADD FOREIGN KEY (`id_1`) REFERENCES `TYPE` (`id`);