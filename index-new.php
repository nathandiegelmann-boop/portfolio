<?php
// Nathan Diegelmann - Page d'accueil (Version avec includes)
require_once __DIR__ . '/includes/init.php';

// Configuration de la page
$page_config = [
    'title' => 'Accueil',
    'description' => 'Portfolio de Nathan Diegelmann - Développeur web HTML/CSS/PHP/SQL et powerlifter. Étudiant en Bachelor Développement Web.',
    'current_page' => 'index',
    'extra_css' => '
        /* CSS critique pour loading rapide */
        :root {
            --terminal-green: #00ff41;
            --code-blue: #61dafb;
            --warning-orange: #ffa500;
            --dark-bg: #0d1117;
            --darker-bg: #010409;
            --code-bg: #161b22;
            --text-primary: #f0f6fc;
            --text-secondary: #7d8590;
            --border-color: #30363d;
            --font-mono: "Fira Code", "JetBrains Mono", monospace;
            --font-primary: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, var(--darker-bg), var(--dark-bg));
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-text h1 {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--terminal-green);
            margin-bottom: 1rem;
            font-family: var(--font-mono);
        }

        .hero-text .subtitle {
            font-size: 1.5rem;
            color: var(--code-blue);
            margin-bottom: 2rem;
            font-family: var(--font-mono);
        }

        .hero-description {
            color: var(--text-secondary);
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 3rem;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-primary, .btn-secondary {
            padding: 1rem 2rem;
            border-radius: 6px;
            text-decoration: none;
            font-family: var(--font-mono);
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--terminal-green);
            color: var(--dark-bg);
        }

        .btn-primary:hover {
            background: var(--code-blue);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: transparent;
            color: var(--terminal-green);
            border: 2px solid var(--terminal-green);
        }

        .btn-secondary:hover {
            background: var(--terminal-green);
            color: var(--dark-bg);
        }

        .hero-visual {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .code-display {
            background: var(--code-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 2rem;
            font-family: var(--font-mono);
            color: var(--text-primary);
            width: 100%;
            max-width: 500px;
        }

        /* Skills Preview Section */
        .skills-preview {
            padding: 6rem 2rem;
            background: var(--code-bg);
        }

        .skills-grid {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }

        .skill-card {
            background: var(--dark-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .skill-card:hover {
            transform: translateY(-5px);
            border-color: var(--terminal-green);
        }

        .skill-icon {
            font-size: 3rem;
            color: var(--terminal-green);
            margin-bottom: 1rem;
        }

        .skill-card h3 {
            color: var(--text-primary);
            font-family: var(--font-mono);
            margin-bottom: 0.5rem;
        }

        .skill-level {
            color: var(--code-blue);
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .hero-content {
                grid-template-columns: 1fr;
                gap: 2rem;
                text-align: center;
            }

            .hero-text h1 {
                font-size: 2.5rem;
            }

            .hero-text .subtitle {
                font-size: 1.2rem;
            }

            .cta-buttons {
                justify-content: center;
            }
        }
    '
];

// Inclure header et navigation
include_header($page_config);
include_nav();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <div class="hero-text">
            <h1>Nathan Diegelmann</h1>
            <div class="subtitle">Développeur Web & Powerlifter</div>
            <p class="hero-description">
                Étudiant en Bachelor Développement Web, passionné par la création d'applications web 
                performantes avec HTML, CSS, PHP et MySQL. Quand je ne code pas, je soulève des barres !
            </p>
            <div class="cta-buttons">
                <a href="projets.php" class="btn-primary">
                    <i class="fas fa-code"></i> Voir mes projets
                </a>
                <a href="contact.php" class="btn-secondary">
                    <i class="fas fa-envelope"></i> Me contacter
                </a>
            </div>
        </div>
        
        <div class="hero-visual">
            <div class="code-display">
                <div style="color: var(--code-blue);">class <span style="color: var(--terminal-green);">Developer</span> extends <span style="color: var(--warning-orange);">PowerLifter</span> {</div>
                <div style="margin-left: 2rem; margin: 1rem 0 1rem 2rem;">
                    <div>constructor() {</div>
                    <div style="margin-left: 2rem;">
                        <div>this.name = '<span style="color: var(--terminal-green);">Nathan</span>';</div>
                        <div>this.skills = ['<span style="color: var(--terminal-green);">HTML</span>', '<span style="color: var(--terminal-green);">CSS</span>', '<span style="color: var(--terminal-green);">PHP</span>', '<span style="color: var(--terminal-green);">SQL</span>'];</div>
                        <div>this.passion = '<span style="color: var(--warning-orange);">PowerLifting</span>';</div>
                    </div>
                    <div>}</div>
                </div>
                <div>}</div>
            </div>
        </div>
    </div>
</section>

<!-- Skills Preview -->
<section class="skills-preview">
    <div class="container">
        <h2 class="section-title text-center mb-3">Mes Compétences</h2>
        
        <div class="skills-grid">
            <div class="skill-card">
                <div class="skill-icon">
                    <i class="fab fa-html5"></i>
                </div>
                <h3>HTML</h3>
                <div class="skill-level"><?php echo getSkillLevel('HTML'); ?>%</div>
            </div>
            
            <div class="skill-card">
                <div class="skill-icon">
                    <i class="fab fa-css3-alt"></i>
                </div>
                <h3>CSS</h3>
                <div class="skill-level"><?php echo getSkillLevel('CSS'); ?>%</div>
            </div>
            
            <div class="skill-card">
                <div class="skill-icon">
                    <i class="fab fa-php"></i>
                </div>
                <h3>PHP</h3>
                <div class="skill-level"><?php echo getSkillLevel('PHP'); ?>%</div>
            </div>
            
            <div class="skill-card">
                <div class="skill-icon">
                    <i class="fas fa-database"></i>
                </div>
                <h3>SQL</h3>
                <div class="skill-level"><?php echo getSkillLevel('SQL'); ?>%</div>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <a href="competences.php" class="btn-primary">
                <i class="fas fa-chart-radar"></i> Voir toutes mes compétences
            </a>
        </div>
    </div>
</section>

<?php
// Inclure le footer
include_footer();
?>