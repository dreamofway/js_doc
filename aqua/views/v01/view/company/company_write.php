<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    기업관리 > 기업 정보 <?=$page_work?>
                    <button type="button" class="pull-right btn btn-inverse waves-effect w-md m-l-5" onclick="location.href='./<?=$page_name?>_list?page=<?=$page?><?=$params?>'">목록</button> 
                </h1>                
            </section>

            <form class="form-horizontal" role="form" method="post" id="form_write" enctype="multipart/form-data"  action="./company_proc">                
                <input type="hidden" name="mode" value="<?=$mode?>" />
                <input type="hidden" name="company_idx" value="<?=$company_idx?>" />
                <input type="hidden" name="page" value="<?=$page?>" />
                <input type="hidden" name="top_code" value="<?=$top_code?>" />
                <input type="hidden" name="left_code" value="<?=$left_code?>" />
                <input type="hidden" name="ref_params" value="<?=$params?>" />

            <?php
                if( $mode == 'edit' ) {
            ?>

            <!-- QR CODE  -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                    
                        <div class="table-responsive m-b-0">
                            <h5 class="header-title m-b-10">
                                <b>QR CODE 정보</b>                                
                            </h5>
                            <hr class="m-t-0">
                            
                            <table class="table table-bordered text-left">
                                <tbody>
                                    <tr style="height:200px">
                                        <th class="info middle-align">QR CODE</th>
                                        <td  >
                                            <?php
                                                if( isset( $path ) == true ) {
                                            ?>
                                            <img src="<?=$path?>/<?=$server_name?>" style="height:170px" />
                                            <br>
                                            <button type="button" class="btn btn-sm btn-purple waves-effect w-md m-l-15" style="margin-left:25px" onclick="location.href='/file_down.php?key=<?=$idx?>'">다운로드</button>
                                            <input type="hidden" name="currnet_qrcode_idx" value="<?=$idx?>" />            
                                            <?php
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            

                        </div> 
                    
                    </div>

                </div>
            </div>
            <!-- // QR CODE  -->
            <?php
                }
            ?>

            <!-- 기본정보 -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        
                        <div class="table-responsive m-b-0">
                            <h5 class="header-title m-b-10">
                                <b>기본정보</b>                    
                            </h5>
                            <hr class="m-t-0">
                            <table class="table table-bordered text-left">
                                <tbody>
                                    <tr>
                                        <th class="info middle-align">국가</th>
                                        <td colspan="3">

                                        <?php                                        
                                            if( count( $country_codes ) > 0 ) {
                                        ?>
                                        <select class="form-control" name="country_code" id="country_code" style="width:200px" >
                                            <?php
                                                foreach( $country_codes AS $item ) {
                                            ?>
                                            <option value="<?=$item['country_code']?>" <?=($country_code == $item['country_code'] ? 'selected="selected"' : '' )?> ><?=$item['country_name']?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                        <?php
                                            }
                                        ?>    

                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="info middle-align">회사명</th>
                                        <td colspan="3">
                                            <input class="form-control" type="text" id="company_name" name="company_name" value="<?=$company_name?>" data-valid="str" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="info middle-align">사업자등록번호</th>
                                        <td colspan="3">
                                            <input class="form-control" type="text" id="registration_no" name="registration_no" maxlength="10" value="<?=$registration_no?>" data-valid="num/10" />
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th class="info middle-align">대표명</th>
                                        <td colspan="3">
                                            <input class="form-control" type="text" id="ceo_name" name="ceo_name" value="<?=$ceo_name?>" data-valid="kr|en" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">전화번호</th>
                                        <td colspan="3">
                                            <input class="form-control" type="text" id="company_tel" name="company_tel" placeholder="'-'없이 입력하세요"  maxlength="11" value="<?=$company_tel?>" data-valid="num/10" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">팩스번호</th>
                                        <td colspan="3">
                                            <input class="form-control" type="text" id="company_fax" name="company_fax" placeholder="'-'없이 입력하세요" maxlength="11" value="<?=$company_fax?>" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">홈페이지</th>
                                        <td colspan="3">
                                            <input class="form-control" type="text" id="company_homepage" name="company_homepage" value="<?=$company_homepage?>" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">주소</th>
                                        <td colspan="3">
                                            <input class="form-control" type="text" id="zip_code" name="zip_code" value="<?=$zip_code?>" maxlength="6" readonly="readonly"style="width:80px !important " /> 
                                            <button type="button" class="btn btn-sm btn-primary waves-effect waves-light m-l-10" onclick="findAddress()">주소찾기</button>
                                            <br><br>
                                            <input class="form-control" type="text" id="addr" name="addr" value="<?=$addr?>" readonly="readonly" style="width:80% !important " />
                                            <br><br>
                                            <input class="form-control" type="text" id="addr_detail" name="addr_detail" placeholder="상세주소" value="<?=$addr_detail?>" style="width:80% !important " />
                                        </td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div> 

                    </div>

                </div>
            </div>
            <!-- //기본정보 -->
            
            <?php
                if( $mode == 'ins' ) {
            ?>
            <!-- 신규 담당자 정보 입력  -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                    
                        <div class="table-responsive m-b-0">
                            <h5 class="header-title m-b-10">
                                <b>담당자 정보</b>
                            </h5>
                            <hr class="m-t-0">
                            
                            <table class="table table-bordered text-left">
                                <tbody>
                                    
                                    <tr>
                                        <th class="info middle-align">휴대폰 번호(ID)</th>
                                        <td colspan="3">
                                            <input class="form-control" type="text" id="phone_no" name="phone_no" placeholder="'-'없이 입력하세요" maxlength="11" value="<?=$phone_no?>" data-valid="num/11" />  
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th class="info middle-align">비밀번호</th>
                                        <td colspan="3">
                                            <input class="form-control" type="password" id="password" name="password" data-valid="pw" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">비밀번호 확인</th>
                                        <td colspan="3">
                                            <input class="form-control" type="password" id="re_password" name="re_password"  />
                                            <span class="m-l-10" id="re_pw_result"></span>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th class="info middle-align">이름</th>
                                        <td colspan="3">
                                            <input class="form-control" type="text" id="member_name" name="member_name" value="<?=$member_name?>" data-valid="kr|en" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">이메일</th>
                                        <td colspan="3">
                                            <input class="form-control" type="text" id="email" name="email" value="<?=$email?>" data-valid="email" />
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                            

                        </div> 
                    
                    </div>

                </div>
            </div>
            <!-- // 신규 담당자 정보 입력  -->
            <?php
                } else {
            ?>
            <!-- 담당자 확인 및 변경  -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                    
                        <div class="table-responsive m-b-0">
                            <h5 class="header-title m-b-10">
                                <b>담당자 정보 </b>
                                <button type="button" class="pull-right btn btn-sm btn-purple waves-effect w-md m-l-5" style="top:-4px;" onclick="changePartner()" >담당자 변경</button> 
                            </h5>
                            <hr class="m-t-0">
                            <input type="hidden" name="current_partner_idx" value="<?=$company_member_idx?>" />
                            <input type="hidden" name="edit_partner_idx" id="edit_partner_idx" value="" />

                            <table class="table table-bordered text-left">
                                <tbody>
                                    
                                    <tr>
                                        <th class="info middle-align">휴대폰 번호(ID)</th>
                                        <td id="current_partner_ph" >
                                            <?=$phone_no?>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th class="info middle-align">이름</th>
                                        <td id="current_partner_name" >
                                            <?=$member_name?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">이메일</th>
                                        <td id="current_partner_email" >
                                            <?=$email?>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                            

                        </div> 
                    
                    </div>

                </div>
            </div>
            <!-- // 담당자 확인 및 변경  -->
            <?php

                }
            ?>

            <!-- 제조식품 유형선택  -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        
                        
                        <div class="table-responsive m-b-0">
                            <h5 class="header-title m-b-10">
                                <b>제조식품 유형</b>                    
                            </h5>
                            <hr class="m-t-0">
                            
                            <div>
                                <?php
                                    if( count( $foodtype_large ) > 0 ) {
                                ?>
                                <select class="form-control" name="foodtype_large" id="foodtype_large" style="width:250px;float:left;margin-right:20px" >
                                    <?php
                                        foreach( $foodtype_large AS $item ) {
                                    ?>
                                    <option value="<?=$item['food_code']?>" ><?=$item['title']?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                                <?php
                                    }
                                ?>

                                <select class="form-control" name="foodtype_middle" id="foodtype_middle" style="width:250px;float:left;margin-right:20px;display:none" >
                                   
                                </select>

                                <select class="form-control" name="foodtype_code" id="foodtype_code" style="width:250px;margin-right:20px;float:left;display:none" >
                                   
                                </select>
                                
                                <button type="button" class="pull-left btn btn-success waves-effect w-md m-l-5" id="btn_food_type_add" style="display:none" onclick="addFoodType();" >추가</button>

                            </div>
                            <div id="added_food_code_area" style="clear:both;padding-top:30px" >
                                <?php
                                    
                                    foreach( $added_food_types AS $item ) {
                                ?>
                                <div class="alert alert-info alert-dismissible" role="alert" id="food_type_div_<?=$item['food_code']?>" >
                                    <input type="hidden" name="food_code[]" id="food_type_input_<?=$item['food_code']?>" value="<?=$item['food_code']?>" >
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
                                    <?=$item['large_title']?> > <?=$item['middle_title']?> > <strong><?=$item['title']?></strong>
                                </div>
                                <?php
                                    }                                  
                                ?>
                            </div>
                        </div> 
                        
                    </div>

                </div>
            </div>
            <!-- // 제조식품 유형선택  -->

            </form>

            <div class="row"> 
                <div class="col-lg-12">
                    <button type="button" class="pull-right btn btn-inverse waves-effect w-md m-l-5" onclick="location.href='./<?=$page_name?>_list?page=<?=$page?><?=$params?>'">목록</button> 
                    <button type="button" class="pull-right btn btn-primary waves-effect w-md m-l-5" onclick="register()">저장</button>
               </div>
            </div>


        </div> <!-- // container -->
    </div> <!-- // content -->
</div> <!-- // content-page -->

<!-- 파트너 회원 변경 레이어  -->
<div id="company_member_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" style="width: 500px; border: 1px solid;">
        <div class="modal-content p-0 b-0">                                    
            <div class="panel panel-color panel-inverse m-0">
                <div class="panel-heading">
                    <button type="button" class="close m-t-5" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="panel-title">기업 회원</h3>
                </div>
                <div class="panel-body">
                    <div class="row img-contents">
                        <div class="col-lg-12 table-responsive m-b-0">
                            
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="active">
                                        <th class="info" style="width: 40%;">이름</th>
                                        <th class="info" style="width: 40%;">전화번호</th>
                                        <th class="info" style="width: 20%;">선택</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if( count( $company_members ) > 0  ){
                                            foreach( $company_members AS $item ) {
                                    ?>
                                    <tr style="cursor: pointer;" >
                                        <td class="text-center"><?=$item['member_name']?></td>
                                        <td class="text-center vertical-align"><?=$item['phone_no']?></td>
                                        <td class="text-center vertical-align">
                                            <button type="button" class="pull-right btn btn-sm btn-purple waves-effect w-md m-l-5" style="top:-4px;" onclick="choicePartner('<?=$item['company_member_idx']?>', '<?=$item['member_name']?>', '<?=$item['phone_no']?>', '<?=$item['email']?>')" >선택</button> 
                                        </td>
                                    </tr>
                                    <?php
                                            }
                                        } else {
                                    ?>
                                    <tr style="cursor: pointer;" >
                                        <td colspan="3" class="text-center">
                                            회원 정보가 없습니다.
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>                                    
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- // 파트너 회원 변경 레이어  -->

<script>

    $(function(){

        getFoodTypeMiddleCate();
        
        $('#foodtype_large').change(function(){
            getFoodTypeMiddleCate();
            $('#btn_food_type_add').hide();
            $('#foodtype_code').hide();
            $('#foodtype_code').html('');
        });

        $('#foodtype_middle').change(function(){            
            if( $(this).val() == '' ) {
                $('#foodtype_code').hide();
                $('#foodtype_code').html('');
                $('#btn_food_type_add').hide();
            } else {
                getFoodType();
            }
        });

    });

    /**
     * 파트너 레이어 open
     */
    function changePartner(){
        $('#company_member_modal').modal();
    }

    /**
     * 파트너 데이터 변경
     */
    function choicePartner( arg_member_idx, arg_member_name, arg_member_hp, arg_member_email ){


        $('#edit_partner_idx').val( arg_member_idx );        
        $('#current_partner_name').html( arg_member_name );
        $('#current_partner_ph').html( arg_member_hp );
        $('#current_partner_email').html( arg_member_email );

        $("#company_member_modal").modal('hide');

        alert('파트너 변경 준비가 완료되었습니다.\n\n하단 저장 버튼을 눌러 작업을 완료하세요.');

    }
    
    /**
     * 식품유형 추가처리
     */
    function addFoodType(){

        // 현재 선택된 식품유형 대분류 정보를 가져온다
        var current_large_code = $('#foodtype_large').val(); 
        var current_large_title = jQuery("#foodtype_large option:selected").text();

        // 현재 선택된 식품유형 중분류 정보를 가져온다
        var current_middle_code = $('#foodtype_middle').val(); 
        var current_middle_title = jQuery("#foodtype_middle option:selected").text();

        // 현재 선택된 식품유형 정보를 가져온다        
        var current_food_code = $('#foodtype_code').val(); 
        var current_food_title = jQuery("#foodtype_code option:selected").text();

        var html = '';

        if( current_food_code == '' ) {
            alert('식품 유형을 선택해주세요.');
        } else {
            
            if( $('#food_type_div_' + current_food_code ).length == 0 ){

                html = '<div class="alert alert-info alert-dismissible" role="alert" id="food_type_div_'+ current_food_code +'"" >';
                html += '   <input type="hidden" name="food_code[]" id="food_type_input_'+ current_food_code +'" value="'+ current_food_code +'" >';
                html += '   <button type="button" class="close" data-dismiss="alert" aria-label="Close" ><span aria-hidden="true">&times;</span></button>';
                html += '   '+current_large_title+' > '+current_middle_title+' > <strong>'+current_food_title+'</strong>';
                html += '</div>  ';

                $('#added_food_code_area').append( html );

            }

        }
        
    }
 
    /**
     * 식품유형 중분류 데이터 요청
     */
    function getFoodTypeMiddleCate(){

        var large_cate_code = '';

        // 대분류 select 값을 가져온다.
        if( $('#foodtype_large').length > 0 ) {
            large_cate_code = $('#foodtype_large').val();            
        }

        if( large_cate_code !== '' ) {
            // 데이터 요청
            jqueryAjaxCall({
                type : "post",
                url : '/food/food_type_proc',
                dataType : "json",
                paramData :{
                    mode : 'get_middle'
                    , group_code : large_cate_code
                    , callBackData : 'middle'
                } ,
                callBack : makeFoodTypeOptionHtml 
            });
            
        }
        
    }

    /**
     * 식품유형 데이터 요청
     */
    function getFoodType(){

        var middle_cate_code = '';

        // 대분류 select 값을 가져온다.
        if( $('#foodtype_middle').length > 0 ) {
            middle_cate_code = $('#foodtype_middle').val();            
        }

        if( middle_cate_code !== '' ) {
            // 데이터 요청
            jqueryAjaxCall({
                type : "post",
                url : '/food/food_type_proc',
                dataType : "json",
                paramData :{
                    mode : 'get_types'
                    , group_code : $('#foodtype_large').val()
                    , parent_code : middle_cate_code
                    , callBackData : 'type'
                } ,
                callBack : makeFoodTypeOptionHtml 
            });
            
        }

        }

    /**
     * 옵션 태그 생성
     */
    function makeFoodTypeOptionHtml( arg_data, arg_type ){
        
        var work_id = (arg_type == 'middle' ) ? '#foodtype_middle' : '#foodtype_code';
        var html = '<option value="" >선택</option>';

        if( arg_data.length > 0 ){

            arg_data.forEach( function( item ){
                html += '<option value="'+ item.food_code +'" >'+ item.title +'</option>';
            });

            $( work_id ).html( html );
            $( work_id ).show();

            if( work_id == '#foodtype_code' ) {
                $('#btn_food_type_add').show();
            }

        }
        
    }

    /**
     * 저장 버튼 동작
     */
    function register(){

        viewFormValid.alert_type = 'add';        
        if( viewFormValid.run( 'form_write' ) === true ) {
            // submit
            $('#form_write').submit();
        }

    }

    /**
     * 주소검색
     */
    function findAddress() {
        new daum.Postcode({
            oncomplete: function(data) {
                console.log( data );
                if( data.postcode1 == '' ) {
                    $("#zip_code").val(data.zonecode);
                } else {
                    $("#zip_code").val(data.postcode1+"-"+data.postcode2);
                }

                
                $("#addr").val(data.address);
                $("#addr_detail").focus();
            }
        }).open();
    }

</script>

<style>    
    .table>tbody>tr>th.info {
        width: 15%;
    }
    table input {
        width: 40% !important; display: inline-block !important;
    }
</style>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script type="text/javascript" src="<?=$aqua_view_path;?>/public/js/view.form.valid.js"></script>
            
