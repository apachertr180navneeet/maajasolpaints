<div class="wrapper">

    @include('layouts.navbars.auth')

    <div class="main-panel">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif
        {{-- @include('layouts.navbars.navs.auth') --}}
        @yield('content')
        @include('layouts.footer')
    </div>
</div>
