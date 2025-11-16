<?php
// Nathan Diegelmann - Page Compétences
require_once __DIR__ . '/includes/config.php';

// Récupération des compétences par catégorie
$skills_sql = "SELECT * FROM skills ORDER BY category, display_order ASC";
$skills_stmt = $pdo->query($skills_sql);
$all_skills = $skills_stmt->fetchAll();

// Organiser par catégorie
$skills_by_category = [];
foreach ($all_skills as $skill) {
    $skills_by_category[$skill['category']][] = $skill;
}

// Calculer moyenne par catégorie
$category_averages = [];
foreach ($skills_by_category as $category => $skills) {
    $total = array_sum(array_column($skills, 'level_percentage'));
    $category_averages[$category] = round($total / count($skills));
}

// Labels des catégories
$category_labels = [
    'programming' => 'Programmation',
    'languages' => 'Langues',
    'tools' => 'Outils',
    'soft_skills' => 'Compétences Relationnelles',
    'sports' => 'Sport & Forme'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compétences - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Compétences techniques et personnelles de Nathan Diegelmann - Python, C++, PowerLifting et plus encore.">
    
    <!-- CSS & Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'neon-cyan': '#00ffff',
                        'neon-blue': '#0080ff',
                        'neon-purple': '#8000ff',
                        'neon-pink': '#ff0080',
                        'darker-bg': '#0d1117',
                        'dark-bg': '#161b22',
                        'code-bg': '#21262d',
                        'border-color': '#30363d',
                        'text-primary': '#f0f6fc',
                        'text-secondary': '#8b949e'
                    },
                    fontFamily: {
                        'orbitron': ['Orbitron', 'monospace'],
                        'rajdhani': ['Rajdhani', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .skills-hero {
            background: linear-gradient(135deg, var(--darker-bg), var(--dark-bg));
            padding: 8rem 2rem 4rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .skills-hero::before {
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
                rgba(0, 255, 255, 0.03) 2px,
                rgba(0, 255, 255, 0.03) 4px
            );
            pointer-events: none;
        }
        
        .radar-section {
            padding: 4rem 2rem;
            background: var(--dark-bg);
            text-align: center;
        }
        
        .radar-container {
            max-width: 800px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 4rem;
            align-items: center;
        }
        
        .radar-canvas-wrapper {
            position: relative;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        
        #radarCanvas {
            width: 100%;
            height: 400px;
            border-radius: 50%;
            box-shadow: 
                0 0 50px rgba(0, 255, 255, 0.3),
                inset 0 0 50px rgba(0, 255, 255, 0.1);
            background: radial-gradient(circle, rgba(0, 255, 255, 0.05) 0%, transparent 70%);
        }
        
        .radar-legend {
            display: grid;
            gap: 1rem;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: var(--border-radius);
            border-left: 3px solid var(--neon-cyan);
            transition: var(--transition);
        }
        
        .legend-item:hover {
            background: rgba(0, 255, 255, 0.1);
            transform: translateX(10px);
        }
        
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            box-shadow: 0 0 10px currentColor;
        }
        
        .legend-info h4 {
            color: var(--text-primary);
            margin-bottom: 0.2rem;
            font-weight: 600;
        }
        
        .legend-info span {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .skills-detailed {
            padding: 4rem 2rem;
            background: linear-gradient(135deg, var(--dark-bg), var(--darker-bg));
        }
        
        .skills-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            gap: 3rem;
        }
        
        .skill-category {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(0, 255, 255, 0.2);
            border-radius: var(--border-radius);
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .skill-category::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--neon-cyan), var(--neon-blue), var(--neon-purple));
        }
        
        .category-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .category-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, var(--neon-cyan), var(--neon-blue));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
        }
        
        .category-info h3 {
            font-family: 'Orbitron', monospace;
            color: var(--neon-cyan);
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .category-average {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .skills-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .skill-item {
            background: rgba(0, 255, 255, 0.05);
            border: 1px solid rgba(0, 255, 255, 0.2);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            transition: var(--transition);
            position: relative;
        }
        
        .skill-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 255, 255, 0.2);
            border-color: var(--neon-cyan);
        }
        
        .skill-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .skill-name {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .skill-name i {
            color: var(--neon-cyan);
        }
        
        .skill-name h4 {
            color: var(--text-primary);
            font-weight: 600;
        }
        
        .skill-level-text {
            font-family: 'Orbitron', monospace;
            color: var(--neon-cyan);
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .skill-bar-container {
            background: rgba(0, 0, 0, 0.5);
            height: 10px;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 1rem;
            position: relative;
        }
        
        .skill-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--neon-cyan), var(--neon-blue));
            border-radius: 5px;
            width: 0%;
            transition: width 2s ease-out;
            box-shadow: 0 0 10px var(--neon-cyan);
            position: relative;
        }
        
        .skill-bar::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 4px;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            animation: shine 2s ease-in-out infinite;
        }
        
        @keyframes shine {
            0%, 100% { opacity: 0; }
            50% { opacity: 1; }
        }
        
        .skill-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .skill-item.primary {
            border-color: var(--neon-pink);
            box-shadow: 0 0 15px rgba(255, 0, 128, 0.2);
        }
        
        .skill-item.primary .skill-bar {
            background: linear-gradient(90deg, var(--neon-pink), var(--neon-purple));
            box-shadow: 0 0 10px var(--neon-pink);
        }
        
        .achievements-section {
            padding: 4rem 2rem;
            background: var(--darker-bg);
            text-align: center;
        }
        
        .achievements-grid {
            max-width: 1000px;
            margin: 3rem auto 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .achievement-card {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(0, 255, 255, 0.3);
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            transition: var(--transition);
        }
        
        .achievement-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 255, 255, 0.3);
        }
        
        .achievement-icon {
            font-size: 3rem;
            color: var(--neon-cyan);
            margin-bottom: 1rem;
            text-shadow: 0 0 20px var(--neon-cyan);
        }
        
        .achievement-title {
            color: var(--text-primary);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .achievement-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .radar-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .skills-list {
                grid-template-columns: 1fr;
            }
            
            .achievements-grid {
                grid-template-columns: 1fr;
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
                <a href="competences.php" class="nav-link active">COMPÉTENCES</a>
                <a href="a-propos.php" class="nav-link">À PROPOS</a>
                <a href="contact.php" class="nav-link">CONTACT</a>
            </div>
            

        </div>
    </nav>

    <!-- Hero Compétences -->
    <section class="skills-hero">
        <div class="container">
            <h1 class="section-title">
                <span class="glitch" data-text="COMPÉTENCES">COMPÉTENCES</span>
            </h1>
            <p class="hero-description">
                Un aperçu de mes capacités techniques et personnelles - 
                De la <strong>programmation</strong> au <strong>powerlifting</strong>
            </p>
        </div>
    </section>

    <!-- Radar des compétences -->
    <section class="radar-section">
        <div class="container">
            <h2 class="section-title" style="margin-bottom: 3rem;">RADAR DES COMPÉTENCES</h2>
            
            <div class="radar-container">
                <div class="radar-canvas-wrapper">
                    <canvas id="radarCanvas" width="400" height="400"></canvas>
                </div>
                
                <div class="radar-legend">
                    <?php foreach ($category_averages as $category => $average): ?>
                        <div class="legend-item" data-category="<?php echo $category; ?>">
                            <div class="legend-color" style="background: var(--neon-<?php 
                                $colors = ['cyan', 'blue', 'purple', 'pink', 'cyan'];
                                echo $colors[array_search($category, array_keys($category_averages))] ?? 'cyan';
                            ?>);"></div>
                            <div class="legend-info">
                                <h4><?php echo $category_labels[$category] ?? ucfirst($category); ?></h4>
                                <span><?php echo $average; ?>% de maîtrise</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Compétences détaillées avec Tailwind -->
    <section class="py-20 bg-gradient-to-br from-dark-bg via-darker-bg to-dark-bg relative overflow-hidden">
        <!-- Background effects -->
        <div class="absolute inset-0 opacity-30">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 20% 50%, rgba(0, 255, 255, 0.1) 0%, transparent 50%), radial-gradient(circle at 80% 20%, rgba(128, 0, 255, 0.1) 0%, transparent 50%), radial-gradient(circle at 40% 80%, rgba(255, 0, 128, 0.1) 0%, transparent 50%);"></div>
        </div>
        
        <div class="container mx-auto px-6 relative z-10">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <h2 class="text-5xl md:text-6xl font-orbitron font-bold text-transparent bg-clip-text bg-gradient-to-r from-neon-cyan via-neon-blue to-neon-purple mb-6">
                    DÉTAIL PAR CATÉGORIE
                </h2>
                <div class="w-32 h-1 bg-gradient-to-r from-neon-cyan to-neon-purple mx-auto mb-4"></div>
                <p class="text-text-secondary text-lg max-w-2xl mx-auto font-rajdhani">
                    Découvrez mes compétences organisées par domaine d'expertise avec des niveaux de maîtrise détaillés
                </p>
            </div>
            
            <!-- Categories Grid -->
            <div class="space-y-12">
                <?php foreach ($skills_by_category as $category => $skills): ?>
                    <?php
                    $category_icons = [
                        'programming' => 'fas fa-code',
                        'languages' => 'fas fa-globe',
                        'tools' => 'fas fa-tools',
                        'soft_skills' => 'fas fa-user-friends',
                        'sports' => 'fas fa-dumbbell'
                    ];
                    
                    $category_colors = [
                        'programming' => ['from-neon-cyan', 'to-neon-blue', 'border-neon-cyan'],
                        'languages' => ['from-neon-blue', 'to-neon-purple', 'border-neon-blue'],
                        'tools' => ['from-neon-purple', 'to-neon-pink', 'border-neon-purple'],
                        'soft_skills' => ['from-neon-pink', 'to-neon-cyan', 'border-neon-pink'],
                        'sports' => ['from-green-400', 'to-neon-cyan', 'border-green-400']
                    ];
                    
                    $colors = $category_colors[$category] ?? ['from-neon-cyan', 'to-neon-blue', 'border-neon-cyan'];
                    ?>
                    
                    <!-- Category Container -->
                    <div class="group hover:scale-[1.02] transition-all duration-500">
                        <!-- Category Header -->
                        <div class="bg-gradient-to-r <?php echo $colors[0] . ' ' . $colors[1]; ?> p-1 rounded-2xl mb-8 shadow-2xl shadow-neon-cyan/20">
                            <div class="bg-code-bg rounded-2xl p-8 backdrop-blur-sm">
                                <div class="flex flex-col md:flex-row items-center gap-6">
                                    <!-- Category Icon -->
                                    <div class="relative">
                                        <div class="w-20 h-20 bg-gradient-to-br <?php echo $colors[0] . ' ' . $colors[1]; ?> rounded-2xl flex items-center justify-center shadow-lg transform group-hover:rotate-12 transition-transform duration-500">
                                            <i class="<?php echo $category_icons[$category] ?? 'fas fa-star'; ?> text-2xl text-dark-bg"></i>
                                        </div>
                                        <div class="absolute -inset-1 bg-gradient-to-br <?php echo $colors[0] . ' ' . $colors[1]; ?> rounded-2xl blur opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                                    </div>
                                    
                                    <!-- Category Info -->
                                    <div class="flex-1 text-center md:text-left">
                                        <h3 class="text-3xl md:text-4xl font-orbitron font-bold text-text-primary mb-2">
                                            <?php echo $category_labels[$category] ?? ucfirst($category); ?>
                                        </h3>
                                        <div class="flex items-center justify-center md:justify-start gap-3">
                                            <span class="text-text-secondary font-rajdhani text-lg">Niveau moyen:</span>
                                            <div class="flex items-center gap-2">
                                                <div class="w-24 h-2 bg-darker-bg rounded-full overflow-hidden">
                                                    <div class="h-full bg-gradient-to-r <?php echo $colors[0] . ' ' . $colors[1]; ?> rounded-full transition-all duration-1000" 
                                                         style="width: <?php echo $category_averages[$category]; ?>%"></div>
                                                </div>
                                                <span class="text-2xl font-orbitron font-bold text-transparent bg-clip-text bg-gradient-to-r <?php echo $colors[0] . ' ' . $colors[1]; ?>">
                                                    <?php echo $category_averages[$category]; ?>%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Stats Badge -->
                                    <div class="bg-darker-bg rounded-xl px-4 py-3 border border-border-color">
                                        <div class="text-center">
                                            <div class="text-2xl font-orbitron font-bold text-<?php echo explode('-', $colors[2])[1]; ?>">
                                                <?php echo count($skills); ?>
                                            </div>
                                            <div class="text-xs text-text-secondary font-rajdhani">
                                                COMPÉTENCES
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Skills Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 ml-4">
                            <?php foreach ($skills as $index => $skill): ?>
                                <div class="group/skill relative">
                                    <!-- Skill Card -->
                                    <div class="bg-gradient-to-br from-code-bg to-darker-bg border border-border-color rounded-xl p-6 hover:border-<?php echo explode('-', $colors[2])[1]; ?> transition-all duration-300 hover:shadow-xl hover:shadow-<?php echo explode('-', $colors[2])[1]; ?>/20 hover:-translate-y-2 <?php echo $skill['is_primary'] ? 'ring-2 ring-neon-pink/50' : ''; ?>">
                                        
                                        <!-- Primary Badge -->
                                        <?php if ($skill['is_primary']): ?>
                                            <div class="absolute -top-2 -right-2 bg-gradient-to-r from-neon-pink to-neon-purple text-white px-3 py-1 rounded-full text-xs font-bold font-rajdhani">
                                                ★ PRINCIPAL
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Skill Header -->
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-gradient-to-br <?php echo $colors[0] . ' ' . $colors[1]; ?> rounded-lg flex items-center justify-center">
                                                    <i class="<?php echo $skill['icon_class'] ?: 'fas fa-star'; ?> text-dark-bg"></i>
                                                </div>
                                                <h4 class="font-rajdhani font-bold text-text-primary text-lg group-hover/skill:text-<?php echo explode('-', $colors[2])[1]; ?> transition-colors">
                                                    <?php echo htmlspecialchars($skill['name']); ?>
                                                </h4>
                                            </div>
                                            
                                            <!-- Level Badge -->
                                            <div class="bg-darker-bg border border-<?php echo explode('-', $colors[2])[1]; ?> rounded-lg px-3 py-1">
                                                <span class="font-orbitron font-bold text-<?php echo explode('-', $colors[2])[1]; ?> text-sm">
                                                    <?php echo $skill['level_percentage']; ?>%
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Progress Bar -->
                                        <div class="mb-4">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-text-secondary font-rajdhani text-sm">Niveau de maîtrise</span>
                                                <div class="flex items-center gap-1">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <div class="w-2 h-2 rounded-full <?php echo ($skill['level_percentage'] >= $i * 20) ? 'bg-' . explode('-', $colors[2])[1] : 'bg-border-color'; ?>"></div>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                            <div class="w-full bg-darker-bg rounded-full h-3 overflow-hidden shadow-inner">
                                                <div class="h-full bg-gradient-to-r <?php echo $colors[0] . ' ' . $colors[1]; ?> rounded-full skill-progress transition-all duration-1000 shadow-lg" 
                                                     data-level="<?php echo $skill['level_percentage']; ?>" 
                                                     style="width: 0%">
                                                    <div class="h-full bg-white/20 animate-pulse"></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Description -->
                                        <?php if ($skill['description']): ?>
                                            <p class="text-text-secondary font-rajdhani text-sm leading-relaxed">
                                                <?php echo htmlspecialchars($skill['description']); ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <!-- Hover Effect -->
                                        <div class="absolute inset-0 bg-gradient-to-br <?php echo $colors[0] . ' ' . $colors[1]; ?> opacity-0 group-hover/skill:opacity-5 rounded-xl transition-opacity duration-300"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Atouts et réalisations -->
    <section class="achievements-section">
        <div class="container">
            <h2 class="section-title">MES ATOUTS</h2>
            <p style="color: var(--text-secondary); max-width: 600px; margin: 1rem auto 0;">
                Au-delà des compétences techniques, voici ce qui me définit
            </p>
            
            <div class="achievements-grid">
                <div class="achievement-card">
                    <div class="achievement-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3 class="achievement-title">Concentration Longue Durée</h3>
                    <p class="achievement-description">
                        Capacité à rester focalisé pendant des heures sur des projets complexes
                    </p>
                </div>
                
                <div class="achievement-card">
                    <div class="achievement-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3 class="achievement-title">Apprentissage Rapide</h3>
                    <p class="achievement-description">
                        Assimilation rapide de nouvelles technologies et concepts
                    </p>
                </div>
                
                <div class="achievement-card">
                    <div class="achievement-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <h3 class="achievement-title">Équilibre Vie/Projets</h3>
                    <p class="achievement-description">
                        Gestion efficace entre études, travail, sport et projets personnels
                    </p>
                </div>
                
                <div class="achievement-card">
                    <div class="achievement-icon">
                        <i class="fas fa-search-plus"></i>
                    </div>
                    <h3 class="achievement-title">Veille Technologique</h3>
                    <p class="achievement-description">
                        Passionné par l'innovation et les nouvelles tendances tech
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script>
        // Radar Chart
        function createRadarChart() {
            const canvas = document.getElementById('radarCanvas');
            const ctx = canvas.getContext('2d');
            const centerX = canvas.width / 2;
            const centerY = canvas.height / 2;
            const radius = 150;
            
            // Données du radar
            const data = <?php echo json_encode($category_averages); ?>;
            const labels = <?php echo json_encode($category_labels); ?>;
            
            const categories = Object.keys(data);
            const values = Object.values(data);
            const angleStep = (Math.PI * 2) / categories.length;
            
            // Couleurs par catégorie
            const colors = [
                'rgba(0, 255, 255, 0.8)',   // cyan
                'rgba(0, 128, 255, 0.8)',   // blue  
                'rgba(128, 0, 255, 0.8)',   // purple
                'rgba(255, 0, 128, 0.8)',   // pink
                'rgba(255, 165, 0, 0.8)'    // orange
            ];
            
            function drawRadarGrid() {
                ctx.strokeStyle = 'rgba(0, 255, 255, 0.2)';
                ctx.lineWidth = 1;
                
                // Cercles concentriques
                for (let i = 1; i <= 5; i++) {
                    const r = (radius / 5) * i;
                    ctx.beginPath();
                    ctx.arc(centerX, centerY, r, 0, Math.PI * 2);
                    ctx.stroke();
                    
                    // Pourcentages
                    ctx.fillStyle = 'rgba(0, 255, 255, 0.6)';
                    ctx.font = '12px Orbitron';
                    ctx.fillText(`${i * 20}%`, centerX + r + 5, centerY - 5);
                }
                
                // Lignes radiales
                categories.forEach((category, index) => {
                    const angle = index * angleStep - Math.PI / 2;
                    const x = centerX + Math.cos(angle) * radius;
                    const y = centerY + Math.sin(angle) * radius;
                    
                    ctx.beginPath();
                    ctx.moveTo(centerX, centerY);
                    ctx.lineTo(x, y);
                    ctx.stroke();
                    
                    // Labels
                    const labelX = centerX + Math.cos(angle) * (radius + 30);
                    const labelY = centerY + Math.sin(angle) * (radius + 30);
                    
                    ctx.fillStyle = '#00ffff';
                    ctx.font = '14px Rajdhani';
                    ctx.textAlign = 'center';
                    ctx.fillText(labels[category], labelX, labelY);
                });
            }
            
            function drawRadarData() {
                // Forme de données
                ctx.beginPath();
                values.forEach((value, index) => {
                    const angle = index * angleStep - Math.PI / 2;
                    const distance = (value / 100) * radius;
                    const x = centerX + Math.cos(angle) * distance;
                    const y = centerY + Math.sin(angle) * distance;
                    
                    if (index === 0) {
                        ctx.moveTo(x, y);
                    } else {
                        ctx.lineTo(x, y);
                    }
                });
                ctx.closePath();
                
                // Remplissage avec gradient
                const gradient = ctx.createRadialGradient(centerX, centerY, 0, centerX, centerY, radius);
                gradient.addColorStop(0, 'rgba(0, 255, 255, 0.3)');
                gradient.addColorStop(1, 'rgba(0, 255, 255, 0.1)');
                ctx.fillStyle = gradient;
                ctx.fill();
                
                // Contour
                ctx.strokeStyle = '#00ffff';
                ctx.lineWidth = 2;
                ctx.stroke();
                
                // Points de données
                values.forEach((value, index) => {
                    const angle = index * angleStep - Math.PI / 2;
                    const distance = (value / 100) * radius;
                    const x = centerX + Math.cos(angle) * distance;
                    const y = centerY + Math.sin(angle) * distance;
                    
                    ctx.beginPath();
                    ctx.arc(x, y, 6, 0, Math.PI * 2);
                    ctx.fillStyle = colors[index % colors.length];
                    ctx.fill();
                    ctx.strokeStyle = '#ffffff';
                    ctx.lineWidth = 2;
                    ctx.stroke();
                    
                    // Glow effect
                    ctx.shadowBlur = 10;
                    ctx.shadowColor = colors[index % colors.length];
                    ctx.beginPath();
                    ctx.arc(x, y, 4, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.shadowBlur = 0;
                });
            }
            
            // Animation
            let animationProgress = 0;
            
            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                drawRadarGrid();
                
                if (animationProgress < 1) {
                    animationProgress += 0.02;
                    
                    // Animer les valeurs
                    const animatedValues = values.map(v => v * animationProgress);
                    
                    // Temporairement remplacer les valeurs
                    const originalValues = [...values];
                    values.splice(0, values.length, ...animatedValues);
                    drawRadarData();
                    values.splice(0, values.length, ...originalValues);
                    
                    requestAnimationFrame(animate);
                } else {
                    drawRadarData();
                }
            }
            
            animate();
        }
        
        // Animer les barres de compétences
        function animateSkillBars() {
            const skillBars = document.querySelectorAll('.skill-bar, .skill-progress');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const bar = entry.target;
                        const level = bar.dataset.level;
                        setTimeout(() => {
                            bar.style.width = level + '%';
                        }, Math.random() * 800);
                    }
                });
            }, { threshold: 0.3 });
            
            skillBars.forEach(bar => observer.observe(bar));
        }
        
        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(createRadarChart, 500);
            animateSkillBars();
            
            // Animation des cartes au scroll
            const cards = document.querySelectorAll('.skill-item, .achievement-card');
            const cardObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }, index * 100);
                    }
                });
            });
            
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.6s ease';
                cardObserver.observe(card);
            });
        });
    </script>
</body>
</html>