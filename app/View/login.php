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

        <form action="/login" method="POST" class="space-y-6">
            
            <div>
                <label for="cedula" class="block text-sm font-medium text-gray-400 mb-1.5 ml-1">Cédula</label>
                <input type="text" id="cedula" name="cedula" 
                    class="input-field w-full px-4 py-3 rounded-xl outline-none transition-all duration-300 placeholder-gray-600" 
                    placeholder="Ingrese su usuario" required>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-400 mb-1.5 ml-1">Contraseña</label>
                <input type="password" id="password" name="password" 
                    class="input-field w-full px-4 py-3 rounded-xl outline-none transition-all duration-300 placeholder-gray-600" 
                    placeholder="••••••••" required>
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

</body>
</html>