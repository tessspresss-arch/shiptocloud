<div class="rdv-card rdv-slot-picker">
    <div class="rdv-slot-head">
        <button type="button" class="rdv-icon-btn" data-month-nav="-1" aria-label="Mois précédent">
            <i class="fas fa-chevron-left"></i>
        </button>
        <h3>{{ $selectedDateObj->translatedFormat('F Y') }}</h3>
        <button type="button" class="rdv-icon-btn" data-month-nav="1" aria-label="Mois suivant">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>

    <div class="rdv-calendar-grid">
        @foreach(['Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa', 'Di'] as $day)
            <span class="rdv-weekday">{{ $day }}</span>
        @endforeach

        @for($dateCursor = $calendarStart->copy(); $dateCursor->lte($calendarEnd); $dateCursor->addDay())
            @php
                $isCurrentMonth = $dateCursor->month === $selectedDateObj->month;
                $isSelected = $dateCursor->isSameDay($selectedDateObj);
                $isPastDate = $dateCursor->lt(now()->startOfDay());
                $dateStr = $dateCursor->format('Y-m-d');
            @endphp
            <button
                type="button"
                class="rdv-day-btn {{ $isCurrentMonth ? '' : 'outside' }} {{ $isSelected ? 'selected' : '' }}"
                data-day="{{ $dateStr }}"
                {{ $isPastDate ? 'disabled' : '' }}
            >
                {{ $dateCursor->day }}
            </button>
        @endfor
    </div>

    <div class="rdv-slot-tools">
        <div class="rdv-period-filter" role="group" aria-label="Filtrer les créneaux">
            <button type="button" class="rdv-chip-filter is-active" data-slot-filter="all">Tous</button>
            <button type="button" class="rdv-chip-filter" data-slot-filter="morning">Matin</button>
            <button type="button" class="rdv-chip-filter" data-slot-filter="afternoon">Après-midi</button>
        </div>
        <input type="search" class="rdv-input" id="slotSearch" placeholder="Rechercher une heure..." autocomplete="off">
    </div>

    <div class="rdv-slot-grid" id="slotGrid">
        @foreach($heures as $heure)
            @php
                $hour = (int) substr($heure, 0, 2);
                $period = $hour < 12 ? 'morning' : 'afternoon';
                $isDisabledSlot = in_array($heure, $blockedSlots, true);
            @endphp
            <button
                type="button"
                class="rdv-slot-btn {{ $heure === $selectedTime ? 'selected' : '' }} {{ $isDisabledSlot ? 'disabled' : 'available' }}"
                data-slot-time="{{ $heure }}"
                data-period="{{ $period }}"
                {{ $isDisabledSlot ? 'disabled' : '' }}
            >
                {{ $heure }}
            </button>
        @endforeach
    </div>

    @error('date')
        <div class="rdv-field-error">{{ $message }}</div>
    @enderror
    @error('heure_debut')
        <div class="rdv-field-error">{{ $message }}</div>
    @enderror
</div>
