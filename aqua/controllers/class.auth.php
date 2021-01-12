<?php

class auth extends baseController {

    private $model;

    function __construct() {
        
        $this->model = $this->new('authModel');
    }

    /**
     * 기본 페이지
     */
    public function index(){
        # 현재 경로 최상위는 login 이므로 로그인 페이지로 이동시킨다.
        movePage('replace', '', '/auth/login');
    }

    /**
     * 로그인 페이지 구성
     */
    public function login(){

        $page_data['use_top'] = false;
        $page_data['use_footer'] = false;
        $page_data['rtn_page'] = $_GET['rtn_page'];
        $page_data['contents_path'] = '/auth/login.php';

        $this->view( $page_data );        
    }

    public function loginProc(){
        
        # post 접근 체크
        postCheck();

        // $this->echoBr( $_POST );
        
        # url query string
        $rtn_page = $_POST['rtn_page'];
        

        $get_data['id'] = $_POST['m_id'];
        $get_data['pw'] = $_POST['m_pw'];
        $get_data['rtn_page'] = $_POST['rtn_page'];
        
        if( empty( $get_data['id'] ) ) {
            errorBack('잘못된 접근 입니다.');
        }

        if( empty( $get_data['pw'] ) ) {
            errorBack('잘못된 접근 입니다.');
        }

        $get_data['pw'] = hash_conv( $get_data['pw'] ); 
        
        $login_state = $this->model->loginProc( $get_data );

        if( $login_state['return_data']['num_rows'] > 0 ) {
            # 로그인 처리

            $set_info['idx'] = $login_state['return_data']['row']['member_idx'];
            $set_info['country'] = $login_state['return_data']['row']['country_code'];
            $set_info['code'] = $login_state['return_data']['row']['member_code'];
            $set_info['name'] = $login_state['return_data']['row']['name'];
            $set_info['phone_number'] = $login_state['return_data']['row']['phone_number'];
            $set_info['email'] = $login_state['return_data']['row']['email'];
            
            # 세션등록처리
            setAccountSession( $set_info );
            
            if( empty( $get_data['rtn_page'] ) ) {
                movePage('replace', '', '/' );
            } else {
                movePage('replace', '', $get_data['rtn_page'] );
            }

        } else {

            # 로그인 실패
            errorBack('일치하는 계정정보가 없습니다.');
        }

    }

    /**
     * 로그아웃 처리를 수행한다.
     */
    public function logout(){
        
        # 로그아웃 처리
        logoutProc();

        movePage('replace', '', '/auth/login');

    }

}

?>