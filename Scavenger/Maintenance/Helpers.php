<?php



enum LogSeverity: int {
	case Info = 0;
	case Warn = 1;
	case Error = 2;
}

function displayIntro()
{
	print("
  ___  ___ __ ___   _____ _ __   __ _  ___ _ __ 
 / __|/ __/ _` \ \ / / _ \ '_ \ / _` |/ _ \ '__|
 \__ \ (_| (_| |\ V /  __/ | | | (_| |  __/ |   
 |___/\___\__,_| \_/ \___|_| |_|\__, |\___|_|   
                                 __/ |          
                                |___/           
\n-------------------------Maintenance Scripts----\n");
}

function getSeverityColor(LogSeverity $severity): string {
	return match ($severity) {
		LogSeverity::Info => "\033[32m",  // Green for Info
		LogSeverity::Warn => "\033[33m",  // Yellow for Warning
		LogSeverity::Error => "\033[31m", // Red for Error
	};
}

function resetColor(): string {
	return "\033[0m"; // Reset to default terminal color
}

function fLog(string $message = null, LogSeverity $severity = LogSeverity::Info, $newline = true, string $filePath = 'log.log') {
	$timestamp = date('Y-m-d H:i:s');

	$newlinee = $newline ? "\n" : "";
	$carrig = str_starts_with($message, "\r") ? "\r" : "";

	$message = str_replace("\r", "", $message);

	$severityLabel = match($severity) {
		LogSeverity::Info => 'INFO',
		LogSeverity::Warn => 'WARNING',
		LogSeverity::Error => 'ERROR',
	};

	$color = getSeverityColor($severity);
	$reset = resetColor();

	$formattedMessage = "{$carrig}[$timestamp] [$color$severityLabel$reset]: $message$newlinee";
	print("$formattedMessage");

	$plainMessage = "[$timestamp] [$severityLabel]: $message$newlinee";
	file_put_contents($filePath, $plainMessage, FILE_APPEND);
}

function httpGet(string $url): array {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);


	curl_setopt($curl, CURLOPT_HTTPHEADER, [
		"Method: GET",
		"X-GitHub-Api-Version: 2022-11-28",
		"Accept: application/vnd.github+json",
		"User-Agent: ScavengerMaintenanceScript",
	]);

	return json_decode(curl_exec($curl), true);
}