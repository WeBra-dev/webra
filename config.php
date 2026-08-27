<?php

$env = [];
$envFile = __DIR__ . '/.env';

if (is_readable($envFile)) {
	foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
		$line = trim($line);

		if ($line === '' || strpos($line, '#') === 0) {
			continue;
		}

		if (strpos($line, 'export ') === 0) {
			$line = substr($line, 7);
		}

		[$key, $value] = array_pad(explode('=', $line, 2), 2, '');
		$key = trim($key);
		$value = trim($value);

		if ($value !== '' && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
			$value = substr($value, 1, -1);
		}

		if ($key !== '') {
			$env[$key] = $value;
		}
	}
}

$id = getenv('DISCORD_WEBHOOK_ID') ?: ($env['DISCORD_WEBHOOK_ID'] ?? '');
$token = getenv('DISCORD_WEBHOOK_TOKEN') ?: ($env['DISCORD_WEBHOOK_TOKEN'] ?? '');
?>