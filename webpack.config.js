const path = require('path');

module.exports = {
    mode: 'development', // Cambia a 'production' si estás listo para producción
    entry: './js/solana-lottery.js', // Asegúrate de que esta ruta sea correcta
    output: {
        filename: 'bundle.js',
        path: path.resolve(__dirname, 'dist'),
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env', '@babel/preset-typescript'],
                    },
                },
            },
        ],
    },
    resolve: {
        extensions: ['.js', '.ts'], // Incluye '.ts' para TypeScript
    },
};
