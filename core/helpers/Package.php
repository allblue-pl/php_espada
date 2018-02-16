<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Package
{

    static private $PackagePaths = null;
    static private $Overwrites = [];

    static public function Details($filePath, $noOverwrites = false)
    {
        $filePath = $package . '/' . $path;

        if (!$noOverwrites) {
            if (isset(self::$Overwrites[$package])) {
                foreach (self::$Overwrites[$package] as $to_package => $to_path) {
                    $details = Package::Details($to_package,
                        "packages/{$package}/{$path}", true);
                    if ($details !== null)
                        return $details;
                }
            }
        }

        foreach (self::GetPackagePaths() as $packagePath) {
            // echo 'Details: ' . $packagePath . '/' . $filePath;
            if (File::Exists($packagePath . '/' . $filePath)) {
                $file_details = array(
                    'package_path' => PATH_ESITE . '/packages/' . $package,
                    'package_uri' => URI_ESITE . 'esite/packages/' . $package,
                    'path' => PATH_ESITE . '/packages/' . $filePath,
                    'uri' => URI_ESITE . 'packages/' . $filePath
                );
    
                return $file_details;
            }
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

    static public function Path($package, $path, $noOverwrites = false)
    {
        $filePath = $package . '/' . $path;
        // if ($package === 'site') {
        //     if (File::Exists(PATH_ESITE . '/' . $filePath))
        //         return PATH_ESITE . '/' . $filePath;
        //
        //     return null;
        // }

        if (!$noOverwrites) {
            if (isset(self::$Overwrites[$package])) {
                foreach (self::$Overwrites[$package] as $to_package => $to_path) {
                    $t_path = Package::Path($to_package,
                        "packages/{$package}/{$path}", true);
                    if ($t_path !== null)
                        return $t_path;
                }
            }
        }

        foreach (self::GetPackagePaths() as $packagePath) {
            // echo 'Path: ' . $packagePath . '/' . $filePath;
            if (File::Exists($packagePath . '/' . $filePath))
                return $packagePath . '/' . $filePath;
        }

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

    static public function Uri($package, $path, $noOverwrites = false)
    {
        $filePath = $package . '/' . $path;

        // if ($package === 'site') {
        //     if (File::Exists(PATH_ESITE . '/' . $filePath))
        //         return SITE_BASE . $filePath;
        //
        //     return null;
        // }
        if (!$noOverwrites) {
            if (isset(self::$Overwrites[$package])) {
                foreach (self::$Overwrites[$package] as $to_package => $to_path) {
                    $uri = Package::Uri($to_package,
                        "packages/{$package}/{$path}", true);
                    if ($uri !== null)
                        return $uri;
                }
            }
        }

        foreach (self::GetPackagePaths() as $packageName => $packagePath) {
            if (File::Exists($packagePath . '/' . $filePath))
                return URI_ESITE . 'packages/' .  $packageName . '/' . $filePath;
        }

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


    static private function GetPackagePaths()
    {
        if (self::$PackagePaths === null) {
            self::$PackagePaths = [];
            $packageNames = scandir(PATH_ESITE . '/packages');
            foreach ($packageNames as $packageName) {
                if ($packageName === '.' || $packageName === '..')
                    continue;

                $packagePath = PATH_ESITE . '/packages/' . $packageName;
                if (is_dir($packagePath))
                    self::$PackagePaths[$packageName] = $packagePath;
            }
        }
        
        return self::$PackagePaths;
    }

}
