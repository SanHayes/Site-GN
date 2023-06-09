<?php

class InstallController extends Controller
{
    protected $extensions = array('PDO', 'pdo_mysql');
    protected $writableFiles = array('data' => 'dir', 'upload' => 'dir', 'php/config/app.settings.php' => 'file', 'php/config/parameters.php' => 'file', 'php/config/widget.blacklist.txt' => 'file');
    protected $writableFilesForVerify = array('php/config/parameters.php' => 'file');
    protected $memoryServices = array('memory', 'memory.talks_map', 'memory.watched_talks', 'memory.stats', 'memory.online', 'memory.msg_q');
    public function indexAction() {
        return $this -> render('admin/install.html');
    }
    public function verifyAction() {
        return $this -> render('admin/install-verify.html', array('config' => $this -> get('config') -> data, 'trans' => json_encode($this -> getJsTranslations())));
    }
    public function verifySubmitAction() {
        $sp32157d = $this -> get('request');
        $sp54244a = $this -> get('verify');
        $sp80634b = $sp32157d -> postVar('config');
        $spe4c800 = $sp80634b['services']['verify']['code'];
        $sp552e31 = $sp80634b['services']['verify']['token'];
        $sp1d6971 = array();
        foreach($this -> writableFilesForVerify as $sp791693 => $sp45e46b) {
            if (!is_writable(ROOT_DIR.
                    '/../'.$sp791693)) {
                $sp1d6971[$sp791693] = $sp45e46b;
            }
        }
        if (!empty($sp1d6971)) {
            return $this -> render('admin/install-verify.html', array('config' => $this -> get('config') -> data, 'notWritable' => $sp1d6971, 'trans' => json_encode($this -> getJsTranslations())));
        }
        if ($sp54244a -> verifyCodeToken($spe4c800, $sp552e31)) {
            $sp54244a -> updateInstallation($spe4c800, $sp552e31);
            return $this -> redirect('Install:wizard');
        }
        return $this -> redirect('Install:verify');
    }
    public function wizardAction() {
        ini_set('opcache.enable', 0);
        if (!$this -> get('verify') -> verifyInstallation()) {
            return $this -> redirect('Install:verify');
        }
        $sp266aa9 = $this -> get('config') -> data;
        return $this -> render('admin/install-wizard.html', array('config' => $sp266aa9));
    }
    public function wizard2Action() {
        $sp32157d = $this -> get('request');
        $spf7a730 = $this -> get('config');
        $sp266aa9 = $sp32157d -> postVar('config');
        $sp221ec4 = $this -> ensureValidConfig();
        if (!empty($sp221ec4)) {
            return $sp221ec4;
        }
        $sp391873 = array();
        foreach($this -> extensions as $sp200a29) {
            if (!extension_loaded($sp200a29)) {
                $sp391873[] = $sp200a29;
            }
        }
        if (!empty($sp391873)) {
            return $this -> render('admin/install-wizard.html', array('config' => $sp266aa9, 'missingExtensions' => $sp391873));
        }
        $sp1d6971 = array();
        foreach($this -> writableFiles as $sp791693 => $sp45e46b) {
            if (!is_writable(ROOT_DIR.
                    '/../'.$sp791693)) {
                $sp1d6971[$sp791693] = $sp45e46b;
            }
        }
        if (!empty($sp1d6971)) {
            return $this -> render('admin/install-wizard.html', array('config' => $sp266aa9, 'notWritable' => $sp1d6971));
        }
        if (!$this -> createMemoryFiles()) {
            return $this -> render('admin/install-wizard.html', array('config' => $sp266aa9, 'notWritable' => array('data' => 'dir')));
        }
        $sp024a0a = false;
        $sp25b805 = $spf7a730 -> data['dbType'].
        ':host='.$sp266aa9['dbHost'].
        ';port='.$sp266aa9['dbPort'];
        try {
            $spf7a730 -> data['appSettings']['installed'] = false;
            if (!$this -> get('db') -> connect($sp25b805, $sp266aa9['dbUser'], $sp266aa9['dbPassword'])) {
                $sp024a0a = true;
            }
        } catch (Exception $sp566d7a) {
            $sp024a0a = true;
        }
        if ($sp024a0a) {
            return $this -> render('admin/install-wizard.html', array('config' => $sp266aa9, 'dbError' => $sp024a0a));
        }
        return $this -> render('admin/install-wizard-2.html', array('config' => $sp266aa9));
    }
    public function wizard3Action() {
        $sp32157d = $this -> get('request');
        $spf7a730 = $this -> get('config');
        $sp266aa9 = $sp32157d -> postVar('config');
        $sp221ec4 = $this -> ensureValidConfig();
        if (!empty($sp221ec4)) {
            return $sp221ec4;
        }
        unset($sp266aa9['superPassRepeat']);
        $sp44548f = $this -> get('config');
        $sp44548f -> updateParameters($sp266aa9);
        $sp44548f -> updateAppSettings($sp266aa9['appSettings']);
        ini_set('opcache.enable', 0);
        $sp44548f -> onRegister();
        $sp816477 = $this -> install();
        if (!$sp816477['success']) {
            $sp86406a = true;
            $sp5b8149 = $sp816477['message'];
            return $this -> render('admin/install-wizard.html', array('config' => $sp266aa9, 'dbCreateError' => $sp86406a, 'message' => $sp5b8149));
        }
        $sp9d1be0 = $this -> get('model_validation') -> validateDb();
        if (count($sp9d1be0) !== 0) {
            $sp86406a = true;
            $sp5b8149 = $sp9d1be0['message'];
            return $this -> render('admin/install-wizard.html', array('config' => $sp266aa9, 'dbCreateError' => $sp86406a, 'message' => $sp5b8149));
        }
        $this -> get('logger') -> info('Application installed');
        return $this -> render('admin/install-wizard-3.html');
    }
    public function uninstallAction() {
        return $this -> render('admin/uninstall.html');
    }
    public function uninstall2Action() {
        $sp32157d = $this -> get('request');
        if (!$sp32157d -> isPost()) {
            return $this -> redirect('Install:uninstall');
        }
        $sp816477 = $this -> uninstall();
        if (!$sp816477['success']) {
            $sp60ab68 = true;
            $spa312ef = $sp816477['errorMsg'];
            return $this -> render('admin/uninstall.html', array('error' => $sp60ab68, 'errorMsg' => $spa312ef));
        }
        $this -> get('logger') -> info('Application uninstalled');
        return $this -> render('admin/uninstall-2.html');
    }
    protected function createMemoryFiles() {
        $sp816477 = true;
        foreach($this -> memoryServices as $sp2fc498) {
            if (!touch($this -> get($sp2fc498) -> getFilePath())) {
                $sp816477 = false;
            }
        }
        return $sp816477;
    }
    protected function ensureValidConfig() {
        $sp32157d = $this -> get('request');
        if ($sp32157d -> isPost()) {
            $sp266aa9 = $sp32157d -> postVar('config');
            $sp5a8f3e = $this -> get('model_validation');
            $sp9d1be0 = $sp5a8f3e -> validateInstallConfig($sp266aa9);
            if (count($sp9d1be0) !== 0) {
                return $this -> render('admin/install-wizard.html', array('config' => $sp266aa9, 'errors' => $sp9d1be0));
            }
        } else {
            return $this -> redirect('Install:wizard');
        }
        return null;
    }
    protected function install() {
        $sp93a5f1 = array('message' => '');
        if ($this -> get('request') -> isPost()) {
            $sp266aa9 = $this -> get('config');
            try {
                $sp21035f = file_get_contents(ROOT_DIR.
                    '/../sql/install_'.$sp266aa9 -> data['dbType'].
                    '.sql');
                $sp21035f = str_replace('%db_name%', $sp266aa9 -> data['dbName'], $sp21035f);
                $sp266aa9 -> data['appSettings']['installed'] = false;
                $sp93a5f1['success'] = $this -> get('db') -> execute($sp21035f);
            } catch (Exception $sp8f5c4b) {
                $sp93a5f1['success'] = false;
                $sp93a5f1['message'] = $sp8f5c4b -> getMessage();
            }
            if ($sp93a5f1['success']) {
                $sp266aa9 -> updateAppSettings(array('installed' => true));
                $sp93a5f1 = $this -> createAdmin($sp266aa9);
                if ($sp93a5f1['success']) {
                    $sp6c63d9 = $sp93a5f1['admin'];
                    $this -> get('auth') -> setUser($sp6c63d9 -> id, $sp6c63d9 -> name, $sp6c63d9 -> roles);
                }
            } else {
                if (empty($sp93a5f1['message'])) {
                    $sp93a5f1['message'] = $this -> get('i18n') -> trans('other.error');
                }
            }
        }
        return $sp93a5f1;
    }
    protected function createAdmin($sp266aa9) {
        $sp93a5f1 = array('message' => '');
        $this -> get('db') -> reconnect();
        $sp6c63d9 = UserModel::repo() -> findOneBy(array('roles' => array('Like', '%ADMIN%')));
        if (!$sp6c63d9) {
            $sp6c63d9 = new UserModel(array('roles' => array('ADMIN', 'OPERATOR'), 'info' => array('ip' => $this -> get('request') -> getIp())));
        }
        $sp6c63d9 -> name = $sp266aa9 -> data['superUser'];
        $sp6c63d9 -> mail = $sp266aa9 -> data['superUser'];
        $sp6c63d9 -> password = $this -> get('security') -> encodePassword($sp266aa9 -> data['superPass']);
        if ($sp6c63d9 -> save()) {
            $sp93a5f1['success'] = true;
            $sp93a5f1['admin'] = $sp6c63d9;
        } else {
            $sp93a5f1['success'] = false;
            $sp93a5f1['message'] = $this -> get('i18n') -> trans('admin.update.error');
        }
        return $sp93a5f1;
    }
    protected function uninstall() {
        $sp93a5f1 = array();
        if ($this -> get('request') -> isPost()) {
            $sp266aa9 = $this -> get('config');
            try {
                $sp21035f = file_get_contents(ROOT_DIR.
                    '/../sql/uninstall_'.$sp266aa9 -> data['dbType'].
                    '.sql');
                $sp21035f = str_replace('%db_name%', $sp266aa9 -> data['dbName'], $sp21035f);
                $sp93a5f1['success'] = $this -> get('db') -> execute($sp21035f);
            } catch (Exception $sp8f5c4b) {
                $sp93a5f1['success'] = false;
                $sp93a5f1['errorMsg'] = $sp8f5c4b -> getMessage();
            }
            if ($sp93a5f1['success']) {
                $sp4fd828 = array('id' => '-1', 'name' => $sp266aa9 -> data['superUser'], 'roles' => array('ADMIN'));
                $this -> get('auth') -> setUser($sp4fd828['id'], $sp4fd828['name'], $sp4fd828['roles']);
                $sp266aa9 = $this -> get('config');
                $sp266aa9 -> updateAppSettings(array('installed' => false));
                $sp266aa9 -> updateParameters(array('superPass' => ''));
            } else {
                $sp93a5f1['error'] = $sp93a5f1['errorMsg'] = $this -> get('i18n') -> trans('uninstall.error');
            }
        }
        return $sp93a5f1;
    }
    protected function getJsTranslations() {
        $spdc8826 = $this -> get('i18n');
        return array('connection.error' => $spdc8826 -> trans('connection.error'), 'invalid.code.format' => $spdc8826 -> trans('invalid.code.format'), 'invalid.purchase' => $spdc8826 -> trans('invalid.purchase'));
    }
}