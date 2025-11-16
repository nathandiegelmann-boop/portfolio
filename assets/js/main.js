// Nathan Diegelmann Portfolio - JavaScript Principal

// Variables globales
let soundEnabled = true;
let particleSystem = null;

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    initNavigation();
    initSoundEffects();
    initScrollEffects();
    initParticles();
    initMobileMenu();
    initThemeToggle();
    console.log('🚀 Portfolio Nathan initialisé!');
});

// Navigation
function initNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage || (currentPage === '' && href === 'index.php')) {
            link.classList.add('active');
        }
        
        link.addEventListener('click', function(e) {
            if (soundEnabled) playSound('click');
        });
    });
}

// Effets sonores
function initSoundEffects() {
    const soundToggle = document.getElementById('soundToggle');
    if (soundToggle) {
        soundToggle.addEventListener('click', toggleSound);
    }
    
    // Ajouter sons aux boutons
    const buttons = document.querySelectorAll('.cyber-btn, .social-link');
    buttons.forEach(btn => {
        btn.addEventListener('mouseenter', () => {
            if (soundEnabled) playSound('hover');
        });
        
        btn.addEventListener('click', () => {
            if (soundEnabled) playSound('click');
        });
    });
}

function toggleSound() {
    soundEnabled = !soundEnabled;
    const icon = document.querySelector('#soundToggle i');
    if (icon) {
        icon.className = soundEnabled ? 'fas fa-volume-up' : 'fas fa-volume-mute';
    }
    
    // Sauvegarde préférence
    localStorage.setItem('soundEnabled', soundEnabled.toString());
}

function playSound(type) {
    if (!soundEnabled) return;
    
    // Utiliser Web Audio API pour des sons synthétiques cyberpunk
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    // Configuration selon le type
    switch(type) {
        case 'hover':
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
            break;
        case 'click':
            oscillator.frequency.setValueAtTime(1200, audioContext.currentTime);
            oscillator.frequency.exponentialRampToValueAtTime(600, audioContext.currentTime + 0.1);
            gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
            break;
    }
    
    oscillator.start();
    oscillator.stop(audioContext.currentTime + 0.1);
}

// Effets de scroll
function initScrollEffects() {
    const nav = document.querySelector('.cyber-nav');
    let lastScroll = 0;
    
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        // Navigation auto-hide
        if (currentScroll > lastScroll && currentScroll > 100) {
            nav.style.transform = 'translateY(-100%)';
        } else {
            nav.style.transform = 'translateY(0)';
        }
        
        // Effet glassmorphism selon scroll
        if (currentScroll > 50) {
            nav.style.background = 'rgba(5, 5, 8, 0.98)';
            nav.style.backdropFilter = 'blur(15px)';
        } else {
            nav.style.background = 'rgba(10, 10, 15, 0.95)';
            nav.style.backdropFilter = 'blur(10px)';
        }
        
        lastScroll = currentScroll;
    });
    
    // Parallax pour le terminal
    const terminal = document.querySelector('.terminal-box');
    if (terminal) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            terminal.style.transform = `translateY(${rate}px)`;
        });
    }
}

// Système de particules
function initParticles() {
    const canvas = document.getElementById('particlesCanvas');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    
    const particles = [];
    const particleCount = 100;
    
    class Particle {
        constructor() {
            this.x = Math.random() * canvas.width;
            this.y = Math.random() * canvas.height;
            this.vx = (Math.random() - 0.5) * 0.5;
            this.vy = (Math.random() - 0.5) * 0.5;
            this.size = Math.random() * 2 + 1;
            this.opacity = Math.random() * 0.5 + 0.2;
            this.color = Math.random() > 0.5 ? '#00ffff' : '#0080ff';
        }
        
        update() {
            this.x += this.vx;
            this.y += this.vy;
            
            // Rebond sur les bords
            if (this.x < 0 || this.x > canvas.width) this.vx *= -1;
            if (this.y < 0 || this.y > canvas.height) this.vy *= -1;
            
            // Oscillation d'opacité
            this.opacity += (Math.random() - 0.5) * 0.02;
            this.opacity = Math.max(0.1, Math.min(0.8, this.opacity));
        }
        
        draw() {
            ctx.save();
            ctx.globalAlpha = this.opacity;
            ctx.fillStyle = this.color;
            ctx.shadowBlur = 10;
            ctx.shadowColor = this.color;
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();
        }
    }
    
    // Créer particules
    for (let i = 0; i < particleCount; i++) {
        particles.push(new Particle());
    }
    
    // Animation
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        particles.forEach(particle => {
            particle.update();
            particle.draw();
        });
        
        // Connexions entre particules proches
        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const dx = particles[i].x - particles[j].x;
                const dy = particles[i].y - particles[j].y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance < 100) {
                    ctx.save();
                    ctx.globalAlpha = (100 - distance) / 100 * 0.2;
                    ctx.strokeStyle = '#00ffff';
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.stroke();
                    ctx.restore();
                }
            }
        }
        
        requestAnimationFrame(animate);
    }
    
    animate();
    
    // Redimensionnement
    window.addEventListener('resize', () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    });
}

// Menu mobile
function initMobileMenu() {
    const mobileToggle = document.getElementById('mobileToggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (mobileToggle && navMenu) {
        mobileToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            mobileToggle.classList.toggle('active');
            if (soundEnabled) playSound('click');
        });
    }
}

// Toggle thème
function initThemeToggle() {
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('light-theme');
            if (soundEnabled) playSound('click');
            
            // Sauvegarde préférence
            const isLight = document.body.classList.contains('light-theme');
            localStorage.setItem('lightTheme', isLight.toString());
        });
    }
    
    // Restaurer thème sauvegardé
    const savedTheme = localStorage.getItem('lightTheme');
    if (savedTheme === 'true') {
        document.body.classList.add('light-theme');
    }
}

// Animations au scroll
function animateOnScroll() {
    const elements = document.querySelectorAll('.animate-on-scroll');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    elements.forEach(el => observer.observe(el));
}

// Effet de saisie pour les textes
function typeWriter(element, text, speed = 50) {
    let i = 0;
    element.textContent = '';
    
    function type() {
        if (i < text.length) {
            element.textContent += text.charAt(i);
            i++;
            setTimeout(type, speed);
        }
    }
    
    type();
}

// Compteur animé
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16); // 60 FPS
    
    function updateCounter() {
        start += increment;
        if (start < target) {
            element.textContent = Math.ceil(start);
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target;
        }
    }
    
    updateCounter();
}

// Glitch effect
function glitchEffect(element, duration = 3000) {
    const originalText = element.textContent;
    const chars = '!<>-_\\/[]{}—=+*^?#________';
    
    function glitch() {
        let iterations = 0;
        const maxIterations = originalText.length;
        
        const interval = setInterval(() => {
            element.textContent = originalText
                .split('')
                .map((char, index) => {
                    if (index < iterations) {
                        return originalText[index];
                    }
                    return chars[Math.floor(Math.random() * chars.length)];
                })
                .join('');
            
            if (iterations >= maxIterations) {
                clearInterval(interval);
                element.textContent = originalText;
            }
            
            iterations += 1/3;
        }, 30);
    }
    
    glitch();
    setInterval(glitch, duration);
}

// Initialiser glitch sur les titres
document.addEventListener('DOMContentLoaded', () => {
    const glitchElements = document.querySelectorAll('.glitch-text');
    glitchElements.forEach(el => {
        glitchEffect(el, 5000);
    });
});

// Gestion des erreurs
window.addEventListener('error', (e) => {
    console.error('💥 Erreur détectée:', e.error);
});

// Easter egg - Konami Code
let konamiCode = [];
const konamiSequence = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65];

document.addEventListener('keydown', (e) => {
    konamiCode.push(e.keyCode);
    if (konamiCode.length > konamiSequence.length) {
        konamiCode.shift();
    }
    
    if (konamiCode.toString() === konamiSequence.toString()) {
        activateEasterEgg();
    }
});

function activateEasterEgg() {
    document.body.style.filter = 'hue-rotate(180deg)';
    alert('🎉 Mode Matrix activé! Nathan approuve ce hack!');
    
    setTimeout(() => {
        document.body.style.filter = 'none';
    }, 5000);
}

// Utilitaires
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Export pour utilisation globale
window.portfolioUtils = {
    typeWriter,
    animateCounter,
    glitchEffect,
    playSound,
    debounce,
    throttle
};