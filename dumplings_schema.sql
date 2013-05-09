CREATE TABLE `users` (
	`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(100) NOT NULL,
	`password` VARCHAR(100) NOT NULL,
	`created_at` DATETIME NOT NULL,
	`updated_at` DATETIME NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `Index_email`(`email`)
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `addresses` (
	`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INTEGER UNSIGNED NOT NULL,
	`street_address` VARCHAR (100) NOT NULL,
	`city` VARCHAR (100) NOT NULL,
	`province` VARCHAR (3) NOT NULL,
	`postal_code` CHAR(6) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `Index_user_id`(`user_id`),
	CONSTRAINT `FK_user_addresses_user_id` FOREIGN KEY `FK_user_addresses_user_id` (`user_id`)
		REFERENCES `users` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `item_sizes` (
	`id` CHAR (1) PRIMARY KEY
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `item_sizes` (`id`) VALUES ('S'), ('M'), ('L'), ('X');

CREATE TABLE `item_types` (
	`id` VARCHAR (20) PRIMARY KEY,
	`slug` VARCHAR(20)
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `item_types` (`id`, `slug`) VALUES ('appetizers', 'appetizers'), ('beverages', 'beverages'), ('dumplings', 'dumplings'), ('noodles', 'noodles'), ('rice bowls', 'rice_bowls'), ('salads', 'salads'), ('sides', 'sides'), ('soups', 'soups'), ('specials', 'specials'), ('vegetarian', 'vegetarian'), ('wraps', 'wraps'); 

CREATE TABLE `items` (
	`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR (100) NOT NULL,
	`price` DECIMAL (4,2) NOT NULL,
	`size` CHAR (1) NOT NULL DEFAULT 'M',
	`spiciness` TINYINT NOT NULL DEFAULT 0,
	`type` VARCHAR (20) NOT NULL,
	`available` BOOLEAN NOT NULL DEFAULT 1,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `Index_name`(`name`),
	CONSTRAINT `FK_item_size` FOREIGN KEY `FK_item_size` (`size`)
		REFERENCES `item_sizes` (`id`),
	CONSTRAINT `FK_item_type` FOREIGN KEY `FK_item_type` (`type`)
		REFERENCES `item_types` (`id`)
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `favorites` (
	`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INTEGER UNSIGNED NOT NULL,
	`item_id` INTEGER UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `Index_user_id_item_id`(`user_id`, `item_id`),
	CONSTRAINT `FK_favorites_user_id` FOREIGN KEY `FK_favorites_user_id` (`user_id`)
		REFERENCES `users` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	CONSTRAINT `FK_favorites_item_id` FOREIGN KEY `FK_favorites_item_id` (`item_id`)
		REFERENCES `items` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci;