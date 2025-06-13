<?php
require_once 'config.php';
require_once 'db_connect.php';
require_once 'functions.php';



if (logged_in()) {
    redirect('../index.php');
}

$errors = [];
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST["username"]);
    $email = sanitize_input($_POST["email"]);
    $password = sanitize_input($_POST["password"]);
    $confirmedPassword = sanitize_input($_POST["confirmedPassword"]);
    // $phone_num = sanitize_input($_POST["phone_num"]);
    $first_name = sanitize_input($_POST["first_name"]);
    $last_name = sanitize_input($_POST["last_name"]);
    

    if (empty($username) || empty($email) || empty($first_name) || empty($last_name)) {
        $errors[] = "All fields are required";
    }

    // Email format validation - THIS WAS MISSING FROM YOUR ORIGINAL CODE!
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

   
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    } elseif ($password != $confirmedPassword) {
        $errors[] = "Passwords do not match";
    }


    
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT user_id FROM users 
                                WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Email already exists. Please choose another one.";
            
        } else {
        
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            //insert new user
            $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name) 
                                    VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $email, $password_hash, $first_name, $last_name);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Registration successful! Please log in.";
                header("Location: ../login.php");
            } else {
                $errors[] = "Registration failed: " . $conn->error;
            }
        }
    }

    // If there are errors, store them in session and redirect back(ai helped with session msgs)
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        redirect("../register.php");
    }

} else {
    redirect('../register.php');    
}
?>


<!-- $errors = [];
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST["username"]);
    $email = sanitize_input($_POST["email"]);
    $password = sanitize_input($_POST["password"]);
    $confirmedPassword = sanitize_input($_POST["confirmedPassword"]);
    $phone_num = sanitize_input($_POST["phone_num"]);
    $first_name = sanitize_input($_POST["first_name"]);
    $last_name = sanitize_input($_POST["last_name"]);
    //username
    if (empty($username)) {
        $errors[] = "Username is required";
    } else {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "Username already exists";
        }
    }
    // email
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    } else {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            $errors[] = "Email already exists. Please choose another one.";
        }
    }
    //password validation
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    } elseif ($password != $confirmedPassword) {
        $errors[] = "Passwords do not match";
    }
    // phone_num
    if (empty($phone_num)) {
        $errors[] = "Phone number is required";
    }
    // first_name
    if (empty($first_name)) {
        $errors[] = "First name is required";
    }
    // last_name
    if (empty($last_name)) {
        $errors[] = "Last name is required";
    }
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name, phone_num) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $email, $hash, $first_name, $last_name, $phone_num);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Registration successful! Please log in.";
            redirect("../login.php");
        } else {
            $errors[] = "Registration failed: " . $conn->error;
        }
    }
    // If there are errors, store them in session and redirect back(ai helped with session msgs)
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        redirect("../register.php");
    }
} else {
    redirect('../register.php');    
} -->


<!-- if(isset($_POST["signup"])) {
    $username = sanitize_input($_POST["username"]);
    $email = sanitize_input($_POST["email"]);
    $password = sanitize_input($_POST["password"]);
    $confirmedPassword = sanitize_input($_POST["confirmedPassword"]);
    $phone_num = sanitize_input($_POST["phone_num"]);
    $first_name = sanitize_input($_POST["first_name"]);
    $last_name = sanitize_input($_POST["last_name"]); 
    $checkEmail="SELECT * FROM users where email='$email'";
    $result=$conn->query($checkEmail);
    if($result->num_rows>0){
        echo "Email address already exists";
    } else {
        $insertQuery = "INSERT INTO users (username, email, password_hash, first_name, last_name, phone_num) 
                        VALUES ('$username', '$email', '$password', '$phone_num', '$first_name', '$last_name')";
            if($conn->query($insertQuery)==TRUE){
                header("location: index.php");
            } else {
                echo "Error:".$conn->error;
            }
}
} -->
