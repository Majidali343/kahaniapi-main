<style>
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
            {{ __('Admin change Passowrd') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class=" text-gray-900">

                    @if (session('success'))
                        <div class="max-w-3xl mx-auto mt-4">
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                                role="alert">
                                <strong class="font-bold">Success!</strong>
                                <span class="block sm:inline">{{ session('success') }}</span>
                                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                    <svg class="fill-current h-6 w-6 text-green-500" role="button"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Close</title>
                                        <path
                                            d="M14.348 5.652a1 1 0 00-1.414 0L10 8.586 7.066 5.652a1 1 0 00-1.414 1.414l2.934 2.934-2.934 2.934a1 1 0 101.414 1.414l2.934-2.934 2.934 2.934a1 1 0 001.414-1.414l-2.934-2.934 2.934-2.934a1 1 0 000-1.414z" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('passwordadmin.update', $admin->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Form Group -->
                        <div class="max-w-sm mb-5">
                            <label for="current-password" class="block text-sm mb-2 dark:text-white"> Password</label>
                            <input id="current-password" type="password" name="password" required
                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                placeholder="Enter password">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            <!-- Checkbox -->
                            <div class="flex mt-4">
                                <input id="hs-toggle-password-checkbox" type="checkbox"
                                    class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                                    onclick="togglePasswordVisibility('current-password', this)">
                                <label for="hs-toggle-password-checkbox" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">Show Password</label>
                            </div>
                        </div>
                        
                        <div class="max-w-sm mb-5">
                            <label for="confirm-password" class="block text-sm mb-2 dark:text-white">Confirm Password</label>
                            <input id="confirm-password" type="password"  name="password_confirmation" required
                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                placeholder="Enter confirm password">
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            <!-- Checkbox -->
                            <div class="flex mt-4">
                                <input id="toggle-confirm-checkbox" type="checkbox" 
                                    class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                                    onclick="togglePasswordVisibility('confirm-password', this)">
                                <label for="toggle-confirm-checkbox" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">Show Password</label>
                            </div>
                        </div>
                        

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Reset Password') }}
                            </x-primary-button>
                        </div>
                    </form>


                </div>


            </div>
        </div>
    </div>
    <script>
        function togglePasswordVisibility(inputId, checkbox) {
    const passwordField = document.getElementById(inputId);

    if (checkbox.checked) {
        passwordField.type = 'text';
    } else {
        passwordField.type = 'password';
    }
    }
    </script>




</x-app-layout>
