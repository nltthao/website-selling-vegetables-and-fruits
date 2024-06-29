<?php

declare(strict_types=1);

namespace ReviewX\Controllers\Storefront\Modules;

class GoogleReviews
{
    public function allData()
    {
        $data = [];
        $data = get_option('rx_location_review');
        return $data;
    }

    public function getPlaceId()
    {
        $placeId = $this->allData();
        if ($placeId) {
            return $placeId['rx_review_id'];
        }
    }

    public function getApiToken()
    {
        $placeId = $this->allData();
        if ($placeId) {
            return $placeId['rx_review_api'];
        }
    }

    public static function rxGoogleReviews()
    {
        $self = new self;
        // $response = wp_remote_get('https://maps.googleapis.com/maps/api/place/details/json?place_id=ChIJHegKoJUfyUwRjMxaCcviZDA&key=AIzaSyAz_zrubj0dPQa2RPnMgh7c-YsUuFOanzc', array('sslverify' => false));
        if (!empty($self->getPlaceId())) {


            $option['cache_data_xdays_local'] = 30;
            if ($self->getPlaceId()) {
                $review_write_url = 'https://search.google.com/local/writereview?placeid=' . urlencode($self->getPlaceId());
            }

            $url = 'https://maps.googleapis.com/maps/api/place/details/json?place_id=' . urlencode($self->getPlaceId() ? $self->getPlaceId() : '') . '&key=' . urlencode($self->getApiToken());
            if (file_exists(plugin_dir_path(__FILE__) . 'reviews.json') && (filemtime(plugin_dir_path(__FILE__) . 'reviews.json') > strtotime('-' . $option['cache_data_xdays_local'] . ' days'))) {
                $response = file_get_contents(plugin_dir_path(__FILE__) . 'reviews.json');
                $json = json_decode($response, true);
                $data = $json['result']['reviews'];
                $company_data = $json['result']['rating'];
            } else {
                $response = wp_remote_get($url . '', array('sslverify' => false));
                try {
                    $json = json_decode($response['body'], true);
                    $data = $json['result']['reviews'];
                    $company_data = $json['result']['rating'];
                } catch (Exception $ex) {
                    echo $json = "probllem" . $ex;
                }
            }

            $option['your_language_for_tran'] = 'en';
            $arrContextOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ),
                'http' => array(
                    'method' => 'GET',
                    'header' => 'Accept-language: ' . $option['your_language_for_tran'] . "\r\n" .
                        "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36\r\n"
                )
            );
            $result = file_get_contents($url, false, stream_context_create($arrContextOptions));
            $fp = fopen(plugin_dir_path(__FILE__) . 'reviews.json', 'w');
            fwrite($fp, $result);
            fclose($fp);
            wp_enqueue_style('slick-main',  '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', array(), '1.1.0', 'all');
            wp_enqueue_style('slick-main-theme',  '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css', array(), '1.1.0', 'all');
            wp_enqueue_style('slick-bootstrap',  '//cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css', array(), '1.1.0', 'all');
            wp_enqueue_style('slick-css',  assets('storefront/css/slick.css'), array(), '1.1.0', 'all');
            wp_enqueue_script('custom-slick', assets('storefront/js/custom.slick.js'), array('jquery'), '1.1.0', true);
            wp_enqueue_script('slick-js', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), '1.1.0', true);
?>

            <!-- Bootstrap Start -->
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div>
                            <div class="d-flex justify-content-center">
                                <div class="review-item-wrappers">
                                    <div class="review-items">
                                        <div class="review-metas">
                                            <span class="review-author">
                                                <?php echo (esc_textarea($json['result']['name'])); ?>
                                            </span>
                                            <span class="review-authors">
                                                <?php echo sprintf("Based on %s reviews", $json['result']['user_ratings_total']); ?>
                                            </span>
                                        </div>
                                        <div class="reviews-stars">

                                            <ul>
                                                <li style="color:#FEC643"><b><?php echo esc_textarea($json['result']['rating']); ?></b></li>
                                                <?php
                                                // $float
                                                $float_rating = $company_data - (int)$company_data;
                                                $a = 1;
                                                while ($a <= (int)$company_data) { ?>
                                                    <li><i class="star" style="color: #E7732F"></i></li>

                                                <?php $a++;
                                                } ?>
                                                <?php if ($float_rating) : ?>
                                                    <li>
                                                        <svg width="17" height="17" viewBox="0 0 1792 1792">
                                                            <path d="M1250 957l257-250-356-52-66-10-30-60-159-322v963l59 31 318 168-60-355-12-66zm452-262l-363 354 86 500q5 33-6 51.5t-34 18.5q-17 0-40-12l-449-236-449 236q-23 12-40 12-23 0-34-18.5t-6-51.5l86-500-364-354q-32-32-23-59.5t54-34.5l502-73 225-455q20-41 49-41 28 0 49 41l225 455 502 73q45 7 54 34.5t-24 59.5z" fill="#e7711b"></path>
                                                        </svg>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                            <div class="google-btn">
                                                <div class="google-icon-wrapper">
                                                    <img class="google-icon" src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" referrerpolicy="no-referrer" />
                                                </div>
                                                <a href="<?php echo esc_url($review_write_url) ?>" onclick="return rplg_leave_review_window.call(this)">
                                                    <p class="btn-text"><b>Write a review</b></p>
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="review-item-wrapper">
                            <?php if (!empty($data)) : ?>
                                <?php for ($i = 0; $i <= 4; $i++) : ?>
                                    <div class="review-item">
                                        <div class="review-meta">
                                            <span class="review-img">
                                                <img src="<?php echo esc_url($data[$i]['profile_photo_url']); ?>" referrerpolicy="no-referrer">
                                            </span>
                                            <span class="review-author"><?php echo esc_textarea($data[$i]['author_name']); ?></span>
                                            <span class="review-sep">, </span>
                                            <span class="review-date"><?php echo esc_textarea($data[$i]['relative_time_description']); ?></span>
                                        </div>
                                        <div class="review-stars">
                                            <ul>
                                                <?php
                                                $rating = $data[$i]['rating'];
                                                $a = 1;
                                                while ($a <= $rating) { ?>
                                                    <li><i class="star"></i></li>
                                                <?php $a++;
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                        <p class="review-text"><?php echo esc_textarea($data[$i]['text']); ?></p>
                                    </div>
                                <?php endfor; ?>
                            <?php else : ?>
                                <span class="review-author"><?php echo "No Review Found"; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>


<?php
        }
    }
}
