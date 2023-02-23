@extends('layouts.master')
@section('title')
    Create Email Template  | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection
@section('content')
    <div class="right_col" role="main">
    <section class="content">
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel tile">
                        <div class="x_title">
                            <h3>Template Editor</h3>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            @include('email.manage._manage')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </div>
    @endsection
@section('script')
    {{--Tiny MCE--}}
    <script src="https://cdn.tiny.cloud/1/blhpq6xwogw0v33qbfrg6e1pgm78yb9v2vcx7cjykwm3l5ni/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      $('#summernote').summernote({
         // height:350,
          popover: {
              image: [
                  ['custom', ['imageAttributes']],
                  ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
                  ['float', ['floatLeft', 'floatRight', 'floatNone']],
                  ['remove', ['removeMedia']]
              ],
          },
          imageAttributes:{
              icon:'<i class="note-icon-pencil"/>',
              removeEmpty:false, // true = remove attributes | false = leave empty if present
              disableUpload: false // true = don't display Upload Options | Display Upload Options
          },
      });

    </script>
    <script>
        $(document).ready(function () {
            if ($('#radio_1').is(':checked')){
                $('#summernote').summernote('enable');
                $('.fileImport').hide();
                $('.editorInput').show();
                $('#templateForm').append('<input type="hidden" name="active" id="priority" value="1" />');
            }
            $('input:radio[name=group1]').change(function(){
               if($('#radio_1').is(':checked')){
                  $('#summernote').summernote('enable');
                  $('.fileImport').hide();
                  $('.editorInput').show();
                   $('#priority').remove();
                   $('#templateForm').append('<input type="hidden" name="active" id="priority" value="1" />');
               }else {
                   $('#summernote').summernote('disable');
                   $('.fileImport').show();
                   $('.editorInput').hide();
                   $('#priority').remove();
                   $('#templateForm').append('<input type="hidden" name="active" id="priority" value="2" />');
               }
            });
        })
    </script>
	 <script>
         $('#saveTemplate').on('click',function () {
             $('#templateForm').submit()
         })
    </script>

    <script>

        tinymce.init({
            selector: '#mytiny',
            height:'850',
            plugins: [
                'advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker',
                'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
                'table emoticons template paste help image code'
            ],
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify |' +
            ' bullist numlist outdent indent | link image | print preview media fullpage | ' +
            'forecolor backcolor emoticons | help',
            menu: {
                favs: {title: 'My Favorites', items: 'code visualaid | searchreplace | spellchecker | emoticons'}
            },
            menubar: 'favs file edit view insert format tools table help',
            content_css: 'css/content.css',
            /* enable title field in the Image dialog*/
            image_title: true,
            /* enable automatic uploads of images represented by blob or data URIs*/
            automatic_uploads: true,
            file_picker_types: 'image',
            /* and here's our custom image picker*/
            file_picker_callback: function (cb, value, meta) {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.onchange = function () {
                    var file = this.files[0];

                    var reader = new FileReader();
                    reader.onload = function () {
                        /*
                          Note: Now we need to register the blob in TinyMCEs image blob
                          registry. In the next release this part hopefully won't be
                          necessary, as we are looking to handle it internally.
                        */
                        var id = 'blobid' + (new Date()).getTime();
                        var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                        var base64 = reader.result.split(',')[1];
                        var blobInfo = blobCache.create(id, file, base64);
                        blobCache.add(blobInfo);

                        /* call the callback and populate the Title field with the file name */
                        cb(blobInfo.blobUri(), {title: file.name});
                    };
                    reader.readAsDataURL(file);
                };

                input.click();
            }
        });
    </script>

@endsection
