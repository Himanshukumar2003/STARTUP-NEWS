<?php
// preview.php
session_start();
include 'db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['preview'])) {
    // simple CSRF skip for preview or you can verify token
    $token = bin2hex(random_bytes(16));
    // collect posted fields (sanitized)
    $data = [
        'heading' => $_POST['heading'] ?? '',
        'subtitle' => $_POST['short_description'] ?? '',
        'content' => $_POST['content'] ?? '',
        'author' => $_POST['author'] ?? '',
        'slug' => $_POST['slug'] ?? '',
    ];
    // store in session for retrieval
    $_SESSION['preview_' . $token] = $data;
    echo json_encode(['token' => $token]);
    exit;
}

// GET: render preview if token provided
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];
    if (!isset($_SESSION['preview_' . $token])) {
        echo "<h3>Preview token invalid or expired.</h3>";
        exit;
    }
    $d = $_SESSION['preview_' . $token];
    // render HTML preview as front-end would show
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Preview — <?= htmlspecialchars($d['heading']) ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        padding: 20px;
        max-width: 900px;
        margin: auto;
    }

    .title {
        font-size: 28px;
        font-weight: 700;
    }

    .subtitle {
        color: #666;
    }
    </style>
</head>

<body>
    <article>
        <div class="title"><?= htmlspecialchars($d['heading']) ?></div>
        <?php if (!empty($d['subtitle'])): ?><div class="subtitle mb-3"><?= nl2br(htmlspecialchars($d['subtitle'])) ?>
        </div><?php endif; ?>
        <div class="meta mb-3">By <?= htmlspecialchars($d['author']) ?> — Preview</div>
        <div class="content"><?= $d['content'] ?></div>
    </article>
</body>

</html>
<?php
    exit;
}

// default
echo json_encode(['error' => 'invalid']);