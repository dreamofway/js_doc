<?php

class security extends baseController {

    private $model;
    private $service_model;
    private $company_model;
    private $paging;
    private $page_name;
    private $file_manager;

    function __construct() {
        
        #로그인 확인
        loginState();
        
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # model instance
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $this->paging = $this->new('pageHelper');
        $this->model = $this->new('securityModel');         
        $this->company_model = $this->new('companyModel'); 
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
            , 'sch_s_date'
            , 'sch_e_date'
        ]);

        $this->page_name = 'security';

    }

    /**
     * 정보보호관리 목록을 생성한다.
     */
    public function security_list(){        

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $query_where = "";
        $query_sort = ' ORDER BY security_idx DESC ';
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
            $query_where .= " AND ( start_date >= '".$this->page_data['sch_s_date']." 00:00:00' ) ";
        }

		if($this->page_data['sch_e_date']) {
            $query_where .= " AND ( end_date <= '".$this->page_data['sch_e_date']." 23:59:59' ) ";
        }

        # 리스트 정보요청
        $list_result = $this->model->getSecuritys([            
            'query_where' => $query_where
            ,'query_sort' => $query_sort
            ,'limit' => $limi
        ]);

        $this->paging->total_rs = $list_result['total_rs'];        
        $this->page_data['paging'] = $this->paging; 
        
        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
        $this->page_data['page_name'] = $this->page_name;
        $this->page_data['contents_path'] = '/security/'. $this->page_name .'_list.php';
        $this->page_data['list'] = $list_result['rows'];        
        $this->view( $this->page_data );

    }

    public function security_write() {

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        
        # 기업정보 요청
        $company_result = $this->company_model->getCompanys([            
            'query_where' => " AND del_flag='N' "
            ,'query_sort' => " ORDER BY company_idx DESC "            
        ]);
        
        $this->page_data['company_list'] = $company_result['rows'];


        if( $this->page_data['mode'] == 'edit') {

            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
            # 필수값 체크
            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=            
            $this->issetParams( $this->page_data, ['security_idx']);
            
            $this->page_data['page_work'] = '수정';
            
            # 보안 정보
            $security_result = $this->model->getSecurity( " security_idx = '". $this->page_data['security_idx'] ."' " );

            if( count( $security_result['row'] ) > 0  ) {
                $this->page_data = array_merge( $this->page_data, $security_result['row'] );
            } else {
                errorBack('해당 게시물이 삭제되었거나 정상적인 접근 방법이 아닙니다.');
            }
            
            # 회사 정보 
            $company_result = $this->company_model->getCompanys([
                'query_where' => " AND company_idx='". $this->page_data['company_idx'] ."' "
            ]);

            if( count( $company_result['rows'][0] ) > 0  ) {
                $this->page_data = array_merge( $this->page_data, $company_result['rows'][0] );
            }

            # 파일 정보            
            $file_result = $this->file_manager->dbGetFile("
                tb_key = '". $this->page_data['security_idx'] ."'
                AND where_used = 'security_file'
                AND tb_name = 't_security'
            ");

            $this->page_data['file_idx'] = $file_result['row']['idx'];
            $this->page_data['file_path'] = $file_result['row']['path'];
            $this->page_data['file_server_name'] = $file_result['row']['server_name'];
            $this->page_data['file_origin_name'] = $file_result['row']['origin_name'];
            
        } else {

            $this->page_data['mode'] = 'ins';
            $this->page_data['page_work'] = '등록';

        }

        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
        $this->page_data['page_name'] = $this->page_name;        
        $this->page_data['contents_path'] = '/security/'. $this->page_name .'_write.php';
        
        
        $this->view( $this->page_data );
        
    }

    /**
     * 기업정보 데이터를 처리한다.
     */
    public function security_proc(){

        # post 접근 체크
        postCheck();

        // echoBr( $this->page_data );

        switch( $this->page_data['mode'] ) {

            case 'ins' : {
                
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 필수값 체크
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
                $this->issetParams( $this->page_data, [
                    'company_idx'
                    ,'write_date'
                    ,'start_date'
                    ,'end_date'
                ]);
    

                # 트랜잭션 시작
                $this->model->runTransaction();

                # 기업 정보 삽입
                $query_result = $this->model->insetSecurity([
                    'write_date' => $this->page_data['write_date']
                    ,'company_idx' => $this->page_data['company_idx']
                    ,'start_date' => $this->page_data['start_date']
                    ,'end_date' => $this->page_data['end_date'] . ' 23:59:59'                                 
                    ,'reg_idx' => getAccountInfo()['idx']
                    ,'reg_date' => 'NOW()'
                    ,'reg_ip' => $this->getIP()
                ]);
                
                # 기업정보 삽입 완료된 기본키를 가져온다.
                $new_contract_idx = $query_result['return_data']['insert_id'];
                
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 파일 업로드
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=  
                $this->file_manager->path = UPLOAD_COMPANY_SECURITY_PATH;
                $this->file_manager->file_element = 'contract_file';
                $this->file_manager->table_data = [
                    'insert'=> [
                        'tb_name' => 't_security'                        
                        ,'where_used' => 'security_file'
                        ,'tb_key' => $new_contract_idx
                    ]
                ];
                $this->file_manager->fileUpload();
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # // 파일 업로드
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=  


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
                    'security_idx'
                    ,'company_idx'
                    ,'write_date'
                    ,'start_date'
                    ,'end_date'
                ]);
                
                # 트랜잭션 시작
                $this->model->runTransaction();

                # 기업 정보 삽입
                $query_result = $this->model->updateSecurity([
                    'write_date' => $this->page_data['write_date']
                    ,'company_idx' => $this->page_data['company_idx']
                    ,'start_date' => $this->page_data['start_date']
                    ,'end_date' => $this->page_data['end_date'] . ' 23:59:59'                    
                    ,'edit_idx' => getAccountInfo()['idx']
                    ,'edit_date' => 'NOW()'
                    ,'edit_ip' => $this->getIP()
                ]," security_idx = '" . $this->page_data['security_idx']. "'" );
                                
                
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 파일 업로드
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=  
                $this->file_manager->path = UPLOAD_COMPANY_SECURITY_PATH;
                $this->file_manager->file_element = 'contract_file';
                $this->file_manager->table_data = [
                    'insert'=> [
                        'tb_name' => 't_security'                        
                        ,'where_used' => 'security_file'
                        ,'tb_key' => $this->page_data['security_idx']
                    ]
                    , 'delete' => " idx='". $this->page_data['file_idx'] ."' "
                ];
                $this->file_manager->fileUpload();
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # // 파일 업로드
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=  


                # 트랜잭션 종료
                $this->model->stopTransaction();


                movePage('replace', '저장되었습니다.', './'. $this->page_name .'_write?security_idx='. $this->page_data['security_idx']. '&mode=edit' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

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