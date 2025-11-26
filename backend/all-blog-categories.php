<?php
error_reporting(0);
session_start();
include 'db.php';
$alertmsg = '';
$link = 'All Blogs Category';
if (isset($_SESSION['user'])) {
  $user = $_SESSION['user'];
  $type = $_SESSION['type'];
  $subid = $_SESSION['subid'];
} else {
  header('location:logout.php');
}
if (isset($_POST['submit'])) {
  extract($_POST);
  $existing = mysqli_query($con, "SELECT * FROM `leads` WHERE `phone` = '$phone' OR `email` = '$email'");
  if (mysqli_num_rows($existing) > 0) {
    $alertmsg = '<sl-alert variant="danger" open duration="1500" closable>
                    <span class="mdi mdi-alpha-x-circle-outline"></span>
                    Leads With This Phone Or Email Already Exists
                  </sl-alert>';
  }
}
?>

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
                <h4 class="py-3 mb-4 text-white"><span class="text-muted fw-light"></span> All Categories</h4>

                <!-- DataTable with Buttons -->
                <div class="card">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatables-basic table table-bordered">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Category</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                    $query = mysqli_query($con, "SELECT * FROM `blog_category`");
                    $number = 1;
                    while ($data = mysqli_fetch_assoc($query)) {
                      $uniqid = $data['uniqid'];
                      echo '<tr>
                                  <td>' . $number . '</td>
                                  <td>
                                    ' . $data['category'] . '
                                  </td>
                                  <td width="100px;">
                                    <div class="d-flex justify-content-center align-items-center">
                                      <a href="edit-blog-category.php?id=' . $data['id'] . '" class="btn btn-primary waves-effect waves-light" style="margin-right:5px;">
                                        <span class="mdi mdi-square-edit-outline">Edit</span>
                                      </a>
                                      
                                      <a href="delete-blog-category.php?id=' . $data['id'] . '" class="btn btn-danger waves-effect waves-light">
                                        <span class="mdi mdi-trash-can-outline">Delete</span>
                                      </a>
                                    </div>
                                  </td>
                                </tr>';
                      $number++;
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