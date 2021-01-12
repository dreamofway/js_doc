<?php

class contract extends baseController {

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
        $this->model = $this->new('contractModel'); 
        $this->service_model = $this->new('serviecModel'); 
        $this->company_model = $this->new('companyModel'); 
        $this->doc_model = $this->new('docModel'); 
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

        $this->page_name = 'contract';

    }

    /**
     * 계약 관계 기업 목록을 생성한다.
     */
    public function contract_list(){        

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $query_where = "";
        $query_sort = ' ORDER BY idx DESC ';
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

        preg_match_all( '/sch_service_[0-9]/i', $this->page_data['params'], $matches);

        if( count( $matches[0] ) > 0 ) {
            $multi_col_where = '';

            foreach( $matches[0] AS $idx=>$item ){                                
                
                if( $idx == 0 ){
                    $multi_col_where = " AND ( ( service_code = '".$this->page_data[ $item ]."' ) ";
                } else {
                    $multi_col_where .= " OR ( service_code = '".$this->page_data[ $item ]."' ) ";
                }
            }

            $query_where .= $multi_col_where . ') ';
        } 

        // echoBr( $query_where ); exit;

        # 서비스 정보 요청
        $service_result = $this->service_model->getServices([            
            'query_where' => " AND use_flag='Y' "
            ,'query_sort' => " ORDER BY service_name ASC  "
        ]);

        $this->page_data['service_list'] = $service_result['rows']; 

        # 리스트 정보요청
        $list_result = $this->model->getContracts([            
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
        $this->page_data['contents_path'] = '/contract/'. $this->page_name .'_list.php';
        $this->page_data['list'] = $list_result['rows'];        
        $this->view( $this->page_data );

    }

    public function contract_write() {

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=

        # 기업정보 요청
        $company_result = $this->company_model->getCompanys([            
            'query_where' => " AND del_flag='N' "
            ,'query_sort' => " ORDER BY company_idx DESC "            
        ]);
        
        $this->page_data['company_list'] = $company_result['rows'];

        # 서비스 정보 요청
        $service_result = $this->service_model->getServices([            
            'query_where' => " AND use_flag='Y' "
            ,'query_sort' => " ORDER BY service_name ASC  "
        ]);
        
        $this->page_data['service_codes'] = $service_result['rows']; 
        
        # 서비스 정보 요청
        $service_item_result = $this->service_model->getServiceItems(" use_flag='Y' AND service_code='SV001' AND depth='1' ");

        $this->page_data['doc_type'] = $service_item_result; 
        
        $this->page_data['document_usage'] = [];

        if( $this->page_data['mode'] == 'edit') {

            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
            # 필수값 체크
            #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=            
            $this->issetParams( $this->page_data, ['idx']);
            
            $this->page_data['page_work'] = '수정';
            
            # 계약 정보
            $contract_result = $this->model->getContract( " idx='". $this->page_data['idx'] ."' " );

            if( count( $contract_result['row'] ) > 0  ) {
                $this->page_data = array_merge( $this->page_data, $contract_result['row'] );
            }
            
            # 회사 정보 
            $company_result = $this->company_model->getCompanys([
                'query_where' => " AND company_idx='". $this->page_data['company_idx'] ."' "
            ]);

            if( count( $company_result['rows'][0] ) > 0  ) {
                $this->page_data = array_merge( $this->page_data, $company_result['rows'][0] );
            }

            # 전자문서 정보 
            $doc_result = $this->doc_model->getDocs([
                'query_where' => " AND company_idx='". $this->page_data['company_idx'] ."' AND del_flag='N' "
            ]);

            if( $doc_result['total_rs'] > 0 ){
                $this->page_data['document_usage'] = $doc_result['rows'];
            } 
   
            # 파일 정보            
            $file_result = $this->file_manager->dbGetFile("
                tb_key = '". $this->page_data['idx'] ."'
                AND where_used = 'contract_file'
                AND tb_name = 't_service_client'
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
        $this->page_data['contents_path'] = '/contract/'. $this->page_name .'_write.php';
        
        
        $this->view( $this->page_data );
        
    }

    /**
     * 기업정보 데이터를 처리한다.
     */
    public function contract_proc(){

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
                    ,'service_code'
                    ,'start_date'
                    ,'end_date'
                ]);
                

                # 기업 기본키와 서비스가 일치하는 값이 기존에 존재 하는지 확인.
                $query_result = $this->model->getContracts([            
                    'query_where' => " AND ( company_idx='". $this->page_data['company_idx'] ."' AND  service_code='". $this->page_data['service_code'] ."' ) "
                ]);

                if( count( $query_result['rows'] ) > 0 ){
                    
                    errorBack('이미 등록된 서비스 입니다.');
                    
                }

                # service_items 문자열로 변환
                if( count( $this->page_data['service_items'] ) > 0 ){
                    $this->page_data['service_items'] = join(',', $this->page_data['service_items'] );
                }

                # 트랜잭션 시작
                $this->model->runTransaction();

                # 기업 정보 삽입
                $query_result = $this->model->insetContract([
                    'service_code' => $this->page_data['service_code']
                    ,'company_idx' => $this->page_data['company_idx']
                    ,'start_date' => $this->page_data['start_date']
                    ,'end_date' => $this->page_data['end_date'] . ' 23:59:59'
                    ,'service_items' => $this->page_data['service_items']                   
                    ,'reg_idx' => getAccountInfo()['idx']
                    ,'reg_date' => 'NOW()'
                    ,'reg_ip' => $this->getIP()
                ]);
                
                # 기업정보 삽입 완료된 기본키를 가져온다.
                $new_contract_idx = $query_result['return_data']['insert_id'];
                
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 파일 업로드
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=  
                $this->file_manager->path = UPLOAD_COMPANY_CONTRACT_PATH;
                $this->file_manager->file_element = 'contract_file';
                $this->file_manager->table_data = [
                    'insert'=> [
                        'tb_name' => 't_service_client'                        
                        ,'where_used' => 'contract_file'
                        ,'tb_key' => $new_contract_idx
                    ]
                ];
                $this->file_manager->fileUpload();
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # // 파일 업로드
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=  


                # 문서 사용정보 변경                
                if( count( $this->page_data['doc_idx'] ) > 0 ) {

                    foreach( $this->page_data['doc_idx'] AS $idx=>$val ) {
                    

                        if( empty( $this->page_data['doc_usage_idx'][$idx] ) == true ){
                           # insert 
                           $insert_result = $this->doc_model->insertDoc([
                                'company_idx' => $this->page_data['company_idx']
                                ,'doc_idx' => $this->page_data['doc_idx'][$idx]
                                ,'item_code' => $this->page_data['item_code'][$idx]
                                ,'doc_type' => $this->page_data['doc_type'][$idx]
                                ,'doc_title' => $this->page_data['doc_title'][$idx]
                                ,'doc_table_style_data' => $this->page_data['doc_table_style_data'][$idx]
                                ,'doc_data' => $this->page_data['doc_data'][$idx]
                                ,'reg_idx' => getAccountInfo()['idx']
                                ,'reg_date' => 'NOW()'
                                ,'reg_ip' => $this->getIP()
                           ]);
                           
                        } 
                        
                    }
                } 


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
                    'idx'
                    ,'company_idx'
                    ,'service_code'
                    ,'start_date'
                    ,'end_date'
                ]);
                
                # service_items 문자열로 변환
                if( count( $this->page_data['service_items'] ) > 0 ){
                    $this->page_data['service_items'] = join(',', $this->page_data['service_items'] );
                }

                # 트랜잭션 시작
                $this->model->runTransaction();

                # 기업 정보 삽입
                $query_result = $this->model->updateContract([
                    'service_code' => $this->page_data['service_code']
                    ,'company_idx' => $this->page_data['company_idx']
                    ,'start_date' => $this->page_data['start_date']
                    ,'end_date' => $this->page_data['end_date'] . ' 23:59:59'
                    ,'service_items' => $this->page_data['service_items']                   
                    ,'edit_idx' => getAccountInfo()['idx']
                    ,'edit_date' => 'NOW()'
                    ,'edit_ip' => $this->getIP()
                ]," idx = '" . $this->page_data['idx']. "'" );
                
                # 문서 사용정보 변경
                $doc_usage_idx = [];
                if( count( $this->page_data['doc_idx'] ) > 0 ) {

                    foreach( $this->page_data['doc_idx'] AS $idx=>$val ) {
                    

                        if( empty( $this->page_data['doc_usage_idx'][$idx] ) == true ){
                           # insert 
                           $insert_result = $this->doc_model->insertDoc([
                                'company_idx' => $this->page_data['company_idx']
                                ,'doc_idx' => $this->page_data['doc_idx'][$idx]
                                ,'item_code' => $this->page_data['item_code'][$idx]
                                ,'doc_type' => $this->page_data['doc_type'][$idx]
                                ,'doc_title' => $this->page_data['doc_title'][$idx]
                                ,'doc_table_style_data' => $this->page_data['doc_table_style_data'][$idx]
                                ,'doc_data' => $this->page_data['doc_data'][$idx]
                                ,'reg_idx' => getAccountInfo()['idx']
                                ,'reg_date' => 'NOW()'
                                ,'reg_ip' => $this->getIP()
                           ]);
                           
                           array_push($doc_usage_idx, $insert_result['return_data']['insert_id'] ); 
    
                        } else {
                           # update 
                            array_push($doc_usage_idx, $this->page_data['doc_usage_idx'][$idx] ); 
                            $this->doc_model->updateDoc([
                                'doc_idx' => $this->page_data['doc_idx'][$idx]
                                ,'item_code' => $this->page_data['item_code'][$idx]
                                ,'doc_type' => $this->page_data['doc_type'][$idx]
                                ,'doc_title' => $this->page_data['doc_title'][$idx]
                                ,'doc_table_style_data' => $this->page_data['doc_table_style_data'][$idx]
                                ,'doc_data' => $this->page_data['doc_data'][$idx]
                                ,'edit_idx' => getAccountInfo()['idx']
                                ,'edit_date' => 'NOW()'
                                ,'edit_ip' => $this->getIP()
                            ], " doc_usage_idx = '" . $this->page_data['doc_usage_idx'][$idx] . "'" );
    
                        }
                        
                    }
                    
                    if( count( $doc_usage_idx ) > 0 ) {
                        $this->doc_model->updateDoc([
                            'del_flag' => 'Y'                            
                            ,'del_idx' => getAccountInfo()['idx']
                            ,'del_date' => 'NOW()'
                            ,'del_ip' => $this->getIP()
                        ], " company_idx='". $this->page_data['company_idx'] ."' AND doc_usage_idx NOT IN (" . join( ',', $doc_usage_idx) . ") " );
                    }


                } else {
                    $this->doc_model->updateDoc([
                        'del_flag' => 'Y'                            
                        ,'del_idx' => getAccountInfo()['idx']
                        ,'del_date' => 'NOW()'
                        ,'del_ip' => $this->getIP()
                    ], " company_idx='". $this->page_data['company_idx'] ."' " );
                }
                
                
                
                
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 파일 업로드
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=  
                $this->file_manager->path = UPLOAD_COMPANY_CONTRACT_PATH;
                $this->file_manager->file_element = 'contract_file';
                $this->file_manager->table_data = [
                    'insert'=> [
                        'tb_name' => 't_service_client'                        
                        ,'where_used' => 'contract_file'
                        ,'tb_key' => $this->page_data['idx']
                    ]
                    , 'delete' => " idx='". $this->page_data['file_idx'] ."' "
                ];
                $this->file_manager->fileUpload();
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # // 파일 업로드
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=  


                # 트랜잭션 종료
                $this->model->stopTransaction();


                movePage('replace', '저장되었습니다.', './'. $this->page_name .'_write?idx='. $this->page_data['idx']. '&mode=edit' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

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