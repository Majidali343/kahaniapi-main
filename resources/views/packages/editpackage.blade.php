<style>
    .buttonuser {
        background: purple !important;
        padding: 10px;
        border-radius: 10px;
        color: beige;
        margin: 14px;
        width: 140px;
        text-align: center;
    }

    @media screen and (max-width:600px) {
        .buttonuser {
            padding: 7px;
            margin: 10px;
            width: 90px;
        }
    }
</style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stories') }}
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
                <div class=" text-gray-900">




                    <form action="{{ route('packageupdate', ['id' => $package->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 font-medium mb-2">Name</label>
                            <input type="text" id="title" name="name"  value="{{ $package->name }}"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                 >
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                            <textarea id="description" name="Description" rows="4" value="{{ old('Description') }}"
                                class="w-full px-4 py-2 border  rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('Description') border-red-500 @enderror"
                                 >{{ $package->Description }}</textarea>
                            @error('Description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 font-medium mb-2">Validity</label>
                            <select id="validity" name="validity"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('validity') border-red-500 @enderror">
                                <option default value="">Select Validity</option>
                                <option value="lifetime" {{ $package->validity == 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                                <option value="yearly" {{ $package->validity == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                <option value="quarter" {{ $package->validity == 'quarter' ? 'selected' : '' }}>Quarter</option>
                                <option value="monthly" {{ $package->validity == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            </select>
                            @error('validity')
                            <p style="color: red" class="text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 font-medium mb-2">Permissions</label>
                            <select id="permissions" name="Permissions"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('Permissions') border-red-500 @enderror"
                                >
                                <option default value="">Select Permissions</option>
                                <option value="1" {{ $package->Permissions == '1' ? 'selected' : '' }}>Reading</option>
                                <option value="2" {{ $package->Permissions == '2' ? 'selected' : '' }}>Reading + Listening</option>
                                <option value="3" {{ $package->Permissions == '3' ? 'selected' : '' }}>Reading + Listening + Watching</option>
                            </select>
                            @error('Permissions')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <!-- Hidden input to store the Permissions value when Lifetime is selected -->
                            <input type="hidden" id="hiddenPermissions" value="">
                        </div>
                      
                      

                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 font-medium mb-2">Price</label>
                            <input type="number" id="title" min="0" name="price"
                                value="{{ $package->price }}" placeholder="Enter Amount"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                                 >
                            @error('price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image -->
                        <div class="mb-4">
                            <label for="image" class="block text-gray-700 font-medium mb-2">Package Image</label>
                            <input type="file" id="image" name="image" 
                                class="w-full px-4 py-2 border  rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('image') border-red-500 @enderror"
                                 >
                            @error('image')
                                <p style="color: red" class=" text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <div class="py-4 px-4">
                                @if ($package->image)
                                    <img height="100px" width="120px"class="rounded-full"
                                        src={{  asset('/storage').'/'.$package->image }} alt="{{ $package->name }}">
                                @endif
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="buttonuser">
                                Update Package
                            </button>
                        </div>
                    </form>



                </div>


            </div>
        </div>
    </div>

    <script>
        document.getElementById('validity').addEventListener('change', function () {
            var validityValue = this.value;
            var permissionsSelect = document.getElementById('permissions');
            var hiddenPermissionsInput = document.getElementById('hiddenPermissions');
    
            if (validityValue === 'lifetime') {
                permissionsSelect.value = '2';
                permissionsSelect.setAttribute('disabled', true);
    
                // Set the hidden input to the correct name and value
                hiddenPermissionsInput.value = '2';
                hiddenPermissionsInput.setAttribute('name', 'Permissions');
                
                // Disable the select so it doesn't submit
                permissionsSelect.removeAttribute('name');
            } else {
                permissionsSelect.removeAttribute('disabled');
    
                // Make sure the select has the correct name
                permissionsSelect.setAttribute('name', 'Permissions');
    
                // Disable and clear the hidden input so it doesn't submit
                hiddenPermissionsInput.removeAttribute('name');
                hiddenPermissionsInput.value = '';
            }
        });
    
        window.onload = function() {
            var validityValue = document.getElementById('validity').value;
            var permissionsSelect = document.getElementById('permissions');
            var hiddenPermissionsInput = document.getElementById('hiddenPermissions');
    
            if (validityValue === 'lifetime') {
                permissionsSelect.value = '2';
                permissionsSelect.setAttribute('disabled', true);
    
                // Set the hidden input to the correct name and value
                hiddenPermissionsInput.value = '2';
                hiddenPermissionsInput.setAttribute('name', 'Permissions');
                
                // Disable the select so it doesn't submit
                permissionsSelect.removeAttribute('name');
            } else {
                // Ensure hidden input is not used
                hiddenPermissionsInput.removeAttribute('name');
                hiddenPermissionsInput.value = '';
            }
        };
    </script>

</x-app-layout>
