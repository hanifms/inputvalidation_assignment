@php
use Laravel\Fortify\Features;
@endphp

<x-profile-layout>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Two Factor Authentication Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Two Factor Authentication (2FA)</h4>
                </div>
                <div class="card-body">
                    <div class="text-muted mb-4">
                        <p class="fw-medium mb-3">
                            Add an extra layer of security to your account with Two-Factor Authentication (2FA).
                        </p>

                        <div class="alert alert-info">
                            <h5 class="alert-heading fw-bold mb-3">How Two-Factor Authentication Works:</h5>
                            <ol class="mb-0">
                                <li class="mb-3">When you enable 2FA, your account will require two forms of verification to log in:
                                    <ul class="mt-2">
                                        <li>Your regular password</li>
                                        <li>A one-time verification code sent to your email</li>
                                    </ul>
                                </li>
                                <li class="mb-3">Each time you log in:
                                    <ul class="mt-2">
                                        <li>First, enter your email and password as usual</li>
                                        <li>Then, you'll receive a 6-digit code via email</li>
                                        <li>Enter this code to complete your login</li>
                                    </ul>
                                </li>
                                <li>About the verification code:
                                    <ul class="mt-2">
                                        <li>Is valid for 10 minutes only</li>
                                        <li>Can only be used once</li>
                                        <li>A new code is generated for each login attempt</li>
                                    </ul>
                                </li>
                            </ol>
                        </div>

                        <div class="alert alert-warning mt-4">
                            <h5 class="alert-heading fw-bold">Important Note:</h5>
                            <p class="mb-0">
                                Make sure you have access to your email account before enabling 2FA.
                                You'll need it to receive verification codes for all future logins.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Back Button -->
            <div class="mb-4">
                <button onclick="window.history.back()" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
            </div>

            <!-- Profile Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Profile Information</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name', auth()->user()->name) }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email', auth()->user()->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Update Profile
                        </button>
                    </form>
                </div>
            </div>

            <!-- Update Password -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Update Password</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                id="current_password" name="current_password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control"
                                id="password_confirmation" name="password_confirmation">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Update Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- Two Factor Authentication -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Two Factor Authentication</h4>
                </div>
                <div class="card-body">
                    <p class="card-text text-muted mb-4">
                        Add additional security to your account using two factor authentication.
                    </p>

                    @if(! auth()->user()->two_factor_code)
                        <form method="POST" action="{{ route('profile.two-factor.enable') }}">
                            @csrf

                            @if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'))
                                <div class="mb-3">
                                    <label for="enable_2fa_password" class="form-label">Password</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                        id="enable_2fa_password" name="current_password">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <button type="submit" class="btn btn-success">
                                Enable Two-Factor
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('profile.two-factor.disable') }}">
                            @csrf
                            @method('DELETE')

                            @if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'))
                                <div class="mb-3">
                                    <label for="disable_2fa_password" class="form-label">Password</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                        id="disable_2fa_password" name="current_password">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <button type="submit" class="btn btn-danger">
                                Disable Two-Factor
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-profile-layout>
