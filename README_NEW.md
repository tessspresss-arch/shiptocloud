# 🏥 Cabinet Médical - Application Web Complète

![Laravel 10](https://img.shields.io/badge/Laravel-10.x-red.svg)
![PHP 8.1+](https://img.shields.io/badge/PHP-8.1+-blue.svg)
![MySQL 8.0+](https://img.shields.io/badge/MySQL-8.0+-orange.svg)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-CSS-blue.svg)

Une solution complète et professionnelle de gestion pour cabinets médicaux, développée avec Laravel 10.

## ✨ Caractéristiques Principales

### 📊 Tableau de Bord
- Statistiques clés en temps réel
- Rendez-vous du jour
- Alertes et notifications
- Graphiques financiers interactifs

### 👥 Gestion des Patients
- Fiches patients complètes
- Historique médical et consultations
- Documents médicaux liés
- Gestion des allergies et antécédents

### 📅 Calendrier & Rendez-Vous
- Agenda médical moderne et intuitif
- Vue jour/semaine/mois
- Gestion des statuts (confirmé, annulé, en attente)
- Synchronisation automatique

### 🔬 Bilans & Examens Complémentaires
- Demande et suivi des examens
- Biologie, imagerie, endoscopie
- Gestion des résultats
- Historique complet

### 💊 Prescriptions & Ordonnances
- Création rapide de prescriptions
- Modèles d'ordonnances personnalisables
- Génération PDF automatique
- Liaison patient-médecin-médicament

### 📜 Certificats Médicaux
- Arrêts de travail
- Justificatifs médicaux
- Dispenses d'activité
- Modèles éditable
- Export PDF

### 💰 Facturation & Dépenses
- Gestion complète des factures
- Suivi des dépenses par catégorie
- Rapports financiers mensuels/annuels
- Statistiques de revenus

### 📱 Rappels SMS
- Envoi automatique de SMS rappels
- Intégration Twilio/AWS SNS
- Paramétrage flexible (24h, 48h avant)
- Historique complet

### 📋 Gestion des Contacts
- Patients, laboratoires, fournisseurs
- Base de données centralisée
- Favoris et statut actif/inactif
- Export Excel

### 📚 Gestion des Documents
- Upload et classement des documents
- Sécurité d'accès par patient
- Support PDF, images, documents Office
- Archivage automatique

### 📈 Statistiques & Rapports
- Statistiques médicales détaillées
- Rapports financiers
- Graphiques exportables
- Tableaux de bord personnalisés

### ⚙️ Paramètres & Configuration
- Gestion centralisée des paramètres
- Gestion des utilisateurs et rôles
- Configuration SMS
- Modèles de documents

---

## 🚀 Installation Rapide

### Prérequis
- PHP 8.1 ou supérieur
- Composer
- MySQL 8.0+
- Node.js 16+
- Git

### Étapes

```bash
# 1. Cloner le repository
git clone <repository-url>
cd cabinet-medical-laravel

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances Node
npm install

# 4. Copier le fichier .env
cp .env.example .env

# 5. Générer la clé d'application
php artisan key:generate

# 6. Configurer la base de données dans .env
# Puis exécuter les migrations
php artisan migrate

# 7. Compiler les assets
npm run build

# 8. Lancer l'application
php artisan serve
```

Accédez à `http://localhost:8000`

---

## 📁 Structure du Projet

```
cabinet-medical-laravel/
├── app/
│   ├── Http/Controllers/        # Contrôleurs (CRUD, logique)
│   ├── Models/                  # Modèles Eloquent
│   ├── Services/                # Services métier
│   ├── Exports/                 # Exports Excel
│   └── Console/Commands/        # Commandes CLI
├── database/
│   ├── migrations/              # Schéma de BD
│   └── seeders/                 # Données d'exemple
├── resources/
│   ├── views/                   # Templates Blade
│   └── css/                     # Styles (Tailwind)
├── routes/
│   └── web.php                  # Routes web
├── storage/                     # Fichiers uploadés
├── config/                      # Configuration
├── public/                      # Fichiers publics
└── tests/                       # Tests automatisés
```

---

## 🔧 Configuration

### Variables d'Environnement Clés

```env
APP_NAME="Cabinet Médical"
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_DATABASE=cabinet_medical
DB_USERNAME=root
DB_PASSWORD=

# SMS (optionnel)
SMS_ENABLED=true
SMS_PROVIDER=twilio
TWILIO_ACCOUNT_SID=your_sid
TWILIO_AUTH_TOKEN=your_token
TWILIO_FROM_NUMBER=+1234567890
```

### Configurer SMS (Twilio)

1. Créer un compte [Twilio](https://www.twilio.com)
2. Copier Account SID et Auth Token
3. Ajouter les variables d'environnement
4. Activer SMS: `SMS_ENABLED=true`

---

## 📚 Modules Développés

| Module | Status | Description |
|--------|--------|-------------|
| 🏥 Patients | ✅ | Gestion complète des dossiers patients |
| 📅 Rendez-Vous | ✅ | Calendrier et gestion des RDV |
| 💊 Prescriptions | ✅ | Création et gestion des prescriptions |
| 📜 Ordonnances | ✅ | Génération PDF d'ordonnances |
| 🏥 Certificats | ✅ | Certificats médicaux avec PDF |
| 🔬 Examens | ✅ | Bilans et résultats d'examens |
| 💰 Facturation | ✅ | Factures et suivi des paiements |
| 📊 Dépenses | ✅ | Suivi des dépenses du cabinet |
| 📱 SMS Reminders | ✅ | Rappels SMS automatiques |
| 📋 Contacts | ✅ | Gestion centralisée des contacts |
| 📈 Statistiques | ✅ | Rapports et graphiques |
| ⚙️ Paramètres | ✅ | Configuration complète |

---

## 📖 Documentation

### Documents Clés
- **[IMPLEMENTATION_PLAN.md](IMPLEMENTATION_PLAN.md)** - Plan d'implémentation détaillé
- **[DEVELOPMENT_GUIDE.md](DEVELOPMENT_GUIDE.md)** - Guide de développement
- **[AGENDA_UPDATE_SUMMARY.md](AGENDA_UPDATE_SUMMARY.md)** - Mises à jour de l'agenda

### Commandes Utiles

```bash
# Migrations
php artisan migrate                 # Appliquer les migrations
php artisan migrate:rollback        # Annuler les migrations
php artisan migrate:refresh         # Réinitialiser BD

# Cache
php artisan cache:clear            # Vider le cache
php artisan config:cache           # Cacher la config
php artisan route:cache            # Cacher les routes

# SMS
php artisan sms:send-reminders     # Envoyer rappels SMS

# Développement
php artisan tinker                 # Console interactive
php artisan test                   # Exécuter tests

# Production
php artisan optimize               # Optimisation prod
php artisan down                   # Maintenance
php artisan up                     # Restaurer
```

---

## 🔐 Sécurité

### Fonctionnalités de Sécurité

✅ **Authentification** - Laravel Sanctum pour sessions sécurisées  
✅ **Autorisation** - RBAC (Rôles et permissions) basé sur les rôles  
✅ **Chiffrement** - Données sensibles chiffrées  
✅ **CSRF Protection** - Protection contre les attaques CSRF  
✅ **Validation** - Validations côté serveur strictes  
✅ **SQL Injection** - Prévention via Eloquent ORM  
✅ **Audit Logs** - Historique des accès aux données médicales  
✅ **GDPR/LGPD** - Conformité avec les régulations  

### Checklist de Sécurité

- [ ] `APP_DEBUG=false` en production
- [ ] Authentification multi-facteur activée
- [ ] Certificat SSL/TLS installé
- [ ] Backups réguliers configurés
- [ ] Logs de sécurité activés
- [ ] Rate limiting activé
- [ ] Permissions des fichiers correctes

---

## 🧪 Tests

```bash
# Exécuter tous les tests
php artisan test

# Tests spécifiques
php artisan test tests/Feature/DepenseTest.php

# Avec coverage
php artisan test --coverage
```

---

## 🚀 Déploiement

### Déploiement sur Serveur

```bash
# 1. Cloner le code
git clone <repo> /var/www/cabinet-medical
cd /var/www/cabinet-medical

# 2. Installer les dépendances
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 3. Configurer l'environnement
cp .env.production .env

# 4. Préparer la BD
php artisan migrate --force

# 5. Permissions
chmod -R 755 storage bootstrap/cache

# 6. Optimiser
php artisan optimize
```

### Déploiement continu (CI/CD)

Utiliser GitHub Actions ou GitLab CI pour:
- Linter le code
- Exécuter les tests
- Déployer automatiquement
- Faire les backups

---

## 📊 Statistiques du Projet

- **Modèles Eloquent**: 19
- **Contrôleurs**: 17
- **Migrations**: 34
- **Routes**: 100+
- **Services**: 3
- **Tests**: À écrire
- **Lignes de Code**: 5000+

---

## 🤝 Support & Contribution

### Signaler un Bug

Créer une issue GitHub avec:
- Description du problème
- Étapes pour reproduire
- Résultat attendu/obtenu
- Environment (OS, PHP, MySQL)

### Contribuer

1. Fork le repository
2. Créer une branch (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add feature'`)
4. Push à la branch (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

---

## 📞 Contact & Support

- **Email**: support@cabinet-medical.fr
- **Issues**: [GitHub Issues](https://github.com/tess-press/cabinet-medical-laravel/issues)
- **Documentation**: Voir [DEVELOPMENT_GUIDE.md](DEVELOPMENT_GUIDE.md)

---

## 📝 License

Ce projet est sous license MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

## ✅ Checklist de Démarrage

- [ ] Cloner le repository
- [ ] Installer les dépendances (Composer + NPM)
- [ ] Créer fichier `.env` et générer clé
- [ ] Configurer base de données MySQL
- [ ] Exécuter migrations: `php artisan migrate`
- [ ] Compiler assets: `npm run build`
- [ ] Lancer serveur: `php artisan serve`
- [ ] Accéder à `http://localhost:8000`
- [ ] Créer compte administrateur
- [ ] Configurer paramètres du cabinet
- [ ] Configurer SMS (optionnel)
- [ ] Inviter utilisateurs

---

## 🎉 Merci!

Merci d'utiliser Cabinet Médical! Pour toute question ou suggestion, n'hésitez pas à nous contacter.

**Version**: 1.0.0  
**Dernière mise à jour**: 2 Février 2026  
**Développé avec ❤️ pour les cabinets médicaux**
