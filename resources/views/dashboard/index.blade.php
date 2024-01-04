@extends('layouts.sidebar')

@section('dashboard-selected', 'active')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase">Total
                            Interviews</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalInterviews }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach ($statusCounts as $statusCount)
                <div class="col-md-3 col-sm-6 pt-2">
                    <div class="card border-left-{{ $statusCount['color'] }} shadow h-100">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-{{ $statusCount['color'] }} text-uppercase">
                                {{ $statusCount['status'] }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCount['count'] }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row pt-3">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-4">
                        <h6 class="m-0 font-weight-bold text-primary text-center">Interviews</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <div class="input-group">
                                    <select id="interviewerFilter" class="form-select" aria-label="Select Interviewer">
                                        <option value="all interviewers" selected>All Interviewers</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->name }}">{{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary" type="button" id="clearInterviewerFilter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-lg-4">
                                <div class="input-group">
                                    <select id="statusFilter" class="form-select" aria-label="Select Status">
                                        <option value="all status" selected>All Statuses</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary" type="button" id="clearStatusFilter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-lg-4">
                                <div class="input-group">
                                    <select id="typeFilter" class="form-select" aria-label="Select Type">
                                        <option value="all types" selected>All Types</option>
                                        <option value="physical">Physical Interviews</option>
                                        <option value="online">Online Interviews</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary" type="button" id="clearTypeFilter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chart-bar">
                            <canvas id="totalInterviews"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        var interviewerFilter = document.getElementById('interviewerFilter');
        var statusFilter = document.getElementById('statusFilter');
        var typeFilter = document.getElementById('typeFilter');
        var colors = [
            'rgba(255, 99, 132, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(255, 205, 86, 0.5)',
            'rgba(75, 192, 192, 0.5)',
            'rgba(153, 102, 255, 0.5)',
            'rgba(255, 159, 64, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(255, 99, 132, 0.5)',
            'rgba(255, 205, 86, 0.5)',
            'rgba(75, 192, 192, 0.5)',
            'rgba(153, 102, 255, 0.5)',
            'rgba(255, 159, 64, 0.5)'
        ];

        // Chart
        var ctx = document.getElementById('totalInterviews').getContext('2d');
        myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach ($interviewsData as $key => $value)
                        '{{ $key }}',
                    @endforeach
                ],
                datasets: [{
                    backgroundColor: [
                        @foreach ($interviewsData as $key => $value)
                            colors[{{ $loop->index % 12 }}],
                        @endforeach
                    ],
                    borderColor: [
                        @foreach ($interviewsData as $key => $value)
                            colors[{{ $loop->index % 12 }}],
                        @endforeach
                    ],
                    data: [
                        @foreach ($interviewsData as $value)
                            '{{ $value }}',
                        @endforeach
                    ],
                    borderWidth: 1,
                    maxBarThickness: 50,
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                layout: {
                    padding: {
                        left: 10,
                        right: 10,
                        top: 20,
                        bottom: 0
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }]
                },
                tooltips: {
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 15,
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 30,
                }
            }
        });

        // Getting data for chart
        function fetchData(selectedStatus) {
            $.ajax({
                url: '{{ route('interviews.filter') }}',
                type: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    interviewer: interviewerFilter.value,
                    status: statusFilter.value,
                    type: typeFilter.value
                },
                success: function(data) {
                    var lastTwelveMonths = getLastTwelveMonths();
                    var chartData = lastTwelveMonths.map(month => data[month] || 0);

                    myChart.data.labels = lastTwelveMonths;
                    myChart.data.datasets[0].data = chartData;
                    myChart.update();
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function getLastTwelveMonths() {
            var months = [];
            var currentDate = new Date();
            for (var i = 11; i >= 0; i--) {
                var d = new Date(currentDate.getFullYear(), currentDate.getMonth() - i, 1);
                months.push(d.toLocaleString('default', {
                    month: 'short'
                }) + ', ' + d.getFullYear());
            }
            return months;
        }

        //Escape button trigger
        $(document).keydown(function(event) {
            if (event.key === "Escape") {
                $('#clearInterviewerFilter').click();
                $('#clearStatusFilter').click();
                $('#clearTypeFilter').click();
            }
        });

        $('#interviewerFilter, #statusFilter, #typeFilter').change(function() {
            fetchData();
        });

        $('#clearInterviewerFilter').click(function() {
            interviewerFilter.value = 'all interviewers';
            fetchData();
        });
        $('#clearStatusFilter').click(function() {
            statusFilter.value = 'all status';
            fetchData();
        });
        $('#clearTypeFilter').click(function() {
            typeFilter.value = 'all types';
            fetchData();
        });

        fetchData();
    </script>
@endsection
