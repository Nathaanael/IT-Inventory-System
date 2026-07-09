<div class="overflow-x-auto overflow-y-auto max-h-[500px] rounded-lg border border-gray-200 dark:border-gray-800 relative">
    <table class="w-full table-auto">
        <thead class="text-left">
            <tr>
                <th class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-800 px-5 py-3 text-left text-sm font-semibold text-gray-800 dark:text-white/90 shadow-[0_1px_0_0_#e5e7eb] dark:shadow-[0_1px_0_0_#1f2937]">Nama Departemen</th>
                <th class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-800 px-5 py-3 text-center text-sm font-semibold text-gray-800 dark:text-white/90 shadow-[0_1px_0_0_#e5e7eb] dark:shadow-[0_1px_0_0_#1f2937]">Unit</th>
                <th class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-800 px-5 py-3 text-center text-sm font-semibold text-gray-800 dark:text-white/90 shadow-[0_1px_0_0_#e5e7eb] dark:shadow-[0_1px_0_0_#1f2937]">Jumlah Inventory</th>
                <th class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-800 px-5 py-3 text-center text-sm font-semibold text-gray-800 dark:text-white/90 shadow-[0_1px_0_0_#e5e7eb] dark:shadow-[0_1px_0_0_#1f2937]">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($departments as $department)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition-colors">
                <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">{{ $department->name }}</td>
                <td class="px-5 py-4 text-sm text-center font-medium text-gray-800 dark:text-white/90">
                    <span class="inline-flex items-center gap-1.5 rounded-md bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 border border-brand-200 dark:border-brand-500/20">
                        {{ $department->unit ?? '-' }}
                    </span>
                </td>
                <td class="px-5 py-4 text-sm text-center text-gray-600 dark:text-gray-400">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                        {{ $department->inventories()->count() }} PC
                    </span>
                </td>
                <td class="px-5 py-4 text-center">
                    <div class="flex items-center justify-center gap-3">
                        <button @click="$dispatch('open-modal', { action: 'edit', id: {{ $department->id }}, name: '{{ addslashes($department->name) }}', unit: '{{ $department->unit }}' })" class="text-blue-500 hover:text-blue-700 transition-colors" title="Edit Data">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        <button @click="$dispatch('open-delete-modal', { id: {{ $department->id }}, hasInventory: {{ $department->inventories()->count() > 0 ? 'true' : 'false' }} })" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus Data">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-10 h-10 mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <p class="text-base font-semibold">Belum ada departemen yang terdaftar.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">
    {{ $departments->links() }}
</div>
