<?php

class MysqlBackup {

    private $host;
    private $db;
    private $user;
    private $passwd;

    private $dest_dir;
    private $dest_file_prefix;

    public $verbose = false;

    public $keep_hourly       = 48;
    public $keep_daily        = 7;
    public $keep_weekly       = 4;
    public $keep_monthly      = 0;

    public $hourly_start_time = '08:00';
    public $hourly_end_time   = '19:00';

    public function __construct($host, $db, $user, $passwd, $dest_dir, $dest_file_prefix) {
        $this->host             = $host;
        $this->db               = $db;
        $this->user             = $user;
        $this->passwd           = $passwd;
        $this->dest_dir         = $dest_dir;
        $this->dest_file_prefix = $dest_file_prefix;
    }

    private function getFilenameStart($type) {
        $prefix = empty($this->dest_file_prefix) ? '' : $this->dest_file_prefix . '.';
        return $prefix . $this->db . "_{$type}_";
    }

    private function getMonthlyFilename() {
        $date     = date('Y-m');
        $filename = $this->getFilenameStart('monthly') . $date . '.sql.gz';
        return $filename;
    }

    private function checkMonthly() {
        return $this->checkFileExists($this->getMonthlyFilename());
    }

    private function getWeeklyFilename() {
        $date     = date('Y-W');
        $filename = $this->getFilenameStart('weekly') . $date . '.sql.gz';
        return $filename;
    }

    private function checkWeekly() {
        return $this->checkFileExists($this->getWeeklyFilename());
    }

    private function getDailyFilename() {
        $date     = date('Y-m-d');
        $filename = $this->getFilenameStart('daily') . $date . '.sql.gz';
        return $filename;
    }

    private function checkDaily() {
        return $this->checkFileExists($this->getDailyFilename());
    }

    private function getHourlyFilename() {
        $date     = date('Y-m-d_H:i:s');
        $filename = $this->getFilenameStart('hourly') . $date . '.sql.gz';
        return $filename;
    }

    public function backupDatabase() {
        if (!$this->checkMonthly()) {
            $this->dumpDatabase($this->getMonthlyFilename());
            return;
        }
        if (!$this->checkWeekly()) {
            $this->dumpDatabase($this->getWeeklyFilename());
            return;
        }
        if (!$this->checkDaily()) {
            $this->dumpDatabase($this->getDailyFilename());
            return;
        }

        //if within timespan, do hourly, no matter what
        $start_timestamp = strtotime(date('Y-m-d') . ' ' . $this->hourly_start_time);
        $end_timestamp   = strtotime(date('Y-m-d') . ' ' . $this->hourly_end_time);
        $time            = time();
        if($time >= $start_timestamp && $time <= $end_timestamp) {
            $this->dumpDatabase($this->getHourlyFilename());
        }

    }

    public function dumpDatabase($filename) {
        if($this->verbose) print "MysqlBackup::dumpDatabase($filename)\n";
        if($this->verbose) print "Dumping database: $this->db\n";
        $dest_file_path = "$this->dest_dir/$filename\n";
        if($this->verbose) print "Dump file destination: $dest_file_path";
        $cmd = "mysqldump --add-drop-table --lock-tables --host=$this->host --user=$this->user --password=$this->passwd $this->db | gzip -c > $dest_file_path";

        $output = system($cmd);
        if($this->verbose) print $output;
        return $output;
    }

    public function dumpTables(array $tables) {
        $tables_str     = implode(' ', $tables);
        if($this->verbose) print "MysqlBackup::dumpTables($tables_str)\n";
        if($this->verbose) print "Dumping database: $this->db, tables: $tables_str\n";
        $date           = date('Y-m-d_H-i-s');
        if($this->verbose) print "Dump file date: $date\n";
        $dest_file      = $this->dest_file_prefix . '.' . $this->db . '_' . implode('_', $tables) . $date . '.sql.gz';
        $dest_file_path = "$this->dest_dir/$dest_file\n";
        if($this->verbose) print "Dump file destination: $dest_file_path";
        $cmd            = "mysqldump --add-drop-table --lock-tables --host=$this->host --user=$this->user --password=$this->passwd $this->db $tables_str | gzip -c > $dest_file_path";

        $output = system($cmd);
        if($this->verbose) print $output;
        return $output;
    }

    private function checkFileExists($filename) {
        return is_file($this->dest_dir.'/'.$filename);
    }

    public function clean() {
        if($this->verbose) print "MysqlBackup::clean()\n";
        $handle              = opendir($this->dest_dir) or die('Could not open directory: ' . $this->dest_dir);
        if($this->verbose) print 'Opened directory: ' . $this->dest_dir . "\n";
        if($this->verbose) print "Examining files\n";

        while (false !== ($file                = readdir($handle))) {
            $dest_file_path = "$this->dest_dir/$file";
            if($this->verbose) print "'$dest_file_path': ";
            if (!is_dir($dest_file_path)) {
                $file_time = filemtime($dest_file_path);
                $base_name = basename($dest_file_path);

                $delete = false;

                if($this->keep_monthly > 0 && strpos($base_name,$this->getFilenameStart('monthly')) !== false) {
                    if($file_time < strtotime("- $this->keep_monthly months")) {
                        $delete = true;
                    }
                }

                if($this->keep_weekly > 0 && strpos($base_name,$this->getFilenameStart('weekly')) !== false) {
                    if($file_time < strtotime("- $this->keep_weekly weeks")) {
                        $delete = true;
                    }
                }

                if($this->keep_daily > 0 && strpos($base_name,$this->getFilenameStart('daily')) !== false) {
                    if($file_time < strtotime("- $this->keep_daily days")) {
                        $delete = true;
                    }
                }

                if($this->keep_hourly > 0 && strpos($base_name,$this->getFilenameStart('hourly')) !== false) {
                    if($file_time < strtotime("- $this->keep_hourly hours")) {
                        $delete = true;
                    }
                }

                if ($delete) {
                    if($this->verbose) print "Yes. Deleting.\n";
                    unlink($dest_file_path);
                } else {
                    if($this->verbose) print "No. Skipping.\n";
                }
            } else {
                if($this->verbose) print "A Directory, excluding\n";
            }
        }
    }

}
