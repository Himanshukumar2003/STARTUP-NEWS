<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TinyMCE Editor Example</title>
    <script src="https://cdn.jsdelivr.net/npm/@tinymce/tinymce-webcomponent@2/dist/tinymce-webcomponent.min.js">
    </script>
</head>

<body style="margin: 40px; background: #f7f7f7; font-family: sans-serif;">
    <h2>Full Featured TinyMCE Editor</h2>

    <!-- ✅ TinyMCE Editor Web Component -->
    <tinymce-editor api-key="26eohrlp913qxavz9xyrl5wszw74jii703o230piigrz0ync" height="500"
        menubar="file edit view insert format tools table help"
        plugins="print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists wordcount textpattern noneditable help charmap quickbars emoticons"
        toolbar="undo redo | blocks fontfamily fontsize | bold italic underline strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table codesample | removeformat | fullscreen preview code help"
        toolbar_mode="sliding"
        content_style="body { font-family:Helvetica,Arial,sans-serif; font-size:14px; line-height:1.6; } img { max-width:100%; height:auto; }">
        <p>Welcome to the <strong>full-featured TinyMCE editor</strong> example!</p>
    </tinymce-editor>
</body>

</html>