<?php
/*
 * First authored by Brian Cray
 * License: http://creativecommons.org/licenses/by/3.0/
 * Contact the author at http://briancray.com/
 *
 * Improved by Armando Lüscher <https://noplanman.ch>
 */

ini_set('display_errors', 0);

$url_to_shorten = get_magic_quotes_gpc() ? stripslashes(trim($_REQUEST['url'])) : trim($_REQUEST['url']);

// Make sure we have a valid URL
if (empty($url_to_shorten) || !preg_match('|^https?://|', $url_to_shorten)) {
    die('Invalid URL');
}

require_once __DIR__ . '/config.php';

// Check if the client IP is allowed to shorten
if (!in_array($_SERVER['REMOTE_ADDR'], LIMIT_TO_IPS, true)) {
    die('You are not allowed to shorten URLs with this service.');
}

// Check if this URL has already been shortened
$url_id = $pdo->query('SELECT `id` FROM ' . DB_TABLE . ' WHERE `url`=' . $pdo->quote($url_to_shorten))->fetchColumn();
if (empty($url_id)) {
    $sth = $pdo->prepare('INSERT INTO ' . DB_TABLE . ' (id, url, creator) VALUES (?, ?, ?)');

    // Failsafe for duplicate ID generation.
    $fails = 0;
    do {
        try {
            $url_id = generateUrlId();
            $ok     = $sth->execute([$url_id, $url_to_shorten, $_SERVER['REMOTE_ADDR']]);
        } catch (Throwable $e) {
            // Silent catch
        }
    } while (!$ok && ++$fails < 5);

    if ($fails >= 5) {
        die('Need to increase the URL ID character count.');
    }
}

echo BASE_HREF . $url_id;

/**
 * Generate a random URL ID
 *
 * @param int $length
 *
 * @return string
 * @throws Exception
 */
function generateUrlId($length = URL_ID_LENGTH): string
{
    $chars_num = strlen(ALLOWED_CHARS);
    $url_id    = '';

    while ($length--) {
        $url_id .= ALLOWED_CHARS[random_int(0, $chars_num - 1)];
    }

    return $url_id;
}
