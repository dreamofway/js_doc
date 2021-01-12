<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    식품유형 관리 > [<?=$large_type_title?>]
                </h1>                   
            </section>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <h5 class="header-title m-t-0">
                            <b>분류정보</b>                        
                        </h5>
                        <hr class="m-t-0">
                        <div class="table-responsive m-b-0">

                        <!-- start -->

                        <div class="write_contents" style="">
                            <div class="write_box_wrap" >	
                                <div class="task_box" >
                                    
                                    <div class="task_box_title" ><h2>중분류</h2></div>
                                    <div class="task_box_top_area" >&nbsp;</div>
                                    <div class="build_field" >
                                        <ul class="field_ul" id="middle_area" >
											
                                        </ul>
                                    </div>

                                    <div class="write_input_area">
                                        <input type="hidden" id="middle_edit_code"  />
                                        <input type="text" class="write_input" name="middle_write" id="middle_write" placeholder="중분류"  />
                                    </div>

                                    <div class="write_btn_area">
                                        <span class="buildField_btnSave" onclick="foodHandler.middleAction('save', '')">저장</span>
                                        <span class="buildField_btnCancel" onclick="foodHandler.middleAction('cancel', '')" >취소</span> 
                                    </div>

                                </div>
                                <div class="task_box" >
                                    <div class="task_box_title" ><h2>유형</h2></div>
                                    <div id="field_status_text_A" ><span class="field_choice_title" id="choice_title" ></span> 의 유형</div>
                                    <div id="field_status_text_B" ><span class="field_choice_title" >유형의 중분류를 먼저 선택해주세요.</span></div>
                                    <div class="build_option" >
                                        <ul id="type_area" >
                                            
                                        </ul>
                                    </div>

                                    <div class="write_input_area">
                                        <input type="hidden" id="type_edit_code"  />
                                        <input type="text" class="write_input" name="type_write" id="type_write" placeholder="유형"  />
                                    </div>

                                    <div class="write_btn_area">
                                        <span class="buildField_btnSave" onclick="foodHandler.typeAction('save', '')">저장</span>
                                        <span class="buildField_btnCancel" onclick="foodHandler.typeAction('cancel', '')" >취소</span>
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
        foodHandler.init();
    });

</script>

<!-- 중분류 리스트 폼 -->
<script id="tmplate_middle" type="text/x-jquery-tmpl">
	<li id="types_${code}" >
		<span class="buildOption_btnAdd" onclick="foodHandler.choiceItem('${code}')" >선택</span>
		${title} 
		<span class="buildField_btnDel" onclick="foodHandler.middleAction('del', '${code}')" >삭제</span>
		<span class="buildField_btnShow" onclick="foodHandler.middleAction('edit', '${code}')" >수정</span>
	</li>
</script>
<!-- 유형 리스트 폼 -->
<script id="tmplate_types" type="text/x-jquery-tmpl">
	<li id="middle_${code}" >
		${title}
		<span class="buildOption_btnDel" onclick="foodHandler.typeAction('del', '${code}')" >삭제</span>
		<span class="buildOption_btnEdit" onclick="foodHandler.typeAction('edit', '${code}')" >수정</span>
	</li>
</script>
<script type="text/javascript" src="<?=$aqua_view_path;?>/public/js/view.food_type_set.js"></script>