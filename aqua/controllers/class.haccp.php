<?php
/**
 * ---------------------------------------------------
 * AQUA Framework 생산관리 v1.0.0
 * ---------------------------------------------------
 * History
 * ---------------------------------------------------
 * 
 * [v1.0.0] 2020.03.28 - 이정훈 
 *  - production_list() 개발
 * 
 * ---------------------------------------------------
*/
class haccp extends baseController {

    private $model;    
    private $paging;
    private $file_manager;    

    function __construct() {
        
        #로그인 확인
        loginState();
        
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # model instance
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $this->paging = $this->new('pageHelper');
        $this->model = $this->new('haccpModel');         
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
            , 'sch_keyword'
            , 'sch_s_date'
            , 'sch_e_date'
            , 'sch_order_field'
            , 'sch_order_status'
        ]);

    }

    /**
     * 모니터링 대시보드
     */
    public function monitor() {

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=        
        // $list_result = $this->model->getDocumentFiles();   

        // # 리스트 정보요청
        // $list_result = $this->model->getDocumentFiles([            
        //     'query_where' => $query_where
        //     ,'query_sort' => $query_sort
        //     ,'limit' => $limit
        // ]);

        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;                        
        $this->page_data['contents_path'] = '/haccp/monitor_dashboard.php';
        $this->page_data['list'] = $list_result['rows'];        
        $this->view( $this->page_data );

    }


    /**
     * 저장고 목록 구현
     */
    public function monitor_limit() {

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $page_name = 'monitor_limit';
        
        $query_where = " AND ( del_flag='N' ) AND ( company_idx = '". $this->page_data['company_idx'] ."') ";
        
        $limit = " LIMIT ".(($this->page_data['page']-1)*$this->page_data['list_rows']).", ".$this->page_data['list_rows'];

        if( $this->page_data['sch_order_field'] ){
            $query_sort = ' ORDER BY '. $this->page_data['sch_order_field'] .' '.$this->page_data['sch_order_status'].' ';
        } else {
            $query_sort = ' ORDER BY storage_idx DESC ';
        }

        if( $this->page_data['sch_keyword'] ) {
            $query_where .= " AND ( 
                                    ( storage_name LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    
                            ) ";
        }

        if($this->page_data['sch_s_date']) {
            $query_where .= " AND ( reg_date >= '".$this->page_data['sch_s_date']." 00:00:00' ) ";
        }

		if($this->page_data['sch_e_date']) {
            $query_where .= " AND ( reg_date <= '".$this->page_data['sch_e_date']." 23:59:59' ) ";
        }


        # 리스트 정보요청
        $list_result = $this->model->getStorages([            
            'query_where' => $query_where
            ,'query_sort' => $query_sort
            ,'limit' => $limit
        ]);
        
        $this->paging->total_rs = $list_result['total_rs'];        
        $this->page_data['paging'] = $this->paging; 

        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
        $this->page_data['page_name'] = $page_name;
        
        $this->page_data['contents_path'] = '/haccp/'. $page_name .'.php';
        $this->page_data['list'] = $list_result['rows'];        
        $this->view( $this->page_data );

    }

    /**
     * 저장고 측정 온도 변경
     */
    public function monitor_limit_proc() {

         # post 접근 체크
         postCheck();

        // echoBr( $this->page_data ); exit;

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # 필수값 체크
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
        $this->issetParams( $this->page_data, [
            'min_temperature'
            ,'max_temperature'
        ]);
        
        # 트랜잭션 시작
        $this->model->runTransaction();

        foreach( $this->page_data['min_temperature'] AS $key=>$value) {
            
            $query_result = $this->model->updateStorage([
                'min_temperature'=> $this->page_data['min_temperature'][$key] 
                ,'max_temperature'=> $this->page_data['max_temperature'][$key] 
            ]," storage_idx = '" . $key . "'" );

        }

        # 트랜잭션 종료
        $this->model->stopTransaction();


        movePage('replace', '저장되었습니다.', './'. $this->page_data['page_name'] .'?page=1' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

    }

    /**
     * 이탈관리 목록 구성
     */
    public function monitor_warning_list(){

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $page_name = 'monitor_warning';
        
        $query_where = " AND ( del_flag='N' ) AND ( company_idx = '". $this->page_data['company_idx'] ."') ";
        
        $limit = " LIMIT ".(($this->page_data['page']-1)*$this->page_data['list_rows']).", ".$this->page_data['list_rows'];

        if( $this->page_data['sch_order_field'] ){
            $query_sort = ' ORDER BY '. $this->page_data['sch_order_field'] .' '.$this->page_data['sch_order_status'].' ';
        } else {
            $query_sort = ' ORDER BY warning_idx DESC ';
        }

        if( $this->page_data['sch_keyword'] ) {
            $query_where .= " AND ( 
                                    ( storage_name LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    
                            ) ";
        }

        if($this->page_data['sch_s_date']) {
            $query_where .= " AND ( reg_date >= '".$this->page_data['sch_s_date']." 00:00:00' ) ";
        }

		if($this->page_data['sch_e_date']) {
            $query_where .= " AND ( reg_date <= '".$this->page_data['sch_e_date']." 23:59:59' ) ";
        }

        $this->page_data['storages'] = $this->model->getStorage( " AND ( del_flag = 'N' ) AND ( use_flag='Y' ) ORDER BY storage_idx ASC " )['rows'];



        # 리스트 정보요청
        $list_result = $this->model->getStorageWarningLogs([
            'query_where' => $query_where
            ,'query_sort' => $query_sort
            ,'limit' => $limit
        ]);
        
        $this->paging->total_rs = $list_result['total_rs'];        
        $this->page_data['paging'] = $this->paging; 

        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
        $this->page_data['page_name'] = $page_name;
        
        $this->page_data['contents_path'] = '/haccp/'. $page_name .'_list.php';
        $this->page_data['list'] = $list_result['rows'];        
        $this->view( $this->page_data );
    }


    /**
     * 이탈관리 상세 구성
     */
    public function monitor_warning_view(){  
        
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $page_name = 'monitor_warning';
        
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # 필수값 체크
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
        $this->issetParams( $this->page_data, [
            'warning_idx' 
        ]);
        
        $this->page_data['page_work'] = '수정';

        # 주문 내역을 요청한다.
        $query_result = $this->model->getStorageWarningLog(" AND warning_idx = '". $this->page_data['warning_idx'] ."' " );    
            
        if( $query_result['num_rows'] == 0 ){
                
            errorBack('해당 게시물이 삭제되었거나 정상적인 접근 방법이 아닙니다.');
            
        }

        $this->page_data = array_merge( $this->page_data, $query_result['row'] );

        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
        $this->page_data['page_name'] = $page_name;
        $this->page_data['contents_path'] = '/haccp/'. $page_name .'_view.php';
        $this->page_data['list'] = $list_result['rows'];
        
        $this->view( $this->page_data );

    }

    public function monitor_warning_proc(){
        # post 접근 체크
        postCheck();

        // echoBr( $this->page_data ); exit;

        switch( $this->page_data['mode'] ) {

            case 'ins' : {
                
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=`
                # 필수값 체크
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
                $this->issetParams( $this->page_data, [
                    'storage_idx'                    
                    ,'warning_temperature'                    
                    ,'warning_cause'                    
                    ,'warning_action'                    
                ]);
                
                
                # 트랜잭션 시작
                $this->model->runTransaction();
                
                $get_storage_result = $this->model->getStorage( " AND ( storage_idx = '".$this->page_data['storage_idx']."' ) " )['row'];
                
                $limit_range = $get_storage_result['min_temperature'].' ~ '. $get_storage_result['max_temperature'];

                $insert_data = [
                    'storage_idx' => $this->page_data['storage_idx']
                    ,'company_idx' => $this->page_data['company_idx']
                    ,'warning_temperature' => $this->page_data['warning_temperature']                        
                    ,'warning_cause' => $this->page_data['warning_cause']                        
                    ,'warning_action' => $this->page_data['warning_action']                        
                    ,'limit_range' => $limit_range                 
                    ,'reg_idx' => getAccountInfo()['idx']
                    ,'reg_date' => 'NOW()'
                    ,'reg_ip' => $this->getIP()
                ];

                # 정보 삽입
                $query_result = $this->model->insertStorageWarningLog( $insert_data );
                
                
                # 트랜잭션 종료
                $this->model->stopTransaction();

                movePage('replace', '저장되었습니다.', './'. $this->page_data['page_name'] .'_list?page=1' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

                break;
            }
            case 'edit' : {

                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 필수값 체크
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
                $this->issetParams( $this->page_data, [
                    'warning_idx'
                    ,'storage_idx'
                    ,'warning_temperature'                    
                    ,'warning_cause'                    
                    ,'warning_action'    
                ]);
               
                # 트랜잭션 시작
                $this->model->runTransaction();

                $get_storage_result = $this->model->getStorage( " AND ( storage_idx = '".$this->page_data['storage_idx']."' ) " )['row'];
                
                $limit_range = $get_storage_result['min_temperature'].' ~ '. $get_storage_result['max_temperature'];

                $update_data = [
                    'storage_idx' => $this->page_data['storage_idx']                    
                    ,'warning_temperature' => $this->page_data['warning_temperature']                        
                    ,'warning_cause' => $this->page_data['warning_cause']                        
                    ,'warning_action' => $this->page_data['warning_action']                        
                    ,'limit_range' => $limit_range                 
                    ,'edit_idx' => getAccountInfo()['idx']
                    ,'edit_date' => 'NOW()'
                    ,'edit_ip' => $this->getIP()
                ];

                # 정보 삽입
                $query_result = $this->model->updateStorageWarningLog( $update_data, " warning_idx = '" . $this->page_data['warning_idx'] . "'" );
                
                # 트랜잭션 종료
                $this->model->stopTransaction();


                movePage('replace', '저장되었습니다.', './'. $this->page_data['page_name'] .'_list?page=1' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

                break;
            }
            case 'del' : {

                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
                # 필수값 체크
                #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=                
                $this->issetParams( $this->page_data, [
                    'warning_idx'
                ]);
                
                # 트랜잭션 시작
                $this->model->runTransaction();

                # 정보 수정
                $query_result = $this->model->updateStorageWarningLog(['del_flag'=>'Y']," warning_idx = '" . $this->page_data['warning_idx'] . "'" );
                
                # 트랜잭션 종료
                $this->model->stopTransaction();

                movePage('replace', '삭제되었습니다.', './'. $this->page_data['page_name'] .'_list?page=1' . htmlspecialchars_decode( $this->page_data['ref_params'] ) );

                break;
            }

            default : {
                errorBack('잘못된 접근입니다.');
            }
        }
    }

    /**
     * 모니터링 목록 구성
     */
    public function monitoring_log(){

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $page_name = 'monitoring_log';
        
        $query_where = " AND ( company_idx = '". $this->page_data['company_idx'] ."') ";
        
        $limit = " LIMIT ".(($this->page_data['page']-1)*$this->page_data['list_rows']).", ".$this->page_data['list_rows'];

        if( $this->page_data['sch_order_field'] ){
            $query_sort = ' ORDER BY '. $this->page_data['sch_order_field'] .' '.$this->page_data['sch_order_status'].' ';
        } else {
            $query_sort = ' ORDER BY temp_log_idx DESC ';
        }

        if( $this->page_data['sch_keyword'] ) {
            $query_where .= " AND ( 
                                    ( storage_name LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    
                            ) ";
        }

        if($this->page_data['sch_s_date']) {
            $query_where .= " AND ( reg_date >= '".$this->page_data['sch_s_date']." 00:00:00' ) ";
        }

		if($this->page_data['sch_e_date']) {
            $query_where .= " AND ( reg_date <= '".$this->page_data['sch_e_date']." 23:59:59' ) ";
        }

        if($this->page_data['sch_storages']) {
            $query_where .= " AND ( storage_idx = '".$this->page_data['sch_storages']."' ) ";
        }

        $this->page_data['storages'] = $this->model->getStorage( " AND ( del_flag = 'N' ) AND ( use_flag='Y' ) ORDER BY storage_idx ASC " )['rows'];



        # 리스트 정보요청
        $list_result = $this->model->getStorageLogs([
            'query_where' => $query_where
            ,'query_sort' => $query_sort
            ,'limit' => $limit
        ]);
        
        $this->paging->total_rs = $list_result['total_rs'];        
        $this->page_data['paging'] = $this->paging; 

        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
        $this->page_data['page_name'] = $page_name;
        
        $this->page_data['contents_path'] = '/haccp/monitoring_log.php';
        $this->page_data['list'] = $list_result['rows'];        
        $this->view( $this->page_data );
    }
    
   
}

?>