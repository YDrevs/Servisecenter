import { defineConfig } from 'vite';
import { resolve } from 'node:path';

// Theme asset source + build output.
const themeDir = resolve(__dirname, '../wp-content/themes/leaderauto');
const srcDir = resolve(themeDir, 'assets/src');

// Must match LEADERAUTO_DEV_ORIGIN / the port read in inc/enqueue.php.
const DEV_PORT = 5173;

export default defineConfig({
  root: srcDir,
  base: '/wp-content/themes/leaderauto/assets/dist/',
  build: {
    outDir: resolve(themeDir, 'assets/dist'),
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: resolve(srcDir, 'js/main.js'),
    },
  },
  server: {
    host: '0.0.0.0',
    port: DEV_PORT,
    strictPort: true,
    origin: `http://localhost:${DEV_PORT}`,
    cors: true,
    // Bind-mounted into a container on macOS — native FS events don't cross the boundary.
    watch: { usePolling: true, interval: 300 },
  },
});
