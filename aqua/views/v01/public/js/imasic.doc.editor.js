;(function (window, document) {

    'use strict';

    /**
     * ---------------------------------------------------
     * i-masic editor Handler  v1.0.0
     * ---------------------------------------------------
     * 설명
     * ---------------------------------------------------
     * 
     * - imasic.doc 으로 생성된 html 과 json 파일에 접근하여 데이터 편집 처리
     * 
     * ---------------------------------------------------
     * History
     * ---------------------------------------------------
     * 
     * [v1.0.0] 2020.07.16 - 이정훈
     * 
     *  - imasic doc html 편집 툴 생성 
     *  - imasic doc html 에 접근하여 관련 doc json 데이터 편집
     * 
     * ---------------------------------------------------
    */

    var imasicAdminEditor = {
        editor_container_id : 'e_doc_edior_container'
        , html_area_id : 'imasic_html_area'        
        , editor_area_id : 'imasic_editor_area'
        , editor_tab_area_id : 'imasic_editor_tab_area'
        , editor_edit_area_id : 'imasic_editor_edit_area'
        , editor_cell_edit_area_id : 'imasic_editor_cell_edit_area'
        , editor_cell_container_id : 'imasic_editor_cell_edit_container'
        , editor_task_area_id : 'imasic_editor_task_area'
        , editor_dimm_area_id : 'imasic_editor_dimm_area'
        , editor_write_textarea_id : 'imasic_editor_write_textarea'
        , write_status : false
        , write_target : ''
        , write_before_key_code : ''
        , selected_element_info : {}
        , selected_status : false
        , selected_bgcolor : '#f4f7f9'
        , dimm : {}
        , edit_merge_status : false
        , std_row_info : {child : []}
        , cell_set_target_info : {}
        , cell_copyed : ''
        , task_stack : []
        , doc_valid_val : 'blank'
        , init_data : {
            target_id : ''
            , return_data_id : ''
            , doc_data : {}
            , set_head : false
            , set_head_type : ''
        }
    }

    imasicAdminEditor.init = function( arg_init_data ){
        
        if( typeof( arg_init_data ) == 'object' ) {

            this.selected_element_info = [];
            this.selected_status = false;
            this.edit_merge_status = false;
            
            //# 초기화 값 대입
            for( var init_key in arg_init_data ) {
                if( this.init_data.hasOwnProperty( init_key ) == true ) {
                    this.init_data[ init_key ] = arg_init_data[ init_key ];
                }
            }

            //# 기존 doc json 데이터 대입
            if( arg_init_data.doc_data ) {
                this.doc_data = arg_init_data.doc_data;
            }
            
            //# html 삽입 영역 확인
            if( document.getElementById( this.init_data.target_id ) == null ) {
                console.error('imasic.init > target_id 값이 없습니다.');
                return;
            } 

            //# 생성된 json 데이터 전달 input 확인
            if( document.getElementById( this.init_data.return_data_id ) == null ) {
                console.error('imasic.init > return_data_id 값이 없습니다.');
                return;
            } 

            this.run();

        } else {
            console.error('imasic.init > 파라미터 값은 객체형태로 설정해야합니다.');
        }
       
    }

    imasicAdminEditor.run = function(){

        var param_data = {};

        //# 에디터 생성
        this.createEditor();
        
        //# 문서 초기화
        imasic.init({
            target_id : this.html_area_id
            , return_data_id : this.init_data.return_data_id
            , doc_data : this.init_data.doc_data
            , set_head : this.init_data.set_head
            , set_head_type : this.init_data.set_head_type
        });

        //# 현재 doc 문서 저장        
        this.task_stack.push( JSON.stringify( imasic.doc_data ) );
        
        //# 행열 추가 초기값 세팅
        this.setStdRows();

        this.domEventHandler({
            selector_type : 'class'
            ,selector : imasic.table_class
            ,event : 'dragover'
            ,fn : this.dragoverHandler
        });

        this.domEventHandler({
            selector_type : 'class'
            ,selector : imasic.table_class
            ,event : 'dragend'
            ,fn : this.dragendHandler
        });

        //# 테이블 td에 이벤트를 등록한다.
        for(var item of document.querySelectorAll('.'+ imasic.table_class +' th, .'+ imasic.table_class +' td') ) {
            param_data['event'] = 'mousedown';
            param_data['fn'] = this.mouseDownHandler;
            param_data['object'] = item;
            this.addDomEvent( param_data );
        }

        this.setEditorPosition();

    }

    /**
     * 편집 영역 및 html 영역을 생성한다.
     */
    imasicAdminEditor.createEditor = function(){
        
        var menu_item_obj = '';
        var return_task_active = '';
        
        if( imasicAdminEditor.task_stack.length > 0 ) {
            return_task_active = 'e_doc_btn_active';
        }

        var edit_function = [
            {type : 'menu', title : '제목+결재 추가 초기화' ,id : '_doc_add_approval_head', class:'__doc_tb_menu_item_set e_doc_edit_btn e_doc_btn_active', event_fn : this.docAddApprovalHandler }
            ,{type : 'menu', title : '일반제목 추가 초기화' ,id : '_doc_add_title_head', class:'__doc_tb_menu_item_set e_doc_edit_btn e_doc_btn_active', event_fn : this.docAddTitleHandler }
            ,{type : 'menu', title : '되돌리기' ,id : '_doc_return_task', class:'__doc_tb_menu_item_set e_doc_edit_btn ' +return_task_active , event_fn : this.docReturnTaskHandler }
            ,{type : 'hr', title : '' ,id : '', class : '__doc_tb_menu_item_line' }
            ,{type : 'menu', title : '편집' ,id : '_doc_write', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docWriteHandler }            
            ,{type : 'menu', title : '내용삭제' ,id : '_doc_content_del', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docContentDelHandler }            
            ,{type : 'menu', title : 'file image 삽입' ,id : '_doc_insert_file', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docInsertFileHandler }
            ,{type : 'hr', title : '' ,id : '', class : '__doc_tb_menu_item_line' }
            ,{type : 'menu', title : 'th 로 변경' ,id : '_doc_change_th', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docChangeThHandler }
            ,{type : 'menu', title : 'td 로 변경' ,id : '_doc_change_td', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docChagneTdHandler }            
            ,{type : 'hr', title : '' ,id : '', class : '__doc_tb_menu_item_line' }
            ,{type : 'menu', title : '너비 변경' ,id : '_doc_edit_width', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docEditWidthHandler }
            ,{type : 'menu', title : '높이 변경' ,id : '_doc_edit_height', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docEditHeightHandler }                   
            ,{type : 'menu', title : 'style 변경' ,id : '_doc_change_style', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docChangeStyleHandler }          
            ,{type : 'hr', title : '' ,id : '', class : '__doc_tb_menu_item_line' }
            ,{type : 'menu', title : '좌측 정렬' ,id : '_doc_edit_align_left', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docEditAlignLeftHandler }
            ,{type : 'menu', title : '가운데 정렬' ,id : '_doc_edit_align_center', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docEditAlignCenterHandler }                   
            ,{type : 'menu', title : '우측 정렬' ,id : '_doc_edit_align_right', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docEditAlignRightHandler }            
            ,{type : 'hr', title : '' ,id : '', class : '__doc_tb_menu_item_line' }
            ,{type : 'menu', title : '병합' ,id : '_doc_merge', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docMergeHandler }
            ,{type : 'menu', title : '병합해제' ,id : '_doc_merge_cancel', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docMergeCancelHandler }
            ,{type : 'hr', title : '' ,id : '', class : '__doc_tb_menu_item_line' }
            ,{type : 'menu', title : '행 삽입' ,id : '_doc_add_row_up', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docAddRowUpHandler }            
            ,{type : 'menu', title : '행 삭제' ,id : '_doc_remove_row', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docRemoveRowHandler }
            // ,{type : 'hr', title : '' ,id : '', class : '__doc_tb_menu_item_line' }
            // ,{type : 'menu', title : '열 삽입' ,id : '_doc_add_col', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docAddColHandler }
            // ,{type : 'menu', title : '열 삭제' ,id : '_doc_remove_col', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docRemoveColHandler }
            ,{type : 'hr', title : '' ,id : '', class : '__doc_tb_menu_item_line' }
            ,{type : 'menu', title : '복사' ,id : '_doc_cell_copy', class:'__doc_tb_menu_item e_doc_edit_btn', event_fn : this.docCellCopyHandler }
            ,{type : 'menu', title : '붙여넣기' ,id : '_doc_cell_paste', class:'__doc_tb_menu_item e_doc_edit_btn ', event_fn : this.docCellPasteHandler }
        ];

        var tab_function = [
            {type : 'menu', title : '문서편집' ,id : '_e_doc_btn_edit', class : 'e_doc_btn e_doc_btn_blue', event_fn : this.editorTabEditHandler }
            // ,{type : 'menu', title : '작업편집' ,id : 'e_doc_btn_task', class : 'e_doc_btn', event_fn : this.editorTabTaskHandler }
        ];

        this.createEditorStyle();

        //# 에디터 전체 영역 생성
        var html_container = imasic.createHtml({ 
            tag : 'div' 
            ,attr : {
                id : this.editor_container_id
                ,width : '100%'
            } 
        });

        //# 에디터 문서영역 생성
        var html_doc_area = imasic.createHtml({ 
            tag : 'div' 
            ,attr : {
                id : this.html_area_id                
            } 
        });

        //# 에디터 편집영역 생성
        var html_editor_area = imasic.createHtml({ 
            tag : 'div' 
            ,attr : {
                id : this.editor_area_id                
            } 
        });

        //# 에디터 편집영역 > 작업 tab 영역 생성
        var html_editor_tab_area = imasic.createHtml({ 
            tag : 'div' 
            ,attr : {
                id : this.editor_tab_area_id                
            } 
        });

        //# 에디터 편집영역 > 편집기 영역 생성
        var html_editor_edit_area = imasic.createHtml({ 
            tag : 'div' 
            ,attr : {
                id : this.editor_edit_area_id
                // ,display: 'none'
            } 
        });

        //# 에디터 편집영역 > 문서 작업처리 편집 영역 생성
        var html_editor_task_area = imasic.createHtml({ 
            tag : 'div' 
            ,attr : {
                id : this.editor_task_area_id
            }
            ,child : [{text:'여기는 작업 등록처리 편집영역'}] 
        });
        
        //# tab 버튼 생성
        for(var item of tab_function ) {

            if( item.type == 'menu' ) {

                menu_item_obj = imasic.createHtml({
                    tag : 'button'
                    , attr : {
                        type : 'button'
                        ,id : item.id
                        ,class : item.class
                    }
                    ,child : [{text:item.title}]
                });

                html_editor_tab_area.appendChild( menu_item_obj );
            
            } 

        }

        //# 메뉴 항목 생성
        for(var item of edit_function ) {

            if( item.type == 'menu' ) {
                menu_item_obj = imasic.createHtml({
                    tag : 'button'
                    , attr : {
                        type : 'button'
                        ,id : item.id
                        ,class : item.class
                    }
                });

                menu_item_obj.appendChild(imasic.createHtml({
                    tag : 'span'
                    , child : [{text : item.title}]
                }));
            
            } else {
                
                menu_item_obj = imasic.createHtml({
                    tag : 'div'
                    , attr : {
                        class : item.class
                    }
                });

                menu_item_obj.appendChild(imasic.createHtml({
                    tag : 'hr'
                }));

            }
            
            //# 메뉴 box 에 추가
            html_editor_edit_area.appendChild( menu_item_obj );
        }

        //# 에디터 편집영역 > dimm 영역 추가 
        var html_editor_dimm_area = imasic.createHtml({ 
            tag : 'div' 
            ,attr : {
                id : this.editor_dimm_area_id       
            } 
        });

        //# cell 편집 영역 생성
        var html_editor_cell_edit_area = imasic.createHtml({ 
            tag : 'div' 
            ,attr : {
                id : this.editor_cell_edit_area_id
                ,display : 'none'
            } 
        });

                
        html_editor_area.appendChild( html_editor_tab_area ); //# 에디터 tab 영역 추가 
        html_editor_area.appendChild( html_editor_edit_area ); //# 에디터 edit 버튼 영역 추가
        html_editor_area.appendChild( html_editor_cell_edit_area ); //# 에디터 내용 작성 영역 추가
        html_editor_area.appendChild( html_editor_task_area ); //# 에디터 문서작업 버튼 영역 추가
        html_editor_area.appendChild( html_editor_dimm_area ); //# 에디터 dimm 영역 추가

        html_container.appendChild( html_doc_area );    //# 문서 영역 추가
        html_container.appendChild( html_editor_area ); //# 에디터 영역 추가

        //# 에디터 DOM 추가
        document.getElementById( this.init_data.target_id ).innerHTML = ''; 
        document.getElementById( this.init_data.target_id ).appendChild( html_container );

        /*******************************************************
         * 에디터 이벤트 등록
        *******************************************************/            

        //# 에디터 버튼 이벤트 등록
        var event_arr = tab_function.concat(edit_function);
        for( var item of event_arr ) {
                
            if( item.type == 'menu' ) {

                this.domEventHandler({
                    selector_type : 'id'
                    ,selector : item.id
                    ,event : 'click'
                    ,fn : item.event_fn
                });

            }
            
        }

        //# scroll 이벤트 등록
        window.addEventListener( 'scroll', imasicAdminEditor.setEditorPosition);

    }

    

    /**
     * cell 편집 > child drop 이벤트
     * @param {*} ev 
     */
    imasicAdminEditor.drop = function( ev ){
        ev.preventDefault();
        
        const data = ev.dataTransfer.getData("application/my-app");        

        var before_idx = document.getElementById(data).getAttribute('data-index');
        var insert_idx = ev.target.getAttribute('data-insert_idx');
        var before_childs = JSON.parse(JSON.stringify( imasicAdminEditor.getDocItem() ));
        
        var before_data = before_childs.child[ before_idx ];
        var resort_arr = [];
        
        console.log( 'before_idx : ' + before_idx );
        console.log( 'insert_idx : ' + insert_idx );
        
        before_childs.child.splice( before_idx, 1);

        console.log( before_childs.child );
      
        for(var items_idx in before_childs.child ){

            if( items_idx == insert_idx ) {
                resort_arr.push( before_data );
            } 

            resort_arr.push( before_childs.child[ items_idx ] );
            

        }

        console.log( resort_arr );

        imasicAdminEditor.getDocItem().child = resort_arr;

        ev.target.before( document.getElementById(data) );
        ev.dataTransfer.dropEffect = "move";
        ev.target.remove();
        
        imasicAdminEditor.resetEditor();
    }

    /**
     * cell 편집 > 이동 영역 이벤트 처리
     * @param {*} ev 
     */
    imasicAdminEditor.ondragover = function( ev ){
        ev.preventDefault();
        ev.dataTransfer.dropEffect = "move";
    }

    /**
     * cell 편집 > 추가 버튼 클릭 처리
     */
    imasicAdminEditor.cellAddTaskHandler = function(){

        imasicAdminEditor.dimm.show({
            type : 'add_cell_child'
            ,top : this.offsetTop
        });

    }

    /**
     * cell 편집 > 편집 버튼 클릭 처리
     */
    imasicAdminEditor.cellSettingClickHandler = function(){

        imasicAdminEditor.cell_set_target_info.current_idx = this.parentNode.getAttribute('data-index');
        imasicAdminEditor.cell_set_target_info.current_type = this.parentNode.getAttribute('data-type');
        imasicAdminEditor.cell_set_target_info.current_tag = this.parentNode.getAttribute('data-tag');
        
        //# 작업 선택 레이어 호출( 삭제, 수정 )
        imasicAdminEditor.dimm.show({
            type : 'cell_setting'
            ,top : this.offsetTop + 90
        });

    }

    /**
     * cell 편집 > child 삭제처리
     */
    imasicAdminEditor.cellChildDeltHandler = function(){        
        imasicAdminEditor.getDocItem().child.splice(imasicAdminEditor.cell_set_target_info.current_idx, 1);
        imasicAdminEditor.resetEditor();
    }

    /**
     * cell 편집 > child 유형에 맞는 편집 레이어 노출
     */
    imasicAdminEditor.cellChildEditHandler = function( arg_this ){

        
        
        var current_class = '';
        var current_id = '';
        var current_name = '';
        var current_style = '';
        var current_value = '';
        var current_valid = '';
        var current_checked = '';
        var current_for = '';                
        var current_text = '';
        var current_calendar = '';
        var current_time = '';

        var scroll_top = arg_this.parentNode.parentNode.parentNode.style.top.replace('px', '');

        if( imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].hasOwnProperty('attr') == true ) {

            if( imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr.hasOwnProperty('class') == true ) {
                current_class = imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr.class;
            }

            if( imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr.hasOwnProperty('id') == true ) {
                current_id = imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr.id;
            }

            if( imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr.hasOwnProperty('name') == true ) {
                current_name = imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr.name;
            }

            if( imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr.hasOwnProperty('style') == true ) {
                current_style = imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr.style;
            }

            if( imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr.hasOwnProperty('value') == true ) {
                current_value = imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr.value;
            }

            if( imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr.hasOwnProperty('data-doc_valid') == true ) {
                current_valid = imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr['data-doc_valid'];
            }

            if( imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr.hasOwnProperty('checked') == true ) {
                current_checked = imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr.checked;
            }

            if( imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr.hasOwnProperty('data-calendar') == true ) {
                current_calendar = imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr['data-calendar'];
            }

            if( imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr.hasOwnProperty('data-time') == true ) {
                current_time = imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr['data-time'];
            }

            if( imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr.hasOwnProperty('for') == true ) {
                current_for = imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].attr.for;
            }

        }
        
        switch( imasicAdminEditor.cell_set_target_info.current_tag ) {
            case 'input' : {

                switch( imasicAdminEditor.cell_set_target_info.current_type ) {
                    case 'text' : {}     
                    case 'radio' : {}
                    case 'checkbox' : {                        
                        imasicAdminEditor.dimm.show({
                            type : 'edit_input_attr'
                            ,mode : 'edit'
                            ,top : scroll_top
                            ,id : current_id
                            ,name : current_name
                            ,class : current_class
                            ,style : current_style
                            ,value : current_value
                            ,valid : current_valid
                            ,checked : current_checked
                            ,calendar : current_calendar
                            ,time : current_time                            
                        });
                        break;
                    }   
                    default : {
                        alert('정의되지 않은 처리값입니다.');
                    }
                }

                break;
            }
            case 'label' : {
                //# for, style, text
                
                current_text = imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].child[0].text;

                imasicAdminEditor.dimm.show({
                    type : 'edit_label_attr'
                    ,mode : 'edit'
                    ,top : scroll_top
                    ,for : current_for
                    ,text : current_text
                    ,style : current_style                    
                });

                break;
            }
            case 'textarea' : {

                imasicAdminEditor.dimm.show({
                    type : 'edit_textarea_attr'
                    ,mode : 'edit'
                    ,top : scroll_top
                    ,id : current_id
                    ,name : current_name
                    ,class : current_class
                    ,style : current_style
                    ,value : current_value
                    ,valid : current_valid
                });

                break;
            }
            case 'br' : {
                alert('수정 할 수 없는 값입니다.');
                break;
            }
            default : {
                switch( imasicAdminEditor.cell_set_target_info.current_type ) {
                    case 'text' : {
                        //# 수정용 textarea 삽입

                        imasicAdminEditor.dimm.show({
                            type : 'cell_edit_text'
                            ,top : scroll_top
                            ,current_data : imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].text
                        });

                        break;
                    }                    
                    default : {
                        alert('정의되지 않은 처리값입니다.');
                    }
                }
            }
        }
    }
    
    /**
     * 에디터 style css 생성
    */
    imasicAdminEditor.createEditorStyle = function(){

        var head_element = document.getElementsByTagName('head')[0];

        var menu_style_contents = '';			
        menu_style_contents += ' #'+ this.editor_container_id +' { ';
        menu_style_contents += '    display: inline-flex ';
        menu_style_contents += ' } ';
        menu_style_contents += ' #'+ this.html_area_id +' { ';
        menu_style_contents += '    width:810px ';
        menu_style_contents += ' } ';
        menu_style_contents += ' #'+ this.editor_area_id +' { ';
        menu_style_contents += '    width:780px; ';
        menu_style_contents += '    position:relative ';
        menu_style_contents += ' } ';
        menu_style_contents += ' #'+ this.editor_edit_area_id +' { ';
        menu_style_contents += '    padding-top:20px ';
        menu_style_contents += ' } ';
        menu_style_contents += ' #'+ this.editor_cell_edit_area_id +' { ';
        menu_style_contents += '    width:100%; ';
        menu_style_contents += '    height:100%; ';
        menu_style_contents += ' } ';
        menu_style_contents += ' #'+ this.editor_cell_container_id +' { ';
        menu_style_contents += '    width: 95%; ';
        menu_style_contents += '    min-height: 630px; ';
        menu_style_contents += '    margin: 5px auto; ';
        menu_style_contents += '    padding-bottom:10px; ';
        menu_style_contents += '    background-color: #fff; ';
        menu_style_contents += '    -webkit-border-radius: 2px; ';
        menu_style_contents += '    -moz-border-radius: 2px; ';
        menu_style_contents += '    border-radius: 2px; ';
        menu_style_contents += '    box-shadow: 0 5px 10px 0 rgba(0,0,0,.24),0 5px 10px 0 rgba(0,0,0,.19)!important; ';
        menu_style_contents += ' } ';
        menu_style_contents += ' #'+ this.editor_task_area_id +' { ';
        menu_style_contents += '    height:100px; ';
        menu_style_contents += '    border: 1px solid #ddd;';
        menu_style_contents += '    display:none';
        menu_style_contents += ' } ';
        menu_style_contents += ' #'+ this.editor_dimm_area_id +' { ';
        menu_style_contents += '	position: absolute;';        
        menu_style_contents += '	top: 0px;';        
        menu_style_contents += '	left: 0px;';        
        menu_style_contents += '	width: 100%;';        
        menu_style_contents += '	height: 100%;';        
        menu_style_contents += '	z-index: 10;';        
        menu_style_contents += '	background-color: #333;';        
        menu_style_contents += '	opacity: 0.6;';        
        menu_style_contents += '	display: none;';        
        menu_style_contents += ' }';
        menu_style_contents += ' #'+ this.editor_edit_area_id +' hr { ';
        menu_style_contents += '	margin:0px;  ';
        menu_style_contents += ' }';
        menu_style_contents += ' #'+ this.editor_write_textarea_id +' { ';
        menu_style_contents += '	width:90%;  ';
        menu_style_contents += '	height:90%;  ';
        menu_style_contents += '	border:none;  ';
        menu_style_contents += '	resize:none;  ';
        menu_style_contents += '	overflow:hidden;  ';
        menu_style_contents += ' }';
        menu_style_contents += ' .cell_item_box {';
        menu_style_contents += '    width:100%;';
        menu_style_contents += '    position:relative;';
        menu_style_contents += ' }';
        menu_style_contents += ' .cell_items {';        
        menu_style_contents += '    heigth:40px;';        
        menu_style_contents += '    margin:10px;';        
        menu_style_contents += '    line-height: 2.5;';
        menu_style_contents += ' }';
        menu_style_contents += ' .cell_items:hover {';        
        menu_style_contents += '    box-shadow:0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12);';        
        menu_style_contents += '    transition: box-shadow 100ms cubic-bezier(0.4, 0.0, 0.2, 1);'; 
        menu_style_contents += ' }';
        menu_style_contents += ' .cell_items_move_box {';
        menu_style_contents += '    display: table-cell;';
        menu_style_contents += '    width:8%;';        
        menu_style_contents += '    text-align:center;';        
        menu_style_contents += '    border-right:1px solid #eee;';
        menu_style_contents += '    cursor:n-resize;';        
        menu_style_contents += '    visibility:hidden;';        
        menu_style_contents += ' }';
        menu_style_contents += ' .cell_items_move_content {';
        menu_style_contents += '    display: table-cell;';        
        menu_style_contents += '    width:100%;';        
        menu_style_contents += '    font-weight: bold;';        
        menu_style_contents += '    font-size: 16px;';        
        menu_style_contents += '    padding-left:10px;';
        menu_style_contents += ' }';        
        menu_style_contents += ' .cell_items_setting_box {';
        menu_style_contents += '    display: table-cell;';        
        menu_style_contents += '    width:4%;';        
        menu_style_contents += '    text-align:center;';        
        menu_style_contents += '    border-left:1px solid #eee;';        
        menu_style_contents += '    padding-left:10px;';        
        menu_style_contents += '    padding-right:10px;';        
        menu_style_contents += '    cursor:pointer;';        
        menu_style_contents += '    visibility:hidden;';        
        menu_style_contents += ' }';
        
        menu_style_contents += ' .cell_add_task {';        
        menu_style_contents += '    heigth:40px;';        
        menu_style_contents += '    margin:10px;';        
        menu_style_contents += '    line-height: 2.5;';
        menu_style_contents += '    cursor:pointer;';
        menu_style_contents += ' }';
        menu_style_contents += ' .cell_add_task:hover {';        
        menu_style_contents += '    box-shadow:0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12);';        
        menu_style_contents += '    transition: box-shadow 100ms cubic-bezier(0.4, 0.0, 0.2, 1);'; 
        menu_style_contents += ' }';

        menu_style_contents += ' .e_doc_btn {';
        menu_style_contents += '    padding:0.5rem 1.5rem 0.2rem;';
        menu_style_contents += '    font-size:12px;';
        menu_style_contents += '    font-weight:400;';
        menu_style_contents += '    background-color: rgba(218, 230, 236, 0.3);';
        menu_style_contents += '    text-align: center;';
        menu_style_contents += '    border: 1px solid #ddd;';
        menu_style_contents += '    border-radius: 2px;';
        menu_style_contents += ' }';
        menu_style_contents += ' .e_doc_edit_btn {';
        menu_style_contents += '    margin:3px;';
        menu_style_contents += '    padding:0.5rem 1.5rem 0.2rem;';
        menu_style_contents += '    font-size:16px;';
        menu_style_contents += '    font-weight:400;';
        menu_style_contents += '    background-color: rgba(218, 230, 236, 0.3);';
        menu_style_contents += '    text-align: center;';
        menu_style_contents += '    border: 1px solid #ddd;';
        menu_style_contents += '    border-radius: 2px;';
        menu_style_contents += ' }';
        menu_style_contents += ' .e_doc_btn_blue {';
        menu_style_contents += '    color: #dcdcdc;';
        menu_style_contents += '    background-color: #1e3c75;';
        menu_style_contents += ' }';
        menu_style_contents += ' .e_doc_btn_active {';
        menu_style_contents += '    color: #dcdcdc;';
        menu_style_contents += '    background-color: #4285f4;';
        menu_style_contents += ' }';        
        menu_style_contents += ' .__doc_tb_menu_box { ';
        menu_style_contents += '		position:absolute; ';
        menu_style_contents += '		width:150px; ';
        menu_style_contents += '		top:400px; ';
        menu_style_contents += '		left:350px; ';
        menu_style_contents += '		z-index:10; ';
        menu_style_contents += '		background-color:#fff; ';
        menu_style_contents += '		border: 1px solid transparent;  ';
        menu_style_contents += '		border-radius: 4px;   ';
        menu_style_contents += '		box-shadow: 0 2px 6px 2px rgba(60,64,67,.15); ';
        menu_style_contents += '		display: none ';
        menu_style_contents += ' }';        
        menu_style_contents += ' .__doc_tb_menu_item { ';
        menu_style_contents += '	margin:10px 0 10px 10px; ';
        menu_style_contents += ' }';
        menu_style_contents += ' .__doc_tb_menu_item_set { ';
        menu_style_contents += '	margin:10px 0 10px 10px; ';
        menu_style_contents += ' }';
        menu_style_contents += ' .__doc_tb_menu_item_line { ';
        menu_style_contents += ' 	margin:7px 8px 7px 12px; ';
        menu_style_contents += ' }';
        menu_style_contents += ' button:disabled { ';
        menu_style_contents += '	cursor:no-drop  ';
        menu_style_contents += ' }';
        menu_style_contents += ' .edit_guide { ';
        menu_style_contents += '	position: absolute; ';
        menu_style_contents += '	top: 0px;  ';
        menu_style_contents += '	margin: 30% auto;  ';
        menu_style_contents += '	width:100%;  ';
        menu_style_contents += '	height:100px;  ';
        menu_style_contents += '	text-align:center;  ';
        menu_style_contents += '	z-index:11;  ';
        menu_style_contents += '	font-size:30px;  ';
        menu_style_contents += '	color:#fff;  ';
        menu_style_contents += ' }';
        menu_style_contents += ' .dimm_container { ';
        menu_style_contents += '	position: absolute; ';
        menu_style_contents += '	top: 10%;  ';
        menu_style_contents += '	width: 100%;  ';
        menu_style_contents += '	z-index:11;  ';
        menu_style_contents += ' }';
        menu_style_contents += ' .dimm_box { ';
        menu_style_contents += '	width: 95%; ';
        menu_style_contents += '	padding: 15px;  ';
        menu_style_contents += '	background-color: #fff;  ';
        menu_style_contents += '	margin: 0 auto;  ';
        menu_style_contents += ' }';
        menu_style_contents += ' col { ';
        menu_style_contents += '	display: table-column; ';
        menu_style_contents += ' }';


        //# 메뉴 style 태그 생성
        var menu_style_obj = imasic.createHtml({
            tag : 'style'            
            , attr : {
                id : '_doc_editor_style'
            }
            ,child : [{text : menu_style_contents}]
        });
        
        //# 메뉴 스타일 태그 삽입
        head_element.appendChild( menu_style_obj );

    }

    /**
     * 일반 제목 입력 초기화
    */
    imasicAdminEditor.docAddTitleHandler = function(){
        if( confirm('제목 영역을 추가합니다.\n\n기존 작성된 문서가 초기화됩니다\n\n추가하시겠습니까?') == true ) {

            imasicAdminEditor.init_data.set_head = true;
            imasicAdminEditor.init_data.set_head_type = 'only_title';

            imasicAdminEditor.init({
                target_id : imasicAdminEditor.init_data.target_id
                , return_data_id : imasicAdminEditor.init_data.return_data_id  
                , doc_data : {}
                , set_head : imasicAdminEditor.init_data.set_head
                , set_head_type : imasicAdminEditor.init_data.set_head_type
            });
        }
    }

    /**
     * 제목 + 결제란 입력 초기화
    */
    imasicAdminEditor.docAddApprovalHandler = function(){
        if( confirm('제목+결재 영역을 추가합니다.\n\n기존 작성된 문서가 초기화됩니다\n\n추가하시겠습니까?') == true ) {

            imasicAdminEditor.init_data.set_head = true;
            imasicAdminEditor.init_data.set_head_type = 'approval';

            imasicAdminEditor.init({
                target_id : imasicAdminEditor.init_data.target_id
                , return_data_id : imasicAdminEditor.init_data.return_data_id  
                , doc_data : {}
                , set_head : imasicAdminEditor.init_data.set_head
                , set_head_type : imasicAdminEditor.init_data.set_head_type
            });
        }
    }

    /**
     * 이전 작업 상태로 문서를 되돌린다.
     */
    imasicAdminEditor.docReturnTaskHandler = function(){
        
        var task_stack_len = imasicAdminEditor.task_stack.length - 2;

        if( task_stack_len > -1 ) {

            imasic.doc_data = JSON.parse( imasicAdminEditor.task_stack[ ( task_stack_len ) ] );
            
            imasicAdminEditor.task_stack.pop();
            imasicAdminEditor.task_stack.pop();
            
            imasicAdminEditor.resetEditor();

        } else {
            // alert('더이상 되돌릴 작업이 없습니다.');
        }

    }

    /**
     * cell 편집 레이어를 닫는다.
     */
    imasicAdminEditor.docWriteCancel = function(){
        document.getElementById( imasicAdminEditor.editor_edit_area_id ).style.display = 'block';
        document.getElementById( imasicAdminEditor.editor_cell_edit_area_id ).style.display = 'none';
    }

    /**
     * cell 내용을 수정 할 수 있게 레이어 노출
    */
    imasicAdminEditor.docWriteHandler = function(){
        
        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }

        var get_child = [];
        var insert_text = '';
        var data_tag = '';
        var data_type = '';


        document.getElementById( imasicAdminEditor.editor_cell_edit_area_id ).innerHTML = '';

        //# 편집 컨테이너 생성
        var html_editor_cell_edit_container = imasic.createHtml({ 
            tag : 'div' 
            ,attr : {
                id : imasicAdminEditor.editor_cell_container_id       
            }
            ,child : [
                { 
                    tag : 'div' 
                    ,attr : {
                        class : 'imasic_editor_cell_edit_area_top'
                    }
                    ,child : [
                        {
                            tag : 'button'
                            , attr : {
                                class : 'e_doc_edit_btn'
                                ,type : 'button'
                                ,onclick : 'imasicAdminEditor.docWriteCancel()'
                            }
                            ,child : [
                                {text:'닫기'}
                            ]
                        }
                    ]
                }
                ,{
                    tag : 'div' 
                    ,attr : {
                        class : 'cell_item_box'                        
                    } 
                }
            ]
        });

        if( imasicAdminEditor.getDocItem().hasOwnProperty('child') == true ) {

            get_child = imasicAdminEditor.getDocItem().child;
            

            for(var child_idx in get_child ){
                
                if( get_child[ child_idx ].hasOwnProperty('tag') == true ) {
                    if( get_child[ child_idx ].hasOwnProperty('attr') == true ) {
                        if( get_child[ child_idx ].attr.hasOwnProperty('type') == true ) {
                            insert_text = 'TAG[' + get_child[ child_idx ].tag + ' : ' + get_child[ child_idx ].attr.type + ']';
                            data_tag = get_child[ child_idx ].tag;
                            data_type = get_child[ child_idx ].attr.type;
                        } else {
                            insert_text = 'TAG[' + get_child[ child_idx ].tag + ']';
                            data_tag = get_child[ child_idx ].tag;
                            data_type = '';
                        }
                    } else {
                        insert_text = 'TAG[' + get_child[ child_idx ].tag + ']';
                        data_tag = get_child[ child_idx ].tag;
                        data_type = '';
                    }
                    
                } else {
                    insert_text = get_child[ child_idx ].text;
                    data_tag = '';
                    data_type = 'text';
                }

                var cell_edit_items = imasic.createHtml({ 
                    tag : 'div' 
                    ,attr : {
                        class : 'cell_items'
                        ,draggable : 'true'
                        ,id : 'drag_item_' + child_idx
                        ,'data-index' : child_idx
                        ,'data-tag' : data_tag
                        ,'data-type' : data_type
                    }
                    ,child : [
                        {
                            tag : 'div' 
                            ,attr : {
                                class : 'cell_items_move_box'                                
                            }
                            ,child : [
                                {
                                    tag: 'span'
                                    ,attr : {
                                        class : 'glyphicon glyphicon-resize-vertical'
                                    }
                                }
                            ]
                        }
                        , {
                            tag : 'div' 
                            ,attr : {
                                class : 'cell_items_move_content'
                            }
                            ,child : [
                                {
                                    text : insert_text
                                }
                            ]
                        }
                        , {
                            tag : 'div' 
                            ,attr : {
                                class : 'cell_items_setting_box'
                            }
                            ,child : [
                                {
                                    tag: 'span'
                                    ,attr : {
                                        class : 'glyphicon glyphicon-option-horizontal'
                                    }
                                }
                            ]
                        }
                    ]
                });
    
                html_editor_cell_edit_container.childNodes[1].appendChild( cell_edit_items );


            }
           
        }
        
            
        var html_editor_cell_edit_add_task = imasic.createHtml({ 
            tag : 'div' 
            ,attr : {
                class : 'cell_add_task'
            }
            ,child : [
                {
                    tag : 'div' 
                    ,attr : {
                        class : 'cell_items_move_content'
                    }
                    ,child : [
                        {
                            tag : 'span' 
                            ,attr : {
                                class : 'glyphicon glyphicon-plus'
                            }
                        }
                        ,{
                            text : '추가' 
                        }
                    ] 
                }
            ]
        });
        
        
        html_editor_cell_edit_container.appendChild( html_editor_cell_edit_add_task );

        document.getElementById( imasicAdminEditor.editor_cell_edit_area_id ).appendChild( html_editor_cell_edit_container );


        /**************************** 이벤트 등록 ********************************/ 

        if( get_child.length > 0 ){ 
            //# cell 편집 내용 mouseover 이벤트 
            imasicAdminEditor.domEventHandler({
                selector_type : 'class'
                ,selector : 'cell_items'
                ,event : 'mouseover'
                ,fn : function(){                
                    this.children[0].style.visibility = 'visible';
                    this.children[2].style.visibility = 'visible';
                }
            });

            //# cell 편집 내용 mouseout 이벤트 
            imasicAdminEditor.domEventHandler({
                selector_type : 'class'
                ,selector : 'cell_items'
                ,event : 'mouseout'
                ,fn : function(){
                    this.children[0].style.visibility = 'hidden';
                    this.children[2].style.visibility = 'hidden';
                }
            });

            //# cell 편집 dragstart 이벤트 처리자
            imasicAdminEditor.domEventHandler({
                selector_type : 'class'
                ,selector : 'cell_items'
                ,event : 'dragstart'
                ,fn : function(ev){                
                    ev.dataTransfer.setData("application/my-app", ev.target.id);                           
                    ev.dataTransfer.setDragImage( ev.target ,0,0);
                    // ev.dataTransfer.dropEffect = "move";

                }
            });

            //# cell 편집 dragover 이벤트 처리자
            imasicAdminEditor.domEventHandler({
                selector_type : 'class'
                ,selector : 'cell_items'
                ,event : 'dragover'
                ,fn : function(ev){     
                    
                    ev.preventDefault();

                    const data = ev.dataTransfer.getData("application/my-app");

                    if( document.getElementById('drop_item') ) {
                        document.getElementById('drop_item').remove();
                    }
                    
                    var drop_area = imasic.createHtml({
                        tag : 'div'
                        ,attr : {
                            id : 'drop_item'                        
                            , width: '100%'
                            , height: '40px'
                            , backgroundColor: '#ccc'
                            , ondrop: 'imasicAdminEditor.drop( event )'
                            , ondragover: 'imasicAdminEditor.ondragover( event )'
                        }
                    });  

                    var current_idx = Number( this.getAttribute('data-index') );

                    if( ev.offsetY < ( this.clientHeight / 2 ) ) {
                        this.before(  drop_area );
                        document.getElementById('drop_item').setAttribute('data-insert_idx', current_idx );
                    } else {                 
                        this.after( drop_area );
                        document.getElementById('drop_item').setAttribute('data-insert_idx', (current_idx + 1) );
                    }
                }
            });

            //# cell 편집 > 편집버튼 click 이벤트 처리자
            imasicAdminEditor.domEventHandler({
                selector_type : 'class'
                ,selector : 'cell_items_setting_box'
                ,event : 'click'
                ,fn : imasicAdminEditor.cellSettingClickHandler
            });
        }

        //# cell 편집 > 작업추가버튼 click 이벤트 처리자
        imasicAdminEditor.domEventHandler({
            selector_type : 'class'
            ,selector : 'cell_add_task'
            ,event : 'click'
            ,fn : imasicAdminEditor.cellAddTaskHandler
        });

        /**************************** 이벤트 등록 ********************************/ 

        document.getElementById( imasicAdminEditor.editor_edit_area_id ).style.display = 'none';
        document.getElementById( imasicAdminEditor.editor_cell_edit_area_id ).style.display = 'block';
       
    }

    /**
     * 작성용 textarea keyup 이벤트 처리를 한다.
    */
    imasicAdminEditor.writeTextareaKeydownHandler = function( arg_event ){        
        
        console.log(arg_event.keyCode);
        if( (imasicAdminEditor.write_before_key_code != 16 ) && ( arg_event.keyCode == 13 ) ) {
            imasicAdminEditor.writeTextareaDoneHandler();
        } else {            
            imasicAdminEditor.write_before_key_code = arg_event.keyCode;
        }

    }

    /**
     * 작성 textarea 값을 td json 값에 대입하고 입력 상태 변경
    */
    imasicAdminEditor.writeTextareaDoneHandler = function(){

        var text_value = document.getElementById( imasicAdminEditor.editor_write_textarea_id ).value;

        // console.log( text_value );
        // console.log( text_value.replace(/\n/g, '\\n') );

        text_value = text_value.replace(/\n/g, '<br>');

        if( text_value !== '' ) {
            if( imasicAdminEditor.getDocItem().hasOwnProperty('child') == true ) {
                //# 내용을 추가한다.
                imasicAdminEditor.getDocItem().child.push({
                    text : text_value
                });
            } else {
                imasicAdminEditor.getDocItem().child = [];
                imasicAdminEditor.getDocItem().child.push({
                    text : text_value
                });
            }
        }
        

        imasicAdminEditor.write_status = false;
        imasicAdminEditor.resetEditor();
        
    }

    /**
     * br tag 추가
     */
    imasicAdminEditor.docInsertCarriageReturnHandler = function(){
        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }

        imasicAdminEditor.getDocItem().child.push({
            tag : 'br'
        });
        
        imasicAdminEditor.resetEditor();
    }

    /**
     * cell 내용 초기화
     */
    imasicAdminEditor.docContentDelHandler = function(){
        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }

        imasicAdminEditor.getDocItem().child = [];
        
        imasicAdminEditor.resetEditor();

    }

    /**
     * input 속성을 입력 받을 수 있는 레이어 노출
     */
    imasicAdminEditor.docInsertInputHandler = function( arg_this ){

        imasicAdminEditor.cell_set_target_info.current_type = 'text';

        imasicAdminEditor.dimm.show({
            type : 'edit_input_attr'
            ,mode : 'add'
            ,top : arg_this.parentNode.parentNode.parentNode.style.top
        });
        
    }

    /**
     * 선택된 cell 에 input tag 삽입
     */
    imasicAdminEditor.docInsertInput = function( arg_mode ){
        
        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }

        var cell_edit_input_class = '';
        var cell_edit_input_style = '';
        var cell_edit_input_value = '';
        var cell_edit_input_name = '';
        var cell_edit_input_id = '';
        var cell_edit_input_checked = '';
        var cell_edit_input_valid = '';
        var cell_edit_input_calendar = '';
        var cell_edit_input_time = '';
        var input_class = '';
        var input_calendar = '__calendar';
        var input_time = '__time';
        var insert_data = {};

        cell_edit_input_class = document.getElementById('cell_edit_input_class').value;
        cell_edit_input_style = document.getElementById('cell_edit_input_style').value;
        cell_edit_input_value = document.getElementById('cell_edit_input_value').value;
        cell_edit_input_name = document.getElementById('cell_edit_input_name').value;
        cell_edit_input_id = document.getElementById('cell_edit_input_id').value;
        cell_edit_input_checked = document.getElementById('cell_edit_input_checked').checked;
        cell_edit_input_valid = document.getElementById('cell_edit_input_valid').checked;
        cell_edit_input_calendar = document.getElementById('cell_edit_input_calendar').checked;
        cell_edit_input_time = document.getElementById('cell_edit_input_time').checked;

        cell_edit_input_class = cell_edit_input_class.replace(input_calendar, '');
        cell_edit_input_class = cell_edit_input_class.replace(input_time, '');


        switch( imasicAdminEditor.cell_set_target_info.current_type ) {
            case 'text' : {
                input_class = ' __doc_td_inputs ';

                if( cell_edit_input_name == '') {
                    cell_edit_input_name = imasicAdminEditor.getDocItem().attr.id + '__doc_td_inputs';
                }

                break
            }
            case 'radio' : {
                input_class = ' __doc_td_radios ';

                if( cell_edit_input_name == '') {
                    cell_edit_input_name = imasicAdminEditor.getDocItem().attr.id + '__doc_td_radios';
                }

                break
            }
            case 'checkbox' : {
                input_class = ' __doc_td_checkboxs ';

                if( cell_edit_input_name == '') {
                    cell_edit_input_name = imasicAdminEditor.getDocItem().attr.id + '__doc_td_checkboxs';
                }
                break
            }
        }

        if( !( cell_edit_input_class.indexOf( input_class.trim() ) > -1 ) ) {
            cell_edit_input_class = cell_edit_input_class + input_class;
        }

        
        if( arg_mode == 'add' ) {
            //# 신규 추가 처리

            if( imasicAdminEditor.getDocItem().hasOwnProperty('child') == false ) {
                imasicAdminEditor.getDocItem().child = [];
            }

            insert_data = {
                tag : 'input'
                , attr : {
                    type : imasicAdminEditor.cell_set_target_info.current_type
                }
            };

            if( cell_edit_input_id !== '' ) {
                insert_data.attr.id = cell_edit_input_id;
            } 

            if( cell_edit_input_name !== '' ) {
                insert_data.attr.name = cell_edit_input_name;
            } 

            if( cell_edit_input_class !== '' ) {
                insert_data.attr.class = cell_edit_input_class;
            } 

            if( cell_edit_input_style !== '' ) {
                insert_data.attr.style = cell_edit_input_style;
            } 

            if( cell_edit_input_value !== '' ) {
                insert_data.attr.value = cell_edit_input_value;
            } 
            
            if( cell_edit_input_checked == true ) {
                insert_data.attr.checked = true;
            } 

            if( cell_edit_input_calendar == true ) {
                insert_data.attr.class = insert_data.attr.class + ' ' + input_calendar;
                insert_data.attr['data-calendar'] = true;
            } else {
                insert_data.attr.class = insert_data.attr.class.replace(input_calendar, '');
            }

            if( cell_edit_input_time == true ) {
                insert_data.attr.class = insert_data.attr.class + ' ' + input_time;
                insert_data.attr['data-time'] = true;
            } else {
                insert_data.attr.class = insert_data.attr.class.replace(input_time, '');
            }


            if( cell_edit_input_valid == true ) {
                insert_data.attr['data-doc_valid'] = imasicAdminEditor.doc_valid_val;
            }

            imasicAdminEditor.getDocItem().child.push( insert_data );

        } else {
            //# 수정 처리
            
            insert_data = {
                tag : 'input'
                , attr : {
                    type : imasicAdminEditor.cell_set_target_info.current_type                                   
                }
            };

            if( cell_edit_input_id !== '' ) {
                insert_data.attr.id = cell_edit_input_id;
            } 

            if( cell_edit_input_name !== '' ) {
                insert_data.attr.name = cell_edit_input_name;
            } 

            if( cell_edit_input_class !== '' ) {
                insert_data.attr.class = cell_edit_input_class;
            } 

            if( cell_edit_input_style !== '' ) {
                insert_data.attr.style = cell_edit_input_style;
            } 

            if( cell_edit_input_value !== '' ) {
                insert_data.attr.value = cell_edit_input_value;
            } 
            
            if( cell_edit_input_checked == true ) {
                insert_data.attr.checked = true;
            } 

            if( cell_edit_input_calendar == true ) {
                insert_data.attr.class = insert_data.attr.class + ' ' + input_calendar;
                insert_data.attr['data-calendar'] = true;
            } else {
                insert_data.attr.class = insert_data.attr.class.replace(input_calendar, '');
            }

            if( cell_edit_input_time == true ) {
                insert_data.attr.class = insert_data.attr.class + ' ' + input_time;
                insert_data.attr['data-time'] = true;
            } else {
                insert_data.attr.class = insert_data.attr.class.replace(input_time, '');
            }

            if( cell_edit_input_valid == true ) {
                insert_data.attr['data-doc_valid'] = imasicAdminEditor.doc_valid_val;
            }
            imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ] = insert_data;
        }

        imasicAdminEditor.resetEditor();

    }

    /**
     * radio 속성을 입력 받을 수 있는 레이어 노출
     */
    imasicAdminEditor.docInsertRadioHandler = function( arg_this ){

        imasicAdminEditor.dimm.show({
            type : 'edit_radio_attr'
            ,mode : 'add'
            ,top : arg_this.parentNode.parentNode.parentNode.style.top
        });

    }

    /**
     * 선택된 cell 에 radio tag 삽입
     */
    imasicAdminEditor.docInsertRadio = function( arg_mode ){

        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }
        var cell_edit_radio_label = '';
        var cell_edit_radio_name = '';
        var cell_edit_radio_value = '';
        var cell_edit_radio_checked = '';
        var cell_edit_radio_valid = '';
        var cell_edit_radio_class = '';        
        var radios_len = 0;
        var radio_id = '';
        var insert_data = {};

        cell_edit_radio_label = document.getElementById('cell_edit_radio_label').value;
        cell_edit_radio_name = document.getElementById('cell_edit_radio_name').value;
        cell_edit_radio_value = document.getElementById('cell_edit_radio_value').value;
        cell_edit_radio_checked = document.getElementById('cell_edit_radio_checked').checked;
        cell_edit_radio_valid = document.getElementById('cell_edit_radio_valid').checked;
        radios_len = document.getElementsByClassName('__doc_td_radios').length;

        radio_id = '__doc_td_radios_' + radios_len;

        if( !( cell_edit_radio_class.indexOf('__doc_td_radios') > -1 ) ) {
            cell_edit_radio_class = cell_edit_radio_class + ' __doc_td_radios';
        }

        if( cell_edit_radio_name == '' ) {            
            cell_edit_radio_name = radio_id;
        }

        if( arg_mode == 'add' ) {
            //# 신규 추가 처리

            if( imasicAdminEditor.getDocItem().hasOwnProperty('child') == false ) {
                imasicAdminEditor.getDocItem().child = [];
            }

            insert_data = {
                tag : 'input'
                , attr : {
                    type : 'radio'
                    ,class : cell_edit_radio_class
                    ,id : radio_id
                    ,name : cell_edit_radio_name
                    ,value : cell_edit_radio_value
                }
            }

            if( cell_edit_radio_checked == true ) {
                insert_data.attr.checked = 'checked';
            }

            if( cell_edit_radio_valid == true ) {
                insert_data.attr['data-doc_valid'] = imasicAdminEditor.doc_valid_val;
            }

            imasicAdminEditor.getDocItem().child.push( insert_data );
            imasicAdminEditor.getDocItem().child.push({
                tag : 'label'                
                , attr : {
                    for : '__doc_td_radios_' + radios_len
                    ,'data-for_type' : 'radio'
                    ,style : 'margin-left:5px'
                }
                ,child : [
                    {text : cell_edit_radio_label}
                ]
            });

        } else {
            //# 수정 처리 - 수정은 docInsertInput 으로 처리됨
         
        }

        imasicAdminEditor.resetEditor();

    }

    /**
     * checkbox 속성을 입력 받을 수 있는 레이어 노출
     */
    imasicAdminEditor.docInsertCheckboxHandler = function( arg_this ){

        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }

        imasicAdminEditor.dimm.show({
            type : 'edit_checkbox_attr'
            ,mode : 'add'
            ,top : arg_this.parentNode.parentNode.parentNode.style.top
        });

    }

    /**
     * 선택된 cell 에 checkbox tag 삽입
     */
    imasicAdminEditor.docInsertCheckbox = function( arg_mode ){

        var cell_edit_checkbox_label = '';
        var cell_edit_checkbox_name = '';
        var cell_edit_checkbox_value = '';
        var cell_edit_checkbox_checked = '';
        var cell_edit_checkbox_valid = '';
        var cell_edit_checkbox_class = '';
        var insert_data = {};
        var checkbox_len = 0;
        var checkbox_id = '';

        cell_edit_checkbox_label = document.getElementById('cell_edit_checkbox_label').value;
        cell_edit_checkbox_name = document.getElementById('cell_edit_checkbox_name').value;
        cell_edit_checkbox_value = document.getElementById('cell_edit_checkbox_value').value;        
        cell_edit_checkbox_checked = document.getElementById('cell_edit_checkbox_checked').checked;
        cell_edit_checkbox_valid = document.getElementById('cell_edit_checkbox_valid').checked;
        checkbox_len = document.getElementsByClassName('__doc_td_checkboxs').length;

        checkbox_id = '__doc_td_checkboxs_' + checkbox_len;

        if( cell_edit_checkbox_name == '' ){
            cell_edit_checkbox_name = checkbox_id;
        }

        if( !( cell_edit_checkbox_class.indexOf('__doc_td_checkboxs') > -1 ) ) {
            cell_edit_checkbox_class = cell_edit_checkbox_class + ' __doc_td_checkboxs';
        }
       
     
        if( arg_mode == 'add' ) {
            //# 신규 추가 처리

            if( imasicAdminEditor.getDocItem().hasOwnProperty('child') == false ) {
                imasicAdminEditor.getDocItem().child = [];
            }

            insert_data = {
                tag : 'input'
                , attr : {
                    type : 'checkbox'
                    ,class : cell_edit_checkbox_class
                    ,id : checkbox_id
                    ,name : cell_edit_checkbox_name
                    ,value : cell_edit_checkbox_value
                    ,'data-doc_valid' : cell_edit_checkbox_valid
                }
            }

            if( cell_edit_checkbox_checked == true ) {
                insert_data.attr.checked = 'checked';
            }

            if( cell_edit_checkbox_valid == true ) {
                insert_data.attr['data-doc_valid'] = imasicAdminEditor.doc_valid_val;
            }

            imasicAdminEditor.getDocItem().child.push( insert_data );

            if( cell_edit_checkbox_label !== '' ) {
                imasicAdminEditor.getDocItem().child.push({
                    tag : 'label'                
                    , attr : {
                        for : '__doc_td_checkboxs_' + checkbox_len
                        ,'data-for_type' : 'checkbox'
                        ,style : 'margin-left:5px'
                    }
                    ,child : [
                        {text : cell_edit_checkbox_label}
                    ]
                });
            }
            

        } else {
            //# 수정 처리 - 수정은 docInsertInput 으로 처리됨
         
        }

        imasicAdminEditor.resetEditor();

    }

    /**
     * Textarea 속성을 입력 받을 수 있는 레이어 노출
     */
    imasicAdminEditor.docInsertTextareaHandler = function( arg_this ){

        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }

        imasicAdminEditor.dimm.show({
            type : 'edit_textarea_attr'
            ,mode : 'add'
            ,top : arg_this.parentNode.parentNode.parentNode.style.top
        });

    }

    /**
     * 선택된 cell 에 textarea tag 삽입
     * @param {*} arg_mode 
     */
    imasicAdminEditor.docInsertTextarea = function( arg_mode ){
        
        var cell_edit_textarea_class = '';
        var cell_edit_textarea_style = '';
        var cell_edit_textarea_value = '';
        var cell_edit_textarea_name = '';
        var cell_edit_textarea_id = '';        
        var cell_edit_textarea_valid = '';
        var textareas_len = 0;
        var insert_data = {};
        
        cell_edit_textarea_class = document.getElementById('cell_edit_textarea_class').value;
        cell_edit_textarea_style = document.getElementById('cell_edit_textarea_style').value;
        cell_edit_textarea_value = document.getElementById('cell_edit_textarea_value').value;
        cell_edit_textarea_name = document.getElementById('cell_edit_textarea_name').value;
        cell_edit_textarea_id = document.getElementById('cell_edit_textarea_id').value;        
        cell_edit_textarea_valid = document.getElementById('cell_edit_textarea_valid').checked;
        textareas_len = document.getElementsByClassName('__doc_td_textareas').length;
      
        if( cell_edit_textarea_id == '' ) {
            cell_edit_textarea_id = '__doc_td_textareas_' + textareas_len;
        }
        
        if( cell_edit_textarea_name == '' ){
            cell_edit_textarea_name = cell_edit_textarea_id;
        }
        
        if( !( cell_edit_textarea_class.indexOf( '__doc_td_textareas' ) > -1 ) ) {
            cell_edit_textarea_class = cell_edit_textarea_class + ' __doc_td_textareas';
        }
        

        if( arg_mode == 'add' ) {
            //# 신규 추가 처리

            if( imasicAdminEditor.getDocItem().hasOwnProperty('child') == false ) {
                imasicAdminEditor.getDocItem().child = [];
            }

            insert_data = {
                tag : 'textarea'
                , attr : {                    
                    id : cell_edit_textarea_id
                    ,name : cell_edit_textarea_name
                    ,class : cell_edit_textarea_class
                    ,style : cell_edit_textarea_style
                    ,value : cell_edit_textarea_value                    
                }
            }

            if( cell_edit_textarea_valid == true ){
                insert_data.attr['data-doc_valid'] = imasicAdminEditor.doc_valid_val;
            }

            imasicAdminEditor.getDocItem().child.push( insert_data );

        } else {
            //# 수정 처리
            
            insert_data = {
                tag : 'textarea'
                , attr : {
                    id : cell_edit_textarea_id
                    ,name : cell_edit_textarea_name
                    ,class : cell_edit_textarea_class
                    ,style : cell_edit_textarea_style
                    ,value : cell_edit_textarea_value
                }
            };

            if( cell_edit_textarea_valid == true ){
                insert_data.attr['data-doc_valid'] = imasicAdminEditor.doc_valid_val;
            }

            imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ] = insert_data;
            
        }

        imasicAdminEditor.resetEditor();

    }

    /**
     * lable 정보를 변경한다.
     */
    imasicAdminEditor.docInsertLabel = function( arg_mode ){

        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }

        var cell_edit_label_for = document.getElementById('cell_edit_label_for').value;
        var cell_edit_label_style = document.getElementById('cell_edit_label_style').value;
        var cell_edit_label_text = document.getElementById('cell_edit_label_text').value;

        if( arg_mode == 'add' ) {
            //# 신규 추가 처리 ( - 추후 필요시 개발 )

        } else {
            //# 수정 처리

            imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ] = {
                tag : 'label'
                , attr : {                    
                    for : cell_edit_label_for
                    ,style : cell_edit_label_style
                }
                ,child : [{text : cell_edit_label_text}]
            };

        }

        imasicAdminEditor.resetEditor();

    }

    /**
     * 파일 업로드 영역 추가
     */
    imasicAdminEditor.docInsertFileHandler = function(){
        
        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }

        var hidden_file_len = document.getElementsByClassName('hidden_file').length;
        var hidden_file_id = 'doc_img_file_'+hidden_file_len;

        if( imasicAdminEditor.getDocItem().hasOwnProperty('child') == false ) {
            imasicAdminEditor.getDocItem().child = [];
        }
        
        imasicAdminEditor.getDocItem().child.push({ 
            tag : 'form'
            , attr : {
                class : 'photo_box_form'                
                ,method : 'post'                
                ,action : '/doc/doc_file_upload'                
                ,enctype : 'multipart/form-data'                
            }
            ,child : [{
                tag : 'div'
                , attr : {
                    class : 'photo_box'                
                }
                ,child : [
                    {
                        tag : 'span'
                        , attr : {
                            class : '__doc_img_area'                
                        }
                        , child : [
                            {
                                tag : 'img'
                                , attr : {
                                    src : '/aqua/views/v01/public/images/empty.png'                
                                    ,id : hidden_file_id + '__file_open'                
                                    ,class : '__file_open'                
                                    ,style : 'width:100%;height:100%;cursor:pointer'                
                                }
                            }
                        ]
                    }
                    ,{
                        tag : 'input'
                        , attr : {
                            type : 'file'                
                            ,class : 'hidden_file'                
                            ,name : 'doc_img_file'                
                            ,style : 'position: absolute;width: 1px;height: 1px;padding: 0;margin: -1px;overflow: hidden;clip: rect(0,0,0,0);border: 0;'                
                        }
                    }
                ]
            }]
            
        });

        imasicAdminEditor.resetEditor();

    }

    

    /**
     * th 로 변경
    */
    imasicAdminEditor.docChangeThHandler = function(){
        
        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }


        var item_id = '';
        var row = '';
        var col = '';

        for(var item of imasicAdminEditor.selected_element_info ) {
                
            item_id = item.getAttribute('id');
            
            row = item_id.split('_')[2];
            col = item_id.split('_')[3];       

            imasic.doc_data.structure.doc_data[ row ].child[col].tag = 'th';

        }

        imasicAdminEditor.resetEditor();
    }

    /**
     * td 로 변경
    */
    imasicAdminEditor.docChagneTdHandler = function(){

        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }

        var item_id = '';
        var row = '';
        var col = '';

        for(var item of imasicAdminEditor.selected_element_info ) {
                
            item_id = item.getAttribute('id');
            
            row = item_id.split('_')[2];
            col = item_id.split('_')[3];       

            imasic.doc_data.structure.doc_data[ row ].child[col].tag = 'td';

        }

        imasicAdminEditor.resetEditor();

    }

    /**
     * 너비 변경
    */
    imasicAdminEditor.docEditWidthHandler = function(){
        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }

        var input_val = prompt('너비');

        imasicAdminEditor.getDocItem().attr.width = input_val;

        imasicAdminEditor.resetEditor();
    }

    /**
     * 높이 변경
    */
    imasicAdminEditor.docEditHeightHandler = function(){
        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }

        var item_id = '';
        var row = '';
        var col = '';

        var input_val = prompt('높이');

        // imasicAdminEditor.getDocItem().attr.height = input_val;

        for(var item of imasicAdminEditor.selected_element_info ) {
                
            item_id = item.getAttribute('id');

            console.log( item_id );
            
            row = item_id.split('_')[2];
            col = item_id.split('_')[3];       
            
            if( imasic.doc_data.structure.doc_data[ row ].child[ col ].hasOwnProperty('attr') == false ) {
                imasic.doc_data.structure.doc_data[ row ].child[ col ]['attr'] = {};
            }

            imasic.doc_data.structure.doc_data[ row ].child[ col ]['attr']['height'] = input_val;

        }

        imasicAdminEditor.resetEditor();

    }

    /**
     * style 변경
    */
    imasicAdminEditor.docChangeStyleHandler = function(){

        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }

        var input_val = '';
        var current_style = '';
        var item_id = '';
        var row = '';
        var col = '';

        if( imasicAdminEditor.selected_element_info.length == 1 ) {

            if( imasicAdminEditor.getDocItem().hasOwnProperty('attr') == true ) {
                if( imasicAdminEditor.getDocItem().attr.hasOwnProperty('style') == true ){
                    current_style = imasicAdminEditor.getDocItem().attr.style;
                }
            }
    
            input_val = prompt('style', current_style );
    
            imasicAdminEditor.getDocItem().attr.style = input_val;

        } else {

            input_val = prompt('style');

            for(var item of imasicAdminEditor.selected_element_info ) {
                
                item_id = item.getAttribute('id');
                
                row = item_id.split('_')[2];
                col = item_id.split('_')[3];       
                
                if( imasic.doc_data.structure.doc_data[ row ].child[col].hasOwnProperty('attr') == false ) {
                    imasic.doc_data.structure.doc_data[ row ].child[col]['attr'] = {};
                }

                imasic.doc_data.structure.doc_data[ row ].child[col]['attr']['style'] = imasic.doc_data.structure.doc_data[ row ].child[col]['attr']['style'] + ';' + input_val + ';';

            }
            
        }
        

        imasicAdminEditor.resetEditor();
        

    }

    /** 
     * cell 좌측 정렬
    */
    imasicAdminEditor.docEditAlignLeftHandler = function(){
        

        var current_style = '';
        var item_id = '';
        var row = '';
        var col = '';

        if( imasicAdminEditor.getDocItem().hasOwnProperty('attr') == true ) {
            if( imasicAdminEditor.getDocItem().attr.hasOwnProperty('style') == true ){
                current_style = imasicAdminEditor.getDocItem().attr.style;
            }
        }

        if( current_style !== '') {
            current_style.replace('text-align : left;', '');
        }

        current_style = current_style + ' text-align : left;'

        for(var item of imasicAdminEditor.selected_element_info ) {
                
            item_id = item.getAttribute('id');
            
            row = item_id.split('_')[2];
            col = item_id.split('_')[3];       

            imasic.doc_data.structure.doc_data[ row ].child[col].attr.style = current_style;

        }


        imasicAdminEditor.resetEditor();

    }

    /** 
     * cell 가운데 정렬
    */
    imasicAdminEditor.docEditAlignCenterHandler = function(){

        var current_style = '';
        var item_id = '';
        var row = '';
        var col = '';

        if( imasicAdminEditor.getDocItem().hasOwnProperty('attr') == true ) {
            if( imasicAdminEditor.getDocItem().attr.hasOwnProperty('style') == true ){
                current_style = imasicAdminEditor.getDocItem().attr.style;
            }
        }

        if( current_style !== '') {
            current_style.replace('text-align : center;', '');
        }

        current_style = current_style + ' text-align : center;'

        for(var item of imasicAdminEditor.selected_element_info ) {
                
            item_id = item.getAttribute('id');
            
            row = item_id.split('_')[2];
            col = item_id.split('_')[3];       

            imasic.doc_data.structure.doc_data[ row ].child[col].attr.style = current_style;

        }


        imasicAdminEditor.resetEditor();
    }

    /** 
     * cell 우측 정렬
    */
    imasicAdminEditor.docEditAlignRightHandler = function(){

        var current_style = '';
        var item_id = '';
        var row = '';
        var col = '';

        if( imasicAdminEditor.getDocItem().hasOwnProperty('attr') == true ) {
            if( imasicAdminEditor.getDocItem().attr.hasOwnProperty('style') == true ){
                current_style = imasicAdminEditor.getDocItem().attr.style;
            }
        }

        if( current_style !== '') {
            current_style.replace('text-align : right;', '');
        }

        current_style = current_style + ' text-align : right;'

        for(var item of imasicAdminEditor.selected_element_info ) {
                
            item_id = item.getAttribute('id');
            
            row = item_id.split('_')[2];
            col = item_id.split('_')[3];       

            imasic.doc_data.structure.doc_data[ row ].child[col].attr.style = current_style;

        }


        imasicAdminEditor.resetEditor();

    }




    /**
     * 병합 버튼 클릭 후 변경 처리
    */
    imasicAdminEditor.docMergeHandler = function(){
        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }

        console.log('병합');

        imasicAdminEditor.edit_merge_status = true;

        imasicAdminEditor.dimm.show({            
            type : 'guide'
            ,msg : '병합 마지막 위치를 클릭하세요.'
        });
        
    }
    
    /**
     * 병합 취소
    */
    imasicAdminEditor.docMergeCancelHandler = function(){
        
        var row = 0;
        var col = 0;

        for(var item of imasicAdminEditor.selected_element_info ) {
            row = item.getAttribute('id').split('_')[2];
            col = item.getAttribute('id').split('_')[3];       
            imasic.doc_data.merge_info[ row ][col] = null;
            if( imasic.doc_data.merge_info[ row ].length == 0 ) {
                imasic.doc_data.merge_info[ row ] = null;
            }
        }

        imasicAdminEditor.resetEditor();

    }

    /**
     * 행 삽입 위
    */
    imasicAdminEditor.docAddRowUpHandler = function(){
        
        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }

        var row = Number( imasicAdminEditor.selected_element_info[0].id.split('_')[2] ); 
        
        imasicAdminEditor.docRowControllMergeHandler( 'add', row );

        imasic.doc_data.structure.doc_data.splice( ( row ), 0, imasicAdminEditor.std_row_info );

        imasic.doc_data.structure.doc_data = JSON.parse(JSON.stringify( imasic.doc_data.structure.doc_data ));
        // var test = [];
        // var loop_add = 0;
        // for(var loop_cnt in imasic.doc_data.structure.doc_data ) {
        //     // console.log( imasic.doc_data.structure.doc_data[ loop_add ] );
        //     if( row == loop_cnt ) {
        //         test.push( imasicAdminEditor.std_row_info );
        //     } else {
        //         test.push( imasic.doc_data.structure.doc_data[ loop_add ] );
        //         loop_add++;
        //     }

        // }

        // imasic.doc_data.structure.doc_data = test;

        // console.log( test );
        
        imasicAdminEditor.resetEditor();

    }

    /**
     * 행 삭제
    */
    imasicAdminEditor.docRemoveRowHandler = function(){

        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }
        
        var row = Number( imasicAdminEditor.selected_element_info[0].id.split('_')[2] ); 
        
        imasicAdminEditor.docRowControllMergeHandler( 'del', row );

        imasic.doc_data.structure.doc_data.splice( ( row ), 1 );
        
        imasic.doc_data.structure.doc_data = JSON.parse(JSON.stringify( imasic.doc_data.structure.doc_data ));

        imasicAdminEditor.resetEditor();

    }

    /**
     * 열 삽입
    */
    imasicAdminEditor.docAddColHandler = function(){
            
        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }

        var merge_data = {
            master : []
            ,slaves : []
        };
        var col = Number( imasicAdminEditor.selected_element_info[0].id.split('_')[3] );

        for( var row_item of imasic.doc_data.structure.doc_data ){            
            
            
            if( ( document.getElementById( row_item.child[col].attr.id ).getAttribute('rowspan') > 0 ) && ( document.getElementById( row_item.child[col].attr.id ).getAttribute('colspan') > 0 ) ) {
                merge_data['master'].push( row_item.child[col].attr.id );
            }

            if( document.getElementById( row_item.child[col].attr.id ).style.display == 'none' ){                
                merge_data['slaves'].push( row_item.child[col].attr.id );
            }

            row_item.child.splice( ( col ), 0, imasicAdminEditor.std_row_info.child[0] );
        }


        imasicAdminEditor.docColControllMergeHandler( 'add', col, merge_data );
        
        imasicAdminEditor.resetEditor();
    }

    /**
     * 열 삭제
    */
    imasicAdminEditor.docRemoveColHandler = function(){
        if( imasicAdminEditor.selected_status == false ) {
            alert('편집 영역을 선택해주세요.');
            return;
        }
    }



    /**
     * 행 삽입/삭제시 병합 정보 변경처리
    */
    imasicAdminEditor.docRowControllMergeHandler = function( arg_mode, arg_row_data ){

        var merge_data = {
            master : []
            ,slaves : []
        };
        var proc_number = 1;

        if( arg_mode == 'add' ) {

            //# 행추가 

            proc_number = proc_number;
            imasic.doc_data.merge_info.splice( ( arg_row_data ), 0, [] );
            imasic.doc_data.merge_info[ arg_row_data ] = JSON.parse(JSON.stringify( imasic.doc_data.merge_info[ ( arg_row_data  + ( proc_number ) ) ] ));

            for(var loop_cnt = ( arg_row_data + ( proc_number) ); loop_cnt < imasic.doc_data.merge_info.length; loop_cnt++ ) {
                
                if( imasic.doc_data.merge_info[ loop_cnt ] ) {
                    for(var merge_col of imasic.doc_data.merge_info[ loop_cnt ] ){
                    
                        if( merge_col ) {
                            if( merge_col.hasOwnProperty('row') == true ) {                                       
                                merge_col.row = Number( merge_col.row ) + ( proc_number);
                            }
                        }
                    }
                }
                
    
            }


        } else {

            //# 행 삭제

            proc_number = proc_number * -1;
            imasic.doc_data.merge_info.splice( ( arg_row_data ), 1 );

            for(var loop_cnt = ( arg_row_data ); loop_cnt < imasic.doc_data.merge_info.length; loop_cnt++ ) {
                
                if( imasic.doc_data.merge_info[ loop_cnt ] ) {
                    for(var merge_col of imasic.doc_data.merge_info[ loop_cnt ] ){
                         
                        if( merge_col ) {
                            if( merge_col.hasOwnProperty('row') == true ) {                                       
                                // console.log( loop_cnt + 'row : ' + merge_col.row );   
                                
                                merge_col.row = Number( merge_col.row ) + ( proc_number);
                            }
                        }
                    }
                }
    
            }
            
        }
        
        for(var item of document.getElementById( 'doc_tr_' + arg_row_data ).childNodes ){

            if( item.style.display == 'none' ){                
                // merge_data['slaves'].push( item.id );
                merge_data['slaves'][ item.id.split('_')[2] ] = item.id.split('_')[2];
            }
            
        }

        if( merge_data.slaves.length > 0 ){
            for(var merge_slaves in merge_data.slaves ) {
 
                find_master : for(var find_master_loop = (merge_slaves - 1); find_master_loop >= 0; find_master_loop--  ) {  
                    
                    for(var master_idx in imasic.doc_data.merge_info[ find_master_loop ] ) {
                        // console.log( imasic.doc_data.merge_info[ find_master_loop ][master_idx] );
                        if( imasic.doc_data.merge_info[ find_master_loop ][master_idx] ) {
                            if( imasic.doc_data.merge_info[ find_master_loop ][master_idx].hasOwnProperty('row') == true ) {
                                // console.log( imasic.doc_data.merge_info[ find_master_loop ][master_idx].row );
                                if( Number( imasic.doc_data.merge_info[ find_master_loop ][master_idx].row ) >= Number( merge_slaves ) ){

                                    // console.log( imasic.doc_data.merge_info[ find_master_loop ][master_idx].row );

                                    imasic.doc_data.merge_info[ find_master_loop ][ master_idx ].row = Number( imasic.doc_data.merge_info[ find_master_loop ][ master_idx ].row ) + ( proc_number );

                                    // break find_master;
                                }
                            }
                        }
                    }
                }  
            }

        }  

    }

    /**
     * 열 삽입/삭제시 병합 정보 변경처리
    */
    imasicAdminEditor.docColControllMergeHandler = function( arg_mode, arg_col_data, arg_merge_data){

        var merge_master_row = '';
        var merge_master_col = '';
        var merge_slaves_row = '';
        var merge_slaves_col = '';
        var proc_number = 1;

        if( arg_mode == 'add' ) {
            proc_number = proc_number;
        } else {
            proc_number = proc_number * -1;
        }

        console.log( arg_col_data );
        console.log( arg_merge_data );

        for(var merge_master of arg_merge_data.master ) {
                
            merge_master_row = Number( merge_master.split('_')[2] );
            merge_master_col = Number( merge_master.split('_')[3] );

            imasic.doc_data.merge_info[ merge_master_row ][merge_master_col+1] = imasic.doc_data.merge_info[ merge_master_row ][merge_master_col];
            imasic.doc_data.merge_info[ merge_master_row ][merge_master_col] = {};

            imasic.doc_data.merge_info[ merge_master_row ][merge_master_col+1].col = Number( imasic.doc_data.merge_info[ merge_master_row ][merge_master_col+1].col ) + ( proc_number );

        }

        for(var merge_slaves of arg_merge_data.slaves ) {
            merge_slaves_row = Number( merge_slaves.split('_')[2] );
            merge_slaves_col = Number( merge_slaves.split('_')[3] );
        }



    }

    /**
     * 병합 처리 
    */
    imasicAdminEditor.mergeProcessor = function(){
        
        var merge_start_row = '';
        var merge_start_col = '';
        var merge_end_row = '';
        var merge_end_col = '';
        var first_point_row = imasicAdminEditor.selected_element_info[0].getAttribute('id').split('_')[2];
        var first_point_col = imasicAdminEditor.selected_element_info[0].getAttribute('id').split('_')[3];
        var last_point_row = imasicAdminEditor.selected_element_info[1].getAttribute('id').split('_')[2];
        var last_point_col = imasicAdminEditor.selected_element_info[1].getAttribute('id').split('_')[3];

        if( first_point_row <= last_point_row ) {
            merge_start_row = first_point_row;
            merge_end_row = last_point_row;
            merge_start_col = first_point_col;
            merge_end_col = last_point_col;
            
        } else {
            merge_start_row = last_point_row;
            merge_end_row = first_point_row;
            merge_start_col = last_point_col;
            merge_end_col = first_point_col;
        }
    
        if( imasic.doc_data.merge_info.hasOwnProperty( merge_start_row ) == false ) {
            imasic.doc_data.merge_info[ merge_start_row ] = [];    
            imasic.doc_data.merge_info[ merge_start_row ][merge_start_col] = [];    
        }

        if( imasic.doc_data.merge_info[ merge_start_row ] == null ) {
            imasic.doc_data.merge_info[ merge_start_row ] = [];
        }

        if( imasic.doc_data.merge_info[ merge_start_row ].hasOwnProperty( merge_start_col ) == false ) {            
            imasic.doc_data.merge_info[ merge_start_row ][merge_start_col] = [];    
        }

        imasic.doc_data.merge_info[ merge_start_row ][ merge_start_col ] = {row : merge_end_row, col : merge_end_col };

        this.resetEditor();

    }

    /** 
     * cell 내용을 복사한다.
    */
    imasicAdminEditor.docCellCopyHandler = function(){

        var current_class = document.getElementById( '_doc_cell_paste' ).getAttribute('class');
        document.getElementById( '_doc_cell_paste' ).setAttribute('class', current_class + ' e_doc_btn_active ');

        imasicAdminEditor.cell_copyed = JSON.stringify( imasicAdminEditor.getDocItem() );        

        
    }

    /** 
     * cell 내용을 붙여넣는다
    */
    imasicAdminEditor.docCellPasteHandler = function(){

        var copyed = JSON.parse( imasicAdminEditor.cell_copyed );
        var row = '';
        var col = '';
        var paste_target_id = '';
        var copy_child = '';
        var cell_radio_cnt = 0;
        var cell_checkbox_cnt = 0;

        if( copyed.hasOwnProperty('child') == true ) {

            for(var paste_item of imasicAdminEditor.selected_element_info ) {
                
                paste_target_id = paste_item.getAttribute('id');
                
                row = paste_target_id.split('_')[2];
                col = paste_target_id.split('_')[3];       

                cell_radio_cnt = 0;
                cell_checkbox_cnt = 0;

                if( imasic.doc_data.structure.doc_data[ row ].child[col].hasOwnProperty('child') == false ) {
                    imasic.doc_data.structure.doc_data[ row ].child[col]['child'] = []; 
                }

                for( var copyed_item of copyed.child ) {

                    copy_child = JSON.parse(JSON.stringify( copyed_item ));

                    switch( copy_child.tag ) {
                        case 'input' : {
                            
                            switch( copy_child.attr.type ) {
                                case 'text' : {
                                    copy_child.attr.name = paste_target_id + '__doc_td_inputs';
                                    copy_child.attr.id = paste_target_id + '__doc_td_inputs';
                                    break;
                                }
                                case 'radio' : {

                                    copy_child.attr.name = paste_target_id + '__doc_td_radios' ;
                                    copy_child.attr.id = paste_target_id + '__doc_td_radios_' + cell_radio_cnt;
                                    

                                    break;
                                }
                                case 'checkbox' : {

                                    copy_child.attr.name = paste_target_id + '__doc_td_checkboxs_' + cell_checkbox_cnt;
                                    copy_child.attr.id = paste_target_id + '__doc_td_checkboxs_' + cell_checkbox_cnt;
                                    
                                    break;
                                }
                            }
    
                            break;
                        }
                        case 'textarea' : {

                            copy_child.attr.name = paste_target_id + '__doc_td_checkboxs';
                            copy_child.attr.id = paste_target_id + '__doc_td_checkboxs';
                            
                            break;
                        }

                        case 'label' : {
                            
                            if( copy_child.attr['data-for_type'] == 'radio' ){                                
                                copy_child.attr.for = paste_target_id + '__doc_td_radios_' + cell_radio_cnt;
                                cell_radio_cnt++;
                            } else {
                                copy_child.attr.for = paste_target_id + '__doc_td_checkboxs_' + cell_checkbox_cnt;
                                cell_checkbox_cnt++;
                            }

                            break;
                        }

                        default : {
                            // if( copyed_item.hasOwnProperty('text') == true ) {
                                
                            // }
                        }
                    }

                    imasic.doc_data.structure.doc_data[ row ].child[ col ].child.push(copy_child);
                }

            }

            // console.log( imasic.doc_data.structure.doc_data[ row ].child[col].child );

            imasicAdminEditor.resetEditor();

        }

    }

    /**
     * 에디터 영역 block 처리
    */
    imasicAdminEditor.dimm.show = function( arg_info ){
        
        imasicAdminEditor.dimm.hide();

        switch( arg_info.type ){
            case 'guide' : {

                document.getElementById( imasicAdminEditor.editor_area_id ).appendChild(imasic.createHtml({
                    tag : 'div'
                    , attr : {
                        class : 'edit_guide'
                    }
                }));
                
                document.getElementsByClassName('edit_guide')[0].innerHTML = arg_info.msg;

                break;
            }
            case 'add_cell_child' : {

                document.getElementById( imasicAdminEditor.editor_area_id ).appendChild(imasic.createHtml({
                    tag : 'div'
                    , attr : {
                        class : 'dimm_container'
                        ,style : 'top:'+arg_info.top+'px'
                    }
                    ,child : [
                        {
                            tag : 'div'
                            , attr : {
                                class : 'dimm_box'
                            }
                            ,child : [
                                {
                                    tag : 'div'
                                    , attr : {
                                        class : 'dim_box_top'
                                        ,style : ' margin-bottom : 10px'
                                    }
                                    ,child : [
                                        {
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.dimm.hide()'
                                            }
                                            ,child : [
                                                {text:'닫기'}
                                            ]
                                        }
                                    ]
                                }
                                ,{
                                    tag : 'div'
                                    , attr : {
                                        class : 'cell_add_task_select_box'
                                    }
                                    ,child : [
                                        {
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.showCellTextEdit(this)'
                                            }
                                            ,child : [
                                                {text:'text'}
                                            ]
                                        }
                                        ,{
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.docInsertInputHandler( this )'
                                            }
                                            ,child : [
                                                {text:'input'}
                                            ]
                                        }
                                        ,{
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.docInsertRadioHandler(this)'
                                            }
                                            ,child : [
                                                {text:'radio'}
                                            ]
                                        }
                                        ,{
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.docInsertCheckboxHandler(this)'
                                            }
                                            ,child : [
                                                {text:'checkbox'}
                                            ]
                                        }
                                        ,{
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.docInsertTextareaHandler( this )'
                                            }
                                            ,child : [
                                                {text:'textarea'}
                                            ]
                                        }
                                        ,{
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.docInsertCarriageReturnHandler()'
                                            }
                                            ,child : [
                                                {text:'줄바꿈'}
                                            ]
                                        }
                                        
                                    ]
                                }
                            ]
                        }
                    ]
                    
                }));


                
                break;
            }
            case 'cell_add_text' : {

                document.getElementById( imasicAdminEditor.editor_area_id ).appendChild(imasic.createHtml({
                    tag : 'div'
                    , attr : {
                        class : 'dimm_container'
                        ,style : 'top:'+arg_info.top
                    }
                    ,child : [
                        {
                            tag : 'div'
                            , attr : {
                                class : 'dimm_box'
                            }
                            ,child : [
                                {
                                    tag : 'div'
                                    , attr : {
                                        class : 'dim_box_top'
                                        ,style : ' margin-bottom : 10px'
                                    }
                                    ,child : [
                                        {
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.dimm.hide()'
                                            }
                                            ,child : [
                                                {text:'닫기'}
                                            ]
                                        }
                                        ,{
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.cellTextEditDoneHanelr("add")'
                                            }
                                            ,child : [
                                                {text:'저장'}
                                            ]
                                        }
                                    ]
                                }
                                ,{
                                    tag : 'div'
                                    , attr : {
                                        class : 'cell_add_task_select_box'
                                    }
                                    ,child : [
                                        {
                                            tag : 'textarea'
                                            , attr : {
                                                id: 'cell_edit_write'
                                                ,style: 'width:100%;height:130px'
                                            }
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                    
                }));

                document.getElementById( 'cell_edit_write' ).focus();

                break;
            }
            case 'cell_setting' : {

                document.getElementById( imasicAdminEditor.editor_area_id ).appendChild(imasic.createHtml({

                    tag : 'div'
                    , attr : {
                        class : 'dimm_container'
                        ,style : 'top:'+arg_info.top+'px'
                    }
                    ,child : [
                        {
                            tag : 'div'
                            , attr : {
                                class : 'dimm_box'
                            }
                            ,child : [
                                {
                                    tag : 'div'
                                    , attr : {
                                        class : 'dim_box_top'
                                        ,style : ' margin-bottom : 10px'
                                    }
                                    ,child : [
                                        {
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.dimm.hide()'
                                            }
                                            ,child : [
                                                {text:'닫기'}
                                            ]
                                        }
                                    ]
                                }
                                ,{
                                    tag : 'div'
                                    , attr : {
                                        class : 'cell_add_task_select_box'
                                    }
                                    ,child : [
                                        {
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.cellChildDeltHandler()'
                                            }
                                            ,child : [
                                                {text:'삭제'}
                                            ]
                                        }
                                        ,{
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.cellChildEditHandler(this)'
                                            }
                                            ,child : [
                                                {text:'수정'}
                                            ]
                                        }
                                        
                                    ]
                                }
                            ]
                        }
                    ]

                    
                    
                }));


                break;
            }
            case 'cell_edit_text' : {

                document.getElementById( imasicAdminEditor.editor_area_id ).appendChild(imasic.createHtml({
                    tag : 'div'
                    , attr : {
                        class : 'dimm_container'
                        ,style : 'top:'+arg_info.top
                    }
                    ,child : [
                        {
                            tag : 'div'
                            , attr : {
                                class : 'dimm_box'
                            }
                            ,child : [
                                {
                                    tag : 'div'
                                    , attr : {
                                        class : 'dim_box_top'
                                        ,style : ' margin-bottom : 10px'
                                    }
                                    ,child : [
                                        {
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.dimm.hide()'
                                            }
                                            ,child : [
                                                {text:'닫기'}
                                            ]
                                        }
                                        ,{
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.cellTextEditDoneHanelr("edit")'
                                            }
                                            ,child : [
                                                {text:'저장'}
                                            ]
                                        }
                                    ]
                                }
                                ,{
                                    tag : 'div'
                                    , attr : {
                                        class : 'cell_add_task_select_box'
                                    }
                                    ,child : [
                                        {
                                            tag : 'input'
                                            , attr : {
                                                type: 'text'
                                                ,id: 'cell_edit_write'
                                                ,style: 'width:100%;'
                                                ,value : arg_info.current_data
                                            }
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                    
                }));

                document.getElementById( 'cell_edit_write' ).focus();

                break;
            }
            case 'edit_input_attr' : {

                var create_dimm_container = {
                    tag : 'div'
                    , attr : {
                        class : 'dimm_container'
                        ,style : 'top:'+arg_info.top + 'px'
                    }
                    ,child : [
                        {
                            tag : 'div'
                            , attr : {
                                class : 'dimm_box'
                            }
                            ,child : [
                                {
                                    tag : 'div'
                                    , attr : {
                                        class : 'dim_box_top'
                                        ,style : ' margin-bottom : 10px'
                                    }
                                    ,child : [
                                        {
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.dimm.hide()'
                                            }
                                            ,child : [
                                                {text:'닫기'}
                                            ]
                                        }
                                        ,{
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.docInsertInput("'+ arg_info.mode +'")'
                                            }
                                            ,child : [
                                                {text:'저장'}
                                            ]
                                        }
                                    ]
                                }
                                ,{
                                    tag : 'div'
                                    , attr : {
                                        class : 'cell_add_task_select_box'
                                    }
                                    ,child : []
                                }
                            ]
                        }
                    ]
                    
                };


                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'class'
                        }
                    ]
                    
                });

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'input'
                    , attr : {
                        type: 'text'
                        ,id: 'cell_edit_input_class'
                        ,style: 'width:80%;'
                        ,value: ( arg_info['class'] ) ? arg_info['class'] : ''
                    }
                });

                create_dimm_container.child[0].child[1].child.push({tag : 'br'},{tag : 'br'});

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'name'
                        }
                    ]
                });

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'input'
                    , attr : {
                        type: 'text'
                        ,id: 'cell_edit_input_name'
                        ,style: 'width:80%;'
                        ,value: ( arg_info['name'] ) ? arg_info['name'] : ''
                    }
                });

                create_dimm_container.child[0].child[1].child.push({tag : 'br'},{tag : 'br'});

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'id'
                        }
                    ]
                });

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'input'
                    , attr : {
                        type: 'text'
                        ,id: 'cell_edit_input_id'
                        ,style: 'width:80%;'
                        ,value: ( arg_info['id'] ) ? arg_info['id'] : ''
                    }
                });

                create_dimm_container.child[0].child[1].child.push({tag : 'br'},{tag : 'br'});

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'style'
                        }
                    ]
                });

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'input'
                    , attr : {
                        type: 'text'
                        ,id: 'cell_edit_input_style'
                        ,style: 'width:80%;'
                        ,value: ( arg_info['style'] ) ? arg_info['style'] : ''
                    }
                });

                create_dimm_container.child[0].child[1].child.push({tag : 'br'},{tag : 'br'});

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'value'
                        }
                    ]
                });

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'input'
                    , attr : {
                        type: 'text'
                        ,id: 'cell_edit_input_value'
                        ,style: 'width:80%;'
                        ,value: ( arg_info['value'] ) ? arg_info['value'] : ''
                    }
                });

                create_dimm_container.child[0].child[1].child.push({tag : 'br'},{tag : 'br'});

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'checked'
                        }
                    ]
                });

                var checked_input = '';
                
                if( arg_info['checked'] ) {
                    checked_input = {
                        tag : 'input'
                        , attr : {
                            type: 'checkbox'
                            ,id: 'cell_edit_input_checked'                        
                            ,checked: ( arg_info['checked'] ) ? arg_info['checked'] : ''
                        }
                    }
                } else {
                    checked_input = {
                        tag : 'input'
                        , attr : {
                            type: 'checkbox'
                            ,id: 'cell_edit_input_checked'                            
                        }
                    }
                }

                create_dimm_container.child[0].child[1].child.push(checked_input);


                create_dimm_container.child[0].child[1].child.push({tag : 'br'},{tag : 'br'});
                
                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'valid'
                        }
                    ]
                });


                if( arg_info['valid'] ) {
                    checked_input = {
                        tag : 'input'
                        , attr : {
                            type: 'checkbox'
                            ,id: 'cell_edit_input_valid'                        
                            ,checked: ( arg_info['valid'] ) ? arg_info['valid'] : ''
                        }
                    }
                } else {
                    checked_input = {
                        tag : 'input'
                        , attr : {
                            type: 'checkbox'
                            ,id: 'cell_edit_input_valid'                            
                        }
                    }
                }


                create_dimm_container.child[0].child[1].child.push(checked_input);

                create_dimm_container.child[0].child[1].child.push({tag : 'br'},{tag : 'br'});
                
                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'calendar'
                        }
                    ]
                });


                if( arg_info['calendar'] ) {
                    checked_input = {
                        tag : 'input'
                        , attr : {
                            type: 'checkbox'
                            ,id: 'cell_edit_input_calendar'                        
                            ,checked: ( arg_info['calendar'] ) ? arg_info['calendar'] : ''
                        }
                    }
                } else {
                    checked_input = {
                        tag : 'input'
                        , attr : {
                            type: 'checkbox'
                            ,id: 'cell_edit_input_calendar'                            
                        }
                    }
                }

                create_dimm_container.child[0].child[1].child.push(checked_input);

                create_dimm_container.child[0].child[1].child.push({tag : 'br'},{tag : 'br'});
                
                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'time'
                        }
                    ]
                });


                if( arg_info['time'] ) {
                    checked_input = {
                        tag : 'input'
                        , attr : {
                            type: 'checkbox'
                            ,id: 'cell_edit_input_time'                        
                            ,checked: ( arg_info['time'] ) ? arg_info['time'] : ''
                        }
                    }
                } else {
                    checked_input = {
                        tag : 'input'
                        , attr : {
                            type: 'checkbox'
                            ,id: 'cell_edit_input_time'                            
                        }
                    }
                }


                create_dimm_container.child[0].child[1].child.push(checked_input);



                document.getElementById( imasicAdminEditor.editor_area_id ).appendChild(imasic.createHtml( create_dimm_container ));


                break;
            }
            case 'edit_radio_attr' : {

                document.getElementById( imasicAdminEditor.editor_area_id ).appendChild(imasic.createHtml({
                    tag : 'div'
                    , attr : {
                        class : 'dimm_container'
                        ,style : 'top:'+ arg_info.top + 'px'
                    }
                    ,child : [
                        {
                            tag : 'div'
                            , attr : {
                                class : 'dimm_box'
                            }
                            ,child : [
                                {
                                    tag : 'div'
                                    , attr : {
                                        class : 'dim_box_top'
                                        ,style : ' margin-bottom : 10px'
                                    }
                                    ,child : [
                                        {
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.dimm.hide()'
                                            }
                                            ,child : [
                                                {text:'닫기'}
                                            ]
                                        }
                                        ,{
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.docInsertRadio("'+ arg_info.mode +'")'
                                            }
                                            ,child : [
                                                {text:'저장'}
                                            ]
                                        }
                                    ]
                                }
                                ,{
                                    tag : 'div'
                                    , attr : {
                                        class : 'cell_add_task_select_box'
                                    }
                                    ,child : [
                                        {
                                            tag : 'div'
                                            ,attr : {
                                                style : 'display:inline-block;width:80px'
                                            }
                                            ,child : [
                                                {
                                                    text : 'label'
                                                }
                                            ]
                                        }
                                        ,{
                                            tag : 'input'
                                            , attr : {
                                                type: 'text'
                                                ,id: 'cell_edit_radio_label'
                                                ,style: 'width:80%;'
                                                ,value: ( arg_info['label'] ) ? arg_info['label'] : ''
                                            }
                                        }
                                        ,{
                                            tag : 'br'
                                        }
                                        ,{
                                            tag : 'br'
                                        }
                                        ,{
                                            tag : 'div'
                                            ,attr : {
                                                style : 'display:inline-block;width:80px'
                                            }
                                            ,child : [
                                                {
                                                    text : 'name'
                                                }
                                            ]
                                        }
                                        ,{
                                            tag : 'input'
                                            , attr : {
                                                type: 'text'
                                                ,id: 'cell_edit_radio_name'
                                                ,style: 'width:80%;'
                                                ,value: ( arg_info['name'] ) ? arg_info['name'] : ''
                                            }
                                        }
                                        ,{
                                            tag : 'br'
                                        }
                                        ,{
                                            tag : 'br'
                                        }
                                        ,{
                                            tag : 'div'
                                            ,attr : {
                                                style : 'display:inline-block;width:80px'
                                            }
                                            ,child : [
                                                {
                                                    text : 'value'
                                                }
                                            ]
                                        }
                                        ,{
                                            tag : 'input'
                                            , attr : {
                                                type: 'text'
                                                ,id: 'cell_edit_radio_value'
                                                ,style: 'width:80%;'
                                                ,value: ( arg_info['value'] ) ? arg_info['value'] : ''
                                            }
                                        }
                                        ,{
                                            tag : 'br'
                                        }
                                        ,{
                                            tag : 'br'
                                        }
                                        ,{
                                            tag : 'div'
                                            ,attr : {
                                                style : 'display:inline-block;width:80px'
                                            }
                                            ,child : [
                                                {
                                                    text : 'checked'
                                                }
                                            ]
                                        }
                                        ,{
                                            tag : 'input'
                                            , attr : {
                                                type: 'checkbox'
                                                ,id: 'cell_edit_radio_checked'
                                            }
                                        }
                                        ,{
                                            tag : 'br'
                                        }
                                        ,{
                                            tag : 'br'
                                        }
                                        ,{
                                            tag : 'div'
                                            ,attr : {
                                                style : 'display:inline-block;width:80px'
                                            }
                                            ,child : [
                                                {
                                                    text : 'valid'
                                                }
                                            ]
                                        }
                                        ,{
                                            tag : 'input'
                                            , attr : {
                                                type: 'checkbox'
                                                ,id: 'cell_edit_radio_valid'
                                            }
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                    
                }));


                break;
            }

            case 'edit_checkbox_attr' : {

                document.getElementById( imasicAdminEditor.editor_area_id ).appendChild(imasic.createHtml({
                    tag : 'div'
                    , attr : {
                        class : 'dimm_container'
                        ,style : 'top:'+ arg_info.top + 'px'
                    }
                    ,child : [
                        {
                            tag : 'div'
                            , attr : {
                                class : 'dimm_box'
                            }
                            ,child : [
                                {
                                    tag : 'div'
                                    , attr : {
                                        class : 'dim_box_top'
                                        ,style : ' margin-bottom : 10px'
                                    }
                                    ,child : [
                                        {
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.dimm.hide()'
                                            }
                                            ,child : [
                                                {text:'닫기'}
                                            ]
                                        }
                                        ,{
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.docInsertCheckbox("'+ arg_info.mode +'")'
                                            }
                                            ,child : [
                                                {text:'저장'}
                                            ]
                                        }
                                    ]
                                }
                                ,{
                                    tag : 'div'
                                    , attr : {
                                        class : 'cell_add_task_select_box'
                                    }
                                    ,child : [
                                        {
                                            tag : 'div'
                                            ,attr : {
                                                style : 'display:inline-block;width:80px'
                                            }
                                            ,child : [
                                                {
                                                    text : 'label'
                                                }
                                            ]
                                        }
                                        ,{
                                            tag : 'input'
                                            , attr : {
                                                type: 'text'
                                                ,id: 'cell_edit_checkbox_label'
                                                ,style: 'width:80%;'
                                                ,value: ( arg_info['label'] ) ? arg_info['label'] : ''
                                            }
                                        }
                                        ,{
                                            tag : 'br'
                                        }
                                        ,{
                                            tag : 'br'
                                        }
                                        ,{
                                            tag : 'div'
                                            ,attr : {
                                                style : 'display:inline-block;width:80px'
                                            }
                                            ,child : [
                                                {
                                                    text : 'name'
                                                }
                                            ]
                                        }
                                        ,{
                                            tag : 'input'
                                            , attr : {
                                                type: 'text'
                                                ,id: 'cell_edit_checkbox_name'
                                                ,style: 'width:80%;'
                                                ,value: ( arg_info['name'] ) ? arg_info['name'] : ''
                                            }
                                        }
                                        ,{
                                            tag : 'br'
                                        }
                                        ,{
                                            tag : 'br'
                                        }
                                        ,{
                                            tag : 'div'
                                            ,attr : {
                                                style : 'display:inline-block;width:80px'
                                            }
                                            ,child : [
                                                {
                                                    text : 'value'
                                                }
                                            ]
                                        }
                                        ,{
                                            tag : 'input'
                                            , attr : {
                                                type: 'text'
                                                ,id: 'cell_edit_checkbox_value'
                                                ,style: 'width:80%;'
                                                ,value: ( arg_info['value'] ) ? arg_info['value'] : ''
                                            }
                                        }
                                        ,{
                                            tag : 'br'
                                        }
                                        ,{
                                            tag : 'br'
                                        }
                                        ,{
                                            tag : 'div'
                                            ,attr : {
                                                style : 'display:inline-block;width:80px'
                                            }
                                            ,child : [
                                                {
                                                    text : 'checked'
                                                }
                                            ]
                                        }
                                        ,{
                                            tag : 'input'
                                            , attr : {
                                                type: 'checkbox'
                                                ,id: 'cell_edit_checkbox_checked'
                                            }
                                        }
                                        ,{
                                            tag : 'br'
                                        }
                                        ,{
                                            tag : 'br'
                                        }
                                        ,{
                                            tag : 'div'
                                            ,attr : {
                                                style : 'display:inline-block;width:80px'
                                            }
                                            ,child : [
                                                {
                                                    text : 'valid'
                                                }
                                            ]
                                        }
                                        ,{
                                            tag : 'input'
                                            , attr : {
                                                type: 'checkbox'
                                                ,id: 'cell_edit_checkbox_valid'
                                            }
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                    
                }));


                break;
            }

            case 'edit_label_attr' : {

                var create_dimm_container = {
                    tag : 'div'
                    , attr : {
                        class : 'dimm_container'
                        ,style : 'top:'+arg_info.top
                    }
                    ,child : [
                        {
                            tag : 'div'
                            , attr : {
                                class : 'dimm_box'
                            }
                            ,child : [
                                {
                                    tag : 'div'
                                    , attr : {
                                        class : 'dim_box_top'
                                        ,style : ' margin-bottom : 10px'
                                    }
                                    ,child : [
                                        {
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.dimm.hide()'
                                            }
                                            ,child : [
                                                {text:'닫기'}
                                            ]
                                        }
                                        ,{
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.docInsertLabel("'+ arg_info.mode +'")'
                                            }
                                            ,child : [
                                                {text:'저장'}
                                            ]
                                        }
                                    ]
                                }
                                ,{
                                    tag : 'div'
                                    , attr : {
                                        class : 'cell_add_task_select_box'
                                    }
                                    ,child : []
                                }
                            ]
                        }
                    ]
                    
                };


                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'for'
                        }
                    ]
                    
                });

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'input'
                    , attr : {
                        type: 'text'
                        ,id: 'cell_edit_label_for'
                        ,style: 'width:80%;'
                        ,value: ( arg_info['for'] ) ? arg_info['for'] : ''
                    }
                });

                create_dimm_container.child[0].child[1].child.push({tag : 'br'},{tag : 'br'});


                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'style'
                        }
                    ]
                });

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'input'
                    , attr : {
                        type: 'text'
                        ,id: 'cell_edit_label_style'
                        ,style: 'width:80%;'
                        ,value: ( arg_info['style'] ) ? arg_info['style'] : ''
                    }
                });

                create_dimm_container.child[0].child[1].child.push({tag : 'br'},{tag : 'br'});

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'text'
                        }
                    ]
                });

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'input'
                    , attr : {
                        type: 'text'
                        ,id: 'cell_edit_label_text'
                        ,style: 'width:80%;'
                        ,value: ( arg_info['text'] ) ? arg_info['text'] : ''
                    }
                });


                document.getElementById( imasicAdminEditor.editor_area_id ).appendChild(imasic.createHtml( create_dimm_container ));


                break;
            }
            case 'edit_textarea_attr' : {

                var create_dimm_container = {
                    tag : 'div'
                    , attr : {
                        class : 'dimm_container'
                        ,style : 'top:'+arg_info.top + 'px'
                    }
                    ,child : [
                        {
                            tag : 'div'
                            , attr : {
                                class : 'dimm_box'
                            }
                            ,child : [
                                {
                                    tag : 'div'
                                    , attr : {
                                        class : 'dim_box_top'
                                        ,style : ' margin-bottom : 10px'
                                    }
                                    ,child : [
                                        {
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.dimm.hide()'
                                            }
                                            ,child : [
                                                {text:'닫기'}
                                            ]
                                        }
                                        ,{
                                            tag : 'button'
                                            , attr : {
                                                class : 'e_doc_edit_btn'
                                                ,type : 'button'
                                                ,onclick : 'imasicAdminEditor.docInsertTextarea("'+ arg_info.mode +'")'
                                            }
                                            ,child : [
                                                {text:'저장'}
                                            ]
                                        }
                                    ]
                                }
                                ,{
                                    tag : 'div'
                                    , attr : {
                                        class : 'cell_add_task_select_box'
                                    }
                                    ,child : []
                                }
                            ]
                        }
                    ]
                    
                };


                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'class'
                        }
                    ]
                    
                });

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'input'
                    , attr : {
                        type: 'text'
                        ,id: 'cell_edit_textarea_class'
                        ,style: 'width:80%;'
                        ,value: ( arg_info['class'] ) ? arg_info['class'] : ''
                    }
                });

                create_dimm_container.child[0].child[1].child.push({tag : 'br'},{tag : 'br'});

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'name'
                        }
                    ]
                });

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'input'
                    , attr : {
                        type: 'text'
                        ,id: 'cell_edit_textarea_name'
                        ,style: 'width:80%;'
                        ,value: ( arg_info['name'] ) ? arg_info['name'] : ''
                    }
                });

                create_dimm_container.child[0].child[1].child.push({tag : 'br'},{tag : 'br'});

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'id'
                        }
                    ]
                });

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'input'
                    , attr : {
                        type: 'text'
                        ,id: 'cell_edit_textarea_id'
                        ,style: 'width:80%;'
                        ,value: ( arg_info['id'] ) ? arg_info['id'] : ''
                    }
                });

                create_dimm_container.child[0].child[1].child.push({tag : 'br'},{tag : 'br'});

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'style'
                        }
                    ]
                });

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'input'
                    , attr : {
                        type: 'text'
                        ,id: 'cell_edit_textarea_style'
                        ,style: 'width:80%;'
                        ,value: ( arg_info['style'] ) ? arg_info['style'] : ''
                    }
                });

                create_dimm_container.child[0].child[1].child.push({tag : 'br'},{tag : 'br'});

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'value'
                        }
                    ]
                });

                create_dimm_container.child[0].child[1].child.push({
                    tag : 'input'
                    , attr : {
                        type: 'text'
                        ,id: 'cell_edit_textarea_value'
                        ,style: 'width:80%;'
                        ,value: ( arg_info['value'] ) ? arg_info['value'] : ''
                    }
                });

                create_dimm_container.child[0].child[1].child.push({tag : 'br'},{tag : 'br'});
                
                create_dimm_container.child[0].child[1].child.push({
                    tag : 'div'
                    ,attr : {
                        style : 'display:inline-block;width:80px'
                    }
                    ,child : [
                        {
                            text : 'valid'
                        }
                    ]
                });


                if( arg_info['valid'] ) {

                    checked_input = {
                        tag : 'input'
                        , attr : {
                            type: 'checkbox'
                            ,id: 'cell_edit_textarea_valid'                        
                            ,checked: ( arg_info['valid'] ) ? arg_info['valid'] : ''
                        }
                    }

                } else {

                    checked_input = {
                        tag : 'input'
                        , attr : {
                            type: 'checkbox'
                            ,id: 'cell_edit_textarea_valid'                            
                        }
                    }

                }


                create_dimm_container.child[0].child[1].child.push(checked_input);

                document.getElementById( imasicAdminEditor.editor_area_id ).appendChild(imasic.createHtml( create_dimm_container ));


                break;
            }

        }

        document.getElementById( imasicAdminEditor.editor_dimm_area_id ).style.display = 'block';

    }


    /**
     * cell 편집 > 작성 레이어 노출
     */
    imasicAdminEditor.showCellTextEdit = function( arg_this ){        
        this.dimm.show({
            type:'cell_add_text'
            ,top:arg_this.parentNode.parentNode.parentNode.style.top
        });
    }

    /**
     * cell 편집 > 작성 처리
     */
    imasicAdminEditor.cellTextEditDoneHanelr = function( arg_mode ){

        var text_value = document.getElementById('cell_edit_write').value;

        if( arg_mode == 'edit' ) {
            imasicAdminEditor.getDocItem().child[ imasicAdminEditor.cell_set_target_info.current_idx ].text = text_value;
        } else {
            if( text_value == '' ) {
                imasicAdminEditor.dimm.hide();
                return;
            }

            var child_arr = text_value.split(/\n/g);

            for( var child_idx in child_arr ) {
                //# 내용추가
                
                if( imasicAdminEditor.getDocItem().hasOwnProperty('child') == false ) {
                    imasicAdminEditor.getDocItem().child = [];
                }

                if( child_arr[ child_idx ] == '' ){
                    imasicAdminEditor.getDocItem().child.push({
                        tag : 'br'
                    });
                } else {
                    imasicAdminEditor.getDocItem().child.push({
                        text : child_arr[ child_idx ]
                    });

                    if( (child_arr.length - 1) > child_idx ) {
                        imasicAdminEditor.getDocItem().child.push({
                            tag : 'br'
                        }); 
                    }
                }
                
            }
            
        }

        imasicAdminEditor.resetEditor();
        
    }

    /**
     * 에디터 영역 block 처리
    */
    imasicAdminEditor.dimm.hide = function(){           

        document.getElementById( imasicAdminEditor.editor_dimm_area_id ).innerHTML = ''; 
        document.getElementById( imasicAdminEditor.editor_dimm_area_id ).style.display = 'none';

        if( document.getElementsByClassName('dimm_container').length > 0 ){
            document.getElementsByClassName('dimm_container')[0].remove();
        }
        
        
    }

    /**
     * 에디터 탭 문서편집 버튼 클릭 이벤트 처리
    */
    imasicAdminEditor.editorTabEditHandler = function(){

        var editor_edit_area = document.getElementById( imasicAdminEditor.editor_edit_area_id );
        var editor_task_area = document.getElementById( imasicAdminEditor.editor_task_area_id );
        
        var get_element = document.getElementsByClassName( 'e_doc_btn_blue' );

        for( var item of get_element) {
            item.setAttribute('class', 'e_doc_btn' );
        }
        
        this.setAttribute('class', 'e_doc_btn e_doc_btn_blue' );

        editor_edit_area.style.display = 'block';
        editor_task_area.style.display = 'none';
        
    }

    /**
     * 에디터 탭 작업편집 버튼 클릭 이벤트 처리
    */
    imasicAdminEditor.editorTabTaskHandler = function(){

        var editor_edit_area = document.getElementById( imasicAdminEditor.editor_edit_area_id );
        var editor_task_area = document.getElementById( imasicAdminEditor.editor_task_area_id );

        var get_element = document.getElementsByClassName( 'e_doc_btn_blue' );

        for( var item of get_element) {
            item.setAttribute('class', 'e_doc_btn' );
        }
        
        this.setAttribute('class', 'e_doc_btn e_doc_btn_blue' );

        editor_edit_area.style.display = 'none';
        editor_task_area.style.display = 'block';
    }

    /**
    * 이벤트 등록 준비 처리 함수
    */
    imasicAdminEditor.domEventHandler = function( arg_data ){

        var get_element = '';

        switch( arg_data.selector_type ) {
            case 'id' : {

                get_element = document.getElementById( arg_data.selector );

                break;
            }
            case 'name' : {
                get_element = document.getElementsByName( arg_data.selector );
                break;
            }
            case 'class' : {
                get_element = document.getElementsByClassName( arg_data.selector );
                break;
            }
        }
   
        if( get_element.length ) {

            for( var item of get_element) {

                arg_data['object'] = item;

                this.addDomEvent( arg_data );

            }
            
        } else {

            arg_data['object'] = get_element;

            this.addDomEvent( arg_data );

        }

    }

    /**
    * imasic.doc_data 에서 td 에 해당하는 값을 반환한다.
    */
    imasicAdminEditor.getDocItem = function() {
        
        if( imasicAdminEditor.selected_element_info.length > 0 ) {
            
            var row = imasicAdminEditor.selected_element_info[0].id.split('_')[2];
            var col = imasicAdminEditor.selected_element_info[0].id.split('_')[3];

            return imasic.doc_data.structure.doc_data[row].child[col];

        } else {
            console.error('선택된 영역이 없습니다.');
        }

    }

    /**
    * 선택된 element 객체를 반환한다.
    */
    imasicAdminEditor.getSelectedElement = function() {
        return imasicAdminEditor.selected_element_info[0];
    }


    /**
    * 이벤트 등록 함수
    */
    imasicAdminEditor.addDomEvent = function( arg_data ) {

console.log( arg_data.object.addEventListener );

        try {

            if( arg_data.object.addEventListener ) {
                // ie 외 브라우저
                arg_data.object.addEventListener( arg_data.event, arg_data.fn );
            } else {
                // ie
                arg_data.object.attachEvent( arg_data.event, arg_data.fn );
            }

        } catch( error_info ) {
            console.error( ' addDomEvent -> ' + error_info );
        }

    }

    /**
    * td mousedown 이벤트 처리자
    */
    imasicAdminEditor.mouseDownHandler = function( arg_event ) {
        
        //# 현재 선택된 위치의 정보를 대입한다.
        // doc.selected_td = {
        //     id : this.id
        //     ,element : this
        //     ,parent : this.parentNode
        // };
        
        imasicAdminEditor.docWriteCancel();
        imasicAdminEditor.dimm.hide();

        for(var item of document.querySelectorAll('.'+ imasic.table_class +' th, .'+ imasic.table_class +' td') ) {            
            // item.style.border = '1px solid';
            
            if( item.tagName == 'TD' ) {
                item.style.background = 'none';
            } else {
                item.style.background = '#ddd';
            }
            
        }

        //# 선택 표시
        this.style.background = '#f4f7f9';
        this.style.background = '#1e3c75';

        //# 병합 명령 확인
        if( imasicAdminEditor.edit_merge_status == false ) {
            imasicAdminEditor.selected_status = true;
            imasicAdminEditor.selected_element_info = [];
            imasicAdminEditor.selected_element_info.push( this );
            console.log( this );
        } else {

            imasicAdminEditor.edit_merge_status = false;
            imasicAdminEditor.dimm.hide();
            imasicAdminEditor.selected_element_info.push( this );
            //# 병합처리 함수 호출
            imasicAdminEditor.mergeProcessor();
        }

        //# 작성 처리 확인
        if( imasicAdminEditor.write_status == true ) {            
            imasicAdminEditor.writeTextareaDoneHandler();            
        }
        
        imasicAdminEditor.selectedAfterHandler();
        
    }

    /**
    * table drageover 이벤트 처리자
    */
    imasicAdminEditor.drag_start_val = '';
    imasicAdminEditor.drag_end_val = '';
    imasicAdminEditor.dragoverHandler = function( arg_event ){
        // arg_event.target.style.background = imasicAdminEditor.selected_bgcolor;

        if( !( imasicAdminEditor.selected_element_info.indexOf( document.getElementById( arg_event.target.id ) ) > -1 ) ){
            imasicAdminEditor.selected_element_info.push( document.getElementById( arg_event.target.id ) );
        }

        var row_idx = arg_event.target.id.split('_')[2];
        var col_idx = arg_event.target.id.split('_')[3];
        var target_num = Number( row_idx +'.'+ col_idx );
        var start_row_pos = 0, end_row_pos = 0;
        var start_col_pos = 0, end_col_pos = 0;

        if( imasicAdminEditor.drag_start_val == '' ) {            
            imasicAdminEditor.drag_start_val = target_num;
        }

        imasicAdminEditor.drag_end_val = target_num;

        if( imasicAdminEditor.drag_end_val > imasicAdminEditor.drag_start_val ) {
            //# 우측으로 드래그 ( 값이 커짐 )
            start_row_pos = Number( String( imasicAdminEditor.drag_start_val ).split('.')[0] );
            start_col_pos = Number( String( imasicAdminEditor.drag_start_val ).split('.')[1] );
            end_row_pos = Number( String( imasicAdminEditor.drag_end_val ).split('.')[0] );
            end_col_pos = Number( String( imasicAdminEditor.drag_end_val ).split('.')[1] );
            // console.log( imasicAdminEditor.drag_end_val );

            

            // for(;start_row_pos <= end_row_pos; start_row_pos++ ){
            //     // console.log( 'start_row_pos =>' + start_row_pos + ' \ end_row_pos ->' + end_row_pos + ' \ end_row_pos ->' + end_col_pos);
            //     for(;start_col_pos <= end_col_pos; start_col_pos++ ){
            //         //  console.log( start_row_pos +'_'+ start_col_pos );
            //         document.getElementById('doc_td_' + start_row_pos +'_'+ start_col_pos ).style.background = '#1e3c75';
            //     }
            // }

        } else if( imasicAdminEditor.drag_end_val < imasicAdminEditor.drag_start_val ) {
            //# 좌측으로 드래그 ( 값이 작아짐 )
            console.log('작다');
        }
        
        arg_event.target.style.background = '#1e3c75';
        
    }

    /**
    * table drageend 이벤트 처리자
    */
    imasicAdminEditor.dragendHandler = function( arg_event ){        
        console.log('드래그종료');
        imasicAdminEditor.selectedAfterHandler();
        imasicAdminEditor.drag_start_val = '';
        imasicAdminEditor.drag_end_val = '';
    }

    /**
     * 선택된 영역 확인
    */
    imasicAdminEditor.selectedAfterHandler = function(){
    
        var current_class = '';
        var merge_status = false;

        for(var item of this.selected_element_info ) {
            if( item.getAttribute('colspan') ) {
                merge_status = true;
            }
        }

        if( this.selected_element_info.length > 1 ) {
            //# 병합버튼만 활성화

            // for(var item of document.getElementsByClassName( '__doc_tb_menu_item' ) ){
            
            //     if( item.id !== '_doc_merge' ) {
            //         current_class = item.getAttribute('class').replace(/e_doc_btn_active/ig, '');
            //         item.setAttribute('class', current_class );
            //         item.setAttribute('disabled', true );
            //     }
    
            // }

            // document.getElementById( '_doc_merge' ).removeAttribute('disabled');
            // current_class = document.getElementById( '_doc_merge' ).getAttribute('class').replace(/e_doc_btn_active/ig, '');
            // document.getElementById( '_doc_merge' ).setAttribute('class', current_class + ' e_doc_btn_active' );

        } else {
            

            for(var item of document.getElementsByClassName( '__doc_tb_menu_item' ) ){
                
                switch( item.id ) {
                    case '_doc_merge_cancel' : {

                        if( merge_status == false ) {
                            current_class = item.getAttribute('class').replace(/e_doc_btn_active/ig, '');
                            item.setAttribute('class', current_class );
                            item.setAttribute('disabled', true );
                        } else {
                            current_class = item.getAttribute('class').replace(/e_doc_btn_active/ig, '');
                            item.setAttribute('class', current_class + ' e_doc_btn_active' );
                            item.removeAttribute('disabled');
                        }


                        break;
                    }
                    case '_doc_cell_paste' : {
                        break;
                    }
                    default : {
                        current_class = item.getAttribute('class').replace(/e_doc_btn_active/ig, '');
                        item.setAttribute('class', current_class + ' e_doc_btn_active' );
                        item.removeAttribute('disabled');    
                    }
                }

            }

        }

    }

    /**
     * 행 기본값 초기화
    */
    imasicAdminEditor.setStdRows = function() {         
        for(var loop_cnt = 0; loop_cnt < imasic.init_data.init_cols; loop_cnt++ ){
            this.std_row_info.child[loop_cnt] = {tag:'td',child:[]};
        }
    }

    /** 
     * 스크롤 이벤트 동작. 에디터 영역 position 값 변경
    */
    imasicAdminEditor.setEditorPosition = function(){
        var scroll_top = window.scrollY || document.documentElement.scrollTop;

        var editor_area_element = document.getElementById( imasicAdminEditor.editor_area_id );

        if( scroll_top > 365 ) {
            
            editor_area_element.style.position = 'fixed';
            editor_area_element.style.top =  '80px';
            editor_area_element.style.left = '1100px';

        } else {            
            editor_area_element.removeAttribute('style');            
        }
    }

    /**
     * 에디터 재구성
    */
    imasicAdminEditor.resetEditor = function() {        
        this.init({
            target_id : this.init_data.target_id
            , return_data_id : this.init_data.return_data_id  
            , doc_data : imasic.doc_data
        });
    }


    window.imasicAdminEditor = imasicAdminEditor;

}(window, document));