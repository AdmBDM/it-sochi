const UglifyJS = require("uglify-js");
const fs = require("fs");
const path = require("path");

// Определяем режим по аргументу
const mode = process.argv[2]; // 'back' или 'front'

if (!mode || !['back', 'front'].includes(mode)) {
    console.error("Usage: node build.js [back|front]");
    process.exit(1);
}

const inputDir = path.join(__dirname, mode);
const outputFile = path.join(__dirname, '../../',
    mode === 'back' ? 'backend/web/js/back.min.js' : 'frontend/web/js/front.min.js'
);

// Читаем все JS-файлы из директории
const files = fs.readdirSync(inputDir)
    .filter(f => f.endsWith('.js'))
    .map(f => path.join(inputDir, f));

if (files.length === 0) {
    console.log(`No JS files found in ${inputDir}`);
    process.exit(0);
}

// Объединяем и минифицируем
const result = UglifyJS.minify(files.map(file => fs.readFileSync(file, "utf8")), {
    compress: true,
    mangle: true
});

if (result.error) {
    console.error(result.error);
    process.exit(1);
}

// Создаём директорию если нужно
fs.mkdirSync(path.dirname(outputFile), { recursive: true });

// Записываем результат
fs.writeFileSync(outputFile, result.code);
console.log(`Built: ${outputFile} (${files.length} files)`);
