<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    사원 관리 > 사원 <?=$page_work?>
                    <button type="button" class="pull-right btn btn-inverse waves-effect w-md m-l-5" onclick="location.href='./<?=$page_name?>_list?page=<?=$page?><?=$params?>'">목록</button> 
                </h1>                
            </section>

            <form class="form-horizontal" role="form" method="post" id="form_write" enctype="multipart/form-data"  action="./<?=$page_name?>_proc">                
                <input type="hidden" name="mode" value="<?=$mode?>" />
                <input type="hidden" name="member_idx" value="<?=$member_idx?>" />                
                <input type="hidden" name="page" value="<?=$page?>" />
                <input type="hidden" name="top_code" value="<?=$top_code?>" />
                <input type="hidden" name="left_code" value="<?=$left_code?>" />
                <input type="hidden" name="ref_params" value="<?=$params?>" />


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
									<?php
										if( $mode == 'ins' ) {
									?>
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
									<?php
									}
									?>
                                    <tr>
                                        <th class="info middle-align">입사일</th>
                                        <td colspan="3" >                                            
                                            <div class="form-group">                                            
                                                <div class="col-sm-4">
                                                    <div class="input-daterange input-group" >
                                                        <input type="text" class="form-control datepicker " name="join_date" id="join_date" value="<?=$join_date?>" data-valid="blank" readonly="readonly" >  
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">휴대폰 번호</th>
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
                                            <input class="form-control" type="text" id="name" name="name" value="<?=$name?>" data-valid="kr|en" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="info middle-align">이메일</th>
                                        <td colspan="3">
                                            <input class="form-control" type="text" id="email" name="email" value="<?=$email?>" data-valid="email" />
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


<script>

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
            
