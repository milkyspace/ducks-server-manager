<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Server Edit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('servers.update',$server->id) }}">
                        @csrf
                        @method('put')
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Title</span>
                                <input type="text" name="name"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" value="{{old('name',$server->name)}}" />
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
                                       placeholder="" value="{{old('ip',$server->ip)}}" />
                            </label>
                            @error('ip')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Api Path</span>
                                <input type="text" name="api_path"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" value="{{old('api_path',$server->api_path)}}" />
                            </label>
                            @error('api_path')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Login</span>
                                <input type="text" name="login"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" value="{{old('login',$server->login)}}" />
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
                                       placeholder="" value="{{old('password',$server->password)}}" />
                            </label>
                            @error('password')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Active</span>
                                <input type="checkbox" name="active"
                                       class="block mt-1 rounded-md"
                                       {{$server->active === 1 ? 'checked' : ''}} />
                            </label>
                            @error('active')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <x-primary-button type="submit">
                            Update
                        </x-primary-button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
