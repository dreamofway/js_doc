<!-- Start content-page -->
<div class="content-page" style="margin-left:0px">
    <!-- Start content -->
    <div class="content">
        <div class="container">

            <section class="content-header">
                <h1>
                    대시보드                
                </h1>                   
            </section>

            <!-- 검색 -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box p-b-0">
                        <div class="row">
                            <form class="form-horizontal group-border-dashed clearfix" name="fsearch" id="fsearch" method="get" action="">
                                
                                <div class="col-sm-4 m-l-40">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">등록일</label>
                                        <div class="col-sm-9">
                                            <div class="input-daterange input-group" >
                                                <input type="text" class="form-control datepicker " name="st_date" value="<?=$st_date;?>">
                                                <span class="input-group-addon bg-primary b-0 text-white">~</span>
                                                <input type="text" class="form-control datepicker" name="ed_date" value="<?=$ed_date;?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">검색</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="keyword" value="<?=$keyword?>" placeholder="회사명, 관리자명">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-sm-offset-5 col-sm-7 m-t-15">
                                            <button type="reset" class="btn btn-primary waves-effect waves-light" onclick="location.href='?top_code=<?=$top_code?>&left_code=<?=$left_code?>'">기본설정</button>
                                            <button type="submit" class="btn btn-inverse waves-effect m-l-5">검색</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- // container -->
    </div> <!-- // content -->
</div> <!-- // content-page -->

            
