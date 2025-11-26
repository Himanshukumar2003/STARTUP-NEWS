<?php
// add-news.php
session_start();
include 'db.php'; // $con = mysqli_connect()

function generate_csrf_token()
{
    if (empty($_SESSION['_csrf_token'])) $_SESSION['_csrf_token'] = bin2hex(random_bytes(24));
    return $_SESSION['_csrf_token'];
}
function verify_csrf($token)
{
    return isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);
}
function s($con, $str)
{
    return mysqli_real_escape_string($con, trim($str));
}

/* IMAGE PROCESSING */
function process_image_upload($file, $uploadDir = "uploads/news/", $maxWidth = 1200, $convertWebP = true)
{
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowed)) return false;

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $basename = time() . "_" . preg_replace('/[^a-zA-Z0-9_\-\.]/', '', basename($file['name']));
    $targetPath = $uploadDir . $basename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) return false;

    list($origW, $origH, $origType) = @getimagesize($targetPath);
    if (!$origW) return $targetPath;

    $ratio = $origW / $origH;
    $newW = min($origW, $maxWidth);
    $newH = (int)($newW / $ratio);

    if ($origW > $maxWidth && function_exists('imagecreatetruecolor')) {
        switch ($origType) {
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($targetPath);
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($targetPath);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) $src = imagecreatefromwebp($targetPath);
                else $src = imagecreatefromstring(file_get_contents($targetPath));
                break;
            default:
                $src = imagecreatefromstring(file_get_contents($targetPath));
        }

        if ($src) {
            $dst = imagecreatetruecolor($newW, $newH);
            if ($origType == IMAGETYPE_PNG) {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
            }
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

            $finalName = $uploadDir . pathinfo($basename, PATHINFO_FILENAME) . "_resized.jpg";
            imagejpeg($dst, $finalName, 82);
            imagedestroy($dst);
            imagedestroy($src);

            if ($convertWebP && function_exists('imagewebp')) {
                $webpName = $uploadDir . pathinfo($basename, PATHINFO_FILENAME) . "_resized.webp";
                $img = imagecreatefromjpeg($finalName);
                imagewebp($img, $webpName, 82);
                imagedestroy($img);
                return $webpName;
            }
            return $finalName;
        }
    }

    return $targetPath;
}

/* ------------------------------------
   POST REQUEST PROCESS
-------------------------------------*/
$alertmsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /* ------------------------------------
   POST REQUEST PROCESS (mysqli_query version)
-------------------------------------*/

    $alertmsg = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (!verify_csrf($_POST['_csrf_token'] ?? '')) {
            die('CSRF verification failed.');
        }

        $heading = trim($_POST['heading'] ?? '');
        if (strlen($heading) < 5) {
            $alertmsg = '<div class="alert alert-danger">Title is too short.</div>';
        }

        if (empty($alertmsg)) {

            /* SLUG */
            $auto_slug = isset($_POST['auto_slug']) && $_POST['auto_slug'] == '1';
            $slug = s($con, $_POST['slug'] ?? '');

            if ($auto_slug || $slug === '') {
                $slug = strtolower($heading);
                $slug = preg_replace('/[^a-z0-9\s-]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $slug));
                $slug = preg_replace('/\s+/', '-', $slug);
            }

            $slug = trim($slug, '-');
            $baseSlug = $slug;
            $i = 1;
            while (true) {
                $check = mysqli_query($con, "SELECT id FROM news WHERE slug='$slug'");
                if (mysqli_num_rows($check) == 0) break;
                $slug = $baseSlug . '-' . $i++;
            }

            /* BASIC FIELDS */
            $subtitle = s($con, $_POST['short_description'] ?? '');
            $content = mysqli_real_escape_string($con, $_POST['content']);
            $title_meta = s($con, $_POST['title'] ?? '');
            $keywords_meta = s($con, $_POST['keywords'] ?? '');
            $description_meta = s($con, $_POST['description'] ?? '');
            $canonical_url = s($con, $_POST['canonical_url'] ?? '');

            $robot_tag = ($_POST['robot_tag'] ?? 'index') == 'noindex' ? 'noindex' : 'index';
            $author = s($con, $_POST['author'] ?? 'Startup News India Team');

            /* SCHEDULE */
            $scheduled_at = !empty($_POST['scheduled_at'])
                ? date('Y-m-d H:i:s', strtotime($_POST['scheduled_at']))
                : null;

            /* FEATURED IMAGE */
            $featured_image_path = null;
            if (!empty($_FILES['featured_image']['name'])) {
                $featured = process_image_upload($_FILES['featured_image'], "uploads/news/", 1200, true);
                if ($featured) $featured_image_path = s($con, $featured);
            }

            /* STATUS */
            $status = $_POST['publish_status'] == '1' ? 1 : 0;
            $today = date('Y-m-d');
            $category = (int)$_POST['category'];

            /* INSERT USING mysqli_query */
            $sql = "
        INSERT INTO news 
        (`heading`,`short_description`,`content`,`featured_image`,`slug`,`title`,
         `description`,`keyword`,`status`,`date`,`scheduled_at`,
         `robot_tag`,`author`,`category_id`)
        VALUES (
            '$heading',
            '$subtitle',
            '$content',
            '$featured_image_path',
            '$slug',
            '$title_meta',
            '$description_meta',
            '$keywords_meta',
            '$status',
            '$today',
            " . ($scheduled_at ? "'$scheduled_at'" : "NULL") . ",
            '$robot_tag',
            '$author',
            '$category'
        )";

            if (mysqli_query($con, $sql)) {
                $_SESSION['msg'] = '<div class="alert alert-success">News Added Successfully</div>';
                header("Location: all-news.php");
                exit;
            } else {
                $alertmsg = '<div class="alert alert-danger">DB Insert Error: ' . mysqli_error($con) . '</div>';
            }
        }
    }
}

/* FETCH CATEGORIES */
$cats = [];
$res = mysqli_query($con, "SELECT id,category,parent_id FROM blog_category ORDER BY category ASC");
while ($r = mysqli_fetch_assoc($res)) $cats[] = $r;
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Add News — Startup News India</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.0.0-beta.83/dist/themes/light.css" />

    <script src="https://cdn.jsdelivr.net/npm/@tinymce/tinymce-webcomponent@2/dist/tinymce-webcomponent.min.js">
    </script>

    <style>
    .container {
        max-width: 980px;
        margin-top: 24px;
    }

    .form-card {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .06);
    }

    .preview-img {
        max-height: 100px;
        margin: 6px;
        border-radius: 6px;
    }

    small.counter {
        font-size: 12px;
        color: #666;
    }

    .badge-suggestion {
        cursor: pointer;
        margin: 2px;
    }
    </style>
</head>

<body>
    <?php include 'sidenav.php'; ?>

    <section class="main-content">
        <div class="form-card">
            <h3>Add News</h3>
            <?= $alertmsg ?>
            <form method="post" enctype="multipart/form-data" id="newsForm">
                <input type="hidden" name="_csrf_token" value="<?= generate_csrf_token() ?>">

                <!-- 1. Title -->
                <div class="mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="heading" name="heading" required minlength="5"
                        maxlength="200" oninput="validateTitle()" placeholder="Enter news title"
                        value="<?= htmlspecialchars($_POST['heading'] ?? '') ?>">
                    <div id="titleFeedback" class="invalid-feedback"></div>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="autoSlug" name="auto_slug" value="1"
                            checked>
                        <label class="form-check-label" for="autoSlug">Auto-generate slug from title</label>
                    </div>
                </div>

                <!-- Slug optional -->
                <div class="mb-3">
                    <label class="form-label">Slug (editable)</label>
                    <input type="text" class="form-control" id="slug" name="slug" placeholder="auto-generated-slug"
                        value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>">
                    <div class="form-text">Only lowercase letters, numbers and hyphens allowed.</div>
                </div>

                <!-- 2. Subtitle -->
                <div class="mb-3">
                    <label class="form-label">Subtitle (optional)</label>
                    <textarea name="short_description" id="short_description" rows="2" class="form-control"
                        oninput="updateCounter()"
                        maxlength="300"><?= htmlspecialchars($_POST['short_description'] ?? '') ?></textarea>
                    <small class="counter"><span id="subCount">0</span>/300 characters</small>
                </div>

                <!-- 3. Categories -->
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <div class="d-flex gap-2">
                        <select name="category" id="categorySelect" class="form-select w-75" required>
                            <option value="">-- Select Category --</option>
                            <?php
                            // print parent/child with simple indentation
                            $byParent = [];
                            foreach ($cats as $c) $byParent[$c['parent_id']][] = $c;
                            function printCats($parent, $level, $byParent)
                            {
                                if (empty($byParent[$parent])) return;
                                foreach ($byParent[$parent] as $c) {
                                    $pad = str_repeat('&nbsp;&nbsp;&nbsp;', $level);
                                    echo "<option value=\"{$c['id']}\">{$pad}{$c['category']}</option>";
                                    printCats($c['id'], $level + 1, $byParent);
                                }
                            }
                            printCats(null, 0, $byParent);
                            ?>
                        </select>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#addCategoryModal">+ Add</button>
                    </div>
                </div>



                <!-- 5. Featured Image -->
                <div class="mb-3">
                    <label class="form-label">Featured Image (JPG / PNG / WebP)</label>
                    <input type="file" name="featured_image" id="featuredImage" accept="image/jpeg,image/png,image/webp"
                        class="form-control">
                    <div id="featPreview" class="mt-2"></div>
                </div>



                <!-- WYSIWYG -->
                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <tinymce-editor api-key="26eohrlp913qxavz9xyrl5wszw74jii703o230piigrz0ync" height="360"
                        plugins="image media link lists table preview code"
                        toolbar="undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link image media | preview code"
                        name="content"><?= htmlspecialchars($_POST['content'] ?? '') ?></tinymce-editor>
                </div>


                <!-- SEO & Options -->
                <div class="mb-3 p-3 border rounded bg-light">
                    <h5>SEO & Options</h5>
                    <div class="mb-2">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="title" class="form-control"
                            value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Meta Description</label>
                        <textarea name="description"
                            class="form-control"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-2">
                        <label for="exampleFormControlTextarea1">Keywords</label>
                        <textarea class="form-control h-px-100" name="keywords" id="exampleFormControlTextarea1"
                            placeholder="Write Keywords Here..."></textarea>
                    </div>

                    <!-- Robot Tag toggle -->
                    <div class="mb-2">
                        <label class="form-label">Robots</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="robot_tag" id="robotIndex" value="index"
                                checked>
                            <label class="form-check-label" for="robotIndex">Index</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="robot_tag" id="robotNoIndex"
                                value="noindex">
                            <label class="form-check-label text-danger" for="robotNoIndex">Noindex</label>
                        </div>
                        <div id="noindexWarning" class="mt-2 text-warning" style="display:none;">
                            Warning: Noindex will prevent search engines from indexing this article.
                        </div>
                    </div>

                    <!-- Author -->
                    <div class="mb-2">
                        <label class="form-label">Author</label>
                        <select name="author" class="form-select">
                            <option>Startup News India Team</option>
                            <option>Shalini Priya</option>
                            <option>Vaibhav Pandey</option>
                        </select>
                    </div>

                    <!-- Canonical auto-populate suggestion -->
                    <!-- <div class="form-text mb-2">Canonical will default to your site URL + slug if left empty after save.
                    </div> -->

                </div>

                <!-- Schedule & Publish -->
                <div class="mb-3 d-flex gap-3 align-items-center">
                    <div>
                        <label class="form-label">Publish Now</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="publish_status" value="1" class="form-check-input" checked>
                            <label class="form-check-label">Publish</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="publish_status" value="0" class="form-check-input">
                            <label class="form-check-label">Save Draft</label>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Schedule (optional)</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control">
                        <small class="form-text">Use this to schedule automatic publishing (requires cron to process
                            scheduled posts).</small>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="submit" class="btn btn-primary">Save</button>
                    <button type="button" id="previewBtn" class="btn btn-outline-secondary">Preview</button>
                    <button type="reset" class="btn btn-light">Reset</button>
                </div>

            </form>
        </div>
        </div>
    </section>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content p-3">
                <h5>Add Category</h5>
                <input id="newCategoryName" class="form-control mb-2" placeholder="Category name">
                <select id="parentCategory" class="form-select mb-2">
                    <option value="">-- Parent (optional) --</option>
                    <?php foreach ($cats as $c) echo "<option value=\"{$c['id']}\">" . htmlspecialchars($c['category']) . "</option>"; ?>
                </select>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" id="saveCategoryBtn">Save</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                <div id="catMsg" class="mt-2"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // --- Client-side helpers ---

    function validateTitle() {
        let el = document.getElementById('heading');
        let v = el.value.trim();
        let feedback = document.getElementById('titleFeedback');
        if (v.length < 5) {
            el.classList.add('is-invalid');
            feedback.textContent = 'Title must be at least 5 characters.';
            return false;
        }
        if (/[<>#\$%{}|\^~\[\]`]/.test(v)) {
            el.classList.add('is-invalid');
            feedback.textContent = 'Title contains invalid characters.';
            return false;
        }
        el.classList.remove('is-invalid');
        feedback.textContent = '';
        // auto-slug update
        if (document.getElementById('autoSlug').checked) {
            let slug = v.toLowerCase().normalize('NFKD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9\s-]/g, '')
                .trim().replace(/\s+/g, '-');
            document.getElementById('slug').value = slug;
        }
        return true;
    }

    document.getElementById('autoSlug').addEventListener('change', () => {
        if (document.getElementById('autoSlug').checked) validateTitle();
    });

    function updateCounter() {
        const el = document.getElementById('short_description');
        document.getElementById('subCount').textContent = el.value.length;
    }
    updateCounter(); // initial

    // Featured image preview
    document.getElementById('featuredImage').addEventListener('change', function(e) {
        const div = document.getElementById('featPreview');
        div.innerHTML = '';
        const f = e.target.files[0];
        if (!f) return;
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(f.type)) {
            alert('Unsupported image format. Use JPG, PNG or WebP.');
            e.target.value = '';
            return;
        }
        const r = new FileReader();
        r.onload = (ev) => {
            const img = document.createElement('img');
            img.src = ev.target.result;
            img.className = 'preview-img';
            div.appendChild(img);
        };
        r.readAsDataURL(f);
    });





    // noindex warning
    document.getElementById('robotNoIndex').addEventListener('change', () => {
        document.getElementById('noindexWarning').style.display = 'block';
    });
    document.getElementById('robotIndex').addEventListener('change', () => {
        document.getElementById('noindexWarning').style.display = 'none';
    });

    // Preview flow: gather form data via fetch to preview.php endpoint -> open new tab with token
    document.getElementById('previewBtn').addEventListener('click', async () => {
        // create FormData
        const form = document.getElementById('newsForm');
        const fd = new FormData(form);
        fd.append('preview', '1');

        // include TinyMCE content (if using tinyMCE webcomponent it binds to name automatically)
        try {
            const res = await fetch('preview.php', {
                method: 'POST',
                body: fd
            });
            const data = await res.json();
            if (data && data.token) {
                window.open('preview.php?token=' + data.token, '_blank');
            } else {
                alert('Preview failed.');
            }
        } catch (e) {
            console.error(e);
            alert('Preview failed (network).');
        }
    });

    // Add category AJAX
    document.getElementById('saveCategoryBtn').addEventListener('click', async () => {
        const name = document.getElementById('newCategoryName').value.trim();
        const parent = document.getElementById('parentCategory').value;
        if (!name) return;
        const fd = new FormData();
        fd.append('name', name);
        fd.append('parent_id', parent);
        fd.append('_csrf_token', '<?= generate_csrf_token() ?>');

        const btn = document.getElementById('saveCategoryBtn');
        btn.disabled = true;
        const res = await fetch('add-category.php', {
            method: 'POST',
            body: fd
        });
        const j = await res.json();
        btn.disabled = false;
        const msg = document.getElementById('catMsg');
        if (j.success) {
            msg.innerHTML = '<div class="alert alert-success">Category added. Reloading...</div>';
            setTimeout(() => location.reload(), 900);
        } else {
            msg.innerHTML = '<div class="alert alert-danger">' + (j.error || 'Failed') + '</div>';
        }
    });
    </script>
</body>

</html>