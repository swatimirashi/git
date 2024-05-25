<?php
session_start();
include("db.php"); // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == "POST") 
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) 
    {
        // Check if admin exists in the admin_page table
        $admin_query = "SELECT * FROM admin_login WHERE email='$email' AND password='$password' LIMIT 1";
        $admin_result = mysqli_query($conn, $admin_query);

        if ($admin_result && mysqli_num_rows($admin_result) > 0) 
        {
            // Admin login successful
            $_SESSION['admin_logged_in'] = true;
            header("location: adminpage.html"); // Redirect to admin page
            exit();
        }
        else
        {
            $_SESSION['login_error']="Invalid admin email or password.Please try again.";
            header("location:login.php");
            exit();
        }
    }
        else{
            $_SESSION['login_error']="Please enter correct email and password.";
            header("location:login.php");
            exit();
    
        }
    }



?>