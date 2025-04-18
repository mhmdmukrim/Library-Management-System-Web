<?php
session_start();

if(isset($_POST['submit']))
{
    $title=$_POST['title'];
    $Description=$_POST['Description'];
    $MembershipNo=$_SESSION['MembershipNo'];

$sql1="insert into LMS.recommendations (Book_Name,Description,MembershipNo) values ('$title','$Description','$MembershipNo')"; 

echo $MembershipNo;

/*
if($conn->query($sql1) === TRUE){


echo "<script type='text/javascript'>alert('Success')</script>";
}
else
{//echo $conn->error;
echo "<script type='text/javascript'>alert('Error')</script>";
}*/
    
}
?> 
