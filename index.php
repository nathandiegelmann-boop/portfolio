<?php
// Nathan Diegelmann - Portfolio Cyberpunk Index
require_once __DIR__ . '/includes/config.php';
?>
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <meta name="description" content="Portfolio futuriste de Nathan Diegelmann, développeur web HTML/CSS/PHP/SQL et powerlifter. Étudiant en Bachelor Développement Web.">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@300;400;500;600;700&family=JetBrains+Mono:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* CSS Critique pour loading rapide */
        :root {
            --terminal-green: #00ff41;
            --code-blue: #61dafb;
            --warning-orange: #ffa500;
            --error-red: #ff6b6b;
            --success-green: #51cf66;
            --dark-bg: #0d1117;
            --darker-bg: #010409;
            --code-bg: #161b22;
            --font-mono: 'Fira Code', 'JetBrains Mono', monospace;
        }
        
        .inline-code {
            background: var(--code-bg);
            color: var(--code-blue);
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            font-family: var(--font-mono);
            font-size: 0.9em;
        }
        
        .syntax-highlight .keyword { color: var(--error-red); font-weight: 600; }
        .syntax-highlight .function { color: var(--code-blue); }
        
        .hero-subtitle {
            font-family: var(--font-mono) !important;
            font-size: 1.2rem !important;
        }
        
        body {
            margin: 0;
            font-family: var(--font-mono);
            background: var(--dark-bg);
            color: white;
            overflow-x: hidden;
            background-image: 
                repeating-linear-gradient(
                    0deg,
                    transparent,
                    transparent 2px,
                    rgba(0, 255, 65, 0.03) 2px,
                    rgba(0, 255, 65, 0.03) 4px
                );
        }
        
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--darker-bg);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 10000;
        }
        
        .cyber-loader {
            width: 80px;
            height: 80px;
            border: 3px solid rgba(0, 255, 65, 0.1);
            border-top: 3px solid var(--terminal-green);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            box-shadow: 0 0 20px var(--terminal-green);
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-text {
            margin-top: 20px;
            font-family: var(--font-mono);
            color: var(--terminal-green);
            text-shadow: 0 0 10px var(--terminal-green);
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div id="loadingScreen" class="loading-screen">
        <div class="cyber-loader"></div>
        <div class="loading-text">INITIALISATION SYSTÈME...</div>
    </div>

    <!-- Navigation -->
    <nav class="cyber-nav">
        <div class="nav-container">
            <div class="nav-logo">
                <span>&lt;NATHAN/&gt;</span>
            </div>
            
            <div class="nav-menu">
                <a href="index.php" class="nav-link active">ACCUEIL</a>
                <a href="projets.php" class="nav-link">PROJETS</a>
                <a href="competences.php" class="nav-link">COMPÉTENCES</a>
                <a href="a-propos.php" class="nav-link">À PROPOS</a>
                <a href="contact.php" class="nav-link">CONTACT</a>
            </div>
            

        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg">
            <canvas id="particlesCanvas"></canvas>
            <div class="cyber-grid"></div>
        </div>
        
        <div class="hero-content">
                <div class="hero-badge">
                    <span class="status-dot"></span>
                    <span class="inline-code">status: "available_for_hire"</span>
                </div>            <h1 class="hero-title">
                <span class="glitch" data-text="<?php echo $nathan_info['nom']; ?>">
                    <?php echo $nathan_info['nom']; ?>
                </span>
            </h1>
            
                <h2 class="hero-subtitle">
                    <span class="syntax-highlight">
                        <span class="keyword">class</span> <span class="function">Developer</span> <span class="keyword">extends</span> <span class="function">PowerLifter</span>
                    </span>
                </h2>            <div class="hero-description">
                <p class="typed-text"></p>
            </div>
            
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number"><?php echo $nathan_info['age']; ?></span>
                    <span class="stat-label">ANS</span>
                </div>
                <div class="stat">
                    <span class="stat-number">5</span>
                    <span class="stat-label">PROJETS</span>
                </div>
                <div class="stat">
                    <span class="stat-number">3</span>
                    <span class="stat-label">LANGAGES</span>
                </div>
            </div>
            
            <div class="hero-buttons">
                <a href="projets.php" class="cyber-btn primary">
                    <span>VOIR PROJETS</span>
                    <i class="fas fa-rocket"></i>
                </a>
                <a href="contact.php" class="cyber-btn secondary">
                    <span>CONTACT</span>
                    <i class="fas fa-envelope"></i>
                </a>
            </div>
            
            <div class="social-links">
                <a href="mailto:<?php echo $nathan_info['email']; ?>" class="social-link">
                    <i class="fas fa-envelope"></i>
                </a>
                <a href="https://instagram.com/<?php echo ltrim($nathan_info['instagram'], '@'); ?>" class="social-link">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="social-link">
                    <i class="fab fa-github"></i>
                </a>
            </div>
        </div>
        
        <!-- Terminal Effect -->
        <div class="terminal-box">
            <div class="terminal-header">
                <div class="terminal-buttons">
                    <span class="btn-close"></span>
                    <span class="btn-minimize"></span>
                    <span class="btn-maximize"></span>
                </div>
                <span class="terminal-title">nathan@portfolio:~$</span>
            </div>
            <div class="terminal-body">
                <div id="terminalOutput"></div>
            </div>
        </div>
    </section>

    <!-- Compétences rapides -->
    <section class="skills-preview">
        <div class="container">
            <h2 class="section-title">COMPÉTENCES PRINCIPALES</h2>
            
            <div class="skills-grid">
                <div class="skill-card">
                    <div class="skill-icon">
                        <i class="fab fa-html5"></i>
                    </div>
                    <h3>HTML</h3>
                    <div class="skill-bar">
                        <div class="skill-progress" data-level="<?php echo getSkillLevel('HTML'); ?>"></div>
                    </div>
                    <span class="skill-level"><?php echo getSkillLevel('HTML'); ?>%</span>
                </div>
                
                <div class="skill-card">
                    <div class="skill-icon">
                        <i class="fab fa-css3-alt"></i>
                    </div>
                    <h3>CSS</h3>
                    <div class="skill-bar">
                        <div class="skill-progress" data-level="<?php echo getSkillLevel('CSS'); ?>"></div>
                    </div>
                    <span class="skill-level"><?php echo getSkillLevel('CSS'); ?>%</span>
                </div>
                
                <div class="skill-card">
                    <div class="skill-icon">
                        <i class="fab fa-php"></i>
                    </div>
                    <h3>PHP</h3>
                    <div class="skill-bar">
                        <div class="skill-progress" data-level="<?php echo getSkillLevel('PHP'); ?>"></div>
                    </div>
                    <span class="skill-level"><?php echo getSkillLevel('PHP'); ?>%</span>
                </div>
                
                <div class="skill-card">
                    <div class="skill-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <h3>SQL</h3>
                    <div class="skill-bar">
                        <div class="skill-progress" data-level="<?php echo getSkillLevel('SQL'); ?>"></div>
                    </div>
                    <span class="skill-level"><?php echo getSkillLevel('SQL'); ?>%</span>
                </div>                <div class="skill-card">
                    <div class="skill-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Recherche</h3>
                    <div class="skill-bar">
                        <div class="skill-progress" data-level="<?php echo getSkillLevel('Recherche projets'); ?>"></div>
                    </div>
                    <span class="skill-level"><?php echo getSkillLevel('Recherche projets'); ?>%</span>
                </div>
            </div>
            
            <div class="skills-action">
                <a href="competences.php" class="cyber-btn outline">
                    TOUTES LES COMPÉTENCES
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script src="assets/js/particles.js"></script>
    <script src="assets/js/main.js"></script>
    
    <script>
        // TypeWriter Effect pour la description
        const typedTextElement = document.querySelector('.typed-text');
        const textToType = "Étudiant de <?php echo $nathan_info['age']; ?> ans passionné par la programmation et le powerlifting. Je combine force physique et logique informatique pour créer des solutions innovantes.";
        let i = 0;

        function typeWriter() {
            if (i < textToType.length) {
                typedTextElement.textContent += textToType.charAt(i);
                i++;
                setTimeout(typeWriter, 50);
            }
        }

        // Terminal Effect
        const terminalOutput = document.getElementById('terminalOutput');
        const commands = [
            'git clone https://github.com/nathan-diegelmann/portfolio.git',
            'cd portfolio && npm install',
            'Scanning dependencies... ✓',
            'Loading developer profile...',
            '├── HTML: <?php echo getSkillLevel("HTML"); ?>% ████████░░',
            '├── CSS: <?php echo getSkillLevel("CSS"); ?>% ████████░░',
            '├── PHP: <?php echo getSkillLevel("PHP"); ?>% ███████░░░',
            '├── SQL: <?php echo getSkillLevel("SQL"); ?>% ███████░░░',
            '└── Ready to collaborate! 🚀',
            'nathan@portfolio:~$ welcome --interactive'
        ];
        
        let commandIndex = 0;
        
        function showNextCommand() {
            if (commandIndex < commands.length) {
                const line = document.createElement('div');
                line.className = 'terminal-line';
                line.innerHTML = `<span class="prompt">nathan@portfolio:~$</span> ${commands[commandIndex]}`;
                terminalOutput.appendChild(line);
                terminalOutput.scrollTop = terminalOutput.scrollHeight;
                commandIndex++;
                setTimeout(showNextCommand, 1000);
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            // Simuler loading
            setTimeout(() => {
                document.getElementById('loadingScreen').style.display = 'none';
                typeWriter();
                showNextCommand();
                
                // Animer les barres de compétences
                const skillBars = document.querySelectorAll('.skill-progress');
                skillBars.forEach(bar => {
                    const level = bar.dataset.level;
                    setTimeout(() => {
                        bar.style.width = level + '%';
                    }, 500);
                });
            }, 2000);
        });
    </script>
</body>
</html>