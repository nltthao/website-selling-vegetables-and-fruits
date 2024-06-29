<?php
    $products  = \ReviewX_Helper::woocommerce_product_list();
    $columns = \ReviewX_Helper::table_columns();
    $sort_data = $columns[0];
    $columns = $columns[1];
    $sort_columns_name = [];

    if ( isset( $sort_data[0] ) && ( is_array( $sort_data[0] ) || is_object( $sort_data[0] ) ) ) {
        foreach($sort_data[0] as $key => $value) {
            $sort_columns_name[] = $key;
        }
    }
?>

<div class="rx-metabox-wrapper rx-metabox-export-wrapper">
    <div class="rx-settings-header rx-export-header">
        <div class="rx-header-left">
            <div class="rx-admin-header">
                <img src="<?php echo esc_url(assets('admin/images/ReviewX.svg')); ?>" alt="<?php esc_attr_e( 'ReviewX', 'reviewx' ) ?>">
                    <div>        
                        <h2 class="rx-plugin-tagline"><?php esc_html_e( 'ReviewX Export Review', 'reviewx' ); ?></h2>
                        <h3 class="rx-export-plugin-tagline2">
                            <?php esc_html_e( 'Review export with custom fields and filters', 'reviewx' ); ?>
                        </h3>
                    </div>
            </div>
        </div>
        <div class="rx-header-right">
            <span><?php esc_html_e( 'ReviewX', 'reviewx' ); ?>: <strong><?php echo REVIEWX_VERSION; ?></strong></span>
            <?php 
                if( class_exists('ReviewXPro') ):
            ?>
            <span><?php esc_html_e( 'ReviewX Pro', 'reviewx' ); ?>: <strong><?php echo REVIEWX_PRO_VERSION; ?></strong></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="rx-export-header-devider"></div>

    <div class="rx-meta-main-container rx-export-main-container">
        <section>
            <div class="container">
                <form id="rvx-export-form" class="rx-export-from">
                    <div class="step step-1 active">
                        <div class="rx-export-steps-wrapper">
                            <div class="rx-export-description">
                                <p>
                                    To begin your review export journey please click the start button. 
                                    You can customize the filters and fields based on your need.
                                </p>
                            </div>
                            <div class="rx-export-start-button">
                                <button type="button" class="next-btn rx-export-next-btn"><?php esc_html_e( 'Get Start', 'reviewx' ); ?></button>
                            </div>
                        </div>
                    </div>
                    <div class="step step-2">
                        <div class="rx-export-heading">
                            <h3><?php esc_html_e( 'Filter data', 'reviewx' ); ?></h3>
                            <div class="rx-export-heading-subtitle"><?php esc_html_e( 'Filter review export data based on different criteria mentioned below.', 'reviewx' ); ?></div>
                        </div>
                        <div class="rx-export-steps-wrapper">
                            <div class="form-group rx-export-form-group1">
                                <div class="rx-export-step2">
                                    <div class="rx-export-step2-fld-lbl">
                                        <label for="total-review" class="rx-export-total-lbl"><?php esc_html_e( 'Total Number of Reviews to Export', 'reviewx' ); ?></label>
                                        <label for="skip-no" class="rx-export-skip-lbl"><?php esc_html_e( 'Skip First n Reviews', 'reviewx' ); ?></label>
                                        <label for="date-from" class="rx-export-date-from-lbl"><?php esc_html_e( 'Date From', 'reviewx' ); ?></label>
                                        <label for="date-to" class="rx-export-date-to-lbl"><?php esc_html_e( 'Date To', 'reviewx' ); ?></label>
                                        <label for="post-name" class="rx-export-post-name-lbl"><?php esc_html_e( 'Products', 'reviewx' ); ?></label>
                                        <label for="selected-review" class="rx-export-review-lbl"><?php esc_html_e( 'Star Rating', 'reviewx' ); ?></label>
                                        <label for="status" class="rx-export-status-lbl"><?php esc_html_e( 'Status of Review', 'reviewx' ); ?></label>
                                        <label for="sort-columns" class="rx-export-sort-lbl"><?php esc_html_e( 'Sort Columns', 'reviewx' ); ?></label>
                                        <label for="sort-by" class="rx-export-sory-by-lbl"><?php esc_html_e( 'Sort By', 'reviewx' ); ?></label>
                                    </div>

                                    <div class="rx-export-step2-fld">
                                        <div class="rx-export-fld-container">
                                            <input type="number" id="total-review" name="total-review" min="0" placeholder="Unlimited" />
                                            <div class="rx-export-fld-suggestion"><?php esc_html_e( 'Exports a specific number of reviews. e.g. Entering 100 with a skip count of 10 will export reviews from 11th to 110th.', 'reviewx' ); ?></div>
                                        </div>

                                        <div class="rx-export-fld-container">
                                            <input type="number" id="skip-no" name="skip-review" min="0" placeholder="None"/>
                                            <div class="rx-export-fld-suggestion"><?php esc_html_e( 'Skips a specific number of reviews from the beginning of the database. e.g. Enter 10 to skip the first 10 reviews during export', 'reviewx' ); ?></div>
                                        </div>

                                        <div class="rx-export-fld-container">
                                            <input type="date" id="date-from" name="date-from" placeholder="All Time"/>
                                            <div class="rx-export-fld-suggestion"><?php esc_html_e( 'The date when the review was received. A list of product reviews received on and after the specified date will be exported.', 'reviewx' ); ?></div>
                                        </div>

                                        <div class="rx-export-fld-container">
                                            <input type="date" id="date-to" name="date-to" placeholder="All Time"/>
                                            <div class="rx-export-fld-suggestion"><?php esc_html_e( 'Date on which the review was received. You can export product reviews received up to a certain date.', 'reviewx' ); ?></div>
                                        </div>

                                        <div class="rx-export-fld-container">
                                            <select id="post-name" name="post-name">
                                                <option value="all">All</option>
                                                <?php foreach( $products as $product): ?>
                                                <option value="<?php echo $product->post_title; ?>"><?php echo $product->post_title; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="rx-export-fld-suggestion"><?php esc_html_e( 'Insert the product name to export respective reviews.', 'reviewx' ); ?></div>
                                        </div>

                                        <div class="rx-export-fld-container">
                                            <select id="selected-review" name="selected-review">
                                                <option value="all">All Rating</option>
                                                <option value="1">1 ≤ 2</option>
                                                <option value="2">2 ≤ 3</option>
                                                <option value="3">3 ≤ 4</option>
                                                <option value="4">4 ≤ 5</option>
                                                <option value="5">Only 5</option>
                                            </select>
                                            <div class="rx-export-fld-suggestion"><?php esc_html_e( 'Export reviews based on a specific star rating.', 'reviewx' ); ?></div>
                                        </div>

                                        <!-- <label for="reviewer-type">Customer/Guest Review</label>
                                        <select id="reviewer-type" name="reviewer-type">
                                            <option value="all">All</option>
                                            <option value="customer">Customer</option>
                                            <option value="guest">Guest</option>
                                        </select> -->
                                        
                                        <div class="rx-export-fld-container">
                                            <select id="status" name="status">
                                                <option value="1">Approved</option>
                                                <option value="0">Unapproved</option>
                                                <option value="trash">Trash</option>
                                                <option value="spam">Spam</option>
                                            </select>
                                            <div class="rx-export-fld-suggestion"><?php esc_html_e( 'Export reviews by specific review status.', 'reviewx' ); ?></div>
                                        </div>

                                        <div class="rx-export-fld-container">
                                            <select id="sort-columns" name="sort-columns">
                                                <?php foreach( $sort_columns_name as $column ): ?>
                                                <option value="<?php echo $column; ?>"><?php echo $column; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="rx-export-fld-suggestion"><?php esc_html_e( 'Sort the exported reviews based on the selected column in the order specified. Defaulted to ascending order.', 'reviewx' ); ?></div>
                                        </div>
                                        
                                        <div class="rx-export-fld-container">
                                            <select id="sort-by" name="sort-by">
                                                <option value="ASC">Ascending</option>
                                                <option value="DESC">Descending</option>
                                            </select>                                
                                            <div class="rx-export-fld-suggestion"><?php esc_html_e( 'Defaulted to Ascending. Applied to the above-selected columns in order.', 'reviewx' ); ?></div>
                                        </div>
                                    </div>                                
                                </div>
                            </div>
                        </div>
                        <div class="rx-export-bottom-devider"></div>
                        <div class="rx-export-bottom-button-container">
                            <button type="button" class="previous-btn rx-export-prev-btn"><?php esc_html_e( 'Back', 'reviewx' ); ?></button>
                            <button type="button" class="next-btn rx-export-next-btn"><?php esc_html_e( 'Next', 'reviewx' ); ?></button>
                        </div>
                    </div>
                    <div class="step step-3">
                        <div class="rx-export-heading">
                            <h3><?php esc_html_e( 'Customize and Select Column ', 'reviewx' ); ?></h3>
                            <div class="rx-export-heading-subtitle"><?php esc_html_e( 'You can customize the column name and select columns to export data as per your requirement ', 'reviewx' ); ?></div>
                        </div>  
                        <div class="rx-export-steps-wrapper">
                            <div class="form-group rx-export-form-group2">
                                <div class="rx-export-steps3-header">
                                    <div class="rx-export-steps3-header-select">
                                        <input type="checkbox" name="select-all-cloumn" id="rvx-select-all-column" name="all" value="all" />
                                        <label for="select-all-cloumn"><?php esc_html_e( 'Column', 'reviewx' ); ?>  </label>
                                    </div>
                                    <label class="rx-export-column-name"><?php esc_html_e( 'Column Name', 'reviewx' ); ?> </label>
                                </div>
                                <div class="rx-export-steps3-header-devider"></div>

                                <?php foreach( $columns as $column ): ?>
                                <div class="rx-export-steps3-content">
                                    <div class="rx-export-steps3-content-select">
                                        <input type="checkbox" id="<?php echo $column; ?>" name="<?php echo $column; ?>" value="<?php echo $column; ?>">
                                        <label for="<?php echo $column; ?>" name=" <?php echo $column; ?>"> <?php echo $column; ?></label>
                                    </div>
                                    <input type="text" id="<?php echo $column; ?>" value="<?php echo $column; ?>"/>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="rx-export-bottom-devider"></div>
                        <div class="rx-export-bottom-button-container">
                            <button type="button" class="previous-btn rx-export-prev-btn"><?php esc_html_e( 'Back', 'reviewx' ); ?></button>
                            <button type="submit" class="submit-btn rx-export-next-btn"><?php esc_html_e( 'Export', 'reviewx' ); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>