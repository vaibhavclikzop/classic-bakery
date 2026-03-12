 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
     @stack('title')


     <meta name="csrf-token" content="{{ csrf_token() }}">

     <!-- Favicon -->
     <link rel="shortcut icon" type="image/x-icon" href="/logo/{{ $setting->img }}">

     <!-- Bootstrap CSS -->
     <link rel="stylesheet" href="/css/bootstrap.min.css">

     <!-- Datetimepicker CSS -->
     <link rel="stylesheet" href="/css/bootstrap-datetimepicker.min.css">

     <!-- animation CSS -->
     <link rel="stylesheet" href="/css/animate.css">

     <!-- Select2 CSS -->


     {{-- <link rel="stylesheet" href="/css/select2.min.css"> --}}

     <!-- Fontawesome CSS -->
     <link rel="stylesheet" href="/css/fontawesome.min.css">
     <link rel="stylesheet" href="/css/all.min.css">
     <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
     <!-- Main CSS -->
     <link rel="stylesheet" href="/css/style.css">
     <link rel="stylesheet" href="/dataTables/datatables.min.css">
     <link rel="stylesheet" href="/richtexteditor/rte_theme_default.css" />
     <script type="text/javascript" src="/richtexteditor/rte.js"></script>
     <script type="text/javascript" src='/richtexteditor/plugins/all_plugins.js'></script>

     <script src="https://code.jquery.com/jquery-2.2.4.min.js"
         integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

 </head>

 <body>


     @php
     $masterRoutes = ['unit-type', 'gst', 'outlet', 'outlet-role', 'mode-of-transport'];
     $advanceMasterRoutes = [
     'advance-order-category',
     'advance-order-flavour',
     'advance-order-shape',
     'advance-order-food-type',
     'advance-order-weight',
     'advance-order-items',
     ];
     $customerRoutes = ['customer-type', 'customers'];
     $rawMaterialRoutes = ['brand', 'category', 'sub-category', 'products'];
     $finishGoodsRoutes = [
     'finish-product-category',
     'finish-product-sub-category',
     'order-type',
     'finish-products',
     ];
     $purchaseRawRoutes = [
     'generate-po',
     'purchase-order/pending',
     'purchase-order/generated',
     'inward-stock',
     'purchase-order/partial',
     'purchase-order/complete',
     ];
     $purchaseFinishRoutes = [
     'generate-po-finish-goods',
     'purchase-order-finish-goods/pending',
     'purchase-order-finish-goods/generated',
     'inward-stock-finish-goods',
     'purchase-order-finish-goods/partial',
     'purchase-order-finish-goods/complete',
     ];
     $outwardRoutes = ['outward-order', 'outward-order-list'];
     $orderManagementRoutes = [
     'create-order',
     'orders/pending',
     'orders/processing',
     'orders/dispatch',
     'outward-customer-order-list',
     'orders/complete',
     'orders/cancel',
     ];
     $advanceOrderSectionRoutes = [
     'create-advance-order',
     'advance-order-list/pending',
     'advance-order-list/processing',
     'advance-order-list/dispatch',
     'advance-order-list/delivered',
     ];
     $stockReportRoutes = [
     'current-stock',
     'current-stock-finish-products',
     'outlet-current-stock',
     'near-by-minimum-stock',
     'finish-goods-defective-stock',
     ];
     $reportsRoutes = [
     'purchase-variation-report',
     'purchase-register-report',
     'sale-register-report',
     'category-subcategory-report',
     'customer-wise-report',
     'tally-report',
     ];
     $stockAuditRoutes = [
     'audit-setting',
     'audit-report',
     'fg-audit-setting',
     'fg-audit-report',
     'outlet-audit-setting',
     'outlet-audit-report',
     ];
     $attendanceRoutes = ['attendance-report', 'attendance-report-monthly'];
     $advanceOrderRoutes = [
     'create-advance-order',
     'advance-order-list/pending',
     'advance-order-list/processing',
     'advance-order-list/dispatch',
     'advance-order-list/delivered',
     'advance-order-list/invoices',
     ];
     $posOrder = ['pos-order', 'kot'];
     $recipeRoute = ['create-recipe', 'recipe-list'];
     @endphp
     <div id="global-loader">
         <div class="whirly-loader"> </div>
     </div>
     <!-- Main Wrapper -->
     <div class="main-wrapper">

         <!-- Header -->
         <div class="header">

             <!-- Logo -->
             <div class="header-left active">
                 <a href="/" class="logo logo-normal">
                     <img src="/logo/{{ $setting->img }}" alt="">
                 </a>
                 <a href="/" class="logo logo-white">
                     <img src="/logo/{{ $setting->img }}" alt="">
                 </a>
                 <a href="/" class="logo-small">
                     <img src="/logo/{{ $setting->img }}" alt="">
                 </a>
                 <a id="toggle_btn" href="javascript:void(0);">
                     <i data-feather="chevrons-left" class="feather-16"></i>
                 </a>
             </div>
             <!-- /Logo -->

             <a id="mobile_btn" class="mobile_btn" href="#sidebar">
                 <span class="bar-icon">
                     <span></span>
                     <span></span>
                     <span></span>
                 </span>
             </a>

             <!-- Header Menu -->
             <ul class="nav user-menu">

                 <!-- Search -->
                 <li class="nav-item nav-searchinputs">
                     <a class="" href="javascript: void(0); " onclick="window.history.back()">

                         Previous Page
                     </a>
                 </li>
                 <!-- /Search -->
                 <li class="nav-item has-arrow main-drop">
                     @if ($attendance && $attendance->start_time && $attendance->end_time)
                     <button type="button" class="btn btn-sm btn-info">
                         You already end your day at {{ $attendance->end_time }}

                     </button>
                     @elseif($attendance && $attendance->start_time && empty($attendance->end_time))
                     <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                         data-bs-target="#endDay">
                         End Day

                     </button>
                     @else
                     <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                         data-bs-target="#startDay">
                         Start Day

                     </button>
                     @endif


                 </li>


                 <li class="nav-item dropdown has-arrow main-drop">
                     <a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
                         <span class="user-info">

                             <i class="fa fa-user-circle" style="font-size: 40px; margin-right: 10px;"></i>
                             <span class="user-detail">
                                 <span class="user-name">{{ session('user')->name }}</span>
                                 <span class="user-role"></span>
                             </span>
                         </span>
                     </a>
                     <div class="dropdown-menu menu-drop-user">
                         <div class="profilename">
                             <div class="profileset">
                                 <!-- <span class="user-img"><img src="/images/avator1.jpg" alt=""> -->
                                 <i class="fa fa-user-circle" style="font-size: 28px;"></i>
                                 <span class="status online"></span></span>
                                 <div class="profilesets">
                                     <h6></h6>
                                     <h5></h5>
                                 </div>
                                 <a href="/profile">Profile</a>
                             </div>
                             <hr class="m-0">

                             <a class="dropdown-item logout pb-0" href="../logout"><img src="/images/log-out.svg"
                                     class="me-2" alt="img">Logout</a>
                         </div>
                     </div>
                 </li>
             </ul>
             <!-- /Header Menu -->

             <!-- Mobile Menu -->
             <div class="dropdown mobile-user-menu">
                 <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                     aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                 <div class="dropdown-menu dropdown-menu-right">
                     <a class="dropdown-item" href="#">My Profile</a>
                     <a class="dropdown-item" href="#">Settings</a>
                     <a class="dropdown-item" href="#">Logout</a>
                 </div>
             </div>
             <!-- /Mobile Menu -->
         </div>
         <!-- /Header -->

         <!-- Sidebar -->
         <div class="sidebar" id="sidebar">
             <div class="sidebar-inner slimscroll">
                 <div id="sidebar-menu" class="sidebar-menu">
                     <ul>

                         <li class="submenu-open {{ Request::is('dashboard') ? 'active' : '' }}">

                             <ul>
                                 <li><a href="/"><i data-feather="home"></i><span>Dashboard</span></a></li>

                             </ul>
                         </li>

                         <li class="submenu-open">

                             <ul>
                                 @if ($rolePermissions->where('permission_id', 1)->where('view', 1)->isNotEmpty())
                                 <li class="{{ Request::is('users') ? 'active' : '' }}"><a href="/users"
                                         class=""><i data-feather="user-plus"></i><span>Users</span></a>
                                 </li>
                                 @endif
                                 @if ($rolePermissions->where('permission_id', 2)->where('view', 1)->isNotEmpty())
                                 <li class="{{ Request::is('department') ? 'active' : '' }}"><a
                                         href="/department"><i data-feather="home"></i><span>Department</span></a>
                                 </li>
                                 @endif


                                 @if ($rolePermissions->where('permission_id', 3)->where('view', 1)->isNotEmpty())
                                 <li class="{{ Request::is('user-role') ? 'active' : '' }}"><a
                                         href="/user-role"><i data-feather="user-plus"></i><span>User Role
                                         </span></a>
                                 </li>
                                 @endif


                             </ul>

                         </li>


                         <li class="submenu-open">
                             {{-- <h6 class="submenu-hdr">Master</h6> --}}
                             <ul>
                                 @if ($rolePermissions->where('permission_id', 4)->where('view', 1)->isNotEmpty())
                                 <li class="submenu">
                                     <a href="javascript:void(0);"
                                         class="{{ in_array(Request::path(), $masterRoutes) ? 'subdrop active' : '' }}">
                                         <i data-feather="layers"></i><span>Masters</span><span
                                             class="menu-arrow"></span>
                                     </a>
                                     <ul
                                         style="{{ in_array(Request::path(), $masterRoutes) ? 'display: block;' : '' }}">
                                         <li><a href="/unit-type"
                                                 class="{{ Request::is('unit-type') ? 'active' : '' }}">Unit
                                                 Type</a>
                                         </li>
                                         <li><a href="/gst"
                                                 class="{{ Request::is('gst') ? 'active' : '' }}">GST</a></li>
                                         <li><a href="/outlet"
                                                 class="{{ Request::is('outlet') ? 'active' : '' }}">Outlet</a>
                                         </li>
                                         <li><a href="/outlet-role"
                                                 class="{{ Request::is('outlet-role') ? 'active' : '' }}">Outlet
                                                 Role</a></li>
                                         <li><a href="/mode-of-transport"
                                                 class="{{ Request::is('mode-of-transport') ? 'active' : '' }}">Mode
                                                 of Transport</a></li>
                                     </ul>
                                 </li>
                                 @endif

                                 @if ($rolePermissions->where('permission_id', 5)->where('view', 1)->isNotEmpty())
                                 <li class="submenu">
                                     <a href="javascript:void(0);"
                                         class="{{ in_array(Request::path(), $advanceMasterRoutes) ? 'subdrop active' : '' }}">
                                         <i class="fa-solid fa-cake-candles"></i> &nbsp; &nbsp;
                                         <span>Advance Order Master</span>
                                         <span class="menu-arrow"></span>
                                     </a>
                                     <ul
                                         style="{{ in_array(Request::path(), $advanceMasterRoutes) ? 'display: block;' : '' }}">

                                         <li><a href="/advance-order-flavour"
                                                 class="{{ Request::is('advance-order-flavour') ? 'active' : '' }}">Flavour</a>
                                         </li>
                                         <li><a href="/advance-order-shape"
                                                 class="{{ Request::is('advance-order-shape') ? 'active' : '' }}">Shape</a>
                                         </li>
                                         <li class="d-none"><a href="/advance-order-food-type"
                                                 class="{{ Request::is('advance-order-food-type') ? '' : '' }}">Food
                                                 Type</a></li>
                                         <li><a href="/advance-order-weight"
                                                 class="{{ Request::is('advance-order-weight') ? 'active' : '' }}">Weight</a>
                                         </li>
                                         <li><a href="/advance-order-items"
                                                 class="{{ Request::is('advance-order-items') ? 'active' : '' }}">Items</a>
                                         </li>
                                     </ul>
                                 </li>
                                 @endif

                                 @if ($rolePermissions->where('permission_id', 6)->where('view', 1)->isNotEmpty())
                                 <li class="{{ Request::is('vendor') ? 'active' : '' }}"><a href="/vendor">
                                         <i class="fa fa-users" aria-hidden="true"></i> &nbsp; &nbsp; <span>
                                             Vendor
                                         </span></a>
                                 </li>
                                 <li class="{{ Request::is('outlet_list') ? 'active' : '' }}"><a
                                         href="/outlet_customer">
                                         <i class="fa fa-users" aria-hidden="true"></i> &nbsp; &nbsp; <span>
                                             Outlet Customer
                                         </span></a>
                                 </li>
                                 @endif
                                 @if ($rolePermissions->where('permission_id', 7)->where('view', 1)->isNotEmpty())
                                 <li class="submenu">
                                     <a href="javascript:void(0);"
                                         class="{{ in_array(Request::path(), $customerRoutes) ? 'subdrop active' : '' }}">
                                         <i class="fa fa-user-circle" aria-hidden="true"></i> &nbsp; &nbsp;
                                         <span>Customer Master</span><span class="menu-arrow"></span>
                                     </a>
                                     <ul>
                                         <li><a href="/customer-type"
                                                 class="{{ Request::is('customer-type') ? 'active' : '' }}">Customer
                                                 Type</a></li>
                                         <li><a href="/customers"
                                                 class="{{ Request::is('customers') ? 'active' : '' }}">Customers</a>
                                         </li>
                                     </ul>
                                 </li>
                                 @endif

                                 @if ($rolePermissions->where('permission_id', 8)->where('view', 1)->isNotEmpty())
                                 <li class="submenu">
                                     <a href="javascript:void(0);"
                                         class="{{ in_array(Request::path(), $rawMaterialRoutes) ? 'subdrop active' : '' }}">
                                         <i class="fa-solid fa-boxes-packing"></i>&nbsp; &nbsp; <span>Raw Material
                                             Master</span><span class="menu-arrow"></span>
                                     </a>
                                     <ul
                                         style="{{ in_array(Request::path(), $rawMaterialRoutes) ? 'display: block;' : '' }}">
                                         <li><a href="/brand"
                                                 class="{{ Request::is('brand') ? 'active' : '' }}">Brand</a></li>
                                         <li><a href="/category"
                                                 class="{{ Request::is('category') ? 'active' : '' }}">Category</a>
                                         </li>
                                         <li><a href="/sub-category"
                                                 class="{{ Request::is('sub-category') ? 'active' : '' }}">Sub
                                                 Category</a></li>
                                         <li><a href="/products"
                                                 class="{{ Request::is('products') ? 'active' : '' }}">Raw
                                                 Material</a></li>
                                     </ul>
                                 </li>
                                 @endif

                                 @if ($rolePermissions->where('permission_id', 9)->where('view', 1)->isNotEmpty())
                                 <li class="submenu">
                                     <a href="javascript:void(0);"
                                         class="{{ in_array(Request::path(), $finishGoodsRoutes) ? 'subdrop active' : '' }}">
                                         <i class="fa-solid fa-shop"></i> &nbsp; &nbsp; <span>Finish Goods
                                             Master</span><span class="menu-arrow"></span>
                                     </a>
                                     <ul
                                         style="{{ in_array(Request::path(), $finishGoodsRoutes) ? 'display: block;' : '' }}">
                                         <li><a href="/finish-product-category"
                                                 class="{{ Request::is('finish-product-category') ? 'active' : '' }}">Category</a>
                                         </li>
                                         <li><a href="/finish-product-sub-category"
                                                 class="{{ Request::is('finish-product-sub-category') ? 'active' : '' }}">Sub
                                                 Category</a></li>
                                         <li><a href="/order-type"
                                                 class="{{ Request::is('order-type') ? 'active' : '' }}">Order
                                                 Type</a></li>
                                         <li><a href="/finish-products"
                                                 class="{{ Request::is('finish-products') ? 'active' : '' }}">Finish
                                                 Products</a></li>
                                     </ul>
                                 </li>
                                 @endif

                                 @if ($rolePermissions->where('permission_id', 10)->where('view', 1)->isNotEmpty())
                                 <li class="submenu">
                                     <a href="javascript:void(0);"
                                         class="{{ in_array(Request::path(), $recipeRoute) ? 'subdrop active' : '' }}">
                                         <i class="fa-solid fa-utensils"></i> &nbsp; &nbsp; <span></span>
                                         Recipe
                                         <span class="menu-arrow"></span>
                                     </a>
                                     <ul
                                         style="{{ in_array(Request::path(), $recipeRoute) ? 'display: block;' : '' }}">
                                         <li><a href="/create-recipe"
                                                 class="{{ Request::is('create-recipe') ? 'active' : '' }}">Create
                                                 Recipe</a>
                                         </li>
                                         <li><a href="/recipe-list"
                                                 class="{{ Request::is('recipe-list') ? 'active' : '' }}">
                                                 Recipe for Production</a>
                                         </li>

                                     </ul>
                                 </li>
                                 @endif

                             </ul>
                         </li>

                         <li class="submenu-open">
                             <ul>

                                 @if ($rolePermissions->where('permission_id', 11)->where('view', 1)->isNotEmpty())
                                 <li
                                     class="submenu {{ in_array(Request::path(), $purchaseRawRoutes) ? 'submenu-open' : '' }}">
                                     <a href="javascript:void(0);"
                                         class="{{ in_array(Request::path(), $purchaseRawRoutes) ? 'subdrop active' : '' }}">
                                         <i data-feather="shopping-cart"></i><span>Purchase Management</span><span
                                             class="menu-arrow"></span>
                                     </a>
                                     <ul
                                         style="{{ in_array(Request::path(), $purchaseRawRoutes) ? 'display: block;' : '' }}">
                                         <li><a href="/generate-po"
                                                 class="{{ Request::is('generate-po') ? 'active' : '' }}">Generate
                                                 PO</a></li>
                                         <li><a href="/purchase-order/pending"
                                                 class="{{ Request::is('purchase-order/pending') ? 'active' : '' }}">Waiting
                                                 for Approval</a></li>
                                         <li><a href="/purchase-order/generated"
                                                 class="{{ Request::is('purchase-order/generated') ? 'active' : '' }}">Generated
                                                 PO</a></li>
                                         <li><a href="/inward-stock"
                                                 class="{{ Request::is('inward-stock') ? 'active' : '' }}">Inward
                                                 Stock</a></li>
                                         <li class="d-none"><a href="/purchase-order/partial"
                                                 class="{{ Request::is('purchase-order/partial') ? 'active' : '' }}">Partial
                                                 Approved</a></li>
                                         <li class="d-none"><a href="/purchase-order/complete"
                                                 class="{{ Request::is('purchase-order/complete') ? 'active' : '' }}">Full
                                                 Approved</a></li>
                                     </ul>
                                 </li>
                                 @endif

                                 {{-- <li
                                     class="submenu {{ in_array(Request::path(), $purchaseFinishRoutes) ? 'submenu-open' : '' }}">
                                 <a href="javascript:void(0);"
                                     class="{{ in_array(Request::path(), $purchaseFinishRoutes) ? 'subdrop active' : '' }}">
                                     <i class="fa fa-cart-plus" aria-hidden="true"></i> &nbsp; &nbsp;
                                     <span>Purchase Finish Goods</span><span class="menu-arrow"></span>
                                 </a>
                                 <ul
                                     style="{{ in_array(Request::path(), $purchaseFinishRoutes) ? 'display: block;' : '' }}">
                                     <li><a href="/generate-po-finish-goods"
                                             class="{{ Request::is('generate-po-finish-goods') ? 'active' : '' }}">Generate
                                             PO</a></li>
                                     <li><a href="/purchase-order-finish-goods/pending"
                                             class="{{ Request::is('purchase-order-finish-goods/pending') ? 'active' : '' }}">Waiting
                                             for Approval</a></li>
                                     <li><a href="/purchase-order-finish-goods/generated"
                                             class="{{ Request::is('purchase-order-finish-goods/generated') ? 'active' : '' }}">Generated
                                             PO</a></li>
                                     <li><a href="/inward-stock-finish-goods"
                                             class="{{ Request::is('inward-stock-finish-goods') ? 'active' : '' }}">Inward
                                             Stock</a></li>
                                     <li><a href="/purchase-order-finish-goods/partial"
                                             class="{{ Request::is('purchase-order-finish-goods/partial') ? 'active' : '' }}">Partial
                                             Approved</a></li>
                                     <li><a href="/purchase-order-finish-goods/complete"
                                             class="{{ Request::is('purchase-order-finish-goods/complete') ? 'active' : '' }}">Full
                                             Approved</a></li>
                                 </ul>
                         </li> --}}
                         @if ($rolePermissions->where('permission_id', 12)->where('view', 1)->isNotEmpty())
                         <li class="{{ Request::is('inward-report') ? 'active' : '' }}">
                             <a href="/inward-report">
                                 <i class="fa fa-download" aria-hidden="true"></i> &nbsp; &nbsp;
                                 <span>Inward
                                     Challans</span>
                             </a>
                         </li>
                         @endif
                         {{--
                                 <li class="{{ Request::is('inward-challan-finish-goods') ? 'active' : '' }}">
                         <a href="/inward-challan-finish-goods">
                             <i class="fa fa-download" aria-hidden="true"></i> &nbsp; &nbsp; <span>Inward
                                 Challans FG</span>
                         </a>
                         </li> --}}
                         @if ($rolePermissions->where('permission_id', 13)->where('view', 1)->isNotEmpty())
                         <li class="{{ Request::is('purchase-return') ? 'active' : '' }}">
                             <a href="/purchase-return">
                                 <i data-feather="corner-down-left"></i> <span>Purchase Return</span>
                             </a>
                         </li>
                         @endif
                         @if ($rolePermissions->where('permission_id', 14)->where('view', 1)->isNotEmpty())
                         <li
                             class="submenu {{ in_array(Request::path(), $outwardRoutes) ? 'submenu-open' : '' }}">
                             <a href="javascript:void(0);"
                                 class="{{ in_array(Request::path(), $outwardRoutes) ? 'subdrop active' : '' }}">
                                 <i class="fa-solid fa-person-walking-luggage"></i> &nbsp; &nbsp;
                                 <span>Outward
                                     for Production</span><span class="menu-arrow"></span>
                             </a>
                             <ul
                                 style="{{ in_array(Request::path(), $outwardRoutes) ? 'display: block;' : '' }}">
                                 <li><a href="/outward-order"
                                         class="{{ Request::is('outward-order') ? 'active' : '' }}">Create
                                         Outward</a></li>
                                 <li><a href="/outward-order-list"
                                         class="{{ Request::is('outward-order-list') ? 'active' : '' }}">Outward
                                         Challan</a></li>
                             </ul>
                         </li>
                         @endif

                     </ul>
                     </li>


                     <li class="submenu-open">
                         <ul>

                             @if ($rolePermissions->where('permission_id', 15)->where('view', 1)->isNotEmpty())
                             <li
                                 class="submenu {{ collect($orderManagementRoutes)->contains(fn($route) => Request::is($route) || strpos(Request::fullUrl(), $route) !== false) ? 'submenu-open' : '' }}">
                                 <a href="javascript:void(0);"
                                     class="{{ collect($orderManagementRoutes)->contains(fn($route) => Request::is($route) || strpos(Request::fullUrl(), $route) !== false) ? 'subdrop active' : '' }}">
                                     <i data-feather="layers"></i><span>Order Management</span><span
                                         class="menu-arrow"></span>
                                 </a>
                                 <ul
                                     style="{{ collect($orderManagementRoutes)->contains(fn($route) => Request::is($route) || strpos(Request::fullUrl(), $route) !== false) ? 'display: block;' : '' }}">
                                     <li><a href="/create-order"
                                             class="{{ Request::is('create-order') ? 'active' : '' }}">Create
                                             Order</a></li>
                                     {{-- <li><a href="/orders/pending?date={{ date('Y-m-d', strtotime('+1 day')) }}"
                                     class="{{ Request::is('orders/pending') ? 'active' : '' }}">Created
                                     Order</a>
                             </li> --}}
                             <li><a href="/orders/processing"
                                     class="{{ Request::is('orders/processing') ? 'active' : '' }}">Production</a>
                             </li>
                             <li><a href="/orders/dispatch"
                                     class="{{ Request::is('orders/dispatch') ? 'active' : '' }}">Dispatch
                                     Order</a></li>
                             <li><a href="{{ url('/outward-customer-order-list?status=dispatch') }}"
                                     class="{{ Request::is('outward-customer-order-list') && request('status') === 'dispatch' ? 'active' : '' }}">Convert
                                     to Invoice</a></li>
                             <li><a href="/orders/complete"
                                     class="{{ Request::is('orders/complete') ? 'active' : '' }}">Delivered</a>
                             </li>
                             <li><a href="/orders/cancel"
                                     class="{{ Request::is('orders/cancel') ? 'active' : '' }}">Cancel
                                     Order</a></li>
                         </ul>
                     </li>
                     @endif
                     @if ($rolePermissions->where('permission_id', 16)->where('view', 1)->isNotEmpty())
                     <li
                         class="submenu {{ collect($advanceOrderRoutes)->contains(fn($route) => Request::is($route)) ? 'submenu-open' : '' }}">
                         <a href="javascript:void(0);"
                             class="{{ collect($advanceOrderRoutes)->contains(fn($route) => Request::is($route)) ? 'subdrop active' : '' }}">
                             <i data-feather="layers"></i><span>Advance Order</span><span
                                 class="menu-arrow"></span>
                         </a>
                         <ul
                             style="{{ collect($advanceOrderRoutes)->contains(fn($route) => Request::is($route)) ? 'display: block;' : '' }}">
                             <li><a href="/create-advance-order"
                                     class="{{ Request::is('create-advance-order') ? 'active' : '' }}">Create
                                     Order</a></li>
                             <li><a href="/advance-order-list/pending"
                                     class="{{ Request::is('advance-order-list/pending') ? 'active' : '' }}">Created
                                     Orders</a></li>
                             {{-- <li><a href="/advance-order-list/processing"
                                                 class="{{ Request::is('advance-order-list/processing') ? 'active' : '' }}">Processing
                             Orders</a>
                     </li> --}}
                     <li><a href="/advance-order-list/dispatch"
                             class="{{ Request::is('advance-order-list/dispatch') ? 'active' : '' }}">Dispatch
                             Orders</a></li>

                     <li><a href="/advance-order-list/complete"
                             class="{{ Request::is('advance-order-list/complete') ? 'active' : '' }}">Convert
                             to invoice
                         </a></li>
                     <li><a href="/advance-order-list/delivered"
                             class="{{ Request::is('advance-order-list/delivered') ? 'active' : '' }}">Delivered
                             Orders</a></li>
                     <li><a href="/advance-order-list/invoices"
                             class="{{ Request::is('advance-order-list/invoice') ? 'active' : '' }}">Invoices</a></li>
                     <li><a href="/advance-order-list/cancel"
                             class="{{ Request::is('advance-order-list/cancel') ? 'active' : '' }}">Cancel
                             Order</a></li>
                     </ul>
                     </li>
                     @endif


                     @if ($rolePermissions->where('permission_id', 27)->where('view', 1)->isNotEmpty())
                     <li
                         class="submenu {{ collect($posOrder)->contains(fn($route) => Request::is($route)) ? 'submenu-open' : '' }}">
                         <a href="javascript:void(0);"
                             class="{{ collect($posOrder)->contains(fn($route) => Request::is($route)) ? 'subdrop active' : '' }}">
                             <i data-feather="layers"></i><span>POS Order</span><span
                                 class="menu-arrow"></span>
                         </a>
                         <ul
                             style="{{ collect($posOrder)->contains(fn($route) => Request::is($route)) ? 'display: block;' : '' }}">
                             <li><a href="/pos-order"
                                     class="{{ Request::is('pos-order') ? 'active' : '' }}">Order
                                 </a></li>
                             <li><a href="/kot"
                                     class="{{ Request::is('kot') ? 'active' : '' }}">KOT</a></li>




                         </ul>
                     </li>
                     @endif

                     @if ($rolePermissions->where('permission_id', 17)->where('view', 1)->isNotEmpty())
                     <li class="{{ Request::is('inward-finish-goods') ? 'active' : '' }}"><a
                             href="/inward-finish-goods"><i data-feather="download"></i><span>Inward
                                 Finish Goods</span></a></li>
                     @endif
                     @if ($rolePermissions->where('permission_id', 19)->where('view', 1)->isNotEmpty())
                     <li class="{{ Request::is('invoices') ? 'active' : '' }}"><a href="/invoices"><i
                                 data-feather="user-plus"></i><span>Invoices</span></a></li>
                     @endif

                     @if ($rolePermissions->where('permission_id', 19)->where('view', 1)->isNotEmpty())
                     <li class="{{ Request::is('sale-return') ? 'active' : '' }}"><a
                             href="/sale-return"><i data-feather="user-plus"></i><span>Sale
                                 Return</span></a></li>
                     @endif
                     @if ($rolePermissions->where('permission_id', 20)->where('view', 1)->isNotEmpty())
                     <li class="{{ Request::is('barcode') ? 'active' : '' }}"><a href="/barcode"><i
                                 class="fa fa-barcode fs-16 me-2"></i><span> Barcode</span></a></li>
                     @endif
                     </ul>
                     </li>

                     <li class="submenu-open">
                         <ul>
                             @if ($rolePermissions->where('permission_id', 21)->where('view', 1)->isNotEmpty())
                             <li
                                 class="submenu {{ collect($stockReportRoutes)->contains(fn($route) => Request::is($route)) ? 'submenu-open' : '' }}">
                                 <a href="javascript:void(0);"
                                     class="{{ collect($stockReportRoutes)->contains(fn($route) => Request::is($route)) ? 'subdrop active' : '' }}">
                                     <i data-feather="layers"></i><span>Stock Report</span><span
                                         class="menu-arrow"></span>
                                 </a>
                                 <ul
                                     style="{{ collect($stockReportRoutes)->contains(fn($route) => Request::is($route)) ? 'display: block;' : '' }}">
                                     <li><a href="/current-stock"
                                             class="{{ Request::is('current-stock') ? 'active' : '' }}">CS Raw
                                             Material</a></li>
                                     <li><a href="/current-stock-finish-products"
                                             class="{{ Request::is('current-stock-finish-products') ? 'active' : '' }}">CS
                                             Finish Products</a></li>
                                     <li><a href="/outlet-current-stock"
                                             class="{{ Request::is('outlet-current-stock') ? 'active' : '' }}">Outlet
                                             Current Stock</a></li>
                                     <li><a href="/near-by-minimum-stock"
                                             class="{{ Request::is('near-by-minimum-stock') ? 'active' : '' }}">Near
                                             by Minimum Stock</a></li>
                                     <li><a href="/finish-goods-defective-stock"
                                             class="{{ Request::is('finish-goods-defective-stock') ? 'active' : '' }}">Finish
                                             Goods Defective Stock</a></li>
                                 </ul>
                             </li>
                             @endif
                             @if ($rolePermissions->where('permission_id', 22)->where('view', 1)->isNotEmpty())
                             <li
                                 class="submenu {{ collect($reportsRoutes)->contains(fn($route) => Request::is($route)) ? 'submenu-open' : '' }}">
                                 <a href="javascript:void(0);"
                                     class="{{ collect($reportsRoutes)->contains(fn($route) => Request::is($route)) ? 'subdrop active' : '' }}">
                                     <i data-feather="layers"></i><span>Reports</span><span
                                         class="menu-arrow"></span>
                                 </a>
                                 <ul
                                     style="{{ collect($reportsRoutes)->contains(fn($route) => Request::is($route)) ? 'display: block;' : '' }}">
                                     <li><a href="/purchase-variation-report"
                                             class="{{ Request::is('purchase-variation-report') ? 'active' : '' }}">Purchase
                                             Variation</a></li>
                                     <li><a href="/purchase-register-report"
                                             class="{{ Request::is('purchase-register-report') ? 'active' : '' }}">Purchase
                                             Register</a></li>
                                     <li><a href="/sale-register-report"
                                             class="{{ Request::is('sale-register-report') ? 'active' : '' }}">Sale
                                             Register</a></li>
                                     <li><a href="/category-subcategory-report"
                                             class="{{ Request::is('category-subcategory-report') ? 'active' : '' }}">Category
                                             Sub Category Report</a></li>
                                     <li><a href="/customer-wise-report"
                                             class="{{ Request::is('customer-wise-report') ? 'active' : '' }}">Customer
                                             Wise Report</a></li>

                                     <li><a href="/tally-report"
                                             class="{{ Request::is('tally-report') ? 'active' : '' }}">Tally
                                             Report</a></li>
                                     <li><a href="/debit-credit-report"
                                             class="{{ Request::is('tally-report') ? 'active' : '' }}">Debit
                                             Credit Note</a></li>
                                     <li><a href="/rm-consumpton-report"
                                             class="{{ Request::is('rm-consumpton-report') ? 'active' : '' }}">RM
                                             Consumption Report</a></li>

                                     <li><a href="/sub-report-consumption"
                                             class="{{ Request::is('sub-report-consumption') ? 'active' : '' }}">Sub
                                             Report Consumption </a></li>
                                     <li><a href="/production-chart-report"
                                             class="{{ Request::is('production-chart-report') ? 'active' : '' }}">
                                           Production Chart Report  </a></li>
                                 </ul>
                             </li>
                             @endif
                             @if ($rolePermissions->where('permission_id', 23)->where('view', 1)->isNotEmpty())
                             <li
                                 class="submenu {{ collect($stockAuditRoutes)->contains(fn($route) => Request::is($route)) ? 'submenu-open' : '' }}">
                                 <a href="javascript:void(0);"
                                     class="{{ collect($stockAuditRoutes)->contains(fn($route) => Request::is($route)) ? 'subdrop active' : '' }}">
                                     <i data-feather="sliders"></i><span>Stock Audit</span><span
                                         class="menu-arrow"></span>
                                 </a>
                                 <ul
                                     style="{{ collect($stockAuditRoutes)->contains(fn($route) => Request::is($route)) ? 'display: block;' : '' }}">
                                     <li><a href="/audit-setting"
                                             class="{{ Request::is('audit-setting') ? 'active' : '' }}">RM
                                             Audit
                                             Settings</a></li>
                                     <li><a href="/audit-report"
                                             class="{{ Request::is('audit-report') ? 'active' : '' }}">RM
                                             Audit
                                             Report</a></li>
                                     <li><a href="/fg-audit-setting"
                                             class="{{ Request::is('fg-audit-setting') ? 'active' : '' }}">FG
                                             Audit Settings</a></li>
                                     <li><a href="/fg-audit-report"
                                             class="{{ Request::is('fg-audit-report') ? 'active' : '' }}">FG
                                             Audit Report</a></li>
                                     <li><a href="/outlet-audit-setting"
                                             class="{{ Request::is('outlet-audit-setting') ? 'active' : '' }}">Outlet
                                             Audit Settings</a></li>
                                     <li><a href="/outlet-audit-report"
                                             class="{{ Request::is('outlet-audit-report') ? 'active' : '' }}">Outlet
                                             Audit Report</a></li>
                                 </ul>
                             </li>
                             @endif
                         </ul>
                     </li>
                     <li class="submenu-open">

                         <ul>
                             @if ($rolePermissions->where('permission_id', 24)->where('view', 1)->isNotEmpty())
                             <li class="submenu">
                                 <a href="javascript:void(0);">
                                     <i data-feather="layers"></i><span>Attendance Report </span><span
                                         class="menu-arrow"></span>
                                 </a>
                                 <ul>
                                     <li><a href="/attendance-report">Attendance Report</a></li>
                                     <li><a href="/attendance-report-monthly">Attendance Monthly</a></li>



                                 </ul>
                             </li>
                             @endif
                             @if ($rolePermissions->where('permission_id', 26)->where('view', 1)->isNotEmpty())
                             <li class="submenu">
                                 <a href="javascript:void(0);">
                                     <i data-feather="layers"></i><span>Expense Management </span><span
                                         class="menu-arrow"></span>
                                 </a>
                                 <ul>
                                     <li><a href="/expense-category">Category</a></li>
                                     <li><a href="/expense-sub-category">Sub Category</a></li>



                                 </ul>
                             </li>
                             @endif
                         </ul>
                     </li>

                     @if ($rolePermissions->where('permission_id', 24)->where('view', 1)->isNotEmpty())
                     <li><a href="/settings" class="{{ Request::is('settings') ? 'active' : '' }}"><i
                                 data-feather="settings"></i><span>Settings</span></a></li>
                     @endif


                     <li><a href="../logout"><i data-feather="log-out"></i><span>Logout</span></a></li>
                     </ul>
                 </div>
             </div>
         </div>

         <form method="POST" action="{{ route('StartDay') }}">
             @csrf
             <div class="modal fade" id="startDay" tabindex="-1" role="dialog" aria-labelledby="modalTitleId"
                 aria-hidden="true">
                 <div class="modal-dialog" role="document">
                     <div class="modal-content">
                         <div class="modal-header">
                             <h5 class="modal-title" id="modalTitleId">
                                 Start Day
                             </h5>
                             <button type="button" class="btn-close" data-bs-dismiss="modal"
                                 aria-label="Close"></button>
                         </div>
                         <div class="modal-body">
                             <input type="hidden" class="location" name="start_location">
                             <h5> You are going to start your day...</h5>
                         </div>
                         <div class="modal-footer">
                             <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                 Close
                             </button>
                             <button type="submit" class="btn btn-success">Start Day</button>
                         </div>
                     </div>
                 </div>
             </div>
         </form>

         <form method="POST" action="{{ route('EndDay') }}">
             @csrf
             <div class="modal fade" id="endDay" tabindex="-1" role="dialog" aria-labelledby="modalTitleId"
                 aria-hidden="true">
                 <div class="modal-dialog" role="document">
                     <div class="modal-content">
                         <div class="modal-header">
                             <h5 class="modal-title" id="modalTitleId">
                                 End Day
                             </h5>
                             <button type="button" class="btn-close" data-bs-dismiss="modal"
                                 aria-label="Close"></button>
                         </div>
                         <div class="modal-body">
                             <input type="hidden" class="location" name="start_location">
                             <h5> You are going to end your day...</h5>
                         </div>
                         <div class="modal-footer">
                             <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                 Close
                             </button>
                             <button type="submit" class="btn btn-success">End Day</button>
                         </div>
                     </div>
                 </div>
             </div>
         </form>

         <script>
             if (navigator.geolocation) {
                 navigator.geolocation.getCurrentPosition(function(position) {
                     var latitude = position.coords.latitude;
                     var longitude = position.coords.longitude;
                     $(".location").val(latitude + ", " + longitude)
                     console.log("Latitude: " + latitude + ", Longitude: " + longitude);
                 }, function(error) {
                     console.log("Error occurred: " + error.message);
                 });
             } else {
                 console.log("Geolocation is not supported by this browser.");
             }
         </script>


         <div class="page-wrapper">
             <div class="content">