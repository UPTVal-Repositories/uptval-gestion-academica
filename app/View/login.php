<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Gestión Académica UPTVal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        uptval: {
                            orange: '#d97b29', /* Naranja extraído del logo */
                            dark: '#a35715',   /* Tono oscuro para hover */
                            grey: '#808285'    /* Gris del arco */
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Efecto vanguardista de Cristal Esmerilado (Glassmorphism) */
        .glass-panel {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .input-field {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(217, 123, 41, 0.2);
            color: white;
        }
        .input-field:focus {
            border-color: #d97b29;
            box-shadow: 0 0 15px rgba(217, 123, 41, 0.15);
            background: rgba(255, 255, 255, 0.05);
        }

        @keyframes shrink {
            from {width: 100%;}
            to {width: 0;}
        }
        .animate-shrink{
            animation: shrink 4s linear forwards;
        }

       :root {
            color-scheme: dark;
        }

        /* Hack estabilizado para WebKit: Retrasa el fondo nativo 5000 segundos */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s !important;
        }
    </style>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center relative overflow-hidden font-sans">
    
    <div class="absolute top-[-15%] left-[-10%] w-96 h-96 bg-uptval-orange rounded-full mix-blend-screen filter blur-[128px] opacity-20 animate-pulse"></div>
    <div class="absolute bottom-[-10%] right-[-5%] w-96 h-96 bg-blue-900 rounded-full mix-blend-screen filter blur-[128px] opacity-30"></div>

    <div class="glass-panel p-8 md:p-10 rounded-2xl w-full max-w-md relative z-10 m-4">
        
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold tracking-wider text-white flex justify-center items-center gap-1">
                <span class="text-uptval-orange drop-shadow-md">UPT</span>Val
            </h1>
            <p class="text-xs text-gray-400 mt-3 tracking-[0.2em] uppercase font-semibold">Sistema de Gestión Académica</p>
        </div>

        <form action="/login" method="POST" class="space-y-6" autocomplete="off">
            
            <div>
                <label for="cedula" class="block text-sm font-medium text-gray-400 mb-1.5 ml-1">Cédula</label>
                <input type="text" id="cedula" name="cedula" 
                    class="input-field w-full px-4 py-3 rounded-xl outline-none transition-all duration-300 placeholder-gray-600" 
                    placeholder="Ingrese su Cédula" required autofocus autocomplete="off">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-400 mb-1.5 ml-1">Contraseña</label>
                <div class="relative">
                    <input type="password" id="password" name="password" 
                        class="input-field w-full px-4 py-3 pr-12 rounded-xl outline-none transition-all duration-300 placeholder-gray-600" 
                        placeholder="••••••••" required autocomplete="new-password">
                    
                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-uptval-orange focus:outline-none transition-colors duration-300">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between text-sm mt-2">
                <label class="flex items-center text-gray-400 cursor-pointer hover:text-gray-200 transition-colors">
                    <input type="checkbox" class="rounded border-gray-600 text-uptval-orange focus:ring-uptval-orange bg-slate-800 mr-2 w-4 h-4">
                    Recordarme
                </label>
                <a href="#" class="text-uptval-orange hover:text-white transition-colors duration-300">¿Olvidó su clave?</a>
            </div>

            <button type="submit" 
                class="w-full mt-8 bg-gradient-to-r from-uptval-orange to-uptval-dark text-white font-bold py-3.5 px-4 rounded-xl shadow-lg hover:shadow-uptval-orange/40 transform hover:-translate-y-0.5 transition-all duration-300">
                Iniciar Sesión
            </button>
        </form>
        
        <div class="mt-8 text-center border-t border-gray-800 pt-6">
             <p class="text-[11px] text-gray-500">Versión 1.0.0 | UPTVal © 2026</p>
        </div>
    </div>

    <div id="toast" class="fixed top-6 right-6 z-50 transform transition-all duration-500 translate-x-[150%] opacity-0 w-full max-w-sm bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0" id="toast-icon-container"></div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p id="toast-title" class="text-sm font-medium text-gray-900"></p>
                    <p id="toast-message" class="mt-1 text-sm text-gray-500"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button type="button" onclick="hideToast()" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uptval-orange transition-colors">
                        <span class="sr-only">Cerrar</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gray-50 w-full">
            <div id="toast-progress" class="h-full w-full"></div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const togglePassword = document.getElementById("togglePassword");
            const passwordInput = document.getElementById("password");
            const eyeIcon = document.getElementById("eyeIcon");

            const pathEyeOpen = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />';
            const pathEyeClosed = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />';

            togglePassword.addEventListener("click", function () {
                const isPassword = passwordInput.getAttribute("type") === "password";
                passwordInput.setAttribute("type", isPassword ? "text" : "password");
                eyeIcon.innerHTML = isPassword ? pathEyeClosed : pathEyeOpen;
            });
        });

        const icons = {
            success: `<svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`,
            error: `<svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`
        };

        function showToast(type, title, message) {
            const toast = document.getElementById('toast');
            const toastTitle = document.getElementById('toast-title');
            const toastMessage = document.getElementById('toast-message');
            const toastProgress = document.getElementById('toast-progress');
            const iconContainer = document.getElementById('toast-icon-container');

            toastTitle.innerText = title;
            toastMessage.innerText = message;

            if (type === 'error') {
                iconContainer.innerHTML = icons.error;
                toastProgress.className = 'h-full w-full bg-red-400';
            } else {
                iconContainer.innerHTML = icons.success;
                toastProgress.className = 'h-full w-full bg-green-400';
            }

            toastProgress.classList.remove('animate-shrink');
            void toastProgress.offsetWidth; 
            toastProgress.classList.add('animate-shrink');

            toast.classList.remove('translate-x-[150%]', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');

            setTimeout(() => { hideToast(); }, 4000);
        }

        function hideToast() {
            const toast = document.getElementById('toast');
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-[150%]', 'opacity-0');
        }
    </script>

    <?php if (isset($error_message) && !empty($error_message)): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                showToast('error', 'Error de acceso', '<?php echo addslashes($error_message); ?>');
            });
        </script>
    <?php endif; ?>
    
    <?php if (isset($success_message) && !empty($success_message)): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                showToast('success', '¡Autenticación exitosa!', '<?php echo addslashes($success_message); ?>');
            });
        </script>
    <?php endif; ?>
</body>
</html>