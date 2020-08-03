CREATE TABLE `hipic_migration` (
	`id` INT(10) NOT NULL,
	`filename` VARCHAR(50) NOT NULL DEFAULT '',
	`create_time` INT(10) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `filename` (`filename`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;