<div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu">
                <?php
                if($this->session->userdata('is_superAdmin') || $this->session->userdata('position')=='Cashier'):
                    //echo Modules::run('cueingsystem/getCueWidget'); 
                   
                endif;
               
                    $position_id = $this->session->userdata('position_id');
                     $menu = $menus->menu_access; //this is taken from the template.php
                    if($menu!=""){
                    $item = explode(",", $menu);
                    
                foreach ($item as $m){
                    $menuItem = Modules::run('nav/getMenuAccess', $m);
                    if($menuItem->menu_parent==0){  
                ?>
                 <li>
                        <a href="<?php echo base_url().$menuItem->menu_link ?>" class="<?php echo $menuItem->menu_li_class ?>">
                            <i class="fa <?php echo $menuItem->menu_a_class ?> fa-fw"></i> <?php echo $menuItem->menu_name ?>
                            <?php if($menuItem->menu_li_class=='dropdown'):?>
                                <span class="fa arrow"></span>
                            <?php endif; ?>
                        </a>
                        <?php
                            if ($menuItem->menu_li_class=='dropdown'){          
                                ?>
                                    <ul class="nav nav-second-level">
                                        <?php 
                                            foreach ($item as $m){
                                                $submenu =  Modules::run('nav/getSubMenu', $menuItem->menu_id, $m);
                                                    if($submenu!='0'){
                                                        if($submenu->menu_type=='submenu'){
                                                            if($submenu->menu_name == 'Admission'):
                                                        ?>  
                                                            <li><a class="<?php echo $submenu->menu_li_class ?>" href="#" data-toggle="modal" data-target="#selectNewOption">
                                                                <i class="fa <?php echo $submenu->menu_a_class ?> fa-fw"></i>
                                                                <?php echo $submenu->menu_name ?>
                                                                </a>
                                                            </li> 
                                                        <?php
                                                            else:
                                                                ?>
                                                            <li><a class="<?php echo $submenu->menu_li_class ?>" href="<?php echo base_url(); ?><?php echo $submenu->menu_link; ?>">
                                                                <i class="fa <?php echo $submenu->menu_a_class ?> fa-fw"></i>
                                                                <?php echo $submenu->menu_name ?>
                                                                </a>
                                                            </li> 
                                                                <?php
                                                            endif;
                                                        }
                                                    }
                                            }
                                        ?>
                                    </ul>

                                <?php  
                            }
                            ?>
                           
                 </li>
                <?php
                    
                            }
                        }
                    }
             //   if($this->session->position == 'High School Faculty' || $this->session->position == 'Faculty' || $this->session->position=='DSA'):    
                    
                    $collegeLoad = Modules::run('college/subjectmanagement/getAssignedSubjectRaw', $this->session->employee_id);
                    if(count($collegeLoad) > 0):
                        ?>
                         <li>
                            <a href="<?php echo base_url('college/gradingsystem') ?>" class="">
                                <i class="fa fa-calculator fa-fw"></i> College Grading System
                                
                            </a>
                         </li>
                        <?php
                    endif;
            //    endif;
                    
                  

               ?>
            </ul>
        </div>
        <!-- /.sidebar-collapse -->
    </div>
