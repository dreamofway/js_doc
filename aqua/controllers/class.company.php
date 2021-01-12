<?php

class company extends baseController {

    private $model;
    private $page_data;
    private $paging;
    private $qrcode;
    private $country_model;
    private $contract_model;
    private $service_model;
    private $page_name;
    
    function __construct() {

        #로그인 확인
        loginState();
        
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # model instance
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $this->paging = $this->new('pageHelper');
        $this->model = $this->new('companyModel');
        $this->food_model = $this->new('foodModel');
        $this->qrcode = $this->new('QRcodeHandler');        
        $this->country_model = $this->new('countryModel'); # 국가코드 모델 인스턴스
        $this->contract_model = $this->new('contractModel'); 
        $this->service_model = $this->new('serviecModel'); 
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # GET parameters
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $this->page_data = $this->paging->getParameters();
       
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET params
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $this->page_data['params'] = $this->paging->setParams(['top_code', 'left_code', 'list_rows', 'sch_type', 'sch_service', 'sch_keyword', 'sch_s_date', 'sch_e_date']);

    }

    /**
     * 기업 목록 페이지를 구성한다.
     */
    public function company_list(){        

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $query_where = " AND del_flag='N' ";
        $query_sort = ' ORDER BY company_idx DESC ';
        $limit = " LIMIT ".(($this->page_data['page']-1)*$this->page_data['list_rows']).", ".$this->page_data['list_rows'];

        if( $this->page_data['sch_keyword'] ) {
            $query_where .= " AND ( 
                                    ( company_name LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    OR ( registration_no LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    OR ( ceo_name LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    OR ( partner_name LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                            ) ";
        }

        if($this->page_data['sch_s_date']) {
            $query_where .= " AND reg_date >= '".$this->page_data['sch_s_date']."' ";
        }

		if($this->page_data['sch_e_date']) {
            $query_where .= " AND reg_date <= '".$this->page_data['sch_e_date']."' ";
        }
/*
echoBr( apache_request_headers()['Host'] );
echoBr( apache_request_headers()['User-Agent'] );
echoBr( apache_request_headers()['Referer'] );
 exit;
 */
        # 리스트 정보요청
        $list_result = $this->model->getCompanys([            
            'query_where' => $query_where
            ,'query_sort' => $query_sort
            ,'limit' => $limit
        ]);

        $this->paging->total_rs = $list_result['total_rs'];        
        $this->page_data['paging'] = $this->paging; 
        
        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
        $this->page_data['page_name'] = 'company';
        $this->page_data['contents_path'] = '/company/company_list.php';
        $this->page_data['list'] = $list_result['rows'];        
        $this->view( $this->page_data );

    }

    /**
     * 기업정보 작성 페이지를 구성한다.
     */
    public function company_write(){
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=

        
        
        # 대분류 유형을 요청
        $this->page_data['foodtype_large'] = $this->food_model->getFoodTypes([            
            'query_where' => " AND ( depth = 1 ) AND use_flag='Y' "
            ,'query_sort' => " ORDER BY title ASC "
        ])['rows'];

        # 국가 코드 요청
        $this->page_data['country_codes'] = $this->country_model->getCountryCodes([            
            'query_where' => " AND use_flag='Y' "
            ,'query_sort' => " ORDER BY country_name ASC "
        ])['rows'];
        
        $this->page_data['added_food_types'] = [];
        $this->page_data['company_members'] = [];

        if( $this->page_data['mode'] == 'edit') {

            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
            # 필수값 체크
            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=            
            $this->issetParams( $this->page_data, ['company_idx']);
            
            $this->page_data['page_work'] = '수정';

            # 기업정보를 요청한다.
            $query_result = $this->model->getCompany( " company_idx = '". $this->page_data['company_idx'] ."' " );

            if( $query_result['num_rows'] == 0 ){
                
                errorBack('해당 게시물이 삭제되었거나 정상적인 접근 방법이 아닙니다.');
                
            }

            $this->page_data = array_merge( $this->page_data, $query_result['row'] );

            

            # 기업 QR code 를 요청한다.            
            $qrcode_result = $this->qrcode->getQRcode([
                'purpose' => 'certify'
                ,'tb_name' => 't_company_info'                
                ,'tb_key' => $this->page_data['company_idx']
            ]);

            if( $qrcode_result['num_rows'] > 0 ){
                
                $this->page_data = array_merge( $this->page_data, $qrcode_result['row'] );
                
            }

            # 기업회원 파트너 정보를 요청한다.
            $query_result = $this->model->getCompanyMember( 
                " company_idx = '". $this->page_data['company_idx'] ."' AND partner = 'Y' AND del_flag='N' "
            );

            if( $query_result['num_rows'] > 0 ){
                
                $this->page_data = array_merge( $this->page_data, $query_result['row'] );
                
            }

            

            # 기업회원 파트너 정보를 요청한다.
            $query_result = $this->model->getCompanyMember( 
                " company_idx = '". $this->page_data['company_idx'] ."' AND partner = 'N' AND del_flag='N' "
            );

            if( $query_result['num_rows'] > 0 ){
                $this->page_data['company_members'] = $query_result['rows'];
            } 
            

            # 제조식품 유형 정보를 요청한다.
            $query_result = $this->model->getFoodUsage( $this->page_data['company_idx'] );    
            
            if( $query_result['num_rows'] > 0 ){
                $this->page_data['added_food_types'] = $query_result['rows'];
            }
        } else {

            $this->page_data['mode'] = 'ins';
            $this->page_data['page_work'] = '등록';

        }
        

        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
        $this->page_data['page_name'] = 'company';
        $this->page_data['contents_path'] = '/company/company_write.php';
        $this->page_data['list'] = $list_result['rows'];
        
        $this->view( $this->page_data );

    }

    /**
     * 기업정보 데이터를 처리한다.
     */
    public function company_proc(){

        # post 접근 체크
        postCheck();

        // echoBr( $this->page_data );

        switch( $this->page_data['mode'] ) {

            case 'ins' : {
                
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 필수값 체크
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
                $this->issetParams( $this->page_data, [
                    'country_code'
                    ,'company_name'
                    ,'registration_no'
                    ,'ceo_name'
                    ,'company_tel'
                ]);

                # 기업 사업자등록 번호와 일치한 정보 확인
                $query_result = $this->model->getCompany( " registration_no = '". $this->page_data['registration_no'] ."' " );

                if( $query_result['num_rows'] > 0 ){
                    
                    errorBack('이미 등록된 사업자등록 번호 입니다.');
                    
                }
                
                # 트랜잭션 시작
                $this->model->runTransaction();

                # 기업 정보 삽입
                $query_result = $this->model->insertCompanyInfo([
                    'country_code' => $this->page_data['country_code']
                    ,'company_name' => $this->page_data['company_name']
                    ,'registration_no' => $this->page_data['registration_no']
                    ,'ceo_name' => $this->page_data['ceo_name']
                    ,'company_tel' => $this->page_data['company_tel']
                    ,'company_fax' => $this->page_data['company_fax']
                    ,'company_homepage' => $this->page_data['company_homepage']
                    ,'zip_code' => $this->page_data['zip_code']
                    ,'addr' => $this->page_data['addr']
                    ,'addr_detail' => $this->page_data['addr_detail']
                    ,'reg_idx' => getAccountInfo()['idx']
                    ,'reg_date' => 'NOW()'
                    ,'reg_ip' => $this->getIP()
                ]);
                
                # 기업정보 삽입 완료된 기본키를 가져온다.
                $new_company_idx = $query_result['return_data']['insert_id'];
                
                if( empty( $this->page_data['member_name'] ) == false ){
                    
                    # 기업 회원 정보 삽입
                    if( $this->page_data['password'] !== $this->page_data['re_password'] ) {
                        errorBack('비밀번호 값과 재입력 값이 일치하지 않습니다.');
                    }

                    $query_result = $this->model->insertCompanyMember([
                        'company_idx ' => $new_company_idx
                        ,'partner' => 'Y'
                        ,'member_name' => $this->page_data['member_name']
                        ,'phone_no' => $this->page_data['phone_no']
                        ,'email' => $this->page_data['email']
                        ,'password' => hash_conv( $this->page_data['password'] )
                        ,'reg_idx' => getAccountInfo()['idx']
                        ,'reg_date' => 'NOW()'
                        ,'reg_ip' => $this->getIP()
                    ]);
                }

                # 기업 제조 식품 유형 정보 처리
                $this->foodTypeUsageProc( $new_company_idx, $this->page_data['food_code'] );
                # 트랜잭션 종료
                $this->model->stopTransaction();

                # QRcode 데이터베이스에 적재
                $this->qrcode->createQRcode([
                    'purpose' => 'certify'
                    ,'qrcode_val' => $this->page_data['registration_no']
                    ,'file_name' => $this->page_data['registration_no']
                    ,'tb_name' => 't_company_info'             
                    ,'tb_key' => $new_company_idx
                    ,'company_idx' => $new_company_idx
                ]);
                

                movePage('replace', '저장되었습니다.', './company_list?page=1' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

                break;
            }
            case 'edit' : {

                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 필수값 체크
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
                $this->issetParams( $this->page_data, [
                    'company_idx'
                    ,'country_code'
                    ,'company_name'
                    ,'registration_no'
                    ,'ceo_name'
                    ,'company_tel'
                ]);
                
                # 트랜잭션 시작
                $this->model->runTransaction();

                # 기업 정보 삽입
                $query_result = $this->model->updateCompanyInfo([
                    'country_code' => $this->page_data['country_code']
                    ,'company_name' => $this->page_data['company_name']
                    ,'registration_no' => $this->page_data['registration_no']
                    ,'ceo_name' => $this->page_data['ceo_name']
                    ,'company_tel' => $this->page_data['company_tel']
                    ,'company_fax' => $this->page_data['company_fax']
                    ,'company_homepage' => $this->page_data['company_homepage']
                    ,'zip_code' => $this->page_data['zip_code']
                    ,'addr' => $this->page_data['addr']
                    ,'addr_detail' => $this->page_data['addr_detail']
                    ,'edit_idx' => getAccountInfo()['idx']
                    ,'edit_date' => 'NOW()'
                    ,'edit_ip' => $this->getIP()
                ] ," company_idx = '" . $this->page_data['company_idx']. "'" );



                if( empty( $this->page_data['edit_partner_idx'] ) == false ) {

                    # 기존 기업회원 파트너 자격 제거 
                    $query_result = $this->model->updateCompanyMember([
                        'partner' => 'N'
                        ,'edit_idx' => getAccountInfo()['idx']
                        ,'edit_date' => 'NOW()'
                        ,'edit_ip' => $this->getIP()
                    ] ," company_member_idx = '" . $this->page_data['current_partner_idx']. "'" );
                    
                    # edit_partner_idx 에 해당하는 기업회원 파트너 자격
                    $query_result = $this->model->updateCompanyMember([
                        'partner' => 'Y'
                        ,'edit_idx' => getAccountInfo()['idx']
                        ,'edit_date' => 'NOW()'
                        ,'edit_ip' => $this->getIP()
                    ] ," company_member_idx = '" . $this->page_data['edit_partner_idx']. "'" );
                    
                }

                # 기업 제조 식품 유형 정보 처리
                $this->foodTypeUsageProc(  $this->page_data['company_idx'], $this->page_data['food_code'] );

                # 트랜잭션 종료
               $this->model->stopTransaction();

                # QRcode 데이터베이스에 적재
                $this->qrcode->renewQRcode([
                    'purpose' => 'certify'
                    ,'qrcode_val' => $this->page_data['registration_no']
                    ,'file_name' => $this->page_data['registration_no']
                    ,'tb_name' => 't_company_info'             
                    ,'tb_key' => $this->page_data['company_idx']
                    ,'company_idx' => $this->page_data['company_idx']
                    ,'fild_key' => $this->page_data['currnet_qrcode_idx']
                ]);

                movePage('replace', '저장되었습니다.', './company_write?company_idx='. $this->page_data['company_idx']. '&mode=edit' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

                break;
            }
            case 'del' : {
                break;
            }
            default : {
                errorBack('잘못된 접근입니다.');
            }
        }
    }

    /**
     * 식품유형 정보를 처리한다.
     */
    private function foodTypeUsageProc( $arg_company_idx, $arg_data ){
        # company_idx 에 해당하는 기존 데이터 삭제처리
        $this->model->updateFoodUsage([
            'del_flag' => 'Y'
        ], " company_idx = '" . $arg_company_idx. "'"  );

        # 신규 insert 처리
        return $this->model->insertFoodUsage( $arg_company_idx, $arg_data );

    }

    /**
     * 회원 목록 페이지를 생성한다.
     */
    public function member_list(){

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $query_where = " AND del_flag='N' ";
        $query_sort = ' ORDER BY company_idx DESC ';
        $limit = " LIMIT ".(($this->page_data['page']-1)*$this->page_data['list_rows']).", ".$this->page_data['list_rows'];

        if( $this->page_data['sch_keyword'] ) {
            $query_where .= " AND ( 
                                    ( company_name LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    OR ( registration_no LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    OR ( ceo_name LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    OR ( member_name LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                            ) ";
        }

        if($this->page_data['sch_s_date']) {
            $query_where .= " AND reg_date >= '".$this->page_data['sch_s_date']." 00:00:00' ";
        }

		if($this->page_data['sch_e_date']) {
            $query_where .= " AND reg_date <= '".$this->page_data['sch_e_date']." 23:59:59' ";
        }


        # 리스트 정보요청
        $list_result = $this->model->getCompanyMembers([            
            'query_where' => $query_where
            ,'query_sort' => $query_sort
            ,'limit' => $limi
        ]);

        $this->paging->total_rs = $list_result['total_rs'];        
        $this->page_data['paging'] = $this->paging; 
        
        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
        $this->page_data['page_name'] = 'member';
        $this->page_data['contents_path'] = '/company/member_list.php';
        $this->page_data['list'] = $list_result['rows'];        
        $this->view( $this->page_data );
        
    }

    /**
     * 회원 정보 등록/수정 페이지를 생성한다.
     */
    public function member_write(){
        
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $query_result = [];

        # 국가 코드 요청
        $this->page_data['country_codes'] = $this->country_model->getCountryCodes([            
            'query_where' => " AND use_flag='Y' "
            ,'query_sort' => " ORDER BY country_name ASC "
        ])['rows'];
        
        # 기업정보 요청
        $company_result = $this->model->getCompanys([            
            'query_where' => " AND del_flag='N' "
            ,'query_sort' => " ORDER BY company_idx DESC "            
        ]);
        
        $this->page_data['company_list'] = $company_result['rows'];
        
        if( $this->page_data['mode'] == 'edit') {

            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
            # 필수값 체크
            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=            
            $this->issetParams( $this->page_data, ['company_member_idx']);
            
            $this->page_data['page_work'] = '수정';
            
            # 회원 정보
            $security_result = $this->model->getCompanyMember( " company_member_idx = '". $this->page_data['company_member_idx'] ."' " );

            if( count( $security_result['row'] ) > 0  ) {
                $this->page_data = array_merge( $this->page_data, $security_result['row'] );
            } else {
                errorBack('해당 게시물이 삭제되었거나 정상적인 접근 방법이 아닙니다.');
            }

            # 회사 정보 
            $company_result = $this->model->getCompanys([
                'query_where' => " AND company_idx='". $this->page_data['company_idx'] ."' "
            ]);

            if( count( $company_result['rows'][0] ) > 0  ) {
                $this->page_data = array_merge( $this->page_data, $company_result['rows'][0] );
            }
            
            # 계약 정보
            $contract_result = $this->contract_model->getContract( " company_idx='". $this->page_data['company_idx'] ."' " );

            if( $contract_result['num_rows'] > 0 ) {
                # $contract_result['row']['service_items'] 에 해당하는 서비스 항목을 호출한다.

                $service_items_arr = explode(',', $contract_result['row']['service_items'] );                
                $service_items = join( "','", $service_items_arr );
                
                $query_result = $this->service_model->getServiceItems(" item_code IN ( '". $service_items ."' ) ");
                
            }
            

        } else {

            $this->page_data['mode'] = 'ins';
            $this->page_data['page_work'] = '등록';

        }

        $this->page_data['service_items'] = $query_result;
		$this->page_data['mes_process'] = $this->getConfig()['mes_process'];


//echoBr(  $this->page_data ); exit;
        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
        $this->page_data['page_name'] = 'member';        
        $this->page_data['contents_path'] = '/company/member_write.php';
        
        
        $this->view( $this->page_data );

    }

    /**
     * 회원 정보 데이터를 처리한다.
     */
    public function member_proc(){

        # post 접근 체크
        postCheck();

        // echoBr( $this->page_data ); 

        $this->page_name = 'member';

        switch( $this->page_data['mode'] ) {

            case 'ins' : {
                
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 필수값 체크
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
                $this->issetParams( $this->page_data, [
                    'company_idx'
                    ,'country_code'
                    ,'phone_no'
                    ,'password'
                    ,'member_name'
                ]);
                
                # 회원 비밀번호 일치 확인
                if( $this->page_data['password'] !== $this->page_data['re_password'] ) {
                    errorBack('비밀번호 값과 재입력 값이 일치하지 않습니다.');
                }

                # 회원 아이디 중복 확인 
                $member_result = $this->model->getCompanyMember(" company_idx='". $this->page_data['company_idx'] ."' AND phone_no='". $this->page_data['phone_no'] ."' ");

                if( $member_result['num_rows'] > 0) {
                    errorBack('중복된 핸드폰 번호입니다.');
                }
                
                # approval_auth 문자열로 변환
                if( count( $this->page_data['approval_auth'] ) > 0 ){
                    $this->page_data['approval_auth'] = join(',', $this->page_data['approval_auth'] );
                }

                # work_auth 문자열로 변환
                if( count( $this->page_data['work_auth'] ) > 0 ){
                    $this->page_data['work_auth'] = join(',', $this->page_data['work_auth'] );
                }
				
				# menu_auth 문자열로 변환
                if( count( $this->page_data['menu_auth'] ) > 0 ){
                    $this->page_data['menu_auth'] = join(',', $this->page_data['menu_auth'] );
                }

                # 트랜잭션 시작
                $this->model->runTransaction();

                $query_result = $this->model->insertCompanyMember([
                    'company_idx ' => $this->page_data['company_idx']                    
                    ,'member_name' => $this->page_data['member_name']
                    ,'phone_no' => $this->page_data['phone_no']
                    ,'email' => $this->page_data['email']
                    ,'approval_auth' => $this->page_data['approval_auth']
                    ,'work_auth' => $this->page_data['work_auth']
					,'menu_auth' => $this->page_data['menu_auth']
                    ,'use_flag' => $this->page_data['use_flag']
                    ,'password' => hash_conv( $this->page_data['password'] )
                    ,'reg_idx' => getAccountInfo()['idx']
                    ,'reg_date' => 'NOW()'
                    ,'reg_ip' => $this->getIP()
                ]);
                
               
                # 트랜잭션 종료
                $this->model->stopTransaction();

                movePage('replace', '저장되었습니다.', './'. $this->page_name .'_list?page=1' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

                break;
            }
            case 'edit' : {

                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 필수값 체크
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
                $this->issetParams( $this->page_data, [
                    'company_member_idx'
                    ,'company_idx'
                    ,'country_code'
                    ,'phone_no'                    
                    ,'member_name'
                ]);
                
                # approval_auth 문자열로 변환
                if( count( $this->page_data['approval_auth'] ) > 0 ){
                    $this->page_data['approval_auth'] = join(',', $this->page_data['approval_auth'] );
                }

                # work_auth 문자열로 변환
                if( count( $this->page_data['work_auth'] ) > 0 ){
                    $this->page_data['work_auth'] = join(',', $this->page_data['work_auth'] );
                }

				# menu_auth 문자열로 변환
                if( count( $this->page_data['menu_auth'] ) > 0 ){
                    $this->page_data['menu_auth'] = join(',', $this->page_data['menu_auth'] );
                }

                # 트랜잭션 시작
                $this->model->runTransaction();

                $update_data = [
                    'company_idx ' => $this->page_data['company_idx']                    
                    ,'member_name' => $this->page_data['member_name']
                    ,'phone_no' => $this->page_data['phone_no']
                    ,'email' => $this->page_data['email']
                    ,'approval_auth' => $this->page_data['approval_auth']
                    ,'work_auth' => $this->page_data['work_auth']
					,'menu_auth' => $this->page_data['menu_auth']
                    ,'use_flag' => $this->page_data['use_flag']                    
                    ,'edit_idx' => getAccountInfo()['idx']
                    ,'edit_date' => 'NOW()'
                    ,'edit_ip' => $this->getIP()
                ];

                if( empty( $this->page_data['password'] ) == false ) {
                    
                    # 회원 비밀번호 일치 확인
                    if( $this->page_data['password'] !== $this->page_data['re_password'] ) {
                        errorBack('비밀번호 값과 재입력 값이 일치하지 않습니다.');
                    }
                    
                    $update_data['password'] = hash_conv( $this->page_data['password'] );
                }

                $this->model->updateCompanyMember( $update_data ," company_member_idx = '" . $this->page_data['company_member_idx']. "'" );
               
               
                # 트랜잭션 종료
                $this->model->stopTransaction();


                movePage('replace', '저장되었습니다.', './'. $this->page_name .'_write?company_member_idx='. $this->page_data['company_member_idx']. '&mode=edit' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

                break;
            }
            case 'del' : {
                break;
            }
            default : {
                errorBack('잘못된 접근입니다.');
            }
        }

    }

    /**
     * 회사 계약 정보의 서비스 항목 정보를 json 형태로 반환 한다.
     */
    public function get_company_contract() {

        if( empty( $this->page_data['company_idx'] ) == true ) {
            $result_array['state'] = 'fail';
            $result_array['msg'] = 'company_idx 누락되었습니다.';
            exit(json_encode( $result_array ));
            
        }

        # 서비스 항목을 반환 한다. 추후 정렬 순서 적용
        $contract_result = $this->contract_model->getContract( " company_idx='". $this->page_data['company_idx'] ."' " );
        
        if( $contract_result['num_rows'] > 0 ) {
            # $contract_result['row']['service_items'] 에 해당하는 서비스 항목을 호출한다.

            $service_items_arr = explode(',', $contract_result['row']['service_items'] );                
            $service_items = join( "','", $service_items_arr );
            
            $query_result = $this->service_model->getServiceItems(" item_code IN ( '". $service_items ."' ) ");
            
        }

        exit(json_encode( $query_result ));

    }
     

}

?>