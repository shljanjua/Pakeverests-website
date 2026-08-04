<?php
/** Admin panel helpers: layout, tables, forms, CRUD utilities. */

declare(strict_types=1);

function admin_url(string $path = ''): string
{
    return url('admin/' . ltrim($path, '/'));
}

function admin_active(string $module): string
{
    return ($GLOBALS['adminModule'] ?? '') === $module ? ' is-active' : '';
}

/** Counts used by the sidebar badges. */
function admin_badges(): array
{
    static $b = null;
    if ($b !== null) {
        return $b;
    }
    $safe = function (string $sql) {
        try { return (int) fetch_val($sql, [], 0); } catch (Throwable $e) { return 0; }
    };
    $b = [
        'orders'       => $safe('SELECT COUNT(*) FROM orders WHERE status = "new"'),
        'messages'     => $safe('SELECT COUNT(*) FROM contact_messages WHERE status = "new"'),
        'distributors' => $safe('SELECT COUNT(*) FROM distributor_applications WHERE status = "new"'),
        'labels'       => $safe('SELECT COUNT(*) FROM label_requests WHERE status = "new"'),
        'reviews'      => $safe('SELECT COUNT(*) FROM reviews WHERE status = "pending"'),
        'careers'      => $safe('SELECT COUNT(*) FROM job_applications WHERE status = "new"'),
    ];
    return $b;
}

/** Coloured status pill. */
function status_pill(string $status): string
{
    $map = [
        'new' => 'info', 'pending' => 'warn', 'read' => 'muted', 'replied' => 'ok',
        'confirmed' => 'info', 'out_for_delivery' => 'warn', 'delivered' => 'ok',
        'cancelled' => 'bad', 'rejected' => 'bad', 'approved' => 'ok', 'reviewing' => 'warn',
        'on_hold' => 'muted', 'archived' => 'muted', 'published' => 'ok', 'draft' => 'muted',
        'active' => 'ok', 'unsubscribed' => 'muted', 'sent' => 'info', 'signed' => 'ok',
        'accepted' => 'ok', 'declined' => 'bad', 'expired' => 'muted', 'failed' => 'bad',
        'quoted' => 'info', 'in_design' => 'warn', 'printing' => 'warn', 'shortlisted' => 'info',
        'interviewed' => 'warn', 'hired' => 'ok',
    ];
    $tone  = $map[$status] ?? 'muted';
    $label = ucwords(str_replace('_', ' ', $status));
    return '<span class="pill pill-' . $tone . '">' . e($label) . '</span>';
}

/** Render the admin page shell. */
function admin_header(string $title, string $subtitle = '', array $actions = []): void
{
    $GLOBALS['adminPageTitle']    = $title;
    $GLOBALS['adminPageSubtitle'] = $subtitle;
    $GLOBALS['adminPageActions']  = $actions;
    require PE_ROOT . '/app/admin/layout-header.php';
}

function admin_footer(): void
{
    require PE_ROOT . '/app/admin/layout-footer.php';
}

/** Generic delete handler used by the simple CRUD modules. */
function admin_handle_delete(string $table, string $label, string $redirect): void
{
    if (is_post() && post('action') === 'delete') {
        $id = (int) post('id');
        if ($id > 0) {
            db_delete($table, $id);
            admin_log('Deleted ' . $label, $table, $id);
            flash('success', $label . ' deleted.');
        }
        redirect($redirect);
    }
}

/** Toggle a boolean column. */
function admin_handle_toggle(string $table, array $allowedColumns, string $redirect): void
{
    if (is_post() && post('action') === 'toggle') {
        $id  = (int) post('id');
        $col = (string) post('column');
        if ($id > 0 && in_array($col, $allowedColumns, true)) {
            q("UPDATE `$table` SET `$col` = IF(`$col` = 1, 0, 1) WHERE id = ?", [$id]);
            admin_log('Toggled ' . $col, $table, $id);
        }
        redirect($redirect);
    }
}

/** Status update handler. */
function admin_handle_status(string $table, array $allowedStatuses, string $redirect, string $noteColumn = 'admin_note'): void
{
    if (is_post() && post('action') === 'status') {
        $id     = (int) post('id');
        $status = (string) post('status');
        if ($id > 0 && in_array($status, $allowedStatuses, true)) {
            $data = ['status' => $status];
            if ($noteColumn !== '' && isset($_POST[$noteColumn])) {
                $data[$noteColumn] = post($noteColumn);
            }
            db_update($table, $data, $id);
            admin_log('Updated status to ' . $status, $table, $id);
            flash('success', 'Status updated.');
        }
        redirect($redirect);
    }
}

/** A small inline delete form (button). */
function delete_button(int $id, string $confirm = 'Delete this item permanently?'): string
{
    return '<form method="post" class="inline-form" onsubmit="return confirm(' . ejs($confirm) . ');">'
        . csrf_field()
        . '<input type="hidden" name="action" value="delete">'
        . '<input type="hidden" name="id" value="' . $id . '">'
        . '<button type="submit" class="btn-icon btn-icon-danger" title="Delete" aria-label="Delete">'
        . '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>'
        . '</button></form>';
}

function toggle_button(int $id, string $column, bool $on, string $label = ''): string
{
    return '<form method="post" class="inline-form">'
        . csrf_field()
        . '<input type="hidden" name="action" value="toggle">'
        . '<input type="hidden" name="column" value="' . e($column) . '">'
        . '<input type="hidden" name="id" value="' . $id . '">'
        . '<button type="submit" class="switch' . ($on ? ' is-on' : '') . '" title="' . e($label ?: $column) . '" aria-label="Toggle ' . e($label ?: $column) . '">'
        . '<span></span></button></form>';
}

/** Build a sortable/pageable list query. */
function admin_list(string $table, array $options = []): array
{
    $perPage = (int) ($options['perPage'] ?? 25);
    $page    = max(1, (int) get('page', 1));
    $where   = $options['where'] ?? '1=1';
    $params  = $options['params'] ?? [];
    $order   = $options['order'] ?? 'id DESC';
    $select  = $options['select'] ?? '*';

    $total = (int) fetch_val("SELECT COUNT(*) FROM `$table` WHERE $where", $params, 0);
    $p     = paginate($total, $perPage, $page);
    $rows  = fetch_all("SELECT $select FROM `$table` WHERE $where ORDER BY $order LIMIT $perPage OFFSET {$p['offset']}", $params);

    return ['rows' => $rows, 'pagination' => $p];
}

/** Handle an admin image upload field, returning the stored path or the existing one. */
function admin_upload_field(string $field, string $folder, string $existing = '', array $allowed = PE_IMAGE_TYPES): string
{
    if (!empty($_FILES[$field]['name'])) {
        $up = handle_upload($_FILES[$field], $folder, $allowed);
        if ($up['ok']) {
            media_record($up, '', $folder);
            return $up['path'];
        }
        flash('error', 'Upload failed: ' . $up['error']);
    }
    /* Allow choosing an existing media path from a text/select input. */
    $manual = post($field . '_path', '');
    if (is_string($manual) && $manual !== '') {
        return $manual;
    }
    return $existing;
}

/** Export rows as a CSV download. */
function admin_export_csv(string $filename, array $rows): void
{
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // BOM so Excel reads UTF-8
    if ($rows) {
        fputcsv($out, array_keys($rows[0]));
        foreach ($rows as $row) {
            fputcsv($out, array_map(fn($v) => is_scalar($v) || $v === null ? (string) $v : json_encode($v), $row));
        }
    }
    fclose($out);
    exit;
}

/** Media picker: recent images the admin can attach without re-uploading. */
function recent_media(int $limit = 40): array
{
    try {
        return fetch_all('SELECT * FROM media WHERE file_type = "image" ORDER BY id DESC LIMIT ' . $limit);
    } catch (Throwable $e) {
        return [];
    }
}

/** Render an image upload control with preview and library picker. */
function image_field(string $name, string $current = '', string $label = 'Image', string $hint = ''): void
{
    $preview = media_url($current, 'placeholder');
    ?>
    <div class="form-group">
      <label for="<?= e($name) ?>"><?= e($label) ?></label>
      <div class="image-field">
        <img src="<?= e($preview) ?>" alt="" class="image-field-preview" id="preview_<?= e($name) ?>">
        <div class="image-field-controls">
          <input type="file" id="<?= e($name) ?>" name="<?= e($name) ?>" accept="image/*"
                 onchange="peImagePreview(this,'preview_<?= e($name) ?>')">
          <input type="text" name="<?= e($name) ?>_path" value="<?= e($current) ?>"
                 placeholder="or paste an existing path, e.g. uploads/products/2026/01/bottle.webp">
          <?php if ($hint): ?><span class="form-hint"><?= e($hint) ?></span><?php endif; ?>
        </div>
      </div>
    </div>
    <?php
}
