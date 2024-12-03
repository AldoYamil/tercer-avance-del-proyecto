<title>Login</title>
<head>
    <?php include '../partials/header.php'; ?>
</head>
<body class="login-body">
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="flip-container">
            <div class="flipper">
                <!-- Login Card (Front) -->
                <div class="front">
                    <div class="card login-card shadow-lg">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-5 border-end d-flex flex-column justify-content-center align-items-center">
                                    <img class="img-fluid logo mb-3" src="../../assets/img/logo.png" alt="logo-cafpath" style="max-width: 200px;">
                                    <h4 class="text-center mb-2">CafPath</h4>
                                    <p class="text-center text-muted">Welcome! Please login to your account or create an account.</p>
                                </div>
                                
                                <div class="col-md-7 ps-md-4">
                                    <form action="../../app/Controllers/AuthUser.php" method="post">
                                        <h3 class="text-center mb-4">LOGIN</h3>
                                        
                                        <?php if(isset($_GET['error'])): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?php echo htmlspecialchars($_GET['error']); ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if(isset($_GET['success'])): ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <?php echo htmlspecialchars($_GET['success']); ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="mb-4">
                                            <input type="user" class="form-control" name="username" required placeholder="Username" 
                                                   value="<?php echo isset($_COOKIE['remember_username']) ? htmlspecialchars($_COOKIE['remember_username']) : ''; ?>">
                                        </div>
                                        
                                        <div class="mb-4 position-relative">
                                            <input type="password" class="form-control" id="password" name="password" required placeholder="Password">
                                            <span id="togglePassword" class="password-toggle">
                                                <svg id="eyeOff" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
                                                    <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5S0 8 0 8s.939 1.721 2.641 3.238l-.702.702a.5.5 0 0 0 .707.707l.702-.702c1.305 1.007 2.836 1.755 4.652 1.755 1.816 0 3.347-.748 4.652-1.755l.702.702a.5.5 0 0 0 .707-.707l-.702-.702zM8 13c1.816 0 3.347-.748 4.652-1.755l-.702-.702A6.713 6.713 0 0 1 8 12c-1.268 0-2.39-.375-3.367-.962l-.702.702C5.347 12.252 6.878 13 8 13z"/>
                                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                </svg>
                                                <svg id="eyeOn" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16" style="display: none;">
                                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                </svg>
                                            </span>
                                        </div>
                                        
                                        <div class="mb-4 form-check">
                                            <input type="checkbox" class="form-check-input" name="remember" id="remember">
                                            <label class="form-check-label" for="remember">Remember me</label>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary w-100 mb-4">Login</button>
                                        
                                        <div class="text-center">
                                            <a href="#" class="text-decoration-none d-block mb-3">Forgot password?</a>
                                            <a href="#" class="text-decoration-none" id="showRegister">Register</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Register Card (Back) - Step 1: Personal Information -->
                <div class="back">
                    <div class="card login-card shadow-lg">
                        <div class="card-body p-4">
                            <h3 class="text-center mb-4">Enter your personal information</h3>
                            <form id="registerForm1">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <input type="text" class="form-control" name="name" required placeholder="Name">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <input type="text" class="form-control" name="lastname" required placeholder="Last Name">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="surname" placeholder="Second Last Name">
                                </div>
                                
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="company" placeholder="Company">
                                </div>
                                
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="phone" placeholder="Phone">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <input type="text" class="form-control" name="street" placeholder="Street">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <input type="text" class="form-control" name="number" placeholder="Number">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="colony" placeholder="Neighborhood">
                                </div>
                                
                                <button type="button" class="btn btn-primary w-100 mb-3" id="nextStep">Next</button>
                                <div class="text-center">
                                    <a href="#" class="text-decoration-none" id="showLogin">Back to Login</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Register Card (Back) - Step 2: Account Information -->
                <div class="back-step2" style="display: none;">
                    <div class="card login-card shadow-lg">
                        <div class="card-body p-4">
                            <h3 class="text-center mb-4">Create an username and password</h3>
                            <form action="../../app/Controllers/RegisterController.php" method="POST" id="registerForm2">
                                <!-- Hidden fields for step 1 data -->
                                <input type="hidden" name="name">
                                <input type="hidden" name="lastname">
                                <input type="hidden" name="surname">
                                <input type="hidden" name="company">
                                <input type="hidden" name="phone">
                                <input type="hidden" name="street">
                                <input type="hidden" name="number">
                                <input type="hidden" name="colony">
                                
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="username" required placeholder="Username">
                                </div>
                                
                                <div class="mb-4">
                                    <input type="password" class="form-control" name="password" required placeholder="Password">
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 mb-3">Register</button>
                                <div class="text-center">
                                    <a href="#" class="text-decoration-none" id="previousStep">Back</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include '../partials/footerViews.php'; ?>
</html>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordField = document.getElementById('password');
        const eyeOff = document.getElementById('eyeOff');
        const eyeOn = document.getElementById('eyeOn');
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        
        if (type === 'password') {
            eyeOff.style.display = 'block';
            eyeOn.style.display = 'none';
        } else {
            eyeOff.style.display = 'none';
            eyeOn.style.display = 'block';
        }
    });

    // Flip animation handlers
    document.getElementById('showRegister').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector('.flip-container').classList.add('flipped');
    });

    document.getElementById('showLogin').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector('.flip-container').classList.remove('flipped');
    });

    // Registration form steps handling
    document.getElementById('nextStep').addEventListener('click', function() {
        // Get all form data from step 1
        const form1 = document.getElementById('registerForm1');
        const form2 = document.getElementById('registerForm2');
        
        // Validate form1
        if (!form1.checkValidity()) {
            form1.reportValidity();
            return;
        }
        
        // Transfer data to hidden fields in form 2
        const formData = new FormData(form1);
        for (let [name, value] of formData.entries()) {
            form2.querySelector(`input[name="${name}"]`).value = value;
        }
        
        // Hide step 1 and show step 2
        document.querySelector('.back').style.display = 'none';
        document.querySelector('.back-step2').style.display = 'block';
    });

    document.getElementById('previousStep').addEventListener('click', function(e) {
        e.preventDefault();
        // Show step 1 and hide step 2
        document.querySelector('.back').style.display = 'block';
        document.querySelector('.back-step2').style.display = 'none';
    });
</script>