<?php
  session_start();
	include 'db.php';
	$id=$_GET['id'];
  $getdata=mysqli_query($con,"SELECT * FROM `blogs` WHERE `id` = '$id'");
  while($data=mysqli_fetch_assoc($getdata)){
      $image=$data['image'];
  }
  unlink($image);
  $query=mysqli_query($con,"DELETE FROM `blogs` WHERE `id` = '$id'");
  if($query){
    $_SESSION['msg'] = '<sl-alert variant="success" open duration="1500" closable>
                    <span class="mdi mdi-check-circle-outline"></span>                  
                    Blog Deleted Successfully
                  </sl-alert>';
  	header('location:all-blogs.php');
  }
?>