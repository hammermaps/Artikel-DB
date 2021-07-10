<?php
/**
 * Created by PhpStorm.
 * User: Lucas
 * Date: 18.03.2018
 * Time: 11:50
 */

use Phpfastcache\CacheManager;
use Nette\Database\{Connection, Explorer, Row};
use Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface;
use Dompdf\Dompdf;

require_once INCLUDE_PATH."/config.inc.php";

$loader = new Nette\Loaders\RobotLoader;
$loader->addDirectory(INCLUDE_PATH);
$loader->addDirectory(CONTROLLER_PATH);
$loader->addDirectory(SCRIPT_PATH.'/model');
$loader->setTempDirectory(SCRIPT_PATH . '/cache');
$loader->register();

class common
{
    public Smarty $smarty;
    public Connection $database;
    public Explorer $explorer;
    public array $assign;
    public mixed $do;
    public GUMP $validator;
    public ExtendedCacheItemPoolInterface $cache;
    public user $users;
    public ModelConfig $config;
    public Updater $updater;
    public bool $ajax;

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheDriverNotFoundException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidConfigurationException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheDriverCheckException
     * @throws SmartyException
     * @throws ReflectionException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheLogicException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheDriverException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException
     */
    public function __construct(bool $ajax=false) {
        global $dsn,$user,$passwd,$debug;
        $this->ajax = $ajax;
        $this->smarty = new Smarty();
        $this->database = new Connection($dsn, $user, $passwd);
        $this->database->query("SET character_set_client=utf8");
        $this->database->query("SET character_set_connection=utf8");
        $this->database->query("SET character_set_results=utf8");

        if(!is_dir(SCRIPT_PATH.'/cache/')) {
            mkdir(SCRIPT_PATH.'/cache');
        }

        $storage = new Nette\Caching\Storages\FileStorage(SCRIPT_PATH.'/cache/');
        $structure = new Nette\Database\Structure($this->database, $storage);
        $conventions = new Nette\Database\Conventions\DiscoveredConventions($structure);
        $this->explorer = new Explorer($this->database, $structure, $conventions, $storage);

        $this->smarty->setTemplateDir(SCRIPT_PATH.'/template/');
        $this->smarty->setCompileDir(SCRIPT_PATH.'/template_c/');
        $this->smarty->setConfigDir(SCRIPT_PATH.'/include/');
        $this->smarty->setCacheDir(SCRIPT_PATH.'/cache/');

        $this->smarty->setCaching(!$debug);
        $this->smarty->setDebugging($debug);
        $this->smarty->setForceCompile($debug);

        $this->config = new ModelConfig($this->explorer);

        if(!$this->ajax) {
            //Check the Database & Update
            $updates = $this->getFiles(SCRIPT_PATH . '/database/updates', false, true, ['sql']);
            $baseVersion = 0;
            foreach ($updates as $update) {
                $version = explode('_', $update);
                $version = str_ireplace('.sql', '', $version['1']);
                if (!$baseVersion)
                    $baseVersion = $this->config->getDBV() + 1;

                if (intval($version) == $baseVersion) {
                    $sql = file_get_contents(SCRIPT_PATH . '/database/updates/' . $update);
                    if (!empty($sql)) {
                        $this->database->beginTransaction();
                        $this->database->query('SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";');
                        $this->database->query($sql);
                        $this->database->commit();
                        $this->config->setDBV($baseVersion);
                        $baseVersion++;
                    }
                }
            }
        }

        $this->do = 'search';
        $this->assign = ['index'=>['dir'=>'template','content'=>'']];

        $this->cache = CacheManager::getInstance('apcu');

        $this->validator = new GUMP('de');
        $_POST = $this->validator->sanitize($_POST);
        $_GET = $this->validator->sanitize($_GET);

        //Updater
        $this->updater = new Updater($this);
        if($this->updater->update()) {
            header("Location: search.html");
            exit();
        }

        //Link Users
        $this->users = new user($this);
        //var_dump($this->users->register('admin','admin'));

        $rules = ['do' => 'required|alpha_numeric'];
        $filters = ['do' => 'trim|sanitize_string'];
        $do_get = $this->validator->filter($_GET, $filters);
        if($this->validator->validate($do_get, $rules) === true)
            $this->do = $do_get['do'];

        //Logout
        if($this->users->is_logged() && $this->do == 'logout') {
            $this->users->logout();
            header("Location: search.html");
            exit();
        }

        $this->assign['index']['version'] = $this->updater->getVersion();
        $this->assign['index']['dbv'] = $this->config->getDBV();

        //Navigation
        $this->smarty->clearAllAssign();
        $this->smarty->assign('','');
        $this->smarty->assign('is_logged',$this->users->is_logged());
        $this->assign['index']['navigation'] = $this->smarty->fetch(SCRIPT_PATH.'/template/navigation/navigation.tpl');
    }

    //Login Page
    public function page_login(string $page): void {
        global $notifications;
        if(array_key_exists('username',$_POST) && array_key_exists('password',$_POST) &&
            !empty($_POST['username']) && !empty($_POST['password'])) {
            $autologin = false;
            if(array_key_exists('remember',$_POST)) {
                $autologin = true; //TODO: Einbauen
            }

            $logged = $this->users->login(trim($_POST['username']),trim($_POST['password']));
            if(!$logged) {
                $notifications->addError('Benutzername oder Kennwort ist ungültig!');
            } else {
                header("Location: ".$page.".html");
                exit();
            }
        }

        $this->smarty->clearAllAssign();
        $this->smarty->assign('notifications',$notifications->display());
        $this->assign['index']['content'] = $this->smarty->fetch(SCRIPT_PATH.'/template/system/login.tpl');
    }

    public function exportPDF(): void {
        $this->smarty->clearAllAssign();
        $show = []; $seite = 0; $count = 0;
        $query = $this->database->query("SELECT `name`,`ean` FROM `artikel` ORDER BY `name` ASC;");
        foreach ($query->fetchAll() as $row) {  // preparing an array
            $row["name"] = preg_replace('/{blank}/',' ',trim($row["name"]));
            $row["ean"] = substr($row["ean"],0,3).'.'.substr($row["ean"],3,6);

            $count++;
            $this->smarty->clearAllAssign();
            $this->smarty->assign('ean',utf8_decode($row["ean"]),true);
            $this->smarty->assign('bez',utf8_decode($row["name"]),true);
            $show[$seite][] = $this->smarty->fetch(SCRIPT_PATH.'/template/export/show.tpl');

            if($count == 45) {
                $seite++;
                $count = 0;
            }
        }

        $tables = '';
        foreach ($show as $show_seite) {
            $tb="";
            foreach ($show_seite as $txt) {
                $tb .= $txt;
            }

            $this->smarty->clearAllAssign();
            $this->smarty->assign('show',$tb);
            $tables .= $this->smarty->fetch(SCRIPT_PATH.'/template/export/table.tpl');
        }

        $this->smarty->assign('tables',$tables);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($this->smarty->fetch(SCRIPT_PATH.'/template/export/index.tpl'));
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream();
        exit();
    }

    //Output Index
    public function page_output(): void {
        $this->smarty->assign('index', $this->assign['index']);
        $this->smarty->display(SCRIPT_PATH.'/template/index.tpl');
    }
	
	public function sqlLogger(string $sql) {
		$file = SCRIPT_PATH.'/database/proc/sql_log_'.date("d_m_Y").'.sql';
		if(!file_exists($file)) {
			file_put_contents($file,'');
		}
		$log = file_get_contents($file);
		$log .= $sql."\n";
		file_put_contents($file,$log);
	}

    public function sqlLoggerInsert(int $ean,string $name,string $tags) {
       $sql = "INSERT INTO `artikel` (`ean`, `name`, `tags`) SELECT * FROM ".
           "(SELECT ".$ean.", '".$name."', ".
           "'".$tags."')".
           " AS tmp WHERE NOT EXISTS ( SELECT `ean` FROM `artikel` WHERE `ean` = ".$ean." ) LIMIT 1;";
       $this->sqlLogger($sql);
    }

    /**
     * Funktion um Dateien aus einem Verzeichnis auszulesen
     * @name        get_files()
     * @access      public
     * @static
     * @param bool $only_dir (optional)
     * @param bool $only_files (optional)
     * @param array $file_ext (optional)
     * @param bool $preg_match (optional)
     * @param array $blacklist (optional)
     * @param bool $blacklist_word (optional)
     * @return array|bool
     */
    public function getFiles(string $dir=null, bool $only_dir=false, bool $only_files=false, array $file_ext= [], bool $preg_match=false, array $blacklist= [], bool $blacklist_word=false): bool|array
    {
        $files = [];
        if (!file_exists($dir) && !is_dir($dir))
            return $files;

        if ($handle = @opendir($dir)) {
            if ($only_dir) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..' && !is_file($dir . '/' . $file)) {
                        if (!count($blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && ($preg_match ? preg_match($preg_match, $file) : true))
                            $files[] = $file;
                        else {
                            if (!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && ($preg_match ? preg_match($preg_match, $file) : true))
                                $files[] = $file;
                        }
                    }
                } //while end
            } else if ($only_files) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..' && is_file($dir . '/' . $file)) {
                        if (!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && !count($file_ext) && ($preg_match ? preg_match($preg_match, $file) : true))
                            $files[] = $file;
                        else {
                            ## Extension Filter ##
                            $exp_string = array_reverse(explode(".", $file));
                            if (!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && in_array(strtolower($exp_string[0]), $file_ext) && ($preg_match ? preg_match($preg_match, $file) : true))
                                $files[] = $file;
                        }
                    }
                } //while end
            } else {
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..' && is_file($dir . '/' . $file)) {
                        if (!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && !count($file_ext) && ($preg_match ? preg_match($preg_match, $file) : true))
                            $files[] = $file;
                        else {
                            ## Extension Filter ##
                            $exp_string = array_reverse(explode(".", $file));
                            if (!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && in_array(strtolower($exp_string[0]), $file_ext) && ($preg_match ? preg_match($preg_match, $file) : true))
                                $files[] = $file;
                        }
                    } else {
                        if (!in_array($file, $blacklist) && (!$blacklist_word || strpos(strtolower($file), $blacklist_word) === false) && $file != '.' && $file != '..' && ($preg_match ? preg_match($preg_match, $file) : true))
                            $files[] = $file;
                    }
                } //while end
            }

            if (is_resource($handle))
                closedir($handle);

            if (!count($files))
                return false;

            return $files;
        } else
            return false;
    }

    /**
     * @return Connection
     */
    public function getDatabase(): Connection
    {
        return $this->database;
    }

    /**
     * @return \Phpfastcache\Cluster\AggregatablePoolInterface|ExtendedCacheItemPoolInterface
     */
    public function getCache(): \Phpfastcache\Cluster\AggregatablePoolInterface|ExtendedCacheItemPoolInterface
    {
        return $this->cache;
    }

    /**
     * @return mixed
     */
    public function getDo(): mixed
    {
        return $this->do;
    }

    /**
     * @return Explorer
     */
    public function getExplorer(): Explorer
    {
        return $this->explorer;
    }

    /**
     * @return Smarty
     */
    public function getSmarty(): Smarty
    {
        return $this->smarty;
    }

    /**
     * @return user
     */
    public function getUsers(): user
    {
        return $this->users;
    }

    /**
     * @return GUMP
     */
    public function getValidator(): GUMP
    {
        return $this->validator;
    }

    /**
     * @return Updater
     */
    public function getUpdater(): Updater
    {
        return $this->updater;
    }

    /**
     * @return Row|null
     */
    public function getConfig(): ?ModelConfig
    {
        return $this->config;
    }

    /**
     * @return bool
     */
    public function isAjax(): bool
    {
        return $this->ajax;
    }
}