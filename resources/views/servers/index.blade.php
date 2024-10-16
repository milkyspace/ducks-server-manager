<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Servers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session()->has('status'))
                        <div class="flex justify-center items-center">

                            <p class="ml-3 text-sm font-bold text-green-600">{{ session()->get('status') }}</p>
                        </div>
                    @endif

                    <div class="mt-1 mb-4">

                        <x-primary-button>
                            <a href="{{ route('servers.create') }}">{{ __('Add server') }}</a>
                        </x-primary-button>
                    </div>
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    #
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Title
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    IP
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Type
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Active
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Edit
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Delete
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($servers as $server)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ $server->id }}
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ $server->name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $server->ip }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $server->type }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $server->active }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('servers.edit', $server->id) }}">Edit</a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <form action="{{ route('servers.destroy', $server->id) }}" method="POST"
                                              onsubmit="return confirm('{{ trans('are You Sure ? ') }}');"
                                              style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="submit" class="px-4 py-2 text-white bg-red-700 rounded"
                                                   value="Delete">
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
