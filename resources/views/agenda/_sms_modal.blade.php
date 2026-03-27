<div class="agenda-sms-flash" data-agenda-sms-flash hidden></div>

<div
    id="agendaSmsModal"
    class="agenda-sms-modal"
    aria-hidden="true"
    aria-modal="true"
    role="dialog"
    aria-labelledby="agenda-sms-title"
>
    <div class="agenda-sms-backdrop" data-close-agenda-sms-modal></div>

    <div class="agenda-sms-dialog" role="document">
        <div class="agenda-sms-shell">
            <div class="agenda-sms-head">
                <div class="agenda-sms-head-copy">
                    <span class="agenda-sms-kicker">SMS patient</span>
                    <h2 id="agenda-sms-title">Envoyer un rappel SMS</h2>
                    <p>Envoyez ou planifiez un rappel sans quitter l agenda.</p>
                </div>

                <button type="button" class="agenda-sms-close" data-close-agenda-sms-modal aria-label="Fermer la popup SMS">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="agenda-sms-alert" data-agenda-sms-errors hidden></div>

            <form method="POST" action="{{ route('sms.store') }}" class="agenda-sms-form" data-agenda-sms-form novalidate>
                @csrf
                <input type="hidden" name="rendezvous_id" value="" data-agenda-sms-rendezvous-id>

                <div class="agenda-sms-summary">
                    <div class="agenda-sms-summary-item">
                        <span>Patient</span>
                        <strong data-agenda-sms-patient>Patient inconnu</strong>
                    </div>
                    <div class="agenda-sms-summary-item">
                        <span>Rendez-vous</span>
                        <strong data-agenda-sms-rendezvous>Rendez-vous non selectionne</strong>
                    </div>
                </div>

                <div class="agenda-sms-grid">
                    <label class="agenda-sms-field">
                        <span>Telephone</span>
                        <input
                            type="tel"
                            name="telephone"
                            placeholder="+212612345678"
                            pattern="^(\+212|0)[0-9]{9}$"
                            inputmode="tel"
                            autocomplete="tel"
                            required
                            data-agenda-sms-phone
                        >
                        <small>Format attendu: +212XXXXXXXXX ou 0XXXXXXXXX.</small>
                    </label>

                    <div class="agenda-sms-preview-card">
                        <span class="agenda-sms-preview-label">Apercu</span>
                        <div class="agenda-sms-preview-phone" data-agenda-sms-preview-phone>Numero non renseigne</div>
                        <div class="agenda-sms-preview-message" data-agenda-sms-preview>Le contenu du SMS apparaitra ici.</div>
                    </div>
                </div>

                <label class="agenda-sms-field agenda-sms-field-full">
                    <span>Message SMS</span>
                    <textarea
                        name="message_template"
                        rows="5"
                        maxlength="160"
                        placeholder="Rédigez un SMS court, clair et exploitable."
                        data-agenda-sms-message
                    ></textarea>
                    <div class="agenda-sms-field-meta">
                        <small>Le flux SMS existant sera utilise a l enregistrement.</small>
                        <strong data-agenda-sms-counter>0/160</strong>
                    </div>
                </label>

                <div class="agenda-sms-actions">
                    <button type="button" class="agenda-sms-btn agenda-sms-btn-muted" data-close-agenda-sms-modal>
                        <i class="fas fa-xmark"></i>
                        Fermer
                    </button>
                    <button type="submit" class="agenda-sms-btn agenda-sms-btn-primary" data-agenda-sms-submit>
                        <i class="fas fa-paper-plane"></i>
                        Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
