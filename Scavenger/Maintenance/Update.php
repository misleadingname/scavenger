<?php
require_once("../../vendor/autoload.php");
require_once("./Helpers.php");

global $httpContext;

displayIntro();

fLog("Checking for updates...");

$releases = httpGet("https://api.github.com/repos/misleadingname/scavenger/releases");
function findVersion(string $label, bool $ignorePrerelease = false): ?string
{
	global $releases;

	$tags = [];
	if(count($releases) <= 1) {
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
$latestVersion = $releases[findVersion("master")];

if($latestVersion["prerelease"] == 1) {
	fLog("This version is marked as prerelease, proceed with caution.", LogSeverity::Warn);
}

$dlLink = "https://github.com/misleadingname/scavenger/archive/refs/tags/{$latestVersion["tag_name"]}.zip";

fLog("Downloading {$latestVersion["name"]}... ($dlLink)");

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
if(!$page) {
	fLog("Error downloading file: " . curl_error($ch), LogSeverity::Error);
	die();
}

curl_close($ch);

$zipObj = new ZipArchive();

if($zipObj->open($zipLoc) != "true"){
	fLog("Error extracting zip.", LogSeverity::Error);
	die();
}

$newFolderName = basename(PROJECT_ROOT);
$zipObj->renameIndex(1, $newFolderName);

fLog($newFolderName);

$zipObj->extractTo(PROJECT_ROOT . "/../");
$zipObj->close();

fLog("Updated.");