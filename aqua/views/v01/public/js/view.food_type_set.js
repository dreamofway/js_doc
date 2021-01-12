var foodHandler = {
    request_path : './food_type_proc'
    , group_code : ''
    , parent_code : ''
    , edit_code : ''
    , middle_list : []
    , types_list : []
    , _this : foodHandler
    , init : function(){

        // 서비스 중분류을 요청한다.
        this.group_code = get_urlParam()['group_code'];

        if( this.group_code == '' ){
            console.error('대분류 코드가 존재하지 않아 더이상 진행이 불가능합니다.');
            return;
        }

        // 중분류 목록을 요청한다.
        this.getMiddleCategorys();
        

        // 서비스 유형을 요청한다.

    }
    , middleAction : function( arg_type, arg_code ){

        /**
            중분류 처리 버튼 동작 처리
         */
        if( arg_code ) {
            this.edit_code = arg_code;
        }

        this.actionHandler({
            work_id : 'middle'
            ,type : arg_type				
            ,work_title : '중분류'				
        });

    }
    , typeAction : function( arg_type, arg_code ){

        /**
            유형 버튼 동작 처리
         */
        if( arg_code ) {
            this.edit_code = arg_code;
        }

        this.actionHandler({
            work_id : 'type'
            ,type : arg_type
            ,work_title : '유형'				
        });

    }
    , actionHandler : function( arg_data ) {

        /**
            중분류 / 유형 버튼 동작 처리
         */
        target_id = '#'+arg_data.work_id+'_write';

        target_val = $( target_id ).val();
        

        switch( arg_data.type ) {
            case 'save' : {

                if( target_val == "" ){

                    alert( arg_data.work_title + '을 입력해주세요.' );
                    $( target_id ).focus();

                    return false;
                }
            
                if( this.edit_code == '' ){

                    mode = 'insert_' + arg_data.work_id;

                } else {

                    mode = 'edit_item';

                }
                
                this.requesData({
                    request_data : {
                        group_code : this.group_code
                        , mode : mode
                        , title : target_val         
                        , food_code : this.edit_code         
                        , parent_code : this.parent_code         
                        , callBackData : arg_data.work_id    
                    }
                    ,callBack : this.actionAfterProc
                });
                
				$( target_id ).val('');
                this.edit_code = '';

                break;

            }
            case 'edit' : {
                
                /**
                    선택한 데이터를 수정 할 수 있도록 배치한다.
                 */
                
                if( arg_data.work_id == 'middle' ) {
                    $( target_id ).val( this.middle_list[ this.edit_code ]['title'] );
                } else {
                    $( target_id ).val( this.types_list[ this.edit_code ]['title'] );
                }
                
                $( target_id ).focus();

                // focus out 이벤트 등록
                this.evWriteInputFocusout( 'on', arg_data );
                
                jQuery('html, body').animate({
                    'scrollTop': $( target_id ).offset().top
                },0);
                
                break;
            }

            case 'cancel' : {
                /**
                    작성 취소 버튼 클릭시
                 */
                $( target_id ).val('');
                this.edit_code = '';
                
                // focus out 이벤트 제거
                this.evWriteInputFocusout( 'off', arg_data );

                break;
            }

            case 'del' : {
                /**
                    삭제 요청을 보낸다.
                 */
                if( confirm( '해당 ' + arg_data.work_title + '을 삭제하시겠습니까?') ) {

                    this.requesData({
                        request_data : {
                            mode : 'del'							
                            , food_code : this.edit_code 
                            , callBackData : arg_data.work_id    							
                        }							
                        ,callBack : this.actionAfterProc
                    });

                }
                
                break;
            }
        }

    }
    , actionAfterProc( req_result, arg_work_id ){
        
        
        if( req_result.state == 'success' ) {

            $( '#' + arg_work_id + '_write' ).val('');
            
            if( arg_work_id == 'middle' ) {

                /**
                    중분류 목록 요청
                */					
                foodHandler.getMiddleCategorys();

            } else {
                /**
                    유형 목록 요청
                */
                foodHandler.getTypes();
            }
            

        } else {

            alert( req_result.msg );

        }
    }
    , getMiddleCategorys : function(){
        /**
            중분류 목록을 요청한다.
         */
        this.requesData({
            request_data : {
                mode : 'get_middle'
                , group_code : this.group_code                    
            },
            callBack : function( req_data ){
                foodHandler.makeMiddleCategorys( req_data );
            }
        });

    }
    , getTypes : function(){
        /**
            유형 목록을 요청한다.
         */
        this.requesData({
            request_data : {
                mode : 'get_types'					
                , group_code : this.group_code 
                , parent_code : this.parent_code
            },
            callBack : function( req_data ){
                foodHandler.makeTypes( req_data );
            }
        });

    }
    , makeMiddleCategorys : function( arg_data ){
        /**
            중분류 목록을 DOM에 생성한다.				
         */
        list = [];
        $('#middle_area').html( '' ); 
        
        if( arg_data.length > 0 ) {

            $.each( arg_data, function(idx, list_val ){
                
                foodHandler.middle_list[ list_val.food_code ] = {	
                    code : list_val.food_code
                    ,title : list_val.title
                };

                list.push({	
                    code : list_val.food_code
                    ,title : list_val.title
                });

            });
            
            $('#middle_area').html( $('#tmplate_middle').tmpl( list ) );  
            

        }

    }
    , makeTypes : function( arg_data ) {
        /**
            유형 목록을 DOM에 생성한다.				
         */
        list = [];
        $('#type_area').html( '' ); 

        if( arg_data.length > 0 ) {

            $.each( arg_data, function(idx, list_val ){
                
                foodHandler.types_list[ list_val.food_code ] = {	
                    code : list_val.food_code
                    ,title : list_val.title
                };

                list.push({	
                    code : list_val.food_code
                    ,title : list_val.title
                });

            });

            $('#type_area').html( $('#tmplate_types').tmpl( list ) );  
            
        }
    }
    , choiceItem : function( arg_code ){

        this.parent_code = arg_code;
        $('#choice_title').html( this.middle_list[ arg_code ]['title'] );
        $('#field_status_text_A').show();
        $('#field_status_text_B').hide();

        $('#type_area').html('');
        // 유형 목록을 호출한다.
        foodHandler.getTypes();
        
    }
    , evWriteInputFocusout : function( arg_flag, arg_data ) {

        target_id = '#' + arg_data.work_id + '_write';

        if( arg_flag == 'on') {
            
            if( arg_data.work_id == 'middle' ) {
                $( target_id ).on('focusout', function(){
                    foodHandler.middleAction('cancel', '');
                });
                
            } else {
                $( target_id ).on('focusout', function(){
                    foodHandler.typeAction('cancel', '');
                });
            }
            
            $( target_id ).on('keyup', function(){
                foodHandler.evWriteInputFocusout('off', arg_data);
            });
            
        } else {
            $( target_id ).off('focusout');
        }

        }
    , requesData : function( arg_obj ){
        
        jqueryAjaxCall({
            type : "post",
            url : this.request_path,
            dataType : "json",
            paramData :arg_obj.request_data ,
            callBack : arg_obj.callBack 
        });    
    }
};