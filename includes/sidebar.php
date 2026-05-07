<div data-mobile-sidebar-overlay class="fixed inset-0 z-30 hidden bg-slate-950/45 lg:hidden"></div>
<aside data-mobile-sidebar
    class="fixed inset-y-0 left-0 z-40 flex min-h-screen w-[88vw] max-w-72 -translate-x-full flex-col bg-[linear-gradient(180deg,_#0f2e1d_0%,_#174d2c_55%,_#0f2e1d_100%)] text-white shadow-2xl transition-transform duration-200 lg:sticky lg:top-0 lg:z-auto lg:w-72 lg:max-w-none lg:translate-x-0">
    <div class="flex items-center justify-between border-b border-emerald-900/50 p-5 lg:p-6">
        <div class="flex items-center gap-3">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/95 p-2 shadow-lg">
                <img src="../img/logo_cooperante.png" alt="Cooperante" class="max-h-full max-w-full object-contain">
            </div>
            <div>
                <h1 class="text-lg font-extrabold tracking-wide">Cooperante</h1>
                <p class="text-xs text-emerald-100">Gestão de Frota</p>
            </div>
        </div>
        <button type="button" data-mobile-menu-close
            class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/10 text-emerald-50 lg:hidden">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <nav class="mobile-sidebar-scroll flex-1 space-y-2 p-4">
        <a href="index.php"
            class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-white/10 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-white/10 text-emerald-200' : 'text-emerald-50'; ?>">
            <i class="fa-solid fa-chart-line w-5"></i> Dashboard
        </a>
        <a href="vehicles.php"
            class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-white/10 <?php echo basename($_SERVER['PHP_SELF']) == 'vehicles.php' ? 'bg-white/10 text-emerald-200' : 'text-emerald-50'; ?>">
            <i class="fa-solid fa-car w-5"></i> Veículos
        </a>
        <a href="reports.php"
            class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-white/10 <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'bg-white/10 text-emerald-200' : 'text-emerald-50'; ?>">
            <i class="fa-solid fa-file-invoice-dollar w-5"></i> Custos Gerais
        </a>
        <a href="fuel.php"
            class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-white/10 <?php echo basename($_SERVER['PHP_SELF']) == 'fuel.php' ? 'bg-white/10 text-emerald-200' : 'text-emerald-50'; ?>">
            <i class="fa-solid fa-gas-pump w-5"></i> Abastecimentos
        </a>
        <a href="lava_rapido.php"
            class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-white/10 <?php echo basename($_SERVER['PHP_SELF']) == 'lava_rapido.php' ? 'bg-white/10 text-emerald-200' : 'text-emerald-50'; ?>">
            <i class="fa-solid fa-soap w-5"></i> Lava Rápido
        </a>
        <a href="veloe.php"
            class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-white/10 <?php echo basename($_SERVER['PHP_SELF']) == 'veloe.php' ? 'bg-white/10 text-emerald-200' : 'text-emerald-50'; ?>">
            <i class="fa-solid fa-credit-card w-5"></i> Cartão Veloe
        </a>
        <a href="manutencao.php"
            class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-white/10 <?php echo basename($_SERVER['PHP_SELF']) == 'manutencao.php' ? 'bg-white/10 text-emerald-200' : 'text-emerald-50'; ?>">
            <i class="fa-solid fa-screwdriver-wrench w-5"></i> Manutenção
        </a>
        <a href="import.php"
            class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-white/10 <?php echo basename($_SERVER['PHP_SELF']) == 'import.php' ? 'bg-white/10 text-emerald-200' : 'text-emerald-50'; ?>">
            <i class="fa-solid fa-file-import w-5"></i> Importar
        </a>
        <?php if (function_exists('isAdmin') && isAdmin()): ?>
            <a href="admin_users.php"
                class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-white/10 <?php echo basename($_SERVER['PHP_SELF']) == 'admin_users.php' ? 'bg-white/10 text-emerald-200' : 'text-emerald-50'; ?>">
                <i class="fa-solid fa-users-gear w-5"></i> Usuarios
            </a>
        <?php endif; ?>
    </nav>
    <div class="border-t border-emerald-900/50 p-4">
        <p class="text-center text-xs text-emerald-100/70">v1.0.0 &copy; 2026</p>
    </div>
</aside>