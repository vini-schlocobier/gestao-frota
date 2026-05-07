<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (login($username, $password, $pdo)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Usuário ou senha inválidos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gestão de Frota</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Manrope', sans-serif; }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-[radial-gradient(circle_at_top,_#dff6e6_0%,_#f7fbf8_38%,_#123120_100%)] p-6">
    <div class="bg-white/95 backdrop-blur p-8 rounded-[28px] shadow-2xl w-full max-w-md border border-emerald-100">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-emerald-50 rounded-[24px] mb-4 p-4 shadow-lg shadow-emerald-100">
                <img src="../img/logo_cooperante.png" alt="Cooperante" class="max-h-full max-w-full object-contain">
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Acesse sua conta</h1>
            <p class="text-gray-500">Sistema de Gestão de Frota Cooperante</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-6 text-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Usuário</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input type="text" name="username" required
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                        placeholder="admin">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" required
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                        placeholder="••••••••">
                </div>
            </div>

            <button type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 rounded-lg transition-colors shadow-lg shadow-emerald-200">
                Entrar
            </button>
        </form>
    </div>
</body>

</html>
