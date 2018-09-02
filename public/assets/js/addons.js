define([], function () {
    require.config({
    paths: {
        'simditor': '../addons/simditor/js/simditor.min',
    },
    shim: {
        'simditor': [
            'css!../addons/simditor/css/simditor.min.css'
        ]
    }
});

if ($(".editor").size() > 0) {
    //修改上传的接口调用
    require(['upload', 'simditor'], function (Upload, Simditor) {
        var editor, mobileToolbar, toolbar;
        Simditor.locale = 'zh-CN';
        Simditor.list = {};
        toolbar = ['title', 'bold', 'italic', 'underline', 'strikethrough', 'fontScale', 'color', '|', 'ol', 'ul', 'blockquote', 'code', 'table', '|', 'link', 'image', 'hr', '|', 'indent', 'outdent', 'alignment'];
        mobileToolbar = ["bold", "underline", "strikethrough", "color", "ul", "ol"];
        $(".editor").each(function () {
            var id = $(this).attr("id");
            editor = new Simditor({
                textarea: this,
                toolbarFloat: false,
                toolbar: toolbar,
                pasteImage: true,
                defaultImage: Fast.api.cdnurl('/assets/addons/simditor/images/image.png'),
                upload: {url: '/'}
            });
            editor.uploader.on('beforeupload', function (e, file) {
                Upload.api.send(file.obj, function (data) {
                    var url = Fast.api.cdnurl(data.url);
                    editor.uploader.trigger("uploadsuccess", [file, {success: true, file_path: url}]);
                });
                return false;
            });
            editor.on("blur", function () {
                this.textarea.trigger("blur");
            });
            Simditor.list[id] = editor;
        });
    });
}
require.config({
    paths: {
        'tinymce': '../addons/tinymce/js/tinymce.min'
    },
});
require(['form', 'upload'], function (Form, Upload) {
    var _bindevent = Form.events.bindevent;
    Form.events.bindevent = function (form) {
        _bindevent.apply(this, [form]);
        try {
            //绑定summernote事件
            if ($(".tinymce,.editor", form).size() > 0) {
                require(['tinymce'], function () {
                    tinymce.init({
                        selector: ".tinymce,.editor",//容器可以是id也可以是class
                        language: 'zh_CN',//语言
                        //language: 'zh_CN',//语言
                        theme: 'modern',//主体默认主题
                        //width: 600,
                        // height: 250,
                        plugins: ['advlist link image lists charmap hr anchor pagebreak searchreplace wordcount visualblocks visualchars code insertdatetime nonbreaking save table contextmenu directionality help autolink autosave print preview spellchecker fullscreen media emoticons template paste textcolor'],//所含插件
                        //content_css: 'css/content.css',//设置样式
                        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons | spellchecker help',//工具栏
                       //图像上传处理
                        convert_urls:false,//关闭url自动检测
                        images_upload_handler: function (blobInfo, success, failure) {
                            Upload.api.send(blobInfo.blob(), function (data) {
                                var url = Fast.api.cdnurl(data.url);
                                success( url);
                                return;
                            },function (data,ret) {
                                failure(ret.msg);
                                return;
                            });
                        },
                    //     contextmenu: false,
                        browser_spellcheck: true,//浏览器检查拼写
                        spellchecker_callback: function(method, text, success, failure) {
                            var words = text.match(this.getWordCharPattern());
                            if (method == "spellcheck") {
                                var suggestions = {};
                                for (var i = 0; i < words.length; i++) {
                                    suggestions[words[i]] = ["First", "Second"];
                                }
                                success(suggestions);
                            }
                        }
                    });
                    $(document).on("click", ":button[type=submit],input[type=submit]", function () {
                        tinymce.triggerSave();
                    });
                });
            }
        } catch (e) {

        }

    };
});

});