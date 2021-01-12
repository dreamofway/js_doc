<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    서비스 관리 > [<?=$service_name?>] 서비스 세부 관리 
                    <!-- <button type="button" class="pull-right btn btn-primary waves-effect w-md" onclick="showAdd()">+추가</button>                  -->
                </h1>                   
            </section>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <h5 class="header-title m-t-0">
                            <b>항목정보</b>                        
                        </h5>
                        <hr class="m-t-0">
                        <div class="table-responsive m-b-0">

                        <!-- start -->

                        <div class="write_contents" style="">
                            <div class="write_box_wrap" >	
                                <div class="task_box" >
                                    
                                    <div class="task_box_title" ><h2>서비스 항목</h2></div>
                                    <div class="task_box_top_area" >&nbsp;</div>
                                    <div class="build_field" >
                                        <ul class="field_ul" id="items_area" >
											
                                        </ul>
                                    </div>

                                    <div class="write_input_area">
                                        <input type="hidden" id="items_edit_code"  />
                                        <input type="text" class="write_input" name="items_write" id="items_write" placeholder="서비스 항목명"  />
                                    </div>

                                    <div class="write_btn_area">
                                        <span class="buildField_btnSave" onclick="serviceHandler.itemsAction('save', '')">저장</span>
                                        <span class="buildField_btnCancel" onclick="serviceHandler.itemsAction('cancel', '')" >취소</span> 
                                    </div>

                                </div>
                                <div class="task_box" >
                                    <div class="task_box_title" ><h2>서비스 기능</h2></div>
                                    <div id="field_status_text_A" ><span class="field_choice_title" id="choice_title" ></span> 의 서비스 기능</div>
                                    <div id="field_status_text_B" ><span class="field_choice_title" >추가할 기능의 서비스 항목을 먼저 선택해주세요.</span></div>
                                    <div class="build_option" >
                                        <ul id="fn_area" >
                                            
                                        </ul>
                                    </div>

                                    <div class="write_input_area">
                                        <input type="hidden" id="fn_edit_code"  />
                                        <input type="text" class="write_input" name="fn_write" id="fn_write" placeholder="서비스 기능명"  />
                                    </div>

                                    <div class="write_btn_area">
                                        <span class="buildField_btnSave" onclick="serviceHandler.fnAction('save', '')">저장</span>
                                        <span class="buildField_btnCancel" onclick="serviceHandler.fnAction('cancel', '')" >취소</span>
                                    </div>


                                </div>	
                                <div style="clear:both" ></div>
                            </div>	
                        </div>
                        
                        <!-- //end -->
                     
                        </div>
                        

                    </div>                       
                </div>
            </div><!-- end row --> 

        </div> <!-- // container -->
    </div> <!-- // content -->
</div> <!-- // content-page -->



<script>
    $(function(){
        serviceHandler.init();
    });
</script>

<!-- 서비스 항목 리스트 폼 -->
<script id="tmplate_items" type="text/x-jquery-tmpl">
	<li id="fns_${code}" >
		<span class="buildOption_btnAdd" onclick="serviceHandler.choiceItem('${code}')" >선택</span>
		${title} 
		<span class="buildField_btnDel" onclick="serviceHandler.itemsAction('del', '${code}')" >삭제</span>
		<span class="buildField_btnShow" onclick="serviceHandler.itemsAction('edit', '${code}')" >수정</span>
	</li>
</script>
<!-- 서비스 항목 기능 리스트 폼 -->
<script id="tmplate_fns" type="text/x-jquery-tmpl">
	<li id="items_${code}" >
		${title}
		<span class="buildOption_btnDel" onclick="serviceHandler.fnAction('del', '${code}')" >삭제</span>
		<span class="buildOption_btnEdit" onclick="serviceHandler.fnAction('edit', '${code}')" >수정</span>
	</li>
</script>

<script type="text/javascript" src="<?=$aqua_view_path;?>/public/js/view.service_items.js"></script>
