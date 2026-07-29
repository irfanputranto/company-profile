<x-adminpanel::layouts.app title="Profil Saya">
    <div class="mx-auto w-full max-w-3xl space-y-6">
        <x-adminpanel::components.page-header title="Profil Saya" description="Informasi akun dan foto profil Anda." />
        <x-adminpanel::components.flash-message />

        <section class="card shadow-base-300/10 overflow-hidden shadow-md">
            <div class="card-body flex flex-col items-center gap-4 border-b p-6 text-center sm:flex-row sm:text-start">
                <x-adminpanel::components.user-avatar size="lg" />
                <div class="min-w-0">
                    <h2 class="line-clamp-2 break-words text-xl font-semibold">{{ auth()->user()?->name ?? 'Pengguna' }}</h2>
                    <p class="text-base-content/60 truncate text-sm">{{ auth()->user()?->email ?? '—' }}</p>
                </div>
            </div>
            <dl class="divide-base-content/10 divide-y">
                <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4"><dt class="text-base-content/60 text-sm">Username</dt><dd class="break-words font-medium sm:col-span-2">{{ auth()->user()?->username ?? '—' }}</dd></div>
                <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4"><dt class="text-base-content/60 text-sm">Role</dt><dd class="break-words font-medium sm:col-span-2">{{ auth()->user()?->getRoleNames()->join(', ') ?: '—' }}</dd></div>
                <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4"><dt class="text-base-content/60 text-sm">Status</dt><dd class="font-medium sm:col-span-2">{{ auth()->user()?->is_active ? 'Aktif' : 'Tidak aktif' }}</dd></div>
            </dl>
        </section>

        <form method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" class="card shadow-base-300/10 shadow-md">
            @csrf
            @method('PATCH')
            <div class="card-body p-5 sm:p-6"><x-adminpanel::components.master.users.photo-upload :user="auth()->user()" /></div>
            <div class="card-footer border-base-content/10 flex flex-wrap items-center justify-between gap-3 border-t p-4 sm:px-6">
                <p class="text-base-content/50 text-xs">Foto baru langsung digunakan di seluruh admin panel.</p>
                <button type="submit" class="btn btn-primary"><span class="icon-[tabler--photo-check] size-5"></span>Simpan foto profil</button>
            </div>
        </form>
    </div>
</x-adminpanel::layouts.app>
