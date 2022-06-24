@extends('main')

@section('content')
<div class="d-flex align-items-center justify-content-center vh-100">
    <div class="col-md-4">
        <div class="card shadow">
            <!-- <img src="{{asset('img/login-icon.png')}}" alt="login logo" class="w-25" style="position: absolute; margin: auto; left: 0; right: 0;text-align: center; z-index: 1; top: -45px;"> -->
            <div class="card-body mx-4">
                <div class="d-flex justify-content-between">
                    <p class="fw-bold mb-4 my-2 fs-3">Masuk</p>
                    <img src="{{ asset('img/favicon.png') }}" class="img-fluid w-25" alt="">
                </div>
                @if (session('status'))
                <div class="alert alert-danger">
                    {{ session('status') }}
                </div>
                @endif
                <form action="{{ Route('do_login') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="exampleInputNim" class="form-label">Username</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="exampleInputNim" aria-describedby="nimHelp" name="username" placeholder="Masukkan nim" value="{{ old('username') }}">
                        @error('username')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="exampleInputPassword1" name="password" placeholder="Masukkan password" value="{{ old('password') }}">
                        @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <a href="https://sim.unusa.ac.id/front/gate/index.php?page=lupapw" class="text-decoration-none">Lupa password?</a>
                        <a href="https://sim.unusa.ac.id/front/gate/index.php?page=lupamail" class=" text-decoration-none ">Lupa akun?</a>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Login</button>
                </form>
            </div>
            <div class="card-footer text-center">
                Powered by <a href="https://www.unusa.ac.id" class="text-decoration-none" target="_blank" style="color: rgba(55,176,145,0.65);">unusa.ac.id</a>
            </div>
        </div>
    </div>
</div>
@endsection