<!-- Start content-page -->
<div class="content-page" >
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">                    
                <h1>
                    HACCP 문서 정보 
                    <button type="button" class="pull-right btn btn-primary waves-effect w-md" onclick="location.href='./<?=$page_name?>_write?page=<?=$page?><?=$params?>'">+등록</button>                 
                </h1>                   
            </section>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <h5 class="header-title m-t-0">
                            <b>목록</b>
                            <!-- <button type="button" class="btn btn-sm btn-primary waves-effect waves-light m-l-5 fright" onclick="tableToExcel('excelTable', '기업회원');">엑셀다운로드</button> -->
                        </h5>
                        <hr class="m-t-0">
                        <div class="table-responsive m-b-0">
                            <table class="table table-bordered text-center" id="excelTable">
                                <thead>
                                    <tr class="active">
                                        <th class="info" style="width: 20%;">CODE</th>
                                        <th class="info" >문서명</th>                                                                
                                    </tr>                                
                                </thead>
                                <tbody>
									<?php
										foreach( $items AS $key=>$val ) {
									?>
                                    <tr>
                                        <td class="info"><?=$val['item_code']?></td>
                                        <td><a href="<?=$page_name?>_write?item_code=<?=$val['item_code']?>&page=<?=$page?><?=$params?>" ><?=$val['title']?></a></td>
                                    </tr>
									<?php
										}
									?>
                                    
                                </tbody>                                       
                            </table>
                        </div>
                        

                    </div>                       
                </div>
            </div><!-- end row --> 

        </div> <!-- // container -->
    </div> <!-- // content -->
</div> <!-- // content-page -->

            
