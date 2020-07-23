<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use File;

class BackupController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $path = storage_path('backup').'/';
        define("BACKUP_PATH", $path);

        if(!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }
        
        $server_name   = env("DB_HOST", "localhost");
        $username      = env("DB_USERNAME", "root");
        $password      = env("DB_PASSWORD", "root");
        $port          = env("DB_PORT", "3306");
        $database_name = env("DB_DATABASE", "apotek");
        $date_string   = strtotime('now');
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $cmd = "C:/xampp/mysql/bin/mysqldump --routines -h {$server_name} -P {$port} -u {$username} -p{$password} {$database_name} > " . BACKUP_PATH . "{$date_string}_{$database_name}.sql";
        } else {
            $cmd = "/Applications/MAMP/Library/bin/mysqldump --routines -h {$server_name} -P {$port} -u {$username} -p{$password} {$database_name} > " . BACKUP_PATH . "{$date_string}_{$database_name}.sql";
        }
        
        exec($cmd);
        return response()->download(BACKUP_PATH . "{$date_string}_{$database_name}.sql", "{$date_string}_{$database_name}.sql");
    }

    public function restore()
    {
        $target_file = storage_path('restore').'/';
        if(!File::exists($target_file)) {
            File::makeDirectory($target_file, $mode = 0777, true, true);
        }
        if (move_uploaded_file($_FILES["database"]["tmp_name"], $target_file.'apotek.sql')) {
            $restore_file  = $target_file.'apotek.sql';
            $server_name   = env("DB_HOST", "localhost");
            $username      = env("DB_USERNAME", "root");
            $password      = env("DB_PASSWORD", "root");
            $port          = env("DB_PORT", "3306");
            $database_name = env("DB_DATABASE", "apotek");

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $cmd = "C:/xampp/mysql/bin/mysql -h {$server_name} -P {$port} -u {$username} -p{$password} {$database_name} < $restore_file";
            } else {
                $cmd = "/Applications/MAMP/Library/bin/mysql -h {$server_name} -P {$port} -u {$username} -p{$password} {$database_name} < $restore_file";
            }
            
            exec($cmd);

            return redirect('/')->with('success','Success Restore');
        }         
    }

}
