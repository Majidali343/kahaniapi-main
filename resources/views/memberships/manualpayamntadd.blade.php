<style>
    .buttonuser{
        background: purple !important;
    padding: 10px;
    border-radius: 10px;
    color: beige;
    margin: 14px;
    width: 165px;
    text-align: center;
    }

    @media screen and (max-width:600px){
        .buttonuser{  
            padding: 7px;
        margin: 10px;
        width: 90px;
        font-size: 12px;
        }
    } 
   
</style>   
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stories') }}
        </h2>
    </x-slot>

@if(session('success'))
    <div class="max-w-3xl mx-auto mt-4">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Close</title>
                    <path d="M14.348 5.652a1 1 0 00-1.414 0L10 8.586 7.066 5.652a1 1 0 00-1.414 1.414l2.934 2.934-2.934 2.934a1 1 0 101.414 1.414l2.934-2.934 2.934 2.934a1 1 0 001.414-1.414l-2.934-2.934 2.934-2.934a1 1 0 000-1.414z" />
                </svg>
            </span>
        </div>
    </div>
@endif



    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class=" text-gray-900">

                    @if (session('message'))
                    <p class="text-red-600 text-lg text-center py-6">{{ session('message') }}  </p>
                   @endif
                    @if (session('success'))
                    <p class="text-green-600 text-lg text-center py-6">{{ session('success') }}  </p>
                   @endif


                   <form action="{{ route('manualpayment.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="user-search" class="block text-gray-700 font-medium mb-2">Search User</label>
                        <input type="text" id="user-search" name="user_search" placeholder="Search user by phone or email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-center" required />
                    
                        <!-- Hidden input to store selected user ID -->
                        <input type="hidden" id="user-id" name="user_id" value="" required />
                    
                        <div id="user-list" class="border rounded-lg mt-2 overflow-auto hidden" style="height: 40vh">
                            @foreach ($users as $user)
                                <div class="user-option px-4 py-2 hover:bg-gray-200 cursor-pointer" data-id="{{ $user->id }}">
                                    {{ $user->email }}  {{$user->phone}}
                                </div>
                            @endforeach
                        </div>
                        
                        @error('user_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="package" class="block text-gray-700 font-medium mb-2">Package</label>
                        <select id="package-select" name="package_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option default value="">Select Package</option>
                        </select>
                        @error('package_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                
                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="buttonuser">Create Membership</button>
                    </div>
                </form>



                </div>


            </div>
        </div>
    </div>

</x-app-layout>

<!-- AJAX to Fetch Packages -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userSearch = document.getElementById('user-search');
        const userList = document.getElementById('user-list');
        const userOptions = document.querySelectorAll('.user-option');
        const userIdInput = document.getElementById('user-id');
        const packageSelect = document.getElementById('package-select');

        // Show user list on focus
        userSearch.addEventListener('focus', function() {
            userList.classList.remove('hidden');
        });

        // Filter users based on input
        userSearch.addEventListener('input', function() {
            const searchValue = userSearch.value.toLowerCase();
            userOptions.forEach(function(option) {
                const phone = option.textContent.toLowerCase();
                if (phone.includes(searchValue)) {
                    option.classList.remove('hidden');
                } else {
                    option.classList.add('hidden');
                }
            });
        });

        // Select user and trigger AJAX for packages
        userOptions.forEach(function(option) {
            option.addEventListener('click', function() {
                userSearch.value = option.textContent;
                userIdInput.value = option.getAttribute('data-id');
                userList.classList.add('hidden');

                // Clear the previous package options
                packageSelect.innerHTML = '<option value="">Select Package</option>';

                // Get suitable packages for the selected user
                const userId = userIdInput.value;
                if (userId) {
                    fetchSuitablePackages(userId);
                }
            });
        });

        // Fetch suitable packages based on user selection
        function fetchSuitablePackages(userId) {
            $.ajax({
                url: '{{ route("get.suitable.packages") }}', 
                type: 'POST',
                data: {
                    user_id: userId,
                    _token: '{{ csrf_token() }}' 
                },
                success: function (data) {
                    if (data.length > 0) {
                        $.each(data, function (index, package) {
                            $('#package-select').append('<option value="' + package.id + '">' + package.name + ' - ' + package.price + '</option>');
                        });
                    } else {
                        $('#package-select').append('<option value="">No packages available</option>');
                    }
                },
                error: function () {
                    alert('No Packages available for this user');
                }
            });
        }

        // Hide the list when clicking outside
        document.addEventListener('click', function(e) {
            if (!userList.contains(e.target) && e.target !== userSearch) {
                userList.classList.add('hidden');
            }
        });
    });
</script>