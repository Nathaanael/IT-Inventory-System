@extends('layouts.fullscreen-layout')

    <div class="min-h-screen flex items-center justify-center bg-white py-10" x-data="{ 
        password: '', 
        confirm_password: '', 
        show1: false, 
        show2: false,
        reqLength() { return this.password.length >= 8; },
        reqUpper() { return /[A-Z]/.test(this.password); },
        reqLower() { return /[a-z]/.test(this.password); },
        reqNumber() { return /[0-9]/.test(this.password); },
        reqSpecial() { return /[!@#$%^&*(),.?\':{}|<>~\\/-]/.test(this.password); }
    }">
        <!-- Card Container -->
        <div class="w-full max-w-[420px] bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] overflow-hidden border border-gray-100">
            
            <!-- Header (Konsisten dengan Login) -->
            <div class="bg-[#5b6ef6] py-6 px-6 flex flex-col items-center justify-center">
                <div class="bg-white/20 p-2 rounded-full mb-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <h2 class="text-white font-medium text-sm">Change Password</h2>
            </div>

            <!-- Body -->
            <div class="px-8 py-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Reset Password</h3>
                <p class="text-sm text-gray-600 mb-1">Almost done. Enter your new password and you're all set.</p>
                <p class="text-xs text-gray-500 mb-6">The password is being reset for account: <strong class="text-gray-700">{{ auth()->user()->name }} ({{ auth()->user()->id_karyawan }})</strong>.</p>
                
                <form action="{{ route('auth.change-password.update') }}" method="POST" class="space-y-4">
                    @csrf

                    @if ($errors->any())
                        <div class="bg-red-50 text-red-500 text-xs p-3 rounded-lg mb-2">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="bg-orange-50 text-orange-600 text-xs p-3 rounded-lg mb-2">
                            {{ session('warning') }}
                        </div>
                    @endif
                    
                    @if(auth()->user()->password !== null)
                    <!-- Current Password -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="text-xs font-medium text-gray-700">
                                Current Password <span class="text-red-500">*</span>
                            </label>
                        </div>
                        <div class="relative">
                            <input type="password" name="current_password" placeholder="Enter current password" 
                                class="w-full bg-[#f0f4ff] border border-transparent text-gray-700 text-sm rounded-full px-5 py-3 focus:outline-none focus:border-[#5b6ef6] focus:ring-2 focus:ring-[#5b6ef6]/20 transition-all placeholder:text-gray-400" required />
                        </div>
                    </div>
                    @endif
                    
                    <!-- New Password -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="text-xs font-medium text-gray-700">
                                New Password <span class="text-red-500">*</span>
                            </label>
                            <button type="button" @click="show1 = !show1" class="text-xs text-gray-500 hover:text-gray-700 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <span x-text="show1 ? 'Hide Password' : 'Show Password'"></span>
                            </button>
                        </div>
                        <div class="relative">
                            <input x-model="password" :type="show1 ? 'text' : 'password'" id="password" name="password" placeholder="Enter new password" 
                                class="w-full bg-[#f0f4ff] border text-gray-700 text-sm rounded-full px-5 py-3 focus:outline-none transition-all placeholder:text-gray-400" 
                                :class="(password.length > 0 && reqLength()) ? 'border-green-400 focus:ring-2 focus:ring-green-400/20 focus:border-green-400' : 'border-transparent focus:border-[#5b6ef6] focus:ring-2 focus:ring-[#5b6ef6]/20'" required />
                        </div>
                    </div>

                    <!-- Validation Rules -->
                    <div class="text-xs space-y-1.5 mt-2 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-2" :class="reqLength() ? 'text-green-600 font-medium' : 'text-gray-500'">
                            <span x-cloak x-show="reqLength()"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg></span>
                            <span x-cloak x-show="!reqLength()"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></span>
                            <span>8 characters minimum</span>
                        </div>
                        <div class="text-gray-700 font-semibold mt-2 mb-1">Must meet two of the following requirements:</div>
                        <div class="flex items-center gap-2" :class="reqUpper() ? 'text-green-600 font-medium' : 'text-gray-500'">
                            <span x-cloak x-show="reqUpper()"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg></span>
                            <span x-cloak x-show="!reqUpper()"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></span>
                            <span>One uppercase letter</span>
                        </div>
                        <div class="flex items-center gap-2" :class="reqLower() ? 'text-green-600 font-medium' : 'text-gray-500'">
                            <span x-cloak x-show="reqLower()"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg></span>
                            <span x-cloak x-show="!reqLower()"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></span>
                            <span>One lowercase letter</span>
                        </div>
                        <div class="flex items-center gap-2" :class="reqNumber() ? 'text-green-600 font-medium' : 'text-gray-500'">
                            <span x-cloak x-show="reqNumber()"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg></span>
                            <span x-cloak x-show="!reqNumber()"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></span>
                            <span>One number</span>
                        </div>
                        <div class="flex items-center gap-2" :class="reqSpecial() ? 'text-green-600 font-medium' : 'text-gray-400'">
                            <span x-cloak x-show="reqSpecial()"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg></span>
                            <span x-cloak x-show="!reqSpecial()">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"></circle></svg>
                            </span>
                            <span>One special character (e.g. ~!@#$%^&*)</span>
                        </div>
                    </div>

                    <!-- Confirm New Password -->
                    <div class="pt-3">
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="text-xs font-medium text-gray-700">
                                Confirm New Password <span class="text-red-500">*</span>
                            </label>
                            <button type="button" @click="show2 = !show2" class="text-xs text-gray-500 hover:text-gray-700 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <span x-text="show2 ? 'Hide Password' : 'Show Password'"></span>
                            </button>
                        </div>
                        <div class="relative">
                            <input x-model="confirm_password" :type="show2 ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" 
                                class="w-full bg-[#f0f4ff] border text-gray-700 text-sm rounded-full px-5 py-3 focus:outline-none transition-all placeholder:text-gray-400"
                                :class="(confirm_password.length > 0 && confirm_password === password) ? 'border-green-400 focus:ring-2 focus:ring-green-400/20 focus:border-green-400' : 'border-transparent focus:border-[#5b6ef6] focus:ring-2 focus:ring-[#5b6ef6]/20'" required />
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-[#5b6ef6] hover:bg-[#4a5ce4] text-white text-sm font-medium rounded-full py-3.5 transition-colors shadow-md shadow-[#5b6ef6]/30">
                            Reset Password
                        </button>
                    </div>
                    
                    <p class="text-[11px] text-gray-400 text-center mt-4">Note: You will be automatically logged into your profile after resetting the password.</p>
                </form>
            </div>
        </div>
    </div>
