<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Forgotten Password</title>
    <style>
        .link > a {
            color: #E14D57 !important;
        }
    </style>
</head>
<body style="background:#F2F2F2;">
<table align="center" width="600" border="0" cellpadding="5" bgcolor="#2A2A2A">
    <tr>
        <td align="center" bgcolor="#333">
            <?= (PROJECT_LOGO != '') ? '<img src="'.base_url(PROJECT_LOGO) . '" width="200" />' : PROJECT_NAME ?>
        </td>
    </tr>
    <tr>
        <td bgcolor="#2a2a2a" style="color:#fff">
            <p style="font-family:Verdana, Geneva, sans-serif; font-size:16px; color:#FFF;">Dear <?= $username ?>
                ,</p>
            <p style="font-family:Verdana, Geneva, sans-serif; font-size:12px; color:#fff;">You can change your password with the link below:</p>
            <p style="font-family:Verdana, Geneva, sans-serif; font-size:12px; color:color:#fff;">&raquo;
                <a style="color:#E14D57; " href="<?= $loginlink ?>">Change Password</a></p>
            <p style="font-family:Verdana, Geneva, sans-serif; font-size:12px; color:#fff !important;">
                <span
                    style="color:#fff">If you cannot press this link then copy and paste following link to an another tab to do so.</span>
                <br/>
                <span class="link"><?= $loginlink ?></span>
            </p>
        </td>
    </tr>
    <tr>
        <td bgcolor="#2a2a2a">
            <p style="font-family:Verdana, Geneva, sans-serif; font-size:12px; color:#fff">Regards,
                <br/>
                <?= PROJECT_NAME ?>.
            </p>
        </td>
    </tr>
    <tr>
    </tr>
    <tr>
        <td align="center" bgcolor="#333">
            <p
                style="font-family:Verdana, Geneva, sans-serif; font-size:10px; color:#FFF;">
                <?php echo date("Y") . ' ' . PROJECT_NAME ?>
            </p>
        </td>
    </tr>
</table>
</body>
</html>