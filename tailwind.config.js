// tailwind.config.js - Versão COMPLETA E VALIDADA
import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",

    // Caminhos de conteúdo unificados para garantir que o Tailwind escanear todos os arquivos Blade
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
    ],

    theme: {
        extend: {
            // Definição das Cores Customizadas (pink-XXX)
            colors: {
                'pink-600': '#db2777', // Cor principal
                'pink-500': '#ec4899',
                'pink-100': '#fce7f6',
                'pink-50': '#fdf2f8',
                'pink-700': '#be123c', // Rosa mais escuro para hover/degradê
            },
            
            // Definição das Fontes Customizadas (Inter e Georgia/serif)
            fontFamily: {
                'sans': ['Inter', ...defaultTheme.fontFamily.sans],
                'serif': ['Georgia', 'Times New Roman', 'serif'],
            },
            
            // Definição do Espaçamento Customizado 'section'
            spacing: {
                'section': '5rem', // Para classes como py-section
            },
        },
    },

    plugins: [
        forms,
    ],
};