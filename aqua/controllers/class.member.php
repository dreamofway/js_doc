<?php

class member extends baseController {

    private $model;
    private $country_model;
    private $page_data;
    private $paging;
	private $page_name;
    
    function __construct() {

        #로그인 확인
        loginState();
        
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # model instance
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $this->paging = $this->new('pageHelper');
        $this->model = $this->new('memberModel');
		$this->country_model = $this->new('countryModel'); # 국가코드 모델 인스턴스
        $this->page_name = 'member';
	
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # GET parameters
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $this->page_data = $this->paging->getParameters();
       
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET params
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $this->page_data['params'] = $this->paging->setParams(['top_code', 'left_code', 'list_rows', 'sch_type', 'sch_service', 'sch_keyword']);

    }

    /**
     * 서비스 세부항목 페이지를 구성한다.
     */
    public function member_list(){

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=

		$query_where = " ";
        $query_sort = ' ORDER BY join_date DESC ';
        $limit = " LIMIT ".(($this->page_data['page']-1)*$this->page_data['list_rows']).", ".$this->page_data['list_rows'];


        if( $this->page_data['sch_keyword'] ) {
            $query_where .= " AND ( 
                                    ( name LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    OR ( phone_number LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    OR ( email LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                            ) ";
        }

        if($this->page_data['sch_s_date']) {
            $query_where .= " AND ( join_date >= '".$this->page_data['sch_s_date']." 00:00:00' ) ";
        }

		if($this->page_data['sch_e_date']) {
            $query_where .= " AND ( join_date <= '".$this->page_data['sch_e_date']." 23:59:59' ) ";
        }

        # 리스트 정보요청
        $list_result = $this->model->getMembers([            
            'query_where' => $query_where
            ,'query_sort' => $query_sort
            ,'limit' => $limit
        ]);
        

		$this->paging->total_rs = $list_result['total_rs'];        
        $this->page_data['paging'] = $this->paging; 

        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
        $this->page_data['page_name'] = $this->page_name;
        $this->page_data['contents_path'] = '/member/'. $this->page_name .'_list.php';
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

        if( $this->page_data['mode'] == 'edit') {

            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
            # 필수값 체크
            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=            
            $this->issetParams( $this->page_data, ['member_idx']);
            
            $this->page_data['page_work'] = '수정';
            
            # 회원 정보
            $security_result = $this->model->getMember( " member_idx = '". $this->page_data['member_idx'] ."' " );

            if( count( $security_result['row'] ) > 0  ) {
                $this->page_data = array_merge( $this->page_data, $security_result['row'] );
            } else {
                errorBack('해당 게시물이 삭제되었거나 정상적인 접근 방법이 아닙니다.');
            }

        } else {

            $this->page_data['mode'] = 'ins';
            $this->page_data['page_work'] = '등록';

        }

        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
        $this->page_data['page_name'] = $this->page_name;
        $this->page_data['contents_path'] =  '/member/'. $this->page_name .'_write.php';
        
        
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
                    'join_date'
                    ,'phone_no'
                    ,'country_code'
                    ,'password'
                    ,'name'
                ]);
                
                # 회원 비밀번호 일치 확인
                if( $this->page_data['password'] !== $this->page_data['re_password'] ) {
                    errorBack('비밀번호 값과 재입력 값이 일치하지 않습니다.');
                }

                # 회원 아이디 중복 확인 
                $member_result = $this->model->getMember(" 
                    phone_no='". $this->page_data['phone_no'] ."'
                ");

                if( $member_result['num_rows'] > 0) {
                    errorBack('중복된 핸드폰 번호입니다.');
                }
                
				# 회원 코드 생성
				$this->page_data['member_code'] = $this->makeNewCode( 
					$this->page_data['country_code']
					, $this->model->getMaxCode( $this->page_data['country_code'],  substr( $this->page_data['join_date'], 2,2) ) 
				, 3 ) . substr( $this->page_data['join_date'], 2,2);


                # 트랜잭션 시작
                $this->model->runTransaction();


                $query_result = $this->model->insertMember([
                    'member_code ' => $this->page_data['member_code']                    
                    ,'country_code ' => $this->page_data['country_code']                    
                    ,'name' => $this->page_data['name']
                    ,'join_date' => $this->page_data['join_date']
                    ,'phone_no' => $this->page_data['phone_no']
                    ,'email' => $this->page_data['email']
                    ,'use_flag' => $this->page_data['use_flag']
                    ,'password' => hash_conv( $this->page_data['password'] )
                    ,'reg_date' => 'NOW()'
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
                    'member_idx'                    
                    ,'phone_no'
                    ,'name'
                ]);
               
                # 트랜잭션 시작
                $this->model->runTransaction();

                $update_data = [                              
                    'name' => $this->page_data['name']
                    ,'phone_no' => $this->page_data['phone_no']
                    ,'email' => $this->page_data['email']
					,'join_date' => $this->page_data['join_date']
                    ,'use_flag' => $this->page_data['use_flag']                    
                    ,'edit_date' => 'NOW()'
                ];

                if( empty( $this->page_data['password'] ) == false ) {
                    
                    # 회원 비밀번호 일치 확인
                    if( $this->page_data['password'] !== $this->page_data['re_password'] ) {
                        errorBack('비밀번호 값과 재입력 값이 일치하지 않습니다.');
                    }
                    
                    $update_data['password'] = hash_conv( $this->page_data['password'] );
                }

                $this->model->updateMember( $update_data ," member_idx = '" . $this->page_data['member_idx']. "'" );
               
               
                # 트랜잭션 종료
                $this->model->stopTransaction();


                movePage('replace', '저장되었습니다.', './'. $this->page_name .'_write?member_idx='. $this->page_data['member_idx']. '&mode=edit' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

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

}

?>