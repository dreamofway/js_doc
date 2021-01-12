<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    국가코드 관리 
                    <button type="button" class="pull-right btn btn-primary waves-effect w-md" onclick="showAdd()">+추가</button>                 
                </h1>                   
            </section>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box p-b-0">
                        <div class="row">
                            <form class="form-horizontal group-border-dashed clearfix" name="fsearch" id="fsearch" method="get" action="">
                                <input type="hidden" name="page" value="<?=$page?>">
                                <input type="hidden" name="list_rows" value="<?=$list_rows?>">                                                        
                                <input type="hidden" name="top_code" value="<?=$top_code?>">
                                <input type="hidden" name="left_code" value="<?=$left_code?>">
                                
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">검색</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="sch_keyword" value="<?=$sch_keyword;?>" placeholder="코드, 국가명">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-sm-offset-5 col-sm-7 m-t-15">
                                            <button type="button" class="btn btn-primary waves-effect waves-light" onclick="location.href='<?=$page_init?>'">기본설정</button>
                                            <!-- <button type="reset" class="btn btn-primary waves-effect waves-light">기본설정</button> -->
                                            <button type="submit" class="btn btn-inverse waves-effect m-l-5">검색</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <h5 class="header-title m-t-0">
                            <b>목록</b>                        
                        </h5>
                        <hr class="m-t-0">
                        <div class="table-responsive m-b-0">
                            <table class="table table-bordered text-center" >
                                <thead>
                                    <tr class="active">
                                        <th class="info" style="width: 10%;">CODE</th>
                                        <th class="info" style="width: 60%;" >국가명</th>
                                        <th class="info" style="width: 10%;" >사용여부</th>
                                        <th class="info" style="width: 20%;" >관리</th>
                                    </tr>                                
                                </thead>
                                <tbody>
                                <?php                                 
                                if( $paging->total_rs > 0 ){ 
                                        
                                    foreach ($list as $key=>$value):
                                ?>
                                    <form id="list_<?=$value['country_code'];?>" method="post" action="./country_code_proc" >
                                        <input type="hidden" name="mode" id="mode_<?=$value['country_code'];?>" value="edit" >
                                        <input type="hidden" name="page" value="<?=$page?>">
                                        <input type="hidden" name="list_rows" value="<?=$list_rows?>">                                                        
                                        <input type="hidden" name="top_code" value="<?=$top_code?>">
                                        <input type="hidden" name="left_code" value="<?=$left_code?>">
                                        <input type="hidden" name="params" value="<?=$params?>">
                                        <input type="hidden" name="edit_code" value="<?=$value['country_code'];?>">
                                        
                                    <tr>
                                        <td><input type="text" class="form-control" name="country_code" id="country_code_<?=$value['country_code'];?>" value="<?=$value['country_code'];?>" ></td>
                                        <td><input type="text" class="form-control" name="country_name" id="country_name_<?=$value['country_code'];?>" value="<?=$value['country_name'];?>" ></td>
                                        <td>
                                            <select class="form-control" name="use_flag" >
                                                <option value="Y" <?=( $value['use_flag'] == "Y") ? 'selected' : '' ?> >사용</option>
                                                <option value="N" <?=( $value['use_flag'] == "N") ? 'selected' : '' ?> >미사용</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary" onclick="editCode('list_<?=$value['country_code'];?>')">수정</button>
                                            <button type="button" class="btn btn-danger" onclick="delCode('list_<?=$value['country_code'];?>')" >삭제</button>
                                        </td>
                                    </tr>
                                    </form>
                                <?php   
                                    endforeach; 
                                } else {
                                ?>                                
                                    <tr><td colspan="4">데이터가 없습니다</td></tr>
                                <?php
                                }
                                ?>
                                </tbody>                                       
                            </table>
                        </div>
                        <div class="text-center">
                            <div class="pagination">                            
                                <?=$paging->draw(); ?>
                            </div>
                        </div>

                        <?php include_once( $this->getViewPhysicalPath( '/view/inc/select_list_rows.php' )  ); ?>

                    </div>                       
                </div>
            </div><!-- end row --> 

        </div> <!-- // container -->
    </div> <!-- // content -->
</div> <!-- // content-page -->


<div id="add_layer" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" >
        <div class="modal-content p-0 b-0">                                    
            <div class="panel panel-color panel-inverse m-0">
                <div class="panel-heading">
                    <button type="button" class="close m-t-5" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="panel-title">국가 추가</h3>
                </div>
                <div class="panel-body">
                    <div class="row img-contents">
                        <div class="col-lg-12 table-responsive m-b-0">
                            <form name="add_form" id="add_form" method="post" action="./country_code_proc" >
                                <input type="hidden" name="mode" value="ins" >
                                <input type="hidden" name="page" value="<?=$page?>">
                                <input type="hidden" name="list_rows" value="<?=$list_rows?>">                                                        
                                <input type="hidden" name="top_code" value="<?=$top_code?>">
                                <input type="hidden" name="left_code" value="<?=$left_code?>">
                                <input type="hidden" name="params" value="<?=$params?>">

                            <table class="table table-bordered text-left">
                                <tbody>
                                
                                    <tr>
                                        <td class="info text-center wper20">CODE</td>
                                        <td class="wper80">
                                            <input class="form-control" type="text" name="country_code" id="country_code" >
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="info text-center wper20">국가명</td>
                                        <td class="wper80">
                                            <input class="form-control" type="text" name="country_name" id="country_name" >
                                        </td>
                                    </tr>
                                
                                </tbody>
                            </table>
                            </form>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="text-center">							
							<button type="button" class="btn btn-primary waves-effect waves-light m-t-10"  onclick="add();">
								저장
							</button>
							<button type="button" class="btn btn-inverse waves-effect waves-light m-t-10 m-l-15" onclick="closeAdd();">
								취소
							</button>
                        </div>
                    </div>
                </div>
            </div>                                    
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div> <!-- /.add_layer --> 

<script>

function showAdd(){
    $("#add_layer").modal();
}

function add(){
    
    if( validCheck( '' ) == false ) {
        return;
    }

    $('#add_form').submit();


}

function closeAdd(){
    $('#country_code').val('');
    $('#country_name').val('');
    $("#add_layer").modal('hide');
}

function editCode( arg_from ) {

    if( confirm('수정하시겠습니까?') === true ) {
        
        if( validCheck( '_' + arg_from.split('_')[1] ) == false ) {
            return;
        }

        $('#' + arg_from ).submit();
    }

}

function delCode( arg_from ){

    if( confirm('삭제하시겠습니까?') === true ) {
        
        
        $('#mode_' + arg_from.split('_')[1] ).val('del');

        $('#' + arg_from ).submit();
        
    }


}

function validCheck( arg_add_str ){

    if( $('#country_code' + arg_add_str ).val() == '' ) {
        alert('CODE를 입력해주세요');
        $('#country_code').focus();
        return false;
    }

    if( $('#country_name' + arg_add_str ).val() == '' ) {
        alert('국가명을 입력해주세요');
        $('#country_name').focus();
        return false;
    }

}

</script>
