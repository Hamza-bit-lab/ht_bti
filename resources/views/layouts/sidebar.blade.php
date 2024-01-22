<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>@yield('title', 'BTI')</title>
    <link rel="shortcut icon" href="{{ asset('logo/favicon.ico') }}" type="image/x-icon">
{{--    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>--}}
{{--    <script src="https://cdn.tiny.cloud/1/7nmspghvzwclhh64xf44x1coa4q1cf1jto24noflohcv8guz/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>--}}
    <script src="https://cdn.tiny.cloud/1/nnd7pakaxqr7isf3oqefsdlew1jsidgl78umfeus6tg21ng0/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>



    <!-- Custom fonts for this template-->
    <link href="{{ asset('asset/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="{{ asset('asset/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="{{ asset('asset/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('css')
</head>

<body id="page-top">
    @include('partials.component2')
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/') }}">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text ml-1">Brown Tech Int</div>
            </a>


            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            @auth
                @if (Auth::user()->role === 'hr')
                    <!-- HR Dashboard -->
                    <li class="nav-item @yield('dashboard-selected')">
                        <a class="nav-link" href="{{ url('index') }}">
                            <i class="fas fa-fw fa-tachometer-alt"></i>
                            <span>Dashboard</span></a>
                    </li>

                    <!-- Divider -->
                    <hr class="sidebar-divider">

                    <div class="sidebar-heading">
                        Interviews
                    </div>

                    <li class="nav-item @yield('create-interview-selected')">
                        <a class="nav-link collapsed" href="{{ url('create-interview') }}">
                            <i class="far fa-calendar-plus"></i>
                            <span>Create Interview</span>
                        </a>
                    </li>

                    <li class="nav-item @yield('yesterday-interview-selected')">
                        <a class="nav-link collapsed" href="{{ url('interviews', 'yesterday') }}">
                            <i class="far fa-calendar-alt"></i>
                            <span>Yesterday Interviews</span>
                        </a>
                    </li>
                    <li class="nav-item @yield('today-interview-selected')">
                        <a class="nav-link collapsed" href="{{ url('interviews', 'today') }}">
                            <i class="far fa-calendar-alt"></i>
                            <span>Today Interviews</span>
                        </a>
                    </li>
                    <li class="nav-item @yield('tomorrow-interview-selected')">
                        <a class="nav-link collapsed" href="{{ url('interviews', 'tomorrow') }}">
                            <i class="far fa-calendar-alt"></i>
                            <span>Tomorrow Interviews</span>
                        </a>
                    </li>
                    <li class="nav-item @yield('all-interview-selected')">
                        <a class="nav-link collapsed" href="{{ url('interviews', 'all') }}">
                            <i class="far fa-calendar-check"></i>
                            <span>All Interviews</span>
                        </a>
                    </li>

                    <!-- Divider -->
                    <hr class="sidebar-divider">

                    <div class="sidebar-heading">
                        Employee
                    </div>

                    <li class="nav-item @yield('create-employee-selected')">
                        <a class="nav-link collapsed" href="{{ url('create-employee') }}">
                            <i class="fas fa-user-plus"></i>
                            <span>Create Employee</span>
                        </a>
                    </li>
                    <li class="nav-item @yield('all-employees-selected')">
                        <a class="nav-link collapsed" href="{{ url('employees') }}">
                            <i class="fas fa-users"></i>
                            <span>Employees</span>
                        </a>
                    </li>

                        <li class="nav-item @yield('employees-events-selected')">
                            <a class="nav-link collapsed" href="{{ url('employee-events') }}">
                                <i class="fas fa-users"></i>
                                <span>Employees' Events</span>
                            </a>
                        </li>

                    <!-- Divider -->
                    <hr class="sidebar-divider">

                    <div class="sidebar-heading">
                        Attendance
                    </div>

                    <li class="nav-item @yield('upload-attendance-selected')">
                        <a class="nav-link collapsed" href="{{ url('upload-attendance') }}">
                            <i class="fa-regular fa-file-excel"></i>
                            <span>Upload Attendance</span>
                        </a>
                    </li>

                    <li class="nav-item @yield('manage-attendance-selected')">
                        <a class="nav-link collapsed" href="{{ url('manage-attendance') }}">
                            <i class="fa-solid fa-file-pen"></i>
                            <span>Manage Attendance</span>
                        </a>
                    </li>

                    <li class="nav-item @yield('view-attendance-selected')">
                        <a class="nav-link collapsed" href="{{ url('view-attendance') }}">
                            <i class="fa-solid fa-clipboard-user"></i>
                            <span>View Attendance</span>
                        </a>
                    </li>

                    <li class="nav-item @yield('office-hours-selected')">
                        <a class="nav-link collapsed" href="{{ url('office-hours') }}">
                            <i class="fa-regular fa-clock"></i>
                            <span>Office Hours</span>
                        </a>
                    </li>

                    <li class="nav-item @yield('holidays-selected')">
                        <a class="nav-link collapsed" href="{{ url('holidays') }}">
                            <i class="fa-solid fa-icons"></i>
                            <span>Holidays</span>
                        </a>
                    </li>

                        <!-- Divider -->
                        <hr class="sidebar-divider">

                        <div class="sidebar-heading">
                            Notifications and Emails
                        </div>
                        <li class="nav-item @yield('notifications-selected')">
                            <a class="nav-link collapsed" href="{{ url('notifications') }}">
                                <i class="fa-solid fa-bell"></i>
                                <span>Notification</span>
                            </a>
                        </li>
                        <li class="nav-item @yield('events-selected')">
                            <a class="nav-link collapsed" href="{{ url('events') }}">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span>Events</span>
                            </a>
                        </li>
                @else
                    <!-- Employee Dashboard -->
                    <li class="nav-item @yield('employee-dashboard-selected')">
                        <a class="nav-link" href="{{ url('employee-dashboard') }}">
                            <i class="fas fa-fw fa-tachometer-alt"></i>
                            <span>Dashboard</span></a>
                    </li>

                    <!-- Divider -->
                    <hr class="sidebar-divider">

                    <div class="sidebar-heading">
                        Attendance
                    </div>

                    <li class="nav-item @yield('employee-attendance-selected')">
                        <a class="nav-link collapsed" href="{{ url('employee-attendance') }}">
                            <i class="fa-solid fa-clipboard-user"></i>
                            <span>View Attendance</span>
                        </a>
                    </li>
                @endif

            @endauth

        </ul>
        <!-- End of Sidebar -->


        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Logout</span>
                                <img class="img-profile rounded-circle"
                                    src="{{ asset('asset/img/undraw_profile.svg') }}">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">


                                <a class="dropdown-item" href="#" data-toggle="modal"
                                    data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                @yield('content')
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->
            <!-- carousal -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Brown Tech Int {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="{{ url('logout') }}">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('asset/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('asset/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('asset/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('asset/js/sb-admin-2.min.js') }}"></script>

    <!-- Page level plugins -->
    <script src="{{ asset('asset/vendor/chart.js/Chart.min.js') }}"></script>

    <!-- Page level custom scripts -->
    <!-- <script src="{{ asset('asset/js/demo/chart-area-demo.js') }}"></script> -->
    <!-- <script src="{{ asset('asset/js/demo/chart-pie-demo.js') }}"></script> -->
    <!-- <script src="{{ asset('asset/js/demo/chart-bar-demo.js') }}"></script> -->


    <script src="{{ asset('asset/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('asset/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Page level custom scripts -->
    <script src="{{ asset('asset/js/datatable.js') }}"></script>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>




    <script>
        tinymce.init({
            selector: 'textarea',

            image_class_list: [
                {title: 'img-responsive', value: 'img-responsive'},
            ],
            height: 500,
            setup: function (editor) {
                editor.on('init change', function () {
                    editor.save();
                });
            },
            plugins: [
                "advlist autolink lists link image charmap print preview anchor lineheight",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste imagetools"
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image lineheight",
            lineheight_formats: '0.3 1 1.5 2 2.5 3',
            content_style: "body { line-height: 0.3; }",
            image_title: true,
            automatic_uploads: true,
            images_upload_url: '/upload',
            file_picker_types: 'image',
            convert_urls: false,
            relative_urls: true,
            file_picker_callback: function(cb, value, meta) {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.onchange = function() {
                    var file = this.files[0];

                    var reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = function () {
                        var id = 'blobid' + (new Date()).getTime();
                        var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                        var base64 = reader.result.split(',')[1];
                        var blobInfo = blobCache.create(id, file, base64);
                        blobCache.add(blobInfo);

                        // Get CSRF token from the meta tag in the document
                        var csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

                        // Use Ajax to upload the image with CSRF token in the headers
                        var xhr, formData;
                        xhr = new XMLHttpRequest();
                        xhr.withCredentials = false;
                        xhr.open('POST', '/upload', true);
                        xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                        xhr.onload = function() {
                            var json;
                            if (xhr.status != 200) {
                                console.log('HTTP Error: ' + xhr.status);
                                return;
                            }
                            json = JSON.parse(xhr.responseText);
                            if (!json || typeof json.location != 'string') {
                                console.log('Invalid JSON: ' + xhr.responseText);
                                return;
                            }
                            cb(json.location, { title: file.name });
                        };
                        formData = new FormData();
                        formData.append('file', file);
                        xhr.send(formData);
                    };
                };
                input.click();
            }
        });
        // $('#editEventDescription').val('ksdfgjd')
        // tinymce.get('#editEventDescription').setContent('Your new value goes here');
    </script>
    @yield('js')
</body>

</html>
