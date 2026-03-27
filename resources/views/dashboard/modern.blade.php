@extends('layouts.app')

@section('title', 'Tableau de Bord')
@section('topbar_subtitle', "Cockpit clinique unifie pour piloter agenda, finance et priorites du cabinet.")
@section('topbar_badge', 'Cockpit SaaS')
@section('topbar_icon', 'fa-chart-pie')
@section('topbar_search_placeholder', 'Rechercher patient, rendez-vous, facture ou action...')

@push('styles')
<style>
:root {
    --dashboard-surface: rgba(255, 255, 255, 0.94);
    --dashboard-surface-strong: rgba(255, 255, 255, 0.98);
    --dashboard-surface-soft: #f8fbff;
    --dashboard-border: #dbe7f3;
    --dashboard-border-strong: #cbd9e7;
    --dashboard-shadow: 0 18px 42px -32px rgba(15, 23, 42, 0.26);
    --dashboard-shadow-hover: 0 26px 50px -32px rgba(15, 23, 42, 0.34);
    --dashboard-text: #10263f;
    --dashboard-text-muted: #5f7691;
    --dashboard-title: #17365d;
    --dashboard-title-soft: #31557e;
    --dashboard-accent: #2563eb;
    --dashboard-accent-soft: rgba(37, 99, 235, 0.1);
    --dashboard-success: #0f8a63;
    --dashboard-success-soft: #ebf8f2;
    --dashboard-warning: #c06a15;
    --dashboard-warning-soft: #fff6ea;
    --dashboard-info: #2b7a9a;
    --dashboard-info-soft: #eef8fb;
    --dashboard-danger: #b45309;
    --dashboard-danger-soft: #fff6ef;
    --dashboard-radius-lg: 20px;
    --dashboard-radius-md: 16px;
    --dashboard-transition: all 0.2s ease;
}

@keyframes dashboardSoftFloat {
    0% {
        opacity: 0;
        transform: translateY(14px) scale(0.985);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.dashboard-shell {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafb 0%, #f0f4f8 100%);
    padding: clamp(0.7rem, 1vw, 1rem);
    overflow-x: clip;
}

.dashboard-shell--headerless {
    padding-top: clamp(0.45rem, 0.75vw, 0.75rem);
}

.dashboard-entrance {
    opacity: 0;
    transform: translateY(14px) scale(0.985);
    will-change: transform, opacity;
}

.dashboard-shell.is-ready .dashboard-entrance {
    animation: dashboardSoftFloat 0.52s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    animation-delay: var(--dashboard-enter-delay, 0ms);
}

.dashboard-wrap {
    width: 100%;
    max-width: none;
    min-width: 0;
    margin: 0;
}

.dashboard-header {
    position: relative;
    z-index: 3;
    min-width: 0;
    background:
        radial-gradient(circle at top right, rgba(80, 165, 255, 0.22) 0%, rgba(80, 165, 255, 0) 42%),
        linear-gradient(180deg, #ffffff 0%, #f6fbff 100%);
    border: 1px solid #d7e4ef;
    border-radius: 22px;
    padding: 1rem 1.05rem;
    box-shadow: 0 24px 44px -36px rgba(2, 30, 64, 0.42);
    margin-bottom: 1.1rem;
    overflow: hidden;
}

.dashboard-header::after {
    content: '';
    position: absolute;
    right: -6%;
    bottom: -155px;
    width: 240px;
    height: 240px;
    border-radius: 999px;
    background: radial-gradient(circle, rgba(80, 165, 255, 0.14) 0%, rgba(80, 165, 255, 0) 72%);
    opacity: 0;
    transform: translateY(14px);
    transition: opacity 0.28s ease, transform 0.28s ease;
    pointer-events: none;
}

.dashboard-header:hover::after {
    opacity: 1;
    transform: translateY(0);
}

.dashboard-header-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 440px) auto;
    align-items: center;
    gap: 0.85rem;
}

.dashboard-brand {
    display: flex;
    align-items: center;
    min-width: 0;
    gap: 0.75rem;
}

.dashboard-brand-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    color: #fff;
    background: linear-gradient(145deg, #1d6fdf, #1d4ed8);
    box-shadow: 0 10px 20px -14px rgba(30, 78, 141, 0.7);
    transition: transform 0.22s ease, box-shadow 0.22s ease, filter 0.22s ease;
}

.dashboard-brand:hover .dashboard-brand-icon {
    transform: translateY(-2px) rotate(-4deg) scale(1.04);
    box-shadow: 0 16px 28px -18px rgba(30, 78, 141, 0.78);
    filter: saturate(1.06);
}

.dashboard-brand-meta {
    min-width: 0;
}

.dashboard-brand-badge {
    display: inline-flex;
    align-items: center;
    width: fit-content;
    margin: 0 0 0.2rem;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #2f6399;
    padding: 3px 9px;
    border-radius: 999px;
    border: 1px solid #c5dbf1;
    background: #eef5ff;
}

.dashboard-brand h1 {
    margin: 0;
    font-size: clamp(1.18rem, 1.8vw, 1.85rem);
    font-weight: 800;
    color: #0f3158;
    line-height: 1.1;
}

.dashboard-brand p {
    margin: 0.2rem 0 0;
    color: #6281a2;
    font-size: 0.86rem;
    font-weight: 600;
}

.dashboard-tools {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.55rem;
    justify-self: stretch;
    min-width: 0;
}

.dashboard-tool-group {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.45rem;
    min-width: 0;
    margin-left: auto;
}

.dashboard-corner {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    justify-self: end;
    min-width: 0;
}

.dashboard-search {
    position: relative;
    width: 100%;
    min-width: 0;
    isolation: isolate;
}

.dashboard-search::after {
    content: '';
    position: absolute;
    inset: 1px;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.08), rgba(37, 99, 235, 0));
    opacity: 0;
    transition: opacity 0.22s ease;
    pointer-events: none;
    z-index: 0;
}

.dashboard-search i {
    position: absolute;
    top: 50%;
    left: 13px;
    transform: translateY(-50%);
    color: #6f8cae;
    transition: transform 0.2s ease, color 0.2s ease;
}

.dashboard-search input {
    width: 100%;
    padding: 0.66rem 0.85rem 0.66rem 2rem;
    border-radius: 12px;
    border: 1px solid #c8d9ed;
    background: #ffffff;
    color: #23476f;
    font-weight: 600;
    box-shadow: 0 8px 18px -20px rgba(15, 61, 117, 0.75);
    position: relative;
    z-index: 1;
    transition: border-color 0.22s ease, box-shadow 0.22s ease, transform 0.22s ease;
}

.dashboard-search input:hover {
    border-color: #b6cdea;
    box-shadow: 0 10px 20px -20px rgba(15, 61, 117, 0.58);
}

.dashboard-search input:focus-visible {
    outline: none;
    border-color: #4b86cb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.22);
}

.dashboard-search:focus-within i {
    color: var(--dashboard-accent);
    transform: translateY(-50%) scale(1.05);
}

.dashboard-search:hover::after,
.dashboard-search:focus-within::after {
    opacity: 1;
}

.dashboard-search:focus-within input {
    transform: translateY(-1px);
}

.dashboard-icon-btn {
    width: 39px;
    height: 39px;
    border: 1px solid #c8d9ed;
    border-radius: 10px;
    color: #2f5a87;
    background: linear-gradient(150deg, #ffffff 0%, #eef5ff 100%);
    box-shadow: 0 8px 18px -18px rgba(15, 61, 117, 0.7);
    transition: all 0.2s ease;
}

.dashboard-icon-btn i {
    transition: transform 0.2s ease;
}

.dashboard-icon-btn:hover {
    transform: translateY(-1px);
    background: linear-gradient(150deg, #ffffff 0%, #e5f0ff 100%);
    border-color: #a6c4e6;
}

.dashboard-icon-btn:hover i {
    transform: scale(1.08) rotate(6deg);
}

.dashboard-icon-btn:focus-visible {
    outline: none;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
}

.dashboard-switch {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.86rem;
    color: #315d89;
    cursor: pointer;
    border: 1px solid #c8d9ed;
    border-radius: 999px;
    background: linear-gradient(150deg, #ffffff 0%, #eef5ff 100%);
    padding: 0.35rem 0.55rem;
    min-height: 39px;
    transition: all 0.2s ease;
}

.dashboard-switch:hover {
    transform: translateY(-1px);
    border-color: #a6c4e6;
}

.dashboard-switch:hover .dashboard-switch-label {
    color: #1f4d83;
}

.dashboard-switch input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.dashboard-switch-track {
    width: 34px;
    height: 19px;
    border-radius: 999px;
    border: 1px solid #bcd0e8;
    background: #e4edf8;
    position: relative;
    transition: all .2s ease;
}

.dashboard-switch-thumb {
    width: 13px;
    height: 13px;
    border-radius: 999px;
    background: #ffffff;
    position: absolute;
    left: 2px;
    top: 2px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.3);
    transition: transform .2s ease;
}

.dashboard-switch:hover .dashboard-switch-thumb {
    transform: translateX(1px) scale(1.06);
}

.dashboard-switch input:checked + .dashboard-switch-track {
    background: linear-gradient(135deg, #1f6ede 0%, #2a7feb 100%);
    border-color: #2a79e7;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.14);
}

.dashboard-switch input:checked + .dashboard-switch-track .dashboard-switch-thumb {
    transform: translateX(14px);
}

.dashboard-switch-label {
    font-weight: 700;
    transition: color 0.2s ease;
}

.dashboard-user-menu {
    position: relative;
}

.dashboard-user-btn {
    min-width: 0;
    max-width: 100%;
    border: 1px solid #c8d9ed;
    border-radius: 12px;
    background: linear-gradient(145deg, #ffffff 0%, #edf5ff 100%);
    color: #1e4672;
    min-height: 42px;
    padding: 3px 10px 3px 4px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    box-shadow: 0 8px 18px -18px rgba(15, 61, 117, 0.75);
}

.dashboard-user-btn:hover {
    transform: translateY(-1px);
    border-color: #a6c4e6;
    background: linear-gradient(145deg, #ffffff 0%, #e6f0ff 100%);
}

.dashboard-user-btn:hover .dashboard-avatar {
    transform: translateY(-1px) scale(1.06);
    box-shadow: 0 12px 20px -16px rgba(29, 78, 216, 0.58);
}

.dashboard-avatar {
    width: 33px;
    height: 33px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    background: linear-gradient(135deg, #1f6fe0 0%, #3b8af6 100%);
    font-size: 12px;
    font-weight: 800;
    overflow: hidden;
    transition: transform 0.22s ease, box-shadow 0.22s ease;
}

.dashboard-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.dashboard-profile-meta {
    display: inline-flex;
    flex-direction: column;
    text-align: left;
    min-width: 0;
    max-width: 100%;
    line-height: 1.1;
}

.dashboard-profile-name {
    font-size: 12.5px;
    font-weight: 700;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 160px;
}

.dashboard-profile-role {
    font-size: 11px;
    color: #5c7ca0;
    font-weight: 700;
    text-transform: capitalize;
}

.dashboard-profile-chevron {
    font-size: 11px;
    color: #6a87a8;
    transition: transform .2s ease;
}

.dashboard-user-btn:hover .dashboard-profile-chevron {
    transform: translateX(2px);
}

.dashboard-user-menu.open .dashboard-profile-chevron {
    transform: rotate(180deg);
}

.dashboard-profile-dropdown {
    position: absolute;
    right: 0;
    top: calc(100% + 10px);
    min-width: 290px;
    max-width: min(320px, calc(100vw - 24px));
    border-radius: 14px;
    border: 1px solid #d8e5f4;
    background: linear-gradient(180deg, #ffffff 0%, #f6fbff 100%);
    box-shadow: 0 20px 34px -24px rgba(15, 23, 42, 0.75);
    padding: 10px;
    display: none;
    z-index: 20;
}

.dashboard-user-menu.open .dashboard-profile-dropdown {
    display: block;
}

.dashboard-profile-head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 4px;
}

.dashboard-profile-head-name {
    color: #143a66;
    font-size: 13px;
    font-weight: 800;
    line-height: 1.2;
}

.dashboard-profile-mail {
    color: #5f7d9f;
    font-size: 12px;
    margin: 2px 0 8px;
    padding: 0 4px 9px;
    border-bottom: 1px solid #e5edf7;
    word-break: break-all;
}

.dashboard-profile-role-chip {
    display: inline-flex;
    align-items: center;
    border: 1px solid #c4d7ee;
    border-radius: 999px;
    background: #eef5ff;
    color: #1f4d83;
    font-size: 11px;
    font-weight: 800;
    padding: 3px 8px;
    margin: 0 4px 8px;
}

.dashboard-dropdown-item {
    width: 100%;
    border: 1px solid #d1e1f2;
    border-radius: 10px;
    background: linear-gradient(160deg, #f8fbff 0%, #edf5ff 100%);
    color: #1b4674;
    font-size: 13px;
    font-weight: 700;
    padding: 8px 11px;
    margin-bottom: 7px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.18s ease;
}

.dashboard-dropdown-item:hover {
    transform: translateY(-1px);
    background: linear-gradient(160deg, #f2f8ff 0%, #e4efff 100%);
    border-color: #abc8e8;
}

.dashboard-dropdown-item-danger {
    border-color: #f0bec7;
    background: linear-gradient(160deg, #fff5f7 0%, #ffeef2 100%);
    color: #b62347;
    margin-bottom: 0;
}

.dashboard-dropdown-item-danger:hover {
    border-color: #e39dab;
    background: linear-gradient(160deg, #ffeef2 0%, #ffdfe7 100%);
    color: #991b37;
}

.dashboard-user-btn:focus-visible,
.dashboard-switch:focus-within,
.dashboard-icon-btn:focus-visible,
.dashboard-dropdown-item:focus-visible,
.quick-link:focus-visible,
.info-link:focus-visible,
.quick-mini a:focus-visible,
.dashboard-card-link:focus-visible,
.action-center-item:focus-visible,
.activity-item:focus-visible {
    outline: none;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
}

.dashboard-kpi-grid {
    margin-top: 0;
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.95rem;
    min-width: 0;
}

.kpi-card {
    position: relative;
    isolation: isolate;
    background: linear-gradient(180deg, var(--dashboard-surface-strong) 0%, var(--dashboard-surface) 100%);
    border: 1px solid var(--dashboard-border);
    border-radius: var(--dashboard-radius-lg);
    padding: 1rem;
    min-height: 142px;
    box-shadow: var(--dashboard-shadow);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
    transition: var(--dashboard-transition);
}

.kpi-card::before {
    content: '';
    position: absolute;
    inset: 0 auto auto 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, rgba(37, 99, 235, 0.18) 0%, rgba(15, 138, 99, 0.18) 48%, rgba(192, 106, 21, 0.18) 100%);
    z-index: -1;
}

.kpi-card:hover {
    transform: translateY(-3px);
    border-color: var(--dashboard-border-strong);
    box-shadow: var(--dashboard-shadow-hover);
}

.kpi-icon,
.dashboard-card-title i,
.quick-link-glyph,
.quick-link-arrow,
.alert-tile-icon,
.mini-action {
    transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
}

.kpi-card:hover .kpi-icon {
    transform: translateY(-2px) scale(1.04);
    box-shadow: 0 14px 22px -18px rgba(37, 99, 235, 0.4);
}

.kpi-card:hover .kpi-value {
    transform: translateY(-1px);
}

.kpi-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.6rem;
}

.kpi-title {
    margin: 0;
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--dashboard-text-muted);
    font-weight: 800;
}

.kpi-value {
    margin: 0.35rem 0 0;
    font-size: clamp(2rem, 2.4vw, 2.4rem);
    line-height: 0.94;
    font-weight: 800;
    letter-spacing: -0.04em;
    color: var(--dashboard-text);
    transition: transform 0.22s ease, color 0.22s ease;
}

.kpi-value-unit {
    font-size: 0.64em;
    font-weight: 700;
    letter-spacing: 0.02em;
    color: var(--dashboard-title-soft);
}

.kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    border: 1px solid rgba(255, 255, 255, 0.7);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.5);
    font-size: 1rem;
}

.kpi-tone-blue { background: linear-gradient(145deg, #edf4ff 0%, #dce9ff 100%); color: #2d66d8; }
.kpi-tone-cyan { background: linear-gradient(145deg, #edf9fc 0%, #daf0f6 100%); color: #2b7a9a; }
.kpi-tone-green { background: linear-gradient(145deg, #edf8f3 0%, #dcf1e7 100%); color: #0f8a63; }
.kpi-tone-amber { background: linear-gradient(145deg, #fff7ef 0%, #fdebd6 100%); color: #c06a15; }

.kpi-foot.kpi-foot-muted {
    color: var(--dashboard-text-muted);
}

.kpi-foot {
    margin-top: 0.9rem;
    padding-top: 0.75rem;
    border-top: 1px solid #edf2f7;
    color: var(--dashboard-text-muted);
    font-size: 0.88rem;
    font-weight: 600;
    line-height: 1.45;
}

.dashboard-main-grid {
    margin-top: 1.05rem;
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
    gap: 1rem;
    align-items: start;
    min-width: 0;
}

.dashboard-stack {
    display: grid;
    gap: 1rem;
}

.dashboard-stack-side {
    align-content: start;
}

.dashboard-card {
    position: relative;
    background: linear-gradient(180deg, var(--dashboard-surface-strong) 0%, var(--dashboard-surface) 100%);
    border: 1px solid var(--dashboard-border);
    border-radius: var(--dashboard-radius-lg);
    padding: 1rem;
    box-shadow: var(--dashboard-shadow);
    transition: var(--dashboard-transition);
}

.dashboard-card-side {
    padding: 0.95rem;
}

.dashboard-card:hover {
    transform: translateY(-2px);
    border-color: var(--dashboard-border-strong);
    box-shadow: var(--dashboard-shadow-hover);
}

.dashboard-card:hover .dashboard-card-title i {
    transform: translateY(-1px) scale(1.06);
}

.dashboard-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.8rem;
    margin-bottom: 0.95rem;
    padding-bottom: 0.8rem;
    border-bottom: 1px solid rgba(219, 231, 243, 0.78);
}

.dashboard-card-title {
    display: inline-flex;
    align-items: center;
    gap: var(--widget-title-gap);
    color: var(--dashboard-title);
    font-size: clamp(1rem, 1.2vw, 1.08rem);
    font-weight: var(--widget-title-weight);
    line-height: var(--widget-title-line-height);
    letter-spacing: var(--widget-title-tracking);
    margin: 0;
}

.dashboard-card-title.mb-3 {
    margin-bottom: 0.75rem !important;
}

.dashboard-card-title i,
.bottom-card-title i {
    flex: 0 0 auto;
    font-size: 0.95em;
    color: var(--dashboard-title-soft);
}

.dashboard-card-link {
    display: inline-flex;
    align-items: center;
    min-height: 44px;
    padding: 0.5rem 0.85rem;
    border-radius: 999px;
    border: 1px solid rgba(37, 99, 235, 0.14);
    color: var(--dashboard-accent);
    text-decoration: none;
    font-size: 0.88rem;
    font-weight: 700;
    background: var(--dashboard-accent-soft);
    transition: var(--dashboard-transition);
}

.dashboard-card-link:hover {
    background: rgba(37, 99, 235, 0.14);
    transform: translateY(-1px);
    box-shadow: 0 12px 20px -18px rgba(37, 99, 235, 0.34);
}

.dashboard-card .text-muted.small {
    white-space: normal;
    overflow-wrap: anywhere;
}

.dashboard-card-side .dashboard-empty {
    min-height: 96px;
}

.dashboard-list {
    display: grid;
    gap: 0.72rem;
}

.dashboard-list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.82rem 0.85rem;
    border-radius: var(--dashboard-radius-md);
    border: 1px solid #edf2f7;
    background: linear-gradient(180deg, #fbfdff 0%, #f6faff 100%);
    transition: var(--dashboard-transition);
}

.dashboard-list-item:hover {
    transform: translateY(-1px);
    border-color: #d7e3ef;
    background: linear-gradient(180deg, #ffffff 0%, #f3f8ff 100%);
}

.dashboard-list-item:hover .dashboard-list-icon {
    transform: scale(1.04);
}

.dashboard-list-main {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    min-width: 0;
}

.dashboard-list-icon {
    width: 40px;
    height: 40px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    border: 1px solid rgba(255, 255, 255, 0.72);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45);
    transition: transform 0.22s ease, box-shadow 0.22s ease;
}

.dashboard-list-text {
    min-width: 0;
}

.dashboard-list-text p {
    margin: 0;
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    text-overflow: ellipsis;
    white-space: normal;
    overflow-wrap: anywhere;
}

.dashboard-list-actions {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.urgent-widget {
    display: grid;
    gap: 0.7rem;
}

.urgent-widget-count {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.24rem 0.62rem;
    border-radius: 999px;
    border: 1px solid #f6d8bf;
    background: var(--dashboard-danger-soft);
    color: var(--dashboard-danger);
    font-size: 0.74rem;
    font-weight: 800;
}

.urgent-list {
    display: grid;
    gap: 0.65rem;
}

.urgent-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.82rem 0.85rem;
    border-radius: var(--dashboard-radius-md);
    border: 1px solid #f5dfcf;
    background: linear-gradient(180deg, #fffaf6 0%, #fff4ec 100%);
    transition: var(--dashboard-transition);
}

.urgent-item:hover {
    transform: translateY(-1px);
    border-color: #efd1b7;
}

.urgent-item-main {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    min-width: 0;
}

.urgent-avatar {
    width: 42px;
    height: 42px;
    border-radius: 999px;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: linear-gradient(135deg, #dd8a4a 0%, #c06a15 100%);
    color: #fff;
    font-size: 0.82rem;
    font-weight: 800;
    box-shadow: 0 12px 20px -18px rgba(239, 68, 68, 0.9);
}

.urgent-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.urgent-item-text {
    min-width: 0;
}

.urgent-item-text p {
    margin: 0;
}

.urgent-name-row {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    flex-wrap: wrap;
}

.urgent-patient-name {
    color: var(--dashboard-text);
    font-weight: 800;
    line-height: 1.25;
    overflow-wrap: anywhere;
}

.urgent-time {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.16rem 0.5rem;
    border-radius: 999px;
    border: 1px solid #f3d2ad;
    background: #fff7ee;
    color: var(--dashboard-danger);
    font-size: 0.72rem;
    font-weight: 800;
}

.urgent-meta {
    color: var(--dashboard-text-muted);
    font-size: 0.8rem;
    line-height: 1.4;
    margin-top: 0.15rem !important;
    overflow-wrap: anywhere;
}

.urgent-empty {
    border: 1px dashed #efdbc7;
    border-radius: var(--dashboard-radius-md);
    background: linear-gradient(180deg, #fffdf9 0%, #fff7ef 100%);
    color: #9a5b1c;
    padding: 1.15rem 1rem;
    text-align: center;
    font-weight: 700;
}

.dashboard-avatar-stack {
    width: 38px;
    height: 38px;
    border-radius: 999px;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1d6fdf 0%, #5aa0ff 100%);
    color: #fff;
    font-size: 0.82rem;
    font-weight: 800;
    flex-shrink: 0;
    box-shadow: 0 10px 18px -16px rgba(30, 64, 175, 0.85);
}

.dashboard-avatar-stack img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.dashboard-meta-row {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    flex-wrap: wrap;
    margin-top: 0.3rem !important;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    border-radius: 999px;
    padding: 0.24rem 0.6rem;
    font-size: 0.72rem;
    font-weight: 800;
    border: 1px solid transparent;
    line-height: 1.15;
    text-align: center;
    white-space: normal;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45);
}

.status-upcoming { background: #eef4ff; color: #2d66d8; border-color: #c9dafb; }
.status-waiting { background: #fff6eb; color: #c06a15; border-color: #f3d2ad; }
.status-active { background: #ebf8f5; color: #0f8a63; border-color: #b8e5d4; }
.status-done { background: #edf8f2; color: #0d7a57; border-color: #b8e2cf; }
.status-missed { background: #fff4ef; color: #b66325; border-color: #f1d2bc; }
.status-neutral { background: #f7fafc; color: #50657f; border-color: #d9e2eb; }

.mini-action {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    border: 1px solid #dbe7f2;
    background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
    color: #33537a;
    transition: var(--dashboard-transition);
}

.mini-action:hover {
    transform: translateY(-1px);
    border-color: #c6d9ed;
    color: #1d4ed8;
    background: linear-gradient(180deg, #ffffff 0%, #eef5ff 100%);
}

.mini-action:active,
.dashboard-card-link:active,
.quick-link:active,
.alert-tile:active,
.info-link:active,
.quick-mini a:active {
    transform: translateY(0) scale(0.985);
}

.mini-action-play { color: #047857; }
.mini-action-note { color: #7c3aed; }
.mini-action-folder { color: #1d4ed8; }
.mini-action-doc { color: #0f766e; }
.mini-action-sms { color: #d97706; }
.mini-action-edit { color: #334155; }
.mini-action-danger { color: #b91c1c; }

.dashboard-list.upcoming-compact-list {
    gap: 0.45rem;
}

.upcoming-compact-card {
    padding: 0.58rem 0.7rem;
    gap: 0.55rem;
}

.upcoming-compact-card .dashboard-list-main {
    gap: 0.55rem;
}

.upcoming-compact-card .dashboard-list-icon {
    width: 34px;
    height: 34px;
    font-size: 0.76rem;
}

.upcoming-compact-card .dashboard-list-text p {
    margin: 0;
}

.upcoming-compact-card .dashboard-meta-row {
    gap: 0.35rem;
    margin-top: 0 !important;
    margin-bottom: 0.12rem;
}

.upcoming-compact-card .dashboard-list-text .fw-semibold {
    font-size: 0.85rem;
    line-height: 1.2;
}

.upcoming-compact-card .status-pill {
    padding: 0.18rem 0.5rem;
    font-size: 0.66rem;
}

.upcoming-inline-meta {
    display: flex;
    align-items: center;
    gap: 0.28rem;
    flex-wrap: wrap;
    font-size: 0.74rem;
    line-height: 1.25;
    color: #64748b;
}

.upcoming-inline-label {
    font-weight: 700;
    color: #7c8ea5;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.upcoming-inline-value {
    font-weight: 700;
    color: #1e293b;
}

.upcoming-inline-dot {
    color: #9fb0c2;
    font-weight: 700;
}

.upcoming-compact-card .dashboard-list-actions {
    gap: 0.32rem;
}

.upcoming-compact-card .mini-action {
    width: 30px;
    height: 30px;
    border-radius: 10px;
    font-size: 0.8rem;
}

.chart-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.8rem;
}

.chart-card {
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    padding: 0.85rem;
}

.chart-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

.chart-title {
    margin: 0;
    color: #1e3a8a;
    font-weight: 700;
    font-size: 0.92rem;
}

.chart-subtitle {
    margin: 0.15rem 0 0;
    color: #64748b;
    font-size: 0.78rem;
}

.bar-chart {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(28px, 1fr));
    align-items: end;
    gap: 0.45rem;
    min-height: 180px;
}

.bar-chart-item {
    display: grid;
    gap: 0.35rem;
    justify-items: center;
}

.bar-chart-track {
    width: 100%;
    height: 138px;
    border-radius: 999px;
    background: #edf2f7;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
}

.bar-chart-fill {
    width: 100%;
    border-radius: 999px;
    min-height: 10px;
    transition: height .25s ease;
}

.bar-chart-value {
    font-size: 0.7rem;
    font-weight: 800;
    color: #334155;
}

.bar-chart-label {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: 700;
}

.doctor-widget {
    display: grid;
    gap: 0.75rem;
}

.doctor-summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.55rem;
}

.doctor-summary-card {
    border-radius: var(--dashboard-radius-md);
    padding: 0.85rem;
    border: 1px solid transparent;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45);
}

.doctor-summary-card strong {
    display: block;
    font-size: 1.2rem;
    line-height: 1;
    margin-top: 0.2rem;
}

.doctor-summary-card span {
    font-size: 0.76rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    line-height: 1.25;
    overflow-wrap: anywhere;
}

.doctor-summary-available { background: #edf8f2; border-color: #b8e2cf; color: #0d7a57; }
.doctor-summary-busy { background: #eef4ff; border-color: #c9dafb; color: #2d66d8; }
.doctor-summary-away { background: #f7fafc; border-color: #d9e2eb; color: #50657f; }

.doctor-list {
    display: grid;
    gap: 0.55rem;
}

.doctor-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.6rem;
    border: 1px solid #e4edf5;
    border-radius: var(--dashboard-radius-md);
    background: linear-gradient(180deg, #fbfdff 0%, #f6faff 100%);
    padding: 0.72rem 0.8rem;
    transition: var(--dashboard-transition);
}

.doctor-item:hover {
    transform: translateY(-1px);
    border-color: #d5e0ec;
}

.doctor-item-main {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    min-width: 0;
    flex: 1 1 auto;
}

.doctor-avatar {
    width: 38px;
    height: 38px;
    border-radius: 999px;
    overflow: hidden;
    flex-shrink: 0;
}

.doctor-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.doctor-item-name {
    margin: 0;
    font-size: 0.88rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.15;
    overflow-wrap: anywhere;
}

.doctor-item-meta {
    margin: 0.1rem 0 0;
    font-size: 0.76rem;
    color: #64748b;
    overflow-wrap: anywhere;
}

.dashboard-empty {
    display: grid;
    justify-items: center;
    gap: 0.5rem;
    text-align: center;
    color: var(--dashboard-text-muted);
    min-height: 112px;
    padding: 1rem 0.85rem;
    border: 1px dashed #d9e4ee;
    border-radius: var(--dashboard-radius-md);
    background: linear-gradient(180deg, #fcfdff 0%, #f7fbff 100%);
}

.dashboard-empty i {
    width: 52px;
    height: 52px;
    display: inline-grid;
    place-items: center;
    margin-bottom: 0 !important;
    border-radius: 16px;
    background: linear-gradient(145deg, #eef5ff 0%, #e0ecff 100%);
    color: var(--dashboard-accent);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45);
}

.dashboard-empty p {
    margin: 0;
    max-width: 30ch;
    color: var(--dashboard-text-muted);
    font-weight: 600;
}

@media (min-width: 1181px) {
    .dashboard-stack-side {
        position: sticky;
        top: 1rem;
    }
}

@media (max-width: 1180px) {
    .doctor-summary {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .doctor-item {
        align-items: flex-start;
    }

    .doctor-item .dashboard-list-actions {
        flex: 0 1 auto;
        max-width: 42%;
        justify-content: flex-end;
        text-align: right;
    }
}

@media (max-width: 860px) {
    .doctor-summary {
        grid-template-columns: 1fr;
    }

    .doctor-item {
        flex-direction: column;
        align-items: stretch;
    }

    .doctor-item .dashboard-list-actions {
        width: 100%;
        max-width: none;
        justify-content: flex-start;
        text-align: left;
        flex-wrap: wrap;
    }
}

.quick-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.65rem;
    padding: 0.88rem 0.92rem;
    border-radius: var(--dashboard-radius-md);
    text-decoration: none;
    font-weight: 700;
    border: 1px solid transparent;
    transition: var(--dashboard-transition);
}

.quick-link:hover {
    transform: translateY(-2px);
}

.quick-link:hover .quick-link-glyph {
    transform: translateY(-1px) scale(1.05);
}

.quick-link:hover .quick-link-arrow {
    transform: translateX(3px);
}

.quick-link:hover .quick-link-label {
    transform: translateX(2px);
}

.quick-link-main {
    display: inline-flex;
    align-items: center;
    gap: 0.72rem;
    min-width: 0;
}

.quick-link-glyph {
    width: 34px;
    height: 34px;
    display: inline-grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.52);
}

.quick-link-label {
    min-width: 0;
    line-height: 1.3;
    transition: transform 0.2s ease;
}

.quick-link-arrow {
    width: 30px;
    height: 30px;
    display: inline-grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.48);
    font-size: 0.82rem;
}

.quick-link-blue {
    background: linear-gradient(180deg, #f4f8ff 0%, #edf4ff 100%);
    color: #295fc7;
    border-color: #d6e3f8;
}

.quick-link-green {
    background: linear-gradient(180deg, #f1faf6 0%, #eaf7f1 100%);
    color: #0d7a57;
    border-color: #d3e9de;
}

.quick-link-purple {
    background: linear-gradient(180deg, #f7f5ff 0%, #f1effe 100%);
    color: #5f48a5;
    border-color: #e2dbf7;
}

.quick-link-amber {
    background: linear-gradient(180deg, #fff9f1 0%, #fff3e3 100%);
    color: #b5661e;
    border-color: #eedcc4;
}

.dashboard-footer {
    margin-top: 1rem;
    border-top: 1px solid #dde7f0;
    padding-top: 0.9rem;
    color: var(--dashboard-text-muted);
    font-size: 0.85rem;
    display: flex;
    justify-content: space-between;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.bottom-card-title {
    margin: 0 0 0.75rem;
    color: var(--dashboard-title);
    font-size: clamp(1rem, 1.2vw, 1.08rem);
    font-weight: var(--widget-title-weight);
    line-height: var(--widget-title-line-height);
    letter-spacing: var(--widget-title-tracking);
    display: inline-flex;
    align-items: center;
    gap: var(--widget-title-gap);
}

.goal-list {
    display: grid;
    gap: 0.7rem;
}

.goal-row {
    display: grid;
    gap: 0.35rem;
}

.goal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.86rem;
    color: #475569;
}

.goal-track {
    width: 100%;
    height: 8px;
    border-radius: 999px;
    background: #e5e7eb;
    overflow: hidden;
}

.goal-fill {
    height: 100%;
    border-radius: 999px;
}

.quick-mini {
    display: grid;
    gap: 0.5rem;
}

.quick-mini a {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.7rem 0.78rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 700;
    border: 1px solid transparent;
    transition: var(--dashboard-transition);
}

.quick-mini a:hover,
.info-link:hover,
.activity-item:hover,
.action-center-item:hover,
.alert-tile:hover {
    transform: translateY(-1px);
}

.quick-mini-blue {
    background: #eff6ff;
    color: #1d4ed8;
    border-color: #bfdbfe !important;
}

.quick-mini-green {
    background: #ecfdf5;
    color: #047857;
    border-color: #a7f3d0 !important;
}

.quick-mini-purple {
    background: #f5f3ff;
    color: #6d28d9;
    border-color: #ddd6fe !important;
}

.info-list {
    display: grid;
    gap: 0.5rem;
}

.info-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.72rem 0.78rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 700;
    border: 1px solid transparent;
    transition: var(--dashboard-transition);
}

.info-link-blue {
    background: #eff6ff;
    color: #1d4ed8;
    border-color: #bfdbfe !important;
}

.info-link-green {
    background: #ecfdf5;
    color: #047857;
    border-color: #a7f3d0 !important;
}

.info-link-orange {
    background: #fff7ed;
    color: #9a3412;
    border-color: #fdba74 !important;
}

.info-link-neutral {
    background: #f8fafc;
    color: #334155;
    border-color: #cbd5e1 !important;
}

.alert-tile {
    padding: 0.82rem 0.86rem;
    border-radius: var(--dashboard-radius-md);
    border: 1px solid transparent;
    transition: var(--dashboard-transition);
}

.alert-tile .alert-tile-text {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.65rem;
    font-weight: 600;
}

.alert-tile:hover .alert-tile-icon {
    transform: translateY(-1px) scale(1.05);
}

.alert-tile:hover .alert-tile-label {
    transform: translateX(2px);
}

.alert-tile-icon {
    width: 30px;
    height: 30px;
    display: inline-grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.56);
}

.alert-tile-label {
    min-width: 0;
    line-height: 1.35;
    transition: transform 0.2s ease;
}

.alert-warning {
    background: linear-gradient(180deg, #fff9f2 0%, #fff3e8 100%);
    border-color: #eedcc5;
}

.alert-warning .alert-tile-text {
    color: #a15d19;
}

.alert-info {
    background: linear-gradient(180deg, #f4f8ff 0%, #edf4ff 100%);
    border-color: #d6e3f8;
}

.alert-info .alert-tile-text {
    color: #295fc7;
}

.alert-success {
    background: linear-gradient(180deg, #f2faf6 0%, #ebf7f1 100%);
    border-color: #d2e8dd;
}

.alert-success .alert-tile-text {
    color: #0d7a57;
}

.activity-feed {
    display: grid;
    gap: 0.55rem;
    max-height: 430px;
    overflow-y: auto;
    padding-right: 2px;
}

.dashboard-card-side .activity-feed {
    max-height: 320px;
}

@media (prefers-reduced-motion: reduce) {
    .dashboard-entrance {
        opacity: 1;
        transform: none;
        animation: none !important;
    }

    .dashboard-shell *,
    .dashboard-shell *::before,
    .dashboard-shell *::after {
        transition: none !important;
        animation: none !important;
        scroll-behavior: auto !important;
    }
}

.activity-feed::-webkit-scrollbar {
    width: 6px;
}

.activity-feed::-webkit-scrollbar-thumb {
    background: #c8d6ea;
    border-radius: 999px;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    border: 1px solid #e4edf5;
    border-radius: var(--dashboard-radius-md);
    padding: 0.72rem;
    background: linear-gradient(180deg, #fbfdff 0%, #f6faff 100%);
    text-decoration: none;
    color: inherit;
    transition: var(--dashboard-transition);
}

.activity-item:hover {
    background: linear-gradient(180deg, #ffffff 0%, #f2f7ff 100%);
    border-color: #d5e1ee;
}

.activity-icon {
    width: 30px;
    height: 30px;
    border-radius: 999px;
    display: grid;
    place-items: center;
    background: linear-gradient(145deg, #edf4ff 0%, #dce9ff 100%);
    color: #2d66d8;
    flex-shrink: 0;
    font-size: 0.82rem;
}

.activity-body {
    min-width: 0;
    width: 100%;
}

.activity-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.4rem;
}

.activity-title {
    font-weight: 700;
    color: var(--dashboard-title);
    font-size: 0.85rem;
}

.activity-time {
    font-size: 0.72rem;
    color: var(--dashboard-text-muted);
    flex-shrink: 0;
}

.activity-text {
    margin: 0.15rem 0 0;
    color: var(--dashboard-text);
    font-size: 0.82rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.activity-meta {
    margin: 0.05rem 0 0;
    color: var(--dashboard-text-muted);
    font-size: 0.76rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.action-center-list {
    display: grid;
    gap: 0.55rem;
}

.action-center-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.6rem;
    border: 1px solid #e4edf5;
    border-radius: var(--dashboard-radius-md);
    padding: 0.72rem 0.8rem;
    background: linear-gradient(180deg, #fbfdff 0%, #f6faff 100%);
    text-decoration: none;
    color: inherit;
    transition: var(--dashboard-transition);
}

.action-center-item:hover {
    background: linear-gradient(180deg, #ffffff 0%, #f2f7ff 100%);
    border-color: #d5e1ee;
}

.action-center-main {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    min-width: 0;
}

.action-center-icon {
    width: 32px;
    height: 32px;
    border-radius: 999px;
    display: grid;
    place-items: center;
    background: linear-gradient(145deg, #edf4ff 0%, #dce9ff 100%);
    color: #2d66d8;
    flex-shrink: 0;
    font-size: 0.82rem;
}

.action-center-title {
    margin: 0;
    font-size: 0.86rem;
    font-weight: 700;
    color: var(--dashboard-title);
}

.action-center-sub {
    margin: 0.05rem 0 0;
    font-size: 0.76rem;
    color: var(--dashboard-text-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.action-center-right {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    flex-shrink: 0;
}

.action-center-count {
    min-width: 24px;
    text-align: center;
    font-size: 0.92rem;
    font-weight: 800;
    color: var(--dashboard-text);
}

.action-center-badge {
    font-size: 0.7rem;
    font-weight: 700;
    border-radius: 999px;
    padding: 0.18rem 0.5rem;
    border: 1px solid transparent;
}

.action-danger .action-center-icon { background: #fff2e8; color: #b5661e; }
.action-danger .action-center-badge { background: #fff6ef; border-color: #f1d2bc; color: #b5661e; }

.action-warning .action-center-icon { background: #fff5ea; color: #c06a15; }
.action-warning .action-center-badge { background: #fff8f0; border-color: #f3d2ad; color: #c06a15; }

.action-info .action-center-icon { background: #edf4ff; color: #2d66d8; }
.action-info .action-center-badge { background: #f4f8ff; border-color: #d6e3f8; color: #2d66d8; }

.action-neutral .action-center-icon { background: #f1f5f9; color: #50657f; }
.action-neutral .action-center-badge { background: #f8fafc; border-color: #d9e2eb; color: #50657f; }

/* Dark mode dashboard */
body.dark-mode .dashboard-shell {
    background: linear-gradient(135deg, #0f172a 0%, #111827 55%, #0b1220 100%) !important;
}

body.dark-mode .dashboard-header {
    background:
        radial-gradient(circle at top right, rgba(67, 134, 214, 0.24) 0%, rgba(67, 134, 214, 0) 44%),
        linear-gradient(180deg, #102236 0%, #0f1d30 100%);
    border-color: #30506d;
    box-shadow: 0 14px 28px -20px rgba(0, 0, 0, 0.65);
}

body.dark-mode .dashboard-brand-badge {
    border-color: #3d6488;
    background: #1a3657;
    color: #d4e5f8;
}

body.dark-mode .dashboard-brand h1 {
    color: #f9fafb;
}

body.dark-mode .dashboard-brand p,
body.dark-mode .dashboard-switch,
body.dark-mode .dashboard-search i,
body.dark-mode .dashboard-footer,
body.dark-mode .dashboard-empty {
    color: #9ca3af;
}

body.dark-mode .dashboard-search input {
    background: #152a43;
    border-color: #35506d;
    color: #f3f4f6;
}

body.dark-mode .dashboard-icon-btn {
    color: #e5e7eb;
    border-color: #35506d;
    background: #152a43;
}

body.dark-mode .dashboard-icon-btn:hover {
    background: #1e3958;
    border-color: #476889;
}

body.dark-mode .dashboard-switch,
body.dark-mode .dashboard-user-btn {
    border-color: #35506d;
    background: #152a43;
    color: #d9e8f7;
}

body.dark-mode .dashboard-switch:hover,
body.dark-mode .dashboard-user-btn:hover {
    background: #1e3958;
    border-color: #476889;
}

body.dark-mode .dashboard-switch-track {
    background: #324a67;
    border-color: #5a78a0;
}

body.dark-mode .dashboard-profile-role,
body.dark-mode .dashboard-profile-chevron {
    color: #a8bfdc;
}

body.dark-mode .dashboard-profile-dropdown {
    background: #111827;
    border-color: #374151;
}

body.dark-mode .dashboard-profile-head-name,
body.dark-mode .dashboard-dropdown-item {
    color: #e5e7eb;
}

body.dark-mode .dashboard-profile-mail {
    color: #9ca3af;
    border-bottom-color: #374151;
}

body.dark-mode .dashboard-profile-role-chip {
    border-color: #466892;
    background: #183556;
    color: #cde2ff;
}

body.dark-mode .dashboard-dropdown-item {
    background: #152a43;
    border-color: #35506d;
}

body.dark-mode .dashboard-dropdown-item:hover {
    background: #1e3958;
    border-color: #476889;
}

body.dark-mode .dashboard-dropdown-item-danger {
    border-color: #7b2d45;
    background: #412035;
    color: #ffbed0;
}

body.dark-mode .dashboard-dropdown-item-danger:hover {
    border-color: #8f3551;
    background: #4f2540;
    color: #ffd4df;
}

body.dark-mode .kpi-card,
body.dark-mode .dashboard-card {
    background: linear-gradient(180deg, rgba(16, 24, 39, 0.98) 0%, rgba(17, 24, 39, 0.94) 100%);
    border-color: #334155;
    box-shadow: 0 22px 40px -30px rgba(0, 0, 0, 0.62);
}

body.dark-mode .kpi-card:hover,
body.dark-mode .dashboard-card:hover {
    box-shadow: 0 28px 46px -28px rgba(0, 0, 0, 0.72);
}

body.dark-mode .dashboard-card-head {
    border-bottom-color: rgba(71, 85, 105, 0.72);
}

body.dark-mode .kpi-title,
body.dark-mode .kpi-foot,
body.dark-mode .dashboard-list-text .text-muted,
body.dark-mode .small.text-muted {
    color: #9ca3af !important;
}

body.dark-mode .kpi-value,
body.dark-mode .dashboard-list-text p.fw-semibold,
body.dark-mode .badge.text-dark,
body.dark-mode .text-dark {
    color: #f9fafb !important;
}

body.dark-mode .dashboard-card-title {
    color: #93c5fd;
}

body.dark-mode .dashboard-card-link {
    color: #60a5fa;
}

body.dark-mode .dashboard-list-item {
    background: linear-gradient(180deg, #182332 0%, #1b2736 100%);
    border-color: #2f3d4f;
}

body.dark-mode .urgent-widget-count {
    border-color: #7f4c20;
    background: rgba(112, 58, 16, 0.34);
    color: #fed7aa;
}

body.dark-mode .urgent-item {
    border-color: #5d4330;
    background: linear-gradient(180deg, rgba(73, 43, 23, 0.45) 0%, rgba(54, 31, 19, 0.58) 100%);
}

body.dark-mode .urgent-patient-name {
    color: #f9fafb;
}

body.dark-mode .urgent-time {
    border-color: #8a5224;
    background: rgba(124, 69, 22, 0.3);
    color: #fdba74;
}

body.dark-mode .urgent-meta {
    color: #cbd5e1;
}

body.dark-mode .urgent-empty {
    border-color: #7a5230;
    background: rgba(73, 43, 23, 0.26);
    color: #fed7aa;
}

body.dark-mode .badge.bg-light {
    background: #111827 !important;
    border: 1px solid #374151;
}

body.dark-mode .kpi-tone-blue { background: linear-gradient(145deg, #183259 0%, #1f3f73 100%); color:#bfdbfe; }
body.dark-mode .kpi-tone-cyan { background: linear-gradient(145deg, #163846 0%, #164e63 100%); color:#a5f3fc; }
body.dark-mode .kpi-tone-green { background: linear-gradient(145deg, #123126 0%, #14532d 100%); color:#bbf7d0; }
body.dark-mode .kpi-tone-amber { background: linear-gradient(145deg, #4d3119 0%, #6b3f18 100%); color:#fde68a; }

body.dark-mode .dashboard-footer {
    border-top-color: #374151;
}

body.dark-mode .bottom-card-title {
    color: #93c5fd;
}

body.dark-mode .goal-head {
    color: #cbd5e1;
}

body.dark-mode .goal-track {
    background: #374151;
}

body.dark-mode .quick-mini a {
    border-color: #374151 !important;
}

body.dark-mode .info-link {
    border-color: #374151 !important;
}

body.dark-mode .quick-link-blue {
    background: linear-gradient(180deg, #183255 0%, #1a365d 100%) !important;
    color: #bfdbfe !important;
    border-color: #295a96 !important;
}

body.dark-mode .quick-link-green {
    background: linear-gradient(180deg, #153127 0%, #15382c 100%) !important;
    color: #a7f3d0 !important;
    border-color: #1f6a4b !important;
}

body.dark-mode .quick-link-purple {
    background: linear-gradient(180deg, #261f44 0%, #2b2250 100%) !important;
    color: #ddd6fe !important;
    border-color: #4d3d92 !important;
}

body.dark-mode .quick-link-amber {
    background: linear-gradient(180deg, #352515 0%, #3b2a1a 100%) !important;
    color: #fdba74 !important;
    border-color: #8a5224 !important;
}

body.dark-mode .info-link-blue,
body.dark-mode .quick-mini-blue {
    background: #1a365d !important;
    color: #bfdbfe !important;
    border-color: #295a96 !important;
}

body.dark-mode .info-link-green,
body.dark-mode .quick-mini-green {
    background: #15382c !important;
    color: #a7f3d0 !important;
    border-color: #1f6a4b !important;
}

body.dark-mode .info-link-orange {
    background: #3b2a1a !important;
    color: #fdba74 !important;
    border-color: #8a5224 !important;
}

body.dark-mode .info-link-neutral {
    background: #1f2937 !important;
    color: #cbd5e1 !important;
    border-color: #475569 !important;
}

body.dark-mode .quick-mini-purple {
    background: #2b2250 !important;
    color: #ddd6fe !important;
    border-color: #4d3d92 !important;
}

body.dark-mode .alert-warning {
    background: linear-gradient(180deg, #352515 0%, #3b2a1a 100%);
    border-color: #8a5224;
}

body.dark-mode .alert-warning .alert-tile-text {
    color: #fed7aa;
}

body.dark-mode .alert-info {
    background: linear-gradient(180deg, #183255 0%, #1a365d 100%);
    border-color: #295a96;
}

body.dark-mode .alert-info .alert-tile-text {
    color: #bfdbfe;
}

body.dark-mode .alert-success {
    background: linear-gradient(180deg, #153127 0%, #15382c 100%);
    border-color: #1f6a4b;
}

body.dark-mode .alert-success .alert-tile-text {
    color: #a7f3d0;
}

body.dark-mode .activity-item {
    background: linear-gradient(180deg, #182332 0%, #1b2736 100%);
    border-color: #2f3d4f;
}

body.dark-mode .activity-item:hover {
    background: linear-gradient(180deg, #1b2a3a 0%, #213244 100%);
}

body.dark-mode .activity-feed::-webkit-scrollbar-thumb {
    background: #3b4f6d;
}

body.dark-mode .activity-icon {
    background: #1e3a8a;
    color: #dbeafe;
}

body.dark-mode .activity-title {
    color: #93c5fd;
}

body.dark-mode .activity-time,
body.dark-mode .activity-meta {
    color: #9ca3af;
}

body.dark-mode .activity-text {
    color: #f3f4f6;
}

body.dark-mode .action-center-item {
    background: linear-gradient(180deg, #182332 0%, #1b2736 100%);
    border-color: #2f3d4f;
}

body.dark-mode .action-center-item:hover {
    background: linear-gradient(180deg, #1b2a3a 0%, #213244 100%);
}

body.dark-mode .action-center-title {
    color: #c7dcfb;
}

body.dark-mode .action-center-sub {
    color: #9ca3af;
}

body.dark-mode .action-center-count {
    color: #f3f4f6;
}

body.dark-mode .action-danger .action-center-badge {
    background: #462717;
    border-color: #8a5224;
    color: #fed7aa;
}

body.dark-mode .action-warning .action-center-badge {
    background: #3b2a1a;
    border-color: #7c2d12;
    color: #fed7aa;
}

body.dark-mode .action-info .action-center-badge {
    background: #1e3a5f;
    border-color: #1d4ed8;
    color: #bfdbfe;
}

body.dark-mode .action-neutral .action-center-badge {
    background: #1f2937;
    border-color: #475569;
    color: #cbd5e1;
}

body.dark-mode .status-pill {
    background: #111827;
    border-color: #334155;
}

body.dark-mode .status-upcoming {
    background: #1e3a5f;
    color: #bfdbfe;
    border-color: #295a96;
}

body.dark-mode .status-waiting {
    background: #3b2a1a;
    color: #fed7aa;
    border-color: #8a5224;
}

body.dark-mode .status-active {
    background: #133d3b;
    color: #99f6e4;
    border-color: #0f766e;
}

body.dark-mode .status-done {
    background: #15382c;
    color: #a7f3d0;
    border-color: #1f6a4b;
}

body.dark-mode .status-missed {
    background: #3f1b24;
    color: #fecdd3;
    border-color: #9f1239;
}

body.dark-mode .status-neutral {
    background: #1f2937;
    color: #cbd5e1;
    border-color: #475569;
}

body.dark-mode .mini-action {
    background: #152a43;
    border-color: #35506d;
    color: #dbeafe;
}

body.dark-mode .mini-action:hover {
    background: #1e3958;
    border-color: #476889;
    color: #ffffff;
}

body.dark-mode .upcoming-inline-meta {
    color: #9db0c7;
}

body.dark-mode .upcoming-inline-label {
    color: #88a3bf;
}

body.dark-mode .upcoming-inline-value {
    color: #ebf4ff;
}

body.dark-mode .upcoming-inline-dot {
    color: #6f86a0;
}

body.dark-mode .chart-card {
    background: #111827;
    border-color: #374151;
}

body.dark-mode .chart-title,
body.dark-mode .doctor-item-name {
    color: #f3f4f6;
}

body.dark-mode .chart-subtitle,
body.dark-mode .bar-chart-label,
body.dark-mode .doctor-item-meta {
    color: #9ca3af;
}

body.dark-mode .bar-chart-track {
    background: #1f2937;
}

body.dark-mode .bar-chart-value {
    color: #e5e7eb;
}

body.dark-mode .doctor-item {
    background: linear-gradient(180deg, #182332 0%, #1b2736 100%);
    border-color: #2f3d4f;
}

body.dark-mode .dashboard-empty {
    background: linear-gradient(180deg, rgba(22, 34, 49, 0.96) 0%, rgba(19, 29, 43, 0.96) 100%);
    border-color: #334155;
}

body.dark-mode .dashboard-empty i {
    background: linear-gradient(145deg, #173154 0%, #1b3d67 100%);
    color: #bfdbfe;
}

body.dark-mode .doctor-summary-available {
    background: #15382c;
    border-color: #1f6a4b;
    color: #a7f3d0;
}

body.dark-mode .doctor-summary-busy {
    background: #1e3a5f;
    border-color: #295a96;
    color: #bfdbfe;
}

body.dark-mode .doctor-summary-away {
    background: #1f2937;
    border-color: #475569;
    color: #cbd5e1;
}

@media (max-width: 1100px) {
    .dashboard-kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dashboard-main-grid {
        grid-template-columns: 1fr;
    }

    .chart-grid {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 900px) {
    .dashboard-header-grid {
        grid-template-columns: 1fr;
    }

    .dashboard-corner {
        position: static;
        width: 100%;
        justify-content: space-between;
        margin-left: 0;
    }

    .dashboard-tools {
        width: 100%;
        justify-content: flex-start;
        flex-wrap: wrap;
    }

    .dashboard-search {
        width: 100%;
    }

    .dashboard-tool-group {
        margin-left: 0;
    }

    .doctor-item {
        align-items: flex-start;
        flex-direction: column;
    }
}

@media (max-width: 700px) {
    .dashboard-shell { padding: 0.55rem; }
    .dashboard-shell--headerless { padding-top: 0.35rem; }

    .dashboard-header { border-radius: 14px; padding: 0.7rem; }

    .dashboard-kpi-grid {
        gap: 0.75rem;
    }

    .dashboard-main-grid,
    .dashboard-stack {
        gap: 0.85rem;
    }

    .dashboard-tools {
        width: 100%;
        gap: 0.4rem;
        align-items: stretch;
        flex-direction: column;
    }

    .dashboard-corner {
        position: static;
        width: 100%;
        justify-content: space-between;
        margin-top: 0.25rem;
        margin-left: 0;
    }

    .dashboard-search { width: 100%; }

    .dashboard-tool-group {
        width: 100%;
        justify-content: space-between;
        margin-left: 0;
    }

    .dashboard-brand {
        align-items: flex-start;
    }

    .dashboard-brand p {
        white-space: normal;
    }

    .kpi-card,
    .dashboard-card {
        padding: 0.88rem;
        border-radius: 16px;
    }

    .kpi-card {
        min-height: 130px;
    }

    .dashboard-card-head {
        margin-bottom: 0.8rem;
        padding-bottom: 0.72rem;
    }

    .dashboard-profile-meta {
        display: none;
    }

    .dashboard-user-btn {
        margin-left: auto;
    }

    .dashboard-kpi-grid {
        grid-template-columns: 1fr;
    }

    .dashboard-card, .kpi-card { border-radius: 14px; }

    .kpi-card { min-height: 112px; }

    .kpi-value { font-size: 1.55rem; }

    .dashboard-footer {
        font-size: 0.8rem;
        gap: 0.4rem;
    }

    .activity-feed {
        max-height: 360px;
    }

    .doctor-summary {
        grid-template-columns: 1fr;
    }

    .dashboard-list-item {
        align-items: flex-start;
    }

    .dashboard-list-main {
        width: 100%;
    }

    .dashboard-list-actions {
        width: 100%;
        justify-content: flex-start;
        flex-wrap: wrap;
    }

    .dashboard-card-head {
        align-items: flex-start;
    }

    .dashboard-card-link {
        width: 100%;
        font-size: 0.84rem;
        justify-content: center;
    }

    .urgent-item {
        align-items: flex-start;
        flex-direction: column;
    }

    .mini-action {
        width: 42px;
        height: 42px;
        border-radius: 12px;
    }

    .activity-head {
        align-items: flex-start;
        flex-direction: column;
    }

    .activity-text,
    .activity-meta {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        white-space: normal;
    }

    .action-center-item {
        align-items: stretch;
        flex-direction: column;
    }

    .action-center-main,
    .action-center-right {
        width: 100%;
    }

    .action-center-right {
        justify-content: space-between;
    }

    .action-center-sub {
        white-space: normal;
        overflow: visible;
        text-overflow: clip;
    }

    .chart-card {
        padding: 0.75rem;
    }

}

.dashboard-premium-header-grid {
    grid-template-columns: minmax(0, 1.3fr) minmax(280px, 0.95fr) minmax(240px, 0.8fr);
    align-items: stretch;
}

.dashboard-hero-title {
    margin: 0.42rem 0 0;
    color: #0f3158;
    font-size: clamp(1.45rem, 2.2vw, 2.2rem);
    font-weight: 800;
    line-height: 1.04;
    letter-spacing: -0.04em;
}

.dashboard-hero-lead {
    margin: 0.4rem 0 0;
    color: #6281a2;
    font-size: 0.96rem;
    line-height: 1.68;
    max-width: 64ch;
}

.dashboard-hero-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.55rem;
    margin-top: 0.9rem;
}

.dashboard-hero-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    min-height: 40px;
    padding: 0 0.82rem;
    border-radius: 999px;
    border: 1px solid #d6e3f0;
    background: rgba(255, 255, 255, 0.82);
    color: #365a86;
    font-size: 0.78rem;
    font-weight: 700;
}

.dashboard-hero-chip.is-warm {
    border-color: #f3d2ad;
    background: #fff8ef;
    color: #b5661e;
}

.dashboard-hero-spotlight {
    display: grid;
    gap: 0.72rem;
}

.dashboard-spotlight-card {
    border-radius: 18px;
    border: 1px solid #d8e4ef;
    background: rgba(255, 255, 255, 0.9);
    padding: 1rem 1.05rem;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.4);
}

.dashboard-spotlight-card.is-primary {
    background: linear-gradient(180deg, #f5f9ff 0%, #edf4ff 100%);
    border-color: #d6e3f8;
}

.dashboard-spotlight-label {
    display: block;
    color: #6d86a5;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.dashboard-spotlight-card strong {
    display: block;
    margin-top: 0.45rem;
    color: #10263f;
    font-size: clamp(1.4rem, 2vw, 1.85rem);
    line-height: 1;
    letter-spacing: -0.04em;
}

.dashboard-spotlight-card small {
    font-size: 0.58em;
    color: #31557e;
    font-weight: 700;
}

.dashboard-spotlight-card p {
    margin: 0.38rem 0 0;
    color: #6281a2;
    font-size: 0.84rem;
    line-height: 1.5;
}

.dashboard-hero-actions {
    display: grid;
    gap: 0.7rem;
    align-content: start;
    justify-self: stretch;
}

.dashboard-hero-action {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.7rem;
    min-height: 54px;
    padding: 0.88rem 0.94rem;
    border-radius: 16px;
    border: 1px solid transparent;
    text-decoration: none;
    font-weight: 700;
    transition: var(--dashboard-transition);
}

.dashboard-hero-action:hover {
    transform: translateY(-2px);
}

.dashboard-hero-action-main {
    display: inline-flex;
    align-items: center;
    gap: 0.7rem;
}

.dashboard-hero-action-main i,
.dashboard-hero-action-arrow {
    width: 34px;
    height: 34px;
    display: inline-grid;
    place-items: center;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.56);
}

.dashboard-hero-action-arrow {
    width: 30px;
    height: 30px;
    border-radius: 10px;
    font-size: 0.82rem;
}

.dashboard-hero-action-blue {
    background: linear-gradient(180deg, #f4f8ff 0%, #edf4ff 100%);
    border-color: #d6e3f8;
    color: #295fc7;
}

.dashboard-hero-action-green {
    background: linear-gradient(180deg, #f1faf6 0%, #eaf7f1 100%);
    border-color: #d3e9de;
    color: #0d7a57;
}

.dashboard-hero-action-purple {
    background: linear-gradient(180deg, #f7f5ff 0%, #f1effe 100%);
    border-color: #e2dbf7;
    color: #5f48a5;
}

.dashboard-hero-action-amber {
    background: linear-gradient(180deg, #fff9f1 0%, #fff3e3 100%);
    border-color: #eedcc4;
    color: #b5661e;
}

.kpi-meta-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.6rem;
    margin-top: 0.95rem;
}

.kpi-meta-chip {
    display: inline-flex;
    align-items: center;
    min-height: 30px;
    padding: 0 0.65rem;
    border-radius: 999px;
    background: #f4f8ff;
    color: #31557e;
    font-size: 0.72rem;
    font-weight: 800;
}

.kpi-meta-helper {
    color: #6a84a7;
    font-size: 0.76rem;
    font-weight: 700;
    text-align: right;
}

.kpi-meter {
    width: 100%;
    height: 7px;
    margin-top: 0.62rem;
    border-radius: 999px;
    background: #e7edf5;
    overflow: hidden;
}

.kpi-meter span {
    display: block;
    height: 100%;
    border-radius: inherit;
    background: var(--kpi-meter, linear-gradient(90deg, #2563eb, #38bdf8));
}

.dashboard-split-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.45fr) minmax(0, 0.95fr);
    gap: 1rem;
}

.dashboard-card-subtitle {
    margin: 0.32rem 0 0;
    color: #6a84a7;
    font-size: 0.84rem;
    line-height: 1.55;
    max-width: 62ch;
}

.dashboard-table-wrap {
    overflow: auto;
    border: 1px solid #edf2f7;
    border-radius: 16px;
}

.dashboard-table {
    width: 100%;
    min-width: 760px;
    border-collapse: separate;
    border-spacing: 0;
    background: #ffffff;
}

.dashboard-table thead th {
    padding: 0.88rem 0.95rem;
    background: #f8fbff;
    color: #6982a0;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    border-bottom: 1px solid #edf2f7;
    white-space: nowrap;
}

.dashboard-table tbody td {
    padding: 0.95rem;
    border-bottom: 1px solid #edf2f7;
    vertical-align: middle;
    color: var(--dashboard-text);
}

.dashboard-table tbody tr:hover {
    background: #fbfdff;
}

.dashboard-table tbody tr:last-child td {
    border-bottom: 0;
}

.dashboard-table-person {
    display: flex;
    align-items: center;
    gap: 0.7rem;
    min-width: 0;
}

.dashboard-table-avatar {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    object-fit: cover;
    display: inline-grid;
    place-items: center;
    flex-shrink: 0;
    background: linear-gradient(145deg, #edf4ff 0%, #dce9ff 100%);
    color: #2d66d8;
    font-size: 0.82rem;
    font-weight: 800;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45);
}

.dashboard-table-copy {
    min-width: 0;
    display: grid;
    gap: 0.18rem;
}

.dashboard-table-copy strong {
    color: #10263f;
    font-size: 0.88rem;
    font-weight: 800;
    line-height: 1.2;
    overflow-wrap: anywhere;
}

.dashboard-table-copy span,
.dashboard-table-cell-muted {
    color: #6a84a7;
    font-size: 0.78rem;
    line-height: 1.4;
}

.dashboard-table-time {
    display: inline-flex;
    align-items: center;
    min-height: 34px;
    padding: 0 0.68rem;
    border-radius: 999px;
    background: #eff6ff;
    color: #295fc7;
    font-size: 0.76rem;
    font-weight: 800;
    white-space: nowrap;
}

.dashboard-table-note {
    display: -webkit-box;
    color: #10263f;
    font-size: 0.84rem;
    line-height: 1.45;
    overflow: hidden;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.dashboard-table-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.42rem;
    flex-wrap: wrap;
}

.dashboard-table-actions form {
    display: inline-flex;
}

.chart-pill {
    display: inline-flex;
    align-items: center;
    min-height: 32px;
    padding: 0 0.62rem;
    border-radius: 999px;
    background: #f4f8ff;
    color: #295fc7;
    font-size: 0.72rem;
    font-weight: 800;
}

.chart-summary {
    display: flex;
    align-items: baseline;
    gap: 0.45rem;
    margin-bottom: 0.85rem;
}

.chart-summary strong {
    color: #10263f;
    font-size: 1.38rem;
    font-weight: 800;
    letter-spacing: -0.04em;
}

.chart-summary span {
    color: #6a84a7;
    font-size: 0.82rem;
    font-weight: 700;
}

.dashboard-stat-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.65rem;
}

.dashboard-stat-tile {
    padding: 0.82rem;
    border-radius: 16px;
    border: 1px solid #d9e4ef;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
}

.dashboard-stat-tile span {
    display: block;
    color: #6a84a7;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.dashboard-stat-tile strong {
    display: block;
    margin-top: 0.45rem;
    color: #10263f;
    font-size: 1rem;
    font-weight: 800;
    line-height: 1.3;
}

.dashboard-stat-tile.is-success {
    background: linear-gradient(180deg, #f1faf6 0%, #eaf7f1 100%);
    border-color: #d3e9de;
}

.dashboard-stat-tile.is-danger {
    background: linear-gradient(180deg, #fff5f5 0%, #fff0f0 100%);
    border-color: #f4d1d1;
}

.dashboard-stat-tile.is-warning {
    background: linear-gradient(180deg, #fff9f1 0%, #fff3e3 100%);
    border-color: #eedcc4;
}

@media (max-width: 1380px) {
    .dashboard-premium-header-grid {
        grid-template-columns: 1fr;
    }

    .dashboard-hero-spotlight {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dashboard-hero-actions {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .dashboard-split-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 1180px) {
    .dashboard-chart-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 900px) {
    .dashboard-hero-spotlight,
    .dashboard-chart-grid,
    .dashboard-stat-grid {
        grid-template-columns: 1fr;
    }

    .dashboard-hero-actions {
        grid-template-columns: 1fr;
    }

    .dashboard-table {
        min-width: 680px;
    }
}

@media (max-width: 640px) {
    .dashboard-hero-chip {
        width: 100%;
        justify-content: center;
    }

    .dashboard-hero-action {
        padding: 0.82rem 0.88rem;
    }

    .kpi-meta-row {
        align-items: flex-start;
        flex-direction: column;
    }
}
</style>
@endpush

@section('content')
    @include('dashboard.partials.premium-content')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const urgentWidget = document.querySelector('[data-urgent-consultations-widget]');
    const dashboardShell = document.querySelector('.dashboard-shell');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    if (dashboardShell && !prefersReducedMotion.matches) {
        const entranceNodes = dashboardShell.querySelectorAll('.dashboard-header, .kpi-card, .dashboard-card, .dashboard-footer');

        entranceNodes.forEach(function (node, index) {
            node.classList.add('dashboard-entrance');
            node.style.setProperty('--dashboard-enter-delay', Math.min(index * 45, 360) + 'ms');
        });

        window.requestAnimationFrame(function () {
            dashboardShell.classList.add('is-ready');
        });
    } else if (dashboardShell) {
        dashboardShell.classList.add('is-ready');
    }

    function renderUrgentConsultations(payload) {
        if (!urgentWidget || !payload) {
            return;
        }

        const countNode = urgentWidget.querySelector('[data-urgent-count]');
        const listNode = urgentWidget.querySelector('[data-urgent-list]');
        const linkNode = urgentWidget.querySelector('.dashboard-card-link');

        if (countNode) {
            countNode.innerHTML = '<i class="fas fa-bolt"></i>' + (payload.count ?? 0);
        }

        if (linkNode && payload.all_url) {
            linkNode.setAttribute('href', payload.all_url);
        }

        if (!listNode) {
            return;
        }

        const items = Array.isArray(payload.items) ? payload.items : [];

        if (items.length === 0) {
            listNode.innerHTML = "<div class=\"urgent-empty\" data-urgent-empty>Aucune consultation urgente aujourd'hui.</div>";
            return;
        }

        listNode.innerHTML = items.map(function (item) {
            const avatar = item.patient_avatar
                ? '<img src="' + item.patient_avatar + '" alt="' + item.patient_name + '">'
                : (item.patient_initials || 'PT');

            return `
                <div class="urgent-item">
                    <div class="urgent-item-main">
                        <div class="urgent-avatar">${avatar}</div>
                        <div class="urgent-item-text">
                            <div class="urgent-name-row">
                                <p class="urgent-patient-name">${item.patient_name || 'Patient'}</p>
                                <span class="urgent-time"><i class="fas fa-clock"></i>${item.time || '-'}</span>
                            </div>
                            <p class="urgent-meta">${item.medecin || 'Dr non assigne'}</p>
                        </div>
                    </div>
                    <div class="dashboard-list-actions">
                        <span class="status-pill ${(item.status && item.status.class) || 'status-neutral'}">${(item.status && item.status.label) || 'A venir'}</span>
                        <a class="mini-action mini-action-folder" href="${item.patient_url || '#'}" title="Ouvrir le dossier patient">
                            <i class="fas fa-folder-open"></i>
                        </a>
                    </div>
                </div>
            `;
        }).join('');
    }

    async function refreshUrgentConsultations() {
        if (!urgentWidget || document.hidden) {
            return;
        }

        try {
            const response = await fetch(urgentWidget.dataset.refreshUrl, {
                headers: { 'Accept': 'application/json' },
            });

            if (!response.ok) {
                throw new Error('urgent_refresh_failed');
            }

            renderUrgentConsultations(await response.json());
        } catch (error) {
            // Keep current server-rendered state if refresh fails.
        }
    }

    document.querySelectorAll('[data-dashboard-status-form]').forEach(function (form) {
        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            const button = form.querySelector('button[type="submit"]');
            if (button) {
                button.disabled = true;
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: new URLSearchParams(new FormData(form)).toString()
                });

                if (!response.ok) {
                    throw new Error('request_failed');
                }

                window.location.reload();
            } catch (error) {
                if (button) {
                    button.disabled = false;
                }
                window.alert(@json(__('messages.dashboard.update_appointment_error')));
            }
        });
    });

    if (urgentWidget) {
        const urgentRefreshInterval = window.setInterval(refreshUrgentConsultations, 60000);
        const handleVisibilityChange = function () {
            if (!document.hidden) {
                refreshUrgentConsultations();
            }
        };

        document.addEventListener('visibilitychange', handleVisibilityChange);

        if (typeof window.__medisysRegisterCleanup === 'function') {
            window.__medisysRegisterCleanup(function () {
                window.clearInterval(urgentRefreshInterval);
                document.removeEventListener('visibilitychange', handleVisibilityChange);
            });
        }
    }
});
</script>
@endpush

