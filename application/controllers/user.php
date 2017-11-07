<?php
/**
 * @name IndexController
 * @author desktop-ndk7m6a\admin
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class UserController extends Yaf_Controller_Abstract {


    public function indexAction() {
        return $this->loginAction();


    }

    public function loginAction() {
        $username = Common_Request::post('username', '');
        $password = Common_Request::request('password', '');
        $submit = Common_Request::post('submit', 0); //为了防止爬虫，其实人为看一下多传递个参数就可以了，但post传递的参数好像看不到

        if ($submit != 1) {
            echo Common_Request::response('', '请通过正常渠道提交');
            return false;
        }

        if (!$username || !$password) {
            echo Common_Request::response('', '用户名或者密码必须传递');
            return false;
        }

        $userModel = new userModel();
        $result = $userModel->login($username, $password);
        if (!$result) {
            echo Common_Request::response($userModel->errno, $userModel->errmsg);
            return false;
        } else {
            session_start();
            $_SESSION['userid'] = $result;
            $_SESSION['user_token'] = md5('cy'.time().$result);

            $_SESSION['login_time'] = time();

            echo Common_Request::response('', '', ['userid'=>$_SESSION['userid'], 'username'=>$username]);
            return false;
        }

    }
	/** 
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/yaf_api/index/index/index/name/desktop-ndk7m6a\admin 的时候, 你就会发现不同
     */
	public function regAction() {
        //获取参数
		//$username = $this->getRequest()->getQuery('username', '');  //感觉这个$this->getRequest()就和我们项目中接口接受的那个参数一样，然后调用获取post参数的方法,query可以获取post或者get传递过来的参数
		$password = Common_Request::post('password', '');
        $username = Common_Request::post('username', '');  //感觉这个$this->getRequest()就和我们项目中接口接受的那个参数一样，然后调用获取post参数的方法
        //$password = $this->getRequest()->getQuery('password', '');
        //验证参数

		if (!$username || !$password) {
		    echo Common_Request::response('', '用户名或者密码必须传递'); //数组转换成json格式传输
		    return false; //为了不去找视图层
        }

		$userModel = new UserModel();
		if (!$userModel->register(trim($username), trim($password))) {
		    echo json_encode($userModel->errno, $userModel->errmsg);
		    return false;
        } else {
		    echo Common_Request::response('', '', ['username'=>$username, 'password'=>$password]);
		    return false;    //yaf框架好像这里只能是return false ，要不然都会找模板，报错信息
        }
	}
}
