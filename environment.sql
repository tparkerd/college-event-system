DROP DATABASE IF EXISTS ces;
CREATE DATABASE ces;
USE ces;

CREATE TABLE student (
  sid CHAR(8), -- based on a UCFID/PID: t2649316
  given_name VARCHAR(35),
  family_name VARCHAR(35),
  university VARCHAR(255),
  email VARCHAR(90) NOT NULL UNIQUE,
  pword CHAR(100) NOT NULL,
  PRIMARY KEY (sid)
) ENGINE = InnoDB;

CREATE TABLE webmaster (
  wid CHAR(8),
  given_name VARCHAR(35),
  family_name VARCHAR(35),
  email VARCHAR(90) NOT NULL,
  pword CHAR(100) NOT NULL,
  PRIMARY KEY (wid),
  FOREIGN KEY (wid) REFERENCES student(sid)
) ENGINE = InnoDB;

CREATE TABLE university (
  university_name VARCHAR(100),
  address VARCHAR(175),
  description VARCHAR(500),
  num_students INTEGER DEFAULT 0,
  picture_one VARCHAR(250),
  picture_two VARCHAR(250),
  PRIMARY KEY (university_name)
) ENGINE = InnoDB;

CREATE TABLE university_approved_by
(
    wid CHAR(8),
    university_name CHAR(255) NOT NULL,
    PRIMARY KEY (university_name),
    FOREIGN KEY (wid) REFERENCES webmaster (wid),
    FOREIGN KEY (university_name) REFERENCES university (university_name)
) ENGINE = InnoDB;

CREATE TABLE superadmin (
  superadmin_id CHAR(8),
  given_name VARCHAR(35),
  family_name VARCHAR(35),
  email VARCHAR(90) NOT NULL,
  pword CHAR(100) NOT NULL,
  PRIMARY KEY (superadmin_id),
  FOREIGN KEY (superadmin_id) REFERENCES student(sid)
) ENGINE = InnoDB;

CREATE TABLE creates_university (
  superadmin_id CHAR(8),
  university_name CHAR(255),
  PRIMARY KEY  (university_name),
  FOREIGN KEY (superadmin_id) REFERENCES superadmin(superadmin_id),
  FOREIGN KEY (university_name) REFERENCES university(university_name)
) ENGINE = InnoDB;

CREATE TABLE affiliates_university (
  sid CHAR(8),
  university_name CHAR(255),
  PRIMARY KEY (sid),
  FOREIGN KEY (sid) REFERENCES student(sid),
  FOREIGN KEY (university_name) REFERENCES university(university_name)
) ENGINE = InnoDB;

CREATE TABLE admin (
  admin_id CHAR(8),
  given_name VARCHAR(35),
  family_name VARCHAR(35),
  email VARCHAR(90) NOT NULL,
  pword CHAR(100) NOT NULL,
  PRIMARY KEY (admin_id),
  FOREIGN KEY (admin_id) REFERENCES student(sid)
) ENGINE = InnoDB;

CREATE TABLE rso (
  rso_name VARCHAR(80),
  admin_id CHAR(8),
  description TEXT(500),
  PRIMARY KEY (rso_name)
) ENGINE = InnoDB;

CREATE TABLE creates_rso (
  sid CHAR(8),
  rso_name VARCHAR(80),
  PRIMARY KEY (sid, rso_name),
  FOREIGN KEY (sid) REFERENCES student(sid),
  FOREIGN KEY (rso_name) REFERENCES rso(rso_name)
) ENGINE = InnoDB;

CREATE TABLE owns_rso (
  admin_id CHAR(8),
  rso_name VARCHAR(80),
  PRIMARY KEY (admin_id, rso_name),
  FOREIGN KEY (admin_id) REFERENCES admin(admin_id),
  FOREIGN KEY (rso_name) REFERENCES rso(rso_name)
) ENGINE = InnoDB;

CREATE TABLE joins_rso (
  sid CHAR(8),
  rso_name VARCHAR(80),
  approved BOOLEAN,
  since DATE, -- bonus! maybe we want to know who joined when
  PRIMARY KEY (sid, rso_name),
  FOREIGN KEY (sid) REFERENCES student(sid),
  FOREIGN KEY (rso_name) REFERENCES rso(rso_name)
) ENGINE = InnoDB;

CREATE TABLE belongs_to_university (
  rso_name VARCHAR(80),
  university_name CHAR(255),
  PRIMARY KEY (rso_name, university_name),
  FOREIGN KEY (rso_name) REFERENCES rso(rso_name),
  FOREIGN KEY (university_name) REFERENCES university(university_name)
) ENGINE = InnoDB;

CREATE TABLE rso_approved_by
(
    superadmin_id CHAR(8) NOT NULL,
    rso_name VARCHAR(80) NOT NULL,
    PRIMARY KEY (superadmin_id, rso_name),
    FOREIGN KEY (superadmin_id) REFERENCES superadmin (superadmin_id),
    FOREIGN KEY (rso_name) REFERENCES rso (rso_name)
) ENGINE = InnoDB;

CREATE TABLE e (
  eid INTEGER(11) AUTO_INCREMENT,
  event_name CHAR(80) NOT NULL,
  event_start_time TIME NOT NULL,
  event_end_time TIME,
  description VARCHAR(500),
  event_category VARCHAR(50),
  contact_email VARCHAR(90) NOT NULL,
  contact_phone VARCHAR(13) NOT NULL,
  rating DECIMAL(3,2) DEFAULT '0.00',
  approved_by_admin CHAR(8) NOT NULL,
  approved_by_superadmin CHAR(8),
  event_date DATE NOT NULL,
  PRIMARY KEY (eid, event_name, event_start_time, event_date)
) ENGINE = InnoDB;

CREATE TABLE location (
  location_name CHAR(200),
  latitude DECIMAL(10,8) NOT NULL,
  longitude DECIMAL(11,8) NOT NULL,
  PRIMARY KEY (location_name)
) ENGINE = InnoDB;

CREATE TABLE at
(
    eid INTEGER(11) NOT NULL,
    location_name CHAR(200) NOT NULL,
    PRIMARY KEY (eid),
    FOREIGN KEY (location_name) REFERENCES location (location_name)
) ENGINE = InnoDB;

CREATE TABLE creates_event (
  admin_id CHAR(8),
  eid INTEGER(11) NOT NULL,
  PRIMARY KEY (admin_id, eid),
  FOREIGN KEY (admin_id) REFERENCES admin(admin_id)
) ENGINE = InnoDB;


CREATE TABLE comments (
  sid CHAR(8),
  eid INTEGER(11),
  rating INTEGER,
  ctimestamp TIMESTAMP,
  `text` TEXT(500),
  PRIMARY KEY (sid, eid),
  FOREIGN KEY (sid) REFERENCES student(sid)
) ENGINE = InnoDB;

CREATE TABLE public_event (
  eid INTEGER(11), -- assumed that these would be behind the scences and generated
  event_name CHAR(80),
  event_start_time TIME,
  event_date DATE,
  description VARCHAR(500),
  event_category VARCHAR(50),
  contact_email VARCHAR(90) NOT NULL,
  contact_phone VARCHAR(13) NOT NULL,
  rating DECIMAL(3,2) DEFAULT '0.00',
  approved_by_admin CHAR(8) NOT NULL,
  approved_by_superadmin CHAR(8),
  event_end_time TIME,
  PRIMARY KEY (eid)
) ENGINE = InnoDB;

CREATE TABLE private_event (
  eid INTEGER(11), -- assumed that these would be behind the scences and generated
  event_name CHAR(80),
  event_start_time TIME,
  event_date DATE,
  description VARCHAR(500),
  event_category VARCHAR(50),
  contact_email VARCHAR(90) NOT NULL,
  contact_phone VARCHAR(13) NOT NULL,
  rating DECIMAL(3,2) DEFAULT '0.00',
  approved_by_admin CHAR(8) NOT NULL,
  approved_by_superadmin CHAR(8),
  event_end_time TIME,
  PRIMARY KEY (eid)
) ENGINE = InnoDB;

CREATE TABLE rso_event (
  eid INTEGER(11), -- assumed that these would be behind the scences and generated
  event_name CHAR(80),
  event_start_time TIME,
  event_date DATE,
  description VARCHAR(500),
  event_category VARCHAR(50),
  contact_email VARCHAR(90) NOT NULL,
  contact_phone VARCHAR(13) NOT NULL,
  rating DECIMAL(3,2) DEFAULT '0.00',
  approved_by_admin CHAR(8) NOT NULL,
  approved_by_superadmin CHAR(8),
  event_end_time TIME,
  PRIMARY KEY (eid)
) ENGINE = InnoDB;

CREATE TABLE owns_event (
  rso_name VARCHAR(80) NOT NULL,
  rso_eid INTEGER(11),
  PRIMARY KEY (rso_eid),
  FOREIGN KEY (rso_name) REFERENCES rso(rso_name),
  FOREIGN KEY (rso_eid) REFERENCES rso_event(eid)
) ENGINE = InnoDB;

CREATE TABLE public_approved_by (
  eid INTEGER(11),
  superadmin_id CHAR(8),
  PRIMARY KEY (eid, superadmin_id),
  FOREIGN KEY (eid) REFERENCES public_event(eid), -- this may need reference the e(eid) instead
  FOREIGN KEY (superadmin_id) REFERENCES superadmin(superadmin_id)
) ENGINE = InnoDB;

CREATE TABLE private_approved_by (
  eid INTEGER(11),
  superadmin_id CHAR(8),
  PRIMARY KEY (eid, superadmin_id),
  FOREIGN KEY (eid) REFERENCES private_event(eid),
  FOREIGN KEY (superadmin_id) REFERENCES superadmin(superadmin_id)
) ENGINE = InnoDB;

CREATE TABLE rso_e_approved_by (
  eid INTEGER,
  superadmin_id CHAR(8),
  PRIMARY KEY (eid, superadmin_id),
  FOREIGN KEY (eid) REFERENCES rso_event(eid),
  FOREIGN KEY (superadmin_id) REFERENCES superadmin(superadmin_id)
) ENGINE = InnoDB;


-- Triggers

DROP TRIGGER IF EXISTS new_student_count_update;
CREATE TRIGGER new_student_count_update
AFTER INSERT ON affiliates_university
FOR EACH ROW
  UPDATE university
  SET num_students = num_students + 1
  WHERE university_name = NEW.university_name;

DROP TRIGGER IF EXISTS switch_student_count_update;
DELIMITER $$
CREATE TRIGGER switch_student_count_update
AFTER UPDATE ON affiliates_university
FOR EACH ROW
  BEGIN
    UPDATE university
    SET num_students = num_students - 1
    WHERE university_name = OLD.university_name;
    UPDATE university
    SET num_students = num_students + 1
    WHERE university_name = NEW.university_name;
  END$$
DELIMITER ;

DROP TRIGGER IF EXISTS requests_university;
CREATE TRIGGER requests_university
BEFORE INSERT ON creates_university
FOR EACH ROW
  INSERT INTO superadmin(superadmin_id, given_name, family_name, email, pword)
  SELECT sid, given_name, family_name, email, pword FROM student WHERE sid = NEW.superadmin_id;

DROP TRIGGER IF EXISTS creates_profile;
CREATE TRIGGER creates_profile
AFTER INSERT ON creates_university
FOR EACH ROW
  INSERT INTO affiliates_university(sid, university_name)
  VALUES (NEW.superadmin_id, NEW.university_name);

-- Whenever someone creates a new comment, update the rating for the event
DROP TRIGGER IF EXISTS update_rating;
CREATE TRIGGER update_rating
AFTER INSERT ON comments
FOR EACH ROW
    UPDATE e
    INNER JOIN (SELECT AVG(c.rating) as result
                FROM comments c
                WHERE c.eid = NEW.eid) as tempTable
    SET rating = `result`
    WHERE e.eid = NEW.eid;

-- When someone creates an event, link them together in CREATES_EVENT
DROP TRIGGER IF EXISTS makes_event;
CREATE TRIGGER makes_event
AFTER INSERT ON e
FOR EACH ROW
  INSERT INTO creates_event(admin_id, eid)
  VALUES (NEW.approved_by_admin, NEW.eid);
