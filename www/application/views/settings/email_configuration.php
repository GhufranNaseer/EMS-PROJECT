<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>
<?php
$email_config = isset($email_config) && is_array($email_config) ? $email_config : array();
$email_config = array_merge(array(
    'mail_from_name' => '',
    'mail_from_email' => '',
    'enable_email_queue' => 'no',
    'mail_driver' => 'mail',
    'mail_host' => '',
    'mail_port' => '',
    'mail_encryption' => 'ssl',
    'mail_username' => '',
    'emails_per_cron' => 10,
    'retry_attempts' => 3,
    'enable_logging' => 'yes',
    'smtp_debug' => 'no',
    'test_email_address' => '',
), $email_config);
?>

<style>
    .email-config-form .btn-group .btn {
        min-width: 80px;
    }
    .email-config-form .alert {
        margin-top: 10px;
    }
    .smtp-fields {
        display: none;
    }
</style>

<div class="content-wrapper" data-page="email_configuration">
    <section class="content-header">
        <h1>
            Email Configuration
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li>Settings</li>
            <li class="active">Email Configuration</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Email Configuration</h3>
                        <a href="<?= base_url('email-logs.html'); ?>" class="btn btn-default pull-right"><i class="fa fa-history"></i> View Email Logs</a>
                    </div>

                    <div class="box-body">
                        <form action="<?= base_url('email-configuration-submit.html'); ?>" method="post" id="email_config_form" class="email-config-form">
                            <div class="js-msgbox"></div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="mail_from_name">Mail From Name *</label>
                                        <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" placeholder="e.g. My Company Name" value="<?= html_escape($email_config['mail_from_name']); ?>">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="mail_from_email">Mail From Email *</label>
                                        <input type="email" class="form-control" id="mail_from_email" name="mail_from_email" placeholder="e.g. noreply@example.com" value="<?= html_escape($email_config['mail_from_email']); ?>">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="cc_email">CC Email (Optional)</label>
                                        <input type="text" class="form-control" id="cc_email" name="cc_email" placeholder="e.g. cc@example.com" value="<?= html_escape(isset($email_config['cc_email']) ? $email_config['cc_email'] : ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Enable Email Queue</label>
                                        <div class="btn-group" data-toggle="buttons">
                                            <label class="btn <?= $email_config['enable_email_queue'] === 'yes' ? 'btn-primary active' : 'btn-default'; ?>">
                                                <input type="radio" name="enable_email_queue" value="yes" autocomplete="off" <?= $email_config['enable_email_queue'] === 'yes' ? 'checked' : ''; ?>> Yes
                                            </label>
                                            <label class="btn <?= $email_config['enable_email_queue'] === 'no' ? 'btn-primary active' : 'btn-default'; ?>">
                                                <input type="radio" name="enable_email_queue" value="no" autocomplete="off" <?= $email_config['enable_email_queue'] === 'no' ? 'checked' : ''; ?>> No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Mail Driver</label>
                                        <div>
                                            <div class="btn-group" data-toggle="buttons">
                                                <label class="btn <?= $email_config['mail_driver'] === 'mail' ? 'btn-primary active' : 'btn-default'; ?>">
                                                    <input type="radio" name="mail_driver" value="mail" autocomplete="off" <?= $email_config['mail_driver'] === 'mail' ? 'checked' : ''; ?>> Mail
                                                </label>
                                                <label class="btn <?= $email_config['mail_driver'] === 'smtp' ? 'btn-primary active' : 'btn-default'; ?>">
                                                    <input type="radio" name="mail_driver" value="smtp" autocomplete="off" <?= $email_config['mail_driver'] === 'smtp' ? 'checked' : ''; ?>> SMTP
                                                </label>
                                            </div>
                                        </div>
                                        <div class="alert alert-warning">
                                            We recommend using SMTP. Mail settings might not work on every server which also
                                            results in emails landing to SPAM. Please also check test email if your mail server
                                            is working or not.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="smtp-fields" id="smtp_fields">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="mail_host">Mail Host *</label>
                                            <input type="text" class="form-control" id="mail_host" name="mail_host" placeholder="e.g. mail.example.com" value="<?= html_escape($email_config['mail_host']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="mail_port">Mail Port *</label>
                                            <input type="number" class="form-control" id="mail_port" name="mail_port" placeholder="465" value="<?= html_escape($email_config['mail_port']); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="mail_encryption">Mail Encryption</label>
                                            <select class="form-control" id="mail_encryption" name="mail_encryption">
                                                <option value="ssl" <?= $email_config['mail_encryption'] === 'ssl' ? 'selected' : ''; ?>>ssl</option>
                                                <option value="tls" <?= $email_config['mail_encryption'] === 'tls' ? 'selected' : ''; ?>>tls</option>
                                                <option value="starttls" <?= $email_config['mail_encryption'] === 'starttls' ? 'selected' : ''; ?>>starttls</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="mail_username">Mail Username *</label>
                                            <input type="text" class="form-control" id="mail_username" name="mail_username" placeholder="e.g. admin@example.com" value="<?= html_escape($email_config['mail_username']); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="mail_password">Mail Password</label>
                                            <input type="password" class="form-control" id="mail_password" name="mail_password" placeholder="••••••••">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="emails_per_cron">Emails Per Cron Run *</label>
                                            <input type="number" class="form-control" id="emails_per_cron" name="emails_per_cron" value="<?= html_escape($email_config['emails_per_cron']); ?>" min="1" max="500">
                                            <p class="help-block">Number of emails processed per cron execution.</p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="retry_attempts">Retry Attempts *</label>
                                            <input type="number" class="form-control" id="retry_attempts" name="retry_attempts" value="<?= html_escape($email_config['retry_attempts']); ?>" min="1" max="10">
                                            <p class="help-block">How many times to retry a failed email before marking it as failed.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Enable Email Logging</label>
                                            <div class="btn-group" data-toggle="buttons">
                                                <label class="btn <?= $email_config['enable_logging'] === 'yes' ? 'btn-primary active' : 'btn-default'; ?>">
                                                    <input type="radio" name="enable_logging" value="yes" autocomplete="off" <?= $email_config['enable_logging'] === 'yes' ? 'checked' : ''; ?>> Yes
                                                </label>
                                                <label class="btn <?= $email_config['enable_logging'] === 'no' ? 'btn-primary active' : 'btn-default'; ?>">
                                                    <input type="radio" name="enable_logging" value="no" autocomplete="off" <?= $email_config['enable_logging'] === 'no' ? 'checked' : ''; ?>> No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>SMTP Debug Mode</label>
                                            <div class="btn-group" data-toggle="buttons">
                                                <label class="btn <?= $email_config['smtp_debug'] === 'yes' ? 'btn-primary active' : 'btn-default'; ?>">
                                                    <input type="radio" name="smtp_debug" value="yes" autocomplete="off" <?= $email_config['smtp_debug'] === 'yes' ? 'checked' : ''; ?>> Yes
                                                </label>
                                                <label class="btn <?= $email_config['smtp_debug'] === 'no' ? 'btn-primary active' : 'btn-default'; ?>">
                                                    <input type="radio" name="smtp_debug" value="no" autocomplete="off" <?= $email_config['smtp_debug'] === 'no' ? 'checked' : ''; ?>> No
                                                </label>
                                            </div>
                                            <p class="help-block">Enable only for troubleshooting SMTP connection issues.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="test_email_address">Send Test Email</label>
                                        <input type="email" class="form-control" id="test_email_address" name="test_email_address" placeholder="admin@company.com" value="<?= html_escape($email_config['test_email_address']); ?>">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-warning btn-block" id="send_test_email_btn">Send Test Email</button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10">
                                </div>
                                <div class="col-xs-2">
                                    <button type="button" class="btn btn-primary btn-block margin-bottom js-form_btn">Save Settings</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php $this->load->view('includes/after_login/footer'); ?>
<script>
    (function () {
        function toggleSmtpFields() {
            var driver = document.querySelector('input[name="mail_driver"]:checked');
            var smtpFields = document.getElementById('smtp_fields');

            if (!driver || !smtpFields) {
                return;
            }

            if (driver.value === 'smtp') {
                $(smtpFields).stop(true, true).slideDown(200);
            } else {
                $(smtpFields).stop(true, true).slideUp(200);
            }
        }

        $(document).on('change', 'input[name="mail_driver"]', function () {
            var group = $(this).closest('.btn-group');
            group.find('.btn').removeClass('btn-primary').addClass('btn-default');
            $(this).closest('.btn').removeClass('btn-default').addClass('btn-primary');
            toggleSmtpFields();
        });

        $(document).on('change', 'input[name="enable_email_queue"], input[name="enable_logging"], input[name="smtp_debug"]', function () {
            var group = $(this).closest('.btn-group');
            group.find('.btn').removeClass('btn-primary').addClass('btn-default');
            $(this).closest('.btn').removeClass('btn-default').addClass('btn-primary');
        });

        doFormValidation({
            'form': '#email_config_form',
            'msgbox': '#email_config_form .js-msgbox',
            'btnClick': '#email_config_form .js-form_btn',
            'urlValidator': "<?= base_url('email-configuration-validate.html'); ?>",
            'loadingImg': "<?= base_url('assets/img/load-indicator.gif'); ?>"
        });

        $('#send_test_email_btn').on('click', function () {
            var btn = $(this);
            var btnText = btn.html();
            btn.attr('disabled', true).html('Please wait...');

            $.ajax({
                url: "<?= base_url('email-configuration-test-email.html'); ?>",
                type: 'POST',
                data: $('#email_config_form').serialize()
            }).done(function (data) {
                if (data === 'done') {
                    if (typeof swal === 'function') {
                        swal({title: '', text: 'Test email sent successfully.', type: 'success'});
                    } else {
                        alert('Test email sent successfully.');
                    }
                } else if (typeof swal === 'function') {
                    swal({title: '', text: data, type: 'warning'});
                } else {
                    alert(data);
                }
            }).always(function () {
                btn.attr('disabled', false).html(btnText);
            });
        });

        toggleSmtpFields();
    })();
</script>
</body>
</html>
