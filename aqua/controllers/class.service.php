<?php

class service extends baseController {

    private $model;
    private $page_data;
    private $paging;
    
    function __construct() {

        #로그인 확인
        loginState();
        
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # model instance
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $this->paging = $this->new('pageHelper');
        $this->model = $this->new('serviecModel');
        

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
    public function service_items(){

        if( empty( $this->page_data['service_code'] ) ){
            errorBack('잘못된 접근입니다.');
        }

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=

        # 서비스 정보요청
        $query_result = $this->model->getService(" service_code = '". $this->page_data['service_code'] ."' " );
         
        
        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
        $this->page_data['page_name'] = 'service';
        $this->page_data['service_name'] = $query_result['service_name'];
        $this->page_data['page_init'] = './service_items?top_code=' . $this->page_data['top_code'] .'&left_code='. $this->page_data['left_code'];
        $this->page_data['contents_path'] = '/service/service_items.php';
        $this->page_data['list'] = $list_result['rows'];
        
        $this->view( $this->page_data );

    }

    /**
     * 서비스 항목관련 데이터 처리
     */
    public function service_items_proc(){

        $result_array['state'] = 'fail';
        
        switch( $this->page_data['mode'] ) {

            case 'get_items' : {
                
                # 서비스 항목을 반환 한다. 추후 정렬 순서 적용
                $query_result = $this->model->getServiceItems(
                    "   service_code = ( '". $this->page_data['service_code'] ."' )
                        AND ( depth = 1 )
                    "
                );
                
                exit(json_encode( $query_result ));

                break;
            }

            case 'get_fns' : {
                
                if( empty( $this->page_data['item_group'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = 'item_group 코드가 누락 되어 목록을 불러올 수 없습니다.';
                    exit(json_encode( $result_array ));
                }
                

                # 서비스 항목을 반환 한다. 추후 정렬 순서 적용
                $query_result = $this->model->getServiceItems(
                    "   service_code = ( '". $this->page_data['service_code'] ."' )
                        AND ( item_group = '". $this->page_data['item_group'] ."' )
                        AND ( depth = 2 )
                    "
                );
                
                exit(json_encode( $query_result ));

                break;
            }

            case 'insert_items' : {

                $result_array['state'] = 'success';

                if( empty( $this->page_data['service_code'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = '서비스 코드가 누락 되었습니다.';
                    exit(json_encode( $result_array ));
                }

                if( empty( $this->page_data['title'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = 'title 누락되었습니다.';
                    exit(json_encode( $result_array ));
                }


                #서비스 코드생성
                $new_item_code = $this->makeNewCode( CODE_SERVICE_ITEM, $this->model->createItemCode( CODE_SERVICE_ITEM ), 3 );
                
                $result_array['title'] = $this->page_data['title'];
                
                # 신규 서비스 항목 등록
                $insert_result = $this->model->insertServiceItem([            
                    'item_code' => $new_item_code
                    ,'service_code' => $this->page_data['service_code']
                    ,'item_group' => $new_item_code
                    ,'depth' => 1
                    ,'title' => $this->page_data['title'] 
                    ,'reg_idx' => getAccountInfo()['idx']
                    ,'reg_date' => 'NOW()'
                    ,'reg_ip' => $this->getIP()
                ]);

                if( $insert_result['state'] ) {
                    
                    $result_array['state'] = 'success';
                    
                } else {
                    $result_array['state'] = 'error';
                    $result_array['msg'] = '데이터 처리에 실패하였습니다.';
                }

                exit(json_encode( $result_array ));

                break;
            }

            case 'insert_fn' : {
                
                if( empty( $this->page_data['title'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = 'title 누락되었습니다.';
                    exit(json_encode( $result_array ));
                }


                # 부모코드 확인
                if( empty( $this->page_data['item_group'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = '서비스 항목을 선택해주세요.';
                    exit(json_encode( $result_array ));
                }

                # 서비스 코드 확인
                if( empty( $this->page_data['service_code'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = '서비스 코드가 누락 되었습니다.';
                    exit(json_encode( $result_array ));
                }

                $result_array['state'] = 'success';

                #서비스 코드생성
                $new_item_code = $this->makeNewCode( CODE_SERVICE_FUNCTION, $this->model->createItemCode( CODE_SERVICE_FUNCTION ), 3 );
                
                $result_array['title'] = $this->page_data['title'];
                
                # 신규 서비스 기능 등록

                # 신규 서비스 항목 등록
                $insert_result = $this->model->insertServiceItem([            
                    'item_code' => $new_item_code
                    ,'service_code' => $this->page_data['service_code']
                    ,'item_group' => $this->page_data['item_group']
                    ,'depth' => 2
                    ,'title' => $this->page_data['title'] 
                    ,'reg_idx' => getAccountInfo()['idx']
                    ,'reg_date' => 'NOW()'
                    ,'reg_ip' => $this->getIP()
                ]);

                if( $insert_result['state'] ) {
                    
                    $result_array['state'] = 'success';
                    
                } else {
                    $result_array['state'] = 'error';
                    $result_array['msg'] = '데이터 처리에 실패하였습니다.';
                }

                exit(json_encode( $result_array ));

                break;
            }

            case 'edit_item' : {
                
                if( empty( $this->page_data['title'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = 'title 누락되었습니다.';
                    exit(json_encode( $result_array ));
                }

                # item code 확인
                if( empty( $this->page_data['item_code'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = 'item_code 코드가 누락 되었습니다.';
                    exit(json_encode( $result_array ));
                }

                # 서비스 코드 확인
                if( empty( $this->page_data['service_code'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = '서비스 코드가 누락 되었습니다.';
                    exit(json_encode( $result_array ));
                }
                
                $update_result = $this->model->updateServiceItem([
                    'title' => $this->page_data['title'] 
                    ,'edit_idx' => getAccountInfo()['idx']
                    ,'edit_date' => 'NOW()'
                    ,'edit_ip' => $this->getIP()
                
                ]," item_code = '" . $this->page_data['item_code']. "'" );


                if( $update_result['state'] ) {
                    
                    $result_array['state'] = 'success';
                    
                } else {
                    $result_array['state'] = 'error';
                    $result_array['msg'] = '데이터 처리에 실패하였습니다.';
                }

                exit(json_encode( $result_array ));

                break;
            }

            case 'del' : {
                
                $del_result = $this->model->deleteServiceItem( " item_code = '" . $this->page_data['item_code']. "'" );

                if( $del_result['state'] ) {
                    
                    $result_array['state'] = 'success';
                    
                } else {
                    $result_array['state'] = 'error';
                    $result_array['msg'] = '데이터 처리에 실패하였습니다.';
                }

                exit(json_encode( $result_array ));

                break;
            }

            default : {
                $result_array['msg'] = '정의되지 않은 mode';
                exit(json_encode( $result_array ));
            }
        }

    }

    /**
     * 서비스 설정
     */
    public function service_list(){

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $query_where = '';
        $query_sort = ' ORDER BY service_name ASC ';
        $limit = " LIMIT ".(($this->page_data['page']-1)*$this->page_data['list_rows']).", ".$this->page_data['list_rows'];

        if( $this->page_data['sch_keyword'] ) {
            $query_where .= " AND ( 
                                    ( service_code LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    OR ( service_name LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                            ) ";
        }


        # 리스트 정보요청
        $list_result = $this->model->getServices([            
            'query_where' => $query_where
            ,'query_sort' => $query_sort
            ,'limit' => $limit
        ]);
        
        
        $this->paging->total_rs = $list_result['total_rs'];        
        $this->page_data['paging'] = $this->paging; 
        
        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
        $this->page_data['page_name'] = 'service';
        $this->page_data['page_init'] = './service_list?top_code=' . $this->page_data['top_code'] .'&left_code='. $this->page_data['left_code'];
        $this->page_data['contents_path'] = '/service/service_list.php';
        $this->page_data['list'] = $list_result['rows'];
        
        $this->view( $this->page_data );
        
    }

    /**
     * 서비스 데이터 처리를 한다.
     */
    public function service_proc(){


        switch( $this->page_data['mode'] ) {

            case 'ins' : {
                #서비스 코드생성
                $this->page_data['service_code'] = $this->makeNewCode( CODE_SERVICE, $this->model->createServiceCode(), 3 );
                
                if( empty( $this->page_data['service_name'] ) ) {
                    errorBack('잘못된 접근입니다.');
                }

                $insert_result = $this->model->insertService([            
                    'service_code' => $this->page_data['service_code']
                    ,'service_name' => $this->page_data['service_name']
                    ,'description' => $this->page_data['description']                    
                ]);

                if( $insert_result['state'] ) {
                    movePage('replace', '저장되었습니다.', './service_list?top_code=' . $this->page_data['top_code'] .'&left_code='. $this->page_data['left_code'] );
                } else {
                    errorBack('데이터 처리에 실패하였습니다.');
                }

                break;
            }

            case 'edit' : {

                $update_result = $this->model->updateService([
                    'service_name' => $this->page_data['service_name']
                    ,'description' => $this->page_data['description']
                    ,'use_flag' => $this->page_data['use_flag']
                
                ]," service_code = '" . $this->page_data['edit_code']. "'" );

                if( $update_result['state'] ) {
                    movePage('replace', '수정되었습니다.', './service_list?page=' . $this->page_data['page'] . $this->page_data['params'] );
                } else {
                    errorBack('데이터 처리에 실패하였습니다.');
                }


                break;
            }

            case 'del' : {
                
                $del_result = $this->model->deleteService( " service_code = '" . $this->page_data['edit_code']. "'" );

                if( $del_result['state'] ) {
                    movePage('replace', '삭제되었습니다.', './service_list?page=' . $this->page_data['page'] . $this->page_data['params'] );
                } else {
                    errorBack('데이터 처리에 실패하였습니다.');
                }

                break;
            }

            default : {
                errorBack('잘못된 접근입니다.');
            }

        }


    }

}

?>