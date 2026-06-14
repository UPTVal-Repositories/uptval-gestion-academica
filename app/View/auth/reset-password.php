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
                <div class="relative">
                    <input type="password" id="password" name="password" required class="input-field w-full px-4 py-3 pr-12 rounded-xl outline-none transition-all" placeholder="Mínimo 8 caracteres">
                    <button type="button" onclick="togglePassword('password', 'eyeIconNew')" class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-uptval-orange focus:outline-none transition-colors duration-300">
                        <svg id="eyeIconNew" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1.5 ml-1">Confirmar Contraseña</label>
                <div class="relative">
                    <input type="password" id="confirm_password" name="confirm_password" required class="input-field w-full px-4 py-3 pr-12 rounded-xl outline-none transition-all" placeholder="Repita la contraseña">
                    <button type="button" onclick="togglePassword('confirm_password', 'eyeIconConfirm')" class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-uptval-orange focus:outline-none transition-colors duration-300">
                        <svg id="eyeIconConfirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </div>
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
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            
            const pathEyeOpen = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />';
            const pathEyeClosed = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />';

            const isPassword = passwordInput.getAttribute("type") === "password";
            passwordInput.setAttribute("type", isPassword ? "text" : "password");
            eyeIcon.innerHTML = isPassword ? pathEyeClosed : pathEyeOpen;
        }

        <?php if (isset($error_message)): ?>
        const toast = document.getElementById('toast');
        document.getElementById('toast-message').innerText = '<?= addslashes($error_message) ?>';
        toast.classList.remove('translate-x-[150%]', 'opacity-0');
        setTimeout(() => { toast.classList.add('translate-x-[150%]', 'opacity-0'); }, 4000);
        <?php endif; ?>
    </script>
</body>
</html>