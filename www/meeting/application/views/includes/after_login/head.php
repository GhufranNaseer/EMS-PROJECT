<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= (isset($this->event)) ? $this->event->exhibition_title : PROJECT_NAME ?></title>

    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('../assets'); ?>/bootstrap/css/bootstrap.css" type="text/css" />
    <link rel="stylesheet" href="<?= base_url('../assets'); ?>/bootstrap/css/font-awesome.min.css" type="text/css" />

    <link rel="stylesheet" href="<?= base_url('../assets'); ?>/css/AdminLTE.min.css" type="text/css" />
    <link rel="stylesheet" href="<?= base_url('../assets'); ?>/css/skins/skin-black.css" type="text/css" />

    <link rel="stylesheet" href="<?= base_url('../assets') ?>/iCheck/square/blue.css">

    <link rel="stylesheet" href="<?= base_url('../assets') ?>/css/jquery-ui.css">

    <link rel="stylesheet" href="<?= base_url('../assets'); ?>/daterangepicker/daterangepicker.css" type="text/css" />

    <link rel="stylesheet" href="<?= base_url('../assets'); ?>/timepicker/bootstrap-timepicker.min.css" type="text/css" />
    <link rel="stylesheet" href="<?= base_url('../assets') ?>/datepicker/datepicker3.css">

    <link rel="stylesheet" href="<?= base_url('../assets') ?>/datatable/css/style.css">

    <link rel="stylesheet" href="<?= base_url('../assets') ?>/file-upload-preview/lib/cropper.min.css">
    <link rel="stylesheet" href="<?= base_url('../assets') ?>/file-upload-preview/css/file-upload-preview.min.css">

    <link rel="stylesheet" href="<?= base_url('../assets') ?>/sweetalert/sweetalert.css">

    <style>
        /* NEW COLOR THEME
        normal #2f4050;
        dark #293846;
        */
        option[disabled] {
            color: #bbb;
        }
        .dataTable thead th {
            font-weight: bold;
        }
        .DataTables_sort_icon {
            display: block;
            float: right;
        }
        .ui-state-default {
            background: #fff;
        }
        .dataTables_scrollHead table {
            margin: 0;
        }
        .ui-widget-header {
            background: #222d32;
            border: 1px solid #1e282c;
        }
        .datepicker table tr td.disabled,
        .datepicker table tr td.disabled:hover {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .bootstrap-timepicker {
            width: 100%;
        }
        .box {
            border-top-color: #222d32 !important;
        }
        .control-text {
            padding-top: 10px;
        }
        .btn-primary {
            background-color: #222d32 !important;
            border-color: #1e282c !important;
        }
        .btn-primary:hover, .btn-primary:active, .btn-primary:active:hover  {
            -webkit-box-shadow: inset 0 0 100px rgba(0,0,0,0.2);
            box-shadow: inset 0 0 100px rgba(0,0,0,0.2);
        }
        .well.well-sm {
            overflow: auto;
        }
        fieldset {
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }
        fieldset legend {
            padding: 0 10px;
            border: 0;
            margin: 0;
        }

        .sidebar-menu .treeview-menu > li > a {
            padding: 10px 5px 10px 15px;
        }
        .sidebar-menu a.btn {
            padding: 6px 12px;
            width: 80%;
            margin: 10px auto;
            border: 1px solid #4b565a !important;
        }
        .sidebar-menu a.btn[disabled] {
            background: transparent !important;
            pointer-events: none;
            color: #b8c7ce !important;
        }
        .sidebar-menu a[disabled] {
            pointer-events: none;
            opacity: 0.7;
            background: transparent !important;
            border-color: transparent !important;
            color: #b8c7ce !important;
        }

        .panel-default {
            border-color: #1e282c;
        }
        .panel-default > .panel-heading {
            color: #fff;
            background-color: #222d32 !important;
            border-color: #1e282c !important;
        }


        <?php if (!is_null($this->event->event_color) && $this->event->event_color != '') { ?>
        .box {
            border-top-color: <?= $this->event->event_color ?> !important;
        }
        .box-header{
            text-align: center;
        }
        .box-header .box-title {
            font-size: 22px;
        }
        .btn-primary {
            background-color: <?= $this->event->event_color ?> !important;
            border-color: <?= $this->event->event_color ?> !important;
        }
        .panel-default {
            border-color: <?= $this->event->event_color ?>;
        }
        .panel-default > .panel-heading {
            color: #fff;
            background-color: <?= $this->event->event_color ?> !important;
            border-color: <?= $this->event->event_color ?> !important;
        }
        <?php } ?>

        <?php if (!is_null($this->event->event_background) && $this->event->event_background != '') { ?>
        .skin-black .main-header li.user-header {
            background-image: url("<?= base_url('../' . $this->event->event_background) ?>") !important;
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            -webkit-background-size: cover;
        }
        <?php } ?>


        #crud-table tbody td {
            vertical-align: middle;
        }

        #gallery_table_wrapper #gallery-table {
            width: 100% !important;
            border: 0;
            margin: 0;
        }
        #gallery_table_wrapper .dataTables_scrollHead {
            display: none;
        }
        #gallery_table_wrapper .dataTables_scrollBody thead {
            display: none;
        }
        #gallery_table_wrapper .dataTables_scrollBody tbody {
            display: block;
            width: 100%;
        }
        #gallery_table_wrapper .dataTables_scrollBody tbody:after {
            content: '';
            display: block;
            clear: both;
        }
        #gallery_table_wrapper .dataTables_scrollBody tbody tr,
        #gallery_table_wrapper .dataTables_scrollBody tbody th,
        #gallery_table_wrapper .dataTables_scrollBody tbody td {
            display: block;
        }
        #gallery_table_wrapper .dataTables_scrollBody tbody tr {
            width: calc(100% - 20px);
            float: left;
            margin: 10px;
            background: #f9f9f9 !important;
            text-align: center;
            border: 1px solid #ddd;
        }
        #gallery_table_wrapper .dataTables_scrollBody tbody img {
            height: auto;
            max-width: 100%;
            max-height: 100px;
        }
        #gallery_table_wrapper .dataTables_scrollBody tbody td:first-child {
            font-weight: bold;
        }
        @media only screen and (min-width: 768px) {
            #gallery_table_wrapper .dataTables_scrollBody tbody tr {
                width: calc((100% / 4) - 20px);
            }
        }


        /* ANIMATION */
        .blink-animation {
            animation: blink-animation .5s steps(5, start) infinite;
            -webkit-animation: blink-animation .8s steps(5, start) infinite;
        }
        @keyframes blink-animation {
            to {
                visibility: hidden;
            }
        }
        @-webkit-keyframes blink-animation {
            to {
                visibility: hidden;
            }
        }
    </style>

</head>
