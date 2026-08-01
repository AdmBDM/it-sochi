import path from 'node:path';

const ROOT = process.cwd();

export default {

    targets: {

        backend: {
            css: {
                entry: path.join(ROOT, 'resources/backend/scss/back.scss'),
                output: path.join(ROOT, 'backend/web/css/back.min.css'),
            },
            js: {
                entry: path.join(ROOT, 'resources/backend/js/back.js'),
                output: path.join(ROOT, 'backend/web/js/back.min.js'),
            },
        },

        frontend: {
            css: {
                entry: path.join(ROOT, 'resources/frontend/scss/front.scss'),
                output: path.join(ROOT, 'frontend/web/css/front.min.css'),
            },
            js: {
                entry: path.join(ROOT, 'resources/frontend/js/front.js'),
                output: path.join(ROOT, 'frontend/web/js/front.min.js'),
            },
        },

    },

};
