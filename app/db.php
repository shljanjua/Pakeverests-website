<?php
/**
 * Thin PDO wrapper — one shared connection, prepared statements everywhere.
 */

declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $c   = PE_CONFIG;
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $c['db_host'], $c['db_name'], $c['db_charset']);

    try {
        $pdo = new PDO($dsn, $c['db_user'], $c['db_pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode='NO_ENGINE_SUBSTITUTION'",
        ]);
    } catch (PDOException $e) {
        if (PE_DEBUG) {
            die('<h1>Database connection failed</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
        }
        http_response_code(503);
        die('<h1>Service temporarily unavailable</h1><p>We are performing maintenance. Please try again shortly.</p>');
    }

    return $pdo;
}

/** Run a query and return the statement. */
function q(string $sql, array $params = []): PDOStatement
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st;
}

/** Fetch a single row (or null). */
function fetch_one(string $sql, array $params = []): ?array
{
    $row = q($sql, $params)->fetch();
    return $row === false ? null : $row;
}

/** Fetch all rows. */
function fetch_all(string $sql, array $params = []): array
{
    return q($sql, $params)->fetchAll();
}

/** Fetch a single scalar value. */
function fetch_val(string $sql, array $params = [], $default = null)
{
    $val = q($sql, $params)->fetchColumn();
    return $val === false ? $default : $val;
}

/** Insert an associative array into a table, return the new id. */
function db_insert(string $table, array $data): int
{
    $cols = array_keys($data);
    $sql  = sprintf(
        'INSERT INTO `%s` (%s) VALUES (%s)',
        $table,
        '`' . implode('`,`', $cols) . '`',
        ':' . implode(',:', $cols)
    );
    q($sql, $data);
    return (int) db()->lastInsertId();
}

/** Update rows matching a simple `id = ?` condition. */
function db_update(string $table, array $data, int $id, string $pk = 'id'): int
{
    $sets = [];
    foreach (array_keys($data) as $col) {
        $sets[] = "`$col` = :$col";
    }
    $data['__pk'] = $id;
    $sql = sprintf('UPDATE `%s` SET %s WHERE `%s` = :__pk', $table, implode(', ', $sets), $pk);
    return q($sql, $data)->rowCount();
}

/** Delete a row by primary key. */
function db_delete(string $table, int $id, string $pk = 'id'): int
{
    return q("DELETE FROM `$table` WHERE `$pk` = ?", [$id])->rowCount();
}

/**
 * Can we reach the database at all? Used by the installer notice so a missing
 * config.local.php shows friendly instructions instead of a fatal error.
 */
function db_available(): bool
{
    static $ok = null;
    if ($ok !== null) {
        return $ok;
    }
    $c   = PE_CONFIG;
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $c['db_host'], $c['db_name'], $c['db_charset']);
    try {
        new PDO($dsn, $c['db_user'], $c['db_pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $ok = true;
    } catch (PDOException $e) {
        $GLOBALS['pe_db_error'] = $e->getMessage();
        $ok = false;
    }
    return $ok;
}

/** Does a table exist? (used by the health check / installer notice) */
function db_table_exists(string $table): bool
{
    if (!db_available()) {
        return false;
    }
    try {
        db()->query("SELECT 1 FROM `$table` LIMIT 1");
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
