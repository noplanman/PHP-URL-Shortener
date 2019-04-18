<?php
/*
 * First authored by Brian Cray
 * License: http://creativecommons.org/licenses/by/3.0/
 * Contact the author at http://briancray.com/
 *
 * Improved by Armando Lüscher <https://noplanman.ch>
 */

ini_set('display_errors', 0);

require_once 'config.php';

$url_id = $_GET['url'] ?? '';
if (!preg_match('|^[' . ALLOWED_CHARS . ']{' . URL_ID_LENGTH . '}$|', $url_id)) {
    die('That is not a valid short url');
}

$url = $pdo->query('SELECT `url` FROM ' . DB_TABLE . ' WHERE `id`=' . $pdo->quote($url_id))->fetchColumn();
if (empty($url)) {
    die('That short url does not exist');
}

if (TRACK) {
    $pdo->exec('UPDATE ' . DB_TABLE . ' SET `clicks`=`clicks`+1 WHERE `id`=' . $pdo->quote($url_id));
}

header('HTTP/1.1 ' . REDIRECT_RESPONSE_CODE);
header('Location: ' . $url);
exit;
