<style>
    .table-header {
        background: #efefef;
        height: 40px;

    }
    .buttonuser {
        background: purple !important;
        padding: 10px;
        border-radius: 10px;
        color: beige;
        margin: 14px;
        width: 145px;
        text-align: center;
    }

    @media screen and (max-width:600px) {
        .buttonuser {
            padding: 7px;
            margin: 10px;
            width: 70px;
        }
    }
</style>


<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users') }}
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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class=" text-gray-900">

                    <a type="button" class="buttonuser" href="{{ route('newcustomer') }}">Add User</a> 
                    
                    <div class="flex items-center justify-center mb-4">
                        <form id="filterForm" method="GET" action="{{ route('allusers') }}">
                            <div class="flex">
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="border rounded px-2 mr-2" style="height: 40px" />
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="border rounded px-2 mr-2" style="height: 40px" />
                                
                                <button type="submit" class="ml-4 bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">Filter</button>
                    
                                <button type="button" onclick="resetFilters()" class="ml-4  bg-transparent hover:bg-red-700 text-blue-700 font-semibold py-2 px-4 border border-red-500 hover:border-transparent rounded">Reset Filter</button>
                                
                                <a href="{{ route('users.export', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="ml-4 bg-green-500 hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">Export to Excel</a>
                    
                                
                            </div>
                        </form>
                    </div>
                    </div>
                    

                    @if (isset($message))
                        <p class= "text-red-600 text-lg text-center py-6   ">{{ $message }}  </p>
                    @endif

                    <div class="p-6 relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table id="users-table" style="padding: 15px"
                            class=" w-full text-sm text-left rtl:text-right text-gray-500 ">
                            <thead class="border-b table-header">
                                <tr>

                                    <th scope="col" class="px-6 py-3">
                                        User Name
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        email
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        phone
                                    </th>
                                    {{-- <th scope="col" class="px-6 py-3">
                                        profile 
                                    </th> --}}
                                    <th scope="col" class="px-6 py-3">
                                        Added date
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Last Login
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Login status
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Change status
                                    </th>
                                   
                                    <th scope="col" class="px-6 py-3">
                                        Edit password
                                    </th>

                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($users))
                                    @foreach ($users as $user)
                                        <tr
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">

                                            <td class="px-4 py-2">
                                                {{ $user->username }}
                                            </td>
                                            <td class="px-4 py-2">
                                                
                                                @php
                                                $email = $user->email;
                                                $midpoint = ceil(strlen($email) / 2); // Find the midpoint
                                                $firstHalf = substr($email, 0, $midpoint); // First half of the email
                                                $secondHalf = substr($email, $midpoint); // Second half of the email
                                            @endphp

                                               {{ $firstHalf }}<br>
                                               {{ $secondHalf }}
                                            </td>
                                            <td class="px-4 py-2">
                                                {{ $user->phone }}
                                            </td>

                                            {{-- <td class="px-4 py-2">
                                 
                                    @if ($user->profileimage)
                                    <img height="100px" width="120px"class="rounded-full"  src={{'/storage/'.$user->profileimage}} alt="{{ $user->name }}">
                                    @endif
                                    
                                </td> --}}
                                            <td class="px-4 py-2">
                                                {{ date_format($user->created_at, 'Y/m/d ') }}
                                            </td>
                                            <td class="px-4 py-2">
                                             @if($user->last_login_at)
                                             {{ $user->last_login_at->timezone('Asia/Karachi')->format('m/d/Y h:i A') }}
                                            
                                               
                                             @endif
                                            </td>
                                            <td class="px-4 py-2">
                                                @if ($user->is_online == 0)
                                                   <span >Not logged In</span> 
                                                @else
                                                   <span style="color: green" >logged in</span> 
                                                @endif
                                                
                                            </td>
                                            <td class="px-4 py-2">
                                                <form id="updateStatusForm" data-membership-id="{{ $user->id }}">
                                                    @csrf
                                                    <select name="status" required>
                                                        <option value="true"
                                                            {{ $user->status == 'true' ? 'selected' : '' }}>Active
                                                        </option>
                                                        <option value="false"
                                                            {{ $user->status == 'false' ? 'selected' : '' }}>
                                                            Inactive
                                                        </option>
                                                    </select>
                                                </form>
                                                <div id="statusMessage-{{ $user->id }}"></div>

                                            </td>


                                            <td class="  px-4 py-2 text-center">
                                                <a href="{{ route('resetuser.password', $user->id) }}"> <span
                                                        class="material-symbols-outlined">edit_square</span></a>
                                            </td>

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



    <script>
        function resetFilters() {
            document.getElementById('filterForm').reset(); // Reset the form fields
            window.location.href = "{{ route('allusers') }}"; // Redirect to the original route
        }
    </script>

    <script>
        $(document).ready(function() {
            var table = $('#users-table').DataTable({
                "pagingType": "full_numbers",
                "searching": true,
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search users...",
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
                        url: '/users/' + membershipId + '/status',
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
