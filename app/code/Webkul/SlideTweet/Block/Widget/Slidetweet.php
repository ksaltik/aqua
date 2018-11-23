<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SlideTweet
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\SlideTweet\Block\Widget;

use Abraham\TwitterOAuth\TwitterOAuth;

class Slidetweet extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_template = 'widget/slidetweet.phtml';
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        array $data = []
    ) {
            $this->_productRepository = $productRepository;
            $this->listProductBlock = $listProductBlock;
            parent::__construct($context, $data);
    }

    public function getVariables()
    {
        $data['title']  = $this->getData('slide_title');
        $data['titleLink'] = $this->getData('slide_title_link');
        $data['titleLinkcolor'] = $this->getData('title_bar_color');
        $data['titleColor'] = $this->getData('title_color');
        $data['tweetTitleColor'] = $this->getData('tweet_color');
        $data['tweetNumber'] = $this->getData('tweets_number');
        $data['term'] = $this->getData('term');
        $data['height'] = $this->getData('frame_height');
        $data['width'] = $this->getData('frame_width');
        $data['framecolor'] = $this->getData('frame_color');
        $data['backgroundcolor'] = $this->getData('background_color');
        $data['frameborder'] = $this->getData('frame_border_width');
        $data['iconheight'] = $this->getData('icon_height');
        $data['iconwidth'] = $this->getData('icon_width');
        $data['timeoutdelay'] = $this->getData('timeout_delay');
        $data['consumerkey'] = $this->getData('consumer_key');
        $data['consumersecret'] = $this->getData('consumer_secret');
        $data['oauthaccesstoken'] = $this->getData('acess_token');
        $data['oauthaccesstokensecret'] = $this->getData('acess_token_secret');
        $data['url'] =  $this->getUrl('slidetweet/twitter/data');
         return $data;
    }
    public function getTwitterFeeds()
    {
        $tweetNumber = $this->getData('tweets_number');
        $term = $this->getData('term');
        $consumerKey    = $this->getData('consumer_key');
        $consumerSecret = $this->getData('consumer_secret');
        $oauthAccessToken = $this->getData('acess_token');
        $oauthAccessTokenSecret = $this->getData('acess_token_secret');
        $connection = new TwitterOAuth(
            $consumerKey,
            $consumerSecret,
            $oauthAccessToken,
            $oauthAccessTokenSecret
        );
        $content = $connection->get("account/verify_credentials");
        $result = $connection->get("search/tweets", [
             "q" => $term,
              "result_type" => "recent",
             "count" => $tweetNumber
        ]);
        if($this->_scopeConfig->getValue('slidetweet/general_settings/status',
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 0){
           return false;
       }
        if(isset($result->errors)){
            foreach($result->errors as $key=>$err){
                $this->_logger->info('Twitter log',[$key => $err]);
                $this->_logger->debug('Twitter log',[$key => $err]);
            }
            return false;
        }
    return $result;
}
    public function getTimeInterval($time)
    {
        $date = date("Y-m-d h:i:sa");
        $datetime1 = strtotime($date);
        $datetime2=strtotime($time);
        $interval = $datetime1-$datetime2;
        $day = floor($interval/86400);
        $showTime = 0;
        if ($interval<60) {
             $showTime = "Just Now";
        } elseif ($interval<120) {
              $showTime= "1 minute ago";
        } elseif ($interval<3600) {
             $showTime = floor($interval/60)." minutes ago";
        } elseif ($interval<7200) {
             $showTime = "1 hour ago";
        } elseif ($interval<86400) {
            $showTime = floor($interval/3600)." hours ago";
        } elseif ($day == 1) {
            $showTime = "Yesterday";
        } elseif ($day<7) {
            $showTime = $day."days ago";
        } elseif ($day>=7) {
            $showTime = floor($day/7)."weeks ago";
        }
        return $showTime;
    }
}
