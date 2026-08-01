<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?= PROJECT_NAME ?> - Account Credentials</title>
    <style>
        .credentials-box {
            background-color: #1e1e1e;
            border: 1px solid #444;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .credentials-row {
            margin-bottom: 8px;
            font-family: Verdana, Geneva, sans-serif;
            font-size: 13px;
            color: #ffffff;
        }
        .credentials-label {
            font-weight: bold;
            color: #cccccc;
        }
        .credentials-value {
            color: #E14D57;
            font-weight: bold;
        }
        .link > a {
            color: #E14D57 !important;
        }
    </style>
</head>
<body style="background:#F2F2F2; margin:0; padding:20px 0;">
<table align="center" width="600" border="0" cellpadding="20" cellspacing="0" bgcolor="#2A2A2A" style="border-radius: 6px; overflow: hidden;">
    <tr>
        <td align="center" bgcolor="#333333" style="border-bottom: 2px solid #E14D57;">
            <?= (defined('PROJECT_LOGO') && PROJECT_LOGO != '') ? '<img src="' . base_url(PROJECT_LOGO) . '" width="200" alt="' . PROJECT_NAME . '" />' : '<h2 style="color:#FFF; margin:0; font-family:Verdana, Geneva, sans-serif;">' . PROJECT_NAME . '</h2>' ?>
        </td>
    </tr>
    <tr>
        <td bgcolor="#2a2a2a" style="color:#fff;">
            <p style="font-family:Verdana, Geneva, sans-serif; font-size:16px; color:#FFF; margin-top:0;">
                Dear <?= html_escape($receiver_name) ?>,
            </p>
            <p style="font-family:Verdana, Geneva, sans-serif; font-size:13px; color:#ddd; line-height: 1.5;">
                Welcome to <strong><?= PROJECT_NAME ?></strong>. Your B2B user account (<?= html_escape($officer_type_label) ?>) has been successfully created. Below are your login credentials:
            </p>

            <div class="credentials-box">
                <div class="credentials-row">
                    <span class="credentials-label">Account Type:</span> <?= html_escape($officer_type_label) ?>
                </div>
                <div class="credentials-row">
                    <span class="credentials-label">Login Email:</span> <span class="credentials-value"><?= html_escape($receiver_email) ?></span>
                </div>
                <div class="credentials-row">
                    <span class="credentials-label">Password:</span> <span class="credentials-value"><?= html_escape($password) ?></span>
                </div>
            </div>

            <p style="font-family:Verdana, Geneva, sans-serif; font-size:13px; color:#ddd;">
                You can access the portal using the link below:
            </p>
            <p style="font-family:Verdana, Geneva, sans-serif; font-size:13px; margin: 15px 0;">
                &raquo; <a style="color:#E14D57; font-weight: bold; text-decoration: none;" href="<?= base_url('meeting/') ?>" target="_blank">Access B2B Portal</a>
            </p>
            <p style="font-family:Verdana, Geneva, sans-serif; font-size:11px; color:#aaa;">
                If you cannot click the button, copy and paste the following link into your browser:
                <br/>
                <span class="link" style="color:#E14D57;"><?= base_url('meeting/') ?></span>
            </p>
        </td>
    </tr>
    <tr>
        <td bgcolor="#222222" style="border-top: 1px solid #333;">
            <p style="font-family:Verdana, Geneva, sans-serif; font-size:12px; color:#bbb; margin:0;">
                Regards,<br/>
                <strong><?= PROJECT_NAME ?> Team</strong>
            </p>
        </td>
    </tr>
    <tr>
        <td align="center" bgcolor="#111111">
            <p style="font-family:Verdana, Geneva, sans-serif; font-size:10px; color:#888; margin:0;">
                &copy; <?= date("Y") . ' ' . PROJECT_NAME ?>. All rights reserved.
            </p>
        </td>
    </tr>
</table>
</body>
</html>
