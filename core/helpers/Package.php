<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Package
{

    static private $Overwrites = [];

    static public function Details($file_path, $no_overwrites = false)
    {
        $file_path = $package . '/' . $path;

        if (!$no_overwrites) {
            if (isset(self::$Overwrites[$package])) {
                foreach (self::$Overwrites[$package] as $to_package => $to_path) {
                    $details = Package::Details($to_package,
                        "packages/{$package}/{$path}", true);
                    if ($details !== null)
                        return $details;
                }
            }
        }

        if (File::Exists(PATH_ESITE . '/packages/' . $file_path)) {
            $file_details = array(
                'package_path' => PATH_ESITE . '/packages/' . $package,
                'package_uri' => URI_ESITE . 'esite/packages/' . $package,
                'path' => PATH_ESITE . '/packages/' . $file_path,
                'uri' => URI_ESITE . 'packages/' . $file_path
            );

            return $file_details;
        }

        if (File::Exists(PATH_ECORE . '/' . $file_path)) {
            $file_details = array(
                'package_path' => PATH_ECORE . '/' . $package,
                'package_uri' => URI_ECORE . $package,
                'path' => PATH_ECORE . '/' . $file_path,
                'uri' => URI_ECORE . $file_path
            );

            return $file_details;
        }

        return null;
    }

    static public function Details_FromPath($path, $dir = '',
            $ext = '')
    {
        $path_array = explode(':', $path);
        if (count($path_array) !== 2)
            throw new \Exception("Wrong path `{$path}` format.");

        if ($dir !== '')
            $dir .= '/';

        return self::Details($path_array[0],
                $dir . $path_array[1] . $ext);
    }

    static public function Path($package, $path, $no_overwrites = false)
    {
        $file_path = $package . '/' . $path;
        // if ($package === 'site') {
        //     if (File::Exists(PATH_ESITE . '/' . $file_path))
        //         return PATH_ESITE . '/' . $file_path;
        //
        //     return null;
        // }

        if (!$no_overwrites) {
            if (isset(self::$Overwrites[$package])) {
                foreach (self::$Overwrites[$package] as $to_package => $to_path) {
                    $t_path = Package::Path($to_package,
                        "packages/{$package}/{$path}", true);
                    if ($t_path !== null)
                        return $t_path;
                }
            }
        }

        if (File::Exists(PATH_ESITE . '/packages/' . $file_path))
            return PATH_ESITE . '/packages/' . $file_path;

        if (File::Exists(PATH_ECORE . '/' . $file_path))
            return PATH_ECORE . '/' . $file_path;

        return null;
    }

    static public function Path_FromPath($path, $dir = '',
            $ext = '')
    {
        $path_array = explode(':', $path);
        if (count($path_array) !== 2)
            throw new \Exception("Wrong path `{$path}` format.");

        if ($dir !== '')
            $dir .= '/';

        return self::Path($path_array[0],
                $dir . $path_array[1] . $ext);
    }

    static public function Uri($package, $path, $no_overwrites = false)
    {
        $file_path = $package . '/' . $path;

        // if ($package === 'site') {
        //     if (File::Exists(PATH_ESITE . '/' . $file_path))
        //         return SITE_BASE . $file_path;
        //
        //     return null;
        // }
        if (!$no_overwrites) {
            if (isset(self::$Overwrites[$package])) {
                foreach (self::$Overwrites[$package] as $to_package => $to_path) {
                    $uri = Package::Uri($to_package,
                        "packages/{$package}/{$path}", true);
                    if ($uri !== null)
                        return $uri;
                }
            }
        }

        if (File::Exists(PATH_ESITE . '/packages/' . $file_path))
            return URI_ESITE . 'packages/' . $file_path;

        if (File::Exists(PATH_ECORE . '/' . $file_path))
            return URI_ECORE . $file_path;

        return null;
    }

    static public function Uri_FromPath($path, $dir, $ext)
    {
        $path_array = explode(':', $path);
        if (count($path_array) !== 2)
            throw new \Exception("Wrong path `{$path}` format.");

        return self::Uri($path_array[0],
                $dir . '/' . $path_array[1] . $ext);
    }

    static public function Overwrite($from_package, $to_package, $path = '*')
	{
		if (!isset(self::$Overwrites[$from_package]))
			self::$Overwrites[$from_package] = [];

        if (!isset(self::$Overwrites[$from_package][$to_package]))
            self::$Overwrites[$from_package][$to_package] = [];

        if (!in_array($path, self::$Overwrites[$from_package][$to_package]))
		      array_unshift(self::$Overwrites[$from_package][$to_package], $path);
	}

    static public function UnOverwrite($from_package, $to_package = null)
    {
        if ($to_package === null)
            unset(self::$Overwrites[$from_package]);

        if (!isset(self::$Overwrites[$from_package][$to_package]))
            return;

        unset(self::$Overwrites[$from_package][$to_package]);
        if (count(self::$Overwrites[$from_package]) === 0)
            unset(self::$Overwrites[$from_package]);
    }

}
