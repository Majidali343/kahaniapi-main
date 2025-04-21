<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>



    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form style="text-align: center" id="date-filter-form ">
                        <label for="start_date">Start Date:</label>
                        <input class="border-gray-300  rounded" type="date" id="start_date" name="start_date">

                        <label for="end_date">End Date:</label>
                        <input class="border-gray-300  rounded" type="date" id="end_date" name="end_date">

                        <button type="button"
                            class="ml-4 bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded"
                            onclick="filterData()">Filter</button>
                    </form>



                    <div class="row mt-4">

                        @if (Auth::user()->type == 'superadmin')
                            <div class="col-md-4 col-xl-4">
                                <div class="card bg-c-yellow order-card">
                                    <div class="card-block">
                                        <h6 class="m-b-20"> Users </h6>
                                        <i class="fa fa-map mt-4"></i>
                                        <div class="m-b-0 mt-5">
                                            <span>Total Users:</span>
                                            <h1 class="d-block f-right" id="users-count">{{ $users }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-4 col-xl-4">
                                <div class="card bg-c-green order-card">
                                    <div class="card-block">
                                        <h6 class="m-b-20"> Stories </h6>
                                        <i class="fa fa-map mt-4"></i>
                                        <div class="m-b-0 mt-5">
                                            <span>Total Stories:</span>
                                            <h1 class="d-block f-right" id="kahanis-count">{{ $kahanis }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-xl-4">
                                <div class="card bg-c-pink order-card">
                                    <div class="card-block">
                                        <h6 class="m-b-20"> Reviews </h6>
                                        <i class="fa fa-map mt-4"></i>
                                        <div class="m-b-0 mt-5">
                                            <span>Total reviews:</span>
                                            <h1 class="d-block f-right" id="reviews-count">{{ $reviews }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-xl-3">
                                <div style="background-color: #ff869a" class="card  order-card">
                                    <div class="card-block">
                                        <h6 class="m-b-20"> Memberships </h6>
                                        <i class="fa fa-map mt-4"></i>
                                        <div class="m-b-0 mt-5">
                                            <span>Memberships Completed</span>
                                            <h1 class="d-block f-right" id="membership-completed-count">
                                                {{ $membershipCompleted }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-3 col-xl-3">
                                <div style="background-color: rgb(42, 170, 170)" class="card  order-card">
                                    <div class="card-block">
                                        <h6 class="m-b-20"> Earnings </h6>
                                        <i class="fa fa-map mt-4"></i>
                                        <div class="m-b-0 mt-5">
                                            <span>Net Income </span>
                                            <h1 class="d-block f-right" id="earning-count">{{ $earnings }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-xl-3">
                                <div style="background-color: #68a748 " class="card  order-card">
                                    <div class="card-block">
                                        <h6 class="m-b-20"> Prices </h6>
                                        <i class="fa fa-map mt-4"></i>
                                        <div class="m-b-0 mt-5">
                                            <span>Total prices </span>
                                            <h1 class="d-block f-right" id="purchase-count">{{ $totalpurchase }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-xl-3">
                                <div style="background-color: rgb(156, 69, 106)" class="card  order-card">
                                    <div class="card-block">
                                        <h6 class="m-b-20">
                                            @if (Auth::user()->type == 'superadmin')
                                                Admin coupons ( <span id="admincoupon-count"> {{ $admincoupon }}
                                                </span> )
                                            @else
                                                Coupons
                                            @endif

                                        </h6>
                                        <i class="fa fa-map mt-4"></i>
                                        <div class="m-b-0 mt-5">
                                            <span>Total Coupons </span>
                                            <h1 class="d-block f-right" id="coupon-count">{{ $coupon }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif






                        @if (Auth::user()->type !== 'superadmin')


                            <div class="col-md-4 col-xl-4">
                                <div style="background-color: rgb(156, 69, 106)" class="card  order-card">
                                    <div class="card-block">
                                        <h6 class="m-b-20">
                                            @if (Auth::user()->type == 'superadmin')
                                                Admin coupons ( <span id="admincoupon-count"> {{ $admincoupon }}
                                                </span> )
                                            @else
                                                Coupons
                                            @endif

                                        </h6>
                                        <i class="fa fa-map mt-4"></i>
                                        <div class="m-b-0 mt-5">
                                            <span>Total Coupons </span>
                                            <h1 class="d-block f-right" id="coupon-count">{{ $coupon }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-xl-4">
                                <div class="card bg-c-yellow order-card">
                                    <div class="card-block">
                                        <h6 class="m-b-20"> Coupon users </h6>
                                        <i class="fa fa-map mt-4"></i>
                                        <div class="m-b-0 mt-5">
                                            <span>Total Coupon Users:</span>
                                            <h1 class="d-block f-right" id="coupon-usermemberships">
                                                {{ $usermemberships }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-xl-4">
                                <div style="background-color: rgb(42, 170, 170)" class="card  order-card">
                                    <div class="card-block">
                                        <h6 class="m-b-20"> Amount </h6>
                                        <i class="fa fa-map mt-4"></i>
                                        <div class="m-b-0 mt-5">
                                            <span>User Paid </span>
                                            <h1 class="d-block f-right" id="subadminearning-count">
                                                {{ $subadminearnings }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-xl-4">
                                <div style="background-color: rgb(201, 64, 128)" class="card  order-card">
                                    <div class="card-block">
                                        <h6 class="m-b-20">Organization Earnings </h6>
                                        <i class="fa fa-map mt-4"></i>
                                        <div class="m-b-0 mt-5">
                                            <span>Your Earning </span>
                                            <h1 class="d-block f-right" id="subadmiprofit-count">
                                                {{ $subadminprofits }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-xl-4">
                                <div style="background-color: #68a748" class="card  order-card">
                                    <div class="card-block">
                                        <h6 class="m-b-20">Total amount </h6>
                                        <i class="fa fa-map mt-4"></i>
                                        <div class="m-b-0 mt-5">
                                            <span>Total amounts </span>
                                            <h1 class="d-block f-right" id="subadminamount-count">
                                                {{ $totalpurchasesubadmin }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                    @if (Auth::user()->type == 'superadmin')
                        <div class="py-6" id="donut-chart"></div>
                    @endif
                </div>
            </div>
        </div>
    </div>









</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    function filterData() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        // Send AJAX request
        $.ajax({
            url: '{{ route('dashboard.filter') }}',
            type: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate,
            },
            success: function(response) {
                // Update the dashboard with new data
                $('#users-count').text(response.users);
                $('#kahanis-count').text(response.kahanis);
                $('#reviews-count').text(response.reviews);
                $('#coupon-count').text(response.coupon);
                $('#admincoupon-count').text(response.admincoupon);
                $('#earning-count').text(response.earnings);
                $('#subadminearning-count').text(response.subadminearnings);
                $('#membership-completed-count').text(response.membershipCompleted);
                $('#coupon-usermemberships').text(response.usermemberships);
                $('#subadmiprofit-count').text(response.subadminprofits);
                $('#subadminamount-count').text(response.totalpurchasesubadminquery);
                $('#purchase-count').text(response.totalpurchase);

                // Now, update the chart data with the new values
                const usersCount = response.users;
                const kahanisCount = response.kahanis;
                const reviewsCount = response.reviews;
                const membershipCompletedCount = response.membershipCompleted;

                // Re-render the chart with the updated data
                updateChart(usersCount, kahanisCount, reviewsCount, membershipCompletedCount);
            },
            error: function(xhr) {
                console.error('An error occurred:', xhr);
            }
        });
    }

    function updateChart(usersCount, kahanisCount, reviewsCount, membershipCompletedCount) {
        const chartOptions = {
            series: [usersCount, kahanisCount, reviewsCount, membershipCompletedCount],
            colors: ["#ffcb80", "#59e0c5", "#ff5370", "#ff869a"],
            chart: {
                height: 320,
                width: "100%",
                type: "donut",
            },
            stroke: {
                colors: ["transparent"],
                lineCap: "",
            },
            grid: {
                padding: {
                    top: -2,
                },
            },
            labels: ["Users", "Stories", "Reviews", "Memberships"],
            dataLabels: {
                enabled: false,
            },
            legend: {
                position: "bottom",
                fontFamily: "Inter, sans-serif",
            },
            yaxis: {
                labels: {
                    formatter: function(value) {
                        return value + "";
                    },
                },
            },
            xaxis: {
                labels: {
                    formatter: function(value) {
                        return value + "";
                    },
                    axisTicks: {
                        show: false,
                    },
                    axisBorder: {
                        show: false,
                    },
                },
            },

            states: {
                hover: {
                    filter: {
                        type: 'none', // Disable hover effect
                    },
                },
            },
        };

        // Destroy the existing chart before re-rendering
        if (window.donutChart) {
            window.donutChart.destroy();
        }

        // Create a new chart and store it in a global variable
        window.donutChart = new ApexCharts(document.getElementById("donut-chart"), chartOptions);
        window.donutChart.render();
    }


    // Initial chart rendering (you can call this on page load with default data)
    updateChart(
        parseInt(document.getElementById('users-count').innerText),
        parseInt(document.getElementById('kahanis-count').innerText),
        parseInt(document.getElementById('reviews-count').innerText),
        parseInt(document.getElementById('membership-completed-count').innerText)
    );
</script>
<style>
    .order-card {
        color: #fff;
    }

    .bg-c-blue {
        background: linear-gradient(45deg, #4099ff, #73b4ff);
    }

    .bg-c-green {
        background: linear-gradient(45deg, #2ed8b6, #59e0c5);
    }

    .bg-c-yellow {
        background: linear-gradient(45deg, #FFB64D, #ffcb80);
    }

    .bg-c-pink {
        background: linear-gradient(45deg, #FF5370, #ff869a);
    }


    .card {
        border-radius: 5px;
        -webkit-box-shadow: 0 1px 2.94px 0.06px rgba(4, 26, 55, 0.16);
        box-shadow: 0 1px 2.94px 0.06px rgba(4, 26, 55, 0.16);
        border: none;
        margin-bottom: 30px;
        -webkit-transition: all 0.3s ease-in-out;
        transition: all 0.3s ease-in-out;
    }

    .card .card-block {
        padding: 25px;
    }

    .order-card i {
        font-size: 26px;
    }

    .f-left {
        float: left;
    }

    .f-right {
        float: right;
    }

    .card:hover {
        box-shadow: 4px 6px 10px rgba(0, 0, 0, 0.4);
        transition: box-shadow 0.2s ease;
    }

    .card-block div h1 {
        font-size: 24px;
    }
</style>
