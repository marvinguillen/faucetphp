<?php
require_once "header.php";
$con=mysqli_connect("localhost","root","","phpscdatabase");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

$sql="SELECT * from tbl_admin where admin_id = 1";

/*if ($result=mysqli_query($con,$sql))
  {
  // Fetch one and one row
  while ($row=mysqli_fetch_row($result))
    {
    printf ("%s (%s)\n",$row['admin_id'],$row['username']);
    }
  // Free result set
  mysqli_free_result($result);
}
*/

$prepare=$db->mysqli->prepare("SELECT admin_id,username,password from tbl_admin where admin_id = 1");

  //$prepare->bind_param('i',1);

  $prepare->execute();

  $prepare->bind_result($aid,$username,$pass);

  $prepare->fetch();

  $prepare->close();
  Echo $username;
//mysqli_close($con);
?>