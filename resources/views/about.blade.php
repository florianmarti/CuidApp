<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>CuidApp - De qué se trata</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Estilos inline (copiados de welcome.blade.php) -->
        <style>
            *,:after,:before{box-sizing:border-box}:where(html){line-height:1.15}:where(body){margin:0;font-family:Instrument Sans,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji}:where([hidden]){display:none}:where(h1){font-size:2em;margin:.67em 0}:where(svg:not(:root)){overflow-clip-margin:content-box;overflow:hidden}:where(a){background-color:transparent;color:#f53003;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:4px}:where(a:hover){color:#db2c03}:where(.dark a){color:#ff4433}:where(.dark a:hover){color:#ff6655}:where(:focus-visible,:focus){outline:2px solid #1b1b18;outline-offset:2px}:where(.dark :focus-visible,.dark :focus){outline-color:#edebec}:where(button,a[href],input[type=button],input[type=submit]){cursor:pointer}:where(.sr-only){position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
        </style>
    </head>
    <body class="bg-[#50503a] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6">
            <nav class="flex items-center justify-between gap-4">
                <a href="{{ route('welcome') }}" class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                    Volver a Inicio
                </a>
            </nav>
        </header>

        <main class="flex max-w-[335px] w-full lg:max-w-4xl flex-col items-center">
            <div class="text-[13px] leading-[20px] w-full p-6 lg:p-10 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg">
                <h1 class="text-xl lg:text-2xl font-medium mb-4 text-[#f53003] dark:text-[#FF4433]">De qué se trata CuidApp</h1>
                <p class="mb-4 text-[#706f6c] dark:text-[#A1A09A]">
                    Si necesitas que un vigilador cuide tu zona de manera diurna o nocturna, puedes encontrarlo aquí y comenzar a disfrutar más del tiempo sin estar pendiente del auto estacionado afuera o de los ingresos a tu casa.
                </p>
                <p class="mb-4 text-[#706f6c] dark:text-[#A1A09A]">
                    Y si quieres trabajar como vigilador, puedes acceder como cuidador zonal en la aplicación y así trabajar en seguridad, cuidando diferentes zonas según tus horarios y disponibilidad.
                </p>
                <a href="{{ route('welcome') }}" class="inline-block px-5 py-1.5 bg-[#1b1b18] dark:bg-[#eeeeec] dark:text-[#1C1C1A] text-white hover:bg-black dark:hover:bg-white rounded-sm border border-black dark:border-[#eeeeec] text-sm leading-normal">
                    Regresar
                </a>
            </div>
        </main>

        <div class="h-14.5 hidden lg:block"></div>
    </body>
</html>
