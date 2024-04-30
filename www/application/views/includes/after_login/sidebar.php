<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar js-menu-active">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= base_url('uploads/profile/') . $this->userdata->user_image ?>" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><?= "{$this->userdata->user_first_name} {$this->userdata->user_last_name}" ?></p>
                <a href="javascript:void(0);"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- search form -->

        <!-- /.search form -->
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

			<?php if (get_instance()->hasRight('usergroup') ||
                get_instance()->hasRight('users') ||
                (get_instance()->hasRight('customers') && $this->userdata->user_group_id != SALES_PERSON) ||
                (get_instance()->hasRight('contactperson') && $this->userdata->user_group_id != SALES_PERSON) ||
				get_instance()->hasRight('agent') ||
				get_instance()->hasRight('organizer') ||
				get_instance()->hasRight('officer') ||
                get_instance()->hasRight('stallbuilder')) { ?>
            <li class="treeview">
                <a href="javascript:void(0)">
                    <i class="fa fa-user"></i>
                    <span>User</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <?php if (get_instance()->hasRight('usergroup')) { ?>
                    <li data-page="user_group">
                        <a href="<?= base_url('user-groups.html'); ?>">
                            <i class="fa fa-users"></i>
                            <span>User Group</span>
                            <small class="label pull-right bg-green"></small>
                        </a>
                    </li>
                    <?php } ?>
					<?php if (get_instance()->hasRight('users')) { ?>
                    <li data-page="users">
                        <a href="<?= base_url('users.html'); ?>">
                            <i class="fa fa-user"></i>
                            <span>Users</span>
                            <small class="label pull-right bg-green"></small>
                        </a>
                    </li>
					<?php } ?>
					<?php if (get_instance()->hasRight('customers') && $this->userdata->user_group_id != SALES_PERSON) { ?>
                    <li data-page="customers">
                        <a href="<?= base_url('customers.html'); ?>">
                            <i class="fa fa-address-card"></i>
                            <span>Customers</span>
                            <small class="label pull-right bg-green"></small>
                        </a>
                    </li>
					<?php } ?>
					<?php if (get_instance()->hasRight('contactperson') && $this->userdata->user_group_id != SALES_PERSON) { ?>
                        <li data-page="contact_person">
                            <a href="<?= base_url('contact_person.html'); ?>">
                                <i class="fa fa-sitemap"></i>
                                <span>Contact person</span>
                                <small class="label pull-right bg-green"></small>
                            </a>
                        </li>
					<?php } ?>
                    <?php if (get_instance()->hasRight('agent')) { ?>
                        <li data-page="agent">
                            <a href="<?= base_url('agent.html'); ?>">
                                <i class="fa fa-bookmark"></i>
                                <span>Agent</span>
                                <small class="label pull-right bg-green"></small>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if (get_instance()->hasRight('organizer')) { ?>
                        <li data-page="organizer">
                            <a href="<?= base_url('organizer.html'); ?>">
                                <i class="fa fa-sitemap"></i>
                                <span>Organizer</span>
                                <small class="label pull-right bg-green"></small>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if (get_instance()->hasRight('officer')) { ?>
                        <li data-page="officer">
                            <a href="<?= base_url('officer_exhibitor.html'); ?>">
                                <i class="fa fa-user-secret"></i>
                                <span>B2B Users</span>
                                <small class="label pull-right bg-green"></small>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if (get_instance()->hasRight('stallbuilder')) { ?>
                        <li data-page="stall_builder">
                            <a href="<?= base_url('stall-builder.html'); ?>">
                                <i class="fa fa-sitemap"></i>
                                <span>Stall Builder Contractors</span>
                                <small class="label pull-right bg-green"></small>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </li>
            <?php } ?>


			<?php if (get_instance()->hasRight('inventorycategory') || get_instance()->hasRight('inventoryitem')) { ?>
            <li class="treeview">
                <a href="javascript:void(0)">
                    <i class="fa fa-cube"></i>
                    <span>Inventory</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
					<?php if (get_instance()->hasRight('inventorycategory')) { ?>
                    <li data-page="inventory_category">
                        <a href="<?= base_url('inventory-category.html'); ?>">
                            <i class="fa fa-cube"></i>
                            <span>Inventory Category</span>
                        </a>
                    </li>
                    <?php } ?>
					<?php if (get_instance()->hasRight('inventoryitem')) { ?>
                    <li data-page="inventory_item">
                        <a href="<?= base_url('inventory-item.html'); ?>">
                            <i class="fa fa-cubes"></i>
                            <span>Global Inventory</span>
                        </a>
                    </li>
					<?php } ?>
                </ul>
            </li>
			<?php } ?>


			<?php if (get_instance()->hasRight('demonstrationvenue') || get_instance()->hasRight('locations') ||
				get_instance()->hasRight('forms') ||
                get_instance()->hasRight('settings') ||
				get_instance()->hasRight('discount')) { ?>
            <li class="treeview">
                <a href="javascript:void(0)">
                    <i class="fa fa-cog"></i>
                    <span>Settings</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
					<?php if (get_instance()->hasRight('demonstrationvenue')) { ?>
                    <li data-page="demonstration_venue">
                        <a href="<?= base_url('demonstration-venue.html'); ?>">
                            <i class="fa fa-map-marker"></i>
                            <span>Demonstration Venue</span>
                        </a>
                    </li>
					<?php } ?>
					<?php if (get_instance()->hasRight('locations')) { ?>
                    <li data-page="locations">
                        <a href="<?= base_url('locations.html'); ?>">
                            <i class="fa fa-map-marker"></i>
                            <span>Locations</span>
                        </a>
                    </li>
					<?php } ?>

                    <?php if (get_instance()->hasRight('forms')) { ?>
                        <li data-page="forms">
                            <a href="<?= base_url('forms.html'); ?>">
                                <i class="fa fa-print"></i>
                                <span>Forms</span>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if (get_instance()->hasRight('settings')) { ?>
                        <li data-page="other_settings">
                            <a href="<?= base_url('other-settings.html'); ?>">
                                <i class="fa fa-cogs"></i>
                                <span>Other Settings</span>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if (get_instance()->hasRight('discount')) { ?>
                        <li data-page="discount">
                            <a href="<?= base_url('discount.html'); ?>">
                                <i class="fa fa-sort-numeric-desc"></i>
                                <span>Discount</span>
                            </a>
                        </li>
                    <?php } ?>

                </ul>
            </li>
			<?php } ?>

			<?php if (get_instance()->hasRight('settings')) { ?>
            <li class="treeview">
                <a href="javascript:void(0)">
                    <i class="fa fa-cog"></i>
                    <span>Accounts</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
					<?php if (get_instance()->hasRight('settings')) { ?>
                        <li data-page="tax_update">
                            <a href="<?= base_url('tax-update.html'); ?>">
                                <i class="fa fa-cogs"></i>
                                <span>Tax</span>
                            </a>
                        </li>
					<?php } ?>
                </ul>
            </li>
			<?php } ?>


			<?php if (get_instance()->hasRight('exhibitions') ||
                get_instance()->hasRight('stalls') ||
                get_instance()->hasRight('tradevisitor') ||
                get_instance()->hasRight('notification') ||
				get_instance()->hasRight('eventinventory') ||
				get_instance()->hasRight('packages') ||
				get_instance()->hasRight('eventform') ||
				get_instance()->hasRight('advertisment')) { ?>
            <li class="treeview">
                <a href="javascript:void(0)">
                    <i class="fa fa-diamond"></i>
                    <span>Events</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
					<?php if (get_instance()->hasRight('exhibitions')) { ?>
                    <li data-page="exhibitions">
                        <a href="<?= base_url('exhibitions.html'); ?>">
                            <i class="fa fa-diamond"></i>
                            <span>Events</span>
                        </a>
                    </li>
					<?php } ?>
					<?php if (get_instance()->hasRight('stalls')) { ?>
                    <li data-page="stalls">
                        <a href="<?= base_url('stalls.html'); ?>">
                            <i class="fa fa-archive"></i>
                            <span>Stalls</span>
                        </a>
                    </li>
					<?php } ?>

                    <?php if (get_instance()->hasRight('tradevisitor')) { ?>
                    <li data-page="trade_visitor">
                        <a href="<?= base_url('trade-visitor.html'); ?>">
                            <i class="fa fa-archive"></i>
                            <span>Trade Visitors</span>
                        </a>
                    </li>
					<?php } ?>
                    <?php if (get_instance()->hasRight('notification')) { ?>
                        <li data-page="event_notification">
                            <a href="<?= base_url('notification.html'); ?>">
                                <i class="fa fa-commenting-o"></i>
                                <span>Notifications</span>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if (get_instance()->hasRight('eventinventory')) { ?>
                        <li data-page="event_inventory">
                            <a href="<?= base_url('event-inventory.html'); ?>">
                                <i class="fa fa-cubes"></i>
                                <span>Event Inventory</span>
                            </a>
                        </li>
                    <?php } ?>
					<?php if (get_instance()->hasRight('packages')) { ?>
                        <li data-page="packages">
                            <a href="<?= base_url('packages-event.html'); ?>">
                                <i class="fa fa-magic"></i>
                                <span>Event Packages</span>
                            </a>
                        </li>
					<?php } ?>
                    <?php if (get_instance()->hasRight('eventform')) { ?>
                        <li data-page="event_form">
                            <a href="<?= base_url('event_form.html'); ?>">
                                <i class="fa fa-file-text-o"></i>
                                <span>Event Forms</span>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if (get_instance()->hasRight('advertisment')) { ?>
                        <li data-page="event_advertisment">
                            <a href="<?= base_url('advertisment.html'); ?>">
                                <i class="fa fa-television"></i>
                                <span>Advertisement</span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </li>
			<?php } ?>

			
			<?php if (get_instance()->hasRight('bookstall')) { ?>
            <li data-page="book_stall">
                <a href="<?= base_url('book-stall-exhibitions.html'); ?>">
                    <i class="fa fa-credit-card"></i>
                    <span>Order Form</span>
                </a>
            </li>
			<?php } ?>

			
            <?php if (get_instance()->hasRight('orderlist')) { ?>
                <li data-page="order_list">
                    <a href="<?= base_url('order_list.html'); ?>">
                        <i class="fa fa-shopping-cart"></i>
                        <span>Order List</span>
                    </a>
                </li>
            <?php } ?>
            <?php if (get_instance()->hasRight('update_order_badges')) { ?>
                <li data-page="update_order_badges">
                    <a href="<?= base_url('update-order-badges.html'); ?>">
                        <i class="fa fa-shopping-cart"></i>
                        <span>Update Order Badges</span>
                    </a>
                </li>
            <?php } ?>


		<?php if (get_instance()->hasRight('enhancedlist')) { ?>
			 <li data-page="enhanced">
                <a href="<?= base_url('enhanced.html'); ?>">
                    <i class="fa fa-shopping-cart"></i>
                    <span> Enhanced List</span>
                </a>
            </li>
		<?php } ?>
        <?php if (get_instance()->hasRight('printjob')) { ?>
            <li data-page="print_job">
                <a href="<?= base_url('print-job.html'); ?>">
                    <i class="fa fa-print"></i>
                    <span>Print jobs</span>
                </a>
            </li>
        <?php } ?>

            <li data-page="email_template">
                <a href="<?= base_url('email_template.html'); ?>">
                    <i class="fa fa-print"></i>
                    <span>Email Template</span>
                </a>
            </li>

            <?php if (get_instance()->hasRight('smsnotification')) { ?>
                <li class="treeview">
                    <a href="javascript:void(0)">
                        <i class="fa fa-commenting-o"></i>
                        <span>Sms Notification's</span>
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        <li data-page="sms_send_to_companies">
                            <a href="<?= base_url('sms-send-to-companies'); ?>">
                                <i class="fa fa-comments"></i>
                                <span>Sms Send To companies</span>
                            </a>
                        </li>
                        <li data-page="sms_individual_send">
                            <a href="<?= base_url('sms-individual-send'); ?>">
                                <i class="fa fa-comments"></i>
                                <span>Individual Send Sms</span>
                            </a>
                        </li>
                    </ul>
                </li>
            <?php } ?>

            <?php if (get_instance()->hasRight('Exhibitorsreport') ||
                get_instance()->hasRight('Statusreport') ||
				get_instance()->hasRight('Badgesreport') ||
				get_instance()->hasRight('Stallbuilders') ||
				get_instance()->hasRight('Fasciareport') ||
				get_instance()->hasRight('Endusercertificatereport') ||
				get_instance()->hasRight('Visatopakistanreport') ||
				get_instance()->hasRight('Hotelreservationreport') ||
				get_instance()->hasRight('Vehiclerentreport') ||
				get_instance()->hasRight('Displaymobilityreport') ||
                get_instance()->hasRight('Meetingreport')
            ) { ?>
                <li class="treeview">
                    <a href="javascript:void(0)">
                        <i class="fa fa-hand-paper-o"></i>
                        <span>Forms Status And Reports</span>
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if (get_instance()->hasRight('Exhibitorsreport')) { ?>
                            <li data-page="exhibitor_list_report">
                                <a href="<?= base_url('form_status.html'); ?>">
                                    <i class="fa fa-file-text-o"></i>
                                    <span>Exhibitors List</span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if (get_instance()->hasRight('Statusreport')) { ?>
                            <li data-page="form_status_report">
                                <a href="<?= base_url('form_status_2.html'); ?>">
                                    <i class="fa fa-file-text-o"></i>
                                    <span>Forms Status</span>
                                </a>
                            </li>
                        <?php } ?>

                        <?php if (get_instance()->hasRight('Badgesreport')) { ?>
                            <li data-page="badges_report">
                                <a href="<?= base_url('badges-report-events.html'); ?>">
                                    <i class="fa fa-file-text-o"></i>
                                    <span>Badges Report</span>
                                </a>
                            </li>
                        <?php } ?>


						<?php if (get_instance()->hasRight('Stallbuilders')) { ?>
                            <li data-page="stall_builders_report">
                                <a href="<?= base_url('stall_builders.html'); ?>">
                                    <i class="fa fa-file-text-o"></i>
                                    <span>Stall Builders Report</span>
                                </a>
                            </li>
						<?php } ?>

						<?php if (get_instance()->hasRight('Fasciareport')) { ?>
                            <li data-page="fascia_report">
                                <a href="<?= base_url('fascia.html'); ?>">
                                    <i class="fa fa-file-text-o"></i>
                                    <span>Fascia Report</span>
                                </a>
                            </li>
						<?php } ?>

						<?php if (get_instance()->hasRight('Endusercertificatereport')) { ?>
                            <li data-page="end_user_certificate_report">
                                <a href="<?= base_url('end_user_certificate.html'); ?>">
                                    <i class="fa fa-file-text-o"></i>
                                    <span>End User Certificate Report</span>
                                </a>
                            </li>
						<?php } ?>

						<?php if (get_instance()->hasRight('Visatopakistanreport')) { ?>
                            <li data-page="visa_to_pakistan_report">
                                <a href="<?= base_url('visa_to_pakistan.html'); ?>">
                                    <i class="fa fa-file-text-o"></i>
                                    <span>Visa To Pakistan Report</span>
                                </a>
                            </li>
						<?php } ?>


						<?php if (get_instance()->hasRight('Hotelreservationreport')) { ?>
                            <li data-page="hotel_reservation_report">
                                <a href="<?= base_url('hotel_reservation.html'); ?>">
                                    <i class="fa fa-file-text-o"></i>
                                    <span>Hotel Reservation Report</span>
                                </a>
                            </li>
						<?php } ?>

						<?php if (get_instance()->hasRight('Vehiclerentreport')) { ?>
                            <li data-page="vehicle_rent_report">
                                <a href="<?= base_url('vehicle_rent.html'); ?>">
                                    <i class="fa fa-file-text-o"></i>
                                    <span>Vehicle Rent Report</span>
                                </a>
                            </li>
						<?php } ?>

						<?php if (get_instance()->hasRight('Displaymobilityreport')) { ?>
                            <li data-page="display_mobility_report">
                                <a href="<?= base_url('display_mobility.html'); ?>">
                                    <i class="fa fa-file-text-o"></i>
                                    <span>Display Mobility Report</span>
                                </a>
                            </li>
						<?php } ?>

                        <?php if (get_instance()->hasRight('Meetingreport')) { ?>
                            <li data-page="meeting_report">
                                <a href="<?= base_url('meeting-report.html'); ?>">
                                    <i class="fa fa-file-text-o"></i>
                                    <span>Meeting Report</span>
                                </a>
                            </li>
                        <?php } ?>

                    </ul>
                </li>
            <?php } ?>

			<?php if (get_instance()->hasRight('Ecommercereport')) { ?>
            <li class="treeview">
                <a href="javascript:void(0)">
                    <i class="fa fa-hand-paper-o"></i>
                    <span>Additional Items Reports</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
					<?php
					$categories = $this->db
						->where('parent_id', null)
						->where('is_deleted', 0)
						->get('es_inventory_category')
						->result();
					foreach ($categories as $category) {
					    echo '<li data-page="ecommerce_report_'.$category->id.'">
                            <a href="' . base_url('ecommerce.html?category_id=' . $category->id) . '" title= "'.$category->category_title.'">
                                <i class="fa fa-file-text-o"></i>
                                <span>'. substr($category->category_title, 0, 25) . ((strlen($category->category_title) > 25) ? '...' : '') . '<span>
                            </a>
                        </li>';
                    }
                    ?>
                </ul>
            </li>
			<?php } ?>

			<?php if (get_instance()->hasRight('Showcataloguereport') || get_instance()->hasRight('brandingreport')) { ?>
            <li class="treeview">
                <a href="javascript:void(0)">
                    <i class="fa fa-hand-paper-o"></i>
                    <span>Creative Department</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li data-page="show_catalogue">
                        <a href="<?= base_url('show_catalogue-report.html'); ?>">
                            <i class="fa fa-file-text-o"></i>
                            <span>Show Catalogue Status</span>
                        </a>
                    </li>

                    <?php if (get_instance()->hasRight('brandingreport')) { ?>
                        <li data-page="branding_report">
                            <a href="<?= base_url('branding.html'); ?>">
                                <i class="fa fa-file-text-o"></i>
                                <span>Branding report</span>
                            </a>
                        </li>
                    <?php } ?>

                </ul>
            </li>
			<?php } ?>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>


