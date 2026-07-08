<div class="overflow-x-auto overflow-y-auto max-h-[500px] rounded-lg border border-gray-200 dark:border-gray-800 relative">
    <table class="w-full table-auto">
      <thead class="text-left">
        <tr>
          <th class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-800 px-5 py-3 text-sm font-semibold text-gray-800 dark:text-white/90 shadow-[0_1px_0_0_#e5e7eb] dark:shadow-[0_1px_0_0_#1f2937]">Timestamp</th>
          <th class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-800 px-5 py-3 text-sm font-semibold text-gray-800 dark:text-white/90 shadow-[0_1px_0_0_#e5e7eb] dark:shadow-[0_1px_0_0_#1f2937]">Aksi</th>
          <th class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-800 px-5 py-3 text-sm font-semibold text-gray-800 dark:text-white/90 shadow-[0_1px_0_0_#e5e7eb] dark:shadow-[0_1px_0_0_#1f2937]">Staf IT (User)</th>
          <th class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-800 px-5 py-3 text-sm font-semibold text-gray-800 dark:text-white/90 shadow-[0_1px_0_0_#e5e7eb] dark:shadow-[0_1px_0_0_#1f2937]">Deskripsi Detail</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
        @forelse($logs as $log)
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition-colors">
          <td class="px-5 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $log->created_at->format('d M Y') }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
          </td>
          <td class="px-5 py-4 whitespace-nowrap">
            <span class="inline-flex items-center text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider 
                {{ $log->action == 'create' ? 'text-emerald-700 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400' : 
                  ($log->action == 'delete' ? 'text-red-700 bg-red-100 dark:bg-red-900/30 dark:text-red-400' : 
                  'text-blue-700 bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400') }}">
                {{ $log->action }}
            </span>
          </td>
          <td class="px-5 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $log->user->name ?? 'System' }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->user->id_karyawan ?? '-' }}</div>
          </td>
          <td class="px-5 py-4">
            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $log->description }}</div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
            <div class="flex flex-col items-center justify-center">
              <svg class="w-10 h-10 mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
              <p class="text-base font-semibold">Belum ada log aktivitas yang terekam.</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
</div>

<div class="mt-5">
  {{ $logs->links() }}
</div>
