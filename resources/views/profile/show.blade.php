@php
use Laravel\Fortify\Features;
@endphp

<x-profile-layout>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Back Button -->
            <div class="mb-4">
                <button onclick="window.history.back()" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
            </div>

            <!-- Avatar -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Profile Picture</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            @if(auth()->user()->avatar)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Profile Picture" class="rounded-circle img-fluid mb-3" style="max-width: 150px; max-height: 150px;">
                            @else
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 150px; height: 150px;">
                                    <span class="text-white" style="font-size: 50px;">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Upload New Profile Picture</label>
                                    <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                                        id="avatar" name="avatar" accept="image/*">
                                    <small class="form-text text-muted">Max size 2MB. Recommended size: 300x300px</small>
                                    @error('avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    Update Profile Picture
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
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
                            <label for="nickname" class="form-label">Nickname</label>
                            <input type="text" class="form-control @error('nickname') is-invalid @enderror"
                                id="nickname" name="nickname" value="{{ old('nickname', auth()->user()->nickname) }}">
                            <small class="form-text text-muted">This will be displayed in the top right menu</small>
                            @error('nickname')
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

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror"
                                id="city" name="city" value="{{ old('city', auth()->user()->city) }}">
                            @error('city')
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
            <div class="card mb-4">
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

            <!-- Delete Account -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Delete Account</h4>
                </div>
                <div class="card-body">
                    <p class="card-text text-muted mb-4">
                        Once your account is deleted, all of its resources and data will be permanently deleted.
                    </p>

                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        Delete Account
                    </button>

                    <!-- Delete Account Modal -->
                    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="deleteAccountModalLabel">Delete Account</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{ route('profile.destroy') }}">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete your account? This action cannot be undone.</p>
                                        <div class="mb-3">
                                            <label for="delete_password" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="delete_password" name="password" required>
                                            <small class="form-text text-muted">Please enter your password to confirm.</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Delete Account</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-profile-layout>
