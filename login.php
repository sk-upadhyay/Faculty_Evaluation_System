<?php
session_start();
include('./db_connect.php');
if (isset($_GET['action']) && $_GET['action'] == 'login') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_type = $_POST['login'];

    // Determine the table based on user type
    if ($user_type == '3') {
        $table = 'student_list';
        $_SESSION['login_view_folder'] = 'student/';
    } else if ($user_type == '2') {
        $table = 'faculty_list';
        $_SESSION['login_view_folder'] = 'faculty/';
    } else if ($user_type == '1') {
        $table = 'admin_list'; // Change this to your actual admin table
        $_SESSION['login_view_folder'] = 'admin/';
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid user type']);
        exit();
    }

    // Fetch the user data from the table
    $query = "SELECT * FROM $table WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['login_id'] = $user['school_id'];
            $_SESSION['login_email'] = $user['email'];
            $_SESSION['login_firstname'] = $user['firstname'];
            $_SESSION['login_avatar']= $user['avatar'];
            $_SESSION['login_type'] = $user_type;

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Username or password is incorrect']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Username or password is incorrect']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'header.php' ?>

<style>
  .login-logo {
    text-align: center;
    margin-bottom: 20px;
  }
  .login-logo img {
    width: 200px;
    height: auto;
  }
</style>

<body class="hold-transition login-page bg-white">
  <h2><b>COER AQMS</b></h2>
  <div class="login-box">
    <div class="login-logo">
      <a href="#" class="text-white"></a>
    </div>
    <div class="card">
      <div class="card-body login-card-body">
        <div class="login-logo">
          <img src="profpraisal.png" alt="Your Image Alt Text">
        </div>
        <form action="" id="login-form">
          <div class="input-group mb-3">
            <input type="email" class="form-control" name="email" required placeholder="Email">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" name="password" required placeholder="Password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock" onclick="togglePasswordVisibility()"></span>
                <span class="fas fa-eye" style="display:none;" onclick="togglePasswordVisibility()"></span>
              </div>
            </div>
          </div>
          <div class="form-group mb-3">
            <label for="">Login As</label>
            <select name="login" class="custom-select custom-select-sm">
              <option value="3">Student</option>
              <option value="2">Faculty</option>
              <option value="1">Admin</option>
            </select>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="remember">
                <label for="remember">
                  Remember Me
                </label>
              </div>
            </div>
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </div>
            <p class="mb-1">
              Don't have an account? <a href="signup.php" class="text-center">Sign up</a>
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function () {
      $('#login-form').submit(function (e) {
          e.preventDefault();
        
        if ($(this).find('.alert-danger').length > 0)
          $(this).find('.alert-danger').remove();
        $.ajax({
          url: 'ajax.php?action=login',
          method: 'POST',
          data: $(this).serialize(),
          error: err => {
           
          },
          success: function (resp) {
            if (resp == 1) {
              location.href = 'index.php?page=home';
            } else {
              $('#login-form').prepend('<div class="alert alert-danger">' + resp + '.</div>')
              
            }
          }
        })
      })
    })
  </script>

  <script>
  
    function togglePasswordVisibility() {
        var passwordInput = document.querySelector('[name="password"]');
        var lockIcon = document.querySelector('.fa-lock');
        var eyeIcon = document.querySelector('.fa-eye');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            lockIcon.style.display = 'none';
            eyeIcon.style.display = 'block';
        } else {
            passwordInput.type = 'password';
            lockIcon.style.display = 'block';
            eyeIcon.style.display = 'none';
        }
    }
  </script>

  <?php include 'footer.php' ?>
</body>
</html>
