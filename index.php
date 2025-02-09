<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyList - Seu Gerenciador de Listas</title>
    <link href="assets/css/tailwind.min.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
    <script>
        // Capturar e logar erros no console
        window.addEventListener('error', function(event) {
            console.error('Erro não tratado:', {
                message: event.message,
                filename: event.filename,
                lineno: event.lineno,
                colno: event.colno,
                error: event.error
            });
        });

        // Capturar erros de recursos não carregados
        document.addEventListener('DOMContentLoaded', function() {
            var resources = document.querySelectorAll('img, link, script');
            resources.forEach(function(resource) {
                resource.addEventListener('error', function(event) {
                    console.error('Recurso não carregado:', {
                        src: event.target.src || event.target.href,
                        type: event.target.tagName
                    });
                });
            });
        });
    </script>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    <header class="fixed w-full z-50 bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="#" class="text-2xl font-bold text-blue-800 animate-fade-in-up">MyList</a>
            <nav>
                <?php if(isset($_SESSION['usuario_id'])): ?>
                    <div class="flex items-center space-x-4">
                        <a href="dashboard.php" class="text-gray-700 hover:text-blue-800 transition animate-fade-in-up">Meu Painel</a>
                        <a href="logout.php" class="btn-primary px-4 py-2 rounded-lg animate-fade-in-up">Sair</a>
                    </div>
                <?php else: ?>
                    <div class="flex items-center space-x-4">
                        <a href="login.php" class="text-gray-700 hover:text-blue-800 transition animate-fade-in-up">Entrar</a>
                        <a href="registro.php" class="btn-primary px-4 py-2 rounded-lg animate-fade-in-up">Cadastrar</a>
                    </div>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="pt-20">
        <section class="hero-gradient text-white py-24">
            <div class="container mx-auto px-4 max-w-4xl text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-6 animate-fade-in-up">Organize Suas Compras</h1>
                <p class="text-xl mb-8 text-gray-200 max-w-2xl mx-auto animate-fade-in-up">Simplifique suas compras com listas inteligentes e fáceis de usar.</p>
                <?php if(!isset($_SESSION['usuario_id'])): ?>
                    <a href="registro.php" class="btn-primary px-6 py-3 rounded-lg text-lg animate-fade-in-up">Começar Agora</a>
                <?php else: ?>
                    <a href="dashboard.php" class="btn-primary px-6 py-3 rounded-lg text-lg animate-fade-in-up">Ir para o Painel</a>
                <?php endif; ?>
            </div>
        </section>

        <section class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-16 text-blue-900 animate-fade-in-up">Como Funciona</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="feature-card p-6 bg-gray-100 rounded-lg text-center animate-fade-in-up">
                        <div class="flex justify-center mb-6">
                            <img src="assets/img/list-icon.svg" alt="Listas" class="w-20 h-20">
                        </div>
                        <h3 class="text-xl font-semibold mb-4 text-blue-800">Listas Personalizadas</h3>
                        <p class="text-gray-600">Crie listas únicas para cada compra, com total flexibilidade.</p>
                    </div>
                    <div class="feature-card p-6 bg-gray-100 rounded-lg text-center animate-fade-in-up">
                        <div class="flex justify-center mb-6">
                            <img src="assets/img/check-icon.svg" alt="Controle" class="w-20 h-20">
                        </div>
                        <h3 class="text-xl font-semibold mb-4 text-blue-800">Controle Total</h3>
                        <p class="text-gray-600">Marque itens comprados e gerencie suas quantidades.</p>
                    </div>
                    <div class="feature-card p-6 bg-gray-100 rounded-lg text-center animate-fade-in-up">
                        <div class="flex justify-center mb-6">
                            <img src="assets/img/device-icon.svg" alt="Multiplataforma" class="w-20 h-20">
                        </div>
                        <h3 class="text-xl font-semibold mb-4 text-blue-800">Multiplataforma</h3>
                        <p class="text-gray-600">Acesse suas listas de qualquer dispositivo.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="hero-gradient text-white py-20 text-center">
            <div class="container mx-auto px-4">
                <h2 class="text-4xl font-bold mb-6 animate-fade-in-up">Comece Agora, é Grátis!</h2>
                <p class="text-xl mb-8 text-gray-200 animate-fade-in-up">Organize suas compras em minutos.</p>
                <?php if(!isset($_SESSION['usuario_id'])): ?>
                    <a href="registro.php" class="btn-primary px-8 py-4 rounded-lg text-xl animate-fade-in-up">Criar Conta</a>
                <?php else: ?>
                    <a href="dashboard.php" class="btn-primary px-8 py-4 rounded-lg text-xl animate-fade-in-up">Ir para o Painel</a>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="bg-gray-900 text-white py-8">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2025 MyList. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
