<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think;

use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\RouteNotFoundException;

/**
 * App Ӧ�ù���
 * @author  liu21st <liu21st@gmail.com>
 */
class App
{
    /**
     * @var bool �Ƿ��ʼ����
     */
    protected static $init = false;

    /**
     * @var string ��ǰģ��·��
     */
    public static $modulePath;

    /**
     * @var bool Ӧ�õ���ģʽ
     */
    public static $debug = true;

    /**
     * @var string Ӧ����������ռ�
     */
    public static $namespace = 'app';

    /**
     * @var bool Ӧ������׺
     */
    public static $suffix = false;

    /**
     * @var bool Ӧ��·�ɼ��
     */
    protected static $routeCheck;

    /**
     * @var bool �ϸ�·�ɼ��
     */
    protected static $routeMust;

    protected static $dispatch;
    protected static $file = [];

    /**
     * ִ��Ӧ�ó���
     * @access public
     * @param Request $request Request����
     * @return Response
     * @throws Exception
     */
    public static function run(Request $request = null)
    {
        is_null($request) && $request = Request::instance();

        try {
            $config = self::initCommon();
            if (defined('BIND_MODULE')) {
                // ģ��/��������
                BIND_MODULE && Route::bind(BIND_MODULE);
            } elseif ($config['auto_bind_module']) {
                // ����Զ���
                $name = pathinfo($request->baseFile(), PATHINFO_FILENAME);
                if ($name && 'index' != $name && is_dir(APP_PATH . $name)) {
                    Route::bind($name);
                }
            }

            $request->filter($config['default_filter']);

            if ($config['lang_switch_on']) {
                // ���������Ի��� ��⵱ǰ����
                Lang::detect();
            } else {
                // ��ȡĬ������
                Lang::range($config['default_lang']);
            }
            $request->langset(Lang::range());
            // ����ϵͳ���԰�
            Lang::load([
                THINK_PATH . 'lang' . DS . $request->langset() . EXT,
                APP_PATH . 'lang' . DS . $request->langset() . EXT,
            ]);

            // ��ȡӦ�õ�����Ϣ
            $dispatch = self::$dispatch;
            if (empty($dispatch)) {
                // ����URL·�ɼ��
                $dispatch = self::routeCheck($request, $config);
            }
            // ��¼��ǰ������Ϣ
            $request->dispatch($dispatch);

            // ��¼·�ɺ�������Ϣ
            if (self::$debug) {
                Log::record('[ ROUTE ] ' . var_export($dispatch, true), 'info');
                Log::record('[ HEADER ] ' . var_export($request->header(), true), 'info');
                Log::record('[ PARAM ] ' . var_export($request->param(), true), 'info');
            }

            // ����app_begin
            Hook::listen('app_begin', $dispatch);
            // ���󻺴���
            $request->cache($config['request_cache'], $config['request_cache_expire']);

            switch ($dispatch['type']) {
                case 'redirect':
                    // ִ���ض�����ת
                    $data = Response::create($dispatch['url'], 'redirect')->code($dispatch['status']);
                    break;
                case 'module':
                    // ģ��/������/����
                    $data = self::module($dispatch['module'], $config, isset($dispatch['convert']) ? $dispatch['convert'] : null);
                    break;
                case 'controller':
                    // ִ�п���������
                    $vars = array_merge(Request::instance()->param(), $dispatch['var']);
                    $data = Loader::action($dispatch['controller'], $vars, $config['url_controller_layer'], $config['controller_suffix']);
                    break;
                case 'method':
                    // ִ�лص�����
                    $vars = array_merge(Request::instance()->param(), $dispatch['var']);
                    $data = self::invokeMethod($dispatch['method'], $vars);
                    break;
                case 'function':
                    // ִ�бհ�
                    $data = self::invokeFunction($dispatch['function']);
                    break;
                case 'response':
                    $data = $dispatch['response'];
                    break;
                default:
                    throw new \InvalidArgumentException('dispatch type not support');
            }
        } catch (HttpResponseException $exception) {
            $data = $exception->getResponse();
        }

        // ������ʵ����
        Loader::clearInstance();

        // ������ݵ��ͻ���
        if ($data instanceof Response) {
            $response = $data;
        } elseif (!is_null($data)) {
            // Ĭ���Զ�ʶ����Ӧ�������
            $isAjax   = $request->isAjax();
            $type     = $isAjax ? Config::get('default_ajax_return') : Config::get('default_return_type');
            $response = Response::create($data, $type);
        } else {
            $response = Response::create();
        }

        // ����app_end
        Hook::listen('app_end', $response);

        return $response;
    }

    /**
     * ���õ�ǰ����ĵ�����Ϣ
     * @access public
     * @param array|string  $dispatch ������Ϣ
     * @param string        $type ��������
     * @return void
     */
    public static function dispatch($dispatch, $type = 'module')
    {
        self::$dispatch = ['type' => $type, $type => $dispatch];
    }

    /**
     * ִ�к������߱հ����� ֧�ֲ�������
     * @access public
     * @param string|array|\Closure $function �������߱հ�
     * @param array                 $vars     ����
     * @return mixed
     */
    public static function invokeFunction($function, $vars = [])
    {
        $reflect = new \ReflectionFunction($function);
        $args    = self::bindParams($reflect, $vars);
        // ��¼ִ����Ϣ
        self::$debug && Log::record('[ RUN ] ' . $reflect->__toString(), 'info');
        return $reflect->invokeArgs($args);
    }

    /**
     * ���÷���ִ����ķ��� ֧�ֲ�����
     * @access public
     * @param string|array $method ����
     * @param array        $vars   ����
     * @return mixed
     */
    public static function invokeMethod($method, $vars = [])
    {
        if (is_array($method)) {
            $class   = is_object($method[0]) ? $method[0] : self::invokeClass($method[0]);
            $reflect = new \ReflectionMethod($class, $method[1]);
        } else {
            // ��̬����
            $reflect = new \ReflectionMethod($method);
        }
        $args = self::bindParams($reflect, $vars);

        self::$debug && Log::record('[ RUN ] ' . $reflect->class . '->' . $reflect->name . '[ ' . $reflect->getFileName() . ' ]', 'info');
        return $reflect->invokeArgs(isset($class) ? $class : null, $args);
    }

    /**
     * ���÷���ִ�����ʵ���� ֧������ע��
     * @access public
     * @param string    $class ����
     * @param array     $vars  ����
     * @return mixed
     */
    public static function invokeClass($class, $vars = [])
    {
        $reflect     = new \ReflectionClass($class);
        $constructor = $reflect->getConstructor();
        if ($constructor) {
            $args = self::bindParams($constructor, $vars);
        } else {
            $args = [];
        }
        return $reflect->newInstanceArgs($args);
    }

    /**
     * �󶨲���
     * @access public
     * @param \ReflectionMethod|\ReflectionFunction $reflect ������
     * @param array                                 $vars    ����
     * @return array
     */
    private static function bindParams($reflect, $vars = [])
    {
        if (empty($vars)) {
            // �Զ���ȡ�������
            if (Config::get('url_param_type')) {
                $vars = Request::instance()->route();
            } else {
                $vars = Request::instance()->param();
            }
        }
        $args = [];
        // �ж��������� ��������ʱ��˳��󶨲���
        reset($vars);
        $type = key($vars) === 0 ? 1 : 0;
        if ($reflect->getNumberOfParameters() > 0) {
            $params = $reflect->getParameters();
            foreach ($params as $param) {
                $name  = $param->getName();
                $class = $param->getClass();
                if ($class) {
                    $className = $class->getName();
                    $bind      = Request::instance()->$name;
                    if ($bind instanceof $className) {
                        $args[] = $bind;
                    } else {
                        if (method_exists($className, 'invoke')) {
                            $method = new \ReflectionMethod($className, 'invoke');
                            if ($method->isPublic() && $method->isStatic()) {
                                $args[] = $className::invoke(Request::instance());
                                continue;
                            }
                        }
                        $args[] = method_exists($className, 'instance') ? $className::instance() : new $className;
                    }
                } elseif (1 == $type && !empty($vars)) {
                    $args[] = array_shift($vars);
                } elseif (0 == $type && isset($vars[$name])) {
                    $args[] = $vars[$name];
                } elseif ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                } else {
                    throw new \InvalidArgumentException('method param miss:' . $name);
                }
            }
        }
        return $args;
    }

    /**
     * ִ��ģ��
     * @access public
     * @param array $result ģ��/������/����
     * @param array $config ���ò���
     * @param bool  $convert �Ƿ��Զ�ת���������Ͳ�����
     * @return mixed
     */
    public static function module($result, $config, $convert = null)
    {
        if (is_string($result)) {
            $result = explode('/', $result);
        }
        $request = Request::instance();
        if ($config['app_multi_module']) {
            // ��ģ�鲿��
            $module    = strip_tags(strtolower($result[0] ?: $config['default_module']));
            $bind      = Route::getBind('module');
            $available = false;
            if ($bind) {
                // ��ģ��
                list($bindModule) = explode('/', $bind);
                if (empty($result[0])) {
                    $module    = $bindModule;
                    $available = true;
                } elseif ($module == $bindModule) {
                    $available = true;
                }
            } elseif (!in_array($module, $config['deny_module_list']) && is_dir(APP_PATH . $module)) {
                $available = true;
            }

            // ģ���ʼ��
            if ($module && $available) {
                // ��ʼ��ģ��
                $request->module($module);
                $config = self::init($module);
                // ģ�����󻺴���
                $request->cache($config['request_cache'], $config['request_cache_expire']);
            } else {
                throw new HttpException(404, 'module not exists:' . $module);
            }
        } else {
            // ��һģ�鲿��
            $module = '';
            $request->module($module);
        }
        // ��ǰģ��·��
        App::$modulePath = APP_PATH . ($module ? $module . DS : '');

        // �Ƿ��Զ�ת���������Ͳ�����
        $convert = is_bool($convert) ? $convert : $config['url_convert'];
        // ��ȡ��������
        $controller = strip_tags($result[1] ?: $config['default_controller']);
        $controller = $convert ? strtolower($controller) : $controller;

        // ��ȡ������
        $actionName = strip_tags($result[2] ?: $config['default_action']);
        $actionName = $convert ? strtolower($actionName) : $actionName;

        // ���õ�ǰ����Ŀ�����������
        $request->controller(Loader::parseName($controller, 1))->action($actionName);

        // ����module_init
        Hook::listen('module_init', $request);

        $instance = Loader::controller($controller, $config['url_controller_layer'], $config['controller_suffix'], $config['empty_controller']);
        if (is_null($instance)) {
            throw new HttpException(404, 'controller not exists:' . Loader::parseName($controller, 1));
        }
        // ��ȡ��ǰ������
        $action = $actionName . $config['action_suffix'];

        $vars = [];
        if (is_callable([$instance, $action])) {
            // ִ�в�������
            $call = [$instance, $action];
        } elseif (is_callable([$instance, '_empty'])) {
            // �ղ���
            $call = [$instance, '_empty'];
            $vars = [$actionName];
        } else {
            // ����������
            throw new HttpException(404, 'method not exists:' . get_class($instance) . '->' . $action . '()');
        }

        Hook::listen('action_begin', $call);

        return self::invokeMethod($call, $vars);
    }

    /**
     * ��ʼ��Ӧ��
     */
    public static function initCommon()
    {
        if (empty(self::$init)) {
            // ��ʼ��Ӧ��
            $config       = self::init();
            self::$suffix = $config['class_suffix'];

            // Ӧ�õ���ģʽ
            self::$debug = Env::get('app_debug', Config::get('app_debug'));
            if (!self::$debug) {
                ini_set('display_errors', 'Off');
            } elseif (!IS_CLI) {
                //��������һ��Ƚϴ��buffer
                if (ob_get_level() > 0) {
                    $output = ob_get_clean();
                }
                ob_start();
                if (!empty($output)) {
                    echo $output;
                }
            }

            // ע��Ӧ�������ռ�
            self::$namespace = $config['app_namespace'];
            Loader::addNamespace($config['app_namespace'], APP_PATH);
            if (!empty($config['root_namespace'])) {
                Loader::addNamespace($config['root_namespace']);
            }

            // ���ض����ļ�
            if (!empty($config['extra_file_list'])) {
                foreach ($config['extra_file_list'] as $file) {
                    $file = strpos($file, '.') ? $file : APP_PATH . $file . EXT;
                    if (is_file($file) && !isset(self::$file[$file])) {
                        include $file;
                        self::$file[$file] = true;
                    }
                }
            }

            // ����ϵͳʱ��
            date_default_timezone_set($config['default_timezone']);

            // ����app_init
            Hook::listen('app_init');

            self::$init = true;
        }
        return Config::get();
    }

    /**
     * ��ʼ��Ӧ�û�ģ��
     * @access public
     * @param string $module ģ����
     * @return array
     */
    private static function init($module = '')
    {
        // ��λģ��Ŀ¼
        $module = $module ? $module . DS : '';

        // ���س�ʼ���ļ�
        if (is_file(APP_PATH . $module . 'init' . EXT)) {
            include APP_PATH . $module . 'init' . EXT;
        } elseif (is_file(RUNTIME_PATH . $module . 'init' . EXT)) {
            include RUNTIME_PATH . $module . 'init' . EXT;
        } else {
            $path = APP_PATH . $module;
            // ����ģ������
            $config = Config::load(CONF_PATH . $module . 'config' . CONF_EXT);
            // ��ȡ���ݿ������ļ�
            $filename = CONF_PATH . $module . 'database' . CONF_EXT;
            Config::load($filename, 'database');
            // ��ȡ��չ�����ļ�
            if (is_dir(CONF_PATH . $module . 'extra')) {
                $dir   = CONF_PATH . $module . 'extra';
                $files = scandir($dir);
                foreach ($files as $file) {
                    if (strpos($file, CONF_EXT)) {
                        $filename = $dir . DS . $file;
                        Config::load($filename, pathinfo($file, PATHINFO_FILENAME));
                    }
                }
            }

            // ����Ӧ��״̬����
            if ($config['app_status']) {
                $config = Config::load(CONF_PATH . $module . $config['app_status'] . CONF_EXT);
            }

            // ������Ϊ��չ�ļ�
            if (is_file(CONF_PATH . $module . 'tags' . EXT)) {
                Hook::import(include CONF_PATH . $module . 'tags' . EXT);
            }

            // ���ع����ļ�
            if (is_file($path . 'common' . EXT)) {
                include $path . 'common' . EXT;
            }

            // ���ص�ǰģ�����԰�
            if ($module) {
                Lang::load($path . 'lang' . DS . Request::instance()->langset() . EXT);
            }
        }
        return Config::get();
    }

    /**
     * URL·�ɼ�⣨����PATH_INFO)
     * @access public
     * @param  \think\Request $request
     * @param  array          $config
     * @return array
     * @throws \think\Exception
     */
    public static function routeCheck($request, array $config)
    {
        $path   = $request->path();
        $depr   = $config['pathinfo_depr'];
        $result = false;
        // ·�ɼ��
        $check = !is_null(self::$routeCheck) ? self::$routeCheck : $config['url_route_on'];
        if ($check) {
            // ����·��
            if (is_file(RUNTIME_PATH . 'route.php')) {
                // ��ȡ·�ɻ���
                $rules = include RUNTIME_PATH . 'route.php';
                if (is_array($rules)) {
                    Route::rules($rules);
                }
            } else {
                $files = $config['route_config_file'];
                foreach ($files as $file) {
                    if (is_file(CONF_PATH . $file . CONF_EXT)) {
                        // ����·������
                        $rules = include CONF_PATH . $file . CONF_EXT;
                        if (is_array($rules)) {
                            Route::import($rules);
                        }
                    }
                }
            }

            // ·�ɼ�⣨����·�ɶ��巵�ز�ͬ��URL���ȣ�
            $result = Route::check($request, $path, $depr, $config['url_domain_deploy']);
            $must   = !is_null(self::$routeMust) ? self::$routeMust : $config['url_route_must'];
            if ($must && false === $result) {
                // ·����Ч
                throw new RouteNotFoundException();
            }
        }
        if (false === $result) {
            // ·����Ч ����ģ��/������/����/����... ֧�ֿ������Զ�����
            $result = Route::parseUrl($path, $depr, $config['controller_auto_search']);
        }
        return $result;
    }

    /**
     * ����Ӧ�õ�·�ɼ�����
     * @access public
     * @param  bool $route �Ƿ���Ҫ���·��
     * @param  bool $must  �Ƿ�ǿ�Ƽ��·��
     * @return void
     */
    public static function route($route, $must = false)
    {
        self::$routeCheck = $route;
        self::$routeMust  = $must;
    }
}
