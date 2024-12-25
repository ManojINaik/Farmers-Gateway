 
            
            <!-- Quick Links -->
            <div class="col-lg-5 col-md-12 mb-3 mb-lg-0">
                <div class="d-flex justify-content-center justify-content-lg-start gap-4">
                    <a href="/about" class="footer-link">About</a>
                    <a href="/services" class="footer-link">Services</a>
                    <a href="/market" class="footer-link">Market</a>
                    <a href="/contact" class="footer-link">Contact</a>
                    <a href="/privacy" class="footer-link">Privacy</a>
                </div>
            </div>
            
            <!-- Contact -->
            <div class="col-lg-3 col-md-12 text-center text-lg-end">
                <a href="tel:+919448936339" class="footer-contact"><i class="fas fa-phone-alt me-2"></i>+91 94489 36339</a>
            </div>
        </div>
    </div>
    
    <!-- Copyright -->
    
</footer>

<style>
.footer {
    background: linear-gradient(135deg, #1a472a 0%, #2c5282 100%);
    color: #fff;
    font-size: 14px;
}

.social-links {
    display: flex;
    gap: 12px;
    margin-top: 10px;
    justify-content: center;
    justify-content: lg-start;
}

.social-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.social-icon:hover {
    background: #fff;
    color: #2c5282;
    transform: translateY(-2px);
}

.footer-link {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: color 0.2s ease;
    font-weight: 500;
}

.footer-link:hover {
    color: #fff;
}

.footer-contact {
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    transition: opacity 0.2s ease;
}

.footer-contact:hover {
    color: #fff;
    opacity: 0.8;
}

.copyright {
    background: rgba(0, 0, 0, 0.2);
    padding: 1rem 0;
    font-size: 13px;
    color: rgba(255, 255, 255, 0.8);
}
</style>

<!-- Scripts -->
<script src="https://cdn.staticfile.org/jquery/3.6.3/jquery.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
<script src="https://cdn.staticfile.org/markdown-it/13.0.1/markdown-it.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://kit.fontawesome.com/your-kit-code.js"></script>

<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize DataTables if needed
    if ($('#myTable').length) {
        $('#myTable').DataTable();
    }
});
</script>

<?php include 'modern-footer.php'; ?>