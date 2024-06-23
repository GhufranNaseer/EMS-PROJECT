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

			<?php
			$hasCataloguesFormData = $this->db
				->where('exhibition_id', $this->event->id)
				->where('booking_id', $this->booking->id)
				->where('form_id', 3)
				->count_all_results('es_exhibition_booking_forms_data');
			
			if ($hasCataloguesFormData > 0) {
			?>
			<li data-page="my-product-profile">
				<a href="<?= base_url('view-product-profile.html?id=' . urlencode(myid($this->booking->id))); ?>">
					<i class="fa fa-id-card"></i>
					<span>My Product Profile</span>
				</a>
			</li>
			<?php } ?>

            <?php
            $forms = $this->db
                ->select('O.*')
                ->where('EF.exhibition_id', $this->event->id)
                ->where('EF.is_active', 1)
				->join('es_order_forms as O', 'O.id = EF.form_id', 'LEFT')
                ->get('es_exhibition_forms as EF')
                ->result();

            ?>

            <li class="treeview">
                <a href="javascript:void(0)">
                    <i class="fa fa-folder"></i>
                    <span>Essential Forms</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <?php
                    foreach ($forms as $form) {
                       
                        if ($form->is_essential == 1) {
                            $has_data = '<small class="label pull-right bg-red">required</small>';

                            $check = $this->db
								->where('exhibition_id', $this->event->id)
								->where('booking_id', $this->booking->id)
								->where('form_id', $form->id)
								->count_all_results('es_exhibition_booking_forms_data');

							if ($check > 0) {
								$has_data = '<small class="label pull-right bg-green">completed</small>';
                            }

                            if ($form->id == 1) {
							    $check_has_bare_stalls = $this->db
									->where('booking_id', $this->booking->id)
									->where('booking_stall_type', 'bare')
									->count_all_results('es_exhibition_booking_stalls');

							    $check_has_shell_stalls = $this->db
									->where('booking_id', $this->booking->id)
									->where('booking_stall_type', 'shell')
									->count_all_results('es_exhibition_booking_stalls');

								$has_bare_data = '<small class="label pull-right bg-red">required</small>';
								$has_shell_data = '<small class="label pull-right bg-red">required</small>';

								$check_data = $this->db
									->where('exhibition_id', $this->event->id)
									->where('booking_id', $this->booking->id)
									->where('form_id', $form->id)
									->get('es_exhibition_booking_forms_data')
                                    ->row();
                                    
                                
								if ($check_data && array_key_exists('bare_stall_data', json_decode($check_data->form_data, true))) {
									$has_bare_data = '<small class="label pull-right bg-green">completed</small>';
                                }
                                if ($check_data && array_key_exists('shell_stall_data', json_decode($check_data->form_data, true))) {
									$has_shell_data = '<small class="label pull-right bg-green">completed</small>';
                                }

                                if ($check_has_bare_stalls > 0) {
									echo '<li data-page="'.$form->form_view.'_bare" >
                                    <a href="'. base_url('forms/' . $form->form_view . '?tab=bare') .'">
                                        <i class="fa fa-folder"></i>
                                        <span>Stall Builder</span>
                                        '.$has_bare_data.'
                                    </a>
                                </li>';
                                }

                                if ($check_has_shell_stalls > 0) {
									echo '<li data-page="'.$form->form_view.'_shell" >
                                    <a href="'. base_url('forms/' . $form->form_view . '?tab=shell') .'">
                                        <i class="fa fa-folder"></i>
                                        <span>Fascia Name</span>
                                        '.$has_shell_data.'
                                    </a>
                                </li>';
                                }

                            } else {
								echo '<li data-page="'.$form->form_view.'" >
                                    <a href="'. base_url('forms/' . $form->form_view) .'">
                                        <i class="fa fa-folder"></i>
                                        <span>'. $form->form_number .'</span>
                                        '.$has_data.'
                                    </a>
                                </li>';
                            }
                        }
                    }
                    ?>

                </ul>
            </li>
            <li class="treeview">
                <a href="javascript:void(0)">
                    <i class="fa fa-folder"></i>
                    <span>Optional Forms</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
				<?php
					// all forms are not completed thats why we only need to show following forms
					$allow_optional_forms = [
						'form_21' => false,
						'form_17' => false,
						'form_23' => false,
						'form_24' => false,
						'form_10' => false,
						'form_25' => false,
					];
					foreach ($forms as $form) {
						if (array_key_exists($form->form_view, $allow_optional_forms)) {
							$allow_optional_forms[$form->form_view] = true;
						}
					}
				?>
				<?php if ($allow_optional_forms['form_21']) { ?>
                    <li data-page="form_21">
                        <a href="<?= base_url('forms/form_21') ?>">
                            <i class="fa fa-folder"></i>
                            <span>Visa Form</span>
                        </a>
                    </li>
				<?php 
				}
				if ($allow_optional_forms['form_17']) {
				?>
                    <li data-page="form_17">
                        <a href="<?= base_url('forms/form_17') ?>">
                            <i class="fa fa-folder"></i>
                            <span>Vehicle Rental</span>
                        </a>
                    </li>
				<?php 
				}
				if ($allow_optional_forms['form_23']) {
				?>
                    <li data-page="form_23">
                        <a href="<?= base_url('forms/form_23') ?>">
                            <i class="fa fa-folder"></i>
                            <span>Display Vehicle Mobility</span>
                        </a>
                    </li>
				<?php 
				}
				if ($allow_optional_forms['form_24']) {
				?>
                    <li data-page="form_24">
                        <a href="<?= base_url('forms/form_24') ?>">
                            <i class="fa fa-folder"></i>
                            <span>Hotel Reservation</span>
                        </a>
                    </li>
				<?php 
				}
				if ($allow_optional_forms['form_10']) {
				?>
                    <li data-page="form_10">
                        <a href="<?= base_url('forms/form_10') ?>">
                            <i class="fa fa-folder"></i>
                            <span>Visitor Badge</span>
                        </a>
                    </li>
				<?php 
				}
				if ($allow_optional_forms['form_25']) {
				?>
                    <li data-page="form_25">
                        <a href="<?= base_url('forms/form_25') ?>">
                            <i class="fa fa-folder"></i>
                            <span>Branding Orders</span>
                        </a>
                    </li>
				<?php 
				}
				?>
                </ul>
            </li>
            <?php if (false) { ?>
            <li class="treeview">
                <a href="javascript:void(0)">
                    <i class="fa fa-folder"></i>
                    <span>Optional Forms</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <?php
					foreach ($forms as $form) {
						if ($form->is_essential == 0) {
							echo '<li data-page="'.$form->form_view.'">
                                    <a href="'. base_url('forms/' . $form->form_view) .'">
                                        <i class="fa fa-folder"></i>
                                        <span>'. $form->form_number .'</span>
                                    </a>
                                </li>';
						}
					}
                    ?>
                </ul>
            </li>
			<?php } ?>

            <li data-page="feedback-form">
                <a href="#">
                    <i class="fa fa-comment-o"></i>
                    <span>Feedback Form</span>
                </a>
            </li>

                <li class="treeview">
                    <a href="javascript:void(0)">
                        <i class="fa fa-shopping-cart"></i>
                        <span>Additional Items</span>
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        <?php
                        $categories = $this->db
                            ->where('parent_id', null)
                            ->where('for_branding', 0)
                            ->where('is_deleted', 0)
                            //->limit(10)
                            ->get('es_inventory_category')
                            ->result();
                        foreach ($categories as $category) {
                            echo '<li>
                            <a href="'. base_url('shop/category/?cat='. myid($category->id) ) .'" title= "'.$category->category_title.'" style="white-space: nowrap; width: 203px; overflow: hidden; text-overflow: ellipsis;">
                            <i class="fa fa-cart-arrow-down"></i>
                            '.$category->category_title.'  
                           <span></span></a>
                            </li>';
                        }
                        ?>
                    </ul>
                </li>
            <li data-page="search-product-profile">
				<a href="<?= base_url('product-profile-list.html'); ?>">
					<i class="fa fa-search"></i>
					<span>Exhibitors to Exhibitors</span>
				</a>
			</li>
            <li class="treeview">
                <a href="javascript:void(0)">
                    <i class="fa fa-folder"></i>
                    <span>Networking</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <!-- <li data-page="meeting-exhibitors">
                        <a href="<?= base_url('exhibitors-list.html') ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>Exhibitors to Exhibitors</span>
                        </a>
                    </li> -->


                    <li data-page="meeting-officer-local_delegates">
                        <a href="<?= base_url('officer-list.html?type=local_delegates') ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>Exhibitors to Local Delegates</span>
                        </a>
                    </li>
                    <li data-page="meeting-officer-foreign_delegates">
                        <a href="<?= base_url('officer-list.html?type=foreign_delegates') ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>Exhibitors to Foreign Delegates</span>
                        </a>
                    </li>
                    <li data-page="meeting-officer-armed_force">
                        <a href="<?= base_url('officer-list.html?type=armed_force') ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>Exhibitors to Armed Force</span>
                        </a>
                    </li>
                    <li data-page="meeting-officer-government_officials">
                        <a href="<?= base_url('officer-list.html?type=government_officials') ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>Exhibitors to Government official</span>
                        </a>
                    </li>
                    <li data-page="meeting-officer-organizer">
                        <a href="<?= base_url('officer-list.html?type=organizer') ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>Exhibitors to Organizer</span>
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
                            ->where('user_type_to', 'exhibitor')
                            ->count_all_results('my_appointments_datatable');

                        ?>
                        <a href="<?= base_url('my_meeting.html') ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>My Meetings <span class="label label-success pull-right"><?= $my_meeting_requests ?></span></span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="javascript:void(0)">
                    <i class="fa fa-folder"></i>
                    <span>MOU/contract signing</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li data-page="mou-exhibitors">
                        <a href="<?= base_url('exhibitors-list-mou.html') ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>To Exhibitors</span>
                        </a>
                    </li>

                    <li data-page="my_mou_sign">
                        <a href="<?= base_url('mou_sign.html') ?>">
                            <i class="fa fa-clock-o"></i>
                            <span>My MoU's Schedules</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- <li class="treeview">
                <a href="javascript:void(0)">
                    <i class="fa fa-folder"></i>
                    <span>Exhibitor Team</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li data-page="organizer-list">
                        <a href="<?= base_url('organizer-list.html') ?>">
                            <i class="fa fa-users"></i>
                            <span>Organizers</span>
                        </a>
                    </li>
                    <li data-page="stall-builder-list">
                        <a href="<?= base_url('stall-builder-list.html') ?>">
                            <i class="fa fa-users"></i>
                            <span>Stall Builder Contractors</span>
                        </a>
                    </li>
					<li data-page="agent-list">
                        <a href="<?= base_url('agent-list.html') ?>">
                            <i class="fa fa-users"></i>
                            <span>Agents</span>
                        </a>
                    </li>
                </ul>
            </li> -->
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>


