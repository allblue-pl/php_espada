<?php namespace E;

use GdImage;

defined('_ESPADA') or die(NO_ACCESS);


class File {
    /**
     * @var array<string, string>
     */
	static private array $MIME_TYPES = [
        'csv' => 'text/csv',
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'less' => 'text/plain',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    ];

	static public function Exists(string $filePath): bool {
	    if(file_exists($filePath))
	        return true;

	    return false;
	}

	static public function GetContents(string $filePath): string {
		if (!self::Exists($filePath))
			throw new \Exception("File `{$filePath}` does not exist.");

		$content = file_get_contents($filePath);
        if ($content === false)
            throw new \Exception("Cannot read `{$filePath}` does not exist.");

        return $content;
    }
        

	static public function NotFound(string|null $errorMessage = null): void {
        \Espada::NotFound($errorMessage === null ? '' : $errorMessage);
	}

	static public function Output(string $fileName, string $content, 
            string $charset ='utf-8'): void {
		set_time_limit(0);

		$content_mime_type = \E\File::GetContentMimeType($fileName);

		header('Content-Description: File Transfer');
		header("Content-Type: {$content_mime_type}; {$charset}");
		header('Pragma: public');
		header('Content-Length: ' . strlen($content));
		header('Content-Disposition: attachment; filename="' . $fileName . '"');

		echo $content;
	}

	static public function OutputImage(string $fileName, GdImage $image): void {
		$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

		$tmpFilePath = tempnam(PATH_TMP, 'img');

		if ($ext === 'png')
			imagepng($image, $tmpFilePath);
		else if ($ext === 'jpg' || $ext === 'jpeg')
			imagejpeg($image, $tmpFilePath);
		else if ($ext === 'gif')
			imagegif($image, $tmpFilePath);
		else
			throw new \Exception('Unknown image type.');

		self::OutputPath($tmpFilePath, $fileName);

		unlink($tmpFilePath);
	}

	static public function OutputPath(string $filePath, string|null $fileName = null): void {
		set_time_limit(0);

        if ($fileName === null)
            $fileName = basename($filePath);

        $content_mime_type = \E\File::GetContentMimeType($fileName);

		header('Content-Description: File Transfer');
		header('Content-Type: '.$content_mime_type);
		header('Pragma: public');
		header('Content-Length: ' . filesize($filePath));
        header('Content-Disposition: attachment; filename="' . $fileName . '"');

		if (ob_get_contents())
			ob_clean();
		flush();

		$handle = fopen($filePath, "rb");
        if ($handle === false)
            throw new \Exception("Cannot open '{$filePath}'.");

		while (!feof($handle))
    		echo fread($handle, 8192);
		fclose($handle);
	}

	static private function GetContentMimeType(string $filename): string {
		$ext_array = explode('.', $filename);
		$ext = strtolower(array_pop($ext_array));

		if (array_key_exists($ext, self::$MIME_TYPES)) {
			return self::$MIME_TYPES[$ext];
		// } else if (function_exists('finfo_open')) {
		// 	$finfo = finfo_open(FILEINFO_MIME);
		// 	$mimetype = finfo_file($finfo, $filename);
		// 	finfo_close($finfo);
		// 	return $mimetype;
		} else {
			return 'application/octet-stream';
		}
	}

	static public function Path(string $path): string|null {
		return Package::Path_FromPath($path);
	}

}
