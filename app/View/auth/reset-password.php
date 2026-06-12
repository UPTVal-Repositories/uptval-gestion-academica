<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña - UPTVal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { uptval: { orange: '#d97b29', dark: '#a35715', grey: '#808285' } } } } }
    </script>
    <style>
        .glass-panel { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .input-field { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(217, 123, 41, 0.2); color: white; }
        .input-field:focus { border-color: #d97b29; box-shadow: 0 0 15px rgba(217, 123, 41, 0.15); }
    </style>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center relative overflow-hidden font-sans text-white">
    <div class="absolute top-[-15%] left-[-10%] w-96 h-96 bg-uptval-orange rounded-full mix-blend-screen filter blur-[128px] opacity-20"></div>
    <div class="absolute bottom-[-10%] right-[-5%] w-96 h-96 bg-blue-900 rounded-full mix-blend-screen filter blur-[128px] opacity-30"></div>

    <div class="glass-panel p-8 md:p-10 rounded-2xl w-full max-w-md relative z-10 m-4">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold tracking-wider"><span class="text-uptval-orange">UPT</span>Val</h1>
            <p class="text-xs text-gray-400 mt-2 uppercase tracking-widest">Establecer Nueva Clave</p>
        </div>

        <form action="/restablecer" method="POST" class="space-y-6">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
            
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1.5 ml-1">Nueva Contraseña</label>
                <input type="password" name="password" required class="input-field w-full px-4 py-3 rounded-xl outline-none transition-all" placeholder="Mínimo 8 caracteres">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1.5 ml-1">Confirmar Contraseña</label>
                <input type="password" name="confirm_password" required class="input-field w-full px-4 py-3 rounded-xl outline-none transition-all" placeholder="Repita la contraseña">
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-uptval-orange to-uptval-dark text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-uptval-orange/40 transform hover:-translate-y-0.5 transition-all">
                Actualizar Contraseña
            </button>
        </form>
    </div>

    <div id="toast" class="fixed top-6 right-6 z-50 transform transition-all duration-500 translate-x-[150%] opacity-0 w-full max-w-sm bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-4 flex items-start">
            <div id="toast-icon" class="flex-shrink-0">
                <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div class="ml-3 flex-1"><p class="text-sm font-medium text-gray-900">Error</p><p id="toast-message" class="mt-1 text-sm text-gray-500"></p></div>
        </div>
    </div>

    <script>
        <?php if (isset($error_message)): ?>
        const toast = document.getElementById('toast');
        document.getElementById('toast-message').innerText = '<?= addslashes($error_message) ?>';
        toast.classList.remove('translate-x-[150%]', 'opacity-0');
        setTimeout(() => { toast.classList.add('translate-x-[150%]', 'opacity-0'); }, 4000);
        <?php endif; ?>
    </script>
</body>
</html>