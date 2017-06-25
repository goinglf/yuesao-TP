

window.addEventListener('load', function () {
    var fileSelect = document.getElementById("selectElem"),
        fileElem = document.getElementById("fileElem");

    fileSelect.addEventListener("click", function (e) {
        if (fileElem) {
            fileElem.click();
        }
        e.preventDefault(); // prevent navigation to "#"
    }, false);

}, false);



function handleFiles(files) {
    var preview = document.querySelector('.uploader__preview');
    for(var i=0;i<files.length;i++){

        var file = files[i];
        var imageType = /^image\//;

        if (!imageType.test(file.type)) {
            continue;
        }

        var div = document.createElement('div');
        div.classList.add('uploader__img');
        div.addEventListener('click', removeImage);
        div.file = file;
        preview.appendChild(div);

        var reader = new FileReader();
        reader.onload = (function (aDiv) {
            return function (e) {
                aDiv.setAttribute('style', 'background-image:url(' + e.target.result + ')');
            };
        })(div);
        reader.readAsDataURL(file);
    }
}

function removeImage(e) {
    var preview = document.querySelector('.uploader__preview');
    preview.removeChild(this);
}

$(function () {
    $('.comment__star').hover(
        function () {
            var filter = $(this).prevAll();
            $(filter).add(this).addClass('comment__star--full');
        },
        function () {
            var $parent = $(this).parent();
            var className = 'comment__star--full';

            var starNum = $parent.data('star-num');
            var $origin = $parent.find('.comment__star:lt('+ starNum +')');

            // 恢复原状
            $parent.find('.' + className).removeClass(className);
            $origin.addClass(className);
        }
    );

    $('.comment__star .comment_score').click( function () {
        var $parent = $(this).parent();
        $parent.data('star-num', $(this).index() + 1);
        $parent.data('pin', true);
    });
});