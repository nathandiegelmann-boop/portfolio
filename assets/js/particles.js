// Nathan Portfolio - Système de particules avancé

class ParticleSystem {
    constructor(canvasId, options = {}) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) return;
        
        this.ctx = this.canvas.getContext('2d');
        this.particles = [];
        this.mouse = { x: 0, y: 0 };
        this.config = {
            particleCount: options.count || 150,
            maxDistance: options.maxDistance || 120,
            mouseRadius: options.mouseRadius || 150,
            colors: options.colors || ['#00ffff', '#0080ff', '#ff0080', '#8000ff'],
            minSize: options.minSize || 1,
            maxSize: options.maxSize || 3,
            minSpeed: options.minSpeed || 0.2,
            maxSpeed: options.maxSpeed || 1,
            interactive: options.interactive !== false
        };
        
        this.init();
    }
    
    init() {
        this.resize();
        this.createParticles();
        this.bindEvents();
        this.animate();
    }
    
    resize() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
    }
    
    createParticles() {
        this.particles = [];
        
        for (let i = 0; i < this.config.particleCount; i++) {
            this.particles.push({
                x: Math.random() * this.canvas.width,
                y: Math.random() * this.canvas.height,
                vx: (Math.random() - 0.5) * (this.config.maxSpeed - this.config.minSpeed) + this.config.minSpeed,
                vy: (Math.random() - 0.5) * (this.config.maxSpeed - this.config.minSpeed) + this.config.minSpeed,
                size: Math.random() * (this.config.maxSize - this.config.minSize) + this.config.minSize,
                color: this.config.colors[Math.floor(Math.random() * this.config.colors.length)],
                opacity: Math.random() * 0.5 + 0.3,
                originalX: 0,
                originalY: 0,
                magnetism: Math.random() * 2 + 1
            });
        }
        
        // Stocker positions originales
        this.particles.forEach(particle => {
            particle.originalX = particle.x;
            particle.originalY = particle.y;
        });
    }
    
    bindEvents() {
        if (this.config.interactive) {
            this.canvas.addEventListener('mousemove', (e) => {
                const rect = this.canvas.getBoundingClientRect();
                this.mouse.x = e.clientX - rect.left;
                this.mouse.y = e.clientY - rect.top;
            });
            
            this.canvas.addEventListener('mouseleave', () => {
                this.mouse.x = -1000;
                this.mouse.y = -1000;
            });
        }
        
        window.addEventListener('resize', () => {
            this.resize();
            this.createParticles();
        });
    }
    
    updateParticle(particle) {
        // Mouvement de base
        particle.x += particle.vx;
        particle.y += particle.vy;
        
        // Rebond sur les bords
        if (particle.x < 0 || particle.x > this.canvas.width) {
            particle.vx *= -1;
        }
        if (particle.y < 0 || particle.y > this.canvas.height) {
            particle.vy *= -1;
        }
        
        // Interaction avec la souris
        if (this.config.interactive) {
            const dx = this.mouse.x - particle.x;
            const dy = this.mouse.y - particle.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            if (distance < this.config.mouseRadius) {
                const force = (this.config.mouseRadius - distance) / this.config.mouseRadius;
                const angle = Math.atan2(dy, dx);
                
                particle.vx += Math.cos(angle) * force * 0.2 * particle.magnetism;
                particle.vy += Math.sin(angle) * force * 0.2 * particle.magnetism;
            }
        }
        
        // Oscillation d'opacité
        particle.opacity += (Math.random() - 0.5) * 0.01;
        particle.opacity = Math.max(0.1, Math.min(0.8, particle.opacity));
        
        // Légère variation de taille
        particle.size += (Math.random() - 0.5) * 0.02;
        particle.size = Math.max(0.5, Math.min(4, particle.size));
    }
    
    drawParticle(particle) {
        this.ctx.save();
        
        // Glow effect
        this.ctx.shadowBlur = 15;
        this.ctx.shadowColor = particle.color;
        
        // Particule principale
        this.ctx.globalAlpha = particle.opacity;
        this.ctx.fillStyle = particle.color;
        this.ctx.beginPath();
        this.ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
        this.ctx.fill();
        
        // Halo externe
        this.ctx.globalAlpha = particle.opacity * 0.3;
        this.ctx.beginPath();
        this.ctx.arc(particle.x, particle.y, particle.size * 2, 0, Math.PI * 2);
        this.ctx.fill();
        
        this.ctx.restore();
    }
    
    drawConnections() {
        for (let i = 0; i < this.particles.length; i++) {
            for (let j = i + 1; j < this.particles.length; j++) {
                const dx = this.particles[i].x - this.particles[j].x;
                const dy = this.particles[i].y - this.particles[j].y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance < this.config.maxDistance) {
                    const opacity = (this.config.maxDistance - distance) / this.config.maxDistance * 0.3;
                    
                    this.ctx.save();
                    this.ctx.globalAlpha = opacity;
                    this.ctx.strokeStyle = this.particles[i].color;
                    this.ctx.lineWidth = 1;
                    this.ctx.beginPath();
                    this.ctx.moveTo(this.particles[i].x, this.particles[i].y);
                    this.ctx.lineTo(this.particles[j].x, this.particles[j].y);
                    this.ctx.stroke();
                    this.ctx.restore();
                }
            }
        }
    }
    
    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        // Mettre à jour et dessiner les particules
        this.particles.forEach(particle => {
            this.updateParticle(particle);
            this.drawParticle(particle);
        });
        
        // Dessiner les connexions
        this.drawConnections();
        
        requestAnimationFrame(() => this.animate());
    }
    
    // Méthodes publiques
    addParticle(x, y) {
        this.particles.push({
            x: x || Math.random() * this.canvas.width,
            y: y || Math.random() * this.canvas.height,
            vx: (Math.random() - 0.5) * 2,
            vy: (Math.random() - 0.5) * 2,
            size: Math.random() * 3 + 1,
            color: this.config.colors[Math.floor(Math.random() * this.config.colors.length)],
            opacity: Math.random() * 0.5 + 0.3,
            magnetism: Math.random() * 2 + 1
        });
    }
    
    removeParticles(count = 1) {
        this.particles.splice(0, count);
    }
    
    setParticleCount(count) {
        this.config.particleCount = count;
        this.createParticles();
    }
    
    destroy() {
        this.particles = [];
        if (this.canvas) {
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        }
    }
}

// Système de particules en constellation (pour sections spéciales)
class ConstellationEffect {
    constructor(canvasId, constellation) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) return;
        
        this.ctx = this.canvas.getContext('2d');
        this.stars = constellation || this.generateConstellation();
        this.animationProgress = 0;
        
        this.init();
    }
    
    generateConstellation() {
        // Constellation de Nathan (forme personnalisée)
        const points = [
            { x: 0.2, y: 0.3 }, // N
            { x: 0.3, y: 0.2 },
            { x: 0.4, y: 0.4 },
            { x: 0.5, y: 0.3 }, // A
            { x: 0.6, y: 0.2 },
            { x: 0.7, y: 0.4 },
            { x: 0.8, y: 0.3 }  // T
        ];
        
        return points.map((point, index) => ({
            x: point.x,
            y: point.y,
            index,
            connected: index < points.length - 1,
            brightness: Math.random() * 0.5 + 0.5
        }));
    }
    
    init() {
        this.resize();
        this.animate();
    }
    
    resize() {
        this.canvas.width = this.canvas.offsetWidth;
        this.canvas.height = this.canvas.offsetHeight;
    }
    
    draw() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        const centerX = this.canvas.width / 2;
        const centerY = this.canvas.height / 2;
        const scale = Math.min(this.canvas.width, this.canvas.height) * 0.4;
        
        // Dessiner les étoiles
        this.stars.forEach((star, index) => {
            const x = centerX + (star.x - 0.5) * scale;
            const y = centerY + (star.y - 0.5) * scale;
            
            // Animation d'apparition progressive
            const appearProgress = Math.max(0, Math.min(1, (this.animationProgress - index * 0.1) / 0.3));
            
            if (appearProgress > 0) {
                this.ctx.save();
                this.ctx.globalAlpha = star.brightness * appearProgress;
                
                // Étoile avec glow
                this.ctx.shadowBlur = 20;
                this.ctx.shadowColor = '#00ffff';
                this.ctx.fillStyle = '#00ffff';
                
                this.ctx.beginPath();
                this.ctx.arc(x, y, 3 * appearProgress, 0, Math.PI * 2);
                this.ctx.fill();
                
                // Connexions
                if (star.connected && index < this.stars.length - 1) {
                    const nextStar = this.stars[index + 1];
                    const nextX = centerX + (nextStar.x - 0.5) * scale;
                    const nextY = centerY + (nextStar.y - 0.5) * scale;
                    
                    const lineProgress = Math.max(0, Math.min(1, (this.animationProgress - (index + 0.5) * 0.1) / 0.2));
                    
                    if (lineProgress > 0) {
                        this.ctx.globalAlpha = 0.6 * lineProgress;
                        this.ctx.strokeStyle = '#00ffff';
                        this.ctx.lineWidth = 2;
                        
                        this.ctx.beginPath();
                        this.ctx.moveTo(x, y);
                        this.ctx.lineTo(
                            x + (nextX - x) * lineProgress,
                            y + (nextY - y) * lineProgress
                        );
                        this.ctx.stroke();
                    }
                }
                
                this.ctx.restore();
            }
        });
    }
    
    animate() {
        this.animationProgress += 0.01;
        if (this.animationProgress > 2) {
            this.animationProgress = 0; // Restart animation
        }
        
        this.draw();
        requestAnimationFrame(() => this.animate());
    }
}

// Initialisation automatique si canvas présent
document.addEventListener('DOMContentLoaded', () => {
    // Particules principales
    if (document.getElementById('particlesCanvas')) {
        window.particleSystem = new ParticleSystem('particlesCanvas', {
            count: 120,
            maxDistance: 100,
            colors: ['#00ffff', '#0080ff', '#ff0080'],
            interactive: true
        });
    }
    
    // Constellation Nathan
    if (document.getElementById('constellationCanvas')) {
        window.constellation = new ConstellationEffect('constellationCanvas');
    }
});

// Export pour utilisation globale
window.ParticleSystem = ParticleSystem;
window.ConstellationEffect = ConstellationEffect;