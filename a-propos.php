<?php
// Nathan Diegelmann - Page À Propos
require_once __DIR__ . '/includes/config.php';

// Récupération des expériences
$experiences_sql = "SELECT * FROM experiences ORDER BY start_date DESC, display_order ASC";
$experiences_stmt = $pdo->query($experiences_sql);
$experiences = $experiences_stmt->fetchAll();

// Calculs stats personnelles
$age = calculateAge('2006-01-19');
$experience_years = date('Y') - 2023; // Depuis début chez Marché Bruno
$study_years = date('Y') - 2021; // Depuis le collège
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À Propos - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Découvrez Nathan Diegelmann - Étudiant développeur passionné par la programmation, le powerlifting et l'innovation technologique.">
    
    <!-- CSS & Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@300;400;500;600;700&family=JetBrains+Mono:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .about-hero {
            background: linear-gradient(135deg, var(--darker-bg), var(--dark-bg));
            padding: 8rem 2rem 4rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .about-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 2px,
                rgba(0, 255, 65, 0.02) 2px,
                rgba(0, 255, 65, 0.02) 4px
            );
            pointer-events: none;
        }
        
        .profile-section {
            padding: 4rem 2rem;
            background: var(--dark-bg);
        }
        
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 4rem;
            align-items: start;
        }
        
        .profile-card {
            background: var(--code-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 2rem;
            position: sticky;
            top: 6rem;
            text-align: center;
        }
        
        .profile-avatar {
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, var(--terminal-green), var(--code-blue));
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: var(--dark-bg);
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.3);
        }
        
        .profile-name {
            font-family: var(--font-mono);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--terminal-green);
            margin-bottom: 0.5rem;
        }
        
        .profile-role {
            color: var(--code-blue);
            font-size: 1rem;
            margin-bottom: 1.5rem;
            font-family: var(--font-mono);
        }
        
        .profile-stats {
            display: grid;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            background: rgba(0, 255, 65, 0.05);
            border-radius: var(--border-radius);
            font-family: var(--font-mono);
            font-size: 0.9rem;
        }
        
        .stat-label {
            color: var(--text-secondary);
        }
        
        .stat-value {
            color: var(--terminal-green);
            font-weight: 600;
        }
        
        .profile-links {
            display: grid;
            gap: 0.5rem;
        }
        
        .profile-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem;
            background: var(--darker-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            color: var(--text-primary);
            text-decoration: none;
            font-family: var(--font-mono);
            font-size: 0.9rem;
            transition: var(--transition);
        }
        
        .profile-link:hover {
            border-color: var(--terminal-green);
            background: rgba(0, 255, 65, 0.1);
            color: var(--terminal-green);
        }
        
        .about-content {
            background: var(--code-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 2rem;
        }
        
        .content-section {
            margin-bottom: 3rem;
        }
        
        .content-section:last-child {
            margin-bottom: 0;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--terminal-green);
        }
        
        .section-icon {
            width: 40px;
            height: 40px;
            background: var(--terminal-green);
            color: var(--dark-bg);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .section-title {
            font-family: var(--font-mono);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--terminal-green);
            margin: 0;
        }
        
        .content-text {
            color: var(--text-primary);
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }
        
        .highlight-box {
            background: rgba(0, 255, 65, 0.05);
            border-left: 4px solid var(--terminal-green);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin: 1.5rem 0;
        }
        
        .highlight-box h4 {
            color: var(--terminal-green);
            font-family: var(--font-mono);
            margin-bottom: 0.5rem;
        }
        
        .timeline-section {
            padding: 6rem 2rem;
            background: linear-gradient(135deg, var(--dark-bg), var(--darker-bg));
            position: relative;
            overflow: hidden;
        }
        
        .timeline-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 100px,
                rgba(0, 255, 65, 0.02) 100px,
                rgba(0, 255, 65, 0.02) 102px
            );
            pointer-events: none;
        }
        
        .timeline-container {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
        }
        
        .timeline-enhanced {
            position: relative;
            padding: 2rem 0;
        }
        
        .timeline-enhanced::before {
            content: '';
            position: absolute;
            left: 40px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(
                to bottom,
                var(--terminal-green),
                var(--code-blue),
                var(--terminal-green)
            );
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.4);
            border-radius: 10px;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 4rem;
            padding-left: 120px;
            opacity: 0;
            transform: translateX(-50px);
            animation: slideInTimeline 0.8s ease forwards;
        }
        
        .timeline-item:nth-child(1) { animation-delay: 0.1s; }
        .timeline-item:nth-child(2) { animation-delay: 0.3s; }
        .timeline-item:nth-child(3) { animation-delay: 0.5s; }
        .timeline-item:nth-child(4) { animation-delay: 0.7s; }
        .timeline-item:nth-child(5) { animation-delay: 0.9s; }
        
        @keyframes slideInTimeline {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .timeline-marker {
            position: absolute;
            left: 25px;
            top: 10px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            border: 3px solid var(--dark-bg);
            box-shadow: 0 0 25px rgba(0, 255, 65, 0.6);
            z-index: 2;
        }
        
        .timeline-marker.active {
            background: var(--terminal-green);
            color: var(--dark-bg);
            animation: pulse 2s infinite;
        }
        
        .timeline-marker.work {
            background: var(--warning-orange);
            color: var(--dark-bg);
        }
        
        .timeline-marker.success {
            background: #28a745;
            color: white;
        }
        
        .timeline-marker.education {
            background: var(--code-blue);
            color: white;
        }
        
        .timeline-marker.foundation {
            background: #6c757d;
            color: white;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .timeline-content {
            position: relative;
        }
        
        .timeline-date {
            margin-bottom: 1rem;
        }
        
        .timeline-date .year {
            font-family: var(--font-mono);
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--terminal-green);
            display: block;
        }
        
        .timeline-date .period {
            font-family: var(--font-mono);
            font-size: 0.8rem;
            color: var(--code-blue);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .timeline-card {
            background: var(--code-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .timeline-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--terminal-green);
            opacity: 0;
            transition: var(--transition);
        }
        
        .timeline-card:hover {
            transform: translateX(15px);
            border-color: var(--terminal-green);
            box-shadow: 0 10px 30px rgba(0, 255, 65, 0.2);
        }
        
        .timeline-card:hover::before {
            opacity: 1;
        }
        
        .timeline-card.highlight {
            border-color: var(--terminal-green);
            box-shadow: 0 0 25px rgba(0, 255, 65, 0.1);
        }
        
        .timeline-card.work {
            border-color: var(--warning-orange);
        }
        
        .timeline-card.success {
            border-color: #28a745;
        }
        
        .timeline-card.education {
            border-color: var(--code-blue);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .timeline-title {
            font-family: var(--font-mono);
            color: var(--text-primary);
            font-weight: 700;
            font-size: 1.3rem;
            margin: 0;
            flex: 1;
        }
        
        .status-badge {
            font-family: var(--font-mono);
            font-size: 0.7rem;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .status-badge.current {
            background: rgba(0, 255, 65, 0.2);
            color: var(--terminal-green);
            border: 1px solid var(--terminal-green);
        }
        
        .status-badge.work {
            background: rgba(255, 107, 107, 0.2);
            color: var(--warning-orange);
            border: 1px solid var(--warning-orange);
        }
        
        .status-badge.success {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid #28a745;
        }
        
        .status-badge.education {
            background: rgba(97, 218, 251, 0.2);
            color: var(--code-blue);
            border: 1px solid var(--code-blue);
        }
        
        .status-badge.foundation {
            background: rgba(108, 117, 125, 0.2);
            color: #6c757d;
            border: 1px solid #6c757d;
        }
        
        .timeline-institution {
            color: var(--code-blue);
            font-size: 1rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .timeline-institution i {
            margin-right: 0.5rem;
            color: var(--terminal-green);
        }
        
        .timeline-description {
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }
        
        .timeline-description p {
            margin-bottom: 1rem;
        }
        
        .timeline-description strong {
            color: var(--text-primary);
        }
        
        .skills-list, .tasks-list {
            list-style: none;
            padding: 0;
            margin: 1rem 0;
            display: grid;
            gap: 0.5rem;
        }
        
        .skills-list li, .tasks-list li {
            padding: 0.5rem;
            background: rgba(0, 255, 65, 0.05);
            border-left: 3px solid var(--terminal-green);
            border-radius: 0 4px 4px 0;
            font-size: 0.9rem;
        }
        
        .skills-list li i {
            color: var(--terminal-green);
            margin-right: 0.5rem;
            width: 16px;
        }
        
        .timeline-achievements {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .achievement {
            font-family: var(--font-mono);
            font-size: 0.7rem;
            padding: 0.3rem 0.8rem;
            background: rgba(97, 218, 251, 0.1);
            color: var(--code-blue);
            border: 1px solid rgba(97, 218, 251, 0.3);
            border-radius: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .interests-section {
            padding: 4rem 2rem;
            background: var(--darker-bg);
        }
        
        .interests-grid {
            max-width: 1000px;
            margin: 3rem auto 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }
        
        .interest-card {
            background: var(--code-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }
        
        .interest-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--terminal-green), var(--code-blue));
            transform: scaleX(0);
            transition: var(--transition);
        }
        
        .interest-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 255, 65, 0.2);
        }
        
        .interest-card:hover::before {
            transform: scaleX(1);
        }
        
        .interest-icon {
            font-size: 3rem;
            color: var(--terminal-green);
            margin-bottom: 1rem;
            text-shadow: 0 0 20px var(--terminal-green);
        }
        
        .interest-title {
            font-family: var(--font-mono);
            color: var(--text-primary);
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }
        
        .interest-description {
            color: var(--text-secondary);
            line-height: 1.6;
        }
        
        .powerlifting-highlight {
            background: linear-gradient(135deg, rgba(255, 107, 107, 0.1), rgba(255, 165, 0, 0.1));
            border-color: var(--warning-orange);
        }
        
        .powerlifting-highlight .interest-icon {
            color: var(--warning-orange);
            text-shadow: 0 0 20px var(--warning-orange);
        }
        
        @media (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .profile-card {
                position: static;
            }
            
            .timeline-section {
                padding: 4rem 1rem;
            }
            
            .timeline-enhanced::before {
                left: 20px;
                width: 2px;
            }
            
            .timeline-item {
                padding-left: 60px;
                margin-bottom: 3rem;
            }
            
            .timeline-marker {
                left: 5px;
                width: 25px;
                height: 25px;
                font-size: 0.8rem;
            }
            
            .timeline-date .year {
                font-size: 1rem;
            }
            
            .timeline-date .period {
                font-size: 0.7rem;
            }
            
            .timeline-card {
                padding: 1.5rem;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .timeline-title {
                font-size: 1.1rem;
            }
            
            .status-badge {
                align-self: flex-start;
            }
            
            .skills-list {
                grid-template-columns: 1fr;
            }
            
            .timeline-achievements {
                justify-content: flex-start;
            }
            
            .interests-grid {
                grid-template-columns: 1fr;
            }
            
            .timeline-card:hover {
                transform: translateX(5px);
            }
        }
        
        @media (max-width: 480px) {
            .timeline-item {
                padding-left: 45px;
            }
            
            .timeline-marker {
                left: 0;
                width: 20px;
                height: 20px;
                font-size: 0.7rem;
            }
            
            .timeline-enhanced::before {
                left: 10px;
            }
            
            .timeline-card {
                padding: 1rem;
            }
            
            .timeline-title {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="cyber-nav">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php" style="text-decoration: none; color: inherit;">
                    <span>&lt;NATHAN/&gt;</span>
                </a>
            </div>
            
            <div class="nav-menu">
                <a href="index.php" class="nav-link">ACCUEIL</a>
                <a href="projets.php" class="nav-link">PROJETS</a>
                <a href="competences.php" class="nav-link">COMPÉTENCES</a>
                <a href="a-propos.php" class="nav-link active">À PROPOS</a>
                <a href="contact.php" class="nav-link">CONTACT</a>
            </div>
            

        </div>
    </nav>

    <!-- Hero À Propos -->
    <section class="about-hero">
        <div class="container">
            <h1 class="section-title">
                <span class="glitch" data-text="À PROPOS DE MOI">À PROPOS DE MOI</span>
            </h1>
            <p class="hero-description">
                <span class="inline-code">const nathan = new Developer({ passion: "code", hobby: "powerlifting" })</span>
            </p>
        </div>
    </section>

    <!-- Profil Principal -->
    <section class="profile-section">
        <div class="profile-container">
            <!-- Carte profil -->
            <div class="profile-card">
                <div class="profile-avatar">
                    <i class="fas fa-user-astronaut"></i>
                </div>
                
                <h2 class="profile-name"><?php echo $nathan_info['nom']; ?></h2>
                <p class="profile-role">Bachelor Dev Web • Développeur • Powerlifter</p>
                
                <div class="profile-stats">
                    <div class="stat-row">
                        <span class="stat-label">Âge:</span>
                        <span class="stat-value"><?php echo $age; ?> ans</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Localisation:</span>
                        <span class="stat-value"><?php echo $nathan_info['ville']; ?></span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Statut:</span>
                        <span class="stat-value">Bac+1 Web</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Expérience:</span>
                        <span class="stat-value"><?php echo $experience_years; ?>+ ans</span>
                    </div>
                </div>
                
                <div class="profile-links">
                    <a href="mailto:<?php echo $nathan_info['email']; ?>" class="profile-link">
                        <i class="fas fa-envelope"></i>
                        Email
                    </a>
                    <a href="tel:<?php echo $nathan_info['telephone']; ?>" class="profile-link">
                        <i class="fas fa-phone"></i>
                        Téléphone
                    </a>
                    <a href="https://instagram.com/<?php echo ltrim($nathan_info['instagram'], '@'); ?>" class="profile-link" target="_blank">
                        <i class="fab fa-instagram"></i>
                        Instagram
                    </a>
                    <a href="#" class="profile-link">
                        <i class="fab fa-github"></i>
                        GitHub
                    </a>
                </div>
            </div>
            
            <!-- Contenu principal -->
            <div class="about-content">
                <!-- Qui suis-je -->
                <div class="content-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-terminal"></i>
                        </div>
                        <h3 class="section-title">Qui suis-je ?</h3>
                    </div>
                    
                    <p class="content-text">
                        Salut ! Je suis <strong>Nathan Diegelmann</strong>, un étudiant de <?php echo $age; ?> ans passionné par le développement logiciel et les défis physiques. 
                        Actuellement en <strong>Bachelor Développement Web</strong> après l'obtention de mon <strong>Baccalauréat en 2024</strong>, je combine mes études avec une expérience professionnelle 
                        chez <strong>Marché Bruno</strong> depuis 2023.
                    </p>
                    
                    <p class="content-text">
                        Mon parcours atypique me permet d'allier <strong>rigueur technique</strong> et <strong>discipline personnelle</strong>. 
                        Que ce soit en codant une application Python complexe ou en soulevant des barres au powerlifting, 
                        j'applique la même méthode : <em>analyse, préparation, exécution, amélioration continue</em>.
                    </p>
                    
                    <div class="highlight-box">
                        <h4><i class="fas fa-lightbulb"></i> Ma philosophie</h4>
                        <p>
                            "Le code parfait n'existe pas, mais l'amélioration continue mène à l'excellence. 
                            Comme au powerlifting, chaque répétition compte, chaque ligne de code a son importance."
                        </p>
                    </div>
                </div>
                
                <!-- Approche du développement -->
                <div class="content-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <h3 class="section-title">Mon Approche du Développement</h3>
                    </div>
                    
                    <p class="content-text">
                        Je privilégie une approche <strong>pragmatique et méthodique</strong> du développement. 
                        Mon expérience en powerlifting m'a appris l'importance de la <strong>progression graduelle</strong> 
                        et de la <strong>consistance</strong> - des qualités que j'applique directement en programmation.
                    </p>
                    
                    <p class="content-text">
                        <strong>Mes spécialisations :</strong>
                    </p>
                    <ul style="color: var(--text-secondary); margin-left: 2rem; line-height: 1.8;">
                        <li><strong>Python</strong> - Automation, data analysis, applications desktop</li>
                        <li><strong>C++</strong> - Programmation système et applications performantes</li>
                        <li><strong>JavaScript</strong> - Développement web et scripts d'automatisation</li>
                        <li><strong>Bases de données</strong> - Conception et optimisation MySQL/SQLite</li>
                    </ul>
                </div>
                
                <!-- Équilibre vie pro/perso -->
                <div class="content-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <h3 class="section-title">Équilibre & Passions</h3>
                    </div>
                    
                    <p class="content-text">
                        En dehors du code, le <strong>powerlifting</strong> occupe une place centrale dans ma vie. 
                        Ce sport m'a enseigné la <strong>discipline</strong>, la <strong>persévérance</strong> et 
                        l'importance de <strong>fixer des objectifs mesurables</strong> - des compétences directement 
                        transférables dans le développement logiciel.
                    </p>
                    
                    <p class="content-text">
                        Mon travail chez <strong>Marché Bruno</strong> m'a également développé d'excellentes 
                        capacités de <strong>service client</strong> et de <strong>gestion du stress</strong> 
                        en environnement dynamique.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Parcours -->
    <section class="timeline-section">
        <div class="container">
            <h2 class="section-title">MON PARCOURS</h2>
            <p style="text-align: center; color: var(--text-secondary); margin-bottom: 3rem; font-family: var(--font-mono);">
                <span class="inline-code">git log --oneline --reverse --graph</span>
            </p>
            
            <div class="timeline-container">
                <div class="timeline-enhanced">
                    <!-- 2024 - PRÉSENT : Bachelor Développement Web -->
                    <div class="timeline-item current">
                        <div class="timeline-marker active">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-date">
                                <span class="year">2024</span>
                                <span class="period">Septembre - Présent</span>
                            </div>
                            <div class="timeline-card highlight">
                                <div class="card-header">
                                    <h4 class="timeline-title">Bachelor Développement Web</h4>
                                    <span class="status-badge current">En cours</span>
                                </div>
                                <p class="timeline-institution">
                                    <i class="fas fa-graduation-cap"></i> 
                                    École Supérieure - Formation Bac+3
                                </p>
                                <div class="timeline-description">
                                    <p><strong>Formation complète en développement web full-stack :</strong></p>
                                    <ul class="skills-list">
                                        <li><i class="fab fa-html5"></i> HTML5 & Sémantique web</li>
                                        <li><i class="fab fa-css3-alt"></i> CSS3 & Design responsive</li>
                                        <li><i class="fab fa-php"></i> PHP & Développement backend</li>
                                        <li><i class="fas fa-database"></i> MySQL & Gestion BDD</li>
                                    </ul>
                                </div>
                                <div class="timeline-achievements">
                                    <span class="achievement">Spécialisation Web</span>
                                    <span class="achievement">Projets pratiques</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2023 - PRÉSENT : Travail Marché Bruno -->
                    <div class="timeline-item current">
                        <div class="timeline-marker work">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-date">
                                <span class="year">2023</span>
                                <span class="period">Janvier - Présent</span>
                            </div>
                            <div class="timeline-card work">
                                <div class="card-header">
                                    <h4 class="timeline-title">Employé Polyvalent</h4>
                                    <span class="status-badge work">2 ans</span>
                                </div>
                                <p class="timeline-institution">
                                    <i class="fas fa-store"></i> 
                                    Marché Bruno - Boussy-Saint-Antoine
                                </p>
                                <div class="timeline-description">
                                    <p><strong>Expérience professionnelle variée :</strong></p>
                                    <ul class="tasks-list">
                                        <li>🥗 Mise en place des étalages produits frais</li>
                                        <li>👥 Service clientèle et conseil</li>
                                        <li>🧹 Nettoyage et organisation des espaces</li>
                                        <li>📦 Gestion des stocks et inventaires</li>
                                    </ul>
                                </div>
                                <div class="timeline-achievements">
                                    <span class="achievement">Autonomie</span>
                                    <span class="achievement">Relation client</span>
                                    <span class="achievement">Polyvalence</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2024 : Obtention du Baccalauréat -->
                    <div class="timeline-item completed">
                        <div class="timeline-marker success">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-date">
                                <span class="year">2024</span>
                                <span class="period">Juillet</span>
                            </div>
                            <div class="timeline-card success">
                                <div class="card-header">
                                    <h4 class="timeline-title">Baccalauréat Obtenu</h4>
                                    <span class="status-badge success">Diplômé</span>
                                </div>
                                <p class="timeline-institution">
                                    <i class="fas fa-school"></i> 
                                    Lycée Christophe Colomb
                                </p>
                                <div class="timeline-description">
                                    <p>Baccalauréat général obtenu avec succès - spécialisation scientifique</p>
                                    <p><em>Étape cruciale vers les études supérieures en informatique</em></p>
                                </div>
                                <div class="timeline-achievements">
                                    <span class="achievement">Diplôme national</span>
                                    <span class="achievement">Spé. Sciences</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2021-2024 : Lycée -->
                    <div class="timeline-item completed">
                        <div class="timeline-marker education">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-date">
                                <span class="year">2021-24</span>
                                <span class="period">3 années</span>
                            </div>
                            <div class="timeline-card education">
                                <div class="card-header">
                                    <h4 class="timeline-title">Formation Lycée</h4>
                                    <span class="status-badge education">Terminé</span>
                                </div>
                                <p class="timeline-institution">
                                    <i class="fas fa-school"></i> 
                                    Lycée Christophe Colomb
                                </p>
                                <div class="timeline-description">
                                    <p>Formation générale avec découverte de la programmation et des sciences</p>
                                    <p><strong>Développement de l'intérêt pour l'informatique</strong></p>
                                </div>
                                <div class="timeline-achievements">
                                    <span class="achievement">Culture générale</span>
                                    <span class="achievement">Méthode de travail</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2018-2021 : Collège -->
                    <div class="timeline-item completed">
                        <div class="timeline-marker foundation">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-date">
                                <span class="year">2018-21</span>
                                <span class="period">Fondations</span>
                            </div>
                            <div class="timeline-card foundation">
                                <div class="card-header">
                                    <h4 class="timeline-title">Brevet des Collèges</h4>
                                    <span class="status-badge foundation">Acquis</span>
                                </div>
                                <p class="timeline-institution">
                                    <i class="fas fa-school"></i> 
                                    Collège La Guinette - Villecresnes
                                </p>
                                <div class="timeline-description">
                                    <p>Formation secondaire - développement des bases fondamentales</p>
                                </div>
                                <div class="timeline-achievements">
                                    <span class="achievement">Bases solides</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Centres d'intérêt -->
    <section class="interests-section">
        <div class="container">
            <h2 class="section-title">CENTRES D'INTÉRÊT</h2>
            <p style="text-align: center; color: var(--text-secondary); margin-bottom: 0; font-family: var(--font-mono);">
                Ce qui me passionne au-delà du code
            </p>
            
            <div class="interests-grid">
                <div class="interest-card powerlifting-highlight">
                    <div class="interest-icon">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <h4 class="interest-title">PowerLifting Compétitif</h4>
                    <p class="interest-description">
                        <strong>Passion depuis 3+ ans</strong><br>
                        Le powerlifting m'a enseigné la discipline, la persévérance et l'importance des objectifs mesurables. 
                        Compétiteur régulier, je trouve dans ce sport l'équilibre parfait avec l'activité intellectuelle du développement. 
                        <em>Force physique + force mentale = combo gagnant !</em>
                    </p>
                </div>
                
                <div class="interest-card">
                    <div class="interest-icon">
                        <i class="fab fa-github"></i>
                    </div>
                    <h4 class="interest-title">Open Source & Communauté</h4>
                    <p class="interest-description">
                        Contribution active aux projets open source et partage de connaissances avec la communauté dev. 
                        J'aime apprendre des autres et transmettre ce que je découvre.
                    </p>
                </div>
                
                <div class="interest-card">
                    <div class="interest-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4 class="interest-title">Veille Technologique</h4>
                    <p class="interest-description">
                        Passionné par l'évolution rapide des technologies. Je suis constamment à l'affût des nouvelles 
                        tendances, frameworks et outils qui pourraient améliorer mes projets.
                    </p>
                </div>
                
                <div class="interest-card">
                    <div class="interest-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4 class="interest-title">Vie Sociale & Réseaux</h4>
                    <p class="interest-description">
                        Équilibre important entre projets techniques et moments avec les amis. 
                        Les réseaux sociaux me permettent de rester connecté et de découvrir de nouvelles inspirations.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script>
        // Animation des cartes au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, index * 100);
                }
            });
        }, observerOptions);
        
        // Animation de la timeline
        const timelineObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);
        
        document.addEventListener('DOMContentLoaded', () => {
            // Animer les cartes d'intérêt
            const cards = document.querySelectorAll('.interest-card');
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                cardObserver.observe(card);
            });
            
            // Animer la timeline
            const timelineItems = document.querySelectorAll('.timeline-item');
            timelineItems.forEach(item => {
                timelineObserver.observe(item);
            });
            
            // Effet parallax léger sur l'avatar
            const avatar = document.querySelector('.profile-avatar');
            if (avatar) {
                window.addEventListener('scroll', () => {
                    const scrolled = window.pageYOffset;
                    const rate = scrolled * -0.1;
                    avatar.style.transform = `translateY(${rate}px) rotateY(${scrolled * 0.1}deg)`;
                });
            }
        });
        
        // Easter egg - click sur l'avatar
        document.querySelector('.profile-avatar')?.addEventListener('click', () => {
            const messages = [
                "console.log('Hello World!')",
                "git commit -m 'Another day, another bug fixed'",
                "// TODO: Become awesome developer",
                "const motivation = powerlifting.enabled ? 'MAX' : 'HIGH'",
                "if (coffee.available) { code.quality++; }"
            ];
            
            const randomMessage = messages[Math.floor(Math.random() * messages.length)];
            
            // Créer notification temporaire
            const notification = document.createElement('div');
            notification.innerHTML = `<code style="color: var(--terminal-green);">${randomMessage}</code>`;
            notification.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: var(--darker-bg);
                border: 2px solid var(--terminal-green);
                padding: 1rem 2rem;
                border-radius: 8px;
                box-shadow: 0 0 30px rgba(0, 255, 65, 0.3);
                z-index: 10000;
                font-family: var(--font-mono);
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 2000);
        });
    </script>
</body>
</html>