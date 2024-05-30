
CREATE VIEW invitation_list_datatable AS
            SELECT
              `b`.`id`            AS `id`,
              `b`.`exhibition_id` AS `exhibition_id`,
              `b`.`booking_id`    AS `booking_id`,
              `c`.`company`       AS `company`,
              `c`.`address`       AS `company_address`,
              `b`.`full_name`     AS `full_name`,
              `b`.`designation`   AS `designation`,
              `b`.`mobile`        AS `mobile`,
              `b`.`cnic`          AS `cnic`,
              `b`.`passport`      AS `passport`,
              `b`.`is_active`     AS `is_active`,
              (SELECT created_on FROM es_exhibition_badges_invitation WHERE badge_id = B.id GROUP BY badge_id ORDER BY created_on DESC) AS last_update_date,
              IF(((SELECT COUNT(0) FROM `es_exhibition_badges_invitation` WHERE ((`es_exhibition_badges_invitation`.`badge_id` = `b`.`id`) AND (`es_exhibition_badges_invitation`.`invitation_type` = 'inauguration'))) > 0),1,0) AS `has_inauguration`,
              IF(((SELECT COUNT(0) FROM `es_exhibition_badges_invitation` WHERE ((`es_exhibition_badges_invitation`.`badge_id` = `b`.`id`) AND (`es_exhibition_badges_invitation`.`invitation_type` = 'seminar'))) > 0),1,0) AS `has_seminar`,
              IF(((SELECT COUNT(0) FROM `es_exhibition_badges_invitation` WHERE ((`es_exhibition_badges_invitation`.`badge_id` = `b`.`id`) AND (`es_exhibition_badges_invitation`.`invitation_type` = 'sideline_conference'))) > 0),1,0) AS `has_sideline_conference`,
              IF(((SELECT COUNT(0) FROM `es_exhibition_badges_invitation` WHERE ((`es_exhibition_badges_invitation`.`badge_id` = `b`.`id`) AND (`es_exhibition_badges_invitation`.`invitation_type` = 'governor_reception'))) > 0),1,0) AS `has_governor_reception`,
              IF(((SELECT COUNT(0) FROM `es_exhibition_badges_invitation` WHERE ((`es_exhibition_badges_invitation`.`badge_id` = `b`.`id`) AND (`es_exhibition_badges_invitation`.`invitation_type` = 'gala_dinner'))) > 0),1,0) AS `has_gala_dinner`,
              IF(((SELECT COUNT(0) FROM `es_exhibition_badges_invitation` WHERE ((`es_exhibition_badges_invitation`.`badge_id` = `b`.`id`) AND (`es_exhibition_badges_invitation`.`invitation_type` = 'closing_ceremony'))) > 0),1,0) AS `has_closing_ceremony`,
              IF(((SELECT COUNT(0) FROM `es_exhibition_badges_invitation` WHERE ((`es_exhibition_badges_invitation`.`badge_id` = `b`.`id`) AND (`es_exhibition_badges_invitation`.`invitation_type` = 'karachi_air_show'))) > 0),1,0) AS `has_karachi_air_show`,
              IF(((SELECT COUNT(0) FROM `es_exhibition_badges_invitation` WHERE ((`es_exhibition_badges_invitation`.`badge_id` = `b`.`id`) AND (`es_exhibition_badges_invitation`.`invitation_type` = 'cm_reception'))) > 0),1,0) AS `has_cm_reception`
            FROM ((`es_exhibition_badges` `b`
                LEFT JOIN `es_exhibition_booking` `o`
                  ON ((`b`.`booking_id` = `o`.`id`)))
               LEFT JOIN `es_customers` `c`
                 ON ((`o`.`customer_id` = `c`.`id`)))



CREATE VIEW `my_appointments_datatable` AS
		SELECT
		  `a`.`id`               AS `id`,
		  `a`.`exhibition_id`    AS `exhibition_id`,
		  `a`.`appointment_from` AS `appointment_from`,
		  `a`.`user_type_from`   AS `user_type_from`,
		  `a`.`appointment_to`   AS `appointment_to`,
		  `a`.`user_type_to`     AS `user_type_to`,
		  `a`.`exhibition_day`   AS `exhibition_day`,
		  `a`.`appointment_date` AS `appointment_date`,
		  `a`.`appointment_time` AS `appointment_time`,
		  `a`.`is_approved`      AS `is_approved`,
		  `a`.`approved_on`      AS `approved_on`,
		  `a`.`is_conducted`      AS `is_conducted`,
		  `a`.`appointment_feedback`      AS `appointment_feedback`,
		  `a`.`is_canceled`      AS `is_canceled`,
		  `a`.`canceled_on`      AS `canceled_on`,
		  `a`.`created_on`       AS `created_on`,
		  `a`.`is_deleted`       AS `is_deleted`,
		  `a`.`deleted_on`       AS `deleted_on`,
		  IF((`a`.`user_type_from` = 'exhibitor'),
			(SELECT `es_customers`.`company` FROM `es_customers` WHERE (`es_customers`.`id` = `a`.`appointment_from`)),
			(SELECT CONCAT(`es_officer`.`officer_designation`,' (',
				IF(es_officer.officer_type="foreign_delegates", 'Foreign Delegate',
				IF(es_officer.officer_type="local_delegates", 'Local Delegate',
				IF(es_officer.officer_type="chief_of_servicing", 'Gov. Services Chief', "-"))),
				')')
			FROM `es_officer` WHERE (`es_officer`.`id` = `a`.`appointment_from`))) AS `appointment_from_company_name`,
		  IF((`a`.`user_type_to` = 'exhibitor'),
			(SELECT `es_customers`.`company` FROM `es_customers` WHERE (`es_customers`.`id` = `a`.`appointment_to`)),
			(SELECT CONCAT(`es_officer`.`officer_designation`,' (',
				IF(es_officer.officer_type="foreign_delegates", 'Foreign Delegate',
				IF(es_officer.officer_type="local_delegates", 'Local Delegate',
				IF(es_officer.officer_type="chief_of_servicing", 'Gov. Services Chief', "-"))),
				')')
			FROM `es_officer` WHERE (`es_officer`.`id` = `a`.`appointment_to`))) AS `appointment_to_company_name`
		FROM `es_exhibition_appointments` `a`







--
-- Table structure for table `email_template`
--

DROP TABLE IF EXISTS `email_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_template` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT NULL,
  `message` text,
  `subject` varchar(256) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `placeholders` text,
  `updated_by` int DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_template`
--

LOCK TABLES `email_template` WRITE;
/*!40000 ALTER TABLE `email_template` DISABLE KEYS */;
INSERT INTO `email_template` VALUES (3,'EVENT_INVITATION','<p>Dear Exhibitor,</p>\r\n\r\n<p>Team IDEAS 2024 are proud to welcome you as a prestigious exhibitors and contributor of 10th anniversary edition of International Defence Exhibition &amp; Seminar &ndash; IDEAS 2024 to be held from 27th to 30th November 2024 at Karachi Expo Centre - Pakistan.</p>\r\n\r\n<p>Our teams are committed to supporting you through this experience. In order to assist you both before and during the event, we have developed and installed a user friendly online Exhibitor Facilitation Centre (EFC) that shall stay in constant communication with you for all your facilitation needs as and when required.</p>\r\n\r\n<p>We would request that you please appoint ONE key contact person for your participation and provide all their contact information to enable us to provide you with our best attention and services.&nbsp;&nbsp;&nbsp;&nbsp;</p>\r\n\r\n<hr />\r\n<p>Following are the login details.</p>\r\n\r\n<p><strong>User ID: </strong>&nbsp;{EMAIL} &nbsp;&nbsp;&nbsp;&nbsp;</p>\r\n\r\n<p><strong>Password:&nbsp;&nbsp;</strong>{PASSWORD} &nbsp;&nbsp;&nbsp;&nbsp;</p>\r\n\r\n<p><a href=\"{LOGIN_URL}\" target=\"_blank\">{LOGIN_URL}</a> &nbsp;&nbsp;&nbsp;&nbsp;</p>\r\n\r\n<p>&nbsp;&nbsp;&nbsp;&nbsp;</p>\r\n\r\n<hr />\r\n<p>&nbsp;&nbsp;&nbsp;&nbsp;</p>\r\n\r\n<p>Furthermore, we highly recommend that you download Exhibitor Manual, this will allow for an in depth understanding of the event and exhibitor facilitation services. In the meantime please feel free to email or contact the undersigned in case of any query that you may have. Our teams look forward to welcoming you at IDEAS 2024.</p>\r\n\r\n<p>Thanks &amp; Best Regards,</p>\r\n\r\n<p>&nbsp;BXSS Facilitation Team - IDEAS 2024</p>\r\n\r\n<p>nternational Communications &amp; Marketing Manager</p>\r\n\r\n<p>Badar Expo Solutions (Pvt.) Ltd.</p>\r\n\r\n<p>Tel: +92-21-34821159-60</p>\r\n\r\n<p>&nbsp;Fax: +92.21.34821179</p>\r\n\r\n<p>&nbsp;Cell: +92-300-0228560</p>\r\n\r\n<p>&nbsp;Email: facilitation@exhibit.com.pk</p>\r\n','Welcome to IDEAS 2024','2024-04-30 05:15:02','2024-05-24 06:40:18','{EMAIL},{PASSWORD},{LOGIN_URL},{EVENT_NAME}',1,'This template is use to send login credentials and login link to new customers.'),(4,'RESET_PASSWORD_LINK','<p>Hello <strong>{CUSTOMER_COMPANY}</strong>,</p>\r\n\r\n<p>You can change your password with the link below:</p>\r\n\r\n<p><a href=\"{PASSWORD_RESET_LINK}\" target=\"_blank\">Change Password</a></p>\r\n\r\n<p>If you cannot press this link then copy and paste following link to an another tab to do so.</p>\r\n\r\n<p>{PASSWORD_RESET_LINK}</p>\r\n','Forgotten Password - {EVENT_NAME}','2024-04-30 05:34:54','2024-05-25 05:57:45','{PASSWORD_RESET_LINK},{CUSTOMER_COMPANY},{CUSTOMER_NAME},{CUSTOMER_EMAIL},{EVENT_NAME}',1,'This template is use to send passowrd reset link to user.'),(6,'APPOINTMENT_SCHEDULE','<p>Dear <strong>{NAME}</strong>,</p>\r\n\r\n<p>{SENDER_NAME} has requested a meeting for {APPOINTMENT_TIME}.<br />\r\n<strong>{APPOINTMENT_AGENDA}</strong></p>\r\n\r\n<hr />\r\n<p>Best,</p>\r\n\r\n<p>{SENDER_COMPANY}</p>\r\n\r\n<p>{SENDER_PHONE}</p>\r\n\r\n<p>{SENDER_EMAIL}</p>\r\n','Appointment Schedule - {APPOINTMENT_TIME}','2024-04-30 05:34:54','2024-05-27 06:12:47','{EVENT_NAME},{NAME},{EMAIL},{PHONE},{COMPANY},{SENDER_NAME},{SENDER_EMAIL},{SENDER_PHONE},{SENDER_COMPANY},{SENDER_WEBSITE},{APPOINTMENT_TIME},{APPOINTMENT_AGENDA}',1,'This template is use to send appointment schedule email notification.'),(7,'APPOINTMENT_ACCEPTED','<p>Dear <strong>{NAME}</strong>,</p>\r\n\r\n<p>Your request for {SENDER_NAME} of {SENDER_COMPANY} for {APPOINTMENT_TIME} has been accepted. Please login for further details.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Regards,</p>\r\n\r\n<p>Team {EVENT_NAME}</p>\r\n','Appointment Accepted - {APPOINTMENT_TIME}','2024-04-30 05:34:54','2024-05-25 19:03:53','{EVENT_NAME},{NAME},{EMAIL},{PHONE},{COMPANY},{SENDER_NAME},{SENDER_EMAIL},{SENDER_PHONE},{SENDER_COMPANY},{SENDER_WEBSITE},{APPOINTMENT_TIME},{APPOINTMENT_AGENDA}',1,'This template is use to send appointment accept email notification.'),(8,'APPOINTMENT_CANCELED','<p>Dear&nbsp;<strong>{NAME},</strong></p>\r\n\r\n<p>Your meeting with&nbsp;{SENDER_NAME} of&nbsp;{SENDER_COMPANY}&nbsp;for&nbsp;{APPOINTMENT_TIME}&nbsp;has been declined.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Regards,</p>\r\n\r\n<p>Team&nbsp;{EVENT_NAME}</p>\r\n','Appointment Canceled - {APPOINTMENT_TIME}','2024-04-30 05:34:54','2024-05-25 19:03:36','{EVENT_NAME},{NAME},{EMAIL},{PHONE},{COMPANY},{SENDER_NAME},{SENDER_EMAIL},{SENDER_PHONE},{SENDER_COMPANY},{SENDER_WEBSITE},{APPOINTMENT_TIME},{APPOINTMENT_AGENDA}',1,'This template is use to send appointment cancel email notification.'),(9,'APPOINTMENT_RE_SCHEDULE','<p>Dear&nbsp;<strong>{NAME}</strong></p>\r\n\r\n<p>{SENDER_NAME}&nbsp;has re-schedule a meeting for&nbsp;{APPOINTMENT_TIME} about&nbsp;{APPOINTMENT_AGENDA}</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><strong>Best,</strong></p>\r\n\r\n<p>{SENDER_COMPANY}</p>\r\n\r\n<p>{SENDER_PHONE}</p>\r\n\r\n<p>{SENDER_EMAIL}</p>\r\n','Appointment Re-Schedule - {APPOINTMENT_TIME}','2024-04-30 05:34:54','2024-05-27 06:13:07','{EVENT_NAME},{NAME},{EMAIL},{PHONE},{COMPANY},{SENDER_NAME},{SENDER_EMAIL},{SENDER_PHONE},{SENDER_COMPANY},{SENDER_WEBSITE},{APPOINTMENT_TIME},{APPOINTMENT_AGENDA}',1,'This template is use to send appointment re-schedule email notification.');
/*!40000 ALTER TABLE `email_template` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;



ALTER TABLE `database`.`es_exhibition_booking` 
ADD COLUMN `visitor_badges_limit` DOUBLE NULL AFTER `badges_total_limit`;


ALTER TABLE `database`.`es_packages` 
ADD COLUMN `visitor_badges` DOUBLE NULL AFTER `package_badges`;

ALTER TABLE `database`.`es_emails_cron` 
ADD COLUMN `from_name` VARCHAR(255) NULL DEFAULT 'Event Management System' AFTER `data`;



CREATE TABLE `es_exhibition_mou_sign` (
  `id` int NOT NULL AUTO_INCREMENT,
  `exhibition_id` int DEFAULT NULL,
  `mou_sign_location` varchar(255) DEFAULT NULL,
  `request_from_id` int DEFAULT NULL,
  `request_to_id` int DEFAULT NULL,
  `user_type_from` varchar(100) DEFAULT NULL,
  `user_type_to` varchar(100) DEFAULT NULL,
  `exhibition_day` varchar(10) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `commercial_value` varchar(255) DEFAULT NULL,
  `mou_sign_date` date DEFAULT NULL,
  `mou_sign_time` time DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT '0',
  `approved_by` int DEFAULT NULL,
  `approved_on` datetime DEFAULT NULL,
  `is_canceled` tinyint(1) DEFAULT '0',
  `canceled_by` int DEFAULT NULL,
  `canceled_on` datetime DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `deleted_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exhibition_id` (`exhibition_id`),
  CONSTRAINT `es_exhibition_mou_sign_ibfk_1` FOREIGN KEY (`exhibition_id`) REFERENCES `es_exhibitions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=latin1;


-- 30 May 2024
ALTER TABLE `database`.`es_exhibition_appointments` 
ADD COLUMN `is_conducted` TINYINT(1) NULL DEFAULT 0 AFTER `approved_on`,
ADD COLUMN `appointment_feedback` TEXT NULL AFTER `is_conducted`;

ALTER TABLE `database`.`es_exhibition_appointments` 
ADD COLUMN `discussion_points` TEXT NULL AFTER `agenda_of_meeting`,
ADD COLUMN `meeting_notes` TEXT NULL AFTER `discussion_points`;
