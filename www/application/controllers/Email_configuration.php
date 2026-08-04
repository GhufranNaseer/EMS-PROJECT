<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_configuration extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->activateRightsSystem();
    }

    protected function rule()
    {
        return array(
            'index' => array(
                'rule' => '@'
            ),
            'crd_edit_validate' => array(
                'rule' => '@',
                'ajaxOnly' => true
            ),
            'crd_edit_submit' => array(
                'rule' => '@'
            ),
            'test_email' => array(
                'rule' => '@',
                'ajaxOnly' => true
            ),
            'email_logs' => array(
                'rule' => '@'
            ),
            'email_logs_datatable' => array(
                'rule' => '@',
                'ajaxOnly' => true
            )
        );
    }

    public function index()
    {
        $this->load->model('email_configuration_model');
        $data['email_config'] = $this->email_configuration_model->get_settings();

        $this->load->view('includes/after_login/head');
        $this->load->view('settings/email_configuration', $data);
    }

    public function crd_edit_validate()
    {
        $this->load->model('email_configuration_model');

        $this->form_validation->set_rules('mail_from_name', 'mail_from_name*Mail From Name', 'trim|required');
        $this->form_validation->set_rules('mail_from_email', 'mail_from_email*Mail From Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('cc_email', 'cc_email*CC Email', 'trim');
        $this->form_validation->set_rules('enable_email_queue', 'enable_email_queue*Enable Email Queue', 'trim|required|in_list[yes,no]');
        $this->form_validation->set_rules('mail_driver', 'mail_driver*Mail Driver', 'trim|required|in_list[mail,smtp]');
        $this->form_validation->set_rules('emails_per_cron', 'emails_per_cron*Emails Per Cron Run', 'trim|required|integer');
        $this->form_validation->set_rules('retry_attempts', 'retry_attempts*Retry Attempts', 'trim|required|integer');
        $this->form_validation->set_rules('enable_logging', 'enable_logging*Enable Email Logging', 'trim|required|in_list[yes,no]');
        $this->form_validation->set_rules('smtp_debug', 'smtp_debug*SMTP Debug Mode', 'trim|required|in_list[yes,no]');
        $this->form_validation->set_rules('test_email_address', 'test_email_address*Send Test Email', 'trim|valid_email');

        if ($this->input->post('mail_driver') === 'smtp') {
            $this->form_validation->set_rules('mail_host', 'mail_host*Mail Host', 'trim|required');
            $this->form_validation->set_rules('mail_port', 'mail_port*Mail Port', 'trim|required|integer');
            $this->form_validation->set_rules('mail_encryption', 'mail_encryption*Mail Encryption', 'trim|required|in_list[ssl,tls,starttls]');
            $this->form_validation->set_rules('mail_username', 'mail_username*Mail Username', 'trim|required');
        } else {
            $this->form_validation->set_rules('mail_host', 'mail_host*Mail Host', 'trim');
            $this->form_validation->set_rules('mail_port', 'mail_port*Mail Port', 'trim|integer');
            $this->form_validation->set_rules('mail_encryption', 'mail_encryption*Mail Encryption', 'trim|in_list[ssl,tls,starttls]');
            $this->form_validation->set_rules('mail_username', 'mail_username*Mail Username', 'trim');
        }

        $this->form_validation->set_rules('mail_password', 'mail_password*Mail Password', 'trim');

        if ($this->form_validation->run() == false) {
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        }

        $emails_per_cron = (int) $this->input->post('emails_per_cron');
        if ($emails_per_cron < 1 || $emails_per_cron > 500) {
            return $this->common->doError(func_num_args(), 'Emails Per Cron Run must be between 1 and 500');
        }

        $retry_attempts = (int) $this->input->post('retry_attempts');
        if ($retry_attempts < 1 || $retry_attempts > 10) {
            return $this->common->doError(func_num_args(), 'Retry Attempts must be between 1 and 10');
        }

        if ($this->input->post('mail_driver') === 'smtp') {
            $mail_port = (int) $this->input->post('mail_port');
            if ($mail_port < 1 || $mail_port > 65535) {
                return $this->common->doError(func_num_args(), 'Mail Port must be between 1 and 65535');
            }

            if (trim($this->input->post('mail_password')) === '' && !$this->email_configuration_model->has_saved_password()) {
                return $this->common->doError(func_num_args(), 'Mail Password is required for SMTP');
            }
        }

        return $this->common->doError(func_num_args(), "done", true);
    }

    public function crd_edit_submit()
    {
        if ($this->crd_edit_validate() !== true) {
            show_404();
        }

        $this->load->model('email_configuration_model');

        $user_id = isset($this->userdata->id) ? $this->userdata->id : null;
        $this->email_configuration_model->save_settings($this->input->post(), $user_id);

        $this->session->set_flashdata('message', 'Email configuration has been updated successfully');
        redirect(base_url('email-configuration'));
    }

    public function test_email()
    {
        $this->load->model('email_configuration_model');

        $this->form_validation->set_rules('test_email_address', 'test_email_address*Send Test Email', 'trim|required|valid_email');

        if ($this->form_validation->run() == false) {
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        }

        $settings = $this->email_configuration_model->get_mailer_settings();
        $to = trim($this->input->post('test_email_address'));
        $subject = 'Email configuration test';
        $message = $this->load->view('email', array(
            'event_name' => $settings['mail_from_name'],
            'message' => '<p>This is a test email from the Email Configuration page.</p>'
        ), true);

        $this->load->helper('phpmailer');
        $result = sendMail($to, $to, $subject, $message, $settings['mail_from_name'], $settings['mail_from_email'], '', array(
            'settings' => $settings,
            'echo' => false,
            'debug' => $settings['smtp_debug'] === 'yes',
        ));

        $this->email_configuration_model->log_email(array(
            'email_type' => 'TEST_EMAIL',
            'recipient_email' => $to,
            'subject' => $subject,
            'driver' => $settings['mail_driver'],
            'status' => $result['success'] ? 'test' : 'failed',
            'attempts' => 1,
            'error_message' => $result['success'] ? null : $result['error'],
            'debug_message' => $settings['smtp_debug'] === 'yes' ? $result['debug'] : null,
        ));

        if ($result['success']) {
            exit('done');
        }

        exit($this->email_configuration_model->sanitize_debug($result['error']));
    }

    public function email_logs()
    {
        $this->load->view('includes/after_login/head');
        $this->load->view('settings/email_logs');
    }

    public function email_logs_datatable()
    {
        $this->load->library('datatables');

        $this->datatables
            ->select('id, recipient_email, subject, email_type, driver, status, attempts, error_message, debug_message, created_on')
            ->from('email_logs');

        $this->datatables->add_column('col_status', function ($row) {
            $status = $row['status'];
            $badge = 'default';
            if ($status === 'sent') {
                $badge = 'success';
            } elseif ($status === 'failed') {
                $badge = 'danger';
            } elseif ($status === 'queued') {
                $badge = 'warning';
            } elseif ($status === 'retry') {
                $badge = 'primary';
            } elseif ($status === 'test') {
                $badge = 'info';
            }
            return '<span class="label label-' . $badge . '">' . ucfirst($status) . '</span>';
        }, NULL);

        $this->datatables->add_column('col_action', function ($row) {
            $id = $row['id'];
            
            $dataAttr = 'data-id="' . $id . '" ';
            $dataAttr .= 'data-recipient="' . html_escape($row['recipient_email']) . '" ';
            $dataAttr .= 'data-subject="' . html_escape($row['subject']) . '" ';
            $dataAttr .= 'data-type="' . html_escape($row['email_type']) . '" ';
            $dataAttr .= 'data-driver="' . html_escape($row['driver']) . '" ';
            $dataAttr .= 'data-status="' . html_escape($row['status']) . '" ';
            $dataAttr .= 'data-attempts="' . html_escape($row['attempts']) . '" ';
            $dataAttr .= 'data-created="' . html_escape($row['created_on']) . '" ';
            
            $dataAttr .= 'data-error="' . (!empty($row['error_message']) ? base64_encode($row['error_message']) : '') . '" ';
            $dataAttr .= 'data-debug="' . (!empty($row['debug_message']) ? base64_encode($row['debug_message']) : '') . '" ';

            $html = '<button type="button" class="btn btn-xs btn-default js-view-log-details" ' . $dataAttr . '>View Details</button>';
            return '<center>' . $html . '</center>';
        }, NULL);

        print ($this->datatables->generate());
    }
}
