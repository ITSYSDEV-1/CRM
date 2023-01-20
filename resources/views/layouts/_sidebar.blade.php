<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
        <h3>Menu</h3>
        <ul class="nav side-menu">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>Dashboard</a></li>
            <li><a><i class="fa fa-user"></i> Contact Management <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="{{ url('contacts/list') }}">View Contacts</a></li>
                    <li><a href="{{ url('contacts/filter') }}">Filter Contact</a></li>
                    <li><a href="{{ url('contacts/incomplete') }}">Incomplete Contact</a></li>
                    <li><a href="{{ url('contacts/excluded/email') }}">Exclude Email / Domain </a></li>
                </ul>
            </li>
            <li><a><i class="fa fa-envelope"></i> Email Marketing <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                        <li><a href="{{url('campaigns') }}">Campaign Management</a></li>
                        <li><a href="{{url('segments') }}">Segment Management</a></li>
                        <li><a href="{{url('contacts/external') }}">External Contacts</a></li>
                        <li><a href="{{url('email/config/prestay')}}">Prestay-Stay Configuration</a></li>
                        <li><a href="{{url('email/config/poststay')}}">Post-Stay Configuration</a></li>
                        <li><a href="{{url('email/config/birthday')}}">Birthday Configuration</a></li>
                        <li><a href="{{url('email/config/miss')}}">We Miss You Configuration</a></li>
                        <li><a href="{{url('email/template')}}">Email Template</a></li>
                        <li><a href="{{url('email/delivery/status')}}">Email Delivery Status</a></li>
                </ul>
            </li>
            <li><a><i class="fa fa-envelope"></i> Pre Stay <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                        <li><a href="{{url('reservation')}}">Folio</a></li>
                </ul>
            </li>
{{--            <li><a><i class="fa fa-envelope"></i> In Stay<span class="fa fa-chevron-down"></span></a>--}}
{{--                <ul class="nav child_menu">--}}
{{--                        <li><a href="{{url('inhouse')}}">Folio</a></li>--}}
{{--                </ul>--}}
{{--            </li>--}}
        </ul>
    </div>
</div>
