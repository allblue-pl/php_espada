<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Path {

    static public function Data($package_name, $file_path)
	{
        if (!file_exists(PATH_DATA))
            mkdir(PATH_DATA, 0777, true);

		$package_name = mb_strtolower($package_name);
		$package_path = PATH_DATA . '/' . $package_name;
		if (!file_exists($package_path))
	  		mkdir($package_path, 0777, true);

		$package_name = mb_strtolower($package_name);
		    $fs_file_path = PATH_DATA . '/' . $package_name . '/' . $file_path;
		    $fs_file_path = $package_path . '/' . $file_path;

		return $fs_file_path;
	}

    static public function Data_Exists($package_name, $file_path)
	{
		$package_name = mb_strtolower($package_name);
        $fs_file_path = PATH_DATA . '/' . $package_name . '/' . $file_path;

		return file_exists($fs_file_path);
	}

	static public function File($e_path)
	{
		return File::Path($e_path);
	}

	static public function Media($package_name, $file_path)
	{
        if (!file_exists(PATH_MEDIA))
            mkdir(PATH_MEDIA, 0777, true);

		$package_name = mb_strtolower($package_name);
		$package_path = PATH_MEDIA . '/' . $package_name;
		if (!file_exists($package_path))
	  		mkdir($package_path, 0777, true);

		$package_name = mb_strtolower($package_name);
		    $fs_file_path = PATH_MEDIA . '/' . $package_name . '/' . $file_path;
		    $fs_file_path = $package_path . '/' . $file_path;

		return $fs_file_path;
	}

	static public function Media_Exists($package_name, $file_path)
	{
		$package_name = mb_strtolower($package_name);
        $fs_file_path = PATH_MEDIA . '/' . $package_name . '/' . $file_path;

		return file_exists($fs_file_path);
	}

}
