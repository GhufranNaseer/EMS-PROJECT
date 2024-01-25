<?php
//print_r($_GET['id']);die();
$data = $this->db
	->where(mycolumn('id'), $_GET['id'])
	->get('es_officer')
	->row();
//print_r($data);die();
$event = $this->db
	->select('E.*')
	->where('E.id', $data->exhibition_id)
	->get('es_exhibitions as E')
	->row();
$organizer = $this->db
	->where('id', $event->event_organizer)
	->get('es_organizer')
	->row();
?>

<style>

    * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    page {
        font-family: Helvetica, Arial, sans-serif;
        font-size: 12px;
    }

    .text-right {
        text-align: right;
    }

    .text-left {
        text-align: left;
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: #777;
    }

    .text-justify {
        text-align: justify;
    }

    table {
        border-spacing: 0;
        border-collapse: collapse;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    table th,
    table td {
        padding: 5px;
        word-break: break-all;
        white-space: normal;
        vertical-align: top;
    }

    table.table-bordered th,
    table.table-bordered td {
        border: 1px solid #ccc;
    }

    .light-bg {
        background: #f9f9f9;
    }

    .dark-bg {
        background: #eee;
    }

    th.bottom-border,
    td.bottom-border {
        border-bottom: 1px solid #ccc;
    }

    .event_logo {
        width: 100px;
    }
</style>

<page>

    <table style="width: 100%;vertical-align: middle">
        <tr>
            <td style="width: 40%; vertical-align: middle">
                <div style="background: <?= $event->event_color ?>;  height: 50px; width: 100%">&nbsp;</div>
            </td>
            <td style="width: 20%;text-align: center">
                <img src="<?= base_url($event->event_logo) ?>" alt="" class="event_logo">
            </td>
            <td style="width: 40%; vertical-align: middle">
                <div style="background: <?= $event->event_color ?>;  height: 50px; width: 100%">&nbsp;</div>
            </td>
        </tr>
    </table>

    <table style="width: 100%;">
        <tr style="width:100%;">
            <td style="width: 60%;">

                <table>
                    <tr>
                        <td colspan="2"><h4 style="margin-bottom: 15px">Officer Detail</h4></td>
                    </tr>
                    <tr>
                        <td><b>Designation:</b></td>
                        <td><?= $data->officer_designation ?> <?= ($data->is_representative == 1) ? ' (Representative)' : '' ?></td>
                    </tr>
					<?php if ($data->officer_type == "local_delegates") { ?>
                        <tr>
                            <td><b>Officer Company:</b></td>
                            <td><?= $data->officer_company ?></td>
                        </tr>
					<?php } ?>
                    <tr>
                        <td><b>Contact Person Name:</b></td>
                        <td><?= $data->contact_person ?></td>
                    </tr>
                    <tr>
                        <td><b>Country:</b></td>
                        <td><?= $data->officer_country ?></td>
                    </tr>
                    <tr>
                        <td><b>Phone No:</b></td>
                        <td><?= $data->officer_phone ?></td>
                    </tr>
					<?php if ($data->officer_type == "local_delegates") { ?>
                        <tr>
                            <td><b>Rank:</b></td>
                            <td><?= $data->officer_rank ?></td>
                        </tr>
					<?php } ?>
                </table>
            </td>
            <td style="width: 40%;">
                <table>
                    <tr>
                        <td colspan="2"><h4 style="margin-bottom: 15px">Login Detail</h4></td>
                    </tr>
                    <tr>
                        <td><b>Email:</b></td>
                        <td><?= $data->officer_email ?></td>
                    </tr>
                    <tr>
                        <td><b>Password:</b></td>
                        <td><?= $data->login_password ?></td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>

    <page_footer>
        <table style="width:100%;vertical-align: middle;">
            <tr>
                <td style="width: 40%; vertical-align: middle;">
                    <div
                        style="background: <?= $event->event_color ?>;  height: 30px; width: 100%; vertical-align: middle; text-align: center; color: #fff">
                        &nbsp;
                    </div>
                </td>
                <td style="width: 10%;text-align: center;">
                    <img src="<?= base_url($event->associate_logo) ?>" alt="" style="width: 60px;">
                </td>
                <td style="width: 10%;text-align: center;">
                    <img src="<?= base_url($organizer->organizer_image) ?>" alt="" style="width: 60px;">
                </td>
                <td style="width: 40%; vertical-align: middle;">
                    <div
                        style="background: <?= $event->event_color ?>; height: 30px; width: 100%; vertical-align: middle; text-align: center; color: #fff">
                        www.ideaspakistan.gov.pk
                    </div>
                </td>
            </tr>
        </table>
    </page_footer>
</page>

