<?php

class doc extends baseController {

    private $model;
    private $model_service;
	private $page_data;
    private $paging;

    function __construct() {
        
		#로그인 확인
        //loginState();
        
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # model instance
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $this->paging = $this->new('pageHelper');
        $this->model = $this->new('docModel');
        $this->model_service = $this->new('serviecModel');        

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # GET parameters
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $this->page_data = $this->paging->getParameters();
       
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET params
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $this->page_data['params'] = $this->paging->setParams([
            'top_code'
            , 'left_code'
            , 'list_rows'
            , 'sch_type'
            , 'sch_service'
            , 'sch_keyword'
            , 'sch_item_code'
            , 'sch_use_flag'
        ]);

    }

    /**
     * HACCP 문서관리
     */
    public function haccp_form_list(){
		
		
		#+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $query_where = " AND del_flag='N' AND doc_type='HACCP'  ";
        $query_sort = ' ORDER BY reg_date DESC ';
        $limit = " LIMIT ".(($this->page_data['page']-1)*$this->page_data['list_rows']).", ".$this->page_data['list_rows'];


        if( $this->page_data['sch_keyword'] ) {
            $query_where .= " AND ( 
                                    ( doc_title LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    OR ( doc_tag LIKE '%". $this->page_data['sch_keyword'] ."%' )                                     
                            ) ";
        }

        if($this->page_data['sch_s_date']) {
            $query_where .= " AND ( reg_date >= '".$this->page_data['sch_s_date']." 00:00:00' ) ";
        }

		if($this->page_data['sch_e_date']) {
            $query_where .= " AND ( reg_date <= '".$this->page_data['sch_e_date']." 23:59:59' ) ";
        }

        if( $this->page_data['sch_item_code'] ) {            
            $query_where .= " AND ( item_code = '".$this->page_data['sch_item_code']."' ) ";
        }

        if($this->page_data['sch_use_flag']) {
            $query_where .= " AND ( use_flag = '".$this->page_data['sch_use_flag']."' ) ";
        }

        # 리스트 정보요청
        $list_result = $this->model->getDocForms([            
            'query_where' => $query_where
            ,'query_sort' => $query_sort
            ,'limit' => $limit
        ]);
        
         # 서비스 정보요청
         $query_result = $this->model_service->getServiceItems(" item_group = 'SI001' AND depth='2' AND use_flag='Y' " );

        
        $this->page_data['doc_items'] = $query_result;
        
        $this->page_data['list'] = $list_result['rows'];

        $this->paging->total_rs = $list_result['total_rs'];        
        $this->page_data['paging'] = $this->paging; 

        $this->page_data['use_top'] = true;
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true; 
		$this->page_data['page_name'] = 'haccp_form';
        $this->page_data['contents_path'] = '/doc/haccp_form_list.php';

        $this->view( $this->page_data );        
        
    }

	 /**
     * HACCP 문서관리
     */
    public function haccp_form_write(){
		
		
		#+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=

        # 서비스 정보요청
        $query_result = $this->model_service->getServiceItems(" item_group = 'SI001' AND depth='2' AND use_flag='Y' " );
        
        $this->page_data['doc_items'] = $query_result;

		if( $this->page_data['mode'] == 'edit') {

            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
            # 필수값 체크
            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=            
            $this->issetParams( $this->page_data, ['doc_idx']);

            # 문서 정보
            $doc_result = $this->model->getDocForm( " doc_idx = '". $this->page_data['doc_idx'] ."' " );

            if( count( $doc_result['row'] ) > 0  ) {
                $this->page_data = array_merge( $this->page_data, $doc_result['row'] );
            } else {
                errorBack('해당 게시물이 삭제되었거나 정상적인 접근 방법이 아닙니다.');
            }

            $this->page_data['page_work'] = '수정';

		} else {

			$this->page_data['mode'] = 'ins';
            $this->page_data['page_work'] = '등록';
            

		}

        $this->page_data['use_top'] = true;
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true; 
		$this->page_data['page_name'] = 'haccp_form';
		$this->page_data['doc_type'] = 'HACCP';
        $this->page_data['contents_path'] = '/doc/doc_form_write.php';

        $this->view( $this->page_data );        
        
    }



	/**
     * 일반 문서관리
     */
    public function general_form_list(){

		$set_info['idx'] = 99;
		$set_info['country'] = 'KR';
		$set_info['code'] = 'KR99920';
		$set_info['name'] = '테스터';
		$set_info['phone_number'] = '';
		$set_info['email'] = '';
		
		# 세션등록처리
		setAccountSession( $set_info );


//echoBr( getAccountInfo() ); exit;
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $query_where = " AND del_flag='N' AND doc_type='MES' ";
        $query_sort = ' ORDER BY reg_date DESC ';
        $limit = " LIMIT ".(($this->page_data['page']-1)*$this->page_data['list_rows']).", ".$this->page_data['list_rows'];


        if( $this->page_data['sch_keyword'] ) {
            $query_where .= " AND ( 
                                    ( doc_title LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    OR ( doc_tag LIKE '%". $this->page_data['sch_keyword'] ."%' )                                     
                            ) ";
        }

        if($this->page_data['sch_s_date']) {
            $query_where .= " AND ( reg_date >= '".$this->page_data['sch_s_date']." 00:00:00' ) ";
        }

		if($this->page_data['sch_e_date']) {
            $query_where .= " AND ( reg_date <= '".$this->page_data['sch_e_date']." 23:59:59' ) ";
        }

        if( $this->page_data['sch_item_code'] ) {            
            $query_where .= " AND ( item_code = '".$this->page_data['sch_item_code']."' ) ";
        }

        if($this->page_data['sch_use_flag']) {
            $query_where .= " AND ( use_flag = '".$this->page_data['sch_use_flag']."' ) ";
        }

        # 리스트 정보요청
        $list_result = $this->model->getDocForms([            
            'query_where' => $query_where
            ,'query_sort' => $query_sort
            ,'limit' => $limit
        ]);
        
         # 서비스 정보요청
         $query_result = $this->model_service->getServiceItems(" item_group = 'SI002' AND depth='2' AND use_flag='Y' " );

        
        $this->page_data['doc_items'] = $query_result;
        
        $this->page_data['list'] = $list_result['rows'];

        $this->paging->total_rs = $list_result['total_rs'];        
        $this->page_data['paging'] = $this->paging; 

        $this->page_data['use_top'] = true;
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
		$this->page_data['page_name'] = 'general_form';
        $this->page_data['contents_path'] = '/doc/general_form_list.php';

        $this->view( $this->page_data );        
        
    }

	 /**
     * 일반 문서관리
     */
    public function general_form_write(){
		
		
		#+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=

        # 서비스 정보요청
        $query_result = $this->model_service->getServiceItems(" item_group = 'SI002' AND depth='2' AND use_flag='Y' " );
        
        $this->page_data['doc_items'] = $query_result;

		if( $this->page_data['mode'] == 'edit') {

            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
            # 필수값 체크
            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=            
            $this->issetParams( $this->page_data, ['doc_idx']);

            # 문서 정보
            $doc_result = $this->model->getDocForm( " doc_idx = '". $this->page_data['doc_idx'] ."' " );

            if( count( $doc_result['row'] ) > 0  ) {
                $this->page_data = array_merge( $this->page_data, $doc_result['row'] );
            } else {
                errorBack('해당 게시물이 삭제되었거나 정상적인 접근 방법이 아닙니다.');
            }

            $this->page_data['page_work'] = '수정';

		} else {

			$this->page_data['mode'] = 'ins';
            $this->page_data['page_work'] = '등록';
            

		}

        $this->page_data['use_top'] = true;
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true; 		
		$this->page_data['doc_type'] = 'MES';
		$this->page_data['page_name'] = 'general_form';
        $this->page_data['contents_path'] = '/doc/doc_form_write.php';

        $this->view( $this->page_data );        
        
    }

    /**
     * 문서 form 데이터 처리
     */
    public function doc_form_proc(){
        # post 접근 체크
        postCheck();

        // echoBr( $this->page_data ); exit;

        switch( $this->page_data['mode'] ) {

            case 'ins' : {
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 필수값 체크
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
                $this->issetParams( $this->page_data, [                                  
                    'doc_type'
                    ,'item_code'
                    ,'doc_title'
                    ,'doc_data'                    
                ]);

                # 트랜잭션 시작
                $this->model->runTransaction();

                $query_result = $this->model->insertDocForm([
                    'doc_type ' => $this->page_data['doc_type']                    
                    ,'country_code ' => $this->page_data['country_code']                    
                    ,'item_code ' => $this->page_data['item_code']                    
                    ,'doc_title' => $this->page_data['doc_title']                    
                    ,'doc_table_style_data' => $this->page_data['doc_table_style_data']
                    ,'doc_data' => $this->page_data['doc_data']
                    ,'use_flag' => $this->page_data['use_flag']
                    ,'reg_idx' => getAccountInfo()['idx']
                    ,'reg_date' => 'NOW()'
                ]);
                
               
                # 트랜잭션 종료
                $this->model->stopTransaction();

                movePage('replace', '저장되었습니다.', './'. $this->page_data['return_page'] .'_list?page=1' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );


                break;
            }

            case 'edit' : {

                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 필수값 체크
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
                $this->issetParams( $this->page_data, [                                  
                    'doc_idx'
                    ,'doc_type'
                    ,'item_code'
                    ,'doc_title'
                    ,'doc_data'                    
                ]);

               
                # 트랜잭션 시작
                $this->model->runTransaction();

                $update_data = [                              
                    'doc_type' => $this->page_data['doc_type']
                    ,'country_code ' => $this->page_data['country_code']         
                    ,'item_code' => $this->page_data['item_code']                    
                    ,'doc_title' => $this->page_data['doc_title']
                    ,'doc_table_style_data' => $this->page_data['doc_table_style_data']
                    ,'doc_data' => $this->page_data['doc_data']
                    ,'use_flag' => $this->page_data['use_flag']
                    ,'edit_idx' => getAccountInfo()['idx']                    
                    ,'edit_date' => 'NOW()'
                ];

                $this->model->updateDocForm( $update_data ," doc_idx = '" . $this->page_data['doc_idx']. "'" );
               
               
                # 트랜잭션 종료
                $this->model->stopTransaction();


                movePage('replace', '저장되었습니다.', './'. $this->page_data['return_page'] .'_write?doc_idx='. $this->page_data['doc_idx']. '&mode=edit' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

                break;
            }

            case 'del' : {
                
                # 트랜잭션 시작
                $this->model->runTransaction();

                $this->model->updateDocForm( [
                    'del_flag' => 'Y'
                    ,'del_date' => 'NOW()'
                    ,'del_idx' => getAccountInfo()['idx']
                    ,'del_ip' => $this->getIP()
                ] ," doc_idx = '" . $this->page_data['doc_idx']. "'" );
               
               
                # 트랜잭션 종료
                $this->model->stopTransaction();
                

                movePage('replace', '삭제되었습니다.', './'. $this->page_data['return_page'] .'_list?page=1' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

                break;
            }

        }

    }


    /**
     * HACCP 문서관리
     */
    public function company_doc_list(){
		
		#+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $query_where = " AND del_flag='N' ";
        $query_sort = ' ORDER BY reg_date DESC ';
        $limit = " LIMIT ".(($this->page_data['page']-1)*$this->page_data['list_rows']).", ".$this->page_data['list_rows'];


        if( $this->page_data['sch_keyword'] ) {
            $query_where .= " AND ( 
                                    ( doc_title LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    OR ( company_name LIKE '%". $this->page_data['sch_keyword'] ."%' )                                     
                            ) ";
        }

        if($this->page_data['sch_s_date']) {
            $query_where .= " AND ( reg_date >= '".$this->page_data['sch_s_date']." 00:00:00' ) ";
        }

		if($this->page_data['sch_e_date']) {
            $query_where .= " AND ( reg_date <= '".$this->page_data['sch_e_date']." 23:59:59' ) ";
        }

        if( $this->page_data['sch_item_code'] ) {            
            $query_where .= " AND ( item_code = '".$this->page_data['sch_item_code']."' ) ";
        }

        if($this->page_data['sch_use_flag']) {
            $query_where .= " AND ( use_flag = '".$this->page_data['sch_use_flag']."' ) ";
        }

        # 리스트 정보요청
        $list_result = $this->model->getDocs([            
            'query_where' => $query_where
            ,'query_sort' => $query_sort
            ,'limit' => $limit
        ]);
        
         # 서비스 정보요청
         $query_result = $this->model_service->getServiceItems(" ( (service_code = 'SV001') OR ( item_group = 'SV002' ) ) AND depth='2' AND use_flag='Y' " );
        
        $this->page_data['doc_items'] = $query_result;
        
        $this->page_data['list'] = $list_result['rows'];

        $this->paging->total_rs = $list_result['total_rs'];        
        $this->page_data['paging'] = $this->paging; 

        $this->page_data['use_top'] = true;
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true; 
		$this->page_data['page_name'] = 'company_doc';
        $this->page_data['contents_path'] = '/doc/company_doc_list.php';

        $this->view( $this->page_data );        
        
    }

	 /**
     * HACCP 문서관리
     */
    public function company_doc_write(){
		
		
		#+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=

		
		if( $this->page_data['mode'] == 'edit') {

            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
            # 필수값 체크
            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=            
            $this->issetParams( $this->page_data, ['doc_usage_idx']);

            # 문서 정보
            $doc_result = $this->model->getDoc( " doc_usage_idx = '". $this->page_data['doc_usage_idx'] ."' " );

            if( count( $doc_result['row'] ) > 0  ) {
                $this->page_data = array_merge( $this->page_data, $doc_result['row'] );
            } else {
                errorBack('해당 게시물이 삭제되었거나 정상적인 접근 방법이 아닙니다.');
            }

            $this->page_data['page_work'] = '수정';

		} else {

			$this->page_data['mode'] = 'ins';
            $this->page_data['page_work'] = '등록';

		}

        $this->page_data['use_top'] = true;
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true; 
		$this->page_data['page_name'] = 'company_doc';		
        $this->page_data['contents_path'] = '/doc/company_doc_write.php';

        $this->view( $this->page_data );        
        
    }


    

    /**
     * 가업 문서 데이터 처리
     */
    public function company_doc_proc(){
        # post 접근 체크
        postCheck();

        // echoBr( $this->page_data ); exit;

        switch( $this->page_data['mode'] ) {

            case 'edit' : {

                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 필수값 체크
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
                $this->issetParams( $this->page_data, [                                  
                    'doc_usage_idx'
                    ,'doc_title'
                    ,'doc_table_style_data'
                    ,'doc_data'                    
                ]);

               
                # 트랜잭션 시작
                $this->model->runTransaction();

                $update_data = [                              
                    'doc_type' => $this->page_data['doc_type']
                    ,'doc_title' => $this->page_data['doc_title']
                    ,'doc_table_style_data' => $this->page_data['doc_table_style_data']
                    ,'doc_data' => $this->page_data['doc_data']
                    ,'use_flag' => $this->page_data['use_flag']
                    ,'edit_idx' => getAccountInfo()['idx']         
                    ,'edit_ip' => $this->getIP()           
                    ,'edit_date' => 'NOW()'
                ];

                $this->model->updateDoc( $update_data ," doc_usage_idx = '" . $this->page_data['doc_usage_idx']. "'" );
               
               
                # 트랜잭션 종료
                $this->model->stopTransaction();


                movePage('replace', '저장되었습니다.', './'. $this->page_data['return_page'] .'_write?doc_usage_idx='. $this->page_data['doc_usage_idx']. '&mode=edit' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

                break;
            }

            case 'del' : {

                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 필수값 체크
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
                $this->issetParams( $this->page_data, [                                  
                    'doc_usage_idx'
                ]);
                
                # 트랜잭션 시작
                $this->model->runTransaction();

                $this->model->updateDoc( [
                    'del_flag' => 'Y'
                    ,'del_date' => 'NOW()'
                    ,'del_idx' => getAccountInfo()['idx']
                    ,'del_ip' => $this->getIP()
                ] ," doc_usage_idx = '" . $this->page_data['doc_usage_idx']. "'" );
               
               
                # 트랜잭션 종료
                $this->model->stopTransaction();
                

                movePage('replace', '삭제되었습니다.', './'. $this->page_data['return_page'] .'_list?page=1' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

                break;
            }

        }

    }

	
    /**
     * 문서 양식 정보를 json 으로 반환한다.
     */
    public function getDocFormListJson(){

        $result_array['state'] = 'fail';

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=

        # 리스트 정보요청
        $list_result = $this->model->getDocForm( " del_flag='N' AND doc_type='". $this->page_data['doc_type'] ."' AND item_code='". $this->page_data['item_code'] ."'  ORDER BY doc_title DESC " );

        exit(json_encode( $list_result['rows'] ));


    }



    

}

?>