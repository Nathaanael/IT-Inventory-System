@extends('layouts.fullscreen-layout')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-white">
        <!-- Card Container -->
        <div class="w-full max-w-[360px] bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] overflow-hidden border border-gray-100">
            
            <!-- Header -->
            <div class="bg-[#5b6ef6] py-6 px-6 flex items-center justify-center gap-3">
                <div class="bg-white/20 p-2 rounded-full">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                    </svg>
                </div>
                <h2 class="text-white font-semibold text-lg tracking-wide">Login VAULT</h2>
            </div>

            <!-- Body -->
            <div class="px-8 py-8">
                <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="bg-red-50 text-red-500 text-xs p-3 rounded-lg mb-4">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <!-- User ID / Username -->
                    <div>
                        <label for="username_ad" class="block text-xs font-medium text-gray-700 mb-1.5">Username AD</label>
                        <input type="text" id="username_ad" name="username_ad" value="{{ old('username_ad') }}" placeholder="Contoh: nathanael.prasetyo" class="w-full bg-[#f0f4ff] border-transparent text-gray-700 text-sm rounded-full px-5 py-3 focus:border-[#5b6ef6] focus:ring-2 focus:ring-[#5b6ef6]/20 focus:outline-none transition-all placeholder:text-gray-400" required />
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="password" class="block text-xs font-medium text-gray-700">Password</label>
                        </div>
                        <div class="relative" x-data="{ show: false }">
                            <input :type="show ? 'text' : 'password'" id="password" name="password" placeholder="Masukkan password" class="w-full bg-[#f0f4ff] border-transparent text-gray-700 text-sm rounded-full pl-5 pr-12 py-3 focus:border-[#5b6ef6] focus:ring-2 focus:ring-[#5b6ef6]/20 focus:outline-none transition-all placeholder:text-gray-400" required />
                            <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                <svg x-cloak x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-cloak x-show="show" class="w-4 h-4 hidden" :class="{'hidden': !show }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Button -->
                    <button type="submit" class="w-full bg-[#5b6ef6] hover:bg-[#4a5ce4] text-white text-sm font-medium rounded-full py-3.5 transition-colors mt-6 shadow-md shadow-[#5b6ef6]/30">
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

            
            <!-- Header -->
            <div class="bg-[#5b6ef6] py-6 px-6 flex items-center justify-center gap-3">
                <div class="bg-white/20 p-2 rounded-full">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                    </svg>
                </div>
                <h2 class="text-white font-semibold text-lg tracking-wide">Login VAULT</h2>
            </div>

            <!-- Body -->
            <div class="px-8 py-8">
                <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="bg-red-50 text-red-500 text-xs p-3 rounded-lg mb-4">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <!-- User ID / Username -->
                    <div>
                        <label for="username_ad" class="block text-xs font-medium text-gray-700 mb-1.5">Username AD</label>
                        <input type="text" id="username_ad" name="username_ad" value="{{ old('username_ad') }}" placeholder="Contoh: nathanael.prasetyo" class="w-full bg-[#f0f4ff] border-transparent text-gray-700 text-sm rounded-full px-5 py-3 focus:border-[#5b6ef6] focus:ring-2 focus:ring-[#5b6ef6]/20 focus:outline-none transition-all placeholder:text-gray-400" required />
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="password" class="block text-xs font-medium text-gray-700">Password</label>
                        </div>
                        <div class="relative" x-data="{ show: false }">
                            <input :type="show ? 'text' : 'password'" id="password" name="password" placeholder="Masukkan password" class="w-full bg-[#f0f4ff] border-transparent text-gray-700 text-sm rounded-full pl-5 pr-12 py-3 focus:border-[#5b6ef6] focus:ring-2 focus:ring-[#5b6ef6]/20 focus:outline-none transition-all placeholder:text-gray-400" required />
                            <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                <svg x-cloak x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-cloak x-show="show" class="w-4 h-4 hidden" :class="{'hidden': !show }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Button -->
                    <button type="submit" class="w-full bg-[#5b6ef6] hover:bg-[#4a5ce4] text-white text-sm font-medium rounded-full py-3.5 transition-colors mt-6 shadow-md shadow-[#5b6ef6]/30">
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
