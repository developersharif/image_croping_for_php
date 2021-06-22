<?php
/*

author: developersharif
github: developersharif

*/
if(isset($_POST['crop_image'])){

    $data = $_POST['crop_image'];
    $image_array_1 = explode(";", $data);
    $image_array_2 = explode(",", $image_array_1[1]);
    $base64_decode = base64_decode($image_array_2[1]);
    $path_img = time().'.png';
    file_put_contents($path_img, $base64_decode);

}
?>
<!DOCTYPE html>
<html>
<head>
<title>Crop image before upload and insert to database using PHP Mysqli and CropperJS </title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script> 
<link rel="stylesheet" href="https://fengyuanchen.github.io/cropperjs/css/cropper.css" />
<script src="https://fengyuanchen.github.io/cropperjs/js/cropper.js"></script> 
<script>
$(document).ready(function(){
    var $modal = $('#modal_crop');
   $("#upload_image").change(function () {
        var fileExtension = ['jpeg', 'jpg', 'png', 'gif'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert("Only formats are allowed : "+fileExtension.join(', '));
             $("#upload_image").modal('hide');
             $('#upload_image').val('');
        }
    });
    var crop_image = document.getElementById('sample_image');
    var cropper;
    $('#upload_image').change(function(event){
        var files = event.target.files;
        var done = function(url){
            crop_image.src = url;
            $modal.modal('show');
        };
        if(files && files.length > 0)
        {
            reader = new FileReader();
            reader.onload = function(event)
            {
                done(reader.result);
            };
            reader.readAsDataURL(files[0]);
        }
    });
    $modal.on('shown.bs.modal', function() {
        cropper = new Cropper(crop_image, {
            aspectRatio: 1,
            viewMode: 3,
            preview:'.preview'
        });
    }).on('hidden.bs.modal', function(){
        cropper.destroy();
        cropper = null;
    });
    $('#crop_and_upload').click(function(){
        canvas = cropper.getCroppedCanvas({
            width:400,
            height:400
        });
        canvas.toBlob(function(blob){
            url = URL.createObjectURL(blob);
            var reader = new FileReader();
            reader.readAsDataURL(blob);
            reader.onloadend = function(){
                var base64data = reader.result; 
                $.ajax({
                    url:'crope.php',
                    method:'POST',
                    data:{crop_image:base64data},
                    success:function(data)
                    {
                        $modal.modal('hide'); 
                        $('#upload_image').val('');
                        $("#up-status").html("<b>Successfully uploaded!</b>");
                        

                    },
                    error:function(e){
                    
                    alert("something went wrong! "+e.status );
                    console.log(e);
                    }
                });
            };
        });
    });
});
</script>
<style>
    img {
        display: block;
        max-width: 100%;
    }
    .preview {
        overflow: hidden;
        width: 160px; 
        height: 160px;
        margin: 10px;
        border: 1px solid red;
    }
    .modal-lg{
        max-width: 1000px !important;
    }
</style>
</head>
    <body>
        <div class="container" align="center">
            <br />
            <h3 align="center">Crop image before upload using CropperJS by {developersharif} </h3>
            <br />
            <div class="row">
<p id="up-status"></p>
                <input type="file" name="crop_image" class="crop_image" id="upload_image" />
            
            <div class="modal fade" id="modal_crop" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Crop Image Before Upload</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="img-container">
                                <div class="row">
                                    <div class="col-md-8">
                                        <img src="" id="sample_image" />
                                    </div>
                                    <div class="col-md-4">
                                        <div class="preview"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="crop_and_upload" class="btn btn-primary">Crop</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </body>
</html>
