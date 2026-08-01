import * as fs from 'node:fs/promises';
import * as sass from 'sass';
import * as esbuild from 'esbuild';

export async function buildCss(name, target) {

    const result = sass.compile(target.css.entry, {
        style: 'compressed',
        sourceMap: false,
        charset: false,
    });

    await fs.writeFile(target.css.output, result.css);

    console.log(`✔ ${name}: CSS → ${target.css.output}`);

}

export async function buildJs(name, target) {

    await esbuild.build({

        entryPoints: [target.js.entry],
        outfile: target.js.output,

        bundle: true,
        minify: true,

        format: 'esm',
        target: 'es2022',

        sourcemap: false,
        logLevel: 'silent',

    });

    console.log(`✔ ${name}: JS  → ${target.js.output}`);

}
