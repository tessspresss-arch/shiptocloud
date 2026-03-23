# 📚 Documentation Développement - Cabinet Médical

**Dernière mise à jour**: 2 Février 2026  
**Version**: 1.0.0

---

## 📋 Table des Matières

1. [Architecture](#architecture)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Modules](#modules)
5. [Services](#services)
6. [Migrations](#migrations)
7. [API & Routes](#api--routes)
8. [Tests](#tests)
9. [Déploiement](#déploiement)
10. [Dépannage](#dépannage)

---

## Architecture

### Structure du Projet

```
app/
├── Http/
│   ├── Controllers/          # Contrôleurs (CRUD, logique métier)
│   ├── Middleware/           # Middlewares d'authentification
│   └── Requests/             # Form Requests (validations)
├── Models/                    # Modèles Eloquent
├── Services/                  # Services métier
├── Exports/                   # Exports Excel (Maatwebsite)
└── Console/
    └── Commands/              # Commandes Artisan personnalisées

database/
├── migrations/                # Migrations de schéma
└── seeders/                   # Seeders pour données de test

resources/
├── views/                     # Templates Blade
│   ├── depenses/
│   ├── contacts/
│   ├── examens/
│   └── certificats/
└── css/                       # Styles (Tailwind)

routes/
└── web.php                    # Routes web
```

### Stack Technique

- **Laravel 10.x** - Framework web PHP moderne
- **PHP 8.1+** - Langage de programmation
- **MySQL 8.0+** - Base de données relationnelle
- **Tailwind CSS 3.x** - Framework CSS utilitaire
- **Alpine.js** - Framework JS réactif léger
- **DOMPDF** - Génération PDF
- **Maatwebsite Excel** - Export Excel
- **Twilio/AWS SNS** - Service SMS (optionnel)

---

## Installation

### Prérequis

- PHP 8.1 ou supérieur
- Composer
- MySQL 8.0+
- Node.js 16+
- Git

### Étapes d'Installation

1. **Cloner le repository**
   ```bash
   git clone <repository-url>
   cd cabinet-medical-laravel
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

3. **Installer les dépendances Node.js**
   ```bash
   npm install
   npm run build
   ```

4. **Créer le fichier `.env`**
   ```bash
   cp .env.example .env
   ```

5. **Générer la clé d'application**
   ```bash
   php artisan key:generate
   ```

6. **Configurer la base de données** (voir section Configuration)

7. **Exécuter les migrations**
   ```bash
   php artisan migrate
   ```

8. **Seeder les données** (optionnel)
   ```bash
   php artisan db:seed
   ```

9. **Compiler les assets**
   ```bash
   npm run dev     # Développement
   npm run build   # Production
   ```

10. **Lancer l'application**
    ```bash
    php artisan serve
    ```

L'application est accessible à `http://localhost:8000`

---

## Configuration

### Variables d'Environnement

Éditer le fichier `.env` :

```env
# Application
APP_NAME="Cabinet Médical"
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cabinet_medical
DB_USERNAME=root
DB_PASSWORD=

# SMS Configuration
SMS_ENABLED=true
SMS_PROVIDER=twilio  # ou aws-sns
SMS_DEFAULT_HOURS=24

# Twilio (si SMS_PROVIDER=twilio)
TWILIO_ACCOUNT_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_FROM_NUMBER=+1234567890

# AWS SNS (si SMS_PROVIDER=aws-sns)
AWS_SMS_KEY=your_key
AWS_SMS_SECRET=your_secret
AWS_SMS_REGION=eu-west-1
```

### Configuration SMS

#### Option 1: Twilio

1. Créer un compte sur [Twilio.com](https://www.twilio.com)
2. Copier Account SID et Auth Token
3. Obtenir un numéro de téléphone (FROM_NUMBER)
4. Ajouter les variables d'environnement

#### Option 2: AWS SNS

1. Créer un compte AWS
2. Configurer IAM credentials
3. Activer SNS service
4. Ajouter les variables d'environnement

### Configuration des Paramètres

Accès: `/parametres`

Les paramètres du cabinet sont stockés dans la table `settings`:

- Nom du cabinet
- Adresse et coordonnées
- Logo et identifiants (SIREN, SIRET, APE)
- Configuration SMS
- Paramètres des modèles

---

## Modules

### 1. **Gestion des Dépenses**

#### Contrôleur
- `DepenseController` - CRUD complet + statistiques

#### Modèles
- `Depense` - Enregistrement de dépense
- `CategorieDepense` - Classification des dépenses

#### Routes
```
GET  /depenses                    # Liste
GET  /depenses/create             # Formulaire création
POST /depenses                    # Stocker
GET  /depenses/{id}               # Détail
GET  /depenses/{id}/edit          # Formulaire édition
PUT  /depenses/{id}               # Mettre à jour
DELETE /depenses/{id}             # Supprimer
GET  /depenses/export             # Export Excel
GET  /depenses/statistiques       # Statistiques mensuelles
```

#### Utilisation
```php
// Créer une dépense
$depense = Depense::create([
    'categorie_id' => 1,
    'description' => 'Achat fournitures médicales',
    'montant' => 150.00,
    'date_depense' => now(),
    'methode_paiement' => 'carte',
    'created_by' => auth()->id(),
]);

// Filtrer par catégorie et période
$depenses = Depense::byCategorie(1)
    ->betweenDates($debut, $fin)
    ->get();

// Statistiques
$total = Depense::byMois(now()->month, now()->year)->sum('montant');
```

---

### 2. **Gestion des Contacts**

#### Contrôleur
- `ContactController` - CRUD + toggles (favoris/actif)

#### Modèle
- `Contact` - Contacts (patients, labs, fournisseurs, etc.)

#### Types de Contacts
- `patient` - Patients
- `laboratoire` - Laboratoires d'analyse
- `fournisseur` - Fournisseurs médicaux
- `hopital` - Hôpitaux partenaires
- `assurance` - Assurances
- `autre` - Autres

#### Routes
```
GET  /contacts                       # Liste
POST /contacts/{id}/toggle-favorite  # Basculer favori
POST /contacts/{id}/toggle-active    # Basculer statut
GET  /contacts/export                # Export Excel
```

---

### 3. **Bilans Complémentaires (Examens)**

#### Contrôleur
- `ExamenController` - Gestion examens + résultats

#### Modèles
- `Examen` - Demande d'examen
- `ResultatExamen` - Résultats des examens

#### Types d'Examens
- `biologie` - Analyses biologiques
- `imagerie` - Radiologie/IRM/Scanner
- `endoscopie` - Endoscopies
- `autre` - Autres examens

#### Statuts
- `demande` - Demande initiée
- `en_attente` - En attente de réalisation
- `termine` - Résultats reçus
- `annule` - Annulé

#### Routes
```
GET  /examens                          # Liste
POST /examens/{examen}/resultats       # Ajouter résultat
DELETE /resultats-examens/{resultat}   # Supprimer résultat
GET  /examens/export                   # Export Excel
```

#### Utilisation
```php
// Créer un examen
$examen = Examen::create([
    'patient_id' => 1,
    'medecin_id' => 1,
    'nom_examen' => 'Prise de sang complète',
    'type' => 'biologie',
    'date_demande' => now(),
    'lieu_realisation' => 'Laboratoire X'
]);

// Ajouter résultats
$examen->resultats()->create([
    'parametre' => 'Glucose',
    'valeur' => '95',
    'unite' => 'mg/dL',
    'valeur_normale' => '70-100',
    'interpretation' => 'normal'
]);

// Récupérer examens en attente
$enAttente = Examen::enAttente()->get();
```

---

### 4. **Certificats Médicaux**

#### Contrôleur
- `CertificatMedicalController` - CRUD + PDF generation

#### Modèles
- `CertificatMedical` - Certificats émis
- `ModeleCertificat` - Templates de certificats

#### Types de Certificats
- `Arrêt de travail`
- `Justificatif`
- `Incapacité`
- `Dispense d'activité physique`
- `Autre`

#### Fonctionnalités
- Génération PDF automatique
- Modèles personnalisables
- Statut transmission (transmis/non transmis)
- Archivage

#### Routes
```
GET  /certificats/{id}/pdf              # Télécharger PDF
POST /certificats/{id}/transmis         # Marquer comme transmis
GET  /certificats/export                # Export Excel
```

#### Utilisation
```php
// Créer un certificat
$cert = CertificatMedical::create([
    'patient_id' => 1,
    'medecin_id' => 1,
    'type' => 'Arrêt de travail',
    'date_emission' => now(),
    'date_debut' => now(),
    'date_fin' => now()->addDays(5),
    'motif' => 'Bronchite aiguë',
    'nombre_jours' => 5
]);

// PDF généré automatiquement
// Télécharger: GET /certificats/{id}/pdf
```

---

### 5. **Rappels SMS**

#### Contrôleur
- `SMSReminderController` - Gestion reminders

#### Modèles
- `SMSReminder` - Rappels planifiés
- `SMSLog` - Historique d'envois

#### Statuts
- `planifie` - En attente d'envoi
- `envoye` - Envoyé avec succès
- `echec` - Erreur lors de l'envoi
- `desactive` - Désactivé manuellement

#### Services
- `SMSService::send()` - Envoyer SMS via provider
- `SMSService::sendReminder()` - Envoyer rappel
- `SMSService::processPending()` - Traiter rappels en attente

#### Routes
```
GET  /sms/logs                           # Historique SMS
GET  /sms/create/{rendezvous}            # Créer rappel
POST /sms/store                          # Stocker rappel
POST /sms/{reminder}/cancel              # Annuler rappel
POST /sms/test                           # Envoyer SMS test
```

#### Commande Artisan
```bash
# Envoyer tous les rappels en attente
php artisan sms:send-reminders
```

#### Utilisation
```php
// Créer un rappel
SMSReminder::create([
    'rendezvous_id' => 1,
    'patient_id' => 1,
    'telephone' => '+33612345678',
    'heures_avant' => 24, // 24h avant le RDV
    'statut' => 'planifie',
    'date_envoi_prevue' => $rendezvous->date_rdv->subHours(24)
]);

// Envoyer manuellement
SMSService::send('+33612345678', 'Message...');

// Traiter tous les en attente
$result = SMSService::processPending();
// $result = ['successful' => 5, 'failed' => 1, 'total' => 6]
```

---

## Services

### DashboardService

Récupère les statistiques pour le dashboard.

```php
use App\Services\DashboardService;

// Statistiques clés
$stats = DashboardService::getStatistics();
// Retourne: patients_total, patients_nouveaux_mois, rdv_aujourd_hui, etc.

// RDV du jour
$today = DashboardService::getRDVToday();

// RDV prochains 7 jours
$upcoming = DashboardService::getUpcomingRDV(7);

// Patients récents
$recent = DashboardService::getRecentPatients(5);

// Résumé financier
$financial = DashboardService::getFinancialSummary($month, $year);

// Graphique revenus mensuels
$chartData = DashboardService::getMonthlyRevenueChart($year);

// Alertes
$alerts = DashboardService::getAlerts();
```

### ReportService

Génère les rapports.

```php
use App\Services\ReportService;

// Rapport revenus mensuels
$revenue = ReportService::monthlyRevenueReport($month, $year);

// Rapport dépenses mensuelles
$expenses = ReportService::monthlyExpensesReport($month, $year);

// Statistiques patients
$patients = ReportService::patientStatisticsReport($month, $year);

// Résumé annuel
$annual = ReportService::annualSummaryReport($year);
```

### SMSService

Gère l'envoi de SMS.

```php
use App\Services\SMSService;

// Envoyer SMS simple
SMSService::send('+33612345678', 'Message', 'reminder', $patientId);

// Envoyer rappel de RDV
SMSService::sendReminder($smsReminder);

// Traiter tous les rappels en attente
$result = SMSService::processPending();
```

---

## Migrations

### Nouvelles Migrations Créées

```
2026_02_02_150000_create_categories_depenses_table
2026_02_02_150100_create_depenses_table
2026_02_02_150200_create_examens_table
2026_02_02_150300_create_resultats_examens_table
2026_02_02_150400_create_contacts_table
2026_02_02_150500_create_sms_reminders_table
2026_02_02_150600_create_sms_logs_table
2026_02_02_150700_create_certificats_medicaux_table
2026_02_02_150800_create_modele_ordonnances_table
2026_02_02_150900_create_modele_certificats_table
```

### Exécuter les Migrations

```bash
# Exécuter toutes les migrations
php artisan migrate

# Exécuter une migration spécifique
php artisan migrate --path=database/migrations/2026_02_02_150000_create_categories_depenses_table.php

# Annuler les dernières migrations
php artisan migrate:rollback

# Annuler et réexécuter
php artisan migrate:refresh
```

---

## API & Routes

### Groupes de Routes

#### Dépenses
```
GET    /depenses
GET    /depenses/create
POST   /depenses
GET    /depenses/{depense}
GET    /depenses/{depense}/edit
PUT    /depenses/{depense}
DELETE /depenses/{depense}
GET    /depenses/export
GET    /depenses/statistiques
```

#### Contacts
```
GET    /contacts
GET    /contacts/create
POST   /contacts
GET    /contacts/{contact}
GET    /contacts/{contact}/edit
PUT    /contacts/{contact}
DELETE /contacts/{contact}
POST   /contacts/{contact}/toggle-favorite
POST   /contacts/{contact}/toggle-active
GET    /contacts/export
```

#### Examens
```
GET    /examens
GET    /examens/create
POST   /examens
GET    /examens/{examen}
GET    /examens/{examen}/edit
PUT    /examens/{examen}
DELETE /examens/{examen}
POST   /examens/{examen}/resultats
DELETE /resultats-examens/{resultat}
GET    /examens/export
```

#### Certificats
```
GET    /certificats
GET    /certificats/create
POST   /certificats
GET    /certificats/{certificat}
GET    /certificats/{certificat}/edit
PUT    /certificats/{certificat}
DELETE /certificats/{certificat}
GET    /certificats/{certificat}/pdf
POST   /certificats/{certificat}/transmis
GET    /certificats/export
```

#### SMS
```
GET    /sms/logs
GET    /sms/create/{rendezvous}
POST   /sms/store
POST   /sms/{reminder}/cancel
POST   /sms/test
```

---

## Tests

### Exécuter les Tests

```bash
# Tous les tests
php artisan test

# Un fichier spécifique
php artisan test tests/Feature/DepenseTest.php

# Avec coverage
php artisan test --coverage
```

### Structure des Tests

```
tests/
├── Feature/          # Tests d'intégration
├── Unit/            # Tests unitaires
└── CreatesApplication.php
```

### Exemple de Test

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Depense;
use App\Models\CategorieDepense;

class DepenseTest extends TestCase
{
    public function test_peut_creer_depense()
    {
        $categorie = CategorieDepense::factory()->create();

        $response = $this->post('/depenses', [
            'categorie_id' => $categorie->id,
            'description' => 'Test',
            'montant' => 100,
            'date_depense' => now()->toDateString(),
            'methode_paiement' => 'carte',
        ]);

        $this->assertDatabaseHas('depenses', [
            'description' => 'Test',
        ]);
    }
}
```

---

## Déploiement

### Checklist Pré-Production

- [ ] Compiler les assets: `npm run build`
- [ ] Optimiser pour production: `php artisan optimize`
- [ ] Configurer `.env` pour production
- [ ] Activer debug = false
- [ ] Configurer SMS service (Twilio/AWS)
- [ ] Sauvegarder la base de données
- [ ] Tester toutes les fonctionnalités

### Déploiement sur Serveur

1. **Cloner le code**
   ```bash
   git clone <repo-url> /var/www/cabinet-medical
   cd /var/www/cabinet-medical
   ```

2. **Installer les dépendances**
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   ```

3. **Configurer l'environnement**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrer la base de données**
   ```bash
   php artisan migrate --force
   ```

5. **Configurer les permissions**
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

6. **Configurer le web server (Nginx)**
   ```nginx
   server {
       listen 80;
       server_name domain.com;
       root /var/www/cabinet-medical/public;

       index index.php;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           fastcgi_pass 127.0.0.1:9000;
           fastcgi_index index.php;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }
   }
   ```

7. **Mettre en place des backups automatiques**
   ```bash
   # Ajouter au crontab
   0 3 * * * mysqldump -u user -p database > /backups/db-$(date +\%Y\%m\%d).sql
   ```

---

## Dépannage

### Problème: Migrations non appliquées

**Solution:**
```bash
php artisan migrate --refresh
# ou
php artisan migrate:reset && php artisan migrate
```

### Problème: SMS non envoyés

**Vérifier:**
```bash
# 1. Vérifier la configuration
cat .env | grep SMS_

# 2. Vérifier les logs
tail -f storage/logs/laravel.log

# 3. Vérifier la table sms_logs
SELECT * FROM sms_logs WHERE statut = 'echec';

# 4. Tester le service
php artisan tinker
>>> \App\Services\SMSService::send('+33612345678', 'Test message');
```

### Problème: PDF non généré

**Vérifier:**
```bash
# 1. Permissions de stockage
chmod -R 775 storage/

# 2. Vérifier DOMPDF
php artisan tinker
>>> Pdf::loadView('test')->save('test.pdf');
```

### Problème: Performance lente

**Optimisation:**
```bash
# Cacher les routes
php artisan route:cache

# Cacher la configuration
php artisan config:cache

# Cacher les vues
php artisan view:cache

# Optimizer class loading
composer install --optimize-autoloader
```

---

## Support & Contact

Pour toute question ou problème:

1. **Documentation**: Voir les fichiers README.md
2. **Issues**: Ouvrir une issue sur GitHub
3. **Support**: Contacter l'équipe de développement

---

**Dernière mise à jour**: 2 Février 2026  
**Développé avec ❤️ pour les cabinets médicaux**
