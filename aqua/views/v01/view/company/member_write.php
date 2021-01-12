<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    기업회원 관리 > 회원 정보 <?=$page_work?>
                    <button type="button" class="pull-right btn btn-inverse waves-effect w-md m-l-5" onclick="location.href='./<?=$page_name?>_list?page=<?=$page?><?=$params?>'">목록</button> 
                </h1>                
            </section>

            <form class="form-horizontal" role="form" method="post" id="form_write" enctype="multipart/form-data"  action="./<?=$page_name?>_proc">                
                <input type="hidden" name="mode" value="<?=$mode?>" />
                <input type="hidden" name="company_member_idx" value="<?=$company_member_idx?>" />
                <input type="hidden" name="company_idx" id="company_idx" value="<?=$company_idx?>" />
                <input type="hidden" name="page" value="<?=$page?>" />
                <input type="hidden" name="top_code" value="<?=$top_code?>" />
                <input type="hidden" name="left_code" value="<?=$left_code?>" />
                <input type="hidden" name="ref_params" value="<?=$params?>" />

           
            <!-- 기업정보 -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        
                        <div class="table-responsive m-b-0">
                            <h5 class="header-title m-b-10">
                                <b>기업정보</b>    
                                <button type="button" class="pull-right btn btn-sm btn-purple waves-effect w-md m-l-5" style="top:-4px;" onclick="changeCompany()">기업선택</button>                
                            </h5>
                            <hr class="m-t-0">
                            <table class="table table-bordered text-left">
                                <tbody>                                    
                                    <tr>
                                        <th class="info middle-align">회사명</th>
                                        <td colspan="3" id="company_name" >
                                            <?=$company_name?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="info middle-align">사업자등록번호</th>
                                        <td colspan="3" id="registration_no" >
                                            <?=$registration_no?>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th class="info middle-align">대표명</th>
                                        <td colspan="3" id="ceo_name" ><?=$ceo_name?></td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">전화번호</th>
                                        <td colspan="3" id="company_tel" ><?=$company_tel?></td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">담당자명</th>
                                        <td colspan="3" id="partner_name" ><?=$partner_name?></td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">담당자 휴대폰 번호</th>
                                        <td colspan="3" id="partner_phone_no" ><?=$partner_phone_no?></td>
                                    </tr>

                          
                                </tbody>
                            </table>
                        </div> 

                    </div>

                </div>
            </div>
            <!-- //기업정보 -->

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
                                        <th class="info middle-align">휴대폰 번호(ID)</th>
                                        <td colspan="3">
                                            <input class="form-control" type="text" id="phone_no" name="phone_no" placeholder="'-'없이 입력하세요" maxlength="11" value="<?=$phone_no?>" data-valid="num/11" />  
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th class="info middle-align">비밀번호</th>
                                        <td colspan="3">
                                            <input class="form-control" type="password" id="password" name="password" <?=( $mode == 'ins' ) ? 'data-valid="pw"' : '' ?> />
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
									
									<tr>
                                        <th class="info middle-align">메뉴접근 권한</th>
                                        <td colspan="3"  >
                                            <?php

                                                foreach( $service_items AS $key=>$val ) {

                                                    if( $key == 0 ) {
                                                        $valid_val = 'data-valid="check"';
                                                    } else {
                                                        $valid_val = '';
                                                    }
                                            ?>
                                            <input type="checkbox" name="menu_auth[]" id="menu_auth_<?=$val['item_code']?>" value="<?=$val['item_code']?>" <?=$valid_val?> <?=(strpos( $menu_auth, $val['item_code'] ) > -1 ) ? 'checked="checked"' : '' ?>  > 
                                            <label class="m-r-10" for="menu_auth_<?=$val['item_code']?>"><?=$val['title']?></label>
                                            <?php
                                                }
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">업무 권한</th>
                                        <td colspan="3">

                                            <input class="m-l-15" type="checkbox" name="approval_auth[]" id="approval_auth_no" value="no" <?=(strpos( $approval_auth, 'no' ) > -1 ) ? 'checked="checked"' : '' ?> >
                                            <label class="m-r-15" for="approval_auth_no">업무담당자</label>

                                            <input class="m-l-15" type="checkbox" name="approval_auth[]" id="approval_auth_leader" value="leader" <?=(strpos( $approval_auth, 'leader' ) > -1 ) ? 'checked="checked"' : '' ?> >
                                            <label class="m-r-15" for="approval_auth_leader">중간승인자(생산팀장)</label>

                                            <input class="m-l-15" type="checkbox" name="approval_auth[]" id="approval_auth_ceo" value="ceo" <?=(strpos( $approval_auth, 'ceo' ) > -1 ) ? 'checked="checked"' : '' ?> >
                                            <label class="m-r-15" for="approval_auth_ceo">최종승인자(대표이사)</label>

                                        </td>
                                    </tr>

                                   <tr>
                                        <th class="info middle-align">담당업무</th>
                                        <td colspan="3">

                                            <?php
                                                $valid_val = 'data-valid="check"';
                                                foreach( $mes_process AS $key=>$val ) {
                                                    
                                            ?>
                                            <input type="checkbox" name="work_auth[]" id="work_auth_<?=$key?>" value="<?=$key?>" <?=$valid_val?> <?=(strpos( $work_auth, $key ) > -1 ) ? 'checked="checked"' : '' ?>  > 
                                            <label class="m-r-10" for="work_auth_<?=$key?>"><?=$val?></label>
                                            <?php
                                                    $valid_val = '';
                                                }
                                            ?>

                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="info middle-align">계정상태</th>
                                        <td colspan="3">

                                            <select class="form-control" name="use_flag" id="use_flag" style="width:200px" >
                                               
                                                <option value="Y" <?=($use_flag == 'Y' ? 'selected="selected"' : '' )?> >사용</option>
                                                <option value="N" <?=($use_flag == 'N' ? 'selected="selected"' : '' )?> >미사용</option>
                                                
                                            </select>

                                        </td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div> 

                    </div>

                </div>
            </div>
            <!-- //기본정보 -->
            
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

<!-- 기업선택 레이어  -->
<div id="company_list_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" style="width: 500px; border: 1px solid;">
        <div class="modal-content p-0 b-0">                                    
            <div class="panel panel-color panel-inverse m-0">
                <div class="panel-heading">
                    <button type="button" class="close m-t-5" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="panel-title">기업 현황</h3>
                </div>
                <div class="panel-body">
                    <div class="row img-contents">
                        <div class="col-lg-12 table-responsive m-b-0">
                            
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="active">
                                        <th class="info" style="width: 40%;">회사명</th>
                                        <th class="info" style="width: 40%;">대표명</th>
                                        <th class="info" style="width: 20%;">사업자등록번호</th>
                                        <th class="info" style="width: 20%;">선택</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if( count( $company_list ) > 0  ){
                                            foreach( $company_list AS $item ) {                                                
                                    ?>
                                    <tr style="cursor: pointer;" >
                                        <td class="text-center"><?=$item['company_name']?></td>
                                        <td class="text-center vertical-align"><?=$item['ceo_name']?></td>
                                        <td class="text-center vertical-align"><?=$item['registration_no']?></td>
                                        <td class="text-center vertical-align">
                                            <button type="button" class="pull-right btn btn-sm btn-purple waves-effect w-md m-l-5" style="top:-4px;" onclick="choiceCompany(this)" data-company="<?=preg_replace( '/\"/', "'" ,json_encode( $item, JSON_UNESCAPED_UNICODE ))?>">선택</button> 
                                        </td>
                                    </tr>
                                    <?php
                                            }
                                        } else {
                                    ?>
                                    <tr style="cursor: pointer;" >
                                        <td colspan="4" class="text-center">
                                            기업 정보가 없습니다.
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
<!-- // 기업선택 레이어  -->

<script>

     /**
     * 저장 버튼 동작
     */
    function register(){

        if( $('#company_idx').val() == '' ) {
            alert('기업을 선택 해주세요.');
            return;
        }

        viewFormValid.alert_type = 'add';        
        if( viewFormValid.run( 'form_write' ) === true ) {
            // submit
           
            $('#form_write').submit();
        }

    }

    function changeCompany(){
        $('#company_list_modal').modal();
    }

    function choiceCompany( arg_this ) {       

        var data = JSON.parse( $(arg_this).data('company').replace(/'/g, '"') );

        $('#company_idx').val( data.company_idx );
        $('#company_name').html( data.company_name );
        $('#ceo_name').html( data.ceo_name );
        $('#registration_no').html( data.registration_no );
        $('#company_tel').html( data.company_tel );
        $('#partner_name').html( data.partner_name );
        $('#partner_phone_no').html( data.partner_phone_no );

        $('#company_list_modal').modal('hide');

        getContractServiceItems( data.company_idx );

    }

    function getContractServiceItems( arg_company_idx ){
        
        if( arg_company_idx !== '' ) {
            // 데이터 요청
            jqueryAjaxCall({
                type : "post",
                url : '/company/get_company_contract',
                dataType : "json",
                paramData :{                    
                    company_idx : arg_company_idx
                } ,
                callBack : makeServiceItemsCheckbox
            });
            
        } 

    }

    function makeServiceItemsCheckbox( arg_data ) {

        if( arg_data.length > 0 ) {
            
            var html = '';
            var valid_val = 'data-valid="check"';
            var checked_str = '';
            
            $.each( arg_data, function(idx, list_val ){
                
                if( idx > 0 ) {
                    valid_val = '';
                } 

                
                html += '<input type="checkbox" name="work_auth[]" id="work_auth_'+list_val['item_code']+'" value="'+ list_val['item_code'] +'" '+ valid_val +' '+ checked_str +' > <label class="m-r-10" for="work_auth_'+list_val['item_code']+'">'+list_val['title']+'</label>'; 
                

            });

            $('#work_auth_area').html( html );


        }

    }


</script>

<style>    
    .table>tbody>tr>th.info {
        width: 15%;
    }
    /* table input {
        width: 40% !important; display: inline-block !important;
    } */
</style>
<script type="text/javascript" src="<?=$aqua_view_path;?>/public/js/view.form.valid.js"></script>
            
