<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class File
{

	static private $MIME_TYPES = array(

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
	);

	static public function Exists($file_path)
	{
	    if(file_exists($file_path))
	        return true;

	    return false;
	}

	static public function GetContents($file_path)
	{
		if (!self::Exists($file_path))
			throw new \Exception("File `{$file_path}` does not exist.");

		return file_get_contents($file_path);
	}

	static public function NotFound($error_message = null)
	{
		if ($error_message !== null)
            echo $error_message;

		\Espada::NotFound();
	}

	static public function Output($file_name, $content, $charset ='utf-8')
	{
		set_time_limit(0);

		$content_mime_type = \E\File::GetContentMimeType($file_name);

		header('Content-Description: File Transfer');
		header("Content-Type: {$content_mime_type}; {$charset}");
		header('Pragma: public');
		header('Content-Length: ' . strlen($content));
		header('Content-Disposition: attachment; filename="' . $file_name . '"');

		echo $content;
	}

	static public function OutputImage($file_name, $image)
	{
		$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

		$tmp_file_path = tempnam(PATH_TMP, 'img');

		if ($ext === 'png')
			imagepng($image, $tmp_file_path);
		else if ($ext === 'jpg' || $ext === 'jpeg')
			imagejpeg($image, $tmp_file_path);
		else if ($ext === 'gif')
			imagegif($image, $tmp_file_path);
		else
			throw new \Exception('Unknown image type.');

		self::OutputPath($tmp_file_path, $file_name);

		unlink($tmp_file_path);
	}

	static public function OutputPath($file_path, $file_name = null)
	{
		set_time_limit(0);

		if ($file_name === null)
			$content_mime_type = \E\File::GetContentMimeType($file_path);
		else
			$content_mime_type = \E\File::GetContentMimeType($file_name);

		header('Content-Description: File Transfer');
		header('Content-Type: '.$content_mime_type);
		header('Pragma: public');
		header('Content-Length: ' . filesize($file_path));
		if ($file_name !== null)
			header('Content-Disposition: attachment; filename="' . $file_name . '"');

		if (ob_get_contents())
			ob_clean();
		flush();

		$handle = fopen($file_path, "rb");
		while (!feof($handle))
    		echo fread($handle, 8192);
		fclose($handle);
	}

	static private function GetContentMimeType($filename) {
		$ext_array = explode('.', $filename);
		$ext = strtolower(array_pop($ext_array));

		if (array_key_exists($ext, self::$MIME_TYPES)) {
			return self::$MIME_TYPES[$ext];
		} elseif (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mimetype;
		}
		else {
			return 'application/octet-stream';
		}
	}

	static public function Path($path)
	{
		return Package::Path_FromPath($path);
	}

}
