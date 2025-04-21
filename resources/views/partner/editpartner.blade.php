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
            {{ __('Add Video') }}
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

                    <form action="{{ route('partnerupdate', ['id' => $youtubevideo->id]) }}" method="POST" enctype="multipart/form-data">

                        @csrf
                        
                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 font-medium mb-2">Partner Title</label>
                            <input type="text" id="title" name="title" value={{$youtubevideo->title}}
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror" required>
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="image" class="block text-gray-700 font-medium mb-2">logo Image</label>
                            <input type="file" id="image" name="logo" 
                                class="w-full px-4 py-2 border  rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('logo') border-red-500 @enderror"
                                >
                            @error('logo')
                                <p style="color: red" class=" text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <div class="py-4 px-4">
                                @if ($youtubevideo->logo)
                                    <img height="100px" width="120px"class="rounded-full"
                                        src={{  asset('/storage').'/'.$youtubevideo->logo }} alt="{{ $youtubevideo->logo }}">
                                @endif
                            </div>
                        </div>


                    
                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="buttonuser">
                              Edit Partner
                            </button>
                        </div>
                    </form>


                </div>


            </div>
        </div>
    </div>

    <script>
        document.getElementById('title').addEventListener('input', function () {
    var value = parseInt(this.value);
    if (value < 0) {
        this.value = 0;
    } else if (value > 100) {
        this.value = 100;
    }
   });
    </script>

</x-app-layout>
