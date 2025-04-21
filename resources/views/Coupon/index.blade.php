<style>
    .buttonuser {
        background: purple !important;
        padding: 10px;
        border-radius: 10px;
        color: beige;
        margin: 14px;
        width: 150px;
        text-align: center;
    }

    @media screen and (max-width:600px) {
        .buttonuser {
            padding: 7px;
            margin: 10px;
            width: 70px;
        }
    }

    .table-header {
        background: #efefef;
        height: 40px;



    }
</style>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Coupons') }}
        </h2>
    </x-slot>

    @if (session('success'))
        <div class="max-w-3xl mx-auto mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20">
                        <title>Close</title>
                        <path
                            d="M14.348 5.652a1 1 0 00-1.414 0L10 8.586 7.066 5.652a1 1 0 00-1.414 1.414l2.934 2.934-2.934 2.934a1 1 0 101.414 1.414l2.934-2.934 2.934 2.934a1 1 0 001.414-1.414l-2.934-2.934 2.934-2.934a1 1 0 000-1.414z" />
                    </svg>
                </span>
            </div>
        </div>
    @endif
    @if (session('message'))
        <div class="max-w-3xl mx-auto mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('message') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20">
                        <title>Close</title>
                        <path
                            d="M14.348 5.652a1 1 0 00-1.414 0L10 8.586 7.066 5.652a1 1 0 00-1.414 1.414l2.934 2.934-2.934 2.934a1 1 0 101.414 1.414l2.934-2.934 2.934 2.934a1 1 0 001.414-1.414l-2.934-2.934 2.934-2.934a1 1 0 000-1.414z" />
                    </svg>
                </span>
            </div>
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">

                @if (Auth::user()->type == 'superadmin')
                    <a type="button" class="buttonuser" href="{{ route('addnew.coupon') }}">Add Coupon</a>
                @endif


                <div class=" text-gray-900">

                    @if (isset($message))
                        <p class= "text-red-600 text-lg text-center py-6   ">{{ $message }} </p>
                    @endif




                    <div class="p-6 relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table id="users-table" style="padding: 15px"
                            class=" w-full text-sm text-left rtl:text-right text-gray-500 ">
                            <thead class="border-b table-header" style=" ; height:40px ">
                                <tr>
                                    <th scope="col" class="px-3  py-3">
                                        Coupon code
                                    </th>
                                    <th scope="col" class="px-3 py-3">
                                        Discount percentage
                                    </th>
                                    <th scope="col" class="px-3 py-3">
                                        Organization Stake
                                    </th>
                                    <th scope="col" class="px-3 py-3">
                                        Used By
                                    </th>

                                    <th scope="col" class="px-3 py-3">
                                        Date
                                    </th>
                                    @if (Auth::user()->type == 'superadmin')
                                        <th scope="col" class="px-3 py-3">
                                            Assign
                                        </th>
                                        <th scope="col" class="px-3 py-3">
                                            Action
                                        </th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody>
                                @if (isset($Coupons))

                                    @foreach ($Coupons as $Coupon)
                                        <tr data-coupon-id="{{ $Coupon->id }}"
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <td class="px-6 py-4">
                                                {{ $Coupon->coupon_code }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $Coupon->discount_percentage }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $Coupon->organization_stake }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <a href="{{ route('couponorders', $Coupon->coupon_code) }}">
                                                    <p style="color: rgb(69, 99, 231);text-decoration: underline;">
                                                        {{ $Coupon->usage_count }}</p>
                                                </a>

                                            </td>


                                            <td class="px-6 py-4">
                                                {{ $Coupon->updated_at }}
                                            </td>
                                            @if (Auth::user()->type == 'superadmin')
                                                <td class="px-6 py-4">
                                          
                                                    @if ($Coupon->admin_id == 1)
                                                        <select id="admin"
                                                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                            <option default value="">Not Assigned</option>

                                                            @foreach ($admins as $admin)
                                                                <option value="{{ $admin->id }}"
                                                                    {{ $admin->id == $Coupon->admin_id ? 'selected' : '' }}>
                                                                    {{ $admin->email }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        @foreach ($admins as $admin)
                                                            @if ($admin->id == $Coupon->admin_id)
                                                                {{ $admin->email }}
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                </td>
                                                <td class="px-6 py-4">
                                                    @if ($Coupon->admin_id == 1)
                                                    <a href="{{ route('coupon.find', $Coupon->id) }}">
                                                        <span class="material-symbols-outlined">edit_square</span>
                                                    </a>
                                                     @endif
                                                    <a href="#"
                                                        onclick="event.preventDefault(); confirmDelete({{ $Coupon->id }});">
                                                        <span class="material-symbols-outlined">
                                                            delete
                                                        </span>
                                                    </a>

                                                    <form id="delete-form-{{ $Coupon->id }}"
                                                        action="{{ route('coupon.delete', $Coupon->id) }}"
                                                        method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>

                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>


            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js">
    </script>
    <script>
        $(document).ready(function() {
            $('#users-table').on('change', 'select#admin', function() {
                var adminId = $(this).val(); // Get the selected admin ID
                var couponId = $(this).closest('tr').data('coupon-id'); // Get the coupon ID from the row
                var token = "{{ csrf_token() }}"; // CSRF token for Laravel
                var $selectTd = $(this).closest('td'); // Reference to the td containing the select

                // Clear any previous message in this td
                $selectTd.find('.response-message').remove();

                if (adminId) {
                    $.ajax({
                        url: '/assign-admin',
                        type: 'POST',
                        data: {
                            _token: token, // Pass the CSRF token
                            admin_id: adminId, // Admin ID
                            coupon_id: couponId // Coupon ID
                        },
                        success: function(response) {
                            // Display the response message in the same td as the select
                            var messageDiv = $(
                                '<div class="mt-2 text-green-500 response-message">' +
                                response.message + '</div>');
                            $selectTd.append(messageDiv);

                            // Optionally, remove the message after a few seconds
                            setTimeout(function() {
                                messageDiv.fadeOut('slow', function() {
                                    $(this).remove();
                                });
                            }, 3000);
                        },
                        error: function(xhr) {
                            // Handle error response
                            var errorDiv = $(
                                '<div class="mt-2 text-red-500 response-message">Error: ' +
                                xhr.responseJSON.message + '</div>');
                            $selectTd.append(errorDiv);

                            // Optionally, remove the error message after a few seconds
                            setTimeout(function() {
                                errorDiv.fadeOut('slow', function() {
                                    $(this).remove();
                                });
                            }, 3000);
                        }
                    });
                }
            });

        });
    </script>


    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this Coupon?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>

    <script>
        $(document).ready(function() {
            var table = $('#users-table').DataTable({
                "pagingType": "full_numbers",
                "searching": true,
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search Coupons...",
                    "lengthMenu": "Show _MENU_ entries",
                }
            });



            // Attach event listener to the DataTable draw event
            table.on('draw', function() {
                $('select[name="status"]').off('change').on('change', function() {
                    var form = $(this).closest('form');
                    var membershipId = form.data('membership-id');
                    var status = $(this).val();
                    var token = form.find('input[name="_token"]').val();

                    $.ajax({
                        url: '/manualpayment/' + membershipId + '/status',
                        type: 'POST',
                        data: {
                            _token: token,
                            status: status
                        },
                        success: function(response) {
                            $('#statusMessage-' + membershipId).html(
                                '<span style="color:green;">' +
                                response.message + '</span>');
                        },
                        error: function(response) {
                            $('#statusMessage-' + membershipId).html(
                                '<span style="color:red;">' +
                                response.responseJSON.message + '</span>');
                        }
                    });
                });
            });

            // Trigger the draw event once after initialization to attach the listeners
            table.draw();
        });
    </script>

</x-app-layout>
