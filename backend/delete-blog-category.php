<?php
  session_start();
	include 'db.php';
	$id=$_GET['id'];

  $query=mysqli_query($con,"DELETE FROM `blog_category` WHERE `id` = '$id'");
  if($query){
    $_SESSION['msg'] = '<sl-alert variant="success" open duration="1500" closable>
                    <span class="mdi mdi-check-circle-outline"></span>                  
                    Blog Deleted Successfully
                  </sl-alert>';
  	header('location:all-blog-categories.php');
  }
?>