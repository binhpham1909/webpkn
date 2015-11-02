<?php 
require_once("../config/config.php"); 
require_once("../config/lang.ini.php");
header('Content-Type: text/html; charset=UTF-8');
session_start();
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

$action=$request->action;
$username=$request->username;
$password=$request->password;
$reply=array();
if ($action == "login")
{
    // Dùng hàm addslashes() để tránh SQL injection, dùng hàm md5() để mã hóa password
    $username = addslashes( $username );
    $password = md5( addslashes( $password ) );
    // Lấy thông tin của username đã nhập trong table members
    $sql_query = @mysql_query("SELECT * FROM pkn_user WHERE username='{$username}' AND password='{$password}'");
    $member = @mysql_fetch_array( $sql_query );
    // Nếu username này không tồn tại thì....
    if ( @mysql_num_rows( $sql_query ) <= 0 )
    {
		$reply['result']="err";
		$reply['message']="Sai tên truy nhập hoặc mật khẩu.";
    }else{
		    // Khởi động phiên làm việc (session)
		$_SESSION['user_id'] = $member['id'];
		$_SESSION['user'] = $member['name'];
		$_SESSION['admin'] = $member['admin'];
		$reply['result']="ok";
		$reply['message']='Đăng nhập thành công.';
	}
    // Thông báo đăng nhập thành công
}else if( $action == "logout"){
	if (session_destroy()){
		$reply['result']="ok";
		$reply['message']='Thoát thành công.';		
	}
	else{
		$reply['result']="err";
		$reply['message']='KO thể thoát dc, có lỗi trong việc hủy session';		
	}
}
echo json_encode($reply);
?>