<?php
require('dbconn.php');

$bookid=$_GET['id1'];

$MembershipNo=$_GET['id2'];

$sql="delete from tfxpuawt_lms.record where MembershipNo='$MembershipNo' and BookId='$bookid'";

if($conn->query($sql) === TRUE)
{
	$sql1="insert into tfxpuawt_lms.message (MembershipNo,Msg,Date,Time) values ('$MembershipNo','Your request for issue of BookId: $bookid  has been rejected',curdate(),curtime())";
 $result=$conn->query($sql1);
echo "<script type='text/javascript'>alert('Success')</script>";
header( "Refresh:0.01; url=issue_requests.php", true, 303);
}
else
{
	echo "<script type='text/javascript'>alert('Error')</script>";
    header( "Refresh:0.01; url=issue_requests.php", true, 303);

}




?>