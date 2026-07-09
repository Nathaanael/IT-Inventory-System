@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 gap-6 relative" x-data="editInventory('{{ addslashes($inventory->password_remote) }}')">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6 shadow-sm">
        
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Edit Data Inventory</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Silakan perbarui formulir di bawah ini untuk mengubah data PC & Remote.</p>
            </div>
            <a href="{{ route('inventory.index') }}" class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-brand-500 dark:text-gray-400 dark:hover:text-brand-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('inventory.update', $inventory->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section: Profile User -->
            <div class="mb-2">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Profil User</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Informasi data diri pengguna</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/50 dark:bg-gray-800/20 p-5 rounded-xl border border-gray-100 dark:border-gray-800 mb-8"> 
                
                <!-- ID Karyawan -->
                <div>
                    <label for="id_karyawan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ID Karyawan</label>
                    <input type="number" id="id_karyawan" name="id_karyawan" value="{{ old('id_karyawan', $inventory->id_karyawan ?? '') }}" placeholder="Contoh: 12345" class="w-full rounded-lg border @error('id_karyawan') border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-brand-500 focus:ring-brand-500 @enderror bg-transparent px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-1 dark:border-gray-700 dark:text-white/90 shadow-sm" />
                    @error('id_karyawan')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Username AD -->
                <div>
                    <label for="username_ad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username AD</label>
                    <input type="text" id="username_ad" name="username_ad" value="{{ old('username_ad', $inventory->username_ad ?? '') }}" placeholder="Contoh: user.name" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-1 dark:border-gray-700 dark:text-white/90 shadow-sm" />
                </div>

                <!-- Nama User -->
                <div>
                    <label for="nama_user" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama User</label>
                    <input type="text" id="nama_user" name="nama_user" value="{{ old('nama_user', $inventory->nama_user) }}" placeholder="Masukkan nama user..." class="w-full rounded-lg border @error('nama_user') border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-brand-500 focus:ring-brand-500 @enderror bg-transparent px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-1 dark:border-gray-700 dark:text-white/90 shadow-sm" required />
                    @error('nama_user')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Departemen -->
                <div>
                    <label for="departemen" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Departemen & Unit</label>
                    <select id="departemen" name="departemen" class="w-full rounded-lg border @error('departemen') border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-brand-500 focus:ring-brand-500 @enderror bg-transparent px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-1 dark:border-gray-700 dark:text-white/90 shadow-sm" required>
                        <option value="" disabled>Pilih departemen...</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ old('departemen', $inventory->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->unit ? $department->unit . ' - ' : '' }}{{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('departemen')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Section: Inventory PC -->
            <div class="mb-2 mt-8">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Informasi Inventory</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Data teknis perangkat dan koneksi</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-brand-50/30 dark:bg-brand-500/5 p-5 rounded-xl border border-brand-100 dark:border-brand-500/20">

                <!-- Nomor Asset PC -->
                <div>
                    <label for="nomor_asset_pc" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nomor Asset PC</label>
                    <input type="text" id="nomor_asset_pc" name="nomor_asset_pc" value="{{ old('nomor_asset_pc', $inventory->nomor_asset_pc ?? '') }}" placeholder="Contoh: PC-IT-001" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-1 dark:border-gray-700 dark:text-white/90 shadow-sm" />
                </div>

                <!-- IP Address -->
                <div>
                    <label for="ip_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">IP Address</label>
                    <div class="relative shadow-sm rounded-lg">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        </div>
                        <input type="text" id="ip_address" name="ip_address" value="{{ old('ip_address', $inventory->ip_address) }}" placeholder="Contoh: 192.168.1.150" class="w-full rounded-lg border @error('ip_address') border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-brand-500 focus:ring-brand-500 @enderror bg-transparent pl-11 pr-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-1 dark:border-gray-700 dark:text-white/90 font-mono" required />
                    </div>
                    @error('ip_address')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Remote -->
                <div>
                    <label for="password_remote" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password Remote</label>
                    <div class="flex gap-2">
                        <div class="relative w-full shadow-sm rounded-lg">
                            <input :type="showPassword ? 'text' : 'password'" id="password_remote" name="password_remote" x-model="password" placeholder="Masukkan password..." class="w-full rounded-lg border @error('password_remote') border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-brand-500 focus:ring-brand-500 @enderror bg-transparent pl-4 pr-11 py-3 text-sm text-gray-800 focus:outline-none focus:ring-1 dark:border-gray-700 dark:text-white/90 font-mono tracking-wider" required />
                            
                            <!-- Toggle Button -->
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-brand-500 focus:outline-none transition-colors" title="Lihat/Sembunyikan">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                        
                        <!-- Generate Button -->
                        <button type="button" @click="generatePassword()" class="flex-shrink-0 flex items-center justify-center gap-2 rounded-lg border border-brand-500 bg-brand-50/50 px-4 py-2 text-sm font-medium text-brand-600 hover:bg-brand-100 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:bg-brand-500/10 dark:text-brand-400 dark:hover:bg-brand-500/20 transition-colors shadow-sm" title="Generate Password Otomatis">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            <span class="hidden sm:inline">Generate</span>
                        </button>
                    </div>

                    <!-- Password Strength Indicator -->
                    <div class="mt-2 flex items-center gap-2 text-xs" x-show="password.length > 0" x-transition.opacity>
                        <span class="text-gray-500 dark:text-gray-400">Kekuatan Sandi:</span>
                        <div class="flex-1 h-1.5 flex gap-1 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700">
                            <div class="h-full rounded-full transition-all duration-300" :class="passwordStrength >= 1 ? passwordStrengthColorBar : 'bg-transparent'" style="width: 33.33%"></div>
                            <div class="h-full rounded-full transition-all duration-300" :class="passwordStrength >= 3 ? passwordStrengthColorBar : 'bg-transparent'" style="width: 33.33%"></div>
                            <div class="h-full rounded-full transition-all duration-300" :class="passwordStrength >= 5 ? passwordStrengthColorBar : 'bg-transparent'" style="width: 33.33%"></div>
                        </div>
                        <span :class="passwordStrengthColorText" class="font-semibold w-16 text-right" x-text="passwordStrengthText"></span>
                    </div>
                    @error('password_remote')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Tambahkan catatan jika diperlukan..." class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-1 dark:border-gray-700 dark:text-white/90 shadow-sm">{{ old('notes', $inventory->notes ?? '') }}</textarea>
                </div>

            </div>

            <!-- Divider -->
            <div class="border-t border-gray-100 dark:border-gray-800"></div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('inventory.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-2 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors shadow-sm">
                    Batal
                </a>
                <button type="submit" class="flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition-colors shadow-theme-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Perbarui Data
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Alpine Logic -->
<script>
    function editInventory(initialPassword) {
        return {
            showPassword: true,
            password: initialPassword,
            
            generatePassword() {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
                let newPassword = '';
                // Generate 12 characters
                for (let i = 0; i < 12; i++) {
                    newPassword += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                
                // Ensure diversity (at least one of each character type)
                newPassword = this.shufflePassword(
                    this.getRandomChar('ABCDEFGHIJKLMNOPQRSTUVWXYZ') +
                    this.getRandomChar('abcdefghijklmnopqrstuvwxyz') +
                    this.getRandomChar('0123456789') +
                    this.getRandomChar('!@#$%^&*') +
                    newPassword.slice(4)
                );

                this.password = newPassword;
                this.showPassword = true;
            },
            
            getRandomChar(str) {
                return str.charAt(Math.floor(Math.random() * str.length));
            },
            
            shufflePassword(str) {
                let array = str.split('');
                for (let i = array.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [array[i], array[j]] = [array[j], array[i]];
                }
                return array.join('');
            },
            
            get passwordStrength() {
                if (this.password.length === 0) return 0;
                let score = 0;
                if (this.password.length > 0) score += 1;
                if (this.password.length >= 8) score += 1;
                if (this.password.length >= 12) score += 1;
                if (/[A-Z]/.test(this.password)) score += 1;
                if (/[0-9]/.test(this.password)) score += 1;
                if (/[^A-Za-z0-9]/.test(this.password)) score += 1;
                return score; // Max 6
            },
            
            get passwordStrengthText() {
                const strength = this.passwordStrength;
                if (strength === 0) return '';
                if (strength <= 2) return 'Lemah';
                if (strength <= 4) return 'Sedang';
                return 'Kuat';
            },
            
            get passwordStrengthColorText() {
                const strength = this.passwordStrength;
                if (strength <= 2) return 'text-red-500';
                if (strength <= 4) return 'text-yellow-500 dark:text-yellow-400';
                return 'text-green-500 dark:text-green-400';
            },
            
            get passwordStrengthColorBar() {
                const strength = this.passwordStrength;
                if (strength <= 2) return 'bg-red-500';
                if (strength <= 4) return 'bg-yellow-500';
                return 'bg-green-500';
            }
        }
    }
</script>
@endsection
