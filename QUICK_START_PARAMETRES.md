# 🚀 Quick Start - Module Paramètres

## ⚡ 5 Minutes de Configuration

### Étape 1: Vérifier la Migration
```bash
php artisan migrate --path="database/migrations/2026_02_02_132827_add_settings_columns_to_settings_table.php"
```

### Étape 2: Charger les Paramètres Par Défaut
```bash
php artisan db:seed --class=DefaultSettingsSeeder
# Output: ✅ 44 paramètres par défaut créés/mis à jour
```

### Étape 3: Accéder au Module
```
Navigateur: http://cabinet-medical-laravel.test/parametres
```

### Étape 4: Configurer les Essentiels
1. **Général**: Remplir nom et email du cabinet
2. **Cabinet**: Adresse et SIRET
3. **Communication**: SMTP pour emails
4. **Sécurité**: Activer 2FA si souhaité

### Étape 5: Tester
- Modifier un paramètre
- Cliquer **💾 Enregistrer**
- Voir le message de succès ✅

---

## 📋 Paramètres Recommandés (Production)

```
📋 GÉNÉRAL
├─ Cabinet: Votre Cabinet Médical
├─ Email: contact@votre-domain.com
├─ Téléphone: +212 6 XX XX XX XX
├─ Fuseau: Africa/Casablanca
├─ Devise: EUR
├─ Langue: FR
└─ Format Date: d/m/Y

🏥 CABINET
├─ Adresse: Votre adresse
├─ Ville: Votre ville
├─ Code Postal: Votre CP
├─ SIRET: Votre SIRET
├─ TVA: Votre TVA
└─ Horaires: Selon vos heures

💬 COMMUNICATION
├─ SMTP Host: smtp.votre-domain.com
├─ SMTP Port: 587
├─ Username: votre@email.com
├─ Password: Mot de passe app
├─ SMS Provider: Twilio
├─ SMS API Key: Votre clé
├─ Email Notifications: ✅ ON
└─ SMS Notifications: ✅ ON

⚕️ MÉDICAL
├─ Services: Consultation, Diagnostic, ...
├─ Durée Consultation: 30
├─ Délai Min RDV: 15
└─ Export Dossiers: ✅ ON

🔒 SÉCURITÉ
├─ Session Timeout: 120 min
├─ Max Tentatives: 5
├─ Chiffrement: ✅ ON
├─ 2FA: ✅ ON (recommandé)
└─ Masquer Sensibles: ✅ ON

💾 SAUVEGARDES
├─ Fréquence: Quotidienne
├─ Heure: 02:00
├─ Rétention: 10 backups
└─ Cloud: Aucun (ou AWS S3)

🔌 INTÉGRATIONS
├─ Google Maps: [Si utilisé]
├─ Webhooks: [Si applicable]
└─ Réseaux Sociaux: [URLs]
```

---

## 🧪 Test Rapide SMTP

```
1. Aller à Communication
2. Remplir SMTP config
3. Cliquer [Test SMTP]
4. Vérifier email reçu
5. Confirmer ✅
```

---

## 📊 Statistiques Système

```
Accéder via: GET /parametres/system/stats

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

---

## 🔄 Sauvegarde Configuration

### Exporter Paramètres
```bash
GET /parametres/export
# Télécharge: parametres_2026-02-03_15-30-45.json
```

### Importer (Futur)
```bash
POST /parametres/import
# Body: { settings: { ... } }
```

---

## 🆘 Troubleshooting Rapide

### Erreur: "Column 'key' not found"
```bash
# Solution:
php artisan migrate --path="database/migrations/2026_02_02_132827_add_settings_columns_to_settings_table.php"
```

### Paramètres vides
```bash
# Solution:
php artisan db:seed --class=DefaultSettingsSeeder
```

### Pas de changement visible
```bash
# Solution:
# 1. Vérifier dans la console "Paramètres sauvegardés ✓"
# 2. Rafraîchir: Ctrl+F5 (hard refresh)
# 3. Vérifier les logs: tail -f storage/logs/laravel.log
```

### SMTP ne fonctionne pas
```bash
# Solution:
# 1. Vérifier credentials
# 2. Cliquer [Test SMTP]
# 3. Vérifier email/password
# 4. Vérifier firewall (port 587)
```

---

## 🎯 Cas d'Usage Courants

### Ajouter un Nouveau Service
```
Médical → Services → Ajouter "Chirurgie"
```

### Changer les Horaires
```
Cabinet → Horaires → Modifier jours → Enregistrer
```

### Renforcer Sécurité
```
Sécurité → Activer 2FA → Augmenter Session Timeout
```

### Activer SMS
```
Communication → SMS Provider → Entrer Clé API → Activer
```

---

## 📚 Fichiers Clés

```
app/Http/Controllers/ParametresController.php   (Logique)
resources/views/parametres/index.blade.php      (Interface)
app/Models/Setting.php                          (ORM)
routes/web.php                                  (Routes)
database/seeders/DefaultSettingsSeeder.php      (Données)
```

---

## 📖 Documentation

| Document | Sujet |
|----------|-------|
| MODULE_PARAMETRES_DOCUMENTATION.md | Technique complet |
| GUIDE_UTILISATEUR_PARAMETRES.md | Utilisation simple |
| PARAMETRES_TODO_FINAL.md | Checklist |
| PARAMETRES_LIVRAISON.md | Vue d'ensemble |
| QUICK_START.md | Ce fichier 👈 |

---

## ✅ Checklist Avant Production

- [ ] Migrer la BD
- [ ] Seeder défaut chargé
- [ ] Paramètres généraux configurés
- [ ] SMTP testé
- [ ] Horaires renseignés
- [ ] 2FA considéré
- [ ] Sauvegardes planifiées
- [ ] Accès admin sécurisé
- [ ] Backups en place

---

## 🎓 Commandes Utiles

```bash
# Afficher tous les paramètres
php artisan tinker
>>> App\Models\Setting::all()

# Modifier un paramètre
>>> Setting::set('cabinet_name', 'Mon Cabinet', 'string')

# Récupérer un paramètre
>>> Setting::get('cabinet_name')

# Vider le cache
>>> Cache::forget('all_settings')

# Réinitialiser
>>> Setting::query()->delete()
>>> php artisan db:seed --class=DefaultSettingsSeeder
```

---

## 🚀 Déploiement

### Sur Production
```bash
# 1. Pousser le code
git push origin main

# 2. SSH sur serveur
ssh user@domain.com

# 3. Migrer
php artisan migrate

# 4. Seeder (première fois)
php artisan db:seed --class=DefaultSettingsSeeder

# 5. Tester
curl https://domain.com/parametres
```

---

## 📞 Support Rapide

- **Bug**: Créer une issue GitHub
- **Question**: Consulter la documentation
- **Feature**: Contacter l'équipe dev
- **Emergency**: Appeler support

---

**Temps Installation**: ⏱️ 5 minutes  
**Complexité**: 🟢 Facile  
**Status**: ✅ Prêt à l'emploi  

> 🎉 Vous êtes maintenant prêt à utiliser le module Paramètres!
