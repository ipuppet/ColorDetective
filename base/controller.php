<?php

/**
 * Created by PhpStorm.
 * User: 61534
 * Date: 2019/1/27
 * Time: 下午 11:41
 */
class controller
{
    public function run()
    {
        include 'model.php';
        $model = new model();
        if (isset($_GET['v']) && !empty($_GET['v'])) {
            $view = $_GET['v'];
        } else {
            $view = 'start';
        }
        $this->loadView($model, $view);

        if (@$_POST['action'] == 'login')
            $this->login($model, $_POST);
        elseif (@$_POST['action'] == 'registered')
            $this->registered($model, $_POST);
        elseif (@$_POST['action'] == 'logout')
            $this->logout();
    }

    public function loadView($model, $view)
    {
        $template = __VIEW__ . $view . '.php';
        include $template;
    }

    public function login($model, $info)
    {
        $ret = $model->login($info);
        switch ($ret) {
            case '0':
                echo '<script>window.parent.showMsg("登陆成功",3000);setTimeout("window.parent.location.reload()",1000)</script>';
                break;
            case '1':
                echo '<script>window.parent.showMsg("密码错误",3000);</script>';
                break;
            case '2':
                echo '<script>window.parent.showMsg("无该用户",3000);</script>';
                break;
        }
    }

    public function logout()
    {
        if (session_destroy()) {
            echo '<script>window.parent.showMsg("已登出",3000);setTimeout("window.parent.location.reload()",1000)</script>';
        }
    }

    public function registered($model, $info)
    {
        $ret = $model->registered($info);
        switch ($ret) {
            case '0':
                echo '<script>window.parent.showMsg("注册成功",3000);setTimeout("window.parent.changeBox(0)",1000)</script>';
                break;
            case '1':
                echo '<script>window.parent.showMsg("该学号已被注册",3000);</script>';
                break;
            case '2':
                echo '<script>window.parent.showMsg("注册失败，请重试或联系管理员",3000);</script>';
                break;
            case '3':
                echo '<script>window.parent.showMsg("注册失败，学号为11位数字",3000);</script>';
                break;
            case '4':
                echo '<script>window.parent.showMsg("密码不能为空",3000);</script>';
                break;
            case '5':
                echo '<script>window.parent.showMsg("用户名不能为空",3000);</script>';
                break;
        }
    }
}