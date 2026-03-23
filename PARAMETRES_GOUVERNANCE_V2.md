# Centre de Gouvernance Applicative (Paramètres V2)

## Objectif

Transformer le module `Paramètres` actuel (écran monolithique) en **centre de gouvernance applicative** modulaire, auditable et aligné mini-HIS.

---

## 1) Gouvernance & Configuration Globale

### Section cible: Paramètres Généraux Avancés

Paramètres à standardiser:

- Timezone dynamique (système + override utilisateur)
- Format date/heure configurable (`dd/MM/yyyy`, `yyyy-MM-dd`, `24h/12h`)
- Devise principale + multi-devises
- Langue système (i18n-ready)
- Paramètres régionaux (locale, séparateur décimal, format adresse)

### Modèle recommandé

- Conserver la table `settings` pour les paramètres simples
- Introduire une convention de clés:
  - `general.timezone.default`
  - `general.datetime.date_format`
  - `general.datetime.time_format`
  - `general.currency.default`
  - `general.currency.enabled`
  - `general.locale.default`
  - `general.language.default`

---

## 2) RBAC Avancé

### Cible

Remplacer le champ `users.module_permissions` (binaire par module) par un RBAC actionnel:

- Permissions granulaires: `view`, `create`, `edit`, `delete`, `export`
- Permissions par action et par ressource
- Héritage de rôles
- Journalisation des modifications des droits
- Prévisualisation des permissions effectives d’un utilisateur

### Schéma BDD cible

- `roles` (`id`, `name`, `label`, `parent_id`, `is_system`)
- `permissions` (`id`, `resource`, `action`, `code`)  
  Exemple `patients.view`, `factures.export`
- `role_permissions` (`role_id`, `permission_id`)
- `user_roles` (`user_id`, `role_id`)
- `user_permission_overrides` (`user_id`, `permission_id`, `effect`)  
  `effect`: allow/deny
- `permission_audit_logs` (`actor_user_id`, `target_type`, `target_id`, `change_set`, `created_at`)

### Service applicatif

- `App\Services\Security\PermissionResolver`
  - Résout l’héritage de rôle
  - Applique les overrides utilisateur
  - Expose `getEffectivePermissions(User $user)`

---

## 3) Sécurité Applicative

### Section cible: Sécurité

- Politique mot de passe (longueur, complexité, expiration)
- Activation 2FA
- Limitation tentatives connexion
- Timeout session configurable
- Whitelist IP (optionnelle)
- Configuration CORS
- Consultation des logs sécurité

### Paramètres recommandés

- `security.password.min_length`
- `security.password.require_uppercase`
- `security.password.require_numeric`
- `security.password.require_special`
- `security.auth.2fa.enabled`
- `security.auth.max_login_attempts`
- `security.session.timeout_minutes`
- `security.network.ip_whitelist`
- `security.cors.allowed_origins`

### Journalisation sécurité

Table `security_events`:

- `event_type` (login_failed, ip_blocked, 2fa_enabled, permission_denied…)
- `severity` (info/warn/critical)
- `user_id`, `ip_address`, `user_agent`
- `context` (JSON)

---

## 4) Audit & Traçabilité

### Section cible: Audit Log

- Journal des actions utilisateurs (CRUD + actions sensibles)
- Filtres par utilisateur/module/date/type action
- Export CSV
- Politique de rétention configurable

### Schéma BDD cible

Table `audit_logs`:

- `user_id`
- `module`
- `action`
- `target_type`, `target_id`
- `old_values` (JSON)
- `new_values` (JSON)
- `ip_address`
- `created_at`

Paramètre de rétention:

- `audit.retention_days`

Commande planifiée:

- `php artisan audit:prune --days=...`

---

## 5) Notifications & Communication

### Cible

- SMTP avancé (TLS/SSL, port, from, test)
- Templates email dynamiques
- Paramètres API SMS
- Webhooks
- Monitoring queue

### Schéma BDD recommandé

- `notification_templates` (`channel`, `code`, `subject`, `body`, `is_active`)
- `webhook_subscriptions` (`direction`, `event`, `url`, `secret`, `active`)
- `webhook_deliveries` (`subscription_id`, `status`, `attempts`, `payload`, `response`)

---

## 6) Système & Performance

### Section cible: Performance & Cache

- Activation cache config
- Gestion cache applicatif (clear/warmup)
- Monitoring files queue
- Paramètres cron jobs
- Maintenance programmable

### Paramètres recommandés

- `performance.cache.config_enabled`
- `performance.cache.store`
- `performance.queue.alert_failed_threshold`
- `performance.cron.healthcheck_enabled`
- `performance.maintenance.scheduled_at`
- `performance.maintenance.message`

---

## 7) API & Intégrations

### Section cible: API & Intégrations

- Génération de tokens API
- Gestion clés API
- Webhooks entrants/sortants
- Documentation Swagger interne
- Limitation rate limit

### Schéma recommandé

- `api_clients` (`name`, `key`, `secret_hash`, `active`, `last_used_at`)
- `api_tokens` (`client_id`, `token_hash`, `scopes`, `expires_at`, `revoked_at`)
- `api_rate_limits` (`client_id`, `limit_per_minute`, `limit_per_day`)

---

## Architecture Laravel recommandée

## Dossiers

- `app/Domain/Governance/*`
- `app/Domain/Security/*`
- `app/Domain/Audit/*`
- `app/Domain/Integration/*`
- `app/Http/Controllers/Admin/Settings/*`
- `resources/views/parametres/v2/*`

## Routes admin (préfixe)

- `/admin/settings/general`
- `/admin/settings/rbac`
- `/admin/settings/security`
- `/admin/settings/audit`
- `/admin/settings/notifications`
- `/admin/settings/performance`
- `/admin/settings/integrations`

Routes déjà créées dans le projet:

- `admin.settings.index`
- `admin.settings.general`
- `admin.settings.rbac`
- `admin.settings.security`
- `admin.settings.audit`
- `admin.settings.notifications`
- `admin.settings.performance`
- `admin.settings.integrations`

## Services clés

- `SystemSettingService`
- `PermissionResolver`
- `AuditLogger`
- `SecurityEventLogger`
- `WebhookDispatcher`

Services déjà ajoutés:

- `App\\Services\\Governance\\SystemSettingService`
- `App\\Services\\Security\\PermissionResolver`
- `App\\Services\\Audit\\AuditLogger`
- `App\\Services\\Security\\SecurityEventLogger`
- `App\\Services\\Integration\\WebhookDispatcher`

---

## Plan de migration (sans rupture)

### Phase 1 (fondations)

1. Ajouter tables RBAC/audit/security/api
2. Créer services de lecture/écriture settings typés
3. Introduire UI V2 en parallèle (sans supprimer l’existant)

### Phase 2 (cohabitation)

1. Mapper `module_permissions` vers `permissions` actionnelles
2. Alimenter `audit_logs` et `security_events`
3. Activer export CSV et filtres avancés

### Phase 3 (bascule)

1. Basculer middleware vers `PermissionResolver`
2. Déprécier `module_permissions`
3. Nettoyer routes legacy `parametres` monolithiques

---

## Critères d’acceptation V2

- Tous les modules critiques couverts par permissions actionnelles
- Traçabilité complète des changements de droits
- Paramètres sécurité modifiables sans redéploiement
- Audit exportable et rétention automatisée
- API tokens + rate limiting opérationnels

---

## Notes d’alignement avec l’existant

- Le projet contient déjà `settings`, `module.access` et des sections de configuration utiles.
- La stratégie recommandée est **évolutive** (cohabitation V1/V2), pour limiter le risque production.
- Conserver temporairement `ParametresController` et ajouter un namespace `Admin\Settings\*` pour la V2.