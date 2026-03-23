# 🚀 GUIDE DE DÉMARRAGE RAPIDE

**Cabinet Médical - Application Web**  
**Version**: 1.0  
**Date**: 2 Février 2026

---

## ⚡ 5 Minutes pour Démarrer

### 1️⃣ Installation de l'Application

```bash
# Aller au dossier du projet
cd c:\laragon\www\cabinet-medical-laravel

# Installer les dépendances PHP
composer install

# Installer les dépendances Node.js
npm install
```

### 2️⃣ Configuration Initiale

```bash
# Copier le fichier d'environnement
copy .env.example .env

# Générer la clé d'application
php artisan key:generate

# Compiler les assets
npm run build
```

### 3️⃣ Base de Données

```bash
# Créer une nouvelle base de données
# Dans MySQL: CREATE DATABASE cabinet_medical CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Éditer .env
# DB_DATABASE=cabinet_medical
# DB_USERNAME=root
# DB_PASSWORD=

# Exécuter les migrations
php artisan migrate

# (Optionnel) Charger les données de test
php artisan db:seed
```

### 4️⃣ Lancer l'Application

```bash
# Terminal 1 - Serveur Laravel
php artisan serve

# Terminal 2 - Compilation des assets (optionnel, pour développement)
npm run dev

# Accédez à http://localhost:8000
```

### 5️⃣ Première Connexion

```
Email: admin@cabinet.local
Password: password

(À changer après la première connexion)
```

---

## 📚 Structure Rapide

### Modules Principaux
```
✅ Gestion des Patients        /patients
✅ Rendez-Vous & Agenda        /rendezvous, /agenda
✅ Consultations               /consultations
✅ Prescriptions               /prescriptions (existant)
✅ Ordonnances                 /ordonnances (existant)
✅ Examens & Bilans            /examens (NOUVEAU)
✅ Certificats Médicaux        /certificats (NOUVEAU)
✅ Gestion Dépenses            /depenses (NOUVEAU)
✅ Gestion Contacts            /contacts (NOUVEAU)
✅ Rappels SMS                 /sms (NOUVEAU)
✅ Factures                    /factures
✅ Statistiques                /statistiques
✅ Paramètres                  /parametres
```

---

## 🔧 Configuration SMS (Optionnel)

### Avec Twilio

1. Créer un compte [Twilio.com](https://www.twilio.com)
2. Copier les identifiants:
   - Account SID
   - Auth Token
   - Phone Number

3. Ajouter à `.env`:
```env
SMS_ENABLED=true
SMS_PROVIDER=twilio
TWILIO_ACCOUNT_SID=your_sid
TWILIO_AUTH_TOKEN=your_token
TWILIO_FROM_NUMBER=+1234567890
```

4. Tester l'envoi:
```bash
php artisan tinker
>>> \App\Services\SMSService::send('+33612345678', 'Test message');
```

### Envoyer les Rappels SMS

```bash
# Envoyer tous les rappels en attente
php artisan sms:send-reminders

# À automatiser dans crontab:
# * * * * * cd /var/www/cabinet-medical && php artisan schedule:run
```

---

## 📖 Documentation Complète

### Fichiers Clés
| Fichier | Description |
|---------|-------------|
| **IMPLEMENTATION_PLAN.md** | Plan global du projet (111h estimées) |
| **DEVELOPMENT_GUIDE.md** | Guide technique détaillé (30 KB) |
| **README_NEW.md** | Documentation complète (15 KB) |
| **FILES_INVENTORY.md** | Inventaire de tous les fichiers créés |
| **Ce fichier** | Guide de démarrage rapide |

### Architecture
- **Backend**: Laravel 10 (PHP 8.1+)
- **Frontend**: Blade + Tailwind CSS + Alpine.js
- **BD**: MySQL 8.0+
- **Assets**: Webpack via Vite

---

## 🧪 Tests Rapides

### Tester les Contrôleurs

```php
php artisan tinker

// Créer une catégorie de dépense
$cat = App\Models\CategorieDepense::create([
    'nom' => 'Fournitures',
    'couleur' => '#FF5733'
]);

// Créer une dépense
$dep = App\Models\Depense::create([
    'categorie_id' => $cat->id,
    'description' => 'Test',
    'montant' => 100,
    'date_depense' => now(),
    'methode_paiement' => 'carte',
    'created_by' => 1
]);

// Vérifier
App\Models\Depense::all();
```

### Tester les Services

```php
php artisan tinker

// Dashboard
$stats = \App\Services\DashboardService::getStatistics();
dd($stats);

// Rapports
$report = \App\Services\ReportService::monthlyRevenueReport(2, 2026);
dd($report);

// SMS (test)
\App\Services\SMSService::send('+33612345678', 'Test SMS');
```

---

## 🔍 Commandes Utiles

### Développement

```bash
# Cache et optimisation
php artisan optimize                # Optimiser l'app
php artisan route:cache            # Cacher les routes
php artisan config:cache           # Cacher la config
php artisan cache:clear            # Vider le cache

# Base de données
php artisan migrate                # Appliquer migrations
php artisan migrate:rollback       # Annuler migrations
php artisan migrate:refresh        # Reset DB
php artisan db:seed                # Charger données test

# Autres
php artisan tinker                 # Console interactive
php artisan test                   # Exécuter tests
```

### Production

```bash
# Pré-déploiement
npm run build                      # Compiler assets
php artisan optimize               # Optimisation

# Post-déploiement
php artisan migrate --force        # Migrations forcées
chmod -R 755 storage               # Permissions
php artisan config:cache           # Cache config
```

---

## 🐛 Dépannage Rapide

### Problème: Port 8000 déjà utilisé
```bash
php artisan serve --port=8001
```

### Problème: Permissions sur storage
```bash
chmod -R 775 storage bootstrap/cache
```

### Problème: Base de données non trouvée
```bash
# 1. Vérifier .env
# 2. Créer BD: CREATE DATABASE cabinet_medical;
# 3. Relancer: php artisan migrate
```

### Problème: Assets non compilés
```bash
npm install
npm run build
# ou pour développement:
npm run dev
```

### Problème: Clé non générée
```bash
php artisan key:generate
```

---

## 📱 Fonctionnalités Clés à Tester

### ✅ À Vérifier Après Installation

```
□ Authentification fonctionnelle
□ CRUD Patients complet
□ CRUD Rendez-vous complet
□ CRUD Dépenses (NOUVEAU)
□ CRUD Contacts (NOUVEAU)
□ CRUD Examens (NOUVEAU)
□ CRUD Certificats (NOUVEAU)
□ Export Excel fonctionne
□ Génération PDF fonctionne
□ Dashboard affiche les stats
□ Paramètres modifiables
□ SMS configurés (si activé)
```

---

## 🎯 Prochaines Étapes Recommandées

### Semaine 1: Interface
- [ ] Créer vues Blade pour tous les modules
- [ ] Implémenter composants Tailwind
- [ ] Tester navigation

### Semaine 2: Fonctionnalités
- [ ] Implémenter calculs statistiques
- [ ] Tester exports Excel
- [ ] Tester générations PDF

### Semaine 3: Tests & Qualité
- [ ] Écrire tests unitaires
- [ ] Écrire tests fonctionnels
- [ ] Tester sécurité

### Semaine 4: Déploiement
- [ ] Configurer serveur production
- [ ] Mettre en place SSL
- [ ] Configurer backups automatiques
- [ ] Déployer

---

## 📞 Ressources Utiles

### Documentation Officielle
- [Laravel 10](https://laravel.com/docs/10.x) - Framework
- [Eloquent](https://laravel.com/docs/10.x/eloquent) - ORM
- [Blade](https://laravel.com/docs/10.x/blade) - Template

### Stack Technique
- [Tailwind CSS](https://tailwindcss.com) - Styling
- [Alpine.js](https://alpinejs.dev) - Interactivité
- [Maatwebsite](https://docs.laravel-excel.com) - Excel

### Services Externes
- [Twilio SMS](https://www.twilio.com) - SMS
- [AWS SNS](https://aws.amazon.com/sns) - SMS alternative
- [DOMPDF](https://dompdf.github.io) - PDF

---

## 🆘 Support

### Où Trouver de l'Aide

1. **Documentation Locale**
   - Consulter DEVELOPMENT_GUIDE.md
   - Lire IMPLEMENTATION_PLAN.md
   - Vérifier FILES_INVENTORY.md

2. **Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Tinker Console**
   ```bash
   php artisan tinker
   ```

4. **Diagnostics**
   ```bash
   php artisan migrate --step     # Voir les migrations
   php artisan route:list         # Voir toutes les routes
   php artisan model:show User    # Info sur un modèle
   ```

---

## ✨ C'est Prêt!

L'application est maintenant **prête à être utilisée et développée**.

Tous les modules sont en place:
- ✅ Structures de base (migrations, modèles, contrôleurs)
- ✅ Services métier
- ✅ Routes API/web
- ✅ Exports et rapports
- ✅ Documentation complète

**Il ne reste qu'à implémenter les vues (interfaces utilisateur).**

---

## 📋 Checklist de Démarrage

```
Démarrage Initial
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
□ Cloner/Accéder au projet
□ Installer Composer (composer install)
□ Installer NPM (npm install)
□ Copier .env.example → .env
□ Générer clé (php artisan key:generate)
□ Créer base de données
□ Configurer .env (DB credentials)
□ Migrer (php artisan migrate)
□ Compiler assets (npm run build)
□ Lancer serveur (php artisan serve)
□ Accéder à http://localhost:8000
□ Vérifier connexion

Configuration SMS (Optionnel)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
□ Créer compte Twilio
□ Copier credentialsentials
□ Ajouter à .env
□ Tester avec tinker
□ Configurer cron job

Tester Modules
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
□ Créer dépenses
□ Créer contacts
□ Créer examens
□ Créer certificats
□ Tester exports
□ Tester statistiques

Production
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
□ Configurer serveur (Nginx/Apache)
□ Installer SSL/TLS
□ Configurer BD production
□ Mettre en place backups
□ Tester SMS en production
□ Activer monitoring
□ Configurer logging

✅ READY TO GO!
```

---

**Bonne Développement! 🎉**

Pour toute question, consulter les documentations complètes incluses.
