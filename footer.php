<?php
// footer.php - Premium Footer for FurShield Pet Adoption
?>
<footer class="footer">
  <div class="footer-container">
    <div class="footer-col">
      <div class="footer-logo">
        <span class="footer-logo-icon"></span>
        FurShield
      </div>
      <p class="footer-about">
        FurShield is a premium pet adoption platform dedicated to connecting loving homes with pets in need. 
        Our mission is to ensure every pet finds their forever family.
      </p>
      <div class="social-links">
        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
        <a href="#" class="social-link"><i class="fab fa-pinterest-p"></i></a>
      </div>
    </div>
    
    <div class="footer-col">
      <h3 class="footer-heading">Quick Links</h3>
      <ul class="footer-links">
        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="adopt.php"><i class="fas fa-paw"></i> Adopt a Pet</a></li>
        <li><a href="my_pets.php"><i class="fas fa-heart"></i> My Pets</a></li>
        <li><a href="book_appointment.php"><i class="fas fa-calendar"></i> Appointments</a></li>
        <li><a href="my_requests.php"><i class="fas fa-file-alt"></i> Adoption Requests</a></li>
      </ul>
    </div>
    
    <div class="footer-col">
      <h3 class="footer-heading">Pet Resources</h3>
      <ul class="footer-links">
        <li><a href="pet_care_tips.php"><i class="fas fa-book-open"></i> Pet Care Tips</a></li>
        <li><a href="training_guides.php"><i class="fas fa-graduation-cap"></i> Training Guides</a></li>
        <li><a href="health_resources.php"><i class="fas fa-heartbeat"></i> Health Resources</a></li>
        <li><a href="faq.php"><i class="fas fa-question-circle"></i> FAQ</a></li>
        <li><a href="blog.php"><i class="fas fa-blog"></i> Blog</a></li>
      </ul>
    </div>
    
    <div class="footer-col">
      <h3 class="footer-heading">Contact Us</h3>
      <ul class="footer-contact">
        <li>
          <i class="fas fa-map-marker-alt"></i>
          <span>123 Pet Avenue, Animal City, AC 12345</span>
        </li>
        <li>
          <i class="fas fa-phone"></i>
          <span>+1 (555) 123-PAWS</span>
        </li>
        <li>
          <i class="fas fa-envelope"></i>
          <span>support@furshield.com</span>
        </li>
        <li>
          <i class="fas fa-clock"></i>
          <span>Mon-Fri: 9am-6pm, Sat: 10am-4pm</span>
        </li>
      </ul>
    </div>
    
    <div class="footer-col">
      <h3 class="footer-heading">Newsletter</h3>
      <p>Subscribe to our newsletter for updates on new pets, events, and pet care tips.</p>
      <form class="newsletter-form">
        <input type="email" class="newsletter-input" placeholder="Your email address" required>
        <button type="submit" class="newsletter-btn"><i class="fas fa-paper-plane"></i></button>
      </form>
      <div class="payment-methods">
        <p>We accept:</p>
        <div class="payment-icons">
          <i class="fab fa-cc-visa"></i>
          <i class="fab fa-cc-mastercard"></i>
          <i class="fab fa-cc-amex"></i>
          <i class="fab fa-cc-paypal"></i>
        </div>
      </div>
    </div>
  </div>
  
  <div class="footer-bottom">
    <div class="copyright">
      &copy; <?php echo date('Y'); ?> FurShield Pet Adoption. All rights reserved.
    </div>
    <div class="footer-bottom-links">
      <a href="privacy_policy.php">Privacy Policy</a>
      <a href="terms_of_service.php">Terms of Service</a>
      <a href="sitemap.php">Sitemap</a>
    </div>
  </div>
  
  <a href="#" class="back-to-top" id="backToTop">
    <i class="fas fa-chevron-up"></i>
  </a>
</footer>

<style>
  /* Footer Styles */
  .footer {
    background: var(--royal-brown);
    color: var(--light);
    padding: 3rem 0 1.5rem;
    margin-top: auto;
  }
  
  .footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2.5rem;
  }
  
  .footer-logo {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
    font-weight: 700;
  }
  
  .footer-logo-icon {
    margin-right: 0.5rem;
    font-size: 2rem;
  }
  
  .footer-about {
    margin-bottom: 1.5rem;
    line-height: 1.7;
  }
  
  .footer-heading {
    font-size: 1.2rem;
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 0.7rem;
  }
  
  .footer-heading:after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 40px;
    height: 2px;
    background: var(--accent);
  }
  
  .footer-links {
    list-style: none;
  }
  
  .footer-links li {
    margin-bottom: 0.8rem;
  }
  
  .footer-links a {
    color: var(--light);
    text-decoration: none;
    transition: var(--transition);
    display: flex;
    align-items: center;
  }
  
  .footer-links a i {
    margin-right: 0.7rem;
    color: var(--accent);
    font-size: 0.9rem;
    width: 20px;
  }
  
  .footer-links a:hover {
    color: var(--accent);
    padding-left: 5px;
  }
  
  .footer-contact {
    list-style: none;
  }
  
  .footer-contact li {
    margin-bottom: 1.2rem;
    display: flex;
    align-items: flex-start;
  }
  
  .footer-contact i {
    margin-right: 1rem;
    color: var(--accent);
    font-size: 1.2rem;
    margin-top: 0.2rem;
  }
  
  .footer-newsletter p {
    margin-bottom: 1.5rem;
    line-height: 1.7;
  }
  
  .newsletter-form {
    display: flex;
    margin-bottom: 1.5rem;
  }
  
  .newsletter-input {
    flex: 1;
    padding: 0.8rem 1rem;
    border: none;
    border-radius: 4px 0 0 4px;
    font-family: inherit;
  }
  
  .newsletter-btn {
    background: var(--accent);
    color: var(--light);
    border: none;
    padding: 0 1.2rem;
    border-radius: 0 4px 4px 0;
    cursor: pointer;
    transition: var(--transition);
  }
  
  .newsletter-btn:hover {
    background: var(--accent-dark);
  }
  
  .social-links {
    display: flex;
    gap: 1rem;
  }
  
  .social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    color: var(--light);
    border-radius: 50%;
    text-decoration: none;
    transition: var(--transition);
  }
  
  .social-link:hover {
    background: var(--accent);
    transform: translateY(-3px);
  }
  
  .payment-methods {
    margin-top: 1.5rem;
  }
  
  .payment-methods p {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
  }
  
  .payment-icons {
    display: flex;
    gap: 0.8rem;
    font-size: 1.8rem;
  }
  
  .footer-bottom {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1.5rem 2rem 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
  }
  
  .copyright {
    font-size: 0.9rem;
  }
  
  .footer-bottom-links {
    display: flex;
    gap: 1.5rem;
  }
  
  .footer-bottom-links a {
    color: var(--light);
    text-decoration: none;
    font-size: 0.9rem;
    transition: var(--transition);
  }
  
  .footer-bottom-links a:hover {
    color: var(--accent);
  }
  
  /* Back to top button */
  .back-to-top {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 50px;
    height: 50px;
    background: var(--accent);
    color: var(--light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow: var(--shadow);
    transition: var(--transition);
    opacity: 0;
    visibility: hidden;
    z-index: 999;
  }
  
  .back-to-top.visible {
    opacity: 1;
    visibility: visible;
  }
  
  .back-to-top:hover {
    background: var(--accent-dark);
    transform: translateY(-5px);
  }
  
  /* Responsive design */
  @media (max-width: 992px) {
    .footer-container {
      grid-template-columns: repeat(2, 1fr);
    }
  }
  
  @media (max-width: 768px) {
    .footer-container {
      grid-template-columns: 1fr;
      gap: 2rem;
    }
    
    .footer-bottom {
      flex-direction: column;
      text-align: center;
    }
    
    .footer-bottom-links {
      justify-content: center;
    }
    
    .newsletter-form {
      flex-direction: column;
    }
    
    .newsletter-input {
      border-radius: 4px;
      margin-bottom: 0.5rem;
    }
    
    .newsletter-btn {
      border-radius: 4px;
      padding: 0.8rem;
    }
  }
</style>

<script>
  // Back to top functionality
  const backToTopButton = document.getElementById('backToTop');
  
  window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
      backToTopButton.classList.add('visible');
    } else {
      backToTopButton.classList.remove('visible');
    }
  });
  
  backToTopButton.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
  
  // Newsletter form submission
  const newsletterForm = document.querySelector('.newsletter-form');
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const emailInput = newsletterForm.querySelector('.newsletter-input');
      if (emailInput.value) {
        alert('Thank you for subscribing to our newsletter!');
        emailInput.value = '';
      }
    });
  }
</script>