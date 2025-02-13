 <!-- Menu -->
 <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
         <a href="#" class="app-brand-link justify-content-center mt-1">
             <img src="{{asset('assets/img/main-loog.jpg')}}"  style="height:100px; width:150px; text-center;" alt="">
         </a>

     <div class="menu-divider mt-0"></div>

     <div class="menu-inner-shadow"></div>



     @php
         $menuItems = [
             [
                 'title' => 'Dashboard',
                 'icon' => 'bx bx-layout',
                 'route' => 'dashboard',
                 'roles' => ['super_admin', 'admin', 'user'], // All roles can see
             ],
             [
                 'title' => 'Manage Serial',
                 'icon' => 'bx bx-layout',
                 'route' => 'manage-serial-number.index',
                 'roles' => ['super_admin', 'admin'], // Restricted to super_admin & admin
             ],
             [
                 'title' => 'Users',
                 'icon' => 'bx bx-layout',
                 'route' => 'users.index',
                 'roles' => ['super_admin', 'admin'], // Restricted to super_admin & admin
             ],
             [
                 'title' => 'Bulk Labels',
                 'icon' => 'bx bx-layout',
                 'route' => 'labels.index',
                 'roles' => ['super_admin', 'admin', 'user'], // All roles can see
             ],
             [
                 'title' => 'History',
                 'icon' => 'bx bx-layout',
                 'route' => 'labels-history',
                 'roles' => ['super_admin', 'admin', 'user'], // All roles can see
             ],
         ];
     @endphp

     <ul class="menu-inner py-1">
         @foreach ($menuItems as $menuItem)
             @php
                 // Check if user has one of the required roles
                 $user = auth()->user();
                 $hasAccess = $user && isset($menuItem['roles']) && $user->hasAnyRole($menuItem['roles']);

                 if (!$hasAccess) {
                     continue; // Skip rendering this menu item
                 }

                 // Check if the current route is active
                 $isActive = isset($menuItem['route']) && request()->routeIs($menuItem['route']);
             @endphp

             <li class="menu-item {{ $isActive ? 'active' : '' }}">
                 <a href="{{ route($menuItem['route']) }}" class="menu-link">
                     <i class="menu-icon tf-icons {{ $menuItem['icon'] }}"></i>
                     <div>{{ $menuItem['title'] }}</div>
                 </a>
             </li>
         @endforeach
     </ul>

 </aside>
 <!-- / Menu -->
