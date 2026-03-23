@extends('layouts.app')

@section('title', 'Gestion des Patients')

@push('styles')
<style>
    /* Styles pour la gestion des patients */
    :root {
        --primary-color: #1e3a8a;
        --primary-light: #3b82f6;
        --secondary-color: #64748b;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 28px;
        padding: 18px 20px;
        border: 1px solid #d9e5f1;
        border-radius: 24px;
        background:
            radial-gradient(circle at top right, rgba(59, 130, 246, 0.14) 0%, rgba(59, 130, 246, 0) 34%),
            linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(246,250,255,0.94) 100%);
        box-shadow: 0 24px 42px -34px rgba(15, 23, 42, 0.24);
    }

    .page-header-main {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        flex: 1 1 420px;
    }

    .page-title {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        min-width: 0;
        gap: 14px;
    }

    .page-title-copy {
        flex: 1 1 auto;
        min-width: 0;
        display: grid;
        gap: 4px;
    }

    .page-eyebrow {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid rgba(44, 123, 229, 0.16);
        background: rgba(239, 246, 255, 0.92);
        color: #1f6fa3;
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .page-title > div {
        min-width: 0;
    }

    .page-title > i {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff !important;
        background: linear-gradient(135deg, #2c7be5 0%, #1f6fa3 100%);
        box-shadow: 0 18px 28px -22px rgba(44, 123, 229, 0.52);
        font-size: 1.2rem !important;
    }

    .page-title h1 {
        font-size: 1.72rem;
        color: var(--primary-color);
        font-weight: 800;
        margin: 0;
        overflow-wrap: anywhere;
        line-height: 1.08;
    }

    .page-title p {
        color: var(--secondary-color);
        margin: 0;
        font-size: 0.98rem;
        overflow-wrap: anywhere;
    }

    .badge {
        background: linear-gradient(180deg, #edf5ff 0%, #e2ecfb 100%);
        color: #214f8b;
        padding: 7px 14px;
        border-radius: 999px;
        font-size: 0.88rem;
        font-weight: 800;
        box-shadow: 0 10px 16px -18px rgba(37, 99, 235, 0.26);
        letter-spacing: 0.2px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        max-width: 100%;
        white-space: normal;
        border: 1px solid #d2e0f2;
    }

    .page-title > .badge {
        flex: 0 0 auto;
        align-self: center;
        white-space: nowrap;
    }

    /* Action Buttons */
    .header-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        flex: 0 0 auto;
    }

    .btn-custom {
        padding: 11px 18px;
        border: 1px solid transparent;
        border-radius: 14px;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        font-size: 0.95rem;
        text-decoration: none;
        min-height: 48px;
        box-shadow: 0 16px 26px -24px rgba(15, 23, 42, 0.22);
    }

    .btn-primary-custom {
        background-color: var(--primary-light);
        color: white;
    }

    .btn-primary-custom:hover {
        background-color: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        color: white;
        text-decoration: none;
    }

    .btn-secondary-custom {
        background: linear-gradient(180deg, #ffffff 0%, #f4f8fc 100%);
        color: #475569;
        border-color: #d5e0ec;
    }

    .btn-secondary-custom:hover {
        background-color: #e2e8f0;
        color: #475569;
        text-decoration: none;
    }

    .header-back-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 48px;
        padding: 0 18px 0 14px;
        border-radius: 16px;
        border: 1px solid rgba(191, 207, 223, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(245, 249, 253, 0.92) 100%);
        color: #385674;
        font-weight: 700;
        letter-spacing: -0.01em;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.92), 0 16px 28px -26px rgba(15, 23, 42, 0.28);
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .header-back-btn:hover,
    .header-back-btn:focus {
        color: #1f6fa3;
        border-color: rgba(44, 123, 229, 0.3);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 244, 251, 0.98) 100%);
        transform: translateY(-1px);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.96), 0 18px 32px -24px rgba(31, 111, 163, 0.22);
        text-decoration: none;
    }

    .header-back-btn-icon {
        width: 28px;
        height: 28px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(44, 123, 229, 0.1);
        color: #2c7be5;
        flex-shrink: 0;
    }

    .btn-success-custom {
        background: linear-gradient(135deg, #2c7be5 0%, #1f6fa3 100%);
        color: white;
    }

    .btn-success-custom:hover {
        transform: translateY(-1px);
        box-shadow: 0 20px 30px -24px rgba(44, 123, 229, 0.48);
        color: white;
        text-decoration: none;
    }

    /* Stats Cards */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: linear-gradient(180deg, rgba(255,255,255,0.96) 0%, rgba(246,250,255,0.92) 100%);
        border-radius: 22px;
        padding: 22px 20px;
        box-shadow: 0 22px 36px -34px rgba(18, 57, 97, 0.22);
        display: flex;
        align-items: center;
        gap: 18px;
        transition: box-shadow 0.22s, border-color 0.22s, transform 0.15s;
        border: 1px solid #d9e5f1;
        backdrop-filter: blur(6px);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 26px 40px -32px rgba(18, 57, 97, 0.28);
        border-color: #bfd3ea;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        border: 1px solid rgba(255,255,255,0.9);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.88);
    }

    .stat-info h3 {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--primary-color);
        margin: 0;
        line-height: 1;
    }

    .stat-info p {
        color: var(--secondary-color);
        font-size: 0.9rem;
        margin: 0;
    }

    /* Search and Filter */
    .search-filter-container {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(248, 251, 255, 0.92) 100%);
        border-radius: 20px;
        padding: 22px;
        margin-bottom: 25px;
        box-shadow: 0 18px 34px -28px rgba(15, 23, 42, 0.2);
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        border: 1px solid rgba(203, 213, 225, 0.88);
        backdrop-filter: blur(6px);
        width: 100%;
        box-sizing: border-box;
    }

    .search-filter-container form {
        width: 100%;
    }

    .filters-toolbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .filters-heading {
        flex: 1 1 560px;
        min-width: 0;
    }

    .filters-heading span {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(44, 123, 229, 0.09);
        color: #1f6fa3;
        font-size: 0.8rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .filters-heading h2 {
        margin: 12px 0 6px;
        font-size: 1.16rem;
        font-weight: 800;
        color: #16324d;
    }

    .filters-heading p {
        margin: 0;
        font-size: 0.94rem;
        color: #64748b;
    }

    .filters-summary {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        min-height: 46px;
        padding: 0 16px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.78);
        border: 1px solid rgba(203, 213, 225, 0.86);
        color: #475569;
        font-size: 0.9rem;
        font-weight: 600;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
        flex: 0 0 auto;
    }

    .filters-summary i {
        color: #2c7be5;
    }

    .filter-row {
        --bs-gutter-x: 0;
        --bs-gutter-y: 0;
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 14px;
        align-items: end;
        margin: 0;
    }

    .filter-col-search {
        flex: initial;
        width: auto;
        max-width: none;
        padding: 0;
    }

    .filter-col-status,
    .filter-col-gender {
        flex: initial;
        width: auto;
        max-width: none;
        padding: 0;
    }

    .filter-col-actions {
        flex: initial;
        width: auto;
        max-width: none;
        padding: 0;
    }

    .filter-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        width: 100%;
    }

    .filter-actions .btn {
        height: 52px;
        border-radius: 14px;
        font-weight: 700;
        white-space: nowrap;
        min-width: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .filter-col-status .form-select,
    .filter-col-gender .form-select {
        height: 52px;
    }

    .search-box {
        min-width: 0;
        position: relative;
        width: 100%;
        box-sizing: border-box;
    }

    .search-box input {
        width: 100%;
        height: 52px;
        padding: 0 18px 0 50px;
        border: 1px solid #d8e2ee;
        border-radius: 14px;
        font-size: 0.96rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.92);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8), 0 10px 22px -24px rgba(15, 23, 42, 0.3);
        transition: all 0.2s ease;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .search-box input:focus {
        outline: none;
        border-color: rgba(44, 123, 229, 0.42);
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(44, 123, 229, 0.12), 0 14px 26px -24px rgba(31, 111, 163, 0.35);
        transform: translateY(-1px);
    }

    .search-box input::placeholder {
        color: #8aa1b7;
        font-weight: 500;
    }

    .search-box i {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #7b91a8;
        font-size: 0.95rem;
    }

    .filter-select,
    .search-filter-container .form-select {
        height: 52px;
        padding: 0 42px 0 16px;
        border: 1px solid #d8e2ee;
        border-radius: 14px;
        background-color: rgba(255, 255, 255, 0.92);
        color: #334155;
        font-size: 0.95rem;
        font-weight: 600;
        min-width: 120px;
        width: 100%;
        box-sizing: border-box;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8), 0 10px 22px -24px rgba(15, 23, 42, 0.28);
        transition: all 0.2s ease;
    }

    .search-filter-container .form-select:focus,
    .filter-select:focus {
        border-color: rgba(44, 123, 229, 0.42);
        box-shadow: 0 0 0 4px rgba(44, 123, 229, 0.12), 0 14px 26px -24px rgba(31, 111, 163, 0.35);
        transform: translateY(-1px);
    }

    .filter-apply-btn {
        color: #fff;
        border: none;
        background: linear-gradient(135deg, #2c7be5 0%, #1f6fa3 100%);
        box-shadow: 0 10px 22px -16px rgba(44, 123, 229, 0.55);
    }

    .filter-apply-btn:hover,
    .filter-apply-btn:focus {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 14px 24px -18px rgba(44, 123, 229, 0.6);
    }

    .filter-reset-btn {
        color: #48627d;
        border: 1px solid rgba(203, 213, 225, 0.95);
        background: rgba(255, 255, 255, 0.84);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.82);
    }

    .filter-reset-btn:hover,
    .filter-reset-btn:focus {
        color: #1f6fa3;
        border-color: rgba(44, 123, 229, 0.26);
        background: rgba(242, 247, 253, 0.96);
        transform: translateY(-1px);
    }

    /* Table */
    .table-container {
        background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(248,251,255,0.94) 100%);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 24px 40px -34px rgba(15, 23, 42, 0.22);
        margin-bottom: 30px;
        border: 1px solid #d9e5f1;
    }

    .table-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 16px 20px;
        border-bottom: 1px solid #e2ebf5;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    .table-head-copy {
        min-width: 0;
        display: grid;
        gap: 4px;
    }

    .table-head-copy h2 {
        margin: 0;
        color: #16324d;
        font-size: 1.06rem;
        font-weight: 800;
    }

    .table-head-copy p {
        margin: 0;
        color: #6b819c;
        font-size: 0.92rem;
    }

    .table-head-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 38px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid #d6e2ef;
        background: linear-gradient(180deg, #ffffff 0%, #f3f8fd 100%);
        color: #446784;
        font-size: 0.82rem;
        font-weight: 800;
    }

    .table-head-tools .display-mode-switch {
        padding: 4px;
        border-radius: 16px;
        border: 1px solid #d6e2ef;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fe 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
    }

    .table-head-tools .display-mode-option {
        min-height: 38px;
        padding: 0 14px;
        border-radius: 12px;
        font-size: 0.84rem;
        font-weight: 800;
    }

    .patients-table {
        width: 100%;
        border-collapse: collapse;
    }

    .patients-table thead {
        background: linear-gradient(180deg, #f8fbff 0%, #f3f8fd 100%);
        border-bottom: 1px solid #e2ebf5;
    }

    .patients-table th {
        padding: 16px 16px;
        text-align: left;
        font-weight: 800;
        color: #59718a;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .patients-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.2s;
    }

    .patients-table tbody tr:hover {
        background: linear-gradient(90deg, #f6fbff 0%, #f1f7fe 100%);
        box-shadow: inset 0 0 0 1px #d8e6f4;
        z-index: 1;
        position: relative;
    }

    .patients-table td {
        padding: 14px 16px;
        color: #334155;
        vertical-align: middle;
    }

    .patient-muted,
    .patient-subtle {
        color: #8ea3b8;
        font-size: 0.78rem;
    }

    .patient-id {
        font-family: 'Courier New', monospace;
        font-weight: 700;
        color: var(--primary-color);
        font-size: 0.86rem;
        line-height: 1.2;
    }

    .patient-name {
        font-weight: 700;
        color: #1e293b;
        overflow-wrap: anywhere;
        line-height: 1.2;
    }

    .patient-profile-cell {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .patient-avatar {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        object-fit: cover;
        flex: 0 0 44px;
        border: 1px solid #d9e5f1;
        background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%);
        box-shadow: 0 12px 20px -20px rgba(37, 99, 235, 0.45);
    }

    .patient-avatar-fallback {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 44px;
        border: 1px solid #d9e5f1;
        background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%);
        color: #2563eb;
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        box-shadow: 0 12px 20px -20px rgba(37, 99, 235, 0.45);
    }

    .patient-profile-copy {
        min-width: 0;
        display: grid;
        gap: 3px;
    }

    .patient-record-meta,
    .patient-inline-id,
    .patient-birth-age {
        display: inline-block;
        line-height: 1.25;
    }

    .patient-inline-id {
        font-size: 0.79rem;
        color: #8fa3b7;
    }

    .contact-info {
        color: #64748b;
        font-size: 0.88rem;
        display: grid;
        gap: 4px;
    }

    .contact-info div {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 0;
        line-height: 1.3;
    }

    .contact-info i {
        margin-top: 2px;
        color: #7288a1;
    }

    .patient-birth-main {
        font-weight: 700;
        color: #334155;
        line-height: 1.2;
    }

    .gender-badge {
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 800;
        text-align: center;
        min-width: 72px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 8px rgba(59,130,246,0.07);
        letter-spacing: 0.2px;
        white-space: normal;
    }

    .gender-male {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    .gender-female {
        background-color: #fce7f3;
        color: #be185d;
    }

    .gender-other {
        background-color: #f0fdf4;
        color: #166534;
    }

    .actions-cell {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .actions-cell form {
        margin: 0;
    }

    .action-btn {
        width: 40px;
        height: 40px;
        border-radius: 11px;
        border: 1px solid #d9e5f0;
        background: linear-gradient(180deg, #ffffff 0%, #f6faff 100%);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: box-shadow 0.18s, background 0.18s, color 0.18s, transform 0.18s;
        color: #64748b;
        text-decoration: none;
        outline: none;
    }

    .action-btn-primary {
        color: #215dc1;
        border-color: #cddcf2;
        background: linear-gradient(180deg, #f4f8ff 0%, #ebf3ff 100%);
        box-shadow: 0 10px 18px -18px rgba(37, 99, 235, 0.34);
    }

    .action-tone-rdv {
        color: #48627d;
    }

    .action-btn:focus {
        box-shadow: 0 0 0 3px #3b82f655;
        border: 1.5px solid #3b82f6;
    }

    .action-btn:hover {
        background: linear-gradient(180deg, #ffffff 0%, #edf5ff 100%);
        border-color: #c5d8eb;
        color: #3b82f6;
        transform: translateY(-1px);
        text-decoration: none;
    }

    .action-btn.view:hover {
        color: var(--success-color);
        background: #e0f7ef;
    }

    .action-btn.action-tone-rdv:hover {
        color: #2563eb;
        background: #eaf2ff;
    }

    .action-btn.edit:hover {
        color: var(--warning-color);
        background: #fff7e6;
    }

    .action-btn.delete:hover {
        color: var(--danger-color);
        background: #ffeaea;
    }

    /* Footer */
    .table-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 22px;
        background: linear-gradient(180deg, #fbfdff 0%, #f5f9fe 100%);
        border-top: 1px solid #e2ebf5;
    }

    .empty-state-panel {
        display: grid;
        justify-items: center;
        gap: 8px;
        padding: 14px 0;
    }

    .empty-state-panel i {
        width: 58px;
        height: 58px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: linear-gradient(145deg, #eef5ff 0%, #e0ecff 100%);
        color: #2c7be5;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.92);
    }

    .empty-state-panel h5 {
        margin: 0;
        color: #16324d;
        font-size: 1.04rem;
        font-weight: 800;
    }

    .empty-state-panel p {
        margin: 0;
        color: #6b819c;
        max-width: 460px;
    }

    .pagination-info {
        color: #64748b;
        font-size: 0.9rem;
    }

    .table-footer .pagination {
        margin: 0;
        gap: 6px;
        flex-wrap: wrap;
    }

    .table-footer .page-item {
        display: inline-flex;
    }

    .table-footer .page-link {
        min-width: 40px;
        height: 40px;
        padding: 0 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        border: 1px solid #d6e2ef;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fe 100%);
        color: #3f6284;
        font-size: 0.9rem;
        font-weight: 700;
        line-height: 1;
        text-decoration: none;
        box-shadow: 0 12px 18px -20px rgba(15, 23, 42, 0.16);
        transition: all 0.2s ease;
    }

    .table-footer .page-item:not(.active):not(.disabled) .page-link:hover {
        color: #1f6fa3;
        border-color: #bfd3ea;
        background: linear-gradient(180deg, #ffffff 0%, #edf5ff 100%);
        transform: translateY(-1px);
        box-shadow: 0 16px 22px -20px rgba(31, 111, 163, 0.22);
    }

    .table-footer .page-item.active .page-link {
        border-color: #2c7be5;
        background: linear-gradient(135deg, #2c7be5 0%, #1f6fa3 100%);
        color: #ffffff;
        box-shadow: 0 18px 24px -22px rgba(44, 123, 229, 0.5);
    }

    .table-footer .page-item.disabled .page-link {
        color: #9cb0c4;
        background: linear-gradient(180deg, #fbfdff 0%, #f5f8fc 100%);
        border-color: #dde7f1;
        box-shadow: none;
        opacity: 0.9;
    }

    .table-head-tools {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }

    .patients-mode-compact .patients-table th,
    .patients-mode-compact .patients-table td {
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .patients-mode-compact .patient-subtle,
    .patients-mode-compact .patient-muted small,
    .patients-mode-compact .contact-info div + div {
        display: none;
    }

    .patients-mode-compact .patient-name {
        font-size: .95rem;
    }

    .patients-mode-compact .contact-info {
        gap: 4px;
    }

    .patients-mode-compact .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
    }

    .patients-mode-cards .patients-table thead {
        display: none;
    }

    .patients-mode-cards .patients-table,
    .patients-mode-cards .patients-table tbody {
        display: grid;
        gap: 14px;
        width: 100%;
    }

    .patients-mode-cards .patients-table tbody tr:not(.empty-state-row) {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px 16px;
        padding: 16px;
        border: 1px solid #dbe6f1;
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 18px 24px -28px rgba(15, 23, 42, .12);
    }

    .patients-mode-cards .patients-table tbody tr:not(.empty-state-row):hover {
        transform: translateY(-1px);
        border-color: #c8d9ea;
        box-shadow: 0 22px 28px -28px rgba(15, 23, 42, .16);
    }

    .patients-mode-cards .patients-table td {
        display: grid;
        gap: 5px;
        padding: 0;
        border: none;
    }

    .patients-mode-cards .patients-table td::before {
        content: attr(data-label);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #71869d;
    }

    .patients-mode-cards .patients-table td[data-label="Actions"] {
        grid-column: 1 / -1;
    }

    .patients-mode-cards .patients-table .actions-cell {
        justify-content: flex-start;
    }

    /* Admin Info */
    .admin-info {
        text-align: center;
        padding: 25px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border-radius: 20px;
        box-shadow: 0 20px 34px -34px rgba(15, 23, 42, 0.22);
        border: 1px solid #d9e5f1;
    }

    .admin-contact {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-top: 10px;
        color: #64748b;
    }

    /* Dark mode */
    body.dark-mode .page-header {
        border-color: #2c4c6f;
        background: linear-gradient(180deg, #10243a 0%, #0f2237 100%);
        box-shadow: 0 24px 40px -34px rgba(0, 0, 0, 0.45);
    }

    body.dark-mode .page-eyebrow {
        background: rgba(93, 165, 255, 0.12);
        border-color: #37618c;
        color: #9ecbff;
    }

    body.dark-mode .page-title > i {
        box-shadow: 0 18px 30px -24px rgba(44, 123, 229, 0.5);
    }

    body.dark-mode .page-title h1 {
        color: #d9e8ff;
    }

    body.dark-mode .page-title p,
    body.dark-mode .stat-info p,
    body.dark-mode .contact-info,
    body.dark-mode .pagination-info,
    body.dark-mode .admin-contact {
        color: #a9bfd8;
    }

    body.dark-mode .stat-card,
    body.dark-mode .search-filter-container,
    body.dark-mode .table-container,
    body.dark-mode .admin-info {
        background: #102136;
        border-color: #2d4a67;
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.28);
    }

    body.dark-mode .stat-card:hover {
        border-color: #5da5ff;
        box-shadow: 0 14px 26px rgba(0, 0, 0, 0.35);
    }

    body.dark-mode .stat-info h3,
    body.dark-mode .patients-table th,
    body.dark-mode .patients-table td,
    body.dark-mode .patient-name,
    body.dark-mode .patient-id,
    body.dark-mode .admin-info h3 {
        color: #e7f0ff;
    }

    body.dark-mode .patient-avatar,
    body.dark-mode .patient-avatar-fallback {
        border-color: #355273;
        background: linear-gradient(180deg, #1b3654 0%, #173149 100%);
        color: #93c5fd;
    }

    body.dark-mode .search-box input,
    body.dark-mode .filter-select,
    body.dark-mode .form-select {
        background: #0d1a2b;
        border-color: #355273;
        color: #e5efff;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03), 0 10px 22px -24px rgba(0, 0, 0, 0.6);
    }

    body.dark-mode .search-box input::placeholder {
        color: #93abc8;
    }

    body.dark-mode .search-box i {
        color: #86a2c3;
    }

    body.dark-mode .btn-outline-secondary {
        color: #cfe0f7;
        border-color: #3a5b80;
        background: #15263c;
    }

    body.dark-mode .btn-outline-secondary:hover {
        color: #ffffff;
        border-color: #5f89b8;
        background: #254061;
    }

    body.dark-mode .header-back-btn {
        background: linear-gradient(180deg, rgba(21, 38, 60, 0.96) 0%, rgba(17, 33, 52, 0.92) 100%);
        border-color: #3a5b80;
        color: #d8e8fb;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.03), 0 18px 30px -24px rgba(0, 0, 0, 0.45);
    }

    body.dark-mode .header-back-btn:hover,
    body.dark-mode .header-back-btn:focus {
        color: #ffffff;
        border-color: #5f89b8;
        background: linear-gradient(180deg, rgba(37, 64, 97, 0.96) 0%, rgba(31, 53, 81, 0.94) 100%);
    }

    body.dark-mode .header-back-btn-icon {
        background: rgba(93, 165, 255, 0.14);
        color: #9ecbff;
    }

    body.dark-mode .filters-heading span {
        background: rgba(93, 165, 255, 0.12);
        color: #9ecbff;
    }

    body.dark-mode .filters-heading h2 {
        color: #e7f0ff;
    }

    body.dark-mode .filters-heading p,
    body.dark-mode .filters-summary {
        color: #a9bfd8;
    }

    body.dark-mode .filters-summary {
        background: rgba(13, 26, 43, 0.88);
        border-color: #345172;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
    }

    body.dark-mode .filter-apply-btn {
        box-shadow: 0 12px 22px -16px rgba(44, 123, 229, 0.55);
    }

    body.dark-mode .filter-reset-btn {
        color: #d8e8fb;
        border-color: #345172;
        background: rgba(17, 33, 52, 0.92);
    }

    body.dark-mode .filter-reset-btn:hover,
    body.dark-mode .filter-reset-btn:focus {
        color: #ffffff;
        border-color: #5d8dbd;
        background: rgba(35, 64, 97, 0.96);
    }

    body.dark-mode .btn-secondary-custom {
        background-color: #203753;
        color: #dceafc;
        border: 1px solid #345172;
    }

    body.dark-mode .btn-secondary-custom:hover {
        background-color: #2b486c;
        color: #ffffff;
    }

    body.dark-mode .patients-table thead,
    body.dark-mode .table-footer {
        background: #13283f;
        border-color: #2b4763;
    }

    body.dark-mode .table-head {
        background: linear-gradient(180deg, #13283f 0%, #102238 100%);
        border-bottom-color: #2b4763;
    }

    body.dark-mode .table-head-copy h2 {
        color: #e7f0ff;
    }

    body.dark-mode .table-head-copy p,
    body.dark-mode .table-head-badge {
        color: #a9bfd8;
    }

    body.dark-mode .table-head-badge {
        background: rgba(17, 33, 52, 0.92);
        border-color: #345172;
    }

    body.dark-mode .table-head-tools .display-mode-switch {
        border-color: #345172;
        background: linear-gradient(180deg, #13283f 0%, #102238 100%);
    }

    body.dark-mode .empty-state-panel i {
        background: linear-gradient(145deg, #17395e 0%, #15314f 100%);
        color: #9ecbff;
    }

    body.dark-mode .empty-state-panel h5 {
        color: #e7f0ff;
    }

    body.dark-mode .empty-state-panel p {
        color: #a9bfd8;
    }

    body.dark-mode .patients-table tbody tr {
        border-bottom-color: #243b55;
    }

    body.dark-mode .patients-table tbody tr:hover {
        background: linear-gradient(90deg, #173252 60%, #122742 100%);
        box-shadow: inset 0 0 0 1px #3f5f82;
    }

    body.dark-mode .action-btn {
        color: #abc1db;
        border-color: #345172;
        background: linear-gradient(180deg, #15263c 0%, #112238 100%);
    }

    body.dark-mode .action-btn-primary {
        color: #9fc2ff;
        border-color: #416184;
        background: linear-gradient(180deg, #183255 0%, #17304f 100%);
    }

    body.dark-mode .action-btn:hover {
        background: #203a59;
        color: #70b3ff;
    }

    body.dark-mode .action-btn.view:hover {
        color: #34d399;
        background: #12342d;
    }

    body.dark-mode .action-btn.edit:hover {
        color: #fbbf24;
        background: #3b2a10;
    }

    body.dark-mode .action-btn.action-tone-rdv:hover {
        color: #93c5fd;
        background: #1a365d;
    }

    body.dark-mode .table-footer .page-link {
        color: #b9cde1;
        border-color: #35506d;
        background: linear-gradient(180deg, #15263c 0%, #112238 100%);
        box-shadow: 0 12px 18px -22px rgba(0, 0, 0, 0.36);
    }

    body.dark-mode .table-footer .page-item:not(.active):not(.disabled) .page-link:hover {
        color: #d7ebff;
        border-color: #4a6b8d;
        background: linear-gradient(180deg, #1a3552 0%, #17304c 100%);
        box-shadow: 0 16px 22px -22px rgba(0, 0, 0, 0.44);
    }

    body.dark-mode .table-footer .page-item.active .page-link {
        border-color: #3b82f6;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
    }

    body.dark-mode .table-footer .page-item.disabled .page-link {
        color: #6f87a2;
        border-color: #2e455e;
        background: linear-gradient(180deg, #132234 0%, #111d2d 100%);
    }

    body.dark-mode .action-btn.delete:hover {
        color: #f87171;
        background: #3d1a21;
    }

    body.dark-mode .gender-male {
        background-color: rgba(37, 99, 235, 0.2);
        color: #9fc2ff;
    }

    body.dark-mode .gender-female {
        background-color: rgba(190, 24, 93, 0.2);
        color: #ff9bc9;
    }

    body.dark-mode .gender-other {
        background-color: rgba(22, 163, 74, 0.2);
        color: #86efac;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .search-filter-container {
            padding: 18px !important;
            width: 100% !important;
        }

        .filter-row {
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        }

        .filter-col-search,
        .filter-col-actions {
            grid-column: 1 / -1;
        }

        .search-box, .filter-select, .btn, .form-select {
            width: 100% !important;
            min-width: 0 !important;
            margin-bottom: 0 !important;
        }

        .search-filter-container .row {
            width: 100% !important;
        }

        .search-filter-container .col-12,
        .search-filter-container .col-md-5,
        .search-filter-container .col-md-2,
        .search-filter-container .col-md-3 {
            width: 100% !important;
            max-width: 100% !important;
        }

        .search-filter-container .d-flex.flex-column.flex-md-row {
            flex-direction: column !important;
        }

        .search-filter-container .btn {
            width: 100% !important;
            margin-bottom: 0 !important;
        }

        .filter-col-search,
        .filter-col-status,
        .filter-col-gender,
        .filter-col-actions {
            max-width: 100%;
        }

        .filter-actions {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .filters-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .filters-summary {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .stats-container {
            grid-template-columns: 1fr;
        }

        .page-header-main {
            width: 100%;
            flex-wrap: wrap;
            align-items: flex-start !important;
        }

        .page-title {
            flex-wrap: wrap;
            width: 100%;
            gap: 10px;
        }

        .page-title > i {
            width: 52px;
            height: 52px;
            border-radius: 16px;
        }

        .page-title h1 {
            font-size: 1.35rem;
        }

        .header-actions {
            width: 100%;
            flex-wrap: wrap;
        }

        .header-actions .btn-custom {
            width: 100%;
            justify-content: center;
        }

        .search-filter-container {
            flex-direction: column;
        }

        .filter-row {
            grid-template-columns: minmax(0, 1fr);
        }

        .filter-col-search,
        .filter-col-status,
        .filter-col-gender,
        .filter-col-actions {
            grid-column: auto;
        }

        .filter-select {
            width: 100%;
        }

        .filters-heading h2 {
            font-size: 1.05rem;
        }

        .filters-heading p {
            font-size: 0.9rem;
        }

        .search-filter-container {
            padding: 16px;
            border-radius: 18px;
        }

        .table-head,
        .table-footer {
            padding-left: 16px;
            padding-right: 16px;
        }

        .table-head {
            align-items: flex-start;
            flex-direction: column;
        }

        .search-box input,
        .search-filter-container .form-select,
        .filter-actions .btn {
            height: 50px;
            border-radius: 12px;
        }

        .filter-actions {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 16px;
            border-radius: 20px;
        }

        .page-header-main,
        .header-actions {
            width: 100%;
        }

        .header-actions {
            justify-content: stretch;
            gap: 10px;
        }

        .header-actions .btn-custom {
            flex: 1 1 220px;
        }
    }

    @media (max-width: 1024px) {
        .table-container {
            background: transparent;
            border: none;
            box-shadow: none;
            overflow: visible;
        }

        .patients-table {
            display: block;
            width: 100%;
        }

        .patients-table thead {
            display: none;
        }

        .patients-table tbody {
            display: grid;
            gap: 12px;
        }

        .patients-table tbody tr {
            display: grid;
            gap: 10px;
            padding: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
        }

        .patients-table td {
            display: grid;
            grid-template-columns: minmax(82px, 104px) minmax(0, 1fr);
            gap: 10px;
            align-items: start;
            padding: 0;
            border: 0;
        }

        .patients-table td::before {
            content: attr(data-label);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: #64748b;
        }

        .patients-table td[data-label="Actions"] {
            grid-template-columns: 1fr;
        }

        .patients-table td[data-label="Actions"]::before {
            margin-bottom: 2px;
        }

        .patients-table .actions-cell {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(44px, 44px));
            justify-content: flex-start;
        }

        .patients-table .contact-info,
        .patients-table .contact-info div {
            min-width: 0;
        }

        .patients-table .contact-info div {
            display: grid;
            grid-template-columns: 14px minmax(0, 1fr);
            align-items: start;
        }

        .patients-table .contact-info div i {
            margin-top: 2px;
        }

        .patients-table .contact-info div > :last-child,
        .patients-table .contact-info {
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .patients-table tbody tr.empty-state-row {
            display: block;
            padding: 0;
            border: none;
            background: #fff;
        }

        .patients-table tbody tr.empty-state-row td {
            display: block;
            padding: 24px 16px;
        }

        .patients-table tbody tr.empty-state-row td::before {
            content: none;
        }

        .table-footer {
            margin-top: 12px;
            border-radius: 12px;
            background-color: #f8fafc;
        }
    }
</style>
@endpush

@section('content')
@php
    $displayMode = request('display', 'table');
@endphp
<div class="container-fluid py-4 patients-mode-{{ $displayMode }}">
    <!-- Page Header -->
    <div class="page-header flex-wrap gap-2">
        <div class="page-header-main mb-2 mb-md-0">
            <!-- Bouton retour dashboard -->
            <a href="{{ route('dashboard') }}" class="header-back-btn me-2">
                <span class="header-back-btn-icon"><i class="fas fa-arrow-left"></i></span>
                <span class="d-none d-sm-inline">Retour</span>
            </a>
            <div class="page-title">
                <i class="fas fa-user-injured"></i>
                <div class="page-title-copy">
                    <span class="page-eyebrow">Dossiers patients</span>
                    <h1>Gestion des Patients</h1>
                </div>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('patients.export', request()->all()) }}" class="btn-custom btn-secondary-custom">
                <i class="fas fa-file-export"></i> Exporter CSV
            </a>
            <a href="{{ route('patients.create') }}" class="btn-custom btn-success-custom">
                <i class="fas fa-user-plus"></i> Nouveau Patient
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: #dbeafe; color: #3b82f6;">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalActivePatients }}</h3>
                <p>Patients actifs</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: #f0fdf4; color: #10b981;">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $todayAppointments }}</h3>
                <p>Rendez-vous aujourd'hui</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: #fef3c7; color: #f59e0b;">
                <i class="fas fa-file-medical"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $medicalRecords }}</h3>
                <p>Dossiers m&eacute;dicaux</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: #fee2e2; color: #ef4444;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $medicalAlerts }}</h3>
                <p>Patients avec allergies</p>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="search-filter-container">
        <form method="GET" action="{{ route('patients.index') }}" id="searchForm">
            <input type="hidden" name="display" value="{{ $displayMode }}">
            <div class="row filter-row">
                <div class="col-12 filter-col-search">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text"
                               name="search"
                               placeholder="Rechercher par nom, pr&eacute;nom, t&eacute;l&eacute;phone ou email..."
                               value="{{ request('search') }}">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Patients Table -->
    <div class="table-container">
        <div class="table-head">
            <div class="table-head-copy">
                <h2>Liste des patients</h2>
                <p>Acc&egrave;s rapide aux fiches, coordonn&eacute;es et actions du cabinet avec une lecture plus claire.</p>
            </div>
            <div class="table-head-tools">
                <div class="table-head-badge">
                    <i class="fas fa-users"></i>
                    <span>{{ $patients->count() }} ligne{{ $patients->count() > 1 ? 's' : '' }} sur cette page</span>
                </div>
                <div class="display-mode-switch" role="group" aria-label="Mode d affichage">
                    <a href="{{ request()->fullUrlWithQuery(['display' => 'table', 'page' => null]) }}" class="display-mode-option {{ $displayMode === 'table' ? 'active' : '' }}">Mode tableau</a>
                    <a href="{{ request()->fullUrlWithQuery(['display' => 'compact', 'page' => null]) }}" class="display-mode-option {{ $displayMode === 'compact' ? 'active' : '' }}">Mode compact</a>
                    <a href="{{ request()->fullUrlWithQuery(['display' => 'cards', 'page' => null]) }}" class="display-mode-option {{ $displayMode === 'cards' ? 'active' : '' }}">Mode cartes</a>
                </div>
            </div>
        </div>
        <table class="patients-table">
            <thead>
                <tr>
                    <th>ID / Dossier</th>
                    <th>Patient</th>
                    <th>Contact</th>
                    <th>CIN</th>
                    <th>Date Naissance</th>
                    <th>Genre</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                    <tr>
                        <td data-label="ID / Dossier">
                            <div class="patient-id">{{ $patient->numero_dossier ?? 'N/A' }}</div>
                            <small class="patient-subtle patient-record-meta">
                                Cr&eacute;&eacute; le {{ $patient->created_at->format('d/m/Y') }}
                            </small>
                        </td>
                        <td data-label="Patient">
                            <div class="patient-profile-cell">
                                <img src="{{ $patient->avatar_url }}" alt="{{ $patient->nom_complet }}" class="patient-avatar">
                                <div class="patient-profile-copy">
                                    <div class="patient-name">{{ strtoupper($patient->nom) }} {{ $patient->prenom }}</div>
                                    <div class="patient-muted patient-inline-id">
                                        ID: {{ $patient->id }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td data-label="Contact">
                            <div class="contact-info">
                                @if($patient->telephone)
                                    <div class="contact-line"><i class="fas fa-phone"></i> {{ $patient->telephone_formatted }}</div>
                                @endif
                                @if($patient->email)
                                    <div class="contact-line"><i class="fas fa-envelope"></i> {{ $patient->email }}</div>
                                @endif
                            </div>
                        </td>
                        <td data-label="CIN">{{ $patient->cin ?? 'N/A' }}</td>
                        <td data-label="Date Naissance">
                            @if($patient->date_naissance)
                                <div class="patient-birth-main">{{ \Carbon\Carbon::parse($patient->date_naissance)->format('d/m/Y') }}</div>
                                <small class="patient-subtle patient-birth-age">
                                    {{ \Carbon\Carbon::parse($patient->date_naissance)->age }} ans
                                </small>
                            @else
                                <div>Non renseign&eacute;e</div>
                            @endif
                        </td>
                        <td data-label="Genre">
                            @php
                                $genderClass = 'gender-other';
                                $genderIcon = 'fas fa-genderless';
                                $genderText = 'Autre';

                                if($patient->genre == 'M') {
                                    $genderClass = 'gender-male';
                                    $genderIcon = 'fas fa-mars';
                                    $genderText = 'Masculin';
                                } elseif($patient->genre == 'F') {
                                    $genderClass = 'gender-female';
                                    $genderIcon = 'fas fa-venus';
                                    $genderText = 'Féminin';
                                }
                            @endphp
                            <span class="gender-badge {{ $genderClass }}">
                                <i class="{{ $genderIcon }}"></i> {{ $genderText }}
                            </span>
                        </td>
                        <td data-label="Actions">
                            <div class="actions-cell">
                                <a href="{{ route('patients.show', $patient->id) }}"
                                   class="action-btn action-btn-primary view action-tone-view"
                                   title="Voir dossier">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('patients.edit', $patient->id) }}"
                                   class="action-btn edit action-tone-edit"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('rendezvous.create', ['patient_id' => $patient->id]) }}"
                                   class="action-btn action-tone-rdv"
                                   title="Prendre rendez-vous">
                                    <i class="fas fa-calendar-plus"></i>
                                </a>
                                <form action="{{ route('patients.destroy', $patient->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Voulez-vous vraiment archiver ce patient ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="action-btn delete action-tone-delete"
                                            title="Archiver">
                                        <i class="fas fa-archive"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-state-row">
                        <td colspan="7" class="text-center py-4">
                            <div class="empty-state-panel text-muted">
                                <i class="fas fa-users-slash fa-2x mb-3"></i>
                                <h5>Aucun patient trouv&eacute;</h5>
                                @if(request()->hasAny(['search', 'status', 'gender']))
                                    <p>Aucun patient trouv&eacute; pour ces crit&egrave;res de recherche</p>
                                @else
                                    <p>Commencez par ajouter votre premier patient</p>
                                @endif
                                <a href="{{ route('patients.create') }}" class="btn-custom btn-success-custom mt-2">
                                    <i class="fas fa-user-plus"></i> Ajouter un patient
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Table Footer with Pagination -->
        <div class="table-footer">
            <div class="pagination-info">
                Affichage de {{ $patients->firstItem() ?? 0 }} &agrave; {{ $patients->lastItem() ?? 0 }}
                sur {{ $patients->total() }} patients
            </div>

            <x-pagination :paginator="$patients" />
        </div>
    </div>

    <!-- Admin Info (Optionnel) -->
    @if(auth()->check())
        <div class="admin-info">
            <h3 style="color: #1e3a8a; margin-bottom: 10px;">
                <i class="fas fa-user-shield"></i>
                {{ auth()->user()->role == 'admin' ? 'Administrateur' : 'Utilisateur' }} Cabinet M&eacute;dical
            </h3>
            <div class="admin-contact">
                <i class="fas fa-envelope"></i>
                <span>{{ auth()->user()->email }}</span>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit filters on change
        document.getElementById('statusFilter').addEventListener('change', function() {
            document.getElementById('searchForm').submit();
        });

        document.getElementById('genderFilter').addEventListener('change', function() {
            document.getElementById('searchForm').submit();
        });

        // Search functionality with debounce
        const searchInput = document.querySelector('input[name="search"]');
        let searchTimeout;

        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);

            // Debounce search for better performance
            searchTimeout = setTimeout(() => {
                if (this.value.length === 0 || this.value.length >= 3) {
                    document.getElementById('searchForm').submit();
                }
            }, 500);
        });
    });
</script>
@endpush



