<?php

/*
 * Direcotry and file Tools
 */


class MSCDir {
    protected $dir_path;

    public function __construct($dir_path) {
        if(empty($dir_path)) {
            throw new Exception("Directory path cannot be empty");
        }

        if(!file_exists($dir_path)) {
            throw new Exception("Dir $dir_path does not exist");
        }
        $this->dir_path = $dir_path;
    }

    public function getSettings() {
        $return = array(
            'dir_path'          => $this->dir_path,
        );
        return $return;
    }

    public function getFilepathString($subpath = null) {
        $return = $this->dir_path;
        if($subpath != null) {
            $return.= '/'.$subpath;
        }
        return $return;
    }

    public function getSize($subdir = null) {
        $size = 0;

        $dir = $this->getFilepathString($subdir);

        foreach(glob($dir . '/*') as $file) {
            if(is_dir($file))
                $size += $this->getSize($file);
            else
                $size += filesize($file);
        }

        return $size;
    }

    public function getFilesArray() {
        $files = array();

        foreach(glob($this->dir_path . '/*') as $file) {
            if(is_dir($file)) {

            } else {
                $files[] = $file;
            }
        }
        return $files;
    }

    public function getFilesOlderThan($datetime) {
        $files = array();

        foreach(glob($this->dir_path . '/*') as $file) {
            if(is_dir($file)) {

            } else {
                $file_datetime = filemtime($file);
                if($file_datetime < $datetime) {
                    $files[] = $file;
                }
            }
        }
        return $files;
    }

    public function getDirsOlderThan($datetime) {
        $dirs = array();

        foreach(glob($this->dir_path . '/*') as $file) {
            if(is_dir($file)) {
                $dir_datetime = filemtime($file);
                if($dir_datetime < $datetime) {
                    $dirs[] = $file;
                }
            } else {
            }
        }
        return $dirs;
    }

    public function getDirectoriesArray() {
        $dirs = array();

        foreach(glob($this->dir_path . '/*') as $file) {
            if(is_dir($file))
                $dirs[] = $file;
        }
        return $dirs;
    }

    public function fileExists($filename) {
        return file_exists($this->dir_path.'/'.$filename);
    }

    public function getFileSize($filename) {
        //force subdir to be in the path of this->dir_path
        if(strpos($this->dir_path,$filename)!=0) {
            $filename = $this->getFilepathString($filename);
        }

        return filesize($filename);
    }

    public function getFileMTime($filename) {
        //force subdir to be in the path of this->dir_path
        if(strpos($this->dir_path,$filename)!=0) {
            $filename = $this->getFilepathString($filename);
        }

        return filemtime($filename);
    }

    public function removeDir($subdir) {
        //force subdir to be in the path of this->dir_path

        if(strpos($this->dir_path,$subdir)!==0) {
            $subdir = $this->getFilepathString($subdir);
        }

        foreach(glob($subdir . '/*') as $file) {
            if(is_dir($file))
                $this->removeDir($file);
            else
                unlink($file);
        }
        rmdir($subdir);
    }

    public function removeFile($filename) {
        //force subdir to be in the path of this->dir_path
        if(strpos($this->dir_path,$filename)!==0) {
            $filename = $this->getFilepathString($filename);
        }

        if(!file_exists($filename)) {
            throw new Exception("File '$filename' does not exist");
        }

        unlink($filename);
    }
}


class MSCFileSet {

    protected $dir;

    protected $files = array();

    public function __construct(MSCDir $dir) {
        $this->dir = $dir;
    }

    public function addFile($filename) {
        $filename = basename($filename);

        if(!$this->dir->fileExists($filename)) {
            throw new Exception("File $filename does not exist.");
        }

        $this->files[count($this->files)] = $filename;
    }

    public function getSize() {
        $size = 0;
        foreach($this->files as $file) {
            $size+=$this->dir->getFileSize($file);
        }
        return $size;
    }

    public function getCount() {
        return count($this->files);
    }

    public function archive(ArchiveDir $archive) {
        foreach($this->files as $file) {
            $archive->copyFile($this->dir->getFilepathString($file));
        }
    }

    public function removeFiles() {
        foreach($this->files as $file) {
            $this->dir->removeFile($file);
        }
    }
}


class ArchiveDir {

    private $parent_dir;
    private $archive_dir;
    protected $dir_handle;

    protected $current_subdir = '';

    public function __construct($parent_dir,$archive_dir) {
        if(!file_exists($parent_dir)) {
            throw new Exception("Dir $parent_dir does not exist");
        }

        if(file_exists($parent_dir.'/'.$archive_dir.'.tar.gz')) {
            throw new Exception("Archive name $archive_dir already archived.");
        }

        if(!file_exists($parent_dir.'/'.$archive_dir)) {
            //make the dir
            mkdir($parent_dir.'/'.$archive_dir,0777,true);
        }

        $this->parent_dir  = $parent_dir;
        $this->archive_dir = $archive_dir;

    }

    public function setCurrentSubdir($subdir) {
        $this->current_subdir = $subdir;
    }

    public static function exists($parent_dir,$archive_dir) {
        return file_exists($parent_dir.'/'.$archive_dir) || file_exists($parent_dir.'/'.$archive_dir.'.tar.gz');
    }

    public static function nextArchiveName($parent_dir,$archive_dir) {
        if(!self::exists($parent_dir, $archive_dir)) {
            return $archive_dir;
        }

        $count = 1;

        while(self::exists($parent_dir, $archive_dir.'_'.$count)) {
            $count++;
        }
        return $archive_dir.'_'.$count;
    }

    private function getArchivedDir() {
        $return = $this->parent_dir.'/'.$this->archive_dir;
        if(!empty($this->current_subdir)) {
            $return.= '/'.$this->current_subdir;
        }
        return $return;
    }

    public function copyDir($source, $diffDir = null) {
        $sourceHandle = opendir($source);
        $dest         = $this->getArchivedDir();
        if($diffDir == null) {
            $diffDir = basename($source);
        }

        if(!file_exists($dest . '/' . $diffDir)) {
            mkdir($dest . '/' . $diffDir,0777,true);
        }

        while($res = readdir($sourceHandle)){
            if($res == '.' || $res == '..') {
                //skip
                continue;
            }

            if(is_dir($source . '/' . $res)){
                $this->copyDir($source . '/' . $res, $dest, $diffDir . '/' . $res);
            } else {
                copy($source . '/' . $res, $dest . '/' . $diffDir . '/' . $res);
                touch($dest . '/' . $diffDir . '/' . $res,filemtime($source . '/' . $res));
            }
        }
    }

    public function copyFile($filepath) {
        if(!file_exists($filepath)) {
            throw new Exception("File $filepath does not exist.");
        }
        $filename = basename($filepath);

        $dir = $this->getArchivedDir();
        if(!file_exists($dir)) {
            mkdir($dir,0777,true);
        }
        $result = copy($filepath,$this->getArchivedDir().'/'.$filename);
        touch($this->getArchivedDir().'/'.$filename,filemtime($filepath));
        return $result;
    }

    public function makeGz() {
        $gz_filename = $this->archive_dir.".tar.gz";
        $gz_filepath = $this->parent_dir.'/'.$gz_filename;
        $output = `tar -czf $gz_filepath -C {$this->parent_dir} {$this->archive_dir} `;

        if(empty($output)) {
            $this->removeArchiveDir();
        }

        return $output;
    }

    public function removeArchiveDir($subdir = null) {
        if($subdir == null) {
            $subdir = $this->parent_dir.'/'.$this->archive_dir;
        }

        //force subdir to be in the path of this->dir_path
        if(strpos($this->parent_dir,$subdir)!=0) {
            $subdir = $this->parent_dir.'/'.$subdir;
        }

        foreach(glob($subdir . '/*') as $file) {
            if(is_dir($file))
                $this->removeArchiveDir($file);
            else
                unlink($file);
        }
        rmdir($subdir);
    }
}




function format_filesize($size = 0, $unit = 'a', $length = 2) {
    if ($unit == '') {
        return $size;
    } elseif ($unit == 'b' XOR $unit=='a' AND strlen($size) < 4) {
        return number_format($size, $length, '.', '') . ' Bytes';
    } elseif ($unit == 'kb' XOR $unit=='a' AND strlen($size) < 7) {
        return number_format(($size / 1024), $length, '.', '') . ' KB';
    } elseif ($unit == 'mb' XOR $unit=='a' AND strlen($size) < 10) {
        return number_format(($size / 1024 / 1024), $length, '.', '') . ' MB';
    } elseif ($unit == 'gb' XOR $unit=='a' AND strlen($size) < 13) {
        return number_format(($size / 1024 / 1024 / 1024), $length, '.', '') . ' GB';
    } elseif ($unit == 'tb' XOR $unit=='a' AND strlen($size) < 16) {
        return number_format(($size / 1024 / 1024 / 1024 / 1024), $length, '.', '') . ' TB';
    }
}
