<?php

session_start();

require 'config/koneksi.php';

$username = mysqli_real_escape_string($conn,$_POST['username']);
$password = md5($_POST['password']);

$query = mysqli_query($conn,"SELECT * FROM users
WHERE username='$username'
AND password='$password'");

if(mysqli_num_rows($query)>0){

    $data = mysqli_fetch_assoc($query);

    $_SESSION['login']=true;
    $_SESSION['id_user']=$data['id_user'];
    $_SESSION['nama']=$data['nama'];

    header("Location: dashboard.php");

}else{

    header("Location: login.php?pesan=gagal");

}