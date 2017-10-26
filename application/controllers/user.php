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
        $username = $this->getRequest()->getPost('username', '');
        $password = $this->getRequest()->getPost('password', '');
        $submit = $this->getRequest()->getPost('submit', 0); //为了防止爬虫，其实人为看一下多传递个参数就可以了，但post传递的参数好像看不到

        if ($submit != 1) {
            echo json_encode([
                'errno'=>6,
                'errmsg'=>'请通过正常渠道登录'
            ]);
        }

        if (!$username || !$password) {
            echo json_encode([
                'errno'=>1,
                'errmsg'=>'用户名或者密码必须传递'
            ]);
        }

        $userModel = new userModel();
        $result = $userModel->login($username, $password);
        if (!$result) {
            echo json_encode([
                'errno'=>$userModel->errno,
                'errmsg'=>$userModel->errmsg,
            ]);
            return false;
        } else {
            session_start();
            $_SESSION['userid'] = $result;
            $_SESSION['user_token'] = md5('cy'.$_SESSION['REQUEST_TIME'].$result);
            $_SESSION['request_time'] = $_SESSION['REQUEST_TIME'];

            echo json_encode([
                'errno'=>7,
                'errmsg'=>'登录成功'
                ]);
            return true;
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
		$password = $this->getRequest()->getPost('password', '');
        $username = $this->getRequest()->getPost('username', '');  //感觉这个$this->getRequest()就和我们项目中接口接受的那个参数一样，然后调用获取post参数的方法
        //$password = $this->getRequest()->getQuery('password', '');
        //验证参数

		if (!$username || !$password) {
		    echo json_encode(['errno'=>1, 'errmsg'=>'用户名和密码必须传递']); //数组转换成json格式传输
		    return false; //为了不去找视图层
        }

		$userModel = new UserModel();
		if (!$userModel->register(trim($username), trim($password))) {
		    echo json_encode([
		        'errno'=>$userModel->errno,
                'errmsg'=>$userModel->errmsg,
            ]);
		    return false;
        } else {
		    echo json_encode([
		        'errno'=>2,
                'errmsg'=>'',
                'data'=>['username'=>$username]
            ]);
		    return true;
        }
	}
}