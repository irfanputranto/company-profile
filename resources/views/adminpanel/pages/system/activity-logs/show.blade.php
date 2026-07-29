<x-adminpanel::layouts.app title="Detail Activity Log">
    <div class="space-y-5 sm:space-y-6">
        <x-adminpanel::components.page-header title="Detail Activity Log"
            description="Informasi lengkap aktivitas #{{ $activity->id }}.">
            <x-slot:actions>
                <a href="{{ route('system.activity-logs.index') }}" class="btn btn-soft gap-2">
                    <span class="icon-[tabler--arrow-left] size-4.5"></span>Kembali
                </a>
            </x-slot:actions>
        </x-adminpanel::components.page-header>

        <section class="card border border-base-content/10 p-4 shadow-sm sm:p-6">
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                <div>
                    <dt class="text-base-content/55 text-xs font-medium uppercase tracking-wide">Waktu</dt>
                    <dd class="mt-1.5 font-medium">{{ $activity->created_at?->format('d/m/Y H:i:s') }}</dd>
                </div>
                <div>
                    <dt class="text-base-content/55 text-xs font-medium uppercase tracking-wide">Pengguna</dt>
                    <dd class="mt-1.5 font-medium">{{ $activity->causer?->name ?? 'Sistem' }}</dd>
                    <dd class="text-base-content/55 text-xs">{{ $activity->causer?->username ?? 'Tanpa pengguna' }}</dd>
                </div>
                <div>
                    <dt class="text-base-content/55 text-xs font-medium uppercase tracking-wide">Aksi</dt>
                    <dd class="mt-1.5">{{ ['created' => 'Tambah', 'updated' => 'Ubah', 'deleted' => 'Hapus', 'restored' => 'Pulihkan'][$activity->event] ?? $activity->event }}</dd>
                </div>
                <div>
                    <dt class="text-base-content/55 text-xs font-medium uppercase tracking-wide">Data</dt>
                    <dd class="mt-1.5 font-medium">{{ class_basename($activity->subject_type ?? 'Sistem') }} #{{ $activity->subject_id ?? '—' }}</dd>
                </div>
                <div class="sm:col-span-2 xl:col-span-4">
                    <dt class="text-base-content/55 text-xs font-medium uppercase tracking-wide">Keterangan</dt>
                    <dd class="mt-1.5">{{ $activity->description }}</dd>
                </div>
            </dl>
        </section>

        <section class="card overflow-hidden border border-base-content/10 shadow-sm">
            <div class="border-b border-base-content/10 p-4 sm:p-5">
                <h2 class="font-semibold">Nilai Lama dan Baru</h2>
                <p class="text-base-content/55 mt-1 text-sm">Kolom yang berubah ditampilkan secara berdampingan.</p>
            </div>
            <x-adminpanel::components.responsive-table>
                <table class="table min-w-[760px]">
                    <thead>
                        <tr>
                            <th>Atribut</th>
                            <th>Nilai Lama</th>
                            <th>Nilai Baru</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($changedAttributes as $change)
                            <tr>
                                <td class="font-mono text-xs font-semibold">{{ $change['attribute'] }}</td>
                                <td class="max-w-sm whitespace-normal break-words font-mono text-xs">
                                    @if (is_bool($change['old']))
                                        {{ $change['old'] ? 'true' : 'false' }}
                                    @elseif (is_array($change['old']) || is_object($change['old']))
                                        {{ json_encode($change['old'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}
                                    @else
                                        {{ $change['old'] ?? '—' }}
                                    @endif
                                </td>
                                <td class="max-w-sm whitespace-normal break-words font-mono text-xs">
                                    @if (is_bool($change['new']))
                                        {{ $change['new'] ? 'true' : 'false' }}
                                    @elseif (is_array($change['new']) || is_object($change['new']))
                                        {{ json_encode($change['new'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}
                                    @else
                                        {{ $change['new'] ?? '—' }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-10 text-center">Tidak ada perubahan atribut yang tersimpan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-adminpanel::components.responsive-table>
        </section>
    </div>
</x-adminpanel::layouts.app>
