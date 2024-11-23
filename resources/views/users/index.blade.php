<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>

    <div class="py-12 px-20 grid grid-cols-2 gap-4">
        @foreach ($users as $user)
            <div class="bg-white rounded-lg shadow-[0px 4px 34px rgba(0,0,0,0.06)] 4px 34px rgba(0,0,0,0.25)]">
                <div class="p-6">
                    <div class="flex justify-items-start items-center gap-4">
                        <div class="flex items-center justify-center w-36 mb-2">
                            <img src="{{ $user->photo }}" alt="{{ $user->name }}" class="w-36 h-36 rounded-full">
                        </div>
                        <div class="mr-auto">
                            <h2 class="mb-4 text-xl font-semibold text-black">{{ $user->name }}</h2>
                            <p class="mb-2 text-sm text-black"><strong>{{__('Email')}}:</strong> {{ $user->email }}</p>
                            <p class="mb-2 text-sm text-black"><strong>{{__('Phone')}}:</strong> {{ $user->phone }}</p>
                            <p class="mb-2 text-sm text-black"><strong>{{__('Position')}}:</strong> {{ $user->position}}</p>
                        </div>
                    </div>

                </div>
            </div>
        @endforeach

        <div class="col-span-2">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
