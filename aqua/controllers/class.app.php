<?php

class app extends baseController {

    private $model;    
	private $page_data;
    private $paging;
    private $file_manager;

    function __construct() {
        
		#로그인 확인
        loginState();
        
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # model instance
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $this->paging = $this->new('pageHelper');
        $this->model = $this->new('appModel');
        $this->file_manager = $this->new('fileUploadHandler'); 
        
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
            , 'sch_use_flag'
        ]);

    }

    /**
     * 앱 목록
     */
    public function app_list(){
		
		
		#+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $query_where = " AND del_flag='N' ";
        $query_sort = ' ORDER BY app_idx DESC ';
        $limit = " LIMIT ".(($this->page_data['page']-1)*$this->page_data['list_rows']).", ".$this->page_data['list_rows'];


        if( $this->page_data['sch_keyword'] ) {
            $query_where .= " AND ( 
                                    ( app_name LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    OR ( description LIKE '%". $this->page_data['sch_keyword'] ."%' )                                     
                            ) ";
        }

        if($this->page_data['sch_s_date']) {
            $query_where .= " AND ( reg_date >= '".$this->page_data['sch_s_date']." 00:00:00' ) ";
        }

		if($this->page_data['sch_e_date']) {
            $query_where .= " AND ( reg_date <= '".$this->page_data['sch_e_date']." 23:59:59' ) ";
        }


        if($this->page_data['sch_use_flag']) {
            $query_where .= " AND ( use_flag = '".$this->page_data['sch_use_flag']."' ) ";
        }

        # 리스트 정보요청
        $list_result = $this->model->getApps([            
            'query_where' => $query_where
            ,'query_sort' => $query_sort
            ,'limit' => $limit
        ]);
        
        
        
        $this->page_data['list'] = $list_result['rows'];

        $this->paging->total_rs = $list_result['total_rs'];        
        $this->page_data['paging'] = $this->paging; 

        $this->page_data['use_top'] = true;
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true; 
		$this->page_data['page_name'] = 'app';
        $this->page_data['contents_path'] = '/app/app_list.php';

        $this->view( $this->page_data );        
        
    }

     
    /**
     * 앱 정보 등록/수정 화면 구성
     */
    public function app_write(){
		
		
		#+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=

		
		if( $this->page_data['mode'] == 'edit') {

            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
            # 필수값 체크
            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=            
            $this->issetParams( $this->page_data, ['app_idx']);

            # 앱 정보
            $app_result = $this->model->getApp( " app_idx = '". $this->page_data['app_idx'] ."' " );

            if( count( $app_result['row'] ) > 0  ) {
                $this->page_data = array_merge( $this->page_data, $app_result['row'] );
            } else {
                errorBack('해당 게시물이 삭제되었거나 정상적인 접근 방법이 아닙니다.');
            }

            # 파일 정보            
            $file_result = $this->file_manager->dbGetFile("
                tb_key = '". $this->page_data['app_idx'] ."'
                AND where_used = 'app_icon'
                AND tb_name = 't_app_Info'
            ");

            $this->page_data['file_idx'] = $file_result['row']['idx'];
            $this->page_data['file_path'] = $file_result['row']['path'];
            $this->page_data['file_server_name'] = $file_result['row']['server_name'];
            $this->page_data['file_origin_name'] = $file_result['row']['origin_name'];

            $this->page_data['page_work'] = '수정';

		} else {

			$this->page_data['mode'] = 'ins';
            $this->page_data['page_work'] = '등록';

		}

        $this->page_data['use_top'] = true;
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true; 
		$this->page_data['page_name'] = 'app';		
        $this->page_data['contents_path'] = '/app/app_write.php';

        $this->view( $this->page_data );        
        
    }


    /**
     * 앱 정보 데이터 처리
     */
    public function app_proc(){
        # post 접근 체크
        postCheck();

        // echoBr( $this->page_data ); exit;

        switch( $this->page_data['mode'] ) {

            case 'ins' : {
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 필수값 체크
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
                $this->issetParams( $this->page_data, [                                  
                    'app_name'           
                ]);
                
                $this->page_data['aos_release'] = (empty( $this->page_data['aos_release'] ) == false ? $this->page_data['aos_release'] : 'N');
                $this->page_data['ios_release'] = (empty( $this->page_data['ios_release'] ) == false ? $this->page_data['ios_release'] : 'N');
                # 트랜잭션 시작
                $this->model->runTransaction();

                $query_result = $this->model->insertApp([
                    'app_name ' => $this->page_data['app_name']                    
                    ,'description ' => $this->page_data['description']                    
                    ,'fcm_access_key ' => $this->page_data['fcm_access_key']
                    ,'aos_release ' => $this->page_data['aos_release']
                    ,'ios_release ' => $this->page_data['ios_release']
                    ,'aos_package' => $this->page_data['aos_package']                    
                    ,'aos_key_hash' => $this->page_data['aos_key_hash']
                    ,'aos_market_url' => $this->page_data['aos_market_url']
                    ,'aos_initial_date' => $this->page_data['aos_initial_date']
                    ,'ios_bundle_id' => $this->page_data['ios_bundle_id']
                    ,'iphone_appstore_id' => $this->page_data['iphone_appstore_id']
                    ,'iphone_market_url' => $this->page_data['iphone_market_url']
                    ,'ipad_appstore_id' => $this->page_data['ipad_appstore_id']
                    ,'ipad_market_url' => $this->page_data['ipad_market_url']
                    ,'ios_initial_date' => $this->page_data['ios_initial_date']
                    ,'reg_idx' => getAccountInfo()['idx']
                    ,'reg_ip' => $this->getIP()
                    ,'reg_date' => 'NOW()'
                ]);
                
                
                # 삽입 완료된 기본키를 가져온다.
                $new_app_idx = $query_result['return_data']['insert_id'];

                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 파일 업로드
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=  
                $this->file_manager->path = UPLOAD_PATH.'/app/icon';
                $this->file_manager->file_element = 'app_icon_file';
                $this->file_manager->table_data = [
                    'insert'=> [
                        'tb_name' => 't_app_Info'                        
                        ,'where_used' => 'app_icon'
                        ,'tb_key' => $new_app_idx
                    ]
                ];
                $this->file_manager->fileUpload();
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # // 파일 업로드
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+= 

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
                    'app_idx'
                    ,'app_name'   
                ]);

               
                # 트랜잭션 시작
                $this->model->runTransaction();

                $query_result = $this->model->updateApp([
                    'app_name ' => $this->page_data['app_name']                    
                    ,'description ' => $this->page_data['description']
					,'fcm_access_key ' => $this->page_data['fcm_access_key']
                    ,'aos_release ' => $this->page_data['aos_release']
                    ,'ios_release ' => $this->page_data['ios_release']
                    ,'aos_package' => $this->page_data['aos_package']                    
                    ,'aos_key_hash' => $this->page_data['aos_key_hash']
                    ,'aos_market_url' => $this->page_data['aos_market_url']
                    ,'aos_initial_date' => $this->page_data['aos_initial_date']
                    ,'ios_bundle_id' => $this->page_data['ios_bundle_id']
                    ,'iphone_appstore_id' => $this->page_data['iphone_appstore_id']
                    ,'iphone_market_url' => $this->page_data['iphone_market_url']
                    ,'ipad_appstore_id' => $this->page_data['ipad_appstore_id']
                    ,'ipad_market_url' => $this->page_data['ipad_market_url']
                    ,'ios_initial_date' => $this->page_data['ios_initial_date']
                    ,'edit_idx' => getAccountInfo()['idx']
                    ,'edit_ip' => $this->getIP()
                    ,'edit_date' => 'NOW()'
                ] ," app_idx = '" . $this->page_data['app_idx']. "'"  );

                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 파일 업로드
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=  
                $this->file_manager->path = UPLOAD_PATH.'/app/icon';
                $this->file_manager->file_element = 'app_icon_file';
                $this->file_manager->table_data = [
                    'insert'=> [
                        'tb_name' => 't_app_Info'                        
                        ,'where_used' => 'app_icon'
                        ,'tb_key' => $this->page_data['app_idx']
                    ]
                    , 'delete' => " idx='". $this->page_data['file_idx'] ."' "
                ];
                $this->file_manager->fileUpload();
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # // 파일 업로드
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+= 
               
               
                # 트랜잭션 종료
                $this->model->stopTransaction();


                movePage('replace', '저장되었습니다.', './'. $this->page_data['return_page'] .'_write?app_idx='. $this->page_data['app_idx']. '&mode=edit' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

                break;
            }

            case 'del' : {
                
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 필수값 체크
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
                $this->issetParams( $this->page_data, [                                  
                    'app_idx'                    
                ]);

                # 트랜잭션 시작
                $this->model->runTransaction();

                $this->model->updateApp( [
                    'del_flag' => 'Y'
                    ,'del_date' => 'NOW()'
                    ,'del_idx' => getAccountInfo()['idx']
                    ,'del_ip' => $this->getIP()
                ] ," app_idx = '" . $this->page_data['app_idx']. "'" );
               
               
                # 트랜잭션 종료
                $this->model->stopTransaction();

                movePage('replace', '삭제되었습니다.', './'. $this->page_data['return_page'] .'_list?page=1' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

                break;
            }

        }
    }


     /**
     * app 버전 관리 목록 생성
     */
    public function app_ver_list(){
		
		
		#+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $query_where = " AND del_flag='N' ";
        $query_sort = ' ORDER BY app_idx DESC ';
        $limit = " LIMIT ".(($this->page_data['page']-1)*$this->page_data['list_rows']).", ".$this->page_data['list_rows'];


        if( $this->page_data['sch_keyword'] ) {
            $query_where .= " AND ( 
                                    ( app_name LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    OR ( description LIKE '%". $this->page_data['sch_keyword'] ."%' )                                     
                            ) ";
        }

        if($this->page_data['sch_s_date']) {
            $query_where .= " AND ( reg_date >= '".$this->page_data['sch_s_date']." 00:00:00' ) ";
        }

		if($this->page_data['sch_e_date']) {
            $query_where .= " AND ( reg_date <= '".$this->page_data['sch_e_date']." 23:59:59' ) ";
        }


        if($this->page_data['sch_use_flag']) {
            $query_where .= " AND ( use_flag = '".$this->page_data['sch_use_flag']."' ) ";
        }

        # 리스트 정보요청
        $list_result = $this->model->getApps([            
            'query_where' => $query_where
            ,'query_sort' => $query_sort
            ,'limit' => $limit
        ]);
        
        
        
        $this->page_data['list'] = $list_result['rows'];

        $this->paging->total_rs = $list_result['total_rs'];        
        $this->page_data['paging'] = $this->paging; 

        $this->page_data['use_top'] = true;
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true; 
		$this->page_data['page_name'] = 'app_ver';
        $this->page_data['contents_path'] = '/app/app_ver_list.php';

        $this->view( $this->page_data );   
        
    }

	 /**
     * app 버전 관리 목록 생성
     */
    public function app_ver_history_list(){
		
        
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # 필수값 체크
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
        $this->issetParams( $this->page_data, [                                  
            'app_idx'                    
        ]);
        
		#+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $query_where = " AND del_flag='N' AND app_idx='". $this->page_data['app_idx'] ."' ";
        $query_sort = ' ORDER BY app_ver_idx DESC ';
        $limit = " LIMIT ".(($this->page_data['page']-1)*$this->page_data['list_rows']).", ".$this->page_data['list_rows'];


        if( $this->page_data['sch_keyword'] ) {
            $query_where .= " AND ( 
                                    ( version LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    OR ( description LIKE '%". $this->page_data['sch_keyword'] ."%' )                                     
                            ) ";
        }

        if($this->page_data['sch_s_date']) {
            $query_where .= " AND ( reg_date >= '".$this->page_data['sch_s_date']." 00:00:00' ) ";
        }

		if($this->page_data['sch_e_date']) {
            $query_where .= " AND ( reg_date <= '".$this->page_data['sch_e_date']." 23:59:59' ) ";
        }


        if($this->page_data['sch_use_flag']) {
            $query_where .= " AND ( use_flag = '".$this->page_data['sch_use_flag']."' ) ";
        }

        # 리스트 정보요청
        $list_result = $this->model->getVerHistorys([            
            'query_where' => $query_where
            ,'query_sort' => $query_sort
            ,'limit' => $limit
        ]);
        
        $this->page_data['params'] .= '&app_idx=' . $this->page_data['app_idx'];
        
        $this->page_data['list'] = $list_result['rows'];

        $this->paging->total_rs = $list_result['total_rs'];        
        $this->page_data['paging'] = $this->paging; 

        $this->page_data['use_top'] = true;
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true; 
		$this->page_data['page_name'] = 'app_ver_history';
        $this->page_data['contents_path'] = '/app/app_ver_history_list.php';

        $this->view( $this->page_data );   
        
    }

    /**
     * 앱 버전 작성 화면 구성
     */
    public function app_ver_history_write(){
		
		#+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # 필수값 체크
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
        $this->issetParams( $this->page_data, [                                  
            'app_idx'                    
        ]);

		#+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=

        $this->page_data['params'] .= '&app_idx=' . $this->page_data['app_idx'];

        
        $app_result = $this->model->getMaxVer( " app_idx = '". $this->page_data['app_idx'] ."' AND apply_flag='Y' " );
        $this->page_data['recently_ver'] = $app_result['row']['max_ver'];

		
		if( $this->page_data['mode'] == 'edit') {

            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
            # 필수값 체크
            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=            
            $this->issetParams( $this->page_data, ['app_ver_idx']);

            # 버전 정보
            $app_result = $this->model->getVerHistory( " app_ver_idx = '". $this->page_data['app_ver_idx'] ."' " );

            if( count( $app_result['row'] ) > 0  ) {
                $this->page_data = array_merge( $this->page_data, $app_result['row'] );
            } else {
                errorBack('해당 게시물이 삭제되었거나 정상적인 접근 방법이 아닙니다.');
            }

            # 파일 정보            
            $file_result = $this->file_manager->dbGetFile("
                tb_key = '". $this->page_data['app_ver_idx'] ."'
                AND where_used = 'app_apk'
                AND tb_name = 't_app_version_history'
            ");

            $this->page_data['file_idx'] = $file_result['row']['idx'];
            $this->page_data['file_path'] = $file_result['row']['path'];
            $this->page_data['file_server_name'] = $file_result['row']['server_name'];
            $this->page_data['file_origin_name'] = $file_result['row']['origin_name'];

            $this->page_data['page_work'] = '수정';

		} else {

			$this->page_data['mode'] = 'ins';
            $this->page_data['page_work'] = '등록';

            

		}

        $this->page_data['use_top'] = true;
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true; 
		$this->page_data['page_name'] = 'app_ver_history';		
        $this->page_data['contents_path'] = '/app/app_ver_history_write.php';

        $this->view( $this->page_data );        
        
    }

    /**
     * 앱 버전 데이터 처리
     */
    public function app_ver_history_proc(){

        # post 접근 체크
        postCheck();

        //echoBr( $_POST ); exit;
       // echoBr( $this->page_data ); exit;

        switch( $this->page_data['mode'] ) {

            case 'ins' : {
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 필수값 체크
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
                $this->issetParams( $this->page_data, [                                  
                    'app_idx'           
                    ,'version'           
                ]);


                # 트랜잭션 시작
                $this->model->runTransaction();

                $query_result = $this->model->insertVerHistory([
                    'app_idx ' => $this->page_data['app_idx']                    
                    ,'version ' => $this->page_data['version']                    
                    ,'description ' => $this->page_data['description']                    
                    ,'os ' => $this->page_data['os']
                    ,'apply_flag ' => $this->page_data['apply_flag']
                    ,'reg_idx' => getAccountInfo()['idx']
                    ,'reg_ip' => $this->getIP()
                    ,'reg_date' => 'NOW()'
                ]);
                
                
                # 버전 기본키를 가져온다.
                $new_app_idx = $query_result['return_data']['insert_id'];


                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 파일 업로드
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=  
                $this->file_manager->path = UPLOAD_PATH.'/app/apk';
                $this->file_manager->file_element = 'app_apk_file';
                $this->file_manager->table_data = [
                    'insert'=> [
                        'tb_name' => 't_app_version_history'                        
                        ,'where_used' => 'app_apk'
                        ,'tb_key' => $new_app_idx
                    ]
                ];
                $this->file_manager->fileUpload();
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # // 파일 업로드
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+= 

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
                    'app_idx'
                    ,'version'   
                    ,'app_ver_idx'   
                ]);

               
                # 트랜잭션 시작
                $this->model->runTransaction();

                if( $this->page_data['apply_flag'] == 'Y') {

                    $query_result = $this->model->updateVerHistory([
                        'apply_flag ' => 'N'
                    ] ," app_idx = '" . $this->page_data['app_idx']. "'"  );

                }

                $query_result = $this->model->updateVerHistory([
                    'version ' => $this->page_data['version']                    
                    ,'description ' => $this->page_data['description']                    
                    ,'os ' => $this->page_data['os']
                    ,'apply_flag ' => $this->page_data['apply_flag']
                    ,'edit_idx' => getAccountInfo()['idx']
                    ,'edit_ip' => $this->getIP()
                    ,'edit_date' => 'NOW()'
                ] ," app_ver_idx = '" . $this->page_data['app_ver_idx']. "'"  );

                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 파일 업로드
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=  
                $this->file_manager->path = UPLOAD_PATH.'/app/apk';
                $this->file_manager->file_element = 'app_apk_file';
                $this->file_manager->table_data = [
                    'insert'=> [
                        'tb_name' => 't_app_version_history'                        
                        ,'where_used' => 'app_apk'
                        ,'tb_key' => $this->page_data['app_ver_idx']
                    ]
                    , 'delete' => " idx='". $this->page_data['file_idx'] ."' "
                ];
                $this->file_manager->fileUpload();
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # // 파일 업로드
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+= 
               
               
                # 트랜잭션 종료
                $this->model->stopTransaction();


                movePage('replace', '저장되었습니다.', './'. $this->page_data['return_page'] .'_write?app_idx='. $this->page_data['app_idx']. '&app_ver_idx='.$this->page_data['app_ver_idx'].'&mode=edit' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

                break;
            }

            case 'del' : {
                
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 필수값 체크
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
                $this->issetParams( $this->page_data, [                                  
                    'app_ver_idx'                    
                ]);

                # 트랜잭션 시작
                $this->model->runTransaction();

                $this->model->updateVerHistory( [
                    'del_flag' => 'Y'
                    ,'del_date' => 'NOW()'
                    ,'del_idx' => getAccountInfo()['idx']
                    ,'del_ip' => $this->getIP()
                ] ," app_ver_idx = '" . $this->page_data['app_ver_idx']. "'" );
               
               
                # 트랜잭션 종료
                $this->model->stopTransaction();

                movePage('replace', '삭제되었습니다.', './'. $this->page_data['return_page'] .'_list?page=1' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

                break;
            }

        }

    }

	/**
     * app 버전 관리 목록 생성
     */
    public function app_push_log(){
		
		
		#+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $query_where = "";
        $query_sort = ' ORDER BY push_idx DESC ';
        $limit = " LIMIT ".(($this->page_data['page']-1)*$this->page_data['list_rows']).", ".$this->page_data['list_rows'];


        if( $this->page_data['sch_keyword'] ) {
            $query_where .= " AND ( 
                                    ( title LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    OR ( contents LIKE '%". $this->page_data['sch_keyword'] ."%' )                                     
                            ) ";
        }

        if($this->page_data['sch_s_date']) {
            $query_where .= " AND ( send_date >= '".$this->page_data['sch_s_date']." 00:00:00' ) ";
        }

		if($this->page_data['sch_e_date']) {
            $query_where .= " AND ( send_date <= '".$this->page_data['sch_e_date']." 23:59:59' ) ";
        }

        # 리스트 정보요청
        $list_result = $this->model->getPushLog([            
            'query_where' => $query_where
            ,'query_sort' => $query_sort
            ,'limit' => $limit
        ]);
        
        
        
        $this->page_data['list'] = $list_result['rows'];

        $this->paging->total_rs = $list_result['total_rs'];        
        $this->page_data['paging'] = $this->paging; 

        $this->page_data['use_top'] = true;
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true; 
		$this->page_data['page_name'] = 'app_ver';
        $this->page_data['contents_path'] = '/app/app_push_log.php';

        $this->view( $this->page_data );   
        
    }
    
	public function test_push_send(){
		
		$push = $this->new('pushHandler');
		$push->company_code = 14;
		$push->push_title = '관리자 발송 테스트';
		$push->message = '관리자 사이트에서 발송 테스트 입니다.';
		$push->storageLogWarningSend();


		movePage('replace', '발송되었습니다.', './app_push_log?page=1' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

	}

	

}

?>