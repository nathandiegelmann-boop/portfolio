// Auto-refresh Script - Portfolio Nathan Diegelmann
// Permet de rafraîchir automatiquement la page toutes les X secondes

(function() {
    'use strict';
    
    // Configuration
    const AUTO_REFRESH_INTERVAL = 30; // secondes
    const SHOW_COUNTDOWN = true; // Afficher le compte à rebours
    
    let countdownInterval;
    let secondsRemaining = AUTO_REFRESH_INTERVAL;
    
    // Créer l'indicateur de compte à rebours
    function createCountdownIndicator() {
        if (!SHOW_COUNTDOWN) return;
        
        const indicator = document.createElement('div');
        indicator.id = 'auto-refresh-indicator';
        indicator.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(30, 41, 59, 0.95);
            color: #E2E8F0;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 9999;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(51, 65, 85, 0.8);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        `;
        
        indicator.innerHTML = `
            <i class="fas fa-sync-alt" style="color: #3B82F6; font-size: 14px;"></i>
            <span>Actualisation dans <strong id="countdown-seconds">${AUTO_REFRESH_INTERVAL}</strong>s</span>
            <button id="pause-refresh" style="
                background: transparent;
                border: 1px solid #475569;
                color: #94A3B8;
                padding: 4px 10px;
                border-radius: 6px;
                cursor: pointer;
                font-size: 11px;
                transition: all 0.2s;
            ">
                <i class="fas fa-pause"></i>
            </button>
        `;
        
        document.body.appendChild(indicator);
        
        // Bouton pause
        const pauseBtn = document.getElementById('pause-refresh');
        let isPaused = false;
        
        pauseBtn.addEventListener('click', function() {
            isPaused = !isPaused;
            if (isPaused) {
                clearInterval(countdownInterval);
                this.innerHTML = '<i class="fas fa-play"></i>';
                this.style.borderColor = '#10B981';
                this.style.color = '#10B981';
            } else {
                secondsRemaining = AUTO_REFRESH_INTERVAL;
                startCountdown();
                this.innerHTML = '<i class="fas fa-pause"></i>';
                this.style.borderColor = '#475569';
                this.style.color = '#94A3B8';
            }
        });
        
        // Cacher au survol
        indicator.addEventListener('mouseenter', function() {
            this.style.opacity = '0.3';
        });
        
        indicator.addEventListener('mouseleave', function() {
            this.style.opacity = '1';
        });
    }
    
    // Démarrer le compte à rebours
    function startCountdown() {
        countdownInterval = setInterval(function() {
            secondsRemaining--;
            
            const countdownElement = document.getElementById('countdown-seconds');
            if (countdownElement) {
                countdownElement.textContent = secondsRemaining;
                
                // Changer la couleur quand il reste peu de temps
                if (secondsRemaining <= 5) {
                    countdownElement.style.color = '#F59E0B';
                } else {
                    countdownElement.style.color = '#3B82F6';
                }
            }
            
            if (secondsRemaining <= 0) {
                clearInterval(countdownInterval);
                location.reload();
            }
        }, 1000);
    }
    
    // Initialiser au chargement de la page
    window.addEventListener('load', function() {
        createCountdownIndicator();
        startCountdown();
    });
    
})();
