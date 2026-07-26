
</main>
<footer class="site-footer">
  <div class="footer-wrap">
    <p> Get <span> Air </span> &middot; Airline Management System</p>
    <p class="fine">Built with PHP &amp; MySQL — Cancel any booking free of charge within 24 hours of booking.</p>
    <?php if (!is_admin()): ?>
      <p class="fine"><a href="<?= isset($in_admin) && $in_admin ? 'login.php' : 'admin/login.php' ?>" style="color:rgba(255,255,255,.6);"> User & Admin Login</a></p>
    <?php endif; ?>
  </div>
</footer> 
</body> 
</html> 
