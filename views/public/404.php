<!DOCTYPE html>
<html>
<head>
    <title data-translate="404Title">404 Page Not Found</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <header>
        <?php include '../partials/header.php'; ?>
    </header>

    <main class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="text-center" data-aos="fade-up">
            <i class="bi bi-exclamation-triangle display-1 text-primary mb-4"></i>
            <h1 class="display-4 fw-bold mb-4" data-translate="404H1">404 - Page Not Found</h1>
            <p class="lead mb-5" data-translate="404Paragraph">
                Oops! The page you're looking for doesn't exist or has been moved.
            </p>
            <div>
                <a href="../../index.php" class="btn btn-primary btn-lg me-3" data-translate="404BtnHome">
                    <i class="bi bi-house-door me-2"></i>Go Home
                </a>
                <a href="contactForm.php" class="btn btn-outline-primary btn-lg" data-translate="404BtnContact">
                    <i class="bi bi-envelope me-2"></i>Contact Support
                </a>
            </div>
        </div>
    </main>

    <?php include '../partials/footer.php'; ?>

    <!-- Scripts -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                once: true
            });
        });
    </script>
</body>
</html>
