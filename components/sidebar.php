<aside class="w-64 bg-white shadow-md p-6 space-y-6 flex-shrink-0 fixed md:relative top-0 left-0 h-full z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out" id="sidebar">
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

<!-- Botão de toggle para mobile -->
<button id="sidebar-toggle" class="fixed top-4 left-4 z-40 md:hidden p-2 bg-white shadow-md rounded-full w-10 h-10 flex items-center justify-center hover:bg-gray-100 transition-colors duration-200">
    <i data-feather="menu" class="w-5 h-5 text-blue-600"></i>
</button>

<!-- Overlay para mobile -->
<div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black bg-opacity-50 hidden"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    // Função para abrir a sidebar
    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebarOverlay.classList.remove('hidden');
    }

    // Função para fechar a sidebar
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
    }

    // Toggle da sidebar em telas menores
    sidebarToggle.addEventListener('click', openSidebar);
    sidebarOverlay.addEventListener('click', closeSidebar);

    // Ajustar sidebar em mudanças de tamanho de tela
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        }
    });
});
</script>