<style>
    .buttonuser{
        background: purple !important;
    padding: 10px;
    border-radius: 10px;
    color: beige;
    margin: 14px;
    width: 140px;
    text-align: center;
    }

    @media screen and (max-width:600px){
        .buttonuser{  
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




                    <form action="{{ route('kahani.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 font-medium mb-2">Title</label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                                required>
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                            <textarea id="description" name="description" rows="4"
                                class="w-full px-4 py-2 border  rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                required>{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div class="mb-4">
                            <label for="Duration" class="block text-gray-700 font-medium mb-2">Duration</label>
                            <input type="text" id="Duration" name="Duration" value="{{ old('Duration') }}"
                                class="w-full px-4 py-2 border  rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('Duration') border-red-500 @enderror"
                                required>
                            @error('Duration')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- pg -->
                        <div class="mb-4">
                            <label for="pg" class="block text-gray-700 font-medium mb-2">pg</label>
                            <select id="pg" name="pg" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('pg') border-red-500 @enderror" required>
                                <option value="">Select pg</option>
                                <option value="3" {{ old('pg') == '3' ? 'selected' : '' }}>PG +3</option>
                                <option value="7" {{ old('pg') == '7' ? 'selected' : '' }}>PG +7</option>
                                <option value="10" {{ old('pg') == '10' ? 'selected' : '' }}>PG +10</option>
                            </select>
                            @error('pg')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        

                        <!-- Audio -->
                        <div class="mb-4">
                            <label for="audio" class="block text-gray-700 font-medium mb-2">Audio File</label>
                            <input type="file" id="audio" name="audio"
                                class="w-full px-4 py-2 border  rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('audio') border-red-500 @enderror"
                                required>
                            @error('audio')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Audio -->
                        <div class="mb-4">
                            <label for="video" class="block text-gray-700 font-medium mb-2">Video File</label>
                            <input type="file" id="video" name="video"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('video') border-red-500 @enderror"
                                accept="video/*" >
                            @error('video')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image -->
                        <div class="mb-4">
                            <label for="image" class="block text-gray-700 font-medium mb-2">Profile picture</label>
                            <input type="file" id="image" name="image"
                                class="w-full px-4 py-2 border  rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('image') border-red-500 @enderror"
                                required>
                            @error('image')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image -->
                        <div class="mb-4">
                            <label for="image" class="block text-gray-700 font-medium mb-2">Video Thumbnail</label>
                            <input type="file" id="image" name="thumbnail"
                                class="w-full px-4 py-2 border  rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('thumbnail') border-red-500 @enderror"
                                >
                            @error('thumbnail')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit"
                                class="buttonuser">
                                Create Kahani
                            </button>
                        </div>
                    </form>



                </div>


            </div>
        </div>
    </div>

</x-app-layout>
