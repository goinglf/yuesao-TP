window.addEventListener('load', function () {
    var fileSelect = document.getElementById("fileSelect"),
        fileElem = document.getElementById("fileElem");

    fileSelect.addEventListener("click", function (e) {
        if (fileElem) {
            fileElem.click();
        }
        e.preventDefault(); // prevent navigation to "#"
    }, false);
}, false);

function handleFiles(files) {
    var file = files[0];
    var imageType = /^image\//;

    if (!imageType.test(file.type)) {
        return;
    }

    var img = document.querySelector('.uploader__preview');
    img.classList.add("obj");
    img.file = file;

    var reader = new FileReader();
    reader.onload = (function (aImg) { return function (e) { aImg.src = e.target.result; }; })(img);
    reader.readAsDataURL(file);
}