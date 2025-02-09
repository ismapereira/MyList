/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./dashboard.php",
        "./minhas_listas.php",
        "./components/*.php",
        "./assets/css/custom.css"
    ],
    theme: {
        extend: {
            fontFamily: {
                'sans': ['Inter', 'ui-sans-serif', 'system-ui']
            }
        }
    },
    plugins: []
};
