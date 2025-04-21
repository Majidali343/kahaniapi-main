 <style>
     .table-header {
         background: #efefef;
         height: 40px;

     }
 </style>
 <x-app-layout>
     <x-slot name="header">
         <h2 class="font-semibold text-xl text-gray-800 leading-tight">
             <a href="{{ route('allstories') }}">Stories </a> / Reviews
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

                     @if (isset($message))
                         <p class= "text-red-600 text-lg text-center py-6   ">{{ $message }} </p>
                     @endif


                     <div class="p-6 relative overflow-x-auto shadow-md sm:rounded-lg">
                         <table id="reviews-table" style="padding: 15px"
                             class=" w-full text-sm text-left rtl:text-right text-gray-500 ">
                             <thead class="border-b " style="background-color: rgb(233, 229, 229) ; height:40px">
                                 <tr class="border-b table-header ">
                                     <th scope="col" class="px-6  py-3">
                                         username
                                     </th>
                                     <th scope="col" class="px-6 py-3">
                                         email
                                     </th>
                                     <th scope="col" class="px-6 py-3">
                                         comment
                                     </th>
                                     <th scope="col" class="px-6 py-3">
                                         comment date
                                     </th>
                                     <th scope="col" class="px-6 py-3">
                                         status
                                     </th>
                                     <th scope="col" class="px-6 py-3">
                                         Action
                                     </th>
                                 </tr>
                             </thead>
                             <tbody>
                                 @if (isset($reviews))
                                     @foreach ($reviews as $review)
                                         <tr
                                             class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                             <th scope="row"
                                                 class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                 {{ $review->username }}
                                             </th>
                                             <td class="px-6 py-4">
                                                 {{ $review->email }}
                                             </td>
                                             <td class="px-6 py-4">
                                                 {{ $review->comment }}
                                             </td>
                                             <td class="px-6 py-4">
                                                 {{ $review->created_at }}
                                             </td>
                                             <td class="px-6 py-4">
                                                 <form id="updateStatusForm" data-membership-id="{{ $review->id }}">
                                                     @csrf
                                                     <select name="status" required>
                                                         <option value="true"
                                                             {{ $review->review_status == 'true' ? 'selected' : '' }}>Approve
                                                         </option>
                                                         <option value="false"
                                                             {{ $review->review_status == 'false' ? 'selected' : '' }}>Disapprove
                                                         </option>
                                                     </select>
                                                 </form>
                                                 <div id="statusMessage-{{ $review->id }}"></div>
                                             </td>
                                             <td class="px-6 py-4">

                                                 <a href="#"
                                                     onclick="event.preventDefault(); confirmDelete({{ $review->id }});">
                                                     <span class="material-symbols-outlined">
                                                         delete
                                                     </span>
                                                 </a>

                                                 <form id="delete-form-{{ $review->id }}"
                                                     action="{{ route('review.delete', $review->id) }}" method="POST"
                                                     style="display: none;">
                                                     @csrf
                                                     @method('DELETE')
                                                 </form>

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

     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script>
         $(document).ready(function() {
             $('select[name="status"]').on('change', function() {
                 var form = $(this).closest('form');
                 var membershipId = form.data('membership-id');
                 var status = $(this).val();
                 var token = form.find('input[name="_token"]').val();

                 $.ajax({
                     url: '/review/' + membershipId + '/status',
                     type: 'POST',
                     data: {
                         _token: token,
                         status: status
                     },
                     success: function(response) {
                         $('#statusMessage-' + membershipId).html('<span style="color:green;">' +
                             response.message + '</span>');
                     },
                     error: function(response) {
                         $('#statusMessage-' + membershipId).html('<span style="color:red;">' +
                             response.responseJSON.message + '</span>');
                     }
                 });
             });
         });
     </script>

     <script>
         function confirmDelete(id) {
             if (confirm('Are you sure you want to delete this review?')) {
                 document.getElementById('delete-form-' + id).submit();
             }
         }

         $(document).ready(function() {
             $('#reviews-table').DataTable({
                 "pagingType": "full_numbers",
                 "searching": true,
                 "language": {
                     "search": "_INPUT_",
                     "searchPlaceholder": "Search Stories...",
                     "lengthMenu": "Show _MENU_ entries",
                 }
             });
         });
     </script>
 </x-app-layout>
