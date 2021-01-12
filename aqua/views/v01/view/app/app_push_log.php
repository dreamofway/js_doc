<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    Push Log
                    <button type="button" class="pull-right btn btn-primary waves-effect w-md" onclick="sendPush()">테스트 발송</button>                 
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
                                            <input type="text" class="form-control" name="sch_keyword" value="<?=$sch_keyword;?>" placeholder="제목, 내용">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-sm-offset-5 col-sm-7 m-t-15">
                                            <button type="button" class="btn btn-primary waves-effect waves-light" onclick="location.href='./<?=$page_name?>_list?top_code=<?=$top_code?>&left_code=<?=$left_code?>'">기본설정</button>
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
                            <!-- <button type="button" class="btn btn-sm btn-primary waves-effect waves-light m-l-5 fright" onclick="tableToExcel('excelTable', '기업회원');">엑셀다운로드</button> -->
                        </h5>
                        <hr class="m-t-0">
                        <div class="table-responsive m-b-0">
                            <table class="table table-bordered text-center" id="excelTable" style="width:1900px">
                                <thead>
                                    <tr class="active">                                        
                                        <th class="info" style="width: 10%;" >발송일시</th>
                                        <th class="info"  style="width: 20%;">제목</th> 
                                        <th class="info" style="width: 30%;" >내용</th>                                        
                                        <th class="info" style="width: 10%;" >앱</th>
                                        <th class="info" style="width: 30%;" >결과</th>                                                                                                                                                 
                                    </tr>                                
                                </thead>
                                <tbody>
                                    <?php 
                                    if( $paging->total_rs > 0 ){ 
                                                
                                        foreach($list AS $key=>$value) {
                                    ?>
                                    <tr>
                                        <td><?=$value['send_date'];?></td>                                        
                                        <td>
                                            <?=$value['title'];?>
                                        </td>                                        
                                        <td><?=$value['contents'];?></td>
                                        <td><?=$value['app_name'];?></td>                                       
                                        <td><?=$value['fcm_return_data'];?></td>                                       
                                    </tr>
                                    <?php   
                                        }
                                    } else {
                                    ?>                                
                                        <tr><td colspan="5">데이터가 없습니다</td></tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>                                       
                            </table>
                        </div>

						<form name="list_form" id="list_form" method="post" action="test_push_send" >
							<input type="hidden" name="page" value="<?=$page?>">
							<input type="hidden" name="list_rows" value="<?=$list_rows?>">                                                        
							<input type="hidden" name="top_code" value="<?=$top_code?>">
							<input type="hidden" name="left_code" value="<?=$left_code?>">
							<input type="hidden" name="ref_params" value="<?=$params?>" />
							<input type="hidden" name="page_name" value="<?=$page_name?>" />
						</form>

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
<script>
	function sendPush() {
		$('#list_form').submit();
	}
</script>
            
