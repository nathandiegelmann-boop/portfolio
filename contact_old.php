<?php
// Nathan Diegelmann - Page Contact
require_once __DIR__ . '/includes/init.php';

// Traitement du formulaire
$message_sent = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Adresse email invalide.';
    } elseif (strlen($message) < 10) {
        $error_message = 'Le message doit contenir au moins 10 caractères.';
    } else {
        // Insertion en base de données
        try {
            $stmt = $pdo->prepare("
                INSERT INTO contact_messages (name, email, subject, message, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$name, $email, $subject, $message]);
            $message_sent = true;
        } catch (PDOException $e) {
            $error_message = 'Erreur lors de l\'envoi du message. Veuillez réessayer.';
            error_log("Erreur contact: " . $e->getMessage());
        }
    }
}

// Récupérer infos de contact depuis la config
$contact_info = [
    'email' => $nathan_info['email'],
    'phone' => $nathan_info['telephone'],
    'location' => $nathan_info['ville'],
    'instagram' => $nathan_info['instagram']
];

// Configuration de la page
$page_config = [
    'title' => 'Contact',
    'description' => 'Contactez Nathan Diegelmann - Développeur web disponible pour projets, collaborations et opportunités professionnelles.',
    'current_page' => 'contact',
    'body_class' => 'dark bg-darker-bg min-h-screen'
];

// Inclure header et navigation
include_header($page_config);
include_nav();
?>

    <!-- Hero Contact -->
    <section class="contact-hero">
        <div class="container">
            <h1 class="section-title">
                <span class="glitch" data-text="CONTACT">CONTACT</span>
            </h1>
            <p class="hero-description">
                <span class="inline-code">$ echo "Parlons de vos projets web !" > contact.txt</span>
            </p>
        </div>
    </section>

    <!-- Section Contact Principale -->
    <section class="contact-main">
        <div class="contact-container">
            <!-- Informations de contact -->
            <div class="contact-info">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-address-book"></i>
                    </div>
                    <h2 class="section-title">Mes Coordonnées</h2>
                </div>
                
                <div class="contact-methods">
                    <a href="mailto:<?php echo $contact_info['email']; ?>" class="contact-method">
                        <div class="method-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="method-details">
                            <h4>Email</h4>
                            <p><?php echo $contact_info['email']; ?></p>
                        </div>
                    </a>
                    
                    <a href="tel:<?php echo $contact_info['phone']; ?>" class="contact-method">
                        <div class="method-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="method-details">
                            <h4>Téléphone</h4>
                            <p><?php echo $contact_info['phone']; ?></p>
                        </div>
                    </a>
                    
                    <div class="contact-method">
                        <div class="method-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="method-details">
                            <h4>Localisation</h4>
                            <p><?php echo $contact_info['location']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="availability-status">
                    <div class="status-indicator">
                        <span class="status-dot"></span>
                        Disponible pour projets
                    </div>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0;">
                        Réponse sous 24h en général
                    </p>
                </div>
                
                <div class="social-links">
                    <a href="https://instagram.com/<?php echo ltrim($contact_info['instagram'], '@'); ?>" class="social-link" target="_blank">
                        <i class="fab fa-instagram"></i>
                        Instagram
                    </a>
                    <a href="#" class="social-link" target="_blank">
                        <i class="fab fa-github"></i>
                        GitHub
                    </a>
                    <a href="#" class="social-link" target="_blank">
                        <i class="fab fa-linkedin"></i>
                        LinkedIn
                    </a>
                </div>
                
                <div class="tech-stack-info">
                    <h4><i class="fas fa-code"></i> Stack Technique</h4>
                    <div class="tech-list">
                        <span class="tech-tag">HTML5</span>
                        <span class="tech-tag">CSS3</span>
                        <span class="tech-tag">PHP</span>
                        <span class="tech-tag">MySQL</span>
                        <span class="tech-tag">Responsive</span>
                    </div>
                </div>
            </div>
            
            <!-- Formulaire de contact -->
            <div class="contact-form-container">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <h2 class="section-title">Envoyez un Message</h2>
                </div>
                
                <?php if ($message_sent): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> 
                        Message envoyé avec succès ! Je vous répondrai rapidement.
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="contact-form" id="contactForm">
                    <div class="form-group">
                        <label for="name" class="form-label">
                            <i class="fas fa-user"></i> Nom complet *
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-input" 
                            required 
                            placeholder="Votre nom et prénom"
                            value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email *
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            required 
                            placeholder="votre.email@exemple.com"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="subject" class="form-label">
                            <i class="fas fa-tag"></i> Sujet *
                        </label>
                        <input 
                            type="text" 
                            id="subject" 
                            name="subject" 
                            class="form-input" 
                            required 
                            placeholder="Sujet de votre message"
                            value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="message" class="form-label">
                            <i class="fas fa-comment"></i> Message *
                        </label>
                        <textarea 
                            id="message" 
                            name="message" 
                            class="form-textarea" 
                            required 
                            placeholder="Décrivez votre projet, vos besoins ou vos questions..."
                        ><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="form-submit">
                        <i class="fas fa-paper-plane"></i>
                        Envoyer le message
                    </button>
                </form>
            </div>
        </div>
    </section>

<?php
// Inclure le footer
include_footer();
?>