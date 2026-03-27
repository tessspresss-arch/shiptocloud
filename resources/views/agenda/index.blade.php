@extends('layouts.app')

@section('title', 'Agenda médical - Cabinet médical')

@section('content')
<style>
    :root {
        --ag-ink: #0f172a;
        --ag-muted: #64748b;
        --ag-primary: #0b7ac7;
        --ag-primary-2: #0ea5e9;
        --ag-success: #0f9f74;
        --ag-danger: #e11d48;
        --ag-warning: #d97706;
        --ag-bg: #eef5fb;
        --ag-card: #ffffff;
        --ag-border: #d8e6f5;
    }

    .agenda-shell {
        background:
            radial-gradient(circle at 10% -10%, #d6ecff 0%, transparent 38%),
            radial-gradient(circle at 100% 0%, #d9f0ff 0%, transparent 32%),
            var(--ag-bg);
        border: 1px solid #d9e7f6;
        border-radius: 22px;
        padding: clamp(10px, 1.5vw, 18px);
    }

    .agenda-grid {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 14px;
    }

    .agenda-side {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .ag-card {
        background: var(--ag-card);
        border: 1px solid var(--ag-border);
        border-radius: 16px;
        box-shadow: 0 18px 26px -32px rgba(20, 70, 120, 0.65);
        overflow: hidden;
    }

    .mini-cal-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .mini-cal-head h3 {
        margin: 0;
        color: #102a4e;
        font-size: 1rem;
        font-weight: 800;
        letter-spacing: .3px;
    }

    .mini-cal-body {
        padding: 14px;
    }

    .mini-nav {
        display: flex;
        gap: 6px;
    }

    .mini-nav button {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        border: 1px solid #d0e0f3;
        background: #f5faff;
        color: #285381;
        cursor: pointer;
        transition: .2s ease;
    }

    .mini-nav button:hover {
        background: linear-gradient(135deg, #0b7ac7, #0ea5e9);
        color: #fff;
        border-color: transparent;
    }

    .mini-weekdays,
    .mini-days {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 4px;
    }

    .mini-weekdays div {
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        color: #6b7d94;
        padding: 4px 0;
        text-transform: uppercase;
    }

    .mini-day {
        border-radius: 8px;
        min-height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        background: #f7fbff;
        border: 1px solid transparent;
        cursor: pointer;
        position: relative;
        transition: .2s ease;
    }

    .mini-day:hover {
        border-color: #85b7ea;
        background: #eef6ff;
    }

    .mini-day.today {
        background: linear-gradient(135deg, #0b7ac7, #0ea5e9);
        color: #fff;
    }

    .mini-day.has-event::after {
        content: '';
        width: 5px;
        height: 5px;
        border-radius: 999px;
        background: #f59e0b;
        position: absolute;
        bottom: 3px;
    }

    .mini-day.pad {
        background: transparent;
        cursor: default;
        border-color: transparent;
    }

    .agenda-metrics {
        padding: 14px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }

    .metric {
        border: 1px solid #d7e7f8;
        background: #f8fcff;
        border-radius: 10px;
        padding: 10px;
    }

    .metric-label {
        color: #627892;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .metric-value {
        color: #133864;
        font-size: 1.18rem;
        font-weight: 800;
        margin-top: 4px;
    }

    .upcoming {
        padding: 14px;
    }

    .upcoming h4 {
        margin: 0 0 10px;
        color: #12345f;
        font-size: 1rem;
        font-weight: 800;
    }

    .up-item {
        border: 1px solid #e0ebf8;
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 8px;
        background: #fbfdff;
        border-left: 4px solid #0b7ac7;
    }

    .up-item:last-child {
        margin-bottom: 0;
    }

    .up-item.confirmed { border-left-color: var(--ag-success); }
    .up-item.urgent { border-left-color: var(--ag-danger); }

    .up-time {
        color: #0b7ac7;
        font-weight: 800;
        font-size: 12px;
    }

    .up-patient {
        color: #0f172a;
        font-weight: 700;
        margin: 2px 0;
    }

    .up-doc {
        color: #64748b;
        font-size: 12px;
    }

    .agenda-main {
        background: #fff;
        border: 1px solid var(--ag-border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 18px 26px -32px rgba(20, 70, 120, 0.65);
    }

    .agenda-top {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        background: linear-gradient(120deg, #0b74ba 0%, #0f8fd1 48%, #1a9ce1 100%);
        color: #fff;
        padding: 18px 18px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        border-bottom: 1px solid rgba(255,255,255,.18);
    }

    .agenda-top::before,
    .agenda-top::after {
        content: '';
        position: absolute;
        pointer-events: none;
        z-index: -1;
    }

    .agenda-top::before {
        width: 320px;
        height: 320px;
        top: -180px;
        left: -120px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,.22) 0%, transparent 70%);
    }

    .agenda-top::after {
        width: 380px;
        height: 380px;
        right: -180px;
        bottom: -240px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,.16) 0%, transparent 72%);
    }

    .agenda-title {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1 1 280px;
        min-width: 240px;
    }

    .agenda-title i {
        width: 48px;
        height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        font-size: 1.4rem;
        background: rgba(255,255,255,.18);
        border: 1px solid rgba(255,255,255,.26);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.24);
    }

    .agenda-title h1 {
        margin: 0;
        font-size: 2.05rem;
        font-weight: 900;
        line-height: 1;
        letter-spacing: .2px;
    }

    .agenda-title p {
        margin: 4px 0 0;
        opacity: .95;
        font-size: 1rem;
        color: rgba(242, 248, 255, .94);
    }

    .date-controls {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 1 1 420px;
        gap: 8px;
        background: rgba(255, 255, 255, .15);
        border: 1px solid rgba(255,255,255,.25);
        border-radius: 14px;
        padding: 7px 9px;
        min-height: 48px;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.24);
        backdrop-filter: blur(2px);
    }

    .date-controls button,
    .btn-today,
    .view-btn,
    .btn-new-rdv {
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: .2s ease;
    }

    .date-controls > button:not(.btn-today) {
        width: 44px;
        height: 44px;
        background: rgba(255,255,255,.24);
        color: #fff;
        font-weight: 900;
        border: 1px solid rgba(255,255,255,.15);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.28);
    }

    .date-label {
        min-width: 230px;
        text-align: center;
        font-weight: 900;
        font-size: 1rem;
        white-space: nowrap;
        line-height: 1;
        padding: 9px 12px;
        border-radius: 11px;
        background: rgba(255,255,255,.1);
    }

    .btn-today {
        background: rgba(255,255,255,.18);
        color: #fff;
        font-weight: 800;
        padding: 8px 13px;
        width: auto;
        min-width: 108px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        border: 1px solid rgba(255,255,255,.2);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.24);
        white-space: nowrap;
    }

    .btn-today:hover,
    .date-controls > button:not(.btn-today):hover {
        background: rgba(255,255,255,.34);
        transform: translateY(-1px);
    }

    .view-tools {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-left: auto;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .view-toggle {
        display: inline-flex;
        border: 1px solid rgba(255,255,255,.33);
        border-radius: 12px;
        background: rgba(255,255,255,.12);
        padding: 4px;
        gap: 4px;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.18);
    }

    .view-btn {
        background: transparent;
        color: rgba(255,255,255,.88);
        padding: 8px 14px;
        font-weight: 800;
        font-size: 13px;
        white-space: nowrap;
        line-height: 1;
    }

    .view-btn.active {
        background: rgba(255,255,255,.26);
        color: #fff;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.24);
    }

    .view-btn:hover {
        background: rgba(255,255,255,.14);
        color: #fff;
    }

    .btn-new-rdv {
        background: #fff;
        color: #0a69bc;
        font-weight: 900;
        padding: 10px 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        white-space: nowrap;
        border-radius: 12px;
        box-shadow: 0 12px 18px -16px rgba(8, 58, 104, .75);
        letter-spacing: .2px;
    }

    .btn-new-rdv:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 22px -16px rgba(8, 58, 104, .85);
        color: #075b9f;
    }

    .agenda-filters {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 10px;
        padding: 12px;
        background: #f8fbff;
        border-bottom: 1px solid #e2edf9;
    }

    .filter-inline {
        display: flex;
        gap: 8px;
    }

    .agenda-filters select,
    .agenda-filters input {
        width: 100%;
        border: 1px solid #cfddf1;
        border-radius: 10px;
        background: #fff;
        padding: 10px 12px;
        font-size: 13px;
        color: #1f334d;
    }

    .agenda-filters select:focus,
    .agenda-filters input:focus {
        outline: none;
        border-color: #71a9e6;
        box-shadow: 0 0 0 3px rgba(11,122,199,.12);
    }

    .btn-reset {
        border: 1px solid #cfddf1;
        background: #fff;
        border-radius: 10px;
        min-height: 42px;
        padding: 0 12px;
        color: #395273;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .btn-apply {
        border: 1px solid #0b7ac7;
        background: linear-gradient(120deg, #0b7ac7 0%, #1098e0 100%);
        color: #fff;
        min-height: 42px;
        border-radius: 10px;
        padding: 0 14px;
        font-weight: 700;
        box-shadow: 0 8px 16px -14px rgba(11, 122, 199, .9);
        cursor: pointer;
    }

    .btn-apply:hover {
        filter: brightness(1.03);
    }

    .filter-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .agenda-body,
    .agenda-body#timelineContainer {
        padding: 12px;
        display: block;
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
        overflow-y: visible;
    }

    .timeline-row {
        display: grid;
        grid-template-columns: 62px 1fr;
        gap: 10px;
        margin-bottom: 10px;
    }

    .timeline-time {
        color: #334d71;
        font-size: 12px;
        font-weight: 800;
        padding-top: 9px;
        text-align: right;
    }

    .timeline-slot {
        min-height: 72px;
        border: 1px solid #dce7f6;
        border-radius: 12px;
        background: #fff;
        padding: 6px;
        position: relative;
    }

    .timeline-slot:hover {
        border-color: #8bb6e7;
        background: #f9fcff;
    }

    .rdv-card {
        border-radius: 10px;
        padding: 8px 10px;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 5px;
        background: linear-gradient(135deg, #0b7ac7, #0ea5e9);
        border: 1px solid rgba(255,255,255,.22);
    }

    .rdv-card.confirmed {
        background: linear-gradient(135deg, #0f9f74, #14b887);
    }

    .rdv-card.urgent {
        background: linear-gradient(135deg, #e11d48, #fb7185);
    }

    .rdv-time,
    .rdv-patient,
    .rdv-info {
        line-height: 1.25;
    }

    .rdv-time { font-weight: 800; }
    .rdv-patient { font-weight: 700; margin: 2px 0; }
    .rdv-info { opacity: .9; font-size: 11px; }

    .quick-add {
        --qa-bg: linear-gradient(180deg, #f9fcff 0%, #eef6ff 100%);
        --qa-border: #9cc0e9;
        --qa-text: #0b69bb;
        --qa-pill-bg: #e7f3ff;
        --qa-pill-border: #b8d5f3;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        min-height: 40px;
        border: 1px dashed var(--qa-border);
        background: var(--qa-bg);
        color: var(--qa-text);
        font-weight: 800;
        letter-spacing: .2px;
        border-radius: 12px;
        padding: 9px 12px;
        font-size: 13px;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, color .2s ease, background .2s ease;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 6px 14px -14px rgba(11, 105, 187, .45);
    }

    .quick-add::before {
        content: '+';
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        border: 1px solid var(--qa-pill-border);
        background: var(--qa-pill-bg);
        color: currentColor;
        font-size: 14px;
        line-height: 1;
        font-weight: 900;
    }

    .quick-add::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, transparent 20%, rgba(255,255,255,.34) 45%, transparent 70%);
        transform: translateX(-120%);
        transition: transform .5s ease;
        pointer-events: none;
    }

    .quick-add:hover {
        transform: translateY(-1px);
        border-color: #6fa7e0;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.95), 0 12px 18px -16px rgba(11, 105, 187, .55);
        color: #0a5ea8;
        background: linear-gradient(180deg, #ffffff 0%, #eaf4ff 100%);
    }

    .quick-add:hover::after {
        transform: translateX(120%);
    }

    .quick-add:focus-visible {
        outline: none;
        border-color: #3e8bd8;
        box-shadow: 0 0 0 4px rgba(11,122,199,.18), inset 0 1px 0 rgba(255,255,255,.95);
    }

    .quick-add:active {
        transform: translateY(0);
    }

    @media (max-width: 1320px) {
        .agenda-grid {
            grid-template-columns: 1fr;
        }

        .agenda-side {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
        }
    }

    @media (max-width: 1180px) {
        .agenda-top {
            justify-content: flex-start;
            gap: 10px;
        }

        .agenda-title {
            flex-basis: 100%;
        }

        .date-controls {
            flex: 1 1 58%;
            min-width: 0;
        }

        .view-tools {
            flex: 1 1 38%;
            margin-left: 0;
            justify-content: flex-end;
        }
    }

    @media (max-width: 980px) {
        .agenda-top {
            text-align: left;
        }

        .agenda-title,
        .date-controls,
        .view-tools {
            justify-content: center;
            flex-basis: 100%;
            min-width: 0;
        }

        .view-tools {
            justify-content: flex-start;
            width: 100%;
            gap: 8px;
        }

        .agenda-filters {
            grid-template-columns: 1fr;
        }

        .filter-inline {
            flex-direction: column;
        }
    }

    @media (max-width: 760px) {
        .agenda-shell { padding: 8px; border-radius: 14px; }
        .agenda-side { grid-template-columns: 1fr; }
        .mini-cal-body,
        .upcoming,
        .agenda-body { padding: 10px; }
        .agenda-title { min-width: 0; }
        .agenda-title h1 { font-size: 1.55rem; }
        .agenda-title i { width: 40px; height: 40px; font-size: 1.1rem; border-radius: 11px; }
        .date-controls { min-width: 0; width: 100%; flex-wrap: wrap; }
        .date-label { min-width: 0; width: 100%; font-size: .86rem; padding: 8px 10px; white-space: normal; line-height: 1.25; }
        .view-tools { justify-content: stretch; width: 100%; }
        .view-toggle { width: 100%; justify-content: center; flex-wrap: wrap; }
        .view-btn { flex: 1 1 90px; }
        .btn-new-rdv { width: 100%; justify-content: center; }
        .timeline-row { grid-template-columns: 46px 1fr; gap: 7px; }
        .timeline-time { font-size: 11px; }
    }

    /* Dark mode (page specific) */
    body.dark-mode .agenda-shell {
        background:
            radial-gradient(circle at 10% -10%, rgba(11, 122, 199, .18) 0%, transparent 45%),
            radial-gradient(circle at 100% 0%, rgba(14, 165, 233, .16) 0%, transparent 40%),
            #0f172a;
        border-color: #1e3a5f;
    }

    body.dark-mode .ag-card,
    body.dark-mode .agenda-main {
        background: #111f33;
        border-color: #27456d;
        box-shadow: 0 18px 30px -34px rgba(0, 0, 0, .9);
    }

    body.dark-mode .agenda-top {
        background: linear-gradient(120deg, #0f3a66 0%, #145189 48%, #1a669d 100%);
        border-bottom-color: rgba(255,255,255,.12);
    }

    body.dark-mode .agenda-title i,
    body.dark-mode .date-controls,
    body.dark-mode .view-toggle {
        border-color: rgba(133, 184, 235, .32);
        background: rgba(20, 51, 84, .64);
    }

    body.dark-mode .date-controls button,
    body.dark-mode .btn-today {
        background: rgba(102, 156, 214, .2);
        border-color: rgba(143, 187, 236, .36);
    }

    body.dark-mode .date-label {
        background: rgba(96, 149, 208, .16);
    }

    body.dark-mode .btn-new-rdv {
        background: linear-gradient(180deg, #f6fbff 0%, #deeeff 100%);
        color: #0b4f8b;
    }

    body.dark-mode .mini-cal-head h3,
    body.dark-mode .upcoming h4,
    body.dark-mode .metric-value,
    body.dark-mode .up-patient,
    body.dark-mode .timeline-time {
        color: #e2ecf8;
    }

    body.dark-mode .metric,
    body.dark-mode .up-item,
    body.dark-mode .mini-day,
    body.dark-mode .timeline-slot {
        background: #13263f;
        border-color: #2d4f7b;
        color: #d7e5f7;
    }

    body.dark-mode .mini-day:hover,
    body.dark-mode .timeline-slot:hover {
        background: #173257;
        border-color: #4a7eb9;
    }

    body.dark-mode .mini-day.pad {
        background: transparent;
        border-color: transparent;
    }

    body.dark-mode .agenda-filters {
        background: #102239;
        border-bottom-color: #29476f;
    }

    body.dark-mode .agenda-filters select,
    body.dark-mode .agenda-filters input,
    body.dark-mode .btn-reset {
        background: #132a45;
        border-color: #355985;
        color: #dce9f8;
    }

    body.dark-mode .btn-reset:hover {
        background: #17365a;
    }

    body.dark-mode .btn-apply {
        border-color: #4f7db0;
        background: linear-gradient(120deg, #275a90 0%, #2e74b5 100%);
    }

    body.dark-mode .quick-add {
        --qa-bg: linear-gradient(180deg, #163457 0%, #133050 100%);
        --qa-border: #3f6799;
        --qa-text: #b8dcff;
        --qa-pill-bg: #1e456f;
        --qa-pill-border: #4d79ab;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.05), 0 12px 24px -22px rgba(0,0,0,.9);
    }

    body.dark-mode .quick-add:hover {
        background: linear-gradient(180deg, #1b426f 0%, #173a61 100%);
        border-color: #6798cf;
        color: #d4e9ff;
    }

    body.dark-mode .up-doc,
    body.dark-mode .rdv-info,
    body.dark-mode .metric-label,
    body.dark-mode .agenda-title p {
        color: #a7bfdc;
    }

    /* Premium retouches */
    .agenda-shell {
        border-radius: 24px;
        padding: clamp(12px, 1.7vw, 20px);
        border: 1px solid #cfe1f5;
        box-shadow: 0 24px 40px -36px rgba(18, 64, 111, .55);
    }

    .agenda-grid {
        grid-template-columns: 330px 1fr;
        gap: 16px;
    }

    .ag-card,
    .agenda-main {
        border-radius: 18px;
        border-color: #d4e4f7;
        box-shadow: 0 18px 30px -34px rgba(20, 70, 120, 0.75), 0 1px 0 rgba(255, 255, 255, .9) inset;
    }

    .mini-cal-head h3,
    .agenda-title h1,
    .upcoming h4,
    .timeline-time,
    .metric-value {
        font-family: "Sora", "Manrope", "Segoe UI", sans-serif;
    }

    .mini-cal-body {
        padding: 16px;
        background: linear-gradient(180deg, #fcfdff 0%, #f6faff 100%);
    }

    .mini-day {
        min-height: 37px;
        border-radius: 10px;
        background: #f7fbff;
        border-color: #e3ebf4;
        transition: transform .2s ease, border-color .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .mini-day:hover {
        transform: translateY(-1px);
        border-color: #bfd4ea;
        background: #f1f7fe;
        box-shadow: 0 10px 18px -18px rgba(20, 70, 120, .32);
    }

    .mini-day.selected {
        border-color: #52a2ea;
        background: linear-gradient(135deg, #e8f4ff 0%, #d8ebff 100%);
        color: #0a4c86;
        box-shadow: inset 0 0 0 1px rgba(82, 162, 234, .22);
    }

    .mini-day.today.selected {
        background: linear-gradient(135deg, #0b7ac7 0%, #17a3e4 100%);
        color: #fff;
        border-color: transparent;
    }

    .agenda-metrics {
        gap: 10px;
    }

    .metric {
        padding: 11px 12px;
        border-radius: 12px;
        background: linear-gradient(180deg, #ffffff 0%, #f4f9ff 100%);
        border: 1px solid #dbe7f4;
        box-shadow: 0 18px 28px -32px rgba(20, 70, 120, .22);
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
    }

    .metric:hover {
        transform: translateY(-2px);
        border-color: #c6d8ea;
        box-shadow: 0 24px 34px -30px rgba(20, 70, 120, .28);
    }

    .agenda-main {
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .agenda-top {
        padding: 20px 20px;
        border-bottom: 1px solid rgba(255,255,255,.24);
        box-shadow: inset 0 -1px 0 rgba(255,255,255,.14);
        display: grid;
        grid-template-columns: minmax(260px, 1fr) minmax(420px, 1.25fr) auto;
        align-items: center;
        gap: 14px;
    }

    .agenda-title h1 {
        font-size: clamp(2rem, 3.1vw, 2.55rem);
        letter-spacing: -.03em;
    }

    .agenda-title p {
        font-size: .98rem;
        letter-spacing: .15px;
    }

    .date-controls {
        border-radius: 15px;
        padding: 8px 10px;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.28), 0 10px 22px -20px rgba(4, 53, 95, .65);
        width: 100%;
        min-width: 0;
        gap: 10px;
    }

    .date-label {
        font-size: 1.02rem;
        letter-spacing: .1px;
        border: 1px solid rgba(255,255,255,.12);
        flex: 1 1 auto;
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .view-btn {
        font-weight: 800;
        min-width: 66px;
    }

    .btn-new-rdv {
        border-radius: 14px;
        min-height: 46px;
        padding-inline: 20px;
        box-shadow: 0 14px 24px -20px rgba(8, 58, 104, .95);
        flex: 0 0 auto;
    }

    .agenda-title {
        min-width: 0;
        margin-right: 2px;
    }

    .agenda-title i {
        flex: 0 0 48px;
    }

    .agenda-title > div {
        min-width: 0;
    }

    .agenda-title h1,
    .agenda-title p {
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .view-tools {
        margin-left: 0;
        justify-self: end;
        gap: 12px;
        flex-wrap: nowrap;
    }

    .view-toggle {
        flex-wrap: nowrap;
    }

    .agenda-filters {
        padding: 12px 12px 13px;
        background: linear-gradient(180deg, #f9fcff 0%, #f4f9ff 100%);
        border-bottom: 1px solid #dbe9f8;
    }

    .agenda-filters select,
    .agenda-filters input {
        min-height: 44px;
        border-radius: 12px;
        border-color: #c9dcf2;
        font-weight: 600;
    }

    .filter-actions {
        align-items: stretch;
    }

    .btn-apply,
    .btn-reset {
        min-height: 44px;
        border-radius: 12px;
        font-weight: 800;
    }

    .timeline-row {
        margin-bottom: 12px;
        transition: background .2s ease;
    }

    .timeline-row:hover {
        background: rgba(243, 248, 254, .58);
    }

    .timeline-time {
        font-size: 13px;
        color: #1e3f68;
    }

    .timeline-slot {
        border-radius: 14px;
        min-height: 76px;
        border-color: #d5e5f7;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.92);
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .timeline-slot.has-events {
        border-color: #b9d6f4;
        background: linear-gradient(180deg, #fafdff 0%, #f4f9ff 100%);
    }

    .timeline-slot:hover {
        transform: translateY(-1px);
        border-color: #c6d9ee;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fe 100%);
        box-shadow: 0 18px 28px -30px rgba(20, 70, 120, .2);
    }

    .rdv-card {
        border-radius: 12px;
        padding: 9px 11px;
        box-shadow: 0 10px 16px -14px rgba(2, 37, 71, .55);
    }

    .rdv-card .rdv-time {
        letter-spacing: .1px;
    }

    .quick-add {
        min-height: 42px;
        border-radius: 13px;
        font-weight: 900;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.92), 0 10px 18px -20px rgba(11, 105, 187, .28);
    }

    .quick-add::before {
        width: 22px;
        height: 22px;
        font-size: 15px;
    }

    @media (max-width: 1320px) {
        .agenda-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }
    }

    @media (max-width: 980px) {
        .agenda-top {
            padding: 15px 14px;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .agenda-title h1 {
            font-size: 1.85rem;
        }

        .agenda-title p {
            font-size: .92rem;
        }

        .date-label {
            width: 100%;
            min-width: 0;
        }

        .view-toggle {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .view-btn {
            flex: 1 1 0;
        }

        .view-tools {
            width: 100%;
            justify-self: stretch;
            flex-wrap: wrap;
            gap: 8px;
        }

        .btn-new-rdv {
            width: 100%;
            justify-content: center;
        }

        .agenda-layout-toggle {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .agenda-layout-btn {
            width: 100%;
        }
    }

    @media (max-width: 1260px) and (min-width: 981px) {
        .agenda-top {
            grid-template-columns: minmax(240px, .95fr) minmax(360px, 1.1fr) auto;
            gap: 10px;
        }

        .agenda-title h1 {
            font-size: clamp(1.82rem, 2.35vw, 2.1rem);
        }

        .agenda-title p {
            font-size: .9rem;
        }

        .date-controls {
            padding: 7px 8px;
            gap: 8px;
        }

        .date-controls > button:not(.btn-today) {
            width: 32px;
            height: 32px;
        }

        .btn-today {
            padding: 8px 10px;
            min-width: 96px;
            font-size: .92rem;
        }

        .view-btn {
            min-width: 58px;
            padding: 8px 10px;
        }

        .btn-new-rdv {
            padding-inline: 16px;
            min-height: 43px;
            font-size: .96rem;
        }
    }

    body.dark-mode .agenda-shell {
        box-shadow: 0 26px 44px -36px rgba(0, 0, 0, .85);
        border-color: #224368;
    }

    body.dark-mode .agenda-main {
        background: linear-gradient(180deg, #112136 0%, #0f1d31 100%);
    }

    body.dark-mode .mini-cal-body {
        background: linear-gradient(180deg, #13263f 0%, #12233a 100%);
    }

    body.dark-mode .mini-day.selected {
        border-color: #5d8fc9;
        background: linear-gradient(180deg, #1c3f68 0%, #17375a 100%);
        color: #dbeafe;
    }

    body.dark-mode .metric {
        background: linear-gradient(180deg, #142944 0%, #12263f 100%);
        border-color: #2f5179;
        box-shadow: 0 20px 28px -32px rgba(0, 0, 0, .45);
    }

    body.dark-mode .agenda-filters {
        background: linear-gradient(180deg, #10253d 0%, #0f2238 100%);
        border-bottom-color: #2d4f79;
    }

    body.dark-mode .timeline-slot {
        background: linear-gradient(180deg, #12253d 0%, #112238 100%);
        border-color: #2d4f7b;
    }

    body.dark-mode .timeline-slot:hover {
        border-color: #4f78aa;
        background: linear-gradient(180deg, #142b45 0%, #12263f 100%);
        box-shadow: 0 22px 30px -34px rgba(0, 0, 0, .5);
    }

    body.dark-mode .timeline-slot.has-events {
        border-color: #4472a8;
        background: linear-gradient(180deg, #132c49 0%, #122742 100%);
    }

    /* Header override: style aligne sur Gestion des Patients */
    .agenda-top {
        background: #ffffff;
        color: #12345f;
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 18px;
        box-shadow: none;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
    }

    .agenda-top::before,
    .agenda-top::after {
        display: none;
    }

    .agenda-title {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1 1 360px;
        min-width: 260px;
        margin-right: 0;
    }

    .agenda-title i {
        width: auto;
        height: auto;
        border: none;
        border-radius: 0;
        background: transparent;
        box-shadow: none;
        color: #3b82f6;
        font-size: 1.6rem;
    }

    .agenda-title > div {
        min-width: 0;
    }

    .agenda-title-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .agenda-title h1 {
        margin: 0;
        color: #1e3a8a;
        font-size: clamp(1.8rem, 2.7vw, 2.1rem);
        font-weight: 700;
        line-height: 1.08;
        letter-spacing: 0;
    }

    .agenda-title p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: .95rem;
        opacity: 1;
    }

    .agenda-count-badge {
        background: linear-gradient(90deg, #eef5ff 0%, #e2ecfb 100%);
        color: #214f8b;
        padding: 7px 14px;
        border-radius: 999px;
        font-size: .88rem;
        font-weight: 800;
        letter-spacing: .2px;
        white-space: nowrap;
        border: 1px solid #d4e1f4;
        box-shadow: 0 10px 16px -18px rgba(37, 99, 235, .32);
    }

    .date-controls {
        background: linear-gradient(180deg, #fbfdff 0%, #f5f9fe 100%);
        border: 1px solid #dbe5f1;
        border-radius: 16px;
        padding: 7px 9px;
        min-height: 52px;
        box-shadow: 0 18px 26px -30px rgba(20, 70, 120, .22);
        backdrop-filter: none;
        flex: 1 1 430px;
        justify-content: center;
        gap: 10px;
    }

    .date-controls > button:not(.btn-today) {
        width: 42px;
        height: 42px;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fe 100%);
        color: #355273;
        border: 1px solid #d7e2ee;
        box-shadow: 0 12px 18px -22px rgba(20, 70, 120, .22);
    }

    .date-label {
        background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
        color: #173454;
        border: 1px solid #d7e2ee;
        font-size: 1rem;
        font-weight: 800;
        min-width: 280px;
        padding: 11px 16px;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.9);
    }

    .btn-today {
        background: linear-gradient(180deg, #ffffff 0%, #edf4fb 100%);
        color: #1d4e89;
        border: 1px solid #d1e0f0;
        box-shadow: 0 12px 18px -22px rgba(20, 70, 120, .22);
        padding: 9px 14px;
    }

    .btn-today:hover,
    .date-controls > button:not(.btn-today):hover {
        background: linear-gradient(180deg, #ffffff 0%, #e8f1fb 100%);
        color: #1c446f;
        transform: translateY(-1px);
    }

    .view-tools {
        margin-left: auto;
        gap: 10px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .view-toggle {
        border: 1px solid #dbe5f1;
        background: linear-gradient(180deg, #fbfdff 0%, #f5f9fe 100%);
        border-radius: 14px;
        padding: 4px;
        gap: 4px;
        box-shadow: 0 16px 24px -30px rgba(20, 70, 120, .2);
    }

    .view-btn {
        color: #355273;
        font-weight: 800;
        border-radius: 10px;
        min-height: 40px;
        padding: 9px 15px;
    }

    .view-btn.active {
        background: linear-gradient(135deg, #2563eb 0%, #1d4fbe 100%);
        color: #fff;
        box-shadow: 0 14px 20px -18px rgba(37, 99, 235, .45);
    }

    .view-btn:hover {
        background: #ecf3ff;
        color: #1b4d87;
    }

    .btn-new-rdv {
        background: linear-gradient(135deg, #2563eb 0%, #1d4fbe 100%);
        border: 1px solid transparent;
        color: #fff;
        border-radius: 14px;
        box-shadow: 0 20px 30px -24px rgba(37, 99, 235, .48);
        font-weight: 800;
        min-height: 46px;
        padding: 0 18px;
    }

    .btn-new-rdv:hover {
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
        border-color: transparent;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 24px 34px -24px rgba(37, 99, 235, .55);
    }

    .agenda-layout-toggle {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px;
        border-radius: 14px;
        border: 1px solid #dbe5f1;
        background: linear-gradient(180deg, #fbfdff 0%, #f5f9fe 100%);
        box-shadow: 0 16px 24px -30px rgba(20, 70, 120, .2);
    }

    .agenda-layout-btn {
        min-height: 40px;
        padding: 9px 14px;
        border-radius: 10px;
        color: #355273;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        transition: background .18s ease, color .18s ease, transform .18s ease;
    }

    .agenda-layout-btn:hover {
        background: #ecf3ff;
        color: #1b4d87;
        text-decoration: none;
    }

    .agenda-layout-btn.active {
        background: linear-gradient(135deg, #2563eb 0%, #1d4fbe 100%);
        color: #fff;
        box-shadow: 0 14px 20px -18px rgba(37, 99, 235, .45);
    }

    .agenda-filters {
        gap: 12px;
        padding: 14px;
        background: linear-gradient(180deg, #fbfdff 0%, #f6faff 100%);
        border-bottom-color: #e1ebf6;
    }

    .agenda-filters select,
    .agenda-filters input {
        min-height: 44px;
        border-radius: 12px;
        border-color: #d6e2ee;
        font-size: 13px;
        font-weight: 600;
        transition: all .2s ease;
    }

    .btn-reset,
    .btn-apply {
        min-height: 44px;
        border-radius: 12px;
        font-weight: 800;
        transition: all .2s ease;
    }

    .btn-reset {
        border-color: #d7e1ec;
        background: linear-gradient(180deg, #ffffff 0%, #f3f7fb 100%);
        color: #5a6f88;
    }

    .btn-reset:hover {
        transform: translateY(-1px);
        background: linear-gradient(180deg, #ffffff 0%, #edf4fb 100%);
        color: #36506f;
    }

    @media (max-width: 980px) {
        .agenda-top {
            padding: 14px;
        }

        .agenda-title,
        .date-controls,
        .view-tools {
            flex: 1 1 100%;
        }

        .date-controls {
            justify-content: center;
            flex-wrap: wrap;
        }

        .date-label {
            width: 100%;
            min-width: 0;
            order: 1;
        }

        .view-tools {
            justify-content: stretch;
            gap: 8px;
            flex-wrap: wrap;
        }

        .view-toggle {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .btn-new-rdv {
            width: 100%;
            justify-content: center;
        }

        .agenda-filters {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-items: stretch;
        }

        .agenda-filters .filter-item,
        .agenda-filters .filter-actions {
            grid-column: span 1;
        }

        .agenda-filters .filter-item.search,
        .agenda-filters .filter-actions {
            grid-column: 1 / -1;
        }

        .timeline-row {
            grid-template-columns: 54px 1fr;
        }

        .timeline-time {
            padding-top: 12px;
        }
    }

    @media (max-width: 760px) {
        .agenda-top {
            gap: 12px;
        }

        .date-controls {
            padding: 8px;
            gap: 8px;
        }

        .date-controls > button:not(.btn-today),
        .btn-today {
            width: calc(50% - 4px);
            justify-content: center;
        }

        .date-label {
            order: 0;
            padding: 10px 14px;
            text-align: center;
        }

        .view-toggle {
            gap: 6px;
        }

        .view-btn {
            min-height: 42px;
            padding: 8px 10px;
            font-size: 12px;
        }

        .agenda-filters {
            grid-template-columns: 1fr;
            padding: 12px;
        }

        .agenda-filters .filter-item,
        .agenda-filters .filter-actions,
        .agenda-filters select,
        .agenda-filters input,
        .btn-reset,
        .btn-apply {
            width: 100%;
        }

        .filter-actions {
            grid-template-columns: 1fr;
        }

        .timeline-row {
            grid-template-columns: 1fr;
            gap: 6px;
            margin-bottom: 14px;
        }

        .timeline-time {
            text-align: left;
            padding-top: 0;
            padding-left: 2px;
        }

        .timeline-slot {
            min-height: 86px;
        }

        .timeline-slot .quick-add {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }
    }

    body.dark-mode .agenda-top {
        background: #0f2238;
        border-bottom-color: #2b4a6a;
        color: #d9e9fa;
    }

    body.dark-mode .agenda-title i {
        color: #77b7ff;
    }

    body.dark-mode .agenda-title h1 {
        color: #e4f1ff;
    }

    body.dark-mode .agenda-title p {
        color: #9db8d5;
    }

    body.dark-mode .agenda-count-badge {
        background: linear-gradient(90deg, #1f5fb3 60%, #123771 100%);
    }

    body.dark-mode .date-controls,
    body.dark-mode .view-toggle {
        background: #132a45;
        border-color: #355677;
    }

    body.dark-mode .date-controls > button:not(.btn-today),
    body.dark-mode .date-label {
        background: #173450;
        border-color: #3f6284;
        color: #d3e7fb;
    }

    body.dark-mode .btn-today {
        background: #1f4168;
        border-color: #3f6284;
        color: #d3e7fb;
    }

    body.dark-mode .btn-today:hover,
    body.dark-mode .date-controls > button:not(.btn-today):hover {
        background: #27517e;
        color: #fff;
    }

    body.dark-mode .view-btn {
        color: #c9def4;
    }

    body.dark-mode .view-btn:hover {
        background: #1f4168;
        color: #fff;
    }

    body.dark-mode .agenda-layout-toggle {
        background: #132a45;
        border-color: #355677;
    }

    body.dark-mode .agenda-layout-btn {
        color: #c9def4;
    }

    body.dark-mode .agenda-layout-btn:hover {
        background: #1f4168;
        color: #fff;
    }

    body.dark-mode .agenda-layout-btn.active {
        background: linear-gradient(135deg, #2563eb 0%, #1d4fbe 100%);
        color: #fff;
    }

    .mini-nav button,
    .date-controls > button:not(.btn-today),
    .btn-today,
    .view-btn {
        min-width: 44px;
        min-height: 44px;
    }

    .view-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    /* Planning Premium - cards + motif colors + responsive */
    .timeline-row {
        display: grid;
        grid-template-columns: 72px minmax(0, 1fr);
        gap: 14px;
        margin: 0;
        padding: 10px 0;
        border-top: 1px solid #edf2f7;
        align-items: start;
    }

    .timeline-row:first-child {
        border-top: none;
    }

    .timeline-row:hover {
        background: transparent;
    }

    .timeline-time {
        position: sticky;
        left: 0;
        top: 0;
        padding-top: 6px;
        color: #8aa0b6;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .timeline-slot {
        min-height: 66px;
        padding: 2px 0 0;
        display: grid;
        gap: 10px;
        align-content: start;
        border: none;
        border-radius: 0;
        background: transparent;
        box-shadow: none;
    }

    .timeline-slot.has-events {
        background: transparent;
    }

    .timeline-slot.is-empty {
        min-height: 72px;
        padding: 0;
        border-radius: 14px;
        border: 1px dashed #dbe6f0;
        background: linear-gradient(180deg, #fcfdff 0%, #f8fbfe 100%);
        cursor: pointer;
        transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .timeline-slot:hover {
        transform: none;
        border: none;
        background: transparent;
        box-shadow: none;
    }

    .timeline-slot.is-empty:hover {
        border-color: #bfd5ea;
        background: linear-gradient(180deg, #ffffff 0%, #f3f8fd 100%);
        box-shadow: 0 16px 24px -28px rgba(20, 70, 120, .3);
    }

    .timeline-slot .quick-add {
        margin-top: 0;
        min-height: 34px;
        border-radius: 10px;
        background: #fafcfe;
        border: 1px dashed #dbe5ee;
        color: #7a8ea2;
        box-shadow: none;
        opacity: 0;
        transform: translateY(4px);
        pointer-events: none;
        transition: opacity .18s ease, transform .18s ease, border-color .18s ease, color .18s ease, background .18s ease;
    }

    .timeline-slot.has-events .quick-add {
        margin-top: 2px;
        min-height: 32px;
    }

    .timeline-slot:hover .quick-add,
    .timeline-slot:focus-within .quick-add {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    .rdv-card {
        --rdv-accent: #2563eb;
        --rdv-soft: #d8e6f2;
        --rdv-text: #16324d;
        border-radius: 16px;
        padding: 12px 13px 11px;
        border: 1px solid var(--rdv-soft);
        background: rgba(255, 255, 255, 0.96);
        color: var(--rdv-text);
        box-shadow: 0 16px 28px -24px rgba(15, 23, 42, 0.14);
        margin-bottom: 0;
        display: grid;
        gap: 10px;
        position: relative;
        overflow: hidden;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .rdv-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        border-radius: 0;
        background: var(--rdv-accent);
        opacity: 1;
    }

    .rdv-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 22px 36px -28px rgba(15, 23, 42, 0.18);
        border-color: color-mix(in srgb, var(--rdv-accent) 12%, white);
    }

    .rdv-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        min-width: 0;
    }

    .rdv-time {
        font-size: 13px;
        line-height: 1.1;
        font-weight: 900;
        color: var(--rdv-accent);
        letter-spacing: 0.2px;
    }

    .rdv-motif-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 8px;
        border-radius: 999px;
        border: 1px solid #d7e2eb;
        background: #f8fafc;
        color: #5f7288;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
        max-width: 48%;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .rdv-patient {
        font-size: 1rem;
        line-height: 1.2;
        font-weight: 800;
        color: #0f2d53;
        margin: 0;
        word-break: break-word;
    }

    .rdv-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 6px 10px;
    }

    .rdv-meta-line {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
        color: #61758c;
        font-size: 12px;
        line-height: 1.25;
        font-weight: 700;
    }

    .rdv-meta-line i {
        color: #89a0b7;
        font-size: 11px;
        width: 12px;
        text-align: center;
        flex: 0 0 auto;
    }

    .rdv-meta-line span {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .rdv-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: fit-content;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .02em;
        border: 1px solid #d7e2eb;
        background: #f8fafc;
        color: #607287;
    }

    .rdv-status-badge.status-a-venir {
        color: #1d4ed8;
        background: #eff6ff;
        border-color: #cfe0ff;
    }

    .rdv-status-badge.status-attente {
        color: #b45309;
        background: #fff7ed;
        border-color: #f7d7b5;
    }

    .rdv-status-badge.status-soins {
        color: #7c3aed;
        background: #f5f3ff;
        border-color: #ddd1ff;
    }

    .rdv-status-badge.status-vu {
        color: #15803d;
        background: #f0fdf4;
        border-color: #cbeecd;
    }

    .rdv-status-badge.status-absent {
        color: #dc2626;
        background: #fef2f2;
        border-color: #f6cccc;
    }

    .rdv-status-badge.status-annule {
        color: #6b7280;
        background: #f4f4f5;
        border-color: #e4e4e7;
    }

    .rdv-card.status-a-venir { --rdv-accent: #2563eb; --rdv-soft: #dbe7ff; }
    .rdv-card.status-attente { --rdv-accent: #f59e0b; --rdv-soft: #fde6bf; }
    .rdv-card.status-soins { --rdv-accent: #8b5cf6; --rdv-soft: #e2d7ff; }
    .rdv-card.status-vu { --rdv-accent: #16a34a; --rdv-soft: #d0f0da; }
    .rdv-card.status-absent { --rdv-accent: #ef4444; --rdv-soft: #f7d2d2; }
    .rdv-card.status-annule { --rdv-accent: #6b7280; --rdv-soft: #e5e7eb; }

    .rdv-quick-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }

    .rdv-quick-action {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        border: 1px solid #d8e3ec;
        background: #ffffff;
        color: #456179;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        box-shadow: 0 8px 16px -16px rgba(15, 23, 42, .26);
        transition: transform .18s ease, border-color .18s ease, background .18s ease, color .18s ease;
    }

    .rdv-quick-action:hover {
        transform: translateY(-1px);
        border-color: color-mix(in srgb, var(--rdv-accent) 28%, #d8e3ec);
        background: color-mix(in srgb, var(--rdv-accent) 8%, #ffffff);
        color: var(--rdv-accent);
        text-decoration: none;
    }

    .rdv-side-actions {
        display: flex;
        justify-content: flex-end;
        align-items: flex-start;
    }

    .rdv-action-stack {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .rdv-action-primary,
    .rdv-action-more {
        min-height: 32px;
        border-radius: 9px;
        border: 1px solid #d8e3ec;
        background: #fff;
        color: #35506d;
        text-decoration: none;
        font-size: 11px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        transition: .2s ease;
    }

    .rdv-action-primary {
        padding: 0 10px;
        background: linear-gradient(135deg, #1f6fa3 0%, #2a7fb5 100%);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 10px 18px -16px rgba(31, 111, 163, .55);
    }

    .rdv-action-primary:hover {
        color: #fff;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .rdv-action-more {
        width: 32px;
        cursor: pointer;
        list-style: none;
    }

    .rdv-action-more::-webkit-details-marker {
        display: none;
    }

    .rdv-action-more:hover {
        background: #f8fafc;
        border-color: #c8d5e2;
        color: #16324d;
    }

    .rdv-more {
        position: relative;
    }

    .rdv-more[open] .rdv-action-more {
        background: #f8fafc;
        border-color: #c8d5e2;
    }

    .rdv-more-menu {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        z-index: 12;
        min-width: 180px;
        padding: 8px;
        border-radius: 12px;
        border: 1px solid #d8e3ec;
        background: #fff;
        box-shadow: 0 20px 28px -24px rgba(15, 23, 42, .28);
        display: grid;
        gap: 6px;
    }

    .rdv-more-link,
    .rdv-more-menu button {
        width: 100%;
        min-height: 34px;
        padding: 0 10px;
        border: 1px solid transparent;
        border-radius: 9px;
        background: #fff;
        color: #456179;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-align: left;
        cursor: pointer;
    }

    .rdv-more-link:hover,
    .rdv-more-menu button:hover {
        background: #f8fafc;
        color: #16324d;
        text-decoration: none;
    }

    .rdv-more-menu form {
        margin: 0;
    }

    @media (max-width: 1120px) {
        .rdv-meta-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .agenda-snapshot {
            grid-template-columns: 1fr;
        }

        .rdv-card {
            padding: 10px 10px 9px;
        }

        .rdv-card-head {
            align-items: flex-start;
            flex-direction: column;
            gap: 6px;
        }

        .rdv-motif-badge {
            max-width: 100%;
        }

        .rdv-time {
            font-size: 12px;
        }

        .rdv-patient {
            font-size: 0.92rem;
        }

        .rdv-card-body {
            gap: 10px;
        }

        .rdv-side-actions {
            justify-content: space-between;
            width: 100%;
        }

        .rdv-quick-actions {
            gap: 5px;
        }

        .rdv-quick-action,
        .rdv-action-more {
            width: 32px;
            height: 32px;
        }
    }

    body.dark-mode .rdv-card {
        border-color: #36587d;
        background: linear-gradient(180deg, #152d49 0%, #122840 100%);
        box-shadow: 0 14px 22px -20px rgba(0, 0, 0, 0.92);
        color: #dbeafe;
    }

    body.dark-mode .rdv-card::before {
        opacity: .95;
    }

    body.dark-mode .rdv-time {
        color: color-mix(in srgb, var(--rdv-accent) 45%, #eaf4ff);
    }

    body.dark-mode .rdv-patient {
        color: #e9f3ff;
    }

    body.dark-mode .rdv-meta-line {
        color: #b7cde5;
    }

    body.dark-mode .rdv-motif-badge {
        background: color-mix(in srgb, var(--rdv-accent) 26%, #0f2439);
        border-color: color-mix(in srgb, var(--rdv-accent) 45%, #365b81);
        color: #e8f2ff;
    }

    body.dark-mode .rdv-quick-action,
    body.dark-mode .rdv-action-more {
        background: #132a45;
        border-color: #355985;
        color: #d6e6f8;
    }

    body.dark-mode .rdv-quick-action:hover,
    body.dark-mode .rdv-action-more:hover {
        background: color-mix(in srgb, var(--rdv-accent) 20%, #132a45);
        border-color: color-mix(in srgb, var(--rdv-accent) 35%, #355985);
        color: #ffffff;
    }

    body.dark-mode .timeline-slot.is-empty {
        border-color: #36587d;
        background: linear-gradient(180deg, #112238 0%, #12253d 100%);
    }

    body.dark-mode .timeline-slot.is-empty:hover {
        border-color: #4f78aa;
        background: linear-gradient(180deg, #142b45 0%, #12263f 100%);
    }

    body.dark-mode .timeline-slot .quick-add {
        background: #132a45;
        border-color: #355985;
        color: #a9c8e8;
    }

    .agenda-side-card {
        display: grid;
        gap: 14px;
    }

    .agenda-side-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .agenda-side-card-header strong {
        color: #163964;
        font-size: .98rem;
        font-weight: 800;
        letter-spacing: -.01em;
    }

    .side-section-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 999px;
        background: #eaf4ff;
        color: #19528c;
        border: 1px solid #cbe0f7;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .agenda-metrics {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .metric {
        display: grid;
        gap: 8px;
    }

    .metric-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .metric-top i {
        width: 30px;
        height: 30px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        color: #1b5f9d;
        background: #e8f3ff;
        border: 1px solid #d2e5f8;
    }

    .metric-meta {
        color: #67809f;
        font-size: 11px;
        line-height: 1.35;
        font-weight: 700;
    }

    .upcoming-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .upcoming-header h4 {
        margin: 0;
    }

    .up-list {
        display: grid;
        gap: 10px;
    }

    .up-item {
        display: grid;
        gap: 8px;
    }

    .up-item-head {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .up-avatar,
    .rdv-avatar {
        width: 42px;
        height: 42px;
        border-radius: 999px;
        overflow: hidden;
        flex: 0 0 42px;
        border: 1px solid #d4e5f6;
        box-shadow: 0 10px 16px -18px rgba(12, 53, 97, .75);
        background: linear-gradient(180deg, #eff7ff 0%, #dcecff 100%);
    }

    .up-avatar img,
    .rdv-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .up-avatar-fallback,
    .rdv-avatar-fallback {
        width: 100%;
        height: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #165290;
        font-size: 13px;
        font-weight: 900;
        letter-spacing: .03em;
    }

    .up-main {
        min-width: 0;
        display: grid;
        gap: 3px;
    }

    .up-patient {
        margin: 0;
    }

    .up-meta {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        color: #68819f;
        font-size: 11px;
        font-weight: 700;
    }

    .up-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        border-radius: 999px;
        border: 1px solid #d3e4f6;
        background: #f7fbff;
        color: #345a86;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .agenda-snapshot {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 8px;
        padding: 10px 12px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        border-bottom: 1px solid #dbe9f8;
    }

    .snapshot-chip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        min-height: 56px;
        padding: 9px 11px;
        border-radius: 12px;
        border: 1px solid #d8e6f6;
        background: #ffffff;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.92);
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .snapshot-chip:hover {
        transform: translateY(-1px);
        border-color: #c8dbef;
        background: linear-gradient(180deg, #ffffff 0%, #eef5ff 100%);
        box-shadow: 0 18px 24px -24px rgba(20, 70, 120, .22);
    }

    .snapshot-chip strong {
        color: #0f3158;
        font-size: 1.02rem;
        line-height: 1;
        font-weight: 900;
        flex: 0 0 auto;
    }

    .snapshot-chip span {
        color: #627c9b;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .07em;
        line-height: 1.2;
    }

    .snapshot-chip.info { border-color: #cfe1f6; background: #f8fbff; }
    .snapshot-chip.waiting { border-color: #f8d9b3; background: #fff9f1; }
    .snapshot-chip.active { border-color: #ddd0ff; background: #f8f5ff; }
    .snapshot-chip.success { border-color: #c3eddc; background: #f4fff9; }
    .snapshot-chip.muted { border-color: #f4c7cf; background: #fff6f7; }

    .rdv-card {
        width: min(100%, 760px);
        gap: 12px;
        padding: 15px 15px 14px 17px;
        border-radius: 16px;
        box-shadow: 0 18px 28px -26px rgba(9, 44, 86, 0.3);
    }

    .rdv-card-head {
        align-items: center;
        gap: 10px;
    }

    .rdv-status-stack {
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        flex-wrap: wrap;
        max-width: 40%;
        margin-left: auto;
    }

    .rdv-card-body {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 14px;
        align-items: start;
    }

    .rdv-main-content,
    .rdv-side-actions {
        display: grid;
        gap: 10px;
        min-width: 0;
    }

    .rdv-patient-row {
        display: grid;
        grid-template-columns: auto 1fr;
        align-items: center;
        gap: 10px;
    }

    .rdv-patient-copy {
        min-width: 0;
        display: grid;
        gap: 5px;
    }

    .rdv-patient-subline {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        color: #7c90a3;
        font-size: 11px;
        font-weight: 700;
    }

    .rdv-patient-subline span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .rdv-meta-line {
        display: grid;
        grid-template-columns: 104px minmax(0, 1fr);
        align-items: start;
        gap: 10px;
        padding: 0;
        border-radius: 0;
        background: transparent;
        border: none;
        box-shadow: none;
    }

    .rdv-meta-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-width: 120px;
        color: #6a82a1;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .rdv-meta-line span:last-child {
        color: #173861;
        text-align: left;
        font-weight: 800;
        min-width: 0;
        line-height: 1.4;
    }

    .rdv-timing-row,
    .rdv-priority-badge {
        display: none;
    }

    .rdv-timing-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        line-height: 1;
        letter-spacing: .05em;
        text-transform: uppercase;
        border: 1px solid transparent;
    }

    .rdv-timing-chip.waiting {
        color: #6d6250;
        background: #faf8f4;
        border-color: #e8dfd1;
    }

    .rdv-timing-chip.consultation {
        color: #546b69;
        background: #f5f8f7;
        border-color: #dbe4e1;
    }

    .rdv-timing-chip.delay {
        color: #745d64;
        background: #faf6f7;
        border-color: #e8dce0;
    }

    .rdv-priority-badge.urgent {
        color: #745d64;
        background: #faf6f7;
        border-color: #e8dce0;
    }

    .rdv-priority-badge.attention {
        color: #6d6250;
        background: #faf8f4;
        border-color: #e8dfd1;
    }

    .rdv-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }

    .rdv-action-form {
        margin: 0;
    }

    .rdv-action,
    .rdv-action-form button {
        width: 100%;
        min-height: 38px;
        border-radius: 11px;
        border: 1px solid #d8e6f6;
        background: #f7fbff;
        color: #1b4d83;
        padding: 0 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        transition: transform .18s ease, border-color .18s ease, background .18s ease, box-shadow .18s ease, color .18s ease;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.95);
        cursor: pointer;
    }

    .rdv-action span,
    .rdv-action-form button span {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .rdv-action:hover,
    .rdv-action-form button:hover {
        transform: translateY(-1px);
        border-color: #b8d4f2;
        background: #edf5ff;
        color: #123f71;
    }

    .rdv-action.primary,
    .rdv-action-form .rdv-action.primary {
        color: #0d5b98;
        background: #ebf6ff;
        border-color: #c3e1fb;
    }

    .rdv-action.success,
    .rdv-action-form .rdv-action.success {
        color: #0f7a58;
        background: #eafbf3;
        border-color: #bfe8d3;
    }

    .rdv-action.warning,
    .rdv-action-form .rdv-action.warning {
        color: #9a5e00;
        background: #fff5e4;
        border-color: #f0d29f;
    }

    .rdv-action.danger,
    .rdv-action-form .rdv-action.danger {
        color: #ae1736;
        background: #fff1f4;
        border-color: #f2c1cf;
    }

    .rdv-status-badge.status-annule {
        background: #f3f4f6;
        border-color: #d6dbe3;
        color: #5b6472;
    }

    @media (max-width: 1260px) {
        .agenda-snapshot {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .rdv-card {
            width: 100%;
        }

        .rdv-card-body {
            grid-template-columns: 1fr;
        }

        .rdv-actions {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 980px) {
        .agenda-metrics,
        .agenda-snapshot,
        .rdv-actions {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .rdv-status-stack {
            max-width: none;
        }

        .rdv-card-head {
            align-items: flex-start;
        }

        .rdv-actions {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .rdv-meta-line {
            grid-template-columns: 1fr;
            gap: 6px;
        }
    }

    @media (max-width: 760px) {
        .agenda-side-card-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .agenda-metrics,
        .agenda-snapshot,
        .rdv-actions {
            grid-template-columns: 1fr;
        }

        .rdv-patient-row {
            grid-template-columns: 1fr;
            align-items: flex-start;
        }

        .rdv-card {
            padding: 13px 13px 12px 15px;
        }

        .rdv-card-head {
            align-items: flex-start;
            flex-direction: column;
        }

        .rdv-status-stack {
            width: 100%;
            justify-content: flex-start;
        }

        .rdv-avatar {
            width: 40px;
            height: 40px;
        }

        .rdv-meta-line {
            grid-template-columns: 1fr;
        }

        .rdv-meta-line span:last-child {
            text-align: left;
        }

        .rdv-actions {
            grid-template-columns: 1fr;
        }
    }

    body.dark-mode .agenda-side-card-header strong,
    body.dark-mode .snapshot-chip strong,
    body.dark-mode .rdv-meta-line span:last-child {
        color: #e3f0ff;
    }

    body.dark-mode .rdv-meta-line {
        background: linear-gradient(180deg, rgba(21, 45, 73, .96) 0%, rgba(18, 40, 64, .92) 100%);
        border-color: #325478;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.03);
    }

    body.dark-mode .side-section-label {
        background: #173858;
        border-color: #3a638d;
        color: #d3e8ff;
    }

    body.dark-mode .metric-top i,
    body.dark-mode .rdv-avatar,
    body.dark-mode .up-avatar {
        background: linear-gradient(180deg, #17395e 0%, #15314f 100%);
        border-color: #3a618a;
        color: #d8ebff;
    }

    body.dark-mode .metric-meta,
    body.dark-mode .up-meta,
    body.dark-mode .snapshot-chip span,
    body.dark-mode .rdv-patient-subline,
    body.dark-mode .rdv-meta-label {
        color: #a9c0da;
    }

    body.dark-mode .up-chip,
    body.dark-mode .snapshot-chip,
    body.dark-mode .rdv-action,
    body.dark-mode .rdv-action-form button {
        background: #132b46;
        border-color: #355b84;
        color: #d4e8ff;
    }

    body.dark-mode .snapshot-chip:hover {
        border-color: #4a6e97;
        background: linear-gradient(180deg, #17314f 0%, #142b46 100%);
    }

    body.dark-mode .snapshot-chip.info,
    body.dark-mode .snapshot-chip.waiting,
    body.dark-mode .snapshot-chip.active,
    body.dark-mode .snapshot-chip.success,
    body.dark-mode .snapshot-chip.muted {
        background: linear-gradient(180deg, #132b46 0%, #11263f 100%);
    }

    body.dark-mode .rdv-action:hover,
    body.dark-mode .rdv-action-form button:hover {
        background: #173858;
        border-color: #4a78a8;
        color: #eff7ff;
    }

    body.dark-mode .rdv-timing-chip.waiting,
    body.dark-mode .rdv-priority-badge.attention {
        background: #3e2f1a;
        border-color: #76552b;
        color: #ffd691;
    }

    body.dark-mode .rdv-timing-chip.consultation {
        background: #18374a;
        border-color: #3b6b88;
        color: #a9e5ff;
    }

    body.dark-mode .rdv-timing-chip.delay,
    body.dark-mode .rdv-priority-badge.urgent {
        background: #432033;
        border-color: #8d4460;
        color: #ffc7d7;
    }

    .agenda-week-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(190px, 1fr));
        gap: 14px;
        min-width: 1180px;
        align-items: start;
        align-content: start;
    }

    .agenda-week-day,
    .agenda-month-cell {
        border: 1px solid #e7eef5;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.94);
        box-shadow: 0 22px 36px -34px rgba(15, 23, 42, 0.12);
        overflow: hidden;
        min-width: 0;
        align-self: start;
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .agenda-week-day:hover,
    .agenda-month-cell:hover {
        transform: translateY(-1px);
        border-color: #d9e4ee;
        box-shadow: 0 26px 40px -34px rgba(15, 23, 42, 0.16);
    }

    .agenda-week-day-head,
    .agenda-month-cell-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 14px 14px 12px;
        border-bottom: 1px solid #eef3f7;
        background: #fcfdfe;
    }

    .agenda-week-day-head strong,
    .agenda-month-cell-head span:first-child {
        color: #24374a;
        font-weight: 900;
        font-size: 13px;
        letter-spacing: -.01em;
    }

    .agenda-week-day-head span,
    .agenda-month-cell-head span {
        color: #8ba0b3;
        font-size: 11px;
    }

    .agenda-week-day-head.is-selected {
        background: linear-gradient(180deg, #f7fafc 0%, #fcfdfe 100%);
        box-shadow: inset 0 -1px 0 rgba(31, 111, 163, .08);
    }

    .agenda-week-count,
    .agenda-month-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 24px;
        height: 24px;
        padding: 0 7px;
        border-radius: 999px;
        border: 1px solid #e3ebf2;
        background: #f8fafc;
        color: #7d90a5;
        font-size: 11px;
        font-weight: 800;
        box-shadow: none;
    }

    .agenda-week-day-body,
    .agenda-month-cell-body {
        display: grid;
        gap: 8px;
        padding: 12px 12px 14px;
        align-content: start;
    }

    .agenda-week-day-body {
        min-height: 280px;
        grid-auto-rows: min-content;
    }

    .week-rdv,
    .month-rdv {
        border: 1px solid #e6edf3;
        border-left: 2px solid #c7d5e2;
        border-radius: 14px;
        padding: 8px 9px;
        background: #ffffff;
        text-decoration: none;
        color: #24374a;
        display: grid;
        gap: 4px;
        box-shadow: none;
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .week-rdv:hover,
    .month-rdv:hover {
        transform: translateY(-1px);
        border-color: #d6e1ea;
        background: #fcfdfe;
        box-shadow: 0 16px 24px -22px rgba(15, 23, 42, .16);
        text-decoration: none;
        color: #24374a;
    }

    .week-rdv.urgent,
    .month-rdv.urgent {
        border-left-color: #d7c4ca;
        background: #ffffff;
    }

    .week-rdv-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
    }

    .week-rdv-time {
        font-size: 13px;
        font-weight: 900;
        color: #35506d;
    }

    .week-rdv-patient {
        color: #102f56;
        font-weight: 800;
        line-height: 1.15;
        font-size: 0.92rem;
    }

    .week-rdv-meta,
    .week-rdv-doctor {
        color: #7d90a3;
        font-size: 10px;
        line-height: 1.2;
    }

    .week-rdv-doctor {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .week-rdv-doctor i {
        color: #a1b1c0;
        font-size: 10px;
    }

    .week-rdv-type {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        max-width: 100%;
        padding: 3px 8px;
        border-radius: 999px;
        border: 1px solid #d7e2eb;
        background: #f8fafc;
        color: #5f7288;
        font-size: 10px;
        font-weight: 700;
        line-height: 1.2;
    }

    .week-rdv-inline {
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
        flex-wrap: wrap;
    }

    .week-rdv-inline .week-rdv-meta {
        min-width: 0;
    }

    .week-rdv-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 1px;
    }

    .agenda-dense-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(220px, 1fr));
        gap: 14px;
        min-width: 1440px;
        align-items: start;
        align-content: start;
    }

    .agenda-dense-day {
        border: 1px solid #e4edf6;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 20px 34px -34px rgba(15, 23, 42, 0.16);
        overflow: hidden;
        min-width: 0;
    }

    .agenda-dense-day.is-selected {
        border-color: #cfe0f3;
        box-shadow: 0 22px 36px -34px rgba(37, 99, 235, .18);
    }

    .agenda-dense-day-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 14px 14px 12px;
        border-bottom: 1px solid #eef4f9;
        background: linear-gradient(180deg, #fcfdff 0%, #f8fbfe 100%);
    }

    .agenda-dense-day-head strong {
        display: block;
        color: #1c3550;
        font-size: 13px;
        font-weight: 900;
        line-height: 1.15;
    }

    .agenda-dense-day-head span {
        color: #7a8fa6;
        font-size: 11px;
        font-weight: 700;
    }

    .agenda-dense-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 26px;
        height: 26px;
        padding: 0 8px;
        border-radius: 999px;
        background: #f3f7fb;
        border: 1px solid #dbe6f1;
        color: #60768f;
        font-size: 11px;
        font-weight: 800;
    }

    .agenda-dense-day-body {
        display: grid;
        gap: 10px;
        padding: 12px;
        align-content: start;
        min-height: 620px;
    }

    .dense-rdv-card {
        --dense-accent: #14b8a6;
        position: relative;
        display: grid;
        gap: 6px;
        padding: 9px 10px 9px 13px;
        border-radius: 14px;
        border: 1px solid color-mix(in srgb, var(--dense-accent) 22%, #dce7f2);
        background: color-mix(in srgb, var(--dense-accent) 7%, #ffffff);
        box-shadow: 0 14px 22px -24px rgba(15, 23, 42, .22);
        cursor: pointer;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        outline: none;
    }

    .dense-rdv-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: var(--dense-accent);
    }

    .dense-rdv-card:hover,
    .dense-rdv-card:focus-visible {
        transform: translateY(-1px);
        box-shadow: 0 18px 28px -24px rgba(15, 23, 42, .24);
        border-color: color-mix(in srgb, var(--dense-accent) 34%, #dce7f2);
    }

    .dense-rdv-topline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .dense-rdv-time {
        color: #173454;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .02em;
    }

    .dense-rdv-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 8px;
        border-radius: 999px;
        background: rgba(255,255,255,.7);
        border: 1px solid rgba(255,255,255,.65);
        color: #49637d;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .dense-rdv-patient {
        color: #0f2d53;
        font-size: 13px;
        font-weight: 900;
        line-height: 1.2;
        word-break: break-word;
    }

    .dense-rdv-type {
        color: #274866;
        font-size: 11px;
        font-weight: 800;
    }

    .dense-rdv-doctor {
        color: #667f98;
        font-size: 10px;
        font-weight: 700;
        line-height: 1.3;
    }

    .dense-rdv-indicators {
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-end;
        margin-top: 0;
    }

    .dense-indicator {
        width: 26px;
        height: 26px;
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,.72);
        background: rgba(255,255,255,.76);
        color: #44627d;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
    }

    .dense-indicator.is-present {
        color: #15803d;
    }

    .dense-indicator.is-pending {
        color: #b45309;
    }

    .dense-rdv-hover-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        opacity: 0;
        transform: translateY(4px);
        pointer-events: none;
        transition: opacity .18s ease, transform .18s ease;
    }

    .dense-rdv-card:hover .dense-rdv-hover-actions,
    .dense-rdv-card:focus-visible .dense-rdv-hover-actions {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    .dense-hover-action {
        width: 30px;
        height: 30px;
        border-radius: 9px;
        border: 1px solid rgba(255,255,255,.74);
        background: rgba(255,255,255,.82);
        color: #274866;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: transform .18s ease, background .18s ease, color .18s ease;
    }

    .dense-hover-action:hover {
        transform: translateY(-1px);
        background: #ffffff;
        color: #12345f;
        text-decoration: none;
    }

    .dense-context-menu {
        position: fixed;
        z-index: 80;
        min-width: 190px;
        display: none;
        padding: 8px;
        border-radius: 14px;
        border: 1px solid #dbe6f1;
        background: #ffffff;
        box-shadow: 0 20px 36px -24px rgba(15, 23, 42, .28);
    }

    .dense-context-menu.is-open {
        display: grid;
        gap: 6px;
    }

    .dense-context-link {
        min-height: 34px;
        padding: 0 10px;
        border-radius: 10px;
        color: #3e5a74;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 700;
    }

    .dense-context-link:hover {
        background: #f6f9fc;
        color: #16324d;
        text-decoration: none;
    }

    .dense-act-bilan { --dense-accent: #8b5cf6; }
    .dense-act-consultation { --dense-accent: #14b8a6; }
    .dense-act-followup { --dense-accent: #ec4899; }
    .dense-act-first { --dense-accent: #f97316; }
    .dense-act-injection { --dense-accent: #ef4444; }
    .dense-act-chimio { --dense-accent: #2563eb; }
    .dense-act-scan { --dense-accent: #16a34a; }
    .dense-act-absence { --dense-accent: #94a3b8; }

    .dense-status-upcoming .dense-rdv-status { color: #1d4ed8; }
    .dense-status-waiting .dense-rdv-status { color: #b45309; }
    .dense-status-active .dense-rdv-status { color: #7c3aed; }
    .dense-status-done .dense-rdv-status { color: #15803d; }
    .dense-status-absent .dense-rdv-status,
    .dense-status-cancelled .dense-rdv-status { color: #6b7280; }

    .dense-add-link {
        min-height: 36px;
        font-size: 12px;
    }

    .dense-empty {
        min-height: 72px;
    }

    .agenda-month-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(170px, 1fr));
        gap: 14px;
        min-width: 1120px;
        align-items: start;
        align-content: start;
    }

    .agenda-month-cell {
        min-height: 210px;
    }

    .agenda-month-cell.is-muted {
        opacity: .72;
    }

    .agenda-month-cell.is-today {
        border-color: #d4e3ee;
        box-shadow: 0 22px 34px -30px rgba(31, 111, 163, .16);
        background: linear-gradient(180deg, #ffffff 0%, #fbfdfe 100%);
    }

    .month-rdv {
        padding: 9px 10px;
        gap: 4px;
    }

    .month-rdv-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .month-rdv strong {
        color: #4d657d;
        font-size: 12px;
    }

    .month-rdv span {
        color: #6f8498;
        font-size: 11px;
        line-height: 1.25;
        word-break: break-word;
    }

    .month-rdv-patient {
        font-weight: 800;
        color: #24374a;
    }

    .month-rdv-type {
        color: #8a9cad;
        font-size: 11px;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .month-rdv-status-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        flex: 0 0 auto;
        background: #a6b9cc;
        box-shadow: 0 0 0 2px rgba(166, 185, 204, 0.16);
    }

    .month-rdv-status-dot.status-attente {
        background: #bea885;
        box-shadow: 0 0 0 2px rgba(190, 168, 133, 0.18);
    }

    .month-rdv-status-dot.status-soins {
        background: #8da8a2;
        box-shadow: 0 0 0 2px rgba(141, 168, 162, 0.18);
    }

    .month-rdv-status-dot.status-vu {
        background: #a4b3bf;
        box-shadow: 0 0 0 2px rgba(164, 179, 191, 0.18);
    }

    .month-rdv-status-dot.status-absent,
    .month-rdv-status-dot.status-annule {
        background: #bda6ae;
        box-shadow: 0 0 0 2px rgba(189, 166, 174, 0.18);
    }

    .month-more-link {
        color: #5a7087;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }

    .month-add-link {
        min-height: 34px;
        font-size: 12px;
        padding: 8px 10px;
        background: #fafcfe;
    }

    .agenda-empty-day {
        min-height: 40px;
        border: 1px dashed #e3ebf2;
        border-radius: 12px;
        background: #fafcfe;
        color: #8a9cad;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 8px;
        transition: border-color .2s ease, background .2s ease, color .2s ease;
    }

    .agenda-empty-day:hover {
        border-color: #d4e0ea;
        background: #fcfdfe;
        color: #6e8396;
    }

    .agenda-empty-day.compact {
        min-height: 30px;
        font-size: 11px;
        border-style: solid;
        opacity: .82;
    }

    .agenda-week-day-body .agenda-empty-day {
        min-height: 56px;
    }

    .agenda-month-cell-body .agenda-empty-day.compact {
        min-height: 44px;
        font-size: 12px;
    }

    @media (max-width: 1320px) {
        .agenda-week-grid {
            grid-template-columns: repeat(7, minmax(172px, 1fr));
        }

        .agenda-dense-grid {
            grid-template-columns: repeat(7, minmax(200px, 1fr));
            min-width: 1360px;
        }

        .agenda-month-grid {
            grid-template-columns: repeat(7, minmax(158px, 1fr));
        }
    }

    @media (max-width: 980px) {
        .agenda-week-grid,
        .agenda-dense-grid,
        .agenda-month-grid {
            min-width: 940px;
        }

        .agenda-week-day-body {
            min-height: 230px;
        }
    }

    @media (max-width: 760px) {
        .agenda-week-grid,
        .agenda-dense-grid,
        .agenda-month-grid {
            min-width: 760px;
        }

        .agenda-month-cell {
            min-height: auto;
        }

        .week-rdv,
        .month-rdv {
            padding: 9px;
        }

        .rdv-action-stack,
        .week-rdv-actions {
            width: 100%;
        }

        .rdv-action-primary {
            flex: 1 1 auto;
            justify-content: center;
        }

        .agenda-week-day-head,
        .agenda-month-cell-head {
            padding: 11px 11px 10px;
        }

        .agenda-week-day-body,
        .agenda-month-cell-body {
            padding: 10px 10px 12px;
        }

        .agenda-dense-day-body {
            min-height: 460px;
            padding: 10px 10px 12px;
        }

        .dense-rdv-hover-actions {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }
    }

    body.dark-mode .agenda-week-day,
    body.dark-mode .agenda-dense-day,
    body.dark-mode .agenda-month-cell {
        background: rgba(17, 31, 51, 0.96);
        border-color: #263d58;
    }

    body.dark-mode .agenda-week-day:hover,
    body.dark-mode .agenda-dense-day:hover,
    body.dark-mode .agenda-month-cell:hover {
        border-color: #3b6490;
        box-shadow: 0 24px 30px -28px rgba(0, 0, 0, .46);
    }

    body.dark-mode .agenda-week-day-head,
    body.dark-mode .agenda-dense-day-head,
    body.dark-mode .agenda-month-cell-head {
        background: #12263d;
        border-bottom-color: #223a57;
    }

    body.dark-mode .week-rdv,
    body.dark-mode .dense-rdv-card,
    body.dark-mode .month-rdv,
    body.dark-mode .agenda-empty-day {
        background: #14283f;
        border-color: #26415f;
        color: #e3edf8;
    }

    body.dark-mode .week-rdv:hover,
    body.dark-mode .dense-rdv-card:hover,
    body.dark-mode .month-rdv:hover,
    body.dark-mode .agenda-empty-day:hover {
        background: #172d47;
        border-color: #335171;
    }

    body.dark-mode .week-rdv-meta,
    body.dark-mode .dense-rdv-doctor,
    body.dark-mode .month-rdv span,
    body.dark-mode .agenda-empty-day {
        color: #b7c9df;
    }

    body.dark-mode .agenda-dense-day-head strong,
    body.dark-mode .dense-rdv-patient,
    body.dark-mode .dense-rdv-time {
        color: #e3f0ff;
    }

    body.dark-mode .agenda-dense-count,
    body.dark-mode .dense-rdv-status,
    body.dark-mode .dense-indicator,
    body.dark-mode .dense-hover-action {
        background: #173450;
        border-color: #355985;
        color: #d6e6f8;
    }

    body.dark-mode .timeline-row {
        border-top-color: #20344c;
    }

    body.dark-mode .timeline-time {
        color: #86a3bf;
    }

    body.dark-mode .timeline-slot,
    body.dark-mode .timeline-slot.has-events {
        background: transparent;
    }

    body.dark-mode .timeline-slot .quick-add,
    body.dark-mode .month-add-link {
        background: #13263d;
        border-color: #2a4563;
        color: #a9c0da;
    }

    body.dark-mode .rdv-action-more,
    body.dark-mode .hero-action,
    body.dark-mode .rdv-more-link,
    body.dark-mode .rdv-more-menu button {
        background: #173153;
        border-color: #2e5a87;
        color: #d7e8fb;
    }

    body.dark-mode .rdv-action-more:hover,
    body.dark-mode .rdv-more-link:hover,
    body.dark-mode .rdv-more-menu button:hover {
        background: #1a395e;
        border-color: #46719f;
    }

    body.dark-mode .rdv-more-menu {
        background: #122840;
        border-color: #2b4d76;
    }

    body.dark-mode .dense-context-menu {
        background: #11263d;
        border-color: #29476f;
        box-shadow: 0 22px 34px -24px rgba(0, 0, 0, .6);
    }

    body.dark-mode .dense-context-link {
        color: #d4e8ff;
    }

    body.dark-mode .dense-context-link:hover {
        background: #173450;
        color: #ffffff;
    }

    /* Final toolbar cleanup */
    .mini-nav button,
    .date-controls > button:not(.btn-today),
    .btn-today,
    .view-btn,
    .agenda-layout-btn,
    .btn-new-rdv,
    .btn-apply,
    .btn-reset {
        transition:
            transform .18s ease,
            box-shadow .18s ease,
            border-color .18s ease,
            background .18s ease,
            color .18s ease,
            filter .18s ease;
    }

    .mini-nav button:focus-visible,
    .date-controls > button:not(.btn-today):focus-visible,
    .btn-today:focus-visible,
    .view-btn:focus-visible,
    .agenda-layout-btn:focus-visible,
    .btn-new-rdv:focus-visible,
    .btn-apply:focus-visible,
    .btn-reset:focus-visible {
        outline: none;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .14);
    }

    .agenda-top {
        display: grid;
        grid-template-columns: minmax(260px, 1.05fr) minmax(360px, 1fr) auto;
        align-items: center;
        gap: 14px;
        padding: 18px 20px;
    }

    .agenda-top--week {
        grid-template-columns: minmax(260px, .95fr) minmax(460px, 1.15fr);
    }

    .agenda-top--week .view-tools {
        grid-column: 1 / -1;
        justify-content: flex-start;
        row-gap: 10px;
    }

    .agenda-top--week .btn-new-rdv {
        margin-left: auto;
    }

    .agenda-top--week .date-controls {
        max-width: 100%;
    }

    .agenda-top--week .date-label {
        justify-content: flex-start;
        text-align: left;
    }

    .agenda-title {
        min-width: 0;
        flex: initial;
    }

    .agenda-title-row {
        gap: 12px;
    }

    .agenda-count-badge {
        min-height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding-inline: 14px;
    }

    .date-controls {
        display: grid;
        grid-template-columns: 44px minmax(220px, 1fr) 44px auto;
        align-items: center;
        justify-content: stretch;
        gap: 8px;
        min-width: 0;
        width: 100%;
        padding: 8px;
        border-radius: 16px;
    }

    .date-controls > button:not(.btn-today) {
        width: 44px;
        height: 44px;
        min-width: 44px;
        min-height: 44px;
        padding: 0;
        border-radius: 12px;
        font-size: 1.08rem;
        font-weight: 800;
    }

    .date-label {
        min-width: 0;
        width: 100%;
        padding: 0 16px;
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        border-radius: 12px;
    }

    .btn-today {
        min-width: 118px;
        min-height: 44px;
        padding: 0 16px;
        border-radius: 12px;
        font-size: .94rem;
        line-height: 1;
    }

    .view-tools {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        min-width: 0;
    }

    .view-toggle,
    .agenda-layout-toggle {
        min-height: 52px;
        align-items: center;
    }

    .view-btn,
    .agenda-layout-btn {
        min-height: 44px;
        padding: 0 16px;
        line-height: 1;
        font-size: .92rem;
        border-radius: 11px;
    }

    .btn-new-rdv {
        min-height: 48px;
        padding: 0 20px;
        line-height: 1;
        gap: 10px;
        flex: 0 0 auto;
    }

    .agenda-filters {
        grid-template-columns: minmax(280px, 1.15fr) minmax(220px, .95fr) auto;
        align-items: center;
    }

    .filter-inline {
        align-items: stretch;
    }

    .btn-apply,
    .btn-reset {
        min-height: 46px;
        padding: 0 18px;
        line-height: 1;
    }

    .btn-apply {
        box-shadow: 0 16px 24px -22px rgba(37, 99, 235, .52);
    }

    .btn-reset:hover,
    .btn-apply:hover,
    .agenda-layout-btn:hover,
    .btn-today:hover,
    .date-controls > button:not(.btn-today):hover,
    .mini-nav button:hover {
        transform: translateY(-1px);
    }

    @media (max-width: 1320px) {
        .agenda-top {
            grid-template-columns: minmax(240px, 1fr) minmax(320px, 1fr);
        }

        .view-tools {
            grid-column: 1 / -1;
            justify-content: flex-start;
        }

        .agenda-filters {
            grid-template-columns: 1fr 1fr;
        }

        .filter-actions {
            grid-column: 1 / -1;
            justify-content: flex-end;
        }
    }

    @media (max-width: 980px) {
        .agenda-top {
            grid-template-columns: 1fr;
            gap: 12px;
            padding: 15px 14px;
        }

        .agenda-title,
        .date-controls,
        .view-tools {
            width: 100%;
        }

        .date-controls {
            grid-template-columns: 44px minmax(0, 1fr) 44px;
        }

        .date-label {
            grid-column: 2;
        }

        .btn-today {
            grid-column: 1 / -1;
            width: 100%;
        }

        .view-tools {
            justify-content: stretch;
        }

        .view-toggle {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .agenda-layout-toggle {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .btn-new-rdv {
            width: 100%;
            justify-content: center;
        }

        .agenda-filters {
            grid-template-columns: 1fr;
            padding: 12px;
        }

        .filter-inline {
            flex-direction: column;
        }

        .filter-actions {
            width: 100%;
            justify-content: stretch;
        }
    }

    @media (max-width: 640px) {
        .agenda-shell {
            padding: 10px;
            border-radius: 18px;
        }

        .agenda-title {
            align-items: flex-start;
        }

        .agenda-title h1 {
            font-size: 1.55rem;
            line-height: 1.08;
        }

        .agenda-title p {
            font-size: .9rem;
        }

        .agenda-count-badge {
            min-height: 34px;
            font-size: .8rem;
            padding-inline: 12px;
        }

        .date-controls {
            gap: 6px;
            padding: 6px;
            border-radius: 14px;
        }

        .date-controls > button:not(.btn-today) {
            width: 42px;
            height: 42px;
            min-width: 42px;
            min-height: 42px;
        }

        .date-label {
            min-height: 42px;
            padding-inline: 12px;
            font-size: .9rem;
            white-space: normal;
            line-height: 1.25;
        }

        .view-btn,
        .agenda-layout-btn,
        .btn-today,
        .btn-new-rdv,
        .btn-apply,
        .btn-reset {
            min-height: 44px;
            font-size: .9rem;
        }

        .filter-actions {
            display: grid;
            grid-template-columns: 1fr;
        }
    }

    .agenda-sms-flash {
        margin-bottom: 14px;
        padding: 12px 14px;
        border-radius: 14px;
        border: 1px solid #cde0f5;
        background: linear-gradient(180deg, #f4f9ff 0%, #edf5ff 100%);
        color: #1c446f;
        font-weight: 700;
        box-shadow: 0 16px 24px -32px rgba(20, 70, 120, .35);
    }

    .agenda-sms-flash.is-error {
        border-color: #f1c6cc;
        background: linear-gradient(180deg, #fff7f8 0%, #fff0f2 100%);
        color: #a63a46;
    }

    .agenda-sms-modal {
        position: fixed;
        inset: 0;
        z-index: 12000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    .agenda-sms-modal.is-open {
        display: flex;
    }

    .agenda-sms-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, .52);
        backdrop-filter: blur(3px);
    }

    .agenda-sms-dialog {
        position: relative;
        z-index: 1;
        width: min(100%, 760px);
        max-height: calc(100vh - 48px);
        overflow: auto;
    }

    .agenda-sms-shell {
        border-radius: 24px;
        border: 1px solid #d7e3f0;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 30px 44px -34px rgba(15, 23, 42, .45);
        overflow: hidden;
    }

    .agenda-sms-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 22px 24px 18px;
        border-bottom: 1px solid #e3ebf5;
        background: linear-gradient(135deg, rgba(37, 99, 235, .08) 0%, rgba(59, 130, 246, .04) 55%, rgba(255, 255, 255, .92) 100%);
    }

    .agenda-sms-kicker {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 999px;
        background: #eef5ff;
        border: 1px solid #d6e4f8;
        color: #22548d;
        font-size: .78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .agenda-sms-head-copy h2 {
        margin: 10px 0 6px;
        color: #12345f;
        font-size: 1.45rem;
        font-weight: 800;
    }

    .agenda-sms-head-copy p {
        margin: 0;
        color: #637b94;
        font-size: .95rem;
    }

    .agenda-sms-close {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        border: 1px solid #d7e3f0;
        background: linear-gradient(180deg, #ffffff 0%, #f3f8ff 100%);
        color: #355273;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all .18s ease;
    }

    .agenda-sms-close:hover {
        transform: translateY(-1px);
        background: linear-gradient(180deg, #ffffff 0%, #eaf2fc 100%);
        color: #1b4d87;
    }

    .agenda-sms-alert {
        margin: 18px 24px 0;
        padding: 12px 14px;
        border-radius: 14px;
        border: 1px solid #f1c6cc;
        background: #fff3f5;
        color: #a63a46;
        font-weight: 700;
        line-height: 1.5;
    }

    .agenda-sms-form {
        padding: 20px 24px 24px;
        display: grid;
        gap: 18px;
    }

    .agenda-sms-summary {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .agenda-sms-summary-item {
        padding: 14px 16px;
        border-radius: 16px;
        border: 1px solid #dce7f3;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    .agenda-sms-summary-item span {
        display: block;
        margin-bottom: 6px;
        color: #6880a0;
        font-size: .77rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .agenda-sms-summary-item strong {
        color: #173454;
        font-size: .96rem;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .agenda-sms-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(260px, .9fr);
        gap: 16px;
        align-items: start;
    }

    .agenda-sms-field {
        display: grid;
        gap: 8px;
    }

    .agenda-sms-field-full {
        gap: 10px;
    }

    .agenda-sms-field span {
        color: #173454;
        font-size: .86rem;
        font-weight: 800;
    }

    .agenda-sms-field input,
    .agenda-sms-field textarea {
        width: 100%;
        border-radius: 14px;
        border: 1px solid #d6e2ee;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        color: #183b62;
        padding: 13px 14px;
        font-size: .96rem;
        font-weight: 600;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .agenda-sms-field textarea {
        min-height: 148px;
        resize: vertical;
    }

    .agenda-sms-field input:focus,
    .agenda-sms-field textarea:focus {
        border-color: #8bb4ef;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);
        outline: none;
    }

    .agenda-sms-field small,
    .agenda-sms-field-meta small {
        color: #6d83a0;
        font-size: .8rem;
        line-height: 1.45;
    }

    .agenda-sms-preview-card {
        padding: 16px;
        border-radius: 18px;
        border: 1px solid #dce7f3;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9ff 100%);
        display: grid;
        gap: 10px;
        min-height: 100%;
    }

    .agenda-sms-preview-label {
        color: #6880a0;
        font-size: .76rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .agenda-sms-preview-phone {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 8px 12px;
        border-radius: 999px;
        border: 1px solid #d7e3f0;
        background: #ffffff;
        color: #214f8b;
        font-weight: 800;
        font-size: .9rem;
        overflow-wrap: anywhere;
    }

    .agenda-sms-preview-message {
        min-height: 124px;
        padding: 14px;
        border-radius: 18px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4fbe 100%);
        color: #ffffff;
        font-size: .95rem;
        line-height: 1.6;
        white-space: pre-wrap;
        box-shadow: 0 20px 30px -26px rgba(37, 99, 235, .5);
    }

    .agenda-sms-preview-message.is-empty {
        background: linear-gradient(180deg, #eef4fb 0%, #e7eff9 100%);
        color: #6782a7;
        box-shadow: none;
    }

    .agenda-sms-field-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .agenda-sms-field-meta strong {
        color: #214f8b;
        font-size: .85rem;
        font-weight: 800;
    }

    .agenda-sms-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .agenda-sms-btn {
        min-height: 46px;
        border-radius: 14px;
        border: 1px solid #d7e3f0;
        padding: 0 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: 800;
        transition: all .18s ease;
    }

    .agenda-sms-btn-muted {
        background: linear-gradient(180deg, #ffffff 0%, #f3f8ff 100%);
        color: #536b86;
    }

    .agenda-sms-btn-muted:hover {
        background: linear-gradient(180deg, #ffffff 0%, #eaf2fc 100%);
        color: #36506f;
    }

    .agenda-sms-btn-primary {
        border-color: transparent;
        background: linear-gradient(135deg, #2563eb 0%, #1d4fbe 100%);
        color: #ffffff;
        box-shadow: 0 18px 28px -24px rgba(37, 99, 235, .48);
    }

    .agenda-sms-btn-primary:hover {
        transform: translateY(-1px);
        background: linear-gradient(135deg, #2e6ef0 0%, #214fbf 100%);
        color: #ffffff;
    }

    .agenda-sms-btn[disabled] {
        opacity: .7;
        cursor: wait;
        transform: none;
    }

    @media (max-width: 768px) {
        .agenda-sms-modal {
            padding: 14px;
        }

        .agenda-sms-dialog {
            width: 100%;
            max-height: calc(100vh - 28px);
        }

        .agenda-sms-head,
        .agenda-sms-form {
            padding-left: 16px;
            padding-right: 16px;
        }

        .agenda-sms-summary,
        .agenda-sms-grid {
            grid-template-columns: 1fr;
        }

        .agenda-sms-actions {
            flex-direction: column-reverse;
        }

        .agenda-sms-btn {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .agenda-sms-head {
            padding-top: 16px;
            padding-bottom: 14px;
        }

        .agenda-sms-head-copy h2 {
            font-size: 1.22rem;
        }

        .agenda-sms-field input,
        .agenda-sms-field textarea {
            font-size: .92rem;
        }

        .agenda-sms-field-meta {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    body.dark-mode .agenda-sms-flash {
        border-color: #355978;
        background: linear-gradient(180deg, #173251 0%, #12304c 100%);
        color: #d5e7ff;
    }

    body.dark-mode .agenda-sms-flash.is-error {
        border-color: #7b3b46;
        background: #3a2327;
        color: #ffd1d8;
    }

    body.dark-mode .agenda-sms-shell {
        border-color: #2f4b67;
        background: linear-gradient(180deg, #102136 0%, #0f1d31 100%);
        box-shadow: 0 30px 44px -34px rgba(0, 0, 0, .72);
    }

    body.dark-mode .agenda-sms-head {
        border-bottom-color: #29435f;
        background: linear-gradient(135deg, rgba(37, 99, 235, .18) 0%, rgba(15, 29, 49, .96) 60%);
    }

    body.dark-mode .agenda-sms-kicker,
    body.dark-mode .agenda-sms-preview-phone,
    body.dark-mode .agenda-sms-summary-item,
    body.dark-mode .agenda-sms-preview-card {
        border-color: #2f4b67;
        background: #11263e;
        color: #dbeafe;
    }

    body.dark-mode .agenda-sms-head-copy h2,
    body.dark-mode .agenda-sms-summary-item strong,
    body.dark-mode .agenda-sms-field span,
    body.dark-mode .agenda-sms-field-meta strong {
        color: #e5edff;
    }

    body.dark-mode .agenda-sms-head-copy p,
    body.dark-mode .agenda-sms-summary-item span,
    body.dark-mode .agenda-sms-field small,
    body.dark-mode .agenda-sms-field-meta small,
    body.dark-mode .agenda-sms-preview-label {
        color: #9fb3cf;
    }

    body.dark-mode .agenda-sms-close,
    body.dark-mode .agenda-sms-btn-muted,
    body.dark-mode .agenda-sms-field input,
    body.dark-mode .agenda-sms-field textarea {
        border-color: #355978;
        background: #111f33;
        color: #e5edff;
    }

    body.dark-mode .agenda-sms-preview-message.is-empty {
        background: #13263f;
        color: #9fb3cf;
    }

    body.dark-mode .agenda-sms-alert {
        border-color: #7b3b46;
        background: #3a2327;
        color: #ffd1d8;
    }

    /* Final polish: header controls and RDV actions */
    .date-controls {
        grid-template-columns: 46px minmax(240px, 1fr) 46px max-content;
        align-items: stretch;
        justify-content: start;
        gap: 6px;
        padding: 6px;
        border-radius: 18px;
        border: 1px solid #dbe7f3;
        background: linear-gradient(180deg, rgba(255, 255, 255, .98) 0%, rgba(243, 248, 255, .96) 100%);
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, .9),
            0 20px 34px -32px rgba(37, 99, 235, .45);
    }

    .date-controls > button:not(.btn-today) {
        width: 46px;
        height: 46px;
        min-width: 46px;
        min-height: 46px;
        border-radius: 13px;
        border-color: #d4e2f1;
        background: linear-gradient(180deg, #ffffff 0%, #f4f8fd 100%);
        color: #1d4e80;
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, .92),
            0 12px 20px -22px rgba(15, 23, 42, .34);
    }

    .date-label {
        min-height: 46px;
        padding-inline: 18px;
        border: 1px solid #dbe7f3;
        background: rgba(255, 255, 255, .94);
        color: #153f69;
        font-size: .97rem;
        font-weight: 800;
        letter-spacing: -.01em;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .86);
    }

    .btn-today {
        justify-self: start;
        min-width: 132px;
        min-height: 46px;
        padding: 0 18px;
        border-radius: 13px;
        border: 1px solid #d4e2f1;
        background: linear-gradient(180deg, #ffffff 0%, #edf4fb 100%);
        color: #1c4f81;
        font-size: .93rem;
        font-weight: 800;
        letter-spacing: -.01em;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, .94),
            0 16px 24px -26px rgba(37, 99, 235, .44);
    }

    .btn-today:hover,
    .date-controls > button:not(.btn-today):hover {
        border-color: #bfd4ea;
        background: linear-gradient(180deg, #ffffff 0%, #eaf3fd 100%);
        color: #133d68;
    }

    .btn-today:active,
    .date-controls > button:not(.btn-today):active {
        transform: translateY(0);
    }

    .week-rdv-actions {
        margin-top: 8px;
    }

    .timeline-slot,
    .rdv-card,
    .agenda-week-grid {
        position: relative;
        overflow: visible;
    }

    .agenda-week-day {
        position: relative;
        overflow: visible;
        z-index: 0;
    }

    .agenda-week-day-body {
        overflow: visible;
    }

    .rdv-card {
        z-index: 0;
    }

    .week-rdv {
        position: relative;
        z-index: 0;
    }

    .timeline-slot:has(.rdv-more[open]),
    .rdv-card:has(.rdv-more[open]),
    .week-rdv:has(.rdv-more[open]),
    .agenda-week-day:has(.rdv-more[open]) {
        z-index: 40;
    }

    .rdv-action-stack {
        align-items: stretch;
        gap: 10px;
        max-width: 100%;
    }

    .rdv-action-primary {
        min-height: 36px;
        padding: 0 12px;
        border-radius: 11px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .01em;
        box-shadow: 0 14px 24px -20px rgba(31, 111, 163, .58);
    }

    .rdv-action-primary i {
        font-size: .84rem;
    }

    .rdv-action-more {
        width: 36px;
        min-height: 36px;
        border-radius: 11px;
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, .88),
            0 12px 20px -20px rgba(15, 23, 42, .3);
    }

    .rdv-more {
        z-index: 2;
    }

    .rdv-more[open] {
        z-index: 50;
    }

    .rdv-more-menu {
        min-width: 216px;
        padding: 10px;
        border-radius: 16px;
        border-color: #d8e4f1;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow:
            0 24px 36px -24px rgba(15, 23, 42, .28),
            0 10px 22px -26px rgba(37, 99, 235, .26);
        gap: 8px;
    }

    .rdv-more-link,
    .rdv-more-menu button {
        min-height: 40px;
        padding: 0 12px;
        border: 1px solid #e0e9f3;
        border-radius: 11px;
        background: #ffffff;
        color: #36526e;
        font-size: 12px;
        font-weight: 800;
        line-height: 1.15;
        justify-content: flex-start;
        white-space: normal;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .92);
        transition:
            transform .18s ease,
            border-color .18s ease,
            background .18s ease,
            color .18s ease,
            box-shadow .18s ease;
    }

    .rdv-more-link i,
    .rdv-more-menu button i {
        width: 15px;
        flex: 0 0 15px;
        text-align: center;
        color: currentColor;
    }

    .rdv-more-link span,
    .rdv-more-menu button span,
    .dense-context-link span {
        min-width: 0;
    }

    .rdv-more-link:hover,
    .rdv-more-menu button:hover {
        transform: translateY(-1px);
        background: linear-gradient(180deg, #ffffff 0%, #f3f8ff 100%);
        border-color: #c9daeb;
        color: #143b64;
        text-decoration: none;
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, .95),
            0 14px 20px -24px rgba(15, 23, 42, .3);
    }

    .rdv-more-menu form {
        width: 100%;
    }

    .rdv-more-link--dossier {
        background: linear-gradient(180deg, #f6fbff 0%, #eef5ff 100%);
        border-color: #cfe0f3;
        color: #195086;
    }

    .rdv-more-link--consultation,
    .rdv-more-button--finish {
        background: linear-gradient(180deg, #f3fbf9 0%, #e8f7f2 100%);
        border-color: #cfe7dc;
        color: #0f6b56;
    }

    .rdv-more-button--start {
        background: linear-gradient(180deg, #f3f8ff 0%, #eaf3ff 100%);
        border-color: #cfe0f4;
        color: #18508a;
    }

    .rdv-more-button--cancel {
        background: linear-gradient(180deg, #fff7f8 0%, #fff0f2 100%);
        border-color: #efc8cf;
        color: #b2434f;
    }

    .dense-context-menu {
        min-width: 208px;
        padding: 10px;
        border-radius: 16px;
        border-color: #d8e4f1;
        box-shadow:
            0 24px 38px -24px rgba(15, 23, 42, .28),
            0 10px 22px -26px rgba(37, 99, 235, .26);
    }

    .dense-context-link {
        min-height: 38px;
        padding: 0 12px;
        border: 1px solid #e0e9f3;
        border-radius: 11px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        color: #36526e;
        font-size: 12px;
        font-weight: 800;
        white-space: normal;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .92);
        transition:
            transform .18s ease,
            border-color .18s ease,
            background .18s ease,
            color .18s ease,
            box-shadow .18s ease;
    }

    .dense-context-link:hover {
        transform: translateY(-1px);
        background: linear-gradient(180deg, #ffffff 0%, #f3f8ff 100%);
        border-color: #c9daeb;
        color: #143b64;
        text-decoration: none;
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, .95),
            0 14px 20px -24px rgba(15, 23, 42, .3);
    }

    @media (max-width: 980px) {
        .date-controls {
            grid-template-columns: 46px minmax(0, 1fr) 46px;
        }

        .btn-today {
            grid-column: 1 / -1;
            width: 100%;
            justify-self: stretch;
        }

        .agenda-top--week .btn-new-rdv {
            margin-left: 0;
        }

        .agenda-top--week .date-label {
            justify-content: center;
            text-align: center;
        }
    }

    @media (max-width: 760px) {
        .rdv-action-stack {
            width: 100%;
            gap: 8px;
        }

        .rdv-action-primary {
            flex: 1 1 auto;
        }

        .rdv-more-menu {
            left: 0;
            right: auto;
            min-width: min(216px, calc(100vw - 48px));
        }

        .timeline-slot:has(.rdv-more[open]),
        .rdv-card:has(.rdv-more[open]),
        .week-rdv:has(.rdv-more[open]),
        .agenda-week-day:has(.rdv-more[open]) {
            z-index: 60;
        }

        .dense-context-menu {
            min-width: min(208px, calc(100vw - 32px));
        }
    }

    @media (max-width: 640px) {
        .date-controls {
            gap: 6px;
            padding: 6px;
            border-radius: 16px;
        }

        .date-controls > button:not(.btn-today),
        .date-label,
        .btn-today {
            min-height: 44px;
        }

        .date-controls > button:not(.btn-today) {
            width: 44px;
            height: 44px;
            min-width: 44px;
        }

        .date-label {
            font-size: .89rem;
            padding-inline: 12px;
        }

        .btn-today {
            min-width: 100%;
            font-size: .9rem;
        }

        .agenda-top--week .view-tools {
            justify-content: stretch;
        }

        .rdv-more-menu,
        .dense-context-menu {
            min-width: min(100%, calc(100vw - 28px));
        }
    }

    body.dark-mode .date-controls {
        border-color: #2c4f75;
        background: linear-gradient(180deg, rgba(16, 37, 60, .96) 0%, rgba(19, 43, 69, .94) 100%);
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, .04),
            0 22px 34px -32px rgba(0, 0, 0, .62);
    }

    body.dark-mode .date-controls > button:not(.btn-today),
    body.dark-mode .date-label,
    body.dark-mode .btn-today {
        border-color: #355b84;
        color: #dbe9f8;
    }

    body.dark-mode .date-controls > button:not(.btn-today) {
        background: linear-gradient(180deg, #173559 0%, #12304f 100%);
    }

    body.dark-mode .date-label {
        background: rgba(15, 34, 55, .94);
        color: #f2f7ff;
    }

    body.dark-mode .btn-today {
        background: linear-gradient(180deg, #17385d 0%, #133152 100%);
        color: #eff6ff;
    }

    body.dark-mode .btn-today:hover,
    body.dark-mode .date-controls > button:not(.btn-today):hover {
        border-color: #4a759f;
        background: linear-gradient(180deg, #1b416a 0%, #16385c 100%);
        color: #ffffff;
    }

    body.dark-mode .rdv-more-menu,
    body.dark-mode .dense-context-menu {
        background: linear-gradient(180deg, #10253c 0%, #142f4d 100%);
        border-color: #2f547e;
        box-shadow:
            0 26px 40px -24px rgba(0, 0, 0, .62),
            0 10px 24px -28px rgba(15, 23, 42, .4);
    }

    body.dark-mode .rdv-more-link,
    body.dark-mode .rdv-more-menu button,
    body.dark-mode .dense-context-link {
        background: #173454;
        border-color: #315a85;
        color: #dbe9f8;
    }

    body.dark-mode .rdv-more-link:hover,
    body.dark-mode .rdv-more-menu button:hover,
    body.dark-mode .dense-context-link:hover {
        background: #1a3c61;
        border-color: #4773a0;
        color: #ffffff;
    }

    body.dark-mode .rdv-more-link--dossier,
    body.dark-mode .rdv-more-button--start {
        background: linear-gradient(180deg, #173a60 0%, #153451 100%);
        border-color: #406992;
        color: #e6f1ff;
    }

    body.dark-mode .rdv-more-link--consultation,
    body.dark-mode .rdv-more-button--finish {
        background: linear-gradient(180deg, #153a37 0%, #12312f 100%);
        border-color: #32695f;
        color: #c7f2e3;
    }

    body.dark-mode .rdv-more-button--cancel {
        background: linear-gradient(180deg, #44222b 0%, #371a22 100%);
        border-color: #7b4050;
        color: #ffd5dc;
    }
</style>

<div class="agenda-shell">
    <div class="agenda-grid">
        <aside class="agenda-side">
            <div class="ag-card mini-cal-body agenda-side-card">
                <div class="agenda-side-card-header">
                    <span class="side-section-label"><i class="fas fa-calendar-day"></i> Navigation</span>
                    <strong>Calendrier rapide</strong>
                </div>
                <div class="mini-cal-head">
                    <h3 id="miniMonthLabel">Fevrier 2026</h3>
                    <div class="mini-nav">
                        <button type="button" onclick="previousMonth()">&lsaquo;</button>
                        <button type="button" onclick="nextMonth()">&rsaquo;</button>
                    </div>
                </div>
                <div class="mini-weekdays">
                    <div>Lu</div><div>Ma</div><div>Me</div><div>Je</div><div>Ve</div><div>Sa</div><div>Di</div>
                </div>
                <div class="mini-days" id="miniCalendarDays"></div>
            </div>

            <div class="ag-card agenda-side-card">
                <div class="agenda-side-card-header">
                    <span class="side-section-label"><i class="fas fa-chart-line"></i> Journee</span>
                    <strong>Indicateurs cliniques</strong>
                </div>
                <div class="agenda-metrics">
                    <div class="metric">
                        <div class="metric-top">
                            <div class="metric-label">En attente</div>
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="metric-value">{{ $waitingCount }}</div>
                        <div class="metric-meta">Patients a appeler</div>
                    </div>
                    <div class="metric">
                        <div class="metric-top">
                            <div class="metric-label">En consultation</div>
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <div class="metric-value" style="color:#0b7ac7;">{{ $inProgressCount }}</div>
                        <div class="metric-meta">Flux en cours</div>
                    </div>
                    <div class="metric">
                        <div class="metric-top">
                            <div class="metric-label">Termines</div>
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="metric-value" style="color:#0f9f74;">{{ $completedTodayCount }}</div>
                        <div class="metric-meta">Consultations finalisees</div>
                    </div>
                    <div class="metric">
                        <div class="metric-top">
                            <div class="metric-label">Retards</div>
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                        <div class="metric-value" style="color:#d97706;">{{ $delayedCount }}</div>
                        <div class="metric-meta">
                            {{ $averageDurationToday > 0 ? $averageDurationToday . ' min en moyenne' : 'Aucune moyenne disponible' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="ag-card upcoming agenda-side-card">
                <div class="agenda-side-card-header upcoming-header">
                    <span class="side-section-label"><i class="fas fa-clock"></i> Priorites</span>
                    <strong>Prochains rendez-vous</strong>
                </div>
                <div class="up-list">
                    @forelse($upcomingAppointments as $upcoming)
                        <div class="up-item {{ $upcoming->display_class }}">
                            <div class="up-item-head">
                                <div class="up-avatar">
                                    @if($upcoming->display_photo)
                                        <img src="{{ $upcoming->display_photo }}" alt="Photo patient">
                                    @else
                                        <span class="up-avatar-fallback">{{ $upcoming->display_initials }}</span>
                                    @endif
                                </div>
                                <div class="up-main">
                                    <div class="up-time">{{ $upcoming->date_heure->format('d/m H:i') }}</div>
                                    <div class="up-patient">{{ $upcoming->display_patient }}</div>
                                    <div class="up-meta">
                                        <span><i class="fas fa-user-doctor"></i> {{ $upcoming->display_doctor }}</span>
                                        @if($upcoming->display_motif !== '')
                                            <span class="up-chip">{{ $upcoming->display_motif }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="up-item">
                            <div class="up-doc">{{ __('messages.agenda.no_upcoming') }}</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </aside>

        <main class="agenda-main">
            <div class="agenda-top {{ $currentView === 'week' ? 'agenda-top--week' : '' }}">
                <div class="agenda-title">
                    <i class="fas fa-calendar-check"></i>
                    <div>
                        <div class="agenda-title-row">
                            <h1>{{ __('messages.agenda.title') }}</h1>
                            <span class="agenda-count-badge">{{ $displayAppointments->count() }} RDV</span>
                        </div>
                        <p>{{ $displayDescription }}</p>
                    </div>
                </div>

                <div class="date-controls">
                    <button type="button" onclick="previousDate()">&lsaquo;</button>
                    <div class="date-label" id="currentDate">{{ $selectedDate->translatedFormat('l d F Y') }}</div>
                    <button type="button" onclick="nextDate()">&rsaquo;</button>
                    <button type="button" class="btn-today" id="todayBtn">Aujourd'hui</button>
                </div>

                <div class="view-tools">
                    <div class="view-toggle">
                        <button class="view-btn {{ $currentView === 'day' ? 'active' : '' }}" data-view="day">Jour</button>
                        <button class="view-btn {{ $currentView === 'week' ? 'active' : '' }}" data-view="week">Semaine</button>
                        <button class="view-btn {{ $currentView === 'month' ? 'active' : '' }}" data-view="month">Mois</button>
                    </div>
                    @if($currentView === 'week')
                        <div class="agenda-layout-toggle" role="group" aria-label="Mode de vue semaine">
                            <a href="{{ route('agenda.index', ['date' => $selectedDate->format('Y-m-d'), 'view' => 'week', 'layout' => 'standard', 'medecin_id' => $selectedMedecinId ?: null, 'statut' => $selectedStatut ?: null, 'search' => $searchTerm ?: null]) }}"
                               class="agenda-layout-btn {{ $weekLayout === 'standard' ? 'active' : '' }}">
                                Vue standard
                            </a>
                            <a href="{{ route('agenda.index', ['date' => $selectedDate->format('Y-m-d'), 'view' => 'week', 'layout' => 'dense', 'medecin_id' => $selectedMedecinId ?: null, 'statut' => $selectedStatut ?: null, 'search' => $searchTerm ?: null]) }}"
                               class="agenda-layout-btn {{ $weekLayout === 'dense' ? 'active' : '' }}">
                                Vue dense medicale
                            </a>
                        </div>
                    @endif
                    <a href="{{ route('rendezvous.create') }}" class="btn-new-rdv" id="newRdvBtn">
                        <i class="fas fa-plus"></i> {{ __('messages.agenda.new_appointment') }}
                    </a>
                </div>
            </div>

            <form class="agenda-filters" method="GET" action="{{ route('agenda.index') }}">
                <input type="hidden" name="date" value="{{ $selectedDate->format('Y-m-d') }}">
                <input type="hidden" name="view" value="{{ $currentView }}">
                @if($currentView === 'week' && $weekLayout === 'dense')
                    <input type="hidden" name="layout" value="dense">
                @endif
                <div class="filter-inline">
                    <select name="medecin_id">
                        <option value="">{{ __('messages.agenda.all_doctors') }}</option>
                        @foreach($medecins as $medecin)
                            <option value="{{ $medecin->id }}" {{ (string) $selectedMedecinId === (string) $medecin->id ? 'selected' : '' }}>
                                Dr. {{ trim(($medecin->prenom ? $medecin->prenom . ' ' : '') . $medecin->nom) }}
                            </option>
                        @endforeach
                    </select>
                    <select name="statut">
                        <option value="">Tous les statuts</option>
                        @foreach($statusOptions as $statusValue => $statusLabel)
                            <option value="{{ $statusValue }}" {{ (string) $selectedStatut === (string) $statusValue ? 'selected' : '' }}>
                                {{ $statusLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-inline">
                    <input type="text" name="search" value="{{ $searchTerm }}" placeholder="{{ __('messages.common.search_patient') }}">
                </div>
                <div class="filter-actions">
                    <button class="btn-apply" type="submit">Appliquer</button>
                    <a class="btn-reset" href="{{ route('agenda.index', ['date' => $selectedDate->format('Y-m-d'), 'view' => $currentView, 'layout' => $currentView === 'week' && $weekLayout === 'dense' ? 'dense' : null]) }}">{{ __('messages.common.reset') }}</a>
                </div>
            </form>

            <div class="agenda-snapshot">
                <div class="snapshot-chip info">
                    <span>{{ $snapshotPrimaryLabel }}</span>
                    <strong>{{ $displayAppointments->count() }}</strong>
                </div>
                <div class="snapshot-chip waiting">
                    <span>En attente</span>
                    <strong>{{ $waitingCount }}</strong>
                </div>
                <div class="snapshot-chip active">
                    <span>En consultation</span>
                    <strong>{{ $inProgressCount }}</strong>
                </div>
                <div class="snapshot-chip success">
                    <span>{{ __('messages.agenda.completed') }}</span>
                    <strong>{{ $completedTodayCount }}</strong>
                </div>
                <div class="snapshot-chip muted">
                    <span>Absents</span>
                    <strong>{{ $absentTodayCount }}</strong>
                </div>
            </div>

            <div class="agenda-body" id="timelineContainer">
                @if($currentView === 'day')
                @for($hour = $startHour; $hour < $endHour; $hour++)
                    @php
                        $hourKey = sprintf('%02d', $hour);
                        $hourAppointments = $appointmentsByHour->get($hourKey, collect());
                        $slotCreateUrl = route('rendezvous.create', ['date' => $selectedDate->format('Y-m-d'), 'heure' => sprintf('%02d:00', $hour)]);
                    @endphp
                    <div class="timeline-row">
                        <div class="timeline-time">{{ sprintf('%02d:00', $hour) }}</div>
                        <div class="timeline-slot {{ $hourAppointments->isNotEmpty() ? 'has-events' : 'is-empty' }}"
                             @if($hourAppointments->isEmpty())
                                 role="button"
                                 tabindex="0"
                                 data-create-url="{{ $slotCreateUrl }}"
                             @endif>
                            @foreach($hourAppointments as $rdv)
                                @php
                                    $normalizedStatus = \App\Models\RendezVous::normalizeStatus((string) $rdv->statut);
                                    $typeRaw = mb_strtolower(trim((string) ($rdv->type ?? '')), 'UTF-8');
                                    $motifRaw = mb_strtolower(trim((string) ($rdv->motif ?? '')), 'UTF-8');
                                    $combinedType = $typeRaw . ' ' . $motifRaw;
                                    $patientName = trim((string) optional($rdv->patient)->prenom . ' ' . (string) optional($rdv->patient)->nom);
                                    $patientName = $patientName !== '' ? $patientName : 'Patient inconnu';
                                    $doctorName = trim((string) optional($rdv->medecin)->prenom . ' ' . (string) optional($rdv->medecin)->nom);
                                    $doctorName = $doctorName !== '' ? 'Dr. ' . $doctorName : 'Medecin inconnu';
                                    $motifLabel = trim((string) ($rdv->type ?: $rdv->motif));
                                    $motifLabel = $motifLabel !== '' ? $motifLabel : 'Consultation generale';
                                    $patientPhotoUrl = optional($rdv->patient)->photo
                                        ? asset('storage/' . optional($rdv->patient)->photo)
                                        : null;
                                    $patientInitials = trim(
                                        mb_substr((string) optional($rdv->patient)->prenom, 0, 1, 'UTF-8')
                                        . mb_substr((string) optional($rdv->patient)->nom, 0, 1, 'UTF-8')
                                    );
                                    $patientInitials = $patientInitials !== '' ? mb_strtoupper($patientInitials, 'UTF-8') : 'PT';
                                    $patientMeta = trim((string) optional($rdv->patient)->numero_dossier);
                                    $patientMeta = $patientMeta !== '' ? $patientMeta : 'ID ' . $rdv->patient_id;
                                    $patientPhone = trim((string) optional($rdv->patient)->telephone);
                                    $isUrgent = str_contains($combinedType, 'urgence');
                                    $delayMinutes = (!in_array($normalizedStatus, ['vu', 'absent', 'annule'], true) && $rdv->date_heure->isPast())
                                        ? $rdv->date_heure->diffInMinutes(now())
                                        : 0;
                                    $waitingMinutes = $rdv->arrived_at ? $rdv->arrived_at->diffInMinutes(now()) : null;
                                    $consultationMinutes = $rdv->consultation_started_at ? $rdv->consultation_started_at->diffInMinutes(now()) : null;
                                    $dossierUrl = $rdv->patient_id ? route('patients.show', $rdv->patient_id) : null;
                                    $smsUrl = route('sms.create', ['rendezvous_id' => $rdv->id]);
                                    $factureUrl = $rdv->patient_id ? route('factures.create', ['patient_id' => $rdv->patient_id]) : null;
                                    $consultationUrl = $rdv->patient_id
                                        ? route('consultations.create', [
                                            'patient_id' => $rdv->patient_id,
                                            'medecin_id' => $rdv->medecin_id,
                                            'rendez_vous_id' => $rdv->id,
                                        ])
                                        : null;
                                    $smsRendezvousLabel = $rdv->date_heure->format('d/m/Y H:i') . ($doctorName !== 'Medecin inconnu' ? ' • ' . $doctorName : '');

                                    $motifClass = 'motif-consultation';
                                    if (str_contains($combinedType, 'urgence')) {
                                        $motifClass = 'motif-urgence';
                                    } elseif (str_contains($combinedType, 'controle') || str_contains($combinedType, 'contrôle')) {
                                        $motifClass = 'motif-controle';
                                    } elseif (str_contains($combinedType, 'suivi')) {
                                        $motifClass = 'motif-suivi';
                                    } elseif (str_contains($combinedType, 'tele') || str_contains($combinedType, 'télé')) {
                                        $motifClass = 'motif-teleconsultation';
                                    } elseif (str_contains($combinedType, 'examen') || str_contains($combinedType, 'bilan')) {
                                        $motifClass = 'motif-examen';
                                    }

                                    $statusLabel = match ($normalizedStatus) {
                                        'en_attente' => 'En attente',
                                        'en_soins' => 'En consultation',
                                        'vu' => 'Termine',
                                        'absent' => 'Absent',
                                        'annule' => 'Annule',
                                        default => 'A venir',
                                    };
                                    $statusClass = match ($normalizedStatus) {
                                        'en_attente' => 'status-attente',
                                        'en_soins' => 'status-soins',
                                        'vu' => 'status-vu',
                                        'absent' => 'status-absent',
                                        'annule' => 'status-annule',
                                        default => 'status-a-venir',
                                    };
                                @endphp
                                <div class="rdv-card {{ $statusClass }}">
                                    <div class="rdv-card-head">
                                        <div class="rdv-time">{{ $rdv->date_heure->format('H:i') }} - {{ $rdv->date_heure->copy()->addMinutes($rdv->duree)->format('H:i') }}</div>
                                        <div class="rdv-status-stack">
                                            <span class="rdv-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                        </div>
                                    </div>
                                    <div class="rdv-card-body">
                                        <div class="rdv-main-content">
                                            <div class="rdv-patient-row">
                                                <div class="rdv-avatar">
                                                    @if($patientPhotoUrl)
                                                        <img src="{{ $patientPhotoUrl }}" alt="Photo patient">
                                                    @else
                                                        <span class="rdv-avatar-fallback">{{ $patientInitials }}</span>
                                                    @endif
                                                </div>
                                                <div class="rdv-patient-copy">
                                                    <div class="rdv-patient">{{ $patientName }}</div>
                                                    <div class="rdv-patient-subline">
                                                        <span><i class="fas fa-id-card"></i> {{ $patientMeta }}</span>
                                                        <span><i class="fas fa-user-doctor"></i> {{ $doctorName }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <span class="rdv-motif-badge" title="{{ $motifLabel }}">{{ $motifLabel }}</span>

                                            <div class="rdv-meta-line">
                                                <span class="rdv-meta-label"><i class="fas fa-notes-medical"></i> Motif</span>
                                                <span>{{ $rdv->motif ?: 'Motif non precise' }}</span>
                                            </div>
                                        </div>

                                        <div class="rdv-side-actions">
                                            <div class="rdv-action-stack" role="group" aria-label="Actions rendez-vous">
                                                <div class="rdv-quick-actions" aria-label="Actions rapides rendez-vous">
                                                    @if($dossierUrl)
                                                        <a href="{{ $dossierUrl }}" class="rdv-quick-action" title="Ouvrir dossier" aria-label="Ouvrir dossier" target="_blank" rel="noopener noreferrer">
                                                            <i class="fas fa-folder-open"></i>
                                                        </a>
                                                    @endif
                                                    <a
                                                        href="{{ $smsUrl }}"
                                                        class="rdv-quick-action"
                                                        title="Envoyer SMS"
                                                        aria-label="Envoyer SMS"
                                                        data-agenda-sms-trigger
                                                        data-rendezvous-id="{{ $rdv->id }}"
                                                        data-patient-name="{{ $patientName }}"
                                                        data-patient-phone="{{ $patientPhone }}"
                                                        data-rendezvous-label="{{ $smsRendezvousLabel }}"
                                                        data-rendezvous-date="{{ $rdv->date_heure->toIso8601String() }}"
                                                        data-doctor-name="{{ $doctorName }}"
                                                    >
                                                        <i class="fas fa-sms"></i>
                                                    </a>
                                                    @if($factureUrl)
                                                        <a href="{{ $factureUrl }}" class="rdv-quick-action" title="Creer facture" aria-label="Creer facture" target="_blank" rel="noopener noreferrer">
                                                            <i class="fas fa-file-invoice-dollar"></i>
                                                        </a>
                                                    @endif
                                                    @if($consultationUrl)
                                                        <a href="{{ $consultationUrl }}" class="rdv-quick-action" title="Ouvrir consultation" aria-label="Ouvrir consultation" target="_blank" rel="noopener noreferrer">
                                                            <i class="fas fa-stethoscope"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                                <a href="{{ route('rendezvous.edit', $rdv) }}"
                                                   class="rdv-action-primary"
                                                   title="Ouvrir le rendez-vous">
                                                    <i class="fas fa-arrow-up-right-from-square"></i><span>Ouvrir</span>
                                                </a>
                                                <details class="rdv-more">
                                                    <summary class="rdv-action-more" aria-label="Plus d actions">
                                                        <i class="fas fa-ellipsis"></i>
                                                    </summary>
                                                    <div class="rdv-more-menu">
                                                        @if($rdv->patient_id)
                                                            <a href="{{ route('patients.show', $rdv->patient_id) }}" class="rdv-more-link rdv-more-link--dossier" target="_blank" rel="noopener noreferrer">
                                                                <i class="fas fa-folder-open"></i><span>Dossier patient</span>
                                                            </a>
                                                        @endif
                                                        <a
                                                            href="{{ route('sms.create', ['rendezvous_id' => $rdv->id]) }}"
                                                            class="rdv-more-link"
                                                            data-agenda-sms-trigger
                                                            data-rendezvous-id="{{ $rdv->id }}"
                                                            data-patient-name="{{ $patientName }}"
                                                            data-patient-phone="{{ $patientPhone }}"
                                                            data-rendezvous-label="{{ $smsRendezvousLabel }}"
                                                            data-rendezvous-date="{{ $rdv->date_heure->toIso8601String() }}"
                                                            data-doctor-name="{{ $doctorName }}"
                                                        >
                                                            <i class="fas fa-sms"></i><span>Envoyer un SMS</span>
                                                        </a>
                                                        <a href="{{ route('rendezvous.edit', $rdv) }}#notes" class="rdv-more-link">
                                                            <i class="fas fa-note-sticky"></i><span>Ajouter une note</span>
                                                        </a>
                                                        @if(in_array($normalizedStatus, ['a_venir', 'en_attente'], true))
                                                            <form action="{{ route('rendezvous.update_status', $rdv->id) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="statut" value="en_soins">
                                                                <button type="submit" class="rdv-more-button rdv-more-button--start">
                                                                    <i class="fas fa-play"></i><span>Demarrer</span>
                                                                </button>
                                                            </form>
                                                        @elseif($normalizedStatus === 'en_soins')
                                                            <form action="{{ route('rendezvous.update_status', $rdv->id) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="statut" value="vu">
                                                                <button type="submit" class="rdv-more-button rdv-more-button--finish">
                                                                    <i class="fas fa-check"></i><span>Terminer</span>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <a href="{{ route('consultations.create', ['patient_id' => $rdv->patient_id]) }}" class="rdv-more-link rdv-more-link--consultation" target="_blank" rel="noopener noreferrer">
                                                                <i class="fas fa-stethoscope"></i><span>Creer une consultation</span>
                                                            </a>
                                                        @endif
                                                        @if(!in_array($normalizedStatus, ['vu', 'absent', 'annule'], true))
                                                            <form action="{{ route('rendezvous.update_status', $rdv->id) }}"
                                                                  method="POST"
                                                                  onsubmit="return confirm('Annuler ce rendez-vous ?');">
                                                                @csrf
                                                                <input type="hidden" name="statut" value="annule">
                                                                <button type="submit" class="rdv-more-button rdv-more-button--cancel">
                                                                    <i class="fas fa-ban"></i><span>Annuler le rendez-vous</span>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </details>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <a href="{{ $slotCreateUrl }}"
                               class="quick-add quick-add-link"
                               data-date="{{ $selectedDate->format('Y-m-d') }}"
                               data-hour="{{ sprintf('%02d:00', $hour) }}">Ajouter RDV</a>
                        </div>
                    </div>
                @endfor
                @elseif($currentView === 'week' && $weekLayout === 'standard')
                    <div class="agenda-week-grid">
                        @foreach($weekDays as $weekDay)
                            @php
                                $dayKey = $weekDay->format('Y-m-d');
                                $dayAppointments = $appointmentsByDate->get($dayKey, collect());
                            @endphp
                            <section class="agenda-week-day">
                                <header class="agenda-week-day-head {{ $weekDay->isSameDay($selectedDate) ? 'is-selected' : '' }}">
                                    <div>
                                        <strong>{{ $weekDay->translatedFormat('l') }}</strong>
                                        <span>{{ $weekDay->format('d/m/Y') }}</span>
                                    </div>
                                    <span class="agenda-week-count">{{ $dayAppointments->count() }}</span>
                                </header>
                                <div class="agenda-week-day-body">
                                    @forelse($dayAppointments as $rdv)
                                        @php
                                            $status = \App\Models\RendezVous::normalizeStatus((string) $rdv->statut) ?? 'a_venir';
                                            $patientName = trim((string) optional($rdv->patient)->prenom . ' ' . (string) optional($rdv->patient)->nom);
                                            $patientName = $patientName !== '' ? $patientName : 'Patient inconnu';
                                            $doctorName = trim((string) optional($rdv->medecin)->prenom . ' ' . (string) optional($rdv->medecin)->nom);
                                            $doctorName = $doctorName !== '' ? 'Dr. ' . $doctorName : 'Medecin inconnu';
                                            $isUrgent = str_contains(mb_strtolower(((string) $rdv->type . ' ' . (string) $rdv->motif), 'UTF-8'), 'urgence');
                                            $statusLabel = match ($status) {
                                                'en_attente' => 'En attente',
                                                'en_soins' => 'En consultation',
                                                'vu' => 'Termine',
                                                'absent' => 'Absent',
                                                'annule' => 'Annule',
                                                default => 'A venir',
                                            };
                                            $statusClass = match ($status) {
                                                'en_attente' => 'status-attente',
                                                'en_soins' => 'status-soins',
                                                'vu' => 'status-vu',
                                                'absent' => 'status-absent',
                                                'annule' => 'status-annule',
                                                default => 'status-a-venir',
                                            };
                                            $typeLabel = trim((string) ($rdv->type ?: $rdv->motif));
                                            $typeLabel = $typeLabel !== '' ? $typeLabel : 'Consultation generale';
                                        @endphp
                                        <article class="week-rdv {{ $isUrgent ? 'urgent' : '' }}">
                                            <div class="week-rdv-head">
                                                <span class="week-rdv-time">{{ $rdv->date_heure->format('H:i') }}</span>
                                                <span class="rdv-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                            </div>
                                            <div class="week-rdv-patient">{{ $patientName }}</div>
                                            <div class="week-rdv-doctor"><i class="fas fa-user-doctor"></i> {{ $doctorName }}</div>
                                            <div class="week-rdv-inline">
                                                <span class="week-rdv-type" title="{{ $typeLabel }}">{{ $typeLabel }}</span>
                                                @if($rdv->motif && $rdv->motif !== $typeLabel)
                                                    <div class="week-rdv-meta">{{ $rdv->motif }}</div>
                                                @endif
                                            </div>
                                            <div class="week-rdv-actions">
                                                <div class="rdv-action-stack">
                                                    <a href="{{ route('rendezvous.edit', $rdv) }}" class="rdv-action-primary" title="Ouvrir le rendez-vous">
                                                        <i class="fas fa-arrow-up-right-from-square"></i><span>Ouvrir</span>
                                                    </a>
                                                    <details class="rdv-more">
                                                        <summary class="rdv-action-more" aria-label="Plus d actions">
                                                            <i class="fas fa-ellipsis"></i>
                                                        </summary>
                                                        <div class="rdv-more-menu">
                                                            @if($rdv->patient_id)
                                                                <a href="{{ route('patients.show', $rdv->patient_id) }}" class="rdv-more-link rdv-more-link--dossier" target="_blank" rel="noopener noreferrer">
                                                                    <i class="fas fa-folder-open"></i><span>Dossier patient</span>
                                                                </a>
                                                            @endif
                                                            @if(in_array($status, ['a_venir', 'en_attente'], true))
                                                                <form action="{{ route('rendezvous.update_status', $rdv->id) }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="statut" value="en_soins">
                                                                    <button type="submit" class="rdv-more-button rdv-more-button--start">
                                                                        <i class="fas fa-play"></i><span>Demarrer</span>
                                                                    </button>
                                                                </form>
                                                            @elseif($status === 'en_soins')
                                                                <form action="{{ route('rendezvous.update_status', $rdv->id) }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="statut" value="vu">
                                                                    <button type="submit" class="rdv-more-button rdv-more-button--finish">
                                                                        <i class="fas fa-check"></i><span>Terminer</span>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <a href="{{ route('consultations.create', ['patient_id' => $rdv->patient_id]) }}" class="rdv-more-link rdv-more-link--consultation" target="_blank" rel="noopener noreferrer">
                                                                    <i class="fas fa-stethoscope"></i><span>Creer une consultation</span>
                                                                </a>
                                                            @endif
                                                            @if(!in_array($status, ['vu', 'absent', 'annule'], true))
                                                                <form action="{{ route('rendezvous.update_status', $rdv->id) }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="statut" value="annule">
                                                                    <button type="submit" class="rdv-more-button rdv-more-button--cancel">
                                                                        <i class="fas fa-ban"></i><span>Annuler</span>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </details>
                                                </div>
                                            </div>
                                        </article>
                                    @empty
                                        <div class="agenda-empty-day">Aucun rendez-vous.</div>
                                    @endforelse
                                    <a href="{{ route('rendezvous.create', ['date' => $weekDay->format('Y-m-d'), 'heure' => '09:00']) }}"
                                       class="quick-add quick-add-link"
                                       data-date="{{ $weekDay->format('Y-m-d') }}"
                                       data-hour="09:00">Ajouter RDV</a>
                                </div>
                            </section>
                        @endforeach
                    </div>
                @elseif($currentView === 'week' && $weekLayout === 'dense')
                    <div class="agenda-dense-grid">
                        @foreach($weekDays as $denseDay)
                            @php
                                $dayKey = $denseDay->format('Y-m-d');
                                $dayAppointments = $denseAppointmentsByDate->get($dayKey, collect());
                            @endphp
                            <section class="agenda-dense-day {{ $denseDay->isSameDay($selectedDate) ? 'is-selected' : '' }}">
                                <header class="agenda-dense-day-head">
                                    <div>
                                        <strong>{{ $denseDay->translatedFormat('l') }}</strong>
                                        <span>{{ $denseDay->format('d/m/Y') }}</span>
                                    </div>
                                    <span class="agenda-dense-count">{{ $dayAppointments->count() }}</span>
                                </header>
                                <div class="agenda-dense-day-body">
                                    @forelse($dayAppointments as $rdv)
                                        <article class="dense-rdv-card {{ $rdv->dense_type_class }} {{ $rdv->dense_status_class }}"
                                                 data-open-url="{{ $rdv->dense_open_url }}"
                                                 data-context-target="dense-menu-{{ $rdv->id }}"
                                                 tabindex="0">
                                            <div class="dense-rdv-topline">
                                                <span class="dense-rdv-time">{{ $rdv->date_heure->format('H:i') }}</span>
                                                <span class="dense-rdv-status">{{ $rdv->dense_status_label }}</span>
                                            </div>
                                            <div class="dense-rdv-patient">{{ $rdv->dense_patient_name }}</div>
                                            <div class="dense-rdv-type">{{ $rdv->dense_type_label }}</div>
                                            <div class="dense-rdv-doctor">{{ $rdv->dense_doctor_name }}</div>

                                            <div class="dense-rdv-indicators" aria-label="Indicateurs rapides">
                                                <span class="dense-indicator" title="SMS"><i class="fas fa-sms"></i></span>
                                                <span class="dense-indicator" title="Facture"><i class="fas fa-file-invoice-dollar"></i></span>
                                                <span class="dense-indicator" title="Ordonnance"><i class="fas fa-prescription"></i></span>
                                                <span class="dense-indicator {{ $rdv->dense_presence_class }}" title="{{ $rdv->dense_presence_label }}"><i class="{{ $rdv->dense_presence_icon }}"></i></span>
                                            </div>

                                            <div class="dense-rdv-hover-actions">
                                                <a href="{{ $rdv->dense_open_url }}" class="dense-hover-action" title="Ouvrir dossier" aria-label="Ouvrir dossier" data-stop-open target="_blank" rel="noopener noreferrer">
                                                    <i class="fas fa-folder-open"></i>
                                                </a>
                                                <a
                                                    href="{{ $rdv->dense_sms_url }}"
                                                    class="dense-hover-action"
                                                    title="Envoyer SMS"
                                                    aria-label="Envoyer SMS"
                                                    data-stop-open
                                                    data-agenda-sms-trigger
                                                    data-rendezvous-id="{{ $rdv->id }}"
                                                    data-patient-name="{{ $rdv->dense_patient_name }}"
                                                    data-patient-phone="{{ trim((string) optional($rdv->patient)->telephone) }}"
                                                    data-rendezvous-label="{{ $rdv->date_heure->format('d/m/Y H:i') }}{{ $rdv->dense_doctor_name !== 'Medecin inconnu' ? ' • ' . $rdv->dense_doctor_name : '' }}"
                                                    data-rendezvous-date="{{ $rdv->date_heure->toIso8601String() }}"
                                                    data-doctor-name="{{ $rdv->dense_doctor_name }}"
                                                >
                                                    <i class="fas fa-sms"></i>
                                                </a>
                                                @if($rdv->dense_facture_url)
                                                    <a href="{{ $rdv->dense_facture_url }}" class="dense-hover-action" title="Creer facture" aria-label="Creer facture" data-stop-open target="_blank" rel="noopener noreferrer">
                                                        <i class="fas fa-file-invoice-dollar"></i>
                                                    </a>
                                                @endif
                                                @if($rdv->dense_consultation_url)
                                                    <a href="{{ $rdv->dense_consultation_url }}" class="dense-hover-action" title="Ouvrir consultation" aria-label="Ouvrir consultation" data-stop-open target="_blank" rel="noopener noreferrer">
                                                        <i class="fas fa-stethoscope"></i>
                                                    </a>
                                                @endif
                                            </div>

                                            <div class="dense-context-menu" id="dense-menu-{{ $rdv->id }}" role="menu">
                                                <a href="{{ $rdv->dense_open_url }}" class="dense-context-link" data-stop-open target="_blank" rel="noopener noreferrer">
                                                    <i class="fas fa-folder-open"></i><span>Ouvrir dossier</span>
                                                </a>
                                                <a
                                                    href="{{ $rdv->dense_sms_url }}"
                                                    class="dense-context-link"
                                                    data-stop-open
                                                    data-agenda-sms-trigger
                                                    data-rendezvous-id="{{ $rdv->id }}"
                                                    data-patient-name="{{ $rdv->dense_patient_name }}"
                                                    data-patient-phone="{{ trim((string) optional($rdv->patient)->telephone) }}"
                                                    data-rendezvous-label="{{ $rdv->date_heure->format('d/m/Y H:i') }}{{ $rdv->dense_doctor_name !== 'Medecin inconnu' ? ' • ' . $rdv->dense_doctor_name : '' }}"
                                                    data-rendezvous-date="{{ $rdv->date_heure->toIso8601String() }}"
                                                    data-doctor-name="{{ $rdv->dense_doctor_name }}"
                                                >
                                                    <i class="fas fa-sms"></i><span>Envoyer SMS</span>
                                                </a>
                                                @if($rdv->dense_facture_url)
                                                    <a href="{{ $rdv->dense_facture_url }}" class="dense-context-link" data-stop-open target="_blank" rel="noopener noreferrer">
                                                        <i class="fas fa-file-invoice-dollar"></i><span>Creer facture</span>
                                                    </a>
                                                @endif
                                                @if($rdv->dense_ordonnance_url)
                                                    <a href="{{ $rdv->dense_ordonnance_url }}" class="dense-context-link" data-stop-open>
                                                        <i class="fas fa-prescription"></i><span>Creer ordonnance</span>
                                                    </a>
                                                @endif
                                                @if($rdv->dense_consultation_url)
                                                    <a href="{{ $rdv->dense_consultation_url }}" class="dense-context-link" data-stop-open target="_blank" rel="noopener noreferrer">
                                                        <i class="fas fa-stethoscope"></i><span>Ouvrir consultation</span>
                                                    </a>
                                                @endif
                                                <a href="{{ $rdv->dense_edit_url }}" class="dense-context-link" data-stop-open>
                                                    <i class="fas fa-pen"></i><span>Modifier le rendez-vous</span>
                                                </a>
                                            </div>
                                        </article>
                                    @empty
                                        <div class="agenda-empty-day dense-empty">Aucun rendez-vous programme</div>
                                    @endforelse

                                    <a href="{{ route('rendezvous.create', ['date' => $denseDay->format('Y-m-d'), 'heure' => '09:00']) }}"
                                       class="quick-add quick-add-link dense-add-link"
                                       data-date="{{ $denseDay->format('Y-m-d') }}"
                                       data-hour="09:00">Ajouter RDV</a>
                                </div>
                            </section>
                        @endforeach
                    </div>
                @else
                    <div class="agenda-month-grid">
                        @foreach($calendarDays as $calendarDay)
                            @php
                                $dayKey = $calendarDay->date->format('Y-m-d');
                                $dayAppointments = $appointmentsByDate->get($dayKey, collect());
                            @endphp
                            <section class="agenda-month-cell {{ !$calendarDay->isCurrentMonth ? 'is-muted' : '' }} {{ $calendarDay->isToday ? 'is-today' : '' }}">
                                <header class="agenda-month-cell-head">
                                    <span>{{ $calendarDay->date->format('d') }}</span>
                                    @if($dayAppointments->isNotEmpty())
                                        <span class="agenda-month-badge">{{ $dayAppointments->count() }}</span>
                                    @endif
                                </header>
                                <div class="agenda-month-cell-body">
                                    @forelse($dayAppointments->take(3) as $rdv)
                                        @php
                                            $patientName = trim((string) optional($rdv->patient)->prenom . ' ' . (string) optional($rdv->patient)->nom);
                                            $patientName = $patientName !== '' ? $patientName : 'Patient inconnu';
                                            $isUrgent = str_contains(mb_strtolower(((string) $rdv->type . ' ' . (string) $rdv->motif), 'UTF-8'), 'urgence');
                                            $status = \App\Models\RendezVous::normalizeStatus((string) $rdv->statut) ?? 'a_venir';
                                            $statusClass = match ($status) {
                                                'en_attente' => 'status-attente',
                                                'en_soins' => 'status-soins',
                                                'vu' => 'status-vu',
                                                'absent' => 'status-absent',
                                                'annule' => 'status-annule',
                                                default => 'status-a-venir',
                                            };
                                            $typeLabel = trim((string) ($rdv->type ?: $rdv->motif));
                                            $typeLabel = $typeLabel !== '' ? $typeLabel : 'Consultation generale';
                                        @endphp
                                        <a href="{{ route('rendezvous.edit', $rdv) }}"
                                           class="month-rdv {{ $isUrgent ? 'urgent' : '' }}"
                                           title="{{ $patientName }} - {{ $rdv->date_heure->format('H:i') }}">
                                            <div class="month-rdv-head">
                                                <strong>{{ $rdv->date_heure->format('H:i') }}</strong>
                                                <span class="month-rdv-status-dot {{ $statusClass }}"></span>
                                            </div>
                                            <span class="month-rdv-patient">{{ $patientName }}</span>
                                            <span class="month-rdv-type">{{ $typeLabel }}</span>
                                        </a>
                                    @empty
                                        <div class="agenda-empty-day compact">Aucun RDV</div>
                                    @endforelse
                                    @if($dayAppointments->count() > 3)
                                        <a href="{{ route('agenda.index', ['date' => $calendarDay->date->format('Y-m-d'), 'view' => 'day']) }}" class="month-more-link">
                                            + {{ $dayAppointments->count() - 3 }} autres
                                        </a>
                                    @endif
                                    <a href="{{ route('rendezvous.create', ['date' => $calendarDay->date->format('Y-m-d'), 'heure' => '09:00']) }}"
                                       class="quick-add quick-add-link month-add-link"
                                       data-date="{{ $calendarDay->date->format('Y-m-d') }}"
                                       data-hour="09:00">Ajouter RDV</a>
                                </div>
                            </section>
                        @endforeach
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>

@include('agenda._sms_modal')

<script type="application/json" id="agendaPagePayload">
{!! json_encode([
    'currentView' => $currentView,
    'weekLayout' => $weekLayout ?? 'standard',
    'selectedDate' => $selectedDate->format('Y-m-d'),
    'agendaBaseUrl' => route('agenda.index'),
    'createBaseUrl' => route('rendezvous.create'),
    'smsStoreUrl' => route('sms.store'),
    'daysWithAppointments' => $daysWithAppointments,
    'selectedMedecinId' => (string) ($selectedMedecinId ?? ''),
    'selectedStatut' => (string) ($selectedStatut ?? ''),
    'searchTerm' => (string) ($searchTerm ?? ''),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endsection
