const fs = require('fs');
const glob = require("glob");
const path = require('path');
const ejs = require('ejs');

let template = `
<!DOCTYPE html>
<html>
<head>
...
</head>
<body>
  <span style="visibility: hidden; position: absolute; z-index: -1;">
    <!-- SVG-Sprite -->
    <%- svgsprite %>
  </span>
...
</body>
</html>
`.trim();

let svgspriteContent = fs.readFileSync('svgsprite.svg', 'utf8');

let html = ejs.render(template, {svgsprite: svgspriteContent});
//fs.writeFileSync('index.html', html);


let demoTemplate = fs.readFileSync('svgsprite-demo.ejs', 'utf8');
let theme = fs.readFileSync('styles/main.css', 'utf8');
let context = {svgsprite: svgspriteContent, theme: theme};

glob("svg/**/*.svg", function (err, files) {
    if (err) {
        console.log(err);
        return;
    }

    context.files = files.map(function(file){
        return path.basename(file, '.svg');
    });

    let demoHtml = ejs.render(demoTemplate, context);
    fs.writeFileSync('svgsprite-demo.html', demoHtml);
});