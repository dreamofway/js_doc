<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    <?=$doc_type?> 문서 <?=$page_work?>
                    <button type="button" class="pull-right btn btn-inverse waves-effect w-md m-l-5" onclick="location.href='./<?=$page_name?>_list?page=<?=$page?><?=$params?>'">목록</button> 
                </h1>                
            </section>

            <form class="form-horizontal" role="form" method="post" id="form_write" enctype="multipart/form-data"  action="./<?=$page_name?>_proc" >                
                <input type="hidden" name="mode" value="<?=$mode?>" />
                <input type="hidden" name="arg_tag_code" value="<?=$arg_tag_code?>" />
                <input type="hidden" name="company_idx" id="company_idx" value="<?=$company_idx?>" />
                <input type="hidden" name="page" value="<?=$page?>" />
                <input type="hidden" name="top_code" value="<?=$top_code?>" />
                <input type="hidden" name="left_code" value="<?=$left_code?>" />
                <input type="hidden" name="ref_params" value="<?=$params?>" />
                <input type="hidden" name="file_idx" id="file_idx" value="<?=$file_idx?>" />

            <!-- 기업정보 -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        
                        <div class="table-responsive m-b-0">
                            <h5 class="header-title m-b-10">
                                <b>생성</b> 
								<button type="button" class="pull-right btn btn-sm btn-purple waves-effect w-md m-l-5" style="top:-4px;" onclick="addForm()" >추가</button>
                            </h5>
                            <hr class="m-t-0">
                            <div id="table_create_area" >
                            </div>
                        </div> 

                    </div>

                </div>
            </div>
            <!-- //기업정보 -->


          
           
            </form>

            <div class="row"> 
                <div class="col-lg-12">
                    <button type="button" class="pull-right btn btn-inverse waves-effect w-md m-l-5" onclick="location.href='./<?=$page_name?>_list?page=<?=$page?><?=$params?>'">목록</button>                     
               </div>
            </div>


        </div> <!-- // container -->
    </div> <!-- // content -->
</div> <!-- // content-page -->


<style>    
    .table>tbody>tr>th.info {
        width: 15%;
    }
    /* table input {
        width: 40% !important; display: inline-block !important;
    } */
</style>
<script>
	
	var doc = new createDoc;
	
	doc.newForm({
		element : 'table_create_area'
		, class_obj : 'doc'
	});

	function createDoc() {

		this.insert_area = '';
		this.table_obj = '';
		this.table_width = 800;
		this.total_rows = 10;
		this.total_columns = 10;
		this.tr_font_size = 9;
		this.td_font_size = 9;
		this.td_height = 30;
		this.doc_menu = '';
		this.doc_obj = {};
		this.selected_td = {};
		this.table_form = '';
		this.td_style = '';
		
		/**
		 테이블 생성 후처리를 수행한다.
		*/
		this.createTableAfterHandler = function( arg_table_obj ) {
			
			this.insert_area.innerHTML = '';

			this.insert_area.appendChild( arg_table_obj );
			//# 테이블 td에 이벤트를 등록한다.
			this.domEventHandler({
				selector_type : 'class'
				,selector : 'doc_tds'
				,event : 'mousedown'
				,fn : this.mouseDownHandler
			});
			
			this.table_obj = arg_table_obj;
			
		}

		/**
		* 비어있는 테이블을 생성한다.
		*/
		this.newForm = function( arg_data ){

			if( document.getElementById( arg_data.element ) ) {
				this.insert_area = document.getElementById( arg_data.element );
			} else {
				console.error( '입력 값 "' + arg_data.element + '" 과 일치하는 태그를 찾을 수 없습니다..');
			}
			
			if( arg_data.hasOwnProperty('table_width') == true ){
				this.table_width = arg_data.table_width;
			}


			// this.total_rows = prompt('전체 행을 입력하세요');
			// this.total_columns = prompt('전체 열을 입력하세요');
			
			
			this.createEmptyTable();
			this.makeMenu();
		}

		/**
		* 비어있는 테이블을 생성한다.
		*/
		this.createEmptyTable = function(){

			var new_table = '';
			var new_tr = '';			
			
			this.table_form = {
				tag : 'table'
				, attr : {
					id : 'doc_table'
					,style : 'border-collapse: collapse; border-spacing:0; width:'+ this.table_width +'px'
				}
			};

			new_table = this.create( this.table_form );
			
			//# 테이블의 row 와 td 수를 반복하여 초기 테이블을 생성한다.
			for( loop_row = 0;  loop_row < this.total_rows;  loop_row++ ) {
				
				this.doc_obj[ 'doc_tr_' + loop_row ] = {
					tag : 'tr'
					, attr : {
						id : 'doc_tr_' + loop_row
					}
					,child : {}
				};

				new_tr = this.create({
					tag : 'tr'
					, attr : {
						id : 'doc_tr_' + loop_row
					}
				});
				
				for( loop_col = 0;  loop_col < this.total_columns;  loop_col++ ) {
					
					this.td_style = 'width:'+ ( this.table_width / this.total_columns ) +'px; height:'+ this.td_height +'px; border:1px solid; font-size:'+ this.td_font_size +'px; text-align:center; cursor:pointer';

					this.doc_obj[ 'doc_tr_' + loop_row ]['child'][ 'doc_td_'+ loop_row +'_' + loop_col ] = {
						tag : 'td'
						,text : ''
						, attr : {
							id : 'doc_td_'+ loop_row +'_' + loop_col
							,class : 'doc_tds'								
							,style : this.td_style
						}
					};

					new_tr.appendChild(
						this.create({
							tag : 'td'
							,text : ''
							, attr : {
								id : 'doc_td_'+ loop_row +'_' + loop_col
								,class : 'doc_tds'								
								,style : this.td_style
							}
						})
					);
				}
				
				new_table.appendChild( new_tr );
			}

			this.createTableAfterHandler( new_table );
			
		}

		/**
		* 우측클릭 이벤트 메뉴 html 생성
		*/
		this.makeMenu = function() {
			
			var menu_info = [
				{type : 'menu', title : '작성' ,id : '_doc_write', event_fn : this.docWriteHandler }
				,{type : 'menu', title : '내용삭제' ,id : '_doc_content_del', event_fn : this.docContentDelHandler }
				,{type : 'hr', title : '' ,id : '' }
				,{type : 'menu', title : 'input 삽입' ,id : '_doc_insert_input', event_fn : this.docInsertInputHandler }
				,{type : 'menu', title : 'radio 삽입' ,id : '_doc_insert_radio', event_fn : this.docInsertRadioHandler }
				,{type : 'menu', title : 'checkbox 삽입' ,id : '_doc_insert_checkbox', event_fn : this.docInsertCheckboxHandler }
				,{type : 'hr', title : '' ,id : '' }
				,{type : 'menu', title : 'th 로 변경' ,id : '_doc_change_th', event_fn : this.docChangeThHandler }
				,{type : 'menu', title : 'td 로 변경' ,id : '_doc_change_td', event_fn : this.docChagneTdHandler }
				,{type : 'hr', title : '' ,id : '' }
				,{type : 'menu', title : '병합' ,id : '_doc_merge', event_fn : this.docMergeHandler }
				,{type : 'hr', title : '' ,id : '' }
				,{type : 'menu', title : '너비 변경' ,id : '_doc_edit_width', event_fn : this.docEditWidthHandler }
				,{type : 'menu', title : '높이 변경' ,id : '_doc_edit_height', event_fn : this.docEditHeightHandler }
				,{type : 'hr', title : '' ,id : '' }
				,{type : 'menu', title : '행 삽입' ,id : '_doc_add_row', event_fn : this.docAddRowHandler }
				,{type : 'menu', title : '열 삽입' ,id : '_doc_add_col', event_fn : this.docAddColHandler }
				,{type : 'hr', title : '' ,id : '' }
				,{type : 'menu', title : '행 삭제' ,id : '_doc_remove_row', event_fn : this.docRemoveRowHandler }
				,{type : 'menu', title : '열 삭제' ,id : '_doc_remove_col', event_fn : this.docRemoveColHandler }
			];

			var menu_item_obj = '';
			var head_element = document.getElementsByTagName('head')[0];
			var body_element = document.getElementsByTagName('body')[0];
			
			var menu_style_contents = '';			
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
			menu_style_contents += '	}';
			menu_style_contents += '	.__doc_tb_menu_item { ';
			menu_style_contents += '		margin:7px 8px 7px 12px; ';
			menu_style_contents += '		cursor:pointer ';
			menu_style_contents += '	}';
			menu_style_contents += '	.__doc_tb_menu_item:hover { ';
			menu_style_contents += '		background-color:#eee; ';
			menu_style_contents += '	}';
			menu_style_contents += '	.__doc_tb_menu_item_line { ';
			menu_style_contents += '		margin:7px 8px 7px 12px; ';
			menu_style_contents += '	}';
			menu_style_contents += '	.__doc_tb_menu_box hr { ';
			menu_style_contents += '		margin:0px;  ';
			menu_style_contents += '	}';
			
			//# 메뉴 style 태그 생성
			var menu_style_obj = this.create({
				tag : 'style'
				,text : menu_style_contents
				, attr : {
					id : '_doc_style'
				}
			});
			
			//# 메뉴 스타일 태그 삽입
			head_element.appendChild( menu_style_obj );
			
			//# 메뉴 box 생성
			var menu_html_obj = this.create({
				tag : 'div'
				, attr : {
					id : '_doc_menu'
					,class : '__doc_tb_menu_box'
				}
			});
			
			//# 메뉴 항목 생성
			for(var item of menu_info ) {

				if( item.type == 'menu' ) {
					menu_item_obj = this.create({
						tag : 'div'
						, attr : {
							id : item.id
							,class : '__doc_tb_menu_item'
						}
					});

					menu_item_obj.appendChild(this.create({
						tag : 'span'
						, text : item.title
					}));
				
				} else {
					
					menu_item_obj = this.create({
						tag : 'div'
						, attr : {
							class : '__doc_tb_menu_item_line'
						}
					});

					menu_item_obj.appendChild(this.create({
						tag : 'hr'
					}));

				}
				
				//# 메뉴 box 에 추가
				menu_html_obj.appendChild( menu_item_obj );
			}
			
			//# table 이후에 삽입
			body_element.appendChild( menu_html_obj );
			
			this.doc_menu = document.getElementById( '_doc_menu' );
			
			for( item of menu_info ) {
				
				if( item.type == 'menu' ) {

					this.domEventHandler({
						selector_type : 'id'
						,selector : item.id
						,event : 'click'
						,fn : item.event_fn
					});

				}
				
			}

		}
		
		/**
		* html 객체 생성
		*/
		this.create = function( arg_tag ){
			
			var result = '';
			
			if( arg_tag.hasOwnProperty('tag') == true ) {
				
				var new_tag = document.createElement( arg_tag.tag );

				if( arg_tag.hasOwnProperty('attr') == true ) {

					if( arg_tag.attr.hasOwnProperty('class') == true ) {						
						new_tag.setAttribute('class', arg_tag.attr.class );
					}

					if( arg_tag.attr.hasOwnProperty('id') == true ) {						
						new_tag.setAttribute('id', arg_tag.attr.id);
					}

					if( arg_tag.attr.hasOwnProperty('type') == true ) {						
						new_tag.setAttribute('type', arg_tag.attr.type);
					}

					if( arg_tag.attr.hasOwnProperty('name') == true ) {						
						new_tag.setAttribute('name', arg_tag.attr.name);
					}

					if( arg_tag.attr.hasOwnProperty('title') == true ) {						
						new_tag.setAttribute('title', arg_tag.attr.title);
					}

					if( arg_tag.attr.hasOwnProperty('src') == true ) {						
						new_tag.setAttribute('src', arg_tag.attr.src);
					}

					if( arg_tag.attr.hasOwnProperty('style') == true ) {
						new_tag.setAttribute('style', arg_tag.attr.style);
					}

					if( arg_tag.attr.hasOwnProperty('for') == true ) {						
						new_tag.setAttribute('for', arg_tag.attr.for);
					}

					if( arg_tag.attr.hasOwnProperty('selected') == true ) {						
						new_tag.setAttribute('selected', arg_tag.attr.selected);
					}

					if( arg_tag.attr.hasOwnProperty('checked') == true ) {						
						new_tag.setAttribute('checked', arg_tag.attr.checked);
					}
					
					if( arg_tag.attr.hasOwnProperty('value') == true ) {						
						new_tag.setAttribute('value', arg_tag.attr.value);
					}

					if( arg_tag.attr.hasOwnProperty('rowspan') == true ) {						
						new_tag.setAttribute('rowspan', arg_tag.attr.rowspan);
					}

					if( arg_tag.attr.hasOwnProperty('colspan') == true ) {						
						new_tag.setAttribute('colspan', arg_tag.attr.colspan);
					}

					if( arg_tag.attr.hasOwnProperty('merge_data') == true ) {						
						new_tag.setAttribute('data-merge', arg_tag.attr.merge_data);
					}

					if( arg_tag.attr.hasOwnProperty('display') == true ) {						
						new_tag.style.display = arg_tag.attr.display;
					}

					if( arg_tag.attr.hasOwnProperty('width') == true ) {						
						new_tag.style.width = arg_tag.attr.width;
					}

					if( arg_tag.attr.hasOwnProperty('height') == true ) {						
						new_tag.style.height = arg_tag.attr.height;
					}
					

				}
				
				if( arg_tag.hasOwnProperty('text') == true ) {
					new_tag.appendChild( document.createTextNode( arg_tag.text ) );
				}

				if( arg_tag.hasOwnProperty('child') == true ) {

					if( arg_tag.child.length > 0 ) {
						for( child_item of arg_tag.child ){						
							new_tag.appendChild( this.create( child_item ) );
						}
					} else {						
						new_tag.appendChild( this.create( arg_tag.child ) );
					}
					
				}

				result = new_tag;
				
			} else {

				if( arg_tag.hasOwnProperty('text') == true ) {
					result = document.createTextNode( arg_tag.text );
				}

			}
			
			return result;

		}
		
		/**
		* 이벤트 등록 준비 처리 함수
		*/
		this.domEventHandler = function( arg_data ){
			
			var get_element = '';
			var element_check = '';

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
		* 이벤트 등록 함수
		*/
		this.addDomEvent = function( arg_data ) {

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
		* td id로 table 객체의 tr key 값을 반환
		*/
		this.getTrKey = function( arg_id ) {
			
			var id_arr = arg_id.split('_');
			
			return id_arr[0] + '_tr_' +  id_arr[ id_arr.length - 2];

		}
		
		/**
		* 메뉴 애니메이션을 처리한다.
		*/
		this.menuActionHandler = function( arg_task ){
			
			if( arg_task.hasOwnProperty('top') == true ) {
				this.doc_menu.style.top = arg_task.top;
			}

			if( arg_task.hasOwnProperty('left') == true ) {
				this.doc_menu.style.left = arg_task.left;
			}
			
			this.doc_menu.style.display = arg_task.display;

		}

		//////////////////////////////////////////////////////////// 이벤트 처리자 ////////////////////////////////////////////////////////////
		/**
		* td mousedown 이벤트 처리자
		*/
		this.mouseDownHandler = function( arg_event ) {
			
			//# 현재 선택된 위치의 정보를 대입한다.
			doc.selected_td = {
				id : this.id
				,element : this
				,parent : this.parentNode
			};
			
			for( var item of  doc.table_obj.getElementsByClassName('doc_tds') ) {
				item.style.border = '1px solid';
			}

			//# 선택 표시
			this.style.border = '2px solid #ff5050';
			
			//# 우클릭인 경우
			if( ( arg_event.button == 2 ) || ( arg_event.which == 2 ) ) {

				doc.menuActionHandler({
					top : arg_event.y + 'px'
					,left : arg_event.x + 'px'
					,display : 'block'
				});
				
				//# 테이블의 우클릭 이벤트를 종료한다.
				doc.table_obj.addEventListener('contextmenu', function() {						  
					event.preventDefault();
				});

			} else {

				doc.menuActionHandler({
					display:'none'
				});

			}

		}
		
		/**
		* 해당 객체에 택스트를 삽입 할 수있도록 UI를 지원 한다.
		*/
		this.docWriteHandler = function() {
			
			var selected = doc.selected_td;
			var input_text = '';

			doc.menuActionHandler({
				display:'none'
			});
			
			input_text = prompt('입력 하세요');
			
			if( input_text ) {

				doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].text = input_text;
				selected.element.appendChild(doc.create({text: input_text}));
				
			}

		}
		
		/**
		* td 내용을 초기화 한다.
		*/
		this.docContentDelHandler = function(){

			var selected = doc.selected_td;

			doc.menuActionHandler({
				display:'none'
			});
			
			//# 영역 초기화
			doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].text = '';
			doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].child = {};
			selected.element.innerHTML = '';

		}

		/**
		* 해당 객체에 input Teg 를 삽입한다.
		*/
		this.docInsertInputHandler = function() {
			
			var selected = doc.selected_td;
			var input_width = '90%';

			//# 메뉴 숨김
			doc.menuActionHandler({
				display:'none'
			});

			input_width = prompt('너비를 입력 하세요');
			
			doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].child = {
				tag : 'input'
				, attr : {
					type : 'text'
					,id : selected.id + '_input'
					,class : '__doc_td_inputs'
					,style : 'width:' + input_width
				}
			};

			selected.element.appendChild( doc.create( doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].child ) );
		}

		/**
		* 해당 객체에 input radio Teg 를 삽입한다.
		*/
		this.docInsertRadioHandler = function() {
			
			var selected = doc.selected_td;

			//# 메뉴 숨김
			doc.menuActionHandler({
				display:'none'
			});

			//# 영역 초기화
			doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].text = '';
			doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].child = [];
			selected.element.innerHTML = '';

			//# radio 1 
			doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].child.push( {
				tag : 'span'
				, attr : {					
					id : selected.id + '_radio_0_span'					
				}
				, child : [
					{
						tag : 'input'
						, attr : {
							type : 'radio'
							,name : selected.id + '_radio'
							,id : selected.id + '_radio_0'
							,class : '__doc_td_radios'
							,value : 'Y'							
							,checked : 'checked'							
						}
					}
					, {
						tag : 'label'
						,text : '적합'
						, attr : {
							for : selected.id + '_radio_0'
						}
					}
				]
			});

			//# radio 2  
			doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].child.push( {
				tag : 'span'
				, attr : {					
					id : selected.id + '_radio_1_span'					
				}
				, child : [
					{
						tag : 'input'
						, attr : {
							type : 'radio'
							,name : selected.id + '_radio'
							,id : selected.id + '_radio_1'
							,class : '__doc_td_radios'
							,value : 'N'							
						}
					}
					, {
						tag : 'label'
						,text : '부적합'
						, attr : {
							for : selected.id + '_radio_1'
						}
					}
				]
			});

			
			for( var item of doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].child ){				
				selected.element.appendChild( doc.create( item ) );
			}

			
		}
		/**
		* 해당 객체에 input checkbox Teg 를 삽입한다.
		*/
		this.docInsertCheckboxHandler = function() {

			var selected = doc.selected_td;

			//# 메뉴 숨김
			doc.menuActionHandler({
				display:'none'
			});
			
			//# 영역 초기화
			doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].text = '';
			doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].child = {};
			selected.element.innerHTML = '';
			
			doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].child = {
				tag : 'input'
				, attr : {
					type : 'checkbox'
					,name : selected.id + '_check'
					,id : selected.id + '_check'
					,class : '__doc_td_checks'
					,value : 'Y'							
					,checked : 'checked'							
				}
			};

			selected.element.appendChild( doc.create( doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].child ) );
		}
		/**
		* 해당 객체를 tr Tag로 변환한다.
		*/
		this.docChangeThHandler = function() {

			var selected = doc.selected_td;
			
			//# 메뉴 숨김
			doc.menuActionHandler({
				display:'none'
			});

			doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].tag = 'th';
			doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].attr.style += ';background:#ddd;';

			doc.reamakeTable();
		}
		/**
		* 해당 객체를 td Tag로 변환한다.
		*/
		this.docChagneTdHandler = function() {

			var selected = doc.selected_td;
			
			//# 메뉴 숨김
			doc.menuActionHandler({
				display:'none'
			});

			doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].tag = 'td';
			doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].attr.style = doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].attr.style.replace( ';background:#ddd', '' );

			doc.reamakeTable();

		}
		/**
		* 선택된 객체들을 병합한다.
		*/
		this.docMergeHandler = function() {

			var selected = doc.selected_td;
			var input_merge_row = '';
			var input_merge_col = '';
			var get_no = '';
			var tr_no = '';
			var td_no = '';
			var row_limit = '';
			var col_limit = '';
			var change_td = '';
			var set_merge_data = [];
			var get_merge_data = '';
			var get_merge_data_arr = [];
			var get_merge_key_arr = [];

			//# 메뉴 숨김
			doc.menuActionHandler({
				display:'none'
			});

			input_merge_row = prompt('행 수');
			input_merge_col = prompt('열 수');

			if( input_merge_row == '' ) {
				input_merge_row = 1;
			}

			if( input_merge_col == '' ) {
				input_merge_col = 1;
			}
			
			get_no = selected.id.split('_'); 
			tr_no = Number( get_no[2] );
			td_no = Number( get_no[3] );

			row_limit = tr_no + Number( input_merge_row );
			col_limit = td_no + Number( input_merge_col );
			
			if( doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].attr.hasOwnProperty( 'merge_data' ) == true ) {
				get_merge_data = doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].attr.merge_data;

				get_merge_data_arr = get_merge_data.split(',');

			}


			for(loop_row = tr_no; loop_row < row_limit; loop_row++ ) {
				
				for(loop_col = td_no; loop_col < col_limit; loop_col++ ) {

					change_td = 'doc_td_'+loop_row+'_'+loop_col;
					
					if( selected.id !== 'doc_td_'+loop_row+'_'+loop_col ) {
						
						doc.doc_obj[ doc.getTrKey( change_td ) ]['child'][ change_td ].attr.display = 'none';

						set_merge_data.push(change_td);
						
						if( get_merge_data !== '' ){
							get_merge_data = get_merge_data.replace(change_td, '');
							
							get_merge_data_arr.splice( get_merge_data_arr.indexOf(change_td) , 1)

						}
					} 
					
				}

			}

			for(var item of get_merge_data_arr){
				doc.doc_obj[ doc.getTrKey( item ) ]['child'][ item ].attr.display = 'table-cell';				
			}
			
			doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].attr.rowspan = input_merge_row;
			doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].attr.colspan = input_merge_col;
			doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].attr.merge_data = set_merge_data.join(',');

			doc.reamakeTable();

		}

		/**
		 병합 해제
		 */
		this.docMergeCancelHandler = function() {

			var selected = doc.selected_td;
			
			//# 메뉴 숨김
			doc.menuActionHandler({
				display:'none'
			});


		}

		/**
		* 선택된 객체의 하단에 신규 row를 추가한다.
		*/
		this.docAddRowHandler = function() {

			var selected = doc.selected_td;
			var new_doc_obj = {};
			var now_no = '';
			var loop_row = 0;
			var loop_col = 0;
			var tr_id = '';
			var current_tr = '';
			var current_td = '';
			
			//# 메뉴 숨김
			doc.menuActionHandler({
				display:'none'
			});

			new_row_no = ( Number( selected.id.split('_')[2] ) ) + 1;
			
			//# 현재 등록된 tr 기준으로 반복
			for( loop_row = 0; loop_row <= Object.keys( doc.doc_obj ).length; loop_row++ ){
				
				tr_id = 'doc_tr_' + loop_row;
				//# 선택한 행보다 작은 경우 기존 데이터 복사
				if( new_row_no > loop_row ) {

					current_tr = doc.doc_obj[ Object.keys( doc.doc_obj )[ loop_row ] ];
					new_doc_obj[ current_tr.attr.id ] = current_tr;					

				} else {
					//# 선택한 행보다 같거나 큰 경우

					
					if( new_row_no == loop_row ) {

						//# 선택한 행과 같은 값인 경우 새로운 데이터 삽입

						new_doc_obj[ tr_id ] = {
							tag : 'tr'							
							,attr : {
								id : tr_id
							}
							,child : {}
						}
										
						for( loop_col = 0; loop_col < doc.total_columns; loop_col++ ){

							td_id = 'doc_td_'+ loop_row +'_' + loop_col;

							new_doc_obj[ tr_id ]['child'][ td_id ] = {
								tag : 'td'
								,text : ''
								, attr : {
									id : td_id
									,class : 'doc_tds'								
									,style : doc.td_style
								}
							};

						}

					} else {
						
						//# 선택한 행보다 큰 경우 key 변경하여 기존 값 대입
						current_tr = doc.doc_obj[ Object.keys( doc.doc_obj )[ loop_row-1 ] ];						
						
						new_doc_obj[ tr_id ] = {
							tag : 'tr'							
							,attr : {
								id : tr_id
							}
							,child : {}
						}

						loop_col = 0;

						for(var td_key in current_tr.child ){
							
							current_td = current_tr.child[ td_key ];
							
							td_id = 'doc_td_'+ loop_row +'_' + loop_col;
							
							new_doc_obj[ tr_id ]['child'][ td_id ] = {
								tag : current_td.tag
								,text : current_td.text
								, attr : {
									id : td_id
									,class : current_td.attr.class
									,style : current_td.attr.style
								}
							};

							loop_col++;

						}						
					}
					
				}

			}

			doc.doc_obj = new_doc_obj;

			//# 테이블 새로 생성
			doc.reamakeTable();

		}
		/**
		* 선택된 객체의 우측에 신규 column을 추가한다.
		*/
		this.docAddColHandler = function() {

			var selected = doc.selected_td;
			var new_doc_obj = {};
			var now_no = '';
			var loop_row = 0;
			var loop_col = 0;
			var tr_id = '';
			var current_tr = '';
			var current_td = '';
			
			//# 메뉴 숨김
			doc.menuActionHandler({
				display:'none'
			});

			new_td_no = ( Number( selected.id.split('_')[3] ) ) + 1;

			//# 현재 등록된 tr 기준으로 반복
			for( loop_row = 0; loop_row < Object.keys( doc.doc_obj ).length; loop_row++ ){

				current_tr = doc.doc_obj[ Object.keys( doc.doc_obj )[ loop_row ] ];
				tr_id = current_tr.attr.id;

				new_doc_obj[ tr_id ] = {
					tag : current_tr.tag							
					,attr : {
						id : tr_id
					}
					,child : {}
				}
				
				for( loop_col = 0; loop_col <= Object.keys( current_tr.child ).length; loop_col++ ){

					td_id = 'doc_td_'+ loop_row +'_' + loop_col;

					if( new_td_no > loop_col ) {

						current_td = current_tr.child[ td_id ];

						new_doc_obj[ tr_id ]['child'][ td_id ] = current_td;

					} else {

						if( new_td_no == loop_col ) {

							new_doc_obj[ tr_id ]['child'][ td_id ] = {
								tag : 'td'
								,text : ''
								, attr : {
									id : td_id
									,class : 'doc_tds'								
									,style : doc.td_style
								}
							};
							
						} else {
							
							current_td = current_tr.child[ 'doc_td_'+ loop_row +'_' + ( loop_col - 1) ];

							new_doc_obj[ tr_id ]['child'][ td_id ] = {
								tag : current_td.tag
								,text : current_td.text
								, attr : {
									id : td_id
									,class : current_td.attr.class
									,style : current_td.attr.style
								}
							};
							
						}

					}

				} // td for

			} // tr for

			doc.doc_obj = new_doc_obj;

			//# 테이블 새로 생성
			doc.reamakeTable();


		}
		/**
		* 선택된 객체의 row를 삭제한다.
		*/
		this.docRemoveRowHandler = function() {

			var selected = doc.selected_td;
			var new_doc_obj = {};	
			var tr_id = '';
			var current_tr = '';
			var current_td = '';

			//# 메뉴 숨김
			doc.menuActionHandler({
				display:'none'
			});

			if( confirm('행을 삭제하시겠습니까?\n삭제된 행의 데이터는 복구 불가능합니다.') == true ) {

				var select_row_no = ( Number( selected.id.split('_')[2] ) );

				for(var tr_key in doc.doc_obj ){
								
					current_tr = doc.doc_obj[ tr_key ];
					current_row_no = Number( current_tr.attr.id.split('_')[2] );

					if( select_row_no < current_row_no ) {

						new_doc_obj[ current_tr.attr.id ] = current_tr;	

					} else {

						if( select_row_no !== current_row_no ) {

							tr_id = 'doc_tr_' + ( current_row_no - 1 );
							tr_no = ( current_row_no - 1 );

							new_doc_obj[ tr_id ] = {
								tag : 'tr'							
								,attr : {
									id : tr_id
								}
								,child : {}
							}

							loop_col = 0;

							for(var td_key in current_tr.child ){
								
								current_td = current_tr.child[ td_key ];
								
								td_id = 'doc_td_'+ tr_no +'_' + loop_col;
								
								new_doc_obj[ tr_id ]['child'][ td_id ] = {
									tag : current_td.tag
									,text : current_td.text
									, attr : {
										id : td_id
										,class : current_td.attr.class
										,style : current_td.attr.style
									}
								};

								loop_col++;

							}	

						}
						
					} // if( select_row_no < current_row_no ) {
					
				
				} // for
				
				doc.doc_obj = new_doc_obj;

				//# 테이블 새로 생성
				doc.reamakeTable();
				
			}

		}
		/**
		* 선택된 객체의 col을 삭제한다.
		*/
		this.docRemoveColHandler = function() {
			
			var selected = doc.selected_td;
			var new_doc_obj = {};
			var now_no = '';
			var loop_row = 0;
			var loop_col = 0;
			var tr_id = '';
			var current_tr = '';
			var current_td = '';
			
			//# 메뉴 숨김
			doc.menuActionHandler({
				display:'none'
			});

			if( confirm('열을 삭제하시겠습니까?\n삭제된 열의 데이터는 복구 불가능합니다.') == true ) {

				var select_td_no = Number( selected.id.split('_')[3] );

				//# 현재 등록된 tr 기준으로 반복
				for( loop_row = 0; loop_row < Object.keys( doc.doc_obj ).length; loop_row++ ){

					current_tr = doc.doc_obj[ Object.keys( doc.doc_obj )[ loop_row ] ];
					tr_id = current_tr.attr.id;

					new_doc_obj[ tr_id ] = {
						tag : current_tr.tag							
						,attr : {
							id : tr_id
						}
						,child : {}
					}

					for( loop_col = 0; loop_col < Object.keys( current_tr.child ).length; loop_col++ ){

						

						if( select_td_no > loop_col ) {
							
							td_id = 'doc_td_'+ loop_row +'_' + loop_col;

							current_td = current_tr.child[ td_id ];

							new_doc_obj[ tr_id ]['child'][ td_id ] = current_td;

						} else {

							if( select_td_no !== loop_col ) {
								
								td_id = 'doc_td_'+ loop_row +'_' + ( loop_col - 1);

								current_td = current_tr.child[ 'doc_td_'+ loop_row +'_' + loop_col ];

								new_doc_obj[ tr_id ]['child'][ td_id ] = {
									tag : current_td.tag
									,text : current_td.text
									, attr : {
										id : td_id
										,class : current_td.attr.class
										,style : current_td.attr.style
									}
								};
								
							} 

						}

					} // td for

				} // tr for


				doc.doc_obj = new_doc_obj;

				//# 테이블 새로 생성
				doc.reamakeTable();

			}
			

		}
		
		/**
			객체 너비 변경
		 */
		this.docEditWidthHandler = function() {

			var selected = doc.selected_td;

			var input_val = prompt('너비');

			//# 메뉴 숨김
			doc.menuActionHandler({
				display:'none'
			});

			if( input_val !== '' ) {

				doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].attr.width = input_val;

				//# 테이블 새로 생성
				doc.reamakeTable();
				
			}

			
		}

		/**
			객체 높이 변경
		 */
		this.docEditHeightHandler = function() {

			var selected = doc.selected_td;

			var input_val = prompt('높이');

			//# 메뉴 숨김
			doc.menuActionHandler({
				display:'none'
			});

			if( input_val !== '' ) {

				doc.doc_obj[ doc.getTrKey( selected.id ) ]['child'][ selected.id ].attr.height = input_val;

				//# 테이블 새로 생성
				doc.reamakeTable();
				
			}

		}


		//////////////////////////////////////////////////////////// 이벤트 처리자 ////////////////////////////////////////////////////////////

		this.reamakeTable = function(){

			var new_table = '';
			var new_tr = '';
		
			new_table = this.create( this.table_form );

			for(var tr_obj in doc.doc_obj ){

				// console.log( doc.doc_obj[ tr_obj ] );

				new_tr = this.create({
					tag : doc.doc_obj[ tr_obj ].tag
					, attr : {
						id : doc.doc_obj[ tr_obj ].attr.id
					}
				});

				for(var td_obj in doc.doc_obj[ tr_obj ]['child'] ){

					// console.log( doc.doc_obj[ tr_obj ]['child'][ td_obj ] );
					new_tr.appendChild(	this.create( doc.doc_obj[ tr_obj ]['child'][ td_obj ] )	);

				}

				new_table.appendChild( new_tr );
			}

			this.createTableAfterHandler( new_table );
			
		}

	} // class




</script>
            
