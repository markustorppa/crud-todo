@include('header')

  <div class="wrapper">

    <div class="flex w-full content-center justify-center">

      <div class="w-3/5 shadow-md mt-12 p-8">

        @if (session()->has('message'))
          <div class="bg-green-400 p-4 mb-2 text-white">
            {{ session()->get('message') }}
          </div>
        @endif

          <div class="flex mb-2">

            <h1 class="text-xl font-bold mb-4">Tehtävälistasi:</h1>

            <div class="flex-grow"></div>

            <p class="self-center mr-3">Olet kirjautunut sisään käyttäjänä <b>{{ Auth::user()->username }}</b></p>

            <form id="logout_form" method="POST" action="{{ route('logout') }}" class="block">
              @csrf

              <button type="submit" class="bg-gray-400 p-3">Kirjaudu ulos</a>

            </form>

          </div>

          <hr>

        <div id="todoapp">

          <todo-component></todo-component>

        </div>

      </div>

    </div>

    <div class="push"></div>

  </div>

  @include('footer')
