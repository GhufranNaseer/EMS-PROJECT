<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar js-menu-active">
        <!-- Sidebar user panel -->
        <div class="user-panel text-center">
            <img src="<?= base_url('../' . $this->event->event_logo)?>" class="img-thumbnail" alt="" width="50%">
        </div>


        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header">Menu</li>
            <li data-page="dashboard">
                <a href="<?= base_url('dashboard'); ?>">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                    <small class="label pull-right bg-green"></small>
                </a>
            </li>
            <li data-page="profile">
                <a href="<?= base_url('my-profile'); ?>">
                    <i class="fa fa-user"></i>
                    <span>Profile</span>
                </a>
            </li>



            <li class="treeview">
                <a href="javascript:void(0)">
                    <i class="fa fa-folder"></i>
                    <span>Networking</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li data-page="search-product-profile">
                        <a href="<?= base_url('product-profile-list.html'); ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>Schedule with Exhibitors</span>
                        </a>
                    </li>
                    <!-- <li data-page="meeting-exhibitors">
                        <a href="<?= base_url('exhibitors-list.html') ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>Schedule with Exhibitors</span>
                        </a>
                    </li> -->

                    <li data-page="meeting-officer-chief_of_servicing">
                        <a href="<?= base_url('officer-list.html?type=chief_of_servicing') ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>Schedule with Chief of Servicing</span>
                        </a>
                    </li>
                    <li data-page="meeting-officer-local_delegates">
                        <a href="<?= base_url('officer-list.html?type=local_delegates') ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>Schedule with Local Delegates</span>
                        </a>
                    </li>
                    <li data-page="meeting-officer-foreign_delegates">
                        <a href="<?= base_url('officer-list.html?type=foreign_delegates') ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>Schedule with Foreign Delegates</span>
                        </a>
                    </li>
                    <li data-page="meeting-officer-armed_force">
                        <a href="<?= base_url('officer-list.html?type=armed_force') ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>Schedule with Armed Force</span>
                        </a>
                    </li>
                    <li data-page="meeting-officer-government_officials">
                        <a href="<?= base_url('officer-list.html?type=government_officials') ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>Schedule with Government official</span>
                        </a>
                    </li>
                    <li data-page="meeting-officer-organizer">
                        <a href="<?= base_url('officer-list.html?type=organizer') ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>Schedule with Organizer</span>
                        </a>
                    </li>

                    <li data-page="my_meeting">
						<?php

						$my_meeting_requests = $this->db
							->where('exhibition_id', $this->event->id)
							->where('is_deleted', 0)
							->where('is_canceled', 0)
							->where('is_approved', 0)
							->where('appointment_to', $this->userdata->id)
							->where('user_type_to', 'officer')
							->count_all_results('my_appointments_datatable');

						?>
                        <a href="<?= base_url('my_meeting.html') ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>My Meetings <span class="label label-success pull-right"><?= $my_meeting_requests ?></span></span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>


