<x-app-layout>
    <div x-data="dashboard" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">

        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 mb-4">
            {{ __('Users') }}
        </h2>

        <div id="user-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @include('partials.user-list', ['users' => $users])
        </div>

        <div class="text-center mt-4">
            <button x-show="hasMore" x-cloak x-on:click="loadMore" class="bg-blue-500 text-white py-2 px-4 rounded">
                Show More
            </button>
        </div>
    </div>

    <script>
      document.addEventListener('alpine:init', () => {
        Alpine.data('dashboard', () => ({
          nextPageUrl: '{{ $users->nextPageUrl() }}',
          hasMore: {{ $users->hasMorePages() ? 'true' : 'false' }},

          init() {
            console.log('Initial nextPageUrl:', this.nextPageUrl);
            console.log('Initial hasMore:', this.hasMore);
          },

          async loadMore() {
            if (!this.hasMore || !this.nextPageUrl) return;

            try {
              const response = await fetch(this.nextPageUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
              });

              if (response.ok) {
                const data = await response.json();
                if (data.html.trim()) {
                  const userList = document.getElementById('user-list');
                  userList.insertAdjacentHTML('beforeend', data.html);

                  this.nextPageUrl = data.nextPageUrl;
                  this.hasMore = data.hasMore;
                } else {
                  this.hasMore = false;
                }
              } else {
                console.error('Failed to fetch data:', response.statusText);
              }
            } catch (error) {
              console.error('Error loading more users:', error);
            }
          }
        }));
      });
    </script>
</x-app-layout>