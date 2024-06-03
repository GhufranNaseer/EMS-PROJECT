
<page>
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
            /*font-family: courier, sans-serif;*/
            /*font-family: Impact, sans-serif;*/
            font-size: 11px;
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

        hr {
            background-color: #333;
            border: 0 none;
            color: #333;
            height: 1px;
        }

        .event_logo {
            width: 70px;
        }
        .organizer_logo {
            width: 30px;
        }
        .manager_logo {
            width: 40px;
        }
    </style>
    <table style="width: 100%; padding: 5px;margin-top: 15.0mm;"  >
        <tr>
            <td style="width: 75%;">
                <br><br>
                <strong style="margin-bottom: 3px; font-size: 13px;"><?=substr($this->badge->full_name,0,24) ?></strong><br>
				<?= substr($company,0,28) ?><br>
				<?php
				// echo '<img src="data:image/png;base64,' . base64_encode($barcode_data) . '" style="width: 180px; height: 25px; margin: 3px 0 0;">';
				echo '<img src="' . base_url($barcode_link) . '" style="width: 180px; height: 25px; margin: 3px 0 0;">';
				?>
                <br>
				<?php
				if ($this->badge->nationality == 'Pakistani' || strtoupper($this->badge->nationality) == 'PAKISTAN') {
					echo $this->badge->cnic . ' / ' . $this->badge->nationality . ' / ID #' . $this->badge->id;
				} else {
					echo $this->badge->passport . ' / ' . $this->badge->nationality . ' / ID #' . $this->badge->id;
				}
				?>
            </td>
            <td rowspan="2" style="width: 25%;" class="text-right">

            </td>
        </tr>
    </table>


</page>
