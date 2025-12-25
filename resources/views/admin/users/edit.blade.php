@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit User</h2>
            <p class="mt-1 text-sm text-gray-600">Update user information and settings</p>
        </div>
        <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
            Cancel
        </a>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium">There were errors with your submission:</h3>
                    <ul class="mt-2 text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Full Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <!-- Phone -->
                        <div>
                            <x-input-label for="phone" :value="__('Phone Number')" />
                            <div class="mt-1 flex gap-2">
                                @php
                                    $currentPhone = old('phone', $user->phone ?? '');
                                    $currentCountryCode = old('country_code', '+1');

                                    // Try to extract country code from phone if it starts with +
                                    if ($currentPhone && str_starts_with($currentPhone, '+')) {
                                        $parts = explode(' ', $currentPhone, 2);
                                        if (count($parts) === 2) {
                                            $currentCountryCode = $parts[0];
                                            $currentPhone = $parts[1];
                                        }
                                    }
                                @endphp
                                <select name="country_code" class="w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="+1" {{ $currentCountryCode === '+1' ? 'selected' : '' }}>+1 (US)</option>
                                    <option value="+44" {{ $currentCountryCode === '+44' ? 'selected' : '' }}>+44 (UK)</option>
                                    <option value="+91" {{ $currentCountryCode === '+91' ? 'selected' : '' }}>+91 (IN)</option>
                                    <option value="+971" {{ $currentCountryCode === '+971' ? 'selected' : '' }}>+971 (AE)</option>
                                    <option value="+966" {{ $currentCountryCode === '+966' ? 'selected' : '' }}>+966 (SA)</option>
                                    <option value="+61" {{ $currentCountryCode === '+61' ? 'selected' : '' }}>+61 (AU)</option>
                                    <option value="+86" {{ $currentCountryCode === '+86' ? 'selected' : '' }}>+86 (CN)</option>
                                </select>
                                <input id="phone" name="phone" type="tel" pattern="[0-9]{10,15}" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ $currentPhone }}" placeholder="1234567890" />
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Enter 10-15 digit phone number without spaces or dashes</p>
                            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                        </div>

                        <!-- Country -->
                        <div>
                            <x-input-label for="country" :value="__('Country')" />
                            <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" :value="old('country', $user->country)" />
                            <x-input-error class="mt-2" :messages="$errors->get('country')" />
                        </div>

                        <!-- City -->
                        <div>
                            <x-input-label for="city" :value="__('City')" />
                            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $user->city)" />
                            <x-input-error class="mt-2" :messages="$errors->get('city')" />
                        </div>

                        <!-- Address -->
                        <div>
                            <x-input-label for="address" :value="__('Address')" />
                            <textarea id="address" name="address" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address', $user->address) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                            <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                            <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
                        </div>

                        <!-- Gender -->
                        <div>
                            <x-input-label for="gender" :value="__('Gender')" />
                            <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                        </div>

                        <!-- Bio -->
                        <div>
                            <x-input-label for="bio" :value="__('Bio')" />
                            <textarea id="bio" name="bio" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('bio', $user->bio ?? '') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                            <p class="mt-1 text-sm text-gray-500">Brief description for the user profile</p>
                        </div>
                    </div>
                </div>

                <!-- Password Change -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Change Password</h3>
                        <p class="mt-1 text-sm text-gray-500">Leave blank to keep current password</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- New Password -->
                        <div>
                            <x-input-label for="password" :value="__('New Password')" />
                            <div class="relative mt-1">
                                <x-text-input id="password" name="password" type="password" class="block w-full pr-10" />
                                <button type="button" onclick="togglePassword('password', 'password-eye')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg id="password-eye" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                            <p class="mt-1 text-sm text-gray-500">Minimum 8 characters</p>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <div class="relative mt-1">
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block w-full pr-10" />
                                <button type="button" onclick="togglePassword('password_confirmation', 'password-confirmation-eye')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg id="password-confirmation-eye" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Roles & Permissions -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Role</h3>
                        <p class="mt-1 text-sm text-gray-500">Select user role</p>
                    </div>
                    <div class="p-6 space-y-3">
                        @foreach($roles ?? [] as $roleItem)
                            <label class="flex items-start cursor-pointer">
                                <input type="radio" name="role" value="{{ $roleItem->name }}"
                                    {{ $user->hasRole($roleItem->name) ? 'checked' : '' }}
                                    class="mt-1 border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" required>
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $roleItem->name)) }}</span>
                                    <p class="text-xs text-gray-500">
                                        @if($roleItem->name === 'super_admin')
                                            Full system access and control
                                        @elseif($roleItem->name === 'tutor')
                                            Can create and manage courses
                                        @else
                                            Can enroll in courses
                                        @endif
                                    </p>
                                </div>
                            </label>
                        @endforeach
                        <x-input-error class="mt-2" :messages="$errors->get('role')" />
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Status</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Active Status -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Active Account</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500">User can login and access the system</p>
                        </div>

                        <!-- Email Verified -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="email_verified" value="1"
                                    {{ old('email_verified', $user->email_verified_at) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Email Verified</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500">Mark email as verified</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-6 space-y-3">
                        <x-primary-button class="w-full justify-center">
                            Update User
                        </x-primary-button>
                        <a href="{{ route('admin.users.show', $user) }}" class="block w-full text-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function togglePassword(inputId, eyeId) {
    const passwordInput = document.getElementById(inputId);
    const eyeIcon = document.getElementById(eyeId);

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
        `;
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        `;
    }
}
</script>
@endsection
