@foreach ($users as $user)
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center gap-4">
            <img src="{{ $user->photo }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full">
            <div>
                <h2 class="text-lg font-semibold">{{ $user->name }}</h2>
                <p class="text-sm"><strong>Email:</strong> {{ $user->email }}</p>
                <p class="text-sm"><strong>Phone:</strong> {{ $user->phone }}</p>
                <p class="text-sm"><strong>Position:</strong> {{ $user->position }}</p>
            </div>
        </div>
    </div>
@endforeach