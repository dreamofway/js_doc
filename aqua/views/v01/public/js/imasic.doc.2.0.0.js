;(function (window, document) {

    'use strict';

    /**
     * ---------------------------------------------------
     * i-masic front-end Handler  v2.0.0
     * ---------------------------------------------------
     * 설명
     * ---------------------------------------------------
     * 
     * - 전자문서 구조 생성
     * - 세부 항목 생성 및 설정
     * - 사용자 입력 값 설정
     * 
     * ---------------------------------------------------
     * History
     * ---------------------------------------------------
     * 
     * [v2.0.0] 2020.07.15 - 이정훈
     *  - json data 내 head 키 추가하여 버전관리
     *  - json data 내 structure 키 하위에 문서 구조 생성관리
     *  - json data 내 task_items 키 하위에 사용자에게 입력 받을 항목 정의
     *  - 기존 json data 내에 고정으로 지정되었던 값을 삭제하고 순서 및 child 순서 구분으로 script 에 따라 형태 변경하여 생성
     * 
     * [v1.0.0] 2020.03.20 - 이정훈
     *  - json doc_style 과 doc_data 로 구분하여 table style과 tr,td의 style 지정하여 json 데이터에 고정 값으로 지정
     *  - json data 안에 tag 및 id 지정하여 값에 맞게 element 생성 및 동작
     *  - 각 tag 정보에 style 지정
     * 
     * ---------------------------------------------------
    */

    var imasic = {
        version : '2.0.0'
        , doc_html : ''
        , doc_data : {}
        , make_type : 'edit'
        , table_class : 'e_doc_table'        
        , init_data : {
            target_id : ''
            , return_data_id : ''
            , init_rows : 20
            , init_cols : 20
            , set_head : false
            , set_head_type : 'approval'
        }
        
    }

    /**
     * 문서 초기화에 필요한 값을 확인하고 문서 작업 시작 
     * @param {target_id, return_data_id, init_rows, init_cols } arg_init_data 
     */
    imasic.init = function( arg_init_data ){
        
        if( typeof( arg_init_data ) == 'object' ) {

            //# 초기화 값 대입
            for( var init_key in arg_init_data ) {
                if( this.init_data.hasOwnProperty( init_key ) == true ) {
                    this.init_data[ init_key ] = arg_init_data[ init_key ];
                }
            }

            //# 기존 doc json 데이터 대입
            if( arg_init_data.doc_data ) {
              
                this.doc_data = ( typeof( arg_init_data.doc_data ) == 'string' ) ? JSON.parse( arg_init_data.doc_data ) : arg_init_data.doc_data;
                
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
    
    /**
     * 문서 생성 처리 
     */
    imasic.run = function(){
        
        var table_html = '';
        if( this.doc_data.hasOwnProperty('header') == false ) {
            //# 최초작성
            this.createJsonData();
        }

        table_html = this.createTableHtml();
        document.getElementById( this.init_data.target_id ).innerHTML = ''; 
        document.getElementById( this.init_data.target_id ).appendChild( table_html );        
        document.getElementById( this.init_data.return_data_id ).value = JSON.stringify( this.doc_data );
        this.mergeHandler();

    }

    imasic.createJsonData = function(){

        //# 빈문서 생성
        var loop_row = 0;
        var loop_col = 0;
        var doc_structure = new Array( this.init_data.init_rows );
        var set_doc_head = [];

        if( this.init_data.set_head == true ) {

            if( this.init_data.set_head_type == 'approval') {

                for(var set_loop = 0; set_loop <= 1; set_loop++ ){
                    set_doc_head[ set_loop ] = new Array(17);
                }

                set_doc_head[0][0] = {row:'3', col:'8'};
                set_doc_head[0][9] = {row:'3', col:'10'};
                set_doc_head[0][11] = {row:'0', col:'13'};
                set_doc_head[0][14] = {row:'0', col:'16'};
                set_doc_head[0][17] = {row:'0', col:'19'};
                set_doc_head[1][11] = {row:'3', col:'13'};
                set_doc_head[1][14] = {row:'3', col:'16'};
                set_doc_head[1][17] = {row:'3', col:'19'};

            } else {

                for(var set_loop = 0; set_loop <= 0; set_loop++ ){
                    set_doc_head[ set_loop ] = new Array(0);
                }

                set_doc_head[0][0] = {row:'0', col:'19'};
            }
            
        }

        //# doc_data 초기화
        this.doc_data = {
            header : {
                version : this.version
            }
            ,merge_info : set_doc_head
            ,merge_index : {}
            ,structure : {}
            ,task_items : {}
        }

        for( loop_row = 0; loop_row < doc_structure.length; loop_row++  ){
        
            doc_structure[ loop_row ] = {};
            doc_structure[ loop_row ]['child'] = [];
            for( loop_col = 0; loop_col < this.init_data.init_cols; loop_col++  ){

                if( this.init_data.set_head == true ) {
                    //# 문서 헤더 영역 자동 생성시 
                    if( this.init_data.set_head_type == 'approval') {
                        switch( loop_row + '_' + loop_col ) {
                            case '0_0' : {
                                doc_structure[ loop_row ]['child'][ loop_col ] = {
                                    tag : 'th'
                                    ,attr : {
                                        class: 'e_doc_title'
                                        ,width: '359px'
                                    }
                                    ,child : [
                                        {text : '문서 제목을 입력하세요.'}
                                    ]
                                };        
                                break
                            }
                            case '0_9' : {
                                doc_structure[ loop_row ]['child'][ loop_col ] = {
                                    tag : 'th'
                                    ,attr : {
                                        width: '80px'
                                    }
                                    ,child : [
                                        {text : '결재'}
                                    ]
                                };        
                                break
                            }
                            case '1_11' : {
                                doc_structure[ loop_row ]['child'][ loop_col ] = {                                
                                    attr : {
                                        height: '110px'
                                    }
                                };        
                                break
                            }
                            case '0_11' : {
                                doc_structure[ loop_row ]['child'][ loop_col ] = {
                                    tag : 'th'
                                    ,attr : {
                                        width: '120px'
                                        ,height: '30px'
                                    }
                                    ,child : [
                                        {text : '작성'}
                                    ]
                                };        
                                break
                            }
                            case '0_14' : {
                                doc_structure[ loop_row ]['child'][ loop_col ] = {
                                    tag : 'th'
                                    ,attr : {
                                        width: '120px'
                                    }
                                    ,child : [
                                        {text : '검토'}
                                    ]
                                };        
                                break
                            }
                            case '0_17' : {
                                doc_structure[ loop_row ]['child'][ loop_col ] = {
                                    tag : 'th'
                                    ,attr : {
                                        width: '120px'
                                    }
                                    ,child : [
                                        {text : '승인'}
                                    ]
                                };        
                                break
                            }
                            default : {
                                doc_structure[ loop_row ]['child'][ loop_col ] = {};        
                            }

                        }
                    } else {
                        switch( loop_row + '_' + loop_col ) {
                            case '0_0' : {
                                doc_structure[ loop_row ]['child'][ loop_col ] = {
                                    tag : 'td'
                                    ,attr : {
                                        class: 'e_doc_only_title'                                        
                                        ,height: '100px'                                        
                                        ,borderTop: 'none'
                                        ,borderLeft: 'none'
                                        ,borderRight: 'none'
                                    }
                                    ,child : [
                                        {text : '문서 제목을 입력하세요.'}
                                    ]
                                };        
                                break
                            }
                            default : {
                                doc_structure[ loop_row ]['child'][ loop_col ] = {};        
                            }

                        }
                    }
                } else {
                    doc_structure[ loop_row ]['child'][ loop_col ] = {};
                }
            }

        }  

        this.doc_data.structure['doc_data'] = doc_structure;
    }

    /**
     * table element 생성
     */
    imasic.createTableHtml = function(){

        var doc_html = '';
        var doc_html_tr = '';
        var td_loop_cnt = 0;
        var colspan = 0;
        var rowspan = 0;
         
        doc_html = this.createHtml({ 
            tag : 'table' 
            ,attr : {
                class : this.table_class
            } 
        });

        for(var tr_idx in this.doc_data.structure.doc_data ){

            tr_idx = Number( tr_idx );

            doc_html_tr = this.createHtml({ 
                tag : 'tr' 
                ,attr : {
                    id : 'doc_tr_' + tr_idx
                } 
            });
            
            td_loop_cnt = 0;

            for( var td_item of this.doc_data.structure.doc_data[tr_idx].child ) {

                //# 병합 정보 삭제
                if( td_item.hasOwnProperty('attr') == true ) {
                    if( td_item.attr.hasOwnProperty('rowspan') == true ) {
                        delete td_item.attr.rowspan;
                        delete td_item.attr.colspan;
                    }
                }

                //# 병합 데이터 확인 및 병합 속성 부여
                if( imasic.mergeExist(tr_idx, td_loop_cnt) == true ) {

                    rowspan = Number( tr_idx - Number( this.doc_data.merge_info[tr_idx][td_loop_cnt].row ) );
                    colspan = Number( td_loop_cnt - Number( this.doc_data.merge_info[tr_idx][td_loop_cnt].col ) );

                    if( rowspan < 0 ) {
                        rowspan = rowspan * (-1);
                    }

                    if( colspan < 0 ) {
                        colspan = colspan * (-1);
                    } 

                    if( td_item.hasOwnProperty('attr') == false ) {
                        td_item.attr = {
                            id : 'doc_td_' + tr_idx + '_' + td_loop_cnt
                            ,rowspan : ( rowspan + 1 )
                            ,colspan : ( colspan + 1 )
                        }
                    } else {
                        td_item.attr['id'] = 'doc_td_' + tr_idx + '_' + td_loop_cnt;
                        td_item.attr['rowspan'] = ( rowspan + 1 );
                        td_item.attr['colspan'] = ( colspan + 1 );
                    }
                      
                } else {
                    
                    if( td_item.hasOwnProperty('attr') == false ) {
                        
                        td_item.attr = {
                            id : 'doc_td_' + tr_idx + '_' + td_loop_cnt
                        }
                    } else {
                        td_item.attr['id'] = 'doc_td_' + tr_idx + '_' + td_loop_cnt;
                    }

                }

                doc_html_tr.appendChild(this.createHtml({ 
                    tag : ( td_item.hasOwnProperty('tag') == true ) ? td_item.tag : 'td'
                    ,attr : ( td_item.hasOwnProperty('attr') == true ) ? td_item.attr : {id : 'doc_td_' + tr_idx + '_' + td_loop_cnt }
                    ,child : ( td_item.hasOwnProperty('child') == true ) ? td_item.child : []
                }));

                td_loop_cnt++;
            }

            doc_html.appendChild( doc_html_tr );
            
        }

        console.log( this.doc_data );

        return doc_html;

    }

    /**
     * 병합 정보 확인
     */
    imasic.mergeExist = function( arg_row, arg_col ){

        var return_val = false;

        if( this.doc_data.merge_info.hasOwnProperty( arg_row ) == true ) {
            if( this.doc_data.merge_info[arg_row] ) {
                if( this.doc_data.merge_info[arg_row].hasOwnProperty( arg_col ) == true ) {
                    if( this.doc_data.merge_info[arg_row][arg_col] ) {
                        return_val = true;
                    }
                }
            }
            
        }
       
        return return_val;

    }

    /**
     * 병합처리
     */
    imasic.mergeHandler = function(){
        
        var rowspan = 0;
        var colspan = 0;

        for(var row_idx in this.doc_data.merge_info ) {            
            for(var col_idx in this.doc_data.merge_info[row_idx] ) {
                if( this.doc_data.merge_info[row_idx][col_idx] ) {
                    for( var merge_row = Number( row_idx ); merge_row <= Number( this.doc_data.merge_info[row_idx][col_idx].row ); merge_row++ ) {
                        for( var merge_col = Number(col_idx); merge_col <= Number( this.doc_data.merge_info[row_idx][col_idx].col ); merge_col++ ) {
                            // console.log( document.getElementById( 'doc_td_'+ merge_row + '_' + merge_col ) );

                            if( document.getElementById( 'doc_td_'+ merge_row + '_' + merge_col ) ) {

                                rowspan = document.getElementById( 'doc_td_'+ merge_row + '_' + merge_col ).getAttribute('rowspan');
                                colspan = document.getElementById( 'doc_td_'+ merge_row + '_' + merge_col ).getAttribute('colspan');

                                if( !( (rowspan) && (colspan) )  ) {
                                    document.getElementById( 'doc_td_'+ merge_row + '_' + merge_col ).style.display = 'none';
                                }
                                
                            }

                            

                    //         if( !( ( row_idx == merge_row ) && (col_idx == merge_col) ) ) {
                    //             // console.log( 'doc_td_'+ merge_row + '_' + merge_col );
                    //             console.log( document.getElementById( 'doc_td_'+ merge_row + '_' + merge_col ) );
                    //             // this.doc_data.merge_index[ 'doc_td_'+ merge_row + '_' + merge_col ] = 

                    //             if( document.getElementById( 'doc_td_'+ merge_row + '_' + merge_col ) ) {
                    //                 document.getElementById( 'doc_td_'+ merge_row + '_' + merge_col ).style.display = 'none';
                    //             }
                                
                    //         } 
                        }
                    }
                }
            }
        }
        

    }

    /** 
     * json attr 의 값을 생성된 html 값에 부여한다.
    */
    imasic.setTagAttr = function( arg_element, arg_data ){

        //# 해당 태그의 속성을 정의한다.
        if( arg_data.hasOwnProperty('attr') == true ) {

            for( var item in arg_data.attr ) {
                switch( item ) {
                    case 'display' : {
                        arg_element.style.display = arg_data.attr.display;
                        break;
                    }
                    case 'width' : {
                        arg_element.style.width = arg_data.attr.width;                                
                        break;
                    }
                    case 'height' : {
                        arg_element.style.height = arg_data.attr.height;
                        break;
                    } 
                    case 'borderTop' : {
                        arg_element.style.borderTop = arg_data.attr.borderTop;
                        break;
                    }
                    case 'borderLeft' : {
                        arg_element.style.borderLeft = arg_data.attr.borderLeft;
                        break;
                    } 
                    case 'borderRight' : {
                        arg_element.style.borderRight = arg_data.attr.borderRight;
                        break;
                    } 
                    case 'font_size' : {
                        arg_element.style.fontSize = arg_data.attr.font_size;
                        break;
                    }
                    case 'border' : {
                        arg_element.style.border = arg_data.attr.border;
                        break;
                    }
                    case 'resize' : {
                        arg_element.style.resize = arg_data.attr.resize;
                        break;
                    }
                    case 'backgroundColor' : {
                        arg_element.style.backgroundColor = arg_data.attr.backgroundColor;
                        break;
                    }
                    
                    default : {
                        arg_element.setAttribute( item , arg_data.attr[ item ] );
                    }
                }
            }
        }
    }

    /** 
     * 작성 모드 element 생성
    */
    imasic.createWrite = function( arg_data ){
        
        var result = '';
        var new_tag = '';

        if( arg_data.hasOwnProperty('tag') == true ) {

            new_tag = document.createElement( arg_data.tag );

            //# tag set
            imasic.setTagAttr( new_tag, arg_data );

            if( arg_data.hasOwnProperty('child') == true ) {
                
                for( var child_item of arg_data.child ){		
                    
                    new_tag.appendChild( this.createHtml( child_item ) );

                }
            } 

            result = new_tag;
        } else {
            
            if( arg_data.hasOwnProperty('text') == true ) {
                result = document.createTextNode( arg_data.text );                
            }

        }

        return result;

    }

    /** 
     * 읽기 모드 element 생성
    */
    imasic.createRead = function( arg_data ){

        var result = '';
        var new_tag = '';

        if( arg_data.hasOwnProperty('tag') == true ) {

            new_tag = document.createElement( arg_data.tag );

            //# tag set
            imasic.setTagAttr( new_tag, arg_data );

            if( arg_data.hasOwnProperty('child') == true ) {
                
                for( var child_item of arg_data.child ) {		
                    
                    if( child_item.tag == 'input' ) {
        
                        // console.log( child_item.attr.value );
                        if( child_item.attr.type == 'radio' ) {
            
                            var radio_text = '';
            
                            if( child_item.attr.hasOwnProperty( 'checked' ) == true ) {
                                
                                
                                if( child_item.attr.value ) {
                                    new_tag.appendChild( document.createTextNode( child_item.attr.value ) );
                                }
                                
                            }
            
                        } else  {
                            
                            if( child_item.attr.value ) {
                                new_tag.appendChild( document.createTextNode( child_item.attr.value ) );
                            }
            
                        }
                        
                    } else {
            
                        if( ( child_item.tag != 'label')  ) {
                            if( child_item.tag == 'textarea' ) {
                                if( child_item.hasOwnProperty('child') == true ) {

                                    var text_value = child_item.child[0].text;

                                    var child_arr = text_value.split(/\n/g);

                                    for( var child_idx in child_arr ) {
                                        
                                        if( child_arr[ child_idx ] == '' ){
                                           
                                            new_tag.appendChild( this.createHtml({
                                                tag : 'br'
                                            }) );

                                        } else {
                                            
                                            new_tag.appendChild( document.createTextNode( child_arr[ child_idx ] ) );
                        
                                            if( (child_arr.length - 1) > child_idx ) {
                                                new_tag.appendChild( this.createHtml({
                                                    tag : 'br'
                                                }) );
                                            }
                                        }

                                    }

                                    // var text_value = text_value.replace(/\n/g, '<br>');

                                    // new_tag.appendChild( document.createTextNode( text_value ) );
                                } else {
                                    new_tag.appendChild( document.createTextNode( '' ) );
                                }
                                
                            } else {                                        
                                new_tag.appendChild( this.createHtml( child_item ) );
                            }
                            
                        }
                        
                    }

                }
            } 

            result = new_tag;

        } else {
            
            if( arg_data.hasOwnProperty('text') == true ) {
                result = document.createTextNode( arg_data.text );                
            }

        }

        return result;
    }


    imasic.createHtml = function( arg_data ){

        if( imasic.make_type == 'edit' ) {
            return imasic.createWrite( arg_data );
        } else {
            return imasic.createRead( arg_data );
        }
        
    }

    window.imasic = imasic;

}(window, document));