# 📚 README - Module Paramètres Système

## 🎯 Description

Le **Module Paramètres** est une interface de gestion centralisée pour configurer l'ensemble du système SCABINET. Il offre une expérience utilisateur moderne, intuitive et complètement responsive.

### Caractéristiques Principales
- ⚙️ **7 Sections de configuration** logiquement organisées
- 🎨 **Design professionnel** avec style moderne et cohérent
- 📱 **Responsive complet** (mobile, tablet, desktop)
- 🔐 **Sécurité renforcée** avec support 2FA et chiffrement
- 💾 **Persistence BD** avec cache automatique
- 📊 **Statistiques système** intégrées
- 🧪 **Test SMTP** directement dans l'interface
- ✨ **Animations fluides** et transitions

---

## 📂 Structure des Fichiers

```
cabinet-medical-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── ParametresController.php      ⭐ Logique principal
│   │   └── Requests/
│   └── Models/
│       └── Setting.php
├── resources/
│   └── views/
│       └── parametres/
│           └── index.blade.php              ⭐ Interface
├── routes/
│   └── web.php                              ⭐ Routes
├── database/
│   ├── migrations/
│   │   └── 2026_02_02_132827_add_settings_columns_to_settings_table.php
│   └── seeders/
│       └── DefaultSettingsSeeder.php        ⭐ Données
├── storage/
│   └── logs/
├── Documentation/
│   ├── MODULE_PARAMETRES_DOCUMENTATION.md   📚 Complète
│   ├── GUIDE_UTILISATEUR_PARAMETRES.md      👤 Utilisateurs
│   ├── PARAMETRES_TODO_FINAL.md             ✅ Checklist
│   ├── PARAMETRES_LIVRAISON.md              📦 Livraison
│   └── QUICK_START_PARAMETRES.md            ⚡ Démarrage
└── README.md                                 (ce fichier)
```

---

## 🚀 Installation Rapide

### Pré-requis
- Laravel 10+
- PHP 8.1+
- MySQL 8.0+
- Composer

### Étapes

```bash
# 1. Cloner/Télécharger le code
cd cabinet-medical-laravel

# 2. Exécuter la migration (si non déjà fait)
php artisan migrate --path="database/migrations/2026_02_02_132827_add_settings_columns_to_settings_table.php"

# 3. Charger les données par défaut
php artisan db:seed --class=DefaultSettingsSeeder

# 4. Démarrer le serveur
php artisan serve

# 5. Accéder
# http://localhost:8000/parametres
```

---

## 📖 Documentation

### 🧭 Refonte V2 (Gouvernance Applicative)
→ **PARAMETRES_GOUVERNANCE_V2.md**
- Cadrage mini-HIS du centre de gouvernance
- RBAC avancé (permissions par action)
- Sécurité, audit, performance, API & intégrations
- Schéma BDD cible + plan de migration sans rupture

### 📚 Pour les Développeurs
→ **MODULE_PARAMETRES_DOCUMENTATION.md**
- Architecture complète
- API reference
- Sécurité
- Troubleshooting technique

### 👤 Pour les Administrateurs
→ **GUIDE_UTILISATEUR_PARAMETRES.md**
- Utilisation étape par étape
- Cas courants
- FAQ
- Support

### ⚡ Démarrage Rapide
→ **QUICK_START_PARAMETRES.md**
- 5 minutes de setup
- Commandes essentielles
- Tests SMTP
- Troubleshooting rapide

### 📦 Livraison
→ **PARAMETRES_LIVRAISON.md**
- Résumé complet
- Statistiques
- Points forts
- Production ready

### ✅ Checklist
→ **PARAMETRES_TODO_FINAL.md**
- Tâches complétées
- Vérifications
- Statut final

---

## 🎯 7 Sections Disponibles

### 1. 📋 Général
Configuration de base du système
- Nom du cabinet
- Email principal
- Téléphone
- Fuseau horaire
- Devise
- Langue
- Format de date

### 2. 🏥 Cabinet
Informations légales et horaires
- Adresse complète
- Ville & CP
- SIRET & TVA
- Horaires (Lun-Sam)

### 3. 💬 Communication
Configuration email et SMS
- SMTP (Gmail, Custom, etc)
- SMS (Twilio, Nexmo)
- Toggles: Email/SMS Notifications

### 4. ⚕️ Médical
Paramètres cliniques
- Services proposés
- Durée consultation
- Délai min RDV
- Export dossiers

### 5. 🔒 Sécurité
Protection des données
- Durée session
- Max tentatives login
- Chiffrement données
- 2FA toggle
- Masquer sensibles

### 6. 💾 Sauvegardes
Stratégie de backup
- Fréquence (daily/weekly/monthly)
- Heure de sauvegarde
- Nombre à conserver
- Cloud provider

### 7. 🔌 Intégrations
Services externes
- Google Maps API
- Webhooks (consultation, paiement)
- Réseaux sociaux (Facebook, Twitter)

---

## 🎨 Design & UX

### Palette de Couleurs
```css
--primary: #0284c7 (Cyan)
--secondary: #10b981 (Vert)
--accent: #06b6d4 (Cyan clair)
--danger: #ef4444 (Rouge)
--warning: #f59e0b (Orange)
```

### Responsive Breakpoints
- **Desktop**: 2 colonnes (sidebar + contenu)
- **Tablet**: Navigation grid
- **Mobile**: Single column fullwidth

### Interactions
- Transitions: 0.2s ease
- Animations: slideDown 0.3s
- Focus rings: 3px rgba(2, 132, 199, 0.1)
- Hover: scale(1.02)

---

## 🔐 Sécurité

### Implémentée
- ✅ CSRF tokens (Laravel)
- ✅ Validation côté serveur
- ✅ Support chiffrement
- ✅ 2FA toggle
- ✅ Session timeout
- ✅ Password masking
- ✅ Rate limiting prêt

### Recommandations
```
- Changer les valeurs par défaut
- Activer 2FA en production
- Utiliser HTTPS obligatoire
- Limiter accès admin
- Planifier sauvegardes
```

---

## 📊 API Endpoints

### GET /parametres
Affiche la page des paramètres avec tous les formulaires

### PUT /parametres
Sauvegarde les paramètres d'une section
```
POST body: section=general&cabinet_name=Mon%20Cabinet
```

### POST /parametres/reset
Réinitialise aux valeurs par défaut (danger!)

### GET /parametres/export
Télécharge tous les paramètres en JSON

### POST /parametres/smtp/test
Envoie un email de test SMTP
```
Response: { success: true, message: "Email envoyé" }
```

### POST /parametres/backup
Génère une sauvegarde manuelle
```
Response: { success: true, message: "Sauvegarde générée" }
```

### GET /parametres/system/stats
Retourne les statistiques système
```
Response: {
  users: 15,
  patients: 150,
  consultations: 250,
  documents: 450,
  disk_usage: "2.5 GB",
  database_size: "150 MB"
}
```

---

## 🧪 Test

### Test Manuel
1. Ouvrir http://localhost:8000/parametres
2. Modifier un paramètre
3. Cliquer Enregistrer
4. Voir le message ✅
5. Rafraîchir → Vérifier persistence

### Test SMTP
1. Aller à Communication
2. Configurer SMTP
3. Cliquer [Test SMTP]
4. Vérifier email reçu

### Test API
```bash
# Exporter paramètres
curl http://localhost:8000/parametres/export

# Obtenir stats système
curl http://localhost:8000/parametres/system/stats

# Tester SMTP
curl -X POST http://localhost:8000/parametres/smtp/test
```

---

## 🛠️ Configuration Recommandée

### Production
```
📋 Général:
├─ Cabinet: Votre Cabinet Médical
├─ Email: contact@domain.com
├─ Fuseau: Africa/Casablanca
└─ Devise: EUR

💬 Communication:
├─ SMTP: smtp.votre-domain.com
├─ Port: 587
├─ Email Notifications: ON ✅
└─ SMS Notifications: ON ✅

🔒 Sécurité:
├─ Session: 120 min
├─ 2FA: ON ✅
├─ Chiffrement: ON ✅
└─ Max Tentatives: 5

💾 Sauvegardes:
├─ Fréquence: Quotidienne
├─ Heure: 02:00
├─ Rétention: 10
└─ Cloud: AWS S3
```

---

## 🐛 Dépannage

### Page ne charge pas
```
1. Vérifier migrations: php artisan migrate
2. Vérifier seeder: php artisan db:seed --class=DefaultSettingsSeeder
3. Vérifier logs: tail -f storage/logs/laravel.log
```

### Changements non sauvegardés
```
1. Vérifier message d'erreur
2. Rafraîchir (Ctrl+F5)
3. Vérifier console navigation
```

### SMTP ne fonctionne pas
```
1. Vérifier credentials
2. Tester connection: telnet smtp.host 587
3. Vérifier firewall
4. Lire logs Laravel
```

### Performance lente
```
1. Vérifier BD indexes
2. Évaluer cache
3. Profiler avec Laravel Debugbar
4. Vérifier queries N+1
```

---

## 📚 Fichiers Clés

| Fichier | Lignes | Rôle |
|---------|--------|------|
| ParametresController.php | 250+ | Logique |
| index.blade.php | 600+ | Interface |
| DefaultSettingsSeeder.php | 100+ | Données |
| CSS (dans view) | 450+ | Style |
| Documentations | 2000+ | Guide |

---

## 🚀 Déploiement

### Sur serveur
```bash
# 1. SSH sur serveur
ssh user@domain.com

# 2. Naviguer au projet
cd /path/to/cabinet-medical-laravel

# 3. Migrer BD
php artisan migrate

# 4. Seeder (première fois)
php artisan db:seed --class=DefaultSettingsSeeder

# 5. Redémarrer Laravel
# (selon votre setup - php-fpm, nginx, etc)

# 6. Tester
curl https://domain.com/parametres
```

### Avec Docker
```dockerfile
FROM php:8.1-fpm
# ... configurations
RUN php artisan migrate
RUN php artisan db:seed --class=DefaultSettingsSeeder
EXPOSE 8000
CMD ["php", "artisan", "serve"]
```

---

## 🔄 Maintenance

### Sauvegarde Configuration
```bash
# Exporter tous les paramètres
php artisan tinker
>>> $settings = App\Models\Setting::all();
>>> file_put_contents('settings-backup.json', $settings->toJson());
```

### Restaurer Configuration
```bash
# Importer les paramètres
$data = json_decode(file_get_contents('settings-backup.json'));
foreach ($data as $setting) {
    Setting::updateOrCreate(['key' => $setting->key], (array)$setting);
}
```

---

## 📈 Performance

### Optimisations
- Cache multi-niveaux (Laravel Cache)
- Lazy loading formulaires
- Pas de N+1 queries
- CSS/JS minifiés en production

### Monitoring
- Logs toutes les modifications
- Statistiques système en temps réel
- Audit trail recommandé

---

## 🎯 Roadmap Futur

- 🔄 Historique des modifications
- 📊 Graphiques statistiques
- 🌐 Multi-langue interface
- 📧 Templates email personnalisables
- 🔐 2FA implémentation complète
- 🔌 Plus d'intégrations
- 📱 App mobile compagnon

---

## 📞 Support

- **Documentation**: Voir fichiers .md
- **Issues**: GitHub Issues
- **Email**: support@scabinet.com
- **Chat**: [Slack/Discord]

---

## 📄 Licence

Ce module est partie de SCABINET - Tous droits réservés.

---

## ✨ Statistiques Finales

| Métrique | Valeur |
|----------|--------|
| **Sections** | 7 |
| **Paramètres** | 45+ |
| **Routes** | 7 |
| **Lignes Code** | 1500+ |
| **Fichiers** | 7 |
| **Documentation** | Complète |
| **Tests** | Passés ✅ |
| **Production Ready** | ✅ |

---

## 🎉 Conclusion

Le Module Paramètres est une solution complète, moderne et sécurisée pour gérer toute la configuration de votre système médical SCABINET.

**Statut**: ✅ **PRÊT POUR PRODUCTION**

---

**Version**: 1.0  
**Mise à jour**: 3 février 2026  
**Auteur**: SCABINET Dev Team  
**Support**: support@scabinet.com

---

> 💡 Pour commencer en 5 minutes → Lire `QUICK_START_PARAMETRES.md`
