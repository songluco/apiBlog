<?php

namespace App\Console\Commands;

use Curl\Curl;
use Illuminate\Console\Command;

class ImitateLoginCodeCast extends Command
{
    protected $url = 'https://www.codecasts.com/user/login?redirect_url=https://www.codecasts.com';

    protected $email = 'songluco@163.com';

    protected $pwd= '6639*songlu*18?';

    /** @var string 第一次表单请求响应的页面内容信息 */
    protected $firstRequestResponseInfo = '/Users/songlu/codecasts/blog/storage/logs/firstRequestResponseInfo.txt';

    /** @var string 第一次表单请求响应的cookie信息 XSRF-TOKEN */
    protected $cookie_xsrf_file = '/Users/songlu/codecasts/blog/app/Console/cookie/xsrf.cookie';

    /** @var string  第一次表单请求响应的cookie信息 laravel_session */
    protected $cookie_laravel_file = '/Users/songlu/codecasts/blog/app/Console/cookie/laravel.cookie';

    /** @var string 模拟登录成功后获取到的home页面信息 */
    protected $responseHomeFilePath = '/Users/songlu/codecasts/blog/storage/logs/HomePageInfo.txt';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ImitateLoginCodeCast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '模拟登陆CodeCast网站';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->executeLogin();
    }


    public function executeLogin()
    {
        //获取cookie信息保存到本地
        $this->curlGet();

        //读取本地cookie发送post请求，模拟登录
        $this->curlPost();

        //登录成功后，获取home页面信息
        $this->getHomeInfo();

    }



    protected function curlGet()
    {
        $curl = new Curl();
        $curl->get($this->url);

        if ($curl->error) {
            echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
        } else {
            file_put_contents($this->firstRequestResponseInfo, $curl->response);
            file_put_contents($this->cookie_xsrf_file, $curl->getResponseCookie('XSRF-TOKEN'));
            file_put_contents($this->cookie_laravel_file, $curl->getResponseCookie('laravel_session'));
        }
    }


    protected function curlPost()
    {
        $curl = new Curl();
        //获取token信息
        $token = $this->getToken();
        $csrf = file_get_contents($this->cookie_xsrf_file);
        $laravel = file_get_contents($this->cookie_laravel_file);
        $curl->setCookie('XSRF-TOKEN', $csrf);
        $curl->setCookie('laravel_session', $laravel);
        $data = [
            'email' => $this->email,
            'password' => $this->pwd,
            '_token' => $token
        ];
        $curl->post($this->url, $data);
        if ($curl->error) {
            echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
        } else {
            echo 'Response:' . "\n";
            file_put_contents($this->cookie_xsrf_file, $curl->getResponseCookie('XSRF-TOKEN'));
            file_put_contents($this->cookie_laravel_file, $curl->getResponseCookie('laravel_session'));
        }
    }

    public function getHomeInfo()
    {
        $url = 'https://www.codecasts.com/user/profile';
        $curl = new Curl();
        $csrf = file_get_contents($this->cookie_xsrf_file);
        $laravel = file_get_contents($this->cookie_laravel_file);
        $curl->setCookie('XSRF-TOKEN', $csrf);
        $curl->setCookie('laravel_session', $laravel);
        $curl->get($url);

        if ($curl->error) {
            echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
        } else {
            file_put_contents($this->responseHomeFilePath, $curl->response);
        }
    }


    public function getToken()
    {
        $str = file_get_contents($this->firstRequestResponseInfo);
        preg_match('/<input type="hidden" name="_token" value="(.*)"/U', $str, $match);
        return $match[1];
    }


}
