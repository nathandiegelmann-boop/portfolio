<?php
// Nathan Diegelmann - Page Contact avec Tailwind CSS
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
                INSERT INTO contact_messages (sender_name, sender_email, subject, message, sender_ip, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$name, $email, $subject, $message, $_SERVER['REMOTE_ADDR']]);
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
    'title' => 'Contact - Nathan Diegelmann | Développeur Web',
    'description' => 'Contactez Nathan Diegelmann - Développeur web disponible pour projets, collaborations et opportunités professionnelles.',
    'current_page' => 'contact'
];

// Inclure header et navigation
include_header($page_config);
include_nav();
?>

<!-- Hero Section avec Tailwind -->
<section class="relative bg-gradient-to-br from-darker-bg to-dark-bg py-32 px-8 text-center overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <div class="absolute inset-0" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 2px, rgba(0, 255, 65, 0.02) 2px, rgba(0, 255, 65, 0.02) 4px);"></div>
    </div>
    <div class="relative max-w-4xl mx-auto">
        <h1 class="text-5xl md:text-7xl font-mono font-bold text-terminal-green mb-6 animate-pulse">
            &lt;/CONTACT&gt;
        </h1>
        <p class="text-xl text-text-secondary font-mono">
            <span class="bg-code-bg px-4 py-2 rounded border border-border-color">
                $ echo "Parlons de vos projets web !" &gt; contact.txt
            </span>
        </p>
    </div>
</section>

<!-- Main Contact Section -->
<section class="py-16 px-4 bg-dark-bg min-h-screen">
    <div class="max-w-6xl mx-auto grid lg:grid-cols-2 gap-12">
        
        <!-- Contact Info -->
        <div class="bg-code-bg border border-border-color rounded-lg p-8 lg:sticky lg:top-24 h-fit">
            <div class="flex items-center gap-4 pb-6 border-b-2 border-terminal-green mb-8">
                <div class="w-12 h-12 bg-terminal-green text-dark-bg rounded-lg flex items-center justify-center">
                    <i class="fas fa-address-card text-lg"></i>
                </div>
                <h2 class="text-2xl font-mono font-bold text-terminal-green">Informations</h2>
            </div>
            
            <div class="space-y-6 mb-8">
                <a href="mailto:<?php echo $contact_info['email']; ?>" 
                   class="flex items-center gap-4 p-4 bg-darker-bg border border-border-color rounded-lg hover:border-terminal-green hover:bg-terminal-green/10 transition-all duration-300 hover:translate-x-2 group">
                    <div class="w-14 h-14 bg-gradient-to-br from-terminal-green to-code-blue rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <i class="fas fa-envelope text-dark-bg"></i>
                    </div>
                    <div>
                        <h4 class="font-mono text-terminal-green font-semibold">Email</h4>
                        <p class="text-text-secondary text-sm break-all"><?php echo $contact_info['email']; ?></p>
                    </div>
                </a>
                
                <a href="tel:<?php echo str_replace([' ', '.'], '', $contact_info['phone']); ?>" 
                   class="flex items-center gap-4 p-4 bg-darker-bg border border-border-color rounded-lg hover:border-terminal-green hover:bg-terminal-green/10 transition-all duration-300 hover:translate-x-2 group">
                    <div class="w-14 h-14 bg-gradient-to-br from-terminal-green to-code-blue rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <i class="fas fa-phone text-dark-bg"></i>
                    </div>
                    <div>
                        <h4 class="font-mono text-terminal-green font-semibold">Téléphone</h4>
                        <p class="text-text-secondary text-sm"><?php echo $contact_info['phone']; ?></p>
                    </div>
                </a>
                
                <div class="flex items-center gap-4 p-4 bg-darker-bg border border-border-color rounded-lg">
                    <div class="w-14 h-14 bg-gradient-to-br from-terminal-green to-code-blue rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-map-marker-alt text-dark-bg"></i>
                    </div>
                    <div>
                        <h4 class="font-mono text-terminal-green font-semibold">Localisation</h4>
                        <p class="text-text-secondary text-sm"><?php echo $contact_info['location']; ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Status -->
            <div class="bg-terminal-green/10 border border-terminal-green rounded-lg p-6 text-center mb-8">
                <div class="flex items-center justify-center gap-2 font-mono text-terminal-green font-semibold mb-2">
                    <span class="w-2 h-2 bg-terminal-green rounded-full animate-pulse"></span>
                    Disponible pour projets
                </div>
                <p class="text-text-secondary text-sm">Réponse sous 24h en général</p>
            </div>
            
            <!-- Social Links -->
            <div class="grid grid-cols-1 gap-4 mb-8">
                <a href="https://instagram.com/<?php echo ltrim($contact_info['instagram'], '@'); ?>" 
                   target="_blank" 
                   class="flex items-center justify-center gap-2 p-3 bg-darker-bg border border-border-color rounded-lg text-text-primary hover:border-pink-500 hover:bg-pink-500/10 hover:text-pink-400 transition-all duration-300">
                    <i class="fab fa-instagram"></i>
                    <span class="font-mono text-sm">Instagram</span>
                </a>
                <a href="#" target="_blank" 
                   class="flex items-center justify-center gap-2 p-3 bg-darker-bg border border-border-color rounded-lg text-text-primary hover:border-purple-500 hover:bg-purple-500/10 hover:text-purple-400 transition-all duration-300">
                    <i class="fab fa-github"></i>
                    <span class="font-mono text-sm">GitHub</span>
                </a>
                <a href="#" target="_blank" 
                   class="flex items-center justify-center gap-2 p-3 bg-darker-bg border border-border-color rounded-lg text-text-primary hover:border-blue-500 hover:bg-blue-500/10 hover:text-blue-400 transition-all duration-300">
                    <i class="fab fa-linkedin"></i>
                    <span class="font-mono text-sm">LinkedIn</span>
                </a>
            </div>
            
            <!-- Tech Stack -->
            <div class="bg-code-blue/5 border-l-4 border-code-blue rounded-lg p-6">
                <h4 class="text-code-blue font-mono font-semibold mb-4 flex items-center gap-2">
                    <i class="fas fa-code"></i> Stack Technique
                </h4>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-code-bg border border-orange-400 text-orange-400 px-3 py-1 rounded-full text-xs font-mono uppercase">HTML5</span>
                    <span class="bg-code-bg border border-blue-400 text-blue-400 px-3 py-1 rounded-full text-xs font-mono uppercase">CSS3</span>
                    <span class="bg-code-bg border border-purple-400 text-purple-400 px-3 py-1 rounded-full text-xs font-mono uppercase">PHP</span>
                    <span class="bg-code-bg border border-yellow-400 text-yellow-400 px-3 py-1 rounded-full text-xs font-mono uppercase">MySQL</span>
                    <span class="bg-code-bg border border-green-400 text-green-400 px-3 py-1 rounded-full text-xs font-mono uppercase">Responsive</span>
                </div>
            </div>
        </div>
        
        <!-- Contact Form -->
        <div class="bg-code-bg border border-border-color rounded-lg p-8">
            <div class="flex items-center gap-4 pb-6 border-b-2 border-terminal-green mb-8">
                <div class="w-12 h-12 bg-terminal-green text-dark-bg rounded-lg flex items-center justify-center">
                    <i class="fas fa-paper-plane text-lg"></i>
                </div>
                <h2 class="text-2xl font-mono font-bold text-terminal-green">Envoyez un Message</h2>
            </div>
            
            <?php if ($message_sent): ?>
                <div class="bg-green-500/20 border border-green-500 text-green-400 p-4 rounded-lg mb-6 font-mono font-semibold flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>Message envoyé avec succès ! Je vous répondrai rapidement.</span>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="bg-red-500/20 border border-red-500 text-red-400 p-4 rounded-lg mb-6 font-mono font-semibold flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><?php echo htmlspecialchars($error_message); ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <div>
                    <label for="name" class="block font-mono text-text-primary font-semibold text-sm mb-2">
                        <i class="fas fa-user mr-2 text-terminal-green"></i> Nom complet *
                    </label>
                    <input type="text" id="name" name="name" required
                           placeholder="Votre nom et prénom"
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                           class="w-full bg-darker-bg border border-border-color rounded-lg px-4 py-3 text-text-primary font-primary focus:outline-none focus:border-terminal-green focus:ring-2 focus:ring-terminal-green/20 transition-all duration-300 placeholder-text-secondary/50">
                </div>
                
                <div>
                    <label for="email" class="block font-mono text-text-primary font-semibold text-sm mb-2">
                        <i class="fas fa-envelope mr-2 text-terminal-green"></i> Email *
                    </label>
                    <input type="email" id="email" name="email" required
                           placeholder="votre.email@exemple.com"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           class="w-full bg-darker-bg border border-border-color rounded-lg px-4 py-3 text-text-primary font-primary focus:outline-none focus:border-terminal-green focus:ring-2 focus:ring-terminal-green/20 transition-all duration-300 placeholder-text-secondary/50">
                </div>
                
                <div>
                    <label for="subject" class="block font-mono text-text-primary font-semibold text-sm mb-2">
                        <i class="fas fa-tag mr-2 text-terminal-green"></i> Sujet *
                    </label>
                    <input type="text" id="subject" name="subject" required
                           placeholder="Sujet de votre message"
                           value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>"
                           class="w-full bg-darker-bg border border-border-color rounded-lg px-4 py-3 text-text-primary font-primary focus:outline-none focus:border-terminal-green focus:ring-2 focus:ring-terminal-green/20 transition-all duration-300 placeholder-text-secondary/50">
                </div>
                
                <div>
                    <label for="message" class="block font-mono text-text-primary font-semibold text-sm mb-2">
                        <i class="fas fa-comment mr-2 text-terminal-green"></i> Message *
                    </label>
                    <textarea id="message" name="message" required rows="6"
                              placeholder="Décrivez votre projet, vos besoins, vos objectifs..."
                              class="w-full bg-darker-bg border border-border-color rounded-lg px-4 py-3 text-text-primary font-mono leading-relaxed resize-vertical min-h-32 focus:outline-none focus:border-terminal-green focus:ring-2 focus:ring-terminal-green/20 transition-all duration-300 placeholder-text-secondary/50"
                    ><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-terminal-green to-code-blue text-dark-bg font-mono font-bold py-4 px-6 rounded-lg uppercase tracking-wide hover:-translate-y-1 hover:shadow-xl hover:shadow-terminal-green/30 active:translate-y-0 transition-all duration-300 flex items-center justify-center gap-3">
                    <i class="fas fa-paper-plane"></i>
                    <span>Envoyer le Message</span>
                </button>
            </form>
        </div>
    </div>
</section>

<?php
// Inclure le footer
include_footer();
?>