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

// @codingStandardsIgnoreFile

namespace FME\Productvideos\Block\Adminhtml\Productvideos\Edit\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Video extends \Magento\Backend\Block\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface {

    /**
     * @param \Magento\Backend\Block\Template\Context    $context      
     * @param \Magento\Framework\Registry                $registry     
     * @param \FME\Productvideos\Helper\Data           $helper       
     * @param \FME\Productvideos\Model\Productvideos $model        
     * @param array                                      $data         
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context,
            \Magento\Framework\Registry $registry,
            \FME\Productvideos\Helper\Data $helper,
            \FME\Productvideos\Model\Productvideos $model,
            array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_registery = $registry;
        $this->_helper = $helper;
        $this->_storeManager = $context->getStoreManager();
        $this->_model = $model;
    }

    /**
     * render 
     * @param  AbstractElement $element 
     * @return html
     */
    public function render(AbstractElement $element) {

        $_val = $this->_registery->registry('productvideos_data');
        $_Typevalfile = '';
        $_Typevalurl = '';
        $html = '';

        if ($_val["video_type"] == 'file') {
            $_Typevalfile = 'checked="checked"';
            $_Typevalurl = '';
        } elseif ($_val["video_type"] == 'url') {
            $_Typevalurl = 'checked="checked"';
            $_Typevalfile = '';
        } else {
            $_Typevalfile = 'checked="checked"';
            $_Typevalurl = '';
        }

        //Get the Current File
        try {
            $object = $this->_model->load($this->getRequest()->getParam('video_id'));
            $note = false;
            //Config For Popup Window
            $popupWidth = 500;
            $popupHeight = 500;
            $autoPlay = true;
            $playAgain = false;

            if ($object["video_type"] == "file") {

                if ($object["video_thumb"] != NULL) {
                    $imgURL = $this->_storeManager->getStore()->getBaseUrl(
                                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                            ) . $object["video_thumb"];
                } else {
                    $imgURL = $this->_storeManager->getStore()->getBaseUrl(
                                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                            ) . "productvideos/no_img.jpg";
                }
                $videoURL = $this->_storeManager->getStore()->getBaseUrl(
                                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                        ) . $object["video_file"];
                if ($object["video_file"] != '') {
                    $html .='<input type="hidden" value="1" name="filenotemty">';
                } else {
                    $html .='<input type="hidden" value="0" name="filenotemty">';
                }
            } elseif ($object["video_type"] == "url") {

                //For Thumb
                $videoURL = $object["video_url"];
                $videoData = $this->_helper->video_info($object["video_url"]);
                if ($videoData !== false) {

                    if ($object["video_thumb"] != NULL) {
                        $imgURL = $this->_storeManager->getStore()->getBaseUrl(
                                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                                ) . $object["video_thumb"];
                    } else {
                        $imgURL = $this->_storeManager->getStore()->getBaseUrl(
                                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                                ) . "productvideos/no_img.jpg";
                    }
                } else {
                    if ($object["video_thumb"] != NULL) {
                        $imgURL = $this->_storeManager->getStore()->getBaseUrl(
                                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                                ) . $object["video_thumb"];
                    } else {
                        $imgURL = $this->_storeManager->getStore()->getBaseUrl(
                                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                                ) . "productvideos/no_img.jpg";
                    }
                }

                //For Video URL
                if ($videoData !== false) {
                    $video_type = $videoData['video_type'];
                    $video_id = $videoData['video_id'];
                    if ($video_type == "vimeo") {

                        $videoURL = 'https://player.vimeo.com/video/' . $video_id . '?autoplay=' . $autoPlay . '';
                    } elseif ($video_type == "youtube") {

                        $videoURL = "http://www.youtube.com/v/" . $video_id;
                    } elseif ($video_type == "dailymotion") {

                        $videoURL = $video_id;
                    }
                } else {
                    $videoURL = "" . $object["video_url"];
                }
                if ($object["video_url"] != '') {
                    $html .='<input type="hidden" value="1" name="videonotempty">';
                } else {
                    $html .='<input type="hidden" value="0" name="videonotempty">';
                }
            }

            if ($object->getId()) {
                $video_typevid = $object["video_type"];
                $videoLink = '&nbsp;&nbsp;<a href="' . $videoURL . '"  target="_blank">View current file</a>';


                $imgSrc = '&nbsp;&nbsp;<a href="' . $imgURL . '" target="_blank">View current file</a>';
            } else {
                $videoLink = "";
                $imgSrc = "";
            }
        } catch (\Exception $e) {
            $videoLink = "";
        }

        $html .= '<div data-ui-id="adminhtml-productvideos-edit-tab-form-0-fieldset-element-form-field-video_type" class="admin__field field field-video_type">
                    	<label data-ui-id="adminhtml-productvideos-edit-tab-form-0-fieldset-element-text-video_type-label" for="page_video_type" class="label admin__field-label">
                    		<span>Choose Video Type</span>
                    	</label>
            			<div class="admin__field-control control">
                			<input type="radio" ' . $_Typevalfile . ' onclick="checkRadios();" id="video_typefile" value="file" name="video_type"><label for="video_typefile" class="inline">&nbsp;Media File</label>&nbsp;
							<input type="radio" id="video_typeurl" ' . $_Typevalurl . ' onclick="checkRadios();" value="url" name="video_type"><label for="video_typeurl" class="inline">&nbsp;URL</label>&nbsp;
							<p class="nm"><small>(If you want to upload file select (<b>Media File</b>) if you want to put yourtube video or link of video select second option)</small></p>	
                		</div>
                    </div>';
        $html .= '<div id="video_file_block">';
        $html .= '<div data-ui-id="adminhtml-productvideos-edit-tab-form-0-fieldset-element-form-field-my_file_uploader" class="admin__field field field-my_file_uploader ">
                    	<label data-ui-id="adminhtml-productvideos-edit-tab-form-0-fieldset-element-text-my_file_uploader-label" for="page_my_file_uploader" class="label admin__field-label">
                    		<span>Video File</span>
                    	</label>
            			<div class="admin__field-control control">
                			<input type="file" value="" name="my_file_uploader" id="my_file_uploader" />' . $videoLink . '
                			<p class="nm"><small>(Supported Format FLV, MPEG, MP4, MP3)</small></p>
                		</div>
                    </div>';
        $html .= '</div>';

        $html .= '<div id="video_url_block" style="display:none">';
        $html .= '<div data-ui-id="adminhtml-productvideos-edit-tab-form-0-fieldset-element-form-field-video_url" class="admin__field field field-video_url">
                    	<label data-ui-id="adminhtml-productvideos-edit-tab-form-0-fieldset-element-text-video_url-label" for="page_video_url" class="label admin__field-label">
                    		<span>Video URL</span>
                    	</label>
            			<div class="admin__field-control control">
                			<input type="text" class="input-text admin__control-text" data-ui-id="adminhtml-productvideos-edit-tab-form-0-fieldset-element-text-video_url" value="' . $_val["video_url"] . '" name="video_url" id="video_url" />' . $videoLink . '
                			<p class="nm"><small>(In URL field out youtube or Vimeo URL OR complete path of video e.g http://www.domain.com/media/abc.flv)</small></p>
                		</div>
                    </div>';
        $html .= '</div>';
        $html .= '<div data-ui-id="adminhtml-productvideos-edit-tab-form-0-fieldset-element-form-field-my_thumb_uploader" class="admin__field field field-my_thumb_uploader">
                    	<label data-ui-id="adminhtml-productvideos-edit-tab-form-0-fieldset-element-text-my_thumb_uploader-label" for="page_my_thumb_uploader" class="label admin__field-label">
                    		<span>Media Thumb</span>
                    	</label>
            			<div class="admin__field-control control">
                				<input type="file" value="" data-ui-id="adminhtml-productvideos-edit-tab-form-0-fieldset-element-text-my_thumb_uploader" name="my_thumb_uploader" id="my_thumb_uploader">' . $imgSrc . '
                				<p class="nm"><small>(Supported Format JPEG, PNG, GIF)</small></p>
                		</div>
                    </div>';
        $html .= '<script type="text/javascript">';
        $html .= "var checkRadios = function(){
					if ($('video_typefile').checked){
						
						$('video_file_block').show();
						$('video_url_block').hide();
			
					} else if($('video_typeurl').checked) {
			
						$('video_url_block').show();
						$('video_file_block').hide();
					}
				}
				window.onload = function() {
					checkRadios();
				}";


        $html .= '</script>';
        return $html;
    }

}

// @codingStandardsIgnoreFile