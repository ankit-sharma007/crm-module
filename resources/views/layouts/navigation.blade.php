<nav class="bg-gradient-to-r from-blue-600 to-blue-800 shadow-lg">
    <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="text-2xl font-bold text-white">CRM</div>
            <div class="flex space-x-6 items-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md transition duration-300">Dashboard</a>
                    <a href="{{ route('leads.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md transition duration-300">Leads</a>
                    <a href="{{ route('leads.my-leads') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md transition duration-300">My Leads</a>
                    @can('viewAny', \App\Models\Lead::class)
                        <a href="{{ route('leads.activities') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md transition duration-300">Activities</a>
                    @endcan
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-white hover:bg-red-600 px-3 py-2 rounded-md transition duration-300">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md transition duration-300">Login</a>
                    <a href="{{ route('register') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md transition duration-300">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
