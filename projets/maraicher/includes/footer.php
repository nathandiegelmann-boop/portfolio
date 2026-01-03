    </main>
    
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>🌱 <?php echo SITE_NAME; ?></h3>
                    <p>Fruits et légumes frais cultivés avec passion dans le respect de la nature.</p>
                    <p>📧 <?php echo SITE_EMAIL; ?></p>
                    <p>📞 01 23 45 67 89</p>
                </div>
                
                <div class="footer-section">
                    <h4>Navigation</h4>
                    <ul>
                        <li><a href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['REQUEST_URI'], '/client/') !== false) ? '../' : ''; ?>index.php">Accueil</a></li>
                        <li><a href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['REQUEST_URI'], '/client/') !== false) ? '../' : ''; ?>catalogue.php">Nos Produits</a></li>
                        <li><a href="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['REQUEST_URI'], '/client/') !== false) ? '../' : ''; ?>contact.php">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Informations</h4>
                    <ul>
                        <li><a href="#conditions">Conditions générales</a></li>
                        <li><a href="#livraison">Modalités de livraison</a></li>
                        <li><a href="#bio">Notre engagement bio</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Horaires</h4>
                    <p><strong>Marché :</strong><br>
                    Mardi et Vendredi : 8h-12h<br>
                    Samedi : 7h-13h</p>
                    <p><strong>Ferme :</strong><br>
                    Sur rendez-vous uniquement</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Tous droits réservés.</p>
                <p>Développé avec ❤️ pour soutenir l'agriculture locale</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
        // Toggle mobile menu
        function toggleMobileMenu() {
            const nav = document.querySelector('.main-nav');
            nav.classList.toggle('mobile-open');
        }
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            const nav = document.querySelector('.main-nav');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (!nav.contains(e.target) && !toggle.contains(e.target)) {
                nav.classList.remove('mobile-open');
            }
        });
        
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });
        
        // Confirm delete actions
        function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
            return confirm(message);
        }
        
        // Add to cart with quantity validation
        function addToCart(form) {
            const quantity = form.querySelector('input[name="quantite"]');
            const stock = parseInt(quantity.getAttribute('max'));
            const value = parseInt(quantity.value);
            
            if (value > stock) {
                alert('Quantité demandée supérieure au stock disponible (' + stock + ' kg)');
                return false;
            }
            
            if (value <= 0) {
                alert('Veuillez sélectionner une quantité valide');
                return false;
            }
            
            return true;
        }
        
        // Update cart totals dynamically
        function updateCartTotals() {
            const forms = document.querySelectorAll('.cart-item form');
            let total = 0;
            
            forms.forEach(form => {
                const quantity = parseFloat(form.querySelector('input[name="quantite"]').value);
                const price = parseFloat(form.querySelector('input[name="prix"]').value);
                const subtotal = quantity * price;
                
                const subtotalElement = form.closest('.cart-item').querySelector('.item-subtotal');
                if (subtotalElement) {
                    subtotalElement.textContent = subtotal.toFixed(2).replace('.', ',') + ' €';
                }
                
                total += subtotal;
            });
            
            const totalElement = document.querySelector('.cart-total');
            if (totalElement) {
                totalElement.textContent = total.toFixed(2).replace('.', ',') + ' €';
            }
        }
    </script>
    
    <?php if (isset($extra_js)): ?>
        <script src="<?php echo $extra_js; ?>"></script>
    <?php endif; ?>
</body>
</html>