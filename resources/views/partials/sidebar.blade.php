<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item nav-category">Users List</li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#userList" aria-expanded="false"
                aria-controls="userList">
                <i class="menu-icon mdi mdi-account-circle-outline"></i>
                <span class="menu-title">Users</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="userList">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{ route('admin_user_list') }}"> Users </a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin_logout') }}">
                <i class="menu-icon fa fa-sign-out"></i>
                <span class="menu-title">Logout</span>
            </a>
        </li>
    </ul>
</nav>
