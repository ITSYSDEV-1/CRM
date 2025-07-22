<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
        <h3>Menu</h3>
        <ul class="nav side-menu">
            @can('2.1.1_view_dashboard')
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>Dashboard</a></li>
            @endcan
            <li><a><i class="fa fa-user"></i> Contact Management <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu" style="display: none;">
                    @can('3.1.1_view_contacts_list')
                    <li><a href="{{ url('contacts/list') }}">View Contacts</a></li>
                    @endcan
                    @can('3.3.1_save_filter_contacts')
                    <li><a href="{{ url('contacts/filter') }}">Filter Contact</a></li>
                    @endcan
                    @can('3.4.1_view_detail_incomplete')
                    <li><a href="{{ url('contacts/incomplete') }}">Incomplete Contact</a></li>
                    @endcan
                    @can('3.5.1_download_template')
                    <li><a href="{{url('contacts/external') }}">External Contacts</a></li>
                    @endcan
                    @can('3.6.1_add_exclude_email')
                    <li><a href="{{ url('contacts/excluded/email') }}">Exclude Email / Domain </a></li>
                    @endcan
                </ul>
            </li>
            <li><a><i class="fa fa-envelope"></i> Email Marketing <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    @can('4.1.1_add_new_campaign')
                        <li><a href="{{url('campaigns') }}">Campaign Management</a></li>
                    @endcan    
                    @can('4.2.1_add_new_segment')
                        <li><a href="{{url('segments') }}">Segment Management</a></li>
                    @endcan
                        
                    @can('5.1.1_view_email_pre_stay')
                        <li><a href="{{url('email/config/prestay')}}">Pre-Stay Configuration</a></li>
                    @endcan
                    @can('5.2.1_view_email_post_stay')
                        <li><a href="{{url('email/config/poststay')}}">Post-Stay Configuration</a></li>
                    @endcan
                    @can('5.3.1_view_email_birthday')
                        <li><a href="{{url('email/config/birthday')}}">Birthday Configuration</a></li>
                    @endcan
                    @can('5.4.1_view_email_miss_you')
                        <li><a href="{{url('email/config/miss')}}">We Miss You Configuration</a></li>
                    @endcan
                    @can('6.1.7_view_list_template')
                        <li><a href="{{url('email/template')}}">Email Template</a></li>
                    @endcan
                    @can('7.1.1_view_delivery_status')
                        <li><a href="{{url('email/delivery/status')}}">Email Delivery Status</a></li>
                    @endcan
                </ul>
            </li>
            <li><a><i class="fa fa-envelope"></i> Pre Stay <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    @can('8.1.1_view_prestay_contact_list')
                        <li><a href="{{url('reservation')}}">Folio</a></li>
                    @endcan
                    @can('8.2.5_view_pre_stay_promo_list')
                        <li><a href="{{route('promo-configuration.index')}}">Pre-Stay Promo Configuration</a></li>
                    @endcan
                </ul>
            </li>
            <li><a><i class="fa fa-cogs"></i> System Management <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    @can('1.1.3_manage_roles')
                        <li><a href="{{route('role-permissions.index')}}">User Role Permission</a></li>
                    @endcan
                    @can('1.1.1_view_preferences')
                    <li><a href="{{url('preferences')}}">System Preferences</a></li>
                    @endcan
                    <li><a href="{{route('logs.index')}}">System & User Logs</a></li>
                </ul>
            </li>

            {{-- Alternative: Separate Logs section (uncomment if needed) --}}
            {{-- <li><a><i class="fa fa-file-text-o"></i> System Logs <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="{{route('logs.index')}}">View Logs</a></li>
                </ul>
            </li> --}}

        </ul>
    </div>
    

</div>
