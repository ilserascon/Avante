<style>
    .admin-pro .hero-card {
        border: 0;
        border-radius: 16px;
        background: linear-gradient(135deg, #f8fbff 0%, #eef4ff 100%);
        box-shadow: 0 10px 25px rgba(43, 65, 98, 0.08);
    }

    .admin-pro .hero-card h3,
    .admin-pro .hero-card h2 {
        margin: 0;
        font-weight: 700;
        letter-spacing: 0.2px;
        color: #26344d;
    }

    .admin-pro .hero-subtitle {
        color: #6b7b95;
        margin-top: 0.25rem;
        margin-bottom: 0;
    }

    .admin-pro .filter-card,
    .admin-pro .table-card,
    .admin-pro .form-card,
    .admin-pro .section-card {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(21, 38, 63, 0.08);
        margin-bottom: 1.25rem;
    }

    .admin-pro .filter-card .card-body,
    .admin-pro .form-card .card-body {
        padding: 1rem 1.25rem;
    }

    .admin-pro .form-card .card-header,
    .admin-pro .section-card .card-header {
        background: #f5f8ff;
        border-bottom: 1px solid #e8eef8;
        padding: 0.9rem 1.25rem;
    }

    .admin-pro .form-card .card-header h5,
    .admin-pro .section-card .card-header h5 {
        margin: 0;
        color: #26344d;
        font-weight: 700;
    }

    .admin-pro .form-card .card-header .text-muted,
    .admin-pro .section-card .card-header .text-muted {
        font-size: 0.82rem;
    }

    .admin-pro .table thead th {
        background: #f5f8ff;
        color: #4a5f83;
        font-weight: 700;
        border-bottom: 0;
        white-space: nowrap;
    }

    .admin-pro .table tbody td {
        vertical-align: middle;
    }

    .admin-pro .table tbody tr {
        transition: all .25s ease;
    }

    .admin-pro .table tbody tr:hover {
        background: #f8fbff;
        transform: translateY(-2px);
        box-shadow: 0 3px 10px rgba(0, 0, 0, .05);
    }

    .admin-pro .table tbody tr:hover td {
        color: #243b63;
        background: #f8fbff;
    }

    .admin-pro .record-link {
        font-weight: 700;
        color: #4e73df;
        text-decoration: none;
        transition: .2s;
    }

    .admin-pro .record-link:hover {
        color: #1a8683;
        text-decoration: none;
    }

    .admin-pro .table tbody tr:hover .record-link {
        color: #1a8683;
    }

    .admin-pro .money-value {
        font-weight: 700;
        color: #1f3a69;
    }

    .admin-pro .actions-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        justify-content: flex-end;
    }

    .admin-pro .action-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        transition: all 0.15s ease-in-out;
        padding: 0;
    }

    .admin-pro .action-btn i {
        font-size: 0.85rem;
    }

    .admin-pro .action-btn:hover {
        transform: translateY(-1px);
    }

    .admin-pro .btn-see { background: #e7f1ff; color: #1d4ed8; border-color: #cfe2ff; }
    .admin-pro .btn-edit { background: #fff5df; color: #b45309; border-color: #ffe3ae; }
    .admin-pro .btn-delete { background: #ffecec; color: #dc2626; border-color: #fecaca; }
    .admin-pro .btn-enable { background: #e8f9ee; color: #15803d; border-color: #bbf7d0; }
    .admin-pro .btn-info-action { background: #e7f1ff; color: #1d4ed8; border-color: #cfe2ff; }

    .admin-pro .field-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #6b7b95;
        font-weight: 700;
        margin-bottom: 0.35rem;
    }

    .admin-pro .item-row {
        background: #fff;
        border: 1px solid #e8eef8;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 0.75rem;
    }

    .admin-pro .item-row:last-child {
        margin-bottom: 0;
    }

    .admin-pro .actions-bar .btn,
    .admin-pro .hero-actions .btn {
        border-radius: 10px;
    }

    .admin-pro .empty-state {
        text-align: center;
        color: #6b7b95;
        padding: 2.5rem 1rem;
    }

    .admin-pro .empty-state i {
        opacity: 0.4;
        margin-bottom: 0.75rem;
    }

    .admin-pro .status-chip {
        display: inline-block;
        padding: 0.28rem 0.65rem;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.2px;
        text-transform: uppercase;
    }

    .admin-pro .status-active { color: #166534; background: #dcfce7; }
    .admin-pro .status-inactive { color: #991b1b; background: #fee2e2; }

    .admin-pro .meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .admin-pro .meta-item {
        background: #fff;
        border: 1px solid #e8eef8;
        border-radius: 12px;
        padding: 0.85rem 1rem;
    }

    .admin-pro .meta-item .label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #6b7b95;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .admin-pro .meta-item .value {
        color: #26344d;
        font-weight: 600;
        word-break: break-word;
    }

    .admin-pro .cantidad-badge {
        display: inline-block;
        min-width: 2.5rem;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        background: #eef4ff;
        color: #1f3a69;
        font-weight: 700;
        text-align: center;
    }

    @media (max-width: 767.98px) {
        .admin-pro .hero-actions {
            margin-top: 0.75rem;
            width: 100%;
        }

        .admin-pro .hero-actions .btn {
            width: 100%;
        }
    }
</style>
