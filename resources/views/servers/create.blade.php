<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Server Create') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('servers.store') }}">
                        @csrf
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Title</span>
                                <input type="text" name="name"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" />
                            </label>
                            @error('name')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">IP</span>
                                <input type="text" name="ip"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" />
                            </label>
                            @error('ip')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Login</span>
                                <input type="text" name="login"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" />
                            </label>
                            @error('login')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Password</span>
                                <input type="text" name="password"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" />
                            </label>
                            @error('password')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Type</span>
                                <input type="text" name="type"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" />
                            </label>
                            @error('type')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <x-primary-button type="submit">
                            Add
                        </x-primary-button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
