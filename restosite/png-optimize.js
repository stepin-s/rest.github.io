const imagemin = require("imagemin");
const imageminPngquant = require("imagemin-pngquant");

(async () => {
    await imagemin(['images/png/*.png'], 'png', {
        plugins: [
            imageminPngquant({
                quality: [0.6, 0.7]
            })
        ]
    });

    console.log('Images optimized');
})();