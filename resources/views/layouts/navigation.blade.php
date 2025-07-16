<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold">CRM</a>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}" class="border-b-2 {{ Route::is('dashboard') ? 'border-indigo-400' : 'border-transparent' }} text-gray-600 hover:text-gray-900 hover:border-indigo-400 px-3 py-2">Dashboard</a>
                    @can('viewAny', \App\Models\Lead::class)
                        <a href="{{ route('leads.index') }}" class="border-b-2 {{ Route::is('leads.index') ? 'border-indigo-400' : 'border-transparent' }} text-gray-600 hover:text-gray-900 hover:border-indigo-400 px-3 py-2">Leads</a>
                        <a href="{{ route('leads.activities') }}" class="border-b-2 {{ Route::is('leads.activities') ? 'border-indigo-400' : 'border-transparent' }} text-gray-600 hover:text-gray-900 hover:border-indigo-400 px-3 py-2">Activities</a>
                    @endcan
                    <a href="{{ route('leads.my-leads') }}" class="border-b-2 {{ Route::is('leads.my-leads') ? 'border-indigo-400' : 'border-transparent' }} text-gray-600 hover:text-gray-900 hover:border-indigo-400 px-3 py-2">My Leads</a>
                </div>
            </div>
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div x-data="{ open: false }" class="ms-3 relative">
                    <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                        {{ Auth::user()->name }}
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute mt-2 w-48 bg-white border rounded shadow-lg">
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Log Out</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
