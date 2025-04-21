<style>
    .buttonuser {
        background: purple !important;
        padding: 10px;
        border-radius: 10px;
        color: beige;
        margin: 14px;
        width: 115px;
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
            {{ __('Stories') }}
        </h2>
    </x-slot>



    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <a type="button" class="buttonuser" href="{{ route('addnew.story') }}">Add Story</a>

                <div class=" text-gray-900">

                    @if (isset($message))
                        <p class= "text-red-600 text-lg text-center py-6   ">{{ $message }}  </p>
                    @endif


                    <div class="p-6 relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table id="users-table" style="padding: 15px"
                            class=" w-full text-sm text-left rtl:text-right text-gray-500 ">
                            <thead class="border-b table-header ">
                                <tr>
                                    <th scope="col" class="px-6  py-3">
                                        title
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Duration
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Views
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Pg
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Reviews
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Audio
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Image
                                    </th>
                                    <!--<th scope="col" class="px-6 py-3">-->
                                    <!--    Video Thumbnail-->
                                    <!--</th>-->
                                    <th scope="col" class="px-6 py-3">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                                @if (isset($kahanis))
                                    @foreach ($kahanis as $kahani)
                                        <tr
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $kahani->title }}
                                            </th>
                                            <td class="px-6 py-4">
                                                {{ $kahani->duration }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $kahani->views }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $kahani->pg }}
                                            </td>
                                            <td class="px-6 py-4 ">
                                                <a href="{{ route('allreviews.find', $kahani->kahani_id) }}">
                                                    <p style="color: rgb(69, 99, 231);text-decoration: underline;">
                                                        {{ $kahani->number_of_reviews }}</p>
                                                </a>

                                            </td>
                                            <td class="px-6 py-4">

                                                @if ($kahani->audio)
                                                    <audio controls
                                                        src={{ asset('/storage') . '/' . $kahani->audio }}></audio>
                                                @endif

                                            </td>

                                            <td class="">
                                                @if ($kahani->image)
                                                <a href="{{ asset('/storage') . '/' . $kahani->image }}"
                                                    target="_blank">
                                                    <img height="100px" width="120px"class="rounded-full"
                                                        src={{ asset('/storage') . '/' . $kahani->image }}
                                                        alt="{{ $kahani->title }}">
                                                    </a>
                                                @endif
                                            </td>

                                            <!--<td class="px-6 py-4">-->

                                            <!--    @if ($kahani->thumbnail)-->
                                            <!--        <img height="120px" width="140px"-->
                                            <!--            src={{ asset('/storage') . '/' . $kahani->thumbnail }}-->
                                            <!--            alt="{{ $kahani->title }}">-->
                                            <!--    @endif-->

                                            <!--</td>-->
                                            <td class="px-6 py-4 text-center">
                                                <a href="{{ route('kahani.find', $kahani->kahani_id) }}">
                                                    <span class="material-symbols-outlined">edit_square</span>
                                                </a>

                                                <a href="#"
                                                    onclick="event.preventDefault(); confirmDelete({{ $kahani->kahani_id }});">
                                                    <span class="material-symbols-outlined">
                                                        delete
                                                    </span>
                                                </a>

                                                <form id="delete-form-{{ $kahani->kahani_id }}"
                                                    action="{{ route('kahani.delete', $kahani->kahani_id) }}"
                                                    method="POST" style="display: none;">
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
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this kahani?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }

        $(document).ready(function() {
            $('#users-table').DataTable({
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
