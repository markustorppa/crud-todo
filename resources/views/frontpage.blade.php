@include('header')

  <div class="wrapper">

    <div class="flex w-full content-center justify-center">

      <div class="w-1/2 shadow-md mt-12 p-8">

        <h1 class="text-xl font-bold mb-4">Kirjaudu sisään {{ env('APP_NAME') }}-sovellukseen</h1>

        @if (session()->has('message'))
          <div class="bg-yellow-400 p-4 text-black mb-2">
            {{ session()->get('message') }}
          </div>
        @endif

        <div class="grid grid-cols-2">

        <div class="login-form">

          <form method="POST" action="{{ route('login') }}">
              @csrf

              <div class="pb-4">
                  <label for="email" class="">{{ __('Käyttäjänimesi:') }}</label>

                  <div class="">
                      <input id="username" type="text" class="border border-solid border-black" name="username" value="{{ old('username') }}" required autofocus>

                      @error('username')
                          <span class="" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror

                  </div>
              </div>

              <div class="pb-4">
                  <label for="password" class="">{{ __('Salasana:') }}</label>

                  <div class="">
                      <input id="password" type="password" class="border border-solid border-black" name="password" required autocomplete="current-password">

                      @error('password')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                  </div>
              </div>

              <div class="">
                  <div class="">
                      <button type="submit" class="p-3 bg-blue-400 text-white">
                          {{ __('Kirjaudu sisään') }}
                      </button>

                  </div>
              </div>
          </form>

        </div>

        <div id="register_form" class="register-form">

          <h2 class="font-bold mb-4">Luo uusi käyttäjä</h2>

          <form method="POST" action="{{ route('register') }}">
              @csrf

              <div class="pb-4">
                  <label for="register_username" class="">{{ __('Käyttäjänimesi:') }}</label>

                  <div class="">
                      <input id="register_username" type="text" class="border border-solid border-black" name="register_username" value="{{ old('register_username') }}" required autofocus>

                      @error('register_username')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                  </div>
              </div>

              <div class="pb-4">
                  <label for="register_password" class="">{{ __('Salasana') }}</label>

                  <div class="">
                      <input id="register_password" type="password" class="border border-solid border-black" name="register_password" required>

                      @error('register_password')
                          <span class="" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                  </div>
              </div>

              <div class="pb-4">
                  <label for="register-password-confirm" class="">{{ __('Vahvista salasana') }}</label>

                  <div class="">
                      <input id="register-password-confirm" type="password" class="border border-solid border-black" name="register_password_confirmation" required>
                  </div>
              </div>

              <div class="">
                  <div class="">
                      <button type="submit" class="p-3 bg-green-500 text-white">
                          {{ __('Luo käyttäjä') }}
                      </button>
                  </div>
              </div>
          </form>

        </div>

      </div>

      </div>

    </div>

    <div class="push"></div>

  </div>

  @include('footer')
