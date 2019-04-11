<?php
// --
function dir_get_files(string $path) : array {
	$r = false;
	
	$load = null;
	$load = function($path) use(&$load) {
		$path = rtrim($path, "/");
		$r = [];
		if (file_exists($path) && ($resource = opendir($path))) {
			$files = [];
			$directories = [];
			while (($item = readdir($resource)) !== false) {
				if ($item !== "." && $item !== "..") {
					$item = $path . "/" . $item;
					if (is_file($item)) {
						$files[] = $item;
					} elseif (is_dir($item)) {
						$directories[] = $item;
					}
				}
			}
			sort($files);
			sort($directories);
			$r = array_merge($r, $files);
			foreach ($directories as $directory) {
				$r = array_merge($r, $load($directory));
			}
		}
		return $r;
	};
	
	$r = $load($path);
	
	return $r;
}
// --
function file_get_extension(string $path) : string {
	return pathinfo($path, PATHINFO_EXTENSION);
}
// --
function mtg_str_clean_content(string $string) : string {
	// --
	$r = html_entity_decode($string);
	// --
	$search = [
		"</a>",
		"<p></p>",
		"<br>",
		"<hr>"
	];
	$replace = [
		"",
		"",
		"<br />",
		"\n\n<hr />\n\n"
	];
	$r = str_replace($search, $replace, $r);
	// --
	$r = preg_replace("/<a[^>]*>/", "", $r);
	// --
	$r = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $r);
	// --
	$search = "/<div[^>]*><img.+src=\".*\/(.*?)\".*><\/div>\s*<p[^>]*>(.*)<\/p>/u";
	$replace = "<figure>\n\t<img alt=\"$2\" src=\"Images/$1\" />\n\t<figcaption>$2</figcaption>\n</figure>";
	$r = preg_replace($search, $replace, $r);
	$search = "/<div[^>]*>\s*<figure>\s*<img.+src=\".*\/(.*?)\".*>\s*<figcaption[^>]*>(.*)<\/figcaption>\s*<\/figure>\s*<\/div>/u";
	$r = preg_replace($search, $replace, $r);
	
	return $r;
}
// --
function uuid(bool $alpha = false) : string {
	$uuid = function() {
		return sprintf("%04x%04x%04x%04x%04x%04x%04x%04x",
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0fff ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	};
	$r = $uuid();
	if ($alpha) {
		while (!in_array($r[0], ["a", "b", "c", "d", "e", "f"])) {
			$r = $uuid();
		}
	}
	
	return $r;
}
?>
