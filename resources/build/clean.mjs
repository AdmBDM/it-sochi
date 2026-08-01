import { rimrafSync } from 'rimraf';

import config from './config.mjs';

for (const target of Object.values(config.targets)) {

    rimrafSync(target.css.output, {
        glob: false,
    });

    rimrafSync(target.js.output, {
        glob: false,
    });

}
