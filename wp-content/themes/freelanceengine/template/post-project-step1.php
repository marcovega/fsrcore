<!-- Step 1 -->
<?php
    $pack_id = isset($_GET['pack_id'] ) ? $_GET['pack_id'] : 0;
    $selected = 0;
    global $user_ID, $ae_post_factory;
    $ae_pack = $ae_post_factory->get('pack');
    $packs = $ae_pack->fetch('pack');
    $package_data = AE_Package::get_package_data( $user_ID );

    if( ! is_acti_fre_membership() )
        $package_data = AE_Package::get_package_data( $user_ID );
   // else
    //   $package_data = fre_membership_get_available_pack();

    $orders = AE_Payment::get_current_order($user_ID);

    $number_free_plan_used = AE_Package::get_used_free_plan($user_ID);
?>
<div id="fre-post-project-1 step-plan" class="fre-post-project-step step-wrapper step-plan active">
    <div class="fre-post-project-box">
        <div class="step-post-package">
            <h2><?php _e('Choose your most appropriate package', ET_DOMAIN)?></h2>
            <ul class="fre-post-package">
                <?php
                $cur_plan = array();
                foreach ($packs as $key => $package) {
                    $number_of_post =   $package->et_number_posts;
                    $static_number = $number_of_post;
                    $post_left = 0;
                    $sku = $package->sku;
                    $text = $pack_description = '';
                    $order = false;
                    if($number_of_post >= 1 ) {
                        // get package current order
                        if(isset($orders[$sku])) {
                            $order = get_post($orders[$sku]);
                        }

                        if( isset($package_data[$sku] ) && isset($order->post_status) && $order->post_status != 'draft') {
                            $package_data_sku = $package_data[$sku];
                            if(isset($package_data_sku['qty']) && $package_data_sku['qty'] > 0 ){
                            /**
                             * print text when company has job left in package
                            */
                            $number_of_post =   $package_data_sku['qty'];
                            }
                        }

                        if( ! $package->et_price ) { // if free package.
                            //number_free_plan_used == number posted free
                            $post_left = (int)$number_of_post - (int)$number_free_plan_used;
                            $post_left = max($post_left, 0);


                            if($number_of_post < 0) $number_of_post = 0;
                        }

                        if( (int) $package->et_price > 0 ) {
                            $price = fre_price_format($package->et_price);
                        } else {
                            $price = __("Free", ET_DOMAIN);
                        }
                        $disabled = '';


                        if ( is_acti_fre_membership() ){
                            $cur_plan = is_exists_membership_record();

                            if($package->et_subscription_time > 1){
                                $pack_description = sprintf(__("%s to post  %s projects for %s months. ", ET_DOMAIN) , $price, $static_number, $package->et_subscription_time);
                            }
                            if( $cur_plan && $cur_plan->pack_sku == $sku ){
                                $pack_description.=sprintf(__("You have %s posts left ", ET_DOMAIN) , $cur_plan->remain_posts );
                            }
                        }

                    }
                    $class_select = '';
                    $checked = false;

                    if( $pack_id ){

                       if( $package->ID == $pack_id && $package->et_price > 0 ){
                            $checked = true;
                            $class_select = 'auto-select';
                        }
                    } else {

                        if( isset( $package_data[$sku] ) ) {
                            $package_data_sku = $package_data[$sku];
                            if( $package->et_price > 0 && isset($package_data_sku['qty']) && $package_data_sku['qty'] > 0 ) {
                                if(isset($orders[$sku])){
                                    $order = get_post($orders[$sku]);
                                    if( $order && !is_wp_error( $order ) && $order->post_status != 'draft'){
                                        // auto select package is available to post.
                                        $class_select = ' auto-select '.$order->post_status ;
                                        $checked = true;
                                    }
                                }
                            }
                        }
                    }
                    if ( is_acti_fre_membership() ){
                        $class_select = '';
                        $checked = false;
                        if( $cur_plan && $cur_plan->pack_sku == $sku ){
                            $class_select = 'current-membership-plan auto-select ';
                            $checked = true;
                        }
                    }


                ?>
                <li class="<?php echo $class_select;?> <?php echo $disabled;?>" data-sku="<?php echo trim($package->sku);?>" data-id="<?php echo $package->ID ?>" data-price="<?php echo $package->et_price; ?>" data-package-type="<?php echo $package->post_type; ?>" data-title="<?php echo $package->post_title ;?>">
                        <label class="fre-radio" for="package-<?php echo $package->ID?>">
                            <input id="package-<?php echo $package->ID?>" name="post-package" type="radio" <?php echo $disabled; echo ($checked) ? "checked='checked'" : '' ;?>>
                            <span><?php echo $package->post_title ; ?></span>
                        </label>
                        <span class="disc"><?php echo $pack_description;?> </span>
                    </li>
                <?php } ?>
            </ul>
            <?php echo '<script type="data/json" id="package_plans">'.json_encode($packs).'</script>'; ?>
            <div class="fre-select-package-btn">
                <!-- <a class="fre-btn" href="">Select Package</a> -->
                <input class="fre-btn fre-post-project-next-btn select-plan primary-bg-color" type="button" value="<?php _e('Next Step', ET_DOMAIN);?>">
            </div>
        </div>
    </div>
</div>

<!-- Step 1 / End -->