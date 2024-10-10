<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('tasks')" :active="request()->routeIs('tasks')">
                        Tasks
                    </x-nav-link>

                    @if ($is_admin)
                    <x-nav-link :href="route('tasks.add')" :active="request()->routeIs('tasks.add')">
                        Add Task
                    </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ count($data) > 0 ? 'Edit' : 'Add' }} Tasks
        </h2>
    </x-slot>

    <div style="margin-left: 5rem; margin-top: 5rem;">
        <form action="{{ route('tasks.edit', $data['id']) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <label for="title">Title</label> <br>
            <input type="text" name="title" id="title" placeholder="Enter Title" value="{{ count($data) > 0 ? $data['title'] : old('title') }}">
            <p style="color: red;">
                @if ($errors->has('title'))
                {{$errors->first('title')}}
                @endif
            </p>

            <br>

            <label for="descripiton">Description</label> <br>
            <textarea name="descripiton" id="descripiton">{{ count($data) > 0 ? $data['descripiton'] : old('descripiton') }}</textarea>
            <p style="color: red;">
                @if ($errors->has('descripiton'))
                {{$errors->first('descripiton')}}
                @endif
            </p>
            <br>

            <label for="due_date">Due date</label> <br>
            <input type="date" name="due_date" id="due_date" placeholder="Enter due date" min="<?= date('Y-m-d') ?>" value="{{ count($data) > 0 ? $data['due_date'] : old('due_date') }}">
            <p style="color: red;">
                @if ($errors->has('due_date'))
                {{$errors->first('due_date')}}
                @endif
            </p>
            <br>

            <label for="file">File</label> <br>
            <input type="file" name="file" id="file" placeholder="add file">
            <p style="color: red;">
                @if ($errors->has('file'))
                {{$errors->first('file')}}
                @endif
            </p>
            <br>

            <label for="assign_to">Assign to</label> <br>
            <select name="assign_to" id="assign_to">
                <option value="">-- Select User --</option>
                @foreach ($users as $singleUser)
                <option value="{{ $singleUser['id'] }}" @selected($singleUser['id']==(count($data)> 0 ? $data['user_id'] : old('assign_to')))>{{ $singleUser['name'] }}</option>
                @endforeach
            </select>
            <p style="color: red;">
                @if ($errors->has('assign_to'))
                {{$errors->first('assign_to')}}
                @endif
            </p>
            <br>
            <br>
            <br>

            <button type="submit">Submit</button>
        </form>
    </div>
</x-app-layout>

<style>
    table,
    td,
    th {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>

<script>

</script>