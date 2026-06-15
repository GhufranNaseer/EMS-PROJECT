<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_configuration_model extends CI_Model
{
    const TABLE = 'email_configurations';
    const LOG_TABLE = 'email_logs';
    const QUEUE_TABLE = 'es_emails_cron';

    private $defaults = array(
        'mail_from_name' => 'Event Management System',
        'mail_from_email' => 'donotreply@exhibit.com.pk',
        'enable_email_queue' => 'yes',
        'mail_driver' => 'mail',
        'mail_host' => null,
        'mail_port' => null,
        'mail_encryption' => 'ssl',
        'mail_username' => null,
        'mail_password' => '',
        'emails_per_cron' => 10,
        'retry_attempts' => 3,
        'enable_logging' => 'yes',
        'smtp_debug' => 'no',
        'test_email_address' => null,
        'is_active' => 1,
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->library('encryption');
        $this->ensure_table();
        $this->ensure_log_table();
        $this->ensure_queue_columns();
        $this->ensure_thank_you_table();
    }

    public function get_settings($include_password = false)
    {
        $row = $this->db
            ->where('is_active', 1)
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get(self::TABLE)
            ->row_array();

        if (!$row) {
            $row = $this->create_default_settings();
        }

        $settings = array_merge($this->defaults, $row);
        $settings['has_mail_password'] = !empty($row['mail_password']);

        if ($include_password) {
            $settings['mail_password'] = $this->decrypt_password($row['mail_password']);
        } else {
            $settings['mail_password'] = '';
        }

        return $settings;
    }

    public function get_mailer_settings()
    {
        $settings = $this->get_settings(true);

        if (empty($settings['mail_from_name'])) {
            $settings['mail_from_name'] = PROJECT_NAME;
        }

        if (empty($settings['mail_from_email'])) {
            $settings['mail_from_email'] = 'donotreply@' . EMAIL_DOMAIN_NAME;
        }

        return $settings;
    }

    public function save_settings($data, $user_id = null)
    {
        $current = $this->get_active_row();
        $password = trim($data['mail_password']);

        $settings = array(
            'mail_from_name' => trim($data['mail_from_name']),
            'mail_from_email' => trim($data['mail_from_email']),
            'enable_email_queue' => $data['enable_email_queue'],
            'mail_driver' => $data['mail_driver'],
            'mail_host' => $data['mail_driver'] === 'smtp' ? trim($data['mail_host']) : null,
            'mail_port' => $data['mail_driver'] === 'smtp' ? (int) $data['mail_port'] : null,
            'mail_encryption' => $data['mail_driver'] === 'smtp' ? $data['mail_encryption'] : 'ssl',
            'mail_username' => $data['mail_driver'] === 'smtp' ? trim($data['mail_username']) : null,
            'emails_per_cron' => (int) $data['emails_per_cron'],
            'retry_attempts' => (int) $data['retry_attempts'],
            'enable_logging' => $data['enable_logging'],
            'smtp_debug' => $data['smtp_debug'],
            'test_email_address' => isset($data['test_email_address']) ? trim($data['test_email_address']) : null,
            'is_active' => 1,
            'updated_by' => $user_id,
            'updated_on' => date('Y-m-d H:i:s'),
        );

        if ($password !== '') {
            $settings['mail_password'] = $this->encrypt_password($password);
        } elseif (!$current) {
            $settings['mail_password'] = null;
        }

        if ($current) {
            $this->db
                ->where('id', $current['id'])
                ->update(self::TABLE, $settings);

            return $current['id'];
        }

        $settings['created_by'] = $user_id;
        $settings['created_on'] = date('Y-m-d H:i:s');
        $this->db->insert(self::TABLE, $settings);

        return $this->db->insert_id();
    }

    public function has_saved_password()
    {
        $row = $this->get_active_row();

        return $row && !empty($row['mail_password']);
    }

    public function log_email($data)
    {
        $settings = $this->get_settings();

        if ($settings['enable_logging'] !== 'yes') {
            return false;
        }

        $this->db->insert(self::LOG_TABLE, array(
            'email_queue_id' => isset($data['email_queue_id']) ? $data['email_queue_id'] : null,
            'email_type' => isset($data['email_type']) ? $data['email_type'] : null,
            'recipient_email' => isset($data['recipient_email']) ? $data['recipient_email'] : null,
            'subject' => isset($data['subject']) ? $data['subject'] : null,
            'driver' => isset($data['driver']) ? $data['driver'] : null,
            'status' => isset($data['status']) ? $data['status'] : 'failed',
            'attempts' => isset($data['attempts']) ? (int) $data['attempts'] : 0,
            'error_message' => isset($data['error_message']) ? $this->sanitize_debug($data['error_message']) : null,
            'debug_message' => isset($data['debug_message']) ? $this->sanitize_debug($data['debug_message']) : null,
            'created_on' => date('Y-m-d H:i:s'),
        ));

        return $this->db->insert_id();
    }

    public function sanitize_debug($message)
    {
        $settings = $this->get_settings(true);
        $message = (string) $message;

        if (!empty($settings['mail_password'])) {
            $message = str_replace($settings['mail_password'], '[password hidden]', $message);
        }

        if (!empty($settings['mail_username'])) {
            $message = str_replace($settings['mail_username'], '[username hidden]', $message);
        }

        return $message;
    }

    public function get_pending_emails($limit, $retry_attempts)
    {
        return $this->db
            ->where('is_sent', 0)
            ->where_in('status', array('pending', 'failed'))
            ->where('attempts <', (int) $retry_attempts)
            ->order_by('created_on', 'ASC')
            ->limit((int) $limit)
            ->get(self::QUEUE_TABLE)
            ->result();
    }

    public function mark_processing($id)
    {
        $now = date('Y-m-d H:i:s');

        return $this->db
            ->where('id', $id)
            ->where('is_sent', 0)
            ->where_in('status', array('pending', 'failed'))
            ->set(array(
                'status' => 'processing',
                'processing_started_on' => $now,
                'updated_on' => $now,
            ))
            ->update(self::QUEUE_TABLE);
    }

    public function mark_sent($id)
    {
        $now = date('Y-m-d H:i:s');

        return $this->db
            ->where('id', $id)
            ->update(self::QUEUE_TABLE, array(
                'is_sent' => 1,
                'status' => 'sent',
                'sent_on' => $now,
                'last_error' => null,
                'updated_on' => $now,
            ));
    }

    public function mark_failed($id, $error, $max_attempts)
    {
        $email = $this->db
            ->where('id', $id)
            ->get(self::QUEUE_TABLE)
            ->row();

        $attempts = isset($email->attempts) ? ((int) $email->attempts + 1) : 1;
        $status = $attempts >= (int) $max_attempts ? 'failed' : 'pending';

        return $this->db
            ->where('id', $id)
            ->update(self::QUEUE_TABLE, array(
                'status' => $status,
                'attempts' => $attempts,
                'last_error' => $this->sanitize_debug($error),
                'processing_started_on' => null,
                'updated_on' => date('Y-m-d H:i:s'),
            ));
    }

    private function get_active_row()
    {
        return $this->db
            ->where('is_active', 1)
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get(self::TABLE)
            ->row_array();
    }

    private function create_default_settings()
    {
        $data = $this->defaults;
        $data['mail_password'] = null;
        $data['created_on'] = date('Y-m-d H:i:s');
        $data['updated_on'] = date('Y-m-d H:i:s');

        $this->db->insert(self::TABLE, $data);
        $data['id'] = $this->db->insert_id();

        return $data;
    }

    private function encrypt_password($password)
    {
        return $this->encryption->encrypt($password);
    }

    private function decrypt_password($password)
    {
        if (!$password) {
            return '';
        }

        $decrypted = $this->encryption->decrypt($password);

        return $decrypted === false ? '' : $decrypted;
    }

    private function ensure_table()
    {
        if ($this->db->table_exists(self::TABLE)) {
            return;
        }

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . self::TABLE . "` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `mail_from_name` VARCHAR(255) NOT NULL DEFAULT 'Event Management System',
                `mail_from_email` VARCHAR(255) NOT NULL DEFAULT 'donotreply@exhibit.com.pk',
                `enable_email_queue` ENUM('yes','no') NOT NULL DEFAULT 'yes',
                `mail_driver` ENUM('mail','smtp') NOT NULL DEFAULT 'mail',
                `mail_host` VARCHAR(255) DEFAULT NULL,
                `mail_port` INT UNSIGNED DEFAULT NULL,
                `mail_encryption` ENUM('ssl','tls','starttls') DEFAULT 'ssl',
                `mail_username` VARCHAR(255) DEFAULT NULL,
                `mail_password` TEXT DEFAULT NULL,
                `emails_per_cron` INT UNSIGNED NOT NULL DEFAULT 10,
                `retry_attempts` INT UNSIGNED NOT NULL DEFAULT 3,
                `enable_logging` ENUM('yes','no') NOT NULL DEFAULT 'yes',
                `smtp_debug` ENUM('yes','no') NOT NULL DEFAULT 'no',
                `test_email_address` VARCHAR(255) DEFAULT NULL,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `created_by` INT DEFAULT NULL,
                `updated_by` INT DEFAULT NULL,
                `created_on` DATETIME DEFAULT NULL,
                `updated_on` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_email_configurations_is_active` (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    private function ensure_log_table()
    {
        if ($this->db->table_exists(self::LOG_TABLE)) {
            return;
        }

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . self::LOG_TABLE . "` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `email_queue_id` INT DEFAULT NULL,
                `email_type` VARCHAR(100) DEFAULT NULL,
                `recipient_email` VARCHAR(255) DEFAULT NULL,
                `subject` VARCHAR(255) DEFAULT NULL,
                `driver` ENUM('mail','smtp') DEFAULT NULL,
                `status` ENUM('queued','sent','failed','retry','test') NOT NULL,
                `attempts` INT UNSIGNED NOT NULL DEFAULT 0,
                `error_message` TEXT DEFAULT NULL,
                `debug_message` TEXT DEFAULT NULL,
                `created_on` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_email_logs_queue_id` (`email_queue_id`),
                KEY `idx_email_logs_status` (`status`),
                KEY `idx_email_logs_created_on` (`created_on`),
                KEY `idx_email_logs_recipient_email` (`recipient_email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    private function ensure_queue_columns()
    {
        if (!$this->db->table_exists(self::QUEUE_TABLE)) {
            return;
        }

        $fields = $this->db->list_fields(self::QUEUE_TABLE);
        $columns = array(
            'status' => "ADD COLUMN `status` ENUM('pending','processing','sent','failed') NOT NULL DEFAULT 'pending' AFTER `is_sent`",
            'attempts' => "ADD COLUMN `attempts` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `status`",
            'last_error' => "ADD COLUMN `last_error` TEXT DEFAULT NULL AFTER `attempts`",
            'processing_started_on' => "ADD COLUMN `processing_started_on` DATETIME DEFAULT NULL AFTER `last_error`",
            'updated_on' => "ADD COLUMN `updated_on` DATETIME DEFAULT NULL AFTER `sent_on`",
        );

        foreach ($columns as $field => $sql) {
            if (!in_array($field, $fields)) {
                $this->db->query("ALTER TABLE `" . self::QUEUE_TABLE . "` " . $sql);
            }
        }

        if (!in_array('from_name', $fields)) {
            $this->db->query("ALTER TABLE `" . self::QUEUE_TABLE . "` ADD COLUMN `from_name` VARCHAR(255) NULL DEFAULT 'Event Management System' AFTER `data`");
        }

        $this->db->query("
            UPDATE `" . self::QUEUE_TABLE . "`
            SET `status` = CASE WHEN `is_sent` = 1 THEN 'sent' ELSE 'pending' END,
                `updated_on` = COALESCE(`sent_on`, `created_on`, NOW())
            WHERE `status` IS NULL OR `status` = ''
        ");
    }

    private function ensure_thank_you_table()
    {
        if (!$this->db->table_exists('es_event_email_log')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `es_event_email_log` (
                    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `event_id` INT UNSIGNED NOT NULL,
                    `exhibitor_id` INT UNSIGNED NOT NULL,
                    `template_id` INT UNSIGNED NOT NULL,
                    `status` ENUM('pending', 'sent', 'failed') NOT NULL DEFAULT 'pending',
                    `sent_at` DATETIME DEFAULT NULL,
                    `error_message` TEXT DEFAULT NULL,
                    `created_on` DATETIME NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `idx_event_exhibitor_template` (`event_id`, `exhibitor_id`, `template_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }

        if ($this->db->table_exists('es_exhibitions')) {
            $fields = $this->db->list_fields('es_exhibitions');
            if (!in_array('thank_you_template_id', $fields)) {
                $this->db->query("ALTER TABLE `es_exhibitions` ADD COLUMN `thank_you_template_id` INT UNSIGNED NULL DEFAULT NULL");
            }
        }
    }
}
