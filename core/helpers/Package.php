<?php namespace E;

use Exception;

defined('_ESPADA') or die(NO_ACCESS);

/**
 * @phpstan-type T_PackagePath array{
 *   name: string,
 *   path: string,
 * }
 * @phpstan-type T_PackageOverwrites array<string, array<string, array<string>>>
 * @package E
 */

class Package {
    /** @var array<T_PackagePath>|null */
    static private array|null $PackagePaths = null;
    /** @var T_PackageOverwrites */
    static private array $Overwrites = [];

    // static public function Details($filePath, $noOverwrites = false) {
    //     $filePath = $package . '/' . $path;

    //     if (!$noOverwrites) {
    //         if (isset(self::$Overwrites[$package])) {
    //             foreach (self::$Overwrites[$package] as $to_package => $to_path) {
    //                 $details = Package::Details($to_package,
    //                     "packages/{$package}/{$path}", true);
    //                 if ($details !== null)
    //                     return $details;
    //             }
    //         }
    //     }

    //     foreach (self::GetPackagePaths() as $packagePath) {
    //         // echo 'Details: ' . $packagePath . '/' . $filePath;
    //         if (File::Exists($packagePath['path'] . '/' . $filePath)) {
    //             $file_details = array(
    //                 'package_path' => PATH_ESITE . '/packages/' . $package,
    //                 'package_uri' => URI_ESITE . 'esite/packages/' . $package,
    //                 'path' => PATH_ESITE . '/packages/' . $filePath,
    //                 'uri' => URI_ESITE . 'packages/' . $filePath
    //             );
    
    //             return $file_details;
    //         }
    //     }

    //     return null;
    // }

    // static public function Details_FromPath($path, $dir = '',
    //         $ext = '') {
    //     $path_array = explode(':', $path);
    //     if (count($path_array) !== 2)
    //         throw new \Exception("Wrong path `{$path}` format.");

    //     if ($dir !== '')
    //         $dir .= '/';

    //     return self::Details($path_array[0],
    //             $dir . $path_array[1] . $ext);
    // }

    static public function Path(string $package, string $path, 
            bool $noOverwrites = false): string|null {
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
            if (File::Exists($packagePath['path'] . '/' . $filePath))
                return $packagePath['path'] . '/' . $filePath;
        }

        return null;
    }

    static public function Path_FromPath(string $path, string $dir = '',
            string $ext = ''): string|null {
        $pathArray = explode(':', $path);
        if (count($pathArray) !== 2)
            throw new \Exception("Wrong path `{$path}` format.");

        if ($dir !== '')
            $dir .= '/';

        return self::Path($pathArray[0], $dir . $pathArray[1] . $ext);
    }

    static public function Uri(string $package, string $path, 
            bool $noOverwrites = false): string|null {
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

        foreach (self::GetPackagePaths() as $packagePath) {
            if (File::Exists($packagePath['path'] . '/' . $filePath))
                return URI_ESITE . 'packages/' .  $packagePath['name'] . '/' . $filePath;
        }

        return null;
    }

    static public function Uri_FromPath(string $path, string $dir, string $ext): 
            string|null {
        $pathArray = explode(':', $path);
        if (count($pathArray) !== 2)
            throw new \Exception("Wrong path `{$path}` format.");

        return self::Uri($pathArray[0],
                $dir . '/' . $pathArray[1] . $ext);
    }

    static public function Overwrite(string $fromPackage, string $toPackage, 
            string $path = '*'): void {
		if (!isset(self::$Overwrites[$fromPackage]))
			self::$Overwrites[$fromPackage] = [];

        if (!isset(self::$Overwrites[$fromPackage][$toPackage]))
            self::$Overwrites[$fromPackage][$toPackage] = [];

        if (!in_array($path, self::$Overwrites[$fromPackage][$toPackage]))
		      array_unshift(self::$Overwrites[$fromPackage][$toPackage], $path);
	}

    static public function UnOverwrite(string $fromPackage, string|null $toPackage = null):
            void {
        if ($toPackage === null) {
            unset(self::$Overwrites[$fromPackage]);
            return;
        }

        if (!isset(self::$Overwrites[$fromPackage][$toPackage]))
            return;

        unset(self::$Overwrites[$fromPackage][$toPackage]);
        if (count(self::$Overwrites[$fromPackage]) === 0)
            unset(self::$Overwrites[$fromPackage]);
    }

    /**
     * @return array<T_PackagePath> 
     * @throws Exception 
     */
    static private function GetPackagePaths(): array {
        if (self::$PackagePaths !== null)
            return self::$PackagePaths;

        if (!defined('EPACKAGES'))
            throw new \Exception("'EPACKAGES' not defined.");

        self::$PackagePaths = [];
        $packageNames = explode(',', str_replace(' ', '', EPACKAGES));
        foreach ($packageNames as $packageName) {
            $packagePath = PATH_ESITE . '/packages/' . $packageName;
            if (!is_dir($packagePath))
                throw new \Exception("Package '{$packageName}' does not exist.");

            self::$PackagePaths[] = [
                'name' => $packageName,
                'path' => $packagePath,
            ];
        }
        
        return self::$PackagePaths;
    }

}
