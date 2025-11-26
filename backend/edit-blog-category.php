<?php
error_reporting(0);
session_start();
include 'db.php';
$alertmsg = '';
$link = 'Blogs Category';
$id = $_GET['id'];
$getdata = mysqli_query($con, "SELECT * FROM `blog_category` WHERE `id` = '$id'");
if (mysqli_num_rows($getdata) > 0) {
  while ($data = mysqli_fetch_assoc($getdata)) {
    $category = $data['category'];
  }
} else {
  header('location:all-jobs.php');
}
if (isset($_POST['submit'])) {
  extract($_POST);
  $query = mysqli_query($con, "UPDATE `blog_category` SET `category` =  '$category' WHERE `id` = '$id'");
  if ($query) {
    $_SESSION['msg'] = '<sl-alert variant="success" open duration="1500" closable>
    <span class="mdi mdi-check-circle-outline"></span>                  
    Category Updated Successfully
    </sl-alert>';
    header('location:all-blog-categories.php');
  } else {
    $_SESSION['msg'] = '<sl-alert variant="danger" open duration="1500" closable>
                  <span class="mdi mdi-alpha-x-circle-outline"></span>
                  Oops Something Went Wrong
                </sl-alert>';
    header('location:all-blog-categories.php');

  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog</title>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.0.0-beta.83/dist/themes/light.css" />

    <style>
    .main-content {
        padding: 20px;
        background: #f8f9fc;
        min-height: 100vh;
    }
    </style>
</head>

<body>
    <?php include('sidenav.php') ?>
    <section class="main-content">
        <h2 class="mb-5">Edit Blog Category</h2>

        <div class="container-xxl flex-grow-1 container-p-y">
            <!-- DataTable with Buttons -->
            <div class="card">
                <div class="card-body">
                    <form class="pt-0 row g-3 mt-3" method="post" enctype="multipart/form-data">
                        <div class="col-lg-6 col-xl-6 col-12 mb-3 mt-0">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="job-title" name="category" class="form-control"
                                        placeholder="Finance" aria-label="Finance" value="<?php echo $category; ?>"
                                        aria-describedby="basicPost2" />
                                    <label for="job-title">Category Name</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <button type="submit" name="submit"
                                class="btn btn-primary data-submit me-sm-3 me-1">Submit</button>
                            <button type="reset" class="btn btn-outline-secondary"
                                data-bs-dismiss="offcanvas">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

</body>

</html>