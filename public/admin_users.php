<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireAdmin();

$pageTitle = 'Administracao de Usuarios';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if ($username === '' || $password === '') {
        $error = 'Preencha usuario e senha.';
    } elseif (!in_array($role, ['admin', 'user'], true)) {
        $error = 'Perfil invalido.';
    } elseif (strlen($password) < 6) {
        $error = 'A senha deve ter pelo menos 6 caracteres.';
    } else {
        $checkStmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $checkStmt->execute([$username]);

        if ($checkStmt->fetch()) {
            $error = 'Ja existe um usuario com esse nome.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
            $insertStmt->execute([$username, $passwordHash, $role]);
            $success = 'Usuario criado com sucesso.';
        }
    }
}

$usersStmt = $pdo->query('SELECT id, username, role, created_at FROM users ORDER BY created_at DESC, username ASC');
$users = $usersStmt->fetchAll();

include '../includes/header.php';
?>

<div class="grid grid-cols-1 gap-6 lg:gap-8 xl:grid-cols-[420px_minmax(0,1fr)]">
    <section class="overflow-hidden rounded-2xl border border-emerald-100 bg-white shadow-sm">
        <div class="border-b border-emerald-100 bg-gradient-to-r from-emerald-50 to-white px-6 py-5">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-emerald-700">Painel Admin</p>
            <h3 class="mt-2 text-2xl font-bold text-gray-800">Adicionar usuario</h3>
            <p class="mt-1 text-sm text-gray-500">Crie acessos para equipe com perfil comum ou administrativo.</p>
        </div>

        <div class="p-6">
            <?php if ($error): ?>
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    <i class="fa-solid fa-circle-check mr-2"></i><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label for="username" class="mb-2 block text-sm font-semibold text-gray-700">Usuario</label>
                    <input id="username" name="username" type="text" required
                        value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="Ex.: joao.silva">
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">Senha</label>
                    <input id="password" name="password" type="password" required minlength="6"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="Minimo de 6 caracteres">
                </div>

                <div>
                    <label for="role" class="mb-2 block text-sm font-semibold text-gray-700">Perfil</label>
                    <select id="role" name="role"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                        <option value="user" <?php echo (($_POST['role'] ?? '') === 'user') ? 'selected' : ''; ?>>Usuario comum</option>
                        <option value="admin" <?php echo (($_POST['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                    </select>
                </div>

                <button type="submit"
                    class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-100 transition hover:bg-blue-700">
                    <i class="fa-solid fa-user-plus mr-2"></i>Criar usuario
                </button>
            </form>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-emerald-100 bg-white shadow-sm">
        <div class="flex flex-col items-start justify-between gap-4 border-b border-emerald-100 px-6 py-5 sm:flex-row sm:items-center">
            <div class="min-w-0">
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-emerald-700">Usuarios</p>
                <h3 class="mt-2 text-2xl font-bold text-gray-800">Acessos cadastrados</h3>
            </div>
            <div class="min-w-[92px] rounded-2xl bg-emerald-50 px-4 py-3 text-center">
                <p class="text-xs uppercase tracking-wide text-emerald-700">Total</p>
                <p class="text-2xl font-bold text-emerald-800"><?php echo count($users); ?></p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="mobile-table-cards min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Usuario</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Perfil</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Criado em</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($users as $user): ?>
                        <tr class="transition-colors hover:bg-emerald-50/50">
                            <td class="px-6 py-4" data-label="Usuario">
                                <div class="font-semibold text-gray-800"><?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="text-xs text-gray-400">ID #<?php echo (int) $user['id']; ?></div>
                            </td>
                            <td class="px-6 py-4" data-label="Perfil">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold <?php echo $user['role'] === 'admin' ? 'bg-amber-100 text-amber-800' : 'bg-sky-100 text-sky-800'; ?>">
                                    <?php echo $user['role'] === 'admin' ? 'Administrador' : 'Usuario'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600" data-label="Criado em">
                                <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (!$users): ?>
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500">Nenhum usuario encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>
