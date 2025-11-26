<?php
error_reporting(0);
session_start();
include 'db.php';
$alertmsg = '';
$link = 'All Blogs';
if (isset($_SESSION['user'])) {
  $user = $_SESSION['user'];
  $type = $_SESSION['type'];
  $subid = $_SESSION['subid'];
  if ($type == 'Admin') {
    $query = mysqli_query($con, "SELECT * FROM `user` WHERE `userid` = '$user'");
  } else {
    $query = mysqli_query($con, "SELECT * FROM `subadmin` WHERE `userid` = '$user'");
  }
  if (mysqli_num_rows($query) > 0) {
    while ($userdata = mysqli_fetch_assoc($query)) {
      $username = $userdata['name'];
    }
  } else {
    header('location:logout.php');
  }
} else {
  header('location:logout.php');
}
?>

<head>
    <title>All News</title>
</head>

<body>
    <div class="sl-toast-stack">
        <?php
    echo $alertmsg;
    echo $_SESSION['msg'];
    unset($_SESSION['msg']);
    ?>
    </div>

    <?php include 'sidenav.php'; ?>
    <div class="main-content">
        <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
                <h4 class="py-3 mb-4 text-black"><span class="text-muted fw-light"></span> All News</h4>

                <!-- DataTable with Buttons -->
                <div class="card">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatables-basic table table-bordered">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Image</th>
                                    <th>Heading</th>
                                    <th>Content</th>
                                    <th>Category</th>
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                $query = mysqli_query($con, "SELECT * FROM `news`");


                while ($data = mysqli_fetch_assoc($query)) {

                  $category = $data['category_id'];
                  $catquery = mysqli_query($con, "SELECT `category` FROM `blog_category` WHERE `id` = '$category'");
                  $catdata = mysqli_fetch_assoc($catquery);
                  $catname = $catdata['category'];
                  $image = explode(',', $data['featured_image']);



                  echo '<tr>
                                  <td>' . $data['id'] . '</td>
                                  <td><img src="' . $image[0] . '" width="50px"></td>
                                  <td>' . $data['heading'] . '</td>
                                  <td>' . strip_tags(substr($data['content'], 0, 100)) . '</td>
                                  <td>' . $catname . '</td>
                                  <td width="100px;">
                                    <div class="d-flex justify-content-center align-items-center">
                                      <a href="edit-news.php?id=' . $data['id'] . '" class="btn btn-primary waves-effect waves-light" style="margin-right:5px;">
                                        <span class="mdi mdi-square-edit-outline">Edit</span>
                                      </a>
                                      <a href="delete-news.php?id=' . $data['id'] . '" class="btn btn-danger waves-effect waves-light">
                                        <span class="mdi mdi-trash-can-outline">Delete</span>
                                      </a>
                                    </div>
                                  </td>
                                </tr>';
                }
                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Modal to add new record -->

                <!--/ DataTable with Buttons -->
            </div>
            <!-- / Content -->

            <!-- Footer -->
            <?php include 'footer.php'; ?>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
        </div>
    </div>

</body>