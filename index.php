<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login | Aplikasi Persediaan Obat</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="description" content="Aplikasi Persediaan Obat pada xy">
  <meta name="author" content="Wildan Mufti & Ramadhani Akbaru R" />

  <!-- Favicon -->
  <link rel="shortcut icon" href="assets/img/BPK-icon.jpg" />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Custom CSS -->
  <style>
    :root {
      --primary: #4361ee;
      --primary-light: #e6f0ff;
      --secondary: #3f37c9;
      --success: #4cc9a0;
      --danger: #f72585;
      --warning: #f8961e;
      --light: #f8f9fa;
      --dark: #212529;
      --white: #ffffff;
      --gray: #6c757d;
      --border-radius: 12px;
      --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      --transition: all 0.3s ease;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    
    .login-container {
      max-width: 450px;
      width: 100%;
      background: var(--white);
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      overflow: hidden;
      transition: var(--transition);
    }
    
    .login-container:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }
    
    .login-header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      color: var(--white);
      padding: 25px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    
    .login-header::before {
      content: "";
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
      transform: rotate(-15deg);
    }
    
    .logo {
      height: 60px;
      margin-bottom: 10px;
    }
    
    .login-title {
      font-size: 1.8rem;
      font-weight: 700;
      margin: 0;
      line-height: 1.3;
    }
    
    .login-body {
      padding: 30px;
    }
    
    .login-subtitle {
      font-size: 1.1rem;
      color: var(--gray);
      text-align: center;
      margin-bottom: 30px;
      font-weight: 500;
    }
    
    .form-group {
      margin-bottom: 20px;
      position: relative;
    }
    
    .form-control {
      height: 50px;
      border-radius: var(--border-radius);
      border: 1px solid #e0e0e0;
      padding-left: 45px;
      font-size: 1rem;
      transition: var(--transition);
    }
    
    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
    }
    
    .input-icon {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--gray);
      font-size: 1.1rem;
    }
    
    .btn-login {
      background-color: var(--primary);
      color: var(--white);
      border: none;
      height: 50px;
      border-radius: var(--border-radius);
      font-size: 1.1rem;
      font-weight: 600;
      transition: var(--transition);
      width: 100%;
    }
    
    .btn-login:hover {
      background-color: var(--secondary);
      transform: translateY(-2px);
    }
    
    .alert {
      border-radius: var(--border-radius);
      border-left: 4px solid transparent;
    }
    
    .alert-danger {
      background-color: rgba(247, 37, 133, 0.1);
      border-left-color: var(--danger);
      color: var(--danger);
    }
    
    .alert-success {
      background-color: rgba(76, 201, 160, 0.1);
      border-left-color: var(--success);
      color: var(--success);
    }
    
    .alert-icon {
      margin-right: 10px;
      font-size: 1.2rem;
    }
    
    @media (max-width: 576px) {
      .login-container {
        max-width: 100%;
      }
      
      .login-header {
        padding: 20px;
      }
      
      .login-title {
        font-size: 1.5rem;
      }
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="login-header">
      <img class="logo" src="assets/img/BPK-Jatim-MANTAP.png" alt="Logo BPK">
      <h1 class="login-title">Persediaan Obat Poli Umum</h1>
    </div>
    
    <div class="login-body">
      <?php
      // fungsi untuk menampilkan pesan
      if (empty($_GET['alert'])) {
        echo "";
      }
      // jika alert = 1
      elseif ($_GET['alert'] == 1) {
        echo "<div class='alert alert-danger mb-4'>
                <div class='d-flex align-items-center'>
                  <i class='fas fa-times-circle alert-icon'></i>
                  <div>
                    <h5 class='mb-1'>Gagal Login!</h5>
                    <p class='mb-0'>Username atau Password salah, cek kembali Username dan Password Anda.</p>
                  </div>
                </div>
              </div>";
      }
      // jika alert = 2
      elseif ($_GET['alert'] == 2) {
        echo "<div class='alert alert-success mb-4'>
                <div class='d-flex align-items-center'>
                  <i class='fas fa-check-circle alert-icon'></i>
                  <div>
                    <h5 class='mb-1'>Sukses!</h5>
                    <p class='mb-0'>Anda telah berhasil logout.</p>
                  </div>
                </div>
              </div>";
      }
      ?>

      <p class="login-subtitle">
        <i class="fas fa-user-circle"></i> Silahkan masuk dengan akun Anda
      </p>
      
      <form action="login-check.php" method="POST">
        <div class="form-group">
          <i class="fas fa-user input-icon"></i>
          <input type="text" class="form-control" name="username" placeholder="Username" autocomplete="off" required />
        </div>

        <div class="form-group">
          <i class="fas fa-lock input-icon"></i>
          <input type="password" class="form-control" name="password" placeholder="Password" required />
        </div>
        
        <div class="form-group mt-4">
          <button type="submit" class="btn btn-login" name="login">
            <i class="fas fa-sign-in-alt me-2"></i> Masuk
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>