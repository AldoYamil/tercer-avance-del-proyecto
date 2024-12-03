<!DOCTYPE html>
<html>
<head>
    <title data-translate="IndexTitlePageName">Homepage</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <header>
        <?php include 'views/partials/homeNav.php'; ?>
    </header>
    <main>
        <!-- Hero Banner Section -->
        <div class="container-fluid p-0">
            <div class="position-relative">
                <img src="assets/img/banner.jpg" alt="banner" class="banner-image">
                <div class="banner-overlay">
                    <h1 class="display-1 fw-bold mb-4" data-translate="IndexH1BannerTitle" data-aos="fade-up">CAFPATH</h1>
                    <p class="lead w-75 text-center mb-5" data-translate="IndexParagraphBannerDesc" data-aos="fade-up" data-aos-delay="200">
                        CAFPATH is your trusted partner in logistics and supply chain management
                    </p>
                    <div data-aos="fade-up" data-aos-delay="400">
                        <a href="views/public/aboutUsView.php" class="btn btn-primary btn-lg me-3" data-translate="IndexBtnAboutUs">About us</a>
                        <a href="views/public/contactForm.php" class="btn btn-outline-light btn-lg" data-translate="IndexBtnContactUs">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Section -->
        <section id="services" class="container my-5 py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-4 fw-bold mb-3" data-translate="IndexH2ServicesTitle">Our Services</h2>
                <p class="lead text-muted w-75 mx-auto" data-translate="IndexParagraphServicesDesc">Comprehensive logistics solutions tailored to your needs</p>
            </div>
            <div class="row g-4">
                <!-- Logistics Service -->
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="card service-card shadow-sm hover-lift h-100">
                        <div class="card-body text-center d-flex flex-column p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="service-icon mb-3" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="#00abfb" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 21v-13l9 -4l9 4v13" />
                                <path d="M13 13h4v8h-10v-6h6" />
                                <path d="M13 21v-9a1 1 0 0 0 -1 -1h-2a1 1 0 0 0 -1 1v3" />
                            </svg>
                            <h3 class="h5 fw-bold mb-3" data-translate="IndexH3LogisticTitle">Logistics</h3>
                            <p class="card-text text-muted flex-grow-1" data-translate="IndexParagraphLogisticDesc">
                                We provide seamless management of inventory, orders, and shipments...
                            </p>
                            <a href="views/public/logisticView.php" class="btn btn-outline-primary mt-3" data-translate="IndexBtnReadMore">Read more...</a>
                        </div>
                    </div>
                </div>

                <!-- Delivery Service -->
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="card service-card shadow-sm hover-lift h-100">
                        <div class="card-body text-center d-flex flex-column p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="service-icon mb-3" viewBox="0 0 24 24" fill="none" stroke="#00abfb" stroke-linecap="round" stroke-linejoin="round" width="48" height="48" stroke-width="1">
                                <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"></path>
                            </svg>
                            <h3 class="h5 fw-bold mb-3" data-translate="IndexH3DeliveryTitle">Delivery</h3>
                            <p class="card-text text-muted flex-grow-1" data-translate="IndexParagraphDeliveryDesc">
                                Our delivery service is focused on reliability and speed...
                            </p>
                            <a href="views/public/deliveryView.php" class="btn btn-outline-primary mt-3" data-translate="IndexBtnReadMore">Read more...</a>
                        </div>
                    </div>
                </div>

                <!-- Tracking Service -->
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="card service-card shadow-sm hover-lift h-100">
                        <div class="card-body text-center d-flex flex-column p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="service-icon mb-3" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="#00abfb" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                <path d="M12 12m-8 0a8 8 0 1 0 16 0a8 8 0 1 0 -16 0" />
                                <path d="M12 2l0 2" />
                                <path d="M12 20l0 2" />
                                <path d="M20 12l2 0" />
                                <path d="M2 12l2 0" />
                            </svg>
                            <h3 class="h5 fw-bold mb-3" data-translate="IndexH3TrackingTitle">Tracking</h3>
                            <p class="card-text text-muted flex-grow-1" data-translate="IndexParagraphTrackingDesc">
                                With our tracking service, clients can monitor shipments in real-time...
                            </p>
                            <a href="views/public/trackingView.php" class="btn btn-outline-primary mt-3" data-translate="IndexBtnReadMore">Read more...</a>
                        </div>
                    </div>
                </div>

                <!-- Support Service -->
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="card service-card shadow-sm hover-lift h-100">
                        <div class="card-body text-center d-flex flex-column p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="service-icon mb-3" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="#00abfb" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M12 7v5l2 2" />
                            </svg>
                            <h3 class="h5 fw-bold mb-3" data-translate="IndexH3SupportTitle">24/7 Support</h3>
                            <p class="card-text text-muted flex-grow-1" data-translate="IndexParagraphSupportDesc">
                                Round-the-clock customer support to assist you with any queries or concerns...
                            </p>
                            <a href="views/public/supportView.php" class="btn btn-outline-primary mt-3" data-translate="IndexBtnReadMore">Read more...</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="container-fluid bg-body py-5">
            <div class="container">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center" data-aos="fade-up">
                        <h2 class="display-4 fw-bold mb-3" data-translate="IndexH2HowItWorksTitle">How It Works</h2>
                        <p class="lead text-muted mb-5" data-translate="IndexParagraphHowItWorksDesc">
                            Our system provides a robust solution for managing inventory and orders, streamlining both logistical and administrative tasks.
                        </p>
                    </div>
                </div>

                <!-- How It Works Section Features -->
                <div class="row g-4">
                    <!-- Feature 1 -->
                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-box text-primary me-3 fs-4"></i>
                            <div>
                                <h3 class="h5 fw-bold mb-2" data-translate="IndexListFeature1">
                                    <span class="text-primary">Inventory Management:</span>
                                </h3>
                                <p class="text-muted" data-translate="IndexParagraphHowItWorksFeature1">Efficient inventory tracking and management to optimize stock levels and reduce waste.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-graph-up text-primary me-3 fs-4"></i>
                            <div>
                                <h3 class="h5 fw-bold mb-2" data-translate="IndexListFeature2">
                                    <span class="text-primary">Order Processing:</span>
                                </h3>
                                <p class="text-muted" data-translate="IndexParagraphHowItWorksFeature2">Streamlined order processing with automated validation and confirmation systems.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-truck text-primary me-3 fs-4"></i>
                            <div>
                                <h3 class="h5 fw-bold mb-2" data-translate="IndexListFeature3">
                                    <span class="text-primary">Route Optimization:</span>
                                </h3>
                                <p class="text-muted" data-translate="IndexParagraphHowItWorksFeature3">Smart routing algorithms to ensure efficient delivery paths and reduced transit times.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 4 -->
                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-shield-check text-primary me-3 fs-4"></i>
                            <div>
                                <h3 class="h5 fw-bold mb-2" data-translate="IndexListFeature4">
                                    <span class="text-primary">Security Measures:</span>
                                </h3>
                                <p class="text-muted" data-translate="IndexParagraphHowItWorksFeature4">Advanced security protocols to protect your shipments and sensitive information.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 5 -->
                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="500">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-graph-up-arrow text-primary me-3 fs-4"></i>
                            <div>
                                <h3 class="h5 fw-bold mb-2" data-translate="IndexListFeature5">
                                    <span class="text-primary">Analytics & Reporting:</span>
                                </h3>
                                <p class="text-muted" data-translate="IndexParagraphHowItWorksFeature5">Comprehensive reporting tools for tracking performance metrics and business insights.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 6 -->
                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="600">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-headset text-primary me-3 fs-4"></i>
                            <div>
                                <h3 class="h5 fw-bold mb-2" data-translate="IndexListFeature6">
                                    <span class="text-primary">Customer Support:</span>
                                </h3>
                                <p class="text-muted" data-translate="IndexParagraphHowItWorksFeature6">24/7 dedicated support team to assist with any inquiries or concerns.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-4 fw-bold mb-3" data-translate="IndexH2TestimonialsTitle">What Our Clients Say</h2>
                <p class="lead text-muted mb-5" data-translate="IndexParagraphTestimonialsDesc">Real feedback from real customers</p>
            </div>
            <div class="row g-4 justify-content-center">
                <!-- Testimonial 1 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-card-modern">
                        <i class="bi bi-quote quote-icon"></i>
                        <div class="testimonial-content">
                            <div class="text-center mb-4">
                                <img src="assets/img/quoteUser1.jpg" alt="User Image" class="author-image">
                            </div>
                            <blockquote data-translate="IndexBlockquoteTestimonial1">
                                "CAFPATH has revolutionized our logistics process. Their real-time tracking and efficient order management have significantly improved our delivery times."
                            </blockquote>
                            <div class="testimonial-author">
                                <div class="author-info text-center">
                                    <h4 class="author-name" data-translate="IndexParagraphTestimonial1Author">Jon Jones</h4>
                                    <p class="author-title" data-translate="IndexParagraphTestimonial1AuthorTitle">CEO of Logistics Co.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial-card-modern">
                        <i class="bi bi-quote quote-icon"></i>
                        <div class="testimonial-content">
                            <div class="text-center mb-4">
                                <img src="assets/img/quoteUser2.jpg" alt="User Image" class="author-image">
                            </div>
                            <blockquote data-translate="IndexBlockquoteTestimonial2">
                                "The transparency and reliability of CAFPATH's services have been a game-changer for our business. We can now focus on growth, knowing our logistics are in good hands."
                            </blockquote>
                            <div class="testimonial-author">
                                <div class="author-info text-center">
                                    <h4 class="author-name" data-translate="IndexParagraphTestimonial2Author">Marlon Wayans</h4>
                                    <p class="author-title" data-translate="IndexParagraphTestimonial2AuthorTitle">Operations Manager at Retail Inc.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="testimonial-card-modern">
                        <i class="bi bi-quote quote-icon"></i>
                        <div class="testimonial-content">
                            <div class="text-center mb-4">
                                <img src="assets/img/quoteUser3.jpg" alt="User Image" class="author-image">
                            </div>
                            <blockquote data-translate="IndexBlockquoteTestimonial3">
                                "Thanks to CAFPATH, our inventory management is more efficient than ever. Their system has reduced our operational costs and increased productivity."
                            </blockquote>
                            <div class="testimonial-author">
                                <div class="author-info text-center">
                                    <h4 class="author-name" data-translate="IndexParagraphTestimonial3Author">Ángel David Revilla Lenoci</h4>
                                    <p class="author-title" data-translate="IndexParagraphTestimonial3AuthorTitle">Supply Chain Director at Manufacturing Ltd.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action Section -->
        <section class="container-fluid bg-primary text-white py-5 mt-5">
            <div class="container text-center py-3">
                <h2 class="display-4 fw-bold mb-4" data-aos="fade-up" data-translate="IndexH2CallToActionTitle">Ready to Get Started?</h2>
                <p class="lead mb-4" data-aos="fade-up" data-aos-delay="100" data-translate="IndexParagraphCallToActionDesc">
                    Join thousands of satisfied customers who trust CAFPATH with their logistics needs
                </p>
                <div data-aos="fade-up" data-aos-delay="200">
                    <a href="views/public/contactForm.php" class="btn btn-light btn-lg" data-translate="IndexBtnContactUsToday">Contact Us Today</a>
                </div>
            </div>
        </section>
    </main>

    <?php include 'views/partials/footer.php'; ?>

    <!-- Scripts -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                once: true,
                offset: 100
            });
        });
    </script>
</body>
</html>