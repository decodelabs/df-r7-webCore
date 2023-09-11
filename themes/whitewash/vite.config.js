import { defineConfig } from 'vite';
import legacy from '@vitejs/plugin-legacy';
import path from 'path';

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [
        legacy({
            targets: [
                "defaults",
                "not IE 11"
            ],
            polyfills: false,
            renderLegacyChunks: false
        })
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './src')
        }
    },

    base: '/theme/whitewash/',
    publicDir: 'assets',

    build: {
        outDir: 'assets/zest',
        assetsDir: '.',
        copyPublicDir: false,
        cssCodeSplit: true,
        manifest: false,
        rollupOptions: {
            input: 'src/sass/style.scss',
            output: {
                entryFileNames: `[name].js`,
                chunkFileNames: `[name].js`,
                assetFileNames: `[name].[ext]`,
                manualChunks: (id) => {
                    /*
                    if(id.includes('file.name')) {
                        return 'files';
                    }
                    */

                    return 'main';
                }
            }
        }
    },

    server: {
        host: 'localhost',
        port: 7671,
        https: false,
        strictPort: true,
        origin: 'http://localhost:7671',
        hmr: {
            protocol: 'ws',
            host: 'localhost',
        }
    }
});
