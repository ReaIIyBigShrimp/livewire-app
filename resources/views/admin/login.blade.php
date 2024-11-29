</html>

@extends('layouts.app')

@section('title')
    Login
@endsection

@section('content')
    <div class="container">
        <div class="col-md-3 mx-auto">
            <h2>Admin Login</h2>
            <form method="POST" action="{{ url('admin/login') }}">
                @csrf
                <div class="form-group mb-3">
                    <label for="email">Email:</label>
                    <input type="email" name="email" class="form-control" id="email" required>
                </div>
                <div class="form-group mb-3">
                    <label for="password">Password:</label>
                    <input type="password" name="password" class="form-control" id="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>
@endsection
