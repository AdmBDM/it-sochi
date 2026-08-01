import config from './config.mjs';
import { buildCss, buildJs } from './compiler.mjs';

for (const [name, target] of Object.entries(config.targets)) {

    await buildCss(name, target);
    await buildJs(name, target);

}
