/*

	DUMPLINGS DATABASE SCHEMA

	RELATIONSHIPS:

	user 					-- one to one -- address (addresses)
			 					-- one to many -- orders (orderheaders)
			 					-- many to many -- items (favorites)
	
	address 			-- none

	itemsizes			-- none

	uniqueitems 	-- one to one -- type (itemtypes)

	itemtypes 		-- none

	items 				-- one to one -- uniqueitems (uniqueitems)
								-- one to one -- sizes (itemsizes)

	favorites 		-- none

	orderheaders 	-- one to many -- details (orderdetails)

	orderdetails 	-- one to one -- item (items)

 */

-- Tables for users

CREATE TABLE `users` (
	`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(100) NOT NULL,
	`password` VARCHAR(100) NOT NULL,
	`lvl` BOOLEAN NOT NULL DEFAULT 0,
	`name` VARCHAR(100),
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

-- Tables for items in the store

CREATE TABLE `itemtypes` (
	`id` VARCHAR (20) PRIMARY KEY,
	`slug` VARCHAR(20)
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `uniqueitems` (
	`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR (100) NOT NULL,
	`type` VARCHAR (20) NOT NULL,
	`imgurl` VARCHAR (200),
	`available` BOOLEAN NOT NULL DEFAULT 1,
	PRIMARY KEY (`id`),
	CONSTRAINT `FK_item_type` FOREIGN KEY `FK_item_type` (`type`)
		REFERENCES `itemtypes` (`id`)
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `itemtypes` (`id`, `slug`) VALUES ('appetizers', 'appetizers'), ('beverages', 'beverages'), ('dumplings', 'dumplings'), ('noodles', 'noodles'), ('rice bowls', 'rice_bowls'), ('salads', 'salads'), ('sides', 'sides'), ('soups', 'soups'), ('specials', 'specials'), ('vegetarian', 'vegetarian'), ('wraps', 'wraps'); 

-- intersection of unique items and item sizes
CREATE TABLE `items` (
	`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`unique_id` INTEGER UNSIGNED NOT NULL,
	`price` DECIMAL (4,2) NOT NULL,
	`size` VARCHAR (5) NOT NULL DEFAULT 'M',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `Index_unique_id_size`(`unique_id`, `size`),
	CONSTRAINT `FK_unique_id` FOREIGN KEY `FK_unique_id` (`unique_id`)
		REFERENCES `uniqueitems` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Users can "favorite" items

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

-- Tables for online ordering

CREATE TABLE `orderheaders` (
	`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME NOT NULL,
	`subtotal_price` DECIMAL (8,2) NOT NULL,
	`tax_price` DECIMAL (8,2) NOT NULL,
	`total_price` DECIMAL (8,2) NOT NULL,
	`customer_name` VARCHAR(100) NOT NULL,
	`note` TEXT,
	`customer_id` INTEGER UNSIGNED,
	`paid` BOOLEAN NOT NULL DEFAULT 0,
	`pickup_time` DATETIME,
	PRIMARY KEY (`id`),
	CONSTRAINT `FK_orderheaders_customer_id` FOREIGN KEY `FK_orderheaders_customer_id` (`customer_id`)
		REFERENCES `users` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `orderdetails` (
	`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`orderheader_id` INTEGER UNSIGNED NOT NULL,
	`item_id` INTEGER UNSIGNED NOT NULL,
	`spiciness` TINYINT NOT NULL DEFAULT 0,
	`instructions` TEXT,
	`quantity` INTEGER NOT NULL DEFAULT 1,
	`sale_price` DECIMAL (4,2) NOT NULL,
	`amount` INTEGER (8) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `Index_orderheader_id_orderitem_id`(`orderheader_id`, `item_id`),
	CONSTRAINT `FK_orderdetails_orderheader_id` FOREIGN KEY `FK_orderdetails_orderheader_id` (`orderheader_id`)
		REFERENCES `orderheaders` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	CONSTRAINT `FK_orderdetails_item_id` FOREIGN KEY `FK_orderdetails_item_id` (`item_id`)
		REFERENCES `items` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci;
