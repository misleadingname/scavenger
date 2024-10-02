<?php
require_once("../../vendor/autoload.php");
require_once("./Helpers.php");

global $httpContext;

displayIntro();
fLog("Checking for updates...");

$semverPattern = '/^\d+\.\d+\.\d+-(master|canary|beta|stable)$/';
$versionFile = PROJECT_ROOT . "/VERSION.txt";

if (!file_exists(PROJECT_ROOT . "/VERSION.txt")) {
	fLog("Version not found, getting latest stable!!!");
	$fullVersion = "0.0.0-stable";
} else {
	$handle = fopen(PROJECT_ROOT . "/VERSION.txt", 'r');
	$line = trim(fgets($handle));
	fclose($handle);

	if (preg_match($semverPattern, $line)) {
		$fullVersion = $line;
	} else {
		fLog("Invalid version format ($line), getting latest stable!!!");
		$fullVersion = "0.0.0-stable";
	}
}

if (!preg_match($semverPattern, $fullVersion)) {
	fLog("Invalid version format ($fullVersion), getting latest stable!!!");
	$fullVersion = "0.0.0-stable";
}

$label = trim(explode("-", $fullVersion)[1]);
$dlLink = "";

fLog("Updating channel $label...");

if ($label === "master") {
	fLog("Downloading latest master, proceed with insane caution!!!", LogSeverity::Warn);
	$dlLink = "https://github.com/misleadingname/scavenger/archive/refs/heads/master.zip";
	fLog("Downloading latest master... ($dlLink)");
} elseif ($label === "stable") {
	$releases = httpGet("https://api.github.com/repos/misleadingname/scavenger/releases");

	function findVersion(string $label, bool $ignorePrerelease = false): ?string
	{
		global $releases;

		if (empty($releases)) {
			fLog("No stable release yet, check back later...", LogSeverity::Error);
			die();
		}

		$tags = array_filter($releases, function ($version) use ($label, $ignorePrerelease) {
			return preg_match("/^\d+\.\d+\.\d+-{$label}$/", $version['tag_name'])
				&& (!$ignorePrerelease || !$version['prerelease']);
		});

		usort($tags, function ($a, $b) {
			return version_compare(explode('-', $b['tag_name'])[0], explode('-', $a['tag_name'])[0]);
		});

		return $tags[0]['tag_name'] ?? null;
	}

	$latestVersion = $releases[findVersion($label)];
	if ($latestVersion['prerelease'] == 1) {
		fLog("This version is marked as prerelease, proceed with caution.", LogSeverity::Warn);
	}

	$dlLink = "https://github.com/misleadingname/scavenger/archive/refs/tags/{$latestVersion['tag_name']}.zip";
	fLog("Downloading {$latestVersion['name']}... ($dlLink)");
} else {
	fLog("Invalid channel", LogSeverity::Error);
	die();
}

$zipLoc = tempnam(sys_get_temp_dir(), "scavengerdownload");
$zipHandle = fopen($zipLoc, "w");

$ch = curl_init($dlLink);
curl_setopt_array($ch, [
	CURLOPT_FAILONERROR => true,
	CURLOPT_HEADER => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_AUTOREFERER => true,
	CURLOPT_TIMEOUT => 10,
	CURLOPT_SSL_VERIFYHOST => 0,
	CURLOPT_SSL_VERIFYPEER => 0,
	CURLOPT_FILE => $zipHandle
]);

if (!curl_exec($ch)) {
	fLog("Error downloading file: " . curl_error($ch), LogSeverity::Error);
	die();
}

curl_close($ch);
fclose($zipHandle);

$zipObj = new ZipArchive();
if ($zipObj->open($zipLoc) !== true) {
	fLog("Error preparing zip.", LogSeverity::Error);
	die();
}

$newFolderName = basename(PROJECT_ROOT);
for ($i = 0; $i < $zipObj->numFiles; $i++) {
	$fileInfo = $zipObj->statIndex($i);
	$oldName = $fileInfo['name'];

	if (str_contains($oldName, '/')) {
		$pathParts = explode('/', $oldName);
		$pathParts[0] = $newFolderName;
		$newName = implode('/', $pathParts);

		if (str_starts_with($newName, basename(PROJECT_ROOT) . "/App") && $newName !== basename(PROJECT_ROOT) . "/App/Public/index.php") {
			fLog("Discarding! $newName");
			if (!$zipObj->deleteName($oldName)) {
				fLog("Error deleting file inside of zip: $oldName", LogSeverity::Error);
				die();
			}
			continue;
		}

		fLog("$oldName -> $newName");
		if (!$zipObj->renameName($oldName, $newName)) {
			fLog("Error renaming file inside zip: $oldName", LogSeverity::Error);
			die();
		}
	}
}

$zipObj->close();

if ($zipObj->open($zipLoc) !== true) {
	fLog("Error trying to extract zip.", LogSeverity::Error);
	die();
}

$zipObj->extractTo(PROJECT_ROOT . "/../");
$zipObj->close();

fLog("Updated.");