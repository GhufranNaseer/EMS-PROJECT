<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmailConfiguration extends MY_Controller
{
    protected function rule()
    {
        return array(
            'index' => array(
                'rule' => '@'
            )
        );
    }

    public function index()
    {
        $this->load->view('includes/after_login/head');
        $this->load->view('settings/email_configuration');
    }
}
