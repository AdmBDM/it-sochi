import * as esbuild from 'esbuild';
import { spawn } from 'node:child_process';

import config from './config.mjs';

// -----------------------------------------------------------------------------
// SCSS
// -----------------------------------------------------------------------------

for (const [name, target] of Object.entries(config.targets)) {

    const process = spawn(

        'npx',

        [
            'sass',
            '--watch',
            '--style=compressed',
            '--no-source-map',
            target.css.entry,
            target.css.output
        ],

        {
            stdio: 'inherit',
            shell: true,
        }

    );

    process.on('error', (error) => {

        console.error(error);

    });

    console.log(`✔ ${name}: CSS watch started`);

}

// -----------------------------------------------------------------------------
// JavaScript
// -----------------------------------------------------------------------------

for (const [name, target] of Object.entries(config.targets)) {

    const context = await esbuild.context({

        entryPoints: [target.js.entry],

        outfile: target.js.output,

        bundle: true,

        minify: true,

        format: 'esm',

        target: 'es2022',

        sourcemap: false,

        logLevel: 'info',

    });

    await context.watch();

    console.log(`✔ ${name}: JS watch started`);

}

console.log();
console.log('======================================');
console.log(' Development mode started');
console.log(' Press Ctrl+C to stop');
console.log('======================================');

process.stdin.resume();
