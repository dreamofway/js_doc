<!-- Begin page -->
<div id="wrapper">
	
			
	<div class="topbar">
		
		<!-- LOGO -->
		<div class="topbar-left">            
			<a href="#">
				<span class="admin_title">
					<?=( ( $this->getIP() == '::1' ) || ( $_SERVER['SERVER_NAME'] == '27.96.135.62' ) ) ? '[DEV]' : '' ?>					
					<?=$meta_title?>
				</span>
			</a>
		</div>

		<!-- Button mobile view to collapse sidebar menu -->
		<div class="navbar navbar-default" role="navigation">
			<div class="container">
				<!-- page title -->
				<ul class="nav navbar-nav m_menu">
				<li>
					<button class="button-menu-mobile open-left"><i class="zmdi zmdi-menu"></i></button>
				</li>
				</ul>
				<?php
                    
                    if( gettype( $menu_info ) === 'array' ) {
                        $top_menu_arr = $menu_info['top_menu'];
                        $left_menu_arr = $menu_info['left_menu'];
                ?>
				<ul class="nav navbar-nav navbar-top nav_main">
                    <?php
                        for($top_loop_cnt = 0; $top_loop_cnt < count($top_menu_arr); $top_loop_cnt++) {
                    ?>
                    <li <?=($top_code == $top_menu_arr[$top_loop_cnt]['code']) ? 'class="m_active"' : '' ?> >
                        <a href="<?=$top_menu_arr[$top_loop_cnt]['link']?>"><?=$top_menu_arr[$top_loop_cnt]['title']?></a>
                        <ul class="depth_menu">
                            <?php
                                for($sub_loop_cnt = 0; $sub_loop_cnt < count($left_menu_arr[$top_menu_arr[$top_loop_cnt]['code']]); $sub_loop_cnt++) {
                            ?>
                            <li>
                                <a href="<?=$left_menu_arr[$top_menu_arr[$top_loop_cnt]['code']][$sub_loop_cnt]['link']?>" class="waves-effect active">
                                    <span><?=$left_menu_arr[$top_menu_arr[$top_loop_cnt]['code']][$sub_loop_cnt]['title']?></span>
                                </a>
                            </li>
                            <?php
                                }
                            ?>
                        </ul>
                    </li>
                    <?php
                        }
                    ?>
                </ul>
                <?php
                    }
                ?>

				<!-- page title -->
		</div>
		</div><!-- end navbar -->
		<div class="profile">
		
			<img class="profile_img" src="<?=$aqua_view_path;?>/public/images/iconfinder_profle.png">
			
			<ul class="nav navbar-nav nav-profile">
			<li>
				<a class="waves-effect waves-light m-1-10">
				<span class="color2">Hi</span>
				<span class="nick_name"><?=getAccountInfo()['name']?>님</span>
				<span class="color1"></span>
				</a>
			</li>
			<li>
				<a href="/auth/logout" class="waves-effect waves-light m-1-10">
				<span class="color2">로그아웃</span>
				</a>
			</li>
			</ul>
		</div>
	</div>