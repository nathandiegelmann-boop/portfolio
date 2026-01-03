// Menu hamburger mobile - Script global
document.addEventListener('DOMContentLoaded', function() {
    // Gérer le menu flottant (nouveau système)
    const floatingNavBtn = document.getElementById('floatingNavBtn');
    const floatingNavMenu = document.getElementById('floatingNavMenu');
    
    if (floatingNavBtn && floatingNavMenu) {
        // Le menu flottant existe, il est déjà géré dans index.php
        console.log('Menu flottant détecté et opérationnel');
        return;
    }
    
    // Ancien système pour compatibilité avec autres pages
    const navContainer = document.querySelector('.nav-container');
    const navMenu = document.querySelector('.nav-menu');
    let mobileBtn = document.getElementById('mobileMenuBtn');
    
    // Si le bouton n'existe pas, le créer
    if (!mobileBtn && navContainer && navMenu) {
        mobileBtn = document.createElement('button');
        mobileBtn.id = 'mobileMenuBtn';
        mobileBtn.className = 'mobile-menu-btn';
        mobileBtn.innerHTML = '<span></span><span></span><span></span>';
        
        const logo = navContainer.querySelector('.nav-logo');
        if (logo) {
            logo.after(mobileBtn);
        }
    }
    
    // Attacher les événements (que le bouton existe déjà ou non)
    if (mobileBtn && navMenu) {
        navMenu.id = 'navMenu';
        
        // Gérer le clic sur le bouton
        mobileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            console.log('Menu clicked!'); // Debug
            this.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
        
        // Fermer le menu quand on clique sur un lien
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                mobileBtn.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
        
        // Fermer le menu quand on clique ailleurs
        document.addEventListener('click', function(e) {
            if (!navContainer.contains(e.target)) {
                mobileBtn.classList.remove('active');
                navMenu.classList.remove('active');
            }
        });
    }
});
