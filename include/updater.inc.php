<?php
class Updater
{
    const _VERSION = '1.0.2';

    private common $common;

    /***
     * Updater constructor.
     * @param common $common
     */
    public function __construct(common $common) {
        $this->common = $common;
    }

    /**
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function update(): bool {
        if($this->common->isAjax())
            return false;

        set_time_limit(120);

        $CachedString = $this->common->getCache()->getItem('update');
        if (isset($_GET['reload']) || is_null($CachedString->get())) {
            $data = $this->call();
            if(!is_null($data) && !empty($data)) {
                $data = json_decode($data,true);
                $CachedString->set(serialize($data))->expiresAfter(600);
                $this->common->getCache()->save($CachedString);
                $data = unserialize($CachedString->get());
            }
        } else {
            $data = unserialize($CachedString->get());
        }

        $clear = false;
        if(is_array($data) && !empty($data)) {
            //1# DB
            if($data['db_version'] > $this->common->getConfig()->getDBV()) {
                //Update Database Version
                foreach ($data['db_versions'] as $key => $file) {
                    if($key > $this->common->getConfig()->getDBV()) {
                        $update_file = $file;
                    } else {
                        continue;
                    }

                    //Update
                    $fullpath_file = SCRIPT_PATH.'/cache/'.$update_file;
                    $zip_stream = $this->call('update/'.$update_file);
                    if(is_dir(SCRIPT_PATH.'/cache')) {
                        file_put_contents($fullpath_file,$zip_stream);
                    } unset($zip_stream);

                    if(file_exists($fullpath_file)) {
                        $zip = new ZipArchive;
                        $res = $zip->open($fullpath_file);
                        if ($res === TRUE) {
                            $zip->extractTo(SCRIPT_PATH);
                            $zip->close();
                            unlink($fullpath_file);
                        } else {
                            set_time_limit(30);
                            return false;
                        }
                    }

                    $sql = file_get_contents(SCRIPT_PATH.'/database/updates/'.str_ireplace('.zip','.sql',$update_file));
                    if(!empty($sql)) {
                        $this->common->getDatabase()->beginTransaction();
                        $this->common->getDatabase()->query('SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";');
                        $this->common->getDatabase()->query($sql);
                        $this->common->getDatabase()->commit();
                        $this->common->getConfig()->setDBV($this->common->getConfig()->getDBV()+1);
                    }

                    if(file_exists(SCRIPT_PATH.'/update.php')) {
                        require_once SCRIPT_PATH.'/update.php';
                        unlink(SCRIPT_PATH.'/update.php');
                    }
                }

                $clear = true;
            }

            //2# Script
            if($data['script_version'] > self::_VERSION) {
                //Update Script Version
                $update_file = $data['script_versions'][$data['script_version']];
                $fullpath_file = SCRIPT_PATH.'/cache/'.$update_file;
                $zip_stream = $this->call('update/'.$update_file);
                if(is_dir(SCRIPT_PATH.'/cache')) {
                    file_put_contents($fullpath_file,$zip_stream);
                } unset($zip_stream);

                if(file_exists($fullpath_file)) {
                    $zip = new ZipArchive;
                    $res = $zip->open($fullpath_file);
                    if ($res === TRUE) {
                        $zip->extractTo(SCRIPT_PATH);
                        $zip->close();
                        unlink($fullpath_file);
                    } else {
                        set_time_limit(30);
                        return false;
                    }
                }

                if(file_exists(SCRIPT_PATH.'/update.php')) {
                    require_once SCRIPT_PATH.'/update.php';
                    unlink(SCRIPT_PATH.'/update.php');
                }

                $clear = true;
            }

            if($clear) {
                $cache = $this->common->getFiles(SCRIPT_PATH.'/cache',false,true);
                foreach ($cache as $files) {
                    unlink(SCRIPT_PATH.'/cache/'.$files);
                }

                $cache = $this->common->getFiles(SCRIPT_PATH.'/template_c',false,true);
                foreach ($cache as $files) {
                    unlink(SCRIPT_PATH.'/template_c/'.$files);
                }

                set_time_limit(30);
                return true;
            }
        }

        set_time_limit(30);
        return false;
    }

    /**
     * @param string $file
     * @param array $data
     * @return bool|string|null
     */
    private function call(string $file='update/update.json', array $data = []) {
        global $update_url;
        if($fp = @fsockopen(str_ireplace('https://','',$update_url),443,$errCode,$errStr,0.2)){
            fclose($fp);
            $ch = curl_init($update_url.'/'.$file);
            $encoded = '';
            if(is_array($data) && count($data) >= 1) {
                foreach ($data as $name => $value) {
                    $encoded .= urlencode($name) . '=' . urlencode($value) . '&';
                }
                $encoded = substr($encoded, 0, strlen($encoded) - 1);
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$encoded);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, !empty($encoded));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $sResult = curl_exec($ch);
            if (curl_errno($ch))
            {
                echo curl_error($ch);
                fclose($fp);
                return null;
            } else
            {
                curl_close($ch);
                return $sResult;
            }
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getVersion(): string {
        return self::_VERSION;
    }
}
