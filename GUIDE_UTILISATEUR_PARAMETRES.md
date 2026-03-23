# 📘 Guide Utilisateur - Module Paramètres

## 🎯 Vue d'ensemble rapide

Le module **Paramètres** permet aux administrateurs du cabinet de configurer tous les aspects du système SCABINET en un seul endroit.

**Accès**: Menu principal → ⚙️ Paramètres  
**URL directe**: `http://cabinet-medical-laravel.test/parametres`

---

## 📋 Première Utilisation

### Étape 1: Configuration Générale
1. Accédez au module Paramètres
2. Cliquez sur **📋 Général** (déjà sélectionné)
3. Remplissez:
   - **Nom du Cabinet**: Le nom de votre cabinet
   - **Email Principal**: L'adresse email de contact
   - **Téléphone**: Votre numéro de téléphone
   - **Fuseau Horaire**: Africa/Casablanca (pour le Maroc)
   - **Devise**: EUR ou MAD selon votre choix
   - **Langue**: Français
4. Cliquez **💾 Enregistrer**

### Étape 2: Informations du Cabinet
1. Cliquez sur **🏥 Cabinet**
2. Complétez les informations légales
3. Renseignez les **Horaires d'Ouverture** jour par jour
4. Cliquez **💾 Enregistrer**

### Étape 3: Configuration Email
1. Cliquez sur **💬 Communication**
2. Remplissez les paramètres **SMTP**:
   - **Serveur**: smtp.gmail.com (pour Gmail)
   - **Port**: 587
   - **Email**: votre@email.com
   - **Mot de passe**: Mot de passe d'application
3. Cliquez le bouton **🧪 Tester SMTP** pour vérifier
4. Activez les **Notifications Email** (toggle)
5. Cliquez **💾 Enregistrer**

### Étape 4: Paramètres Médicaux
1. Cliquez sur **⚕️ Médical**
2. Listez vos **Services** (Consultation, Diagnostic, etc.)
3. Définissez la **Durée Standard** des consultations (30 min)
4. Cliquez **💾 Enregistrer**

### Étape 5: Sécurité
1. Cliquez sur **🔒 Sécurité**
2. Réglages recommandés:
   - **Durée Session**: 120 minutes
   - **Max Tentatives**: 5
   - **Chiffrer Données**: ✅ Activé
   - **2FA**: À décider selon vos besoins
3. Cliquez **💾 Enregistrer**

### Étape 6: Sauvegardes
1. Cliquez sur **💾 Sauvegardes**
2. Réglez:
   - **Fréquence**: Quotidienne (recommandé)
   - **Heure**: 02:00 (la nuit)
   - **Provider Cloud**: Aucun (ou AWS si abonnement)
3. Cliquez **💾 Enregistrer**

### Étape 7: Intégrations
1. Cliquez sur **🔌 Intégrations**
2. Remplissez vos URLs de réseaux sociaux si applicable
3. Cliquez **💾 Enregistrer**

---

## 🎯 Cas d'Usage Courants

### 🔧 Changer l'Email Principal

```
Général → Email Principal → Nouveau mail → Enregistrer
```

### 📱 Activer les Notifications SMS

```
Communication → Sélectionner Fournisseur (Twilio)
              → Entrer Clé API
              → Toggle SMS Notifications
              → Enregistrer
```

### 🕐 Modifier les Horaires

```
Cabinet → Horaires d'Ouverture → Modifier jour par jour → Enregistrer
```

### 🔐 Renforcer la Sécurité

```
Sécurité → Activer 2FA → Augmenter Session Timeout → Enregistrer
```

### 💼 Ajouter un Service Médical

```
Médical → Services → Ajouter "Chirurgie, Consultation, ..." → Enregistrer
```

---

## 🎨 Éléments d'Interface

### Types d'Inputs

| Type | Exemple | Utilisation |
|------|---------|-------------|
| **Text** | Cabinet Medical | Textes généraux |
| **Email** | contact@cabinet.com | Emails |
| **Tel** | +212 6 XX XX XX | Téléphones |
| **Number** | 30 | Nombres (durée, etc) |
| **Time** | 02:00 | Horaires |
| **Password** | ••••••• | Mots de passe |
| **Select** | [EUR ▼] | Listes prédéfinies |
| **Textarea** | Description... | Textes longs |
| **Toggle** | ⊙—— | Oui/Non |

### Toggles (Interrupteurs)

**Utilisation**:
- Glisser le curseur à droite pour **ACTIVER** ✅
- Glisser le curseur à gauche pour **DÉSACTIVER** ❌
- Apparaît bleu quand activé

**Exemple**:
```
Chiffrer Données Sensibles: ⊙—— (Bleu = Activé)
2FA: ——⊙ (Gris = Désactivé)
```

---

## ✅ Messages de Confirmation

### ✓ Succès
```
✅ Paramètres sauvegardés avec succès!
```
Le message vert s'affiche 5 secondes puis disparaît automatiquement.

### ✕ Erreur
```
❌ Erreur: [description du problème]
```
Le message rouge reste jusqu'à correction.

---

## 🔐 Recommandations de Sécurité

### Mots de Passe
- ✅ Utiliser des mots de passe forts
- ❌ Ne pas partager les accès SMTP
- ✅ Changer régulièrement

### Accès Admin
- ✅ Limiter le nombre d'admins
- ✅ Utiliser 2FA
- ✅ Monitorer les modifications

### Données Sensibles
- ✅ Chiffrement activé
- ✅ Sauvegardes régulières
- ✅ Backup cloud recommandé

---

## 🆘 Dépannage

### Je ne peux pas enregistrer

**Solutions**:
1. Vérifier les erreurs affichées
2. Rafraîchir la page (F5)
3. Vérifier les droits d'accès
4. Contacter le support

### Les emails ne s'envoient pas

**Solutions**:
1. Aller dans **Communication**
2. Cliquer **🧪 Tester SMTP**
3. Vérifier les identifiants SMTP
4. Vérifier la connexion internet
5. Vérifier les logs du serveur

### Les SMS ne s'envoient pas

**Solutions**:
1. Vérifier la clé API SMS
2. Vérifier le solde du compte SMS
3. Vérifier que le numéro est au format correct

### Je ne vois pas mon changement

**Solutions**:
1. Rafraîchir la page (Ctrl+F5)
2. Vider le cache navigateur
3. Vérifier que l'enregistrement s'est bien déroulé
4. Redémarrer l'application

---

## 📊 Informations Utiles

### Fuseau Horaire
- **Maroc**: Africa/Casablanca
- **France**: Europe/Paris
- **GMT**: UTC

### Devises
- **EUR**: Euro (€) - France, Europe
- **MAD**: Dirham marocain (د.م.)
- **USD**: Dollar américain ($)

### Fournisseurs SMS
- **Twilio**: Fiable, bien documenté
- **Nexmo**: Alternative populaire
- **Custom**: Votre propre API

---

## 🚀 Conseils de Pro

### Configuration Optimale

**Paramètres recommandés**:
```
✅ Fuseau Horaire: Africa/Casablanca
✅ Session Timeout: 120 minutes
✅ Chiffrement Données: Activé
✅ Sauvegarde: Quotidienne à 2:00
✅ Notifications Email: Activées
✅ 2FA: Activé pour admins
```

### Performance
- Les changements sont **immédiatement effectifs**
- Le cache est **invalidé automatiquement**
- Pas besoin de redémarrer l'application

### Backup
- **Fréquence recommandée**: Quotidienne
- **Heure recommandée**: 02:00 (période creuse)
- **Rétention**: Garder au moins 10 derniers backups

---

## 📚 Besoin d'Aide?

### Documentation Complète
Voir: `MODULE_PARAMETRES_DOCUMENTATION.md`

### Support
- 📧 Email: support@scabinet.com
- 💬 Chat: [Accès support]
- 📞 Téléphone: +212 6 XX XX XX

### Tutoriels Vidéo
- Configuration initiale (5 min)
- Sécurité avancée (10 min)
- Troubleshooting (15 min)

---

**Version**: 1.0  
**Mise à jour**: 3 février 2026  
**Pour**: Administrateurs du Cabinet

> 💡 **Conseil**: Sauvegardez cette page pour référence ultérieure!
