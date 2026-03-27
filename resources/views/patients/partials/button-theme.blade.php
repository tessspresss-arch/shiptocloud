@once
<style>
    .patient-module-btn {
        --patient-btn-height: 48px;
        --patient-btn-radius: 16px;
        --patient-btn-padding-x: 18px;
        --patient-btn-border: rgba(191, 207, 223, 0.95);
        --patient-btn-bg: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(244, 248, 253, 0.94) 100%);
        --patient-btn-color: #385674;
        --patient-btn-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.92), 0 16px 28px -24px rgba(15, 23, 42, 0.22);
        --patient-btn-hover-border: rgba(44, 123, 229, 0.32);
        --patient-btn-hover-bg: linear-gradient(180deg, rgba(255, 255, 255, 0.99) 0%, rgba(236, 244, 251, 0.98) 100%);
        --patient-btn-hover-color: #1f6fa3;
        --patient-btn-hover-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.96), 0 18px 32px -24px rgba(31, 111, 163, 0.24);
        min-height: var(--patient-btn-height);
        padding: 0 var(--patient-btn-padding-x);
        border-radius: var(--patient-btn-radius);
        border: 1px solid var(--patient-btn-border);
        background: var(--patient-btn-bg);
        color: var(--patient-btn-color);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-size: 0.95rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.01em;
        text-decoration: none;
        box-shadow: var(--patient-btn-shadow);
        cursor: pointer;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease, background 0.18s ease, color 0.18s ease, opacity 0.18s ease;
    }

    .patient-module-btn:hover,
    .patient-module-btn:focus-visible {
        color: var(--patient-btn-hover-color);
        border-color: var(--patient-btn-hover-border);
        background: var(--patient-btn-hover-bg);
        box-shadow: var(--patient-btn-hover-shadow);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .patient-module-btn:focus-visible {
        outline: none;
        box-shadow: 0 0 0 4px rgba(44, 123, 229, 0.12), var(--patient-btn-hover-shadow);
    }

    .patient-module-btn:active {
        transform: translateY(0);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.92), 0 10px 20px -18px rgba(15, 23, 42, 0.18);
    }

    .patient-module-btn:disabled,
    .patient-module-btn[disabled],
    .patient-module-btn[aria-disabled="true"] {
        opacity: 0.58;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
        pointer-events: none;
    }

    .patient-module-btn__icon {
        width: 30px;
        height: 30px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: rgba(44, 123, 229, 0.1);
        color: #2c7be5;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.74);
    }

    .patient-module-btn--surface {
        --patient-btn-border: rgba(191, 207, 223, 0.95);
        --patient-btn-bg: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(244, 248, 253, 0.94) 100%);
        --patient-btn-color: #385674;
        --patient-btn-hover-border: rgba(44, 123, 229, 0.3);
        --patient-btn-hover-bg: linear-gradient(180deg, rgba(255, 255, 255, 0.99) 0%, rgba(236, 244, 251, 0.98) 100%);
        --patient-btn-hover-color: #1f6fa3;
    }

    .patient-module-btn--primary {
        --patient-btn-border: rgba(36, 94, 194, 0.76);
        --patient-btn-bg: linear-gradient(135deg, #2c7be5 0%, #1f6fa3 100%);
        --patient-btn-color: #ffffff;
        --patient-btn-shadow: 0 18px 30px -22px rgba(44, 123, 229, 0.46);
        --patient-btn-hover-border: rgba(31, 111, 163, 0.92);
        --patient-btn-hover-bg: linear-gradient(135deg, #256fd5 0%, #1b648f 100%);
        --patient-btn-hover-color: #ffffff;
        --patient-btn-hover-shadow: 0 20px 34px -22px rgba(44, 123, 229, 0.54);
    }

    .patient-module-btn--primary .patient-module-btn__icon {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
    }

    .patient-module-btn--wide {
        width: 100%;
    }

    .patient-module-segmented {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px;
        border-radius: 16px;
        border: 1px solid #d6e2ef;
        background: linear-gradient(180deg, #ffffff 0%, #f5f9fe 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.88), 0 16px 24px -26px rgba(15, 23, 42, 0.16);
    }

    .patient-module-segmented__option {
        min-height: 40px;
        padding: 0 14px;
        border-radius: 12px;
        border: 1px solid transparent;
        color: #486178;
        font-size: 0.84rem;
        font-weight: 800;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        white-space: nowrap;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease, background 0.18s ease, color 0.18s ease;
    }

    .patient-module-segmented__option .patient-module-btn__icon {
        width: 24px;
        height: 24px;
        border-radius: 8px;
        font-size: 0.72rem;
    }

    .patient-module-segmented__option:hover,
    .patient-module-segmented__option:focus-visible {
        color: #1e4f88;
        border-color: rgba(44, 123, 229, 0.14);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(238, 245, 255, 0.98) 100%);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .patient-module-segmented__option:focus-visible {
        outline: none;
        box-shadow: 0 0 0 4px rgba(44, 123, 229, 0.1);
    }

    .patient-module-segmented__option.active {
        border-color: rgba(36, 94, 194, 0.72);
        background: linear-gradient(135deg, #2c7be5 0%, #1f6fa3 100%);
        color: #ffffff;
        box-shadow: 0 18px 26px -22px rgba(44, 123, 229, 0.46);
    }

    .patient-module-segmented__option.active .patient-module-btn__icon {
        background: rgba(255, 255, 255, 0.16);
        color: #ffffff;
    }

    .patient-module-icon-btn {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        border: 1px solid #d8e3ef;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(246, 250, 255, 0.94) 100%);
        color: #557089;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.88), 0 14px 24px -24px rgba(15, 23, 42, 0.24);
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease, background 0.18s ease, color 0.18s ease, opacity 0.18s ease;
    }

    .patient-module-icon-btn:hover,
    .patient-module-icon-btn:focus-visible {
        text-decoration: none;
        transform: translateY(-1px);
    }

    .patient-module-icon-btn:focus-visible {
        outline: none;
        box-shadow: 0 0 0 4px rgba(44, 123, 229, 0.1), 0 16px 26px -22px rgba(15, 23, 42, 0.22);
    }

    .patient-module-icon-btn:active {
        transform: translateY(0);
    }

    .patient-module-icon-btn:disabled,
    .patient-module-icon-btn[disabled],
    .patient-module-icon-btn[aria-disabled="true"] {
        opacity: 0.58;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
        pointer-events: none;
    }

    .patient-module-icon-btn--view {
        color: #215dc1;
        border-color: #ccdcf2;
        background: linear-gradient(180deg, #f4f8ff 0%, #ebf3ff 100%);
    }

    .patient-module-icon-btn--view:hover,
    .patient-module-icon-btn--view:focus-visible {
        color: #ffffff;
        border-color: #2563eb;
        background: linear-gradient(135deg, #2563eb 0%, #1d4fbe 100%);
        box-shadow: 0 18px 24px -22px rgba(37, 99, 235, 0.42);
    }

    .patient-module-icon-btn--edit {
        color: #b7791f;
        border-color: #f4d9a6;
        background: linear-gradient(180deg, #fffaf0 0%, #fff3d6 100%);
    }

    .patient-module-icon-btn--edit:hover,
    .patient-module-icon-btn--edit:focus-visible {
        color: #ffffff;
        border-color: #f59e0b;
        background: linear-gradient(135deg, #f59e0b 0%, #d88908 100%);
        box-shadow: 0 18px 24px -22px rgba(245, 158, 11, 0.38);
    }

    .patient-module-icon-btn--schedule {
        color: #0f766e;
        border-color: #bfe5dd;
        background: linear-gradient(180deg, #f0fdfa 0%, #d7f7ef 100%);
    }

    .patient-module-icon-btn--schedule:hover,
    .patient-module-icon-btn--schedule:focus-visible {
        color: #ffffff;
        border-color: #0f766e;
        background: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%);
        box-shadow: 0 18px 24px -22px rgba(20, 184, 166, 0.4);
    }

    .patient-module-icon-btn--archive {
        color: #8a4d63;
        border-color: #eccfda;
        background: linear-gradient(180deg, #fff8fb 0%, #fde8ef 100%);
    }

    .patient-module-icon-btn--archive:hover,
    .patient-module-icon-btn--archive:focus-visible {
        color: #ffffff;
        border-color: #e11d48;
        background: linear-gradient(135deg, #ef4444 0%, #e11d48 100%);
        box-shadow: 0 18px 24px -22px rgba(239, 68, 68, 0.38);
    }

    .patients-management-page .patient-module-btn.btn-custom,
    .patients-management-page .patient-module-btn.header-back-btn,
    .patient-create-page .patient-module-btn.header-return-btn,
    .patient-create-page .patient-module-btn.btn-gradient-blue,
    .patient-create-page .patient-module-btn.sidebar-reset-btn,
    .patient-edit-page .patient-module-btn.btn-custom,
    .patient-edit-page .patient-module-btn.header-back-btn {
        min-height: var(--patient-btn-height);
        padding: 0 var(--patient-btn-padding-x);
        border-radius: var(--patient-btn-radius);
        border: 1px solid var(--patient-btn-border);
        background: var(--patient-btn-bg);
        color: var(--patient-btn-color);
        box-shadow: var(--patient-btn-shadow);
        gap: 10px;
        font-size: 0.95rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.01em;
        text-decoration: none;
    }

    .patients-management-page .table-head-tools .patient-module-segmented {
        display: inline-flex;
    }

    .patients-management-page .table-head-tools .patient-module-segmented__option {
        min-height: 40px;
        padding: 0 14px;
        border-radius: 12px;
    }

    .patients-management-page .patient-module-icon-btn.action-btn,
    .patients-management-page .patient-module-icon-btn.action-btn-primary {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.88), 0 14px 24px -24px rgba(15, 23, 42, 0.24);
    }

    body.dark-mode .patient-module-btn,
    body.theme-dark .patient-module-btn {
        --patient-btn-border: #3a5b80;
        --patient-btn-bg: linear-gradient(180deg, rgba(21, 38, 60, 0.96) 0%, rgba(17, 33, 52, 0.92) 100%);
        --patient-btn-color: #d8e8fb;
        --patient-btn-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03), 0 18px 30px -24px rgba(0, 0, 0, 0.45);
        --patient-btn-hover-border: #5f89b8;
        --patient-btn-hover-bg: linear-gradient(180deg, rgba(37, 64, 97, 0.96) 0%, rgba(31, 53, 81, 0.94) 100%);
        --patient-btn-hover-color: #ffffff;
        --patient-btn-hover-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04), 0 22px 34px -24px rgba(0, 0, 0, 0.44);
    }

    body.dark-mode .patient-module-btn__icon,
    body.theme-dark .patient-module-btn__icon {
        background: rgba(119, 183, 255, 0.16);
        color: #9fd0ff;
    }

    body.dark-mode .patient-module-btn--primary,
    body.theme-dark .patient-module-btn--primary {
        --patient-btn-border: rgba(59, 130, 246, 0.85);
        --patient-btn-bg: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        --patient-btn-color: #ffffff;
        --patient-btn-shadow: 0 18px 28px -20px rgba(37, 99, 235, 0.42);
        --patient-btn-hover-border: rgba(96, 165, 250, 0.96);
        --patient-btn-hover-bg: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        --patient-btn-hover-color: #ffffff;
    }

    body.dark-mode .patient-module-btn--primary .patient-module-btn__icon,
    body.theme-dark .patient-module-btn--primary .patient-module-btn__icon {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
    }

    body.dark-mode .patient-module-segmented,
    body.theme-dark .patient-module-segmented {
        border-color: #345172;
        background: linear-gradient(180deg, #13283f 0%, #102238 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
    }

    body.dark-mode .patient-module-segmented__option,
    body.theme-dark .patient-module-segmented__option {
        color: #d4e8ff;
    }

    body.dark-mode .patient-module-segmented__option:hover,
    body.dark-mode .patient-module-segmented__option:focus-visible,
    body.theme-dark .patient-module-segmented__option:hover,
    body.theme-dark .patient-module-segmented__option:focus-visible {
        color: #ffffff;
        border-color: rgba(96, 165, 250, 0.18);
        background: linear-gradient(180deg, #1c3e67 0%, #163758 100%);
    }

    body.dark-mode .patient-module-segmented__option.active,
    body.theme-dark .patient-module-segmented__option.active {
        border-color: rgba(59, 130, 246, 0.9);
        background: linear-gradient(135deg, #2563eb 0%, #1d4fbe 100%);
        color: #ffffff;
    }

    body.dark-mode .patient-module-icon-btn,
    body.theme-dark .patient-module-icon-btn {
        border-color: #345172;
        background: linear-gradient(180deg, #15263c 0%, #112238 100%);
        color: #abc1db;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03), 0 12px 20px -18px rgba(0, 0, 0, 0.36);
    }

    body.dark-mode .patient-module-icon-btn--view,
    body.theme-dark .patient-module-icon-btn--view {
        color: #9fc2ff;
        border-color: #416184;
        background: linear-gradient(180deg, #183255 0%, #17304f 100%);
    }

    body.dark-mode .patient-module-icon-btn--edit,
    body.theme-dark .patient-module-icon-btn--edit {
        color: #f8c768;
        border-color: #67522e;
        background: linear-gradient(180deg, #3d2a12 0%, #33230f 100%);
    }

    body.dark-mode .patient-module-icon-btn--schedule,
    body.theme-dark .patient-module-icon-btn--schedule {
        color: #74e2d8;
        border-color: #2b645f;
        background: linear-gradient(180deg, #113836 0%, #102f2d 100%);
    }

    body.dark-mode .patient-module-icon-btn--archive,
    body.theme-dark .patient-module-icon-btn--archive {
        color: #f6aac0;
        border-color: #6b3647;
        background: linear-gradient(180deg, #381823 0%, #30141d 100%);
    }

    @media (max-width: 768px) {
        .patient-module-btn {
            --patient-btn-height: 46px;
            --patient-btn-radius: 14px;
            --patient-btn-padding-x: 16px;
        }

        .patient-module-segmented {
            width: 100%;
            justify-content: stretch;
            overflow-x: auto;
        }

        .patient-module-segmented__option {
            flex: 1 0 auto;
            min-width: max-content;
        }

        .patient-module-icon-btn {
            width: 42px;
            height: 42px;
            border-radius: 13px;
        }
    }
</style>
@endonce
