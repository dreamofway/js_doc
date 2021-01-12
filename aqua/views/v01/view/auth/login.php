        <style type="text/css">
            .btn-bordred.btn-primary {
                border-bottom: 2px solid #d50c0c !important;
                background-color: #d50c0c !important;
                border: 1px solid #d50c0c !important;
                font-weight: bold;
                padding: 1rem;
            }
        </style>

        <div class="account-pages"></div>
        <div class="clearfix"></div>
        <div class="wrapper-page">
            <div class="login-box card-box">
                <div class="text-center" style="margin-top: 25px;">
                    <h1 class="m-b-20"></h1>
                    <h4 class="font-bold">스마트공장 관리시스템</h4>
                </div>
                <div class="panel-body">                    
                    <form class="form-horizontal" id="loginform" method="post" action="./loginProc">                        
                        <input type="hidden" name="rtn_page" id="rtn_page" value="<?=$rtn_page;?>"> 
                        <div class="form-group ">
                            <div class="col-xs-12">
                                <input class="form-control" type="text" name ="m_id" id="m_id" placeholder="ID" autofocus>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12">
                                <input class="form-control" type="password" name ="m_pw" id="m_pw" placeholder="PASSWORD">
                            </div>
                        </div>

                        <div class="form-group text-center m-t-30">
                            <div class="col-xs-12">
                                <button type="button" class="btn btn-primary btn-bordred btn-block waves-effect waves-light" onclick="login()">로그인</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- end card-box-->
        </div>
        <!-- end wrapper page -->
       

        <script>

            function login() {

                var m_id = $("#m_id").val();
                var m_pw = $("#m_pw").val();

                if(m_id == "") {
                    alert("아이디를 입력해주세요.");
					$("#m_id").focus();
                    return;
                }
                if(m_pw == "") {
                    alert("비밀번호를 입력해주세요.");
					$("#m_pw").focus();
                    return;
                }

                var base_url = $("#base_url").val();

                $("#loginform").submit();  
            }

			$(function(){
				$('#m_id, #m_pw').keyup(function(e){
					if( e.keyCode === 13 ) {
						login();
					}
				});
			});
            

        </script>            
   