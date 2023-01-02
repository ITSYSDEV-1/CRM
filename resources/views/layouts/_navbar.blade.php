<div class="top_nav">
    <div class="nav_menu">
        <nav>
            <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>
            <div class="navbar navbar-brand navbar-dark">
                <h1>{{ $configuration->hotel_name }}</h1>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li class="">
                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user() ? Auth::user()->name:'' }}
                        <span class=" fa fa-angle-down"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-usermenu pull-right">
                        <li><a href="javascript:;"> Profile</a></li>
                        <li><a href="{{ url('preferences') }}"> Preferences</a></li>
                        <li><a href="#" onclick="$('#logout').submit()"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                    </ul>
                    <form id="logout" method="POST" action="{{ route('logout') }}">
                        @csrf
                    </form>
                </li>


            </ul>
        </nav>
    </div>
</div>