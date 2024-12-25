<!-- Modern Footer -->
<footer class="modern-footer">
    <div class="footer-content">
        <div class="container">
            <div class="row g-4 justify-content-between">
                <!-- Brand Section -->
                <div class="col-lg-4">
                    <div class="footer-brand">
                        <div class="brand-icon">
                            <img src="assets/img/footer-logo.png" alt="Farmers Gateway" class="img-fluid" style="max-width: 120px;">
                        </div>
                    </div>
                    <p class="footer-description">Empowering farmers with technology and innovation for sustainable agriculture.</p>
                </div>

                <!-- Address Section -->
                <div class="col-lg-4">
                    <div class="footer-info">
                        <h4>Our Address</h4>
                        <p class="location">
                            Mangalore, Valachil<br>
                            Srinivas College - 575029, Karnataka
                        </p>
                        <div class="contact-buttons">
                            <a href="tel:+919448936339" class="btn-contact call">
                                <i class="fas fa-phone-alt"></i>
                                <span>CALL</span>
                            </a>
                            <a href="fax:+919448936339" class="btn-contact fax">
                                <i class="fas fa-fax"></i>
                                <span>FAX</span>
                            </a>
                            <a href="mailto:info@agricultureportal.com" class="btn-contact email">
                                <i class="fas fa-envelope"></i>
                                <span>EMAIL</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Social Links -->
                <div class="col-lg-3">
                    <div class="footer-social">
                        <h4>Connect With Us</h4>
                        <div class="social-icons">
                            <a href="#" class="social-icon" style="background: #EF4444;" title="Email Us">
                                <i class="fas fa-envelope"></i>
                            </a>
                            <a href="#" class="social-icon" style="background: #1DA1F2;" title="Follow on Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-icon" style="background: #1877F2;" title="Follow on Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-icon" style="background: #E4405F;" title="Follow on Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-icon" style="background: #333;" title="View on GitHub">
                                <i class="fab fa-github"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Copyright Bar -->
    <div class="copyright">
        <div class="container">
            <p>&copy; Copyright <?php echo date('Y'); ?> Farmers Gateway, All Rights Reserved</p>
        </div>
    </div>
</footer>

<?php include(__DIR__ . '/includes/chat-widget.php'); ?>

<style>
    .modern-footer {
        background: linear-gradient(180deg, #111827 0%, #1F2937 100%);
        color: #fff;
        padding-top: 4rem;
        position: relative;
        width: 100%;
        bottom: 0;
        left: 0;
        z-index: 1000;
    }

    .modern-footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, 
            rgba(45, 206, 137, 0) 0%,
            rgba(45, 206, 137, 0.5) 50%,
            rgba(45, 206, 137, 0) 100%);
    }

    .modern-footer .footer-content {
        position: relative;
        z-index: 1;
    }

    .modern-footer .footer-brand {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .modern-footer .brand-icon {
        background: rgba(45, 206, 137, 0.1);
        padding: 0.8rem;
        border-radius: 12px;
    }

    .modern-footer .footer-description {
        color: #9CA3AF;
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    .modern-footer .footer-info h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #fff;
    }

    .modern-footer .location {
        color: #9CA3AF;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }

    .modern-footer .contact-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .modern-footer .btn-contact {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .modern-footer .btn-contact.call {
        background: rgba(45, 206, 137, 0.1);
        color: #2dce89;
    }

    .modern-footer .btn-contact.fax {
        background: rgba(99, 102, 241, 0.1);
        color: #818CF8;
    }

    .modern-footer .btn-contact.email {
        background: rgba(239, 68, 68, 0.1);
        color: #EF4444;
    }

    .modern-footer .btn-contact:hover {
        transform: translateY(-2px);
        filter: brightness(1.1);
    }

    .modern-footer .footer-social h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #fff;
    }

    .modern-footer .social-icons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .modern-footer .social-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        color: #fff !important;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .modern-footer .social-icon:hover {
        transform: translateY(-3px);
        filter: brightness(1.1);
    }

    .modern-footer .copyright {
        text-align: center;
        padding: 2rem 0;
        margin-top: 4rem;
        background: rgba(17, 24, 39, 0.8);
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .modern-footer .copyright p {
        color: #9CA3AF;
        font-size: 0.9rem;
        margin: 0;
    }

    @media (max-width: 768px) {
        .modern-footer {
            padding-top: 3rem;
        }

        .modern-footer .footer-brand, 
        .modern-footer .footer-info, 
        .modern-footer .footer-social {
            text-align: center;
        }

        .modern-footer .footer-brand {
            justify-content: center;
        }

        .modern-footer .contact-buttons, 
        .modern-footer .social-icons {
            justify-content: center;
        }
    }

    /* Fix for sticky footer */
    html {
        height: 100%;
    }

    body {
        min-height: 100%;
        display: flex;
        flex-direction: column;
    }

    main {
        flex: 1 0 auto;
    }

    .modern-footer {
        flex-shrink: 0;
    }
</style>
