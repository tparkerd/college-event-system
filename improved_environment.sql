-- College Event Website
-- Environment Creation

-- Create Database
DROP DATABASE IF EXISTS ces;
CREATE DATABASE ces
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;
USE ces;

-- User
-- Possible improvement would to be to use just the email as the primary key
-- instead of the SID. Since the project was meant for more than one university,
-- there may be overlapping SIDs. As such, the index would be better placed on
-- the email instead of SID
CREATE TABLE `user`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `sid` CHAR(8) NOT NULL, -- Not null because we want it to be required
    `given_name` VARCHAR(35),
    `family_name` VARCHAR(35),
    `email` VARCHAR(90) NOT NULL,
    `pword` CHAR(100) NOT NULL,
    CONSTRAINT PRIMARY KEY (`id`),
    CONSTRAINT UNIQUE (`email`)
) ENGINE = InnoDB;
CREATE INDEX `id` ON `user`(`id`);

-- Webmaster
-- There needs to be someone to maintain the site with full permissions, so this
-- would be created first, and it only depends on student. One major improvement
-- would be to remove the redundant attributes on the table that already exist
-- in the student table.
CREATE TABLE `webmaster`
(
    `id` INTEGER(11),
    CONSTRAINT PRIMARY KEY (`id`),
    CONSTRAINT FOREIGN KEY (`id`) REFERENCES user(`id`)
      ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;
CREATE INDEX `id` ON `webmaster`(`id`);


CREATE TABLE `university`
(
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100),
  `address` VARCHAR(175),
  `description` VARCHAR(500),
  `num_students` INTEGER(11) DEFAULT 0,
  `picture_one` VARCHAR(250),
  `picture_two` VARCHAR(250),
  CONSTRAINT PRIMARY KEY (`id`),
  CONSTRAINT UNIQUE (`name`)
) ENGINE = InnoDB;
CREATE INDEX id ON `university`(`id`);


-- Superadmin
CREATE TABLE `superadmin`
(
 `id` INTEGER(11) NOT NULL,
 CONSTRAINT PRIMARY KEY (`id`),
 CONSTRAINT FOREIGN KEY (`id`) REFERENCES `user`(`id`)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- Create relationship to user
CREATE TABLE `user_owns_uni`
(
  `user_id` INTEGER(11),
  `university_id` INTEGER(11),
  CONSTRAINT PRIMARY KEY (`user_id`, `university_id`), -- double check that this doesn't mean that you can have more than one of each
  CONSTRAINT FOREIGN KEY (`user_id`) REFERENCES `superadmin`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`university_id`) REFERENCES `university`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- DB: Promote user to superadmin
DELIMITER $$
CREATE TRIGGER `user_promoted_to_superadmin`
BEFORE INSERT ON `user_owns_uni`
FOR EACH ROW
  BEGIN
    -- Since nothing can be done with the university by others until it is
    -- approved, just go ahead and promote the user to superadmin
    INSERT INTO `superadmin` VALUES (NEW.user_id);
  END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER user_creates_uni
AFTER INSERT ON `user_owns_uni`
FOR EACH ROW
  BEGIN
    -- Affiliate the user with the university
    INSERT INTO `user_affiliates_uni`
      VALUES(NEW.user_id, NEW.university_id);
  END$$
DELIMITER ;

-- Allows users to join a single university
-- Therefore, the primary key is the user's ID
CREATE TABLE `user_affiliates_uni`
(
  `user_id` INTEGER(11),
  `university_id` INTEGER(11),
  CONSTRAINT PRIMARY KEY (`user_id`),
  CONSTRAINT FOREIGN KEY (`user_id`) REFERENCES `user`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`university_id`) REFERENCES `university`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- Update the number of students associated with the university as needed
DELIMITER $$
CREATE TRIGGER `user_joins_uni`
AFTER INSERT ON `user_affiliates_uni`
FOR EACH ROW
  BEGIN
    UPDATE `university`
    SET `num_students` = `num_students` + 1
    WHERE `id` = NEW.university_id;
  END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `user_changes_uni`
AFTER UPDATE ON `user_affiliates_uni`
FOR EACH ROW
  BEGIN
  UPDATE `university` u
  SET `num_students` = `num_students` - 1
  WHERE u.id = OLD.university_id;
  UPDATE `university` u
  SET `num_students` = `num_students` + 1
  WHERE u.id = NEW.university_id;
  END$$
DELIMITER ;


-- Approve a university
CREATE TABLE `webmaster_approves_uni`
(
  `webmaster_id` INTEGER(11),
  `university_id` INTEGER(11),
  PRIMARY KEY (`webmaster_id`, `university_id`),
  FOREIGN KEY (`webmaster_id`) REFERENCES `webmaster`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`university_id`) REFERENCES `university`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

  -- ADMIN
CREATE TABLE `admin`
(
  `id` INTEGER(11),
  CONSTRAINT PRIMARY KEY (`id`),
  CONSTRAINT FOREIGN KEY (`id`) REFERENCES `user`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- RSO
-- NOTE(timp): removed admin_id, as it will now be on the user_owns_rso
CREATE TABLE `rso`
(
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80),
  `description` TEXT,
  CONSTRAINT PRIMARY KEY (`id`),
  CONSTRAINT UNIQUE (`name`) -- RSOs must have a unique name
) ENGINE = InnoDB;
CREATE INDEX `id` ON `rso`(`id`);

-- User owns RSO
-- Allow user to own a single RSO
-- NOTE(timp): improvement - allow someone to own multiple RSOs
CREATE TABLE `user_owns_rso` (
  `user_id` INTEGER(11),
  `rso_id` INTEGER(11),
  CONSTRAINT PRIMARY KEY (`rso_id`, `user_id`),
  CONSTRAINT FOREIGN KEY (`user_id`) REFERENCES `admin`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`rso_id`) REFERENCES `rso`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;


CREATE TABLE `user_joins_rso`
(
  `user_id` INTEGER(11),
  `rso_id` INTEGER(11),
  CONSTRAINT PRIMARY KEY (`rso_id`),
  CONSTRAINT FOREIGN KEY (`user_id`) REFERENCES `user`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`rso_id`) REFERENCES `rso`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;
CREATE INDEX `user_id` ON `user_joins_rso`(`user_id`);

CREATE TABLE `rso_belongs_to_uni`
(
  `rso_id` INTEGER(11),
  `university_id` INTEGER(11),
  CONSTRAINT PRIMARY KEY (`rso_id`),
  CONSTRAINT FOREIGN KEY (`rso_id`) REFERENCES `rso`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`university_id`) REFERENCES `university`(`id`)
) ENGINE = InnoDB;
CREATE INDEX university_id ON `rso_belongs_to_uni`(`university_id`);

-- DB: Promote user to superadmin
DELIMITER $$
CREATE TRIGGER `user_promoted_to_admin`
BEFORE INSERT ON `user_owns_rso`
FOR EACH ROW
  BEGIN
    -- Since nothing can be done with the RSO by others until it is
    -- approved, just go ahead and promote the user to admin
    INSERT INTO `admin` VALUES (NEW.user_id);
  END$$
DELIMITER ;

-- On creation, have the admin join the RSO
DELIMITER $$
CREATE TRIGGER `user_creates_rso`
AFTER INSERT ON `user_owns_rso`
FOR EACH ROW
  BEGIN
    INSERT INTO `user_joins_rso`
      VALUES(NEW.user_id, NEW.rso_id);
    -- Establish the university for the RSO based on the university
    -- affiliated with the user
    INSERT INTO `rso_belongs_to_uni`(`rso_id`, `university_id`)
      VALUES (NEW.rso_id, (SELECT u.university_id FROM `user_affiliates_uni` u WHERE u.user_id = NEW.user_id));
  END$$
DELIMITER ;

-- Superadmin approves RSO creation
CREATE TABLE `superadmin_approves_rso`
(
  `rso_id` INTEGER(11),
  `user_id` INTEGER(11),
  CONSTRAINT PRIMARY KEY (`rso_id`),
  CONSTRAINT FOREIGN KEY (`rso_id`) REFERENCES `rso`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`user_id`) REFERENCES `superadmin`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- Event
-- NOTE(timp): PHP will have to handle the constraints
-- I'm not sure how to handle it in SQL without using a transaction
CREATE TABLE `event`
(
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` CHAR(80) NOT NULL,
  `start_date` DATE NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME,
  `end_date` DATE,
  `description` VARCHAR(500),
  `category` VARCHAR(50),
  `email` VARCHAR(90) NOT NULL,
  `phone` VARCHAR(13) NOT NULL,
  `rating` DECIMAL(3,2) DEFAULT '0.00',
  `approved_by_admin` INTEGER(11) NOT NULL,
  CONSTRAINT PRIMARY KEY (`id`)
) ENGINE = InnoDB;

-- location
CREATE TABLE `location`
(
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `name` CHAR(200),
  `latitude` DECIMAL(10,8) NOT NULL,
  `longitude` DECIMAL(11,8) NOT NULL,
  CONSTRAINT PRIMARY KEY (`id`),
  CONSTRAINT UNIQUE (`name`)
) ENGINE = InnoDB;
CREATE INDEX `location_name` ON `location`(`name`);


-- Associate an event with its location
CREATE TABLE `at`
(
  `event_id` INTEGER(11),
  `location_id`  INTEGER(11),
  CONSTRAINT PRIMARY KEY (`event_id`),
  CONSTRAINT FOREIGN KEY (`event_id`) REFERENCES `event`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`location_id`) REFERENCES `location`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;
CREATE INDEX `event_id` ON `at`(`event_id`);

CREATE TABLE `user_owns_event`
(
  `user_id` INTEGER(11),
  `event_id` INTEGER(11),
  CONSTRAINT PRIMARY KEY (`event_id`),
  CONSTRAINT FOREIGN KEY (`event_id`) REFERENCES `event`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`user_id`) REFERENCES `admin`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;
CREATE TABLE `user_comments_event`
(
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `user_id` INTEGER(11) NOT NULL,
  `event_id` INTEGER(11) NOT NULL,
  `rating` INTEGER(11) NOT NULL,
  `timestamp` TIMESTAMP,
  `text` TEXT(500),
  CONSTRAINT PRIMARY KEY (`id`),
  CONSTRAINT FOREIGN KEY (`user_id`) REFERENCES `user`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`event_id`) REFERENCES `event`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;
CREATE INDEX `comment_index` ON `user_comments_event`(`user_id`);

CREATE TABLE `event_public`
(
  `id` INTEGER(11),
  CONSTRAINT PRIMARY KEY (`id`),
  CONSTRAINT FOREIGN KEY (`id`) REFERENCES `event`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;
CREATE TABLE `event_private`
(
  `id` INTEGER(11),
  CONSTRAINT PRIMARY KEY (`id`),
  CONSTRAINT FOREIGN KEY (`id`) REFERENCES `event`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;
CREATE TABLE `event_rso`
(
  `id` INTEGER(11),
  CONSTRAINT PRIMARY KEY (`id`),
  CONSTRAINT FOREIGN KEY (`id`) REFERENCES `event`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;
CREATE TABLE `rso_owns_event`
(
  `rso_id` INTEGER(11),
  `event_id` INTEGER(11),
  CONSTRAINT PRIMARY KEY (`event_id`),
  CONSTRAINT FOREIGN KEY (`event_id`) REFERENCES `event`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`rso_id`) REFERENCES `rso`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;
CREATE TABLE `superadmin_approves_public_event`
(
  `event_id` INTEGER(11),
  `user_id` INTEGER(11),
  CONSTRAINT PRIMARY KEY (`event_id`),
  CONSTRAINT FOREIGN KEY (`event_id`) REFERENCES `event_public`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`user_id`) REFERENCES `superadmin`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;
CREATE TABLE `superadmin_approves_private_event`
(
  `event_id` INTEGER(11),
  `user_id` INTEGER(11),
  CONSTRAINT PRIMARY KEY (`event_id`),
  CONSTRAINT FOREIGN KEY (`event_id`) REFERENCES `event_private`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`user_id`) REFERENCES `superadmin`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;
CREATE TABLE `superadmin_approves_rso_event`
(
  `event_id` INTEGER(11),
  `user_id` INTEGER(11),
  CONSTRAINT PRIMARY KEY (`event_id`),
  CONSTRAINT FOREIGN KEY (`event_id`) REFERENCES `event_rso`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`user_id`) REFERENCES `superadmin`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;























-- GUI/DB: Register - Create the webmaster's account
INSERT INTO `user`(`sid`, `given_name`, `family_name`, `email`, `pword`)
  VALUES('-', 'Webmaster', NULL, 'web@master.com', 'test');
INSERT INTO `webmaster` VALUES(LAST_INSERT_ID());

-- GUI: Register - Create user to make the university
INSERT INTO `user`(`sid`, `given_name`, `family_name`, `email`, `pword`)
  VALUES('ucfsuper', 'UCF Superadmin', NULL, 'superadmin@email.com', 'test');

-- GUI: Create a university
INSERT INTO `university`(`name`, `address`, `description`, `picture_one`, `picture_two`)
  VALUES('University of Central Florida', '4000 Central Florida Blvd, Orlando, FL 32816',
         'The University of Central Florida is an American public research university in Orlando, Florida. It is the largest university in the United States by undergraduate enrollment, and the second largest by total enrollment.',
         'http://goo.gl/e3mMpp', 'http://goo.gl/9ZKK8E');

-- GUI: after creation of uni, make the user an owner (behind the scenes, PHP)
INSERT INTO `user_owns_uni`(`user_id`, `university_id`)
 VALUES((SELECT `id` FROM `user` u WHERE u.sid = 'ucfsuper'), LAST_INSERT_ID());

-- GUI: Approve the university
INSERT INTO `webmaster_approves_uni` VALUES((SELECT w.`id`
                                            FROM `webmaster` w
                                            LIMIT 1),
                                            (SELECT u.id
                                            FROM `university` u
                                            WHERE u.id = (SELECT `id` FROM `university` u2 WHERE u2.name = 'University of Central Florida')));

-- GUI: Make a new user - ADMIN
INSERT INTO `user`(`sid`, `given_name`, `email`, `pword`)
 VALUES('ucfadmin', 'UCF Admin', 'admin@email.com', 'test');
-- Assume that the user selected a university, so an extra insertion happens
-- This will have to be an extra PHP query, but using a user-defined variable for now
SET @tmp = (SELECT u.id FROM `university` u WHERE u.name = 'University of Central Florida');
INSERT INTO `user_affiliates_uni`(`user_id`, `university_id`)
  VALUES(LAST_INSERT_ID(), @tmp);

-- GUI: request to create an RSO
INSERT INTO `rso`(`name`, `description`) VALUES ('TechKnights', 'Tech Knights is a student organization at the University of Central Florida, devoted to the builder in everyone. As an RSO, we provide workshops, activities, and support for the hackers of UCF, giving the tools and skills needed to build something extraordinary.');
-- GUI: after creation of RSO, make the user the owner (behind the scenes, PHP)
INSERT INTO `user_owns_rso`(`user_id`, `rso_id`)
 VALUES((SELECT `id` FROM `user` u WHERE u.sid = 'ucfadmin'), LAST_INSERT_ID());

-- GUI: superadmin approves of RSO
INSERT INTO `superadmin_approves_rso`(`user_id`, `rso_id`)
  VALUES((SELECT u.id FROM `user` u WHERE u.sid = 'ucfsuper'), (SELECT r.id FROM `rso` r WHERE r.name = 'TechKnights'));

-- GUI: Create events
-- Create public event
-- Just make sure the user is an admin on insertion
-- We're not checking anything else because we will set the public, private, and rso stuff later
INSERT INTO `event`(`name`,
                    `start_date`,
                    `start_time`,
                    `end_date`,
                    `end_time`,
                    `description`,
                    `category`,
                    `email`,
                    `phone`,
                    `approved_by_admin`)
  VALUES(
    'Angular Workshop',
    CURDATE(),
    CURTIME(),
    CURDATE(),
    ADDTIME(CURTIME(), '01:00:00'),
    'An introduction to AngularJS: we will build a todo list app using AngularJS
    and compare it to other MVC JavaScript frameworks such as ReactJS.',
    'Workshop',
    'admin@email.com',
    '5551234567',
    (SELECT `id`
      FROM `admin`
      WHERE `id`= (SELECT `id`
                   FROM `user`
                   WHERE `email` = 'admin@email.com')));
INSERT INTO `event_public` VALUES(LAST_INSERT_ID());
-- Assign it a location
SET @tmp_event_id = LAST_INSERT_ID();
INSERT INTO `location`(`name`, `latitude`, `longitude`) VALUES ('UCF HEC', 28.6005753, -81.1998684);
INSERT INTO `at`(`location_id`, `event_id`) VALUES(LAST_INSERT_ID(), @tmp_event_id);

-- GUI: create private event
INSERT INTO `event`(`name`,
                    `start_date`,
                    `start_time`,
                    `end_date`,
                    `end_time`,
                    `description`,
                    `category`,
                    `email`,
                    `phone`,
                    `approved_by_admin`)
VALUES(
  'Exercise Seminar',
  CURDATE(),
  CURTIME(),
  CURDATE(),
  ADDTIME(CURTIME(), '01:00:00'),
  'A get together to inform students about the importance of maintaining a healthy exercise routine throughout their academic careers.',
  'Seminar',
  'admin@email.com',
  '5551234567',
  (SELECT `id`
    FROM `admin`
    WHERE `id`= (SELECT `id`
                 FROM `user`
                 WHERE `email` = 'admin@email.com')));

INSERT INTO `event_private` VALUES(LAST_INSERT_ID());
-- Assign it a location
SET @tmp_event_id = LAST_INSERT_ID();
INSERT INTO `location`(`name`, `latitude`, `longitude`) VALUES ('UCF Reflection Pond', 28.5996364, -81.2041248);
INSERT INTO `at`(`location_id`, `event_id`) VALUES(LAST_INSERT_ID(), @tmp_event_id);

-- GUI: create RSO event
INSERT INTO `event`(`name`,
                    `start_date`,
                    `start_time`,
                    `end_date`,
                    `end_time`,
                    `description`,
                    `category`,
                    `email`,
                    `phone`,
                    `approved_by_admin`)
VALUES(
  'Hackathon Sponsorship Meeting',
  CURDATE(),
  CURTIME(),
  CURDATE(),
  ADDTIME(CURTIME(), '02:30:00'),
  'Members only meeting to hash out the details on getting as many sponsors on board for the next hackathon.',
  'Meeting',
  'admin@email.com',
  '5551234567',
  (SELECT `id`
    FROM `admin`
    WHERE `id`= (SELECT `id`
                 FROM `user`
                 WHERE `email` = 'admin@email.com')));
INSERT INTO `event_rso` VALUES(LAST_INSERT_ID());
-- Assign it a location
SET @tmp_event_id = LAST_INSERT_ID();
INSERT INTO `location`(`name`, `latitude`, `longitude`) VALUES ('UCF Student Union', 28.601936, -81.2027372);
INSERT INTO `at`(`location_id`, `event_id`) VALUES(LAST_INSERT_ID(), @tmp_event_id);

-- Approve the public event
