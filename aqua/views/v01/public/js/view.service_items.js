var serviceHandler = {
    request_path : './service_items_proc'
    , service_code : ''
    , item_group_code : ''
    , edit_code : ''
    , items_list : []
    , fns_list : []
    , _this : serviceHandler
    , init : function(){

        // 서비스 항목을 요청한다.
        this.service_code = get_urlParam()['service_code'];

        if( this.service_code == '' ){
            console.error('서비스 코드가 존재하지 않아 더이상 진행이 불가능합니다.');
            return;
        }

        // 항목 목록을 요청한다.
        this.getServiceItems();
        

        // 서비스 기능을 요청한다.

    }
    , itemsAction : function( arg_type, arg_code ){

        /**
            항목 처리 버튼 동작 처리
         */

        if( arg_code ) {
            this.edit_code = arg_code;
        }

        this.actionHandler({
            work_id : 'items'
            ,type : arg_type				
            ,work_title : '항목'				
        });

    }
    , fnAction : function( arg_type, arg_code ){

        /**
            기능 버튼 동작 처리
         */

        if( arg_code ) {
            this.edit_code = arg_code;
        }

        this.actionHandler({
            work_id : 'fn'
            ,type : arg_type
            ,work_title : '기능'				
        });

    }
    , actionHandler : function( arg_data ) {
        
        /**
            항목 / 기능 버튼 동작 처리
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
                        service_code : this.service_code
                        , mode : mode
                        , title : target_val         
                        , item_code : this.edit_code         
                        , item_group : this.item_group_code         
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
                
                if( arg_data.work_id == 'items' ) {
                    $( target_id ).val( this.items_list[ this.edit_code ]['title'] );
                } else {
                    $( target_id ).val( this.fns_list[ this.edit_code ]['title'] );
                }
                
                $( target_id ).focus();

                // focus out 이벤트 등록
                this.evWriteInputFocusout( 'on', arg_data );

                $('html, body').animate({
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
                            , item_code : this.edit_code 
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
            
            if( arg_work_id == 'items' ) {

                /**
                    항목 목록 요청
                */                
                serviceHandler.getServiceItems();

            } else {
                /**
                    기능 목록 요청
                */
                serviceHandler.getServiceFns();
            }
            

        } else {

            alert( req_result.msg );

        }
    }
    , getServiceItems : function(){
        /**
            항목 목록을 요청한다.
         */
        this.requesData({
            request_data : {
                mode : 'get_items'
                , service_code : this.service_code                    
            },
            callBack : function( req_data ){
                serviceHandler.makeServiceItems( req_data );
            }
        });

    }
    , getServiceFns : function(){
        /**
            기능 목록을 요청한다.
         */
        this.requesData({
            request_data : {
                mode : 'get_fns'					
                , service_code : this.service_code 
                , item_group : this.item_group_code
            },
            callBack : function( req_data ){
                serviceHandler.makeServiceFns( req_data );
            }
        });

    }
    , makeServiceItems : function( arg_data ){
        /**
            항목 목록을 DOM에 생성한다.				
         */
        list = [];
        $('#items_area').html( '' );  

        if( arg_data.length > 0 ) {

            $.each( arg_data, function(idx, list_val ){
                
                serviceHandler.items_list[ list_val.item_code ] = {	
                    code : list_val.item_code
                    ,title : list_val.title
                };

                list.push({	
                    code : list_val.item_code
                    ,title : list_val.title
                });

            });

            $('#items_area').html( $('#tmplate_items').tmpl( list ) );  
            // $('#items_area').html( '<li>아오</li>' );  

        }

    }
    , makeServiceFns : function( arg_data ) {
        /**
            기능 목록을 DOM에 생성한다.				
         */
        list = [];
        $('#fn_area').html( '' );  
        
        if( arg_data.length > 0 ) {

            $.each( arg_data, function(idx, list_val ){
                
                serviceHandler.fns_list[ list_val.item_code ] = {	
                    code : list_val.item_code
                    ,title : list_val.title
                };

                list.push({	
                    code : list_val.item_code
                    ,title : list_val.title
                });

            });

            $('#fn_area').html( $('#tmplate_fns').tmpl( list ) );  
            
        }
    }
    , choiceItem : function( arg_code ){

        this.item_group_code = arg_code;
        $('#choice_title').html( this.items_list[ arg_code ]['title'] );
        $('#field_status_text_A').show();
        $('#field_status_text_B').hide();

        $('#fn_area').html('');
        // 기능 목록을 호출한다.
        serviceHandler.getServiceFns();
        
    }
    , evWriteInputFocusout : function( arg_flag, arg_data ) {

        target_id = '#' + arg_data.work_id + '_write';

        if( arg_flag == 'on') {
            if( arg_data.work_id == 'items' ) {
                $( target_id ).on('focusout', function(){
                    serviceHandler.itemsAction('cancel', '');
                });
            } else {
                $( target_id ).on('focusout', function(){
                    serviceHandler.fnAction('cancel', '');
                });
            }
            
            $( target_id ).on('keyup', function(){
                serviceHandler.evWriteInputFocusout('off', arg_data);
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