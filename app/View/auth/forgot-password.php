<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Clave - UPTVal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { uptval: { orange: '#d97b29', dark: '#a35715', grey: '#808285' } } } } }
    </script>
    <style>
        .glass-panel { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .input-field { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(217, 123, 41, 0.2); color: white; }
        .input-field:focus { border-color: #d97b29; box-shadow: 0 0 15px rgba(217, 123, 41, 0.15); }
        @keyframes shrink { from {width: 100%;} to {width: 0;} }
        .animate-shrink { animation: shrink 4s linear forwards; }
    </style>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center relative overflow-hidden font-sans text-white">
    <div class="absolute top-[-15%] left-[-10%] w-96 h-96 bg-uptval-orange rounded-full mix-blend-screen filter blur-[128px] opacity-20 animate-pulse"></div>
    <div class="absolute bottom-[-10%] right-[-5%] w-96 h-96 bg-blue-900 rounded-full mix-blend-screen filter blur-[128px] opacity-30"></div>

    <div class="glass-panel p-8 md:p-10 rounded-2xl w-full max-w-md relative z-10 m-4">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold tracking-wider"><span class="text-uptval-orange">UPT</span>Val</h1>
            <p class="text-xs text-gray-400 mt-2 uppercase tracking-widest">Recuperación de Acceso</p>
        </div>

        <form action="/recuperar" method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2 ml-1">Cédula de Identidad</label>
                <input type="text" name="cedula" required class="input-field w-full px-4 py-3 rounded-xl outline-none transition-all" placeholder="Ej: 20123456">
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-uptval-orange to-uptval-dark text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-uptval-orange/40 transform hover:-translate-y-0.5 transition-all">
                Enviar Instrucciones
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="/login" class="text-sm text-gray-400 hover:text-uptval-orange transition-colors">Volver al inicio de sesión</a>
        </div>
    </div>

    <!-- Toast System -->
    <div id="toast" class="fixed top-6 right-6 z-50 transform transition-all duration-500 translate-x-[150%] opacity-0 w-full max-w-sm bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-4 flex items-start">
            <div id="toast-icon" class="flex-shrink-0"></div>
            <div class="ml-3 flex-1"><p id="toast-title" class="text-sm font-medium text-gray-900"></p><p id="toast-message" class="mt-1 text-sm text-gray-500"></p></div>
        </div>
        <div class="h-1 bg-gray-100"><div id="toast-progress" class="h-full"></div></div>
    </div>

    <script>
        function showToast(type, title, message) {
            const toast = document.getElementById('toast');
            const progress = document.getElementById('toast-progress');
            document.getElementById('toast-title').innerText = title;
            document.getElementById('toast-message').innerText = message;
            document.getElementById('toast-icon').innerHTML = type === 'success' ? 
                '<svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' : 
                '<svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
            
            progress.className = 'h-full ' + (type === 'success' ? 'bg-green-500' : 'bg-red-500') + ' animate-shrink';
            toast.classList.remove('translate-x-[150%]', 'opacity-0');
            setTimeout(() => { toast.classList.add('translate-x-[150%]', 'opacity-0'); }, 4000);
        }
        <?php if (isset($error_message)): ?> showToast('error', 'Error', '<?= addslashes($error_message) ?>'); <?php endif; ?>
        <?php if (isset($success_message)): ?> showToast('success', 'Enviado', '<?= addslashes($success_message) ?>'); <?php endif; ?>
    </script>
</body>
</html>