# Module Paramètres - Documentation Complète

> ⚠️ Cette documentation décrit principalement la version actuelle (V1) du module.
>
> Pour la refonte professionnelle et modulaire orientée mini-HIS (RBAC avancé, sécurité, audit, performance, API & intégrations), consulter **PARAMETRES_GOUVERNANCE_V2.md**.

## 🎯 Vue d'ensemble

Le module Paramètres est une interface centralisée pour gérer la configuration complète du système SCABINET. Il offre une expérience utilisateur moderne et intuitive avec:

- **7 sections principales** pour une organisation logique
- **Infrastructure responsive** optimisée pour tous les appareils
- **Sauvegarde automatique** des paramètres
- **Validation des données** côté serveur et client
- **Sécurité renforcée** avec support 2FA et chiffrement

## 📋 Architecture

### Structure Base de Données

La table `settings` stocke tous les paramètres:

```sql
CREATE TABLE settings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    cle VARCHAR(255) UNIQUE NOT NULL,
    valeur LONGTEXT,
    type VARCHAR(50) DEFAULT 'string',
    description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Contrôleur Principal

**Fichier**: `app/Http/Controllers/ParametresController.php`

**Méthodes disponibles**:

| Méthode | Route | Description |
|---------|-------|-------------|
| `index()` | GET /parametres | Affiche la page des paramètres |
| `update()` | PUT /parametres | Sauvegarde les paramètres d'une section |
| `export()` | GET /parametres/export | Exporte tous les paramètres en JSON |
| `reset()` | POST /parametres/reset | Réinitialise aux valeurs par défaut |
| `testSmtp()` | POST /parametres/smtp/test | Teste la configuration SMTP |
| `backup()` | POST /parametres/backup | Génère une sauvegarde manuelle |
| `systemStats()` | GET /parametres/system/stats | Retourne les stats système |

## 📑 7 Sections de Paramètres

### 1️⃣ Général (📋 General)

**Paramètres disponibles**:
- Nom du Cabinet
- Email Principal
- Téléphone
- Fuseau Horaire
- Devise
- Langue par Défaut
- Format de Date

**Utilisation**:
```php
$cabinet_name = Setting::get('cabinet_name');
$email = Setting::get('email_principal');
$timezone = Setting::get('timezone');
```

### 2️⃣ Cabinet (🏥 Cabinet)

**Paramètres disponibles**:
- Adresse Complète
- Ville
- Code Postal
- Numéro SIRET
- Numéro TVA
- Horaires d'Ouverture (Lundi à Samedi)

**Cas d'usage**:
- Génération de factures
- Affichage sur documents
- Information publique du cabinet

### 3️⃣ Communication (💬 Communication)

**Paramètres disponibles**:

#### Email (SMTP)
- Serveur SMTP
- Port SMTP
- Utilisateur SMTP
- Mot de Passe SMTP
- **Action**: Tester la connexion

#### SMS
- Fournisseur (Twilio, Nexmo, Personnalisé)
- Clé API SMS

#### Notifications
- Toggle: Activer notifications email
- Toggle: Activer notifications SMS

**Exemple de configuration**:
```php
config([
    'mail.host' => Setting::get('smtp_host'),
    'mail.port' => Setting::get('smtp_port'),
    'mail.username' => Setting::get('smtp_username'),
    'mail.password' => Setting::get('smtp_password'),
]);
```

### 4️⃣ Médical (⚕️ Medical)

**Paramètres disponibles**:
- Services Disponibles (texte libre, virgule-séparé)
- Durée Standard Consultation (minutes)
- Délai Minimum Entre RDV (minutes)
- Autoriser Export Dossiers (toggle)

**Exemple**:
```php
$services = explode(',', Setting::get('services'));
$duration = Setting::get('consultation_duration'); // en minutes
```

### 5️⃣ Sécurité (🔒 Security)

**Paramètres disponibles**:

#### Authentification
- Durée de Session (minutes)
- Tentatives de Connexion Maximal

#### Chiffrement
- Toggle: Chiffrer Données Sensibles
- Toggle: Authentification 2FA
- Toggle: Masquer Données Sensibles

**Implémentation**:
```php
session_set_cookie_params([
    'lifetime' => Setting::get('session_timeout') * 60,
    'secure' => true,
    'httponly' => true,
]);
```

### 6️⃣ Sauvegardes (💾 Backups)

**Paramètres disponibles**:

#### Sauvegarde Automatique
- Fréquence (Quotidienne, Hebdomadaire, Mensuelle)
- Heure de la Sauvegarde

#### Stockage Cloud
- Fournisseur (Aucun, AWS S3, Azure, Google Cloud)
- Nombre de Sauvegardes à Conserver

**Configuration Laravel**:
```php
'backup' => [
    'frequency' => Setting::get('backup_frequency'),
    'time' => Setting::get('backup_time'),
    'cloud' => Setting::get('cloud_provider'),
]
```

### 7️⃣ Intégrations (🔌 Integrations)

**Paramètres disponibles**:

#### API Externes
- Clé API Google Maps

#### Webhooks
- URL Webhook Consultation
- URL Webhook Paiement

#### Réseaux Sociaux
- Lien Facebook
- Lien Twitter

**Utilisation des Webhooks**:
```php
// Trigger webhook après consultation
Http::post(Setting::get('webhook_consultation'), [
    'consultation_id' => $consultation->id,
    'status' => 'completed'
]);
```

## 🎨 Design & UX

### Caractéristiques de l'Interface

**Color Scheme**:
- Primary: #0284c7 (Cyan)
- Secondary: #10b981 (Vert)
- Danger: #ef4444 (Rouge)
- Warning: #f59e0b (Orange)

**Responsive Design**:
- Desktop: Sidebar gauche + Contenu droit (2 colonnes)
- Tablet: Navigation supérieure en grid
- Mobile: Single column, fullwidth

**Interactions**:
- Navigation par onglets sans rechargement
- Validation en temps réel
- Messages de succès/erreur avec animations
- Transitions fluides (0.2s)

### Éléments d'Interface

1. **Toggles Switches** pour les paramètres booléens
2. **Selects** pour les options prédéfinies
3. **Inputs Text/Email/Tel** pour les données simples
4. **Textareas** pour les descriptions longues
5. **Time Inputs** pour les horaires
6. **Buttons** avec animations hover

## 🔐 Sécurité

### Mesures Implémentées

1. **CSRF Protection**: Tokens Laravel standard
2. **Validation**: Côté serveur avec Laravel Validator
3. **Chiffrement**: Support pour données sensibles
4. **2FA**: Toggle pour activer/désactiver
5. **Session Timeout**: Configurable par paramètre
6. **Rate Limiting**: Max tentatives connexion

### Meilleures Pratiques

```php
// ✅ Utiliser les constantes pour les paramètres sensibles
$password = decrypt(Setting::get('smtp_password'));

// ✅ Valider les types de données
Setting::set('session_timeout', 120, 'integer');

// ✅ Ajouter des descriptions pour maintenance
Setting::create([
    'cle' => 'smtp_host',
    'valeur' => 'smtp.gmail.com',
    'description' => 'Serveur SMTP pour emails sortants'
]);
```

## 📊 Statistiques Système

### Données Disponibles

```php
GET /parametres/system/stats

Retourne:
{
    "users": 15,
    "patients": 150,
    "consultations": 250,
    "documents": 450,
    "disk_usage": "2.5 GB",
    "database_size": "150 MB"
}
```

### Utilitaires

```php
// Calcul taille disque
$size = $controller->getDiskUsage();

// Calcul taille BD
$size = $controller->getDatabaseSize();

// Formatage bytes
$formatted = $controller->formatBytes(1024000);
```

## 🚀 Fonctionnalités Avancées

### Test SMTP

```bash
# API pour tester la configuration
POST /parametres/smtp/test

# Envoie un email de test à l'adresse du cabinet
# Retour: { success: true, message: "Email envoyé" }
```

### Export/Import

```bash
# Exporter tous les paramètres
GET /parametres/export
# Retour: JSON avec tous les paramètres

# Importer depuis JSON
POST /parametres/import
# Body: { settings: { ... } }
```

### Réinitialisation

```bash
# Réinitialiser aux valeurs par défaut
POST /parametres/reset

# Attention: Supprime tous les paramètres personnalisés
```

## 📱 Support Mobile

- Sidebar se transforme en grid responsive
- Navigation tactile optimisée
- Formulaires full-width sur mobile
- Texte redimensionné (24px headers)

## ✅ Checklist de Configuration

Avant la mise en production:

- [ ] Configurer le Fuseau Horaire correct
- [ ] Renseigner Email Principal du Cabinet
- [ ] Paramétrer SMTP pour les emails
- [ ] Configurer SMS si nécessaire
- [ ] Définir les Horaires d'Ouverture
- [ ] Ajouter SIRET/TVA
- [ ] Configurer 2FA
- [ ] Tester envoi email
- [ ] Planifier les sauvegardes
- [ ] Ajouter URLs webhooks si applicable

## 🔄 Workflow Typique

1. **Administrateur** accède à `/parametres`
2. **Navigation** dans les 7 sections
3. **Modification** des paramètres
4. **Validation** des données
5. **Sauvegarde** dans la BD
6. **Cache** invalidé automatiquement
7. **Confirmation** affichée à l'utilisateur
8. **Effectivité** immédiate du changement

## 🐛 Troubleshooting

### Erreur "Paramètres non sauvegardés"
- Vérifier les droits d'accès
- Contrôler les logs Laravel
- Valider les données soumises

### Emails non envoyés
- Tester SMTP via interface
- Vérifier les credentials
- Contrôler firewall/ports

### Performance lente
- Réduire le nombre de requêtes
- Utiliser le cache (Laravel Cache)
- Optimiser les requêtes DB

## 📚 Références

- [Fichier Contrôleur](../app/Http/Controllers/ParametresController.php)
- [Vue Template](../resources/views/parametres/index.blade.php)
- [Modèle Setting](../app/Models/Setting.php)
- [Routes](../routes/web.php)

---

**Version**: 1.0  
**Mise à jour**: 3 février 2026  
**Auteur**: SCABINET Dev Team
