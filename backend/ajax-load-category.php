<?php
include "db.php";

$result = mysqli_query($con, "SELECT * FROM blog_category ORDER BY category ASC");

echo "<option value=''>Select Category</option>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<option value='" . $row['id'] . "'>" . $row['category'] . "</option>";
}
