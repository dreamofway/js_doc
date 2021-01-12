<?php

class country extends baseController {

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
        $this->model = $this->new('countryModel');
        

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
     * 국가 코드 목록 페이지 구성
     */
    public function country_code_set(){

        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        # SET Values
        #+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=+=
        $query_where = '';
        $query_sort = ' ORDER BY use_flag ASC, country_name ASC ';
        $limit = " LIMIT ".(($this->page_data['page']-1)*$this->page_data['list_rows']).", ".$this->page_data['list_rows'];

        if( $this->page_data['sch_keyword'] ) {
            $query_where .= " AND ( 
                                    ( country_code LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                                    OR ( country_name LIKE '%". $this->page_data['sch_keyword'] ."%' ) 
                            ) ";
        }


        # 리스트 정보요청
        $list_result = $this->model->getCountryCodes([            
            'query_where' => $query_where
            ,'query_sort' => $query_sort
            ,'limit' => $limit
        ]);
        
        
        $this->paging->total_rs = $list_result['total_rs'];        
        $this->page_data['paging'] = $this->paging; 
        
        $this->page_data['use_top'] = true;        
        $this->page_data['use_left'] = true;
        $this->page_data['use_footer'] = true;        
        $this->page_data['page_name'] = 'country_code_set';
        $this->page_data['page_init'] = './country_code_set?top_code=' . $this->page_data['top_code'] .'&left_code='. $this->page_data['left_code'];
        $this->page_data['contents_path'] = '/country/country_code_set.php';
        $this->page_data['list'] = $list_result['rows'];
        
        $this->view( $this->page_data );

    }

    /**
     * 국가코드 설정 데이터 처리 요청
     */
    public function country_code_proc(){
        
        switch( $this->page_data['mode'] ) {

            case 'ins' : {
                
                $insert_result = $this->model->insertCountryCode([            
                    'country_code' => $this->page_data['country_code']
                    ,'country_name' => $this->page_data['country_name']
                    ,'use_flag' => 'Y'
                ]);

                if( $insert_result['state'] ) {
                    movePage('replace', '저장되었습니다.', './country_code_set?top_code=' . $this->page_data['top_code'] .'&left_code='. $this->page_data['left_code'] );
                } else {
                    errorBack('데이터 처리에 실패하였습니다.');
                }

                break;
            }

            case 'edit' : {

                $update_result = $this->model->updateCountryCode([            
                    'country_code' => $this->page_data['country_code']
                    ,'country_name' => $this->page_data['country_name']
                    ,'use_flag' => $this->page_data['use_flag']
                
                ]," country_code = '" . $this->page_data['edit_code']. "'" );

                if( $update_result['state'] ) {
                    movePage('replace', '수정되었습니다.', './country_code_set?page=' . $this->page_data['page'] . $this->page_data['params'] );
                } else {
                    errorBack('데이터 처리에 실패하였습니다.');
                }


                break;
            }

            case 'del' : {
                
                $del_result = $this->model->deleteCountryCode( " country_code = '" . $this->page_data['edit_code']. "'" );

                if( $del_result['state'] ) {
                    movePage('replace', '삭제되었습니다.', './country_code_set?page=' . $this->page_data['page'] . $this->page_data['params'] );
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