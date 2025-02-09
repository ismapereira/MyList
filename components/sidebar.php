<aside class="w-64 bg-white shadow-md p-6 space-y-6 flex-shrink-0">
    <div class="flex items-center space-x-4">
        <div class="w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center text-xl font-bold">
            <?php echo strtoupper(substr($usuario->nome, 0, 1)); ?>
        </div>
        <div>
            <h2 class="text-lg font-semibold"><?php echo htmlspecialchars($usuario->nome); ?></h2>
            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($usuario->email); ?></p>
        </div>
    </div>

    <nav class="space-y-2">
        <a href="dashboard.php" class="flex items-center space-x-3 text-gray-700 p-2 hover:bg-blue-50 rounded-lg transition <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'bg-blue-50 text-blue-600' : ''; ?>">
            <i data-feather="home" class="w-5 h-5"></i>
            <span>Início</span>
        </a>
        <a href="configuracoes.php" class="flex items-center space-x-3 text-gray-700 p-2 hover:bg-blue-50 rounded-lg transition <?php echo (basename($_SERVER['PHP_SELF']) == 'configuracoes.php') ? 'bg-blue-50 text-blue-600' : ''; ?>">
            <i data-feather="settings" class="w-5 h-5"></i>
            <span>Configurações</span>
        </a>
        <a href="logout.php" class="flex items-center space-x-3 text-red-600 p-2 hover:bg-red-50 rounded-lg transition">
            <i data-feather="log-out" class="w-5 h-5"></i>
            <span>Sair</span>
        </a>
    </nav>
</aside>
