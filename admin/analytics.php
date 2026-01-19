<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Statistiques générales
$stats = [];

// Visiteurs actifs (dernières 5 minutes)
$stats['active_now'] = $pdo->query("SELECT COUNT(DISTINCT session_id) FROM active_sessions WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)")->fetchColumn();

// Visites aujourd'hui
$stats['today'] = $pdo->query("SELECT COUNT(*) FROM site_visits WHERE DATE(visit_time) = CURDATE()")->fetchColumn();

// Visites cette semaine
$stats['this_week'] = $pdo->query("SELECT COUNT(*) FROM site_visits WHERE YEARWEEK(visit_time, 1) = YEARWEEK(CURDATE(), 1)")->fetchColumn();

// Visites ce mois
$stats['this_month'] = $pdo->query("SELECT COUNT(*) FROM site_visits WHERE YEAR(visit_time) = YEAR(CURDATE()) AND MONTH(visit_time) = MONTH(CURDATE())")->fetchColumn();

// Total de visites
$stats['total'] = $pdo->query("SELECT COUNT(*) FROM site_visits")->fetchColumn();

// Visiteurs uniques (par IP) aujourd'hui
$stats['unique_today'] = $pdo->query("SELECT COUNT(DISTINCT visitor_ip) FROM site_visits WHERE DATE(visit_time) = CURDATE()")->fetchColumn();

// Visiteurs actifs en ce moment
$active_visitors = $pdo->query("
    SELECT session_id, visitor_ip, last_activity, page_current 
    FROM active_sessions 
    WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
    ORDER BY last_activity DESC
")->fetchAll();

// Pages les plus visitées (24h)
$popular_pages = $pdo->query("
    SELECT page_url, COUNT(*) as visits 
    FROM site_visits 
    WHERE visit_time > DATE_SUB(NOW(), INTERVAL 24 HOUR)
    GROUP BY page_url 
    ORDER BY visits DESC 
    LIMIT 10
")->fetchAll();

// Visites par heure (dernières 24h)
$visits_by_hour = $pdo->query("
    SELECT HOUR(visit_time) as hour, COUNT(*) as visits 
    FROM site_visits 
    WHERE visit_time > DATE_SUB(NOW(), INTERVAL 24 HOUR)
    GROUP BY HOUR(visit_time)
    ORDER BY hour
")->fetchAll();

// Visites par jour (7 derniers jours)
$visits_by_day = $pdo->query("
    SELECT DATE(visit_time) as date, COUNT(*) as visits 
    FROM site_visits 
    WHERE visit_time > DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(visit_time)
    ORDER BY date
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - Administration</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .stats-grid-analytics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-box {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-box i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .stat-box.active {
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
        }
        .stat-box.today {
            background: linear-gradient(135deg, #3B82F6, #1D4ED8);
            color: white;
        }
        .stat-box.week {
            background: linear-gradient(135deg, #F59E0B, #D97706);
            color: white;
        }
        .stat-box.month {
            background: linear-gradient(135deg, #8B5CF6, #7C3AED);
            color: white;
        }
        .stat-box.total {
            background: linear-gradient(135deg, #EC4899, #DB2777);
            color: white;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0.5rem 0;
        }
        .stat-label {
            font-size: 0.95rem;
            opacity: 0.9;
        }
        .active-visitor {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            border-left: 3px solid #10B981;
        }
        .visitor-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .visitor-pulse {
            width: 12px;
            height: 12px;
            background: #10B981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .page-visit {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .page-visit:last-child {
            border-bottom: none;
        }
    </style>
    <meta http-equiv="refresh" content="30">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-chart-line"></i> Statistiques du Site</h1>
                <p class="subtitle">Analyse du trafic et des visiteurs en temps réel</p>
            </div>
            <div style="color: #64748B; font-size: 0.875rem;">
                <i class="fas fa-sync-alt"></i> Actualisation automatique toutes les 30s
            </div>
        </div>
        
        <!-- Statistiques principales -->
        <div class="stats-grid-analytics">
            <div class="stat-box active">
                <i class="fas fa-users"></i>
                <div class="stat-number"><?php echo $stats['active_now']; ?></div>
                <div class="stat-label">En ligne maintenant</div>
            </div>
            
            <div class="stat-box today">
                <i class="fas fa-calendar-day"></i>
                <div class="stat-number"><?php echo $stats['today']; ?></div>
                <div class="stat-label">Visites aujourd'hui</div>
            </div>
            
            <div class="stat-box week">
                <i class="fas fa-calendar-week"></i>
                <div class="stat-number"><?php echo $stats['this_week']; ?></div>
                <div class="stat-label">Cette semaine</div>
            </div>
            
            <div class="stat-box month">
                <i class="fas fa-calendar-alt"></i>
                <div class="stat-number"><?php echo $stats['this_month']; ?></div>
                <div class="stat-label">Ce mois</div>
            </div>
            
            <div class="stat-box total">
                <i class="fas fa-chart-bar"></i>
                <div class="stat-number"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Total visites</div>
            </div>
        </div>
        
        <!-- Visiteurs actifs -->
        <div class="section-card">
            <h2><i class="fas fa-user-clock"></i> Visiteurs Actifs (<?php echo count($active_visitors); ?>)</h2>
            <?php if (!empty($active_visitors)): ?>
                <?php foreach ($active_visitors as $visitor): ?>
                <div class="active-visitor">
                    <div class="visitor-info">
                        <div class="visitor-pulse"></div>
                        <div>
                            <strong><?php echo htmlspecialchars($visitor['visitor_ip']); ?></strong>
                            <div style="font-size: 0.875rem; color: #64748B;">
                                <?php echo htmlspecialchars($visitor['page_current']); ?>
                            </div>
                        </div>
                    </div>
                    <div style="font-size: 0.875rem; color: #64748B;">
                        Il y a <?php echo round((time() - strtotime($visitor['last_activity'])) / 60); ?> min
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-user-slash"></i>
                    <h3>Aucun visiteur actif</h3>
                    <p>Personne n'est actuellement sur le site</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pages populaires -->
        <div class="section-card">
            <h2><i class="fas fa-star"></i> Pages les Plus Visitées (24h)</h2>
            <?php if (!empty($popular_pages)): ?>
                <?php foreach ($popular_pages as $page): ?>
                <div class="page-visit">
                    <div>
                        <strong><?php echo htmlspecialchars($page['page_url'] ?: '/'); ?></strong>
                    </div>
                    <div>
                        <span class="badge"><?php echo $page['visits']; ?> visites</span>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-data">Aucune donnée disponible</p>
            <?php endif; ?>
        </div>
        
        <!-- Graphique par jour -->
        <div class="chart-container">
            <h2><i class="fas fa-chart-area"></i> Visites des 7 Derniers Jours</h2>
            <div style="margin-top: 1.5rem;">
                <?php if (!empty($visits_by_day)): ?>
                    <?php 
                    $max_visits = max(array_column($visits_by_day, 'visits'));
                    foreach ($visits_by_day as $day): 
                        $percentage = $max_visits > 0 ? ($day['visits'] / $max_visits) * 100 : 0;
                    ?>
                    <div style="margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                            <span style="font-weight: 600;"><?php echo date('d/m/Y', strtotime($day['date'])); ?></span>
                            <span style="color: #3B82F6; font-weight: 700;"><?php echo $day['visits']; ?> visites</span>
                        </div>
                        <div style="background: #E5E7EB; border-radius: 4px; overflow: hidden; height: 8px;">
                            <div style="background: linear-gradient(90deg, #3B82F6, #1D4ED8); width: <?php echo $percentage; ?>%; height: 100%;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-data">Aucune donnée disponible</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
