<?php

/* ////////////////////////////////////////////////////////////////////////////////
  \\\\\\\\\\\\\\\\\\\\\\\\\  FME Productvideos Module  \\\\\\\\\\\\\\\\\\\\\\\\\
  /////////////////////////////////////////////////////////////////////////////////
  \\\\\\\\\\\\\\\\\\\\\\\\\ NOTICE OF LICENSE\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
  ///////                                                                   ///////
  \\\\\\\ This source file is subject to the Open Software License (OSL 3.0)\\\\\\\
  ///////   that is bundled with this package in the file LICENSE.txt.      ///////
  \\\\\\\   It is also available through the world-wide-web at this URL:    \\\\\\\
  ///////          http://opensource.org/licenses/osl-3.0.php               ///////
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
  ///////                      * @category   FME                            ///////
  \\\\\\\                      * @package    FME_Productvideos              \\\\\\\
  ///////    * @author    FME Extensions <support@fmeextensions.com>   ///////
  \\\\\\\                                                                   \\\\\\\
  /////////////////////////////////////////////////////////////////////////////////
  \\* @copyright  Copyright 2015 © fmeextensions.com All right reserved\\\
  /////////////////////////////////////////////////////////////////////////////////
 */

namespace FME\Productvideos\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @param \Magento\Framework\App\Helper\Context              $context
     * @param \Magento\Framework\ObjectManagerInterface          $objectManager
     * @param \Magento\Framework\Registry                        $registry
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param \FME\Productvideos\Model\ProductvideosFactory    $productvideosFactory
     * @param \FME\Productvideos\Model\Productvideos           $productvideos
     * @param \Magento\Framework\Image\Factory                   $imageFactory
     * @param \Magento\Framework\App\Resource                    $coreResource
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \FME\Productvideos\Model\ProductvideosFactory $productvideosFactory,
        \FME\Productvideos\Model\Productvideos $productvideos,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\App\ResourceConnection $coreResource
    ) 
    {
    
     
     
     
     
     
     
     
     
     
    

        $this->_productvideosFactory = $productvideosFactory;
        $this->_productvideos = $productvideos;
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_eventManager = $context->getEventManager();
        $this->_imageFactory = $imageFactory;
        $this->_resource = $coreResource;

        parent::__construct($context);
    }
    const XML_PATH_YOUTUBE_API_KEY = 'catalog/product_video/youtube_api_key';
    const XML_PATH_MODULE_ENABLE = 'productvideos/general/enable_module';
    const XML_PATH_THUMB_WIDTH = 'productvideos/general/thumb_width';
    const XML_PATH_THUMB_HEIGHT = 'productvideos/general/thumb_height';
    const XML_PATH_TITLE = 'productvideos/general/title';

    const XML_PATH_COLOR = 'productvideos/general/bg_color';
    const XML_PATH_FRAME = 'productvideos/general/frame_thumb';
    const XML_PATH_ASPECTRATIO = 'productvideos/general/aspect_ration';


    public function productVideosEnable()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MODULE_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getThumbWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMB_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getColor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getAspectRatio()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ASPECTRATIO,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getFrame()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FRAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getThumbHeight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMB_HEIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getYouTubeApiKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_YOUTUBE_API_KEY);
    }
    /**
     * getMediaUrl
     * @return string
     */
    public function getMediaUrl()
    {

        return $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
    }

    /**
     *
     * @param  $imgUrl
     * @param  $x
     * @param  $y
     * @param  $imagePath
     * @return string
     */
    public function hexToRgb($hex, $alpha = false)
    {
        $hex      = str_replace('#', '', $hex);
        $length   = strlen($hex);
        $rgb['0'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
        $rgb['1'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
        $rgb['2'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
        if ($alpha) {
            $rgb['a'] = $alpha;
        }
        return $rgb;
    }
    public function resizeImage($imgUrl, $x = null, $y = null, $imagePath = null)
    {
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
        $baseScmsMediaURL = $mediaDirectory->getAbsolutePath();
        if ($x == null && $y == null) {
            $x = $this->getThumbWidth();
            $y = $this->getThumbHeight();
            if ($x == null && $y == null) {
                $x = 200;
                $y = 200;
            }
        }



        $imgPath = $this->splitImageValue($imgUrl, "path");
        $imgName = $this->splitImageValue($imgUrl, "name");



        /**
         * Path with Directory Seperator
         */
        $imgPath = str_replace("/", '/', $imgPath);

        /**
         * Absolute full path of Image
         */
        $imgPathFull = $baseScmsMediaURL . $imgPath . '/' . $imgName;


        /**
         * If Y is not set set it to as X
         */
        $width = $x;
        $y ? $height = $y : $height = $x;

        /**
         * Resize folder is widthXheight
         */
        $resizeFolder = $width . "X" . $height;

        /**
         * Image resized path will then be
         */
        $imageResizedPath = $baseScmsMediaURL . $imgPath . '/' . $resizeFolder . '/' . $imgName;

        /**
         * First check in cache i.e image resized path
         * If not in cache then create image of the width=X and height = Y
         */
        $colorArray = [];
        $color = "161,0,27";
        $colorArray = explode(",", $color);
        $bgColor = $this->getColor();
        $bgColorArray = $this->hexToRgb($bgColor);
        $keepRatio = false;
        $keepFrame = false;
        if ($this->getAspectRatio() == 1) {
            $keepRatio = true;
        } else {
            $keepRatio = false;
        }
        
        if ($this->getFrame() == 1) {
            $keepFrame = true;
        } else {
            $keepFrame = false;
        }
        /*
        $width = $this->_helper->getThumbWidth();
        $height = $this->_helper->getThumbHeight();
        $bgColor = $this->_helper->getBgcolor();
        $bgColorArray = $this->hexToRgb($bgColor);

        //$bgColorArray = explode(",", $bgColor);
        $imageObj = $this->_imageFactory->create($source);
        $imageObj->constrainOnly(true);
        $imageObj->keepAspectRatio($keepRatio);
        $imageObj->keepFrame($keepFrame);
        $imageObj->backgroundColor([intval($bgColorArray[0]),intval($bgColorArray[1]),intval($bgColorArray[2])]);
        $imageObj->resize($width, $height);


        */
        //print_r($colorArray); exit();
        if (!file_exists($imageResizedPath) && file_exists($imgPathFull)) :
            $imageObj = $this->_imageFactory->create($imgPathFull);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio($keepRatio);
            $imageObj->keepFrame($keepFrame);
            $imageObj->backgroundColor([intval($bgColorArray[0]),intval($bgColorArray[1]),intval($bgColorArray[2])]);
            $imageObj->resize($width, $height);
            $imageObj->save($imageResizedPath);
        endif;

        /**
         * Else image is in cache replace the Image Path with / for http path.
         */
        $imgUrl = str_replace('/', "/", $imgPath);

        /**
         * Return full http path of the image
         */
        return $this->getMediaUrl() . $imgUrl . "/" . $resizeFolder . "/" . $imgName;
    }

    /**
     * splitImageValue
     * @param  $imageValue
     * @param  string $attr
     * @return string
     */
    public function splitImageValue($imageValue, $attr = "name")
    {
        $imArray = explode("/", $imageValue);

        $name = $imArray[count($imArray) - 1];
        $path = implode("/", array_diff($imArray, [$name]));
        if ($attr == "path") {
            return $path;
        } else {
            return $name;
        }
    }

    /**
     * video_info
     * @param   $url
     * @return   array
     */
    public function videoinfo($url)
    {

        // Handle Youtube
        if (strpos($url, "youtube.com") !== false || strpos($url, "youtu.be") !== false) {
            $data = $this->getYouTubeInfo($url);
        } // End Youtube
        // Handle Vimeo
        elseif (strpos($url, "vimeo.com") !== false) {
            $data = $this->getVimeoInfo($url);
        } // End Vimeo
        // Handle Dailymotion
        elseif (strpos($url, "dailymotion.com") !== false) {
            $data['video_type'] = 'dailymotion';
            $video_id = explode('dailymotion.com/video/', $url);
            $video_id = $video_id[1];
            $data['video_id'] = '//www.dailymotion.com/embed/video/' . $video_id . '?autoPlay=1';
            return $data;
        } //End Dailymotion
        // Set false if invalid URL
        else {
            $data = false;
        }

        return $data;
    }

    /**
     * getYouTubeInfo
     * @param  $url
     * @return array
     */
    public function getYouTubeInfo($url)
    {

       // $url = parse_url($url);
       //  print_r($url);exit;
        //$vid = parse_str($url['query'], $output);
      //  $video_id = $output['v'];
         if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
    $video_id = $match[1];
         }
        $data['video_type'] = 'youtube';
        $data['video_id'] = $video_id;
        return $data;
    }

    /**
     * getVimeoInfo
     * @param  $url
     * @return array
     */
    public function getVimeoInfo($url)
    {
        $video_id = explode('vimeo.com/', $url);
        $video_id = $video_id[1];
        $data['video_type'] = 'vimeo';
        $data['video_id'] = $video_id;
        return $data;
    }

    public function getImageUrl($img)
    {
        if ($img != null) {
            if (strpos($img, 'https://') !== false) {
                       $img_url = $img;
            } else {
                $img_url = $this->resizeImage($img);
            }
        } else {
            $img = 'productvideos/no_img.jpg';
            $img_url = $this->resizeImage($img);
        }

        return $img_url;
    }

    public function getVideoData($_item)
    {
        $daysUploded = $this->timeFrame($_item['created_time']);
        $thumbb=$this->getMediaUrl().$_item['video_thumb'];

        if ($_item["video_type"] == "file") {
            $file = $this->getMediaUrl() . $_item['video_file'];
            $data = "data-toggle='jwplayer' data-diff='" . $daysUploded . "' data-title='" . $_item['title'] . "' data-content='" . $_item['content'] . "' data-target='#videoModal' data-video='" . $file . "' data-thumbb='".$thumbb . "'";
        } elseif ($_item["video_type"] == "url") {
            $videoURL = $_item["video_url"];
            $videoData = $this->videoinfo($_item["video_url"]);
            //For Video URL
            if ($videoData !== false) {
                $video_type = $videoData['video_type'];
                $video_id = $videoData['video_id'];
                if ($video_type == "vimeo") {
                    $videoURL = 'http://player.vimeo.com/video/' . $video_id . '?portrait=0&autoplay=1';
                    $data = "data-toggle='iframe' data-diff='" . $daysUploded . "' data-title='" . $_item['title'] . "' data-content='" . $_item['content'] . "' data-target='#videoModal' data-video='" . $videoURL . "'";
                } elseif ($video_type == "youtube") {
                    $videoURL = "http://www.youtube.com/embed/" . $video_id;
                    $data = "data-toggle='iframe' data-diff='" . $daysUploded . "' data-title='" . $_item['title'] . "' data-content='" . $_item['content'] . "' data-target='#videoModal' data-video='" . $videoURL . "'";
                } elseif ($video_type == "dailymotion") {
                    $videoURL = $video_id;
                    $data = "data-toggle='iframe' data-diff='" . $daysUploded . "' data-title='" . $_item['title'] . "' data-content='" . $_item['content'] . "' data-target='#videoModal' data-video='" . $videoURL . "'";
                }
            }
        }

        //print_r($data);exit;
        return $data;
    }

    public function timeFrame($from)
    {
        $day = 24 * 3600;
        $to = ((new \DateTime())->format('Y-m-d'));
        $from = strtotime($from);
        $to = strtotime($to) + $day;
        $diff = abs($to - $from);
        $weeks = floor($diff / $day / 7);
        $days = floor($diff / $day - $weeks * 7);
        $out = [];
        if ($weeks) {
            $out[] = "$weeks Week" . ($weeks > 1 ? 's' : '');
        }

        if ($days) {
            $out[] = "$days Day" . ($days > 1 ? 's' : '');
        }

        return implode(', ', $out);
    }
}
