$(document).on('click', '.field-image', function () {
    BrowseServer('Images:/', $(this))
});
$(document).on('click', '.field-file', function () {
    BrowseServer('Files:/', $(this))
});
$(document).on('click', '.field-pdf', function () {
    BrowseServer('Files:/pdf/', $(this))
});
$(document).on('click', '.field-flash', function () {
    BrowseServer('Flash:/', $(this))
});
$(document).on('click', '.field-folder', function () {
    var e = $(this), folder = e.val().replace('/upload/images/', '');
    BrowseServer('Images:/' + folder + '/', e, 'crop')
});

function BrowseServer(path, e, w) {
    var finder = new CKFinder();
    finder.basePath = '../vendor/ckfinder/';
    finder.startupPath = path;
    if (w == 'crop') {
        finder.selectActionFunction = SetInputCropped
    } else {
        finder.selectActionFunction = SetInput
    }
    finder.selectActionData = e;
    finder.popup();
}

function SetInput(fileUrl, data, allFiles) {
    var files = [];
    for (var file in allFiles) {
        files.push(allFiles[file]['url'])
    }
    files = array_unique(files);
    data['selectActionData'].val(files.join()).change();
}

function SetInputCropped(fileUrl, data) {
    data['selectActionData'].val(fileUrl.split('/').splice(3, fileUrl.split('/').length - 4).join('/')).change()
}

function array_unique(arr) {
    var tmp_arr = [];
    for (var i = 0; i < arr.length; i++) {
        if (tmp_arr.indexOf(arr[i]) == "-1") {
            tmp_arr.push(arr[i]);
        }
    }
    return tmp_arr;
}
