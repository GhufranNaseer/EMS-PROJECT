<?php
$event = $this->db
	->select('E.*')
	->where(mycolumn('B.id'), $_GET['id'])
	->join('es_exhibitions as E', 'E.id = B.exhibition_id')
	->get('es_exhibition_booking as B')
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
                    <div style="background: <?= $event->event_color ?>; height: 50px; width: 100%">&nbsp;</div>
                </td>
                <td style="width: 20%;text-align: center">
                    <img src="<?= base_url( $event->event_logo) ?>" alt="" class="event_logo" style="height: 80px; width: 100px">
                </td>
                <td style="width: 40%; vertical-align: middle">
                    <div style="background: <?= $event->event_color ?>; height: 50px; width: 100%">&nbsp;</div>
                </td>
            </tr>
        </table>









    <?php
    $rows = $this->db
        ->select('F.*, C.company')
        ->where(mycolumn('F.booking_id'), $_GET['id'])
        ->where('F.form_id', 3)
        ->join('es_exhibition_booking as B', 'F.booking_id = B.id', 'LEFT')
        ->join('es_customers as C', 'B.customer_id = C.id', 'LEFT')
        ->get('es_exhibition_booking_forms_data as F')
        ->row();

    $data = (isset($rows->form_data)) ? json_decode($rows->form_data,false) : null;
//var_dump(contact_person);die;
    ?>

	<table style="width: 100%;">
		<tr style="width:100%;">
            <td style="width:70%;padding: 0">
                <table style="width: 70%">
                    <tr>
                        <td colspan="2"><strong style="margin-bottom: 3px; font-size: 13pt; color: <?= $event->event_color ?>"><?= (isset($rows->company)) ? wordwrap($rows->company, 35, "<br>\n") : '' ?></strong><br></td>
                    </tr>
                    <tr>
                        <th>Address:</th>
                        <td style="width: 90%"><?= (isset($data->exhibit->address)) ? ucfirst(strtolower($data->exhibit->address)) : '' ?></td>
                    </tr>
                    <tr>
                        <th>Country:</th>
                        <td><?= (isset($data->exhibit->country)) ? $data->exhibit->country : '' ?></td>
                    </tr>
                    <tr>
                        <th>Telephone:</th>
                        <td><?= (isset($data->exhibit->telephone)) ? $data->exhibit->telephone : '' ?></td>
                    </tr>
                    <tr>
                        <th>Fax:</th>
                        <td><?= (isset($data->exhibit->fax)) ? $data->exhibit->fax : '' ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?= (isset($data->exhibit->email)) ? $data->exhibit->email : '' ?></td>
                    </tr>
                    <tr>
                        <th>Website:</th>
                        <td><?= (isset($data->exhibit->website)) ? $data->exhibit->website : '' ?></td>
                    </tr>
                    <tr>
                        <th>Contact Person:</th>
                        <td><?= (isset($data->exhibit->contact_person->name)) ? $data->exhibit->contact_person->name : '' ?></td>
                    </tr>
                    <tr>
                        <th>Designation:</th>
                        <td><?= (isset($data->exhibit->contact_person->designation)) ? $data->exhibit->contact_person->designation : '' ?></td>
                    </tr>
                </table>
            </td>
            <td style="width:30%;">
                <?php
                if (isset($data->exhibit->company_logo)) {
					$logo = $data->exhibit->company_logo[0];
					$logo = str_replace('uploaded:', '', $logo);
					echo '<img src="' . base_url('client/' . $logo) . '" alt="" style="width: 200px; text-align: right; ">';
				}
                ?>
            </td>
		</tr>

	</table>

<table style="width: calc(100%- 10px); margin-top: 10px;">
    <tr>
        <td style="text-align: justify; font-size: 10pt">
            <?= (isset($data->profile)) ? $data->profile : '' ?>
        </td>
    </tr>
</table>

    <?php if ($data->principle->is_active == 1) { ?>
    <h4 style="margin-top: 15px">Company Principal</h4>
    <table style="width: 100%;" border="1">
        <tr>
            <th>Company</th>
            <th>Country</th>
            <th>Phone</th>
            <th>Email</th>
            <th></th>
        </tr>
        <?php
        foreach ($data->principle->principles_list as $row) {

            ?>
            <tr style="width: 100%;">
                <td style="width: 20%;"><?= isset($row->full_name) ? str_replace('+', ' ', $row->full_name) : '' ?></td>
                <td style="width: 20%;"><?= isset($row->country) ? $row->country : '' ?></td>
                <td style="width: 20%;"><?= isset($row->phone) ? $row->phone : '' ?></td>
                <td style="width: 20%;"><?= isset($row->email) ? $row->email : '' ?></td>
                <td style="width: 20%;">
                    <?php
                    if (isset($row->company_logo)) {
						$logo = $row->company_logo;
						$logo = str_replace('uploaded:', '', $logo);
						echo '<img src="' . base_url('client/' . $logo) . '" alt="" style="width: 60px; text-align: right; ">';
					}
                    ?>
                </td>
            </tr>
        <?php } ?>
    </table>
	<?php } ?>

    <page_footer>
    <table style="width:100%;vertical-align: middle;">
        <tr>
            <td style="width: 40%; vertical-align: middle;">
                <div style="background: <?= $event->event_color ?>; height: 30px; width: 100%; vertical-align: middle; text-align: center; color: #fff">&nbsp;</div>
            </td>
            <td style="width: 10%;text-align: center;">
                <img src="<?= base_url( $event->associate_logo) ?>" alt="" style="width: 60px;">
            </td>
            <td style="width: 10%;text-align: center;">
                <img src="<?= base_url( $organizer->organizer_image) ?>" alt="" style="width: 60px;">
            </td>
            <td style="width: 40%; vertical-align: middle;">
                <div style="background: <?= $event->event_color ?>; height: 30px; width: 100%; vertical-align: middle; text-align: center; color: #fff">www.ideaspakistan.gov.pk</div>
            </td>
        </tr>
    </table>
    </page_footer>
</page>

