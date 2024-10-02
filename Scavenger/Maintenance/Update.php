<?php
require_once("../../vendor/autoload.php");
require_once("./Helpers.php");

global $httpContext;

displayIntro();

fLog("Checking for updates...");

if (!file_exists(PROJECT_ROOT . "/VERSION.txt")) {
	fLog("Version not found, getting latest stable!!!");
	$fullVersion = "0.0.0-stable";
} else {
	$fullVersion = $line = fgets(fopen(PROJECT_ROOT . "/VERSION.txt", 'r'));
}


$label = trim(explode("-", $fullVersion)[1]);
$dlLink = "";

fLog("Updating channel $label...");

if ($label == "master") {
	fLog("Downloading latest master, proceed with insane caution!!!", LogSeverity::Warn);
	$dlLink = "https://github.com/misleadingname/scavenger/archive/refs/heads/master.zip";
	fLog("Downloading latest master... ($dlLink)");
} else {
	$releases = httpGet("https://api.github.com/repos/misleadingname/scavenger/releases");
	function findVersion(string $label, bool $ignorePrerelease = false): ?string
	{
		global $releases;

		$tags = [];
		if(count($releases) == 0) {
			fLog("No stable release yet, check back later...", LogSeverity::Error);
			die();
		}

		if (count($releases) <= 1) {
			$version = $releases[0];

			$releases[$version["tag_name"]] = $version;
			$tags[] = $version["tag_name"];
		} else {
			for ($i = 0; $i > count($releases); $i++) {
				$version = $releases[$i];

				if ($ignorePrerelease && $version["prerelease"] === 1) {
					continue;
				}

				$releases[$version["tag_name"]] = $version;
				$tags[] = $version["tag_name"];
			}
		}

		$filteredVersions = array_filter($tags, function ($version) use ($label) {
			return preg_match("/^\d+\.\d+\.\d+-{$label}$/", $version);
		});

		if (empty($filteredVersions)) {
			return null;
		}

		usort($filteredVersions, function ($a, $b) {
			$aNumeric = explode('-', $a)[0];
			$bNumeric = explode('-', $b)[0];

			return version_compare($bNumeric, $aNumeric);
		});

		return $filteredVersions[0];
	}

	$c = count($releases);
	fLog("Queried $c releases.");
	$latestVersion = $releases[findVersion($label)];

	if ($latestVersion["prerelease"] == 1) {
		fLog("This version is marked as prerelease, proceed with caution.", LogSeverity::Warn);
	}

	$dlLink = "https://github.com/misleadingname/scavenger/archive/refs/tags/{$latestVersion["tag_name"]}.zip";

	fLog("Downloading {$latestVersion["name"]}... ($dlLink)");
}

$zipLoc = tempnam(sys_get_temp_dir(), "scavengerdownload");
$zipHandle = fopen($zipLoc, "w");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $dlLink);
curl_setopt($ch, CURLOPT_FAILONERROR, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_AUTOREFERER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_FILE, $zipHandle);
$page = curl_exec($ch);
if (!$page) {
	fLog("Error downloading file: " . curl_error($ch), LogSeverity::Error);
	die();
}

curl_close($ch);

$zipObj = new ZipArchive();

if ($zipObj->open($zipLoc) != "true") {
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

if ($zipObj->open($zipLoc) != "true") {
	fLog("Error trying to extract zip.", LogSeverity::Error);
	die();
}

$zipObj->extractTo(PROJECT_ROOT . "/../");
$zipObj->close();

fLog("Updated.");
