{"version":3,"sources":["script.js"],"names":["exports","main_core","main_core_events","main_loader","_templateObject","data","babelHelpers","taggedTemplateLiteral","ImageInput","params","arguments","length","undefined","classCallCheck","this","defineProperty","instanceId","containerId","loaderContainerId","settings","addImageHandler","addImage","bind","editImageHandler","editImage","EventEmitter","subscribe","onUploaderIsInitedHandler","createClass","key","value","event","_this","_event$getCompatData","getCompatData","_event$getCompatData2","slicedToArray","id","uploader","getPreviews","Dom","addClass","getFileWrapper","requestAnimationFrame","getLoaderContainer","style","display","getContainer","onFileIsDeletedHandler","onUploadStartHandler","onUploadDoneHandler","onFileCanvasIsLoadedHandler","getInputInstance","BX","UI","FileInput","getInstance","getFileInput","agent","fileInput","container","document","getElementById","Type","isDomNode","Error","concat","fileWrapper","querySelector","loaderContainer","getAddButton","addButton","target","detail","preventDefault","inputInstance","items","getItems","hasOwnProperty","frameFlags","active","frameFiles","stopPropagation","click","getLoader","loader","Loader","showLoader","setOptions","size","Math","min","offsetHeight","offsetWidth","show","hideLoader","hide","_this2","timeout","clearTimeout","setTimeout","recalculateWrapper","_this3","_event$getCompatData3","_event$getCompatData4","stream","uploading","_this4","_event$getCompatData5","_event$getCompatData6","_this5","isMultipleInput","uploadParams","maxCount","buildShadowElement","wrapper","shadowElement","Tag","render","prepend","canvas","bottomMargin","height","width","closest","querySelectorAll","previews","Event","unbind","removeClass","Reflection","namespace","window"],"mappings":"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","file":"script.map.js"}