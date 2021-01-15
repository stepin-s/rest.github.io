const imagemin = require('imagemin');
const imageminSvgo = require('imagemin-svgo');

imagemin(['images/svg/**/*.svg'], 'svg', {
    use: [
        imageminSvgo({
            plugins: [
                {cleanupIDs: {remove: false}},
                {cleanupNumericValues: {floatPrecision: 2}},
                {removeStyleElement: true}
                ],
            multipass: true
        })
    ]
}).then(function () {
    console.log('SVG-Icons were successfully optimized');
});