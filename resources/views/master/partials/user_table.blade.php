<div class="overflow-x-auto overflow-y-auto max-h-[500px] rounded-lg border border-gray-200 dark:border-gray-800 relative">
    <table class="w-full table-auto">
        <thead class="text-left">
            <tr>
                <th class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-800 px-5 py-3 text-sm font-semibold text-gray-800 dark:text-white/90 shadow-[0_1px_0_0_#e5e7eb] dark:shadow-[0_1px_0_0_#1f2937]">ID Karyawan</th>
                <th class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-800 px-5 py-3 text-sm font-semibold text-gray-800 dark:text-white/90 shadow-[0_1px_0_0_#e5e7eb] dark:shadow-[0_1px_0_0_#1f2937]">Nama Lengkap</th>
                <th class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-800 px-5 py-3 text-sm font-semibold text-gray-800 dark:text-white/90 shadow-[0_1px_0_0_#e5e7eb] dark:shadow-[0_1px_0_0_#1f2937]">Username AD</th>
                <th class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-800 px-5 py-3 text-sm font-semibold text-gray-800 dark:text-white/90 shadow-[0_1px_0_0_#e5e7eb] dark:shadow-[0_1px_0_0_#1f2937]">Role</th>
                <th class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-800 px-5 py-3 text-center text-sm font-semibold text-gray-800 dark:text-white/90 shadow-[0_1px_0_0_#e5e7eb] dark:shadow-[0_1px_0_0_#1f2937]">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition-colors">
                <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">{{ $user->id_karyawan }}</td>
                <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $user->name }}</td>
                <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $user->username_ad }}</td>
                <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold 
                        {{ $user->role === 'Super Admin' ? 'bg-brand-100 text-brand-700 dark:bg-brand-900/30 dark:text-brand-400' : 
                          ($user->role === 'IT Support' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 
                          'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300') }}">
                        {{ $user->role }}
                    </span>
                </td>
                <td class="px-5 py-4 text-center">
                    <div class="flex items-center justify-center gap-3">
                        <button @click="$dispatch('open-modal', { action: 'edit', id: {{ $user->id }}, id_karyawan: '{{ $user->id_karyawan }}', name: '{{ addslashes($user->name) }}', username_ad: '{{ $user->username_ad }}', role: '{{ $user->role }}' })" class="text-blue-500 hover:text-blue-700 transition-colors" title="Edit Data">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        
                        <button @click="$dispatch('open-reset-password-modal', { id: {{ $user->id }}, name: '{{ addslashes($user->name) }}' })" class="text-orange-500 hover:text-orange-700 transition-colors" title="Reset Password Login">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                        </button>
                        
                        <button @click="$dispatch('open-reset-pin-modal', { id: {{ $user->id }}, name: '{{ addslashes($user->name) }}' })" class="text-yellow-500 hover:text-yellow-700 transition-colors" title="Reset Vault PIN">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </button>
                        
                        @if($user->id !== Auth::id())
                        <button @click="$dispatch('open-delete-modal', { id: {{ $user->id }}, role: '{{ $user->role }}' })" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus Data">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-10 h-10 mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <p class="text-base font-semibold">Belum ada data staf IT yang terdaftar.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">
    {{ $users->links() }}
</div>
