document.addEventListener('DOMContentLoaded', () => {
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    const htmlElement = document.documentElement;

    // Função para salvar preferência de tema
    function saveThemePreference(theme) {
        localStorage.setItem('theme-preference', theme);
    }

    // Função para aplicar tema
    function applyTheme(theme) {
        if (theme === 'dark') {
            htmlElement.classList.add('dark');
            darkModeToggle.innerHTML = '<i data-feather="sun" class="w-5 h-5"></i>';
        } else {
            htmlElement.classList.remove('dark');
            darkModeToggle.innerHTML = '<i data-feather="moon" class="w-5 h-5"></i>';
        }
        
        // Recarregar ícones do Feather após mudança
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    // Verificar preferência salva
    const savedTheme = localStorage.getItem('theme-preference');
    
    // Definir tema inicial
    if (savedTheme) {
        applyTheme(savedTheme);
    } else {
        // Verificar preferência do sistema
        const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
        applyTheme(prefersDarkScheme.matches ? 'dark' : 'light');
    }

    // Evento de toggle
    darkModeToggle.addEventListener('click', () => {
        const currentTheme = htmlElement.classList.contains('dark') ? 'light' : 'dark';
        applyTheme(currentTheme);
        saveThemePreference(currentTheme);
    });

    // Observar mudanças no tema do sistema
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem('theme-preference')) {
            applyTheme(e.matches ? 'dark' : 'light');
        }
    });
});
