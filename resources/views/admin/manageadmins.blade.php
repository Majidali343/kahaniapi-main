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
    .table-header{
    background: #efefef;
    height:40px;

    

}
</style>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Orders') }}
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
            <strong class="font-bold">Message !</strong>
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

                <a type="button" class="buttonuser" href="{{ route('admin.subadmin') }}">Add Subadmin</a> 


                <div class=" text-gray-900">

                    @if (isset($message))
                        <p class= "text-red-600 text-lg text-center py-6   ">{{ $message }}  </p>
                    @endif


                    <div class="p-6 relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table id="users-table" style="padding: 15px"
                            class=" w-full text-sm text-left rtl:text-right text-gray-500 ">
                            <thead class="border-b table-header" style=" ; height:40px ">
                                <tr >
                                    <th scope="col" class="px-3  py-3">
                                        Name
                                    </th>
                                    <th scope="col" class="px-3 py-3">
                                        email
                                    </th>
                                    <th scope="col" class="px-3 py-3">
                                       creation date
                                    </th>
                                    <th scope="col" class="px-3 py-3">
                                        Action
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Edit password
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($admins))

                                    @foreach ($admins as $admin)
                                        <tr
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $admin->name }}
                                            </th>
                                            <td class="px-6 py-4">
                                                {{ $admin->email }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $admin->created_at }}
                                            </td>
                                          
                                            <td class="px-6 py-4">
                                                {{-- <a href="{{ route('package.find', $admin->id) }}">
                                                    <span class="material-symbols-outlined">edit_square</span>
                                                </a> --}}

                                                <a href="#"
                                                    onclick="event.preventDefault(); confirmDelete({{ $admin->id }});">
                                                    <span class="material-symbols-outlined">
                                                        delete
                                                    </span>
                                                </a>

                                                <form id="delete-form-{{ $admin->id }}"
                                                    action="{{ route('admin.delete', $admin->id) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                            
                                            <td class="  px-4 py-2 text-center">
                                                <a href="{{ route('resetadmin.password', $admin->id) }}"> <span
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
          function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this Package?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }

          $(document).ready(function() {
    var table = $('#users-table').DataTable({
        "pagingType": "full_numbers",
        "searching": true,
        "language": {
            "search": "_INPUT_",
            "searchPlaceholder": "Search Packages...",
            "lengthMenu": "Show _MENU_ entries",
        }
    });

    // Attach event listener to the DataTable draw event
    // table.on('draw', function() {
    //     $('select[name="status"]').off('change').on('change', function() {
    //         var form = $(this).closest('form');
    //         var membershipId = form.data('membership-id');
    //         var status = $(this).val();
    //         var token = form.find('input[name="_token"]').val();

    //         $.ajax({
    //             url: '/memberships/' + membershipId + '/status',
    //             type: 'POST',
    //             data: {
    //                 _token: token,
    //                 status: status
    //             },
    //             success: function(response) {
    //                 $('#statusMessage-' + membershipId).html('<span style="color:green;">' +
    //                     response.message + '</span>');
    //             },
    //             error: function(response) {
    //                 $('#statusMessage-' + membershipId).html('<span style="color:red;">' +
    //                     response.responseJSON.message + '</span>');
    //             }
    //         });
    //     });
    // });

    // Trigger the draw event once after initialization to attach the listeners
    table.draw();
});

    </script>
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this kahani?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }

     
    </script>
</x-app-layout>
