<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Path {

    static public function Data(string $packageName, string $filePath): string {
        if (!file_exists(PATH_DATA))
            mkdir(PATH_DATA, 0777, true);

		$packageName = mb_strtolower($packageName);
		$package_path = PATH_DATA . '/' . $packageName;
		if (!file_exists($package_path))
	  		mkdir($package_path, 0777, true);

		$packageName = mb_strtolower($packageName);
		    $fsFilePath = PATH_DATA . '/' . $packageName . '/' . $filePath;
		    $fsFilePath = $package_path . '/' . $filePath;

		return $fsFilePath;
	}

    static public function Data_Exists(string $packageName, string $filePath): bool {
		$packageName = mb_strtolower($packageName);
        $fsFilePath = PATH_DATA . '/' . $packageName . '/' . $filePath;

		return file_exists($fsFilePath);
	}

	static public function File(string $ePath): string|null {
		return File::Path($ePath);
	}

	static public function Media(string $packageName, string $filePath): string {
        if (!file_exists(PATH_MEDIA))
            mkdir(PATH_MEDIA, 0777, true);

		$packageName = mb_strtolower($packageName);
		$package_path = PATH_MEDIA . '/' . $packageName;
		if (!file_exists($package_path))
	  		mkdir($package_path, 0777, true);

		$packageName = mb_strtolower($packageName);
		    $fsFilePath = PATH_MEDIA . '/' . $packageName . '/' . $filePath;
		    $fsFilePath = $package_path . '/' . $filePath;

		return $fsFilePath;
	}

	static public function Media_Exists(string $packageName, string $filePath): bool {
		$packageName = mb_strtolower($packageName);
        $fsFilePath = PATH_MEDIA . '/' . $packageName . '/' . $filePath;

		return file_exists($fsFilePath);
	}

}
