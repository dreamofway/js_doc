<?php

class food extends baseController {

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
        $this->model = $this->new('foodModel');
        

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
     * 식품유형 목록을 요청한다.
     */
    public function food_type_list(){

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $this->page_data['list_rows'] = 25;
        $this->paging->list_rows = 25;

        $query_where = " AND ( depth = 1 ) ";        
        $query_sort = ' ORDER BY use_flag ASC, title ASC ';
        $query_sort = ' ORDER BY food_idx ASC ';
        $limit = " LIMIT ".(($this->page_data['page']-1)*$this->page_data['list_rows']).", ".$this->page_data['list_rows'];

        if( $this->page_data['sch_keyword'] ) {
            $query_where .= " AND ( 
                                    ( title LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    
                            ) ";
        }


        # 리스트 정보요청
        $list_result = $this->model->getFoodTypes([            
            'query_where' => $query_where
            ,'query_sort' => $query_sort
            ,'limit' => $limit
        ]);
        
        
        $this->paging->total_rs = $list_result['total_rs'];        
        $this->page_data['paging'] = $this->paging; 
        
        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
        $this->page_data['page_name'] = 'food_type';
        $this->page_data['page_init'] = './food_type_list?top_code=' . $this->page_data['top_code'] .'&left_code='. $this->page_data['left_code'];
        $this->page_data['contents_path'] = '/food/food_type_list.php';
        $this->page_data['list'] = $list_result['rows'];
        
        $this->view( $this->page_data );

    }

    /**
     * 식품유형 대분류 데이터 처리
     */
    public function food_large_cate_proc(){

        switch( $this->page_data['mode'] ) {

            case 'ins' : {

                #코드생성
                $this->page_data['food_code'] = $this->makeNewCode( CODE_FOOD_LARGE, $this->model->createFoodTypeCode( CODE_FOOD_LARGE ), 3 );
                
                if( empty( $this->page_data['title'] ) ) {
                    errorBack('잘못된 접근입니다.');
                }

                $insert_result = $this->model->insertFoodType([            
                    'food_code' => $this->page_data['food_code']
                    ,'title' => $this->page_data['title']
                    ,'group_code' => $this->page_data['food_code']
                    ,'parent_code' => $this->page_data['food_code']
                    ,'depth' => 1                     
                    ,'reg_idx' => getAccountInfo()['idx']
                    ,'reg_date' => 'NOW()'
                    ,'reg_ip' => $this->getIP()              
                ]);

                if( $insert_result['state'] ) {
                    movePage('replace', '저장되었습니다.', './food_type_list?top_code=' . $this->page_data['top_code'] .'&left_code='. $this->page_data['left_code'] );
                } else {
                    errorBack('데이터 처리에 실패하였습니다.');
                }

                break;
            }

            case 'edit' : {

                $update_result = $this->model->updateFoodType([
                    'title' => $this->page_data['title']
                    ,'use_flag' => $this->page_data['use_flag']
                
                ]," food_code = '" . $this->page_data['edit_code']. "'" );

                if( $update_result['state'] ) {
                    movePage('replace', '수정되었습니다.', './food_type_list?page=' . $this->page_data['page'] . $this->page_data['params'] );
                } else {
                    errorBack('데이터 처리에 실패하였습니다.');
                }


                break;
            }

            case 'del' : {
                
                $del_result = $this->model->deleteFoodType( " food_code = '" . $this->page_data['edit_code']. "'" );

                if( $del_result['state'] ) {
                    movePage('replace', '삭제되었습니다.', './food_type_list?page=' . $this->page_data['page'] . $this->page_data['params'] );
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

    /**
     * 식품 유형의 중분류와 유형 데이터를 처리한다.
     */
    public function food_type_proc() {
        
        $result_array['state'] = 'fail';
        
        switch( $this->page_data['mode'] ) {

            case 'get_middle' : {
                
                $query_where = "
                    AND ( group_code = '". $this->page_data['group_code'] ."' )
                    AND ( depth = 2 )
                ";

                $query_sort = " ORDER BY title ASC ";

                # 중분류 항목을 반환 한다. 추후 정렬 순서 적용
                $query_result = $this->model->getFoodTypes([            
                    'query_where' => $query_where
                    ,'query_sort' => $query_sort                    
                ])['rows'];
                
                exit(json_encode( $query_result ));

                break;
            }

            case 'get_types' : {
                
                if( empty( $this->page_data['group_code'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = 'group_code 코드가 누락 되어 목록을 불러올 수 없습니다.';
                    exit(json_encode( $result_array ));
                }

                if( empty( $this->page_data['parent_code'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = 'parent_code 코드가 누락 되어 목록을 불러올 수 없습니다.';
                    exit(json_encode( $result_array ));
                }
                

                $query_where = "
                    AND ( group_code = '". $this->page_data['group_code'] ."' )
                    AND ( parent_code = '". $this->page_data['parent_code'] ."' )
                    AND ( depth = 3 )
                ";

                $query_sort = " ORDER BY title ASC ";

                # 중분류 항목을 반환 한다. 추후 정렬 순서 적용
                $query_result = $this->model->getFoodTypes([            
                    'query_where' => $query_where
                    ,'query_sort' => $query_sort                    
                ])['rows'];
                
                exit(json_encode( $query_result ));

                break;
            }

            case 'insert_middle' : {

                $result_array['state'] = 'success';

                if( empty( $this->page_data['group_code'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = '서비스 코드가 누락 되었습니다.';
                    exit(json_encode( $result_array ));
                }

                if( empty( $this->page_data['title'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = 'title 누락되었습니다.';
                    exit(json_encode( $result_array ));
                }


                #중분류 코드생성
                $new_food_code = $this->makeNewCode( CODE_FOOD_MIDDLE, $this->model->createFoodTypeCode( CODE_FOOD_MIDDLE ), 3 );
                
                $result_array['title'] = $this->page_data['title'];
                
                # 신규 중분류 등록
                $insert_result = $this->model->insertFoodType([            
                    'food_code' => $new_food_code
                    ,'title' => $this->page_data['title']
                    ,'group_code' => $this->page_data['group_code']
                    ,'parent_code' => $new_food_code
                    ,'depth' => 2                     
                    ,'use_flag' => 'Y'
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

            case 'insert_type' : {
                
                if( empty( $this->page_data['title'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = 'title 누락되었습니다.';
                    exit(json_encode( $result_array ));
                }


                # 부모코드 확인
                if( empty( $this->page_data['parent_code'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = '중분류를 선택해주세요.';
                    exit(json_encode( $result_array ));
                }

                # 대분류 코드 확인
                if( empty( $this->page_data['group_code'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = '대분류 코드가 누락 되었습니다.';
                    exit(json_encode( $result_array ));
                }

                $result_array['state'] = 'success';

                #유형 코드생성
                $new_food_code = $this->makeNewCode( CODE_FOOD_TYPE, $this->model->createFoodTypeCode( CODE_FOOD_TYPE ), 3 );
                
                $result_array['title'] = $this->page_data['title'];
                
                # 신규 유형 등록
                $insert_result = $this->model->insertFoodType([            
                    'food_code' => $new_food_code
                    ,'title' => $this->page_data['title']
                    ,'group_code' => $this->page_data['group_code']
                    ,'parent_code' => $this->page_data['parent_code']
                    ,'depth' => 3                    
                    ,'use_flag' => 'Y' 
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
                if( empty( $this->page_data['food_code'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = 'food_code 코드가 누락 되었습니다.';
                    exit(json_encode( $result_array ));
                }

                # 서비스 코드 확인
                if( empty( $this->page_data['group_code'] ) ){
                    $result_array['state'] = 'fail';
                    $result_array['msg'] = '서비스 코드가 누락 되었습니다.';
                    exit(json_encode( $result_array ));
                }
                
                $update_result = $this->model->updateFoodType([
                    'title' => $this->page_data['title'] 
                    ,'use_flag' => 'Y'
                    ,'edit_idx' => getAccountInfo()['idx']
                    ,'edit_date' => 'NOW()'
                    ,'edit_ip' => $this->getIP()
                
                ]," food_code = '" . $this->page_data['food_code']. "'" );


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
                
                $del_result = $this->model->deleteFoodType( " food_code = '" . $this->page_data['food_code']. "'" );

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

    public function food_type_set(){

        if( empty( $this->page_data['group_code'] ) ){
            errorBack('잘못된 접근입니다.');
        }

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=

        # 서비스 정보요청
        $query_result = $this->model->getFoodTypeInfo(" food_code = '". $this->page_data['group_code'] ."' " );
         
        
        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
        $this->page_data['page_name'] = 'food_type';
        $this->page_data['large_type_title'] = $query_result['title'];
        $this->page_data['page_init'] = './food_type_set?top_code=' . $this->page_data['top_code'] .'&left_code='. $this->page_data['left_code'];
        $this->page_data['contents_path'] = '/food/food_type_set.php';
        $this->page_data['list'] = $list_result['rows'];
        
        $this->view( $this->page_data );
    }

}

?>